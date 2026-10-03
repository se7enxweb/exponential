CREATE TABLE expservices_token (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL DEFAULT '0',
  name varchar(100) NOT NULL DEFAULT '',
  token_hash char(64) NOT NULL DEFAULT '',
  token_hint varchar(16) NOT NULL DEFAULT '',
  created INTEGER NOT NULL DEFAULT '0',
  last_used INTEGER NOT NULL DEFAULT '0',
  expires INTEGER NOT NULL DEFAULT '0',
  revoked INTEGER NOT NULL DEFAULT '0'
);
CREATE UNIQUE INDEX expservices_token_hash ON expservices_token ( token_hash );
CREATE INDEX expservices_token_user ON expservices_token ( user_id );
