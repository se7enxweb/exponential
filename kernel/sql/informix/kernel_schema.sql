-- Written by expInformixSchema for Informix 14.10 and 15 (dbaccess <database> <file>), for a database in a dbspace of 16 KB pages

CREATE TABLE expaudit_cursor (
  byte_offset BIGINT DEFAULT 0 NOT NULL,
  channel VARCHAR(128) DEFAULT '' NOT NULL,
  file_name LVARCHAR(256) DEFAULT '' NOT NULL,
  last_hash LVARCHAR(320) DEFAULT NULL,
  last_seq INTEGER DEFAULT 0 NOT NULL,
  updated_ms BIGINT DEFAULT 0 NOT NULL
);
ALTER TABLE expaudit_cursor ADD CONSTRAINT PRIMARY KEY (channel, file_name) CONSTRAINT pk_expaudit_cursor;

CREATE TABLE expaudit_event (
  channel VARCHAR(128) DEFAULT '' NOT NULL,
  depth INTEGER DEFAULT 0 NOT NULL,
  domain_name VARCHAR(64) DEFAULT '' NOT NULL,
  engine VARCHAR(64) DEFAULT NULL,
  file_name LVARCHAR(256) DEFAULT '' NOT NULL,
  id VARCHAR(104) DEFAULT '' NOT NULL,
  imported INTEGER DEFAULT 0 NOT NULL,
  ip LVARCHAR(256) DEFAULT NULL,
  job_id VARCHAR(128) DEFAULT NULL,
  login LVARCHAR(600) DEFAULT NULL,
  module_view LVARCHAR(512) DEFAULT NULL,
  name LVARCHAR(512) DEFAULT '' NOT NULL,
  object_id LVARCHAR(256) DEFAULT NULL,
  object_name LVARCHAR(1020) DEFAULT NULL,
  object_type VARCHAR(128) DEFAULT NULL,
  parent_id VARCHAR(104) DEFAULT NULL,
  pseudonymised INTEGER DEFAULT 0 NOT NULL,
  reason VARCHAR(128) DEFAULT NULL,
  record CLOB,
  request_id VARCHAR(160) DEFAULT NULL,
  result VARCHAR(32) DEFAULT NULL,
  run_id VARCHAR(160) DEFAULT NULL,
  search_text LVARCHAR(25773) DEFAULT NULL,
  seq INTEGER DEFAULT 0 NOT NULL,
  session_h VARCHAR(96) DEFAULT NULL,
  severity INTEGER DEFAULT 0 NOT NULL,
  siteaccess LVARCHAR(256) DEFAULT NULL,
  target_id LVARCHAR(256) DEFAULT NULL,
  target_type VARCHAR(128) DEFAULT NULL,
  time_ms BIGINT DEFAULT 0 NOT NULL,
  ua LVARCHAR(512) DEFAULT NULL,
  user_id INTEGER DEFAULT NULL,
  verb VARCHAR(128) DEFAULT NULL
);
ALTER TABLE expaudit_event ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_expaudit_event;
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
  archive_path LVARCHAR(1020) DEFAULT NULL,
  break_line INTEGER DEFAULT 0 NOT NULL,
  channel VARCHAR(128) DEFAULT '' NOT NULL,
  file_name LVARCHAR(256) DEFAULT '' NOT NULL,
  records INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(64) DEFAULT 'live' NOT NULL,
  verified VARCHAR(64) DEFAULT 'unchecked' NOT NULL,
  verified_ms BIGINT DEFAULT 0 NOT NULL
);
ALTER TABLE expaudit_file ADD CONSTRAINT PRIMARY KEY (channel, file_name) CONSTRAINT pk_expaudit_file;

CREATE TABLE expmail_category (
  created INTEGER DEFAULT 0 NOT NULL,
  default_on INTEGER DEFAULT 0 NOT NULL,
  description LVARCHAR(29061) DEFAULT NULL,
  double_opt_in INTEGER DEFAULT 0 NOT NULL,
  essential INTEGER DEFAULT 0 NOT NULL,
  frequencies LVARCHAR(400) DEFAULT '' NOT NULL,
  handler_class LVARCHAR(1020) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  identifier LVARCHAR(400) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expmail_category ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_expmail_category;
CREATE UNIQUE INDEX expmail_category_identifier ON expmail_category (identifier);

CREATE TABLE expmail_consent_log (
  action VARCHAR(120) DEFAULT '' NOT NULL,
  actor_user_id INTEGER DEFAULT 0 NOT NULL,
  anonymised INTEGER DEFAULT 0 NOT NULL,
  category LVARCHAR(400) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  email LVARCHAR(1020) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  ip LVARCHAR(256) DEFAULT '' NOT NULL,
  new_value LVARCHAR(400) DEFAULT '' NOT NULL,
  old_value LVARCHAR(400) DEFAULT '' NOT NULL,
  recipient_key LVARCHAR(320) DEFAULT '' NOT NULL,
  siteaccess LVARCHAR(400) DEFAULT '' NOT NULL,
  source VARCHAR(80) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL,
  wording LVARCHAR(28514) DEFAULT NULL
);
ALTER TABLE expmail_consent_log ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_expmail_consent_log;
CREATE INDEX expmail_consent_log_created ON expmail_consent_log (created);
CREATE INDEX expmail_consent_log_recipient ON expmail_consent_log (recipient_key, created);
CREATE INDEX expmail_consent_log_user ON expmail_consent_log (user_id);

CREATE TABLE expmail_pending (
  category LVARCHAR(400) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  data LVARCHAR(31140) DEFAULT NULL,
  expires INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  kind VARCHAR(80) DEFAULT '' NOT NULL,
  recipient_key LVARCHAR(320) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expmail_pending ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_expmail_pending;
CREATE INDEX expmail_pending_expires ON expmail_pending (expires);
CREATE INDEX expmail_pending_recipient ON expmail_pending (recipient_key, kind);

CREATE TABLE expmail_preference (
  category LVARCHAR(400) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  frequency VARCHAR(80) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  recipient_key LVARCHAR(320) DEFAULT '' NOT NULL,
  state VARCHAR(80) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expmail_preference ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_expmail_preference;
CREATE INDEX expmail_preference_category ON expmail_preference (category, state);
CREATE UNIQUE INDEX expmail_preference_recipient ON expmail_preference (recipient_key, category);
CREATE INDEX expmail_preference_user ON expmail_preference (user_id);

CREATE TABLE expmail_suppression (
  created INTEGER DEFAULT 0 NOT NULL,
  created_by INTEGER DEFAULT 0 NOT NULL,
  email_hash LVARCHAR(256) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  note LVARCHAR(31579) DEFAULT NULL,
  reason VARCHAR(120) DEFAULT '' NOT NULL
);
ALTER TABLE expmail_suppression ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_expmail_suppression;
CREATE UNIQUE INDEX expmail_suppression_hash ON expmail_suppression (email_hash);

CREATE TABLE ezapprove_items (
  collaboration_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  workflow_process_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezapprove_items ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezapprove_items;

CREATE TABLE ezbasket (
  id SERIAL NOT NULL,
  order_id INTEGER DEFAULT 0 NOT NULL,
  productcollection_id INTEGER DEFAULT 0 NOT NULL,
  session_id LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezbasket ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezbasket;
CREATE INDEX ezbasket_session_id ON ezbasket (session_id);

CREATE TABLE ezbinaryfile (
  contentobject_attribute_id INTEGER DEFAULT 0 NOT NULL,
  download_count INTEGER DEFAULT 0 NOT NULL,
  filename LVARCHAR(1020) DEFAULT '' NOT NULL,
  mime_type LVARCHAR(1020) DEFAULT '' NOT NULL,
  original_filename LVARCHAR(1020) DEFAULT '' NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezbinaryfile ADD CONSTRAINT PRIMARY KEY (contentobject_attribute_id, version) CONSTRAINT pk_ezbinaryfile;

CREATE TABLE ezcobj_state (
  default_language_id BIGINT DEFAULT 0 NOT NULL,
  group_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  identifier VARCHAR(180) DEFAULT '' NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcobj_state;
CREATE UNIQUE INDEX ezcobj_state_identifier ON ezcobj_state (group_id, identifier);
CREATE INDEX ezcobj_state_lmask ON ezcobj_state (language_mask);
CREATE INDEX ezcobj_state_priority ON ezcobj_state (priority);

CREATE TABLE ezcobj_state_group (
  default_language_id BIGINT DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  identifier VARCHAR(180) DEFAULT '' NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state_group ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcobj_state_group;
CREATE UNIQUE INDEX ezcobj_state_group_identifier ON ezcobj_state_group (identifier);
CREATE INDEX ezcobj_state_group_lmask ON ezcobj_state_group (language_mask);

CREATE TABLE ezcobj_state_group_language (
  contentobject_state_group_id INTEGER DEFAULT 0 NOT NULL,
  description LVARCHAR(31778) NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  name VARCHAR(180) DEFAULT '' NOT NULL,
  real_language_id BIGINT DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state_group_language ADD CONSTRAINT PRIMARY KEY (contentobject_state_group_id, real_language_id) CONSTRAINT pk_ezcobj_state_group_language;

CREATE TABLE ezcobj_state_language (
  contentobject_state_id INTEGER DEFAULT 0 NOT NULL,
  description LVARCHAR(31790) NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  name VARCHAR(180) DEFAULT '' NOT NULL
);
ALTER TABLE ezcobj_state_language ADD CONSTRAINT PRIMARY KEY (contentobject_state_id, language_id) CONSTRAINT pk_ezcobj_state_language;

CREATE TABLE ezcobj_state_link (
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_state_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state_link ADD CONSTRAINT PRIMARY KEY (contentobject_id, contentobject_state_id) CONSTRAINT pk_ezcobj_state_link;

CREATE TABLE ezcollab_group (
  created INTEGER DEFAULT 0 NOT NULL,
  depth INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  is_open INTEGER DEFAULT 1 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  parent_group_id INTEGER DEFAULT 0 NOT NULL,
  path_string LVARCHAR(1020) DEFAULT '' NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL,
  title LVARCHAR(1020) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_group ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcollab_group;
CREATE INDEX ezcollab_group_depth ON ezcollab_group (depth);
CREATE INDEX ezcollab_group_path ON ezcollab_group (path_string);

CREATE TABLE ezcollab_item (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  data_float1 SMALLFLOAT DEFAULT 0 NOT NULL,
  data_float2 SMALLFLOAT DEFAULT 0 NOT NULL,
  data_float3 SMALLFLOAT DEFAULT 0 NOT NULL,
  data_int1 INTEGER DEFAULT 0 NOT NULL,
  data_int2 INTEGER DEFAULT 0 NOT NULL,
  data_int3 INTEGER DEFAULT 0 NOT NULL,
  data_text1 LVARCHAR(10565) NOT NULL,
  data_text2 LVARCHAR(10565) NOT NULL,
  data_text3 LVARCHAR(10565) NOT NULL,
  id SERIAL NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  status INTEGER DEFAULT 1 NOT NULL,
  type_identifier VARCHAR(160) DEFAULT '' NOT NULL
);
ALTER TABLE ezcollab_item ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcollab_item;

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
ALTER TABLE ezcollab_item_group_link ADD CONSTRAINT PRIMARY KEY (collaboration_id, group_id, user_id) CONSTRAINT pk_ezcollab_item_group_link;

CREATE TABLE ezcollab_item_message_link (
  collaboration_id INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  message_id INTEGER DEFAULT 0 NOT NULL,
  message_type INTEGER DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  participant_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_item_message_link ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcollab_item_message_link;

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
ALTER TABLE ezcollab_item_participant_link ADD CONSTRAINT PRIMARY KEY (collaboration_id, participant_id) CONSTRAINT pk_ezcollab_item_participant_link;

CREATE TABLE ezcollab_item_status (
  collaboration_id INTEGER DEFAULT 0 NOT NULL,
  is_active INTEGER DEFAULT 1 NOT NULL,
  is_read INTEGER DEFAULT 0 NOT NULL,
  last_read INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_item_status ADD CONSTRAINT PRIMARY KEY (collaboration_id, user_id) CONSTRAINT pk_ezcollab_item_status;

CREATE TABLE ezcollab_notification_rule (
  collab_identifier LVARCHAR(1020) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  user_id LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezcollab_notification_rule ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcollab_notification_rule;

CREATE TABLE ezcollab_profile (
  created INTEGER DEFAULT 0 NOT NULL,
  data_text1 LVARCHAR(31937) NOT NULL,
  id SERIAL NOT NULL,
  main_group INTEGER DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_profile ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcollab_profile;

CREATE TABLE ezcollab_simple_message (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  data_float1 SMALLFLOAT DEFAULT 0 NOT NULL,
  data_float2 SMALLFLOAT DEFAULT 0 NOT NULL,
  data_float3 SMALLFLOAT DEFAULT 0 NOT NULL,
  data_int1 INTEGER DEFAULT 0 NOT NULL,
  data_int2 INTEGER DEFAULT 0 NOT NULL,
  data_int3 INTEGER DEFAULT 0 NOT NULL,
  data_text1 LVARCHAR(10569) NOT NULL,
  data_text2 LVARCHAR(10569) NOT NULL,
  data_text3 LVARCHAR(10569) NOT NULL,
  id SERIAL NOT NULL,
  message_type VARCHAR(160) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_simple_message ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcollab_simple_message;

CREATE TABLE ezcontent_language (
  disabled INTEGER DEFAULT 0 NOT NULL,
  id BIGINT DEFAULT 0 NOT NULL,
  locale VARCHAR(80) DEFAULT '' NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezcontent_language ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcontent_language;
CREATE INDEX ezcontent_language_name ON ezcontent_language (name);

CREATE TABLE ezcontentbrowsebookmark (
  id SERIAL NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL,
  folder_id INTEGER DEFAULT 0 NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentbrowsebookmark ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcontentbrowsebookmark;
CREATE INDEX ezcontentbrowsebookmark_folder ON ezcontentbrowsebookmark (user_id, folder_id);
CREATE INDEX ezcontentbrowsebookmark_user ON ezcontentbrowsebookmark (user_id);

CREATE TABLE expbookmark_folder (
  created INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  parent_id INTEGER DEFAULT 0 NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expbookmark_folder ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_expbookmark_folder;
CREATE INDEX expbookmark_folder_user ON expbookmark_folder (user_id, parent_id);

CREATE TABLE expbookmark_view (
  bookmark_id INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  module_name LVARCHAR(400) DEFAULT '' NOT NULL,
  module_uri LVARCHAR(15576),
  user_id INTEGER DEFAULT 0 NOT NULL,
  view_name LVARCHAR(400) DEFAULT '' NOT NULL,
  view_parameters LVARCHAR(15576)
);
ALTER TABLE expbookmark_view ADD CONSTRAINT PRIMARY KEY (bookmark_id) CONSTRAINT pk_expbookmark_view;
CREATE INDEX expbookmark_view_module ON expbookmark_view (module_name, view_name);
CREATE INDEX expbookmark_view_user ON expbookmark_view (user_id);

CREATE TABLE ezcontentbrowserecent (
  created INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentbrowserecent ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcontentbrowserecent;
CREATE INDEX ezcontentbrowserecent_user ON ezcontentbrowserecent (user_id);

CREATE TABLE ezcontentclass (
  always_available INTEGER DEFAULT 0 NOT NULL,
  contentobject_name LVARCHAR(1020) DEFAULT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  identifier VARCHAR(200) DEFAULT '' NOT NULL,
  initial_language_id BIGINT DEFAULT 0 NOT NULL,
  is_container INTEGER DEFAULT 0 NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  remote_id LVARCHAR(400) DEFAULT '' NOT NULL,
  serialized_description_list LVARCHAR(14599),
  serialized_name_list LVARCHAR(14599),
  sort_field INTEGER DEFAULT 1 NOT NULL,
  sort_order INTEGER DEFAULT 1 NOT NULL,
  url_alias_name LVARCHAR(1020) DEFAULT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentclass ADD CONSTRAINT PRIMARY KEY (id, version) CONSTRAINT pk_ezcontentclass;
CREATE INDEX ezcontentclass_version ON ezcontentclass (version);
CREATE INDEX ezcontentclass_identifier ON ezcontentclass (identifier, version);

CREATE TABLE ezcontentclass_attribute (
  can_translate INTEGER DEFAULT 1,
  category VARCHAR(100) DEFAULT '' NOT NULL,
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  data_float1 FLOAT DEFAULT NULL,
  data_float2 FLOAT DEFAULT NULL,
  data_float3 FLOAT DEFAULT NULL,
  data_float4 FLOAT DEFAULT NULL,
  data_int1 INTEGER DEFAULT NULL,
  data_int2 INTEGER DEFAULT NULL,
  data_int3 INTEGER DEFAULT NULL,
  data_int4 INTEGER DEFAULT NULL,
  data_text1 VARCHAR(200) DEFAULT NULL,
  data_text2 VARCHAR(200) DEFAULT NULL,
  data_text3 VARCHAR(200) DEFAULT NULL,
  data_text4 LVARCHAR(1020) DEFAULT NULL,
  data_text5 LVARCHAR(7413),
  data_type_string VARCHAR(200) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  identifier VARCHAR(200) DEFAULT '' NOT NULL,
  is_information_collector INTEGER DEFAULT 0 NOT NULL,
  is_required INTEGER DEFAULT 0 NOT NULL,
  is_searchable INTEGER DEFAULT 0 NOT NULL,
  placement INTEGER DEFAULT 0 NOT NULL,
  serialized_data_text LVARCHAR(7413),
  serialized_description_list LVARCHAR(7413),
  serialized_name_list LVARCHAR(7413) NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentclass_attribute ADD CONSTRAINT PRIMARY KEY (id, version) CONSTRAINT pk_ezcontentclass_attribute;
CREATE INDEX ezcontentclass_attribute_ezcontentclass_attr_ccid ON ezcontentclass_attribute (contentclass_id);

CREATE TABLE ezcontentclass_classgroup (
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  contentclass_version INTEGER DEFAULT 0 NOT NULL,
  group_id INTEGER DEFAULT 0 NOT NULL,
  group_name LVARCHAR(1020) DEFAULT NULL
);
ALTER TABLE ezcontentclass_classgroup ADD CONSTRAINT PRIMARY KEY (contentclass_id, contentclass_version, group_id) CONSTRAINT pk_ezcontentclass_classgroup;

CREATE TABLE ezcontentclass_name (
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  contentclass_version INTEGER DEFAULT 0 NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  language_locale VARCHAR(80) DEFAULT '' NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezcontentclass_name ADD CONSTRAINT PRIMARY KEY (contentclass_id, contentclass_version, language_id) CONSTRAINT pk_ezcontentclass_name;

CREATE TABLE ezcontentclassgroup (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT NULL
);
ALTER TABLE ezcontentclassgroup ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcontentclassgroup;

CREATE TABLE ezcontentobject (
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  current_version INTEGER DEFAULT NULL,
  id SERIAL NOT NULL,
  initial_language_id BIGINT DEFAULT 0 NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT NULL,
  owner_id INTEGER DEFAULT 0 NOT NULL,
  published INTEGER DEFAULT 0 NOT NULL,
  remote_id LVARCHAR(400) DEFAULT NULL,
  section_id INTEGER DEFAULT 0 NOT NULL,
  status INTEGER DEFAULT 0
);
ALTER TABLE ezcontentobject ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcontentobject;
CREATE INDEX ezcontentobject_classid ON ezcontentobject (contentclass_id);
CREATE INDEX ezcontentobject_currentversion ON ezcontentobject (current_version);
CREATE INDEX ezcontentobject_lmask ON ezcontentobject (language_mask);
CREATE INDEX ezcontentobject_modified ON ezcontentobject (modified);
CREATE INDEX ezcontentobject_owner ON ezcontentobject (owner_id);
CREATE INDEX ezcontentobject_pub ON ezcontentobject (published);
CREATE UNIQUE INDEX ezcontentobject_remote_id ON ezcontentobject (remote_id);
CREATE INDEX ezcontentobject_status ON ezcontentobject (status);

CREATE TABLE ezcontentobject_attribute (
  attribute_original_id INTEGER DEFAULT 0,
  contentclassattribute_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  data_float FLOAT DEFAULT NULL,
  data_int INTEGER DEFAULT NULL,
  data_text CLOB,
  data_type_string VARCHAR(200) DEFAULT '',
  id SERIAL NOT NULL,
  language_code VARCHAR(80) DEFAULT '' NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  sort_key_int INTEGER DEFAULT 0 NOT NULL,
  sort_key_string LVARCHAR(1020) DEFAULT '' NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentobject_attribute ADD CONSTRAINT PRIMARY KEY (id, version) CONSTRAINT pk_ezcontentobject_attribute;
CREATE INDEX ezcontentobject_attribute_co_id_ver_lang_code ON ezcontentobject_attribute (contentobject_id, version, language_code);
CREATE INDEX ezcontentobject_attribute_language_code ON ezcontentobject_attribute (language_code);
CREATE INDEX ezcontentobject_attribute_ezcontentobject_classattr_id ON ezcontentobject_attribute (contentclassattribute_id);
CREATE INDEX ezcontentobject_attribute_sort_key_int ON ezcontentobject_attribute (sort_key_int);
CREATE INDEX ezcontentobject_attribute_sort_key_string ON ezcontentobject_attribute (sort_key_string);

CREATE TABLE ezcontentobject_link (
  contentclassattribute_id INTEGER DEFAULT 0 NOT NULL,
  from_contentobject_id INTEGER DEFAULT 0 NOT NULL,
  from_contentobject_version INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  relation_type INTEGER DEFAULT 1 NOT NULL,
  to_contentobject_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentobject_link ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcontentobject_link;
CREATE INDEX ezcontentobject_link_ezco_link_from ON ezcontentobject_link (from_contentobject_id, from_contentobject_version, contentclassattribute_id);
CREATE INDEX ezcontentobject_link_ezco_link_to_co_id ON ezcontentobject_link (to_contentobject_id);

CREATE TABLE ezcontentobject_name (
  content_translation VARCHAR(80) DEFAULT '' NOT NULL,
  content_version INTEGER DEFAULT 0 NOT NULL,
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT NULL,
  real_translation VARCHAR(80) DEFAULT NULL
);
ALTER TABLE ezcontentobject_name ADD CONSTRAINT PRIMARY KEY (contentobject_id, content_version, content_translation) CONSTRAINT pk_ezcontentobject_name;
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
  path_identification_string LVARCHAR(3224),
  path_string LVARCHAR(1020) DEFAULT '' NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL,
  remote_id LVARCHAR(400) DEFAULT '' NOT NULL,
  sort_field INTEGER DEFAULT 1,
  sort_order INTEGER DEFAULT 1,
  trashed INTEGER DEFAULT 0 NOT NULL,
  trashed_by INTEGER DEFAULT 0 NOT NULL,
  trashed_via LVARCHAR(400) DEFAULT '' NOT NULL
);
ALTER TABLE ezcontentobject_trash ADD CONSTRAINT PRIMARY KEY (node_id) CONSTRAINT pk_ezcontentobject_trash;
CREATE INDEX ezcontentobject_trash_ezcobj_trash_co_id ON ezcontentobject_trash (contentobject_id);
CREATE INDEX ezcontentobject_trash_ezcobj_trash_depth ON ezcontentobject_trash (depth);
CREATE INDEX ezcontentobject_trash_ezcobj_trash_modified_subnode ON ezcontentobject_trash (modified_subnode);
CREATE INDEX ezcontentobject_trash_ezcobj_trash_p_node_id ON ezcontentobject_trash (parent_node_id);
CREATE INDEX ezcontentobject_trash_ezcobj_trash_path ON ezcontentobject_trash (path_string);
CREATE INDEX ezcontentobject_trash_ezcobj_trash_path_ident ON ezcontentobject_trash (path_identification_string);
CREATE INDEX ezcontentobject_trash_ezcobj_trash_trashed_by ON ezcontentobject_trash (trashed_by);

CREATE TABLE ezcontentobject_tree (
  contentobject_id INTEGER DEFAULT NULL,
  contentobject_is_published INTEGER DEFAULT NULL,
  contentobject_version INTEGER DEFAULT NULL,
  depth INTEGER DEFAULT 0 NOT NULL,
  is_hidden INTEGER DEFAULT 0 NOT NULL,
  is_invisible INTEGER DEFAULT 0 NOT NULL,
  main_node_id INTEGER DEFAULT NULL,
  modified_subnode INTEGER DEFAULT 0,
  node_id SERIAL NOT NULL,
  parent_node_id INTEGER DEFAULT 0 NOT NULL,
  path_identification_string LVARCHAR(3224),
  path_string LVARCHAR(1020) DEFAULT '' NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL,
  remote_id LVARCHAR(400) DEFAULT '' NOT NULL,
  sort_field INTEGER DEFAULT 1,
  sort_order INTEGER DEFAULT 1
);
ALTER TABLE ezcontentobject_tree ADD CONSTRAINT PRIMARY KEY (node_id) CONSTRAINT pk_ezcontentobject_tree;
CREATE INDEX ezcontentobject_tree_remote_id ON ezcontentobject_tree (remote_id);
CREATE INDEX ezcontentobject_tree_co_id ON ezcontentobject_tree (contentobject_id);
CREATE INDEX ezcontentobject_tree_depth ON ezcontentobject_tree (depth);
CREATE INDEX ezcontentobject_tree_p_node_id ON ezcontentobject_tree (parent_node_id);
CREATE INDEX ezcontentobject_tree_path ON ezcontentobject_tree (path_string);
CREATE INDEX ezcontentobject_tree_path_ident ON ezcontentobject_tree (path_identification_string);
CREATE INDEX ezcontentobject_tree_modified_subnode ON ezcontentobject_tree (modified_subnode);

CREATE TABLE ezcontentobject_version (
  contentobject_id INTEGER DEFAULT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  initial_language_id BIGINT DEFAULT 0 NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL,
  workflow_event_pos INTEGER DEFAULT 0
);
ALTER TABLE ezcontentobject_version ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcontentobject_version;
CREATE INDEX ezcontentobject_version_ezcobj_version_creator_id ON ezcontentobject_version (creator_id);
CREATE INDEX ezcontentobject_version_ezcobj_version_status ON ezcontentobject_version (status);
CREATE INDEX ezcontentobject_version_idx_object_version_objver ON ezcontentobject_version (contentobject_id, version);
CREATE INDEX ezcontentobject_version_ezcontobj_version_obj_status ON ezcontentobject_version (contentobject_id, status);

CREATE TABLE ezcurrencydata (
  auto_rate_value DECIMAL(10,5) DEFAULT 0 NOT NULL,
  code VARCHAR(16) DEFAULT '' NOT NULL,
  custom_rate_value DECIMAL(10,5) DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  locale LVARCHAR(1020) DEFAULT '' NOT NULL,
  rate_factor DECIMAL(10,5) DEFAULT 1 NOT NULL,
  status INTEGER DEFAULT 1 NOT NULL,
  symbol LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezcurrencydata ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezcurrencydata;
CREATE INDEX ezcurrencydata_code ON ezcurrencydata (code);

CREATE TABLE ezdiscountrule (
  id SERIAL NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezdiscountrule ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezdiscountrule;

CREATE TABLE ezdiscountsubrule (
  discount_percent SMALLFLOAT DEFAULT NULL,
  discountrule_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  limitation VARCHAR(4) DEFAULT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezdiscountsubrule ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezdiscountsubrule;

CREATE TABLE ezdiscountsubrule_value (
  discountsubrule_id INTEGER DEFAULT 0 NOT NULL,
  issection INTEGER DEFAULT 0 NOT NULL,
  value INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezdiscountsubrule_value ADD CONSTRAINT PRIMARY KEY (discountsubrule_id, value, issection) CONSTRAINT pk_ezdiscountsubrule_value;

CREATE TABLE ezenumobjectvalue (
  contentobject_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_attribute_version INTEGER DEFAULT 0 NOT NULL,
  enumelement LVARCHAR(1020) DEFAULT '' NOT NULL,
  enumid INTEGER DEFAULT 0 NOT NULL,
  enumvalue LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezenumobjectvalue ADD CONSTRAINT PRIMARY KEY (contentobject_attribute_id, contentobject_attribute_version, enumid) CONSTRAINT pk_ezenumobjectvalue;

CREATE TABLE ezenumvalue (
  contentclass_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentclass_attribute_version INTEGER DEFAULT 0 NOT NULL,
  enumelement LVARCHAR(1020) DEFAULT '' NOT NULL,
  enumvalue LVARCHAR(1020) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  placement INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezenumvalue ADD CONSTRAINT PRIMARY KEY (id, contentclass_attribute_id, contentclass_attribute_version) CONSTRAINT pk_ezenumvalue;
CREATE INDEX ezenumvalue_co_cl_attr_id_co_class_att_ver ON ezenumvalue (contentclass_attribute_id, contentclass_attribute_version);

CREATE TABLE ezforgot_password (
  hash_key VARCHAR(128) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  time INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezforgot_password ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezforgot_password;
CREATE INDEX ezforgot_password_user ON ezforgot_password (user_id);

CREATE TABLE ezgeneral_digest_user_settings (
  day LVARCHAR(1020) DEFAULT '' NOT NULL,
  digest_type INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  receive_digest INTEGER DEFAULT 0 NOT NULL,
  time LVARCHAR(1020) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezgeneral_digest_user_settings ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezgeneral_digest_user_settings;
CREATE UNIQUE INDEX ezgeneral_digest_user_settings_ezgeneral_digest_user_id ON ezgeneral_digest_user_settings (user_id);

CREATE TABLE ezimagefile (
  contentobject_attribute_id INTEGER DEFAULT 0 NOT NULL,
  filepath LVARCHAR(3224) NOT NULL,
  id SERIAL NOT NULL
);
ALTER TABLE ezimagefile ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezimagefile;
CREATE INDEX ezimagefile_coid ON ezimagefile (contentobject_attribute_id);
CREATE INDEX ezimagefile_file ON ezimagefile (filepath);

CREATE TABLE ezinfocollection (
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  modified INTEGER DEFAULT 0,
  user_identifier VARCHAR(136) DEFAULT NULL
);
ALTER TABLE ezinfocollection ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezinfocollection;
CREATE INDEX ezinfocollection_co_id_created ON ezinfocollection (contentobject_id, created);

CREATE TABLE ezinfocollection_attribute (
  contentclass_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_attribute_id INTEGER DEFAULT NULL,
  contentobject_id INTEGER DEFAULT NULL,
  data_float SMALLFLOAT DEFAULT NULL,
  data_int INTEGER DEFAULT NULL,
  data_text CLOB,
  id SERIAL NOT NULL,
  informationcollection_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezinfocollection_attribute ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezinfocollection_attribute;
CREATE INDEX ezinfocollection_attribute_ezinfocollection_attr_cca_id ON ezinfocollection_attribute (contentclass_attribute_id);
CREATE INDEX ezinfocollection_attribute_ezinfocollection_attr_co_id ON ezinfocollection_attribute (contentobject_id);
CREATE INDEX ezinfocollection_attribute_ezinfocollection_attr_coa_id ON ezinfocollection_attribute (contentobject_attribute_id);
CREATE INDEX ezinfocollection_attribute_ezinfocollection_attr_ic_id ON ezinfocollection_attribute (informationcollection_id);

CREATE TABLE ezisbn_group (
  description LVARCHAR(1020) DEFAULT '' NOT NULL,
  group_number INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL
);
ALTER TABLE ezisbn_group ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezisbn_group;

CREATE TABLE ezisbn_group_range (
  from_number INTEGER DEFAULT 0 NOT NULL,
  group_from VARCHAR(128) DEFAULT '' NOT NULL,
  group_length INTEGER DEFAULT 0 NOT NULL,
  group_to VARCHAR(128) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  to_number INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezisbn_group_range ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezisbn_group_range;

CREATE TABLE ezisbn_registrant_range (
  from_number INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  isbn_group_id INTEGER DEFAULT 0 NOT NULL,
  registrant_from VARCHAR(128) DEFAULT '' NOT NULL,
  registrant_length INTEGER DEFAULT 0 NOT NULL,
  registrant_to VARCHAR(128) DEFAULT '' NOT NULL,
  to_number INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezisbn_registrant_range ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezisbn_registrant_range;

CREATE TABLE ezkeyword (
  class_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  keyword LVARCHAR(1020) DEFAULT NULL
);
ALTER TABLE ezkeyword ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezkeyword;
CREATE INDEX ezkeyword_keyword ON ezkeyword (keyword);

CREATE TABLE ezkeyword_attribute_link (
  id SERIAL NOT NULL,
  keyword_id INTEGER DEFAULT 0 NOT NULL,
  objectattribute_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezkeyword_attribute_link ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezkeyword_attribute_link;
CREATE INDEX ezkeyword_attribute_link_ezkeyword_attr_link_kid_oaid ON ezkeyword_attribute_link (keyword_id, objectattribute_id);
CREATE INDEX ezkeyword_attribute_link_ezkeyword_attr_link_oaid ON ezkeyword_attribute_link (objectattribute_id);

CREATE TABLE ezmedia (
  contentobject_attribute_id INTEGER DEFAULT 0 NOT NULL,
  controls VARCHAR(200) DEFAULT NULL,
  filename LVARCHAR(1020) DEFAULT '' NOT NULL,
  has_controller INTEGER DEFAULT 0,
  height INTEGER DEFAULT NULL,
  is_autoplay INTEGER DEFAULT 0,
  is_loop INTEGER DEFAULT 0,
  mime_type VARCHAR(200) DEFAULT '' NOT NULL,
  original_filename LVARCHAR(1020) DEFAULT '' NOT NULL,
  pluginspage LVARCHAR(1020) DEFAULT NULL,
  quality VARCHAR(200) DEFAULT NULL,
  version INTEGER DEFAULT 0 NOT NULL,
  width INTEGER DEFAULT NULL
);
ALTER TABLE ezmedia ADD CONSTRAINT PRIMARY KEY (contentobject_attribute_id, version) CONSTRAINT pk_ezmedia;

CREATE TABLE ezmessage (
  body CLOB,
  destination_address VARCHAR(200) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  is_sent INTEGER DEFAULT 0 NOT NULL,
  send_method VARCHAR(200) DEFAULT '' NOT NULL,
  send_time VARCHAR(200) DEFAULT '' NOT NULL,
  send_weekday VARCHAR(200) DEFAULT '' NOT NULL,
  title LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezmessage ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezmessage;

CREATE TABLE ezmodule_run (
  function_name LVARCHAR(1020) DEFAULT NULL,
  id SERIAL NOT NULL,
  module_data CLOB,
  module_name LVARCHAR(1020) DEFAULT NULL,
  workflow_process_id INTEGER DEFAULT NULL
);
ALTER TABLE ezmodule_run ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezmodule_run;
CREATE UNIQUE INDEX ezmodule_run_workflow_process_id_s ON ezmodule_run (workflow_process_id);

CREATE TABLE ezmultipricedata (
  contentobject_attr_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_attr_version INTEGER DEFAULT 0 NOT NULL,
  currency_code VARCHAR(16) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  type INTEGER DEFAULT 0 NOT NULL,
  value DECIMAL(15,2) DEFAULT 0 NOT NULL
);
ALTER TABLE ezmultipricedata ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezmultipricedata;
CREATE INDEX ezmultipricedata_coa_id ON ezmultipricedata (contentobject_attr_id);
CREATE INDEX ezmultipricedata_coa_version ON ezmultipricedata (contentobject_attr_version);
CREATE INDEX ezmultipricedata_currency_code ON ezmultipricedata (currency_code);

CREATE TABLE eznode_assignment (
  contentobject_id INTEGER DEFAULT NULL,
  contentobject_version INTEGER DEFAULT NULL,
  from_node_id INTEGER DEFAULT 0,
  id SERIAL NOT NULL,
  is_main INTEGER DEFAULT 0 NOT NULL,
  op_code INTEGER DEFAULT 0 NOT NULL,
  parent_node INTEGER DEFAULT NULL,
  parent_remote_id LVARCHAR(400) DEFAULT '' NOT NULL,
  remote_id LVARCHAR(400) DEFAULT '0' NOT NULL,
  sort_field INTEGER DEFAULT 1,
  sort_order INTEGER DEFAULT 1,
  priority INTEGER DEFAULT 0 NOT NULL,
  is_hidden INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE eznode_assignment ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_eznode_assignment;
CREATE INDEX eznode_assignment_co_version ON eznode_assignment (contentobject_version);
CREATE INDEX eznode_assignment_coid_cov ON eznode_assignment (contentobject_id, contentobject_version);
CREATE INDEX eznode_assignment_is_main ON eznode_assignment (is_main);
CREATE INDEX eznode_assignment_parent_node ON eznode_assignment (parent_node);

CREATE TABLE eznotificationcollection (
  data_subject LVARCHAR(29847) NOT NULL,
  data_text CLOB NOT NULL,
  event_id INTEGER DEFAULT 0 NOT NULL,
  handler LVARCHAR(1020) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  transport LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE eznotificationcollection ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_eznotificationcollection;

CREATE TABLE eznotificationcollection_item (
  address LVARCHAR(1020) DEFAULT '' NOT NULL,
  collection_id INTEGER DEFAULT 0 NOT NULL,
  event_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  send_date INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE eznotificationcollection_item ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_eznotificationcollection_item;

CREATE TABLE eznotificationevent (
  data_int1 INTEGER DEFAULT 0 NOT NULL,
  data_int2 INTEGER DEFAULT 0 NOT NULL,
  data_int3 INTEGER DEFAULT 0 NOT NULL,
  data_int4 INTEGER DEFAULT 0 NOT NULL,
  data_text1 LVARCHAR(7723) NOT NULL,
  data_text2 LVARCHAR(7723) NOT NULL,
  data_text3 LVARCHAR(7723) NOT NULL,
  data_text4 LVARCHAR(7723) NOT NULL,
  event_type_string LVARCHAR(1020) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  status INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE eznotificationevent ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_eznotificationevent;

CREATE TABLE ezoperation_memento (
  id SERIAL NOT NULL,
  main INTEGER DEFAULT 0 NOT NULL,
  main_key VARCHAR(128) DEFAULT '' NOT NULL,
  memento_data CLOB NOT NULL,
  memento_key VARCHAR(128) DEFAULT '' NOT NULL
);
ALTER TABLE ezoperation_memento ADD CONSTRAINT PRIMARY KEY (id, memento_key) CONSTRAINT pk_ezoperation_memento;
CREATE INDEX ezoperation_memento_memento_key_main ON ezoperation_memento (memento_key, main);

CREATE TABLE ezorder (
  account_identifier LVARCHAR(400) DEFAULT 'default' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  data_text_1 LVARCHAR(14916),
  data_text_2 LVARCHAR(14916),
  email LVARCHAR(600) DEFAULT '',
  id SERIAL NOT NULL,
  ignore_vat INTEGER DEFAULT 0 NOT NULL,
  is_archived INTEGER DEFAULT 0 NOT NULL,
  is_temporary INTEGER DEFAULT 1 NOT NULL,
  order_nr INTEGER DEFAULT 0 NOT NULL,
  productcollection_id INTEGER DEFAULT 0 NOT NULL,
  siteaccess LVARCHAR(1020) DEFAULT '' NOT NULL,
  status_id INTEGER DEFAULT 0,
  status_modified INTEGER DEFAULT 0,
  status_modifier_id INTEGER DEFAULT 0,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezorder;
CREATE INDEX ezorder_is_archived ON ezorder (is_archived);
CREATE INDEX ezorder_is_tmp ON ezorder (is_temporary);

CREATE TABLE ezorder_nr_incr (
  id SERIAL NOT NULL
);
ALTER TABLE ezorder_nr_incr ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezorder_nr_incr;

CREATE TABLE ezorder_item (
  description LVARCHAR(1020) DEFAULT NULL,
  id SERIAL NOT NULL,
  is_vat_inc INTEGER DEFAULT 0 NOT NULL,
  order_id INTEGER DEFAULT 0 NOT NULL,
  price SMALLFLOAT DEFAULT NULL,
  type VARCHAR(120) DEFAULT NULL,
  vat_value SMALLFLOAT DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder_item ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezorder_item;
CREATE INDEX ezorder_item_order_id ON ezorder_item (order_id);
CREATE INDEX ezorder_item_type ON ezorder_item (type);

CREATE TABLE ezorder_status (
  id SERIAL NOT NULL,
  is_active INTEGER DEFAULT 1 NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  status_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder_status ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezorder_status;
CREATE INDEX ezorder_status_active ON ezorder_status (is_active);
CREATE INDEX ezorder_status_name ON ezorder_status (name);
CREATE INDEX ezorder_status_sid ON ezorder_status (status_id);

CREATE TABLE ezorder_status_history (
  id SERIAL NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  order_id INTEGER DEFAULT 0 NOT NULL,
  status_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder_status_history ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezorder_status_history;
CREATE INDEX ezorder_status_history_mod ON ezorder_status_history (modified);
CREATE INDEX ezorder_status_history_oid ON ezorder_status_history (order_id);
CREATE INDEX ezorder_status_history_sid ON ezorder_status_history (status_id);

CREATE TABLE ezpackage (
  id SERIAL NOT NULL,
  install_date INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(400) DEFAULT '' NOT NULL,
  version VARCHAR(120) DEFAULT '0' NOT NULL
);
ALTER TABLE ezpackage ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezpackage;

CREATE TABLE ezpaymentobject (
  id SERIAL NOT NULL,
  order_id INTEGER DEFAULT 0 NOT NULL,
  payment_string LVARCHAR(1020) DEFAULT '' NOT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  workflowprocess_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezpaymentobject ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezpaymentobject;

CREATE TABLE ezpdf_export (
  footer_text LVARCHAR(1020) DEFAULT '' NOT NULL,
  show_footer INTEGER DEFAULT 1 NOT NULL,
  created INTEGER DEFAULT NULL,
  creator_id INTEGER DEFAULT NULL,
  export_classes LVARCHAR(1020) DEFAULT NULL,
  export_structure LVARCHAR(1020) DEFAULT NULL,
  id SERIAL NOT NULL,
  intro_text LVARCHAR(12868),
  modified INTEGER DEFAULT NULL,
  modifier_id INTEGER DEFAULT NULL,
  pdf_filename LVARCHAR(1020) DEFAULT NULL,
  show_frontpage INTEGER DEFAULT NULL,
  site_access LVARCHAR(1020) DEFAULT NULL,
  source_node_id INTEGER DEFAULT NULL,
  status INTEGER DEFAULT NULL,
  sub_text LVARCHAR(12868),
  title LVARCHAR(1020) DEFAULT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezpdf_export ADD CONSTRAINT PRIMARY KEY (id, version) CONSTRAINT pk_ezpdf_export;

CREATE TABLE ezpending_actions (
  id SERIAL NOT NULL,
  action LVARCHAR(256) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT NULL,
  param LVARCHAR(31714)
);
ALTER TABLE ezpending_actions ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezpending_actions;
CREATE INDEX ezpending_actions_action ON ezpending_actions (action);
CREATE INDEX ezpending_actions_created ON ezpending_actions (created);

CREATE TABLE ezpolicy (
  function_name LVARCHAR(1020) DEFAULT NULL,
  id SERIAL NOT NULL,
  module_name LVARCHAR(1020) DEFAULT NULL,
  original_id INTEGER DEFAULT 0 NOT NULL,
  role_id INTEGER DEFAULT NULL
);
ALTER TABLE ezpolicy ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezpolicy;
CREATE INDEX ezpolicy_original_id ON ezpolicy (original_id);
CREATE INDEX ezpolicy_role_id ON ezpolicy (role_id);

CREATE TABLE ezpolicy_limitation (
  id SERIAL NOT NULL,
  identifier LVARCHAR(1020) DEFAULT '' NOT NULL,
  policy_id INTEGER DEFAULT NULL
);
ALTER TABLE ezpolicy_limitation ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezpolicy_limitation;
CREATE INDEX ezpolicy_limitation_policy_id ON ezpolicy_limitation (policy_id);

CREATE TABLE ezpolicy_limitation_value (
  id SERIAL NOT NULL,
  limitation_id INTEGER DEFAULT NULL,
  value LVARCHAR(1020) DEFAULT NULL
);
ALTER TABLE ezpolicy_limitation_value ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezpolicy_limitation_value;
CREATE INDEX ezpolicy_limitation_value_val ON ezpolicy_limitation_value (value);
CREATE INDEX ezpolicy_limitation_value_ezpolicy_limit_value_limit_id ON ezpolicy_limitation_value (limitation_id);

CREATE TABLE ezpreferences (
  id SERIAL NOT NULL,
  name LVARCHAR(400) DEFAULT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL,
  value LVARCHAR(31570)
);
ALTER TABLE ezpreferences ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezpreferences;
CREATE INDEX ezpreferences_name ON ezpreferences (name);
CREATE INDEX ezpreferences_user_id_idx ON ezpreferences (user_id, name);

CREATE TABLE ezprest_authcode (
  client_id LVARCHAR(800) DEFAULT '' NOT NULL,
  code_challenge LVARCHAR(512) DEFAULT NULL,
  code_challenge_method VARCHAR(40) DEFAULT NULL,
  expirytime BIGINT DEFAULT 0 NOT NULL,
  id LVARCHAR(800) DEFAULT '' NOT NULL,
  scope LVARCHAR(800) DEFAULT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezprest_authcode ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezprest_authcode;
CREATE INDEX ezprest_authcode_authcode_client_id ON ezprest_authcode (client_id);

CREATE TABLE ezprest_authorized_clients (
  created INTEGER DEFAULT NULL,
  id SERIAL NOT NULL,
  rest_client_id INTEGER DEFAULT NULL,
  user_id INTEGER DEFAULT NULL
);
ALTER TABLE ezprest_authorized_clients ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezprest_authorized_clients;
CREATE INDEX ezprest_authorized_clients_client_user ON ezprest_authorized_clients (rest_client_id, user_id);

CREATE TABLE ezprest_clients (
  client_id LVARCHAR(800) DEFAULT NULL,
  client_secret LVARCHAR(800) DEFAULT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  description LVARCHAR(29125),
  endpoint_uri LVARCHAR(800) DEFAULT NULL,
  id SERIAL NOT NULL,
  name LVARCHAR(400) DEFAULT NULL,
  owner_id INTEGER DEFAULT 0 NOT NULL,
  updated INTEGER DEFAULT 0 NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezprest_clients ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezprest_clients;
CREATE UNIQUE INDEX ezprest_clients_client_id_unique ON ezprest_clients (client_id, version);

CREATE TABLE ezprest_token (
  client_id LVARCHAR(800) DEFAULT '' NOT NULL,
  expirytime BIGINT DEFAULT 0 NOT NULL,
  id LVARCHAR(800) DEFAULT '' NOT NULL,
  refresh_token LVARCHAR(800) DEFAULT '' NOT NULL,
  scope LVARCHAR(800) DEFAULT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezprest_token ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezprest_token;
CREATE INDEX ezprest_token_token_client_id ON ezprest_token (client_id);

CREATE TABLE expapikey (
  created INTEGER DEFAULT 0 NOT NULL,
  created_by INTEGER DEFAULT 0 NOT NULL,
  expires INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  key_prefix VARCHAR(160) DEFAULT '' NOT NULL,
  last_ip LVARCHAR(256) DEFAULT '' NOT NULL,
  last_used INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  revoked INTEGER DEFAULT 0 NOT NULL,
  revoked_by INTEGER DEFAULT 0 NOT NULL,
  salt LVARCHAR(256) DEFAULT '' NOT NULL,
  scopes LVARCHAR(1020) DEFAULT '' NOT NULL,
  secret_hash LVARCHAR(512) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expapikey ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_expapikey;
CREATE UNIQUE INDEX expapikey_prefix ON expapikey (key_prefix);
CREATE INDEX expapikey_user ON expapikey (user_id);

CREATE TABLE exppasskey (
  aaguid VARCHAR(144) DEFAULT '' NOT NULL,
  algorithm INTEGER DEFAULT 0 NOT NULL,
  backup_eligible INTEGER DEFAULT 0 NOT NULL,
  backup_state INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  credential_hash LVARCHAR(256) DEFAULT '' NOT NULL,
  credential_id LVARCHAR(14336) DEFAULT NULL,
  id SERIAL NOT NULL,
  last_ip LVARCHAR(256) DEFAULT '' NOT NULL,
  last_used INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  public_key LVARCHAR(14336) DEFAULT NULL,
  sign_count BIGINT DEFAULT 0 NOT NULL,
  transports LVARCHAR(1020) DEFAULT '' NOT NULL,
  user_handle LVARCHAR(512) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exppasskey ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_exppasskey;
CREATE UNIQUE INDEX exppasskey_credential ON exppasskey (credential_hash);
CREATE INDEX exppasskey_user ON exppasskey (user_id);

CREATE TABLE exp_transfer_run (
  bytes BIGINT DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  detail LVARCHAR(25240),
  detail_url LVARCHAR(1020) DEFAULT '' NOT NULL,
  direction VARCHAR(40) DEFAULT '' NOT NULL,
  duration INTEGER DEFAULT 0 NOT NULL,
  engine VARCHAR(64) DEFAULT '' NOT NULL,
  error LVARCHAR(1020) DEFAULT '' NOT NULL,
  file_ref LVARCHAR(1020) DEFAULT '' NOT NULL,
  finished INTEGER DEFAULT 0 NOT NULL,
  format VARCHAR(128) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  job_ref LVARCHAR(256) DEFAULT '' NOT NULL,
  kind VARCHAR(80) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  rows_done INTEGER DEFAULT 0 NOT NULL,
  rows_failed INTEGER DEFAULT 0 NOT NULL,
  rows_total INTEGER DEFAULT 0 NOT NULL,
  scope VARCHAR(80) DEFAULT '' NOT NULL,
  scope_ref LVARCHAR(1020) DEFAULT '' NOT NULL,
  siteaccess LVARCHAR(256) DEFAULT '' NOT NULL,
  started INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(64) DEFAULT '' NOT NULL,
  title LVARCHAR(1020) DEFAULT '' NOT NULL,
  tool LVARCHAR(256) DEFAULT '' NOT NULL,
  tool_ref LVARCHAR(256) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exp_transfer_run ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_exp_transfer_run;
CREATE INDEX exp_transfer_run_started ON exp_transfer_run (started);
CREATE INDEX exp_transfer_run_tool ON exp_transfer_run (tool, started);
CREATE INDEX exp_transfer_run_toolref ON exp_transfer_run (tool, tool_ref);
CREATE INDEX exp_transfer_run_user ON exp_transfer_run (user_id);

CREATE TABLE exp_data_connection (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  database_name LVARCHAR(1020) DEFAULT '' NOT NULL,
  description LVARCHAR(1020) DEFAULT '' NOT NULL,
  engine VARCHAR(128) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  identifier LVARCHAR(256) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  options LVARCHAR(25893),
  port INTEGER DEFAULT 0 NOT NULL,
  read_only INTEGER DEFAULT 0 NOT NULL,
  secret LVARCHAR(512) DEFAULT '' NOT NULL,
  server LVARCHAR(1020) DEFAULT '' NOT NULL,
  user_name LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE exp_data_connection ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_exp_data_connection;
CREATE UNIQUE INDEX exp_data_connection_identifier ON exp_data_connection (identifier);

CREATE TABLE exp_secret (
  ciphertext LVARCHAR(30073),
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  description LVARCHAR(1020) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  key_id VARCHAR(64) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(512) DEFAULT '' NOT NULL,
  scope LVARCHAR(256) DEFAULT '' NOT NULL
);
ALTER TABLE exp_secret ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_exp_secret;
CREATE UNIQUE INDEX exp_secret_name ON exp_secret (name);
CREATE INDEX exp_secret_scope ON exp_secret (scope);

CREATE TABLE exp_data_sync (
  class_name LVARCHAR(512) DEFAULT '' NOT NULL,
  conflict_rule VARCHAR(64) DEFAULT '' NOT NULL,
  content_class LVARCHAR(512) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  direction VARCHAR(64) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  interval_seconds INTEGER DEFAULT 0 NOT NULL,
  key_columns LVARCHAR(1020) DEFAULT '' NOT NULL,
  last_run INTEGER DEFAULT 0 NOT NULL,
  last_run_id INTEGER DEFAULT 0 NOT NULL,
  map LVARCHAR(14271),
  modified INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  next_run INTEGER DEFAULT 0 NOT NULL,
  options LVARCHAR(14271),
  run_mode VARCHAR(64) DEFAULT '' NOT NULL,
  state VARCHAR(64) DEFAULT '' NOT NULL,
  subtree_node_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exp_data_sync ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_exp_data_sync;
CREATE INDEX exp_data_sync_mode ON exp_data_sync (run_mode, state);

CREATE TABLE exp_data_sync_conflict (
  content_values CLOB,
  detected INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  link_id INTEGER DEFAULT 0 NOT NULL,
  object_id INTEGER DEFAULT 0 NOT NULL,
  resolution VARCHAR(64) DEFAULT '' NOT NULL,
  resolved INTEGER DEFAULT 0 NOT NULL,
  resolver_id INTEGER DEFAULT 0 NOT NULL,
  row_key LVARCHAR(1020) DEFAULT '' NOT NULL,
  row_values CLOB,
  state VARCHAR(64) DEFAULT '' NOT NULL,
  sync_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exp_data_sync_conflict ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_exp_data_sync_conflict;
CREATE INDEX exp_data_sync_conflict_state ON exp_data_sync_conflict (sync_id, state);

CREATE TABLE exp_data_sync_link (
  content_hash VARCHAR(128) DEFAULT '' NOT NULL,
  content_modified INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  object_id INTEGER DEFAULT 0 NOT NULL,
  row_hash VARCHAR(128) DEFAULT '' NOT NULL,
  row_key LVARCHAR(1020) DEFAULT '' NOT NULL,
  row_modified INTEGER DEFAULT 0 NOT NULL,
  sync_id INTEGER DEFAULT 0 NOT NULL,
  synced INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exp_data_sync_link ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_exp_data_sync_link;
CREATE UNIQUE INDEX exp_data_sync_link_key ON exp_data_sync_link (sync_id, row_key);
CREATE INDEX exp_data_sync_link_object ON exp_data_sync_link (sync_id, object_id);

CREATE TABLE expuser_session (
  browser LVARCHAR(256) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  device_type VARCHAR(64) DEFAULT '' NOT NULL,
  end_reason VARCHAR(128) DEFAULT '' NOT NULL,
  ended INTEGER DEFAULT 0 NOT NULL,
  ended_by INTEGER DEFAULT 0 NOT NULL,
  first_ip LVARCHAR(256) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  last_active INTEGER DEFAULT 0 NOT NULL,
  last_ip LVARCHAR(256) DEFAULT '' NOT NULL,
  method VARCHAR(128) DEFAULT '' NOT NULL,
  os LVARCHAR(256) DEFAULT '' NOT NULL,
  remember_until INTEGER DEFAULT 0 NOT NULL,
  siteaccess LVARCHAR(256) DEFAULT '' NOT NULL,
  token_hash LVARCHAR(256) DEFAULT '' NOT NULL,
  user_agent LVARCHAR(2048) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expuser_session ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_expuser_session;
CREATE UNIQUE INDEX expuser_session_token ON expuser_session (token_hash);
CREATE INDEX expuser_session_user ON expuser_session (user_id, ended);
CREATE INDEX expuser_session_active ON expuser_session (ended, last_active);

CREATE TABLE expuser_session_info (
  auth_detail VARCHAR(128) DEFAULT '' NOT NULL,
  country VARCHAR(8) DEFAULT '' NOT NULL,
  host LVARCHAR(1020) DEFAULT '' NOT NULL,
  location LVARCHAR(512) DEFAULT '' NOT NULL,
  session_id INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE expuser_session_info ADD CONSTRAINT PRIMARY KEY (session_id) CONSTRAINT pk_expuser_session_info;
CREATE INDEX expuser_session_info_user ON expuser_session_info (user_id);

CREATE TABLE expuser_audit (
  actor_id INTEGER DEFAULT 0 NOT NULL,
  actor_login LVARCHAR(600) DEFAULT '' NOT NULL,
  client_ip LVARCHAR(256) DEFAULT '' NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  event_id VARCHAR(104) DEFAULT '' NOT NULL,
  event_name LVARCHAR(512) DEFAULT '' NOT NULL,
  host LVARCHAR(1020) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  module_view LVARCHAR(512) DEFAULT '' NOT NULL,
  reason LVARCHAR(256) DEFAULT '' NOT NULL,
  request_id VARCHAR(160) DEFAULT '' NOT NULL,
  result VARCHAR(64) DEFAULT '' NOT NULL,
  siteaccess LVARCHAR(256) DEFAULT '' NOT NULL,
  target_id INTEGER DEFAULT 0 NOT NULL,
  target_login LVARCHAR(600) DEFAULT '' NOT NULL,
  target_type VARCHAR(64) DEFAULT '' NOT NULL,
  time_ms BIGINT DEFAULT 0 NOT NULL,
  value_after CLOB,
  value_before CLOB
);
ALTER TABLE expuser_audit ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_expuser_audit;
CREATE UNIQUE INDEX expuser_audit_event ON expuser_audit (event_id);
CREATE INDEX expuser_audit_target ON expuser_audit (target_id, created);
CREATE INDEX expuser_audit_actor ON expuser_audit (actor_id, created);
CREATE INDEX expuser_audit_name ON expuser_audit (event_name, created);
CREATE INDEX expuser_audit_created ON expuser_audit (created);

CREATE TABLE exphelp_help_id (
  help_id LVARCHAR(320) DEFAULT '' NOT NULL,
  object_id INTEGER DEFAULT 0 NOT NULL,
  released INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exphelp_help_id ADD CONSTRAINT PRIMARY KEY (help_id) CONSTRAINT pk_exphelp_help_id;
CREATE INDEX exphelp_help_id_object ON exphelp_help_id (object_id);

CREATE TABLE exphelp_feedback (
  created INTEGER DEFAULT 0 NOT NULL,
  help_id LVARCHAR(320) DEFAULT '' NOT NULL,
  helpful INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  language VARCHAR(80) DEFAULT '' NOT NULL,
  object_id INTEGER DEFAULT 0 NOT NULL,
  reason VARCHAR(128) DEFAULT '' NOT NULL
);
ALTER TABLE exphelp_feedback ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_exphelp_feedback;
CREATE INDEX exphelp_feedback_object ON exphelp_feedback (object_id, created);
CREATE INDEX exphelp_feedback_created ON exphelp_feedback (created);

CREATE TABLE exp_migration (
  caption_id LVARCHAR(600) DEFAULT '' NOT NULL,
  checksum LVARCHAR(256) DEFAULT '' NOT NULL,
  duration_ms INTEGER DEFAULT 0 NOT NULL,
  engine VARCHAR(120) DEFAULT '' NOT NULL,
  environment VARCHAR(200) DEFAULT '' NOT NULL,
  file_path LVARCHAR(1020) DEFAULT '' NOT NULL,
  finished_at INTEGER DEFAULT 0 NOT NULL,
  message LVARCHAR(4000) DEFAULT '' NOT NULL,
  migration_id LVARCHAR(600) DEFAULT '' NOT NULL,
  revision LVARCHAR(256) DEFAULT '' NOT NULL,
  run_by LVARCHAR(600) DEFAULT '' NOT NULL,
  run_id LVARCHAR(256) DEFAULT '' NOT NULL,
  source LVARCHAR(400) DEFAULT '' NOT NULL,
  stage VARCHAR(80) DEFAULT '' NOT NULL,
  started_at INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(80) DEFAULT '' NOT NULL,
  version VARCHAR(120) DEFAULT '' NOT NULL
);
ALTER TABLE exp_migration ADD CONSTRAINT PRIMARY KEY (source, migration_id) CONSTRAINT pk_exp_migration;
CREATE INDEX exp_migration_run_id ON exp_migration (run_id);

CREATE TABLE exp_migration_deploy (
  deploy_id LVARCHAR(256) DEFAULT '' NOT NULL,
  finished_at INTEGER DEFAULT 0 NOT NULL,
  in_sync INTEGER DEFAULT 0 NOT NULL,
  message LVARCHAR(4000) DEFAULT '' NOT NULL,
  mode VARCHAR(80) DEFAULT '' NOT NULL,
  next_stage INTEGER DEFAULT 0 NOT NULL,
  run_by LVARCHAR(600) DEFAULT '' NOT NULL,
  stages INTEGER DEFAULT 0 NOT NULL,
  started_at INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(80) DEFAULT '' NOT NULL
);
ALTER TABLE exp_migration_deploy ADD CONSTRAINT PRIMARY KEY (deploy_id) CONSTRAINT pk_exp_migration_deploy;
CREATE INDEX exp_migration_deploy_started ON exp_migration_deploy (started_at);

CREATE TABLE exp_migration_deploy_host (
  address LVARCHAR(1020) DEFAULT '' NOT NULL,
  applied INTEGER DEFAULT 0 NOT NULL,
  caption LVARCHAR(600) DEFAULT '' NOT NULL,
  deploy_id LVARCHAR(256) DEFAULT '' NOT NULL,
  environment VARCHAR(200) DEFAULT '' NOT NULL,
  message LVARCHAR(4000) DEFAULT '' NOT NULL,
  remote_id LVARCHAR(256) DEFAULT '' NOT NULL,
  stage INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(80) DEFAULT '' NOT NULL,
  transport VARCHAR(80) DEFAULT '' NOT NULL
);
ALTER TABLE exp_migration_deploy_host ADD CONSTRAINT PRIMARY KEY (deploy_id, remote_id) CONSTRAINT pk_exp_migration_deploy_host;
CREATE INDEX exp_migration_deploy_host_remote ON exp_migration_deploy_host (remote_id);

CREATE TABLE exp_migration_run (
  caption_post LVARCHAR(600) DEFAULT '' NOT NULL,
  caption_pre LVARCHAR(600) DEFAULT '' NOT NULL,
  commit_id LVARCHAR(256) DEFAULT '' NOT NULL,
  environment VARCHAR(200) DEFAULT '' NOT NULL,
  finished_at INTEGER DEFAULT 0 NOT NULL,
  log_file LVARCHAR(1020) DEFAULT '' NOT NULL,
  message LVARCHAR(4000) DEFAULT '' NOT NULL,
  push_state VARCHAR(80) DEFAULT '' NOT NULL,
  run_by LVARCHAR(600) DEFAULT '' NOT NULL,
  run_id LVARCHAR(256) DEFAULT '' NOT NULL,
  started_at INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(80) DEFAULT '' NOT NULL,
  tag_name LVARCHAR(600) DEFAULT '' NOT NULL
);
ALTER TABLE exp_migration_run ADD CONSTRAINT PRIMARY KEY (run_id) CONSTRAINT pk_exp_migration_run;
CREATE INDEX exp_migration_run_started ON exp_migration_run (started_at);

CREATE TABLE exp_static_page (
  built_at INTEGER DEFAULT 0 NOT NULL,
  hash LVARCHAR(256) DEFAULT '' NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  page_bytes INTEGER DEFAULT 0 NOT NULL,
  path LVARCHAR(1020) DEFAULT '' NOT NULL,
  stale INTEGER DEFAULT 0 NOT NULL,
  target VARCHAR(200) DEFAULT '' NOT NULL
);
ALTER TABLE exp_static_page ADD CONSTRAINT PRIMARY KEY (target, path) CONSTRAINT pk_exp_static_page;
CREATE INDEX exp_static_page_node ON exp_static_page (node_id);

CREATE TABLE exp_static_run (
  added INTEGER DEFAULT 0 NOT NULL,
  bytes INTEGER DEFAULT 0 NOT NULL,
  changed INTEGER DEFAULT 0 NOT NULL,
  commit_id LVARCHAR(256) DEFAULT '' NOT NULL,
  files INTEGER DEFAULT 0 NOT NULL,
  finished_at INTEGER DEFAULT 0 NOT NULL,
  kind VARCHAR(80) DEFAULT '' NOT NULL,
  message LVARCHAR(4000) DEFAULT '' NOT NULL,
  pages INTEGER DEFAULT 0 NOT NULL,
  push_state VARCHAR(80) DEFAULT '' NOT NULL,
  removed INTEGER DEFAULT 0 NOT NULL,
  run_by LVARCHAR(600) DEFAULT '' NOT NULL,
  run_id LVARCHAR(256) DEFAULT '' NOT NULL,
  started_at INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(80) DEFAULT '' NOT NULL,
  tag_name LVARCHAR(600) DEFAULT '' NOT NULL,
  target VARCHAR(200) DEFAULT '' NOT NULL
);
ALTER TABLE exp_static_run ADD CONSTRAINT PRIMARY KEY (run_id) CONSTRAINT pk_exp_static_run;
CREATE INDEX exp_static_run_target ON exp_static_run (target, started_at);

CREATE TABLE exp_migration_recording (
  environment VARCHAR(200) DEFAULT '' NOT NULL,
  event_count INTEGER DEFAULT 0 NOT NULL,
  kind VARCHAR(80) DEFAULT '' NOT NULL,
  label LVARCHAR(1020) DEFAULT '' NOT NULL,
  message LVARCHAR(4000) DEFAULT '' NOT NULL,
  recording_id LVARCHAR(256) DEFAULT '' NOT NULL,
  started_at INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(80) DEFAULT '' NOT NULL,
  stopped_at INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exp_migration_recording ADD CONSTRAINT PRIMARY KEY (recording_id) CONSTRAINT pk_exp_migration_recording;
CREATE INDEX exp_migration_recording_state ON exp_migration_recording (state);

CREATE TABLE exp_migration_recording_event (
  actor_id INTEGER DEFAULT 0 NOT NULL,
  audit_name LVARCHAR(400) DEFAULT '' NOT NULL,
  event_id VARCHAR(128) DEFAULT '' NOT NULL,
  payload CLOB,
  recorded_at INTEGER DEFAULT 0 NOT NULL,
  recording_id LVARCHAR(256) DEFAULT '' NOT NULL,
  step_mode VARCHAR(80) DEFAULT '' NOT NULL,
  step_type VARCHAR(200) DEFAULT '' NOT NULL,
  subject LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE exp_migration_recording_event ADD CONSTRAINT PRIMARY KEY (recording_id, event_id) CONSTRAINT pk_exp_migration_recording_event;

CREATE TABLE ezproductcategory (
  id SERIAL NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezproductcategory ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezproductcategory;

CREATE TABLE ezproductcollection (
  created INTEGER DEFAULT NULL,
  currency_code VARCHAR(16) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL
);
ALTER TABLE ezproductcollection ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezproductcollection;

CREATE TABLE ezproductcollection_item (
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  discount SMALLFLOAT DEFAULT NULL,
  id SERIAL NOT NULL,
  is_vat_inc INTEGER DEFAULT NULL,
  item_count INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  price SMALLFLOAT DEFAULT 0,
  productcollection_id INTEGER DEFAULT 0 NOT NULL,
  vat_value SMALLFLOAT DEFAULT NULL
);
ALTER TABLE ezproductcollection_item ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezproductcollection_item;
CREATE INDEX ezproductcollection_item_contentobject_id ON ezproductcollection_item (contentobject_id);
CREATE INDEX ezproductcollection_item_productcollection_id ON ezproductcollection_item (productcollection_id);

CREATE TABLE ezproductcollection_item_opt (
  id SERIAL NOT NULL,
  item_id INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  object_attribute_id INTEGER DEFAULT NULL,
  option_item_id INTEGER DEFAULT 0 NOT NULL,
  price SMALLFLOAT DEFAULT 0 NOT NULL,
  value LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezproductcollection_item_opt ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezproductcollection_item_opt;
CREATE INDEX ezproductcollection_item_opt_item_id ON ezproductcollection_item_opt (item_id);

CREATE TABLE ezpublishingqueueprocesses (
  created INTEGER DEFAULT NULL,
  ezcontentobject_version_id INTEGER DEFAULT 0 NOT NULL,
  finished INTEGER DEFAULT NULL,
  pid INTEGER DEFAULT NULL,
  started INTEGER DEFAULT NULL,
  status INTEGER DEFAULT NULL
);
ALTER TABLE ezpublishingqueueprocesses ADD CONSTRAINT PRIMARY KEY (ezcontentobject_version_id) CONSTRAINT pk_ezpublishingqueueprocesses;

CREATE TABLE ezrole (
  id SERIAL NOT NULL,
  is_new INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  value VARCHAR(4) DEFAULT NULL,
  version INTEGER DEFAULT 0
);
ALTER TABLE ezrole ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezrole;

CREATE TABLE ezrss_export (
  access_url LVARCHAR(1020) DEFAULT NULL,
  opml_head LVARCHAR(8914),
  podcast_head LVARCHAR(8914),
  active INTEGER DEFAULT NULL,
  created INTEGER DEFAULT NULL,
  creator_id INTEGER DEFAULT NULL,
  description LVARCHAR(8914),
  id SERIAL NOT NULL,
  image_id INTEGER DEFAULT NULL,
  main_node_only INTEGER DEFAULT 1 NOT NULL,
  modified INTEGER DEFAULT NULL,
  modifier_id INTEGER DEFAULT NULL,
  node_id INTEGER DEFAULT NULL,
  number_of_objects INTEGER DEFAULT 0 NOT NULL,
  rss_version LVARCHAR(1020) DEFAULT NULL,
  site_access LVARCHAR(1020) DEFAULT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  title LVARCHAR(1020) DEFAULT NULL,
  url LVARCHAR(1020) DEFAULT NULL
);
ALTER TABLE ezrss_export ADD CONSTRAINT PRIMARY KEY (id, status) CONSTRAINT pk_ezrss_export;

CREATE TABLE ezrss_export_item (
  category LVARCHAR(1020) DEFAULT NULL,
  class_id INTEGER DEFAULT NULL,
  description LVARCHAR(1020) DEFAULT NULL,
  enclosure LVARCHAR(1020) DEFAULT NULL,
  id SERIAL NOT NULL,
  rssexport_id INTEGER DEFAULT NULL,
  source_node_id INTEGER DEFAULT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  subnodes INTEGER DEFAULT 0 NOT NULL,
  title LVARCHAR(1020) DEFAULT NULL
);
ALTER TABLE ezrss_export_item ADD CONSTRAINT PRIMARY KEY (id, status) CONSTRAINT pk_ezrss_export_item;
CREATE INDEX ezrss_export_item_ezrss_export_rsseid ON ezrss_export_item (rssexport_id);

CREATE TABLE ezrss_export_opml_item (
  category LVARCHAR(1020) DEFAULT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  description LVARCHAR(1020) DEFAULT NULL,
  html_url LVARCHAR(1020) DEFAULT NULL,
  id SERIAL NOT NULL,
  is_breakpoint INTEGER DEFAULT 0 NOT NULL,
  is_comment INTEGER DEFAULT 0 NOT NULL,
  language VARCHAR(200) DEFAULT NULL,
  outline_text LVARCHAR(1020) DEFAULT NULL,
  outline_type VARCHAR(200) DEFAULT 'rss',
  parent_id INTEGER DEFAULT 0 NOT NULL,
  priority INTEGER DEFAULT 0 NOT NULL,
  rssexport_id INTEGER DEFAULT 0 NOT NULL,
  source_node_id INTEGER DEFAULT 0 NOT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  subnodes INTEGER DEFAULT 0 NOT NULL,
  target_export_id INTEGER DEFAULT 0 NOT NULL,
  title LVARCHAR(1020) DEFAULT NULL,
  url LVARCHAR(1020) DEFAULT NULL,
  xml_url LVARCHAR(1020) DEFAULT NULL
);
ALTER TABLE ezrss_export_opml_item ADD CONSTRAINT PRIMARY KEY (id, status) CONSTRAINT pk_ezrss_export_opml_item;
CREATE INDEX ezrss_export_opml_item_ezrss_export_opml_rsseid ON ezrss_export_opml_item (rssexport_id);

CREATE TABLE ezrss_import (
  active INTEGER DEFAULT NULL,
  class_description LVARCHAR(1020) DEFAULT NULL,
  class_id INTEGER DEFAULT NULL,
  class_title LVARCHAR(1020) DEFAULT NULL,
  class_url LVARCHAR(1020) DEFAULT NULL,
  created INTEGER DEFAULT NULL,
  creator_id INTEGER DEFAULT NULL,
  destination_node_id INTEGER DEFAULT NULL,
  id SERIAL NOT NULL,
  import_description LVARCHAR(13891) NOT NULL,
  modified INTEGER DEFAULT NULL,
  modifier_id INTEGER DEFAULT NULL,
  name LVARCHAR(1020) DEFAULT NULL,
  object_owner_id INTEGER DEFAULT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  url LVARCHAR(13891)
);
ALTER TABLE ezrss_import ADD CONSTRAINT PRIMARY KEY (id, status) CONSTRAINT pk_ezrss_import;

CREATE TABLE ezscheduled_script (
  command LVARCHAR(1020) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  last_report_timestamp INTEGER DEFAULT 0 NOT NULL,
  name VARCHAR(200) DEFAULT '' NOT NULL,
  process_id INTEGER DEFAULT 0 NOT NULL,
  progress INTEGER DEFAULT 0,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezscheduled_script ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezscheduled_script;
CREATE INDEX ezscheduled_script_timestamp ON ezscheduled_script (last_report_timestamp);

CREATE TABLE ezsearch_object_word_link (
  contentclass_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  frequency SMALLFLOAT DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  identifier LVARCHAR(1020) DEFAULT '' NOT NULL,
  integer_value INTEGER DEFAULT 0 NOT NULL,
  next_word_id INTEGER DEFAULT 0 NOT NULL,
  placement INTEGER DEFAULT 0 NOT NULL,
  prev_word_id INTEGER DEFAULT 0 NOT NULL,
  published INTEGER DEFAULT 0 NOT NULL,
  section_id INTEGER DEFAULT 0 NOT NULL,
  word_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezsearch_object_word_link ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezsearch_object_word_link;
CREATE INDEX ezsearch_object_word_link_frequency ON ezsearch_object_word_link (frequency);
CREATE INDEX ezsearch_object_word_link_identifier ON ezsearch_object_word_link (identifier);
CREATE INDEX ezsearch_object_word_link_integer_value ON ezsearch_object_word_link (integer_value);
CREATE INDEX ezsearch_object_word_link_object ON ezsearch_object_word_link (contentobject_id);
CREATE INDEX ezsearch_object_word_link_word ON ezsearch_object_word_link (word_id);

CREATE TABLE ezsearch_search_phrase (
  id SERIAL NOT NULL,
  phrase LVARCHAR(1000) DEFAULT NULL,
  phrase_count INTEGER DEFAULT 0,
  result_count INTEGER DEFAULT 0
);
ALTER TABLE ezsearch_search_phrase ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezsearch_search_phrase;
CREATE INDEX ezsearch_search_phrase_count ON ezsearch_search_phrase (phrase_count);
CREATE UNIQUE INDEX ezsearch_search_phrase_phrase ON ezsearch_search_phrase (phrase);

CREATE TABLE ezsearch_word (
  id SERIAL NOT NULL,
  object_count INTEGER DEFAULT 0 NOT NULL,
  word LVARCHAR(600) DEFAULT NULL
);
ALTER TABLE ezsearch_word ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezsearch_word;
CREATE INDEX ezsearch_word_obj_count ON ezsearch_word (object_count);
CREATE INDEX ezsearch_word_word_i ON ezsearch_word (word);

CREATE TABLE ezsection (
  id SERIAL NOT NULL,
  identifier LVARCHAR(1020) DEFAULT NULL,
  locale LVARCHAR(1020) DEFAULT NULL,
  name LVARCHAR(1020) DEFAULT NULL,
  navigation_part_identifier LVARCHAR(400) DEFAULT 'ezcontentnavigationpart'
);
ALTER TABLE ezsection ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezsection;

CREATE TABLE ezsession (
  data CLOB NOT NULL,
  expiration_time INTEGER DEFAULT 0 NOT NULL,
  session_key VARCHAR(128) DEFAULT '' NOT NULL,
  user_hash VARCHAR(128) DEFAULT '' NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezsession ADD CONSTRAINT PRIMARY KEY (session_key) CONSTRAINT pk_ezsession;
CREATE INDEX ezsession_expiration_time ON ezsession (expiration_time);
CREATE INDEX ezsession_user_id ON ezsession (user_id);

CREATE TABLE ezsite_data (
  name VARCHAR(240) DEFAULT '' NOT NULL,
  value LVARCHAR(31754) NOT NULL
);
ALTER TABLE ezsite_data ADD CONSTRAINT PRIMARY KEY (name) CONSTRAINT pk_ezsite_data;

CREATE TABLE ezsubtree_notification_rule (
  id SERIAL NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  use_digest INTEGER DEFAULT 0,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezsubtree_notification_rule ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezsubtree_notification_rule;
CREATE INDEX ezsubtree_notification_rule_user_id ON ezsubtree_notification_rule (user_id);

CREATE TABLE eztipafriend_counter (
  count INTEGER DEFAULT 0 NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  requested INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE eztipafriend_counter ADD CONSTRAINT PRIMARY KEY (node_id, requested) CONSTRAINT pk_eztipafriend_counter;

CREATE TABLE eztipafriend_request (
  created INTEGER DEFAULT 0 NOT NULL,
  email_receiver LVARCHAR(400) DEFAULT '' NOT NULL
);
CREATE INDEX eztipafriend_request_created ON eztipafriend_request (created);
CREATE INDEX eztipafriend_request_email_rec ON eztipafriend_request (email_receiver);

CREATE TABLE eztrigger (
  connect_type VARCHAR(4) DEFAULT '' NOT NULL,
  function_name LVARCHAR(800) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  module_name LVARCHAR(800) DEFAULT '' NOT NULL,
  name LVARCHAR(1020) DEFAULT NULL,
  workflow_id INTEGER DEFAULT NULL
);
ALTER TABLE eztrigger ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_eztrigger;
CREATE UNIQUE INDEX eztrigger_def_id ON eztrigger (module_name, function_name, connect_type);
CREATE INDEX eztrigger_fetch ON eztrigger (name, module_name, function_name);

CREATE TABLE ezurl (
  created INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  is_valid INTEGER DEFAULT 1 NOT NULL,
  last_checked INTEGER DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  original_url_md5 VARCHAR(128) DEFAULT '' NOT NULL,
  url LVARCHAR(3224)
);
ALTER TABLE ezurl ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezurl;
CREATE INDEX ezurl_url ON ezurl (url);

CREATE TABLE ezurl_object_link (
  contentobject_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentobject_attribute_version INTEGER DEFAULT 0 NOT NULL,
  url_id INTEGER DEFAULT 0 NOT NULL
);
CREATE INDEX ezurl_object_link_ezurl_ol_coa_id ON ezurl_object_link (contentobject_attribute_id);
CREATE INDEX ezurl_object_link_ezurl_ol_coa_version ON ezurl_object_link (contentobject_attribute_version);
CREATE INDEX ezurl_object_link_ezurl_ol_url_id ON ezurl_object_link (url_id);

CREATE TABLE ezurlalias (
  destination_url LVARCHAR(3224) NOT NULL,
  forward_to_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  is_imported INTEGER DEFAULT 0 NOT NULL,
  is_internal INTEGER DEFAULT 1 NOT NULL,
  is_wildcard INTEGER DEFAULT 0 NOT NULL,
  source_md5 VARCHAR(128) DEFAULT NULL,
  source_url LVARCHAR(3224) NOT NULL
);
ALTER TABLE ezurlalias ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezurlalias;
CREATE INDEX ezurlalias_desturl ON ezurlalias (destination_url);
CREATE INDEX ezurlalias_forward_to_id ON ezurlalias (forward_to_id);
CREATE INDEX ezurlalias_imp_wcard_fwd ON ezurlalias (is_imported, is_wildcard, forward_to_id);
CREATE INDEX ezurlalias_source_md5 ON ezurlalias (source_md5);
CREATE INDEX ezurlalias_source_url ON ezurlalias (source_url);
CREATE INDEX ezurlalias_wcard_fwd ON ezurlalias (is_wildcard, forward_to_id);

CREATE TABLE ezurlalias_ml (
  action LVARCHAR(3212) NOT NULL,
  action_type VARCHAR(128) DEFAULT '' NOT NULL,
  alias_redirects INTEGER DEFAULT 1 NOT NULL,
  id INTEGER DEFAULT 0 NOT NULL,
  is_alias INTEGER DEFAULT 0 NOT NULL,
  is_original INTEGER DEFAULT 0 NOT NULL,
  lang_mask BIGINT DEFAULT 0 NOT NULL,
  link INTEGER DEFAULT 0 NOT NULL,
  parent INTEGER DEFAULT 0 NOT NULL,
  text LVARCHAR(3212) NOT NULL,
  text_md5 VARCHAR(128) DEFAULT '' NOT NULL
);
ALTER TABLE ezurlalias_ml ADD CONSTRAINT PRIMARY KEY (parent, text_md5) CONSTRAINT pk_ezurlalias_ml;
CREATE INDEX ezurlalias_ml_act_org ON ezurlalias_ml (action, is_original);
CREATE INDEX ezurlalias_ml_actt_org_al ON ezurlalias_ml (action_type, is_original, is_alias);
CREATE INDEX ezurlalias_ml_id ON ezurlalias_ml (id);
CREATE INDEX ezurlalias_ml_par_act_id_lnk ON ezurlalias_ml (action, id, link, parent);
CREATE INDEX ezurlalias_ml_par_lnk_txt ON ezurlalias_ml (parent, text, link);
CREATE INDEX ezurlalias_ml_text ON ezurlalias_ml (text, id, link);
CREATE INDEX ezurlalias_ml_text_lang ON ezurlalias_ml (text, lang_mask, parent);

CREATE TABLE ezurlalias_ml_incr (
  id SERIAL NOT NULL
);
ALTER TABLE ezurlalias_ml_incr ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezurlalias_ml_incr;

CREATE TABLE ezurlwildcard (
  destination_url LVARCHAR(15985) NOT NULL,
  id SERIAL NOT NULL,
  source_url LVARCHAR(15985) NOT NULL,
  type INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezurlwildcard ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezurlwildcard;

CREATE TABLE ezuser (
  contentobject_id INTEGER DEFAULT 0 NOT NULL,
  email LVARCHAR(600) DEFAULT '' NOT NULL,
  login LVARCHAR(600) DEFAULT '' NOT NULL,
  password_hash LVARCHAR(1020) DEFAULT NULL,
  password_hash_type INTEGER DEFAULT 1 NOT NULL
);
ALTER TABLE ezuser ADD CONSTRAINT PRIMARY KEY (contentobject_id) CONSTRAINT pk_ezuser;
CREATE UNIQUE INDEX ezuser_login ON ezuser (login);

CREATE TABLE ezuser_accountkey (
  hash_key VARCHAR(128) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  time INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezuser_accountkey ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezuser_accountkey;
CREATE INDEX ezuser_accountkey_hash_key ON ezuser_accountkey (hash_key);

CREATE TABLE ezuser_discountrule (
  contentobject_id INTEGER DEFAULT NULL,
  discountrule_id INTEGER DEFAULT NULL,
  id SERIAL NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezuser_discountrule ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezuser_discountrule;

CREATE TABLE ezuser_role (
  contentobject_id INTEGER DEFAULT NULL,
  id SERIAL NOT NULL,
  limit_identifier LVARCHAR(1020) DEFAULT '',
  limit_value LVARCHAR(1020) DEFAULT '',
  role_id INTEGER DEFAULT NULL
);
ALTER TABLE ezuser_role ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezuser_role;
CREATE INDEX ezuser_role_contentobject_id ON ezuser_role (contentobject_id);
CREATE INDEX ezuser_role_role_id ON ezuser_role (role_id);

CREATE TABLE ezuser_setting (
  is_enabled INTEGER DEFAULT 0 NOT NULL,
  max_login INTEGER DEFAULT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezuser_setting ADD CONSTRAINT PRIMARY KEY (user_id) CONSTRAINT pk_ezuser_setting;

CREATE TABLE ezuservisit (
  current_visit_timestamp INTEGER DEFAULT 0 NOT NULL,
  failed_login_attempts INTEGER DEFAULT 0 NOT NULL,
  last_visit_timestamp INTEGER DEFAULT 0 NOT NULL,
  login_count INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezuservisit ADD CONSTRAINT PRIMARY KEY (user_id) CONSTRAINT pk_ezuservisit;
CREATE INDEX ezuservisit_co_visit_count ON ezuservisit (current_visit_timestamp, login_count);

CREATE TABLE ezvatrule (
  country_code LVARCHAR(1020) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  vat_type INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezvatrule ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezvatrule;

CREATE TABLE ezvatrule_product_category (
  product_category_id INTEGER DEFAULT 0 NOT NULL,
  vatrule_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezvatrule_product_category ADD CONSTRAINT PRIMARY KEY (vatrule_id, product_category_id) CONSTRAINT pk_ezvatrule_product_category;

CREATE TABLE ezvattype (
  id SERIAL NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  percentage SMALLFLOAT DEFAULT NULL
);
ALTER TABLE ezvattype ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezvattype;

CREATE TABLE ezview_counter (
  count INTEGER DEFAULT 0 NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezview_counter ADD CONSTRAINT PRIMARY KEY (node_id) CONSTRAINT pk_ezview_counter;

CREATE TABLE ezwaituntildatevalue (
  contentclass_attribute_id INTEGER DEFAULT 0 NOT NULL,
  contentclass_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  workflow_event_id INTEGER DEFAULT 0 NOT NULL,
  workflow_event_version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezwaituntildatevalue ADD CONSTRAINT PRIMARY KEY (id, workflow_event_id, workflow_event_version) CONSTRAINT pk_ezwaituntildatevalue;
CREATE INDEX ezwaituntildatevalue_ezwaituntildateevalue_wf_ev_id_wf_ver ON ezwaituntildatevalue (workflow_event_id, workflow_event_version);

CREATE TABLE ezwishlist (
  id SERIAL NOT NULL,
  productcollection_id INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezwishlist ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezwishlist;

CREATE TABLE ezworkflow (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  is_enabled INTEGER DEFAULT 0 NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL,
  workflow_type_string VARCHAR(200) DEFAULT '' NOT NULL
);
ALTER TABLE ezworkflow ADD CONSTRAINT PRIMARY KEY (id, version) CONSTRAINT pk_ezworkflow;

CREATE TABLE ezworkflow_assign (
  access_type INTEGER DEFAULT 0 NOT NULL,
  as_tree INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  workflow_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezworkflow_assign ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezworkflow_assign;

CREATE TABLE ezworkflow_event (
  data_int1 INTEGER DEFAULT NULL,
  data_int2 INTEGER DEFAULT NULL,
  data_int3 INTEGER DEFAULT NULL,
  data_int4 INTEGER DEFAULT NULL,
  data_text1 LVARCHAR(1020) DEFAULT NULL,
  data_text2 LVARCHAR(1020) DEFAULT NULL,
  data_text3 LVARCHAR(1020) DEFAULT NULL,
  data_text4 LVARCHAR(1020) DEFAULT NULL,
  data_text5 LVARCHAR(27403),
  description VARCHAR(200) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  placement INTEGER DEFAULT 0 NOT NULL,
  version INTEGER DEFAULT 0 NOT NULL,
  workflow_id INTEGER DEFAULT 0 NOT NULL,
  workflow_type_string VARCHAR(200) DEFAULT '' NOT NULL
);
ALTER TABLE ezworkflow_event ADD CONSTRAINT PRIMARY KEY (id, version) CONSTRAINT pk_ezworkflow_event;
CREATE INDEX ezworkflow_event_wid_version_placement ON ezworkflow_event (workflow_id, version, placement);

CREATE TABLE ezworkflow_group (
  created INTEGER DEFAULT 0 NOT NULL,
  creator_id INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  modifier_id INTEGER DEFAULT 0 NOT NULL,
  name LVARCHAR(1020) DEFAULT '' NOT NULL
);
ALTER TABLE ezworkflow_group ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezworkflow_group;

CREATE TABLE ezworkflow_group_link (
  group_id INTEGER DEFAULT 0 NOT NULL,
  group_name LVARCHAR(1020) DEFAULT NULL,
  workflow_id INTEGER DEFAULT 0 NOT NULL,
  workflow_version INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezworkflow_group_link ADD CONSTRAINT PRIMARY KEY (workflow_id, group_id, workflow_version) CONSTRAINT pk_ezworkflow_group_link;

CREATE TABLE ezworkflow_process (
  activation_date INTEGER DEFAULT NULL,
  content_id INTEGER DEFAULT 0 NOT NULL,
  content_version INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  event_id INTEGER DEFAULT 0 NOT NULL,
  event_position INTEGER DEFAULT 0 NOT NULL,
  event_state INTEGER DEFAULT NULL,
  event_status INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  last_event_id INTEGER DEFAULT 0 NOT NULL,
  last_event_position INTEGER DEFAULT 0 NOT NULL,
  last_event_status INTEGER DEFAULT 0 NOT NULL,
  memento_key VARCHAR(128) DEFAULT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  node_id INTEGER DEFAULT 0 NOT NULL,
  parameters CLOB,
  process_key VARCHAR(128) DEFAULT '' NOT NULL,
  session_key VARCHAR(128) DEFAULT '0' NOT NULL,
  status INTEGER DEFAULT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL,
  workflow_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE ezworkflow_process ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_ezworkflow_process;
CREATE INDEX ezworkflow_process_process_key ON ezworkflow_process (process_key);

CREATE TABLE exp_url_redirect (
  batch_id INTEGER DEFAULT 0 NOT NULL,
  created INTEGER DEFAULT 0 NOT NULL,
  id SERIAL NOT NULL,
  language VARCHAR(80) DEFAULT '' NOT NULL,
  modified INTEGER DEFAULT 0 NOT NULL,
  siteaccess LVARCHAR(400) DEFAULT '' NOT NULL,
  source_key VARCHAR(128) DEFAULT '' NOT NULL,
  source_path LVARCHAR(4000) DEFAULT '' NOT NULL,
  source_query LVARCHAR(4000) DEFAULT '' NOT NULL,
  status INTEGER DEFAULT 301 NOT NULL,
  target LVARCHAR(8000) DEFAULT '' NOT NULL
);
ALTER TABLE exp_url_redirect ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_exp_url_redirect;
CREATE INDEX exp_url_redirect_batch ON exp_url_redirect (batch_id);
CREATE INDEX exp_url_redirect_source ON exp_url_redirect (source_key);

CREATE TABLE exp_urlmap_batch (
  created INTEGER DEFAULT 0 NOT NULL,
  created_count INTEGER DEFAULT 0 NOT NULL,
  failed_count INTEGER DEFAULT 0 NOT NULL,
  format VARCHAR(80) DEFAULT '' NOT NULL,
  id SERIAL NOT NULL,
  language VARCHAR(80) DEFAULT '' NOT NULL,
  on_conflict VARCHAR(80) DEFAULT '' NOT NULL,
  rows_total INTEGER DEFAULT 0 NOT NULL,
  siteaccess LVARCHAR(400) DEFAULT '' NOT NULL,
  skipped_count INTEGER DEFAULT 0 NOT NULL,
  source_name LVARCHAR(1020) DEFAULT '' NOT NULL,
  state VARCHAR(80) DEFAULT '' NOT NULL,
  summary LVARCHAR(30119),
  undone INTEGER DEFAULT 0 NOT NULL,
  undone_by INTEGER DEFAULT 0 NOT NULL,
  updated_count INTEGER DEFAULT 0 NOT NULL,
  user_id INTEGER DEFAULT 0 NOT NULL
);
ALTER TABLE exp_urlmap_batch ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_exp_urlmap_batch;
CREATE INDEX exp_urlmap_batch_created ON exp_urlmap_batch (created);

CREATE TABLE exp_urlmap_batch_item (
  action VARCHAR(80) DEFAULT '' NOT NULL,
  after_image CLOB,
  batch_id INTEGER DEFAULT 0 NOT NULL,
  before_image CLOB,
  id SERIAL NOT NULL,
  kind VARCHAR(120) DEFAULT '' NOT NULL,
  line INTEGER DEFAULT 0 NOT NULL,
  old_url LVARCHAR(4000) DEFAULT '' NOT NULL,
  seq INTEGER DEFAULT 0 NOT NULL,
  state VARCHAR(80) DEFAULT '' NOT NULL,
  target_key LVARCHAR(1020) DEFAULT '' NOT NULL,
  target_table VARCHAR(200) DEFAULT '' NOT NULL
);
ALTER TABLE exp_urlmap_batch_item ADD CONSTRAINT PRIMARY KEY (id) CONSTRAINT pk_exp_urlmap_batch_item;
CREATE INDEX exp_urlmap_batch_item_batch ON exp_urlmap_batch_item (batch_id, seq);
