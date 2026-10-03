CREATE TABLE expservices_token (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL DEFAULT '0',
  name varchar(100) NOT NULL DEFAULT '',
  token_hash char(64) NOT NULL DEFAULT '',
  token_hint varchar(16) NOT NULL DEFAULT '',
  created int(11) NOT NULL DEFAULT '0',
  last_used int(11) NOT NULL DEFAULT '0',
  expires int(11) NOT NULL DEFAULT '0',
  revoked int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  UNIQUE KEY expservices_token_hash ( token_hash ),
  KEY expservices_token_user ( user_id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;
