#!/usr/bin/perl

use strict;
use warnings;

use DBI;
use Date::Parse;
use Time::ParseDate;

my $dbh = DBI->connect("dbi:Pg:dbname=sshlog", 'sshlog', 'sshlog', {AutoCommit => 1});

die "Unable to connect to db" unless $dbh;

my $sth = $dbh->prepare(qq{
	insert into dates (the_date) values (to_timestamp(?))
});

my $time = time();
for (my $i = $time - (20000 * 86400) ; $i < $time + (86400 * 20000); $i += 86400) {
	$sth->execute($i);	
}

