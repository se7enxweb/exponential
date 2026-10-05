SET default_storage_engine=InnoDB;
UPDATE ezsite_data SET value='6.0.0' WHERE name='ezpublish-version';
UPDATE ezsite_data SET value='1' WHERE name='ezpublish-release';

--
-- ezuser.password_hash takes the hashes Exponential writes.
--
-- Every 5.x schema up to 2017.08 has password_hash varchar(50). Exponential
-- stores php_default (bcrypt, 60 characters) hashes and, with
-- [UserSettings] UpdateHash=true, rewrites a user's hash at the first sign-in,
-- which does not fit: the sign-in fails, or the hash is cut and the next one
-- fails. 255 is the width of the kernel schema. The statement gives the column
-- the definition it already has on a newer database, so it can run again.
--

ALTER TABLE ezuser CHANGE password_hash password_hash VARCHAR(255) default NULL;
