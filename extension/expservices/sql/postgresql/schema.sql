CREATE SEQUENCE expservices_token_s START 1 INCREMENT 1 MAXVALUE 9223372036854775807 MINVALUE 1 CACHE 1;

CREATE TABLE expservices_token (
  id integer DEFAULT nextval('expservices_token_s'::text) NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  name varchar(100) DEFAULT '' NOT NULL,
  token_hash char(64) DEFAULT '' NOT NULL,
  token_hint varchar(16) DEFAULT '' NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  last_used integer DEFAULT 0 NOT NULL,
  expires integer DEFAULT 0 NOT NULL,
  revoked integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE UNIQUE INDEX expservices_token_hash ON expservices_token USING btree ( token_hash );
CREATE INDEX expservices_token_user ON expservices_token USING btree ( user_id );
