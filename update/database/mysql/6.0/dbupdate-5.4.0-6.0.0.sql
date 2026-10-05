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

--
-- ezcontentobject_trash.trashed: the time an object was moved to the trash.
--
-- The 6.0 kernel writes it when content goes to the trash and sorts and
-- filters the trash by it, so without the column moving content to the trash
-- fails. A 5.4 database does not have it: upstream added it in its 7.3 update
-- file (EZP-28881), which is not on this path. Same definition as there and in
-- the kernel schema, no backfill.
--
-- MySQL has ADD COLUMN IF NOT EXISTS only from 8.0.29, so the statement is
-- chosen by a look at information_schema: the ALTER when the column is
-- missing, DO 0 (nothing) when it is there. That way the lines can run again.
--

SET @exp_trashed_sql := IF(
    ( SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'ezcontentobject_trash'
        AND COLUMN_NAME = 'trashed' ) = 0,
    'ALTER TABLE ezcontentobject_trash ADD trashed int(11) NOT NULL DEFAULT ''0''',
    'DO 0' );
PREPARE exp_trashed_stmt FROM @exp_trashed_sql;
EXECUTE exp_trashed_stmt;
DEALLOCATE PREPARE exp_trashed_stmt;
