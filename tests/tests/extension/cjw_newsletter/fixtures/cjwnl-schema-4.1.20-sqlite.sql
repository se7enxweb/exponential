-- cjw_newsletter, SQLite: generated from share/db_schema.dba by the kernel's SQLite schema handler.
-- Index names are "<table>__<name>" (SQLite index names are database-wide).

CREATE TABLE cjwnl_blacklist_item (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email_hash varchar(255) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  newsletter_user_id INTEGER(11) NOT NULL DEFAULT '0',
  created INTEGER(11) DEFAULT NULL,
  creator_contentobject_id INTEGER(11) DEFAULT NULL,
  note text
);
CREATE  INDEX cjwnl_blacklist_item__cjwnewsletter_user_id ON cjwnl_blacklist_item  ( newsletter_user_id );

CREATE TABLE cjwnl_edition (
  contentobject_attribute_id INTEGER(11) NOT NULL DEFAULT '0',
  contentobject_attribute_version INTEGER(11) NOT NULL DEFAULT '0',
  contentobject_id INTEGER(11) NOT NULL DEFAULT '0',
  contentclass_id INTEGER(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( contentobject_attribute_id, contentobject_attribute_version )
);
CREATE  INDEX cjwnl_edition__contentobject_attribute_id ON cjwnl_edition  ( contentobject_attribute_id );
CREATE  INDEX cjwnl_edition__contentobject_attribute_version ON cjwnl_edition  ( contentobject_attribute_version );
CREATE  INDEX cjwnl_edition__contentobject_id ON cjwnl_edition  ( contentobject_id );

CREATE TABLE cjwnl_edition_send (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  list_contentobject_id INTEGER(11) NOT NULL DEFAULT '0',
  list_contentobject_version INTEGER(11) NOT NULL DEFAULT '0',
  list_is_virtual tinyint(1) NOT NULL DEFAULT '0',
  edition_contentobject_id INTEGER(11) NOT NULL DEFAULT '0',
  edition_contentobject_version INTEGER(11) NOT NULL DEFAULT '0',
  created INTEGER(11) NOT NULL DEFAULT '0',
  status tinyint(4) NOT NULL DEFAULT '0',
  siteaccess varchar(50) NOT NULL DEFAULT '',
  output_format_array_string varchar(50) NOT NULL DEFAULT '',
  creator_id INTEGER(11) NOT NULL DEFAULT '0',
  mailqueue_created INTEGER(11) NOT NULL DEFAULT '0',
  mailqueue_process_scheduled INTEGER(11) DEFAULT NULL,
  mailqueue_process_started INTEGER(11) NOT NULL DEFAULT '0',
  mailqueue_process_finished INTEGER(11) NOT NULL DEFAULT '0',
  mailqueue_process_aborted INTEGER(11) NOT NULL DEFAULT '0',
  output_xml longtext NOT NULL,
  hash varchar(255) NOT NULL DEFAULT '',
  email_sender varchar(255) NOT NULL DEFAULT '',
  email_reply_to varchar(255) NOT NULL DEFAULT '',
  email_return_path varchar(255) NOT NULL DEFAULT '',
  email_sender_name varchar(255) NOT NULL DEFAULT '',
  personalize_content tinyint(1) NOT NULL DEFAULT '0'
);
CREATE  INDEX cjwnl_edition_send__edition_contentobject_id ON cjwnl_edition_send  ( edition_contentobject_id );
CREATE  INDEX cjwnl_edition_send__edition_contentobject_version ON cjwnl_edition_send  ( edition_contentobject_version );
CREATE  INDEX cjwnl_edition_send__list_contentobject_id ON cjwnl_edition_send  ( list_contentobject_id );

CREATE TABLE cjwnl_edition_send_item (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  edition_send_id INTEGER(11) NOT NULL DEFAULT '0',
  newsletter_user_id INTEGER(11) NOT NULL DEFAULT '0',
  output_format_id tinyint(4) NOT NULL DEFAULT '0',
  subscription_id INTEGER(11) NOT NULL DEFAULT '0',
  created INTEGER(11) NOT NULL DEFAULT '0',
  processed INTEGER(11) NOT NULL DEFAULT '0',
  status tinyint(4) NOT NULL DEFAULT '0',
  hash varchar(255) NOT NULL DEFAULT '',
  bounced INTEGER(11) NOT NULL DEFAULT '0'
);
CREATE  INDEX cjwnl_edition_send_item__edition_send_id ON cjwnl_edition_send_item  ( edition_send_id );
CREATE  INDEX cjwnl_edition_send_item__newsletter_user_id ON cjwnl_edition_send_item  ( newsletter_user_id );
CREATE  INDEX cjwnl_edition_send_item__subscription_id ON cjwnl_edition_send_item  ( subscription_id );

CREATE TABLE cjwnl_import (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  type varchar(255) NOT NULL DEFAULT '',
  list_contentobject_id INTEGER(11) DEFAULT NULL,
  created INTEGER(11) DEFAULT NULL,
  creator_contentobject_id varchar(45) DEFAULT NULL,
  note text,
  data_text longtext NOT NULL,
  remote_id varchar(255) NOT NULL DEFAULT '',
  data_xml longtext NOT NULL,
  imported INTEGER(11) NOT NULL DEFAULT '0',
  imported_user_count INTEGER(11) NOT NULL DEFAULT '0',
  imported_subscription_count INTEGER(11) NOT NULL DEFAULT '0'
);

CREATE TABLE cjwnl_list (
  contentobject_attribute_id INTEGER(11) NOT NULL DEFAULT '0',
  contentobject_attribute_version INTEGER(11) NOT NULL DEFAULT '0',
  contentobject_id INTEGER(11) NOT NULL DEFAULT '0',
  contentclass_id INTEGER(11) NOT NULL DEFAULT '0',
  main_siteaccess varchar(255) NOT NULL DEFAULT '',
  siteaccess_array_string varchar(255) NOT NULL DEFAULT '',
  output_format_array_string varchar(255) NOT NULL DEFAULT '',
  email_sender_name varchar(255) NOT NULL DEFAULT '',
  email_sender varchar(255) NOT NULL DEFAULT '',
  email_reply_to varchar(255) NOT NULL DEFAULT '',
  email_return_path varchar(255) NOT NULL DEFAULT '',
  email_receiver_test varchar(255) NOT NULL DEFAULT '',
  auto_approve_registered_user tinyint(1) NOT NULL DEFAULT '0',
  skin_name varchar(255) NOT NULL DEFAULT 'default',
  personalize_content tinyint(1) NOT NULL DEFAULT '0',
  user_data_fields text NOT NULL,
  is_virtual tinyint(1) NOT NULL DEFAULT '0',
  virtual_filter text NOT NULL,
  PRIMARY KEY ( contentobject_attribute_id, contentobject_attribute_version )
);
CREATE  INDEX cjwnl_list__contentobject_attribute_id ON cjwnl_list  ( contentobject_attribute_id );
CREATE  INDEX cjwnl_list__contentobject_attribute_version ON cjwnl_list  ( contentobject_attribute_version );
CREATE  INDEX cjwnl_list__contentobject_id ON cjwnl_list  ( contentobject_id );

CREATE TABLE cjwnl_mailbox (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email varchar(255) DEFAULT NULL,
  server varchar(255) DEFAULT NULL,
  port INTEGER(11) DEFAULT NULL,
  user_name varchar(255) DEFAULT NULL,
  password varchar(255) DEFAULT NULL,
  type varchar(10) DEFAULT 'imap',
  delete_mails_from_server tinyint(1) NOT NULL DEFAULT '0',
  is_ssl tinyint(1) NOT NULL DEFAULT '0',
  is_activated tinyint(1) DEFAULT '1',
  last_server_connect INTEGER(11) DEFAULT NULL
);

CREATE TABLE cjwnl_mailbox_item (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  mailbox_id INTEGER(11) DEFAULT NULL,
  message_id INTEGER(11) DEFAULT NULL,
  message_identifier varchar(50) DEFAULT NULL,
  message_size INTEGER(11) NOT NULL DEFAULT '0',
  created INTEGER(11) DEFAULT NULL,
  processed INTEGER(11) DEFAULT NULL,
  bounce_code varchar(255) DEFAULT NULL,
  email_from varchar(255) DEFAULT NULL,
  email_to varchar(255) DEFAULT NULL,
  email_subject varchar(255) DEFAULT NULL,
  email_send_date INTEGER(11) DEFAULT NULL,
  edition_send_id INTEGER(11) DEFAULT NULL,
  edition_send_item_id INTEGER(11) NOT NULL DEFAULT '0',
  newsletter_user_id INTEGER(11) DEFAULT NULL
);
CREATE  INDEX cjwnl_mailbox_item__edition_send_id ON cjwnl_mailbox_item  ( edition_send_id );
CREATE  INDEX cjwnl_mailbox_item__mailbox_id ON cjwnl_mailbox_item  ( mailbox_id );
CREATE  INDEX cjwnl_mailbox_item__newsletter_user_id ON cjwnl_mailbox_item  ( newsletter_user_id );

CREATE TABLE cjwnl_subscription (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  list_contentobject_id INTEGER(11) NOT NULL DEFAULT '0',
  newsletter_user_id INTEGER(11) DEFAULT NULL,
  hash varchar(255) NOT NULL DEFAULT '',
  status tinyint(4) NOT NULL DEFAULT '0',
  output_format_array_string varchar(255) NOT NULL DEFAULT '',
  creator_contentobject_id INTEGER(11) NOT NULL DEFAULT '0',
  created INTEGER(11) NOT NULL DEFAULT '0',
  modifier_contentobject_id INTEGER(11) NOT NULL DEFAULT '0',
  modified INTEGER(11) NOT NULL DEFAULT '0',
  confirmed INTEGER(11) NOT NULL DEFAULT '0',
  approved INTEGER(11) NOT NULL DEFAULT '0',
  removed INTEGER(11) NOT NULL DEFAULT '0',
  remote_id varchar(255) NOT NULL DEFAULT '',
  import_id INTEGER(11) NOT NULL DEFAULT '0'
);
CREATE  INDEX cjwnl_subscription__import_id ON cjwnl_subscription  ( import_id );
CREATE  INDEX cjwnl_subscription__list_contentobject_id ON cjwnl_subscription  ( list_contentobject_id );
CREATE  INDEX cjwnl_subscription__newsletter_user_id ON cjwnl_subscription  ( newsletter_user_id );

CREATE TABLE cjwnl_user (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email varchar(255) DEFAULT NULL,
  salutation tinyint(4) DEFAULT NULL,
  first_name varchar(255) DEFAULT NULL,
  last_name varchar(255) DEFAULT NULL,
  organisation varchar(255) DEFAULT NULL,
  birthday varchar(10) DEFAULT NULL,
  data_xml text,
  hash varchar(255) DEFAULT NULL,
  ez_user_id INTEGER(11) DEFAULT NULL,
  status tinyint(4) NOT NULL DEFAULT '0',
  creator_contentobject_id INTEGER(11) NOT NULL DEFAULT '0',
  created INTEGER(11) NOT NULL DEFAULT '0',
  modified INTEGER(11) NOT NULL DEFAULT '0',
  modifier_contentobject_id INTEGER(11) NOT NULL DEFAULT '0',
  confirmed INTEGER(11) NOT NULL DEFAULT '0',
  removed INTEGER(11) NOT NULL DEFAULT '0',
  bounced INTEGER(11) NOT NULL DEFAULT '0',
  blacklisted INTEGER(11) NOT NULL DEFAULT '0',
  note text,
  external_user_id INTEGER(11) DEFAULT NULL,
  remote_id varchar(255) DEFAULT NULL,
  import_id INTEGER(11) DEFAULT NULL,
  bounce_count tinyint(4) DEFAULT '0',
  data_text text,
  custom_data_text_1 varchar(255) NOT NULL DEFAULT '',
  custom_data_text_2 varchar(255) NOT NULL DEFAULT '',
  custom_data_text_3 varchar(255) NOT NULL DEFAULT '',
  custom_data_text_4 varchar(255) NOT NULL DEFAULT ''
);
CREATE  INDEX cjwnl_user__ez_user_id ON cjwnl_user  ( ez_user_id );
CREATE  INDEX cjwnl_user__import_id ON cjwnl_user  ( import_id );
