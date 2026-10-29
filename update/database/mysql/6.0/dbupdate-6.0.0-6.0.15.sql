--
-- Exponential 6.0.0 to 6.0.15, MySQL.
--
-- Note: no SET default_storage_engine here, because every CREATE TABLE below
-- names its engine (ENGINE=InnoDB). The older files of the chain open with
-- SET default_storage_engine=InnoDB; the spelling SET storage_engine they
-- once used was removed in MySQL 5.7.5 and in MariaDB 12.0.
--

UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version';
UPDATE ezsite_data SET value='1' WHERE name='ezpublish-release';

--
-- ezuser.password_hash takes the hashes Exponential writes.
--
-- A database that came from 5.x has password_hash varchar(50), too narrow for
-- the php_default (bcrypt, 60 characters) hash every user is given at the
-- first sign-in. dbupdate-5.4.0-6.0.0.sql widens it since October 2026; this
-- statement is here for a site that reached 6.0 with an older copy of that
-- file. It gives the column the definition of the kernel schema, so on a
-- database that already has it nothing changes. It comes first because the
-- statements after it add columns and stop on a second run.
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

--
-- ezcontentobject_trash.trashed_by and trashed_via: who moved an object to
-- the trash (the user's content object id, 0 when not known) and from where
-- ("web <siteaccess>" or "cli <script>").
--
-- The kernel writes both when content goes to the trash, in the same row and
-- transaction as the rest of it. Until they exist it stores the row without them
-- and keeps writing the old file, so the order of code and update does not
-- matter. They replace <VarDir>/trash/trashed.json, which was local to
-- one web server and outside the database's transactions and backups.
-- update/common/scripts/6.0/movetrashrecords.php copies what that file holds
-- into the columns afterwards.
--
-- Added only when missing, the same way as trashed above, so the lines can run
-- again.
--

SET @exp_trashed_by_sql := IF(
    ( SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'ezcontentobject_trash'
        AND COLUMN_NAME = 'trashed_by' ) = 0,
    'ALTER TABLE ezcontentobject_trash ADD trashed_by int(11) NOT NULL DEFAULT ''0''',
    'DO 0' );
PREPARE exp_trashed_by_stmt FROM @exp_trashed_by_sql;
EXECUTE exp_trashed_by_stmt;
DEALLOCATE PREPARE exp_trashed_by_stmt;

SET @exp_trashed_via_sql := IF(
    ( SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'ezcontentobject_trash'
        AND COLUMN_NAME = 'trashed_via' ) = 0,
    'ALTER TABLE ezcontentobject_trash ADD trashed_via varchar(100) NOT NULL DEFAULT ''''',
    'DO 0' );
PREPARE exp_trashed_via_stmt FROM @exp_trashed_via_sql;
EXECUTE exp_trashed_via_stmt;
DEALLOCATE PREPARE exp_trashed_via_stmt;

--
-- An index on ezcontentobject_trash.trashed_by: the trash view counts the
-- items per user and filters by user. On a trash of 100,000 rows the list of
-- users took 100 ms without it and 16 ms with it. Created only when missing.
--

SET @exp_trashed_by_index_sql := IF(
    ( SELECT COUNT(*) FROM information_schema.STATISTICS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'ezcontentobject_trash'
        AND INDEX_NAME = 'ezcobj_trash_trashed_by' ) = 0,
    'CREATE INDEX ezcobj_trash_trashed_by ON ezcontentobject_trash ( trashed_by )',
    'DO 0' );
PREPARE exp_trashed_by_index_stmt FROM @exp_trashed_by_index_sql;
EXECUTE exp_trashed_by_index_stmt;
DEALLOCATE PREPARE exp_trashed_by_index_stmt;

--
-- The pdf export carries its own footer wording.
--
-- Every page of every export used to read "Exponential PDF export", because
-- those words were written into content/pdf/footer.tpl and there was no way to
-- change them short of overriding the template. The wording now belongs to the
-- export and is set on its own edit page: show_footer says whether the footer
-- carries a line of text at all, footer_text says what it reads. Left empty
-- with the box still ticked, the wording that shipped is used, so an export
-- that predates this change looks exactly as it did.
--

ALTER TABLE ezpdf_export ADD COLUMN show_footer int(11) NOT NULL DEFAULT 1;
ALTER TABLE ezpdf_export ADD COLUMN footer_text varchar(255) NOT NULL DEFAULT '';

--
-- OPML exports.
--
-- An RSS export can now be written as OPML, which is a list of feeds rather
-- than a list of articles. Such an export has no content source and no class
-- mapping; what it has instead is a set of outlines, one per row below, each
-- naming another export on this installation or a content node. The address is
-- worked out when the document is written, so renaming a feed cannot leave a
-- dead entry behind.
--
-- opml_head carries the OPML head fields - owner, docs, expansion and window
-- state - as json. One column rather than a dozen, because they are written
-- once, read once and never searched on, and an installation with no OPML
-- export never fills it in.
--

ALTER TABLE ezrss_export ADD COLUMN opml_head longtext;

CREATE TABLE ezrss_export_opml_item (
  id int(11) NOT NULL auto_increment,
  rssexport_id int(11) NOT NULL default 0,
  parent_id int(11) NOT NULL default 0,
  priority int(11) NOT NULL default 0,
  target_export_id int(11) NOT NULL default 0,
  source_node_id int(11) NOT NULL default 0,
  subnodes int(11) NOT NULL default 0,
  outline_type varchar(50) default 'rss',
  outline_text varchar(255) default NULL,
  title varchar(255) default NULL,
  description varchar(255) default NULL,
  category varchar(255) default NULL,
  language varchar(50) default NULL,
  xml_url varchar(255) default NULL,
  html_url varchar(255) default NULL,
  url varchar(255) default NULL,
  is_comment int(11) NOT NULL default 0,
  is_breakpoint int(11) NOT NULL default 0,
  created int(11) NOT NULL default 0,
  status int(11) NOT NULL default 0,
  PRIMARY KEY  (id,status),
  KEY ezrss_export_opml_rsseid (rssexport_id)
) ENGINE=InnoDB;

--
-- podcast_head carries the channel level fields an Apple Podcasts feed needs
-- and no other format has anywhere to put: author, owner name and email,
-- artwork, category and subcategory, explicit, and whether the show is
-- episodic or serial. Json in one column, for the same reason opml_head is:
-- written once, read once, never searched on, and empty on any installation
-- with no podcast.
--

ALTER TABLE ezrss_export ADD COLUMN podcast_head longtext;

--
-- The order statuses of a whole order lifecycle.
--
-- An installation shipped with three statuses, Pending, Processing and
-- Delivered, so a shop that takes payment, ships parcels or handles returns
-- had to create its own before it could say where an order stood. These are
-- the steps most shops need, as internal statuses (below 1000) that cannot be
-- removed by mistake: payment (Awaiting payment, Paid, Payment failed),
-- fulfilment (On hold, Backordered, Packed, Shipped, Ready for pickup), the
-- end of an order (Completed, Cancelled) and what can follow it (Return
-- requested, Returned, Partially refunded, Refunded).
--
-- A status is only added when no row has its status_id yet, and its id is left
-- to the database, so a custom status that already uses one of those ids keeps
-- it.
--

INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Awaiting payment', 4 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 4);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Paid', 5 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 5);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Payment failed', 6 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 6);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'On hold', 7 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 7);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Backordered', 8 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 8);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Packed', 9 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 9);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Shipped', 10 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 10);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Ready for pickup', 11 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 11);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Completed', 12 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 12);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Cancelled', 13 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 13);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Return requested', 14 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 14);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Returned', 15 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 15);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Partially refunded', 16 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 16);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Refunded', 17 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 17);


--
-- The audit index (doc/eng-us/bc/6.0/audit.md, "The index"): one row per audit record (expaudit_event), how far
-- each live channel file has been indexed (expaudit_cursor) and each file's verification state (expaudit_file).
-- The JSON lines files under var/<site>/log/audit/ are the record; these tables are a copy for the console,
-- filled by the auditindex cronjob part and rebuilt from the files at any time. New in 6.0.15.
-- update/common/scripts/6.0/createaudittables.php does the same on any engine (Oracle and MongoDB too)
-- and skips tables that exist.
--
CREATE TABLE expaudit_cursor (
  byte_offset bigint(20) NOT NULL DEFAULT '0',
  channel varchar(32) NOT NULL DEFAULT '',
  file_name varchar(64) NOT NULL DEFAULT '',
  last_hash varchar(80) DEFAULT NULL,
  last_seq int(11) NOT NULL DEFAULT '0',
  updated_ms bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY ( channel, file_name )
) ENGINE=InnoDB;
CREATE TABLE expaudit_event (
  channel varchar(32) NOT NULL DEFAULT '',
  depth int(4) NOT NULL DEFAULT '0',
  domain_name varchar(16) NOT NULL DEFAULT '',
  engine varchar(16) DEFAULT NULL,
  file_name varchar(64) NOT NULL DEFAULT '',
  id char(26) NOT NULL DEFAULT '',
  imported int(4) NOT NULL DEFAULT '0',
  ip varchar(64) DEFAULT NULL,
  job_id varchar(32) DEFAULT NULL,
  login varchar(150) DEFAULT NULL,
  module_view varchar(128) DEFAULT NULL,
  name varchar(128) NOT NULL DEFAULT '',
  object_id varchar(64) DEFAULT NULL,
  object_name varchar(255) DEFAULT NULL,
  object_type varchar(32) DEFAULT NULL,
  parent_id char(26) DEFAULT NULL,
  pseudonymised int(4) NOT NULL DEFAULT '0',
  reason varchar(32) DEFAULT NULL,
  record longtext DEFAULT NULL,
  request_id varchar(40) DEFAULT NULL,
  result varchar(8) DEFAULT NULL,
  run_id varchar(40) DEFAULT NULL,
  search_text longtext DEFAULT NULL,
  seq int(11) NOT NULL DEFAULT '0',
  session_h varchar(24) DEFAULT NULL,
  severity int(4) NOT NULL DEFAULT '0',
  siteaccess varchar(64) DEFAULT NULL,
  target_id varchar(64) DEFAULT NULL,
  target_type varchar(32) DEFAULT NULL,
  time_ms bigint(20) NOT NULL DEFAULT '0',
  ua varchar(128) DEFAULT NULL,
  user_id int(11) DEFAULT NULL,
  verb varchar(32) DEFAULT NULL,
  PRIMARY KEY ( id ),
  KEY expaudit_event_domain ( domain_name, severity, time_ms ),
  UNIQUE KEY expaudit_event_file_seq ( channel, file_name, seq ),
  KEY expaudit_event_ip ( ip, time_ms ),
  KEY expaudit_event_job ( job_id ),
  KEY expaudit_event_name ( name, time_ms ),
  KEY expaudit_event_object ( object_type, object_id, time_ms ),
  KEY expaudit_event_parent ( parent_id ),
  KEY expaudit_event_request ( request_id ),
  KEY expaudit_event_result ( result, time_ms ),
  KEY expaudit_event_time ( time_ms ),
  KEY expaudit_event_user ( user_id, time_ms )
) ENGINE=InnoDB;
CREATE TABLE expaudit_file (
  archive_path varchar(255) DEFAULT NULL,
  break_line int(11) NOT NULL DEFAULT '0',
  channel varchar(32) NOT NULL DEFAULT '',
  file_name varchar(64) NOT NULL DEFAULT '',
  records int(11) NOT NULL DEFAULT '0',
  state varchar(16) NOT NULL DEFAULT 'live',
  verified varchar(16) NOT NULL DEFAULT 'unchecked',
  verified_ms bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY ( channel, file_name )
) ENGINE=InnoDB;

-- Full-text search: FULLTEXT on InnoDB (MySQL 5.6+, MariaDB 10.0.5+); without it the console searches with LIKE.
ALTER TABLE expaudit_event ADD FULLTEXT INDEX expaudit_event_fts (search_text);

-- Bookmark folders.
--
-- A user can organise bookmarks in a tree of virtual folders. folder_id is the folder
-- of a bookmark (0 is the top level, so every existing bookmark stays where it was
-- and nothing needs to be migrated), priority is the order within the folder.
-- expbookmark_folder holds the folders: parent_id 0 is the top level.
ALTER TABLE ezcontentbrowsebookmark ADD COLUMN folder_id int(11) NOT NULL DEFAULT '0';
ALTER TABLE ezcontentbrowsebookmark ADD COLUMN priority int(11) NOT NULL DEFAULT '0';
ALTER TABLE ezcontentbrowsebookmark ADD INDEX ezcontentbrowsebookmark_folder (user_id, folder_id);
CREATE TABLE expbookmark_folder (
  created int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL DEFAULT '',
  parent_id int(11) NOT NULL DEFAULT '0',
  priority int(11) NOT NULL DEFAULT '0',
  user_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY expbookmark_folder_user ( user_id, parent_id )
) ENGINE=InnoDB;

-- Bookmarks of system pages (doc/eng-us/features/6.0/bookmarks-of-system-pages.md).
--
-- A bookmark can point to a page of a module view (setup/systemupgrade, audit/console ...)
-- instead of a node. Such a bookmark is a row of ezcontentbrowsebookmark with node_id 0, so
-- it has a name, a folder and a place in its folder like every other; expbookmark_view holds
-- its address (module_uri, without the siteaccess). New table only: node bookmarks do not
-- change. Written with IF NOT EXISTS, so running this statement again changes nothing.
CREATE TABLE IF NOT EXISTS expbookmark_view (
  bookmark_id int(11) NOT NULL DEFAULT '0',
  created int(11) NOT NULL DEFAULT '0',
  module_uri longtext,
  user_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( bookmark_id ),
  KEY expbookmark_view_user ( user_id )
) ENGINE=InnoDB;

-- E-mail preferences and consent.
--
-- New tables only; nothing existing changes. Mail without a category is sent as before, so the update can run
-- before or after the code. The site secret of the links is generated on first use into
-- settings/override/mailpreferences.ini.append.php.
CREATE TABLE expmail_category (
  created int(11) NOT NULL DEFAULT '0',
  default_on int(11) NOT NULL DEFAULT '0',
  description longtext,
  double_opt_in int(11) NOT NULL DEFAULT '0',
  essential int(11) NOT NULL DEFAULT '0',
  frequencies varchar(100) NOT NULL DEFAULT '',
  handler_class varchar(255) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  identifier varchar(100) NOT NULL DEFAULT '',
  modified int(11) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  priority int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  UNIQUE KEY expmail_category_identifier ( identifier )
) ENGINE=InnoDB;
CREATE TABLE expmail_consent_log (
  action varchar(30) NOT NULL DEFAULT '',
  actor_user_id int(11) NOT NULL DEFAULT '0',
  anonymised int(11) NOT NULL DEFAULT '0',
  category varchar(100) NOT NULL DEFAULT '',
  created int(11) NOT NULL DEFAULT '0',
  email varchar(255) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  ip varchar(64) NOT NULL DEFAULT '',
  new_value varchar(100) NOT NULL DEFAULT '',
  old_value varchar(100) NOT NULL DEFAULT '',
  recipient_key varchar(80) NOT NULL DEFAULT '',
  siteaccess varchar(100) NOT NULL DEFAULT '',
  source varchar(20) NOT NULL DEFAULT '',
  user_id int(11) NOT NULL DEFAULT '0',
  wording longtext,
  PRIMARY KEY ( id ),
  KEY expmail_consent_log_created ( created ),
  KEY expmail_consent_log_recipient ( recipient_key, created ),
  KEY expmail_consent_log_user ( user_id )
) ENGINE=InnoDB;
CREATE TABLE expmail_pending (
  category varchar(100) NOT NULL DEFAULT '',
  created int(11) NOT NULL DEFAULT '0',
  data longtext,
  expires int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  kind varchar(20) NOT NULL DEFAULT '',
  recipient_key varchar(80) NOT NULL DEFAULT '',
  user_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY expmail_pending_expires ( expires ),
  KEY expmail_pending_recipient ( recipient_key, kind )
) ENGINE=InnoDB;
CREATE TABLE expmail_preference (
  category varchar(100) NOT NULL DEFAULT '',
  created int(11) NOT NULL DEFAULT '0',
  frequency varchar(20) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  modified int(11) NOT NULL DEFAULT '0',
  recipient_key varchar(80) NOT NULL DEFAULT '',
  state varchar(20) NOT NULL DEFAULT '',
  user_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY expmail_preference_category ( category, state ),
  UNIQUE KEY expmail_preference_recipient ( recipient_key, category ),
  KEY expmail_preference_user ( user_id )
) ENGINE=InnoDB;
CREATE TABLE expmail_suppression (
  created int(11) NOT NULL DEFAULT '0',
  created_by int(11) NOT NULL DEFAULT '0',
  email_hash varchar(64) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  note longtext,
  reason varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY ( id ),
  UNIQUE KEY expmail_suppression_hash ( email_hash )
) ENGINE=InnoDB;

-- Personal API keys (doc/eng-us/guides/api-keys.md).
--
-- One row per key a user made on the API access page (apikey/list). key_prefix is the
-- public part of the key (expk_<id>); the secret is never stored, only secret_hash,
-- HMAC-SHA-256 of the secret keyed with the row's own salt. scopes is a space separated
-- list of the scope ids of rest.ini [ApiKeySettings]. expires, last_used and revoked are
-- timestamps (0 = never / not yet). Written with IF NOT EXISTS, so running this
-- statement again changes nothing.
CREATE TABLE IF NOT EXISTS expapikey (
  created int(11) NOT NULL DEFAULT '0',
  created_by int(11) NOT NULL DEFAULT '0',
  expires int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  key_prefix varchar(40) NOT NULL DEFAULT '',
  last_ip varchar(64) NOT NULL DEFAULT '',
  last_used int(11) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  revoked int(11) NOT NULL DEFAULT '0',
  revoked_by int(11) NOT NULL DEFAULT '0',
  salt varchar(64) NOT NULL DEFAULT '',
  scopes varchar(255) NOT NULL DEFAULT '',
  secret_hash varchar(128) NOT NULL DEFAULT '',
  user_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  UNIQUE KEY expapikey_prefix ( key_prefix ),
  KEY expapikey_user ( user_id )
) ENGINE=InnoDB;

--
-- The run history of the import and export tools (doc/eng-us/features/6.0/transfer-runs.md).
--
-- One row per run of a tool of the transfer registry (Export, CIE, Imports ...): tool is the registry id
-- (transfer.xrowextract), direction import or export, kind export, import, dry_run, retry or undo, state queued,
-- running, done, failed or cancelled; the counts, the duration in seconds, the engine that ran it (expqueue,
-- contentjob, process) with its job (job_ref), the tool's own job or run (tool_ref), a file reference relative to
-- the tool (never a path) and the link to the tool's own page of the run. Written with IF NOT EXISTS, so running
-- this statement again changes nothing.
CREATE TABLE IF NOT EXISTS exp_transfer_run (
  bytes bigint(20) NOT NULL DEFAULT '0',
  created int(11) NOT NULL DEFAULT '0',
  detail longtext,
  detail_url varchar(255) NOT NULL DEFAULT '',
  direction varchar(10) NOT NULL DEFAULT '',
  duration int(11) NOT NULL DEFAULT '0',
  engine varchar(16) NOT NULL DEFAULT '',
  error varchar(255) NOT NULL DEFAULT '',
  file_ref varchar(255) NOT NULL DEFAULT '',
  finished int(11) NOT NULL DEFAULT '0',
  format varchar(32) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  job_ref varchar(64) NOT NULL DEFAULT '',
  kind varchar(20) NOT NULL DEFAULT '',
  modified int(11) NOT NULL DEFAULT '0',
  rows_done int(11) NOT NULL DEFAULT '0',
  rows_failed int(11) NOT NULL DEFAULT '0',
  rows_total int(11) NOT NULL DEFAULT '0',
  scope varchar(20) NOT NULL DEFAULT '',
  scope_ref varchar(255) NOT NULL DEFAULT '',
  siteaccess varchar(64) NOT NULL DEFAULT '',
  started int(11) NOT NULL DEFAULT '0',
  state varchar(16) NOT NULL DEFAULT '',
  title varchar(255) NOT NULL DEFAULT '',
  tool varchar(64) NOT NULL DEFAULT '',
  tool_ref varchar(64) NOT NULL DEFAULT '',
  user_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY exp_transfer_run_started ( started ),
  KEY exp_transfer_run_tool ( tool, started ),
  KEY exp_transfer_run_toolref ( tool, tool_ref ),
  KEY exp_transfer_run_user ( user_id )
) ENGINE=InnoDB;

--
-- The named database connections of the data tables (doc/eng-us/features/6.0/data-tables.md).
--
-- One row per connection made in the administration (Setup > Data tables > Connections): its identifier, the
-- engine (sqlite, mysql, postgresql, oracle, sqlserver, mongodb), server, port, database (for SQLite the file) and
-- user, and the NAME of the secret that holds its password (never the password). The administration creates the
-- table when it is missing. Written with IF NOT EXISTS, so running this statement again changes nothing.
CREATE TABLE IF NOT EXISTS exp_data_connection (
  created int(11) NOT NULL DEFAULT '0',
  creator_id int(11) NOT NULL DEFAULT '0',
  database_name varchar(255) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL DEFAULT '',
  engine varchar(32) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  identifier varchar(64) NOT NULL DEFAULT '',
  modified int(11) NOT NULL DEFAULT '0',
  modifier_id int(11) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  options longtext,
  port int(11) NOT NULL DEFAULT '0',
  read_only int(11) NOT NULL DEFAULT '0',
  secret varchar(128) NOT NULL DEFAULT '',
  server varchar(255) NOT NULL DEFAULT '',
  user_name varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY ( id ),
  UNIQUE KEY exp_data_connection_identifier ( identifier )
) ENGINE=InnoDB;

--
-- The kernel's secret store (doc/eng-us/features/6.0/secret-store.md).
--
-- One row per secret of a tool (a connection's password, an import source's token): its name, the tool it belongs
-- to (scope), and its value encrypted with XChaCha20-Poly1305 under the store's key (key_id names the key, never
-- holds it). The administration (Setup > Secrets) shows names only, and creates the table when it is missing.
-- Written with IF NOT EXISTS, so running this statement again changes nothing.
CREATE TABLE IF NOT EXISTS exp_secret (
  ciphertext longtext,
  created int(11) NOT NULL DEFAULT '0',
  creator_id int(11) NOT NULL DEFAULT '0',
  description varchar(255) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  key_id varchar(16) NOT NULL DEFAULT '',
  modified int(11) NOT NULL DEFAULT '0',
  modifier_id int(11) NOT NULL DEFAULT '0',
  name varchar(128) NOT NULL DEFAULT '',
  scope varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY ( id ),
  UNIQUE KEY exp_secret_name ( name ),
  KEY exp_secret_scope ( scope )
) ENGINE=InnoDB;

--
-- The synchronisation between content and data tables (doc/eng-us/features/6.0/data-tables.md, "Synchronisation").
--
-- exp_data_sync holds the definitions (class, content class, node, map, key, direction, mode, conflict rule),
-- exp_data_sync_link the pairing of a row and an object with the hashes of both at the last run, and
-- exp_data_sync_conflict the conflicts of the rule "ask" until they are resolved in the administration. The page
-- Setup > Data tables > Synchronisations creates them when they are missing.
-- Written with IF NOT EXISTS, so running these statements again changes nothing.
CREATE TABLE IF NOT EXISTS exp_data_sync (
  class_name varchar(128) NOT NULL DEFAULT '',
  conflict_rule varchar(16) NOT NULL DEFAULT '',
  content_class varchar(128) NOT NULL DEFAULT '',
  created int(11) NOT NULL DEFAULT '0',
  creator_id int(11) NOT NULL DEFAULT '0',
  direction varchar(16) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  interval_seconds int(11) NOT NULL DEFAULT '0',
  key_columns varchar(255) NOT NULL DEFAULT '',
  last_run int(11) NOT NULL DEFAULT '0',
  last_run_id int(11) NOT NULL DEFAULT '0',
  map longtext,
  modified int(11) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  next_run int(11) NOT NULL DEFAULT '0',
  options longtext,
  run_mode varchar(16) NOT NULL DEFAULT '',
  state varchar(16) NOT NULL DEFAULT '',
  subtree_node_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY exp_data_sync_mode ( run_mode, state )
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS exp_data_sync_conflict (
  content_values longtext,
  detected int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  link_id int(11) NOT NULL DEFAULT '0',
  object_id int(11) NOT NULL DEFAULT '0',
  resolution varchar(16) NOT NULL DEFAULT '',
  resolved int(11) NOT NULL DEFAULT '0',
  resolver_id int(11) NOT NULL DEFAULT '0',
  row_key varchar(255) NOT NULL DEFAULT '',
  row_values longtext,
  state varchar(16) NOT NULL DEFAULT '',
  sync_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY exp_data_sync_conflict_state ( sync_id, state )
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS exp_data_sync_link (
  content_hash varchar(32) NOT NULL DEFAULT '',
  content_modified int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  object_id int(11) NOT NULL DEFAULT '0',
  row_hash varchar(32) NOT NULL DEFAULT '',
  row_key varchar(255) NOT NULL DEFAULT '',
  row_modified int(11) NOT NULL DEFAULT '0',
  sync_id int(11) NOT NULL DEFAULT '0',
  synced int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  UNIQUE KEY exp_data_sync_link_key ( sync_id, row_key ),
  KEY exp_data_sync_link_object ( sync_id, object_id )
) ENGINE=InnoDB;

--
-- The signed-in sessions of the users, "Your devices" (doc/eng-us/features/6.0/user-sessions.md).
--
-- One row per sign-in: when it started and was last used, the address, the User-Agent header and what it names, the
-- sign-in method, Remember me, and when and why it ended. The row is found by the SHA-256 of a random token kept in
-- the session; neither the token nor the session id is stored. Without the table nothing is tracked and every page
-- works as before; update/common/scripts/6.0/createusersessiontable.php creates it on any engine.
-- Written with IF NOT EXISTS, so running this statement again changes nothing.
CREATE TABLE IF NOT EXISTS expuser_session (
  browser varchar(64) NOT NULL DEFAULT '',
  created int(11) NOT NULL DEFAULT '0',
  device_type varchar(16) NOT NULL DEFAULT '',
  end_reason varchar(32) NOT NULL DEFAULT '',
  ended int(11) NOT NULL DEFAULT '0',
  ended_by int(11) NOT NULL DEFAULT '0',
  first_ip varchar(64) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  last_active int(11) NOT NULL DEFAULT '0',
  last_ip varchar(64) NOT NULL DEFAULT '',
  method varchar(32) NOT NULL DEFAULT '',
  os varchar(64) NOT NULL DEFAULT '',
  remember_until int(11) NOT NULL DEFAULT '0',
  siteaccess varchar(64) NOT NULL DEFAULT '',
  token_hash varchar(64) NOT NULL DEFAULT '',
  user_agent varchar(512) NOT NULL DEFAULT '',
  user_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  UNIQUE KEY expuser_session_token ( token_hash ),
  KEY expuser_session_user ( user_id, ended ),
  KEY expuser_session_active ( ended, last_active )
) ENGINE=InnoDB;

--
-- What a signed-in session adds to its row of expuser_session (doc/eng-us/features/6.0/user-sessions.md): the host
-- the sign-in was made on, the country and place a local GeoIP database names (empty without one) and the second
-- step of the sign-in (totp, email, recovery). One row per session, removed with it. Without the table the sessions
-- are tracked as before, without these details; update/common/scripts/6.0/createusersessiontable.php creates it on
-- any engine. Written with IF NOT EXISTS, so running this statement again changes nothing.
CREATE TABLE IF NOT EXISTS expuser_session_info (
  auth_detail varchar(32) NOT NULL DEFAULT '',
  country varchar(2) NOT NULL DEFAULT '',
  host varchar(255) NOT NULL DEFAULT '',
  location varchar(128) NOT NULL DEFAULT '',
  session_id int(11) NOT NULL DEFAULT '0',
  user_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( session_id ),
  KEY expuser_session_info_user ( user_id )
) ENGINE=InnoDB;

--
-- ezuser.login: one account per login.
--
-- A new installation has the unique index ezuser_login from the kernel schema.
-- On an upgrade this file does not add it: the statement fails on a database
-- where two accounts have the same login, and then stops the rest of the file.
-- Add it with the command, which lists the duplicates first and adds the index
-- only when there are none:
--
--   ./console exp:user-login-check              (report, changes nothing)
--   ./console exp:user-login-check --fix-index  (adds the index after a clean report)
--
-- What --fix-index runs on MySQL and MariaDB (the column's collation decides
-- which logins are equal; as shipped it ignores case and trailing spaces):
--
-- ALTER TABLE ezuser DROP INDEX ezuser_login, ADD UNIQUE KEY ezuser_login ( login );

--
-- ezprest_authcode: PKCE (RFC 7636) for the OAuth authorization code flow.
-- oauth/authorize stores the code_challenge and code_challenge_method of the request with the code; the token end
-- point checks the code_verifier against them. Without these columns no authorization code can be issued.
-- Run once; a second run stops at "Duplicate column name", which means the columns are there.
--
ALTER TABLE ezprest_authcode
  ADD COLUMN code_challenge varchar(128) default NULL,
  ADD COLUMN code_challenge_method varchar(10) default NULL;

-- Passkeys (doc/eng-us/guides/passkeys.md).
--
-- One row per passkey (WebAuthn credential) a user registered on user/passkeys. credential_id
-- and public_key are base64url (the COSE key the authenticator sent); credential_hash is the
-- SHA-256 (hex) of the credential id, what a sign-in looks the passkey up by. user_handle is
-- the random handle the browser keeps with the passkey (never the user id). sign_count is the
-- authenticator's signature counter; created and last_used are timestamps (0 = not yet).
-- Written with IF NOT EXISTS, so running this statement again changes nothing.
CREATE TABLE IF NOT EXISTS exppasskey (
  aaguid varchar(36) NOT NULL DEFAULT '',
  algorithm int(11) NOT NULL DEFAULT '0',
  backup_eligible int(11) NOT NULL DEFAULT '0',
  backup_state int(11) NOT NULL DEFAULT '0',
  created int(11) NOT NULL DEFAULT '0',
  credential_hash varchar(64) NOT NULL DEFAULT '',
  credential_id longtext,
  id int(11) NOT NULL AUTO_INCREMENT,
  last_ip varchar(64) NOT NULL DEFAULT '',
  last_used int(11) NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL DEFAULT '',
  public_key longtext,
  sign_count bigint(20) NOT NULL DEFAULT '0',
  transports varchar(255) NOT NULL DEFAULT '',
  user_handle varchar(128) NOT NULL DEFAULT '',
  user_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  UNIQUE KEY exppasskey_credential ( credential_hash ),
  KEY exppasskey_user ( user_id )

-- Help maintained as content (doc/eng-us/features/6.0/help-content.md): exphelp_help_id records every help_id that
-- was Released and the object it belongs to, so the validator reports a help_id that was renamed or reused (clients
-- call it); exphelp_feedback keeps the answers to "Was this helpful?": the topic, yes or no, a reason from a fixed
-- list and the day, nothing about who answered. Without the tables both features are off and every page works as
-- before; ./console exp:help:content install creates them on any engine.
-- Written with IF NOT EXISTS, so running these statements again changes nothing.
CREATE TABLE IF NOT EXISTS exphelp_help_id (
  help_id varchar(80) NOT NULL default '',
  object_id int(11) NOT NULL default '0',
  released int(11) NOT NULL default '0',
  PRIMARY KEY  (help_id),
  KEY exphelp_help_id_object (object_id)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS exphelp_feedback (
  created int(11) NOT NULL default '0',
  help_id varchar(80) NOT NULL default '',
  helpful int(11) NOT NULL default '0',
  id int(11) NOT NULL auto_increment,
  language varchar(20) NOT NULL default '',
  object_id int(11) NOT NULL default '0',
  reason varchar(32) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY exphelp_feedback_object (object_id,created),
  KEY exphelp_feedback_created (created)
) ENGINE=InnoDB;

--
-- ezorder.siteaccess: the siteaccess (store or site) an order was placed in
-- (doc/eng-us/features/6.0/order-siteaccess.md). The shop records it when the
-- basket becomes an order; the order view and the order lists show it and
-- filter by it. Orders placed before keep '' and are shown as unknown: which
-- siteaccess they came from is not recorded; bin/php/expbackfillordersiteaccess.php
-- infers it from the content tree where it can (dry run unless --apply).
-- Until the column is there the kernel stores and reads orders without it.
-- Added only when it is missing, so the lines can run again.
--

SET @exp_order_siteaccess_sql := IF(
    ( SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'ezorder'
        AND COLUMN_NAME = 'siteaccess' ) = 0,
    'ALTER TABLE ezorder ADD siteaccess varchar(255) NOT NULL DEFAULT ''''',
    'DO 0' );
PREPARE exp_order_siteaccess_stmt FROM @exp_order_siteaccess_sql;
EXECUTE exp_order_siteaccess_stmt;
DEALLOCATE PREPARE exp_order_siteaccess_stmt;

--
-- expuser_audit: the user audit log (doc/eng-us/features/6.0/user-audit.md). One row per
-- security event about a user account or a user group (a change with its fields before and
-- after, a sign-in, a refused attempt, a role assignment): who, when, where (siteaccess, host,
-- the address as the audit's privacy rules leave it), whom and what. Filled from the audit
-- records of the access channel; read on user/audit. Never a password, a hash or a secret.
-- Written with IF NOT EXISTS, so running this statement again changes nothing.
--
CREATE TABLE IF NOT EXISTS expuser_audit (
  actor_id int(11) NOT NULL DEFAULT '0',
  actor_login varchar(150) NOT NULL DEFAULT '',
  client_ip varchar(64) NOT NULL DEFAULT '',
  created int(11) NOT NULL DEFAULT '0',
  event_id varchar(26) NOT NULL DEFAULT '',
  event_name varchar(128) NOT NULL DEFAULT '',
  host varchar(255) NOT NULL DEFAULT '',
  id int(11) NOT NULL AUTO_INCREMENT,
  module_view varchar(128) NOT NULL DEFAULT '',
  reason varchar(64) NOT NULL DEFAULT '',
  request_id varchar(40) NOT NULL DEFAULT '',
  result varchar(16) NOT NULL DEFAULT '',
  siteaccess varchar(64) NOT NULL DEFAULT '',
  target_id int(11) NOT NULL DEFAULT '0',
  target_login varchar(150) NOT NULL DEFAULT '',
  target_type varchar(16) NOT NULL DEFAULT '',
  time_ms bigint(20) NOT NULL DEFAULT '0',
  value_after longtext,
  value_before longtext,
  PRIMARY KEY ( id ),
  UNIQUE KEY expuser_audit_event ( event_id ),
  KEY expuser_audit_target ( target_id, created ),
  KEY expuser_audit_actor ( actor_id, created ),
  KEY expuser_audit_name ( event_name, created ),
  KEY expuser_audit_created ( created )
) ENGINE=InnoDB;
