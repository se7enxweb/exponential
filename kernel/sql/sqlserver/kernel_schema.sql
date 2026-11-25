-- Exponential kernel schema for Microsoft SQL Server, written by expSQLServerSchema from share/db_schema.dba

CREATE TABLE expaudit_cursor (
  byte_offset BIGINT CONSTRAINT [DF_expaudit_cursor_byte_offset] DEFAULT 0 NOT NULL,
  channel NVARCHAR(32) CONSTRAINT [DF_expaudit_cursor_channel] DEFAULT N'' NOT NULL,
  file_name NVARCHAR(64) CONSTRAINT [DF_expaudit_cursor_file_name] DEFAULT N'' NOT NULL,
  last_hash NVARCHAR(80) CONSTRAINT [DF_expaudit_cursor_last_hash] DEFAULT NULL NULL,
  last_seq INT CONSTRAINT [DF_expaudit_cursor_last_seq] DEFAULT 0 NOT NULL,
  updated_ms BIGINT CONSTRAINT [DF_expaudit_cursor_updated_ms] DEFAULT 0 NOT NULL
);
ALTER TABLE expaudit_cursor ADD CONSTRAINT [PK_expaudit_cursor] PRIMARY KEY (channel, file_name);

CREATE TABLE expaudit_event (
  channel NVARCHAR(32) CONSTRAINT [DF_expaudit_event_channel] DEFAULT N'' NOT NULL,
  depth INT CONSTRAINT [DF_expaudit_event_depth] DEFAULT 0 NOT NULL,
  domain_name NVARCHAR(16) CONSTRAINT [DF_expaudit_event_domain_name] DEFAULT N'' NOT NULL,
  engine NVARCHAR(16) CONSTRAINT [DF_expaudit_event_engine] DEFAULT NULL NULL,
  file_name NVARCHAR(64) CONSTRAINT [DF_expaudit_event_file_name] DEFAULT N'' NOT NULL,
  id NCHAR(26) CONSTRAINT [DF_expaudit_event_id] DEFAULT N'' NOT NULL,
  imported INT CONSTRAINT [DF_expaudit_event_imported] DEFAULT 0 NOT NULL,
  ip NVARCHAR(64) CONSTRAINT [DF_expaudit_event_ip] DEFAULT NULL NULL,
  job_id NVARCHAR(32) CONSTRAINT [DF_expaudit_event_job_id] DEFAULT NULL NULL,
  login NVARCHAR(150) CONSTRAINT [DF_expaudit_event_login] DEFAULT NULL NULL,
  module_view NVARCHAR(128) CONSTRAINT [DF_expaudit_event_module_view] DEFAULT NULL NULL,
  name NVARCHAR(128) CONSTRAINT [DF_expaudit_event_name] DEFAULT N'' NOT NULL,
  object_id NVARCHAR(64) CONSTRAINT [DF_expaudit_event_object_id] DEFAULT NULL NULL,
  object_name NVARCHAR(255) CONSTRAINT [DF_expaudit_event_object_name] DEFAULT NULL NULL,
  object_type NVARCHAR(32) CONSTRAINT [DF_expaudit_event_object_type] DEFAULT NULL NULL,
  parent_id NCHAR(26) CONSTRAINT [DF_expaudit_event_parent_id] DEFAULT NULL NULL,
  pseudonymised INT CONSTRAINT [DF_expaudit_event_pseudonymised] DEFAULT 0 NOT NULL,
  reason NVARCHAR(32) CONSTRAINT [DF_expaudit_event_reason] DEFAULT NULL NULL,
  record NVARCHAR(MAX) CONSTRAINT [DF_expaudit_event_record] DEFAULT NULL NULL,
  request_id NVARCHAR(40) CONSTRAINT [DF_expaudit_event_request_id] DEFAULT NULL NULL,
  result NVARCHAR(8) CONSTRAINT [DF_expaudit_event_result] DEFAULT NULL NULL,
  run_id NVARCHAR(40) CONSTRAINT [DF_expaudit_event_run_id] DEFAULT NULL NULL,
  search_text NVARCHAR(MAX) CONSTRAINT [DF_expaudit_event_search_text] DEFAULT NULL NULL,
  seq INT CONSTRAINT [DF_expaudit_event_seq] DEFAULT 0 NOT NULL,
  session_h NVARCHAR(24) CONSTRAINT [DF_expaudit_event_session_h] DEFAULT NULL NULL,
  severity INT CONSTRAINT [DF_expaudit_event_severity] DEFAULT 0 NOT NULL,
  siteaccess NVARCHAR(64) CONSTRAINT [DF_expaudit_event_siteaccess] DEFAULT NULL NULL,
  target_id NVARCHAR(64) CONSTRAINT [DF_expaudit_event_target_id] DEFAULT NULL NULL,
  target_type NVARCHAR(32) CONSTRAINT [DF_expaudit_event_target_type] DEFAULT NULL NULL,
  time_ms BIGINT CONSTRAINT [DF_expaudit_event_time_ms] DEFAULT 0 NOT NULL,
  ua NVARCHAR(128) CONSTRAINT [DF_expaudit_event_ua] DEFAULT NULL NULL,
  user_id INT CONSTRAINT [DF_expaudit_event_user_id] DEFAULT NULL NULL,
  verb NVARCHAR(32) CONSTRAINT [DF_expaudit_event_verb] DEFAULT NULL NULL
);
ALTER TABLE expaudit_event ADD CONSTRAINT [PK_expaudit_event] PRIMARY KEY (id);
CREATE INDEX [expaudit_event_domain] ON expaudit_event (domain_name, severity, time_ms);
CREATE UNIQUE INDEX [expaudit_event_file_seq] ON expaudit_event (channel, file_name, seq);
CREATE INDEX [expaudit_event_ip] ON expaudit_event (ip, time_ms);
CREATE INDEX [expaudit_event_job] ON expaudit_event (job_id);
CREATE INDEX [expaudit_event_name] ON expaudit_event (name, time_ms);
CREATE INDEX [expaudit_event_object] ON expaudit_event (object_type, object_id, time_ms);
CREATE INDEX [expaudit_event_parent] ON expaudit_event (parent_id);
CREATE INDEX [expaudit_event_request] ON expaudit_event (request_id);
CREATE INDEX [expaudit_event_result] ON expaudit_event (result, time_ms);
CREATE INDEX [expaudit_event_time] ON expaudit_event (time_ms);
CREATE INDEX [expaudit_event_user] ON expaudit_event (user_id, time_ms);

CREATE TABLE expaudit_file (
  archive_path NVARCHAR(255) CONSTRAINT [DF_expaudit_file_archive_path] DEFAULT NULL NULL,
  break_line INT CONSTRAINT [DF_expaudit_file_break_line] DEFAULT 0 NOT NULL,
  channel NVARCHAR(32) CONSTRAINT [DF_expaudit_file_channel] DEFAULT N'' NOT NULL,
  file_name NVARCHAR(64) CONSTRAINT [DF_expaudit_file_file_name] DEFAULT N'' NOT NULL,
  records INT CONSTRAINT [DF_expaudit_file_records] DEFAULT 0 NOT NULL,
  state NVARCHAR(16) CONSTRAINT [DF_expaudit_file_state] DEFAULT N'live' NOT NULL,
  verified NVARCHAR(16) CONSTRAINT [DF_expaudit_file_verified] DEFAULT N'unchecked' NOT NULL,
  verified_ms BIGINT CONSTRAINT [DF_expaudit_file_verified_ms] DEFAULT 0 NOT NULL
);
ALTER TABLE expaudit_file ADD CONSTRAINT [PK_expaudit_file] PRIMARY KEY (channel, file_name);

CREATE TABLE expmail_category (
  created INT CONSTRAINT [DF_expmail_category_created] DEFAULT 0 NOT NULL,
  default_on INT CONSTRAINT [DF_expmail_category_default_on] DEFAULT 0 NOT NULL,
  description NVARCHAR(MAX) CONSTRAINT [DF_expmail_category_description] DEFAULT NULL NULL,
  double_opt_in INT CONSTRAINT [DF_expmail_category_double_opt_in] DEFAULT 0 NOT NULL,
  essential INT CONSTRAINT [DF_expmail_category_essential] DEFAULT 0 NOT NULL,
  frequencies NVARCHAR(100) CONSTRAINT [DF_expmail_category_frequencies] DEFAULT N'' NOT NULL,
  handler_class NVARCHAR(255) CONSTRAINT [DF_expmail_category_handler_class] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  identifier NVARCHAR(100) CONSTRAINT [DF_expmail_category_identifier] DEFAULT N'' NOT NULL,
  modified INT CONSTRAINT [DF_expmail_category_modified] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_expmail_category_name] DEFAULT N'' NOT NULL,
  priority INT CONSTRAINT [DF_expmail_category_priority] DEFAULT 0 NOT NULL
);
ALTER TABLE expmail_category ADD CONSTRAINT [PK_expmail_category] PRIMARY KEY (id);
CREATE UNIQUE INDEX [expmail_category_identifier] ON expmail_category (identifier);

CREATE TABLE expmail_consent_log (
  action NVARCHAR(30) CONSTRAINT [DF_expmail_consent_log_action] DEFAULT N'' NOT NULL,
  actor_user_id INT CONSTRAINT [DF_expmail_consent_log_actor_user_id] DEFAULT 0 NOT NULL,
  anonymised INT CONSTRAINT [DF_expmail_consent_log_anonymised] DEFAULT 0 NOT NULL,
  category NVARCHAR(100) CONSTRAINT [DF_expmail_consent_log_category] DEFAULT N'' NOT NULL,
  created INT CONSTRAINT [DF_expmail_consent_log_created] DEFAULT 0 NOT NULL,
  email NVARCHAR(255) CONSTRAINT [DF_expmail_consent_log_email] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  ip NVARCHAR(64) CONSTRAINT [DF_expmail_consent_log_ip] DEFAULT N'' NOT NULL,
  new_value NVARCHAR(100) CONSTRAINT [DF_expmail_consent_log_new_value] DEFAULT N'' NOT NULL,
  old_value NVARCHAR(100) CONSTRAINT [DF_expmail_consent_log_old_value] DEFAULT N'' NOT NULL,
  recipient_key NVARCHAR(80) CONSTRAINT [DF_expmail_consent_log_recipient_key] DEFAULT N'' NOT NULL,
  siteaccess NVARCHAR(100) CONSTRAINT [DF_expmail_consent_log_siteaccess] DEFAULT N'' NOT NULL,
  source NVARCHAR(20) CONSTRAINT [DF_expmail_consent_log_source] DEFAULT N'' NOT NULL,
  user_id INT CONSTRAINT [DF_expmail_consent_log_user_id] DEFAULT 0 NOT NULL,
  wording NVARCHAR(MAX) CONSTRAINT [DF_expmail_consent_log_wording] DEFAULT NULL NULL
);
ALTER TABLE expmail_consent_log ADD CONSTRAINT [PK_expmail_consent_log] PRIMARY KEY (id);
CREATE INDEX [expmail_consent_log_created] ON expmail_consent_log (created);
CREATE INDEX [expmail_consent_log_recipient] ON expmail_consent_log (recipient_key, created);
CREATE INDEX [expmail_consent_log_user] ON expmail_consent_log (user_id);

CREATE TABLE expmail_pending (
  category NVARCHAR(100) CONSTRAINT [DF_expmail_pending_category] DEFAULT N'' NOT NULL,
  created INT CONSTRAINT [DF_expmail_pending_created] DEFAULT 0 NOT NULL,
  data NVARCHAR(MAX) CONSTRAINT [DF_expmail_pending_data] DEFAULT NULL NULL,
  expires INT CONSTRAINT [DF_expmail_pending_expires] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  kind NVARCHAR(20) CONSTRAINT [DF_expmail_pending_kind] DEFAULT N'' NOT NULL,
  recipient_key NVARCHAR(80) CONSTRAINT [DF_expmail_pending_recipient_key] DEFAULT N'' NOT NULL,
  user_id INT CONSTRAINT [DF_expmail_pending_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE expmail_pending ADD CONSTRAINT [PK_expmail_pending] PRIMARY KEY (id);
CREATE INDEX [expmail_pending_expires] ON expmail_pending (expires);
CREATE INDEX [expmail_pending_recipient] ON expmail_pending (recipient_key, kind);

CREATE TABLE expmail_preference (
  category NVARCHAR(100) CONSTRAINT [DF_expmail_preference_category] DEFAULT N'' NOT NULL,
  created INT CONSTRAINT [DF_expmail_preference_created] DEFAULT 0 NOT NULL,
  frequency NVARCHAR(20) CONSTRAINT [DF_expmail_preference_frequency] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  modified INT CONSTRAINT [DF_expmail_preference_modified] DEFAULT 0 NOT NULL,
  recipient_key NVARCHAR(80) CONSTRAINT [DF_expmail_preference_recipient_key] DEFAULT N'' NOT NULL,
  state NVARCHAR(20) CONSTRAINT [DF_expmail_preference_state] DEFAULT N'' NOT NULL,
  user_id INT CONSTRAINT [DF_expmail_preference_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE expmail_preference ADD CONSTRAINT [PK_expmail_preference] PRIMARY KEY (id);
CREATE INDEX [expmail_preference_category] ON expmail_preference (category, state);
CREATE UNIQUE INDEX [expmail_preference_recipient] ON expmail_preference (recipient_key, category);
CREATE INDEX [expmail_preference_user] ON expmail_preference (user_id);

CREATE TABLE expmail_suppression (
  created INT CONSTRAINT [DF_expmail_suppression_created] DEFAULT 0 NOT NULL,
  created_by INT CONSTRAINT [DF_expmail_suppression_created_by] DEFAULT 0 NOT NULL,
  email_hash NVARCHAR(64) CONSTRAINT [DF_expmail_suppression_email_hash] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  note NVARCHAR(MAX) CONSTRAINT [DF_expmail_suppression_note] DEFAULT NULL NULL,
  reason NVARCHAR(30) CONSTRAINT [DF_expmail_suppression_reason] DEFAULT N'' NOT NULL
);
ALTER TABLE expmail_suppression ADD CONSTRAINT [PK_expmail_suppression] PRIMARY KEY (id);
CREATE UNIQUE INDEX [expmail_suppression_hash] ON expmail_suppression (email_hash);

CREATE TABLE ezapprove_items (
  collaboration_id INT CONSTRAINT [DF_ezapprove_items_collaboration_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  workflow_process_id INT CONSTRAINT [DF_ezapprove_items_workflow_process_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezapprove_items ADD CONSTRAINT [PK_ezapprove_items] PRIMARY KEY (id);

CREATE TABLE ezbasket (
  id INT IDENTITY(1,1) NOT NULL,
  order_id INT CONSTRAINT [DF_ezbasket_order_id] DEFAULT 0 NOT NULL,
  productcollection_id INT CONSTRAINT [DF_ezbasket_productcollection_id] DEFAULT 0 NOT NULL,
  session_id NVARCHAR(255) CONSTRAINT [DF_ezbasket_session_id] DEFAULT N'' NOT NULL
);
ALTER TABLE ezbasket ADD CONSTRAINT [PK_ezbasket] PRIMARY KEY (id);
CREATE INDEX [ezbasket_session_id] ON ezbasket (session_id);

CREATE TABLE ezbinaryfile (
  contentobject_attribute_id INT CONSTRAINT [DF_ezbinaryfile_contentobject_attribute_id] DEFAULT 0 NOT NULL,
  download_count INT CONSTRAINT [DF_ezbinaryfile_download_count] DEFAULT 0 NOT NULL,
  filename NVARCHAR(255) CONSTRAINT [DF_ezbinaryfile_filename] DEFAULT N'' NOT NULL,
  mime_type NVARCHAR(255) CONSTRAINT [DF_ezbinaryfile_mime_type] DEFAULT N'' NOT NULL,
  original_filename NVARCHAR(255) CONSTRAINT [DF_ezbinaryfile_original_filename] DEFAULT N'' NOT NULL,
  version INT CONSTRAINT [DF_ezbinaryfile_version] DEFAULT 0 NOT NULL
);
ALTER TABLE ezbinaryfile ADD CONSTRAINT [PK_ezbinaryfile] PRIMARY KEY (contentobject_attribute_id, version);

CREATE TABLE ezcobj_state (
  default_language_id BIGINT CONSTRAINT [DF_ezcobj_state_default_language_id] DEFAULT 0 NOT NULL,
  group_id INT CONSTRAINT [DF_ezcobj_state_group_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  identifier NVARCHAR(45) CONSTRAINT [DF_ezcobj_state_identifier] DEFAULT N'' NOT NULL,
  language_mask BIGINT CONSTRAINT [DF_ezcobj_state_language_mask] DEFAULT 0 NOT NULL,
  priority INT CONSTRAINT [DF_ezcobj_state_priority] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state ADD CONSTRAINT [PK_ezcobj_state] PRIMARY KEY (id);
CREATE UNIQUE INDEX [ezcobj_state_identifier] ON ezcobj_state (group_id, identifier);
CREATE INDEX [ezcobj_state_lmask] ON ezcobj_state (language_mask);
CREATE INDEX [ezcobj_state_priority] ON ezcobj_state (priority);

CREATE TABLE ezcobj_state_group (
  default_language_id BIGINT CONSTRAINT [DF_ezcobj_state_group_default_language_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  identifier NVARCHAR(45) CONSTRAINT [DF_ezcobj_state_group_identifier] DEFAULT N'' NOT NULL,
  language_mask BIGINT CONSTRAINT [DF_ezcobj_state_group_language_mask] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state_group ADD CONSTRAINT [PK_ezcobj_state_group] PRIMARY KEY (id);
CREATE UNIQUE INDEX [ezcobj_state_group_identifier] ON ezcobj_state_group (identifier);
CREATE INDEX [ezcobj_state_group_lmask] ON ezcobj_state_group (language_mask);

CREATE TABLE ezcobj_state_group_language (
  contentobject_state_group_id INT CONSTRAINT [DF_ezcobj_state_group_language_contentobject_state_group_id] DEFAULT 0 NOT NULL,
  description NVARCHAR(MAX) NOT NULL,
  language_id BIGINT CONSTRAINT [DF_ezcobj_state_group_language_language_id] DEFAULT 0 NOT NULL,
  name NVARCHAR(45) CONSTRAINT [DF_ezcobj_state_group_language_name] DEFAULT N'' NOT NULL,
  real_language_id BIGINT CONSTRAINT [DF_ezcobj_state_group_language_real_language_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state_group_language ADD CONSTRAINT [PK_ezcobj_state_group_language] PRIMARY KEY (contentobject_state_group_id, real_language_id);

CREATE TABLE ezcobj_state_language (
  contentobject_state_id INT CONSTRAINT [DF_ezcobj_state_language_contentobject_state_id] DEFAULT 0 NOT NULL,
  description NVARCHAR(MAX) NOT NULL,
  language_id BIGINT CONSTRAINT [DF_ezcobj_state_language_language_id] DEFAULT 0 NOT NULL,
  name NVARCHAR(45) CONSTRAINT [DF_ezcobj_state_language_name] DEFAULT N'' NOT NULL
);
ALTER TABLE ezcobj_state_language ADD CONSTRAINT [PK_ezcobj_state_language] PRIMARY KEY (contentobject_state_id, language_id);

CREATE TABLE ezcobj_state_link (
  contentobject_id INT CONSTRAINT [DF_ezcobj_state_link_contentobject_id] DEFAULT 0 NOT NULL,
  contentobject_state_id INT CONSTRAINT [DF_ezcobj_state_link_contentobject_state_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcobj_state_link ADD CONSTRAINT [PK_ezcobj_state_link] PRIMARY KEY (contentobject_id, contentobject_state_id);

CREATE TABLE ezcollab_group (
  created INT CONSTRAINT [DF_ezcollab_group_created] DEFAULT 0 NOT NULL,
  depth INT CONSTRAINT [DF_ezcollab_group_depth] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  is_open INT CONSTRAINT [DF_ezcollab_group_is_open] DEFAULT 1 NOT NULL,
  modified INT CONSTRAINT [DF_ezcollab_group_modified] DEFAULT 0 NOT NULL,
  parent_group_id INT CONSTRAINT [DF_ezcollab_group_parent_group_id] DEFAULT 0 NOT NULL,
  path_string NVARCHAR(255) CONSTRAINT [DF_ezcollab_group_path_string] DEFAULT N'' NOT NULL,
  priority INT CONSTRAINT [DF_ezcollab_group_priority] DEFAULT 0 NOT NULL,
  title NVARCHAR(255) CONSTRAINT [DF_ezcollab_group_title] DEFAULT N'' NOT NULL,
  user_id INT CONSTRAINT [DF_ezcollab_group_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_group ADD CONSTRAINT [PK_ezcollab_group] PRIMARY KEY (id);
CREATE INDEX [ezcollab_group_depth] ON ezcollab_group (depth);
CREATE INDEX [ezcollab_group_path] ON ezcollab_group (path_string);

CREATE TABLE ezcollab_item (
  created INT CONSTRAINT [DF_ezcollab_item_created] DEFAULT 0 NOT NULL,
  creator_id INT CONSTRAINT [DF_ezcollab_item_creator_id] DEFAULT 0 NOT NULL,
  data_float1 REAL CONSTRAINT [DF_ezcollab_item_data_float1] DEFAULT 0 NOT NULL,
  data_float2 REAL CONSTRAINT [DF_ezcollab_item_data_float2] DEFAULT 0 NOT NULL,
  data_float3 REAL CONSTRAINT [DF_ezcollab_item_data_float3] DEFAULT 0 NOT NULL,
  data_int1 INT CONSTRAINT [DF_ezcollab_item_data_int1] DEFAULT 0 NOT NULL,
  data_int2 INT CONSTRAINT [DF_ezcollab_item_data_int2] DEFAULT 0 NOT NULL,
  data_int3 INT CONSTRAINT [DF_ezcollab_item_data_int3] DEFAULT 0 NOT NULL,
  data_text1 NVARCHAR(MAX) NOT NULL,
  data_text2 NVARCHAR(MAX) NOT NULL,
  data_text3 NVARCHAR(MAX) NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  modified INT CONSTRAINT [DF_ezcollab_item_modified] DEFAULT 0 NOT NULL,
  status INT CONSTRAINT [DF_ezcollab_item_status] DEFAULT 1 NOT NULL,
  type_identifier NVARCHAR(40) CONSTRAINT [DF_ezcollab_item_type_identifier] DEFAULT N'' NOT NULL
);
ALTER TABLE ezcollab_item ADD CONSTRAINT [PK_ezcollab_item] PRIMARY KEY (id);

CREATE TABLE ezcollab_item_group_link (
  collaboration_id INT CONSTRAINT [DF_ezcollab_item_group_link_collaboration_id] DEFAULT 0 NOT NULL,
  created INT CONSTRAINT [DF_ezcollab_item_group_link_created] DEFAULT 0 NOT NULL,
  group_id INT CONSTRAINT [DF_ezcollab_item_group_link_group_id] DEFAULT 0 NOT NULL,
  is_active INT CONSTRAINT [DF_ezcollab_item_group_link_is_active] DEFAULT 1 NOT NULL,
  is_read INT CONSTRAINT [DF_ezcollab_item_group_link_is_read] DEFAULT 0 NOT NULL,
  last_read INT CONSTRAINT [DF_ezcollab_item_group_link_last_read] DEFAULT 0 NOT NULL,
  modified INT CONSTRAINT [DF_ezcollab_item_group_link_modified] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_ezcollab_item_group_link_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_item_group_link ADD CONSTRAINT [PK_ezcollab_item_group_link] PRIMARY KEY (collaboration_id, group_id, user_id);

CREATE TABLE ezcollab_item_message_link (
  collaboration_id INT CONSTRAINT [DF_ezcollab_item_message_link_collaboration_id] DEFAULT 0 NOT NULL,
  created INT CONSTRAINT [DF_ezcollab_item_message_link_created] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  message_id INT CONSTRAINT [DF_ezcollab_item_message_link_message_id] DEFAULT 0 NOT NULL,
  message_type INT CONSTRAINT [DF_ezcollab_item_message_link_message_type] DEFAULT 0 NOT NULL,
  modified INT CONSTRAINT [DF_ezcollab_item_message_link_modified] DEFAULT 0 NOT NULL,
  participant_id INT CONSTRAINT [DF_ezcollab_item_message_link_participant_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_item_message_link ADD CONSTRAINT [PK_ezcollab_item_message_link] PRIMARY KEY (id);

CREATE TABLE ezcollab_item_participant_link (
  collaboration_id INT CONSTRAINT [DF_ezcollab_item_participant_link_collaboration_id] DEFAULT 0 NOT NULL,
  created INT CONSTRAINT [DF_ezcollab_item_participant_link_created] DEFAULT 0 NOT NULL,
  is_active INT CONSTRAINT [DF_ezcollab_item_participant_link_is_active] DEFAULT 1 NOT NULL,
  is_read INT CONSTRAINT [DF_ezcollab_item_participant_link_is_read] DEFAULT 0 NOT NULL,
  last_read INT CONSTRAINT [DF_ezcollab_item_participant_link_last_read] DEFAULT 0 NOT NULL,
  modified INT CONSTRAINT [DF_ezcollab_item_participant_link_modified] DEFAULT 0 NOT NULL,
  participant_id INT CONSTRAINT [DF_ezcollab_item_participant_link_participant_id] DEFAULT 0 NOT NULL,
  participant_role INT CONSTRAINT [DF_ezcollab_item_participant_link_participant_role] DEFAULT 1 NOT NULL,
  participant_type INT CONSTRAINT [DF_ezcollab_item_participant_link_participant_type] DEFAULT 1 NOT NULL
);
ALTER TABLE ezcollab_item_participant_link ADD CONSTRAINT [PK_ezcollab_item_participant_link] PRIMARY KEY (collaboration_id, participant_id);

CREATE TABLE ezcollab_item_status (
  collaboration_id INT CONSTRAINT [DF_ezcollab_item_status_collaboration_id] DEFAULT 0 NOT NULL,
  is_active INT CONSTRAINT [DF_ezcollab_item_status_is_active] DEFAULT 1 NOT NULL,
  is_read INT CONSTRAINT [DF_ezcollab_item_status_is_read] DEFAULT 0 NOT NULL,
  last_read INT CONSTRAINT [DF_ezcollab_item_status_last_read] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_ezcollab_item_status_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_item_status ADD CONSTRAINT [PK_ezcollab_item_status] PRIMARY KEY (collaboration_id, user_id);

CREATE TABLE ezcollab_notification_rule (
  collab_identifier NVARCHAR(255) CONSTRAINT [DF_ezcollab_notification_rule_collab_identifier] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  user_id NVARCHAR(255) CONSTRAINT [DF_ezcollab_notification_rule_user_id] DEFAULT N'' NOT NULL
);
ALTER TABLE ezcollab_notification_rule ADD CONSTRAINT [PK_ezcollab_notification_rule] PRIMARY KEY (id);

CREATE TABLE ezcollab_profile (
  created INT CONSTRAINT [DF_ezcollab_profile_created] DEFAULT 0 NOT NULL,
  data_text1 NVARCHAR(MAX) NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  main_group INT CONSTRAINT [DF_ezcollab_profile_main_group] DEFAULT 0 NOT NULL,
  modified INT CONSTRAINT [DF_ezcollab_profile_modified] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_ezcollab_profile_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_profile ADD CONSTRAINT [PK_ezcollab_profile] PRIMARY KEY (id);

CREATE TABLE ezcollab_simple_message (
  created INT CONSTRAINT [DF_ezcollab_simple_message_created] DEFAULT 0 NOT NULL,
  creator_id INT CONSTRAINT [DF_ezcollab_simple_message_creator_id] DEFAULT 0 NOT NULL,
  data_float1 REAL CONSTRAINT [DF_ezcollab_simple_message_data_float1] DEFAULT 0 NOT NULL,
  data_float2 REAL CONSTRAINT [DF_ezcollab_simple_message_data_float2] DEFAULT 0 NOT NULL,
  data_float3 REAL CONSTRAINT [DF_ezcollab_simple_message_data_float3] DEFAULT 0 NOT NULL,
  data_int1 INT CONSTRAINT [DF_ezcollab_simple_message_data_int1] DEFAULT 0 NOT NULL,
  data_int2 INT CONSTRAINT [DF_ezcollab_simple_message_data_int2] DEFAULT 0 NOT NULL,
  data_int3 INT CONSTRAINT [DF_ezcollab_simple_message_data_int3] DEFAULT 0 NOT NULL,
  data_text1 NVARCHAR(MAX) NOT NULL,
  data_text2 NVARCHAR(MAX) NOT NULL,
  data_text3 NVARCHAR(MAX) NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  message_type NVARCHAR(40) CONSTRAINT [DF_ezcollab_simple_message_message_type] DEFAULT N'' NOT NULL,
  modified INT CONSTRAINT [DF_ezcollab_simple_message_modified] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcollab_simple_message ADD CONSTRAINT [PK_ezcollab_simple_message] PRIMARY KEY (id);

CREATE TABLE ezcontent_language (
  disabled INT CONSTRAINT [DF_ezcontent_language_disabled] DEFAULT 0 NOT NULL,
  id BIGINT CONSTRAINT [DF_ezcontent_language_id] DEFAULT 0 NOT NULL,
  locale NVARCHAR(20) CONSTRAINT [DF_ezcontent_language_locale] DEFAULT N'' NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezcontent_language_name] DEFAULT N'' NOT NULL
);
ALTER TABLE ezcontent_language ADD CONSTRAINT [PK_ezcontent_language] PRIMARY KEY (id);
CREATE INDEX [ezcontent_language_name] ON ezcontent_language (name);

CREATE TABLE ezcontentbrowsebookmark (
  id INT IDENTITY(1,1) NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezcontentbrowsebookmark_name] DEFAULT N'' NOT NULL,
  node_id INT CONSTRAINT [DF_ezcontentbrowsebookmark_node_id] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_ezcontentbrowsebookmark_user_id] DEFAULT 0 NOT NULL,
  folder_id INT CONSTRAINT [DF_ezcontentbrowsebookmark_folder_id] DEFAULT 0 NOT NULL,
  priority INT CONSTRAINT [DF_ezcontentbrowsebookmark_priority] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentbrowsebookmark ADD CONSTRAINT [PK_ezcontentbrowsebookmark] PRIMARY KEY (id);
CREATE INDEX [ezcontentbrowsebookmark_folder] ON ezcontentbrowsebookmark (user_id, folder_id);
CREATE INDEX [ezcontentbrowsebookmark_user] ON ezcontentbrowsebookmark (user_id);

CREATE TABLE expbookmark_folder (
  created INT CONSTRAINT [DF_expbookmark_folder_created] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_expbookmark_folder_name] DEFAULT N'' NOT NULL,
  parent_id INT CONSTRAINT [DF_expbookmark_folder_parent_id] DEFAULT 0 NOT NULL,
  priority INT CONSTRAINT [DF_expbookmark_folder_priority] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_expbookmark_folder_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE expbookmark_folder ADD CONSTRAINT [PK_expbookmark_folder] PRIMARY KEY (id);
CREATE INDEX [expbookmark_folder_user] ON expbookmark_folder (user_id, parent_id);

CREATE TABLE expbookmark_view (
  bookmark_id INT CONSTRAINT [DF_expbookmark_view_bookmark_id] DEFAULT 0 NOT NULL,
  created INT CONSTRAINT [DF_expbookmark_view_created] DEFAULT 0 NOT NULL,
  module_name NVARCHAR(100) CONSTRAINT [DF_expbookmark_view_module_name] DEFAULT N'' NOT NULL,
  module_uri NVARCHAR(MAX) NULL,
  user_id INT CONSTRAINT [DF_expbookmark_view_user_id] DEFAULT 0 NOT NULL,
  view_name NVARCHAR(100) CONSTRAINT [DF_expbookmark_view_view_name] DEFAULT N'' NOT NULL,
  view_parameters NVARCHAR(MAX) NULL
);
ALTER TABLE expbookmark_view ADD CONSTRAINT [PK_expbookmark_view] PRIMARY KEY (bookmark_id);
CREATE INDEX [expbookmark_view_module] ON expbookmark_view (module_name, view_name);
CREATE INDEX [expbookmark_view_user] ON expbookmark_view (user_id);

CREATE TABLE ezcontentbrowserecent (
  created INT CONSTRAINT [DF_ezcontentbrowserecent_created] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezcontentbrowserecent_name] DEFAULT N'' NOT NULL,
  node_id INT CONSTRAINT [DF_ezcontentbrowserecent_node_id] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_ezcontentbrowserecent_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentbrowserecent ADD CONSTRAINT [PK_ezcontentbrowserecent] PRIMARY KEY (id);
CREATE INDEX [ezcontentbrowserecent_user] ON ezcontentbrowserecent (user_id);

CREATE TABLE ezcontentclass (
  always_available INT CONSTRAINT [DF_ezcontentclass_always_available] DEFAULT 0 NOT NULL,
  contentobject_name NVARCHAR(255) CONSTRAINT [DF_ezcontentclass_contentobject_name] DEFAULT NULL NULL,
  created INT CONSTRAINT [DF_ezcontentclass_created] DEFAULT 0 NOT NULL,
  creator_id INT CONSTRAINT [DF_ezcontentclass_creator_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  identifier NVARCHAR(50) CONSTRAINT [DF_ezcontentclass_identifier] DEFAULT N'' NOT NULL,
  initial_language_id BIGINT CONSTRAINT [DF_ezcontentclass_initial_language_id] DEFAULT 0 NOT NULL,
  is_container INT CONSTRAINT [DF_ezcontentclass_is_container] DEFAULT 0 NOT NULL,
  language_mask BIGINT CONSTRAINT [DF_ezcontentclass_language_mask] DEFAULT 0 NOT NULL,
  modified INT CONSTRAINT [DF_ezcontentclass_modified] DEFAULT 0 NOT NULL,
  modifier_id INT CONSTRAINT [DF_ezcontentclass_modifier_id] DEFAULT 0 NOT NULL,
  remote_id NVARCHAR(100) CONSTRAINT [DF_ezcontentclass_remote_id] DEFAULT N'' NOT NULL,
  serialized_description_list NVARCHAR(MAX) NULL,
  serialized_name_list NVARCHAR(MAX) NULL,
  sort_field INT CONSTRAINT [DF_ezcontentclass_sort_field] DEFAULT 1 NOT NULL,
  sort_order INT CONSTRAINT [DF_ezcontentclass_sort_order] DEFAULT 1 NOT NULL,
  url_alias_name NVARCHAR(255) CONSTRAINT [DF_ezcontentclass_url_alias_name] DEFAULT NULL NULL,
  version INT CONSTRAINT [DF_ezcontentclass_version] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentclass ADD CONSTRAINT [PK_ezcontentclass] PRIMARY KEY (id, version);
CREATE INDEX [ezcontentclass_version] ON ezcontentclass (version);
CREATE INDEX [ezcontentclass_identifier] ON ezcontentclass (identifier, version);

CREATE TABLE ezcontentclass_attribute (
  can_translate INT CONSTRAINT [DF_ezcontentclass_attribute_can_translate] DEFAULT 1 NULL,
  category NVARCHAR(25) CONSTRAINT [DF_ezcontentclass_attribute_category] DEFAULT N'' NOT NULL,
  contentclass_id INT CONSTRAINT [DF_ezcontentclass_attribute_contentclass_id] DEFAULT 0 NOT NULL,
  data_float1 FLOAT CONSTRAINT [DF_ezcontentclass_attribute_data_float1] DEFAULT NULL NULL,
  data_float2 FLOAT CONSTRAINT [DF_ezcontentclass_attribute_data_float2] DEFAULT NULL NULL,
  data_float3 FLOAT CONSTRAINT [DF_ezcontentclass_attribute_data_float3] DEFAULT NULL NULL,
  data_float4 FLOAT CONSTRAINT [DF_ezcontentclass_attribute_data_float4] DEFAULT NULL NULL,
  data_int1 INT CONSTRAINT [DF_ezcontentclass_attribute_data_int1] DEFAULT NULL NULL,
  data_int2 INT CONSTRAINT [DF_ezcontentclass_attribute_data_int2] DEFAULT NULL NULL,
  data_int3 INT CONSTRAINT [DF_ezcontentclass_attribute_data_int3] DEFAULT NULL NULL,
  data_int4 INT CONSTRAINT [DF_ezcontentclass_attribute_data_int4] DEFAULT NULL NULL,
  data_text1 NVARCHAR(50) CONSTRAINT [DF_ezcontentclass_attribute_data_text1] DEFAULT NULL NULL,
  data_text2 NVARCHAR(50) CONSTRAINT [DF_ezcontentclass_attribute_data_text2] DEFAULT NULL NULL,
  data_text3 NVARCHAR(50) CONSTRAINT [DF_ezcontentclass_attribute_data_text3] DEFAULT NULL NULL,
  data_text4 NVARCHAR(255) CONSTRAINT [DF_ezcontentclass_attribute_data_text4] DEFAULT NULL NULL,
  data_text5 NVARCHAR(MAX) NULL,
  data_type_string NVARCHAR(50) CONSTRAINT [DF_ezcontentclass_attribute_data_type_string] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  identifier NVARCHAR(50) CONSTRAINT [DF_ezcontentclass_attribute_identifier] DEFAULT N'' NOT NULL,
  is_information_collector INT CONSTRAINT [DF_ezcontentclass_attribute_is_information_collector] DEFAULT 0 NOT NULL,
  is_required INT CONSTRAINT [DF_ezcontentclass_attribute_is_required] DEFAULT 0 NOT NULL,
  is_searchable INT CONSTRAINT [DF_ezcontentclass_attribute_is_searchable] DEFAULT 0 NOT NULL,
  placement INT CONSTRAINT [DF_ezcontentclass_attribute_placement] DEFAULT 0 NOT NULL,
  serialized_data_text NVARCHAR(MAX) NULL,
  serialized_description_list NVARCHAR(MAX) NULL,
  serialized_name_list NVARCHAR(MAX) NOT NULL,
  version INT CONSTRAINT [DF_ezcontentclass_attribute_version] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentclass_attribute ADD CONSTRAINT [PK_ezcontentclass_attribute] PRIMARY KEY (id, version);
CREATE INDEX [ezcontentclass_attr_ccid] ON ezcontentclass_attribute (contentclass_id);

CREATE TABLE ezcontentclass_classgroup (
  contentclass_id INT CONSTRAINT [DF_ezcontentclass_classgroup_contentclass_id] DEFAULT 0 NOT NULL,
  contentclass_version INT CONSTRAINT [DF_ezcontentclass_classgroup_contentclass_version] DEFAULT 0 NOT NULL,
  group_id INT CONSTRAINT [DF_ezcontentclass_classgroup_group_id] DEFAULT 0 NOT NULL,
  group_name NVARCHAR(255) CONSTRAINT [DF_ezcontentclass_classgroup_group_name] DEFAULT NULL NULL
);
ALTER TABLE ezcontentclass_classgroup ADD CONSTRAINT [PK_ezcontentclass_classgroup] PRIMARY KEY (contentclass_id, contentclass_version, group_id);

CREATE TABLE ezcontentclass_name (
  contentclass_id INT CONSTRAINT [DF_ezcontentclass_name_contentclass_id] DEFAULT 0 NOT NULL,
  contentclass_version INT CONSTRAINT [DF_ezcontentclass_name_contentclass_version] DEFAULT 0 NOT NULL,
  language_id BIGINT CONSTRAINT [DF_ezcontentclass_name_language_id] DEFAULT 0 NOT NULL,
  language_locale NVARCHAR(20) CONSTRAINT [DF_ezcontentclass_name_language_locale] DEFAULT N'' NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezcontentclass_name_name] DEFAULT N'' NOT NULL
);
ALTER TABLE ezcontentclass_name ADD CONSTRAINT [PK_ezcontentclass_name] PRIMARY KEY (contentclass_id, contentclass_version, language_id);

CREATE TABLE ezcontentclassgroup (
  created INT CONSTRAINT [DF_ezcontentclassgroup_created] DEFAULT 0 NOT NULL,
  creator_id INT CONSTRAINT [DF_ezcontentclassgroup_creator_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  modified INT CONSTRAINT [DF_ezcontentclassgroup_modified] DEFAULT 0 NOT NULL,
  modifier_id INT CONSTRAINT [DF_ezcontentclassgroup_modifier_id] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezcontentclassgroup_name] DEFAULT NULL NULL
);
ALTER TABLE ezcontentclassgroup ADD CONSTRAINT [PK_ezcontentclassgroup] PRIMARY KEY (id);

CREATE TABLE ezcontentobject (
  contentclass_id INT CONSTRAINT [DF_ezcontentobject_contentclass_id] DEFAULT 0 NOT NULL,
  current_version INT CONSTRAINT [DF_ezcontentobject_current_version] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  initial_language_id BIGINT CONSTRAINT [DF_ezcontentobject_initial_language_id] DEFAULT 0 NOT NULL,
  language_mask BIGINT CONSTRAINT [DF_ezcontentobject_language_mask] DEFAULT 0 NOT NULL,
  modified INT CONSTRAINT [DF_ezcontentobject_modified] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezcontentobject_name] DEFAULT NULL NULL,
  owner_id INT CONSTRAINT [DF_ezcontentobject_owner_id] DEFAULT 0 NOT NULL,
  published INT CONSTRAINT [DF_ezcontentobject_published] DEFAULT 0 NOT NULL,
  remote_id NVARCHAR(100) CONSTRAINT [DF_ezcontentobject_remote_id] DEFAULT NULL NULL,
  section_id INT CONSTRAINT [DF_ezcontentobject_section_id] DEFAULT 0 NOT NULL,
  status INT CONSTRAINT [DF_ezcontentobject_status] DEFAULT 0 NULL
);
ALTER TABLE ezcontentobject ADD CONSTRAINT [PK_ezcontentobject] PRIMARY KEY (id);
CREATE INDEX [ezcontentobject_classid] ON ezcontentobject (contentclass_id);
CREATE INDEX [ezcontentobject_currentversion] ON ezcontentobject (current_version);
CREATE INDEX [ezcontentobject_lmask] ON ezcontentobject (language_mask);
CREATE INDEX [ezcontentobject_modified] ON ezcontentobject (modified);
CREATE INDEX [ezcontentobject_owner] ON ezcontentobject (owner_id);
CREATE INDEX [ezcontentobject_pub] ON ezcontentobject (published);
CREATE UNIQUE INDEX [ezcontentobject_remote_id] ON ezcontentobject (remote_id);
CREATE INDEX [ezcontentobject_status] ON ezcontentobject (status);

CREATE TABLE ezcontentobject_attribute (
  attribute_original_id INT CONSTRAINT [DF_ezcontentobject_attribute_attribute_original_id] DEFAULT 0 NULL,
  contentclassattribute_id INT CONSTRAINT [DF_ezcontentobject_attribute_contentclassattribute_id] DEFAULT 0 NOT NULL,
  contentobject_id INT CONSTRAINT [DF_ezcontentobject_attribute_contentobject_id] DEFAULT 0 NOT NULL,
  data_float FLOAT CONSTRAINT [DF_ezcontentobject_attribute_data_float] DEFAULT NULL NULL,
  data_int INT CONSTRAINT [DF_ezcontentobject_attribute_data_int] DEFAULT NULL NULL,
  data_text NVARCHAR(MAX) NULL,
  data_type_string NVARCHAR(50) CONSTRAINT [DF_ezcontentobject_attribute_data_type_string] DEFAULT N'' NULL,
  id INT IDENTITY(1,1) NOT NULL,
  language_code NVARCHAR(20) CONSTRAINT [DF_ezcontentobject_attribute_language_code] DEFAULT N'' NOT NULL,
  language_id BIGINT CONSTRAINT [DF_ezcontentobject_attribute_language_id] DEFAULT 0 NOT NULL,
  sort_key_int INT CONSTRAINT [DF_ezcontentobject_attribute_sort_key_int] DEFAULT 0 NOT NULL,
  sort_key_string NVARCHAR(255) CONSTRAINT [DF_ezcontentobject_attribute_sort_key_string] DEFAULT N'' NOT NULL,
  version INT CONSTRAINT [DF_ezcontentobject_attribute_version] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentobject_attribute ADD CONSTRAINT [PK_ezcontentobject_attribute] PRIMARY KEY (id, version);
CREATE INDEX [ezcontentobject_attribute_co_id_ver_lang_code] ON ezcontentobject_attribute (contentobject_id, version, language_code);
CREATE INDEX [ezcontentobject_attribute_language_code] ON ezcontentobject_attribute (language_code);
CREATE INDEX [ezcontentobject_classattr_id] ON ezcontentobject_attribute (contentclassattribute_id);
CREATE INDEX [sort_key_int] ON ezcontentobject_attribute (sort_key_int);
CREATE INDEX [sort_key_string] ON ezcontentobject_attribute (sort_key_string);

CREATE TABLE ezcontentobject_link (
  contentclassattribute_id INT CONSTRAINT [DF_ezcontentobject_link_contentclassattribute_id] DEFAULT 0 NOT NULL,
  from_contentobject_id INT CONSTRAINT [DF_ezcontentobject_link_from_contentobject_id] DEFAULT 0 NOT NULL,
  from_contentobject_version INT CONSTRAINT [DF_ezcontentobject_link_from_contentobject_version] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  relation_type INT CONSTRAINT [DF_ezcontentobject_link_relation_type] DEFAULT 1 NOT NULL,
  to_contentobject_id INT CONSTRAINT [DF_ezcontentobject_link_to_contentobject_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezcontentobject_link ADD CONSTRAINT [PK_ezcontentobject_link] PRIMARY KEY (id);
CREATE INDEX [ezco_link_from] ON ezcontentobject_link (from_contentobject_id, from_contentobject_version, contentclassattribute_id);
CREATE INDEX [ezco_link_to_co_id] ON ezcontentobject_link (to_contentobject_id);

CREATE TABLE ezcontentobject_name (
  content_translation NVARCHAR(20) CONSTRAINT [DF_ezcontentobject_name_content_translation] DEFAULT N'' NOT NULL,
  content_version INT CONSTRAINT [DF_ezcontentobject_name_content_version] DEFAULT 0 NOT NULL,
  contentobject_id INT CONSTRAINT [DF_ezcontentobject_name_contentobject_id] DEFAULT 0 NOT NULL,
  language_id BIGINT CONSTRAINT [DF_ezcontentobject_name_language_id] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezcontentobject_name_name] DEFAULT NULL NULL,
  real_translation NVARCHAR(20) CONSTRAINT [DF_ezcontentobject_name_real_translation] DEFAULT NULL NULL
);
ALTER TABLE ezcontentobject_name ADD CONSTRAINT [PK_ezcontentobject_name] PRIMARY KEY (contentobject_id, content_version, content_translation);
CREATE INDEX [ezcontentobject_name_cov_id] ON ezcontentobject_name (content_version);
CREATE INDEX [ezcontentobject_name_lang_id] ON ezcontentobject_name (language_id);
CREATE INDEX [ezcontentobject_name_name] ON ezcontentobject_name (name);

CREATE TABLE ezcontentobject_trash (
  contentobject_id INT CONSTRAINT [DF_ezcontentobject_trash_contentobject_id] DEFAULT NULL NULL,
  contentobject_version INT CONSTRAINT [DF_ezcontentobject_trash_contentobject_version] DEFAULT NULL NULL,
  depth INT CONSTRAINT [DF_ezcontentobject_trash_depth] DEFAULT 0 NOT NULL,
  is_hidden INT CONSTRAINT [DF_ezcontentobject_trash_is_hidden] DEFAULT 0 NOT NULL,
  is_invisible INT CONSTRAINT [DF_ezcontentobject_trash_is_invisible] DEFAULT 0 NOT NULL,
  main_node_id INT CONSTRAINT [DF_ezcontentobject_trash_main_node_id] DEFAULT NULL NULL,
  modified_subnode INT CONSTRAINT [DF_ezcontentobject_trash_modified_subnode] DEFAULT 0 NULL,
  node_id INT CONSTRAINT [DF_ezcontentobject_trash_node_id] DEFAULT 0 NOT NULL,
  parent_node_id INT CONSTRAINT [DF_ezcontentobject_trash_parent_node_id] DEFAULT 0 NOT NULL,
  path_identification_string NVARCHAR(840) NULL,
  path_string NVARCHAR(255) CONSTRAINT [DF_ezcontentobject_trash_path_string] DEFAULT N'' NOT NULL,
  priority INT CONSTRAINT [DF_ezcontentobject_trash_priority] DEFAULT 0 NOT NULL,
  remote_id NVARCHAR(100) CONSTRAINT [DF_ezcontentobject_trash_remote_id] DEFAULT N'' NOT NULL,
  sort_field INT CONSTRAINT [DF_ezcontentobject_trash_sort_field] DEFAULT 1 NULL,
  sort_order INT CONSTRAINT [DF_ezcontentobject_trash_sort_order] DEFAULT 1 NULL,
  trashed INT CONSTRAINT [DF_ezcontentobject_trash_trashed] DEFAULT 0 NOT NULL,
  trashed_by INT CONSTRAINT [DF_ezcontentobject_trash_trashed_by] DEFAULT 0 NOT NULL,
  trashed_via NVARCHAR(100) CONSTRAINT [DF_ezcontentobject_trash_trashed_via] DEFAULT N'' NOT NULL
);
ALTER TABLE ezcontentobject_trash ADD CONSTRAINT [PK_ezcontentobject_trash] PRIMARY KEY (node_id);
CREATE INDEX [ezcobj_trash_co_id] ON ezcontentobject_trash (contentobject_id);
CREATE INDEX [ezcobj_trash_depth] ON ezcontentobject_trash (depth);
CREATE INDEX [ezcobj_trash_modified_subnode] ON ezcontentobject_trash (modified_subnode);
CREATE INDEX [ezcobj_trash_p_node_id] ON ezcontentobject_trash (parent_node_id);
CREATE INDEX [ezcobj_trash_path] ON ezcontentobject_trash (path_string);
CREATE INDEX [ezcobj_trash_path_ident] ON ezcontentobject_trash (path_identification_string);
CREATE INDEX [ezcobj_trash_trashed_by] ON ezcontentobject_trash (trashed_by);

CREATE TABLE ezcontentobject_tree (
  contentobject_id INT CONSTRAINT [DF_ezcontentobject_tree_contentobject_id] DEFAULT NULL NULL,
  contentobject_is_published INT CONSTRAINT [DF_ezcontentobject_tree_contentobject_is_published] DEFAULT NULL NULL,
  contentobject_version INT CONSTRAINT [DF_ezcontentobject_tree_contentobject_version] DEFAULT NULL NULL,
  depth INT CONSTRAINT [DF_ezcontentobject_tree_depth] DEFAULT 0 NOT NULL,
  is_hidden INT CONSTRAINT [DF_ezcontentobject_tree_is_hidden] DEFAULT 0 NOT NULL,
  is_invisible INT CONSTRAINT [DF_ezcontentobject_tree_is_invisible] DEFAULT 0 NOT NULL,
  main_node_id INT CONSTRAINT [DF_ezcontentobject_tree_main_node_id] DEFAULT NULL NULL,
  modified_subnode INT CONSTRAINT [DF_ezcontentobject_tree_modified_subnode] DEFAULT 0 NULL,
  node_id INT IDENTITY(1,1) NOT NULL,
  parent_node_id INT CONSTRAINT [DF_ezcontentobject_tree_parent_node_id] DEFAULT 0 NOT NULL,
  path_identification_string NVARCHAR(840) NULL,
  path_string NVARCHAR(255) CONSTRAINT [DF_ezcontentobject_tree_path_string] DEFAULT N'' NOT NULL,
  priority INT CONSTRAINT [DF_ezcontentobject_tree_priority] DEFAULT 0 NOT NULL,
  remote_id NVARCHAR(100) CONSTRAINT [DF_ezcontentobject_tree_remote_id] DEFAULT N'' NOT NULL,
  sort_field INT CONSTRAINT [DF_ezcontentobject_tree_sort_field] DEFAULT 1 NULL,
  sort_order INT CONSTRAINT [DF_ezcontentobject_tree_sort_order] DEFAULT 1 NULL
);
ALTER TABLE ezcontentobject_tree ADD CONSTRAINT [PK_ezcontentobject_tree] PRIMARY KEY (node_id);
CREATE INDEX [ezcontentobject_tree_remote_id] ON ezcontentobject_tree (remote_id);
CREATE INDEX [ezcontentobject_tree_co_id] ON ezcontentobject_tree (contentobject_id);
CREATE INDEX [ezcontentobject_tree_depth] ON ezcontentobject_tree (depth);
CREATE INDEX [ezcontentobject_tree_p_node_id] ON ezcontentobject_tree (parent_node_id);
CREATE INDEX [ezcontentobject_tree_path] ON ezcontentobject_tree (path_string);
CREATE INDEX [ezcontentobject_tree_path_ident] ON ezcontentobject_tree (path_identification_string);
CREATE INDEX [modified_subnode] ON ezcontentobject_tree (modified_subnode);

CREATE TABLE ezcontentobject_version (
  contentobject_id INT CONSTRAINT [DF_ezcontentobject_version_contentobject_id] DEFAULT NULL NULL,
  created INT CONSTRAINT [DF_ezcontentobject_version_created] DEFAULT 0 NOT NULL,
  creator_id INT CONSTRAINT [DF_ezcontentobject_version_creator_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  initial_language_id BIGINT CONSTRAINT [DF_ezcontentobject_version_initial_language_id] DEFAULT 0 NOT NULL,
  language_mask BIGINT CONSTRAINT [DF_ezcontentobject_version_language_mask] DEFAULT 0 NOT NULL,
  modified INT CONSTRAINT [DF_ezcontentobject_version_modified] DEFAULT 0 NOT NULL,
  status INT CONSTRAINT [DF_ezcontentobject_version_status] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_ezcontentobject_version_user_id] DEFAULT 0 NOT NULL,
  version INT CONSTRAINT [DF_ezcontentobject_version_version] DEFAULT 0 NOT NULL,
  workflow_event_pos INT CONSTRAINT [DF_ezcontentobject_version_workflow_event_pos] DEFAULT 0 NULL
);
ALTER TABLE ezcontentobject_version ADD CONSTRAINT [PK_ezcontentobject_version] PRIMARY KEY (id);
CREATE INDEX [ezcobj_version_creator_id] ON ezcontentobject_version (creator_id);
CREATE INDEX [ezcobj_version_status] ON ezcontentobject_version (status);
CREATE INDEX [idx_object_version_objver] ON ezcontentobject_version (contentobject_id, version);
CREATE INDEX [ezcontobj_version_obj_status] ON ezcontentobject_version (contentobject_id, status);

CREATE TABLE ezcurrencydata (
  auto_rate_value DECIMAL(10,5) CONSTRAINT [DF_ezcurrencydata_auto_rate_value] DEFAULT 0 NOT NULL,
  code NVARCHAR(4) CONSTRAINT [DF_ezcurrencydata_code] DEFAULT N'' NOT NULL,
  custom_rate_value DECIMAL(10,5) CONSTRAINT [DF_ezcurrencydata_custom_rate_value] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  locale NVARCHAR(255) CONSTRAINT [DF_ezcurrencydata_locale] DEFAULT N'' NOT NULL,
  rate_factor DECIMAL(10,5) CONSTRAINT [DF_ezcurrencydata_rate_factor] DEFAULT 1 NOT NULL,
  status INT CONSTRAINT [DF_ezcurrencydata_status] DEFAULT 1 NOT NULL,
  symbol NVARCHAR(255) CONSTRAINT [DF_ezcurrencydata_symbol] DEFAULT N'' NOT NULL
);
ALTER TABLE ezcurrencydata ADD CONSTRAINT [PK_ezcurrencydata] PRIMARY KEY (id);
CREATE INDEX [ezcurrencydata_code] ON ezcurrencydata (code);

CREATE TABLE ezdiscountrule (
  id INT IDENTITY(1,1) NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezdiscountrule_name] DEFAULT N'' NOT NULL
);
ALTER TABLE ezdiscountrule ADD CONSTRAINT [PK_ezdiscountrule] PRIMARY KEY (id);

CREATE TABLE ezdiscountsubrule (
  discount_percent REAL CONSTRAINT [DF_ezdiscountsubrule_discount_percent] DEFAULT NULL NULL,
  discountrule_id INT CONSTRAINT [DF_ezdiscountsubrule_discountrule_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  limitation NCHAR(1) CONSTRAINT [DF_ezdiscountsubrule_limitation] DEFAULT NULL NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezdiscountsubrule_name] DEFAULT N'' NOT NULL
);
ALTER TABLE ezdiscountsubrule ADD CONSTRAINT [PK_ezdiscountsubrule] PRIMARY KEY (id);

CREATE TABLE ezdiscountsubrule_value (
  discountsubrule_id INT CONSTRAINT [DF_ezdiscountsubrule_value_discountsubrule_id] DEFAULT 0 NOT NULL,
  issection INT CONSTRAINT [DF_ezdiscountsubrule_value_issection] DEFAULT 0 NOT NULL,
  value INT CONSTRAINT [DF_ezdiscountsubrule_value_value] DEFAULT 0 NOT NULL
);
ALTER TABLE ezdiscountsubrule_value ADD CONSTRAINT [PK_ezdiscountsubrule_value] PRIMARY KEY (discountsubrule_id, value, issection);

CREATE TABLE ezenumobjectvalue (
  contentobject_attribute_id INT CONSTRAINT [DF_ezenumobjectvalue_contentobject_attribute_id] DEFAULT 0 NOT NULL,
  contentobject_attribute_version INT CONSTRAINT [DF_ezenumobjectvalue_contentobject_attribute_version] DEFAULT 0 NOT NULL,
  enumelement NVARCHAR(255) CONSTRAINT [DF_ezenumobjectvalue_enumelement] DEFAULT N'' NOT NULL,
  enumid INT CONSTRAINT [DF_ezenumobjectvalue_enumid] DEFAULT 0 NOT NULL,
  enumvalue NVARCHAR(255) CONSTRAINT [DF_ezenumobjectvalue_enumvalue] DEFAULT N'' NOT NULL
);
ALTER TABLE ezenumobjectvalue ADD CONSTRAINT [PK_ezenumobjectvalue] PRIMARY KEY (contentobject_attribute_id, contentobject_attribute_version, enumid);

CREATE TABLE ezenumvalue (
  contentclass_attribute_id INT CONSTRAINT [DF_ezenumvalue_contentclass_attribute_id] DEFAULT 0 NOT NULL,
  contentclass_attribute_version INT CONSTRAINT [DF_ezenumvalue_contentclass_attribute_version] DEFAULT 0 NOT NULL,
  enumelement NVARCHAR(255) CONSTRAINT [DF_ezenumvalue_enumelement] DEFAULT N'' NOT NULL,
  enumvalue NVARCHAR(255) CONSTRAINT [DF_ezenumvalue_enumvalue] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  placement INT CONSTRAINT [DF_ezenumvalue_placement] DEFAULT 0 NOT NULL
);
ALTER TABLE ezenumvalue ADD CONSTRAINT [PK_ezenumvalue] PRIMARY KEY (id, contentclass_attribute_id, contentclass_attribute_version);
CREATE INDEX [ezenumvalue_co_cl_attr_id_co_class_att_ver] ON ezenumvalue (contentclass_attribute_id, contentclass_attribute_version);

CREATE TABLE ezforgot_password (
  hash_key NVARCHAR(32) CONSTRAINT [DF_ezforgot_password_hash_key] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  time INT CONSTRAINT [DF_ezforgot_password_time] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_ezforgot_password_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezforgot_password ADD CONSTRAINT [PK_ezforgot_password] PRIMARY KEY (id);
CREATE INDEX [ezforgot_password_user] ON ezforgot_password (user_id);

CREATE TABLE ezgeneral_digest_user_settings (
  day NVARCHAR(255) CONSTRAINT [DF_ezgeneral_digest_user_settings_day] DEFAULT N'' NOT NULL,
  digest_type INT CONSTRAINT [DF_ezgeneral_digest_user_settings_digest_type] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  receive_digest INT CONSTRAINT [DF_ezgeneral_digest_user_settings_receive_digest] DEFAULT 0 NOT NULL,
  time NVARCHAR(255) CONSTRAINT [DF_ezgeneral_digest_user_settings_time] DEFAULT N'' NOT NULL,
  user_id INT CONSTRAINT [DF_ezgeneral_digest_user_settings_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezgeneral_digest_user_settings ADD CONSTRAINT [PK_ezgeneral_digest_user_settings] PRIMARY KEY (id);
CREATE UNIQUE INDEX [ezgeneral_digest_user_id] ON ezgeneral_digest_user_settings (user_id);

CREATE TABLE ezimagefile (
  contentobject_attribute_id INT CONSTRAINT [DF_ezimagefile_contentobject_attribute_id] DEFAULT 0 NOT NULL,
  filepath NVARCHAR(840) NOT NULL,
  id INT IDENTITY(1,1) NOT NULL
);
ALTER TABLE ezimagefile ADD CONSTRAINT [PK_ezimagefile] PRIMARY KEY (id);
CREATE INDEX [ezimagefile_coid] ON ezimagefile (contentobject_attribute_id);
CREATE INDEX [ezimagefile_file] ON ezimagefile (filepath);

CREATE TABLE ezinfocollection (
  contentobject_id INT CONSTRAINT [DF_ezinfocollection_contentobject_id] DEFAULT 0 NOT NULL,
  created INT CONSTRAINT [DF_ezinfocollection_created] DEFAULT 0 NOT NULL,
  creator_id INT CONSTRAINT [DF_ezinfocollection_creator_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  modified INT CONSTRAINT [DF_ezinfocollection_modified] DEFAULT 0 NULL,
  user_identifier NVARCHAR(34) CONSTRAINT [DF_ezinfocollection_user_identifier] DEFAULT NULL NULL
);
ALTER TABLE ezinfocollection ADD CONSTRAINT [PK_ezinfocollection] PRIMARY KEY (id);
CREATE INDEX [ezinfocollection_co_id_created] ON ezinfocollection (contentobject_id, created);

CREATE TABLE ezinfocollection_attribute (
  contentclass_attribute_id INT CONSTRAINT [DF_ezinfocollection_attribute_contentclass_attribute_id] DEFAULT 0 NOT NULL,
  contentobject_attribute_id INT CONSTRAINT [DF_ezinfocollection_attribute_contentobject_attribute_id] DEFAULT NULL NULL,
  contentobject_id INT CONSTRAINT [DF_ezinfocollection_attribute_contentobject_id] DEFAULT NULL NULL,
  data_float REAL CONSTRAINT [DF_ezinfocollection_attribute_data_float] DEFAULT NULL NULL,
  data_int INT CONSTRAINT [DF_ezinfocollection_attribute_data_int] DEFAULT NULL NULL,
  data_text NVARCHAR(MAX) NULL,
  id INT IDENTITY(1,1) NOT NULL,
  informationcollection_id INT CONSTRAINT [DF_ezinfocollection_attribute_informationcollection_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezinfocollection_attribute ADD CONSTRAINT [PK_ezinfocollection_attribute] PRIMARY KEY (id);
CREATE INDEX [ezinfocollection_attr_cca_id] ON ezinfocollection_attribute (contentclass_attribute_id);
CREATE INDEX [ezinfocollection_attr_co_id] ON ezinfocollection_attribute (contentobject_id);
CREATE INDEX [ezinfocollection_attr_coa_id] ON ezinfocollection_attribute (contentobject_attribute_id);
CREATE INDEX [ezinfocollection_attr_ic_id] ON ezinfocollection_attribute (informationcollection_id);

CREATE TABLE ezisbn_group (
  description NVARCHAR(255) CONSTRAINT [DF_ezisbn_group_description] DEFAULT N'' NOT NULL,
  group_number INT CONSTRAINT [DF_ezisbn_group_group_number] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL
);
ALTER TABLE ezisbn_group ADD CONSTRAINT [PK_ezisbn_group] PRIMARY KEY (id);

CREATE TABLE ezisbn_group_range (
  from_number INT CONSTRAINT [DF_ezisbn_group_range_from_number] DEFAULT 0 NOT NULL,
  group_from NVARCHAR(32) CONSTRAINT [DF_ezisbn_group_range_group_from] DEFAULT N'' NOT NULL,
  group_length INT CONSTRAINT [DF_ezisbn_group_range_group_length] DEFAULT 0 NOT NULL,
  group_to NVARCHAR(32) CONSTRAINT [DF_ezisbn_group_range_group_to] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  to_number INT CONSTRAINT [DF_ezisbn_group_range_to_number] DEFAULT 0 NOT NULL
);
ALTER TABLE ezisbn_group_range ADD CONSTRAINT [PK_ezisbn_group_range] PRIMARY KEY (id);

CREATE TABLE ezisbn_registrant_range (
  from_number INT CONSTRAINT [DF_ezisbn_registrant_range_from_number] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  isbn_group_id INT CONSTRAINT [DF_ezisbn_registrant_range_isbn_group_id] DEFAULT 0 NOT NULL,
  registrant_from NVARCHAR(32) CONSTRAINT [DF_ezisbn_registrant_range_registrant_from] DEFAULT N'' NOT NULL,
  registrant_length INT CONSTRAINT [DF_ezisbn_registrant_range_registrant_length] DEFAULT 0 NOT NULL,
  registrant_to NVARCHAR(32) CONSTRAINT [DF_ezisbn_registrant_range_registrant_to] DEFAULT N'' NOT NULL,
  to_number INT CONSTRAINT [DF_ezisbn_registrant_range_to_number] DEFAULT 0 NOT NULL
);
ALTER TABLE ezisbn_registrant_range ADD CONSTRAINT [PK_ezisbn_registrant_range] PRIMARY KEY (id);

CREATE TABLE ezkeyword (
  class_id INT CONSTRAINT [DF_ezkeyword_class_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  keyword NVARCHAR(255) CONSTRAINT [DF_ezkeyword_keyword] DEFAULT NULL NULL
);
ALTER TABLE ezkeyword ADD CONSTRAINT [PK_ezkeyword] PRIMARY KEY (id);
CREATE INDEX [ezkeyword_keyword] ON ezkeyword (keyword);

CREATE TABLE ezkeyword_attribute_link (
  id INT IDENTITY(1,1) NOT NULL,
  keyword_id INT CONSTRAINT [DF_ezkeyword_attribute_link_keyword_id] DEFAULT 0 NOT NULL,
  objectattribute_id INT CONSTRAINT [DF_ezkeyword_attribute_link_objectattribute_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezkeyword_attribute_link ADD CONSTRAINT [PK_ezkeyword_attribute_link] PRIMARY KEY (id);
CREATE INDEX [ezkeyword_attr_link_kid_oaid] ON ezkeyword_attribute_link (keyword_id, objectattribute_id);
CREATE INDEX [ezkeyword_attr_link_oaid] ON ezkeyword_attribute_link (objectattribute_id);

CREATE TABLE ezmedia (
  contentobject_attribute_id INT CONSTRAINT [DF_ezmedia_contentobject_attribute_id] DEFAULT 0 NOT NULL,
  controls NVARCHAR(50) CONSTRAINT [DF_ezmedia_controls] DEFAULT NULL NULL,
  filename NVARCHAR(255) CONSTRAINT [DF_ezmedia_filename] DEFAULT N'' NOT NULL,
  has_controller INT CONSTRAINT [DF_ezmedia_has_controller] DEFAULT 0 NULL,
  height INT CONSTRAINT [DF_ezmedia_height] DEFAULT NULL NULL,
  is_autoplay INT CONSTRAINT [DF_ezmedia_is_autoplay] DEFAULT 0 NULL,
  is_loop INT CONSTRAINT [DF_ezmedia_is_loop] DEFAULT 0 NULL,
  mime_type NVARCHAR(50) CONSTRAINT [DF_ezmedia_mime_type] DEFAULT N'' NOT NULL,
  original_filename NVARCHAR(255) CONSTRAINT [DF_ezmedia_original_filename] DEFAULT N'' NOT NULL,
  pluginspage NVARCHAR(255) CONSTRAINT [DF_ezmedia_pluginspage] DEFAULT NULL NULL,
  quality NVARCHAR(50) CONSTRAINT [DF_ezmedia_quality] DEFAULT NULL NULL,
  version INT CONSTRAINT [DF_ezmedia_version] DEFAULT 0 NOT NULL,
  width INT CONSTRAINT [DF_ezmedia_width] DEFAULT NULL NULL
);
ALTER TABLE ezmedia ADD CONSTRAINT [PK_ezmedia] PRIMARY KEY (contentobject_attribute_id, version);

CREATE TABLE ezmessage (
  body NVARCHAR(MAX) NULL,
  destination_address NVARCHAR(50) CONSTRAINT [DF_ezmessage_destination_address] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  is_sent INT CONSTRAINT [DF_ezmessage_is_sent] DEFAULT 0 NOT NULL,
  send_method NVARCHAR(50) CONSTRAINT [DF_ezmessage_send_method] DEFAULT N'' NOT NULL,
  send_time NVARCHAR(50) CONSTRAINT [DF_ezmessage_send_time] DEFAULT N'' NOT NULL,
  send_weekday NVARCHAR(50) CONSTRAINT [DF_ezmessage_send_weekday] DEFAULT N'' NOT NULL,
  title NVARCHAR(255) CONSTRAINT [DF_ezmessage_title] DEFAULT N'' NOT NULL
);
ALTER TABLE ezmessage ADD CONSTRAINT [PK_ezmessage] PRIMARY KEY (id);

CREATE TABLE ezmodule_run (
  function_name NVARCHAR(255) CONSTRAINT [DF_ezmodule_run_function_name] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  module_data NVARCHAR(MAX) NULL,
  module_name NVARCHAR(255) CONSTRAINT [DF_ezmodule_run_module_name] DEFAULT NULL NULL,
  workflow_process_id INT CONSTRAINT [DF_ezmodule_run_workflow_process_id] DEFAULT NULL NULL
);
ALTER TABLE ezmodule_run ADD CONSTRAINT [PK_ezmodule_run] PRIMARY KEY (id);
CREATE UNIQUE INDEX [ezmodule_run_workflow_process_id_s] ON ezmodule_run (workflow_process_id);

CREATE TABLE ezmultipricedata (
  contentobject_attr_id INT CONSTRAINT [DF_ezmultipricedata_contentobject_attr_id] DEFAULT 0 NOT NULL,
  contentobject_attr_version INT CONSTRAINT [DF_ezmultipricedata_contentobject_attr_version] DEFAULT 0 NOT NULL,
  currency_code NVARCHAR(4) CONSTRAINT [DF_ezmultipricedata_currency_code] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  type INT CONSTRAINT [DF_ezmultipricedata_type] DEFAULT 0 NOT NULL,
  value DECIMAL(15,2) CONSTRAINT [DF_ezmultipricedata_value] DEFAULT 0 NOT NULL
);
ALTER TABLE ezmultipricedata ADD CONSTRAINT [PK_ezmultipricedata] PRIMARY KEY (id);
CREATE INDEX [ezmultipricedata_coa_id] ON ezmultipricedata (contentobject_attr_id);
CREATE INDEX [ezmultipricedata_coa_version] ON ezmultipricedata (contentobject_attr_version);
CREATE INDEX [ezmultipricedata_currency_code] ON ezmultipricedata (currency_code);

CREATE TABLE eznode_assignment (
  contentobject_id INT CONSTRAINT [DF_eznode_assignment_contentobject_id] DEFAULT NULL NULL,
  contentobject_version INT CONSTRAINT [DF_eznode_assignment_contentobject_version] DEFAULT NULL NULL,
  from_node_id INT CONSTRAINT [DF_eznode_assignment_from_node_id] DEFAULT 0 NULL,
  id INT IDENTITY(1,1) NOT NULL,
  is_main INT CONSTRAINT [DF_eznode_assignment_is_main] DEFAULT 0 NOT NULL,
  op_code INT CONSTRAINT [DF_eznode_assignment_op_code] DEFAULT 0 NOT NULL,
  parent_node INT CONSTRAINT [DF_eznode_assignment_parent_node] DEFAULT NULL NULL,
  parent_remote_id NVARCHAR(100) CONSTRAINT [DF_eznode_assignment_parent_remote_id] DEFAULT N'' NOT NULL,
  remote_id NVARCHAR(100) CONSTRAINT [DF_eznode_assignment_remote_id] DEFAULT N'0' NOT NULL,
  sort_field INT CONSTRAINT [DF_eznode_assignment_sort_field] DEFAULT 1 NULL,
  sort_order INT CONSTRAINT [DF_eznode_assignment_sort_order] DEFAULT 1 NULL,
  priority INT CONSTRAINT [DF_eznode_assignment_priority] DEFAULT 0 NOT NULL,
  is_hidden INT CONSTRAINT [DF_eznode_assignment_is_hidden] DEFAULT 0 NOT NULL
);
ALTER TABLE eznode_assignment ADD CONSTRAINT [PK_eznode_assignment] PRIMARY KEY (id);
CREATE INDEX [eznode_assignment_co_version] ON eznode_assignment (contentobject_version);
CREATE INDEX [eznode_assignment_coid_cov] ON eznode_assignment (contentobject_id, contentobject_version);
CREATE INDEX [eznode_assignment_is_main] ON eznode_assignment (is_main);
CREATE INDEX [eznode_assignment_parent_node] ON eznode_assignment (parent_node);

CREATE TABLE eznotificationcollection (
  data_subject NVARCHAR(MAX) NOT NULL,
  data_text NVARCHAR(MAX) NOT NULL,
  event_id INT CONSTRAINT [DF_eznotificationcollection_event_id] DEFAULT 0 NOT NULL,
  handler NVARCHAR(255) CONSTRAINT [DF_eznotificationcollection_handler] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  transport NVARCHAR(255) CONSTRAINT [DF_eznotificationcollection_transport] DEFAULT N'' NOT NULL
);
ALTER TABLE eznotificationcollection ADD CONSTRAINT [PK_eznotificationcollection] PRIMARY KEY (id);

CREATE TABLE eznotificationcollection_item (
  address NVARCHAR(255) CONSTRAINT [DF_eznotificationcollection_item_address] DEFAULT N'' NOT NULL,
  collection_id INT CONSTRAINT [DF_eznotificationcollection_item_collection_id] DEFAULT 0 NOT NULL,
  event_id INT CONSTRAINT [DF_eznotificationcollection_item_event_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  send_date INT CONSTRAINT [DF_eznotificationcollection_item_send_date] DEFAULT 0 NOT NULL
);
ALTER TABLE eznotificationcollection_item ADD CONSTRAINT [PK_eznotificationcollection_item] PRIMARY KEY (id);

CREATE TABLE eznotificationevent (
  data_int1 INT CONSTRAINT [DF_eznotificationevent_data_int1] DEFAULT 0 NOT NULL,
  data_int2 INT CONSTRAINT [DF_eznotificationevent_data_int2] DEFAULT 0 NOT NULL,
  data_int3 INT CONSTRAINT [DF_eznotificationevent_data_int3] DEFAULT 0 NOT NULL,
  data_int4 INT CONSTRAINT [DF_eznotificationevent_data_int4] DEFAULT 0 NOT NULL,
  data_text1 NVARCHAR(MAX) NOT NULL,
  data_text2 NVARCHAR(MAX) NOT NULL,
  data_text3 NVARCHAR(MAX) NOT NULL,
  data_text4 NVARCHAR(MAX) NOT NULL,
  event_type_string NVARCHAR(255) CONSTRAINT [DF_eznotificationevent_event_type_string] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  status INT CONSTRAINT [DF_eznotificationevent_status] DEFAULT 0 NOT NULL
);
ALTER TABLE eznotificationevent ADD CONSTRAINT [PK_eznotificationevent] PRIMARY KEY (id);

CREATE TABLE ezoperation_memento (
  id INT IDENTITY(1,1) NOT NULL,
  main INT CONSTRAINT [DF_ezoperation_memento_main] DEFAULT 0 NOT NULL,
  main_key NVARCHAR(32) CONSTRAINT [DF_ezoperation_memento_main_key] DEFAULT N'' NOT NULL,
  memento_data NVARCHAR(MAX) NOT NULL,
  memento_key NVARCHAR(32) CONSTRAINT [DF_ezoperation_memento_memento_key] DEFAULT N'' NOT NULL
);
ALTER TABLE ezoperation_memento ADD CONSTRAINT [PK_ezoperation_memento] PRIMARY KEY (id, memento_key);
CREATE INDEX [ezoperation_memento_memento_key_main] ON ezoperation_memento (memento_key, main);

CREATE TABLE ezorder (
  account_identifier NVARCHAR(100) CONSTRAINT [DF_ezorder_account_identifier] DEFAULT N'default' NOT NULL,
  created INT CONSTRAINT [DF_ezorder_created] DEFAULT 0 NOT NULL,
  data_text_1 NVARCHAR(MAX) NULL,
  data_text_2 NVARCHAR(MAX) NULL,
  email NVARCHAR(150) CONSTRAINT [DF_ezorder_email] DEFAULT N'' NULL,
  id INT IDENTITY(1,1) NOT NULL,
  ignore_vat INT CONSTRAINT [DF_ezorder_ignore_vat] DEFAULT 0 NOT NULL,
  is_archived INT CONSTRAINT [DF_ezorder_is_archived] DEFAULT 0 NOT NULL,
  is_temporary INT CONSTRAINT [DF_ezorder_is_temporary] DEFAULT 1 NOT NULL,
  order_nr INT CONSTRAINT [DF_ezorder_order_nr] DEFAULT 0 NOT NULL,
  productcollection_id INT CONSTRAINT [DF_ezorder_productcollection_id] DEFAULT 0 NOT NULL,
  siteaccess NVARCHAR(255) CONSTRAINT [DF_ezorder_siteaccess] DEFAULT N'' NOT NULL,
  status_id INT CONSTRAINT [DF_ezorder_status_id] DEFAULT 0 NULL,
  status_modified INT CONSTRAINT [DF_ezorder_status_modified] DEFAULT 0 NULL,
  status_modifier_id INT CONSTRAINT [DF_ezorder_status_modifier_id] DEFAULT 0 NULL,
  user_id INT CONSTRAINT [DF_ezorder_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder ADD CONSTRAINT [PK_ezorder] PRIMARY KEY (id);
CREATE INDEX [ezorder_is_archived] ON ezorder (is_archived);
CREATE INDEX [ezorder_is_tmp] ON ezorder (is_temporary);

CREATE TABLE ezorder_nr_incr (
  id INT IDENTITY(1,1) NOT NULL
);
ALTER TABLE ezorder_nr_incr ADD CONSTRAINT [PK_ezorder_nr_incr] PRIMARY KEY (id);

CREATE TABLE ezorder_item (
  description NVARCHAR(255) CONSTRAINT [DF_ezorder_item_description] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  is_vat_inc INT CONSTRAINT [DF_ezorder_item_is_vat_inc] DEFAULT 0 NOT NULL,
  order_id INT CONSTRAINT [DF_ezorder_item_order_id] DEFAULT 0 NOT NULL,
  price REAL CONSTRAINT [DF_ezorder_item_price] DEFAULT NULL NULL,
  type NVARCHAR(30) CONSTRAINT [DF_ezorder_item_type] DEFAULT NULL NULL,
  vat_value REAL CONSTRAINT [DF_ezorder_item_vat_value] DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder_item ADD CONSTRAINT [PK_ezorder_item] PRIMARY KEY (id);
CREATE INDEX [ezorder_item_order_id] ON ezorder_item (order_id);
CREATE INDEX [ezorder_item_type] ON ezorder_item (type);

CREATE TABLE ezorder_status (
  id INT IDENTITY(1,1) NOT NULL,
  is_active INT CONSTRAINT [DF_ezorder_status_is_active] DEFAULT 1 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezorder_status_name] DEFAULT N'' NOT NULL,
  status_id INT CONSTRAINT [DF_ezorder_status_status_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder_status ADD CONSTRAINT [PK_ezorder_status] PRIMARY KEY (id);
CREATE INDEX [ezorder_status_active] ON ezorder_status (is_active);
CREATE INDEX [ezorder_status_name] ON ezorder_status (name);
CREATE INDEX [ezorder_status_sid] ON ezorder_status (status_id);

CREATE TABLE ezorder_status_history (
  id INT IDENTITY(1,1) NOT NULL,
  modified INT CONSTRAINT [DF_ezorder_status_history_modified] DEFAULT 0 NOT NULL,
  modifier_id INT CONSTRAINT [DF_ezorder_status_history_modifier_id] DEFAULT 0 NOT NULL,
  order_id INT CONSTRAINT [DF_ezorder_status_history_order_id] DEFAULT 0 NOT NULL,
  status_id INT CONSTRAINT [DF_ezorder_status_history_status_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezorder_status_history ADD CONSTRAINT [PK_ezorder_status_history] PRIMARY KEY (id);
CREATE INDEX [ezorder_status_history_mod] ON ezorder_status_history (modified);
CREATE INDEX [ezorder_status_history_oid] ON ezorder_status_history (order_id);
CREATE INDEX [ezorder_status_history_sid] ON ezorder_status_history (status_id);

CREATE TABLE ezpackage (
  id INT IDENTITY(1,1) NOT NULL,
  install_date INT CONSTRAINT [DF_ezpackage_install_date] DEFAULT 0 NOT NULL,
  name NVARCHAR(100) CONSTRAINT [DF_ezpackage_name] DEFAULT N'' NOT NULL,
  version NVARCHAR(30) CONSTRAINT [DF_ezpackage_version] DEFAULT N'0' NOT NULL
);
ALTER TABLE ezpackage ADD CONSTRAINT [PK_ezpackage] PRIMARY KEY (id);

CREATE TABLE ezpaymentobject (
  id INT IDENTITY(1,1) NOT NULL,
  order_id INT CONSTRAINT [DF_ezpaymentobject_order_id] DEFAULT 0 NOT NULL,
  payment_string NVARCHAR(255) CONSTRAINT [DF_ezpaymentobject_payment_string] DEFAULT N'' NOT NULL,
  status INT CONSTRAINT [DF_ezpaymentobject_status] DEFAULT 0 NOT NULL,
  workflowprocess_id INT CONSTRAINT [DF_ezpaymentobject_workflowprocess_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezpaymentobject ADD CONSTRAINT [PK_ezpaymentobject] PRIMARY KEY (id);

CREATE TABLE ezpdf_export (
  footer_text NVARCHAR(255) CONSTRAINT [DF_ezpdf_export_footer_text] DEFAULT N'' NOT NULL,
  show_footer INT CONSTRAINT [DF_ezpdf_export_show_footer] DEFAULT 1 NOT NULL,
  created INT CONSTRAINT [DF_ezpdf_export_created] DEFAULT NULL NULL,
  creator_id INT CONSTRAINT [DF_ezpdf_export_creator_id] DEFAULT NULL NULL,
  export_classes NVARCHAR(255) CONSTRAINT [DF_ezpdf_export_export_classes] DEFAULT NULL NULL,
  export_structure NVARCHAR(255) CONSTRAINT [DF_ezpdf_export_export_structure] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  intro_text NVARCHAR(MAX) NULL,
  modified INT CONSTRAINT [DF_ezpdf_export_modified] DEFAULT NULL NULL,
  modifier_id INT CONSTRAINT [DF_ezpdf_export_modifier_id] DEFAULT NULL NULL,
  pdf_filename NVARCHAR(255) CONSTRAINT [DF_ezpdf_export_pdf_filename] DEFAULT NULL NULL,
  show_frontpage INT CONSTRAINT [DF_ezpdf_export_show_frontpage] DEFAULT NULL NULL,
  site_access NVARCHAR(255) CONSTRAINT [DF_ezpdf_export_site_access] DEFAULT NULL NULL,
  source_node_id INT CONSTRAINT [DF_ezpdf_export_source_node_id] DEFAULT NULL NULL,
  status INT CONSTRAINT [DF_ezpdf_export_status] DEFAULT NULL NULL,
  sub_text NVARCHAR(MAX) NULL,
  title NVARCHAR(255) CONSTRAINT [DF_ezpdf_export_title] DEFAULT NULL NULL,
  version INT CONSTRAINT [DF_ezpdf_export_version] DEFAULT 0 NOT NULL
);
ALTER TABLE ezpdf_export ADD CONSTRAINT [PK_ezpdf_export] PRIMARY KEY (id, version);

CREATE TABLE ezpending_actions (
  id INT IDENTITY(1,1) NOT NULL,
  action NVARCHAR(64) CONSTRAINT [DF_ezpending_actions_action] DEFAULT N'' NOT NULL,
  created INT CONSTRAINT [DF_ezpending_actions_created] DEFAULT NULL NULL,
  param NVARCHAR(MAX) NULL
);
ALTER TABLE ezpending_actions ADD CONSTRAINT [PK_ezpending_actions] PRIMARY KEY (id);
CREATE INDEX [ezpending_actions_action] ON ezpending_actions (action);
CREATE INDEX [ezpending_actions_created] ON ezpending_actions (created);

CREATE TABLE ezpolicy (
  function_name NVARCHAR(255) CONSTRAINT [DF_ezpolicy_function_name] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  module_name NVARCHAR(255) CONSTRAINT [DF_ezpolicy_module_name] DEFAULT NULL NULL,
  original_id INT CONSTRAINT [DF_ezpolicy_original_id] DEFAULT 0 NOT NULL,
  role_id INT CONSTRAINT [DF_ezpolicy_role_id] DEFAULT NULL NULL
);
ALTER TABLE ezpolicy ADD CONSTRAINT [PK_ezpolicy] PRIMARY KEY (id);
CREATE INDEX [ezpolicy_original_id] ON ezpolicy (original_id);
CREATE INDEX [ezpolicy_role_id] ON ezpolicy (role_id);

CREATE TABLE ezpolicy_limitation (
  id INT IDENTITY(1,1) NOT NULL,
  identifier NVARCHAR(255) CONSTRAINT [DF_ezpolicy_limitation_identifier] DEFAULT N'' NOT NULL,
  policy_id INT CONSTRAINT [DF_ezpolicy_limitation_policy_id] DEFAULT NULL NULL
);
ALTER TABLE ezpolicy_limitation ADD CONSTRAINT [PK_ezpolicy_limitation] PRIMARY KEY (id);
CREATE INDEX [policy_id] ON ezpolicy_limitation (policy_id);

CREATE TABLE ezpolicy_limitation_value (
  id INT IDENTITY(1,1) NOT NULL,
  limitation_id INT CONSTRAINT [DF_ezpolicy_limitation_value_limitation_id] DEFAULT NULL NULL,
  value NVARCHAR(255) CONSTRAINT [DF_ezpolicy_limitation_value_value] DEFAULT NULL NULL
);
ALTER TABLE ezpolicy_limitation_value ADD CONSTRAINT [PK_ezpolicy_limitation_value] PRIMARY KEY (id);
CREATE INDEX [ezpolicy_limitation_value_val] ON ezpolicy_limitation_value (value);
CREATE INDEX [ezpolicy_limit_value_limit_id] ON ezpolicy_limitation_value (limitation_id);

CREATE TABLE ezpreferences (
  id INT IDENTITY(1,1) NOT NULL,
  name NVARCHAR(100) CONSTRAINT [DF_ezpreferences_name] DEFAULT NULL NULL,
  user_id INT CONSTRAINT [DF_ezpreferences_user_id] DEFAULT 0 NOT NULL,
  value NVARCHAR(MAX) NULL
);
ALTER TABLE ezpreferences ADD CONSTRAINT [PK_ezpreferences] PRIMARY KEY (id);
CREATE INDEX [ezpreferences_name] ON ezpreferences (name);
CREATE INDEX [ezpreferences_user_id_idx] ON ezpreferences (user_id, name);

CREATE TABLE ezprest_authcode (
  client_id NVARCHAR(200) CONSTRAINT [DF_ezprest_authcode_client_id] DEFAULT N'' NOT NULL,
  code_challenge NVARCHAR(128) CONSTRAINT [DF_ezprest_authcode_code_challenge] DEFAULT NULL NULL,
  code_challenge_method NVARCHAR(10) CONSTRAINT [DF_ezprest_authcode_code_challenge_method] DEFAULT NULL NULL,
  expirytime BIGINT CONSTRAINT [DF_ezprest_authcode_expirytime] DEFAULT 0 NOT NULL,
  id NVARCHAR(200) CONSTRAINT [DF_ezprest_authcode_id] DEFAULT N'' NOT NULL,
  scope NVARCHAR(200) CONSTRAINT [DF_ezprest_authcode_scope] DEFAULT NULL NULL,
  user_id INT CONSTRAINT [DF_ezprest_authcode_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezprest_authcode ADD CONSTRAINT [PK_ezprest_authcode] PRIMARY KEY (id);
CREATE INDEX [authcode_client_id] ON ezprest_authcode (client_id);

CREATE TABLE ezprest_authorized_clients (
  created INT CONSTRAINT [DF_ezprest_authorized_clients_created] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  rest_client_id INT CONSTRAINT [DF_ezprest_authorized_clients_rest_client_id] DEFAULT NULL NULL,
  user_id INT CONSTRAINT [DF_ezprest_authorized_clients_user_id] DEFAULT NULL NULL
);
ALTER TABLE ezprest_authorized_clients ADD CONSTRAINT [PK_ezprest_authorized_clients] PRIMARY KEY (id);
CREATE INDEX [client_user] ON ezprest_authorized_clients (rest_client_id, user_id);

CREATE TABLE ezprest_clients (
  client_id NVARCHAR(200) CONSTRAINT [DF_ezprest_clients_client_id] DEFAULT NULL NULL,
  client_secret NVARCHAR(200) CONSTRAINT [DF_ezprest_clients_client_secret] DEFAULT NULL NULL,
  created INT CONSTRAINT [DF_ezprest_clients_created] DEFAULT 0 NOT NULL,
  description NVARCHAR(MAX) NULL,
  endpoint_uri NVARCHAR(200) CONSTRAINT [DF_ezprest_clients_endpoint_uri] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  name NVARCHAR(100) CONSTRAINT [DF_ezprest_clients_name] DEFAULT NULL NULL,
  owner_id INT CONSTRAINT [DF_ezprest_clients_owner_id] DEFAULT 0 NOT NULL,
  updated INT CONSTRAINT [DF_ezprest_clients_updated] DEFAULT 0 NOT NULL,
  version INT CONSTRAINT [DF_ezprest_clients_version] DEFAULT 0 NOT NULL
);
ALTER TABLE ezprest_clients ADD CONSTRAINT [PK_ezprest_clients] PRIMARY KEY (id);
CREATE UNIQUE INDEX [client_id_unique] ON ezprest_clients (client_id, version);

CREATE TABLE ezprest_token (
  client_id NVARCHAR(200) CONSTRAINT [DF_ezprest_token_client_id] DEFAULT N'' NOT NULL,
  expirytime BIGINT CONSTRAINT [DF_ezprest_token_expirytime] DEFAULT 0 NOT NULL,
  id NVARCHAR(200) CONSTRAINT [DF_ezprest_token_id] DEFAULT N'' NOT NULL,
  refresh_token NVARCHAR(200) CONSTRAINT [DF_ezprest_token_refresh_token] DEFAULT N'' NOT NULL,
  scope NVARCHAR(200) CONSTRAINT [DF_ezprest_token_scope] DEFAULT NULL NULL,
  user_id INT CONSTRAINT [DF_ezprest_token_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezprest_token ADD CONSTRAINT [PK_ezprest_token] PRIMARY KEY (id);
CREATE INDEX [token_client_id] ON ezprest_token (client_id);

CREATE TABLE expapikey (
  created INT CONSTRAINT [DF_expapikey_created] DEFAULT 0 NOT NULL,
  created_by INT CONSTRAINT [DF_expapikey_created_by] DEFAULT 0 NOT NULL,
  expires INT CONSTRAINT [DF_expapikey_expires] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  key_prefix NVARCHAR(40) CONSTRAINT [DF_expapikey_key_prefix] DEFAULT N'' NOT NULL,
  last_ip NVARCHAR(64) CONSTRAINT [DF_expapikey_last_ip] DEFAULT N'' NOT NULL,
  last_used INT CONSTRAINT [DF_expapikey_last_used] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_expapikey_name] DEFAULT N'' NOT NULL,
  revoked INT CONSTRAINT [DF_expapikey_revoked] DEFAULT 0 NOT NULL,
  revoked_by INT CONSTRAINT [DF_expapikey_revoked_by] DEFAULT 0 NOT NULL,
  salt NVARCHAR(64) CONSTRAINT [DF_expapikey_salt] DEFAULT N'' NOT NULL,
  scopes NVARCHAR(255) CONSTRAINT [DF_expapikey_scopes] DEFAULT N'' NOT NULL,
  secret_hash NVARCHAR(128) CONSTRAINT [DF_expapikey_secret_hash] DEFAULT N'' NOT NULL,
  user_id INT CONSTRAINT [DF_expapikey_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE expapikey ADD CONSTRAINT [PK_expapikey] PRIMARY KEY (id);
CREATE UNIQUE INDEX [expapikey_prefix] ON expapikey (key_prefix);
CREATE INDEX [expapikey_user] ON expapikey (user_id);

CREATE TABLE exppasskey (
  aaguid NVARCHAR(36) CONSTRAINT [DF_exppasskey_aaguid] DEFAULT N'' NOT NULL,
  algorithm INT CONSTRAINT [DF_exppasskey_algorithm] DEFAULT 0 NOT NULL,
  backup_eligible INT CONSTRAINT [DF_exppasskey_backup_eligible] DEFAULT 0 NOT NULL,
  backup_state INT CONSTRAINT [DF_exppasskey_backup_state] DEFAULT 0 NOT NULL,
  created INT CONSTRAINT [DF_exppasskey_created] DEFAULT 0 NOT NULL,
  credential_hash NVARCHAR(64) CONSTRAINT [DF_exppasskey_credential_hash] DEFAULT N'' NOT NULL,
  credential_id NVARCHAR(MAX) CONSTRAINT [DF_exppasskey_credential_id] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  last_ip NVARCHAR(64) CONSTRAINT [DF_exppasskey_last_ip] DEFAULT N'' NOT NULL,
  last_used INT CONSTRAINT [DF_exppasskey_last_used] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_exppasskey_name] DEFAULT N'' NOT NULL,
  public_key NVARCHAR(MAX) CONSTRAINT [DF_exppasskey_public_key] DEFAULT NULL NULL,
  sign_count BIGINT CONSTRAINT [DF_exppasskey_sign_count] DEFAULT 0 NOT NULL,
  transports NVARCHAR(255) CONSTRAINT [DF_exppasskey_transports] DEFAULT N'' NOT NULL,
  user_handle NVARCHAR(128) CONSTRAINT [DF_exppasskey_user_handle] DEFAULT N'' NOT NULL,
  user_id INT CONSTRAINT [DF_exppasskey_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE exppasskey ADD CONSTRAINT [PK_exppasskey] PRIMARY KEY (id);
CREATE UNIQUE INDEX [exppasskey_credential] ON exppasskey (credential_hash);
CREATE INDEX [exppasskey_user] ON exppasskey (user_id);

CREATE TABLE exp_transfer_run (
  bytes BIGINT CONSTRAINT [DF_exp_transfer_run_bytes] DEFAULT 0 NOT NULL,
  created INT CONSTRAINT [DF_exp_transfer_run_created] DEFAULT 0 NOT NULL,
  detail NVARCHAR(MAX) NULL,
  detail_url NVARCHAR(255) CONSTRAINT [DF_exp_transfer_run_detail_url] DEFAULT N'' NOT NULL,
  direction NVARCHAR(10) CONSTRAINT [DF_exp_transfer_run_direction] DEFAULT N'' NOT NULL,
  duration INT CONSTRAINT [DF_exp_transfer_run_duration] DEFAULT 0 NOT NULL,
  engine NVARCHAR(16) CONSTRAINT [DF_exp_transfer_run_engine] DEFAULT N'' NOT NULL,
  error NVARCHAR(255) CONSTRAINT [DF_exp_transfer_run_error] DEFAULT N'' NOT NULL,
  file_ref NVARCHAR(255) CONSTRAINT [DF_exp_transfer_run_file_ref] DEFAULT N'' NOT NULL,
  finished INT CONSTRAINT [DF_exp_transfer_run_finished] DEFAULT 0 NOT NULL,
  format NVARCHAR(32) CONSTRAINT [DF_exp_transfer_run_format] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  job_ref NVARCHAR(64) CONSTRAINT [DF_exp_transfer_run_job_ref] DEFAULT N'' NOT NULL,
  kind NVARCHAR(20) CONSTRAINT [DF_exp_transfer_run_kind] DEFAULT N'' NOT NULL,
  modified INT CONSTRAINT [DF_exp_transfer_run_modified] DEFAULT 0 NOT NULL,
  rows_done INT CONSTRAINT [DF_exp_transfer_run_rows_done] DEFAULT 0 NOT NULL,
  rows_failed INT CONSTRAINT [DF_exp_transfer_run_rows_failed] DEFAULT 0 NOT NULL,
  rows_total INT CONSTRAINT [DF_exp_transfer_run_rows_total] DEFAULT 0 NOT NULL,
  scope NVARCHAR(20) CONSTRAINT [DF_exp_transfer_run_scope] DEFAULT N'' NOT NULL,
  scope_ref NVARCHAR(255) CONSTRAINT [DF_exp_transfer_run_scope_ref] DEFAULT N'' NOT NULL,
  siteaccess NVARCHAR(64) CONSTRAINT [DF_exp_transfer_run_siteaccess] DEFAULT N'' NOT NULL,
  started INT CONSTRAINT [DF_exp_transfer_run_started] DEFAULT 0 NOT NULL,
  state NVARCHAR(16) CONSTRAINT [DF_exp_transfer_run_state] DEFAULT N'' NOT NULL,
  title NVARCHAR(255) CONSTRAINT [DF_exp_transfer_run_title] DEFAULT N'' NOT NULL,
  tool NVARCHAR(64) CONSTRAINT [DF_exp_transfer_run_tool] DEFAULT N'' NOT NULL,
  tool_ref NVARCHAR(64) CONSTRAINT [DF_exp_transfer_run_tool_ref] DEFAULT N'' NOT NULL,
  user_id INT CONSTRAINT [DF_exp_transfer_run_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE exp_transfer_run ADD CONSTRAINT [PK_exp_transfer_run] PRIMARY KEY (id);
CREATE INDEX [exp_transfer_run_started] ON exp_transfer_run (started);
CREATE INDEX [exp_transfer_run_tool] ON exp_transfer_run (tool, started);
CREATE INDEX [exp_transfer_run_toolref] ON exp_transfer_run (tool, tool_ref);
CREATE INDEX [exp_transfer_run_user] ON exp_transfer_run (user_id);

CREATE TABLE exp_data_connection (
  created INT CONSTRAINT [DF_exp_data_connection_created] DEFAULT 0 NOT NULL,
  creator_id INT CONSTRAINT [DF_exp_data_connection_creator_id] DEFAULT 0 NOT NULL,
  database_name NVARCHAR(255) CONSTRAINT [DF_exp_data_connection_database_name] DEFAULT N'' NOT NULL,
  description NVARCHAR(255) CONSTRAINT [DF_exp_data_connection_description] DEFAULT N'' NOT NULL,
  engine NVARCHAR(32) CONSTRAINT [DF_exp_data_connection_engine] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  identifier NVARCHAR(64) CONSTRAINT [DF_exp_data_connection_identifier] DEFAULT N'' NOT NULL,
  modified INT CONSTRAINT [DF_exp_data_connection_modified] DEFAULT 0 NOT NULL,
  modifier_id INT CONSTRAINT [DF_exp_data_connection_modifier_id] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_exp_data_connection_name] DEFAULT N'' NOT NULL,
  options NVARCHAR(MAX) NULL,
  port INT CONSTRAINT [DF_exp_data_connection_port] DEFAULT 0 NOT NULL,
  read_only INT CONSTRAINT [DF_exp_data_connection_read_only] DEFAULT 0 NOT NULL,
  secret NVARCHAR(128) CONSTRAINT [DF_exp_data_connection_secret] DEFAULT N'' NOT NULL,
  server NVARCHAR(255) CONSTRAINT [DF_exp_data_connection_server] DEFAULT N'' NOT NULL,
  user_name NVARCHAR(255) CONSTRAINT [DF_exp_data_connection_user_name] DEFAULT N'' NOT NULL
);
ALTER TABLE exp_data_connection ADD CONSTRAINT [PK_exp_data_connection] PRIMARY KEY (id);
CREATE UNIQUE INDEX [exp_data_connection_identifier] ON exp_data_connection (identifier);

CREATE TABLE exp_secret (
  ciphertext NVARCHAR(MAX) NULL,
  created INT CONSTRAINT [DF_exp_secret_created] DEFAULT 0 NOT NULL,
  creator_id INT CONSTRAINT [DF_exp_secret_creator_id] DEFAULT 0 NOT NULL,
  description NVARCHAR(255) CONSTRAINT [DF_exp_secret_description] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  key_id NVARCHAR(16) CONSTRAINT [DF_exp_secret_key_id] DEFAULT N'' NOT NULL,
  modified INT CONSTRAINT [DF_exp_secret_modified] DEFAULT 0 NOT NULL,
  modifier_id INT CONSTRAINT [DF_exp_secret_modifier_id] DEFAULT 0 NOT NULL,
  name NVARCHAR(128) CONSTRAINT [DF_exp_secret_name] DEFAULT N'' NOT NULL,
  scope NVARCHAR(64) CONSTRAINT [DF_exp_secret_scope] DEFAULT N'' NOT NULL
);
ALTER TABLE exp_secret ADD CONSTRAINT [PK_exp_secret] PRIMARY KEY (id);
CREATE UNIQUE INDEX [exp_secret_name] ON exp_secret (name);
CREATE INDEX [exp_secret_scope] ON exp_secret (scope);

CREATE TABLE exp_data_sync (
  class_name NVARCHAR(128) CONSTRAINT [DF_exp_data_sync_class_name] DEFAULT N'' NOT NULL,
  conflict_rule NVARCHAR(16) CONSTRAINT [DF_exp_data_sync_conflict_rule] DEFAULT N'' NOT NULL,
  content_class NVARCHAR(128) CONSTRAINT [DF_exp_data_sync_content_class] DEFAULT N'' NOT NULL,
  created INT CONSTRAINT [DF_exp_data_sync_created] DEFAULT 0 NOT NULL,
  creator_id INT CONSTRAINT [DF_exp_data_sync_creator_id] DEFAULT 0 NOT NULL,
  direction NVARCHAR(16) CONSTRAINT [DF_exp_data_sync_direction] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  interval_seconds INT CONSTRAINT [DF_exp_data_sync_interval_seconds] DEFAULT 0 NOT NULL,
  key_columns NVARCHAR(255) CONSTRAINT [DF_exp_data_sync_key_columns] DEFAULT N'' NOT NULL,
  last_run INT CONSTRAINT [DF_exp_data_sync_last_run] DEFAULT 0 NOT NULL,
  last_run_id INT CONSTRAINT [DF_exp_data_sync_last_run_id] DEFAULT 0 NOT NULL,
  map NVARCHAR(MAX) NULL,
  modified INT CONSTRAINT [DF_exp_data_sync_modified] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_exp_data_sync_name] DEFAULT N'' NOT NULL,
  next_run INT CONSTRAINT [DF_exp_data_sync_next_run] DEFAULT 0 NOT NULL,
  options NVARCHAR(MAX) NULL,
  run_mode NVARCHAR(16) CONSTRAINT [DF_exp_data_sync_run_mode] DEFAULT N'' NOT NULL,
  state NVARCHAR(16) CONSTRAINT [DF_exp_data_sync_state] DEFAULT N'' NOT NULL,
  subtree_node_id INT CONSTRAINT [DF_exp_data_sync_subtree_node_id] DEFAULT 0 NOT NULL
);
ALTER TABLE exp_data_sync ADD CONSTRAINT [PK_exp_data_sync] PRIMARY KEY (id);
CREATE INDEX [exp_data_sync_mode] ON exp_data_sync (run_mode, state);

CREATE TABLE exp_data_sync_conflict (
  content_values NVARCHAR(MAX) NULL,
  detected INT CONSTRAINT [DF_exp_data_sync_conflict_detected] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  link_id INT CONSTRAINT [DF_exp_data_sync_conflict_link_id] DEFAULT 0 NOT NULL,
  object_id INT CONSTRAINT [DF_exp_data_sync_conflict_object_id] DEFAULT 0 NOT NULL,
  resolution NVARCHAR(16) CONSTRAINT [DF_exp_data_sync_conflict_resolution] DEFAULT N'' NOT NULL,
  resolved INT CONSTRAINT [DF_exp_data_sync_conflict_resolved] DEFAULT 0 NOT NULL,
  resolver_id INT CONSTRAINT [DF_exp_data_sync_conflict_resolver_id] DEFAULT 0 NOT NULL,
  row_key NVARCHAR(255) CONSTRAINT [DF_exp_data_sync_conflict_row_key] DEFAULT N'' NOT NULL,
  row_values NVARCHAR(MAX) NULL,
  state NVARCHAR(16) CONSTRAINT [DF_exp_data_sync_conflict_state] DEFAULT N'' NOT NULL,
  sync_id INT CONSTRAINT [DF_exp_data_sync_conflict_sync_id] DEFAULT 0 NOT NULL
);
ALTER TABLE exp_data_sync_conflict ADD CONSTRAINT [PK_exp_data_sync_conflict] PRIMARY KEY (id);
CREATE INDEX [exp_data_sync_conflict_state] ON exp_data_sync_conflict (sync_id, state);

CREATE TABLE exp_data_sync_link (
  content_hash NVARCHAR(32) CONSTRAINT [DF_exp_data_sync_link_content_hash] DEFAULT N'' NOT NULL,
  content_modified INT CONSTRAINT [DF_exp_data_sync_link_content_modified] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  object_id INT CONSTRAINT [DF_exp_data_sync_link_object_id] DEFAULT 0 NOT NULL,
  row_hash NVARCHAR(32) CONSTRAINT [DF_exp_data_sync_link_row_hash] DEFAULT N'' NOT NULL,
  row_key NVARCHAR(255) CONSTRAINT [DF_exp_data_sync_link_row_key] DEFAULT N'' NOT NULL,
  row_modified INT CONSTRAINT [DF_exp_data_sync_link_row_modified] DEFAULT 0 NOT NULL,
  sync_id INT CONSTRAINT [DF_exp_data_sync_link_sync_id] DEFAULT 0 NOT NULL,
  synced INT CONSTRAINT [DF_exp_data_sync_link_synced] DEFAULT 0 NOT NULL
);
ALTER TABLE exp_data_sync_link ADD CONSTRAINT [PK_exp_data_sync_link] PRIMARY KEY (id);
CREATE UNIQUE INDEX [exp_data_sync_link_key] ON exp_data_sync_link (sync_id, row_key);
CREATE INDEX [exp_data_sync_link_object] ON exp_data_sync_link (sync_id, object_id);

CREATE TABLE expuser_session (
  browser NVARCHAR(64) CONSTRAINT [DF_expuser_session_browser] DEFAULT N'' NOT NULL,
  created INT CONSTRAINT [DF_expuser_session_created] DEFAULT 0 NOT NULL,
  device_type NVARCHAR(16) CONSTRAINT [DF_expuser_session_device_type] DEFAULT N'' NOT NULL,
  end_reason NVARCHAR(32) CONSTRAINT [DF_expuser_session_end_reason] DEFAULT N'' NOT NULL,
  ended INT CONSTRAINT [DF_expuser_session_ended] DEFAULT 0 NOT NULL,
  ended_by INT CONSTRAINT [DF_expuser_session_ended_by] DEFAULT 0 NOT NULL,
  first_ip NVARCHAR(64) CONSTRAINT [DF_expuser_session_first_ip] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  last_active INT CONSTRAINT [DF_expuser_session_last_active] DEFAULT 0 NOT NULL,
  last_ip NVARCHAR(64) CONSTRAINT [DF_expuser_session_last_ip] DEFAULT N'' NOT NULL,
  method NVARCHAR(32) CONSTRAINT [DF_expuser_session_method] DEFAULT N'' NOT NULL,
  os NVARCHAR(64) CONSTRAINT [DF_expuser_session_os] DEFAULT N'' NOT NULL,
  remember_until INT CONSTRAINT [DF_expuser_session_remember_until] DEFAULT 0 NOT NULL,
  siteaccess NVARCHAR(64) CONSTRAINT [DF_expuser_session_siteaccess] DEFAULT N'' NOT NULL,
  token_hash NVARCHAR(64) CONSTRAINT [DF_expuser_session_token_hash] DEFAULT N'' NOT NULL,
  user_agent NVARCHAR(512) CONSTRAINT [DF_expuser_session_user_agent] DEFAULT N'' NOT NULL,
  user_id INT CONSTRAINT [DF_expuser_session_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE expuser_session ADD CONSTRAINT [PK_expuser_session] PRIMARY KEY (id);
CREATE UNIQUE INDEX [expuser_session_token] ON expuser_session (token_hash);
CREATE INDEX [expuser_session_user] ON expuser_session (user_id, ended);
CREATE INDEX [expuser_session_active] ON expuser_session (ended, last_active);

CREATE TABLE expuser_session_info (
  auth_detail NVARCHAR(32) CONSTRAINT [DF_expuser_session_info_auth_detail] DEFAULT N'' NOT NULL,
  country NVARCHAR(2) CONSTRAINT [DF_expuser_session_info_country] DEFAULT N'' NOT NULL,
  host NVARCHAR(255) CONSTRAINT [DF_expuser_session_info_host] DEFAULT N'' NOT NULL,
  location NVARCHAR(128) CONSTRAINT [DF_expuser_session_info_location] DEFAULT N'' NOT NULL,
  session_id INT CONSTRAINT [DF_expuser_session_info_session_id] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_expuser_session_info_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE expuser_session_info ADD CONSTRAINT [PK_expuser_session_info] PRIMARY KEY (session_id);
CREATE INDEX [expuser_session_info_user] ON expuser_session_info (user_id);

CREATE TABLE expuser_audit (
  actor_id INT CONSTRAINT [DF_expuser_audit_actor_id] DEFAULT 0 NOT NULL,
  actor_login NVARCHAR(150) CONSTRAINT [DF_expuser_audit_actor_login] DEFAULT N'' NOT NULL,
  client_ip NVARCHAR(64) CONSTRAINT [DF_expuser_audit_client_ip] DEFAULT N'' NOT NULL,
  created INT CONSTRAINT [DF_expuser_audit_created] DEFAULT 0 NOT NULL,
  event_id NVARCHAR(26) CONSTRAINT [DF_expuser_audit_event_id] DEFAULT N'' NOT NULL,
  event_name NVARCHAR(128) CONSTRAINT [DF_expuser_audit_event_name] DEFAULT N'' NOT NULL,
  host NVARCHAR(255) CONSTRAINT [DF_expuser_audit_host] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  module_view NVARCHAR(128) CONSTRAINT [DF_expuser_audit_module_view] DEFAULT N'' NOT NULL,
  reason NVARCHAR(64) CONSTRAINT [DF_expuser_audit_reason] DEFAULT N'' NOT NULL,
  request_id NVARCHAR(40) CONSTRAINT [DF_expuser_audit_request_id] DEFAULT N'' NOT NULL,
  result NVARCHAR(16) CONSTRAINT [DF_expuser_audit_result] DEFAULT N'' NOT NULL,
  siteaccess NVARCHAR(64) CONSTRAINT [DF_expuser_audit_siteaccess] DEFAULT N'' NOT NULL,
  target_id INT CONSTRAINT [DF_expuser_audit_target_id] DEFAULT 0 NOT NULL,
  target_login NVARCHAR(150) CONSTRAINT [DF_expuser_audit_target_login] DEFAULT N'' NOT NULL,
  target_type NVARCHAR(16) CONSTRAINT [DF_expuser_audit_target_type] DEFAULT N'' NOT NULL,
  time_ms BIGINT CONSTRAINT [DF_expuser_audit_time_ms] DEFAULT 0 NOT NULL,
  value_after NVARCHAR(MAX) CONSTRAINT [DF_expuser_audit_value_after] DEFAULT NULL NULL,
  value_before NVARCHAR(MAX) CONSTRAINT [DF_expuser_audit_value_before] DEFAULT NULL NULL
);
ALTER TABLE expuser_audit ADD CONSTRAINT [PK_expuser_audit] PRIMARY KEY (id);
CREATE UNIQUE INDEX [expuser_audit_event] ON expuser_audit (event_id);
CREATE INDEX [expuser_audit_target] ON expuser_audit (target_id, created);
CREATE INDEX [expuser_audit_actor] ON expuser_audit (actor_id, created);
CREATE INDEX [expuser_audit_name] ON expuser_audit (event_name, created);
CREATE INDEX [expuser_audit_created] ON expuser_audit (created);

CREATE TABLE exphelp_help_id (
  help_id NVARCHAR(80) CONSTRAINT [DF_exphelp_help_id_help_id] DEFAULT N'' NOT NULL,
  object_id INT CONSTRAINT [DF_exphelp_help_id_object_id] DEFAULT 0 NOT NULL,
  released INT CONSTRAINT [DF_exphelp_help_id_released] DEFAULT 0 NOT NULL
);
ALTER TABLE exphelp_help_id ADD CONSTRAINT [PK_exphelp_help_id] PRIMARY KEY (help_id);
CREATE INDEX [exphelp_help_id_object] ON exphelp_help_id (object_id);

CREATE TABLE exphelp_feedback (
  created INT CONSTRAINT [DF_exphelp_feedback_created] DEFAULT 0 NOT NULL,
  help_id NVARCHAR(80) CONSTRAINT [DF_exphelp_feedback_help_id] DEFAULT N'' NOT NULL,
  helpful INT CONSTRAINT [DF_exphelp_feedback_helpful] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  language NVARCHAR(20) CONSTRAINT [DF_exphelp_feedback_language] DEFAULT N'' NOT NULL,
  object_id INT CONSTRAINT [DF_exphelp_feedback_object_id] DEFAULT 0 NOT NULL,
  reason NVARCHAR(32) CONSTRAINT [DF_exphelp_feedback_reason] DEFAULT N'' NOT NULL
);
ALTER TABLE exphelp_feedback ADD CONSTRAINT [PK_exphelp_feedback] PRIMARY KEY (id);
CREATE INDEX [exphelp_feedback_object] ON exphelp_feedback (object_id, created);
CREATE INDEX [exphelp_feedback_created] ON exphelp_feedback (created);

CREATE TABLE exp_migration (
  caption_id NVARCHAR(150) CONSTRAINT [DF_exp_migration_caption_id] DEFAULT N'' NOT NULL,
  checksum NVARCHAR(64) CONSTRAINT [DF_exp_migration_checksum] DEFAULT N'' NOT NULL,
  duration_ms INT CONSTRAINT [DF_exp_migration_duration_ms] DEFAULT 0 NOT NULL,
  engine NVARCHAR(30) CONSTRAINT [DF_exp_migration_engine] DEFAULT N'' NOT NULL,
  environment NVARCHAR(50) CONSTRAINT [DF_exp_migration_environment] DEFAULT N'' NOT NULL,
  file_path NVARCHAR(255) CONSTRAINT [DF_exp_migration_file_path] DEFAULT N'' NOT NULL,
  finished_at INT CONSTRAINT [DF_exp_migration_finished_at] DEFAULT 0 NOT NULL,
  message NVARCHAR(1000) CONSTRAINT [DF_exp_migration_message] DEFAULT N'' NOT NULL,
  migration_id NVARCHAR(150) CONSTRAINT [DF_exp_migration_migration_id] DEFAULT N'' NOT NULL,
  revision NVARCHAR(64) CONSTRAINT [DF_exp_migration_revision] DEFAULT N'' NOT NULL,
  run_by NVARCHAR(150) CONSTRAINT [DF_exp_migration_run_by] DEFAULT N'' NOT NULL,
  run_id NVARCHAR(64) CONSTRAINT [DF_exp_migration_run_id] DEFAULT N'' NOT NULL,
  source NVARCHAR(100) CONSTRAINT [DF_exp_migration_source] DEFAULT N'' NOT NULL,
  stage NVARCHAR(20) CONSTRAINT [DF_exp_migration_stage] DEFAULT N'' NOT NULL,
  started_at INT CONSTRAINT [DF_exp_migration_started_at] DEFAULT 0 NOT NULL,
  state NVARCHAR(20) CONSTRAINT [DF_exp_migration_state] DEFAULT N'' NOT NULL,
  version NVARCHAR(30) CONSTRAINT [DF_exp_migration_version] DEFAULT N'' NOT NULL
);
ALTER TABLE exp_migration ADD CONSTRAINT [PK_exp_migration] PRIMARY KEY (source, migration_id);
CREATE INDEX [exp_migration_run_id] ON exp_migration (run_id);

CREATE TABLE exp_migration_deploy (
  deploy_id NVARCHAR(64) CONSTRAINT [DF_exp_migration_deploy_deploy_id] DEFAULT N'' NOT NULL,
  finished_at INT CONSTRAINT [DF_exp_migration_deploy_finished_at] DEFAULT 0 NOT NULL,
  in_sync INT CONSTRAINT [DF_exp_migration_deploy_in_sync] DEFAULT 0 NOT NULL,
  message NVARCHAR(1000) CONSTRAINT [DF_exp_migration_deploy_message] DEFAULT N'' NOT NULL,
  mode NVARCHAR(20) CONSTRAINT [DF_exp_migration_deploy_mode] DEFAULT N'' NOT NULL,
  next_stage INT CONSTRAINT [DF_exp_migration_deploy_next_stage] DEFAULT 0 NOT NULL,
  run_by NVARCHAR(150) CONSTRAINT [DF_exp_migration_deploy_run_by] DEFAULT N'' NOT NULL,
  stages INT CONSTRAINT [DF_exp_migration_deploy_stages] DEFAULT 0 NOT NULL,
  started_at INT CONSTRAINT [DF_exp_migration_deploy_started_at] DEFAULT 0 NOT NULL,
  state NVARCHAR(20) CONSTRAINT [DF_exp_migration_deploy_state] DEFAULT N'' NOT NULL
);
ALTER TABLE exp_migration_deploy ADD CONSTRAINT [PK_exp_migration_deploy] PRIMARY KEY (deploy_id);
CREATE INDEX [exp_migration_deploy_started] ON exp_migration_deploy (started_at);

CREATE TABLE exp_migration_deploy_host (
  address NVARCHAR(255) CONSTRAINT [DF_exp_migration_deploy_host_address] DEFAULT N'' NOT NULL,
  applied INT CONSTRAINT [DF_exp_migration_deploy_host_applied] DEFAULT 0 NOT NULL,
  caption NVARCHAR(150) CONSTRAINT [DF_exp_migration_deploy_host_caption] DEFAULT N'' NOT NULL,
  deploy_id NVARCHAR(64) CONSTRAINT [DF_exp_migration_deploy_host_deploy_id] DEFAULT N'' NOT NULL,
  environment NVARCHAR(50) CONSTRAINT [DF_exp_migration_deploy_host_environment] DEFAULT N'' NOT NULL,
  message NVARCHAR(1000) CONSTRAINT [DF_exp_migration_deploy_host_message] DEFAULT N'' NOT NULL,
  remote_id NVARCHAR(64) CONSTRAINT [DF_exp_migration_deploy_host_remote_id] DEFAULT N'' NOT NULL,
  stage INT CONSTRAINT [DF_exp_migration_deploy_host_stage] DEFAULT 0 NOT NULL,
  state NVARCHAR(20) CONSTRAINT [DF_exp_migration_deploy_host_state] DEFAULT N'' NOT NULL,
  transport NVARCHAR(20) CONSTRAINT [DF_exp_migration_deploy_host_transport] DEFAULT N'' NOT NULL
);
ALTER TABLE exp_migration_deploy_host ADD CONSTRAINT [PK_exp_migration_deploy_host] PRIMARY KEY (deploy_id, remote_id);
CREATE INDEX [exp_migration_deploy_host_remote] ON exp_migration_deploy_host (remote_id);

CREATE TABLE exp_migration_run (
  caption_post NVARCHAR(150) CONSTRAINT [DF_exp_migration_run_caption_post] DEFAULT N'' NOT NULL,
  caption_pre NVARCHAR(150) CONSTRAINT [DF_exp_migration_run_caption_pre] DEFAULT N'' NOT NULL,
  commit_id NVARCHAR(64) CONSTRAINT [DF_exp_migration_run_commit_id] DEFAULT N'' NOT NULL,
  environment NVARCHAR(50) CONSTRAINT [DF_exp_migration_run_environment] DEFAULT N'' NOT NULL,
  finished_at INT CONSTRAINT [DF_exp_migration_run_finished_at] DEFAULT 0 NOT NULL,
  log_file NVARCHAR(255) CONSTRAINT [DF_exp_migration_run_log_file] DEFAULT N'' NOT NULL,
  message NVARCHAR(1000) CONSTRAINT [DF_exp_migration_run_message] DEFAULT N'' NOT NULL,
  push_state NVARCHAR(20) CONSTRAINT [DF_exp_migration_run_push_state] DEFAULT N'' NOT NULL,
  run_by NVARCHAR(150) CONSTRAINT [DF_exp_migration_run_run_by] DEFAULT N'' NOT NULL,
  run_id NVARCHAR(64) CONSTRAINT [DF_exp_migration_run_run_id] DEFAULT N'' NOT NULL,
  started_at INT CONSTRAINT [DF_exp_migration_run_started_at] DEFAULT 0 NOT NULL,
  state NVARCHAR(20) CONSTRAINT [DF_exp_migration_run_state] DEFAULT N'' NOT NULL,
  tag_name NVARCHAR(150) CONSTRAINT [DF_exp_migration_run_tag_name] DEFAULT N'' NOT NULL
);
ALTER TABLE exp_migration_run ADD CONSTRAINT [PK_exp_migration_run] PRIMARY KEY (run_id);
CREATE INDEX [exp_migration_run_started] ON exp_migration_run (started_at);

CREATE TABLE exp_static_page (
  built_at INT CONSTRAINT [DF_exp_static_page_built_at] DEFAULT 0 NOT NULL,
  hash NVARCHAR(64) CONSTRAINT [DF_exp_static_page_hash] DEFAULT N'' NOT NULL,
  node_id INT CONSTRAINT [DF_exp_static_page_node_id] DEFAULT 0 NOT NULL,
  page_bytes INT CONSTRAINT [DF_exp_static_page_page_bytes] DEFAULT 0 NOT NULL,
  path NVARCHAR(255) CONSTRAINT [DF_exp_static_page_path] DEFAULT N'' NOT NULL,
  stale INT CONSTRAINT [DF_exp_static_page_stale] DEFAULT 0 NOT NULL,
  target NVARCHAR(50) CONSTRAINT [DF_exp_static_page_target] DEFAULT N'' NOT NULL
);
ALTER TABLE exp_static_page ADD CONSTRAINT [PK_exp_static_page] PRIMARY KEY (target, path);
CREATE INDEX [exp_static_page_node] ON exp_static_page (node_id);

CREATE TABLE exp_static_run (
  added INT CONSTRAINT [DF_exp_static_run_added] DEFAULT 0 NOT NULL,
  bytes INT CONSTRAINT [DF_exp_static_run_bytes] DEFAULT 0 NOT NULL,
  changed INT CONSTRAINT [DF_exp_static_run_changed] DEFAULT 0 NOT NULL,
  commit_id NVARCHAR(64) CONSTRAINT [DF_exp_static_run_commit_id] DEFAULT N'' NOT NULL,
  files INT CONSTRAINT [DF_exp_static_run_files] DEFAULT 0 NOT NULL,
  finished_at INT CONSTRAINT [DF_exp_static_run_finished_at] DEFAULT 0 NOT NULL,
  kind NVARCHAR(20) CONSTRAINT [DF_exp_static_run_kind] DEFAULT N'' NOT NULL,
  message NVARCHAR(1000) CONSTRAINT [DF_exp_static_run_message] DEFAULT N'' NOT NULL,
  pages INT CONSTRAINT [DF_exp_static_run_pages] DEFAULT 0 NOT NULL,
  push_state NVARCHAR(20) CONSTRAINT [DF_exp_static_run_push_state] DEFAULT N'' NOT NULL,
  removed INT CONSTRAINT [DF_exp_static_run_removed] DEFAULT 0 NOT NULL,
  run_by NVARCHAR(150) CONSTRAINT [DF_exp_static_run_run_by] DEFAULT N'' NOT NULL,
  run_id NVARCHAR(64) CONSTRAINT [DF_exp_static_run_run_id] DEFAULT N'' NOT NULL,
  started_at INT CONSTRAINT [DF_exp_static_run_started_at] DEFAULT 0 NOT NULL,
  state NVARCHAR(20) CONSTRAINT [DF_exp_static_run_state] DEFAULT N'' NOT NULL,
  tag_name NVARCHAR(150) CONSTRAINT [DF_exp_static_run_tag_name] DEFAULT N'' NOT NULL,
  target NVARCHAR(50) CONSTRAINT [DF_exp_static_run_target] DEFAULT N'' NOT NULL
);
ALTER TABLE exp_static_run ADD CONSTRAINT [PK_exp_static_run] PRIMARY KEY (run_id);
CREATE INDEX [exp_static_run_target] ON exp_static_run (target, started_at);

CREATE TABLE exp_migration_recording (
  environment NVARCHAR(50) CONSTRAINT [DF_exp_migration_recording_environment] DEFAULT N'' NOT NULL,
  event_count INT CONSTRAINT [DF_exp_migration_recording_event_count] DEFAULT 0 NOT NULL,
  kind NVARCHAR(20) CONSTRAINT [DF_exp_migration_recording_kind] DEFAULT N'' NOT NULL,
  label NVARCHAR(255) CONSTRAINT [DF_exp_migration_recording_label] DEFAULT N'' NOT NULL,
  message NVARCHAR(1000) CONSTRAINT [DF_exp_migration_recording_message] DEFAULT N'' NOT NULL,
  recording_id NVARCHAR(64) CONSTRAINT [DF_exp_migration_recording_recording_id] DEFAULT N'' NOT NULL,
  started_at INT CONSTRAINT [DF_exp_migration_recording_started_at] DEFAULT 0 NOT NULL,
  state NVARCHAR(20) CONSTRAINT [DF_exp_migration_recording_state] DEFAULT N'' NOT NULL,
  stopped_at INT CONSTRAINT [DF_exp_migration_recording_stopped_at] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_exp_migration_recording_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE exp_migration_recording ADD CONSTRAINT [PK_exp_migration_recording] PRIMARY KEY (recording_id);
CREATE INDEX [exp_migration_recording_state] ON exp_migration_recording (state);

CREATE TABLE exp_migration_recording_event (
  actor_id INT CONSTRAINT [DF_exp_migration_recording_event_actor_id] DEFAULT 0 NOT NULL,
  audit_name NVARCHAR(100) CONSTRAINT [DF_exp_migration_recording_event_audit_name] DEFAULT N'' NOT NULL,
  event_id NVARCHAR(32) CONSTRAINT [DF_exp_migration_recording_event_event_id] DEFAULT N'' NOT NULL,
  payload NVARCHAR(MAX) CONSTRAINT [DF_exp_migration_recording_event_payload] DEFAULT NULL NULL,
  recorded_at INT CONSTRAINT [DF_exp_migration_recording_event_recorded_at] DEFAULT 0 NOT NULL,
  recording_id NVARCHAR(64) CONSTRAINT [DF_exp_migration_recording_event_recording_id] DEFAULT N'' NOT NULL,
  step_mode NVARCHAR(20) CONSTRAINT [DF_exp_migration_recording_event_step_mode] DEFAULT N'' NOT NULL,
  step_type NVARCHAR(50) CONSTRAINT [DF_exp_migration_recording_event_step_type] DEFAULT N'' NOT NULL,
  subject NVARCHAR(255) CONSTRAINT [DF_exp_migration_recording_event_subject] DEFAULT N'' NOT NULL
);
ALTER TABLE exp_migration_recording_event ADD CONSTRAINT [PK_exp_migration_recording_event] PRIMARY KEY (recording_id, event_id);

CREATE TABLE ezproductcategory (
  id INT IDENTITY(1,1) NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezproductcategory_name] DEFAULT N'' NOT NULL
);
ALTER TABLE ezproductcategory ADD CONSTRAINT [PK_ezproductcategory] PRIMARY KEY (id);

CREATE TABLE ezproductcollection (
  created INT CONSTRAINT [DF_ezproductcollection_created] DEFAULT NULL NULL,
  currency_code NVARCHAR(4) CONSTRAINT [DF_ezproductcollection_currency_code] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL
);
ALTER TABLE ezproductcollection ADD CONSTRAINT [PK_ezproductcollection] PRIMARY KEY (id);

CREATE TABLE ezproductcollection_item (
  contentobject_id INT CONSTRAINT [DF_ezproductcollection_item_contentobject_id] DEFAULT 0 NOT NULL,
  discount REAL CONSTRAINT [DF_ezproductcollection_item_discount] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  is_vat_inc INT CONSTRAINT [DF_ezproductcollection_item_is_vat_inc] DEFAULT NULL NULL,
  item_count INT CONSTRAINT [DF_ezproductcollection_item_item_count] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezproductcollection_item_name] DEFAULT N'' NOT NULL,
  price REAL CONSTRAINT [DF_ezproductcollection_item_price] DEFAULT 0 NULL,
  productcollection_id INT CONSTRAINT [DF_ezproductcollection_item_productcollection_id] DEFAULT 0 NOT NULL,
  vat_value REAL CONSTRAINT [DF_ezproductcollection_item_vat_value] DEFAULT NULL NULL
);
ALTER TABLE ezproductcollection_item ADD CONSTRAINT [PK_ezproductcollection_item] PRIMARY KEY (id);
CREATE INDEX [ezproductcollection_item_contentobject_id] ON ezproductcollection_item (contentobject_id);
CREATE INDEX [ezproductcollection_item_productcollection_id] ON ezproductcollection_item (productcollection_id);

CREATE TABLE ezproductcollection_item_opt (
  id INT IDENTITY(1,1) NOT NULL,
  item_id INT CONSTRAINT [DF_ezproductcollection_item_opt_item_id] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezproductcollection_item_opt_name] DEFAULT N'' NOT NULL,
  object_attribute_id INT CONSTRAINT [DF_ezproductcollection_item_opt_object_attribute_id] DEFAULT NULL NULL,
  option_item_id INT CONSTRAINT [DF_ezproductcollection_item_opt_option_item_id] DEFAULT 0 NOT NULL,
  price REAL CONSTRAINT [DF_ezproductcollection_item_opt_price] DEFAULT 0 NOT NULL,
  value NVARCHAR(255) CONSTRAINT [DF_ezproductcollection_item_opt_value] DEFAULT N'' NOT NULL
);
ALTER TABLE ezproductcollection_item_opt ADD CONSTRAINT [PK_ezproductcollection_item_opt] PRIMARY KEY (id);
CREATE INDEX [ezproductcollection_item_opt_item_id] ON ezproductcollection_item_opt (item_id);

CREATE TABLE ezpublishingqueueprocesses (
  created INT CONSTRAINT [DF_ezpublishingqueueprocesses_created] DEFAULT NULL NULL,
  ezcontentobject_version_id INT CONSTRAINT [DF_ezpublishingqueueprocesses_ezcontentobject_version_id] DEFAULT 0 NOT NULL,
  finished INT CONSTRAINT [DF_ezpublishingqueueprocesses_finished] DEFAULT NULL NULL,
  pid INT CONSTRAINT [DF_ezpublishingqueueprocesses_pid] DEFAULT NULL NULL,
  started INT CONSTRAINT [DF_ezpublishingqueueprocesses_started] DEFAULT NULL NULL,
  status INT CONSTRAINT [DF_ezpublishingqueueprocesses_status] DEFAULT NULL NULL
);
ALTER TABLE ezpublishingqueueprocesses ADD CONSTRAINT [PK_ezpublishingqueueprocesses] PRIMARY KEY (ezcontentobject_version_id);

CREATE TABLE ezrole (
  id INT IDENTITY(1,1) NOT NULL,
  is_new INT CONSTRAINT [DF_ezrole_is_new] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezrole_name] DEFAULT N'' NOT NULL,
  value NCHAR(1) CONSTRAINT [DF_ezrole_value] DEFAULT NULL NULL,
  version INT CONSTRAINT [DF_ezrole_version] DEFAULT 0 NULL
);
ALTER TABLE ezrole ADD CONSTRAINT [PK_ezrole] PRIMARY KEY (id);

CREATE TABLE ezrss_export (
  access_url NVARCHAR(255) CONSTRAINT [DF_ezrss_export_access_url] DEFAULT NULL NULL,
  opml_head NVARCHAR(MAX) NULL,
  podcast_head NVARCHAR(MAX) NULL,
  active INT CONSTRAINT [DF_ezrss_export_active] DEFAULT NULL NULL,
  created INT CONSTRAINT [DF_ezrss_export_created] DEFAULT NULL NULL,
  creator_id INT CONSTRAINT [DF_ezrss_export_creator_id] DEFAULT NULL NULL,
  description NVARCHAR(MAX) NULL,
  id INT IDENTITY(1,1) NOT NULL,
  image_id INT CONSTRAINT [DF_ezrss_export_image_id] DEFAULT NULL NULL,
  main_node_only INT CONSTRAINT [DF_ezrss_export_main_node_only] DEFAULT 1 NOT NULL,
  modified INT CONSTRAINT [DF_ezrss_export_modified] DEFAULT NULL NULL,
  modifier_id INT CONSTRAINT [DF_ezrss_export_modifier_id] DEFAULT NULL NULL,
  node_id INT CONSTRAINT [DF_ezrss_export_node_id] DEFAULT NULL NULL,
  number_of_objects INT CONSTRAINT [DF_ezrss_export_number_of_objects] DEFAULT 0 NOT NULL,
  rss_version NVARCHAR(255) CONSTRAINT [DF_ezrss_export_rss_version] DEFAULT NULL NULL,
  site_access NVARCHAR(255) CONSTRAINT [DF_ezrss_export_site_access] DEFAULT NULL NULL,
  status INT CONSTRAINT [DF_ezrss_export_status] DEFAULT 0 NOT NULL,
  title NVARCHAR(255) CONSTRAINT [DF_ezrss_export_title] DEFAULT NULL NULL,
  url NVARCHAR(255) CONSTRAINT [DF_ezrss_export_url] DEFAULT NULL NULL
);
ALTER TABLE ezrss_export ADD CONSTRAINT [PK_ezrss_export] PRIMARY KEY (id, status);

CREATE TABLE ezrss_export_item (
  category NVARCHAR(255) CONSTRAINT [DF_ezrss_export_item_category] DEFAULT NULL NULL,
  class_id INT CONSTRAINT [DF_ezrss_export_item_class_id] DEFAULT NULL NULL,
  description NVARCHAR(255) CONSTRAINT [DF_ezrss_export_item_description] DEFAULT NULL NULL,
  enclosure NVARCHAR(255) CONSTRAINT [DF_ezrss_export_item_enclosure] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  rssexport_id INT CONSTRAINT [DF_ezrss_export_item_rssexport_id] DEFAULT NULL NULL,
  source_node_id INT CONSTRAINT [DF_ezrss_export_item_source_node_id] DEFAULT NULL NULL,
  status INT CONSTRAINT [DF_ezrss_export_item_status] DEFAULT 0 NOT NULL,
  subnodes INT CONSTRAINT [DF_ezrss_export_item_subnodes] DEFAULT 0 NOT NULL,
  title NVARCHAR(255) CONSTRAINT [DF_ezrss_export_item_title] DEFAULT NULL NULL
);
ALTER TABLE ezrss_export_item ADD CONSTRAINT [PK_ezrss_export_item] PRIMARY KEY (id, status);
CREATE INDEX [ezrss_export_rsseid] ON ezrss_export_item (rssexport_id);

CREATE TABLE ezrss_export_opml_item (
  category NVARCHAR(255) CONSTRAINT [DF_ezrss_export_opml_item_category] DEFAULT NULL NULL,
  created INT CONSTRAINT [DF_ezrss_export_opml_item_created] DEFAULT 0 NOT NULL,
  description NVARCHAR(255) CONSTRAINT [DF_ezrss_export_opml_item_description] DEFAULT NULL NULL,
  html_url NVARCHAR(255) CONSTRAINT [DF_ezrss_export_opml_item_html_url] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  is_breakpoint INT CONSTRAINT [DF_ezrss_export_opml_item_is_breakpoint] DEFAULT 0 NOT NULL,
  is_comment INT CONSTRAINT [DF_ezrss_export_opml_item_is_comment] DEFAULT 0 NOT NULL,
  language NVARCHAR(50) CONSTRAINT [DF_ezrss_export_opml_item_language] DEFAULT NULL NULL,
  outline_text NVARCHAR(255) CONSTRAINT [DF_ezrss_export_opml_item_outline_text] DEFAULT NULL NULL,
  outline_type NVARCHAR(50) CONSTRAINT [DF_ezrss_export_opml_item_outline_type] DEFAULT N'rss' NULL,
  parent_id INT CONSTRAINT [DF_ezrss_export_opml_item_parent_id] DEFAULT 0 NOT NULL,
  priority INT CONSTRAINT [DF_ezrss_export_opml_item_priority] DEFAULT 0 NOT NULL,
  rssexport_id INT CONSTRAINT [DF_ezrss_export_opml_item_rssexport_id] DEFAULT 0 NOT NULL,
  source_node_id INT CONSTRAINT [DF_ezrss_export_opml_item_source_node_id] DEFAULT 0 NOT NULL,
  status INT CONSTRAINT [DF_ezrss_export_opml_item_status] DEFAULT 0 NOT NULL,
  subnodes INT CONSTRAINT [DF_ezrss_export_opml_item_subnodes] DEFAULT 0 NOT NULL,
  target_export_id INT CONSTRAINT [DF_ezrss_export_opml_item_target_export_id] DEFAULT 0 NOT NULL,
  title NVARCHAR(255) CONSTRAINT [DF_ezrss_export_opml_item_title] DEFAULT NULL NULL,
  url NVARCHAR(255) CONSTRAINT [DF_ezrss_export_opml_item_url] DEFAULT NULL NULL,
  xml_url NVARCHAR(255) CONSTRAINT [DF_ezrss_export_opml_item_xml_url] DEFAULT NULL NULL
);
ALTER TABLE ezrss_export_opml_item ADD CONSTRAINT [PK_ezrss_export_opml_item] PRIMARY KEY (id, status);
CREATE INDEX [ezrss_export_opml_rsseid] ON ezrss_export_opml_item (rssexport_id);

CREATE TABLE ezrss_import (
  active INT CONSTRAINT [DF_ezrss_import_active] DEFAULT NULL NULL,
  class_description NVARCHAR(255) CONSTRAINT [DF_ezrss_import_class_description] DEFAULT NULL NULL,
  class_id INT CONSTRAINT [DF_ezrss_import_class_id] DEFAULT NULL NULL,
  class_title NVARCHAR(255) CONSTRAINT [DF_ezrss_import_class_title] DEFAULT NULL NULL,
  class_url NVARCHAR(255) CONSTRAINT [DF_ezrss_import_class_url] DEFAULT NULL NULL,
  created INT CONSTRAINT [DF_ezrss_import_created] DEFAULT NULL NULL,
  creator_id INT CONSTRAINT [DF_ezrss_import_creator_id] DEFAULT NULL NULL,
  destination_node_id INT CONSTRAINT [DF_ezrss_import_destination_node_id] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  import_description NVARCHAR(MAX) NOT NULL,
  modified INT CONSTRAINT [DF_ezrss_import_modified] DEFAULT NULL NULL,
  modifier_id INT CONSTRAINT [DF_ezrss_import_modifier_id] DEFAULT NULL NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezrss_import_name] DEFAULT NULL NULL,
  object_owner_id INT CONSTRAINT [DF_ezrss_import_object_owner_id] DEFAULT NULL NULL,
  status INT CONSTRAINT [DF_ezrss_import_status] DEFAULT 0 NOT NULL,
  url NVARCHAR(MAX) NULL
);
ALTER TABLE ezrss_import ADD CONSTRAINT [PK_ezrss_import] PRIMARY KEY (id, status);

CREATE TABLE ezscheduled_script (
  command NVARCHAR(255) CONSTRAINT [DF_ezscheduled_script_command] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  last_report_timestamp INT CONSTRAINT [DF_ezscheduled_script_last_report_timestamp] DEFAULT 0 NOT NULL,
  name NVARCHAR(50) CONSTRAINT [DF_ezscheduled_script_name] DEFAULT N'' NOT NULL,
  process_id INT CONSTRAINT [DF_ezscheduled_script_process_id] DEFAULT 0 NOT NULL,
  progress INT CONSTRAINT [DF_ezscheduled_script_progress] DEFAULT 0 NULL,
  user_id INT CONSTRAINT [DF_ezscheduled_script_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezscheduled_script ADD CONSTRAINT [PK_ezscheduled_script] PRIMARY KEY (id);
CREATE INDEX [ezscheduled_script_timestamp] ON ezscheduled_script (last_report_timestamp);

CREATE TABLE ezsearch_object_word_link (
  contentclass_attribute_id INT CONSTRAINT [DF_ezsearch_object_word_link_contentclass_attribute_id] DEFAULT 0 NOT NULL,
  contentclass_id INT CONSTRAINT [DF_ezsearch_object_word_link_contentclass_id] DEFAULT 0 NOT NULL,
  contentobject_id INT CONSTRAINT [DF_ezsearch_object_word_link_contentobject_id] DEFAULT 0 NOT NULL,
  frequency REAL CONSTRAINT [DF_ezsearch_object_word_link_frequency] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  identifier NVARCHAR(255) CONSTRAINT [DF_ezsearch_object_word_link_identifier] DEFAULT N'' NOT NULL,
  integer_value INT CONSTRAINT [DF_ezsearch_object_word_link_integer_value] DEFAULT 0 NOT NULL,
  next_word_id INT CONSTRAINT [DF_ezsearch_object_word_link_next_word_id] DEFAULT 0 NOT NULL,
  placement INT CONSTRAINT [DF_ezsearch_object_word_link_placement] DEFAULT 0 NOT NULL,
  prev_word_id INT CONSTRAINT [DF_ezsearch_object_word_link_prev_word_id] DEFAULT 0 NOT NULL,
  published INT CONSTRAINT [DF_ezsearch_object_word_link_published] DEFAULT 0 NOT NULL,
  section_id INT CONSTRAINT [DF_ezsearch_object_word_link_section_id] DEFAULT 0 NOT NULL,
  word_id INT CONSTRAINT [DF_ezsearch_object_word_link_word_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezsearch_object_word_link ADD CONSTRAINT [PK_ezsearch_object_word_link] PRIMARY KEY (id);
CREATE INDEX [ezsearch_object_word_link_frequency] ON ezsearch_object_word_link (frequency);
CREATE INDEX [ezsearch_object_word_link_identifier] ON ezsearch_object_word_link (identifier);
CREATE INDEX [ezsearch_object_word_link_integer_value] ON ezsearch_object_word_link (integer_value);
CREATE INDEX [ezsearch_object_word_link_object] ON ezsearch_object_word_link (contentobject_id);
CREATE INDEX [ezsearch_object_word_link_word] ON ezsearch_object_word_link (word_id);

CREATE TABLE ezsearch_search_phrase (
  id INT IDENTITY(1,1) NOT NULL,
  phrase NVARCHAR(250) CONSTRAINT [DF_ezsearch_search_phrase_phrase] DEFAULT NULL NULL,
  phrase_count INT CONSTRAINT [DF_ezsearch_search_phrase_phrase_count] DEFAULT 0 NULL,
  result_count INT CONSTRAINT [DF_ezsearch_search_phrase_result_count] DEFAULT 0 NULL
);
ALTER TABLE ezsearch_search_phrase ADD CONSTRAINT [PK_ezsearch_search_phrase] PRIMARY KEY (id);
CREATE INDEX [ezsearch_search_phrase_count] ON ezsearch_search_phrase (phrase_count);
CREATE UNIQUE INDEX [ezsearch_search_phrase_phrase] ON ezsearch_search_phrase (phrase);

CREATE TABLE ezsearch_word (
  id INT IDENTITY(1,1) NOT NULL,
  object_count INT CONSTRAINT [DF_ezsearch_word_object_count] DEFAULT 0 NOT NULL,
  word NVARCHAR(150) CONSTRAINT [DF_ezsearch_word_word] DEFAULT NULL NULL
);
ALTER TABLE ezsearch_word ADD CONSTRAINT [PK_ezsearch_word] PRIMARY KEY (id);
CREATE INDEX [ezsearch_word_obj_count] ON ezsearch_word (object_count);
CREATE INDEX [ezsearch_word_word_i] ON ezsearch_word (word);

CREATE TABLE ezsection (
  id INT IDENTITY(1,1) NOT NULL,
  identifier NVARCHAR(255) CONSTRAINT [DF_ezsection_identifier] DEFAULT NULL NULL,
  locale NVARCHAR(255) CONSTRAINT [DF_ezsection_locale] DEFAULT NULL NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezsection_name] DEFAULT NULL NULL,
  navigation_part_identifier NVARCHAR(100) CONSTRAINT [DF_ezsection_navigation_part_identifier] DEFAULT N'ezcontentnavigationpart' NULL
);
ALTER TABLE ezsection ADD CONSTRAINT [PK_ezsection] PRIMARY KEY (id);

CREATE TABLE ezsession (
  data NVARCHAR(MAX) NOT NULL,
  expiration_time INT CONSTRAINT [DF_ezsession_expiration_time] DEFAULT 0 NOT NULL,
  session_key NVARCHAR(32) CONSTRAINT [DF_ezsession_session_key] DEFAULT N'' NOT NULL,
  user_hash NVARCHAR(32) CONSTRAINT [DF_ezsession_user_hash] DEFAULT N'' NOT NULL,
  user_id INT CONSTRAINT [DF_ezsession_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezsession ADD CONSTRAINT [PK_ezsession] PRIMARY KEY (session_key);
CREATE INDEX [expiration_time] ON ezsession (expiration_time);
CREATE INDEX [ezsession_user_id] ON ezsession (user_id);

CREATE TABLE ezsite_data (
  name NVARCHAR(60) CONSTRAINT [DF_ezsite_data_name] DEFAULT N'' NOT NULL,
  value NVARCHAR(MAX) NOT NULL
);
ALTER TABLE ezsite_data ADD CONSTRAINT [PK_ezsite_data] PRIMARY KEY (name);

CREATE TABLE ezsubtree_notification_rule (
  id INT IDENTITY(1,1) NOT NULL,
  node_id INT CONSTRAINT [DF_ezsubtree_notification_rule_node_id] DEFAULT 0 NOT NULL,
  use_digest INT CONSTRAINT [DF_ezsubtree_notification_rule_use_digest] DEFAULT 0 NULL,
  user_id INT CONSTRAINT [DF_ezsubtree_notification_rule_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezsubtree_notification_rule ADD CONSTRAINT [PK_ezsubtree_notification_rule] PRIMARY KEY (id);
CREATE INDEX [ezsubtree_notification_rule_user_id] ON ezsubtree_notification_rule (user_id);

CREATE TABLE eztipafriend_counter (
  count INT CONSTRAINT [DF_eztipafriend_counter_count] DEFAULT 0 NOT NULL,
  node_id INT CONSTRAINT [DF_eztipafriend_counter_node_id] DEFAULT 0 NOT NULL,
  requested INT CONSTRAINT [DF_eztipafriend_counter_requested] DEFAULT 0 NOT NULL
);
ALTER TABLE eztipafriend_counter ADD CONSTRAINT [PK_eztipafriend_counter] PRIMARY KEY (node_id, requested);

CREATE TABLE eztipafriend_request (
  created INT CONSTRAINT [DF_eztipafriend_request_created] DEFAULT 0 NOT NULL,
  email_receiver NVARCHAR(100) CONSTRAINT [DF_eztipafriend_request_email_receiver] DEFAULT N'' NOT NULL
);
CREATE INDEX [eztipafriend_request_created] ON eztipafriend_request (created);
CREATE INDEX [eztipafriend_request_email_rec] ON eztipafriend_request (email_receiver);

CREATE TABLE eztrigger (
  connect_type NCHAR(1) CONSTRAINT [DF_eztrigger_connect_type] DEFAULT N'' NOT NULL,
  function_name NVARCHAR(200) CONSTRAINT [DF_eztrigger_function_name] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  module_name NVARCHAR(200) CONSTRAINT [DF_eztrigger_module_name] DEFAULT N'' NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_eztrigger_name] DEFAULT NULL NULL,
  workflow_id INT CONSTRAINT [DF_eztrigger_workflow_id] DEFAULT NULL NULL
);
ALTER TABLE eztrigger ADD CONSTRAINT [PK_eztrigger] PRIMARY KEY (id);
CREATE UNIQUE INDEX [eztrigger_def_id] ON eztrigger (module_name, function_name, connect_type);
CREATE INDEX [eztrigger_fetch] ON eztrigger (name, module_name, function_name);

CREATE TABLE ezurl (
  created INT CONSTRAINT [DF_ezurl_created] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  is_valid INT CONSTRAINT [DF_ezurl_is_valid] DEFAULT 1 NOT NULL,
  last_checked INT CONSTRAINT [DF_ezurl_last_checked] DEFAULT 0 NOT NULL,
  modified INT CONSTRAINT [DF_ezurl_modified] DEFAULT 0 NOT NULL,
  original_url_md5 NVARCHAR(32) CONSTRAINT [DF_ezurl_original_url_md5] DEFAULT N'' NOT NULL,
  url NVARCHAR(840) NULL
);
ALTER TABLE ezurl ADD CONSTRAINT [PK_ezurl] PRIMARY KEY (id);
CREATE INDEX [ezurl_url] ON ezurl (url);

CREATE TABLE ezurl_object_link (
  contentobject_attribute_id INT CONSTRAINT [DF_ezurl_object_link_contentobject_attribute_id] DEFAULT 0 NOT NULL,
  contentobject_attribute_version INT CONSTRAINT [DF_ezurl_object_link_contentobject_attribute_version] DEFAULT 0 NOT NULL,
  url_id INT CONSTRAINT [DF_ezurl_object_link_url_id] DEFAULT 0 NOT NULL
);
CREATE INDEX [ezurl_ol_coa_id] ON ezurl_object_link (contentobject_attribute_id);
CREATE INDEX [ezurl_ol_coa_version] ON ezurl_object_link (contentobject_attribute_version);
CREATE INDEX [ezurl_ol_url_id] ON ezurl_object_link (url_id);

CREATE TABLE ezurlalias (
  destination_url NVARCHAR(840) NOT NULL,
  forward_to_id INT CONSTRAINT [DF_ezurlalias_forward_to_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  is_imported INT CONSTRAINT [DF_ezurlalias_is_imported] DEFAULT 0 NOT NULL,
  is_internal INT CONSTRAINT [DF_ezurlalias_is_internal] DEFAULT 1 NOT NULL,
  is_wildcard INT CONSTRAINT [DF_ezurlalias_is_wildcard] DEFAULT 0 NOT NULL,
  source_md5 NVARCHAR(32) CONSTRAINT [DF_ezurlalias_source_md5] DEFAULT NULL NULL,
  source_url NVARCHAR(840) NOT NULL
);
ALTER TABLE ezurlalias ADD CONSTRAINT [PK_ezurlalias] PRIMARY KEY (id);
CREATE INDEX [ezurlalias_desturl] ON ezurlalias (destination_url);
CREATE INDEX [ezurlalias_forward_to_id] ON ezurlalias (forward_to_id);
CREATE INDEX [ezurlalias_imp_wcard_fwd] ON ezurlalias (is_imported, is_wildcard, forward_to_id);
CREATE INDEX [ezurlalias_source_md5] ON ezurlalias (source_md5);
CREATE INDEX [ezurlalias_source_url] ON ezurlalias (source_url);
CREATE INDEX [ezurlalias_wcard_fwd] ON ezurlalias (is_wildcard, forward_to_id);

CREATE TABLE ezurlalias_ml (
  action NVARCHAR(840) NOT NULL,
  action_type NVARCHAR(32) CONSTRAINT [DF_ezurlalias_ml_action_type] DEFAULT N'' NOT NULL,
  alias_redirects INT CONSTRAINT [DF_ezurlalias_ml_alias_redirects] DEFAULT 1 NOT NULL,
  id INT CONSTRAINT [DF_ezurlalias_ml_id] DEFAULT 0 NOT NULL,
  is_alias INT CONSTRAINT [DF_ezurlalias_ml_is_alias] DEFAULT 0 NOT NULL,
  is_original INT CONSTRAINT [DF_ezurlalias_ml_is_original] DEFAULT 0 NOT NULL,
  lang_mask BIGINT CONSTRAINT [DF_ezurlalias_ml_lang_mask] DEFAULT 0 NOT NULL,
  link INT CONSTRAINT [DF_ezurlalias_ml_link] DEFAULT 0 NOT NULL,
  parent INT CONSTRAINT [DF_ezurlalias_ml_parent] DEFAULT 0 NOT NULL,
  text NVARCHAR(840) NOT NULL,
  text_md5 NVARCHAR(32) CONSTRAINT [DF_ezurlalias_ml_text_md5] DEFAULT N'' NOT NULL
);
ALTER TABLE ezurlalias_ml ADD CONSTRAINT [PK_ezurlalias_ml] PRIMARY KEY (parent, text_md5);
CREATE INDEX [ezurlalias_ml_act_org] ON ezurlalias_ml (action, is_original);
CREATE INDEX [ezurlalias_ml_actt_org_al] ON ezurlalias_ml (action_type, is_original, is_alias);
CREATE INDEX [ezurlalias_ml_id] ON ezurlalias_ml (id);
CREATE INDEX [ezurlalias_ml_par_act_id_lnk] ON ezurlalias_ml (action, id, link, parent);
CREATE INDEX [ezurlalias_ml_par_lnk_txt] ON ezurlalias_ml (parent, text, link);
CREATE INDEX [ezurlalias_ml_text] ON ezurlalias_ml (text, id, link);
CREATE INDEX [ezurlalias_ml_text_lang] ON ezurlalias_ml (text, lang_mask, parent);

CREATE TABLE ezurlalias_ml_incr (
  id INT IDENTITY(1,1) NOT NULL
);
ALTER TABLE ezurlalias_ml_incr ADD CONSTRAINT [PK_ezurlalias_ml_incr] PRIMARY KEY (id);

CREATE TABLE ezurlwildcard (
  destination_url NVARCHAR(MAX) NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  source_url NVARCHAR(MAX) NOT NULL,
  type INT CONSTRAINT [DF_ezurlwildcard_type] DEFAULT 0 NOT NULL
);
ALTER TABLE ezurlwildcard ADD CONSTRAINT [PK_ezurlwildcard] PRIMARY KEY (id);

CREATE TABLE ezuser (
  contentobject_id INT CONSTRAINT [DF_ezuser_contentobject_id] DEFAULT 0 NOT NULL,
  email NVARCHAR(150) CONSTRAINT [DF_ezuser_email] DEFAULT N'' NOT NULL,
  login NVARCHAR(150) CONSTRAINT [DF_ezuser_login] DEFAULT N'' NOT NULL,
  password_hash NVARCHAR(255) CONSTRAINT [DF_ezuser_password_hash] DEFAULT NULL NULL,
  password_hash_type INT CONSTRAINT [DF_ezuser_password_hash_type] DEFAULT 1 NOT NULL
);
ALTER TABLE ezuser ADD CONSTRAINT [PK_ezuser] PRIMARY KEY (contentobject_id);
CREATE UNIQUE INDEX [ezuser_login] ON ezuser (login);

CREATE TABLE ezuser_accountkey (
  hash_key NVARCHAR(32) CONSTRAINT [DF_ezuser_accountkey_hash_key] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  time INT CONSTRAINT [DF_ezuser_accountkey_time] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_ezuser_accountkey_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezuser_accountkey ADD CONSTRAINT [PK_ezuser_accountkey] PRIMARY KEY (id);
CREATE INDEX [hash_key] ON ezuser_accountkey (hash_key);

CREATE TABLE ezuser_discountrule (
  contentobject_id INT CONSTRAINT [DF_ezuser_discountrule_contentobject_id] DEFAULT NULL NULL,
  discountrule_id INT CONSTRAINT [DF_ezuser_discountrule_discountrule_id] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezuser_discountrule_name] DEFAULT N'' NOT NULL
);
ALTER TABLE ezuser_discountrule ADD CONSTRAINT [PK_ezuser_discountrule] PRIMARY KEY (id);

CREATE TABLE ezuser_role (
  contentobject_id INT CONSTRAINT [DF_ezuser_role_contentobject_id] DEFAULT NULL NULL,
  id INT IDENTITY(1,1) NOT NULL,
  limit_identifier NVARCHAR(255) CONSTRAINT [DF_ezuser_role_limit_identifier] DEFAULT N'' NULL,
  limit_value NVARCHAR(255) CONSTRAINT [DF_ezuser_role_limit_value] DEFAULT N'' NULL,
  role_id INT CONSTRAINT [DF_ezuser_role_role_id] DEFAULT NULL NULL
);
ALTER TABLE ezuser_role ADD CONSTRAINT [PK_ezuser_role] PRIMARY KEY (id);
CREATE INDEX [ezuser_role_contentobject_id] ON ezuser_role (contentobject_id);
CREATE INDEX [ezuser_role_role_id] ON ezuser_role (role_id);

CREATE TABLE ezuser_setting (
  is_enabled INT CONSTRAINT [DF_ezuser_setting_is_enabled] DEFAULT 0 NOT NULL,
  max_login INT CONSTRAINT [DF_ezuser_setting_max_login] DEFAULT NULL NULL,
  user_id INT CONSTRAINT [DF_ezuser_setting_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezuser_setting ADD CONSTRAINT [PK_ezuser_setting] PRIMARY KEY (user_id);

CREATE TABLE ezuservisit (
  current_visit_timestamp INT CONSTRAINT [DF_ezuservisit_current_visit_timestamp] DEFAULT 0 NOT NULL,
  failed_login_attempts INT CONSTRAINT [DF_ezuservisit_failed_login_attempts] DEFAULT 0 NOT NULL,
  last_visit_timestamp INT CONSTRAINT [DF_ezuservisit_last_visit_timestamp] DEFAULT 0 NOT NULL,
  login_count INT CONSTRAINT [DF_ezuservisit_login_count] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_ezuservisit_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezuservisit ADD CONSTRAINT [PK_ezuservisit] PRIMARY KEY (user_id);
CREATE INDEX [ezuservisit_co_visit_count] ON ezuservisit (current_visit_timestamp, login_count);

CREATE TABLE ezvatrule (
  country_code NVARCHAR(255) CONSTRAINT [DF_ezvatrule_country_code] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  vat_type INT CONSTRAINT [DF_ezvatrule_vat_type] DEFAULT 0 NOT NULL
);
ALTER TABLE ezvatrule ADD CONSTRAINT [PK_ezvatrule] PRIMARY KEY (id);

CREATE TABLE ezvatrule_product_category (
  product_category_id INT CONSTRAINT [DF_ezvatrule_product_category_product_category_id] DEFAULT 0 NOT NULL,
  vatrule_id INT CONSTRAINT [DF_ezvatrule_product_category_vatrule_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezvatrule_product_category ADD CONSTRAINT [PK_ezvatrule_product_category] PRIMARY KEY (vatrule_id, product_category_id);

CREATE TABLE ezvattype (
  id INT IDENTITY(1,1) NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezvattype_name] DEFAULT N'' NOT NULL,
  percentage REAL CONSTRAINT [DF_ezvattype_percentage] DEFAULT NULL NULL
);
ALTER TABLE ezvattype ADD CONSTRAINT [PK_ezvattype] PRIMARY KEY (id);

CREATE TABLE ezview_counter (
  count INT CONSTRAINT [DF_ezview_counter_count] DEFAULT 0 NOT NULL,
  node_id INT CONSTRAINT [DF_ezview_counter_node_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezview_counter ADD CONSTRAINT [PK_ezview_counter] PRIMARY KEY (node_id);

CREATE TABLE ezwaituntildatevalue (
  contentclass_attribute_id INT CONSTRAINT [DF_ezwaituntildatevalue_contentclass_attribute_id] DEFAULT 0 NOT NULL,
  contentclass_id INT CONSTRAINT [DF_ezwaituntildatevalue_contentclass_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  workflow_event_id INT CONSTRAINT [DF_ezwaituntildatevalue_workflow_event_id] DEFAULT 0 NOT NULL,
  workflow_event_version INT CONSTRAINT [DF_ezwaituntildatevalue_workflow_event_version] DEFAULT 0 NOT NULL
);
ALTER TABLE ezwaituntildatevalue ADD CONSTRAINT [PK_ezwaituntildatevalue] PRIMARY KEY (id, workflow_event_id, workflow_event_version);
CREATE INDEX [ezwaituntildateevalue_wf_ev_id_wf_ver] ON ezwaituntildatevalue (workflow_event_id, workflow_event_version);

CREATE TABLE ezwishlist (
  id INT IDENTITY(1,1) NOT NULL,
  productcollection_id INT CONSTRAINT [DF_ezwishlist_productcollection_id] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_ezwishlist_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezwishlist ADD CONSTRAINT [PK_ezwishlist] PRIMARY KEY (id);

CREATE TABLE ezworkflow (
  created INT CONSTRAINT [DF_ezworkflow_created] DEFAULT 0 NOT NULL,
  creator_id INT CONSTRAINT [DF_ezworkflow_creator_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  is_enabled INT CONSTRAINT [DF_ezworkflow_is_enabled] DEFAULT 0 NOT NULL,
  modified INT CONSTRAINT [DF_ezworkflow_modified] DEFAULT 0 NOT NULL,
  modifier_id INT CONSTRAINT [DF_ezworkflow_modifier_id] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezworkflow_name] DEFAULT N'' NOT NULL,
  version INT CONSTRAINT [DF_ezworkflow_version] DEFAULT 0 NOT NULL,
  workflow_type_string NVARCHAR(50) CONSTRAINT [DF_ezworkflow_workflow_type_string] DEFAULT N'' NOT NULL
);
ALTER TABLE ezworkflow ADD CONSTRAINT [PK_ezworkflow] PRIMARY KEY (id, version);

CREATE TABLE ezworkflow_assign (
  access_type INT CONSTRAINT [DF_ezworkflow_assign_access_type] DEFAULT 0 NOT NULL,
  as_tree INT CONSTRAINT [DF_ezworkflow_assign_as_tree] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  node_id INT CONSTRAINT [DF_ezworkflow_assign_node_id] DEFAULT 0 NOT NULL,
  workflow_id INT CONSTRAINT [DF_ezworkflow_assign_workflow_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezworkflow_assign ADD CONSTRAINT [PK_ezworkflow_assign] PRIMARY KEY (id);

CREATE TABLE ezworkflow_event (
  data_int1 INT CONSTRAINT [DF_ezworkflow_event_data_int1] DEFAULT NULL NULL,
  data_int2 INT CONSTRAINT [DF_ezworkflow_event_data_int2] DEFAULT NULL NULL,
  data_int3 INT CONSTRAINT [DF_ezworkflow_event_data_int3] DEFAULT NULL NULL,
  data_int4 INT CONSTRAINT [DF_ezworkflow_event_data_int4] DEFAULT NULL NULL,
  data_text1 NVARCHAR(255) CONSTRAINT [DF_ezworkflow_event_data_text1] DEFAULT NULL NULL,
  data_text2 NVARCHAR(255) CONSTRAINT [DF_ezworkflow_event_data_text2] DEFAULT NULL NULL,
  data_text3 NVARCHAR(255) CONSTRAINT [DF_ezworkflow_event_data_text3] DEFAULT NULL NULL,
  data_text4 NVARCHAR(255) CONSTRAINT [DF_ezworkflow_event_data_text4] DEFAULT NULL NULL,
  data_text5 NVARCHAR(MAX) NULL,
  description NVARCHAR(50) CONSTRAINT [DF_ezworkflow_event_description] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  placement INT CONSTRAINT [DF_ezworkflow_event_placement] DEFAULT 0 NOT NULL,
  version INT CONSTRAINT [DF_ezworkflow_event_version] DEFAULT 0 NOT NULL,
  workflow_id INT CONSTRAINT [DF_ezworkflow_event_workflow_id] DEFAULT 0 NOT NULL,
  workflow_type_string NVARCHAR(50) CONSTRAINT [DF_ezworkflow_event_workflow_type_string] DEFAULT N'' NOT NULL
);
ALTER TABLE ezworkflow_event ADD CONSTRAINT [PK_ezworkflow_event] PRIMARY KEY (id, version);
CREATE INDEX [wid_version_placement] ON ezworkflow_event (workflow_id, version, placement);

CREATE TABLE ezworkflow_group (
  created INT CONSTRAINT [DF_ezworkflow_group_created] DEFAULT 0 NOT NULL,
  creator_id INT CONSTRAINT [DF_ezworkflow_group_creator_id] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  modified INT CONSTRAINT [DF_ezworkflow_group_modified] DEFAULT 0 NOT NULL,
  modifier_id INT CONSTRAINT [DF_ezworkflow_group_modifier_id] DEFAULT 0 NOT NULL,
  name NVARCHAR(255) CONSTRAINT [DF_ezworkflow_group_name] DEFAULT N'' NOT NULL
);
ALTER TABLE ezworkflow_group ADD CONSTRAINT [PK_ezworkflow_group] PRIMARY KEY (id);

CREATE TABLE ezworkflow_group_link (
  group_id INT CONSTRAINT [DF_ezworkflow_group_link_group_id] DEFAULT 0 NOT NULL,
  group_name NVARCHAR(255) CONSTRAINT [DF_ezworkflow_group_link_group_name] DEFAULT NULL NULL,
  workflow_id INT CONSTRAINT [DF_ezworkflow_group_link_workflow_id] DEFAULT 0 NOT NULL,
  workflow_version INT CONSTRAINT [DF_ezworkflow_group_link_workflow_version] DEFAULT 0 NOT NULL
);
ALTER TABLE ezworkflow_group_link ADD CONSTRAINT [PK_ezworkflow_group_link] PRIMARY KEY (workflow_id, group_id, workflow_version);

CREATE TABLE ezworkflow_process (
  activation_date INT CONSTRAINT [DF_ezworkflow_process_activation_date] DEFAULT NULL NULL,
  content_id INT CONSTRAINT [DF_ezworkflow_process_content_id] DEFAULT 0 NOT NULL,
  content_version INT CONSTRAINT [DF_ezworkflow_process_content_version] DEFAULT 0 NOT NULL,
  created INT CONSTRAINT [DF_ezworkflow_process_created] DEFAULT 0 NOT NULL,
  event_id INT CONSTRAINT [DF_ezworkflow_process_event_id] DEFAULT 0 NOT NULL,
  event_position INT CONSTRAINT [DF_ezworkflow_process_event_position] DEFAULT 0 NOT NULL,
  event_state INT CONSTRAINT [DF_ezworkflow_process_event_state] DEFAULT NULL NULL,
  event_status INT CONSTRAINT [DF_ezworkflow_process_event_status] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  last_event_id INT CONSTRAINT [DF_ezworkflow_process_last_event_id] DEFAULT 0 NOT NULL,
  last_event_position INT CONSTRAINT [DF_ezworkflow_process_last_event_position] DEFAULT 0 NOT NULL,
  last_event_status INT CONSTRAINT [DF_ezworkflow_process_last_event_status] DEFAULT 0 NOT NULL,
  memento_key NVARCHAR(32) CONSTRAINT [DF_ezworkflow_process_memento_key] DEFAULT NULL NULL,
  modified INT CONSTRAINT [DF_ezworkflow_process_modified] DEFAULT 0 NOT NULL,
  node_id INT CONSTRAINT [DF_ezworkflow_process_node_id] DEFAULT 0 NOT NULL,
  parameters NVARCHAR(MAX) NULL,
  process_key NVARCHAR(32) CONSTRAINT [DF_ezworkflow_process_process_key] DEFAULT N'' NOT NULL,
  session_key NVARCHAR(32) CONSTRAINT [DF_ezworkflow_process_session_key] DEFAULT N'0' NOT NULL,
  status INT CONSTRAINT [DF_ezworkflow_process_status] DEFAULT NULL NULL,
  user_id INT CONSTRAINT [DF_ezworkflow_process_user_id] DEFAULT 0 NOT NULL,
  workflow_id INT CONSTRAINT [DF_ezworkflow_process_workflow_id] DEFAULT 0 NOT NULL
);
ALTER TABLE ezworkflow_process ADD CONSTRAINT [PK_ezworkflow_process] PRIMARY KEY (id);
CREATE INDEX [ezworkflow_process_process_key] ON ezworkflow_process (process_key);

CREATE TABLE exp_url_redirect (
  batch_id INT CONSTRAINT [DF_exp_url_redirect_batch_id] DEFAULT 0 NOT NULL,
  created INT CONSTRAINT [DF_exp_url_redirect_created] DEFAULT 0 NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  language NVARCHAR(20) CONSTRAINT [DF_exp_url_redirect_language] DEFAULT N'' NOT NULL,
  modified INT CONSTRAINT [DF_exp_url_redirect_modified] DEFAULT 0 NOT NULL,
  siteaccess NVARCHAR(100) CONSTRAINT [DF_exp_url_redirect_siteaccess] DEFAULT N'' NOT NULL,
  source_key NVARCHAR(32) CONSTRAINT [DF_exp_url_redirect_source_key] DEFAULT N'' NOT NULL,
  source_path NVARCHAR(1000) CONSTRAINT [DF_exp_url_redirect_source_path] DEFAULT N'' NOT NULL,
  source_query NVARCHAR(1000) CONSTRAINT [DF_exp_url_redirect_source_query] DEFAULT N'' NOT NULL,
  status INT CONSTRAINT [DF_exp_url_redirect_status] DEFAULT 301 NOT NULL,
  target NVARCHAR(2000) CONSTRAINT [DF_exp_url_redirect_target] DEFAULT N'' NOT NULL
);
ALTER TABLE exp_url_redirect ADD CONSTRAINT [PK_exp_url_redirect] PRIMARY KEY (id);
CREATE INDEX [exp_url_redirect_batch] ON exp_url_redirect (batch_id);
CREATE INDEX [exp_url_redirect_source] ON exp_url_redirect (source_key);

CREATE TABLE exp_urlmap_batch (
  created INT CONSTRAINT [DF_exp_urlmap_batch_created] DEFAULT 0 NOT NULL,
  created_count INT CONSTRAINT [DF_exp_urlmap_batch_created_count] DEFAULT 0 NOT NULL,
  failed_count INT CONSTRAINT [DF_exp_urlmap_batch_failed_count] DEFAULT 0 NOT NULL,
  format NVARCHAR(20) CONSTRAINT [DF_exp_urlmap_batch_format] DEFAULT N'' NOT NULL,
  id INT IDENTITY(1,1) NOT NULL,
  language NVARCHAR(20) CONSTRAINT [DF_exp_urlmap_batch_language] DEFAULT N'' NOT NULL,
  on_conflict NVARCHAR(20) CONSTRAINT [DF_exp_urlmap_batch_on_conflict] DEFAULT N'' NOT NULL,
  rows_total INT CONSTRAINT [DF_exp_urlmap_batch_rows_total] DEFAULT 0 NOT NULL,
  siteaccess NVARCHAR(100) CONSTRAINT [DF_exp_urlmap_batch_siteaccess] DEFAULT N'' NOT NULL,
  skipped_count INT CONSTRAINT [DF_exp_urlmap_batch_skipped_count] DEFAULT 0 NOT NULL,
  source_name NVARCHAR(255) CONSTRAINT [DF_exp_urlmap_batch_source_name] DEFAULT N'' NOT NULL,
  state NVARCHAR(20) CONSTRAINT [DF_exp_urlmap_batch_state] DEFAULT N'' NOT NULL,
  summary NVARCHAR(MAX) NULL,
  undone INT CONSTRAINT [DF_exp_urlmap_batch_undone] DEFAULT 0 NOT NULL,
  undone_by INT CONSTRAINT [DF_exp_urlmap_batch_undone_by] DEFAULT 0 NOT NULL,
  updated_count INT CONSTRAINT [DF_exp_urlmap_batch_updated_count] DEFAULT 0 NOT NULL,
  user_id INT CONSTRAINT [DF_exp_urlmap_batch_user_id] DEFAULT 0 NOT NULL
);
ALTER TABLE exp_urlmap_batch ADD CONSTRAINT [PK_exp_urlmap_batch] PRIMARY KEY (id);
CREATE INDEX [exp_urlmap_batch_created] ON exp_urlmap_batch (created);

CREATE TABLE exp_urlmap_batch_item (
  action NVARCHAR(20) CONSTRAINT [DF_exp_urlmap_batch_item_action] DEFAULT N'' NOT NULL,
  after_image NVARCHAR(MAX) NULL,
  batch_id INT CONSTRAINT [DF_exp_urlmap_batch_item_batch_id] DEFAULT 0 NOT NULL,
  before_image NVARCHAR(MAX) NULL,
  id INT IDENTITY(1,1) NOT NULL,
  kind NVARCHAR(30) CONSTRAINT [DF_exp_urlmap_batch_item_kind] DEFAULT N'' NOT NULL,
  line INT CONSTRAINT [DF_exp_urlmap_batch_item_line] DEFAULT 0 NOT NULL,
  old_url NVARCHAR(1000) CONSTRAINT [DF_exp_urlmap_batch_item_old_url] DEFAULT N'' NOT NULL,
  seq INT CONSTRAINT [DF_exp_urlmap_batch_item_seq] DEFAULT 0 NOT NULL,
  state NVARCHAR(20) CONSTRAINT [DF_exp_urlmap_batch_item_state] DEFAULT N'' NOT NULL,
  target_key NVARCHAR(255) CONSTRAINT [DF_exp_urlmap_batch_item_target_key] DEFAULT N'' NOT NULL,
  target_table NVARCHAR(50) CONSTRAINT [DF_exp_urlmap_batch_item_target_table] DEFAULT N'' NOT NULL
);
ALTER TABLE exp_urlmap_batch_item ADD CONSTRAINT [PK_exp_urlmap_batch_item] PRIMARY KEY (id);
CREATE INDEX [exp_urlmap_batch_item_batch] ON exp_urlmap_batch_item (batch_id, seq);
