--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: ssh_hack_attempts; Type: TABLE; Schema: public; Owner: sshlog; Tablespace: 
--

CREATE TABLE ssh_hack_attempts (
    id integer NOT NULL,
    datetime timestamp with time zone NOT NULL,
    remote_addr inet NOT NULL,
    username character varying(255),
    country_code character varying(20),
    country_name character varying(500),
    region_name character varying(500),
    city character varying(500),
    lat numeric,
    long numeric
);


ALTER TABLE public.ssh_hack_attempts OWNER TO sshlog;

--
-- Name: ssh_hack_attempts_id_seq; Type: SEQUENCE; Schema: public; Owner: sshlog
--

CREATE SEQUENCE ssh_hack_attempts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.ssh_hack_attempts_id_seq OWNER TO sshlog;

--
-- Name: ssh_hack_attempts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sshlog
--

ALTER SEQUENCE ssh_hack_attempts_id_seq OWNED BY ssh_hack_attempts.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: sshlog
--

ALTER TABLE ssh_hack_attempts ALTER COLUMN id SET DEFAULT nextval('ssh_hack_attempts_id_seq'::regclass);


--
-- Name: ssh_hack_attempts_pkey; Type: CONSTRAINT; Schema: public; Owner: sshlog; Tablespace: 
--

ALTER TABLE ONLY ssh_hack_attempts
    ADD CONSTRAINT ssh_hack_attempts_pkey PRIMARY KEY (id);


--
-- Name: idx_hack_date; Type: INDEX; Schema: public; Owner: sshlog; Tablespace: 
--

CREATE INDEX idx_hack_date ON ssh_hack_attempts USING btree (datetime);


--
-- PostgreSQL database dump complete
--

