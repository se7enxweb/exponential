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
-- ezprest_authcode: PKCE (RFC 7636) for the OAuth authorization code flow.
-- oauth/authorize stores the code_challenge and code_challenge_method of the request with the code; the token end
-- point checks the code_verifier against them. Without these columns no authorization code can be issued.
-- Run once; a second run stops at "duplicate column name", which means the columns are there.
--
ALTER TABLE ezprest_authcode ADD COLUMN code_challenge varchar(128) DEFAULT NULL;
ALTER TABLE ezprest_authcode ADD COLUMN code_challenge_method varchar(10) DEFAULT NULL;

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
-- ezorder.siteaccess: the siteaccess (store or site) an order was placed in
-- (doc/eng-us/features/6.0/order-siteaccess.md). The shop records it when the
-- basket becomes an order; the order view and the order lists show it and
-- filter by it. Orders placed before keep '' and are shown as unknown: which
-- siteaccess they came from is not recorded; bin/php/expbackfillordersiteaccess.php
-- infers it from the content tree where it can (dry run unless --apply).
-- Until the column is there the kernel stores and reads orders without it.
-- Run once; a second run stops at "duplicate column name", which means the column is there.
--
ALTER TABLE ezorder ADD COLUMN siteaccess varchar(255) NOT NULL DEFAULT '';

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

-- Bookmarks of system pages (doc/eng-us/features/6.0/bookmarks-of-system-pages.md).
--
-- A bookmark can point to a page of a module view (setup/systemupgrade, audit/console ...)
-- instead of a node. Such a bookmark is a row of ezcontentbrowsebookmark with node_id 0, so
-- it has a name, a folder and a place in its folder like every other; expbookmark_view holds
-- its address (module_uri, without the siteaccess). New table only: node bookmarks do not
-- change. Written with IF NOT EXISTS, so running these statements again changes nothing.
CREATE TABLE IF NOT EXISTS expbookmark_view (
  bookmark_id integer NOT NULL DEFAULT 0,
  created integer NOT NULL DEFAULT 0,
  module_uri longtext,
  user_id integer NOT NULL DEFAULT 0,
  PRIMARY KEY ( bookmark_id )
);
CREATE INDEX IF NOT EXISTS expbookmark_view_user ON expbookmark_view ( user_id );

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

-- Personal API keys (doc/eng-us/guides/api-keys.md).
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

-- The run history of the import and export tools (doc/eng-us/features/6.0/transfer-runs.md).
--
-- One row per run of a tool of the transfer registry (Export, CIE, Imports ...): tool is the registry id
-- (transfer.xrowextract), direction import or export, kind export, import, dry_run, retry or undo, state queued,
-- running, done, failed or cancelled; the counts, the duration in seconds, the engine that ran it (expqueue,
-- contentjob, process) with its job (job_ref), the tool's own job or run (tool_ref), a file reference relative to
-- the tool (never a path) and the link to the tool's own page of the run. Written with IF NOT EXISTS, so running
-- these statements again changes nothing.
CREATE TABLE IF NOT EXISTS exp_transfer_run (
  bytes integer NOT NULL DEFAULT 0,
  created integer NOT NULL DEFAULT 0,
  detail longtext,
  detail_url varchar(255) NOT NULL DEFAULT '',
  direction varchar(10) NOT NULL DEFAULT '',
  duration integer NOT NULL DEFAULT 0,
  engine varchar(16) NOT NULL DEFAULT '',
  error varchar(255) NOT NULL DEFAULT '',
  file_ref varchar(255) NOT NULL DEFAULT '',
  finished integer NOT NULL DEFAULT 0,
  format varchar(32) NOT NULL DEFAULT '',
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  job_ref varchar(64) NOT NULL DEFAULT '',
  kind varchar(20) NOT NULL DEFAULT '',
  modified integer NOT NULL DEFAULT 0,
  rows_done integer NOT NULL DEFAULT 0,
  rows_failed integer NOT NULL DEFAULT 0,
  rows_total integer NOT NULL DEFAULT 0,
  scope varchar(20) NOT NULL DEFAULT '',
  scope_ref varchar(255) NOT NULL DEFAULT '',
  siteaccess varchar(64) NOT NULL DEFAULT '',
  started integer NOT NULL DEFAULT 0,
  state varchar(16) NOT NULL DEFAULT '',
  title varchar(255) NOT NULL DEFAULT '',
  tool varchar(64) NOT NULL DEFAULT '',
  tool_ref varchar(64) NOT NULL DEFAULT '',
  user_id integer NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS exp_transfer_run_started ON exp_transfer_run ( started );
CREATE INDEX IF NOT EXISTS exp_transfer_run_tool ON exp_transfer_run ( tool, started );
CREATE INDEX IF NOT EXISTS exp_transfer_run_toolref ON exp_transfer_run ( tool, tool_ref );
CREATE INDEX IF NOT EXISTS exp_transfer_run_user ON exp_transfer_run ( user_id );

-- The named database connections of the data tables (doc/eng-us/features/6.0/data-tables.md).
--
-- One row per connection made in the administration (Setup > Data tables > Connections): its identifier, the
-- engine (sqlite, mysql, postgresql, oracle, sqlserver, mongodb), server, port, database (for SQLite the file) and
-- user, and the NAME of the secret that holds its password (never the password). The administration creates the
-- table when it is missing. Written with IF NOT EXISTS, so running these statements again changes nothing.
CREATE TABLE IF NOT EXISTS exp_data_connection (
  created integer NOT NULL DEFAULT 0,
  creator_id integer NOT NULL DEFAULT 0,
  database_name varchar(255) NOT NULL DEFAULT '',
  description varchar(255) NOT NULL DEFAULT '',
  engine varchar(32) NOT NULL DEFAULT '',
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  identifier varchar(64) NOT NULL DEFAULT '',
  modified integer NOT NULL DEFAULT 0,
  modifier_id integer NOT NULL DEFAULT 0,
  name varchar(255) NOT NULL DEFAULT '',
  options longtext,
  port integer NOT NULL DEFAULT 0,
  read_only integer NOT NULL DEFAULT 0,
  secret varchar(128) NOT NULL DEFAULT '',
  server varchar(255) NOT NULL DEFAULT '',
  user_name varchar(255) NOT NULL DEFAULT ''
);
CREATE UNIQUE INDEX IF NOT EXISTS exp_data_connection_identifier ON exp_data_connection ( identifier );

-- The kernel's secret store (doc/eng-us/features/6.0/secret-store.md).
--
-- One row per secret of a tool (a connection's password, an import source's token): its name, the tool it belongs
-- to (scope), and its value encrypted with XChaCha20-Poly1305 under the store's key (key_id names the key, never
-- holds it). The administration (Setup > Secrets) shows names only, and creates the table when it is missing.
-- Written with IF NOT EXISTS, so running these statements again changes nothing.
CREATE TABLE IF NOT EXISTS exp_secret (
  ciphertext longtext,
  created integer NOT NULL DEFAULT 0,
  creator_id integer NOT NULL DEFAULT 0,
  description varchar(255) NOT NULL DEFAULT '',
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  key_id varchar(16) NOT NULL DEFAULT '',
  modified integer NOT NULL DEFAULT 0,
  modifier_id integer NOT NULL DEFAULT 0,
  name varchar(128) NOT NULL DEFAULT '',
  scope varchar(64) NOT NULL DEFAULT ''
);
CREATE UNIQUE INDEX IF NOT EXISTS exp_secret_name ON exp_secret ( name );
CREATE INDEX IF NOT EXISTS exp_secret_scope ON exp_secret ( scope );

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
  created integer NOT NULL DEFAULT 0,
  creator_id integer NOT NULL DEFAULT 0,
  direction varchar(16) NOT NULL DEFAULT '',
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  interval_seconds integer NOT NULL DEFAULT 0,
  key_columns varchar(255) NOT NULL DEFAULT '',
  last_run integer NOT NULL DEFAULT 0,
  last_run_id integer NOT NULL DEFAULT 0,
  map longtext,
  modified integer NOT NULL DEFAULT 0,
  name varchar(255) NOT NULL DEFAULT '',
  next_run integer NOT NULL DEFAULT 0,
  options longtext,
  run_mode varchar(16) NOT NULL DEFAULT '',
  state varchar(16) NOT NULL DEFAULT '',
  subtree_node_id integer NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS exp_data_sync_mode ON exp_data_sync ( run_mode, state );
CREATE TABLE IF NOT EXISTS exp_data_sync_conflict (
  content_values longtext,
  detected integer NOT NULL DEFAULT 0,
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  link_id integer NOT NULL DEFAULT 0,
  object_id integer NOT NULL DEFAULT 0,
  resolution varchar(16) NOT NULL DEFAULT '',
  resolved integer NOT NULL DEFAULT 0,
  resolver_id integer NOT NULL DEFAULT 0,
  row_key varchar(255) NOT NULL DEFAULT '',
  row_values longtext,
  state varchar(16) NOT NULL DEFAULT '',
  sync_id integer NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS exp_data_sync_conflict_state ON exp_data_sync_conflict ( sync_id, state );
CREATE TABLE IF NOT EXISTS exp_data_sync_link (
  content_hash varchar(32) NOT NULL DEFAULT '',
  content_modified integer NOT NULL DEFAULT 0,
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  object_id integer NOT NULL DEFAULT 0,
  row_hash varchar(32) NOT NULL DEFAULT '',
  row_key varchar(255) NOT NULL DEFAULT '',
  row_modified integer NOT NULL DEFAULT 0,
  sync_id integer NOT NULL DEFAULT 0,
  synced integer NOT NULL DEFAULT 0
);
CREATE UNIQUE INDEX IF NOT EXISTS exp_data_sync_link_key ON exp_data_sync_link ( sync_id, row_key );
CREATE INDEX IF NOT EXISTS exp_data_sync_link_object ON exp_data_sync_link ( sync_id, object_id );

-- The signed-in sessions of the users, "Your devices" (doc/eng-us/features/6.0/user-sessions.md).
--
-- One row per sign-in: when it started and was last used, the address, the User-Agent header and what it names, the
-- sign-in method, Remember me, and when and why it ended. The row is found by the SHA-256 of a random token kept in
-- the session; neither the token nor the session id is stored. Without the table nothing is tracked and every page
-- works as before; update/common/scripts/6.0/createusersessiontable.php creates it on any engine.
-- Written with IF NOT EXISTS, so running these statements again changes nothing.
CREATE TABLE IF NOT EXISTS expuser_session (
  browser varchar(64) NOT NULL DEFAULT '',
  created integer NOT NULL DEFAULT 0,
  device_type varchar(16) NOT NULL DEFAULT '',
  end_reason varchar(32) NOT NULL DEFAULT '',
  ended integer NOT NULL DEFAULT 0,
  ended_by integer NOT NULL DEFAULT 0,
  first_ip varchar(64) NOT NULL DEFAULT '',
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  last_active integer NOT NULL DEFAULT 0,
  last_ip varchar(64) NOT NULL DEFAULT '',
  method varchar(32) NOT NULL DEFAULT '',
  os varchar(64) NOT NULL DEFAULT '',
  remember_until integer NOT NULL DEFAULT 0,
  siteaccess varchar(64) NOT NULL DEFAULT '',
  token_hash varchar(64) NOT NULL DEFAULT '',
  user_agent varchar(512) NOT NULL DEFAULT '',
  user_id integer NOT NULL DEFAULT 0
);
CREATE UNIQUE INDEX IF NOT EXISTS expuser_session_token ON expuser_session ( token_hash );
CREATE INDEX IF NOT EXISTS expuser_session_user ON expuser_session ( user_id, ended );
CREATE INDEX IF NOT EXISTS expuser_session_active ON expuser_session ( ended, last_active );

--
-- ezuser.login: one account per login.
--
-- A new installation has a unique index on ezuser.login from the kernel schema.
-- On an upgrade this file does not add it: the statement fails on a database
-- where two accounts have the same login. Add it with the command, which lists
-- the duplicates first (also logins that differ only in case, which signing in
-- cannot tell apart) and adds the index only when there are none:
--
--   ./console exp:user-login-check              (report, changes nothing)
--   ./console exp:user-login-check --fix-index  (adds the index after a clean report)
--
-- What --fix-index runs on SQLite, in one transaction, keeping the name of the
-- index the database has (ezuser_login or idx_ezuser_ezuser_login):
--
-- DROP INDEX ezuser_login;
-- CREATE UNIQUE INDEX ezuser_login ON ezuser ( login );

-- Passkeys (doc/eng-us/guides/passkeys.md).
--
-- One row per passkey (WebAuthn credential) a user registered on user/passkeys. credential_id
-- and public_key are base64url (the COSE key the authenticator sent); credential_hash is the
-- SHA-256 (hex) of the credential id, what a sign-in looks the passkey up by. user_handle is
-- the random handle the browser keeps with the passkey (never the user id). sign_count is the
-- authenticator's signature counter; created and last_used are timestamps (0 = not yet).
-- Written with IF NOT EXISTS, so running these statements again changes nothing.
CREATE TABLE IF NOT EXISTS exppasskey (
  aaguid varchar(36) NOT NULL DEFAULT '',
  algorithm integer NOT NULL DEFAULT 0,
  backup_eligible integer NOT NULL DEFAULT 0,
  backup_state integer NOT NULL DEFAULT 0,
  created integer NOT NULL DEFAULT 0,
  credential_hash varchar(64) NOT NULL DEFAULT '',
  credential_id longtext,
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  last_ip varchar(64) NOT NULL DEFAULT '',
  last_used integer NOT NULL DEFAULT 0,
  name varchar(255) NOT NULL DEFAULT '',
  public_key longtext,
  sign_count integer NOT NULL DEFAULT 0,
  transports varchar(255) NOT NULL DEFAULT '',
  user_handle varchar(128) NOT NULL DEFAULT '',
  user_id integer NOT NULL DEFAULT 0
);
CREATE UNIQUE INDEX IF NOT EXISTS exppasskey_credential ON exppasskey ( credential_hash );
CREATE INDEX IF NOT EXISTS exppasskey_user ON exppasskey ( user_id );

-- Help maintained as content (doc/eng-us/features/6.0/help-content.md): exphelp_help_id records every help_id that
-- was Released and the object it belongs to, so the validator reports a help_id that was renamed or reused (clients
-- call it); exphelp_feedback keeps the answers to "Was this helpful?": the topic, yes or no, a reason from a fixed
-- list and the day, nothing about who answered. Without the tables both features are off and every page works as
-- before; ./console exp:help:content install creates them on any engine.
-- Written with IF NOT EXISTS, so running these statements again changes nothing.
CREATE TABLE IF NOT EXISTS exphelp_help_id (
  help_id varchar(80) NOT NULL DEFAULT '' PRIMARY KEY,
  object_id integer NOT NULL DEFAULT '0',
  released integer NOT NULL DEFAULT '0'
);
CREATE INDEX IF NOT EXISTS exphelp_help_id_object ON exphelp_help_id ( object_id );
CREATE TABLE IF NOT EXISTS exphelp_feedback (
  created integer NOT NULL DEFAULT '0',
  help_id varchar(80) NOT NULL DEFAULT '',
  helpful integer NOT NULL DEFAULT '0',
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  language varchar(20) NOT NULL DEFAULT '',
  object_id integer NOT NULL DEFAULT '0',
  reason varchar(32) NOT NULL DEFAULT ''
);
CREATE INDEX IF NOT EXISTS exphelp_feedback_object ON exphelp_feedback ( object_id, created );
CREATE INDEX IF NOT EXISTS exphelp_feedback_created ON exphelp_feedback ( created );

-- expuser_audit: the user audit log (doc/eng-us/features/6.0/user-audit.md). One row per security event about a user
-- account or a user group (a change with its fields before and after, a sign-in, a refused attempt, a role
-- assignment): who, when, where (siteaccess, host, the address as the audit's privacy rules leave it), whom and what.
-- Filled from the audit records of the access channel; read on user/audit. Never a password, a hash or a secret.
-- Written with IF NOT EXISTS, so running these statements again changes nothing.
CREATE TABLE IF NOT EXISTS expuser_audit (
  actor_id integer NOT NULL DEFAULT '0',
  actor_login varchar(150) NOT NULL DEFAULT '',
  client_ip varchar(64) NOT NULL DEFAULT '',
  created integer NOT NULL DEFAULT '0',
  event_id varchar(26) NOT NULL DEFAULT '',
  event_name varchar(128) NOT NULL DEFAULT '',
  host varchar(255) NOT NULL DEFAULT '',
  id integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  module_view varchar(128) NOT NULL DEFAULT '',
  reason varchar(64) NOT NULL DEFAULT '',
  request_id varchar(40) NOT NULL DEFAULT '',
  result varchar(16) NOT NULL DEFAULT '',
  siteaccess varchar(64) NOT NULL DEFAULT '',
  target_id integer NOT NULL DEFAULT '0',
  target_login varchar(150) NOT NULL DEFAULT '',
  target_type varchar(16) NOT NULL DEFAULT '',
  time_ms integer NOT NULL DEFAULT '0',
  value_after longtext,
  value_before longtext
);
CREATE UNIQUE INDEX IF NOT EXISTS expuser_audit_event ON expuser_audit ( event_id );
CREATE INDEX IF NOT EXISTS expuser_audit_target ON expuser_audit ( target_id, created );
CREATE INDEX IF NOT EXISTS expuser_audit_actor ON expuser_audit ( actor_id, created );
CREATE INDEX IF NOT EXISTS expuser_audit_name ON expuser_audit ( event_name, created );
CREATE INDEX IF NOT EXISTS expuser_audit_created ON expuser_audit ( created );

-- expupgrade_step: the record of exp:upgrade (doc/eng-us/guides/upgrading-with-the-console.md). One
-- row per upgrade step it applied or tried. Written with IF NOT EXISTS, so running this statement
-- again changes nothing.
CREATE TABLE IF NOT EXISTS expupgrade_step (
  applied_at integer NOT NULL DEFAULT '0',
  applied_by varchar(150) NOT NULL DEFAULT '',
  checksum varchar(64) NOT NULL DEFAULT '',
  duration_ms integer NOT NULL DEFAULT '0',
  engine varchar(30) NOT NULL DEFAULT '',
  message varchar(1000) NOT NULL DEFAULT '',
  stage varchar(20) NOT NULL DEFAULT '',
  state varchar(20) NOT NULL DEFAULT '',
  step_id varchar(150) NOT NULL DEFAULT '' PRIMARY KEY,
  version varchar(30) NOT NULL DEFAULT ''
);
