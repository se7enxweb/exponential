-- Written by expDb2Schema for Db2 11.5 and 12.1 (db2 -tvf <file>), for a database of PAGESIZE 32 K and CODESET UTF-8

CREATE TABLE expaudit_cursor (
  byte_offset BIGINT DEFAULT 0 NOT NULL,
  channel VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  file_name VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  last_hash VARCHAR(80 CODEUNITS32) DEFAULT NULL,
  last_seq INTEGER DEFAULT 0 NOT NULL,
  updated_ms BIGINT DEFAULT 0 NOT NULL
);
ALTER TABLE expaudit_cursor ADD CONSTRAINT PK_EXPAUDIT_CURSOR PRIMARY KEY (channel, file_name);

CREATE TABLE expaudit_event (
  channel VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  depth INTEGER DEFAULT 0 NOT NULL,
  domain_name VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  engine VARCHAR(16 CODEUNITS32) DEFAULT NULL,
  file_name VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  id VARCHAR(26 CODEUNITS32) DEFAULT '' NOT NULL,
  imported INTEGER DEFAULT 0 NOT NULL,
  ip VARCHAR(64 CODEUNITS32) DEFAULT NULL,
  job_id VARCHAR(32 CODEUNITS32) DEFAULT NULL,
  login VARCHAR(150 CODEUNITS32) DEFAULT NULL,
  module_view VARCHAR(128 CODEUNITS32) DEFAULT NULL,
  name VARCHAR(128 CODEUNITS32) DEFAULT '' NOT NULL,
  object_id VARCHAR(64 CODEUNITS32) DEFAULT NULL,
  object_name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  object_type VARCHAR(32 CODEUNITS32) DEFAULT NULL,
  parent_id VARCHAR(26 CODEUNITS32) DEFAULT NULL,
  pseudonymised INTEGER DEFAULT 0 NOT NULL,
  reason VARCHAR(32 CODEUNITS32) DEFAULT NULL,
  record CLOB(1G),
  request_id VARCHAR(40 CODEUNITS32) DEFAULT NULL,
  result VARCHAR(8 CODEUNITS32) DEFAULT NULL,
  run_id VARCHAR(40 CODEUNITS32) DEFAULT NULL,
  search_text VARCHAR(32672) DEFAULT NULL,
  seq INTEGER DEFAULT 0 NOT NULL,
  session_h VARCHAR(24 CODEUNITS32) DEFAULT NULL,
  severity INTEGER DEFAULT 0 NOT NULL,
  siteaccess VARCHAR(64 CODEUNITS32) DEFAULT NULL,
  target_id VARCHAR(64 CODEUNITS32) DEFAULT NULL,
  target_type VARCHAR(32 CODEUNITS32) DEFAULT NULL,
  time_ms BIGINT DEFAULT 0 NOT NULL,
  ua VARCHAR(128 CODEUNITS32) DEFAULT NULL,
  user_id INTEGER DEFAULT NULL,
  verb VARCHAR(32 CODEUNITS32) DEFAULT NULL
);
COMMENT ON COLUMN expaudit_event.id IS 'exp:char';
COMMENT ON COLUMN expaudit_event.parent_id IS 'exp:char';
COMMENT ON COLUMN expaudit_event.record IS 'exp:clob';
ALTER TABLE expaudit_event ADD CONSTRAINT PK_EXPAUDIT_EVENT PRIMARY KEY (id);
CREATE INDEX expaudit_event_domain ON expaudit_event (domain_name, severity, time_ms);
CREATE UNIQUE INDEX expaudit_event_file_seq ON expaudit_event (channel, file_name, seq);
CREATE INDEX expaudit_event_ip ON expaudit_event (ip, time_ms);
CREATE INDEX expaudit_event_job ON expaudit_event (job_id);
CREATE INDEX expaudit_event_name ON expaudit_event (name, time_ms);
CREATE INDEX expaudit_event_object ON expaudit_event (object_type, object_id, time_ms);
CREATE INDEX expaudit_event_parent ON expaudit_event (parent_id);
CREATE INDEX expaudit_event_request ON expaudit_event (request_id);
CREATE INDEX expaudit_event_result ON expaudit_event (result, time_ms);
CREATE INDEX expaudit_event_time ON expaudit_event (time_ms);
CREATE INDEX expaudit_event_user ON expaudit_event (user_id, time_ms);

CREATE TABLE expaudit_file (
  archive_path VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  break_line INTEGER DEFAULT 0 NOT NULL,
  channel VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  file_name VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  records INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(16 CODEUNITS32) DEFAULT 'live' NOT NULL,
  verified VARCHAR(16 CODEUNITS32) DEFAULT 'unchecked' NOT NULL,
  verified_ms BIGINT DEFAULT 0 NOT NULL
);
ALTER TABLE expaudit_file ADD CONSTRAINT PK_EXPAUDIT_FILE PRIMARY KEY (channel, file_name);

CREATE TABLE expmail_category (
  created INTEGER DEFAULT 0 NOT NULL,
  default_on INTEGER DEFAULT 0 NOT NULL,
  description VARCHAR(32672) DEFAULT NULL,
  double_opt_in INTEGER DEFAULT 0 NOT NULL,
  essential INTEGER DEFAULT 0 NOT NULL,
  frequencies VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  handler_class VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  identifier VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expmail_category ADD CONSTRAINT PK_EXPMAIL_CATEGORY PRIMARY KEY (id);
CREATE UNIQUE INDEX expmail_category_identifier ON expmail_category (identifier);
CREATE SEQUENCE S_EXPMAIL_CATEGORY AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXPMAIL_CATEGORY IS 'expmail_category.id';
CREATE TRIGGER T_EXPMAIL_CATEGORY_BI NO CASCADE BEFORE INSERT ON expmail_category REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXPMAIL_CATEGORY;

CREATE TABLE expmail_consent_log (
  action VARCHAR(30 CODEUNITS32) DEFAULT '' NOT NULL,
  actor_user_id INTEGER DEFAULT 0 NOT NULL,
  anonymised INTEGER DEFAULT 0 NOT NULL,
  category VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  email VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  ip VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  new_value VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  old_value VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  recipient_key VARCHAR(80 CODEUNITS32) DEFAULT '' NOT NULL,
  siteaccess VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  source VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL,
  wording VARCHAR(32672) DEFAULT NULL
);
ALTER TABLE expmail_consent_log ADD CONSTRAINT PK_EXPMAIL_CONSENT_LOG PRIMARY KEY (id);
CREATE INDEX expmail_consent_log_created ON expmail_consent_log (created);
CREATE INDEX expmail_consent_log_recipient ON expmail_consent_log (recipient_key, created);
CREATE INDEX expmail_consent_log_user ON expmail_consent_log (user_id);
CREATE SEQUENCE S_EXPMAIL_CONSENT_LOG AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXPMAIL_CONSENT_LOG IS 'expmail_consent_log.id';
CREATE TRIGGER T_EXPMAIL_CONSENT_LOG_BI NO CASCADE BEFORE INSERT ON expmail_consent_log REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXPMAIL_CONSENT_LOG;

CREATE TABLE expmail_pending (
  category VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  data VARCHAR(32672) DEFAULT NULL,
  expires INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  kind VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  recipient_key VARCHAR(80 CODEUNITS32) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expmail_pending ADD CONSTRAINT PK_EXPMAIL_PENDING PRIMARY KEY (id);
CREATE INDEX expmail_pending_expires ON expmail_pending (expires);
CREATE INDEX expmail_pending_recipient ON expmail_pending (recipient_key, kind);
CREATE SEQUENCE S_EXPMAIL_PENDING AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXPMAIL_PENDING IS 'expmail_pending.id';
CREATE TRIGGER T_EXPMAIL_PENDING_BI NO CASCADE BEFORE INSERT ON expmail_pending REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXPMAIL_PENDING;

CREATE TABLE expmail_preference (
  category VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  frequency VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  recipient_key VARCHAR(80 CODEUNITS32) DEFAULT '' NOT NULL,
  state VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expmail_preference ADD CONSTRAINT PK_EXPMAIL_PREFERENCE PRIMARY KEY (id);
CREATE INDEX expmail_preference_category ON expmail_preference (category, state);
CREATE UNIQUE INDEX expmail_preference_recipient ON expmail_preference (recipient_key, category);
CREATE INDEX expmail_preference_user ON expmail_preference (user_id);
CREATE SEQUENCE S_EXPMAIL_PREFERENCE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXPMAIL_PREFERENCE IS 'expmail_preference.id';
CREATE TRIGGER T_EXPMAIL_PREFERENCE_BI NO CASCADE BEFORE INSERT ON expmail_preference REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXPMAIL_PREFERENCE;

CREATE TABLE expmail_suppression (
  created INTEGER DEFAULT 0 NOT NULL,
  created_by INTEGER DEFAULT 0 NOT NULL,
  email_hash VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  note VARCHAR(32672) DEFAULT NULL,
  reason VARCHAR(30 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE expmail_suppression ADD CONSTRAINT PK_EXPMAIL_SUPPRESSION PRIMARY KEY (id);
CREATE UNIQUE INDEX expmail_suppression_hash ON expmail_suppression (email_hash);
CREATE SEQUENCE S_EXPMAIL_SUPPRESSION AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXPMAIL_SUPPRESSION IS 'expmail_suppression.id';
CREATE TRIGGER T_EXPMAIL_SUPPRESSION_BI NO CASCADE BEFORE INSERT ON expmail_suppression REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXPMAIL_SUPPRESSION;

CREATE TABLE ezapprove_items (
  collaboration_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  workflow_process_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezapprove_items ADD CONSTRAINT PK_EZAPPROVE_ITEMS PRIMARY KEY (id);
CREATE SEQUENCE S_EZAPPROVE_ITEMS AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZAPPROVE_ITEMS IS 'ezapprove_items.id';
CREATE TRIGGER T_EZAPPROVE_ITEMS_BI NO CASCADE BEFORE INSERT ON ezapprove_items REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZAPPROVE_ITEMS;

CREATE TABLE ezbasket (
  id INTEGER NOT NULL,
  order_id INTEGER DEFAULT 0 NOT NULL,
  productcollection_id INTEGER DEFAULT 0 NOT NULL,
  session_id VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezbasket ADD CONSTRAINT PK_EZBASKET PRIMARY KEY (id);
CREATE INDEX ezbasket_session_id ON ezbasket (session_id);
CREATE SEQUENCE S_EZBASKET AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZBASKET IS 'ezbasket.id';
CREATE TRIGGER T_EZBASKET_BI NO CASCADE BEFORE INSERT ON ezbasket REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZBASKET;

CREATE TABLE ezbinaryfile (
  contentobject_attribute_id INTEGER DEFAULT 0 NOT NULL,
  download_count INTEGER DEFAULT 0 NOT NULL,
  filename VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  mime_type VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  original_filename VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezbinaryfile ADD CONSTRAINT PK_EZBINARYFILE PRIMARY KEY (contentobject_attribute_id, version);

CREATE TABLE ezcobj_state (
  default_language_id BIGINT DEFAULT 0 NOT NULL,
  group_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  identifier VARCHAR(45 CODEUNITS32) DEFAULT '' NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state ADD CONSTRAINT PK_EZCOBJ_STATE PRIMARY KEY (id);
CREATE UNIQUE INDEX ezcobj_state_identifier ON ezcobj_state (group_id, identifier);
CREATE INDEX ezcobj_state_lmask ON ezcobj_state (language_mask);
CREATE INDEX ezcobj_state_priority ON ezcobj_state (priority);
CREATE SEQUENCE S_EZCOBJ_STATE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCOBJ_STATE IS 'ezcobj_state.id';
CREATE TRIGGER T_EZCOBJ_STATE_BI NO CASCADE BEFORE INSERT ON ezcobj_state REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCOBJ_STATE;

CREATE TABLE ezcobj_state_group (
  default_language_id BIGINT DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  identifier VARCHAR(45 CODEUNITS32) DEFAULT '' NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state_group ADD CONSTRAINT PK_EZCOBJ_STATE_GROUP PRIMARY KEY (id);
CREATE UNIQUE INDEX ezcobj_state_group_identifier ON ezcobj_state_group (identifier);
CREATE INDEX ezcobj_state_group_lmask ON ezcobj_state_group (language_mask);
CREATE SEQUENCE S_EZCOBJ_STATE_GROUP AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCOBJ_STATE_GROUP IS 'ezcobj_state_group.id';
CREATE TRIGGER T_EZCOBJ_STATE_GROUP_BI NO CASCADE BEFORE INSERT ON ezcobj_state_group REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCOBJ_STATE_GROUP;

CREATE TABLE ezcobj_state_group_language (
  contentobject_state_group_id INTEGER DEFAULT 0 NOT NULL,
  description VARCHAR(32672) NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  name VARCHAR(45 CODEUNITS32) DEFAULT '' NOT NULL,
  real_language_id BIGINT DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state_group_language ADD CONSTRAINT PK_EZCOBJ_STATE_GROUP_LANGUAGE PRIMARY KEY (contentobject_state_group_id, real_language_id);

CREATE TABLE ezcobj_state_language (
  contentobject_state_id INTEGER DEFAULT 0 NOT NULL,
  description VARCHAR(32672) NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  name VARCHAR(45 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezcobj_state_language ADD CONSTRAINT PK_EZCOBJ_STATE_LANGUAGE PRIMARY KEY (contentobject_state_id, language_id);

CREATE TABLE ezcobj_state_link (
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_state_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state_link ADD CONSTRAINT PK_EZCOBJ_STATE_LINK PRIMARY KEY (contentobject_id, contentobject_state_id);

CREATE TABLE ezcollab_group (
  created INTEGER DEFAULT 0 NOT NULL,
  depth INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  is_open INTEGER DEFAULT 1 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  parent_group_id INTEGER DEFAULT 0 NOT NULL,
  path_string VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL,
  title VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_group ADD CONSTRAINT PK_EZCOLLAB_GROUP PRIMARY KEY (id);
CREATE INDEX ezcollab_group_depth ON ezcollab_group (depth);
CREATE INDEX ezcollab_group_path ON ezcollab_group (path_string);
CREATE SEQUENCE S_EZCOLLAB_GROUP AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCOLLAB_GROUP IS 'ezcollab_group.id';
CREATE TRIGGER T_EZCOLLAB_GROUP_BI NO CASCADE BEFORE INSERT ON ezcollab_group REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCOLLAB_GROUP;

CREATE TABLE ezcollab_item (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  data_float1 REAL DEFAULT 0 NOT NULL,
  data_float2 REAL DEFAULT 0 NOT NULL,
  data_float3 REAL DEFAULT 0 NOT NULL,
  data_int1 INTEGER DEFAULT 0 NOT NULL,
  data_int2 INTEGER DEFAULT 0 NOT NULL,
  data_int3 INTEGER DEFAULT 0 NOT NULL,
  data_text1 VARCHAR(32672) NOT NULL,
  data_text2 VARCHAR(32672) NOT NULL,
  data_text3 VARCHAR(32672) NOT NULL,
  id INTEGER NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  status INTEGER DEFAULT 1 NOT NULL,
  type_identifier VARCHAR(40 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezcollab_item ADD CONSTRAINT PK_EZCOLLAB_ITEM PRIMARY KEY (id);
CREATE SEQUENCE S_EZCOLLAB_ITEM AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCOLLAB_ITEM IS 'ezcollab_item.id';
CREATE TRIGGER T_EZCOLLAB_ITEM_BI NO CASCADE BEFORE INSERT ON ezcollab_item REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCOLLAB_ITEM;

CREATE TABLE ezcollab_item_group_link (
  collaboration_id INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  group_id INTEGER DEFAULT 0 NOT NULL,
  is_active INTEGER DEFAULT 1 NOT NULL,
  is_read INTEGER DEFAULT 0 NOT NULL,
  last_read INTEGER DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_item_group_link ADD CONSTRAINT PK_EZCOLLAB_ITEM_GROUP_LINK PRIMARY KEY (collaboration_id, group_id, user_id);

CREATE TABLE ezcollab_item_message_link (
  collaboration_id INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  message_id INTEGER DEFAULT 0 NOT NULL,
  message_type INTEGER DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  participant_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_item_message_link ADD CONSTRAINT PK_EZCOLLAB_ITEM_MESSAGE_LINK PRIMARY KEY (id);
CREATE SEQUENCE S_EZCOLLAB_ITEM_MESSAGE_LINK AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCOLLAB_ITEM_MESSAGE_LINK IS 'ezcollab_item_message_link.id';
CREATE TRIGGER T_EZCOLLAB_ITEM_MESSAGE_LINK_BI NO CASCADE BEFORE INSERT ON ezcollab_item_message_link REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCOLLAB_ITEM_MESSAGE_LINK;

CREATE TABLE ezcollab_item_participant_link (
  collaboration_id INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  is_active INTEGER DEFAULT 1 NOT NULL,
  is_read INTEGER DEFAULT 0 NOT NULL,
  last_read INTEGER DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  participant_id INTEGER DEFAULT 0 NOT NULL,
  participant_role INTEGER DEFAULT 1 NOT NULL,
  participant_type INTEGER DEFAULT 1 NOT NULL
);
ALTER TABLE ezcollab_item_participant_link ADD CONSTRAINT PK_EZCOLLAB_ITEM_PARTICIPANT_LINK PRIMARY KEY (collaboration_id, participant_id);

CREATE TABLE ezcollab_item_status (
  collaboration_id INTEGER DEFAULT 0 NOT NULL,
  is_active INTEGER DEFAULT 1 NOT NULL,
  is_read INTEGER DEFAULT 0 NOT NULL,
  last_read INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_item_status ADD CONSTRAINT PK_EZCOLLAB_ITEM_STATUS PRIMARY KEY (collaboration_id, user_id);

CREATE TABLE ezcollab_notification_rule (
  collab_identifier VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  user_id VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezcollab_notification_rule ADD CONSTRAINT PK_EZCOLLAB_NOTIFICATION_RULE PRIMARY KEY (id);
CREATE SEQUENCE S_EZCOLLAB_NOTIFICATION_RULE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCOLLAB_NOTIFICATION_RULE IS 'ezcollab_notification_rule.id';
CREATE TRIGGER T_EZCOLLAB_NOTIFICATION_RULE_BI NO CASCADE BEFORE INSERT ON ezcollab_notification_rule REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCOLLAB_NOTIFICATION_RULE;

CREATE TABLE ezcollab_profile (
  created INTEGER DEFAULT 0 NOT NULL,
  data_text1 VARCHAR(32672) NOT NULL,
  id INTEGER NOT NULL,
  main_group INTEGER DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_profile ADD CONSTRAINT PK_EZCOLLAB_PROFILE PRIMARY KEY (id);
CREATE SEQUENCE S_EZCOLLAB_PROFILE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCOLLAB_PROFILE IS 'ezcollab_profile.id';
CREATE TRIGGER T_EZCOLLAB_PROFILE_BI NO CASCADE BEFORE INSERT ON ezcollab_profile REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCOLLAB_PROFILE;

CREATE TABLE ezcollab_simple_message (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  data_float1 REAL DEFAULT 0 NOT NULL,
  data_float2 REAL DEFAULT 0 NOT NULL,
  data_float3 REAL DEFAULT 0 NOT NULL,
  data_int1 INTEGER DEFAULT 0 NOT NULL,
  data_int2 INTEGER DEFAULT 0 NOT NULL,
  data_int3 INTEGER DEFAULT 0 NOT NULL,
  data_text1 VARCHAR(32672) NOT NULL,
  data_text2 VARCHAR(32672) NOT NULL,
  data_text3 VARCHAR(32672) NOT NULL,
  id INTEGER NOT NULL,
  message_type VARCHAR(40 CODEUNITS32) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_simple_message ADD CONSTRAINT PK_EZCOLLAB_SIMPLE_MESSAGE PRIMARY KEY (id);
CREATE SEQUENCE S_EZCOLLAB_SIMPLE_MESSAGE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCOLLAB_SIMPLE_MESSAGE IS 'ezcollab_simple_message.id';
CREATE TRIGGER T_EZCOLLAB_SIMPLE_MESSAGE_BI NO CASCADE BEFORE INSERT ON ezcollab_simple_message REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCOLLAB_SIMPLE_MESSAGE;

CREATE TABLE ezcontent_language (
  disabled INTEGER DEFAULT 0 NOT NULL,
  id BIGINT DEFAULT 0 NOT NULL,
  locale VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezcontent_language ADD CONSTRAINT PK_EZCONTENT_LANGUAGE PRIMARY KEY (id);
CREATE INDEX ezcontent_language_name ON ezcontent_language (name);

CREATE TABLE ezcontentbrowsebookmark (
  id INTEGER NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL,
  folder_id INTEGER DEFAULT 0 NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentbrowsebookmark ADD CONSTRAINT PK_EZCONTENTBROWSEBOOKMARK PRIMARY KEY (id);
CREATE INDEX ezcontentbrowsebookmark_folder ON ezcontentbrowsebookmark (user_id, folder_id);
CREATE INDEX ezcontentbrowsebookmark_user ON ezcontentbrowsebookmark (user_id);
CREATE SEQUENCE S_EZCONTENTBROWSEBOOKMARK AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCONTENTBROWSEBOOKMARK IS 'ezcontentbrowsebookmark.id';
CREATE TRIGGER T_EZCONTENTBROWSEBOOKMARK_BI NO CASCADE BEFORE INSERT ON ezcontentbrowsebookmark REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCONTENTBROWSEBOOKMARK;

CREATE TABLE expbookmark_folder (
  created INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  parent_id INTEGER DEFAULT 0 NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expbookmark_folder ADD CONSTRAINT PK_EXPBOOKMARK_FOLDER PRIMARY KEY (id);
CREATE INDEX expbookmark_folder_user ON expbookmark_folder (user_id, parent_id);
CREATE SEQUENCE S_EXPBOOKMARK_FOLDER AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXPBOOKMARK_FOLDER IS 'expbookmark_folder.id';
CREATE TRIGGER T_EXPBOOKMARK_FOLDER_BI NO CASCADE BEFORE INSERT ON expbookmark_folder REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXPBOOKMARK_FOLDER;

CREATE TABLE expbookmark_view (
  bookmark_id INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  module_name VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  module_uri VARCHAR(32672),
  user_id INTEGER DEFAULT 0 NOT NULL,
  view_name VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  view_parameters VARCHAR(32672)
);
ALTER TABLE expbookmark_view ADD CONSTRAINT PK_EXPBOOKMARK_VIEW PRIMARY KEY (bookmark_id);
CREATE INDEX expbookmark_view_module ON expbookmark_view (module_name, view_name);
CREATE INDEX expbookmark_view_user ON expbookmark_view (user_id);

CREATE TABLE ezcontentbrowserecent (
  created INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentbrowserecent ADD CONSTRAINT PK_EZCONTENTBROWSERECENT PRIMARY KEY (id);
CREATE INDEX ezcontentbrowserecent_user ON ezcontentbrowserecent (user_id);
CREATE SEQUENCE S_EZCONTENTBROWSERECENT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCONTENTBROWSERECENT IS 'ezcontentbrowserecent.id';
CREATE TRIGGER T_EZCONTENTBROWSERECENT_BI NO CASCADE BEFORE INSERT ON ezcontentbrowserecent REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCONTENTBROWSERECENT;

CREATE TABLE ezcontentclass (
  always_available INTEGER DEFAULT 0 NOT NULL,
  contentobject_name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  identifier VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  initial_language_id BIGINT DEFAULT 0 NOT NULL,
  is_container INTEGER DEFAULT 0 NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  remote_id VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  serialized_description_list VARCHAR(32672),
  serialized_name_list VARCHAR(32672),
  sort_field INTEGER DEFAULT 1 NOT NULL,
  sort_order INTEGER DEFAULT 1 NOT NULL,
  url_alias_name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentclass ADD CONSTRAINT PK_EZCONTENTCLASS PRIMARY KEY (id, version);
CREATE INDEX ezcontentclass_version ON ezcontentclass (version);
CREATE INDEX ezcontentclass_identifier ON ezcontentclass (identifier, version);
CREATE SEQUENCE S_EZCONTENTCLASS AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCONTENTCLASS IS 'ezcontentclass.id';
CREATE TRIGGER T_EZCONTENTCLASS_BI NO CASCADE BEFORE INSERT ON ezcontentclass REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCONTENTCLASS;

CREATE TABLE ezcontentclass_attribute (
  can_translate INTEGER DEFAULT 1,
  category VARCHAR(25 CODEUNITS32) DEFAULT '' NOT NULL,
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  data_float1 DOUBLE DEFAULT NULL,
  data_float2 DOUBLE DEFAULT NULL,
  data_float3 DOUBLE DEFAULT NULL,
  data_float4 DOUBLE DEFAULT NULL,
  data_int1 INTEGER DEFAULT NULL,
  data_int2 INTEGER DEFAULT NULL,
  data_int3 INTEGER DEFAULT NULL,
  data_int4 INTEGER DEFAULT NULL,
  data_text1 VARCHAR(50 CODEUNITS32) DEFAULT NULL,
  data_text2 VARCHAR(50 CODEUNITS32) DEFAULT NULL,
  data_text3 VARCHAR(50 CODEUNITS32) DEFAULT NULL,
  data_text4 VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  data_text5 VARCHAR(32672),
  data_type_string VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  identifier VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  is_information_collector INTEGER DEFAULT 0 NOT NULL,
  is_required INTEGER DEFAULT 0 NOT NULL,
  is_searchable INTEGER DEFAULT 0 NOT NULL,
  placement INTEGER DEFAULT 0 NOT NULL,
  serialized_data_text VARCHAR(32672),
  serialized_description_list VARCHAR(32672),
  serialized_name_list VARCHAR(32672) NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentclass_attribute ADD CONSTRAINT PK_EZCONTENTCLASS_ATTRIBUTE PRIMARY KEY (id, version);
CREATE INDEX ezcontentclass_attribute_ezcontentclass_attr_ccid ON ezcontentclass_attribute (contentclass_id);
COMMENT ON INDEX ezcontentclass_attribute_ezcontentclass_attr_ccid IS 'exp:index:ezcontentclass_attr_ccid';
CREATE SEQUENCE S_EZCONTENTCLASS_ATTRIBUTE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCONTENTCLASS_ATTRIBUTE IS 'ezcontentclass_attribute.id';
CREATE TRIGGER T_EZCONTENTCLASS_ATTRIBUTE_BI NO CASCADE BEFORE INSERT ON ezcontentclass_attribute REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCONTENTCLASS_ATTRIBUTE;

CREATE TABLE ezcontentclass_classgroup (
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  contentclass_version INTEGER DEFAULT 0 NOT NULL,
  group_id INTEGER DEFAULT 0 NOT NULL,
  group_name VARCHAR(255 CODEUNITS32) DEFAULT NULL
);
ALTER TABLE ezcontentclass_classgroup ADD CONSTRAINT PK_EZCONTENTCLASS_CLASSGROUP PRIMARY KEY (contentclass_id, contentclass_version, group_id);

CREATE TABLE ezcontentclass_name (
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  contentclass_version INTEGER DEFAULT 0 NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  language_locale VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezcontentclass_name ADD CONSTRAINT PK_EZCONTENTCLASS_NAME PRIMARY KEY (contentclass_id, contentclass_version, language_id);

CREATE TABLE ezcontentclassgroup (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT NULL
);
ALTER TABLE ezcontentclassgroup ADD CONSTRAINT PK_EZCONTENTCLASSGROUP PRIMARY KEY (id);
CREATE SEQUENCE S_EZCONTENTCLASSGROUP AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCONTENTCLASSGROUP IS 'ezcontentclassgroup.id';
CREATE TRIGGER T_EZCONTENTCLASSGROUP_BI NO CASCADE BEFORE INSERT ON ezcontentclassgroup REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCONTENTCLASSGROUP;

CREATE TABLE ezcontentobject (
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  current_version INTEGER DEFAULT NULL,
  id INTEGER NOT NULL,
  initial_language_id BIGINT DEFAULT 0 NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  owner_id INTEGER DEFAULT 0 NOT NULL,
  published INTEGER DEFAULT 0 NOT NULL,
  remote_id VARCHAR(100 CODEUNITS32) DEFAULT NULL,
  section_id INTEGER DEFAULT 0 NOT NULL,
  status INTEGER DEFAULT 0
);
ALTER TABLE ezcontentobject ADD CONSTRAINT PK_EZCONTENTOBJECT PRIMARY KEY (id);
CREATE INDEX ezcontentobject_classid ON ezcontentobject (contentclass_id);
CREATE INDEX ezcontentobject_currentversion ON ezcontentobject (current_version);
CREATE INDEX ezcontentobject_lmask ON ezcontentobject (language_mask);
CREATE INDEX ezcontentobject_modified ON ezcontentobject (modified);
CREATE INDEX ezcontentobject_owner ON ezcontentobject (owner_id);
CREATE INDEX ezcontentobject_pub ON ezcontentobject (published);
CREATE UNIQUE INDEX ezcontentobject_remote_id ON ezcontentobject (remote_id);
CREATE INDEX ezcontentobject_status ON ezcontentobject (status);
CREATE SEQUENCE S_EZCONTENTOBJECT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCONTENTOBJECT IS 'ezcontentobject.id';
CREATE TRIGGER T_EZCONTENTOBJECT_BI NO CASCADE BEFORE INSERT ON ezcontentobject REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCONTENTOBJECT;

CREATE TABLE ezcontentobject_attribute (
  attribute_original_id INTEGER DEFAULT 0,
  contentclassattribute_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  data_float DOUBLE DEFAULT NULL,
  data_int INTEGER DEFAULT NULL,
  data_text CLOB(1G),
  data_type_string VARCHAR(50 CODEUNITS32) DEFAULT '',
  id INTEGER NOT NULL,
  language_code VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  sort_key_int INTEGER DEFAULT 0 NOT NULL,
  sort_key_string VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
COMMENT ON COLUMN ezcontentobject_attribute.data_text IS 'exp:clob';
ALTER TABLE ezcontentobject_attribute ADD CONSTRAINT PK_EZCONTENTOBJECT_ATTRIBUTE PRIMARY KEY (id, version);
CREATE INDEX ezcontentobject_attribute_co_id_ver_lang_code ON ezcontentobject_attribute (contentobject_id, version, language_code);
CREATE INDEX ezcontentobject_attribute_language_code ON ezcontentobject_attribute (language_code);
CREATE INDEX ezcontentobject_attribute_ezcontentobject_classattr_id ON ezcontentobject_attribute (contentclassattribute_id);
COMMENT ON INDEX ezcontentobject_attribute_ezcontentobject_classattr_id IS 'exp:index:ezcontentobject_classattr_id';
CREATE INDEX ezcontentobject_attribute_sort_key_int ON ezcontentobject_attribute (sort_key_int);
COMMENT ON INDEX ezcontentobject_attribute_sort_key_int IS 'exp:index:sort_key_int';
CREATE INDEX ezcontentobject_attribute_sort_key_string ON ezcontentobject_attribute (sort_key_string);
COMMENT ON INDEX ezcontentobject_attribute_sort_key_string IS 'exp:index:sort_key_string';
CREATE SEQUENCE S_EZCONTENTOBJECT_ATTRIBUTE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCONTENTOBJECT_ATTRIBUTE IS 'ezcontentobject_attribute.id';
CREATE TRIGGER T_EZCONTENTOBJECT_ATTRIBUTE_BI NO CASCADE BEFORE INSERT ON ezcontentobject_attribute REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCONTENTOBJECT_ATTRIBUTE;

CREATE TABLE ezcontentobject_link (
  contentclassattribute_id INTEGER DEFAULT 0 NOT NULL,
  from_contentobject_id INTEGER DEFAULT 0 NOT NULL,
  from_contentobject_version INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  relation_type INTEGER DEFAULT 1 NOT NULL,
  to_contentobject_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentobject_link ADD CONSTRAINT PK_EZCONTENTOBJECT_LINK PRIMARY KEY (id);
CREATE INDEX ezcontentobject_link_ezco_link_from ON ezcontentobject_link (from_contentobject_id, from_contentobject_version, contentclassattribute_id);
COMMENT ON INDEX ezcontentobject_link_ezco_link_from IS 'exp:index:ezco_link_from';
CREATE INDEX ezcontentobject_link_ezco_link_to_co_id ON ezcontentobject_link (to_contentobject_id);
COMMENT ON INDEX ezcontentobject_link_ezco_link_to_co_id IS 'exp:index:ezco_link_to_co_id';
CREATE SEQUENCE S_EZCONTENTOBJECT_LINK AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCONTENTOBJECT_LINK IS 'ezcontentobject_link.id';
CREATE TRIGGER T_EZCONTENTOBJECT_LINK_BI NO CASCADE BEFORE INSERT ON ezcontentobject_link REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCONTENTOBJECT_LINK;

CREATE TABLE ezcontentobject_name (
  content_translation VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  content_version INTEGER DEFAULT 0 NOT NULL,
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  real_translation VARCHAR(20 CODEUNITS32) DEFAULT NULL
);
ALTER TABLE ezcontentobject_name ADD CONSTRAINT PK_EZCONTENTOBJECT_NAME PRIMARY KEY (contentobject_id, content_version, content_translation);
CREATE INDEX ezcontentobject_name_cov_id ON ezcontentobject_name (content_version);
CREATE INDEX ezcontentobject_name_lang_id ON ezcontentobject_name (language_id);
CREATE INDEX ezcontentobject_name_name ON ezcontentobject_name (name);

CREATE TABLE ezcontentobject_trash (
  contentobject_id INTEGER DEFAULT NULL,
  contentobject_version INTEGER DEFAULT NULL,
  depth INTEGER DEFAULT 0 NOT NULL,
  is_hidden INTEGER DEFAULT 0 NOT NULL,
  is_invisible INTEGER DEFAULT 0 NOT NULL,
  main_node_id INTEGER DEFAULT NULL,
  modified_subnode INTEGER DEFAULT 0,
  node_id INTEGER DEFAULT 0 NOT NULL,
  parent_node_id INTEGER DEFAULT 0 NOT NULL,
  path_identification_string VARCHAR(2022 CODEUNITS32),
  path_string VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL,
  remote_id VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  sort_field INTEGER DEFAULT 1,
  sort_order INTEGER DEFAULT 1,
  trashed INTEGER DEFAULT 0 NOT NULL,
  trashed_by INTEGER DEFAULT 0 NOT NULL,
  trashed_via VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL
);
COMMENT ON COLUMN ezcontentobject_trash.path_identification_string IS 'exp:longtext';
ALTER TABLE ezcontentobject_trash ADD CONSTRAINT PK_EZCONTENTOBJECT_TRASH PRIMARY KEY (node_id);
CREATE INDEX ezcontentobject_trash_ezcobj_trash_co_id ON ezcontentobject_trash (contentobject_id);
COMMENT ON INDEX ezcontentobject_trash_ezcobj_trash_co_id IS 'exp:index:ezcobj_trash_co_id';
CREATE INDEX ezcontentobject_trash_ezcobj_trash_depth ON ezcontentobject_trash (depth);
COMMENT ON INDEX ezcontentobject_trash_ezcobj_trash_depth IS 'exp:index:ezcobj_trash_depth';
CREATE INDEX ezcontentobject_trash_ezcobj_trash_modified_subnode ON ezcontentobject_trash (modified_subnode);
COMMENT ON INDEX ezcontentobject_trash_ezcobj_trash_modified_subnode IS 'exp:index:ezcobj_trash_modified_subnode';
CREATE INDEX ezcontentobject_trash_ezcobj_trash_p_node_id ON ezcontentobject_trash (parent_node_id);
COMMENT ON INDEX ezcontentobject_trash_ezcobj_trash_p_node_id IS 'exp:index:ezcobj_trash_p_node_id';
CREATE INDEX ezcontentobject_trash_ezcobj_trash_path ON ezcontentobject_trash (path_string);
COMMENT ON INDEX ezcontentobject_trash_ezcobj_trash_path IS 'exp:index:ezcobj_trash_path';
CREATE INDEX ezcontentobject_trash_ezcobj_trash_path_ident ON ezcontentobject_trash (path_identification_string);
COMMENT ON INDEX ezcontentobject_trash_ezcobj_trash_path_ident IS 'exp:index:ezcobj_trash_path_ident';
CREATE INDEX ezcontentobject_trash_ezcobj_trash_trashed_by ON ezcontentobject_trash (trashed_by);
COMMENT ON INDEX ezcontentobject_trash_ezcobj_trash_trashed_by IS 'exp:index:ezcobj_trash_trashed_by';

CREATE TABLE ezcontentobject_tree (
  contentobject_id INTEGER DEFAULT NULL,
  contentobject_is_published INTEGER DEFAULT NULL,
  contentobject_version INTEGER DEFAULT NULL,
  depth INTEGER DEFAULT 0 NOT NULL,
  is_hidden INTEGER DEFAULT 0 NOT NULL,
  is_invisible INTEGER DEFAULT 0 NOT NULL,
  main_node_id INTEGER DEFAULT NULL,
  modified_subnode INTEGER DEFAULT 0,
  node_id INTEGER NOT NULL,
  parent_node_id INTEGER DEFAULT 0 NOT NULL,
  path_identification_string VARCHAR(2022 CODEUNITS32),
  path_string VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL,
  remote_id VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  sort_field INTEGER DEFAULT 1,
  sort_order INTEGER DEFAULT 1
);
COMMENT ON COLUMN ezcontentobject_tree.path_identification_string IS 'exp:longtext';
ALTER TABLE ezcontentobject_tree ADD CONSTRAINT PK_EZCONTENTOBJECT_TREE PRIMARY KEY (node_id);
CREATE INDEX ezcontentobject_tree_remote_id ON ezcontentobject_tree (remote_id);
CREATE INDEX ezcontentobject_tree_co_id ON ezcontentobject_tree (contentobject_id);
CREATE INDEX ezcontentobject_tree_depth ON ezcontentobject_tree (depth);
CREATE INDEX ezcontentobject_tree_p_node_id ON ezcontentobject_tree (parent_node_id);
CREATE INDEX ezcontentobject_tree_path ON ezcontentobject_tree (path_string);
CREATE INDEX ezcontentobject_tree_path_ident ON ezcontentobject_tree (path_identification_string);
CREATE INDEX ezcontentobject_tree_modified_subnode ON ezcontentobject_tree (modified_subnode);
COMMENT ON INDEX ezcontentobject_tree_modified_subnode IS 'exp:index:modified_subnode';
CREATE SEQUENCE S_EZCONTENTOBJECT_TREE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCONTENTOBJECT_TREE IS 'ezcontentobject_tree.node_id';
CREATE TRIGGER T_EZCONTENTOBJECT_TREE_BI NO CASCADE BEFORE INSERT ON ezcontentobject_tree REFERENCING NEW AS N FOR EACH ROW WHEN (N.node_id IS NULL OR N.node_id = 0) SET N.node_id = NEXT VALUE FOR S_EZCONTENTOBJECT_TREE;

CREATE TABLE ezcontentobject_version (
  contentobject_id INTEGER DEFAULT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  initial_language_id BIGINT DEFAULT 0 NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL,
  workflow_event_pos INTEGER DEFAULT 0
);
ALTER TABLE ezcontentobject_version ADD CONSTRAINT PK_EZCONTENTOBJECT_VERSION PRIMARY KEY (id);
CREATE INDEX ezcontentobject_version_ezcobj_version_creator_id ON ezcontentobject_version (creator_id);
COMMENT ON INDEX ezcontentobject_version_ezcobj_version_creator_id IS 'exp:index:ezcobj_version_creator_id';
CREATE INDEX ezcontentobject_version_ezcobj_version_status ON ezcontentobject_version (status);
COMMENT ON INDEX ezcontentobject_version_ezcobj_version_status IS 'exp:index:ezcobj_version_status';
CREATE INDEX ezcontentobject_version_idx_object_version_objver ON ezcontentobject_version (contentobject_id, version);
COMMENT ON INDEX ezcontentobject_version_idx_object_version_objver IS 'exp:index:idx_object_version_objver';
CREATE INDEX ezcontentobject_version_ezcontobj_version_obj_status ON ezcontentobject_version (contentobject_id, status);
COMMENT ON INDEX ezcontentobject_version_ezcontobj_version_obj_status IS 'exp:index:ezcontobj_version_obj_status';
CREATE SEQUENCE S_EZCONTENTOBJECT_VERSION AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCONTENTOBJECT_VERSION IS 'ezcontentobject_version.id';
CREATE TRIGGER T_EZCONTENTOBJECT_VERSION_BI NO CASCADE BEFORE INSERT ON ezcontentobject_version REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCONTENTOBJECT_VERSION;

CREATE TABLE ezcurrencydata (
  auto_rate_value DECIMAL(10,5) DEFAULT 0 NOT NULL,
  code VARCHAR(4 CODEUNITS32) DEFAULT '' NOT NULL,
  custom_rate_value DECIMAL(10,5) DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  locale VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  rate_factor DECIMAL(10,5) DEFAULT 1 NOT NULL,
  status INTEGER DEFAULT 1 NOT NULL,
  symbol VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezcurrencydata ADD CONSTRAINT PK_EZCURRENCYDATA PRIMARY KEY (id);
CREATE INDEX ezcurrencydata_code ON ezcurrencydata (code);
CREATE SEQUENCE S_EZCURRENCYDATA AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZCURRENCYDATA IS 'ezcurrencydata.id';
CREATE TRIGGER T_EZCURRENCYDATA_BI NO CASCADE BEFORE INSERT ON ezcurrencydata REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZCURRENCYDATA;

CREATE TABLE ezdiscountrule (
  id INTEGER NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezdiscountrule ADD CONSTRAINT PK_EZDISCOUNTRULE PRIMARY KEY (id);
CREATE SEQUENCE S_EZDISCOUNTRULE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZDISCOUNTRULE IS 'ezdiscountrule.id';
CREATE TRIGGER T_EZDISCOUNTRULE_BI NO CASCADE BEFORE INSERT ON ezdiscountrule REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZDISCOUNTRULE;

CREATE TABLE ezdiscountsubrule (
  discount_percent REAL DEFAULT NULL,
  discountrule_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  limitation VARCHAR(1 CODEUNITS32) DEFAULT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
COMMENT ON COLUMN ezdiscountsubrule.limitation IS 'exp:char';
ALTER TABLE ezdiscountsubrule ADD CONSTRAINT PK_EZDISCOUNTSUBRULE PRIMARY KEY (id);
CREATE SEQUENCE S_EZDISCOUNTSUBRULE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZDISCOUNTSUBRULE IS 'ezdiscountsubrule.id';
CREATE TRIGGER T_EZDISCOUNTSUBRULE_BI NO CASCADE BEFORE INSERT ON ezdiscountsubrule REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZDISCOUNTSUBRULE;

CREATE TABLE ezdiscountsubrule_value (
  discountsubrule_id INTEGER DEFAULT 0 NOT NULL,
  issection INTEGER DEFAULT 0 NOT NULL,
  value INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezdiscountsubrule_value ADD CONSTRAINT PK_EZDISCOUNTSUBRULE_VALUE PRIMARY KEY (discountsubrule_id, value, issection);

CREATE TABLE ezenumobjectvalue (
  contentobject_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_attribute_version INTEGER DEFAULT 0 NOT NULL,
  enumelement VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  enumid INTEGER DEFAULT 0 NOT NULL,
  enumvalue VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezenumobjectvalue ADD CONSTRAINT PK_EZENUMOBJECTVALUE PRIMARY KEY (contentobject_attribute_id, contentobject_attribute_version, enumid);

CREATE TABLE ezenumvalue (
  contentclass_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentclass_attribute_version INTEGER DEFAULT 0 NOT NULL,
  enumelement VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  enumvalue VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  placement INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezenumvalue ADD CONSTRAINT PK_EZENUMVALUE PRIMARY KEY (id, contentclass_attribute_id, contentclass_attribute_version);
CREATE INDEX ezenumvalue_co_cl_attr_id_co_class_att_ver ON ezenumvalue (contentclass_attribute_id, contentclass_attribute_version);
CREATE SEQUENCE S_EZENUMVALUE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZENUMVALUE IS 'ezenumvalue.id';
CREATE TRIGGER T_EZENUMVALUE_BI NO CASCADE BEFORE INSERT ON ezenumvalue REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZENUMVALUE;

CREATE TABLE ezforgot_password (
  hash_key VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  time INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezforgot_password ADD CONSTRAINT PK_EZFORGOT_PASSWORD PRIMARY KEY (id);
CREATE INDEX ezforgot_password_user ON ezforgot_password (user_id);
CREATE SEQUENCE S_EZFORGOT_PASSWORD AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZFORGOT_PASSWORD IS 'ezforgot_password.id';
CREATE TRIGGER T_EZFORGOT_PASSWORD_BI NO CASCADE BEFORE INSERT ON ezforgot_password REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZFORGOT_PASSWORD;

CREATE TABLE ezgeneral_digest_user_settings (
  day VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  digest_type INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  receive_digest INTEGER DEFAULT 0 NOT NULL,
  time VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezgeneral_digest_user_settings ADD CONSTRAINT PK_EZGENERAL_DIGEST_USER_SETTINGS PRIMARY KEY (id);
CREATE UNIQUE INDEX ezgeneral_digest_user_settings_ezgeneral_digest_user_id ON ezgeneral_digest_user_settings (user_id);
COMMENT ON INDEX ezgeneral_digest_user_settings_ezgeneral_digest_user_id IS 'exp:index:ezgeneral_digest_user_id';
CREATE SEQUENCE S_EZGENERAL_DIGEST_USER_SETTINGS AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZGENERAL_DIGEST_USER_SETTINGS IS 'ezgeneral_digest_user_settings.id';
CREATE TRIGGER T_EZGENERAL_DIGEST_USER_SETTINGS_BI NO CASCADE BEFORE INSERT ON ezgeneral_digest_user_settings REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZGENERAL_DIGEST_USER_SETTINGS;

CREATE TABLE ezimagefile (
  contentobject_attribute_id INTEGER DEFAULT 0 NOT NULL,
  filepath VARCHAR(2022 CODEUNITS32) NOT NULL,
  id INTEGER NOT NULL
);
COMMENT ON COLUMN ezimagefile.filepath IS 'exp:longtext';
ALTER TABLE ezimagefile ADD CONSTRAINT PK_EZIMAGEFILE PRIMARY KEY (id);
CREATE INDEX ezimagefile_coid ON ezimagefile (contentobject_attribute_id);
CREATE INDEX ezimagefile_file ON ezimagefile (filepath);
CREATE SEQUENCE S_EZIMAGEFILE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZIMAGEFILE IS 'ezimagefile.id';
CREATE TRIGGER T_EZIMAGEFILE_BI NO CASCADE BEFORE INSERT ON ezimagefile REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZIMAGEFILE;

CREATE TABLE ezinfocollection (
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  modified INTEGER DEFAULT 0,
  user_identifier VARCHAR(34 CODEUNITS32) DEFAULT NULL
);
ALTER TABLE ezinfocollection ADD CONSTRAINT PK_EZINFOCOLLECTION PRIMARY KEY (id);
CREATE INDEX ezinfocollection_co_id_created ON ezinfocollection (contentobject_id, created);
CREATE SEQUENCE S_EZINFOCOLLECTION AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZINFOCOLLECTION IS 'ezinfocollection.id';
CREATE TRIGGER T_EZINFOCOLLECTION_BI NO CASCADE BEFORE INSERT ON ezinfocollection REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZINFOCOLLECTION;

CREATE TABLE ezinfocollection_attribute (
  contentclass_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_attribute_id INTEGER DEFAULT NULL,
  contentobject_id INTEGER DEFAULT NULL,
  data_float REAL DEFAULT NULL,
  data_int INTEGER DEFAULT NULL,
  data_text CLOB(1G),
  id INTEGER NOT NULL,
  informationcollection_id INTEGER DEFAULT 0 NOT NULL
);
COMMENT ON COLUMN ezinfocollection_attribute.data_text IS 'exp:clob';
ALTER TABLE ezinfocollection_attribute ADD CONSTRAINT PK_EZINFOCOLLECTION_ATTRIBUTE PRIMARY KEY (id);
CREATE INDEX ezinfocollection_attribute_ezinfocollection_attr_cca_id ON ezinfocollection_attribute (contentclass_attribute_id);
COMMENT ON INDEX ezinfocollection_attribute_ezinfocollection_attr_cca_id IS 'exp:index:ezinfocollection_attr_cca_id';
CREATE INDEX ezinfocollection_attribute_ezinfocollection_attr_co_id ON ezinfocollection_attribute (contentobject_id);
COMMENT ON INDEX ezinfocollection_attribute_ezinfocollection_attr_co_id IS 'exp:index:ezinfocollection_attr_co_id';
CREATE INDEX ezinfocollection_attribute_ezinfocollection_attr_coa_id ON ezinfocollection_attribute (contentobject_attribute_id);
COMMENT ON INDEX ezinfocollection_attribute_ezinfocollection_attr_coa_id IS 'exp:index:ezinfocollection_attr_coa_id';
CREATE INDEX ezinfocollection_attribute_ezinfocollection_attr_ic_id ON ezinfocollection_attribute (informationcollection_id);
COMMENT ON INDEX ezinfocollection_attribute_ezinfocollection_attr_ic_id IS 'exp:index:ezinfocollection_attr_ic_id';
CREATE SEQUENCE S_EZINFOCOLLECTION_ATTRIBUTE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZINFOCOLLECTION_ATTRIBUTE IS 'ezinfocollection_attribute.id';
CREATE TRIGGER T_EZINFOCOLLECTION_ATTRIBUTE_BI NO CASCADE BEFORE INSERT ON ezinfocollection_attribute REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZINFOCOLLECTION_ATTRIBUTE;

CREATE TABLE ezisbn_group (
  description VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  group_number INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL
);
ALTER TABLE ezisbn_group ADD CONSTRAINT PK_EZISBN_GROUP PRIMARY KEY (id);
CREATE SEQUENCE S_EZISBN_GROUP AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZISBN_GROUP IS 'ezisbn_group.id';
CREATE TRIGGER T_EZISBN_GROUP_BI NO CASCADE BEFORE INSERT ON ezisbn_group REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZISBN_GROUP;

CREATE TABLE ezisbn_group_range (
  from_number INTEGER DEFAULT 0 NOT NULL,
  group_from VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  group_length INTEGER DEFAULT 0 NOT NULL,
  group_to VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  to_number INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezisbn_group_range ADD CONSTRAINT PK_EZISBN_GROUP_RANGE PRIMARY KEY (id);
CREATE SEQUENCE S_EZISBN_GROUP_RANGE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZISBN_GROUP_RANGE IS 'ezisbn_group_range.id';
CREATE TRIGGER T_EZISBN_GROUP_RANGE_BI NO CASCADE BEFORE INSERT ON ezisbn_group_range REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZISBN_GROUP_RANGE;

CREATE TABLE ezisbn_registrant_range (
  from_number INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  isbn_group_id INTEGER DEFAULT 0 NOT NULL,
  registrant_from VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  registrant_length INTEGER DEFAULT 0 NOT NULL,
  registrant_to VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  to_number INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezisbn_registrant_range ADD CONSTRAINT PK_EZISBN_REGISTRANT_RANGE PRIMARY KEY (id);
CREATE SEQUENCE S_EZISBN_REGISTRANT_RANGE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZISBN_REGISTRANT_RANGE IS 'ezisbn_registrant_range.id';
CREATE TRIGGER T_EZISBN_REGISTRANT_RANGE_BI NO CASCADE BEFORE INSERT ON ezisbn_registrant_range REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZISBN_REGISTRANT_RANGE;

CREATE TABLE ezkeyword (
  class_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  keyword VARCHAR(255 CODEUNITS32) DEFAULT NULL
);
ALTER TABLE ezkeyword ADD CONSTRAINT PK_EZKEYWORD PRIMARY KEY (id);
CREATE INDEX ezkeyword_keyword ON ezkeyword (keyword);
CREATE SEQUENCE S_EZKEYWORD AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZKEYWORD IS 'ezkeyword.id';
CREATE TRIGGER T_EZKEYWORD_BI NO CASCADE BEFORE INSERT ON ezkeyword REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZKEYWORD;

CREATE TABLE ezkeyword_attribute_link (
  id INTEGER NOT NULL,
  keyword_id INTEGER DEFAULT 0 NOT NULL,
  objectattribute_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezkeyword_attribute_link ADD CONSTRAINT PK_EZKEYWORD_ATTRIBUTE_LINK PRIMARY KEY (id);
CREATE INDEX ezkeyword_attribute_link_ezkeyword_attr_link_kid_oaid ON ezkeyword_attribute_link (keyword_id, objectattribute_id);
COMMENT ON INDEX ezkeyword_attribute_link_ezkeyword_attr_link_kid_oaid IS 'exp:index:ezkeyword_attr_link_kid_oaid';
CREATE INDEX ezkeyword_attribute_link_ezkeyword_attr_link_oaid ON ezkeyword_attribute_link (objectattribute_id);
COMMENT ON INDEX ezkeyword_attribute_link_ezkeyword_attr_link_oaid IS 'exp:index:ezkeyword_attr_link_oaid';
CREATE SEQUENCE S_EZKEYWORD_ATTRIBUTE_LINK AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZKEYWORD_ATTRIBUTE_LINK IS 'ezkeyword_attribute_link.id';
CREATE TRIGGER T_EZKEYWORD_ATTRIBUTE_LINK_BI NO CASCADE BEFORE INSERT ON ezkeyword_attribute_link REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZKEYWORD_ATTRIBUTE_LINK;

CREATE TABLE ezmedia (
  contentobject_attribute_id INTEGER DEFAULT 0 NOT NULL,
  controls VARCHAR(50 CODEUNITS32) DEFAULT NULL,
  filename VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  has_controller INTEGER DEFAULT 0,
  height INTEGER DEFAULT NULL,
  is_autoplay INTEGER DEFAULT 0,
  is_loop INTEGER DEFAULT 0,
  mime_type VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  original_filename VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  pluginspage VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  quality VARCHAR(50 CODEUNITS32) DEFAULT NULL,
  version INTEGER DEFAULT 0 NOT NULL,
  width INTEGER DEFAULT NULL
);
ALTER TABLE ezmedia ADD CONSTRAINT PK_EZMEDIA PRIMARY KEY (contentobject_attribute_id, version);

CREATE TABLE ezmessage (
  body CLOB(1G),
  destination_address VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  is_sent INTEGER DEFAULT 0 NOT NULL,
  send_method VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  send_time VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  send_weekday VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  title VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
COMMENT ON COLUMN ezmessage.body IS 'exp:clob';
ALTER TABLE ezmessage ADD CONSTRAINT PK_EZMESSAGE PRIMARY KEY (id);
CREATE SEQUENCE S_EZMESSAGE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZMESSAGE IS 'ezmessage.id';
CREATE TRIGGER T_EZMESSAGE_BI NO CASCADE BEFORE INSERT ON ezmessage REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZMESSAGE;

CREATE TABLE ezmodule_run (
  function_name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  id INTEGER NOT NULL,
  module_data CLOB(1G),
  module_name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  workflow_process_id INTEGER DEFAULT NULL
);
COMMENT ON COLUMN ezmodule_run.module_data IS 'exp:clob';
ALTER TABLE ezmodule_run ADD CONSTRAINT PK_EZMODULE_RUN PRIMARY KEY (id);
CREATE UNIQUE INDEX ezmodule_run_workflow_process_id_s ON ezmodule_run (workflow_process_id);
CREATE SEQUENCE S_EZMODULE_RUN AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZMODULE_RUN IS 'ezmodule_run.id';
CREATE TRIGGER T_EZMODULE_RUN_BI NO CASCADE BEFORE INSERT ON ezmodule_run REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZMODULE_RUN;

CREATE TABLE ezmultipricedata (
  contentobject_attr_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_attr_version INTEGER DEFAULT 0 NOT NULL,
  currency_code VARCHAR(4 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  type INTEGER DEFAULT 0 NOT NULL,
  value DECIMAL(15,2) DEFAULT 0 NOT NULL
);
ALTER TABLE ezmultipricedata ADD CONSTRAINT PK_EZMULTIPRICEDATA PRIMARY KEY (id);
CREATE INDEX ezmultipricedata_coa_id ON ezmultipricedata (contentobject_attr_id);
CREATE INDEX ezmultipricedata_coa_version ON ezmultipricedata (contentobject_attr_version);
CREATE INDEX ezmultipricedata_currency_code ON ezmultipricedata (currency_code);
CREATE SEQUENCE S_EZMULTIPRICEDATA AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZMULTIPRICEDATA IS 'ezmultipricedata.id';
CREATE TRIGGER T_EZMULTIPRICEDATA_BI NO CASCADE BEFORE INSERT ON ezmultipricedata REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZMULTIPRICEDATA;

CREATE TABLE eznode_assignment (
  contentobject_id INTEGER DEFAULT NULL,
  contentobject_version INTEGER DEFAULT NULL,
  from_node_id INTEGER DEFAULT 0,
  id INTEGER NOT NULL,
  is_main INTEGER DEFAULT 0 NOT NULL,
  op_code INTEGER DEFAULT 0 NOT NULL,
  parent_node INTEGER DEFAULT NULL,
  parent_remote_id VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  remote_id VARCHAR(100 CODEUNITS32) DEFAULT '0' NOT NULL,
  sort_field INTEGER DEFAULT 1,
  sort_order INTEGER DEFAULT 1,
  priority INTEGER DEFAULT 0 NOT NULL,
  is_hidden INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE eznode_assignment ADD CONSTRAINT PK_EZNODE_ASSIGNMENT PRIMARY KEY (id);
CREATE INDEX eznode_assignment_co_version ON eznode_assignment (contentobject_version);
CREATE INDEX eznode_assignment_coid_cov ON eznode_assignment (contentobject_id, contentobject_version);
CREATE INDEX eznode_assignment_is_main ON eznode_assignment (is_main);
CREATE INDEX eznode_assignment_parent_node ON eznode_assignment (parent_node);
CREATE SEQUENCE S_EZNODE_ASSIGNMENT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZNODE_ASSIGNMENT IS 'eznode_assignment.id';
CREATE TRIGGER T_EZNODE_ASSIGNMENT_BI NO CASCADE BEFORE INSERT ON eznode_assignment REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZNODE_ASSIGNMENT;

CREATE TABLE eznotificationcollection (
  data_subject VARCHAR(32672) NOT NULL,
  data_text CLOB(1G) NOT NULL,
  event_id INTEGER DEFAULT 0 NOT NULL,
  handler VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  transport VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
COMMENT ON COLUMN eznotificationcollection.data_text IS 'exp:clob';
ALTER TABLE eznotificationcollection ADD CONSTRAINT PK_EZNOTIFICATIONCOLLECTION PRIMARY KEY (id);
CREATE SEQUENCE S_EZNOTIFICATIONCOLLECTION AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZNOTIFICATIONCOLLECTION IS 'eznotificationcollection.id';
CREATE TRIGGER T_EZNOTIFICATIONCOLLECTION_BI NO CASCADE BEFORE INSERT ON eznotificationcollection REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZNOTIFICATIONCOLLECTION;

CREATE TABLE eznotificationcollection_item (
  address VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  collection_id INTEGER DEFAULT 0 NOT NULL,
  event_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  send_date INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE eznotificationcollection_item ADD CONSTRAINT PK_EZNOTIFICATIONCOLLECTION_ITEM PRIMARY KEY (id);
CREATE SEQUENCE S_EZNOTIFICATIONCOLLECTION_ITEM AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZNOTIFICATIONCOLLECTION_ITEM IS 'eznotificationcollection_item.id';
CREATE TRIGGER T_EZNOTIFICATIONCOLLECTION_ITEM_BI NO CASCADE BEFORE INSERT ON eznotificationcollection_item REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZNOTIFICATIONCOLLECTION_ITEM;

CREATE TABLE eznotificationevent (
  data_int1 INTEGER DEFAULT 0 NOT NULL,
  data_int2 INTEGER DEFAULT 0 NOT NULL,
  data_int3 INTEGER DEFAULT 0 NOT NULL,
  data_int4 INTEGER DEFAULT 0 NOT NULL,
  data_text1 VARCHAR(32672) NOT NULL,
  data_text2 VARCHAR(32672) NOT NULL,
  data_text3 VARCHAR(32672) NOT NULL,
  data_text4 VARCHAR(32672) NOT NULL,
  event_type_string VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  status INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE eznotificationevent ADD CONSTRAINT PK_EZNOTIFICATIONEVENT PRIMARY KEY (id);
CREATE SEQUENCE S_EZNOTIFICATIONEVENT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZNOTIFICATIONEVENT IS 'eznotificationevent.id';
CREATE TRIGGER T_EZNOTIFICATIONEVENT_BI NO CASCADE BEFORE INSERT ON eznotificationevent REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZNOTIFICATIONEVENT;

CREATE TABLE ezoperation_memento (
  id INTEGER NOT NULL,
  main INTEGER DEFAULT 0 NOT NULL,
  main_key VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  memento_data CLOB(1G) NOT NULL,
  memento_key VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL
);
COMMENT ON COLUMN ezoperation_memento.memento_data IS 'exp:clob';
ALTER TABLE ezoperation_memento ADD CONSTRAINT PK_EZOPERATION_MEMENTO PRIMARY KEY (id, memento_key);
CREATE INDEX ezoperation_memento_memento_key_main ON ezoperation_memento (memento_key, main);
CREATE SEQUENCE S_EZOPERATION_MEMENTO AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZOPERATION_MEMENTO IS 'ezoperation_memento.id';
CREATE TRIGGER T_EZOPERATION_MEMENTO_BI NO CASCADE BEFORE INSERT ON ezoperation_memento REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZOPERATION_MEMENTO;

CREATE TABLE ezorder (
  account_identifier VARCHAR(100 CODEUNITS32) DEFAULT 'default' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  data_text_1 VARCHAR(32672),
  data_text_2 VARCHAR(32672),
  email VARCHAR(150 CODEUNITS32) DEFAULT '',
  id INTEGER NOT NULL,
  ignore_vat INTEGER DEFAULT 0 NOT NULL,
  is_archived INTEGER DEFAULT 0 NOT NULL,
  is_temporary INTEGER DEFAULT 1 NOT NULL,
  order_nr INTEGER DEFAULT 0 NOT NULL,
  productcollection_id INTEGER DEFAULT 0 NOT NULL,
  siteaccess VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  status_id INTEGER DEFAULT 0,
  status_modified INTEGER DEFAULT 0,
  status_modifier_id INTEGER DEFAULT 0,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder ADD CONSTRAINT PK_EZORDER PRIMARY KEY (id);
CREATE INDEX ezorder_is_archived ON ezorder (is_archived);
CREATE INDEX ezorder_is_tmp ON ezorder (is_temporary);
CREATE SEQUENCE S_EZORDER AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZORDER IS 'ezorder.id';
CREATE TRIGGER T_EZORDER_BI NO CASCADE BEFORE INSERT ON ezorder REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZORDER;

CREATE TABLE ezorder_nr_incr (
  id INTEGER NOT NULL
);
ALTER TABLE ezorder_nr_incr ADD CONSTRAINT PK_EZORDER_NR_INCR PRIMARY KEY (id);
CREATE SEQUENCE S_EZORDER_NR_INCR AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZORDER_NR_INCR IS 'ezorder_nr_incr.id';
CREATE TRIGGER T_EZORDER_NR_INCR_BI NO CASCADE BEFORE INSERT ON ezorder_nr_incr REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZORDER_NR_INCR;

CREATE TABLE ezorder_item (
  description VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  id INTEGER NOT NULL,
  is_vat_inc INTEGER DEFAULT 0 NOT NULL,
  order_id INTEGER DEFAULT 0 NOT NULL,
  price REAL DEFAULT NULL,
  type VARCHAR(30 CODEUNITS32) DEFAULT NULL,
  vat_value REAL DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder_item ADD CONSTRAINT PK_EZORDER_ITEM PRIMARY KEY (id);
CREATE INDEX ezorder_item_order_id ON ezorder_item (order_id);
CREATE INDEX ezorder_item_type ON ezorder_item (type);
CREATE SEQUENCE S_EZORDER_ITEM AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZORDER_ITEM IS 'ezorder_item.id';
CREATE TRIGGER T_EZORDER_ITEM_BI NO CASCADE BEFORE INSERT ON ezorder_item REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZORDER_ITEM;

CREATE TABLE ezorder_status (
  id INTEGER NOT NULL,
  is_active INTEGER DEFAULT 1 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  status_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder_status ADD CONSTRAINT PK_EZORDER_STATUS PRIMARY KEY (id);
CREATE INDEX ezorder_status_active ON ezorder_status (is_active);
CREATE INDEX ezorder_status_name ON ezorder_status (name);
CREATE INDEX ezorder_status_sid ON ezorder_status (status_id);
CREATE SEQUENCE S_EZORDER_STATUS AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZORDER_STATUS IS 'ezorder_status.id';
CREATE TRIGGER T_EZORDER_STATUS_BI NO CASCADE BEFORE INSERT ON ezorder_status REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZORDER_STATUS;

CREATE TABLE ezorder_status_history (
  id INTEGER NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  order_id INTEGER DEFAULT 0 NOT NULL,
  status_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder_status_history ADD CONSTRAINT PK_EZORDER_STATUS_HISTORY PRIMARY KEY (id);
CREATE INDEX ezorder_status_history_mod ON ezorder_status_history (modified);
CREATE INDEX ezorder_status_history_oid ON ezorder_status_history (order_id);
CREATE INDEX ezorder_status_history_sid ON ezorder_status_history (status_id);
CREATE SEQUENCE S_EZORDER_STATUS_HISTORY AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZORDER_STATUS_HISTORY IS 'ezorder_status_history.id';
CREATE TRIGGER T_EZORDER_STATUS_HISTORY_BI NO CASCADE BEFORE INSERT ON ezorder_status_history REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZORDER_STATUS_HISTORY;

CREATE TABLE ezpackage (
  id INTEGER NOT NULL,
  install_date INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  version VARCHAR(30 CODEUNITS32) DEFAULT '0' NOT NULL
);
ALTER TABLE ezpackage ADD CONSTRAINT PK_EZPACKAGE PRIMARY KEY (id);
CREATE SEQUENCE S_EZPACKAGE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPACKAGE IS 'ezpackage.id';
CREATE TRIGGER T_EZPACKAGE_BI NO CASCADE BEFORE INSERT ON ezpackage REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPACKAGE;

CREATE TABLE ezpaymentobject (
  id INTEGER NOT NULL,
  order_id INTEGER DEFAULT 0 NOT NULL,
  payment_string VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  workflowprocess_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezpaymentobject ADD CONSTRAINT PK_EZPAYMENTOBJECT PRIMARY KEY (id);
CREATE SEQUENCE S_EZPAYMENTOBJECT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPAYMENTOBJECT IS 'ezpaymentobject.id';
CREATE TRIGGER T_EZPAYMENTOBJECT_BI NO CASCADE BEFORE INSERT ON ezpaymentobject REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPAYMENTOBJECT;

CREATE TABLE ezpdf_export (
  footer_text VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  show_footer INTEGER DEFAULT 1 NOT NULL,
  created INTEGER DEFAULT NULL,
  creator_id INTEGER DEFAULT NULL,
  export_classes VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  export_structure VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  id INTEGER NOT NULL,
  intro_text VARCHAR(32672),
  modified INTEGER DEFAULT NULL,
  modifier_id INTEGER DEFAULT NULL,
  pdf_filename VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  show_frontpage INTEGER DEFAULT NULL,
  site_access VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  source_node_id INTEGER DEFAULT NULL,
  status INTEGER DEFAULT NULL,
  sub_text VARCHAR(32672),
  title VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezpdf_export ADD CONSTRAINT PK_EZPDF_EXPORT PRIMARY KEY (id, version);
CREATE SEQUENCE S_EZPDF_EXPORT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPDF_EXPORT IS 'ezpdf_export.id';
CREATE TRIGGER T_EZPDF_EXPORT_BI NO CASCADE BEFORE INSERT ON ezpdf_export REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPDF_EXPORT;

CREATE TABLE ezpending_actions (
  id INTEGER NOT NULL,
  action VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT NULL,
  param VARCHAR(32672)
);
ALTER TABLE ezpending_actions ADD CONSTRAINT PK_EZPENDING_ACTIONS PRIMARY KEY (id);
CREATE INDEX ezpending_actions_action ON ezpending_actions (action);
CREATE INDEX ezpending_actions_created ON ezpending_actions (created);
CREATE SEQUENCE S_EZPENDING_ACTIONS AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPENDING_ACTIONS IS 'ezpending_actions.id';
CREATE TRIGGER T_EZPENDING_ACTIONS_BI NO CASCADE BEFORE INSERT ON ezpending_actions REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPENDING_ACTIONS;

CREATE TABLE ezpolicy (
  function_name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  id INTEGER NOT NULL,
  module_name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  original_id INTEGER DEFAULT 0 NOT NULL,
  role_id INTEGER DEFAULT NULL
);
ALTER TABLE ezpolicy ADD CONSTRAINT PK_EZPOLICY PRIMARY KEY (id);
CREATE INDEX ezpolicy_original_id ON ezpolicy (original_id);
CREATE INDEX ezpolicy_role_id ON ezpolicy (role_id);
CREATE SEQUENCE S_EZPOLICY AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPOLICY IS 'ezpolicy.id';
CREATE TRIGGER T_EZPOLICY_BI NO CASCADE BEFORE INSERT ON ezpolicy REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPOLICY;

CREATE TABLE ezpolicy_limitation (
  id INTEGER NOT NULL,
  identifier VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  policy_id INTEGER DEFAULT NULL
);
ALTER TABLE ezpolicy_limitation ADD CONSTRAINT PK_EZPOLICY_LIMITATION PRIMARY KEY (id);
CREATE INDEX ezpolicy_limitation_policy_id ON ezpolicy_limitation (policy_id);
COMMENT ON INDEX ezpolicy_limitation_policy_id IS 'exp:index:policy_id';
CREATE SEQUENCE S_EZPOLICY_LIMITATION AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPOLICY_LIMITATION IS 'ezpolicy_limitation.id';
CREATE TRIGGER T_EZPOLICY_LIMITATION_BI NO CASCADE BEFORE INSERT ON ezpolicy_limitation REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPOLICY_LIMITATION;

CREATE TABLE ezpolicy_limitation_value (
  id INTEGER NOT NULL,
  limitation_id INTEGER DEFAULT NULL,
  value VARCHAR(255 CODEUNITS32) DEFAULT NULL
);
ALTER TABLE ezpolicy_limitation_value ADD CONSTRAINT PK_EZPOLICY_LIMITATION_VALUE PRIMARY KEY (id);
CREATE INDEX ezpolicy_limitation_value_val ON ezpolicy_limitation_value (value);
CREATE INDEX ezpolicy_limitation_value_ezpolicy_limit_value_limit_id ON ezpolicy_limitation_value (limitation_id);
COMMENT ON INDEX ezpolicy_limitation_value_ezpolicy_limit_value_limit_id IS 'exp:index:ezpolicy_limit_value_limit_id';
CREATE SEQUENCE S_EZPOLICY_LIMITATION_VALUE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPOLICY_LIMITATION_VALUE IS 'ezpolicy_limitation_value.id';
CREATE TRIGGER T_EZPOLICY_LIMITATION_VALUE_BI NO CASCADE BEFORE INSERT ON ezpolicy_limitation_value REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPOLICY_LIMITATION_VALUE;

CREATE TABLE ezpreferences (
  id INTEGER NOT NULL,
  name VARCHAR(100 CODEUNITS32) DEFAULT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL,
  value VARCHAR(32672)
);
ALTER TABLE ezpreferences ADD CONSTRAINT PK_EZPREFERENCES PRIMARY KEY (id);
CREATE INDEX ezpreferences_name ON ezpreferences (name);
CREATE INDEX ezpreferences_user_id_idx ON ezpreferences (user_id, name);
CREATE SEQUENCE S_EZPREFERENCES AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPREFERENCES IS 'ezpreferences.id';
CREATE TRIGGER T_EZPREFERENCES_BI NO CASCADE BEFORE INSERT ON ezpreferences REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPREFERENCES;

CREATE TABLE ezprest_authcode (
  client_id VARCHAR(200 CODEUNITS32) DEFAULT '' NOT NULL,
  code_challenge VARCHAR(128 CODEUNITS32) DEFAULT NULL,
  code_challenge_method VARCHAR(10 CODEUNITS32) DEFAULT NULL,
  expirytime BIGINT DEFAULT 0 NOT NULL,
  id VARCHAR(200 CODEUNITS32) DEFAULT '' NOT NULL,
  scope VARCHAR(200 CODEUNITS32) DEFAULT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezprest_authcode ADD CONSTRAINT PK_EZPREST_AUTHCODE PRIMARY KEY (id);
CREATE INDEX ezprest_authcode_authcode_client_id ON ezprest_authcode (client_id);
COMMENT ON INDEX ezprest_authcode_authcode_client_id IS 'exp:index:authcode_client_id';

CREATE TABLE ezprest_authorized_clients (
  created INTEGER DEFAULT NULL,
  id INTEGER NOT NULL,
  rest_client_id INTEGER DEFAULT NULL,
  user_id INTEGER DEFAULT NULL
);
ALTER TABLE ezprest_authorized_clients ADD CONSTRAINT PK_EZPREST_AUTHORIZED_CLIENTS PRIMARY KEY (id);
CREATE INDEX ezprest_authorized_clients_client_user ON ezprest_authorized_clients (rest_client_id, user_id);
COMMENT ON INDEX ezprest_authorized_clients_client_user IS 'exp:index:client_user';
CREATE SEQUENCE S_EZPREST_AUTHORIZED_CLIENTS AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPREST_AUTHORIZED_CLIENTS IS 'ezprest_authorized_clients.id';
CREATE TRIGGER T_EZPREST_AUTHORIZED_CLIENTS_BI NO CASCADE BEFORE INSERT ON ezprest_authorized_clients REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPREST_AUTHORIZED_CLIENTS;

CREATE TABLE ezprest_clients (
  client_id VARCHAR(200 CODEUNITS32) DEFAULT NULL,
  client_secret VARCHAR(200 CODEUNITS32) DEFAULT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  description VARCHAR(32672),
  endpoint_uri VARCHAR(200 CODEUNITS32) DEFAULT NULL,
  id INTEGER NOT NULL,
  name VARCHAR(100 CODEUNITS32) DEFAULT NULL,
  owner_id INTEGER DEFAULT 0 NOT NULL,
  updated INTEGER DEFAULT 0 NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezprest_clients ADD CONSTRAINT PK_EZPREST_CLIENTS PRIMARY KEY (id);
CREATE UNIQUE INDEX ezprest_clients_client_id_unique ON ezprest_clients (client_id, version);
COMMENT ON INDEX ezprest_clients_client_id_unique IS 'exp:index:client_id_unique';
CREATE SEQUENCE S_EZPREST_CLIENTS AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPREST_CLIENTS IS 'ezprest_clients.id';
CREATE TRIGGER T_EZPREST_CLIENTS_BI NO CASCADE BEFORE INSERT ON ezprest_clients REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPREST_CLIENTS;

CREATE TABLE ezprest_token (
  client_id VARCHAR(200 CODEUNITS32) DEFAULT '' NOT NULL,
  expirytime BIGINT DEFAULT 0 NOT NULL,
  id VARCHAR(200 CODEUNITS32) DEFAULT '' NOT NULL,
  refresh_token VARCHAR(200 CODEUNITS32) DEFAULT '' NOT NULL,
  scope VARCHAR(200 CODEUNITS32) DEFAULT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezprest_token ADD CONSTRAINT PK_EZPREST_TOKEN PRIMARY KEY (id);
CREATE INDEX ezprest_token_token_client_id ON ezprest_token (client_id);
COMMENT ON INDEX ezprest_token_token_client_id IS 'exp:index:token_client_id';

CREATE TABLE expapikey (
  created INTEGER DEFAULT 0 NOT NULL,
  created_by INTEGER DEFAULT 0 NOT NULL,
  expires INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  key_prefix VARCHAR(40 CODEUNITS32) DEFAULT '' NOT NULL,
  last_ip VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  last_used INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  revoked INTEGER DEFAULT 0 NOT NULL,
  revoked_by INTEGER DEFAULT 0 NOT NULL,
  salt VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  scopes VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  secret_hash VARCHAR(128 CODEUNITS32) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expapikey ADD CONSTRAINT PK_EXPAPIKEY PRIMARY KEY (id);
CREATE UNIQUE INDEX expapikey_prefix ON expapikey (key_prefix);
CREATE INDEX expapikey_user ON expapikey (user_id);
CREATE SEQUENCE S_EXPAPIKEY AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXPAPIKEY IS 'expapikey.id';
CREATE TRIGGER T_EXPAPIKEY_BI NO CASCADE BEFORE INSERT ON expapikey REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXPAPIKEY;

CREATE TABLE exppasskey (
  aaguid VARCHAR(36 CODEUNITS32) DEFAULT '' NOT NULL,
  algorithm INTEGER DEFAULT 0 NOT NULL,
  backup_eligible INTEGER DEFAULT 0 NOT NULL,
  backup_state INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  credential_hash VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  credential_id VARCHAR(32672) DEFAULT NULL,
  id INTEGER NOT NULL,
  last_ip VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  last_used INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  public_key VARCHAR(32672) DEFAULT NULL,
  sign_count BIGINT DEFAULT 0 NOT NULL,
  transports VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  user_handle VARCHAR(128 CODEUNITS32) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exppasskey ADD CONSTRAINT PK_EXPPASSKEY PRIMARY KEY (id);
CREATE UNIQUE INDEX exppasskey_credential ON exppasskey (credential_hash);
CREATE INDEX exppasskey_user ON exppasskey (user_id);
CREATE SEQUENCE S_EXPPASSKEY AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXPPASSKEY IS 'exppasskey.id';
CREATE TRIGGER T_EXPPASSKEY_BI NO CASCADE BEFORE INSERT ON exppasskey REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXPPASSKEY;

CREATE TABLE exp_transfer_run (
  bytes BIGINT DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  detail VARCHAR(32672),
  detail_url VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  direction VARCHAR(10 CODEUNITS32) DEFAULT '' NOT NULL,
  duration INTEGER DEFAULT 0 NOT NULL,
  engine VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  error VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  file_ref VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  finished INTEGER DEFAULT 0 NOT NULL,
  format VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  job_ref VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  kind VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  rows_done INTEGER DEFAULT 0 NOT NULL,
  rows_failed INTEGER DEFAULT 0 NOT NULL,
  rows_total INTEGER DEFAULT 0 NOT NULL,
  scope VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  scope_ref VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  siteaccess VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  started INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  title VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  tool VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  tool_ref VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exp_transfer_run ADD CONSTRAINT PK_EXP_TRANSFER_RUN PRIMARY KEY (id);
CREATE INDEX exp_transfer_run_started ON exp_transfer_run (started);
CREATE INDEX exp_transfer_run_tool ON exp_transfer_run (tool, started);
CREATE INDEX exp_transfer_run_toolref ON exp_transfer_run (tool, tool_ref);
CREATE INDEX exp_transfer_run_user ON exp_transfer_run (user_id);
CREATE SEQUENCE S_EXP_TRANSFER_RUN AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXP_TRANSFER_RUN IS 'exp_transfer_run.id';
CREATE TRIGGER T_EXP_TRANSFER_RUN_BI NO CASCADE BEFORE INSERT ON exp_transfer_run REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXP_TRANSFER_RUN;

CREATE TABLE exp_data_connection (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  database_name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  description VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  engine VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  identifier VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  options VARCHAR(32672),
  port INTEGER DEFAULT 0 NOT NULL,
  read_only INTEGER DEFAULT 0 NOT NULL,
  secret VARCHAR(128 CODEUNITS32) DEFAULT '' NOT NULL,
  server VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  user_name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE exp_data_connection ADD CONSTRAINT PK_EXP_DATA_CONNECTION PRIMARY KEY (id);
CREATE UNIQUE INDEX exp_data_connection_identifier ON exp_data_connection (identifier);
CREATE SEQUENCE S_EXP_DATA_CONNECTION AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXP_DATA_CONNECTION IS 'exp_data_connection.id';
CREATE TRIGGER T_EXP_DATA_CONNECTION_BI NO CASCADE BEFORE INSERT ON exp_data_connection REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXP_DATA_CONNECTION;

CREATE TABLE exp_secret (
  ciphertext VARCHAR(32672),
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  description VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  key_id VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(128 CODEUNITS32) DEFAULT '' NOT NULL,
  scope VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE exp_secret ADD CONSTRAINT PK_EXP_SECRET PRIMARY KEY (id);
CREATE UNIQUE INDEX exp_secret_name ON exp_secret (name);
CREATE INDEX exp_secret_scope ON exp_secret (scope);
CREATE SEQUENCE S_EXP_SECRET AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXP_SECRET IS 'exp_secret.id';
CREATE TRIGGER T_EXP_SECRET_BI NO CASCADE BEFORE INSERT ON exp_secret REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXP_SECRET;

CREATE TABLE exp_data_sync (
  class_name VARCHAR(128 CODEUNITS32) DEFAULT '' NOT NULL,
  conflict_rule VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  content_class VARCHAR(128 CODEUNITS32) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  direction VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  interval_seconds INTEGER DEFAULT 0 NOT NULL,
  key_columns VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  last_run INTEGER DEFAULT 0 NOT NULL,
  last_run_id INTEGER DEFAULT 0 NOT NULL,
  map VARCHAR(32672),
  modified INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  next_run INTEGER DEFAULT 0 NOT NULL,
  options VARCHAR(32672),
  run_mode VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  state VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  subtree_node_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exp_data_sync ADD CONSTRAINT PK_EXP_DATA_SYNC PRIMARY KEY (id);
CREATE INDEX exp_data_sync_mode ON exp_data_sync (run_mode, state);
CREATE SEQUENCE S_EXP_DATA_SYNC AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXP_DATA_SYNC IS 'exp_data_sync.id';
CREATE TRIGGER T_EXP_DATA_SYNC_BI NO CASCADE BEFORE INSERT ON exp_data_sync REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXP_DATA_SYNC;

CREATE TABLE exp_data_sync_conflict (
  content_values CLOB(1G),
  detected INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  link_id INTEGER DEFAULT 0 NOT NULL,
  object_id INTEGER DEFAULT 0 NOT NULL,
  resolution VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  resolved INTEGER DEFAULT 0 NOT NULL,
  resolver_id INTEGER DEFAULT 0 NOT NULL,
  row_key VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  row_values CLOB(1G),
  state VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  sync_id INTEGER DEFAULT 0 NOT NULL
);
COMMENT ON COLUMN exp_data_sync_conflict.content_values IS 'exp:clob';
COMMENT ON COLUMN exp_data_sync_conflict.row_values IS 'exp:clob';
ALTER TABLE exp_data_sync_conflict ADD CONSTRAINT PK_EXP_DATA_SYNC_CONFLICT PRIMARY KEY (id);
CREATE INDEX exp_data_sync_conflict_state ON exp_data_sync_conflict (sync_id, state);
CREATE SEQUENCE S_EXP_DATA_SYNC_CONFLICT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXP_DATA_SYNC_CONFLICT IS 'exp_data_sync_conflict.id';
CREATE TRIGGER T_EXP_DATA_SYNC_CONFLICT_BI NO CASCADE BEFORE INSERT ON exp_data_sync_conflict REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXP_DATA_SYNC_CONFLICT;

CREATE TABLE exp_data_sync_link (
  content_hash VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  content_modified INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  object_id INTEGER DEFAULT 0 NOT NULL,
  row_hash VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  row_key VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  row_modified INTEGER DEFAULT 0 NOT NULL,
  sync_id INTEGER DEFAULT 0 NOT NULL,
  synced INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exp_data_sync_link ADD CONSTRAINT PK_EXP_DATA_SYNC_LINK PRIMARY KEY (id);
CREATE UNIQUE INDEX exp_data_sync_link_key ON exp_data_sync_link (sync_id, row_key);
CREATE INDEX exp_data_sync_link_object ON exp_data_sync_link (sync_id, object_id);
CREATE SEQUENCE S_EXP_DATA_SYNC_LINK AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXP_DATA_SYNC_LINK IS 'exp_data_sync_link.id';
CREATE TRIGGER T_EXP_DATA_SYNC_LINK_BI NO CASCADE BEFORE INSERT ON exp_data_sync_link REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXP_DATA_SYNC_LINK;

CREATE TABLE expuser_session (
  browser VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  device_type VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  end_reason VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  ended INTEGER DEFAULT 0 NOT NULL,
  ended_by INTEGER DEFAULT 0 NOT NULL,
  first_ip VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  last_active INTEGER DEFAULT 0 NOT NULL,
  last_ip VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  method VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  os VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  remember_until INTEGER DEFAULT 0 NOT NULL,
  siteaccess VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  token_hash VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  user_agent VARCHAR(512 CODEUNITS32) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expuser_session ADD CONSTRAINT PK_EXPUSER_SESSION PRIMARY KEY (id);
CREATE UNIQUE INDEX expuser_session_token ON expuser_session (token_hash);
CREATE INDEX expuser_session_user ON expuser_session (user_id, ended);
CREATE INDEX expuser_session_active ON expuser_session (ended, last_active);
CREATE SEQUENCE S_EXPUSER_SESSION AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXPUSER_SESSION IS 'expuser_session.id';
CREATE TRIGGER T_EXPUSER_SESSION_BI NO CASCADE BEFORE INSERT ON expuser_session REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXPUSER_SESSION;

CREATE TABLE expuser_session_info (
  auth_detail VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  country VARCHAR(2 CODEUNITS32) DEFAULT '' NOT NULL,
  host VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  location VARCHAR(128 CODEUNITS32) DEFAULT '' NOT NULL,
  session_id INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expuser_session_info ADD CONSTRAINT PK_EXPUSER_SESSION_INFO PRIMARY KEY (session_id);
CREATE INDEX expuser_session_info_user ON expuser_session_info (user_id);

CREATE TABLE expuser_audit (
  actor_id INTEGER DEFAULT 0 NOT NULL,
  actor_login VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  client_ip VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  event_id VARCHAR(26 CODEUNITS32) DEFAULT '' NOT NULL,
  event_name VARCHAR(128 CODEUNITS32) DEFAULT '' NOT NULL,
  host VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  module_view VARCHAR(128 CODEUNITS32) DEFAULT '' NOT NULL,
  reason VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  request_id VARCHAR(40 CODEUNITS32) DEFAULT '' NOT NULL,
  result VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  siteaccess VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  target_id INTEGER DEFAULT 0 NOT NULL,
  target_login VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  target_type VARCHAR(16 CODEUNITS32) DEFAULT '' NOT NULL,
  time_ms BIGINT DEFAULT 0 NOT NULL,
  value_after CLOB(1G),
  value_before CLOB(1G)
);
COMMENT ON COLUMN expuser_audit.value_after IS 'exp:clob';
COMMENT ON COLUMN expuser_audit.value_before IS 'exp:clob';
ALTER TABLE expuser_audit ADD CONSTRAINT PK_EXPUSER_AUDIT PRIMARY KEY (id);
CREATE UNIQUE INDEX expuser_audit_event ON expuser_audit (event_id);
CREATE INDEX expuser_audit_target ON expuser_audit (target_id, created);
CREATE INDEX expuser_audit_actor ON expuser_audit (actor_id, created);
CREATE INDEX expuser_audit_name ON expuser_audit (event_name, created);
CREATE INDEX expuser_audit_created ON expuser_audit (created);
CREATE SEQUENCE S_EXPUSER_AUDIT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXPUSER_AUDIT IS 'expuser_audit.id';
CREATE TRIGGER T_EXPUSER_AUDIT_BI NO CASCADE BEFORE INSERT ON expuser_audit REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXPUSER_AUDIT;

CREATE TABLE exphelp_help_id (
  help_id VARCHAR(80 CODEUNITS32) DEFAULT '' NOT NULL,
  object_id INTEGER DEFAULT 0 NOT NULL,
  released INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exphelp_help_id ADD CONSTRAINT PK_EXPHELP_HELP_ID PRIMARY KEY (help_id);
CREATE INDEX exphelp_help_id_object ON exphelp_help_id (object_id);

CREATE TABLE exphelp_feedback (
  created INTEGER DEFAULT 0 NOT NULL,
  help_id VARCHAR(80 CODEUNITS32) DEFAULT '' NOT NULL,
  helpful INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  language VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  object_id INTEGER DEFAULT 0 NOT NULL,
  reason VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE exphelp_feedback ADD CONSTRAINT PK_EXPHELP_FEEDBACK PRIMARY KEY (id);
CREATE INDEX exphelp_feedback_object ON exphelp_feedback (object_id, created);
CREATE INDEX exphelp_feedback_created ON exphelp_feedback (created);
CREATE SEQUENCE S_EXPHELP_FEEDBACK AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXPHELP_FEEDBACK IS 'exphelp_feedback.id';
CREATE TRIGGER T_EXPHELP_FEEDBACK_BI NO CASCADE BEFORE INSERT ON exphelp_feedback REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXPHELP_FEEDBACK;

CREATE TABLE exp_migration (
  caption_id VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  checksum VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  duration_ms INTEGER DEFAULT 0 NOT NULL,
  engine VARCHAR(30 CODEUNITS32) DEFAULT '' NOT NULL,
  environment VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  file_path VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  finished_at INTEGER DEFAULT 0 NOT NULL,
  message VARCHAR(1000 CODEUNITS32) DEFAULT '' NOT NULL,
  migration_id VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  revision VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  run_by VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  run_id VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  source VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  stage VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  started_at INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  version VARCHAR(30 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE exp_migration ADD CONSTRAINT PK_EXP_MIGRATION PRIMARY KEY (source, migration_id);
CREATE INDEX exp_migration_run_id ON exp_migration (run_id);

CREATE TABLE exp_migration_deploy (
  deploy_id VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  finished_at INTEGER DEFAULT 0 NOT NULL,
  in_sync INTEGER DEFAULT 0 NOT NULL,
  message VARCHAR(1000 CODEUNITS32) DEFAULT '' NOT NULL,
  mode VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  next_stage INTEGER DEFAULT 0 NOT NULL,
  run_by VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  stages INTEGER DEFAULT 0 NOT NULL,
  started_at INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE exp_migration_deploy ADD CONSTRAINT PK_EXP_MIGRATION_DEPLOY PRIMARY KEY (deploy_id);
CREATE INDEX exp_migration_deploy_started ON exp_migration_deploy (started_at);

CREATE TABLE exp_migration_deploy_host (
  address VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  applied INTEGER DEFAULT 0 NOT NULL,
  caption VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  deploy_id VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  environment VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  message VARCHAR(1000 CODEUNITS32) DEFAULT '' NOT NULL,
  remote_id VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  stage INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  transport VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE exp_migration_deploy_host ADD CONSTRAINT PK_EXP_MIGRATION_DEPLOY_HOST PRIMARY KEY (deploy_id, remote_id);
CREATE INDEX exp_migration_deploy_host_remote ON exp_migration_deploy_host (remote_id);

CREATE TABLE exp_migration_run (
  caption_post VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  caption_pre VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  commit_id VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  environment VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  finished_at INTEGER DEFAULT 0 NOT NULL,
  log_file VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  message VARCHAR(1000 CODEUNITS32) DEFAULT '' NOT NULL,
  push_state VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  run_by VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  run_id VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  started_at INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  tag_name VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE exp_migration_run ADD CONSTRAINT PK_EXP_MIGRATION_RUN PRIMARY KEY (run_id);
CREATE INDEX exp_migration_run_started ON exp_migration_run (started_at);

CREATE TABLE exp_static_page (
  built_at INTEGER DEFAULT 0 NOT NULL,
  hash VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  page_bytes INTEGER DEFAULT 0 NOT NULL,
  path VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  stale INTEGER DEFAULT 0 NOT NULL,
  target VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE exp_static_page ADD CONSTRAINT PK_EXP_STATIC_PAGE PRIMARY KEY (target, path);
CREATE INDEX exp_static_page_node ON exp_static_page (node_id);

CREATE TABLE exp_static_run (
  added INTEGER DEFAULT 0 NOT NULL,
  bytes INTEGER DEFAULT 0 NOT NULL,
  changed INTEGER DEFAULT 0 NOT NULL,
  commit_id VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  files INTEGER DEFAULT 0 NOT NULL,
  finished_at INTEGER DEFAULT 0 NOT NULL,
  kind VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  message VARCHAR(1000 CODEUNITS32) DEFAULT '' NOT NULL,
  pages INTEGER DEFAULT 0 NOT NULL,
  push_state VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  removed INTEGER DEFAULT 0 NOT NULL,
  run_by VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  run_id VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  started_at INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  tag_name VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  target VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE exp_static_run ADD CONSTRAINT PK_EXP_STATIC_RUN PRIMARY KEY (run_id);
CREATE INDEX exp_static_run_target ON exp_static_run (target, started_at);

CREATE TABLE exp_migration_recording (
  environment VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  event_count INTEGER DEFAULT 0 NOT NULL,
  kind VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  label VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  message VARCHAR(1000 CODEUNITS32) DEFAULT '' NOT NULL,
  recording_id VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  started_at INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  stopped_at INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exp_migration_recording ADD CONSTRAINT PK_EXP_MIGRATION_RECORDING PRIMARY KEY (recording_id);
CREATE INDEX exp_migration_recording_state ON exp_migration_recording (state);

CREATE TABLE exp_migration_recording_event (
  actor_id INTEGER DEFAULT 0 NOT NULL,
  audit_name VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  event_id VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  payload CLOB(1G),
  recorded_at INTEGER DEFAULT 0 NOT NULL,
  recording_id VARCHAR(64 CODEUNITS32) DEFAULT '' NOT NULL,
  step_mode VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  step_type VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  subject VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
COMMENT ON COLUMN exp_migration_recording_event.payload IS 'exp:clob';
ALTER TABLE exp_migration_recording_event ADD CONSTRAINT PK_EXP_MIGRATION_RECORDING_EVENT PRIMARY KEY (recording_id, event_id);

CREATE TABLE ezproductcategory (
  id INTEGER NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezproductcategory ADD CONSTRAINT PK_EZPRODUCTCATEGORY PRIMARY KEY (id);
CREATE SEQUENCE S_EZPRODUCTCATEGORY AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPRODUCTCATEGORY IS 'ezproductcategory.id';
CREATE TRIGGER T_EZPRODUCTCATEGORY_BI NO CASCADE BEFORE INSERT ON ezproductcategory REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPRODUCTCATEGORY;

CREATE TABLE ezproductcollection (
  created INTEGER DEFAULT NULL,
  currency_code VARCHAR(4 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL
);
ALTER TABLE ezproductcollection ADD CONSTRAINT PK_EZPRODUCTCOLLECTION PRIMARY KEY (id);
CREATE SEQUENCE S_EZPRODUCTCOLLECTION AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPRODUCTCOLLECTION IS 'ezproductcollection.id';
CREATE TRIGGER T_EZPRODUCTCOLLECTION_BI NO CASCADE BEFORE INSERT ON ezproductcollection REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPRODUCTCOLLECTION;

CREATE TABLE ezproductcollection_item (
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  discount REAL DEFAULT NULL,
  id INTEGER NOT NULL,
  is_vat_inc INTEGER DEFAULT NULL,
  item_count INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  price REAL DEFAULT 0,
  productcollection_id INTEGER DEFAULT 0 NOT NULL,
  vat_value REAL DEFAULT NULL
);
ALTER TABLE ezproductcollection_item ADD CONSTRAINT PK_EZPRODUCTCOLLECTION_ITEM PRIMARY KEY (id);
CREATE INDEX ezproductcollection_item_contentobject_id ON ezproductcollection_item (contentobject_id);
CREATE INDEX ezproductcollection_item_productcollection_id ON ezproductcollection_item (productcollection_id);
CREATE SEQUENCE S_EZPRODUCTCOLLECTION_ITEM AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPRODUCTCOLLECTION_ITEM IS 'ezproductcollection_item.id';
CREATE TRIGGER T_EZPRODUCTCOLLECTION_ITEM_BI NO CASCADE BEFORE INSERT ON ezproductcollection_item REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPRODUCTCOLLECTION_ITEM;

CREATE TABLE ezproductcollection_item_opt (
  id INTEGER NOT NULL,
  item_id INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  object_attribute_id INTEGER DEFAULT NULL,
  option_item_id INTEGER DEFAULT 0 NOT NULL,
  price REAL DEFAULT 0 NOT NULL,
  value VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezproductcollection_item_opt ADD CONSTRAINT PK_EZPRODUCTCOLLECTION_ITEM_OPT PRIMARY KEY (id);
CREATE INDEX ezproductcollection_item_opt_item_id ON ezproductcollection_item_opt (item_id);
CREATE SEQUENCE S_EZPRODUCTCOLLECTION_ITEM_OPT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZPRODUCTCOLLECTION_ITEM_OPT IS 'ezproductcollection_item_opt.id';
CREATE TRIGGER T_EZPRODUCTCOLLECTION_ITEM_OPT_BI NO CASCADE BEFORE INSERT ON ezproductcollection_item_opt REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZPRODUCTCOLLECTION_ITEM_OPT;

CREATE TABLE ezpublishingqueueprocesses (
  created INTEGER DEFAULT NULL,
  ezcontentobject_version_id INTEGER DEFAULT 0 NOT NULL,
  finished INTEGER DEFAULT NULL,
  pid INTEGER DEFAULT NULL,
  started INTEGER DEFAULT NULL,
  status INTEGER DEFAULT NULL
);
ALTER TABLE ezpublishingqueueprocesses ADD CONSTRAINT PK_EZPUBLISHINGQUEUEPROCESSES PRIMARY KEY (ezcontentobject_version_id);

CREATE TABLE ezrole (
  id INTEGER NOT NULL,
  is_new INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  value VARCHAR(1 CODEUNITS32) DEFAULT NULL,
  version INTEGER DEFAULT 0
);
COMMENT ON COLUMN ezrole.value IS 'exp:char';
ALTER TABLE ezrole ADD CONSTRAINT PK_EZROLE PRIMARY KEY (id);
CREATE SEQUENCE S_EZROLE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZROLE IS 'ezrole.id';
CREATE TRIGGER T_EZROLE_BI NO CASCADE BEFORE INSERT ON ezrole REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZROLE;

CREATE TABLE ezrss_export (
  access_url VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  opml_head VARCHAR(32672),
  podcast_head VARCHAR(32672),
  active INTEGER DEFAULT NULL,
  created INTEGER DEFAULT NULL,
  creator_id INTEGER DEFAULT NULL,
  description VARCHAR(32672),
  id INTEGER NOT NULL,
  image_id INTEGER DEFAULT NULL,
  main_node_only INTEGER DEFAULT 1 NOT NULL,
  modified INTEGER DEFAULT NULL,
  modifier_id INTEGER DEFAULT NULL,
  node_id INTEGER DEFAULT NULL,
  number_of_objects INTEGER DEFAULT 0 NOT NULL,
  rss_version VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  site_access VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  title VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  url VARCHAR(255 CODEUNITS32) DEFAULT NULL
);
ALTER TABLE ezrss_export ADD CONSTRAINT PK_EZRSS_EXPORT PRIMARY KEY (id, status);
CREATE SEQUENCE S_EZRSS_EXPORT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZRSS_EXPORT IS 'ezrss_export.id';
CREATE TRIGGER T_EZRSS_EXPORT_BI NO CASCADE BEFORE INSERT ON ezrss_export REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZRSS_EXPORT;

CREATE TABLE ezrss_export_item (
  category VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  class_id INTEGER DEFAULT NULL,
  description VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  enclosure VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  id INTEGER NOT NULL,
  rssexport_id INTEGER DEFAULT NULL,
  source_node_id INTEGER DEFAULT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  subnodes INTEGER DEFAULT 0 NOT NULL,
  title VARCHAR(255 CODEUNITS32) DEFAULT NULL
);
ALTER TABLE ezrss_export_item ADD CONSTRAINT PK_EZRSS_EXPORT_ITEM PRIMARY KEY (id, status);
CREATE INDEX ezrss_export_item_ezrss_export_rsseid ON ezrss_export_item (rssexport_id);
COMMENT ON INDEX ezrss_export_item_ezrss_export_rsseid IS 'exp:index:ezrss_export_rsseid';
CREATE SEQUENCE S_EZRSS_EXPORT_ITEM AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZRSS_EXPORT_ITEM IS 'ezrss_export_item.id';
CREATE TRIGGER T_EZRSS_EXPORT_ITEM_BI NO CASCADE BEFORE INSERT ON ezrss_export_item REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZRSS_EXPORT_ITEM;

CREATE TABLE ezrss_export_opml_item (
  category VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  description VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  html_url VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  id INTEGER NOT NULL,
  is_breakpoint INTEGER DEFAULT 0 NOT NULL,
  is_comment INTEGER DEFAULT 0 NOT NULL,
  language VARCHAR(50 CODEUNITS32) DEFAULT NULL,
  outline_text VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  outline_type VARCHAR(50 CODEUNITS32) DEFAULT 'rss',
  parent_id INTEGER DEFAULT 0 NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL,
  rssexport_id INTEGER DEFAULT 0 NOT NULL,
  source_node_id INTEGER DEFAULT 0 NOT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  subnodes INTEGER DEFAULT 0 NOT NULL,
  target_export_id INTEGER DEFAULT 0 NOT NULL,
  title VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  url VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  xml_url VARCHAR(255 CODEUNITS32) DEFAULT NULL
);
ALTER TABLE ezrss_export_opml_item ADD CONSTRAINT PK_EZRSS_EXPORT_OPML_ITEM PRIMARY KEY (id, status);
CREATE INDEX ezrss_export_opml_item_ezrss_export_opml_rsseid ON ezrss_export_opml_item (rssexport_id);
COMMENT ON INDEX ezrss_export_opml_item_ezrss_export_opml_rsseid IS 'exp:index:ezrss_export_opml_rsseid';
CREATE SEQUENCE S_EZRSS_EXPORT_OPML_ITEM AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZRSS_EXPORT_OPML_ITEM IS 'ezrss_export_opml_item.id';
CREATE TRIGGER T_EZRSS_EXPORT_OPML_ITEM_BI NO CASCADE BEFORE INSERT ON ezrss_export_opml_item REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZRSS_EXPORT_OPML_ITEM;

CREATE TABLE ezrss_import (
  active INTEGER DEFAULT NULL,
  class_description VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  class_id INTEGER DEFAULT NULL,
  class_title VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  class_url VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  created INTEGER DEFAULT NULL,
  creator_id INTEGER DEFAULT NULL,
  destination_node_id INTEGER DEFAULT NULL,
  id INTEGER NOT NULL,
  import_description VARCHAR(32672) NOT NULL,
  modified INTEGER DEFAULT NULL,
  modifier_id INTEGER DEFAULT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  object_owner_id INTEGER DEFAULT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  url VARCHAR(32672)
);
ALTER TABLE ezrss_import ADD CONSTRAINT PK_EZRSS_IMPORT PRIMARY KEY (id, status);
CREATE SEQUENCE S_EZRSS_IMPORT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZRSS_IMPORT IS 'ezrss_import.id';
CREATE TRIGGER T_EZRSS_IMPORT_BI NO CASCADE BEFORE INSERT ON ezrss_import REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZRSS_IMPORT;

CREATE TABLE ezscheduled_script (
  command VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  last_report_timestamp INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  process_id INTEGER DEFAULT 0 NOT NULL,
  progress INTEGER DEFAULT 0,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezscheduled_script ADD CONSTRAINT PK_EZSCHEDULED_SCRIPT PRIMARY KEY (id);
CREATE INDEX ezscheduled_script_timestamp ON ezscheduled_script (last_report_timestamp);
CREATE SEQUENCE S_EZSCHEDULED_SCRIPT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZSCHEDULED_SCRIPT IS 'ezscheduled_script.id';
CREATE TRIGGER T_EZSCHEDULED_SCRIPT_BI NO CASCADE BEFORE INSERT ON ezscheduled_script REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZSCHEDULED_SCRIPT;

CREATE TABLE ezsearch_object_word_link (
  contentclass_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  frequency REAL DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  identifier VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  integer_value INTEGER DEFAULT 0 NOT NULL,
  next_word_id INTEGER DEFAULT 0 NOT NULL,
  placement INTEGER DEFAULT 0 NOT NULL,
  prev_word_id INTEGER DEFAULT 0 NOT NULL,
  published INTEGER DEFAULT 0 NOT NULL,
  section_id INTEGER DEFAULT 0 NOT NULL,
  word_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezsearch_object_word_link ADD CONSTRAINT PK_EZSEARCH_OBJECT_WORD_LINK PRIMARY KEY (id);
CREATE INDEX ezsearch_object_word_link_frequency ON ezsearch_object_word_link (frequency);
CREATE INDEX ezsearch_object_word_link_identifier ON ezsearch_object_word_link (identifier);
CREATE INDEX ezsearch_object_word_link_integer_value ON ezsearch_object_word_link (integer_value);
CREATE INDEX ezsearch_object_word_link_object ON ezsearch_object_word_link (contentobject_id);
CREATE INDEX ezsearch_object_word_link_word ON ezsearch_object_word_link (word_id);
CREATE SEQUENCE S_EZSEARCH_OBJECT_WORD_LINK AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZSEARCH_OBJECT_WORD_LINK IS 'ezsearch_object_word_link.id';
CREATE TRIGGER T_EZSEARCH_OBJECT_WORD_LINK_BI NO CASCADE BEFORE INSERT ON ezsearch_object_word_link REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZSEARCH_OBJECT_WORD_LINK;

CREATE TABLE ezsearch_search_phrase (
  id INTEGER NOT NULL,
  phrase VARCHAR(250 CODEUNITS32) DEFAULT NULL,
  phrase_count INTEGER DEFAULT 0,
  result_count INTEGER DEFAULT 0
);
ALTER TABLE ezsearch_search_phrase ADD CONSTRAINT PK_EZSEARCH_SEARCH_PHRASE PRIMARY KEY (id);
CREATE INDEX ezsearch_search_phrase_count ON ezsearch_search_phrase (phrase_count);
CREATE UNIQUE INDEX ezsearch_search_phrase_phrase ON ezsearch_search_phrase (phrase);
CREATE SEQUENCE S_EZSEARCH_SEARCH_PHRASE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZSEARCH_SEARCH_PHRASE IS 'ezsearch_search_phrase.id';
CREATE TRIGGER T_EZSEARCH_SEARCH_PHRASE_BI NO CASCADE BEFORE INSERT ON ezsearch_search_phrase REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZSEARCH_SEARCH_PHRASE;

CREATE TABLE ezsearch_word (
  id INTEGER NOT NULL,
  object_count INTEGER DEFAULT 0 NOT NULL,
  word VARCHAR(150 CODEUNITS32) DEFAULT NULL
);
ALTER TABLE ezsearch_word ADD CONSTRAINT PK_EZSEARCH_WORD PRIMARY KEY (id);
CREATE INDEX ezsearch_word_obj_count ON ezsearch_word (object_count);
CREATE INDEX ezsearch_word_word_i ON ezsearch_word (word);
CREATE SEQUENCE S_EZSEARCH_WORD AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZSEARCH_WORD IS 'ezsearch_word.id';
CREATE TRIGGER T_EZSEARCH_WORD_BI NO CASCADE BEFORE INSERT ON ezsearch_word REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZSEARCH_WORD;

CREATE TABLE ezsection (
  id INTEGER NOT NULL,
  identifier VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  locale VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  navigation_part_identifier VARCHAR(100 CODEUNITS32) DEFAULT 'ezcontentnavigationpart'
);
ALTER TABLE ezsection ADD CONSTRAINT PK_EZSECTION PRIMARY KEY (id);
CREATE SEQUENCE S_EZSECTION AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZSECTION IS 'ezsection.id';
CREATE TRIGGER T_EZSECTION_BI NO CASCADE BEFORE INSERT ON ezsection REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZSECTION;

CREATE TABLE ezsession (
  data CLOB(1G) NOT NULL,
  expiration_time INTEGER DEFAULT 0 NOT NULL,
  session_key VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  user_hash VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
COMMENT ON COLUMN ezsession.data IS 'exp:clob';
ALTER TABLE ezsession ADD CONSTRAINT PK_EZSESSION PRIMARY KEY (session_key);
CREATE INDEX ezsession_expiration_time ON ezsession (expiration_time);
COMMENT ON INDEX ezsession_expiration_time IS 'exp:index:expiration_time';
CREATE INDEX ezsession_user_id ON ezsession (user_id);

CREATE TABLE ezsite_data (
  name VARCHAR(60 CODEUNITS32) DEFAULT '' NOT NULL,
  value VARCHAR(32672) NOT NULL
);
ALTER TABLE ezsite_data ADD CONSTRAINT PK_EZSITE_DATA PRIMARY KEY (name);

CREATE TABLE ezsubtree_notification_rule (
  id INTEGER NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  use_digest INTEGER DEFAULT 0,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezsubtree_notification_rule ADD CONSTRAINT PK_EZSUBTREE_NOTIFICATION_RULE PRIMARY KEY (id);
CREATE INDEX ezsubtree_notification_rule_user_id ON ezsubtree_notification_rule (user_id);
CREATE SEQUENCE S_EZSUBTREE_NOTIFICATION_RULE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZSUBTREE_NOTIFICATION_RULE IS 'ezsubtree_notification_rule.id';
CREATE TRIGGER T_EZSUBTREE_NOTIFICATION_RULE_BI NO CASCADE BEFORE INSERT ON ezsubtree_notification_rule REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZSUBTREE_NOTIFICATION_RULE;

CREATE TABLE eztipafriend_counter (
  count INTEGER DEFAULT 0 NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  requested INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE eztipafriend_counter ADD CONSTRAINT PK_EZTIPAFRIEND_COUNTER PRIMARY KEY (node_id, requested);

CREATE TABLE eztipafriend_request (
  created INTEGER DEFAULT 0 NOT NULL,
  email_receiver VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL
);
CREATE INDEX eztipafriend_request_created ON eztipafriend_request (created);
CREATE INDEX eztipafriend_request_email_rec ON eztipafriend_request (email_receiver);

CREATE TABLE eztrigger (
  connect_type VARCHAR(1 CODEUNITS32) DEFAULT '' NOT NULL,
  function_name VARCHAR(200 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  module_name VARCHAR(200 CODEUNITS32) DEFAULT '' NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  workflow_id INTEGER DEFAULT NULL
);
COMMENT ON COLUMN eztrigger.connect_type IS 'exp:char';
ALTER TABLE eztrigger ADD CONSTRAINT PK_EZTRIGGER PRIMARY KEY (id);
CREATE UNIQUE INDEX eztrigger_def_id ON eztrigger (module_name, function_name, connect_type);
CREATE INDEX eztrigger_fetch ON eztrigger (name, module_name, function_name);
CREATE SEQUENCE S_EZTRIGGER AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZTRIGGER IS 'eztrigger.id';
CREATE TRIGGER T_EZTRIGGER_BI NO CASCADE BEFORE INSERT ON eztrigger REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZTRIGGER;

CREATE TABLE ezurl (
  created INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  is_valid INTEGER DEFAULT 1 NOT NULL,
  last_checked INTEGER DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  original_url_md5 VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  url VARCHAR(2022 CODEUNITS32)
);
COMMENT ON COLUMN ezurl.url IS 'exp:longtext';
ALTER TABLE ezurl ADD CONSTRAINT PK_EZURL PRIMARY KEY (id);
CREATE INDEX ezurl_url ON ezurl (url);
CREATE SEQUENCE S_EZURL AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZURL IS 'ezurl.id';
CREATE TRIGGER T_EZURL_BI NO CASCADE BEFORE INSERT ON ezurl REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZURL;

CREATE TABLE ezurl_object_link (
  contentobject_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_attribute_version INTEGER DEFAULT 0 NOT NULL,
  url_id INTEGER DEFAULT 0 NOT NULL
);
CREATE INDEX ezurl_object_link_ezurl_ol_coa_id ON ezurl_object_link (contentobject_attribute_id);
COMMENT ON INDEX ezurl_object_link_ezurl_ol_coa_id IS 'exp:index:ezurl_ol_coa_id';
CREATE INDEX ezurl_object_link_ezurl_ol_coa_version ON ezurl_object_link (contentobject_attribute_version);
COMMENT ON INDEX ezurl_object_link_ezurl_ol_coa_version IS 'exp:index:ezurl_ol_coa_version';
CREATE INDEX ezurl_object_link_ezurl_ol_url_id ON ezurl_object_link (url_id);
COMMENT ON INDEX ezurl_object_link_ezurl_ol_url_id IS 'exp:index:ezurl_ol_url_id';

CREATE TABLE ezurlalias (
  destination_url VARCHAR(2022 CODEUNITS32) NOT NULL,
  forward_to_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  is_imported INTEGER DEFAULT 0 NOT NULL,
  is_internal INTEGER DEFAULT 1 NOT NULL,
  is_wildcard INTEGER DEFAULT 0 NOT NULL,
  source_md5 VARCHAR(32 CODEUNITS32) DEFAULT NULL,
  source_url VARCHAR(2022 CODEUNITS32) NOT NULL
);
COMMENT ON COLUMN ezurlalias.destination_url IS 'exp:longtext';
COMMENT ON COLUMN ezurlalias.source_url IS 'exp:longtext';
ALTER TABLE ezurlalias ADD CONSTRAINT PK_EZURLALIAS PRIMARY KEY (id);
CREATE INDEX ezurlalias_desturl ON ezurlalias (destination_url);
CREATE INDEX ezurlalias_forward_to_id ON ezurlalias (forward_to_id);
CREATE INDEX ezurlalias_imp_wcard_fwd ON ezurlalias (is_imported, is_wildcard, forward_to_id);
CREATE INDEX ezurlalias_source_md5 ON ezurlalias (source_md5);
CREATE INDEX ezurlalias_source_url ON ezurlalias (source_url);
CREATE INDEX ezurlalias_wcard_fwd ON ezurlalias (is_wildcard, forward_to_id);
CREATE SEQUENCE S_EZURLALIAS AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZURLALIAS IS 'ezurlalias.id';
CREATE TRIGGER T_EZURLALIAS_BI NO CASCADE BEFORE INSERT ON ezurlalias REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZURLALIAS;

CREATE TABLE ezurlalias_ml (
  action VARCHAR(2019 CODEUNITS32) NOT NULL,
  action_type VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  alias_redirects INTEGER DEFAULT 1 NOT NULL,
  id INTEGER DEFAULT 0 NOT NULL,
  is_alias INTEGER DEFAULT 0 NOT NULL,
  is_original INTEGER DEFAULT 0 NOT NULL,
  lang_mask BIGINT DEFAULT 0 NOT NULL,
  link INTEGER DEFAULT 0 NOT NULL,
  parent INTEGER DEFAULT 0 NOT NULL,
  text VARCHAR(2019 CODEUNITS32) NOT NULL,
  text_md5 VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL
);
COMMENT ON COLUMN ezurlalias_ml.action IS 'exp:longtext';
COMMENT ON COLUMN ezurlalias_ml.text IS 'exp:longtext';
ALTER TABLE ezurlalias_ml ADD CONSTRAINT PK_EZURLALIAS_ML PRIMARY KEY (parent, text_md5);
CREATE INDEX ezurlalias_ml_act_org ON ezurlalias_ml (action, is_original);
CREATE INDEX ezurlalias_ml_actt_org_al ON ezurlalias_ml (action_type, is_original, is_alias);
CREATE INDEX ezurlalias_ml_id ON ezurlalias_ml (id);
CREATE INDEX ezurlalias_ml_par_act_id_lnk ON ezurlalias_ml (action, id, link, parent);
CREATE INDEX ezurlalias_ml_par_lnk_txt ON ezurlalias_ml (parent, text, link);
CREATE INDEX ezurlalias_ml_text ON ezurlalias_ml (text, id, link);
CREATE INDEX ezurlalias_ml_text_lang ON ezurlalias_ml (text, lang_mask, parent);

CREATE TABLE ezurlalias_ml_incr (
  id INTEGER NOT NULL
);
ALTER TABLE ezurlalias_ml_incr ADD CONSTRAINT PK_EZURLALIAS_ML_INCR PRIMARY KEY (id);
CREATE SEQUENCE S_EZURLALIAS_ML_INCR AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZURLALIAS_ML_INCR IS 'ezurlalias_ml_incr.id';
CREATE TRIGGER T_EZURLALIAS_ML_INCR_BI NO CASCADE BEFORE INSERT ON ezurlalias_ml_incr REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZURLALIAS_ML_INCR;

CREATE TABLE ezurlwildcard (
  destination_url VARCHAR(32672) NOT NULL,
  id INTEGER NOT NULL,
  source_url VARCHAR(32672) NOT NULL,
  type INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezurlwildcard ADD CONSTRAINT PK_EZURLWILDCARD PRIMARY KEY (id);
CREATE SEQUENCE S_EZURLWILDCARD AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZURLWILDCARD IS 'ezurlwildcard.id';
CREATE TRIGGER T_EZURLWILDCARD_BI NO CASCADE BEFORE INSERT ON ezurlwildcard REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZURLWILDCARD;

CREATE TABLE ezuser (
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  email VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  login VARCHAR(150 CODEUNITS32) DEFAULT '' NOT NULL,
  password_hash VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  password_hash_type INTEGER DEFAULT 1 NOT NULL
);
ALTER TABLE ezuser ADD CONSTRAINT PK_EZUSER PRIMARY KEY (contentobject_id);
CREATE UNIQUE INDEX ezuser_login ON ezuser (login);

CREATE TABLE ezuser_accountkey (
  hash_key VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  time INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezuser_accountkey ADD CONSTRAINT PK_EZUSER_ACCOUNTKEY PRIMARY KEY (id);
CREATE INDEX ezuser_accountkey_hash_key ON ezuser_accountkey (hash_key);
COMMENT ON INDEX ezuser_accountkey_hash_key IS 'exp:index:hash_key';
CREATE SEQUENCE S_EZUSER_ACCOUNTKEY AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZUSER_ACCOUNTKEY IS 'ezuser_accountkey.id';
CREATE TRIGGER T_EZUSER_ACCOUNTKEY_BI NO CASCADE BEFORE INSERT ON ezuser_accountkey REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZUSER_ACCOUNTKEY;

CREATE TABLE ezuser_discountrule (
  contentobject_id INTEGER DEFAULT NULL,
  discountrule_id INTEGER DEFAULT NULL,
  id INTEGER NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezuser_discountrule ADD CONSTRAINT PK_EZUSER_DISCOUNTRULE PRIMARY KEY (id);
CREATE SEQUENCE S_EZUSER_DISCOUNTRULE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZUSER_DISCOUNTRULE IS 'ezuser_discountrule.id';
CREATE TRIGGER T_EZUSER_DISCOUNTRULE_BI NO CASCADE BEFORE INSERT ON ezuser_discountrule REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZUSER_DISCOUNTRULE;

CREATE TABLE ezuser_role (
  contentobject_id INTEGER DEFAULT NULL,
  id INTEGER NOT NULL,
  limit_identifier VARCHAR(255 CODEUNITS32) DEFAULT '',
  limit_value VARCHAR(255 CODEUNITS32) DEFAULT '',
  role_id INTEGER DEFAULT NULL
);
ALTER TABLE ezuser_role ADD CONSTRAINT PK_EZUSER_ROLE PRIMARY KEY (id);
CREATE INDEX ezuser_role_contentobject_id ON ezuser_role (contentobject_id);
CREATE INDEX ezuser_role_role_id ON ezuser_role (role_id);
CREATE SEQUENCE S_EZUSER_ROLE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZUSER_ROLE IS 'ezuser_role.id';
CREATE TRIGGER T_EZUSER_ROLE_BI NO CASCADE BEFORE INSERT ON ezuser_role REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZUSER_ROLE;

CREATE TABLE ezuser_setting (
  is_enabled INTEGER DEFAULT 0 NOT NULL,
  max_login INTEGER DEFAULT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezuser_setting ADD CONSTRAINT PK_EZUSER_SETTING PRIMARY KEY (user_id);

CREATE TABLE ezuservisit (
  current_visit_timestamp INTEGER DEFAULT 0 NOT NULL,
  failed_login_attempts INTEGER DEFAULT 0 NOT NULL,
  last_visit_timestamp INTEGER DEFAULT 0 NOT NULL,
  login_count INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezuservisit ADD CONSTRAINT PK_EZUSERVISIT PRIMARY KEY (user_id);
CREATE INDEX ezuservisit_co_visit_count ON ezuservisit (current_visit_timestamp, login_count);

CREATE TABLE ezvatrule (
  country_code VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  vat_type INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezvatrule ADD CONSTRAINT PK_EZVATRULE PRIMARY KEY (id);
CREATE SEQUENCE S_EZVATRULE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZVATRULE IS 'ezvatrule.id';
CREATE TRIGGER T_EZVATRULE_BI NO CASCADE BEFORE INSERT ON ezvatrule REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZVATRULE;

CREATE TABLE ezvatrule_product_category (
  product_category_id INTEGER DEFAULT 0 NOT NULL,
  vatrule_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezvatrule_product_category ADD CONSTRAINT PK_EZVATRULE_PRODUCT_CATEGORY PRIMARY KEY (vatrule_id, product_category_id);

CREATE TABLE ezvattype (
  id INTEGER NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  percentage REAL DEFAULT NULL
);
ALTER TABLE ezvattype ADD CONSTRAINT PK_EZVATTYPE PRIMARY KEY (id);
CREATE SEQUENCE S_EZVATTYPE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZVATTYPE IS 'ezvattype.id';
CREATE TRIGGER T_EZVATTYPE_BI NO CASCADE BEFORE INSERT ON ezvattype REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZVATTYPE;

CREATE TABLE ezview_counter (
  count INTEGER DEFAULT 0 NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezview_counter ADD CONSTRAINT PK_EZVIEW_COUNTER PRIMARY KEY (node_id);

CREATE TABLE ezwaituntildatevalue (
  contentclass_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  workflow_event_id INTEGER DEFAULT 0 NOT NULL,
  workflow_event_version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezwaituntildatevalue ADD CONSTRAINT PK_EZWAITUNTILDATEVALUE PRIMARY KEY (id, workflow_event_id, workflow_event_version);
CREATE INDEX ezwaituntildatevalue_ezwaituntildateevalue_wf_ev_id_wf_ver ON ezwaituntildatevalue (workflow_event_id, workflow_event_version);
COMMENT ON INDEX ezwaituntildatevalue_ezwaituntildateevalue_wf_ev_id_wf_ver IS 'exp:index:ezwaituntildateevalue_wf_ev_id_wf_ver';
CREATE SEQUENCE S_EZWAITUNTILDATEVALUE AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZWAITUNTILDATEVALUE IS 'ezwaituntildatevalue.id';
CREATE TRIGGER T_EZWAITUNTILDATEVALUE_BI NO CASCADE BEFORE INSERT ON ezwaituntildatevalue REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZWAITUNTILDATEVALUE;

CREATE TABLE ezwishlist (
  id INTEGER NOT NULL,
  productcollection_id INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezwishlist ADD CONSTRAINT PK_EZWISHLIST PRIMARY KEY (id);
CREATE SEQUENCE S_EZWISHLIST AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZWISHLIST IS 'ezwishlist.id';
CREATE TRIGGER T_EZWISHLIST_BI NO CASCADE BEFORE INSERT ON ezwishlist REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZWISHLIST;

CREATE TABLE ezworkflow (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  is_enabled INTEGER DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL,
  workflow_type_string VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezworkflow ADD CONSTRAINT PK_EZWORKFLOW PRIMARY KEY (id, version);
CREATE SEQUENCE S_EZWORKFLOW AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZWORKFLOW IS 'ezworkflow.id';
CREATE TRIGGER T_EZWORKFLOW_BI NO CASCADE BEFORE INSERT ON ezworkflow REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZWORKFLOW;

CREATE TABLE ezworkflow_assign (
  access_type INTEGER DEFAULT 0 NOT NULL,
  as_tree INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  workflow_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezworkflow_assign ADD CONSTRAINT PK_EZWORKFLOW_ASSIGN PRIMARY KEY (id);
CREATE SEQUENCE S_EZWORKFLOW_ASSIGN AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZWORKFLOW_ASSIGN IS 'ezworkflow_assign.id';
CREATE TRIGGER T_EZWORKFLOW_ASSIGN_BI NO CASCADE BEFORE INSERT ON ezworkflow_assign REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZWORKFLOW_ASSIGN;

CREATE TABLE ezworkflow_event (
  data_int1 INTEGER DEFAULT NULL,
  data_int2 INTEGER DEFAULT NULL,
  data_int3 INTEGER DEFAULT NULL,
  data_int4 INTEGER DEFAULT NULL,
  data_text1 VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  data_text2 VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  data_text3 VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  data_text4 VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  data_text5 VARCHAR(32672),
  description VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  placement INTEGER DEFAULT 0 NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL,
  workflow_id INTEGER DEFAULT 0 NOT NULL,
  workflow_type_string VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezworkflow_event ADD CONSTRAINT PK_EZWORKFLOW_EVENT PRIMARY KEY (id, version);
CREATE INDEX ezworkflow_event_wid_version_placement ON ezworkflow_event (workflow_id, version, placement);
COMMENT ON INDEX ezworkflow_event_wid_version_placement IS 'exp:index:wid_version_placement';
CREATE SEQUENCE S_EZWORKFLOW_EVENT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZWORKFLOW_EVENT IS 'ezworkflow_event.id';
CREATE TRIGGER T_EZWORKFLOW_EVENT_BI NO CASCADE BEFORE INSERT ON ezworkflow_event REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZWORKFLOW_EVENT;

CREATE TABLE ezworkflow_group (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE ezworkflow_group ADD CONSTRAINT PK_EZWORKFLOW_GROUP PRIMARY KEY (id);
CREATE SEQUENCE S_EZWORKFLOW_GROUP AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZWORKFLOW_GROUP IS 'ezworkflow_group.id';
CREATE TRIGGER T_EZWORKFLOW_GROUP_BI NO CASCADE BEFORE INSERT ON ezworkflow_group REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZWORKFLOW_GROUP;

CREATE TABLE ezworkflow_group_link (
  group_id INTEGER DEFAULT 0 NOT NULL,
  group_name VARCHAR(255 CODEUNITS32) DEFAULT NULL,
  workflow_id INTEGER DEFAULT 0 NOT NULL,
  workflow_version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezworkflow_group_link ADD CONSTRAINT PK_EZWORKFLOW_GROUP_LINK PRIMARY KEY (workflow_id, group_id, workflow_version);

CREATE TABLE ezworkflow_process (
  activation_date INTEGER DEFAULT NULL,
  content_id INTEGER DEFAULT 0 NOT NULL,
  content_version INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  event_id INTEGER DEFAULT 0 NOT NULL,
  event_position INTEGER DEFAULT 0 NOT NULL,
  event_state INTEGER DEFAULT NULL,
  event_status INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  last_event_id INTEGER DEFAULT 0 NOT NULL,
  last_event_position INTEGER DEFAULT 0 NOT NULL,
  last_event_status INTEGER DEFAULT 0 NOT NULL,
  memento_key VARCHAR(32 CODEUNITS32) DEFAULT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  parameters CLOB(1G),
  process_key VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  session_key VARCHAR(32 CODEUNITS32) DEFAULT '0' NOT NULL,
  status INTEGER DEFAULT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL,
  workflow_id INTEGER DEFAULT 0 NOT NULL
);
COMMENT ON COLUMN ezworkflow_process.parameters IS 'exp:clob';
ALTER TABLE ezworkflow_process ADD CONSTRAINT PK_EZWORKFLOW_PROCESS PRIMARY KEY (id);
CREATE INDEX ezworkflow_process_process_key ON ezworkflow_process (process_key);
CREATE SEQUENCE S_EZWORKFLOW_PROCESS AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EZWORKFLOW_PROCESS IS 'ezworkflow_process.id';
CREATE TRIGGER T_EZWORKFLOW_PROCESS_BI NO CASCADE BEFORE INSERT ON ezworkflow_process REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EZWORKFLOW_PROCESS;

CREATE TABLE exp_url_redirect (
  batch_id INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  id INTEGER NOT NULL,
  language VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  siteaccess VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  source_key VARCHAR(32 CODEUNITS32) DEFAULT '' NOT NULL,
  source_path VARCHAR(1000 CODEUNITS32) DEFAULT '' NOT NULL,
  source_query VARCHAR(1000 CODEUNITS32) DEFAULT '' NOT NULL,
  status INTEGER DEFAULT 301 NOT NULL,
  target VARCHAR(2000 CODEUNITS32) DEFAULT '' NOT NULL
);
ALTER TABLE exp_url_redirect ADD CONSTRAINT PK_EXP_URL_REDIRECT PRIMARY KEY (id);
CREATE INDEX exp_url_redirect_batch ON exp_url_redirect (batch_id);
CREATE INDEX exp_url_redirect_source ON exp_url_redirect (source_key);
CREATE SEQUENCE S_EXP_URL_REDIRECT AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXP_URL_REDIRECT IS 'exp_url_redirect.id';
CREATE TRIGGER T_EXP_URL_REDIRECT_BI NO CASCADE BEFORE INSERT ON exp_url_redirect REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXP_URL_REDIRECT;

CREATE TABLE exp_urlmap_batch (
  created INTEGER DEFAULT 0 NOT NULL,
  created_count INTEGER DEFAULT 0 NOT NULL,
  failed_count INTEGER DEFAULT 0 NOT NULL,
  format VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  id INTEGER NOT NULL,
  language VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  on_conflict VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  rows_total INTEGER DEFAULT 0 NOT NULL,
  siteaccess VARCHAR(100 CODEUNITS32) DEFAULT '' NOT NULL,
  skipped_count INTEGER DEFAULT 0 NOT NULL,
  source_name VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  state VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  summary VARCHAR(32672),
  undone INTEGER DEFAULT 0 NOT NULL,
  undone_by INTEGER DEFAULT 0 NOT NULL,
  updated_count INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exp_urlmap_batch ADD CONSTRAINT PK_EXP_URLMAP_BATCH PRIMARY KEY (id);
CREATE INDEX exp_urlmap_batch_created ON exp_urlmap_batch (created);
CREATE SEQUENCE S_EXP_URLMAP_BATCH AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXP_URLMAP_BATCH IS 'exp_urlmap_batch.id';
CREATE TRIGGER T_EXP_URLMAP_BATCH_BI NO CASCADE BEFORE INSERT ON exp_urlmap_batch REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXP_URLMAP_BATCH;

CREATE TABLE exp_urlmap_batch_item (
  action VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  after_image CLOB(1G),
  batch_id INTEGER DEFAULT 0 NOT NULL,
  before_image CLOB(1G),
  id INTEGER NOT NULL,
  kind VARCHAR(30 CODEUNITS32) DEFAULT '' NOT NULL,
  line INTEGER DEFAULT 0 NOT NULL,
  old_url VARCHAR(1000 CODEUNITS32) DEFAULT '' NOT NULL,
  seq INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(20 CODEUNITS32) DEFAULT '' NOT NULL,
  target_key VARCHAR(255 CODEUNITS32) DEFAULT '' NOT NULL,
  target_table VARCHAR(50 CODEUNITS32) DEFAULT '' NOT NULL
);
COMMENT ON COLUMN exp_urlmap_batch_item.after_image IS 'exp:clob';
COMMENT ON COLUMN exp_urlmap_batch_item.before_image IS 'exp:clob';
ALTER TABLE exp_urlmap_batch_item ADD CONSTRAINT PK_EXP_URLMAP_BATCH_ITEM PRIMARY KEY (id);
CREATE INDEX exp_urlmap_batch_item_batch ON exp_urlmap_batch_item (batch_id, seq);
CREATE SEQUENCE S_EXP_URLMAP_BATCH_ITEM AS INTEGER START WITH 1 INCREMENT BY 1 NO CYCLE CACHE 20;
COMMENT ON SEQUENCE S_EXP_URLMAP_BATCH_ITEM IS 'exp_urlmap_batch_item.id';
CREATE TRIGGER T_EXP_URLMAP_BATCH_ITEM_BI NO CASCADE BEFORE INSERT ON exp_urlmap_batch_item REFERENCING NEW AS N FOR EACH ROW WHEN (N.id IS NULL OR N.id = 0) SET N.id = NEXT VALUE FOR S_EXP_URLMAP_BATCH_ITEM;
