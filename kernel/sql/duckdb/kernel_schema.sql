CREATE TABLE IF NOT EXISTS expaudit_cursor (
  byte_offset bigint DEFAULT '0' NOT NULL,
  channel character varying(32) DEFAULT ''::character varying NOT NULL,
  file_name character varying(64) DEFAULT ''::character varying NOT NULL,
  last_hash character varying(80) DEFAULT NULL,
  last_seq integer DEFAULT 0 NOT NULL,
  updated_ms bigint DEFAULT '0' NOT NULL,
  PRIMARY KEY ( channel, file_name )
);

CREATE TABLE IF NOT EXISTS expaudit_event (
  channel character varying(32) DEFAULT ''::character varying NOT NULL,
  depth integer DEFAULT 0 NOT NULL,
  domain_name character varying(16) DEFAULT ''::character varying NOT NULL,
  engine character varying(16) DEFAULT NULL,
  file_name character varying(64) DEFAULT ''::character varying NOT NULL,
  id character(26) DEFAULT ''::bpchar NOT NULL,
  imported integer DEFAULT 0 NOT NULL,
  ip character varying(64) DEFAULT NULL,
  job_id character varying(32) DEFAULT NULL,
  login character varying(150) DEFAULT NULL,
  module_view character varying(128) DEFAULT NULL,
  name character varying(128) DEFAULT ''::character varying NOT NULL,
  object_id character varying(64) DEFAULT NULL,
  object_name character varying(255) DEFAULT NULL,
  object_type character varying(32) DEFAULT NULL,
  parent_id character(26) DEFAULT NULL,
  pseudonymised integer DEFAULT 0 NOT NULL,
  reason character varying(32) DEFAULT NULL,
  record text DEFAULT NULL,
  request_id character varying(40) DEFAULT NULL,
  result character varying(8) DEFAULT NULL,
  run_id character varying(40) DEFAULT NULL,
  search_text text DEFAULT NULL,
  seq integer DEFAULT 0 NOT NULL,
  session_h character varying(24) DEFAULT NULL,
  severity integer DEFAULT 0 NOT NULL,
  siteaccess character varying(64) DEFAULT NULL,
  target_id character varying(64) DEFAULT NULL,
  target_type character varying(32) DEFAULT NULL,
  time_ms bigint DEFAULT '0' NOT NULL,
  ua character varying(128) DEFAULT NULL,
  user_id integer DEFAULT NULL,
  verb character varying(32) DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX expaudit_event_domain ON expaudit_event ( domain_name, severity, time_ms );

CREATE UNIQUE INDEX expaudit_event_file_seq ON expaudit_event ( channel, file_name, seq );

CREATE INDEX expaudit_event_ip ON expaudit_event ( ip, time_ms );

CREATE INDEX expaudit_event_job ON expaudit_event ( job_id );

CREATE INDEX expaudit_event_name ON expaudit_event ( name, time_ms );

CREATE INDEX expaudit_event_object ON expaudit_event ( object_type, object_id, time_ms );

CREATE INDEX expaudit_event_parent ON expaudit_event ( parent_id );

CREATE INDEX expaudit_event_request ON expaudit_event ( request_id );

CREATE INDEX expaudit_event_result ON expaudit_event ( result, time_ms );

CREATE INDEX expaudit_event_time ON expaudit_event ( time_ms );

CREATE INDEX expaudit_event_user ON expaudit_event ( user_id, time_ms );


CREATE TABLE IF NOT EXISTS expaudit_file (
  archive_path character varying(255) DEFAULT NULL,
  break_line integer DEFAULT 0 NOT NULL,
  channel character varying(32) DEFAULT ''::character varying NOT NULL,
  file_name character varying(64) DEFAULT ''::character varying NOT NULL,
  records integer DEFAULT 0 NOT NULL,
  state character varying(16) DEFAULT 'live'::character varying NOT NULL,
  verified character varying(16) DEFAULT 'unchecked'::character varying NOT NULL,
  verified_ms bigint DEFAULT '0' NOT NULL,
  PRIMARY KEY ( channel, file_name )
);

CREATE SEQUENCE IF NOT EXISTS expmail_category_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS expmail_category (
  created integer DEFAULT 0 NOT NULL,
  default_on integer DEFAULT 0 NOT NULL,
  description text DEFAULT NULL,
  double_opt_in integer DEFAULT 0 NOT NULL,
  essential integer DEFAULT 0 NOT NULL,
  frequencies character varying(100) DEFAULT ''::character varying NOT NULL,
  handler_class character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('expmail_category_id_seq'::text) NOT NULL,
  identifier character varying(100) DEFAULT ''::character varying NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  priority integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX expmail_category_identifier ON expmail_category ( identifier );


CREATE SEQUENCE IF NOT EXISTS expmail_consent_log_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS expmail_consent_log (
  "action" character varying(30) DEFAULT ''::character varying NOT NULL,
  actor_user_id integer DEFAULT 0 NOT NULL,
  anonymised integer DEFAULT 0 NOT NULL,
  category character varying(100) DEFAULT ''::character varying NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  email character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('expmail_consent_log_id_seq'::text) NOT NULL,
  ip character varying(64) DEFAULT ''::character varying NOT NULL,
  new_value character varying(100) DEFAULT ''::character varying NOT NULL,
  old_value character varying(100) DEFAULT ''::character varying NOT NULL,
  recipient_key character varying(80) DEFAULT ''::character varying NOT NULL,
  siteaccess character varying(100) DEFAULT ''::character varying NOT NULL,
  source character varying(20) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  wording text DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX expmail_consent_log_created ON expmail_consent_log ( created );

CREATE INDEX expmail_consent_log_recipient ON expmail_consent_log ( recipient_key, created );

CREATE INDEX expmail_consent_log_user ON expmail_consent_log ( user_id );


CREATE SEQUENCE IF NOT EXISTS expmail_pending_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS expmail_pending (
  category character varying(100) DEFAULT ''::character varying NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  data text DEFAULT NULL,
  expires integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('expmail_pending_id_seq'::text) NOT NULL,
  kind character varying(20) DEFAULT ''::character varying NOT NULL,
  recipient_key character varying(80) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX expmail_pending_expires ON expmail_pending ( expires );

CREATE INDEX expmail_pending_recipient ON expmail_pending ( recipient_key, kind );


CREATE SEQUENCE IF NOT EXISTS expmail_preference_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS expmail_preference (
  category character varying(100) DEFAULT ''::character varying NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  frequency character varying(20) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('expmail_preference_id_seq'::text) NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  recipient_key character varying(80) DEFAULT ''::character varying NOT NULL,
  state character varying(20) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX expmail_preference_category ON expmail_preference ( category, state );

CREATE UNIQUE INDEX expmail_preference_recipient ON expmail_preference ( recipient_key, category );

CREATE INDEX expmail_preference_user ON expmail_preference ( user_id );


CREATE SEQUENCE IF NOT EXISTS expmail_suppression_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS expmail_suppression (
  created integer DEFAULT 0 NOT NULL,
  created_by integer DEFAULT 0 NOT NULL,
  email_hash character varying(64) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('expmail_suppression_id_seq'::text) NOT NULL,
  note text DEFAULT NULL,
  reason character varying(30) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX expmail_suppression_hash ON expmail_suppression ( email_hash );


CREATE SEQUENCE IF NOT EXISTS ezapprove_items_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezapprove_items (
  collaboration_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezapprove_items_id_seq'::text) NOT NULL,
  workflow_process_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezbasket_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezbasket (
  id integer DEFAULT nextval('ezbasket_id_seq'::text) NOT NULL,
  order_id integer DEFAULT 0 NOT NULL,
  productcollection_id integer DEFAULT 0 NOT NULL,
  session_id character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezbasket_session_id ON ezbasket ( session_id );


CREATE TABLE IF NOT EXISTS ezbinaryfile (
  contentobject_attribute_id integer DEFAULT 0 NOT NULL,
  download_count integer DEFAULT 0 NOT NULL,
  filename character varying(255) DEFAULT ''::character varying NOT NULL,
  mime_type character varying(255) DEFAULT ''::character varying NOT NULL,
  original_filename character varying(255) DEFAULT ''::character varying NOT NULL,
  "version" integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( contentobject_attribute_id, "version" )
);

CREATE SEQUENCE IF NOT EXISTS ezcobj_state_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcobj_state (
  default_language_id bigint DEFAULT '0' NOT NULL,
  group_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezcobj_state_id_seq'::text) NOT NULL,
  identifier character varying(45) DEFAULT ''::character varying NOT NULL,
  language_mask bigint DEFAULT '0' NOT NULL,
  priority integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX ezcobj_state_identifier ON ezcobj_state ( group_id, identifier );

CREATE INDEX ezcobj_state_lmask ON ezcobj_state ( language_mask );

CREATE INDEX ezcobj_state_priority ON ezcobj_state ( priority );


CREATE SEQUENCE IF NOT EXISTS ezcobj_state_group_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcobj_state_group (
  default_language_id bigint DEFAULT '0' NOT NULL,
  id integer DEFAULT nextval('ezcobj_state_group_id_seq'::text) NOT NULL,
  identifier character varying(45) DEFAULT ''::character varying NOT NULL,
  language_mask bigint DEFAULT '0' NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX ezcobj_state_group_identifier ON ezcobj_state_group ( identifier );

CREATE INDEX ezcobj_state_group_lmask ON ezcobj_state_group ( language_mask );


CREATE TABLE IF NOT EXISTS ezcobj_state_group_language (
  contentobject_state_group_id integer DEFAULT 0 NOT NULL,
  description text NOT NULL,
  language_id bigint DEFAULT '0' NOT NULL,
  name character varying(45) DEFAULT ''::character varying NOT NULL,
  real_language_id bigint DEFAULT '0' NOT NULL,
  PRIMARY KEY ( contentobject_state_group_id, real_language_id )
);

CREATE TABLE IF NOT EXISTS ezcobj_state_language (
  contentobject_state_id integer DEFAULT 0 NOT NULL,
  description text NOT NULL,
  language_id bigint DEFAULT '0' NOT NULL,
  name character varying(45) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( contentobject_state_id, language_id )
);

CREATE TABLE IF NOT EXISTS ezcobj_state_link (
  contentobject_id integer DEFAULT 0 NOT NULL,
  contentobject_state_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( contentobject_id, contentobject_state_id )
);

CREATE SEQUENCE IF NOT EXISTS ezcollab_group_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcollab_group (
  created integer DEFAULT 0 NOT NULL,
  depth integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezcollab_group_id_seq'::text) NOT NULL,
  is_open integer DEFAULT 1 NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  parent_group_id integer DEFAULT 0 NOT NULL,
  path_string character varying(255) DEFAULT ''::character varying NOT NULL,
  priority integer DEFAULT 0 NOT NULL,
  title character varying(255) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezcollab_group_depth ON ezcollab_group ( depth );

CREATE INDEX ezcollab_group_path ON ezcollab_group ( path_string );


CREATE SEQUENCE IF NOT EXISTS ezcollab_item_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcollab_item (
  created integer DEFAULT 0 NOT NULL,
  creator_id integer DEFAULT 0 NOT NULL,
  data_float1 real DEFAULT 0::real NOT NULL,
  data_float2 real DEFAULT 0::real NOT NULL,
  data_float3 real DEFAULT 0::real NOT NULL,
  data_int1 integer DEFAULT 0 NOT NULL,
  data_int2 integer DEFAULT 0 NOT NULL,
  data_int3 integer DEFAULT 0 NOT NULL,
  data_text1 text NOT NULL,
  data_text2 text NOT NULL,
  data_text3 text NOT NULL,
  id integer DEFAULT nextval('ezcollab_item_id_seq'::text) NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  status integer DEFAULT 1 NOT NULL,
  type_identifier character varying(40) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE IF NOT EXISTS ezcollab_item_group_link (
  collaboration_id integer DEFAULT 0 NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  group_id integer DEFAULT 0 NOT NULL,
  is_active integer DEFAULT 1 NOT NULL,
  is_read integer DEFAULT 0 NOT NULL,
  last_read integer DEFAULT 0 NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( collaboration_id, group_id, user_id )
);

CREATE SEQUENCE IF NOT EXISTS ezcollab_item_message_link_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcollab_item_message_link (
  collaboration_id integer DEFAULT 0 NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezcollab_item_message_link_id_seq'::text) NOT NULL,
  message_id integer DEFAULT 0 NOT NULL,
  message_type integer DEFAULT 0 NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  participant_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE IF NOT EXISTS ezcollab_item_participant_link (
  collaboration_id integer DEFAULT 0 NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  is_active integer DEFAULT 1 NOT NULL,
  is_read integer DEFAULT 0 NOT NULL,
  last_read integer DEFAULT 0 NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  participant_id integer DEFAULT 0 NOT NULL,
  participant_role integer DEFAULT 1 NOT NULL,
  participant_type integer DEFAULT 1 NOT NULL,
  PRIMARY KEY ( collaboration_id, participant_id )
);

CREATE TABLE IF NOT EXISTS ezcollab_item_status (
  collaboration_id integer DEFAULT 0 NOT NULL,
  is_active integer DEFAULT 1 NOT NULL,
  is_read integer DEFAULT 0 NOT NULL,
  last_read integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( collaboration_id, user_id )
);

CREATE SEQUENCE IF NOT EXISTS ezcollab_notification_rule_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcollab_notification_rule (
  collab_identifier character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezcollab_notification_rule_id_seq'::text) NOT NULL,
  user_id character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezcollab_profile_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcollab_profile (
  created integer DEFAULT 0 NOT NULL,
  data_text1 text NOT NULL,
  id integer DEFAULT nextval('ezcollab_profile_id_seq'::text) NOT NULL,
  main_group integer DEFAULT 0 NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezcollab_simple_message_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcollab_simple_message (
  created integer DEFAULT 0 NOT NULL,
  creator_id integer DEFAULT 0 NOT NULL,
  data_float1 real DEFAULT 0::real NOT NULL,
  data_float2 real DEFAULT 0::real NOT NULL,
  data_float3 real DEFAULT 0::real NOT NULL,
  data_int1 integer DEFAULT 0 NOT NULL,
  data_int2 integer DEFAULT 0 NOT NULL,
  data_int3 integer DEFAULT 0 NOT NULL,
  data_text1 text NOT NULL,
  data_text2 text NOT NULL,
  data_text3 text NOT NULL,
  id integer DEFAULT nextval('ezcollab_simple_message_id_seq'::text) NOT NULL,
  message_type character varying(40) DEFAULT ''::character varying NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE IF NOT EXISTS ezcontent_language (
  disabled integer DEFAULT 0 NOT NULL,
  id bigint DEFAULT '0' NOT NULL,
  locale character varying(20) DEFAULT ''::character varying NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezcontent_language_name ON ezcontent_language ( name );


CREATE SEQUENCE IF NOT EXISTS ezcontentbrowsebookmark_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcontentbrowsebookmark (
  id integer DEFAULT nextval('ezcontentbrowsebookmark_id_seq'::text) NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  node_id integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  folder_id integer DEFAULT 0 NOT NULL,
  priority integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezcontentbrowsebookmark_folder ON ezcontentbrowsebookmark ( user_id, folder_id );

CREATE INDEX ezcontentbrowsebookmark_user ON ezcontentbrowsebookmark ( user_id );


CREATE SEQUENCE IF NOT EXISTS expbookmark_folder_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS expbookmark_folder (
  created integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('expbookmark_folder_id_seq'::text) NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  parent_id integer DEFAULT 0 NOT NULL,
  priority integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX expbookmark_folder_user ON expbookmark_folder ( user_id, parent_id );


CREATE TABLE IF NOT EXISTS expbookmark_view (
  bookmark_id integer DEFAULT 0 NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  module_name character varying(100) DEFAULT ''::character varying NOT NULL,
  module_uri text,
  user_id integer DEFAULT 0 NOT NULL,
  view_name character varying(100) DEFAULT ''::character varying NOT NULL,
  view_parameters text,
  PRIMARY KEY ( bookmark_id )
);
CREATE INDEX expbookmark_view_module ON expbookmark_view ( module_name, view_name );

CREATE INDEX expbookmark_view_user ON expbookmark_view ( user_id );


CREATE SEQUENCE IF NOT EXISTS ezcontentbrowserecent_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcontentbrowserecent (
  created integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezcontentbrowserecent_id_seq'::text) NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  node_id integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezcontentbrowserecent_user ON ezcontentbrowserecent ( user_id );


CREATE SEQUENCE IF NOT EXISTS ezcontentclass_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcontentclass (
  always_available integer DEFAULT 0 NOT NULL,
  contentobject_name character varying(255) DEFAULT NULL,
  created integer DEFAULT 0 NOT NULL,
  creator_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezcontentclass_id_seq'::text) NOT NULL,
  identifier character varying(50) DEFAULT ''::character varying NOT NULL,
  initial_language_id bigint DEFAULT '0' NOT NULL,
  is_container integer DEFAULT 0 NOT NULL,
  language_mask bigint DEFAULT '0' NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  modifier_id integer DEFAULT 0 NOT NULL,
  remote_id character varying(100) DEFAULT ''::character varying NOT NULL,
  serialized_description_list text,
  serialized_name_list text,
  sort_field integer DEFAULT 1 NOT NULL,
  sort_order integer DEFAULT 1 NOT NULL,
  url_alias_name character varying(255) DEFAULT NULL,
  "version" integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id, "version" )
);
CREATE INDEX ezcontentclass_version ON ezcontentclass ( "version" );

CREATE INDEX ezcontentclass_identifier ON ezcontentclass ( identifier, "version" );


CREATE SEQUENCE IF NOT EXISTS ezcontentclass_attribute_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcontentclass_attribute (
  can_translate integer DEFAULT 1,
  category character varying(25) DEFAULT ''::character varying NOT NULL,
  contentclass_id integer DEFAULT 0 NOT NULL,
  data_float1 double precision DEFAULT NULL,
  data_float2 double precision DEFAULT NULL,
  data_float3 double precision DEFAULT NULL,
  data_float4 double precision DEFAULT NULL,
  data_int1 integer DEFAULT NULL,
  data_int2 integer DEFAULT NULL,
  data_int3 integer DEFAULT NULL,
  data_int4 integer DEFAULT NULL,
  data_text1 character varying(50) DEFAULT NULL,
  data_text2 character varying(50) DEFAULT NULL,
  data_text3 character varying(50) DEFAULT NULL,
  data_text4 character varying(255) DEFAULT NULL,
  data_text5 text,
  data_type_string character varying(50) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezcontentclass_attribute_id_seq'::text) NOT NULL,
  identifier character varying(50) DEFAULT ''::character varying NOT NULL,
  is_information_collector integer DEFAULT 0 NOT NULL,
  is_required integer DEFAULT 0 NOT NULL,
  is_searchable integer DEFAULT 0 NOT NULL,
  placement integer DEFAULT 0 NOT NULL,
  serialized_data_text text,
  serialized_description_list text,
  serialized_name_list text NOT NULL,
  "version" integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id, "version" )
);
CREATE INDEX ezcontentclass_attr_ccid ON ezcontentclass_attribute ( contentclass_id );


CREATE TABLE IF NOT EXISTS ezcontentclass_classgroup (
  contentclass_id integer DEFAULT 0 NOT NULL,
  contentclass_version integer DEFAULT 0 NOT NULL,
  group_id integer DEFAULT 0 NOT NULL,
  group_name character varying(255) DEFAULT NULL,
  PRIMARY KEY ( contentclass_id, contentclass_version, group_id )
);

CREATE TABLE IF NOT EXISTS ezcontentclass_name (
  contentclass_id integer DEFAULT 0 NOT NULL,
  contentclass_version integer DEFAULT 0 NOT NULL,
  language_id bigint DEFAULT '0' NOT NULL,
  language_locale character varying(20) DEFAULT ''::character varying NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( contentclass_id, contentclass_version, language_id )
);

CREATE SEQUENCE IF NOT EXISTS ezcontentclassgroup_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcontentclassgroup (
  created integer DEFAULT 0 NOT NULL,
  creator_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezcontentclassgroup_id_seq'::text) NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  modifier_id integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezcontentobject_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcontentobject (
  contentclass_id integer DEFAULT 0 NOT NULL,
  current_version integer DEFAULT NULL,
  id integer DEFAULT nextval('ezcontentobject_id_seq'::text) NOT NULL,
  initial_language_id bigint DEFAULT '0' NOT NULL,
  language_mask bigint DEFAULT '0' NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT NULL,
  owner_id integer DEFAULT 0 NOT NULL,
  published integer DEFAULT 0 NOT NULL,
  remote_id character varying(100) DEFAULT NULL,
  section_id integer DEFAULT 0 NOT NULL,
  status integer DEFAULT 0,
  PRIMARY KEY ( id )
);
CREATE INDEX ezcontentobject_classid ON ezcontentobject ( contentclass_id );

CREATE INDEX ezcontentobject_currentversion ON ezcontentobject ( current_version );

CREATE INDEX ezcontentobject_lmask ON ezcontentobject ( language_mask );

CREATE INDEX ezcontentobject_modified ON ezcontentobject ( modified );

CREATE INDEX ezcontentobject_owner ON ezcontentobject ( owner_id );

CREATE INDEX ezcontentobject_pub ON ezcontentobject ( published );

CREATE UNIQUE INDEX ezcontentobject_remote_id ON ezcontentobject ( remote_id );

CREATE INDEX ezcontentobject_status ON ezcontentobject ( status );


CREATE SEQUENCE IF NOT EXISTS ezcontentobject_attribute_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcontentobject_attribute (
  attribute_original_id integer DEFAULT 0,
  contentclassattribute_id integer DEFAULT 0 NOT NULL,
  contentobject_id integer DEFAULT 0 NOT NULL,
  data_float double precision DEFAULT NULL,
  data_int integer DEFAULT NULL,
  data_text text,
  data_type_string character varying(50) DEFAULT ''::character varying,
  id integer DEFAULT nextval('ezcontentobject_attribute_id_seq'::text) NOT NULL,
  language_code character varying(20) DEFAULT ''::character varying NOT NULL,
  language_id bigint DEFAULT '0' NOT NULL,
  sort_key_int integer DEFAULT 0 NOT NULL,
  sort_key_string character varying(255) DEFAULT ''::character varying NOT NULL,
  "version" integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id, "version" )
);
CREATE INDEX ezcontentobject_attribute_co_id_ver_lang_code ON ezcontentobject_attribute ( contentobject_id, "version", language_code );

CREATE INDEX ezcontentobject_attribute_language_code ON ezcontentobject_attribute ( language_code );

CREATE INDEX ezcontentobject_classattr_id ON ezcontentobject_attribute ( contentclassattribute_id );

CREATE INDEX sort_key_int ON ezcontentobject_attribute ( sort_key_int );

CREATE INDEX sort_key_string ON ezcontentobject_attribute ( sort_key_string );


CREATE SEQUENCE IF NOT EXISTS ezcontentobject_link_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcontentobject_link (
  contentclassattribute_id integer DEFAULT 0 NOT NULL,
  from_contentobject_id integer DEFAULT 0 NOT NULL,
  from_contentobject_version integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezcontentobject_link_id_seq'::text) NOT NULL,
  relation_type integer DEFAULT 1 NOT NULL,
  to_contentobject_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezco_link_from ON ezcontentobject_link ( from_contentobject_id, from_contentobject_version, contentclassattribute_id );

CREATE INDEX ezco_link_to_co_id ON ezcontentobject_link ( to_contentobject_id );


CREATE TABLE IF NOT EXISTS ezcontentobject_name (
  content_translation character varying(20) DEFAULT ''::character varying NOT NULL,
  content_version integer DEFAULT 0 NOT NULL,
  contentobject_id integer DEFAULT 0 NOT NULL,
  language_id bigint DEFAULT '0' NOT NULL,
  name character varying(255) DEFAULT NULL,
  real_translation character varying(20) DEFAULT NULL,
  PRIMARY KEY ( contentobject_id, content_version, content_translation )
);
CREATE INDEX ezcontentobject_name_cov_id ON ezcontentobject_name ( content_version );

CREATE INDEX ezcontentobject_name_lang_id ON ezcontentobject_name ( language_id );

CREATE INDEX ezcontentobject_name_name ON ezcontentobject_name ( name );


CREATE TABLE IF NOT EXISTS ezcontentobject_trash (
  contentobject_id integer DEFAULT NULL,
  contentobject_version integer DEFAULT NULL,
  depth integer DEFAULT 0 NOT NULL,
  is_hidden integer DEFAULT 0 NOT NULL,
  is_invisible integer DEFAULT 0 NOT NULL,
  main_node_id integer DEFAULT NULL,
  modified_subnode integer DEFAULT 0,
  node_id integer DEFAULT 0 NOT NULL,
  parent_node_id integer DEFAULT 0 NOT NULL,
  path_identification_string text,
  path_string character varying(255) DEFAULT ''::character varying NOT NULL,
  priority integer DEFAULT 0 NOT NULL,
  remote_id character varying(100) DEFAULT ''::character varying NOT NULL,
  sort_field integer DEFAULT 1,
  sort_order integer DEFAULT 1,
  trashed integer DEFAULT 0 NOT NULL,
  trashed_by integer DEFAULT 0 NOT NULL,
  trashed_via character varying(100) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( node_id )
);
CREATE INDEX ezcobj_trash_co_id ON ezcontentobject_trash ( contentobject_id );

CREATE INDEX ezcobj_trash_depth ON ezcontentobject_trash ( depth );

CREATE INDEX ezcobj_trash_modified_subnode ON ezcontentobject_trash ( modified_subnode );

CREATE INDEX ezcobj_trash_p_node_id ON ezcontentobject_trash ( parent_node_id );

CREATE INDEX ezcobj_trash_path ON ezcontentobject_trash ( path_string );

CREATE INDEX ezcobj_trash_path_ident ON ezcontentobject_trash ( path_identification_string );

CREATE INDEX ezcobj_trash_trashed_by ON ezcontentobject_trash ( trashed_by );


CREATE SEQUENCE IF NOT EXISTS ezcontentobject_tree_node_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcontentobject_tree (
  contentobject_id integer DEFAULT NULL,
  contentobject_is_published integer DEFAULT NULL,
  contentobject_version integer DEFAULT NULL,
  depth integer DEFAULT 0 NOT NULL,
  is_hidden integer DEFAULT 0 NOT NULL,
  is_invisible integer DEFAULT 0 NOT NULL,
  main_node_id integer DEFAULT NULL,
  modified_subnode integer DEFAULT 0,
  node_id integer DEFAULT nextval('ezcontentobject_tree_node_id_seq'::text) NOT NULL,
  parent_node_id integer DEFAULT 0 NOT NULL,
  path_identification_string text,
  path_string character varying(255) DEFAULT ''::character varying NOT NULL,
  priority integer DEFAULT 0 NOT NULL,
  remote_id character varying(100) DEFAULT ''::character varying NOT NULL,
  sort_field integer DEFAULT 1,
  sort_order integer DEFAULT 1,
  PRIMARY KEY ( node_id )
);
CREATE INDEX ezcontentobject_tree_remote_id ON ezcontentobject_tree ( remote_id );

CREATE INDEX ezcontentobject_tree_co_id ON ezcontentobject_tree ( contentobject_id );

CREATE INDEX ezcontentobject_tree_depth ON ezcontentobject_tree ( depth );

CREATE INDEX ezcontentobject_tree_p_node_id ON ezcontentobject_tree ( parent_node_id );

CREATE INDEX ezcontentobject_tree_path ON ezcontentobject_tree ( path_string );

CREATE INDEX ezcontentobject_tree_path_ident ON ezcontentobject_tree ( path_identification_string );

CREATE INDEX modified_subnode ON ezcontentobject_tree ( modified_subnode );


CREATE SEQUENCE IF NOT EXISTS ezcontentobject_version_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcontentobject_version (
  contentobject_id integer DEFAULT NULL,
  created integer DEFAULT 0 NOT NULL,
  creator_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezcontentobject_version_id_seq'::text) NOT NULL,
  initial_language_id bigint DEFAULT '0' NOT NULL,
  language_mask bigint DEFAULT '0' NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  status integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  "version" integer DEFAULT 0 NOT NULL,
  workflow_event_pos integer DEFAULT 0,
  PRIMARY KEY ( id )
);
CREATE INDEX ezcobj_version_creator_id ON ezcontentobject_version ( creator_id );

CREATE INDEX ezcobj_version_status ON ezcontentobject_version ( status );

CREATE INDEX idx_object_version_objver ON ezcontentobject_version ( contentobject_id, "version" );

CREATE INDEX ezcontobj_version_obj_status ON ezcontentobject_version ( contentobject_id, status );


CREATE SEQUENCE IF NOT EXISTS ezcurrencydata_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezcurrencydata (
  auto_rate_value numeric(10,5) DEFAULT '0.00000' NOT NULL,
  code character varying(4) DEFAULT ''::character varying NOT NULL,
  custom_rate_value numeric(10,5) DEFAULT '0.00000' NOT NULL,
  id integer DEFAULT nextval('ezcurrencydata_id_seq'::text) NOT NULL,
  locale character varying(255) DEFAULT ''::character varying NOT NULL,
  rate_factor numeric(10,5) DEFAULT '1.00000' NOT NULL,
  status integer DEFAULT 1 NOT NULL,
  symbol character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezcurrencydata_code ON ezcurrencydata ( code );


CREATE SEQUENCE IF NOT EXISTS ezdiscountrule_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezdiscountrule (
  id integer DEFAULT nextval('ezdiscountrule_id_seq'::text) NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezdiscountsubrule_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezdiscountsubrule (
  discount_percent real DEFAULT NULL,
  discountrule_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezdiscountsubrule_id_seq'::text) NOT NULL,
  limitation character(1) DEFAULT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE IF NOT EXISTS ezdiscountsubrule_value (
  discountsubrule_id integer DEFAULT 0 NOT NULL,
  issection integer DEFAULT 0 NOT NULL,
  value integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( discountsubrule_id, value, issection )
);

CREATE TABLE IF NOT EXISTS ezenumobjectvalue (
  contentobject_attribute_id integer DEFAULT 0 NOT NULL,
  contentobject_attribute_version integer DEFAULT 0 NOT NULL,
  enumelement character varying(255) DEFAULT ''::character varying NOT NULL,
  enumid integer DEFAULT 0 NOT NULL,
  enumvalue character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( contentobject_attribute_id, contentobject_attribute_version, enumid )
);

CREATE SEQUENCE IF NOT EXISTS ezenumvalue_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezenumvalue (
  contentclass_attribute_id integer DEFAULT 0 NOT NULL,
  contentclass_attribute_version integer DEFAULT 0 NOT NULL,
  enumelement character varying(255) DEFAULT ''::character varying NOT NULL,
  enumvalue character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezenumvalue_id_seq'::text) NOT NULL,
  placement integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id, contentclass_attribute_id, contentclass_attribute_version )
);
CREATE INDEX ezenumvalue_co_cl_attr_id_co_class_att_ver ON ezenumvalue ( contentclass_attribute_id, contentclass_attribute_version );


CREATE SEQUENCE IF NOT EXISTS ezforgot_password_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezforgot_password (
  hash_key character varying(32) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezforgot_password_id_seq'::text) NOT NULL,
  "time" integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezforgot_password_user ON ezforgot_password ( user_id );


CREATE SEQUENCE IF NOT EXISTS ezgeneral_digest_user_settings_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezgeneral_digest_user_settings (
  "day" character varying(255) DEFAULT ''::character varying NOT NULL,
  digest_type integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezgeneral_digest_user_settings_id_seq'::text) NOT NULL,
  receive_digest integer DEFAULT 0 NOT NULL,
  "time" character varying(255) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX ezgeneral_digest_user_id ON ezgeneral_digest_user_settings ( user_id );


CREATE SEQUENCE IF NOT EXISTS ezimagefile_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezimagefile (
  contentobject_attribute_id integer DEFAULT 0 NOT NULL,
  filepath text NOT NULL,
  id integer DEFAULT nextval('ezimagefile_id_seq'::text) NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezimagefile_coid ON ezimagefile ( contentobject_attribute_id );

CREATE INDEX ezimagefile_file ON ezimagefile ( filepath );


CREATE SEQUENCE IF NOT EXISTS ezinfocollection_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezinfocollection (
  contentobject_id integer DEFAULT 0 NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  creator_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezinfocollection_id_seq'::text) NOT NULL,
  modified integer DEFAULT 0,
  user_identifier character varying(34) DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezinfocollection_co_id_created ON ezinfocollection ( contentobject_id, created );


CREATE SEQUENCE IF NOT EXISTS ezinfocollection_attribute_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezinfocollection_attribute (
  contentclass_attribute_id integer DEFAULT 0 NOT NULL,
  contentobject_attribute_id integer DEFAULT NULL,
  contentobject_id integer DEFAULT NULL,
  data_float real DEFAULT NULL,
  data_int integer DEFAULT NULL,
  data_text text,
  id integer DEFAULT nextval('ezinfocollection_attribute_id_seq'::text) NOT NULL,
  informationcollection_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezinfocollection_attr_cca_id ON ezinfocollection_attribute ( contentclass_attribute_id );

CREATE INDEX ezinfocollection_attr_co_id ON ezinfocollection_attribute ( contentobject_id );

CREATE INDEX ezinfocollection_attr_coa_id ON ezinfocollection_attribute ( contentobject_attribute_id );

CREATE INDEX ezinfocollection_attr_ic_id ON ezinfocollection_attribute ( informationcollection_id );


CREATE SEQUENCE IF NOT EXISTS ezisbn_group_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezisbn_group (
  description character varying(255) DEFAULT ''::character varying NOT NULL,
  group_number integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezisbn_group_id_seq'::text) NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezisbn_group_range_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezisbn_group_range (
  from_number integer DEFAULT 0 NOT NULL,
  group_from character varying(32) DEFAULT ''::character varying NOT NULL,
  group_length integer DEFAULT 0 NOT NULL,
  group_to character varying(32) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezisbn_group_range_id_seq'::text) NOT NULL,
  to_number integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezisbn_registrant_range_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezisbn_registrant_range (
  from_number integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezisbn_registrant_range_id_seq'::text) NOT NULL,
  isbn_group_id integer DEFAULT 0 NOT NULL,
  registrant_from character varying(32) DEFAULT ''::character varying NOT NULL,
  registrant_length integer DEFAULT 0 NOT NULL,
  registrant_to character varying(32) DEFAULT ''::character varying NOT NULL,
  to_number integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezkeyword_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezkeyword (
  class_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezkeyword_id_seq'::text) NOT NULL,
  keyword character varying(255) DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezkeyword_keyword ON ezkeyword ( keyword );


CREATE SEQUENCE IF NOT EXISTS ezkeyword_attribute_link_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezkeyword_attribute_link (
  id integer DEFAULT nextval('ezkeyword_attribute_link_id_seq'::text) NOT NULL,
  keyword_id integer DEFAULT 0 NOT NULL,
  objectattribute_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezkeyword_attr_link_kid_oaid ON ezkeyword_attribute_link ( keyword_id, objectattribute_id );

CREATE INDEX ezkeyword_attr_link_oaid ON ezkeyword_attribute_link ( objectattribute_id );


CREATE TABLE IF NOT EXISTS ezmedia (
  contentobject_attribute_id integer DEFAULT 0 NOT NULL,
  controls character varying(50) DEFAULT NULL,
  filename character varying(255) DEFAULT ''::character varying NOT NULL,
  has_controller integer DEFAULT 0,
  height integer DEFAULT NULL,
  is_autoplay integer DEFAULT 0,
  is_loop integer DEFAULT 0,
  mime_type character varying(50) DEFAULT ''::character varying NOT NULL,
  original_filename character varying(255) DEFAULT ''::character varying NOT NULL,
  pluginspage character varying(255) DEFAULT NULL,
  quality character varying(50) DEFAULT NULL,
  "version" integer DEFAULT 0 NOT NULL,
  width integer DEFAULT NULL,
  PRIMARY KEY ( contentobject_attribute_id, "version" )
);

CREATE SEQUENCE IF NOT EXISTS ezmessage_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezmessage (
  body text,
  destination_address character varying(50) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezmessage_id_seq'::text) NOT NULL,
  is_sent integer DEFAULT 0 NOT NULL,
  send_method character varying(50) DEFAULT ''::character varying NOT NULL,
  send_time character varying(50) DEFAULT ''::character varying NOT NULL,
  send_weekday character varying(50) DEFAULT ''::character varying NOT NULL,
  title character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezmodule_run_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezmodule_run (
  function_name character varying(255) DEFAULT NULL,
  id integer DEFAULT nextval('ezmodule_run_id_seq'::text) NOT NULL,
  module_data text,
  module_name character varying(255) DEFAULT NULL,
  workflow_process_id integer DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX ezmodule_run_workflow_process_id_s ON ezmodule_run ( workflow_process_id );


CREATE SEQUENCE IF NOT EXISTS ezmultipricedata_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezmultipricedata (
  contentobject_attr_id integer DEFAULT 0 NOT NULL,
  contentobject_attr_version integer DEFAULT 0 NOT NULL,
  currency_code character varying(4) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezmultipricedata_id_seq'::text) NOT NULL,
  "type" integer DEFAULT 0 NOT NULL,
  value numeric(15,2) DEFAULT '0.00' NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezmultipricedata_coa_id ON ezmultipricedata ( contentobject_attr_id );

CREATE INDEX ezmultipricedata_coa_version ON ezmultipricedata ( contentobject_attr_version );

CREATE INDEX ezmultipricedata_currency_code ON ezmultipricedata ( currency_code );


CREATE SEQUENCE IF NOT EXISTS eznode_assignment_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS eznode_assignment (
  contentobject_id integer DEFAULT NULL,
  contentobject_version integer DEFAULT NULL,
  from_node_id integer DEFAULT 0,
  id integer DEFAULT nextval('eznode_assignment_id_seq'::text) NOT NULL,
  is_main integer DEFAULT 0 NOT NULL,
  op_code integer DEFAULT 0 NOT NULL,
  parent_node integer DEFAULT NULL,
  parent_remote_id character varying(100) DEFAULT ''::character varying NOT NULL,
  remote_id character varying(100) DEFAULT '0'::character varying NOT NULL,
  sort_field integer DEFAULT 1,
  sort_order integer DEFAULT 1,
  priority integer DEFAULT 0 NOT NULL,
  is_hidden integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX eznode_assignment_co_version ON eznode_assignment ( contentobject_version );

CREATE INDEX eznode_assignment_coid_cov ON eznode_assignment ( contentobject_id, contentobject_version );

CREATE INDEX eznode_assignment_is_main ON eznode_assignment ( is_main );

CREATE INDEX eznode_assignment_parent_node ON eznode_assignment ( parent_node );


CREATE SEQUENCE IF NOT EXISTS eznotificationcollection_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS eznotificationcollection (
  data_subject text NOT NULL,
  data_text text NOT NULL,
  event_id integer DEFAULT 0 NOT NULL,
  "handler" character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('eznotificationcollection_id_seq'::text) NOT NULL,
  transport character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS eznotificationcollection_item_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS eznotificationcollection_item (
  address character varying(255) DEFAULT ''::character varying NOT NULL,
  collection_id integer DEFAULT 0 NOT NULL,
  event_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('eznotificationcollection_item_id_seq'::text) NOT NULL,
  send_date integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS eznotificationevent_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS eznotificationevent (
  data_int1 integer DEFAULT 0 NOT NULL,
  data_int2 integer DEFAULT 0 NOT NULL,
  data_int3 integer DEFAULT 0 NOT NULL,
  data_int4 integer DEFAULT 0 NOT NULL,
  data_text1 text NOT NULL,
  data_text2 text NOT NULL,
  data_text3 text NOT NULL,
  data_text4 text NOT NULL,
  event_type_string character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('eznotificationevent_id_seq'::text) NOT NULL,
  status integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezoperation_memento_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezoperation_memento (
  id integer DEFAULT nextval('ezoperation_memento_id_seq'::text) NOT NULL,
  main integer DEFAULT 0 NOT NULL,
  main_key character varying(32) DEFAULT ''::character varying NOT NULL,
  memento_data text NOT NULL,
  memento_key character varying(32) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id, memento_key )
);
CREATE INDEX ezoperation_memento_memento_key_main ON ezoperation_memento ( memento_key, main );


CREATE SEQUENCE IF NOT EXISTS ezorder_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezorder (
  account_identifier character varying(100) DEFAULT 'default'::character varying NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  data_text_1 text,
  data_text_2 text,
  email character varying(150) DEFAULT ''::character varying,
  id integer DEFAULT nextval('ezorder_id_seq'::text) NOT NULL,
  ignore_vat integer DEFAULT 0 NOT NULL,
  is_archived integer DEFAULT 0 NOT NULL,
  is_temporary integer DEFAULT 1 NOT NULL,
  order_nr integer DEFAULT 0 NOT NULL,
  productcollection_id integer DEFAULT 0 NOT NULL,
  siteaccess character varying(255) DEFAULT ''::character varying NOT NULL,
  status_id integer DEFAULT 0,
  status_modified integer DEFAULT 0,
  status_modifier_id integer DEFAULT 0,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezorder_is_archived ON ezorder ( is_archived );

CREATE INDEX ezorder_is_tmp ON ezorder ( is_temporary );


CREATE SEQUENCE IF NOT EXISTS ezorder_nr_incr_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezorder_nr_incr (
  id integer DEFAULT nextval('ezorder_nr_incr_id_seq'::text) NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezorder_item_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezorder_item (
  description character varying(255) DEFAULT NULL,
  id integer DEFAULT nextval('ezorder_item_id_seq'::text) NOT NULL,
  is_vat_inc integer DEFAULT 0 NOT NULL,
  order_id integer DEFAULT 0 NOT NULL,
  price real DEFAULT NULL,
  "type" character varying(30) DEFAULT NULL,
  vat_value real DEFAULT 0::real NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezorder_item_order_id ON ezorder_item ( order_id );

CREATE INDEX ezorder_item_type ON ezorder_item ( "type" );


CREATE SEQUENCE IF NOT EXISTS ezorder_status_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezorder_status (
  id integer DEFAULT nextval('ezorder_status_id_seq'::text) NOT NULL,
  is_active integer DEFAULT 1 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  status_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezorder_status_active ON ezorder_status ( is_active );

CREATE INDEX ezorder_status_name ON ezorder_status ( name );

CREATE INDEX ezorder_status_sid ON ezorder_status ( status_id );


CREATE SEQUENCE IF NOT EXISTS ezorder_status_history_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezorder_status_history (
  id integer DEFAULT nextval('ezorder_status_history_id_seq'::text) NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  modifier_id integer DEFAULT 0 NOT NULL,
  order_id integer DEFAULT 0 NOT NULL,
  status_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezorder_status_history_mod ON ezorder_status_history ( modified );

CREATE INDEX ezorder_status_history_oid ON ezorder_status_history ( order_id );

CREATE INDEX ezorder_status_history_sid ON ezorder_status_history ( status_id );


CREATE SEQUENCE IF NOT EXISTS ezpackage_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezpackage (
  id integer DEFAULT nextval('ezpackage_id_seq'::text) NOT NULL,
  install_date integer DEFAULT 0 NOT NULL,
  name character varying(100) DEFAULT ''::character varying NOT NULL,
  "version" character varying(30) DEFAULT '0'::character varying NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezpaymentobject_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezpaymentobject (
  id integer DEFAULT nextval('ezpaymentobject_id_seq'::text) NOT NULL,
  order_id integer DEFAULT 0 NOT NULL,
  payment_string character varying(255) DEFAULT ''::character varying NOT NULL,
  status integer DEFAULT 0 NOT NULL,
  workflowprocess_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezpdf_export_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezpdf_export (
  footer_text character varying(255) DEFAULT ''::character varying NOT NULL,
  show_footer integer DEFAULT 1 NOT NULL,
  created integer DEFAULT NULL,
  creator_id integer DEFAULT NULL,
  export_classes character varying(255) DEFAULT NULL,
  export_structure character varying(255) DEFAULT NULL,
  id integer DEFAULT nextval('ezpdf_export_id_seq'::text) NOT NULL,
  intro_text text,
  modified integer DEFAULT NULL,
  modifier_id integer DEFAULT NULL,
  pdf_filename character varying(255) DEFAULT NULL,
  show_frontpage integer DEFAULT NULL,
  site_access character varying(255) DEFAULT NULL,
  source_node_id integer DEFAULT NULL,
  status integer DEFAULT NULL,
  sub_text text,
  title character varying(255) DEFAULT NULL,
  "version" integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id, "version" )
);

CREATE SEQUENCE IF NOT EXISTS ezpending_actions_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezpending_actions (
  id integer DEFAULT nextval('ezpending_actions_id_seq'::text) NOT NULL,
  "action" character varying(64) DEFAULT ''::character varying NOT NULL,
  created integer DEFAULT NULL,
  param text,
  PRIMARY KEY ( id )
);
CREATE INDEX ezpending_actions_action ON ezpending_actions ( "action" );

CREATE INDEX ezpending_actions_created ON ezpending_actions ( created );


CREATE SEQUENCE IF NOT EXISTS ezpolicy_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezpolicy (
  function_name character varying(255) DEFAULT NULL,
  id integer DEFAULT nextval('ezpolicy_id_seq'::text) NOT NULL,
  module_name character varying(255) DEFAULT NULL,
  original_id integer DEFAULT 0 NOT NULL,
  role_id integer DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezpolicy_original_id ON ezpolicy ( original_id );

CREATE INDEX ezpolicy_role_id ON ezpolicy ( role_id );


CREATE SEQUENCE IF NOT EXISTS ezpolicy_limitation_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezpolicy_limitation (
  id integer DEFAULT nextval('ezpolicy_limitation_id_seq'::text) NOT NULL,
  identifier character varying(255) DEFAULT ''::character varying NOT NULL,
  policy_id integer DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX policy_id ON ezpolicy_limitation ( policy_id );


CREATE SEQUENCE IF NOT EXISTS ezpolicy_limitation_value_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezpolicy_limitation_value (
  id integer DEFAULT nextval('ezpolicy_limitation_value_id_seq'::text) NOT NULL,
  limitation_id integer DEFAULT NULL,
  value character varying(255) DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezpolicy_limitation_value_val ON ezpolicy_limitation_value ( value );

CREATE INDEX ezpolicy_limit_value_limit_id ON ezpolicy_limitation_value ( limitation_id );


CREATE SEQUENCE IF NOT EXISTS ezpreferences_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezpreferences (
  id integer DEFAULT nextval('ezpreferences_id_seq'::text) NOT NULL,
  name character varying(100) DEFAULT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  value text,
  PRIMARY KEY ( id )
);
CREATE INDEX ezpreferences_name ON ezpreferences ( name );

CREATE INDEX ezpreferences_user_id_idx ON ezpreferences ( user_id, name );


CREATE TABLE IF NOT EXISTS ezprest_authcode (
  client_id character varying(200) DEFAULT ''::character varying NOT NULL,
  code_challenge character varying(128) DEFAULT NULL,
  code_challenge_method character varying(10) DEFAULT NULL,
  expirytime bigint DEFAULT '0' NOT NULL,
  id character varying(200) DEFAULT ''::character varying NOT NULL,
  scope character varying(200) DEFAULT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX authcode_client_id ON ezprest_authcode ( client_id );


CREATE SEQUENCE IF NOT EXISTS ezprest_authorized_clients_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezprest_authorized_clients (
  created integer DEFAULT NULL,
  id integer DEFAULT nextval('ezprest_authorized_clients_id_seq'::text) NOT NULL,
  rest_client_id integer DEFAULT NULL,
  user_id integer DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX client_user ON ezprest_authorized_clients ( rest_client_id, user_id );


CREATE SEQUENCE IF NOT EXISTS ezprest_clients_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezprest_clients (
  client_id character varying(200) DEFAULT NULL,
  client_secret character varying(200) DEFAULT NULL,
  created integer DEFAULT 0 NOT NULL,
  description text,
  endpoint_uri character varying(200) DEFAULT NULL,
  id integer DEFAULT nextval('ezprest_clients_id_seq'::text) NOT NULL,
  name character varying(100) DEFAULT NULL,
  owner_id integer DEFAULT 0 NOT NULL,
  updated integer DEFAULT 0 NOT NULL,
  "version" integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX client_id_unique ON ezprest_clients ( client_id, "version" );


CREATE TABLE IF NOT EXISTS ezprest_token (
  client_id character varying(200) DEFAULT ''::character varying NOT NULL,
  expirytime bigint DEFAULT '0' NOT NULL,
  id character varying(200) DEFAULT ''::character varying NOT NULL,
  refresh_token character varying(200) DEFAULT ''::character varying NOT NULL,
  scope character varying(200) DEFAULT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX token_client_id ON ezprest_token ( client_id );


CREATE SEQUENCE IF NOT EXISTS expapikey_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS expapikey (
  created integer DEFAULT 0 NOT NULL,
  created_by integer DEFAULT 0 NOT NULL,
  expires integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('expapikey_id_seq'::text) NOT NULL,
  key_prefix character varying(40) DEFAULT ''::character varying NOT NULL,
  last_ip character varying(64) DEFAULT ''::character varying NOT NULL,
  last_used integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  revoked integer DEFAULT 0 NOT NULL,
  revoked_by integer DEFAULT 0 NOT NULL,
  salt character varying(64) DEFAULT ''::character varying NOT NULL,
  scopes character varying(255) DEFAULT ''::character varying NOT NULL,
  secret_hash character varying(128) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX expapikey_prefix ON expapikey ( key_prefix );

CREATE INDEX expapikey_user ON expapikey ( user_id );


CREATE SEQUENCE IF NOT EXISTS exppasskey_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS exppasskey (
  aaguid character varying(36) DEFAULT ''::character varying NOT NULL,
  algorithm integer DEFAULT 0 NOT NULL,
  backup_eligible integer DEFAULT 0 NOT NULL,
  backup_state integer DEFAULT 0 NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  credential_hash character varying(64) DEFAULT ''::character varying NOT NULL,
  credential_id text DEFAULT NULL,
  id integer DEFAULT nextval('exppasskey_id_seq'::text) NOT NULL,
  last_ip character varying(64) DEFAULT ''::character varying NOT NULL,
  last_used integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  public_key text DEFAULT NULL,
  sign_count bigint DEFAULT '0' NOT NULL,
  transports character varying(255) DEFAULT ''::character varying NOT NULL,
  user_handle character varying(128) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX exppasskey_credential ON exppasskey ( credential_hash );

CREATE INDEX exppasskey_user ON exppasskey ( user_id );


CREATE SEQUENCE IF NOT EXISTS exp_transfer_run_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS exp_transfer_run (
  bytes bigint DEFAULT '0' NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  detail text,
  detail_url character varying(255) DEFAULT ''::character varying NOT NULL,
  direction character varying(10) DEFAULT ''::character varying NOT NULL,
  duration integer DEFAULT 0 NOT NULL,
  engine character varying(16) DEFAULT ''::character varying NOT NULL,
  error character varying(255) DEFAULT ''::character varying NOT NULL,
  file_ref character varying(255) DEFAULT ''::character varying NOT NULL,
  finished integer DEFAULT 0 NOT NULL,
  format character varying(32) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('exp_transfer_run_id_seq'::text) NOT NULL,
  job_ref character varying(64) DEFAULT ''::character varying NOT NULL,
  kind character varying(20) DEFAULT ''::character varying NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  rows_done integer DEFAULT 0 NOT NULL,
  rows_failed integer DEFAULT 0 NOT NULL,
  rows_total integer DEFAULT 0 NOT NULL,
  scope character varying(20) DEFAULT ''::character varying NOT NULL,
  scope_ref character varying(255) DEFAULT ''::character varying NOT NULL,
  siteaccess character varying(64) DEFAULT ''::character varying NOT NULL,
  started integer DEFAULT 0 NOT NULL,
  state character varying(16) DEFAULT ''::character varying NOT NULL,
  title character varying(255) DEFAULT ''::character varying NOT NULL,
  tool character varying(64) DEFAULT ''::character varying NOT NULL,
  tool_ref character varying(64) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX exp_transfer_run_started ON exp_transfer_run ( started );

CREATE INDEX exp_transfer_run_tool ON exp_transfer_run ( tool, started );

CREATE INDEX exp_transfer_run_toolref ON exp_transfer_run ( tool, tool_ref );

CREATE INDEX exp_transfer_run_user ON exp_transfer_run ( user_id );


CREATE SEQUENCE IF NOT EXISTS exp_data_connection_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS exp_data_connection (
  created integer DEFAULT 0 NOT NULL,
  creator_id integer DEFAULT 0 NOT NULL,
  database_name character varying(255) DEFAULT ''::character varying NOT NULL,
  description character varying(255) DEFAULT ''::character varying NOT NULL,
  engine character varying(32) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('exp_data_connection_id_seq'::text) NOT NULL,
  identifier character varying(64) DEFAULT ''::character varying NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  modifier_id integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  options text,
  port integer DEFAULT 0 NOT NULL,
  read_only integer DEFAULT 0 NOT NULL,
  secret character varying(128) DEFAULT ''::character varying NOT NULL,
  server character varying(255) DEFAULT ''::character varying NOT NULL,
  user_name character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX exp_data_connection_identifier ON exp_data_connection ( identifier );


CREATE SEQUENCE IF NOT EXISTS exp_secret_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS exp_secret (
  ciphertext text,
  created integer DEFAULT 0 NOT NULL,
  creator_id integer DEFAULT 0 NOT NULL,
  description character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('exp_secret_id_seq'::text) NOT NULL,
  key_id character varying(16) DEFAULT ''::character varying NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  modifier_id integer DEFAULT 0 NOT NULL,
  name character varying(128) DEFAULT ''::character varying NOT NULL,
  scope character varying(64) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX exp_secret_name ON exp_secret ( name );

CREATE INDEX exp_secret_scope ON exp_secret ( scope );


CREATE SEQUENCE IF NOT EXISTS exp_data_sync_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS exp_data_sync (
  class_name character varying(128) DEFAULT ''::character varying NOT NULL,
  conflict_rule character varying(16) DEFAULT ''::character varying NOT NULL,
  content_class character varying(128) DEFAULT ''::character varying NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  creator_id integer DEFAULT 0 NOT NULL,
  direction character varying(16) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('exp_data_sync_id_seq'::text) NOT NULL,
  interval_seconds integer DEFAULT 0 NOT NULL,
  key_columns character varying(255) DEFAULT ''::character varying NOT NULL,
  last_run integer DEFAULT 0 NOT NULL,
  last_run_id integer DEFAULT 0 NOT NULL,
  map text,
  modified integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  next_run integer DEFAULT 0 NOT NULL,
  options text,
  run_mode character varying(16) DEFAULT ''::character varying NOT NULL,
  state character varying(16) DEFAULT ''::character varying NOT NULL,
  subtree_node_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX exp_data_sync_mode ON exp_data_sync ( run_mode, state );


CREATE SEQUENCE IF NOT EXISTS exp_data_sync_conflict_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS exp_data_sync_conflict (
  content_values text,
  detected integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('exp_data_sync_conflict_id_seq'::text) NOT NULL,
  link_id integer DEFAULT 0 NOT NULL,
  object_id integer DEFAULT 0 NOT NULL,
  resolution character varying(16) DEFAULT ''::character varying NOT NULL,
  resolved integer DEFAULT 0 NOT NULL,
  resolver_id integer DEFAULT 0 NOT NULL,
  row_key character varying(255) DEFAULT ''::character varying NOT NULL,
  row_values text,
  state character varying(16) DEFAULT ''::character varying NOT NULL,
  sync_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX exp_data_sync_conflict_state ON exp_data_sync_conflict ( sync_id, state );


CREATE SEQUENCE IF NOT EXISTS exp_data_sync_link_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS exp_data_sync_link (
  content_hash character varying(32) DEFAULT ''::character varying NOT NULL,
  content_modified integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('exp_data_sync_link_id_seq'::text) NOT NULL,
  object_id integer DEFAULT 0 NOT NULL,
  row_hash character varying(32) DEFAULT ''::character varying NOT NULL,
  row_key character varying(255) DEFAULT ''::character varying NOT NULL,
  row_modified integer DEFAULT 0 NOT NULL,
  sync_id integer DEFAULT 0 NOT NULL,
  synced integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX exp_data_sync_link_key ON exp_data_sync_link ( sync_id, row_key );

CREATE INDEX exp_data_sync_link_object ON exp_data_sync_link ( sync_id, object_id );


CREATE SEQUENCE IF NOT EXISTS expuser_session_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS expuser_session (
  browser character varying(64) DEFAULT ''::character varying NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  device_type character varying(16) DEFAULT ''::character varying NOT NULL,
  end_reason character varying(32) DEFAULT ''::character varying NOT NULL,
  ended integer DEFAULT 0 NOT NULL,
  ended_by integer DEFAULT 0 NOT NULL,
  first_ip character varying(64) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('expuser_session_id_seq'::text) NOT NULL,
  last_active integer DEFAULT 0 NOT NULL,
  last_ip character varying(64) DEFAULT ''::character varying NOT NULL,
  method character varying(32) DEFAULT ''::character varying NOT NULL,
  os character varying(64) DEFAULT ''::character varying NOT NULL,
  remember_until integer DEFAULT 0 NOT NULL,
  siteaccess character varying(64) DEFAULT ''::character varying NOT NULL,
  token_hash character varying(64) DEFAULT ''::character varying NOT NULL,
  user_agent character varying(512) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX expuser_session_token ON expuser_session ( token_hash );

CREATE INDEX expuser_session_user ON expuser_session ( user_id, ended );

CREATE INDEX expuser_session_active ON expuser_session ( ended, last_active );


CREATE TABLE IF NOT EXISTS expuser_session_info (
  auth_detail character varying(32) DEFAULT ''::character varying NOT NULL,
  country character varying(2) DEFAULT ''::character varying NOT NULL,
  host character varying(255) DEFAULT ''::character varying NOT NULL,
  "location" character varying(128) DEFAULT ''::character varying NOT NULL,
  session_id integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( session_id )
);
CREATE INDEX expuser_session_info_user ON expuser_session_info ( user_id );


CREATE SEQUENCE IF NOT EXISTS expuser_audit_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS expuser_audit (
  actor_id integer DEFAULT 0 NOT NULL,
  actor_login character varying(150) DEFAULT ''::character varying NOT NULL,
  client_ip character varying(64) DEFAULT ''::character varying NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  event_id character varying(26) DEFAULT ''::character varying NOT NULL,
  event_name character varying(128) DEFAULT ''::character varying NOT NULL,
  host character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('expuser_audit_id_seq'::text) NOT NULL,
  module_view character varying(128) DEFAULT ''::character varying NOT NULL,
  reason character varying(64) DEFAULT ''::character varying NOT NULL,
  request_id character varying(40) DEFAULT ''::character varying NOT NULL,
  result character varying(16) DEFAULT ''::character varying NOT NULL,
  siteaccess character varying(64) DEFAULT ''::character varying NOT NULL,
  target_id integer DEFAULT 0 NOT NULL,
  target_login character varying(150) DEFAULT ''::character varying NOT NULL,
  target_type character varying(16) DEFAULT ''::character varying NOT NULL,
  time_ms bigint DEFAULT '0' NOT NULL,
  value_after text DEFAULT NULL,
  value_before text DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX expuser_audit_event ON expuser_audit ( event_id );

CREATE INDEX expuser_audit_target ON expuser_audit ( target_id, created );

CREATE INDEX expuser_audit_actor ON expuser_audit ( actor_id, created );

CREATE INDEX expuser_audit_name ON expuser_audit ( event_name, created );

CREATE INDEX expuser_audit_created ON expuser_audit ( created );


CREATE TABLE IF NOT EXISTS exphelp_help_id (
  help_id character varying(80) DEFAULT ''::character varying NOT NULL,
  object_id integer DEFAULT 0 NOT NULL,
  released integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( help_id )
);
CREATE INDEX exphelp_help_id_object ON exphelp_help_id ( object_id );


CREATE SEQUENCE IF NOT EXISTS exphelp_feedback_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS exphelp_feedback (
  created integer DEFAULT 0 NOT NULL,
  help_id character varying(80) DEFAULT ''::character varying NOT NULL,
  helpful integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('exphelp_feedback_id_seq'::text) NOT NULL,
  "language" character varying(20) DEFAULT ''::character varying NOT NULL,
  object_id integer DEFAULT 0 NOT NULL,
  reason character varying(32) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX exphelp_feedback_object ON exphelp_feedback ( object_id, created );

CREATE INDEX exphelp_feedback_created ON exphelp_feedback ( created );


CREATE TABLE IF NOT EXISTS exp_migration (
  caption_id character varying(150) DEFAULT ''::character varying NOT NULL,
  checksum character varying(64) DEFAULT ''::character varying NOT NULL,
  duration_ms integer DEFAULT 0 NOT NULL,
  engine character varying(30) DEFAULT ''::character varying NOT NULL,
  environment character varying(50) DEFAULT ''::character varying NOT NULL,
  file_path character varying(255) DEFAULT ''::character varying NOT NULL,
  finished_at integer DEFAULT 0 NOT NULL,
  message character varying(1000) DEFAULT ''::character varying NOT NULL,
  migration_id character varying(150) DEFAULT ''::character varying NOT NULL,
  revision character varying(64) DEFAULT ''::character varying NOT NULL,
  run_by character varying(150) DEFAULT ''::character varying NOT NULL,
  run_id character varying(64) DEFAULT ''::character varying NOT NULL,
  source character varying(100) DEFAULT ''::character varying NOT NULL,
  stage character varying(20) DEFAULT ''::character varying NOT NULL,
  started_at integer DEFAULT 0 NOT NULL,
  state character varying(20) DEFAULT ''::character varying NOT NULL,
  "version" character varying(30) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( source, migration_id )
);
CREATE INDEX exp_migration_run_id ON exp_migration ( run_id );


CREATE TABLE IF NOT EXISTS exp_migration_deploy (
  deploy_id character varying(64) DEFAULT ''::character varying NOT NULL,
  finished_at integer DEFAULT 0 NOT NULL,
  in_sync integer DEFAULT 0 NOT NULL,
  message character varying(1000) DEFAULT ''::character varying NOT NULL,
  "mode" character varying(20) DEFAULT ''::character varying NOT NULL,
  next_stage integer DEFAULT 0 NOT NULL,
  run_by character varying(150) DEFAULT ''::character varying NOT NULL,
  stages integer DEFAULT 0 NOT NULL,
  started_at integer DEFAULT 0 NOT NULL,
  state character varying(20) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( deploy_id )
);
CREATE INDEX exp_migration_deploy_started ON exp_migration_deploy ( started_at );


CREATE TABLE IF NOT EXISTS exp_migration_deploy_host (
  address character varying(255) DEFAULT ''::character varying NOT NULL,
  applied integer DEFAULT 0 NOT NULL,
  caption character varying(150) DEFAULT ''::character varying NOT NULL,
  deploy_id character varying(64) DEFAULT ''::character varying NOT NULL,
  environment character varying(50) DEFAULT ''::character varying NOT NULL,
  message character varying(1000) DEFAULT ''::character varying NOT NULL,
  remote_id character varying(64) DEFAULT ''::character varying NOT NULL,
  stage integer DEFAULT 0 NOT NULL,
  state character varying(20) DEFAULT ''::character varying NOT NULL,
  transport character varying(20) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( deploy_id, remote_id )
);
CREATE INDEX exp_migration_deploy_host_remote ON exp_migration_deploy_host ( remote_id );


CREATE TABLE IF NOT EXISTS exp_migration_run (
  caption_post character varying(150) DEFAULT ''::character varying NOT NULL,
  caption_pre character varying(150) DEFAULT ''::character varying NOT NULL,
  commit_id character varying(64) DEFAULT ''::character varying NOT NULL,
  environment character varying(50) DEFAULT ''::character varying NOT NULL,
  finished_at integer DEFAULT 0 NOT NULL,
  log_file character varying(255) DEFAULT ''::character varying NOT NULL,
  message character varying(1000) DEFAULT ''::character varying NOT NULL,
  push_state character varying(20) DEFAULT ''::character varying NOT NULL,
  run_by character varying(150) DEFAULT ''::character varying NOT NULL,
  run_id character varying(64) DEFAULT ''::character varying NOT NULL,
  started_at integer DEFAULT 0 NOT NULL,
  state character varying(20) DEFAULT ''::character varying NOT NULL,
  tag_name character varying(150) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( run_id )
);
CREATE INDEX exp_migration_run_started ON exp_migration_run ( started_at );


CREATE TABLE IF NOT EXISTS exp_static_page (
  built_at integer DEFAULT 0 NOT NULL,
  hash character varying(64) DEFAULT ''::character varying NOT NULL,
  node_id integer DEFAULT 0 NOT NULL,
  page_bytes integer DEFAULT 0 NOT NULL,
  "path" character varying(255) DEFAULT ''::character varying NOT NULL,
  stale integer DEFAULT 0 NOT NULL,
  target character varying(50) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( target, "path" )
);
CREATE INDEX exp_static_page_node ON exp_static_page ( node_id );


CREATE TABLE IF NOT EXISTS exp_static_run (
  added integer DEFAULT 0 NOT NULL,
  bytes integer DEFAULT 0 NOT NULL,
  changed integer DEFAULT 0 NOT NULL,
  commit_id character varying(64) DEFAULT ''::character varying NOT NULL,
  files integer DEFAULT 0 NOT NULL,
  finished_at integer DEFAULT 0 NOT NULL,
  kind character varying(20) DEFAULT ''::character varying NOT NULL,
  message character varying(1000) DEFAULT ''::character varying NOT NULL,
  pages integer DEFAULT 0 NOT NULL,
  push_state character varying(20) DEFAULT ''::character varying NOT NULL,
  removed integer DEFAULT 0 NOT NULL,
  run_by character varying(150) DEFAULT ''::character varying NOT NULL,
  run_id character varying(64) DEFAULT ''::character varying NOT NULL,
  started_at integer DEFAULT 0 NOT NULL,
  state character varying(20) DEFAULT ''::character varying NOT NULL,
  tag_name character varying(150) DEFAULT ''::character varying NOT NULL,
  target character varying(50) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( run_id )
);
CREATE INDEX exp_static_run_target ON exp_static_run ( target, started_at );


CREATE TABLE IF NOT EXISTS exp_migration_recording (
  environment character varying(50) DEFAULT ''::character varying NOT NULL,
  event_count integer DEFAULT 0 NOT NULL,
  kind character varying(20) DEFAULT ''::character varying NOT NULL,
  label character varying(255) DEFAULT ''::character varying NOT NULL,
  message character varying(1000) DEFAULT ''::character varying NOT NULL,
  recording_id character varying(64) DEFAULT ''::character varying NOT NULL,
  started_at integer DEFAULT 0 NOT NULL,
  state character varying(20) DEFAULT ''::character varying NOT NULL,
  stopped_at integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( recording_id )
);
CREATE INDEX exp_migration_recording_state ON exp_migration_recording ( state );


CREATE TABLE IF NOT EXISTS exp_migration_recording_event (
  actor_id integer DEFAULT 0 NOT NULL,
  audit_name character varying(100) DEFAULT ''::character varying NOT NULL,
  event_id character varying(32) DEFAULT ''::character varying NOT NULL,
  payload text DEFAULT NULL,
  recorded_at integer DEFAULT 0 NOT NULL,
  recording_id character varying(64) DEFAULT ''::character varying NOT NULL,
  step_mode character varying(20) DEFAULT ''::character varying NOT NULL,
  step_type character varying(50) DEFAULT ''::character varying NOT NULL,
  subject character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( recording_id, event_id )
);

CREATE SEQUENCE IF NOT EXISTS ezproductcategory_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezproductcategory (
  id integer DEFAULT nextval('ezproductcategory_id_seq'::text) NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezproductcollection_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezproductcollection (
  created integer DEFAULT NULL,
  currency_code character varying(4) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezproductcollection_id_seq'::text) NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezproductcollection_item_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezproductcollection_item (
  contentobject_id integer DEFAULT 0 NOT NULL,
  discount real DEFAULT NULL,
  id integer DEFAULT nextval('ezproductcollection_item_id_seq'::text) NOT NULL,
  is_vat_inc integer DEFAULT NULL,
  item_count integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  price real DEFAULT 0::real,
  productcollection_id integer DEFAULT 0 NOT NULL,
  vat_value real DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezproductcollection_item_contentobject_id ON ezproductcollection_item ( contentobject_id );

CREATE INDEX ezproductcollection_item_productcollection_id ON ezproductcollection_item ( productcollection_id );


CREATE SEQUENCE IF NOT EXISTS ezproductcollection_item_opt_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezproductcollection_item_opt (
  id integer DEFAULT nextval('ezproductcollection_item_opt_id_seq'::text) NOT NULL,
  item_id integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  object_attribute_id integer DEFAULT NULL,
  option_item_id integer DEFAULT 0 NOT NULL,
  price real DEFAULT 0::real NOT NULL,
  value character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezproductcollection_item_opt_item_id ON ezproductcollection_item_opt ( item_id );


CREATE TABLE IF NOT EXISTS ezpublishingqueueprocesses (
  created integer DEFAULT NULL,
  ezcontentobject_version_id integer DEFAULT 0 NOT NULL,
  finished integer DEFAULT NULL,
  pid integer DEFAULT NULL,
  started integer DEFAULT NULL,
  status integer DEFAULT NULL,
  PRIMARY KEY ( ezcontentobject_version_id )
);

CREATE SEQUENCE IF NOT EXISTS ezrole_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezrole (
  id integer DEFAULT nextval('ezrole_id_seq'::text) NOT NULL,
  is_new integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  value character(1) DEFAULT NULL,
  "version" integer DEFAULT 0,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezrss_export_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezrss_export (
  access_url character varying(255) DEFAULT NULL,
  opml_head text,
  podcast_head text,
  active integer DEFAULT NULL,
  created integer DEFAULT NULL,
  creator_id integer DEFAULT NULL,
  description text,
  id integer DEFAULT nextval('ezrss_export_id_seq'::text) NOT NULL,
  image_id integer DEFAULT NULL,
  main_node_only integer DEFAULT 1 NOT NULL,
  modified integer DEFAULT NULL,
  modifier_id integer DEFAULT NULL,
  node_id integer DEFAULT NULL,
  number_of_objects integer DEFAULT 0 NOT NULL,
  rss_version character varying(255) DEFAULT NULL,
  site_access character varying(255) DEFAULT NULL,
  status integer DEFAULT 0 NOT NULL,
  title character varying(255) DEFAULT NULL,
  url character varying(255) DEFAULT NULL,
  PRIMARY KEY ( id, status )
);

CREATE SEQUENCE IF NOT EXISTS ezrss_export_item_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezrss_export_item (
  category character varying(255) DEFAULT NULL,
  class_id integer DEFAULT NULL,
  description character varying(255) DEFAULT NULL,
  enclosure character varying(255) DEFAULT NULL,
  id integer DEFAULT nextval('ezrss_export_item_id_seq'::text) NOT NULL,
  rssexport_id integer DEFAULT NULL,
  source_node_id integer DEFAULT NULL,
  status integer DEFAULT 0 NOT NULL,
  subnodes integer DEFAULT 0 NOT NULL,
  title character varying(255) DEFAULT NULL,
  PRIMARY KEY ( id, status )
);
CREATE INDEX ezrss_export_rsseid ON ezrss_export_item ( rssexport_id );


CREATE SEQUENCE IF NOT EXISTS ezrss_export_opml_item_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezrss_export_opml_item (
  category character varying(255) DEFAULT NULL,
  created integer DEFAULT 0 NOT NULL,
  description character varying(255) DEFAULT NULL,
  html_url character varying(255) DEFAULT NULL,
  id integer DEFAULT nextval('ezrss_export_opml_item_id_seq'::text) NOT NULL,
  is_breakpoint integer DEFAULT 0 NOT NULL,
  is_comment integer DEFAULT 0 NOT NULL,
  "language" character varying(50) DEFAULT NULL,
  outline_text character varying(255) DEFAULT NULL,
  outline_type character varying(50) DEFAULT 'rss'::character varying,
  parent_id integer DEFAULT 0 NOT NULL,
  priority integer DEFAULT 0 NOT NULL,
  rssexport_id integer DEFAULT 0 NOT NULL,
  source_node_id integer DEFAULT 0 NOT NULL,
  status integer DEFAULT 0 NOT NULL,
  subnodes integer DEFAULT 0 NOT NULL,
  target_export_id integer DEFAULT 0 NOT NULL,
  title character varying(255) DEFAULT NULL,
  url character varying(255) DEFAULT NULL,
  xml_url character varying(255) DEFAULT NULL,
  PRIMARY KEY ( id, status )
);
CREATE INDEX ezrss_export_opml_rsseid ON ezrss_export_opml_item ( rssexport_id );


CREATE SEQUENCE IF NOT EXISTS ezrss_import_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezrss_import (
  active integer DEFAULT NULL,
  class_description character varying(255) DEFAULT NULL,
  class_id integer DEFAULT NULL,
  class_title character varying(255) DEFAULT NULL,
  class_url character varying(255) DEFAULT NULL,
  created integer DEFAULT NULL,
  creator_id integer DEFAULT NULL,
  destination_node_id integer DEFAULT NULL,
  id integer DEFAULT nextval('ezrss_import_id_seq'::text) NOT NULL,
  import_description text NOT NULL,
  modified integer DEFAULT NULL,
  modifier_id integer DEFAULT NULL,
  name character varying(255) DEFAULT NULL,
  object_owner_id integer DEFAULT NULL,
  status integer DEFAULT 0 NOT NULL,
  url text,
  PRIMARY KEY ( id, status )
);

CREATE SEQUENCE IF NOT EXISTS ezscheduled_script_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezscheduled_script (
  command character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezscheduled_script_id_seq'::text) NOT NULL,
  last_report_timestamp integer DEFAULT 0 NOT NULL,
  name character varying(50) DEFAULT ''::character varying NOT NULL,
  process_id integer DEFAULT 0 NOT NULL,
  progress integer DEFAULT 0,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezscheduled_script_timestamp ON ezscheduled_script ( last_report_timestamp );


CREATE SEQUENCE IF NOT EXISTS ezsearch_object_word_link_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezsearch_object_word_link (
  contentclass_attribute_id integer DEFAULT 0 NOT NULL,
  contentclass_id integer DEFAULT 0 NOT NULL,
  contentobject_id integer DEFAULT 0 NOT NULL,
  frequency real DEFAULT 0::real NOT NULL,
  id integer DEFAULT nextval('ezsearch_object_word_link_id_seq'::text) NOT NULL,
  identifier character varying(255) DEFAULT ''::character varying NOT NULL,
  integer_value integer DEFAULT 0 NOT NULL,
  next_word_id integer DEFAULT 0 NOT NULL,
  placement integer DEFAULT 0 NOT NULL,
  prev_word_id integer DEFAULT 0 NOT NULL,
  published integer DEFAULT 0 NOT NULL,
  section_id integer DEFAULT 0 NOT NULL,
  word_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezsearch_object_word_link_frequency ON ezsearch_object_word_link ( frequency );

CREATE INDEX ezsearch_object_word_link_identifier ON ezsearch_object_word_link ( identifier );

CREATE INDEX ezsearch_object_word_link_integer_value ON ezsearch_object_word_link ( integer_value );

CREATE INDEX ezsearch_object_word_link_object ON ezsearch_object_word_link ( contentobject_id );

CREATE INDEX ezsearch_object_word_link_word ON ezsearch_object_word_link ( word_id );


CREATE SEQUENCE IF NOT EXISTS ezsearch_search_phrase_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezsearch_search_phrase (
  id integer DEFAULT nextval('ezsearch_search_phrase_id_seq'::text) NOT NULL,
  phrase character varying(250) DEFAULT NULL,
  phrase_count integer DEFAULT 0,
  result_count integer DEFAULT 0,
  PRIMARY KEY ( id )
);
CREATE INDEX ezsearch_search_phrase_count ON ezsearch_search_phrase ( phrase_count );

CREATE UNIQUE INDEX ezsearch_search_phrase_phrase ON ezsearch_search_phrase ( phrase );


CREATE SEQUENCE IF NOT EXISTS ezsearch_word_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezsearch_word (
  id integer DEFAULT nextval('ezsearch_word_id_seq'::text) NOT NULL,
  object_count integer DEFAULT 0 NOT NULL,
  word character varying(150) DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezsearch_word_obj_count ON ezsearch_word ( object_count );

CREATE INDEX ezsearch_word_word_i ON ezsearch_word ( word );


CREATE SEQUENCE IF NOT EXISTS ezsection_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezsection (
  id integer DEFAULT nextval('ezsection_id_seq'::text) NOT NULL,
  identifier character varying(255) DEFAULT NULL,
  locale character varying(255) DEFAULT NULL,
  name character varying(255) DEFAULT NULL,
  navigation_part_identifier character varying(100) DEFAULT 'ezcontentnavigationpart'::character varying,
  PRIMARY KEY ( id )
);

CREATE TABLE IF NOT EXISTS ezsession (
  data text NOT NULL,
  expiration_time integer DEFAULT 0 NOT NULL,
  session_key character varying(32) DEFAULT ''::character varying NOT NULL,
  user_hash character varying(32) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( session_key )
);
CREATE INDEX expiration_time ON ezsession ( expiration_time );

CREATE INDEX ezsession_user_id ON ezsession ( user_id );


CREATE TABLE IF NOT EXISTS ezsite_data (
  name character varying(60) DEFAULT ''::character varying NOT NULL,
  value text NOT NULL,
  PRIMARY KEY ( name )
);

CREATE SEQUENCE IF NOT EXISTS ezsubtree_notification_rule_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezsubtree_notification_rule (
  id integer DEFAULT nextval('ezsubtree_notification_rule_id_seq'::text) NOT NULL,
  node_id integer DEFAULT 0 NOT NULL,
  use_digest integer DEFAULT 0,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezsubtree_notification_rule_user_id ON ezsubtree_notification_rule ( user_id );


CREATE TABLE IF NOT EXISTS eztipafriend_counter (
  count integer DEFAULT 0 NOT NULL,
  node_id integer DEFAULT 0 NOT NULL,
  requested integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( node_id, requested )
);

CREATE TABLE IF NOT EXISTS eztipafriend_request (
  created integer DEFAULT 0 NOT NULL,
  email_receiver character varying(100) DEFAULT ''::character varying NOT NULL
);
CREATE INDEX eztipafriend_request_created ON eztipafriend_request ( created );

CREATE INDEX eztipafriend_request_email_rec ON eztipafriend_request ( email_receiver );


CREATE SEQUENCE IF NOT EXISTS eztrigger_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS eztrigger (
  connect_type character(1) DEFAULT ''::bpchar NOT NULL,
  function_name character varying(200) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('eztrigger_id_seq'::text) NOT NULL,
  module_name character varying(200) DEFAULT ''::character varying NOT NULL,
  name character varying(255) DEFAULT NULL,
  workflow_id integer DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE UNIQUE INDEX eztrigger_def_id ON eztrigger ( module_name, function_name, connect_type );

CREATE INDEX eztrigger_fetch ON eztrigger ( name, module_name, function_name );


CREATE SEQUENCE IF NOT EXISTS ezurl_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezurl (
  created integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezurl_id_seq'::text) NOT NULL,
  is_valid integer DEFAULT 1 NOT NULL,
  last_checked integer DEFAULT 0 NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  original_url_md5 character varying(32) DEFAULT ''::character varying NOT NULL,
  url text,
  PRIMARY KEY ( id )
);
CREATE INDEX ezurl_url ON ezurl ( url );


CREATE TABLE IF NOT EXISTS ezurl_object_link (
  contentobject_attribute_id integer DEFAULT 0 NOT NULL,
  contentobject_attribute_version integer DEFAULT 0 NOT NULL,
  url_id integer DEFAULT 0 NOT NULL
);
CREATE INDEX ezurl_ol_coa_id ON ezurl_object_link ( contentobject_attribute_id );

CREATE INDEX ezurl_ol_coa_version ON ezurl_object_link ( contentobject_attribute_version );

CREATE INDEX ezurl_ol_url_id ON ezurl_object_link ( url_id );


CREATE SEQUENCE IF NOT EXISTS ezurlalias_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezurlalias (
  destination_url text NOT NULL,
  forward_to_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezurlalias_id_seq'::text) NOT NULL,
  is_imported integer DEFAULT 0 NOT NULL,
  is_internal integer DEFAULT 1 NOT NULL,
  is_wildcard integer DEFAULT 0 NOT NULL,
  source_md5 character varying(32) DEFAULT NULL,
  source_url text NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezurlalias_desturl ON ezurlalias ( destination_url );

CREATE INDEX ezurlalias_forward_to_id ON ezurlalias ( forward_to_id );

CREATE INDEX ezurlalias_imp_wcard_fwd ON ezurlalias ( is_imported, is_wildcard, forward_to_id );

CREATE INDEX ezurlalias_source_md5 ON ezurlalias ( source_md5 );

CREATE INDEX ezurlalias_source_url ON ezurlalias ( source_url );

CREATE INDEX ezurlalias_wcard_fwd ON ezurlalias ( is_wildcard, forward_to_id );


CREATE TABLE IF NOT EXISTS ezurlalias_ml (
  "action" text NOT NULL,
  action_type character varying(32) DEFAULT ''::character varying NOT NULL,
  alias_redirects integer DEFAULT 1 NOT NULL,
  id integer DEFAULT 0 NOT NULL,
  is_alias integer DEFAULT 0 NOT NULL,
  is_original integer DEFAULT 0 NOT NULL,
  lang_mask bigint DEFAULT '0' NOT NULL,
  link integer DEFAULT 0 NOT NULL,
  parent integer DEFAULT 0 NOT NULL,
  text text NOT NULL,
  text_md5 character varying(32) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( parent, text_md5 )
);
CREATE INDEX ezurlalias_ml_act_org ON ezurlalias_ml ( "action", is_original );

CREATE INDEX ezurlalias_ml_actt_org_al ON ezurlalias_ml ( action_type, is_original, is_alias );

CREATE INDEX ezurlalias_ml_id ON ezurlalias_ml ( id );

CREATE INDEX ezurlalias_ml_par_act_id_lnk ON ezurlalias_ml ( "action", id, link, parent );

CREATE INDEX ezurlalias_ml_par_lnk_txt ON ezurlalias_ml ( parent, text, link );

CREATE INDEX ezurlalias_ml_text ON ezurlalias_ml ( text, id, link );

CREATE INDEX ezurlalias_ml_text_lang ON ezurlalias_ml ( text, lang_mask, parent );


CREATE SEQUENCE IF NOT EXISTS ezurlalias_ml_incr_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezurlalias_ml_incr (
  id integer DEFAULT nextval('ezurlalias_ml_incr_id_seq'::text) NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezurlwildcard_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezurlwildcard (
  destination_url text NOT NULL,
  id integer DEFAULT nextval('ezurlwildcard_id_seq'::text) NOT NULL,
  source_url text NOT NULL,
  "type" integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE IF NOT EXISTS ezuser (
  contentobject_id integer DEFAULT 0 NOT NULL,
  email character varying(150) DEFAULT ''::character varying NOT NULL,
  login character varying(150) DEFAULT ''::character varying NOT NULL,
  password_hash character varying(255) DEFAULT NULL,
  password_hash_type integer DEFAULT 1 NOT NULL,
  PRIMARY KEY ( contentobject_id )
);
CREATE UNIQUE INDEX ezuser_login ON ezuser ( login );


CREATE SEQUENCE IF NOT EXISTS ezuser_accountkey_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezuser_accountkey (
  hash_key character varying(32) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezuser_accountkey_id_seq'::text) NOT NULL,
  "time" integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX hash_key ON ezuser_accountkey ( hash_key );


CREATE SEQUENCE IF NOT EXISTS ezuser_discountrule_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezuser_discountrule (
  contentobject_id integer DEFAULT NULL,
  discountrule_id integer DEFAULT NULL,
  id integer DEFAULT nextval('ezuser_discountrule_id_seq'::text) NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezuser_role_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezuser_role (
  contentobject_id integer DEFAULT NULL,
  id integer DEFAULT nextval('ezuser_role_id_seq'::text) NOT NULL,
  limit_identifier character varying(255) DEFAULT ''::character varying,
  limit_value character varying(255) DEFAULT ''::character varying,
  role_id integer DEFAULT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezuser_role_contentobject_id ON ezuser_role ( contentobject_id );

CREATE INDEX ezuser_role_role_id ON ezuser_role ( role_id );


CREATE TABLE IF NOT EXISTS ezuser_setting (
  is_enabled integer DEFAULT 0 NOT NULL,
  max_login integer DEFAULT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( user_id )
);

CREATE TABLE IF NOT EXISTS ezuservisit (
  current_visit_timestamp integer DEFAULT 0 NOT NULL,
  failed_login_attempts integer DEFAULT 0 NOT NULL,
  last_visit_timestamp integer DEFAULT 0 NOT NULL,
  login_count integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( user_id )
);
CREATE INDEX ezuservisit_co_visit_count ON ezuservisit ( current_visit_timestamp, login_count );


CREATE SEQUENCE IF NOT EXISTS ezvatrule_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezvatrule (
  country_code character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezvatrule_id_seq'::text) NOT NULL,
  vat_type integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE IF NOT EXISTS ezvatrule_product_category (
  product_category_id integer DEFAULT 0 NOT NULL,
  vatrule_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( vatrule_id, product_category_id )
);

CREATE SEQUENCE IF NOT EXISTS ezvattype_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezvattype (
  id integer DEFAULT nextval('ezvattype_id_seq'::text) NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  percentage real DEFAULT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE IF NOT EXISTS ezview_counter (
  count integer DEFAULT 0 NOT NULL,
  node_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( node_id )
);

CREATE SEQUENCE IF NOT EXISTS ezwaituntildatevalue_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezwaituntildatevalue (
  contentclass_attribute_id integer DEFAULT 0 NOT NULL,
  contentclass_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezwaituntildatevalue_id_seq'::text) NOT NULL,
  workflow_event_id integer DEFAULT 0 NOT NULL,
  workflow_event_version integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id, workflow_event_id, workflow_event_version )
);
CREATE INDEX ezwaituntildateevalue_wf_ev_id_wf_ver ON ezwaituntildatevalue ( workflow_event_id, workflow_event_version );


CREATE SEQUENCE IF NOT EXISTS ezwishlist_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezwishlist (
  id integer DEFAULT nextval('ezwishlist_id_seq'::text) NOT NULL,
  productcollection_id integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezworkflow_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezworkflow (
  created integer DEFAULT 0 NOT NULL,
  creator_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezworkflow_id_seq'::text) NOT NULL,
  is_enabled integer DEFAULT 0 NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  modifier_id integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  "version" integer DEFAULT 0 NOT NULL,
  workflow_type_string character varying(50) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id, "version" )
);

CREATE SEQUENCE IF NOT EXISTS ezworkflow_assign_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezworkflow_assign (
  access_type integer DEFAULT 0 NOT NULL,
  as_tree integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezworkflow_assign_id_seq'::text) NOT NULL,
  node_id integer DEFAULT 0 NOT NULL,
  workflow_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);

CREATE SEQUENCE IF NOT EXISTS ezworkflow_event_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezworkflow_event (
  data_int1 integer DEFAULT NULL,
  data_int2 integer DEFAULT NULL,
  data_int3 integer DEFAULT NULL,
  data_int4 integer DEFAULT NULL,
  data_text1 character varying(255) DEFAULT NULL,
  data_text2 character varying(255) DEFAULT NULL,
  data_text3 character varying(255) DEFAULT NULL,
  data_text4 character varying(255) DEFAULT NULL,
  data_text5 text,
  description character varying(50) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('ezworkflow_event_id_seq'::text) NOT NULL,
  placement integer DEFAULT 0 NOT NULL,
  "version" integer DEFAULT 0 NOT NULL,
  workflow_id integer DEFAULT 0 NOT NULL,
  workflow_type_string character varying(50) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id, "version" )
);
CREATE INDEX wid_version_placement ON ezworkflow_event ( workflow_id, "version", placement );


CREATE SEQUENCE IF NOT EXISTS ezworkflow_group_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezworkflow_group (
  created integer DEFAULT 0 NOT NULL,
  creator_id integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezworkflow_group_id_seq'::text) NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  modifier_id integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE IF NOT EXISTS ezworkflow_group_link (
  group_id integer DEFAULT 0 NOT NULL,
  group_name character varying(255) DEFAULT NULL,
  workflow_id integer DEFAULT 0 NOT NULL,
  workflow_version integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( workflow_id, group_id, workflow_version )
);

CREATE SEQUENCE IF NOT EXISTS ezworkflow_process_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS ezworkflow_process (
  activation_date integer DEFAULT NULL,
  content_id integer DEFAULT 0 NOT NULL,
  content_version integer DEFAULT 0 NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  event_id integer DEFAULT 0 NOT NULL,
  event_position integer DEFAULT 0 NOT NULL,
  event_state integer DEFAULT NULL,
  event_status integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('ezworkflow_process_id_seq'::text) NOT NULL,
  last_event_id integer DEFAULT 0 NOT NULL,
  last_event_position integer DEFAULT 0 NOT NULL,
  last_event_status integer DEFAULT 0 NOT NULL,
  memento_key character varying(32) DEFAULT NULL,
  modified integer DEFAULT 0 NOT NULL,
  node_id integer DEFAULT 0 NOT NULL,
  parameters text,
  process_key character varying(32) DEFAULT ''::character varying NOT NULL,
  session_key character varying(32) DEFAULT '0'::character varying NOT NULL,
  status integer DEFAULT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  workflow_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX ezworkflow_process_process_key ON ezworkflow_process ( process_key );


CREATE SEQUENCE IF NOT EXISTS exp_url_redirect_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS exp_url_redirect (
  batch_id integer DEFAULT 0 NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('exp_url_redirect_id_seq'::text) NOT NULL,
  "language" character varying(20) DEFAULT ''::character varying NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  siteaccess character varying(100) DEFAULT ''::character varying NOT NULL,
  source_key character varying(32) DEFAULT ''::character varying NOT NULL,
  source_path character varying(1000) DEFAULT ''::character varying NOT NULL,
  source_query character varying(1000) DEFAULT ''::character varying NOT NULL,
  status integer DEFAULT 301 NOT NULL,
  target character varying(2000) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX exp_url_redirect_batch ON exp_url_redirect ( batch_id );

CREATE INDEX exp_url_redirect_source ON exp_url_redirect ( source_key );


CREATE SEQUENCE IF NOT EXISTS exp_urlmap_batch_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS exp_urlmap_batch (
  created integer DEFAULT 0 NOT NULL,
  created_count integer DEFAULT 0 NOT NULL,
  failed_count integer DEFAULT 0 NOT NULL,
  format character varying(20) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('exp_urlmap_batch_id_seq'::text) NOT NULL,
  "language" character varying(20) DEFAULT ''::character varying NOT NULL,
  on_conflict character varying(20) DEFAULT ''::character varying NOT NULL,
  rows_total integer DEFAULT 0 NOT NULL,
  siteaccess character varying(100) DEFAULT ''::character varying NOT NULL,
  skipped_count integer DEFAULT 0 NOT NULL,
  source_name character varying(255) DEFAULT ''::character varying NOT NULL,
  state character varying(20) DEFAULT ''::character varying NOT NULL,
  summary text,
  undone integer DEFAULT 0 NOT NULL,
  undone_by integer DEFAULT 0 NOT NULL,
  updated_count integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX exp_urlmap_batch_created ON exp_urlmap_batch ( created );


CREATE SEQUENCE IF NOT EXISTS exp_urlmap_batch_item_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1;
CREATE TABLE IF NOT EXISTS exp_urlmap_batch_item (
  "action" character varying(20) DEFAULT ''::character varying NOT NULL,
  after_image text,
  batch_id integer DEFAULT 0 NOT NULL,
  before_image text,
  id integer DEFAULT nextval('exp_urlmap_batch_item_id_seq'::text) NOT NULL,
  kind character varying(30) DEFAULT ''::character varying NOT NULL,
  line integer DEFAULT 0 NOT NULL,
  old_url character varying(1000) DEFAULT ''::character varying NOT NULL,
  seq integer DEFAULT 0 NOT NULL,
  state character varying(20) DEFAULT ''::character varying NOT NULL,
  target_key character varying(255) DEFAULT ''::character varying NOT NULL,
  target_table character varying(50) DEFAULT ''::character varying NOT NULL,
  PRIMARY KEY ( id )
);
CREATE INDEX exp_urlmap_batch_item_batch ON exp_urlmap_batch_item ( batch_id, seq );

