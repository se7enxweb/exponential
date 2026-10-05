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
