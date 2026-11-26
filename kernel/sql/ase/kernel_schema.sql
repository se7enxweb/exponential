-- Exponential kernel schema for SAP ASE 15.7 and 16.0, written by expASESchema from share/db_schema.dba
-- for a database with a 16 KB logical page and the utf8 character set (isql -i kernel_schema.sql)

CREATE TABLE expaudit_cursor (
  byte_offset BIGINT DEFAULT 0 NOT NULL,
  channel UNIVARCHAR(32) DEFAULT '' NOT NULL,
  file_name UNIVARCHAR(64) DEFAULT '' NOT NULL,
  last_hash UNIVARCHAR(80) NULL,
  last_seq INT DEFAULT 0 NOT NULL,
  updated_ms BIGINT DEFAULT 0 NOT NULL
)
go
ALTER TABLE expaudit_cursor ADD CONSTRAINT PK_expaudit_cursor PRIMARY KEY (channel, file_name)
go

CREATE TABLE expaudit_event (
  channel UNIVARCHAR(32) DEFAULT '' NOT NULL,
  depth INT DEFAULT 0 NOT NULL,
  domain_name UNIVARCHAR(16) DEFAULT '' NOT NULL,
  engine UNIVARCHAR(16) NULL,
  file_name UNIVARCHAR(64) DEFAULT '' NOT NULL,
  id UNIVARCHAR(26) DEFAULT '' NOT NULL,
  imported INT DEFAULT 0 NOT NULL,
  ip UNIVARCHAR(64) NULL,
  job_id UNIVARCHAR(32) NULL,
  login UNIVARCHAR(150) NULL,
  module_view UNIVARCHAR(128) NULL,
  name UNIVARCHAR(128) DEFAULT '' NOT NULL,
  object_id UNIVARCHAR(64) NULL,
  object_name UNIVARCHAR(255) NULL,
  object_type UNIVARCHAR(32) NULL,
  parent_id UNIVARCHAR(26) NULL,
  pseudonymised INT DEFAULT 0 NOT NULL,
  reason UNIVARCHAR(32) NULL,
  record UNITEXT NULL,
  request_id UNIVARCHAR(40) NULL,
  result UNIVARCHAR(8) NULL,
  run_id UNIVARCHAR(40) NULL,
  search_text UNITEXT NULL,
  seq INT DEFAULT 0 NOT NULL,
  session_h UNIVARCHAR(24) NULL,
  severity INT DEFAULT 0 NOT NULL,
  siteaccess UNIVARCHAR(64) NULL,
  target_id UNIVARCHAR(64) NULL,
  target_type UNIVARCHAR(32) NULL,
  time_ms BIGINT DEFAULT 0 NOT NULL,
  ua UNIVARCHAR(128) NULL,
  user_id INT NULL,
  verb UNIVARCHAR(32) NULL
)
go
ALTER TABLE expaudit_event ADD CONSTRAINT PK_expaudit_event PRIMARY KEY (id)
go
CREATE INDEX expaudit_event_domain ON expaudit_event (domain_name, severity, time_ms)
go
CREATE UNIQUE INDEX expaudit_event_file_seq ON expaudit_event (channel, file_name, seq)
go
CREATE INDEX expaudit_event_ip ON expaudit_event (ip, time_ms)
go
CREATE INDEX expaudit_event_job ON expaudit_event (job_id)
go
CREATE INDEX expaudit_event_name ON expaudit_event (name, time_ms)
go
CREATE INDEX expaudit_event_object ON expaudit_event (object_type, object_id, time_ms)
go
CREATE INDEX expaudit_event_parent ON expaudit_event (parent_id)
go
CREATE INDEX expaudit_event_request ON expaudit_event (request_id)
go
CREATE INDEX expaudit_event_result ON expaudit_event (result, time_ms)
go
CREATE INDEX expaudit_event_time ON expaudit_event (time_ms)
go
CREATE INDEX expaudit_event_user ON expaudit_event (user_id, time_ms)
go

CREATE TABLE expaudit_file (
  archive_path UNIVARCHAR(255) NULL,
  break_line INT DEFAULT 0 NOT NULL,
  channel UNIVARCHAR(32) DEFAULT '' NOT NULL,
  file_name UNIVARCHAR(64) DEFAULT '' NOT NULL,
  records INT DEFAULT 0 NOT NULL,
  state UNIVARCHAR(16) DEFAULT 'live' NOT NULL,
  verified UNIVARCHAR(16) DEFAULT 'unchecked' NOT NULL,
  verified_ms BIGINT DEFAULT 0 NOT NULL
)
go
ALTER TABLE expaudit_file ADD CONSTRAINT PK_expaudit_file PRIMARY KEY (channel, file_name)
go

CREATE TABLE expmail_category (
  created INT DEFAULT 0 NOT NULL,
  default_on INT DEFAULT 0 NOT NULL,
  description UNITEXT NULL,
  double_opt_in INT DEFAULT 0 NOT NULL,
  essential INT DEFAULT 0 NOT NULL,
  frequencies UNIVARCHAR(100) DEFAULT '' NOT NULL,
  handler_class UNIVARCHAR(255) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  identifier UNIVARCHAR(100) DEFAULT '' NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  priority INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE expmail_category ADD CONSTRAINT PK_expmail_category PRIMARY KEY (id)
go
CREATE UNIQUE INDEX expmail_category_identifier ON expmail_category (identifier)
go

CREATE TABLE expmail_consent_log (
  action UNIVARCHAR(30) DEFAULT '' NOT NULL,
  actor_user_id INT DEFAULT 0 NOT NULL,
  anonymised INT DEFAULT 0 NOT NULL,
  category UNIVARCHAR(100) DEFAULT '' NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  email UNIVARCHAR(255) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  ip UNIVARCHAR(64) DEFAULT '' NOT NULL,
  new_value UNIVARCHAR(100) DEFAULT '' NOT NULL,
  old_value UNIVARCHAR(100) DEFAULT '' NOT NULL,
  recipient_key UNIVARCHAR(80) DEFAULT '' NOT NULL,
  siteaccess UNIVARCHAR(100) DEFAULT '' NOT NULL,
  source UNIVARCHAR(20) DEFAULT '' NOT NULL,
  user_id INT DEFAULT 0 NOT NULL,
  wording UNITEXT NULL
)
go
ALTER TABLE expmail_consent_log ADD CONSTRAINT PK_expmail_consent_log PRIMARY KEY (id)
go
CREATE INDEX expmail_consent_log_created ON expmail_consent_log (created)
go
CREATE INDEX expmail_consent_log_recipient ON expmail_consent_log (recipient_key, created)
go
CREATE INDEX expmail_consent_log_user ON expmail_consent_log (user_id)
go

CREATE TABLE expmail_pending (
  category UNIVARCHAR(100) DEFAULT '' NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  data UNITEXT NULL,
  expires INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  kind UNIVARCHAR(20) DEFAULT '' NOT NULL,
  recipient_key UNIVARCHAR(80) DEFAULT '' NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE expmail_pending ADD CONSTRAINT PK_expmail_pending PRIMARY KEY (id)
go
CREATE INDEX expmail_pending_expires ON expmail_pending (expires)
go
CREATE INDEX expmail_pending_recipient ON expmail_pending (recipient_key, kind)
go

CREATE TABLE expmail_preference (
  category UNIVARCHAR(100) DEFAULT '' NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  frequency UNIVARCHAR(20) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  recipient_key UNIVARCHAR(80) DEFAULT '' NOT NULL,
  state UNIVARCHAR(20) DEFAULT '' NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE expmail_preference ADD CONSTRAINT PK_expmail_preference PRIMARY KEY (id)
go
CREATE INDEX expmail_preference_category ON expmail_preference (category, state)
go
CREATE UNIQUE INDEX expmail_preference_recipient ON expmail_preference (recipient_key, category)
go
CREATE INDEX expmail_preference_user ON expmail_preference (user_id)
go

CREATE TABLE expmail_suppression (
  created INT DEFAULT 0 NOT NULL,
  created_by INT DEFAULT 0 NOT NULL,
  email_hash UNIVARCHAR(64) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  note UNITEXT NULL,
  reason UNIVARCHAR(30) DEFAULT '' NOT NULL
)
go
ALTER TABLE expmail_suppression ADD CONSTRAINT PK_expmail_suppression PRIMARY KEY (id)
go
CREATE UNIQUE INDEX expmail_suppression_hash ON expmail_suppression (email_hash)
go

CREATE TABLE ezapprove_items (
  collaboration_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  workflow_process_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezapprove_items ADD CONSTRAINT PK_ezapprove_items PRIMARY KEY (id)
go

CREATE TABLE ezbasket (
  id INT IDENTITY NOT NULL,
  order_id INT DEFAULT 0 NOT NULL,
  productcollection_id INT DEFAULT 0 NOT NULL,
  session_id UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezbasket ADD CONSTRAINT PK_ezbasket PRIMARY KEY (id)
go
CREATE INDEX ezbasket_session_id ON ezbasket (session_id)
go

CREATE TABLE ezbinaryfile (
  contentobject_attribute_id INT DEFAULT 0 NOT NULL,
  download_count INT DEFAULT 0 NOT NULL,
  filename UNIVARCHAR(255) DEFAULT '' NOT NULL,
  mime_type UNIVARCHAR(255) DEFAULT '' NOT NULL,
  original_filename UNIVARCHAR(255) DEFAULT '' NOT NULL,
  version INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezbinaryfile ADD CONSTRAINT PK_ezbinaryfile PRIMARY KEY (contentobject_attribute_id, version)
go

CREATE TABLE ezcobj_state (
  default_language_id BIGINT DEFAULT 0 NOT NULL,
  group_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  identifier UNIVARCHAR(45) DEFAULT '' NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  priority INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcobj_state ADD CONSTRAINT PK_ezcobj_state PRIMARY KEY (id)
go
CREATE UNIQUE INDEX ezcobj_state_identifier ON ezcobj_state (group_id, identifier)
go
CREATE INDEX ezcobj_state_lmask ON ezcobj_state (language_mask)
go
CREATE INDEX ezcobj_state_priority ON ezcobj_state (priority)
go

CREATE TABLE ezcobj_state_group (
  default_language_id BIGINT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  identifier UNIVARCHAR(45) DEFAULT '' NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcobj_state_group ADD CONSTRAINT PK_ezcobj_state_group PRIMARY KEY (id)
go
CREATE UNIQUE INDEX ezcobj_state_group_identifier ON ezcobj_state_group (identifier)
go
CREATE INDEX ezcobj_state_group_lmask ON ezcobj_state_group (language_mask)
go

CREATE TABLE ezcobj_state_group_language (
  contentobject_state_group_id INT DEFAULT 0 NOT NULL,
  description UNITEXT NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(45) DEFAULT '' NOT NULL,
  real_language_id BIGINT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcobj_state_group_language ADD CONSTRAINT PK_ezcobj_state_group_language PRIMARY KEY (contentobject_state_group_id, real_language_id)
go

CREATE TABLE ezcobj_state_language (
  contentobject_state_id INT DEFAULT 0 NOT NULL,
  description UNITEXT NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(45) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezcobj_state_language ADD CONSTRAINT PK_ezcobj_state_language PRIMARY KEY (contentobject_state_id, language_id)
go

CREATE TABLE ezcobj_state_link (
  contentobject_id INT DEFAULT 0 NOT NULL,
  contentobject_state_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcobj_state_link ADD CONSTRAINT PK_ezcobj_state_link PRIMARY KEY (contentobject_id, contentobject_state_id)
go

CREATE TABLE ezcollab_group (
  created INT DEFAULT 0 NOT NULL,
  depth INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  is_open INT DEFAULT 1 NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  parent_group_id INT DEFAULT 0 NOT NULL,
  path_string UNIVARCHAR(255) DEFAULT '' NOT NULL,
  priority INT DEFAULT 0 NOT NULL,
  title UNIVARCHAR(255) DEFAULT '' NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcollab_group ADD CONSTRAINT PK_ezcollab_group PRIMARY KEY (id)
go
CREATE INDEX ezcollab_group_depth ON ezcollab_group (depth)
go
CREATE INDEX ezcollab_group_path ON ezcollab_group (path_string)
go

CREATE TABLE ezcollab_item (
  created INT DEFAULT 0 NOT NULL,
  creator_id INT DEFAULT 0 NOT NULL,
  data_float1 REAL DEFAULT 0 NOT NULL,
  data_float2 REAL DEFAULT 0 NOT NULL,
  data_float3 REAL DEFAULT 0 NOT NULL,
  data_int1 INT DEFAULT 0 NOT NULL,
  data_int2 INT DEFAULT 0 NOT NULL,
  data_int3 INT DEFAULT 0 NOT NULL,
  data_text1 UNITEXT NOT NULL,
  data_text2 UNITEXT NOT NULL,
  data_text3 UNITEXT NOT NULL,
  id INT IDENTITY NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  status INT DEFAULT 1 NOT NULL,
  type_identifier UNIVARCHAR(40) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezcollab_item ADD CONSTRAINT PK_ezcollab_item PRIMARY KEY (id)
go

CREATE TABLE ezcollab_item_group_link (
  collaboration_id INT DEFAULT 0 NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  group_id INT DEFAULT 0 NOT NULL,
  is_active INT DEFAULT 1 NOT NULL,
  is_read INT DEFAULT 0 NOT NULL,
  last_read INT DEFAULT 0 NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcollab_item_group_link ADD CONSTRAINT PK_ezcollab_item_group_link PRIMARY KEY (collaboration_id, group_id, user_id)
go

CREATE TABLE ezcollab_item_message_link (
  collaboration_id INT DEFAULT 0 NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  message_id INT DEFAULT 0 NOT NULL,
  message_type INT DEFAULT 0 NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  participant_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcollab_item_message_link ADD CONSTRAINT PK_ezcollab_item_message_link PRIMARY KEY (id)
go

CREATE TABLE ezcollab_item_participant_link (
  collaboration_id INT DEFAULT 0 NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  is_active INT DEFAULT 1 NOT NULL,
  is_read INT DEFAULT 0 NOT NULL,
  last_read INT DEFAULT 0 NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  participant_id INT DEFAULT 0 NOT NULL,
  participant_role INT DEFAULT 1 NOT NULL,
  participant_type INT DEFAULT 1 NOT NULL
)
go
ALTER TABLE ezcollab_item_participant_link ADD CONSTRAINT PK_ezcollab_item_participant_link PRIMARY KEY (collaboration_id, participant_id)
go

CREATE TABLE ezcollab_item_status (
  collaboration_id INT DEFAULT 0 NOT NULL,
  is_active INT DEFAULT 1 NOT NULL,
  is_read INT DEFAULT 0 NOT NULL,
  last_read INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcollab_item_status ADD CONSTRAINT PK_ezcollab_item_status PRIMARY KEY (collaboration_id, user_id)
go

CREATE TABLE ezcollab_notification_rule (
  collab_identifier UNIVARCHAR(255) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  user_id UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezcollab_notification_rule ADD CONSTRAINT PK_ezcollab_notification_rule PRIMARY KEY (id)
go

CREATE TABLE ezcollab_profile (
  created INT DEFAULT 0 NOT NULL,
  data_text1 UNITEXT NOT NULL,
  id INT IDENTITY NOT NULL,
  main_group INT DEFAULT 0 NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcollab_profile ADD CONSTRAINT PK_ezcollab_profile PRIMARY KEY (id)
go

CREATE TABLE ezcollab_simple_message (
  created INT DEFAULT 0 NOT NULL,
  creator_id INT DEFAULT 0 NOT NULL,
  data_float1 REAL DEFAULT 0 NOT NULL,
  data_float2 REAL DEFAULT 0 NOT NULL,
  data_float3 REAL DEFAULT 0 NOT NULL,
  data_int1 INT DEFAULT 0 NOT NULL,
  data_int2 INT DEFAULT 0 NOT NULL,
  data_int3 INT DEFAULT 0 NOT NULL,
  data_text1 UNITEXT NOT NULL,
  data_text2 UNITEXT NOT NULL,
  data_text3 UNITEXT NOT NULL,
  id INT IDENTITY NOT NULL,
  message_type UNIVARCHAR(40) DEFAULT '' NOT NULL,
  modified INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcollab_simple_message ADD CONSTRAINT PK_ezcollab_simple_message PRIMARY KEY (id)
go

CREATE TABLE ezcontent_language (
  disabled INT DEFAULT 0 NOT NULL,
  id BIGINT DEFAULT 0 NOT NULL,
  locale UNIVARCHAR(20) DEFAULT '' NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezcontent_language ADD CONSTRAINT PK_ezcontent_language PRIMARY KEY (id)
go
CREATE INDEX ezcontent_language_name ON ezcontent_language (name)
go

CREATE TABLE ezcontentbrowsebookmark (
  id INT IDENTITY NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  node_id INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL,
  folder_id INT DEFAULT 0 NOT NULL,
  priority INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcontentbrowsebookmark ADD CONSTRAINT PK_ezcontentbrowsebookmark PRIMARY KEY (id)
go
CREATE INDEX ezcontentbrowsebookmark_folder ON ezcontentbrowsebookmark (user_id, folder_id)
go
CREATE INDEX ezcontentbrowsebookmark_user ON ezcontentbrowsebookmark (user_id)
go

CREATE TABLE expbookmark_folder (
  created INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  parent_id INT DEFAULT 0 NOT NULL,
  priority INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE expbookmark_folder ADD CONSTRAINT PK_expbookmark_folder PRIMARY KEY (id)
go
CREATE INDEX expbookmark_folder_user ON expbookmark_folder (user_id, parent_id)
go

CREATE TABLE expbookmark_view (
  bookmark_id INT DEFAULT 0 NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  module_name UNIVARCHAR(100) DEFAULT '' NOT NULL,
  module_uri UNITEXT NULL,
  user_id INT DEFAULT 0 NOT NULL,
  view_name UNIVARCHAR(100) DEFAULT '' NOT NULL,
  view_parameters UNITEXT NULL
)
go
ALTER TABLE expbookmark_view ADD CONSTRAINT PK_expbookmark_view PRIMARY KEY (bookmark_id)
go
CREATE INDEX expbookmark_view_module ON expbookmark_view (module_name, view_name)
go
CREATE INDEX expbookmark_view_user ON expbookmark_view (user_id)
go

CREATE TABLE ezcontentbrowserecent (
  created INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  node_id INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcontentbrowserecent ADD CONSTRAINT PK_ezcontentbrowserecent PRIMARY KEY (id)
go
CREATE INDEX ezcontentbrowserecent_user ON ezcontentbrowserecent (user_id)
go

CREATE TABLE ezcontentclass (
  always_available INT DEFAULT 0 NOT NULL,
  contentobject_name UNIVARCHAR(255) NULL,
  created INT DEFAULT 0 NOT NULL,
  creator_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  identifier UNIVARCHAR(50) DEFAULT '' NOT NULL,
  initial_language_id BIGINT DEFAULT 0 NOT NULL,
  is_container INT DEFAULT 0 NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  modifier_id INT DEFAULT 0 NOT NULL,
  remote_id UNIVARCHAR(100) DEFAULT '' NOT NULL,
  serialized_description_list UNITEXT NULL,
  serialized_name_list UNITEXT NULL,
  sort_field INT DEFAULT 1 NOT NULL,
  sort_order INT DEFAULT 1 NOT NULL,
  url_alias_name UNIVARCHAR(255) NULL,
  version INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcontentclass ADD CONSTRAINT PK_ezcontentclass PRIMARY KEY (id, version)
go
CREATE INDEX ezcontentclass_version ON ezcontentclass (version)
go
CREATE INDEX ezcontentclass_identifier ON ezcontentclass (identifier, version)
go

CREATE TABLE ezcontentclass_attribute (
  can_translate INT DEFAULT 1 NULL,
  category UNIVARCHAR(25) DEFAULT '' NOT NULL,
  contentclass_id INT DEFAULT 0 NOT NULL,
  data_float1 DOUBLE PRECISION NULL,
  data_float2 DOUBLE PRECISION NULL,
  data_float3 DOUBLE PRECISION NULL,
  data_float4 DOUBLE PRECISION NULL,
  data_int1 INT NULL,
  data_int2 INT NULL,
  data_int3 INT NULL,
  data_int4 INT NULL,
  data_text1 UNIVARCHAR(50) NULL,
  data_text2 UNIVARCHAR(50) NULL,
  data_text3 UNIVARCHAR(50) NULL,
  data_text4 UNIVARCHAR(255) NULL,
  data_text5 UNITEXT NULL,
  data_type_string UNIVARCHAR(50) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  identifier UNIVARCHAR(50) DEFAULT '' NOT NULL,
  is_information_collector INT DEFAULT 0 NOT NULL,
  is_required INT DEFAULT 0 NOT NULL,
  is_searchable INT DEFAULT 0 NOT NULL,
  placement INT DEFAULT 0 NOT NULL,
  serialized_data_text UNITEXT NULL,
  serialized_description_list UNITEXT NULL,
  serialized_name_list UNITEXT NOT NULL,
  version INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcontentclass_attribute ADD CONSTRAINT PK_ezcontentclass_attribute PRIMARY KEY (id, version)
go
CREATE INDEX ezcontentclass_attr_ccid ON ezcontentclass_attribute (contentclass_id)
go

CREATE TABLE ezcontentclass_classgroup (
  contentclass_id INT DEFAULT 0 NOT NULL,
  contentclass_version INT DEFAULT 0 NOT NULL,
  group_id INT DEFAULT 0 NOT NULL,
  group_name UNIVARCHAR(255) NULL
)
go
ALTER TABLE ezcontentclass_classgroup ADD CONSTRAINT PK_ezcontentclass_classgroup PRIMARY KEY (contentclass_id, contentclass_version, group_id)
go

CREATE TABLE ezcontentclass_name (
  contentclass_id INT DEFAULT 0 NOT NULL,
  contentclass_version INT DEFAULT 0 NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  language_locale UNIVARCHAR(20) DEFAULT '' NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezcontentclass_name ADD CONSTRAINT PK_ezcontentclass_name PRIMARY KEY (contentclass_id, contentclass_version, language_id)
go

CREATE TABLE ezcontentclassgroup (
  created INT DEFAULT 0 NOT NULL,
  creator_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  modifier_id INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) NULL
)
go
ALTER TABLE ezcontentclassgroup ADD CONSTRAINT PK_ezcontentclassgroup PRIMARY KEY (id)
go

CREATE TABLE ezcontentobject (
  contentclass_id INT DEFAULT 0 NOT NULL,
  current_version INT NULL,
  id INT IDENTITY NOT NULL,
  initial_language_id BIGINT DEFAULT 0 NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) NULL,
  owner_id INT DEFAULT 0 NOT NULL,
  published INT DEFAULT 0 NOT NULL,
  remote_id UNIVARCHAR(100) NULL,
  section_id INT DEFAULT 0 NOT NULL,
  status INT DEFAULT 0 NULL
)
go
ALTER TABLE ezcontentobject ADD CONSTRAINT PK_ezcontentobject PRIMARY KEY (id)
go
CREATE INDEX ezcontentobject_classid ON ezcontentobject (contentclass_id)
go
CREATE INDEX ezcontentobject_currentversion ON ezcontentobject (current_version)
go
CREATE INDEX ezcontentobject_lmask ON ezcontentobject (language_mask)
go
CREATE INDEX ezcontentobject_modified ON ezcontentobject (modified)
go
CREATE INDEX ezcontentobject_owner ON ezcontentobject (owner_id)
go
CREATE INDEX ezcontentobject_pub ON ezcontentobject (published)
go
CREATE UNIQUE INDEX ezcontentobject_remote_id ON ezcontentobject (remote_id)
go
CREATE INDEX ezcontentobject_status ON ezcontentobject (status)
go

CREATE TABLE ezcontentobject_attribute (
  attribute_original_id INT DEFAULT 0 NULL,
  contentclassattribute_id INT DEFAULT 0 NOT NULL,
  contentobject_id INT DEFAULT 0 NOT NULL,
  data_float DOUBLE PRECISION NULL,
  data_int INT NULL,
  data_text UNITEXT NULL,
  data_type_string UNIVARCHAR(50) DEFAULT '' NULL,
  id INT IDENTITY NOT NULL,
  language_code UNIVARCHAR(20) DEFAULT '' NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  sort_key_int INT DEFAULT 0 NOT NULL,
  sort_key_string UNIVARCHAR(255) DEFAULT '' NOT NULL,
  version INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcontentobject_attribute ADD CONSTRAINT PK_ezcontentobject_attribute PRIMARY KEY (id, version)
go
CREATE INDEX ezcontentobject_attribute_co_id_ver_lang_code ON ezcontentobject_attribute (contentobject_id, version, language_code)
go
CREATE INDEX ezcontentobject_attribute_language_code ON ezcontentobject_attribute (language_code)
go
CREATE INDEX ezcontentobject_classattr_id ON ezcontentobject_attribute (contentclassattribute_id)
go
CREATE INDEX sort_key_int ON ezcontentobject_attribute (sort_key_int)
go
CREATE INDEX sort_key_string ON ezcontentobject_attribute (sort_key_string)
go

CREATE TABLE ezcontentobject_link (
  contentclassattribute_id INT DEFAULT 0 NOT NULL,
  from_contentobject_id INT DEFAULT 0 NOT NULL,
  from_contentobject_version INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  relation_type INT DEFAULT 1 NOT NULL,
  to_contentobject_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezcontentobject_link ADD CONSTRAINT PK_ezcontentobject_link PRIMARY KEY (id)
go
CREATE INDEX ezco_link_from ON ezcontentobject_link (from_contentobject_id, from_contentobject_version, contentclassattribute_id)
go
CREATE INDEX ezco_link_to_co_id ON ezcontentobject_link (to_contentobject_id)
go

CREATE TABLE ezcontentobject_name (
  content_translation UNIVARCHAR(20) DEFAULT '' NOT NULL,
  content_version INT DEFAULT 0 NOT NULL,
  contentobject_id INT DEFAULT 0 NOT NULL,
  language_id BIGINT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) NULL,
  real_translation UNIVARCHAR(20) NULL
)
go
ALTER TABLE ezcontentobject_name ADD CONSTRAINT PK_ezcontentobject_name PRIMARY KEY (contentobject_id, content_version, content_translation)
go
CREATE INDEX ezcontentobject_name_cov_id ON ezcontentobject_name (content_version)
go
CREATE INDEX ezcontentobject_name_lang_id ON ezcontentobject_name (language_id)
go
CREATE INDEX ezcontentobject_name_name ON ezcontentobject_name (name)
go

CREATE TABLE ezcontentobject_trash (
  contentobject_id INT NULL,
  contentobject_version INT NULL,
  depth INT DEFAULT 0 NOT NULL,
  is_hidden INT DEFAULT 0 NOT NULL,
  is_invisible INT DEFAULT 0 NOT NULL,
  main_node_id INT NULL,
  modified_subnode INT DEFAULT 0 NULL,
  node_id INT DEFAULT 0 NOT NULL,
  parent_node_id INT DEFAULT 0 NOT NULL,
  path_identification_string UNIVARCHAR(1000) NULL,
  path_string UNIVARCHAR(255) DEFAULT '' NOT NULL,
  priority INT DEFAULT 0 NOT NULL,
  remote_id UNIVARCHAR(100) DEFAULT '' NOT NULL,
  sort_field INT DEFAULT 1 NULL,
  sort_order INT DEFAULT 1 NULL,
  trashed INT DEFAULT 0 NOT NULL,
  trashed_by INT DEFAULT 0 NOT NULL,
  trashed_via UNIVARCHAR(100) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezcontentobject_trash ADD CONSTRAINT PK_ezcontentobject_trash PRIMARY KEY (node_id)
go
CREATE INDEX ezcobj_trash_co_id ON ezcontentobject_trash (contentobject_id)
go
CREATE INDEX ezcobj_trash_depth ON ezcontentobject_trash (depth)
go
CREATE INDEX ezcobj_trash_modified_subnode ON ezcontentobject_trash (modified_subnode)
go
CREATE INDEX ezcobj_trash_p_node_id ON ezcontentobject_trash (parent_node_id)
go
CREATE INDEX ezcobj_trash_path ON ezcontentobject_trash (path_string)
go
CREATE INDEX ezcobj_trash_path_ident ON ezcontentobject_trash (path_identification_string)
go
CREATE INDEX ezcobj_trash_trashed_by ON ezcontentobject_trash (trashed_by)
go

CREATE TABLE ezcontentobject_tree (
  contentobject_id INT NULL,
  contentobject_is_published INT NULL,
  contentobject_version INT NULL,
  depth INT DEFAULT 0 NOT NULL,
  is_hidden INT DEFAULT 0 NOT NULL,
  is_invisible INT DEFAULT 0 NOT NULL,
  main_node_id INT NULL,
  modified_subnode INT DEFAULT 0 NULL,
  node_id INT IDENTITY NOT NULL,
  parent_node_id INT DEFAULT 0 NOT NULL,
  path_identification_string UNIVARCHAR(1000) NULL,
  path_string UNIVARCHAR(255) DEFAULT '' NOT NULL,
  priority INT DEFAULT 0 NOT NULL,
  remote_id UNIVARCHAR(100) DEFAULT '' NOT NULL,
  sort_field INT DEFAULT 1 NULL,
  sort_order INT DEFAULT 1 NULL
)
go
ALTER TABLE ezcontentobject_tree ADD CONSTRAINT PK_ezcontentobject_tree PRIMARY KEY (node_id)
go
CREATE INDEX ezcontentobject_tree_remote_id ON ezcontentobject_tree (remote_id)
go
CREATE INDEX ezcontentobject_tree_co_id ON ezcontentobject_tree (contentobject_id)
go
CREATE INDEX ezcontentobject_tree_depth ON ezcontentobject_tree (depth)
go
CREATE INDEX ezcontentobject_tree_p_node_id ON ezcontentobject_tree (parent_node_id)
go
CREATE INDEX ezcontentobject_tree_path ON ezcontentobject_tree (path_string)
go
CREATE INDEX ezcontentobject_tree_path_ident ON ezcontentobject_tree (path_identification_string)
go
CREATE INDEX modified_subnode ON ezcontentobject_tree (modified_subnode)
go

CREATE TABLE ezcontentobject_version (
  contentobject_id INT NULL,
  created INT DEFAULT 0 NOT NULL,
  creator_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  initial_language_id BIGINT DEFAULT 0 NOT NULL,
  language_mask BIGINT DEFAULT 0 NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  status INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL,
  version INT DEFAULT 0 NOT NULL,
  workflow_event_pos INT DEFAULT 0 NULL
)
go
ALTER TABLE ezcontentobject_version ADD CONSTRAINT PK_ezcontentobject_version PRIMARY KEY (id)
go
CREATE INDEX ezcobj_version_creator_id ON ezcontentobject_version (creator_id)
go
CREATE INDEX ezcobj_version_status ON ezcontentobject_version (status)
go
CREATE INDEX idx_object_version_objver ON ezcontentobject_version (contentobject_id, version)
go
CREATE INDEX ezcontobj_version_obj_status ON ezcontentobject_version (contentobject_id, status)
go

CREATE TABLE ezcurrencydata (
  auto_rate_value DECIMAL(10,5) DEFAULT 0 NOT NULL,
  code UNIVARCHAR(4) DEFAULT '' NOT NULL,
  custom_rate_value DECIMAL(10,5) DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  locale UNIVARCHAR(255) DEFAULT '' NOT NULL,
  rate_factor DECIMAL(10,5) DEFAULT 1 NOT NULL,
  status INT DEFAULT 1 NOT NULL,
  symbol UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezcurrencydata ADD CONSTRAINT PK_ezcurrencydata PRIMARY KEY (id)
go
CREATE INDEX ezcurrencydata_code ON ezcurrencydata (code)
go

CREATE TABLE ezdiscountrule (
  id INT IDENTITY NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezdiscountrule ADD CONSTRAINT PK_ezdiscountrule PRIMARY KEY (id)
go

CREATE TABLE ezdiscountsubrule (
  discount_percent REAL NULL,
  discountrule_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  limitation UNIVARCHAR(1) NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezdiscountsubrule ADD CONSTRAINT PK_ezdiscountsubrule PRIMARY KEY (id)
go

CREATE TABLE ezdiscountsubrule_value (
  discountsubrule_id INT DEFAULT 0 NOT NULL,
  issection INT DEFAULT 0 NOT NULL,
  value INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezdiscountsubrule_value ADD CONSTRAINT PK_ezdiscountsubrule_value PRIMARY KEY (discountsubrule_id, value, issection)
go

CREATE TABLE ezenumobjectvalue (
  contentobject_attribute_id INT DEFAULT 0 NOT NULL,
  contentobject_attribute_version INT DEFAULT 0 NOT NULL,
  enumelement UNIVARCHAR(255) DEFAULT '' NOT NULL,
  enumid INT DEFAULT 0 NOT NULL,
  enumvalue UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezenumobjectvalue ADD CONSTRAINT PK_ezenumobjectvalue PRIMARY KEY (contentobject_attribute_id, contentobject_attribute_version, enumid)
go

CREATE TABLE ezenumvalue (
  contentclass_attribute_id INT DEFAULT 0 NOT NULL,
  contentclass_attribute_version INT DEFAULT 0 NOT NULL,
  enumelement UNIVARCHAR(255) DEFAULT '' NOT NULL,
  enumvalue UNIVARCHAR(255) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  placement INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezenumvalue ADD CONSTRAINT PK_ezenumvalue PRIMARY KEY (id, contentclass_attribute_id, contentclass_attribute_version)
go
CREATE INDEX ezenumvalue_co_cl_attr_id_co_class_att_ver ON ezenumvalue (contentclass_attribute_id, contentclass_attribute_version)
go

CREATE TABLE ezforgot_password (
  hash_key UNIVARCHAR(32) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  time INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezforgot_password ADD CONSTRAINT PK_ezforgot_password PRIMARY KEY (id)
go
CREATE INDEX ezforgot_password_user ON ezforgot_password (user_id)
go

CREATE TABLE ezgeneral_digest_user_settings (
  day UNIVARCHAR(255) DEFAULT '' NOT NULL,
  digest_type INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  receive_digest INT DEFAULT 0 NOT NULL,
  time UNIVARCHAR(255) DEFAULT '' NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezgeneral_digest_user_settings ADD CONSTRAINT PK_ezgeneral_digest_user_settings PRIMARY KEY (id)
go
CREATE UNIQUE INDEX ezgeneral_digest_user_id ON ezgeneral_digest_user_settings (user_id)
go

CREATE TABLE ezimagefile (
  contentobject_attribute_id INT DEFAULT 0 NOT NULL,
  filepath UNIVARCHAR(1000) NOT NULL,
  id INT IDENTITY NOT NULL
)
go
ALTER TABLE ezimagefile ADD CONSTRAINT PK_ezimagefile PRIMARY KEY (id)
go
CREATE INDEX ezimagefile_coid ON ezimagefile (contentobject_attribute_id)
go
CREATE INDEX ezimagefile_file ON ezimagefile (filepath)
go

CREATE TABLE ezinfocollection (
  contentobject_id INT DEFAULT 0 NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  creator_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  modified INT DEFAULT 0 NULL,
  user_identifier UNIVARCHAR(34) NULL
)
go
ALTER TABLE ezinfocollection ADD CONSTRAINT PK_ezinfocollection PRIMARY KEY (id)
go
CREATE INDEX ezinfocollection_co_id_created ON ezinfocollection (contentobject_id, created)
go

CREATE TABLE ezinfocollection_attribute (
  contentclass_attribute_id INT DEFAULT 0 NOT NULL,
  contentobject_attribute_id INT NULL,
  contentobject_id INT NULL,
  data_float REAL NULL,
  data_int INT NULL,
  data_text UNITEXT NULL,
  id INT IDENTITY NOT NULL,
  informationcollection_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezinfocollection_attribute ADD CONSTRAINT PK_ezinfocollection_attribute PRIMARY KEY (id)
go
CREATE INDEX ezinfocollection_attr_cca_id ON ezinfocollection_attribute (contentclass_attribute_id)
go
CREATE INDEX ezinfocollection_attr_co_id ON ezinfocollection_attribute (contentobject_id)
go
CREATE INDEX ezinfocollection_attr_coa_id ON ezinfocollection_attribute (contentobject_attribute_id)
go
CREATE INDEX ezinfocollection_attr_ic_id ON ezinfocollection_attribute (informationcollection_id)
go

CREATE TABLE ezisbn_group (
  description UNIVARCHAR(255) DEFAULT '' NOT NULL,
  group_number INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL
)
go
ALTER TABLE ezisbn_group ADD CONSTRAINT PK_ezisbn_group PRIMARY KEY (id)
go

CREATE TABLE ezisbn_group_range (
  from_number INT DEFAULT 0 NOT NULL,
  group_from UNIVARCHAR(32) DEFAULT '' NOT NULL,
  group_length INT DEFAULT 0 NOT NULL,
  group_to UNIVARCHAR(32) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  to_number INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezisbn_group_range ADD CONSTRAINT PK_ezisbn_group_range PRIMARY KEY (id)
go

CREATE TABLE ezisbn_registrant_range (
  from_number INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  isbn_group_id INT DEFAULT 0 NOT NULL,
  registrant_from UNIVARCHAR(32) DEFAULT '' NOT NULL,
  registrant_length INT DEFAULT 0 NOT NULL,
  registrant_to UNIVARCHAR(32) DEFAULT '' NOT NULL,
  to_number INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezisbn_registrant_range ADD CONSTRAINT PK_ezisbn_registrant_range PRIMARY KEY (id)
go

CREATE TABLE ezkeyword (
  class_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  keyword UNIVARCHAR(255) NULL
)
go
ALTER TABLE ezkeyword ADD CONSTRAINT PK_ezkeyword PRIMARY KEY (id)
go
CREATE INDEX ezkeyword_keyword ON ezkeyword (keyword)
go

CREATE TABLE ezkeyword_attribute_link (
  id INT IDENTITY NOT NULL,
  keyword_id INT DEFAULT 0 NOT NULL,
  objectattribute_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezkeyword_attribute_link ADD CONSTRAINT PK_ezkeyword_attribute_link PRIMARY KEY (id)
go
CREATE INDEX ezkeyword_attr_link_kid_oaid ON ezkeyword_attribute_link (keyword_id, objectattribute_id)
go
CREATE INDEX ezkeyword_attr_link_oaid ON ezkeyword_attribute_link (objectattribute_id)
go

CREATE TABLE ezmedia (
  contentobject_attribute_id INT DEFAULT 0 NOT NULL,
  controls UNIVARCHAR(50) NULL,
  filename UNIVARCHAR(255) DEFAULT '' NOT NULL,
  has_controller INT DEFAULT 0 NULL,
  height INT NULL,
  is_autoplay INT DEFAULT 0 NULL,
  is_loop INT DEFAULT 0 NULL,
  mime_type UNIVARCHAR(50) DEFAULT '' NOT NULL,
  original_filename UNIVARCHAR(255) DEFAULT '' NOT NULL,
  pluginspage UNIVARCHAR(255) NULL,
  quality UNIVARCHAR(50) NULL,
  version INT DEFAULT 0 NOT NULL,
  width INT NULL
)
go
ALTER TABLE ezmedia ADD CONSTRAINT PK_ezmedia PRIMARY KEY (contentobject_attribute_id, version)
go

CREATE TABLE ezmessage (
  body UNITEXT NULL,
  destination_address UNIVARCHAR(50) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  is_sent INT DEFAULT 0 NOT NULL,
  send_method UNIVARCHAR(50) DEFAULT '' NOT NULL,
  send_time UNIVARCHAR(50) DEFAULT '' NOT NULL,
  send_weekday UNIVARCHAR(50) DEFAULT '' NOT NULL,
  title UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezmessage ADD CONSTRAINT PK_ezmessage PRIMARY KEY (id)
go

CREATE TABLE ezmodule_run (
  function_name UNIVARCHAR(255) NULL,
  id INT IDENTITY NOT NULL,
  module_data UNITEXT NULL,
  module_name UNIVARCHAR(255) NULL,
  workflow_process_id INT NULL
)
go
ALTER TABLE ezmodule_run ADD CONSTRAINT PK_ezmodule_run PRIMARY KEY (id)
go
CREATE UNIQUE INDEX ezmodule_run_workflow_process_id_s ON ezmodule_run (workflow_process_id)
go

CREATE TABLE ezmultipricedata (
  contentobject_attr_id INT DEFAULT 0 NOT NULL,
  contentobject_attr_version INT DEFAULT 0 NOT NULL,
  currency_code UNIVARCHAR(4) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  type INT DEFAULT 0 NOT NULL,
  value DECIMAL(15,2) DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezmultipricedata ADD CONSTRAINT PK_ezmultipricedata PRIMARY KEY (id)
go
CREATE INDEX ezmultipricedata_coa_id ON ezmultipricedata (contentobject_attr_id)
go
CREATE INDEX ezmultipricedata_coa_version ON ezmultipricedata (contentobject_attr_version)
go
CREATE INDEX ezmultipricedata_currency_code ON ezmultipricedata (currency_code)
go

CREATE TABLE eznode_assignment (
  contentobject_id INT NULL,
  contentobject_version INT NULL,
  from_node_id INT DEFAULT 0 NULL,
  id INT IDENTITY NOT NULL,
  is_main INT DEFAULT 0 NOT NULL,
  op_code INT DEFAULT 0 NOT NULL,
  parent_node INT NULL,
  parent_remote_id UNIVARCHAR(100) DEFAULT '' NOT NULL,
  remote_id UNIVARCHAR(100) DEFAULT '0' NOT NULL,
  sort_field INT DEFAULT 1 NULL,
  sort_order INT DEFAULT 1 NULL,
  priority INT DEFAULT 0 NOT NULL,
  is_hidden INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE eznode_assignment ADD CONSTRAINT PK_eznode_assignment PRIMARY KEY (id)
go
CREATE INDEX eznode_assignment_co_version ON eznode_assignment (contentobject_version)
go
CREATE INDEX eznode_assignment_coid_cov ON eznode_assignment (contentobject_id, contentobject_version)
go
CREATE INDEX eznode_assignment_is_main ON eznode_assignment (is_main)
go
CREATE INDEX eznode_assignment_parent_node ON eznode_assignment (parent_node)
go

CREATE TABLE eznotificationcollection (
  data_subject UNITEXT NOT NULL,
  data_text UNITEXT NOT NULL,
  event_id INT DEFAULT 0 NOT NULL,
  handler UNIVARCHAR(255) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  transport UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE eznotificationcollection ADD CONSTRAINT PK_eznotificationcollection PRIMARY KEY (id)
go

CREATE TABLE eznotificationcollection_item (
  address UNIVARCHAR(255) DEFAULT '' NOT NULL,
  collection_id INT DEFAULT 0 NOT NULL,
  event_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  send_date INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE eznotificationcollection_item ADD CONSTRAINT PK_eznotificationcollection_item PRIMARY KEY (id)
go

CREATE TABLE eznotificationevent (
  data_int1 INT DEFAULT 0 NOT NULL,
  data_int2 INT DEFAULT 0 NOT NULL,
  data_int3 INT DEFAULT 0 NOT NULL,
  data_int4 INT DEFAULT 0 NOT NULL,
  data_text1 UNITEXT NOT NULL,
  data_text2 UNITEXT NOT NULL,
  data_text3 UNITEXT NOT NULL,
  data_text4 UNITEXT NOT NULL,
  event_type_string UNIVARCHAR(255) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  status INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE eznotificationevent ADD CONSTRAINT PK_eznotificationevent PRIMARY KEY (id)
go

CREATE TABLE ezoperation_memento (
  id INT IDENTITY NOT NULL,
  main INT DEFAULT 0 NOT NULL,
  main_key UNIVARCHAR(32) DEFAULT '' NOT NULL,
  memento_data UNITEXT NOT NULL,
  memento_key UNIVARCHAR(32) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezoperation_memento ADD CONSTRAINT PK_ezoperation_memento PRIMARY KEY (id, memento_key)
go
CREATE INDEX ezoperation_memento_memento_key_main ON ezoperation_memento (memento_key, main)
go

CREATE TABLE ezorder (
  account_identifier UNIVARCHAR(100) DEFAULT 'default' NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  data_text_1 UNITEXT NULL,
  data_text_2 UNITEXT NULL,
  email UNIVARCHAR(150) DEFAULT '' NULL,
  id INT IDENTITY NOT NULL,
  ignore_vat INT DEFAULT 0 NOT NULL,
  is_archived INT DEFAULT 0 NOT NULL,
  is_temporary INT DEFAULT 1 NOT NULL,
  order_nr INT DEFAULT 0 NOT NULL,
  productcollection_id INT DEFAULT 0 NOT NULL,
  siteaccess UNIVARCHAR(255) DEFAULT '' NOT NULL,
  status_id INT DEFAULT 0 NULL,
  status_modified INT DEFAULT 0 NULL,
  status_modifier_id INT DEFAULT 0 NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezorder ADD CONSTRAINT PK_ezorder PRIMARY KEY (id)
go
CREATE INDEX ezorder_is_archived ON ezorder (is_archived)
go
CREATE INDEX ezorder_is_tmp ON ezorder (is_temporary)
go

CREATE TABLE ezorder_nr_incr (
  id INT IDENTITY NOT NULL
)
go
ALTER TABLE ezorder_nr_incr ADD CONSTRAINT PK_ezorder_nr_incr PRIMARY KEY (id)
go

CREATE TABLE ezorder_item (
  description UNIVARCHAR(255) NULL,
  id INT IDENTITY NOT NULL,
  is_vat_inc INT DEFAULT 0 NOT NULL,
  order_id INT DEFAULT 0 NOT NULL,
  price REAL NULL,
  type UNIVARCHAR(30) NULL,
  vat_value REAL DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezorder_item ADD CONSTRAINT PK_ezorder_item PRIMARY KEY (id)
go
CREATE INDEX ezorder_item_order_id ON ezorder_item (order_id)
go
CREATE INDEX ezorder_item_type ON ezorder_item (type)
go

CREATE TABLE ezorder_status (
  id INT IDENTITY NOT NULL,
  is_active INT DEFAULT 1 NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  status_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezorder_status ADD CONSTRAINT PK_ezorder_status PRIMARY KEY (id)
go
CREATE INDEX ezorder_status_active ON ezorder_status (is_active)
go
CREATE INDEX ezorder_status_name ON ezorder_status (name)
go
CREATE INDEX ezorder_status_sid ON ezorder_status (status_id)
go

CREATE TABLE ezorder_status_history (
  id INT IDENTITY NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  modifier_id INT DEFAULT 0 NOT NULL,
  order_id INT DEFAULT 0 NOT NULL,
  status_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezorder_status_history ADD CONSTRAINT PK_ezorder_status_history PRIMARY KEY (id)
go
CREATE INDEX ezorder_status_history_mod ON ezorder_status_history (modified)
go
CREATE INDEX ezorder_status_history_oid ON ezorder_status_history (order_id)
go
CREATE INDEX ezorder_status_history_sid ON ezorder_status_history (status_id)
go

CREATE TABLE ezpackage (
  id INT IDENTITY NOT NULL,
  install_date INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(100) DEFAULT '' NOT NULL,
  version UNIVARCHAR(30) DEFAULT '0' NOT NULL
)
go
ALTER TABLE ezpackage ADD CONSTRAINT PK_ezpackage PRIMARY KEY (id)
go

CREATE TABLE ezpaymentobject (
  id INT IDENTITY NOT NULL,
  order_id INT DEFAULT 0 NOT NULL,
  payment_string UNIVARCHAR(255) DEFAULT '' NOT NULL,
  status INT DEFAULT 0 NOT NULL,
  workflowprocess_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezpaymentobject ADD CONSTRAINT PK_ezpaymentobject PRIMARY KEY (id)
go

CREATE TABLE ezpdf_export (
  footer_text UNIVARCHAR(255) DEFAULT '' NOT NULL,
  show_footer INT DEFAULT 1 NOT NULL,
  created INT NULL,
  creator_id INT NULL,
  export_classes UNIVARCHAR(255) NULL,
  export_structure UNIVARCHAR(255) NULL,
  id INT IDENTITY NOT NULL,
  intro_text UNITEXT NULL,
  modified INT NULL,
  modifier_id INT NULL,
  pdf_filename UNIVARCHAR(255) NULL,
  show_frontpage INT NULL,
  site_access UNIVARCHAR(255) NULL,
  source_node_id INT NULL,
  status INT NULL,
  sub_text UNITEXT NULL,
  title UNIVARCHAR(255) NULL,
  version INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezpdf_export ADD CONSTRAINT PK_ezpdf_export PRIMARY KEY (id, version)
go

CREATE TABLE ezpending_actions (
  id INT IDENTITY NOT NULL,
  action UNIVARCHAR(64) DEFAULT '' NOT NULL,
  created INT NULL,
  param UNITEXT NULL
)
go
ALTER TABLE ezpending_actions ADD CONSTRAINT PK_ezpending_actions PRIMARY KEY (id)
go
CREATE INDEX ezpending_actions_action ON ezpending_actions (action)
go
CREATE INDEX ezpending_actions_created ON ezpending_actions (created)
go

CREATE TABLE ezpolicy (
  function_name UNIVARCHAR(255) NULL,
  id INT IDENTITY NOT NULL,
  module_name UNIVARCHAR(255) NULL,
  original_id INT DEFAULT 0 NOT NULL,
  role_id INT NULL
)
go
ALTER TABLE ezpolicy ADD CONSTRAINT PK_ezpolicy PRIMARY KEY (id)
go
CREATE INDEX ezpolicy_original_id ON ezpolicy (original_id)
go
CREATE INDEX ezpolicy_role_id ON ezpolicy (role_id)
go

CREATE TABLE ezpolicy_limitation (
  id INT IDENTITY NOT NULL,
  identifier UNIVARCHAR(255) DEFAULT '' NOT NULL,
  policy_id INT NULL
)
go
ALTER TABLE ezpolicy_limitation ADD CONSTRAINT PK_ezpolicy_limitation PRIMARY KEY (id)
go
CREATE INDEX policy_id ON ezpolicy_limitation (policy_id)
go

CREATE TABLE ezpolicy_limitation_value (
  id INT IDENTITY NOT NULL,
  limitation_id INT NULL,
  value UNIVARCHAR(255) NULL
)
go
ALTER TABLE ezpolicy_limitation_value ADD CONSTRAINT PK_ezpolicy_limitation_value PRIMARY KEY (id)
go
CREATE INDEX ezpolicy_limitation_value_val ON ezpolicy_limitation_value (value)
go
CREATE INDEX ezpolicy_limit_value_limit_id ON ezpolicy_limitation_value (limitation_id)
go

CREATE TABLE ezpreferences (
  id INT IDENTITY NOT NULL,
  name UNIVARCHAR(100) NULL,
  user_id INT DEFAULT 0 NOT NULL,
  value UNITEXT NULL
)
go
ALTER TABLE ezpreferences ADD CONSTRAINT PK_ezpreferences PRIMARY KEY (id)
go
CREATE INDEX ezpreferences_name ON ezpreferences (name)
go
CREATE INDEX ezpreferences_user_id_idx ON ezpreferences (user_id, name)
go

CREATE TABLE ezprest_authcode (
  client_id UNIVARCHAR(200) DEFAULT '' NOT NULL,
  code_challenge UNIVARCHAR(128) NULL,
  code_challenge_method UNIVARCHAR(10) NULL,
  expirytime BIGINT DEFAULT 0 NOT NULL,
  id UNIVARCHAR(200) DEFAULT '' NOT NULL,
  scope UNIVARCHAR(200) NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezprest_authcode ADD CONSTRAINT PK_ezprest_authcode PRIMARY KEY (id)
go
CREATE INDEX authcode_client_id ON ezprest_authcode (client_id)
go

CREATE TABLE ezprest_authorized_clients (
  created INT NULL,
  id INT IDENTITY NOT NULL,
  rest_client_id INT NULL,
  user_id INT NULL
)
go
ALTER TABLE ezprest_authorized_clients ADD CONSTRAINT PK_ezprest_authorized_clients PRIMARY KEY (id)
go
CREATE INDEX client_user ON ezprest_authorized_clients (rest_client_id, user_id)
go

CREATE TABLE ezprest_clients (
  client_id UNIVARCHAR(200) NULL,
  client_secret UNIVARCHAR(200) NULL,
  created INT DEFAULT 0 NOT NULL,
  description UNITEXT NULL,
  endpoint_uri UNIVARCHAR(200) NULL,
  id INT IDENTITY NOT NULL,
  name UNIVARCHAR(100) NULL,
  owner_id INT DEFAULT 0 NOT NULL,
  updated INT DEFAULT 0 NOT NULL,
  version INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezprest_clients ADD CONSTRAINT PK_ezprest_clients PRIMARY KEY (id)
go
CREATE UNIQUE INDEX client_id_unique ON ezprest_clients (client_id, version)
go

CREATE TABLE ezprest_token (
  client_id UNIVARCHAR(200) DEFAULT '' NOT NULL,
  expirytime BIGINT DEFAULT 0 NOT NULL,
  id UNIVARCHAR(200) DEFAULT '' NOT NULL,
  refresh_token UNIVARCHAR(200) DEFAULT '' NOT NULL,
  scope UNIVARCHAR(200) NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezprest_token ADD CONSTRAINT PK_ezprest_token PRIMARY KEY (id)
go
CREATE INDEX token_client_id ON ezprest_token (client_id)
go

CREATE TABLE expapikey (
  created INT DEFAULT 0 NOT NULL,
  created_by INT DEFAULT 0 NOT NULL,
  expires INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  key_prefix UNIVARCHAR(40) DEFAULT '' NOT NULL,
  last_ip UNIVARCHAR(64) DEFAULT '' NOT NULL,
  last_used INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  revoked INT DEFAULT 0 NOT NULL,
  revoked_by INT DEFAULT 0 NOT NULL,
  salt UNIVARCHAR(64) DEFAULT '' NOT NULL,
  scopes UNIVARCHAR(255) DEFAULT '' NOT NULL,
  secret_hash UNIVARCHAR(128) DEFAULT '' NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE expapikey ADD CONSTRAINT PK_expapikey PRIMARY KEY (id)
go
CREATE UNIQUE INDEX expapikey_prefix ON expapikey (key_prefix)
go
CREATE INDEX expapikey_user ON expapikey (user_id)
go

CREATE TABLE exppasskey (
  aaguid UNIVARCHAR(36) DEFAULT '' NOT NULL,
  algorithm INT DEFAULT 0 NOT NULL,
  backup_eligible INT DEFAULT 0 NOT NULL,
  backup_state INT DEFAULT 0 NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  credential_hash UNIVARCHAR(64) DEFAULT '' NOT NULL,
  credential_id UNITEXT NULL,
  id INT IDENTITY NOT NULL,
  last_ip UNIVARCHAR(64) DEFAULT '' NOT NULL,
  last_used INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  public_key UNITEXT NULL,
  sign_count BIGINT DEFAULT 0 NOT NULL,
  transports UNIVARCHAR(255) DEFAULT '' NOT NULL,
  user_handle UNIVARCHAR(128) DEFAULT '' NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE exppasskey ADD CONSTRAINT PK_exppasskey PRIMARY KEY (id)
go
CREATE UNIQUE INDEX exppasskey_credential ON exppasskey (credential_hash)
go
CREATE INDEX exppasskey_user ON exppasskey (user_id)
go

CREATE TABLE exp_transfer_run (
  bytes BIGINT DEFAULT 0 NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  detail UNITEXT NULL,
  detail_url UNIVARCHAR(255) DEFAULT '' NOT NULL,
  direction UNIVARCHAR(10) DEFAULT '' NOT NULL,
  duration INT DEFAULT 0 NOT NULL,
  engine UNIVARCHAR(16) DEFAULT '' NOT NULL,
  error UNIVARCHAR(255) DEFAULT '' NOT NULL,
  file_ref UNIVARCHAR(255) DEFAULT '' NOT NULL,
  finished INT DEFAULT 0 NOT NULL,
  format UNIVARCHAR(32) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  job_ref UNIVARCHAR(64) DEFAULT '' NOT NULL,
  kind UNIVARCHAR(20) DEFAULT '' NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  rows_done INT DEFAULT 0 NOT NULL,
  rows_failed INT DEFAULT 0 NOT NULL,
  rows_total INT DEFAULT 0 NOT NULL,
  scope UNIVARCHAR(20) DEFAULT '' NOT NULL,
  scope_ref UNIVARCHAR(255) DEFAULT '' NOT NULL,
  siteaccess UNIVARCHAR(64) DEFAULT '' NOT NULL,
  started INT DEFAULT 0 NOT NULL,
  state UNIVARCHAR(16) DEFAULT '' NOT NULL,
  title UNIVARCHAR(255) DEFAULT '' NOT NULL,
  tool UNIVARCHAR(64) DEFAULT '' NOT NULL,
  tool_ref UNIVARCHAR(64) DEFAULT '' NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE exp_transfer_run ADD CONSTRAINT PK_exp_transfer_run PRIMARY KEY (id)
go
CREATE INDEX exp_transfer_run_started ON exp_transfer_run (started)
go
CREATE INDEX exp_transfer_run_tool ON exp_transfer_run (tool, started)
go
CREATE INDEX exp_transfer_run_toolref ON exp_transfer_run (tool, tool_ref)
go
CREATE INDEX exp_transfer_run_user ON exp_transfer_run (user_id)
go

CREATE TABLE exp_data_connection (
  created INT DEFAULT 0 NOT NULL,
  creator_id INT DEFAULT 0 NOT NULL,
  database_name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  description UNIVARCHAR(255) DEFAULT '' NOT NULL,
  engine UNIVARCHAR(32) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  identifier UNIVARCHAR(64) DEFAULT '' NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  modifier_id INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  options UNITEXT NULL,
  port INT DEFAULT 0 NOT NULL,
  read_only INT DEFAULT 0 NOT NULL,
  secret UNIVARCHAR(128) DEFAULT '' NOT NULL,
  server UNIVARCHAR(255) DEFAULT '' NOT NULL,
  user_name UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE exp_data_connection ADD CONSTRAINT PK_exp_data_connection PRIMARY KEY (id)
go
CREATE UNIQUE INDEX exp_data_connection_identifier ON exp_data_connection (identifier)
go

CREATE TABLE exp_secret (
  ciphertext UNITEXT NULL,
  created INT DEFAULT 0 NOT NULL,
  creator_id INT DEFAULT 0 NOT NULL,
  description UNIVARCHAR(255) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  key_id UNIVARCHAR(16) DEFAULT '' NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  modifier_id INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(128) DEFAULT '' NOT NULL,
  scope UNIVARCHAR(64) DEFAULT '' NOT NULL
)
go
ALTER TABLE exp_secret ADD CONSTRAINT PK_exp_secret PRIMARY KEY (id)
go
CREATE UNIQUE INDEX exp_secret_name ON exp_secret (name)
go
CREATE INDEX exp_secret_scope ON exp_secret (scope)
go

CREATE TABLE exp_data_sync (
  class_name UNIVARCHAR(128) DEFAULT '' NOT NULL,
  conflict_rule UNIVARCHAR(16) DEFAULT '' NOT NULL,
  content_class UNIVARCHAR(128) DEFAULT '' NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  creator_id INT DEFAULT 0 NOT NULL,
  direction UNIVARCHAR(16) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  interval_seconds INT DEFAULT 0 NOT NULL,
  key_columns UNIVARCHAR(255) DEFAULT '' NOT NULL,
  last_run INT DEFAULT 0 NOT NULL,
  last_run_id INT DEFAULT 0 NOT NULL,
  map UNITEXT NULL,
  modified INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  next_run INT DEFAULT 0 NOT NULL,
  options UNITEXT NULL,
  run_mode UNIVARCHAR(16) DEFAULT '' NOT NULL,
  state UNIVARCHAR(16) DEFAULT '' NOT NULL,
  subtree_node_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE exp_data_sync ADD CONSTRAINT PK_exp_data_sync PRIMARY KEY (id)
go
CREATE INDEX exp_data_sync_mode ON exp_data_sync (run_mode, state)
go

CREATE TABLE exp_data_sync_conflict (
  content_values UNITEXT NULL,
  detected INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  link_id INT DEFAULT 0 NOT NULL,
  object_id INT DEFAULT 0 NOT NULL,
  resolution UNIVARCHAR(16) DEFAULT '' NOT NULL,
  resolved INT DEFAULT 0 NOT NULL,
  resolver_id INT DEFAULT 0 NOT NULL,
  row_key UNIVARCHAR(255) DEFAULT '' NOT NULL,
  row_values UNITEXT NULL,
  state UNIVARCHAR(16) DEFAULT '' NOT NULL,
  sync_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE exp_data_sync_conflict ADD CONSTRAINT PK_exp_data_sync_conflict PRIMARY KEY (id)
go
CREATE INDEX exp_data_sync_conflict_state ON exp_data_sync_conflict (sync_id, state)
go

CREATE TABLE exp_data_sync_link (
  content_hash UNIVARCHAR(32) DEFAULT '' NOT NULL,
  content_modified INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  object_id INT DEFAULT 0 NOT NULL,
  row_hash UNIVARCHAR(32) DEFAULT '' NOT NULL,
  row_key UNIVARCHAR(255) DEFAULT '' NOT NULL,
  row_modified INT DEFAULT 0 NOT NULL,
  sync_id INT DEFAULT 0 NOT NULL,
  synced INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE exp_data_sync_link ADD CONSTRAINT PK_exp_data_sync_link PRIMARY KEY (id)
go
CREATE UNIQUE INDEX exp_data_sync_link_key ON exp_data_sync_link (sync_id, row_key)
go
CREATE INDEX exp_data_sync_link_object ON exp_data_sync_link (sync_id, object_id)
go

CREATE TABLE expuser_session (
  browser UNIVARCHAR(64) DEFAULT '' NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  device_type UNIVARCHAR(16) DEFAULT '' NOT NULL,
  end_reason UNIVARCHAR(32) DEFAULT '' NOT NULL,
  ended INT DEFAULT 0 NOT NULL,
  ended_by INT DEFAULT 0 NOT NULL,
  first_ip UNIVARCHAR(64) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  last_active INT DEFAULT 0 NOT NULL,
  last_ip UNIVARCHAR(64) DEFAULT '' NOT NULL,
  method UNIVARCHAR(32) DEFAULT '' NOT NULL,
  os UNIVARCHAR(64) DEFAULT '' NOT NULL,
  remember_until INT DEFAULT 0 NOT NULL,
  siteaccess UNIVARCHAR(64) DEFAULT '' NOT NULL,
  token_hash UNIVARCHAR(64) DEFAULT '' NOT NULL,
  user_agent UNIVARCHAR(512) DEFAULT '' NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE expuser_session ADD CONSTRAINT PK_expuser_session PRIMARY KEY (id)
go
CREATE UNIQUE INDEX expuser_session_token ON expuser_session (token_hash)
go
CREATE INDEX expuser_session_user ON expuser_session (user_id, ended)
go
CREATE INDEX expuser_session_active ON expuser_session (ended, last_active)
go

CREATE TABLE expuser_session_info (
  auth_detail UNIVARCHAR(32) DEFAULT '' NOT NULL,
  country UNIVARCHAR(2) DEFAULT '' NOT NULL,
  host UNIVARCHAR(255) DEFAULT '' NOT NULL,
  location UNIVARCHAR(128) DEFAULT '' NOT NULL,
  session_id INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE expuser_session_info ADD CONSTRAINT PK_expuser_session_info PRIMARY KEY (session_id)
go
CREATE INDEX expuser_session_info_user ON expuser_session_info (user_id)
go

CREATE TABLE expuser_audit (
  actor_id INT DEFAULT 0 NOT NULL,
  actor_login UNIVARCHAR(150) DEFAULT '' NOT NULL,
  client_ip UNIVARCHAR(64) DEFAULT '' NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  event_id UNIVARCHAR(26) DEFAULT '' NOT NULL,
  event_name UNIVARCHAR(128) DEFAULT '' NOT NULL,
  host UNIVARCHAR(255) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  module_view UNIVARCHAR(128) DEFAULT '' NOT NULL,
  reason UNIVARCHAR(64) DEFAULT '' NOT NULL,
  request_id UNIVARCHAR(40) DEFAULT '' NOT NULL,
  result UNIVARCHAR(16) DEFAULT '' NOT NULL,
  siteaccess UNIVARCHAR(64) DEFAULT '' NOT NULL,
  target_id INT DEFAULT 0 NOT NULL,
  target_login UNIVARCHAR(150) DEFAULT '' NOT NULL,
  target_type UNIVARCHAR(16) DEFAULT '' NOT NULL,
  time_ms BIGINT DEFAULT 0 NOT NULL,
  value_after UNITEXT NULL,
  value_before UNITEXT NULL
)
go
ALTER TABLE expuser_audit ADD CONSTRAINT PK_expuser_audit PRIMARY KEY (id)
go
CREATE UNIQUE INDEX expuser_audit_event ON expuser_audit (event_id)
go
CREATE INDEX expuser_audit_target ON expuser_audit (target_id, created)
go
CREATE INDEX expuser_audit_actor ON expuser_audit (actor_id, created)
go
CREATE INDEX expuser_audit_name ON expuser_audit (event_name, created)
go
CREATE INDEX expuser_audit_created ON expuser_audit (created)
go

CREATE TABLE exphelp_help_id (
  help_id UNIVARCHAR(80) DEFAULT '' NOT NULL,
  object_id INT DEFAULT 0 NOT NULL,
  released INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE exphelp_help_id ADD CONSTRAINT PK_exphelp_help_id PRIMARY KEY (help_id)
go
CREATE INDEX exphelp_help_id_object ON exphelp_help_id (object_id)
go

CREATE TABLE exphelp_feedback (
  created INT DEFAULT 0 NOT NULL,
  help_id UNIVARCHAR(80) DEFAULT '' NOT NULL,
  helpful INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  language UNIVARCHAR(20) DEFAULT '' NOT NULL,
  object_id INT DEFAULT 0 NOT NULL,
  reason UNIVARCHAR(32) DEFAULT '' NOT NULL
)
go
ALTER TABLE exphelp_feedback ADD CONSTRAINT PK_exphelp_feedback PRIMARY KEY (id)
go
CREATE INDEX exphelp_feedback_object ON exphelp_feedback (object_id, created)
go
CREATE INDEX exphelp_feedback_created ON exphelp_feedback (created)
go

CREATE TABLE exp_migration (
  caption_id UNIVARCHAR(150) DEFAULT '' NOT NULL,
  checksum UNIVARCHAR(64) DEFAULT '' NOT NULL,
  duration_ms INT DEFAULT 0 NOT NULL,
  engine UNIVARCHAR(30) DEFAULT '' NOT NULL,
  environment UNIVARCHAR(50) DEFAULT '' NOT NULL,
  file_path UNIVARCHAR(255) DEFAULT '' NOT NULL,
  finished_at INT DEFAULT 0 NOT NULL,
  message UNIVARCHAR(1000) DEFAULT '' NOT NULL,
  migration_id UNIVARCHAR(150) DEFAULT '' NOT NULL,
  revision UNIVARCHAR(64) DEFAULT '' NOT NULL,
  run_by UNIVARCHAR(150) DEFAULT '' NOT NULL,
  run_id UNIVARCHAR(64) DEFAULT '' NOT NULL,
  source UNIVARCHAR(100) DEFAULT '' NOT NULL,
  stage UNIVARCHAR(20) DEFAULT '' NOT NULL,
  started_at INT DEFAULT 0 NOT NULL,
  state UNIVARCHAR(20) DEFAULT '' NOT NULL,
  version UNIVARCHAR(30) DEFAULT '' NOT NULL
)
go
ALTER TABLE exp_migration ADD CONSTRAINT PK_exp_migration PRIMARY KEY (source, migration_id)
go
CREATE INDEX exp_migration_run_id ON exp_migration (run_id)
go

CREATE TABLE exp_migration_deploy (
  deploy_id UNIVARCHAR(64) DEFAULT '' NOT NULL,
  finished_at INT DEFAULT 0 NOT NULL,
  in_sync INT DEFAULT 0 NOT NULL,
  message UNIVARCHAR(1000) DEFAULT '' NOT NULL,
  mode UNIVARCHAR(20) DEFAULT '' NOT NULL,
  next_stage INT DEFAULT 0 NOT NULL,
  run_by UNIVARCHAR(150) DEFAULT '' NOT NULL,
  stages INT DEFAULT 0 NOT NULL,
  started_at INT DEFAULT 0 NOT NULL,
  state UNIVARCHAR(20) DEFAULT '' NOT NULL
)
go
ALTER TABLE exp_migration_deploy ADD CONSTRAINT PK_exp_migration_deploy PRIMARY KEY (deploy_id)
go
CREATE INDEX exp_migration_deploy_started ON exp_migration_deploy (started_at)
go

CREATE TABLE exp_migration_deploy_host (
  address UNIVARCHAR(255) DEFAULT '' NOT NULL,
  applied INT DEFAULT 0 NOT NULL,
  caption UNIVARCHAR(150) DEFAULT '' NOT NULL,
  deploy_id UNIVARCHAR(64) DEFAULT '' NOT NULL,
  environment UNIVARCHAR(50) DEFAULT '' NOT NULL,
  message UNIVARCHAR(1000) DEFAULT '' NOT NULL,
  remote_id UNIVARCHAR(64) DEFAULT '' NOT NULL,
  stage INT DEFAULT 0 NOT NULL,
  state UNIVARCHAR(20) DEFAULT '' NOT NULL,
  transport UNIVARCHAR(20) DEFAULT '' NOT NULL
)
go
ALTER TABLE exp_migration_deploy_host ADD CONSTRAINT PK_exp_migration_deploy_host PRIMARY KEY (deploy_id, remote_id)
go
CREATE INDEX exp_migration_deploy_host_remote ON exp_migration_deploy_host (remote_id)
go

CREATE TABLE exp_migration_run (
  caption_post UNIVARCHAR(150) DEFAULT '' NOT NULL,
  caption_pre UNIVARCHAR(150) DEFAULT '' NOT NULL,
  commit_id UNIVARCHAR(64) DEFAULT '' NOT NULL,
  environment UNIVARCHAR(50) DEFAULT '' NOT NULL,
  finished_at INT DEFAULT 0 NOT NULL,
  log_file UNIVARCHAR(255) DEFAULT '' NOT NULL,
  message UNIVARCHAR(1000) DEFAULT '' NOT NULL,
  push_state UNIVARCHAR(20) DEFAULT '' NOT NULL,
  run_by UNIVARCHAR(150) DEFAULT '' NOT NULL,
  run_id UNIVARCHAR(64) DEFAULT '' NOT NULL,
  started_at INT DEFAULT 0 NOT NULL,
  state UNIVARCHAR(20) DEFAULT '' NOT NULL,
  tag_name UNIVARCHAR(150) DEFAULT '' NOT NULL
)
go
ALTER TABLE exp_migration_run ADD CONSTRAINT PK_exp_migration_run PRIMARY KEY (run_id)
go
CREATE INDEX exp_migration_run_started ON exp_migration_run (started_at)
go

CREATE TABLE exp_static_page (
  built_at INT DEFAULT 0 NOT NULL,
  hash UNIVARCHAR(64) DEFAULT '' NOT NULL,
  node_id INT DEFAULT 0 NOT NULL,
  page_bytes INT DEFAULT 0 NOT NULL,
  path UNIVARCHAR(255) DEFAULT '' NOT NULL,
  stale INT DEFAULT 0 NOT NULL,
  target UNIVARCHAR(50) DEFAULT '' NOT NULL
)
go
ALTER TABLE exp_static_page ADD CONSTRAINT PK_exp_static_page PRIMARY KEY (target, path)
go
CREATE INDEX exp_static_page_node ON exp_static_page (node_id)
go

CREATE TABLE exp_static_run (
  added INT DEFAULT 0 NOT NULL,
  bytes INT DEFAULT 0 NOT NULL,
  changed INT DEFAULT 0 NOT NULL,
  commit_id UNIVARCHAR(64) DEFAULT '' NOT NULL,
  files INT DEFAULT 0 NOT NULL,
  finished_at INT DEFAULT 0 NOT NULL,
  kind UNIVARCHAR(20) DEFAULT '' NOT NULL,
  message UNIVARCHAR(1000) DEFAULT '' NOT NULL,
  pages INT DEFAULT 0 NOT NULL,
  push_state UNIVARCHAR(20) DEFAULT '' NOT NULL,
  removed INT DEFAULT 0 NOT NULL,
  run_by UNIVARCHAR(150) DEFAULT '' NOT NULL,
  run_id UNIVARCHAR(64) DEFAULT '' NOT NULL,
  started_at INT DEFAULT 0 NOT NULL,
  state UNIVARCHAR(20) DEFAULT '' NOT NULL,
  tag_name UNIVARCHAR(150) DEFAULT '' NOT NULL,
  target UNIVARCHAR(50) DEFAULT '' NOT NULL
)
go
ALTER TABLE exp_static_run ADD CONSTRAINT PK_exp_static_run PRIMARY KEY (run_id)
go
CREATE INDEX exp_static_run_target ON exp_static_run (target, started_at)
go

CREATE TABLE exp_migration_recording (
  environment UNIVARCHAR(50) DEFAULT '' NOT NULL,
  event_count INT DEFAULT 0 NOT NULL,
  kind UNIVARCHAR(20) DEFAULT '' NOT NULL,
  label UNIVARCHAR(255) DEFAULT '' NOT NULL,
  message UNIVARCHAR(1000) DEFAULT '' NOT NULL,
  recording_id UNIVARCHAR(64) DEFAULT '' NOT NULL,
  started_at INT DEFAULT 0 NOT NULL,
  state UNIVARCHAR(20) DEFAULT '' NOT NULL,
  stopped_at INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE exp_migration_recording ADD CONSTRAINT PK_exp_migration_recording PRIMARY KEY (recording_id)
go
CREATE INDEX exp_migration_recording_state ON exp_migration_recording (state)
go

CREATE TABLE exp_migration_recording_event (
  actor_id INT DEFAULT 0 NOT NULL,
  audit_name UNIVARCHAR(100) DEFAULT '' NOT NULL,
  event_id UNIVARCHAR(32) DEFAULT '' NOT NULL,
  payload UNITEXT NULL,
  recorded_at INT DEFAULT 0 NOT NULL,
  recording_id UNIVARCHAR(64) DEFAULT '' NOT NULL,
  step_mode UNIVARCHAR(20) DEFAULT '' NOT NULL,
  step_type UNIVARCHAR(50) DEFAULT '' NOT NULL,
  subject UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE exp_migration_recording_event ADD CONSTRAINT PK_exp_migration_recording_event PRIMARY KEY (recording_id, event_id)
go

CREATE TABLE ezproductcategory (
  id INT IDENTITY NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezproductcategory ADD CONSTRAINT PK_ezproductcategory PRIMARY KEY (id)
go

CREATE TABLE ezproductcollection (
  created INT NULL,
  currency_code UNIVARCHAR(4) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL
)
go
ALTER TABLE ezproductcollection ADD CONSTRAINT PK_ezproductcollection PRIMARY KEY (id)
go

CREATE TABLE ezproductcollection_item (
  contentobject_id INT DEFAULT 0 NOT NULL,
  discount REAL NULL,
  id INT IDENTITY NOT NULL,
  is_vat_inc INT NULL,
  item_count INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  price REAL DEFAULT 0 NULL,
  productcollection_id INT DEFAULT 0 NOT NULL,
  vat_value REAL NULL
)
go
ALTER TABLE ezproductcollection_item ADD CONSTRAINT PK_ezproductcollection_item PRIMARY KEY (id)
go
CREATE INDEX ezproductcollection_item_contentobject_id ON ezproductcollection_item (contentobject_id)
go
CREATE INDEX ezproductcollection_item_productcollection_id ON ezproductcollection_item (productcollection_id)
go

CREATE TABLE ezproductcollection_item_opt (
  id INT IDENTITY NOT NULL,
  item_id INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  object_attribute_id INT NULL,
  option_item_id INT DEFAULT 0 NOT NULL,
  price REAL DEFAULT 0 NOT NULL,
  value UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezproductcollection_item_opt ADD CONSTRAINT PK_ezproductcollection_item_opt PRIMARY KEY (id)
go
CREATE INDEX ezproductcollection_item_opt_item_id ON ezproductcollection_item_opt (item_id)
go

CREATE TABLE ezpublishingqueueprocesses (
  created INT NULL,
  ezcontentobject_version_id INT DEFAULT 0 NOT NULL,
  finished INT NULL,
  pid INT NULL,
  started INT NULL,
  status INT NULL
)
go
ALTER TABLE ezpublishingqueueprocesses ADD CONSTRAINT PK_ezpublishingqueueprocesses PRIMARY KEY (ezcontentobject_version_id)
go

CREATE TABLE ezrole (
  id INT IDENTITY NOT NULL,
  is_new INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  value UNIVARCHAR(1) NULL,
  version INT DEFAULT 0 NULL
)
go
ALTER TABLE ezrole ADD CONSTRAINT PK_ezrole PRIMARY KEY (id)
go

CREATE TABLE ezrss_export (
  access_url UNIVARCHAR(255) NULL,
  opml_head UNITEXT NULL,
  podcast_head UNITEXT NULL,
  active INT NULL,
  created INT NULL,
  creator_id INT NULL,
  description UNITEXT NULL,
  id INT IDENTITY NOT NULL,
  image_id INT NULL,
  main_node_only INT DEFAULT 1 NOT NULL,
  modified INT NULL,
  modifier_id INT NULL,
  node_id INT NULL,
  number_of_objects INT DEFAULT 0 NOT NULL,
  rss_version UNIVARCHAR(255) NULL,
  site_access UNIVARCHAR(255) NULL,
  status INT DEFAULT 0 NOT NULL,
  title UNIVARCHAR(255) NULL,
  url UNIVARCHAR(255) NULL
)
go
ALTER TABLE ezrss_export ADD CONSTRAINT PK_ezrss_export PRIMARY KEY (id, status)
go

CREATE TABLE ezrss_export_item (
  category UNIVARCHAR(255) NULL,
  class_id INT NULL,
  description UNIVARCHAR(255) NULL,
  enclosure UNIVARCHAR(255) NULL,
  id INT IDENTITY NOT NULL,
  rssexport_id INT NULL,
  source_node_id INT NULL,
  status INT DEFAULT 0 NOT NULL,
  subnodes INT DEFAULT 0 NOT NULL,
  title UNIVARCHAR(255) NULL
)
go
ALTER TABLE ezrss_export_item ADD CONSTRAINT PK_ezrss_export_item PRIMARY KEY (id, status)
go
CREATE INDEX ezrss_export_rsseid ON ezrss_export_item (rssexport_id)
go

CREATE TABLE ezrss_export_opml_item (
  category UNIVARCHAR(255) NULL,
  created INT DEFAULT 0 NOT NULL,
  description UNIVARCHAR(255) NULL,
  html_url UNIVARCHAR(255) NULL,
  id INT IDENTITY NOT NULL,
  is_breakpoint INT DEFAULT 0 NOT NULL,
  is_comment INT DEFAULT 0 NOT NULL,
  language UNIVARCHAR(50) NULL,
  outline_text UNIVARCHAR(255) NULL,
  outline_type UNIVARCHAR(50) DEFAULT 'rss' NULL,
  parent_id INT DEFAULT 0 NOT NULL,
  priority INT DEFAULT 0 NOT NULL,
  rssexport_id INT DEFAULT 0 NOT NULL,
  source_node_id INT DEFAULT 0 NOT NULL,
  status INT DEFAULT 0 NOT NULL,
  subnodes INT DEFAULT 0 NOT NULL,
  target_export_id INT DEFAULT 0 NOT NULL,
  title UNIVARCHAR(255) NULL,
  url UNIVARCHAR(255) NULL,
  xml_url UNIVARCHAR(255) NULL
)
go
ALTER TABLE ezrss_export_opml_item ADD CONSTRAINT PK_ezrss_export_opml_item PRIMARY KEY (id, status)
go
CREATE INDEX ezrss_export_opml_rsseid ON ezrss_export_opml_item (rssexport_id)
go

CREATE TABLE ezrss_import (
  active INT NULL,
  class_description UNIVARCHAR(255) NULL,
  class_id INT NULL,
  class_title UNIVARCHAR(255) NULL,
  class_url UNIVARCHAR(255) NULL,
  created INT NULL,
  creator_id INT NULL,
  destination_node_id INT NULL,
  id INT IDENTITY NOT NULL,
  import_description UNITEXT NOT NULL,
  modified INT NULL,
  modifier_id INT NULL,
  name UNIVARCHAR(255) NULL,
  object_owner_id INT NULL,
  status INT DEFAULT 0 NOT NULL,
  url UNITEXT NULL
)
go
ALTER TABLE ezrss_import ADD CONSTRAINT PK_ezrss_import PRIMARY KEY (id, status)
go

CREATE TABLE ezscheduled_script (
  command UNIVARCHAR(255) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  last_report_timestamp INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(50) DEFAULT '' NOT NULL,
  process_id INT DEFAULT 0 NOT NULL,
  progress INT DEFAULT 0 NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezscheduled_script ADD CONSTRAINT PK_ezscheduled_script PRIMARY KEY (id)
go
CREATE INDEX ezscheduled_script_timestamp ON ezscheduled_script (last_report_timestamp)
go

CREATE TABLE ezsearch_object_word_link (
  contentclass_attribute_id INT DEFAULT 0 NOT NULL,
  contentclass_id INT DEFAULT 0 NOT NULL,
  contentobject_id INT DEFAULT 0 NOT NULL,
  frequency REAL DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  identifier UNIVARCHAR(255) DEFAULT '' NOT NULL,
  integer_value INT DEFAULT 0 NOT NULL,
  next_word_id INT DEFAULT 0 NOT NULL,
  placement INT DEFAULT 0 NOT NULL,
  prev_word_id INT DEFAULT 0 NOT NULL,
  published INT DEFAULT 0 NOT NULL,
  section_id INT DEFAULT 0 NOT NULL,
  word_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezsearch_object_word_link ADD CONSTRAINT PK_ezsearch_object_word_link PRIMARY KEY (id)
go
CREATE INDEX ezsearch_object_word_link_frequency ON ezsearch_object_word_link (frequency)
go
CREATE INDEX ezsearch_object_word_link_identifier ON ezsearch_object_word_link (identifier)
go
CREATE INDEX ezsearch_object_word_link_integer_value ON ezsearch_object_word_link (integer_value)
go
CREATE INDEX ezsearch_object_word_link_object ON ezsearch_object_word_link (contentobject_id)
go
CREATE INDEX ezsearch_object_word_link_word ON ezsearch_object_word_link (word_id)
go

CREATE TABLE ezsearch_search_phrase (
  id INT IDENTITY NOT NULL,
  phrase UNIVARCHAR(250) NULL,
  phrase_count INT DEFAULT 0 NULL,
  result_count INT DEFAULT 0 NULL
)
go
ALTER TABLE ezsearch_search_phrase ADD CONSTRAINT PK_ezsearch_search_phrase PRIMARY KEY (id)
go
CREATE INDEX ezsearch_search_phrase_count ON ezsearch_search_phrase (phrase_count)
go
CREATE UNIQUE INDEX ezsearch_search_phrase_phrase ON ezsearch_search_phrase (phrase)
go

CREATE TABLE ezsearch_word (
  id INT IDENTITY NOT NULL,
  object_count INT DEFAULT 0 NOT NULL,
  word UNIVARCHAR(150) NULL
)
go
ALTER TABLE ezsearch_word ADD CONSTRAINT PK_ezsearch_word PRIMARY KEY (id)
go
CREATE INDEX ezsearch_word_obj_count ON ezsearch_word (object_count)
go
CREATE INDEX ezsearch_word_word_i ON ezsearch_word (word)
go

CREATE TABLE ezsection (
  id INT IDENTITY NOT NULL,
  identifier UNIVARCHAR(255) NULL,
  locale UNIVARCHAR(255) NULL,
  name UNIVARCHAR(255) NULL,
  navigation_part_identifier UNIVARCHAR(100) DEFAULT 'ezcontentnavigationpart' NULL
)
go
ALTER TABLE ezsection ADD CONSTRAINT PK_ezsection PRIMARY KEY (id)
go

CREATE TABLE ezsession (
  data UNITEXT NOT NULL,
  expiration_time INT DEFAULT 0 NOT NULL,
  session_key UNIVARCHAR(32) DEFAULT '' NOT NULL,
  user_hash UNIVARCHAR(32) DEFAULT '' NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezsession ADD CONSTRAINT PK_ezsession PRIMARY KEY (session_key)
go
CREATE INDEX expiration_time ON ezsession (expiration_time)
go
CREATE INDEX ezsession_user_id ON ezsession (user_id)
go

CREATE TABLE ezsite_data (
  name UNIVARCHAR(60) DEFAULT '' NOT NULL,
  value UNITEXT NOT NULL
)
go
ALTER TABLE ezsite_data ADD CONSTRAINT PK_ezsite_data PRIMARY KEY (name)
go

CREATE TABLE ezsubtree_notification_rule (
  id INT IDENTITY NOT NULL,
  node_id INT DEFAULT 0 NOT NULL,
  use_digest INT DEFAULT 0 NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezsubtree_notification_rule ADD CONSTRAINT PK_ezsubtree_notification_rule PRIMARY KEY (id)
go
CREATE INDEX ezsubtree_notification_rule_user_id ON ezsubtree_notification_rule (user_id)
go

CREATE TABLE eztipafriend_counter (
  [count] INT DEFAULT 0 NOT NULL,
  node_id INT DEFAULT 0 NOT NULL,
  requested INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE eztipafriend_counter ADD CONSTRAINT PK_eztipafriend_counter PRIMARY KEY (node_id, requested)
go

CREATE TABLE eztipafriend_request (
  created INT DEFAULT 0 NOT NULL,
  email_receiver UNIVARCHAR(100) DEFAULT '' NOT NULL
)
go
CREATE INDEX eztipafriend_request_created ON eztipafriend_request (created)
go
CREATE INDEX eztipafriend_request_email_rec ON eztipafriend_request (email_receiver)
go

CREATE TABLE eztrigger (
  connect_type UNIVARCHAR(1) DEFAULT '' NOT NULL,
  function_name UNIVARCHAR(200) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  module_name UNIVARCHAR(200) DEFAULT '' NOT NULL,
  name UNIVARCHAR(255) NULL,
  workflow_id INT NULL
)
go
ALTER TABLE eztrigger ADD CONSTRAINT PK_eztrigger PRIMARY KEY (id)
go
CREATE UNIQUE INDEX eztrigger_def_id ON eztrigger (module_name, function_name, connect_type)
go
CREATE INDEX eztrigger_fetch ON eztrigger (name, module_name, function_name)
go

CREATE TABLE ezurl (
  created INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  is_valid INT DEFAULT 1 NOT NULL,
  last_checked INT DEFAULT 0 NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  original_url_md5 UNIVARCHAR(32) DEFAULT '' NOT NULL,
  url UNIVARCHAR(1000) NULL
)
go
ALTER TABLE ezurl ADD CONSTRAINT PK_ezurl PRIMARY KEY (id)
go
CREATE INDEX ezurl_url ON ezurl (url)
go

CREATE TABLE ezurl_object_link (
  contentobject_attribute_id INT DEFAULT 0 NOT NULL,
  contentobject_attribute_version INT DEFAULT 0 NOT NULL,
  url_id INT DEFAULT 0 NOT NULL
)
go
CREATE INDEX ezurl_ol_coa_id ON ezurl_object_link (contentobject_attribute_id)
go
CREATE INDEX ezurl_ol_coa_version ON ezurl_object_link (contentobject_attribute_version)
go
CREATE INDEX ezurl_ol_url_id ON ezurl_object_link (url_id)
go

CREATE TABLE ezurlalias (
  destination_url UNIVARCHAR(1000) NOT NULL,
  forward_to_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  is_imported INT DEFAULT 0 NOT NULL,
  is_internal INT DEFAULT 1 NOT NULL,
  is_wildcard INT DEFAULT 0 NOT NULL,
  source_md5 UNIVARCHAR(32) NULL,
  source_url UNIVARCHAR(1000) NOT NULL
)
go
ALTER TABLE ezurlalias ADD CONSTRAINT PK_ezurlalias PRIMARY KEY (id)
go
CREATE INDEX ezurlalias_desturl ON ezurlalias (destination_url)
go
CREATE INDEX ezurlalias_forward_to_id ON ezurlalias (forward_to_id)
go
CREATE INDEX ezurlalias_imp_wcard_fwd ON ezurlalias (is_imported, is_wildcard, forward_to_id)
go
CREATE INDEX ezurlalias_source_md5 ON ezurlalias (source_md5)
go
CREATE INDEX ezurlalias_source_url ON ezurlalias (source_url)
go
CREATE INDEX ezurlalias_wcard_fwd ON ezurlalias (is_wildcard, forward_to_id)
go

CREATE TABLE ezurlalias_ml (
  action UNIVARCHAR(1000) NOT NULL,
  action_type UNIVARCHAR(32) DEFAULT '' NOT NULL,
  alias_redirects INT DEFAULT 1 NOT NULL,
  id INT DEFAULT 0 NOT NULL,
  is_alias INT DEFAULT 0 NOT NULL,
  is_original INT DEFAULT 0 NOT NULL,
  lang_mask BIGINT DEFAULT 0 NOT NULL,
  link INT DEFAULT 0 NOT NULL,
  parent INT DEFAULT 0 NOT NULL,
  text UNIVARCHAR(1000) NOT NULL,
  text_md5 UNIVARCHAR(32) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezurlalias_ml ADD CONSTRAINT PK_ezurlalias_ml PRIMARY KEY (parent, text_md5)
go
CREATE INDEX ezurlalias_ml_act_org ON ezurlalias_ml (action, is_original)
go
CREATE INDEX ezurlalias_ml_actt_org_al ON ezurlalias_ml (action_type, is_original, is_alias)
go
CREATE INDEX ezurlalias_ml_id ON ezurlalias_ml (id)
go
CREATE INDEX ezurlalias_ml_par_act_id_lnk ON ezurlalias_ml (action, id, link, parent)
go
CREATE INDEX ezurlalias_ml_par_lnk_txt ON ezurlalias_ml (parent, text, link)
go
CREATE INDEX ezurlalias_ml_text ON ezurlalias_ml (text, id, link)
go
CREATE INDEX ezurlalias_ml_text_lang ON ezurlalias_ml (text, lang_mask, parent)
go

CREATE TABLE ezurlalias_ml_incr (
  id INT IDENTITY NOT NULL
)
go
ALTER TABLE ezurlalias_ml_incr ADD CONSTRAINT PK_ezurlalias_ml_incr PRIMARY KEY (id)
go

CREATE TABLE ezurlwildcard (
  destination_url UNITEXT NOT NULL,
  id INT IDENTITY NOT NULL,
  source_url UNITEXT NOT NULL,
  type INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezurlwildcard ADD CONSTRAINT PK_ezurlwildcard PRIMARY KEY (id)
go

CREATE TABLE ezuser (
  contentobject_id INT DEFAULT 0 NOT NULL,
  email UNIVARCHAR(150) DEFAULT '' NOT NULL,
  login UNIVARCHAR(150) DEFAULT '' NOT NULL,
  password_hash UNIVARCHAR(255) NULL,
  password_hash_type INT DEFAULT 1 NOT NULL
)
go
ALTER TABLE ezuser ADD CONSTRAINT PK_ezuser PRIMARY KEY (contentobject_id)
go
CREATE UNIQUE INDEX ezuser_login ON ezuser (login)
go

CREATE TABLE ezuser_accountkey (
  hash_key UNIVARCHAR(32) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  time INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezuser_accountkey ADD CONSTRAINT PK_ezuser_accountkey PRIMARY KEY (id)
go
CREATE INDEX hash_key ON ezuser_accountkey (hash_key)
go

CREATE TABLE ezuser_discountrule (
  contentobject_id INT NULL,
  discountrule_id INT NULL,
  id INT IDENTITY NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezuser_discountrule ADD CONSTRAINT PK_ezuser_discountrule PRIMARY KEY (id)
go

CREATE TABLE ezuser_role (
  contentobject_id INT NULL,
  id INT IDENTITY NOT NULL,
  limit_identifier UNIVARCHAR(255) DEFAULT '' NULL,
  limit_value UNIVARCHAR(255) DEFAULT '' NULL,
  role_id INT NULL
)
go
ALTER TABLE ezuser_role ADD CONSTRAINT PK_ezuser_role PRIMARY KEY (id)
go
CREATE INDEX ezuser_role_contentobject_id ON ezuser_role (contentobject_id)
go
CREATE INDEX ezuser_role_role_id ON ezuser_role (role_id)
go

CREATE TABLE ezuser_setting (
  is_enabled INT DEFAULT 0 NOT NULL,
  max_login INT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezuser_setting ADD CONSTRAINT PK_ezuser_setting PRIMARY KEY (user_id)
go

CREATE TABLE ezuservisit (
  current_visit_timestamp INT DEFAULT 0 NOT NULL,
  failed_login_attempts INT DEFAULT 0 NOT NULL,
  last_visit_timestamp INT DEFAULT 0 NOT NULL,
  login_count INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezuservisit ADD CONSTRAINT PK_ezuservisit PRIMARY KEY (user_id)
go
CREATE INDEX ezuservisit_co_visit_count ON ezuservisit (current_visit_timestamp, login_count)
go

CREATE TABLE ezvatrule (
  country_code UNIVARCHAR(255) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  vat_type INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezvatrule ADD CONSTRAINT PK_ezvatrule PRIMARY KEY (id)
go

CREATE TABLE ezvatrule_product_category (
  product_category_id INT DEFAULT 0 NOT NULL,
  vatrule_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezvatrule_product_category ADD CONSTRAINT PK_ezvatrule_product_category PRIMARY KEY (vatrule_id, product_category_id)
go

CREATE TABLE ezvattype (
  id INT IDENTITY NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  percentage REAL NULL
)
go
ALTER TABLE ezvattype ADD CONSTRAINT PK_ezvattype PRIMARY KEY (id)
go

CREATE TABLE ezview_counter (
  [count] INT DEFAULT 0 NOT NULL,
  node_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezview_counter ADD CONSTRAINT PK_ezview_counter PRIMARY KEY (node_id)
go

CREATE TABLE ezwaituntildatevalue (
  contentclass_attribute_id INT DEFAULT 0 NOT NULL,
  contentclass_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  workflow_event_id INT DEFAULT 0 NOT NULL,
  workflow_event_version INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezwaituntildatevalue ADD CONSTRAINT PK_ezwaituntildatevalue PRIMARY KEY (id, workflow_event_id, workflow_event_version)
go
CREATE INDEX ezwaituntildateevalue_wf_ev_id_wf_ver ON ezwaituntildatevalue (workflow_event_id, workflow_event_version)
go

CREATE TABLE ezwishlist (
  id INT IDENTITY NOT NULL,
  productcollection_id INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezwishlist ADD CONSTRAINT PK_ezwishlist PRIMARY KEY (id)
go

CREATE TABLE ezworkflow (
  created INT DEFAULT 0 NOT NULL,
  creator_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  is_enabled INT DEFAULT 0 NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  modifier_id INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  version INT DEFAULT 0 NOT NULL,
  workflow_type_string UNIVARCHAR(50) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezworkflow ADD CONSTRAINT PK_ezworkflow PRIMARY KEY (id, version)
go

CREATE TABLE ezworkflow_assign (
  access_type INT DEFAULT 0 NOT NULL,
  as_tree INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  node_id INT DEFAULT 0 NOT NULL,
  workflow_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezworkflow_assign ADD CONSTRAINT PK_ezworkflow_assign PRIMARY KEY (id)
go

CREATE TABLE ezworkflow_event (
  data_int1 INT NULL,
  data_int2 INT NULL,
  data_int3 INT NULL,
  data_int4 INT NULL,
  data_text1 UNIVARCHAR(255) NULL,
  data_text2 UNIVARCHAR(255) NULL,
  data_text3 UNIVARCHAR(255) NULL,
  data_text4 UNIVARCHAR(255) NULL,
  data_text5 UNITEXT NULL,
  description UNIVARCHAR(50) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  placement INT DEFAULT 0 NOT NULL,
  version INT DEFAULT 0 NOT NULL,
  workflow_id INT DEFAULT 0 NOT NULL,
  workflow_type_string UNIVARCHAR(50) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezworkflow_event ADD CONSTRAINT PK_ezworkflow_event PRIMARY KEY (id, version)
go
CREATE INDEX wid_version_placement ON ezworkflow_event (workflow_id, version, placement)
go

CREATE TABLE ezworkflow_group (
  created INT DEFAULT 0 NOT NULL,
  creator_id INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  modifier_id INT DEFAULT 0 NOT NULL,
  name UNIVARCHAR(255) DEFAULT '' NOT NULL
)
go
ALTER TABLE ezworkflow_group ADD CONSTRAINT PK_ezworkflow_group PRIMARY KEY (id)
go

CREATE TABLE ezworkflow_group_link (
  group_id INT DEFAULT 0 NOT NULL,
  group_name UNIVARCHAR(255) NULL,
  workflow_id INT DEFAULT 0 NOT NULL,
  workflow_version INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezworkflow_group_link ADD CONSTRAINT PK_ezworkflow_group_link PRIMARY KEY (workflow_id, group_id, workflow_version)
go

CREATE TABLE ezworkflow_process (
  activation_date INT NULL,
  content_id INT DEFAULT 0 NOT NULL,
  content_version INT DEFAULT 0 NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  event_id INT DEFAULT 0 NOT NULL,
  event_position INT DEFAULT 0 NOT NULL,
  event_state INT NULL,
  event_status INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  last_event_id INT DEFAULT 0 NOT NULL,
  last_event_position INT DEFAULT 0 NOT NULL,
  last_event_status INT DEFAULT 0 NOT NULL,
  memento_key UNIVARCHAR(32) NULL,
  modified INT DEFAULT 0 NOT NULL,
  node_id INT DEFAULT 0 NOT NULL,
  parameters UNITEXT NULL,
  process_key UNIVARCHAR(32) DEFAULT '' NOT NULL,
  session_key UNIVARCHAR(32) DEFAULT '0' NOT NULL,
  status INT NULL,
  user_id INT DEFAULT 0 NOT NULL,
  workflow_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE ezworkflow_process ADD CONSTRAINT PK_ezworkflow_process PRIMARY KEY (id)
go
CREATE INDEX ezworkflow_process_process_key ON ezworkflow_process (process_key)
go

CREATE TABLE exp_url_redirect (
  batch_id INT DEFAULT 0 NOT NULL,
  created INT DEFAULT 0 NOT NULL,
  id INT IDENTITY NOT NULL,
  language UNIVARCHAR(20) DEFAULT '' NOT NULL,
  modified INT DEFAULT 0 NOT NULL,
  siteaccess UNIVARCHAR(100) DEFAULT '' NOT NULL,
  source_key UNIVARCHAR(32) DEFAULT '' NOT NULL,
  source_path UNIVARCHAR(1000) DEFAULT '' NOT NULL,
  source_query UNIVARCHAR(1000) DEFAULT '' NOT NULL,
  status INT DEFAULT 301 NOT NULL,
  target UNIVARCHAR(2000) DEFAULT '' NOT NULL
)
go
ALTER TABLE exp_url_redirect ADD CONSTRAINT PK_exp_url_redirect PRIMARY KEY (id)
go
CREATE INDEX exp_url_redirect_batch ON exp_url_redirect (batch_id)
go
CREATE INDEX exp_url_redirect_source ON exp_url_redirect (source_key)
go

CREATE TABLE exp_urlmap_batch (
  created INT DEFAULT 0 NOT NULL,
  created_count INT DEFAULT 0 NOT NULL,
  failed_count INT DEFAULT 0 NOT NULL,
  format UNIVARCHAR(20) DEFAULT '' NOT NULL,
  id INT IDENTITY NOT NULL,
  language UNIVARCHAR(20) DEFAULT '' NOT NULL,
  on_conflict UNIVARCHAR(20) DEFAULT '' NOT NULL,
  rows_total INT DEFAULT 0 NOT NULL,
  siteaccess UNIVARCHAR(100) DEFAULT '' NOT NULL,
  skipped_count INT DEFAULT 0 NOT NULL,
  source_name UNIVARCHAR(255) DEFAULT '' NOT NULL,
  state UNIVARCHAR(20) DEFAULT '' NOT NULL,
  summary UNITEXT NULL,
  undone INT DEFAULT 0 NOT NULL,
  undone_by INT DEFAULT 0 NOT NULL,
  updated_count INT DEFAULT 0 NOT NULL,
  user_id INT DEFAULT 0 NOT NULL
)
go
ALTER TABLE exp_urlmap_batch ADD CONSTRAINT PK_exp_urlmap_batch PRIMARY KEY (id)
go
CREATE INDEX exp_urlmap_batch_created ON exp_urlmap_batch (created)
go

CREATE TABLE exp_urlmap_batch_item (
  action UNIVARCHAR(20) DEFAULT '' NOT NULL,
  after_image UNITEXT NULL,
  batch_id INT DEFAULT 0 NOT NULL,
  before_image UNITEXT NULL,
  id INT IDENTITY NOT NULL,
  kind UNIVARCHAR(30) DEFAULT '' NOT NULL,
  line INT DEFAULT 0 NOT NULL,
  old_url UNIVARCHAR(1000) DEFAULT '' NOT NULL,
  seq INT DEFAULT 0 NOT NULL,
  state UNIVARCHAR(20) DEFAULT '' NOT NULL,
  target_key UNIVARCHAR(255) DEFAULT '' NOT NULL,
  target_table UNIVARCHAR(50) DEFAULT '' NOT NULL
)
go
ALTER TABLE exp_urlmap_batch_item ADD CONSTRAINT PK_exp_urlmap_batch_item PRIMARY KEY (id)
go
CREATE INDEX exp_urlmap_batch_item_batch ON exp_urlmap_batch_item (batch_id, seq)
go
