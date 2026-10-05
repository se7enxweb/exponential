UPDATE ezsite_data SET value='6.0.0' WHERE name='ezpublish-version';
UPDATE ezsite_data SET value='1' WHERE name='ezpublish-release';

--
-- ezuser.password_hash takes the hashes Exponential writes.
--
-- Every 5.x schema up to 2017.08 has password_hash character varying(50).
-- Exponential stores php_default (bcrypt, 60 characters) hashes and, with
-- [UserSettings] UpdateHash=true, rewrites a user's hash at the first sign-in,
-- which does not fit: the sign-in fails. 255 is the width of the kernel
-- schema. On a column that is already 255 wide the statement changes nothing,
-- so it can run again.
--

ALTER TABLE ezuser ALTER COLUMN password_hash TYPE VARCHAR(255);
