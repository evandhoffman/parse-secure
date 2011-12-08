#!/usr/bin/perl

#########################################################################
# Parse /var/log/secure for attacks and log the attacks to a database.
# 11/16/2011
# evandhoffman@gmail.com
#########################################################################

use strict;
use warnings;

use DBI;
use Date::Parse;
use Time::ParseDate;

use Geo::IP;
my $gi = Geo::IP->open('/var/www/html/GeoLiteCity.dat', GEOIP_STANDARD);


my @patterns = ( 	qr/(\w{3}\s{1,2}\d{1,2} \d{2}:\d{2}:\d{2}).+invalid user ([\S]+) from ([\d\.]+) port/, 
			qr/(\w{3}\s{1,2}\d{1,2} \d{2}:\d{2}:\d{2}).+User ([\S]+) from ([\d\.]+) not allowed because/, 
			qr/(\w{3}\s{1,2}\d{1,2} \d{2}:\d{2}:\d{2}).+Failed password for (root) from ([\d\.]+) port/ );

#Dec  8 08:57:20 lunix2011 sshd[26778]: User root from 78.129.230.32 not allowed because not listed in AllowUsers
#Dec  8 08:57:20 lunix2011 sshd[26779]: input_userauth_request: invalid user root
#Dec  8 08:57:20 lunix2011 sshd[26778]: pam_unix(sshd:auth): authentication failure; logname= uid=0 euid=0 tty=ssh ruser= rhost=78.129.230.32  user=root
#Dec  8 08:57:23 lunix2011 sshd[26778]: Failed password for invalid user root from 78.129.230.32 port 38062 ssh2

my $dbh = DBI->connect("dbi:Pg:dbname=sshlog", 'sshlog', 'sshlog', {AutoCommit => 1});

die "Unable to connect to db" unless $dbh;

### Find the most recent record in the table.  This is so we can ignore records we don't add stuff to the DB twice.

my $sth = $dbh->prepare(qq{
	select extract(EPOCH FROM max(datetime)) as newest from ssh_hack_attempts
});
$sth->execute();
my $result = $sth->fetchrow_hashref();
my $newest_record = $result->{'newest'};
#print "Newest record: $newest_record\n";

# insert into ssh_hack_attempts (datetime, remote_addr, username) values (to_timestamp('Nov 01 2011 00:00:00','Mon DD YYYY HH24:MI:SS'), '127.0.0.1', 'evan');
$sth = $dbh->prepare(qq{
	insert into ssh_hack_attempts (datetime, remote_addr, username, country_code, country_name, region_name, city, lat, long) values (to_timestamp(?), ?, ?, ?, ?, ?, ? , ? ,?)	
});

while(<>) {
	#Nov 16 07:29:55
	chomp;
	foreach my $pat (@patterns) {
		if (my ($date, $user, $ip) = ( $_ =~ $pat )) {
#			print "==> MATCH $_\n"; 
			my $epoch = parsedate($date);
			if ($epoch > $newest_record) {
		#	print "$epoch\t$ip\t$user\n";
				my $record = $gi->record_by_addr($ip);
				$sth->execute($epoch, $ip, $user, $record->country_code, $record->country_name, $record->region_name,
					$record->city, $record->latitude, $record->longitude);
			}

		#	my ($ss,$mm,$hh,$day,$month,$year,$zone) = strptime($date);
		#	print "$year-$month-$day $hh:$mm:$ss\t$ip\t$user\n";
			#next;
		}
	}
}
