--
-- Exponential 6.0.0 to 6.0.15, SQLite.
--
-- Each ALTER adds one column: SQLite takes only one per statement, and cannot
-- drop one again, so run these once.
--
-- No statement widens ezuser.password_hash here, unlike the MySQL and
-- PostgreSQL files: SQLite does not enforce the length of a VARCHAR, so a
-- 60 character bcrypt hash fits whatever the column says, and an SQLite
-- database was never a 5.x one; it was installed by 6.0 with varchar(255).
-- For the same reason it needs neither ezcontentobject_trash.trashed nor the
-- sequence renames of the MySQL and PostgreSQL files: the SQLite schema has
-- had the column since its first version, and SQLite has no sequences.
--

UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version';
UPDATE ezsite_data SET value='1' WHERE name='ezpublish-release';

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

ALTER TABLE ezcontentobject_trash ADD COLUMN trashed_by integer NOT NULL DEFAULT 0;
ALTER TABLE ezcontentobject_trash ADD COLUMN trashed_via varchar(100) NOT NULL DEFAULT '';

--
-- An index on ezcontentobject_trash.trashed_by: the trash view counts the
-- items per user and filters by user. On a trash of 100,000 rows the list of
-- users took 100 ms without it and 16 ms with it. Created only when missing.
--

CREATE INDEX IF NOT EXISTS ezcontentobject_trash__ezcobj_trash_trashed_by ON ezcontentobject_trash ( trashed_by );

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

ALTER TABLE ezpdf_export ADD COLUMN show_footer integer NOT NULL DEFAULT 1;
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

CREATE TABLE `ezrss_export_opml_item` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `rssexport_id` integer NOT NULL DEFAULT '0'
,  `parent_id` integer NOT NULL DEFAULT '0'
,  `priority` integer NOT NULL DEFAULT '0'
,  `target_export_id` integer NOT NULL DEFAULT '0'
,  `source_node_id` integer NOT NULL DEFAULT '0'
,  `subnodes` integer NOT NULL DEFAULT '0'
,  `outline_type` varchar(50) DEFAULT 'rss'
,  `outline_text` varchar(255) DEFAULT NULL
,  `title` varchar(255) DEFAULT NULL
,  `description` varchar(255) DEFAULT NULL
,  `category` varchar(255) DEFAULT NULL
,  `language` varchar(50) DEFAULT NULL
,  `xml_url` varchar(255) DEFAULT NULL
,  `html_url` varchar(255) DEFAULT NULL
,  `url` varchar(255) DEFAULT NULL
,  `is_comment` integer NOT NULL DEFAULT '0'
,  `is_breakpoint` integer NOT NULL DEFAULT '0'
,  `created` integer NOT NULL DEFAULT '0'
,  `status` integer NOT NULL DEFAULT '0'
);

CREATE INDEX "idx_ezrss_export_opml_item_ezrss_export_opml_rsseid" ON "ezrss_export_opml_item" (`rssexport_id`);

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
  SELECT 1, 'Awaiting payment', 4 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 4);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Paid', 5 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 5);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Payment failed', 6 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 6);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'On hold', 7 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 7);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Backordered', 8 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 8);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Packed', 9 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 9);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Shipped', 10 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 10);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Ready for pickup', 11 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 11);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Completed', 12 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 12);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Cancelled', 13 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 13);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Return requested', 14 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 14);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Returned', 15 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 15);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Partially refunded', 16 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 16);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Refunded', 17 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 17);


--
-- The audit index (doc/bc/6.0/audit.md, "The index"): one row per audit record (expaudit_event), how far
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
  last_seq INTEGER(11) NOT NULL DEFAULT '0',
  updated_ms bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY ( channel, file_name )
);
CREATE TABLE expaudit_event (
  channel varchar(32) NOT NULL DEFAULT '',
  depth INTEGER(4) NOT NULL DEFAULT '0',
  domain_name varchar(16) NOT NULL DEFAULT '',
  engine varchar(16) DEFAULT NULL,
  file_name varchar(64) NOT NULL DEFAULT '',
  id char(26) NOT NULL DEFAULT '',
  imported INTEGER(4) NOT NULL DEFAULT '0',
  ip varchar(64) DEFAULT NULL,
  job_id varchar(32) DEFAULT NULL,
  login varchar(150) DEFAULT NULL,
  module_view varchar(128) DEFAULT NULL,
  name varchar(128) NOT NULL DEFAULT '',
  object_id varchar(64) DEFAULT NULL,
  object_name varchar(255) DEFAULT NULL,
  object_type varchar(32) DEFAULT NULL,
  parent_id char(26) DEFAULT NULL,
  pseudonymised INTEGER(4) NOT NULL DEFAULT '0',
  reason varchar(32) DEFAULT NULL,
  record longtext DEFAULT NULL,
  request_id varchar(40) DEFAULT NULL,
  result varchar(8) DEFAULT NULL,
  run_id varchar(40) DEFAULT NULL,
  search_text longtext DEFAULT NULL,
  seq INTEGER(11) NOT NULL DEFAULT '0',
  session_h varchar(24) DEFAULT NULL,
  severity INTEGER(4) NOT NULL DEFAULT '0',
  siteaccess varchar(64) DEFAULT NULL,
  target_id varchar(64) DEFAULT NULL,
  target_type varchar(32) DEFAULT NULL,
  time_ms bigint(20) NOT NULL DEFAULT '0',
  ua varchar(128) DEFAULT NULL,
  user_id INTEGER(11) DEFAULT NULL,
  verb varchar(32) DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE  INDEX expaudit_event_domain ON expaudit_event  ( domain_name, severity, time_ms );
CREATE  UNIQUE INDEX expaudit_event_file_seq ON expaudit_event  ( channel, file_name, seq );
CREATE  INDEX expaudit_event_ip ON expaudit_event  ( ip, time_ms );
CREATE  INDEX expaudit_event_job ON expaudit_event  ( job_id );
CREATE  INDEX expaudit_event_name ON expaudit_event  ( name, time_ms );
CREATE  INDEX expaudit_event_object ON expaudit_event  ( object_type, object_id, time_ms );
CREATE  INDEX expaudit_event_parent ON expaudit_event  ( parent_id );
CREATE  INDEX expaudit_event_request ON expaudit_event  ( request_id );
CREATE  INDEX expaudit_event_result ON expaudit_event  ( result, time_ms );
CREATE  INDEX expaudit_event_time ON expaudit_event  ( time_ms );
CREATE  INDEX expaudit_event_user ON expaudit_event  ( user_id, time_ms );
CREATE TABLE expaudit_file (
  archive_path varchar(255) DEFAULT NULL,
  break_line INTEGER(11) NOT NULL DEFAULT '0',
  channel varchar(32) NOT NULL DEFAULT '',
  file_name varchar(64) NOT NULL DEFAULT '',
  records INTEGER(11) NOT NULL DEFAULT '0',
  state varchar(16) NOT NULL DEFAULT 'live',
  verified varchar(16) NOT NULL DEFAULT 'unchecked',
  verified_ms bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY ( channel, file_name )
);

-- Full-text search: an FTS5 table over search_text (external content; the trigram tokenizer of SQLite 3.34+,
-- so a search finds a part of a word as LIKE does). Without FTS5 or trigram in the SQLite build,
-- leave the statement out: search uses LIKE.
CREATE VIRTUAL TABLE expaudit_event_fts USING fts5(search_text, content='expaudit_event', content_rowid='rowid', tokenize='trigram');

-- Bookmark folders.
--
-- A user can organise bookmarks in a tree of virtual folders. folder_id is the folder
-- of a bookmark (0 is the top level, so every existing bookmark stays where it was
-- and nothing needs to be migrated), priority is the order within the folder.
-- expbookmark_folder holds the folders: parent_id 0 is the top level.
ALTER TABLE ezcontentbrowsebookmark ADD COLUMN folder_id integer NOT NULL DEFAULT 0;
ALTER TABLE ezcontentbrowsebookmark ADD COLUMN priority integer NOT NULL DEFAULT 0;
CREATE INDEX ezcontentbrowsebookmark_folder ON ezcontentbrowsebookmark ( user_id, folder_id );
CREATE TABLE expbookmark_folder (
  created integer NOT NULL DEFAULT 0,
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  name varchar(255) NOT NULL DEFAULT '',
  parent_id integer NOT NULL DEFAULT 0,
  priority integer NOT NULL DEFAULT 0,
  user_id integer NOT NULL DEFAULT 0
);
CREATE INDEX expbookmark_folder_user ON expbookmark_folder ( user_id, parent_id );

-- E-mail preferences and consent.
--
-- New tables only; nothing existing changes. Mail without a category is sent as before, so the update can run
-- before or after the code. The site secret of the links is generated on first use into
-- settings/override/mailpreferences.ini.append.php.
CREATE TABLE expmail_category (
  created integer NOT NULL DEFAULT 0,
  default_on integer NOT NULL DEFAULT 0,
  description text,
  double_opt_in integer NOT NULL DEFAULT 0,
  essential integer NOT NULL DEFAULT 0,
  frequencies varchar(100) NOT NULL DEFAULT '',
  handler_class varchar(255) NOT NULL DEFAULT '',
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  identifier varchar(100) NOT NULL DEFAULT '',
  modified integer NOT NULL DEFAULT 0,
  name varchar(255) NOT NULL DEFAULT '',
  priority integer NOT NULL DEFAULT 0
);
CREATE UNIQUE INDEX expmail_category_identifier ON expmail_category ( identifier );
CREATE TABLE expmail_consent_log (
  action varchar(30) NOT NULL DEFAULT '',
  actor_user_id integer NOT NULL DEFAULT 0,
  anonymised integer NOT NULL DEFAULT 0,
  category varchar(100) NOT NULL DEFAULT '',
  created integer NOT NULL DEFAULT 0,
  email varchar(255) NOT NULL DEFAULT '',
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  ip varchar(64) NOT NULL DEFAULT '',
  new_value varchar(100) NOT NULL DEFAULT '',
  old_value varchar(100) NOT NULL DEFAULT '',
  recipient_key varchar(80) NOT NULL DEFAULT '',
  siteaccess varchar(100) NOT NULL DEFAULT '',
  source varchar(20) NOT NULL DEFAULT '',
  user_id integer NOT NULL DEFAULT 0,
  wording text
);
CREATE INDEX expmail_consent_log_created ON expmail_consent_log ( created );
CREATE INDEX expmail_consent_log_recipient ON expmail_consent_log ( recipient_key, created );
CREATE INDEX expmail_consent_log_user ON expmail_consent_log ( user_id );
CREATE TABLE expmail_pending (
  category varchar(100) NOT NULL DEFAULT '',
  created integer NOT NULL DEFAULT 0,
  data text,
  expires integer NOT NULL DEFAULT 0,
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  kind varchar(20) NOT NULL DEFAULT '',
  recipient_key varchar(80) NOT NULL DEFAULT '',
  user_id integer NOT NULL DEFAULT 0
);
CREATE INDEX expmail_pending_expires ON expmail_pending ( expires );
CREATE INDEX expmail_pending_recipient ON expmail_pending ( recipient_key, kind );
CREATE TABLE expmail_preference (
  category varchar(100) NOT NULL DEFAULT '',
  created integer NOT NULL DEFAULT 0,
  frequency varchar(20) NOT NULL DEFAULT '',
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  modified integer NOT NULL DEFAULT 0,
  recipient_key varchar(80) NOT NULL DEFAULT '',
  state varchar(20) NOT NULL DEFAULT '',
  user_id integer NOT NULL DEFAULT 0
);
CREATE INDEX expmail_preference_category ON expmail_preference ( category, state );
CREATE UNIQUE INDEX expmail_preference_recipient ON expmail_preference ( recipient_key, category );
CREATE INDEX expmail_preference_user ON expmail_preference ( user_id );
CREATE TABLE expmail_suppression (
  created integer NOT NULL DEFAULT 0,
  created_by integer NOT NULL DEFAULT 0,
  email_hash varchar(64) NOT NULL DEFAULT '',
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  note text,
  reason varchar(30) NOT NULL DEFAULT ''
);
CREATE UNIQUE INDEX expmail_suppression_hash ON expmail_suppression ( email_hash );

-- Personal API keys (doc/guides/api-keys.md).
--
-- One row per key a user made on the API access page (apikey/list). key_prefix is the
-- public part of the key (expk_<id>); the secret is never stored, only secret_hash,
-- HMAC-SHA-256 of the secret keyed with the row's own salt. scopes is a space separated
-- list of the scope ids of rest.ini [ApiKeySettings]. expires, last_used and revoked are
-- timestamps (0 = never / not yet). Written with IF NOT EXISTS, so running these
-- statements again changes nothing.
CREATE TABLE IF NOT EXISTS expapikey (
  created integer NOT NULL DEFAULT 0,
  created_by integer NOT NULL DEFAULT 0,
  expires integer NOT NULL DEFAULT 0,
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  key_prefix varchar(40) NOT NULL DEFAULT '',
  last_ip varchar(64) NOT NULL DEFAULT '',
  last_used integer NOT NULL DEFAULT 0,
  name varchar(255) NOT NULL DEFAULT '',
  revoked integer NOT NULL DEFAULT 0,
  revoked_by integer NOT NULL DEFAULT 0,
  salt varchar(64) NOT NULL DEFAULT '',
  scopes varchar(255) NOT NULL DEFAULT '',
  secret_hash varchar(128) NOT NULL DEFAULT '',
  user_id integer NOT NULL DEFAULT 0
);
CREATE UNIQUE INDEX IF NOT EXISTS expapikey_prefix ON expapikey ( key_prefix );
CREATE INDEX IF NOT EXISTS expapikey_user ON expapikey ( user_id );
