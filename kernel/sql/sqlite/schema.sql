CREATE TABLE `ezapprove_items` (
  `collaboration_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `workflow_process_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE sqlite_sequence(name,seq);
CREATE TABLE `ezbasket` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `order_id` integer NOT NULL DEFAULT '0'
,  `productcollection_id` integer NOT NULL DEFAULT '0'
,  `session_id` varchar(255) NOT NULL DEFAULT ''
);
CREATE TABLE `ezbinaryfile` (
  `contentobject_attribute_id` integer NOT NULL DEFAULT '0'
,  `download_count` integer NOT NULL DEFAULT '0'
,  `filename` varchar(255) NOT NULL DEFAULT ''
,  `mime_type` varchar(255) NOT NULL DEFAULT ''
,  `original_filename` varchar(255) NOT NULL DEFAULT ''
,  `version` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`contentobject_attribute_id`,`version`)
);
CREATE TABLE `ezcobj_state` (
  `default_language_id` integer NOT NULL DEFAULT '0'
,  `group_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `identifier` varchar(45) NOT NULL DEFAULT ''
,  `language_mask` integer NOT NULL DEFAULT '0'
,  `priority` integer NOT NULL DEFAULT '0'
,  UNIQUE (`group_id`,`identifier`)
);
CREATE TABLE `ezcobj_state_group` (
  `default_language_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `identifier` varchar(45) NOT NULL DEFAULT ''
,  `language_mask` integer NOT NULL DEFAULT '0'
,  UNIQUE (`identifier`)
);
CREATE TABLE `ezcobj_state_group_language` (
  `contentobject_state_group_id` integer NOT NULL DEFAULT '0'
,  `description` longtext NOT NULL
,  `language_id` integer NOT NULL DEFAULT '0'
,  `name` varchar(45) NOT NULL DEFAULT ''
,  `real_language_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`contentobject_state_group_id`,`real_language_id`)
);
CREATE TABLE `ezcobj_state_language` (
  `contentobject_state_id` integer NOT NULL DEFAULT '0'
,  `description` longtext NOT NULL
,  `language_id` integer NOT NULL DEFAULT '0'
,  `name` varchar(45) NOT NULL DEFAULT ''
,  PRIMARY KEY (`contentobject_state_id`,`language_id`)
);
CREATE TABLE `ezcobj_state_link` (
  `contentobject_id` integer NOT NULL DEFAULT '0'
,  `contentobject_state_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`contentobject_id`,`contentobject_state_id`)
);
CREATE TABLE `ezcollab_group` (
  `created` integer NOT NULL DEFAULT '0'
,  `depth` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `is_open` integer NOT NULL DEFAULT '1'
,  `modified` integer NOT NULL DEFAULT '0'
,  `parent_group_id` integer NOT NULL DEFAULT '0'
,  `path_string` varchar(255) NOT NULL DEFAULT ''
,  `priority` integer NOT NULL DEFAULT '0'
,  `title` varchar(255) NOT NULL DEFAULT ''
,  `user_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezcollab_item` (
  `created` integer NOT NULL DEFAULT '0'
,  `creator_id` integer NOT NULL DEFAULT '0'
,  `data_float1` float NOT NULL DEFAULT '0'
,  `data_float2` float NOT NULL DEFAULT '0'
,  `data_float3` float NOT NULL DEFAULT '0'
,  `data_int1` integer NOT NULL DEFAULT '0'
,  `data_int2` integer NOT NULL DEFAULT '0'
,  `data_int3` integer NOT NULL DEFAULT '0'
,  `data_text1` longtext NOT NULL
,  `data_text2` longtext NOT NULL
,  `data_text3` longtext NOT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `modified` integer NOT NULL DEFAULT '0'
,  `status` integer NOT NULL DEFAULT '1'
,  `type_identifier` varchar(40) NOT NULL DEFAULT ''
);
CREATE TABLE `ezcollab_item_group_link` (
  `collaboration_id` integer NOT NULL DEFAULT '0'
,  `created` integer NOT NULL DEFAULT '0'
,  `group_id` integer NOT NULL DEFAULT '0'
,  `is_active` integer NOT NULL DEFAULT '1'
,  `is_read` integer NOT NULL DEFAULT '0'
,  `last_read` integer NOT NULL DEFAULT '0'
,  `modified` integer NOT NULL DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`collaboration_id`,`group_id`,`user_id`)
);
CREATE TABLE `ezcollab_item_message_link` (
  `collaboration_id` integer NOT NULL DEFAULT '0'
,  `created` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `message_id` integer NOT NULL DEFAULT '0'
,  `message_type` integer NOT NULL DEFAULT '0'
,  `modified` integer NOT NULL DEFAULT '0'
,  `participant_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezcollab_item_participant_link` (
  `collaboration_id` integer NOT NULL DEFAULT '0'
,  `created` integer NOT NULL DEFAULT '0'
,  `is_active` integer NOT NULL DEFAULT '1'
,  `is_read` integer NOT NULL DEFAULT '0'
,  `last_read` integer NOT NULL DEFAULT '0'
,  `modified` integer NOT NULL DEFAULT '0'
,  `participant_id` integer NOT NULL DEFAULT '0'
,  `participant_role` integer NOT NULL DEFAULT '1'
,  `participant_type` integer NOT NULL DEFAULT '1'
,  PRIMARY KEY (`collaboration_id`,`participant_id`)
);
CREATE TABLE `ezcollab_item_status` (
  `collaboration_id` integer NOT NULL DEFAULT '0'
,  `is_active` integer NOT NULL DEFAULT '1'
,  `is_read` integer NOT NULL DEFAULT '0'
,  `last_read` integer NOT NULL DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`collaboration_id`,`user_id`)
);
CREATE TABLE `ezcollab_notification_rule` (
  `collab_identifier` varchar(255) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `user_id` varchar(255) NOT NULL DEFAULT ''
);
CREATE TABLE `ezcollab_profile` (
  `created` integer NOT NULL DEFAULT '0'
,  `data_text1` longtext NOT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `main_group` integer NOT NULL DEFAULT '0'
,  `modified` integer NOT NULL DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezcollab_simple_message` (
  `created` integer NOT NULL DEFAULT '0'
,  `creator_id` integer NOT NULL DEFAULT '0'
,  `data_float1` float NOT NULL DEFAULT '0'
,  `data_float2` float NOT NULL DEFAULT '0'
,  `data_float3` float NOT NULL DEFAULT '0'
,  `data_int1` integer NOT NULL DEFAULT '0'
,  `data_int2` integer NOT NULL DEFAULT '0'
,  `data_int3` integer NOT NULL DEFAULT '0'
,  `data_text1` longtext NOT NULL
,  `data_text2` longtext NOT NULL
,  `data_text3` longtext NOT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `message_type` varchar(40) NOT NULL DEFAULT ''
,  `modified` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezcontent_language` (
  `disabled` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL DEFAULT '0'
,  `locale` varchar(20) NOT NULL DEFAULT ''
,  `name` varchar(255) NOT NULL DEFAULT ''
,  PRIMARY KEY (`id`)
);
CREATE TABLE `ezcontentbrowsebookmark` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(255) NOT NULL DEFAULT ''
,  `node_id` integer NOT NULL DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezcontentbrowserecent` (
  `created` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(255) NOT NULL DEFAULT ''
,  `node_id` integer NOT NULL DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezcontentclass` (
  `always_available` integer NOT NULL DEFAULT '0'
,  `contentobject_name` varchar(255) DEFAULT NULL
,  `created` integer NOT NULL DEFAULT '0'
,  `creator_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `identifier` varchar(50) NOT NULL DEFAULT ''
,  `initial_language_id` integer NOT NULL DEFAULT '0'
,  `is_container` integer NOT NULL DEFAULT '0'
,  `language_mask` integer NOT NULL DEFAULT '0'
,  `modified` integer NOT NULL DEFAULT '0'
,  `modifier_id` integer NOT NULL DEFAULT '0'
,  `remote_id` varchar(100) NOT NULL DEFAULT ''
,  `serialized_description_list` longtext
,  `serialized_name_list` longtext
,  `sort_field` integer NOT NULL DEFAULT '1'
,  `sort_order` integer NOT NULL DEFAULT '1'
,  `url_alias_name` varchar(255) DEFAULT NULL
,  `version` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezcontentclass_attribute` (
  `can_translate` integer DEFAULT '1'
,  `category` varchar(25) NOT NULL DEFAULT ''
,  `contentclass_id` integer NOT NULL DEFAULT '0'
,  `data_float1` double DEFAULT NULL
,  `data_float2` double DEFAULT NULL
,  `data_float3` double DEFAULT NULL
,  `data_float4` double DEFAULT NULL
,  `data_int1` integer DEFAULT NULL
,  `data_int2` integer DEFAULT NULL
,  `data_int3` integer DEFAULT NULL
,  `data_int4` integer DEFAULT NULL
,  `data_text1` varchar(50) DEFAULT NULL
,  `data_text2` varchar(50) DEFAULT NULL
,  `data_text3` varchar(50) DEFAULT NULL
,  `data_text4` varchar(255) DEFAULT NULL
,  `data_text5` longtext
,  `data_type_string` varchar(50) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `identifier` varchar(50) NOT NULL DEFAULT ''
,  `is_information_collector` integer NOT NULL DEFAULT '0'
,  `is_required` integer NOT NULL DEFAULT '0'
,  `is_searchable` integer NOT NULL DEFAULT '0'
,  `placement` integer NOT NULL DEFAULT '0'
,  `serialized_data_text` longtext
,  `serialized_description_list` longtext
,  `serialized_name_list` longtext NOT NULL
,  `version` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezcontentclass_classgroup` (
  `contentclass_id` integer NOT NULL DEFAULT '0'
,  `contentclass_version` integer NOT NULL DEFAULT '0'
,  `group_id` integer NOT NULL DEFAULT '0'
,  `group_name` varchar(255) DEFAULT NULL
,  PRIMARY KEY (`contentclass_id`,`contentclass_version`,`group_id`)
);
CREATE TABLE `ezcontentclass_name` (
  `contentclass_id` integer NOT NULL DEFAULT '0'
,  `contentclass_version` integer NOT NULL DEFAULT '0'
,  `language_id` integer NOT NULL DEFAULT '0'
,  `language_locale` varchar(20) NOT NULL DEFAULT ''
,  `name` varchar(255) NOT NULL DEFAULT ''
,  PRIMARY KEY (`contentclass_id`,`contentclass_version`,`language_id`)
);
CREATE TABLE `ezcontentclassgroup` (
  `created` integer NOT NULL DEFAULT '0'
,  `creator_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `modified` integer NOT NULL DEFAULT '0'
,  `modifier_id` integer NOT NULL DEFAULT '0'
,  `name` varchar(255) DEFAULT NULL
);
CREATE TABLE `ezcontentobject` (
  `contentclass_id` integer NOT NULL DEFAULT '0'
,  `current_version` integer DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `initial_language_id` integer NOT NULL DEFAULT '0'
,  `language_mask` integer NOT NULL DEFAULT '0'
,  `modified` integer NOT NULL DEFAULT '0'
,  `name` varchar(255) DEFAULT NULL
,  `owner_id` integer NOT NULL DEFAULT '0'
,  `published` integer NOT NULL DEFAULT '0'
,  `remote_id` varchar(100) DEFAULT NULL
,  `section_id` integer NOT NULL DEFAULT '0'
,  `status` integer DEFAULT '0'
,  UNIQUE (`remote_id`)
);
CREATE TABLE `ezcontentobject_attribute` (
  `attribute_original_id` integer DEFAULT '0'
,  `contentclassattribute_id` integer NOT NULL DEFAULT '0'
,  `contentobject_id` integer NOT NULL DEFAULT '0'
,  `data_float` double DEFAULT NULL
,  `data_int` integer DEFAULT NULL
,  `data_text` longtext
,  `data_type_string` varchar(50) DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `language_code` varchar(20) NOT NULL DEFAULT ''
,  `language_id` integer NOT NULL DEFAULT '0'
,  `sort_key_int` integer NOT NULL DEFAULT '0'
,  `sort_key_string` varchar(255) NOT NULL DEFAULT ''
,  `version` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezcontentobject_link` (
  `contentclassattribute_id` integer NOT NULL DEFAULT '0'
,  `from_contentobject_id` integer NOT NULL DEFAULT '0'
,  `from_contentobject_version` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `relation_type` integer NOT NULL DEFAULT '1'
,  `to_contentobject_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezcontentobject_name` (
  `content_translation` varchar(20) NOT NULL DEFAULT ''
,  `content_version` integer NOT NULL DEFAULT '0'
,  `contentobject_id` integer NOT NULL DEFAULT '0'
,  `language_id` integer NOT NULL DEFAULT '0'
,  `name` varchar(255) DEFAULT NULL
,  `real_translation` varchar(20) DEFAULT NULL
,  PRIMARY KEY (`contentobject_id`,`content_version`,`content_translation`)
);
CREATE TABLE `ezcontentobject_trash` (
  `contentobject_id` integer DEFAULT NULL
,  `contentobject_version` integer DEFAULT NULL
,  `depth` integer NOT NULL DEFAULT '0'
,  `is_hidden` integer NOT NULL DEFAULT '0'
,  `is_invisible` integer NOT NULL DEFAULT '0'
,  `main_node_id` integer DEFAULT NULL
,  `modified_subnode` integer DEFAULT '0'
,  `node_id` integer NOT NULL DEFAULT '0'
,  `parent_node_id` integer NOT NULL DEFAULT '0'
,  `path_identification_string` longtext
,  `path_string` varchar(255) NOT NULL DEFAULT ''
,  `priority` integer NOT NULL DEFAULT '0'
,  `remote_id` varchar(100) NOT NULL DEFAULT ''
,  `sort_field` integer DEFAULT '1'
,  `sort_order` integer DEFAULT '1'
,  `trashed` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`node_id`)
);
CREATE TABLE `ezcontentobject_tree` (
  `contentobject_id` integer DEFAULT NULL
,  `contentobject_is_published` integer DEFAULT NULL
,  `contentobject_version` integer DEFAULT NULL
,  `depth` integer NOT NULL DEFAULT '0'
,  `is_hidden` integer NOT NULL DEFAULT '0'
,  `is_invisible` integer NOT NULL DEFAULT '0'
,  `main_node_id` integer DEFAULT NULL
,  `modified_subnode` integer DEFAULT '0'
,  `node_id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `parent_node_id` integer NOT NULL DEFAULT '0'
,  `path_identification_string` longtext
,  `path_string` varchar(255) NOT NULL DEFAULT ''
,  `priority` integer NOT NULL DEFAULT '0'
,  `remote_id` varchar(100) NOT NULL DEFAULT ''
,  `sort_field` integer DEFAULT '1'
,  `sort_order` integer DEFAULT '1'
);
CREATE TABLE `ezcontentobject_version` (
  `contentobject_id` integer DEFAULT NULL
,  `created` integer NOT NULL DEFAULT '0'
,  `creator_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `initial_language_id` integer NOT NULL DEFAULT '0'
,  `language_mask` integer NOT NULL DEFAULT '0'
,  `modified` integer NOT NULL DEFAULT '0'
,  `status` integer NOT NULL DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
,  `version` integer NOT NULL DEFAULT '0'
,  `workflow_event_pos` integer DEFAULT '0'
);
CREATE TABLE `ezcurrencydata` (
  `auto_rate_value` decimal(10,5) NOT NULL DEFAULT '0.00000'
,  `code` varchar(4) NOT NULL DEFAULT ''
,  `custom_rate_value` decimal(10,5) NOT NULL DEFAULT '0.00000'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `locale` varchar(255) NOT NULL DEFAULT ''
,  `rate_factor` decimal(10,5) NOT NULL DEFAULT '1.00000'
,  `status` integer NOT NULL DEFAULT '1'
,  `symbol` varchar(255) NOT NULL DEFAULT ''
);
CREATE TABLE `ezdiscountrule` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(255) NOT NULL DEFAULT ''
);
CREATE TABLE `ezdiscountsubrule` (
  `discount_percent` float DEFAULT NULL
,  `discountrule_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `limitation` char(1) DEFAULT NULL
,  `name` varchar(255) NOT NULL DEFAULT ''
);
CREATE TABLE `ezdiscountsubrule_value` (
  `discountsubrule_id` integer NOT NULL DEFAULT '0'
,  `issection` integer NOT NULL DEFAULT '0'
,  `value` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`discountsubrule_id`,`value`,`issection`)
);
CREATE TABLE `ezenumobjectvalue` (
  `contentobject_attribute_id` integer NOT NULL DEFAULT '0'
,  `contentobject_attribute_version` integer NOT NULL DEFAULT '0'
,  `text` NOT NULL DEFAULT ''
,  `enumid` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`contentobject_attribute_id`,`contentobject_attribute_version`,`text`)
);
CREATE TABLE `ezenumvalue` (
  `contentclass_attribute_id` integer NOT NULL DEFAULT '0'
,  `contentclass_attribute_version` integer NOT NULL DEFAULT '0'
,  `text` NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY
,  `placement` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezforgot_password` (
  `hash_key` varchar(32) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `time` integer NOT NULL DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezgeneral_digest_user_settings` (
  `day` varchar(255) NOT NULL DEFAULT ''
,  `digest_type` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `receive_digest` integer NOT NULL DEFAULT '0'
,  `time` varchar(255) NOT NULL DEFAULT ''
,  `user_id` integer NOT NULL DEFAULT '0'
,  UNIQUE (`user_id`)
);
CREATE TABLE `ezgmaplocation` (
  `contentobject_attribute_id` integer NOT NULL DEFAULT '0'
,  `contentobject_version` integer NOT NULL DEFAULT '0'
,  `latitude` double NOT NULL DEFAULT '0'
,  `longitude` double NOT NULL DEFAULT '0'
,  `address` varchar(150) DEFAULT NULL
,  PRIMARY KEY (`contentobject_attribute_id`,`contentobject_version`)
);
CREATE TABLE `ezimagefile` (
  `contentobject_attribute_id` integer NOT NULL DEFAULT '0'
,  `filepath` longtext NOT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
);
CREATE TABLE `ezinfocollection` (
  `contentobject_id` integer NOT NULL DEFAULT '0'
,  `created` integer NOT NULL DEFAULT '0'
,  `creator_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `modified` integer DEFAULT '0'
,  `user_identifier` varchar(34) DEFAULT NULL
);
CREATE TABLE `ezinfocollection_attribute` (
  `contentclass_attribute_id` integer NOT NULL DEFAULT '0'
,  `contentobject_attribute_id` integer DEFAULT NULL
,  `contentobject_id` integer DEFAULT NULL
,  `data_float` float DEFAULT NULL
,  `data_int` integer DEFAULT NULL
,  `data_text` longtext
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `informationcollection_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezisbn_group` (
  `description` varchar(255) NOT NULL DEFAULT ''
,  `group_number` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
);
CREATE TABLE `ezisbn_group_range` (
  `from_number` integer NOT NULL DEFAULT '0'
,  `group_from` varchar(32) NOT NULL DEFAULT ''
,  `group_length` integer NOT NULL DEFAULT '0'
,  `group_to` varchar(32) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `to_number` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezisbn_registrant_range` (
  `from_number` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `isbn_group_id` integer NOT NULL DEFAULT '0'
,  `registrant_from` varchar(32) NOT NULL DEFAULT ''
,  `registrant_length` integer NOT NULL DEFAULT '0'
,  `registrant_to` varchar(32) NOT NULL DEFAULT ''
,  `to_number` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezkeyword` (
  `class_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `keyword` varchar(255) DEFAULT NULL
);
CREATE TABLE `ezkeyword_attribute_link` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `keyword_id` integer NOT NULL DEFAULT '0'
,  `objectattribute_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezmedia` (
  `contentobject_attribute_id` integer NOT NULL DEFAULT '0'
,  `controls` varchar(50) DEFAULT NULL
,  `filename` varchar(255) NOT NULL DEFAULT ''
,  `has_controller` integer DEFAULT '0'
,  `height` integer DEFAULT NULL
,  `is_autoplay` integer DEFAULT '0'
,  `is_loop` integer DEFAULT '0'
,  `mime_type` varchar(50) NOT NULL DEFAULT ''
,  `original_filename` varchar(255) NOT NULL DEFAULT ''
,  `pluginspage` varchar(255) DEFAULT NULL
,  `quality` varchar(50) DEFAULT NULL
,  `version` integer NOT NULL DEFAULT '0'
,  `width` integer DEFAULT NULL
,  PRIMARY KEY (`contentobject_attribute_id`,`version`)
);
CREATE TABLE `ezmessage` (
  `body` longtext
,  `destination_address` varchar(50) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `is_sent` integer NOT NULL DEFAULT '0'
,  `send_method` varchar(50) NOT NULL DEFAULT ''
,  `send_time` varchar(50) NOT NULL DEFAULT ''
,  `send_weekday` varchar(50) NOT NULL DEFAULT ''
,  `title` varchar(255) NOT NULL DEFAULT ''
);
CREATE TABLE `ezmodule_run` (
  `function_name` varchar(255) DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `module_data` longtext
,  `module_name` varchar(255) DEFAULT NULL
,  `workflow_process_id` integer DEFAULT NULL
,  UNIQUE (`workflow_process_id`)
);
CREATE TABLE `ezmultipricedata` (
  `contentobject_attr_id` integer NOT NULL DEFAULT '0'
,  `contentobject_attr_version` integer NOT NULL DEFAULT '0'
,  `currency_code` varchar(4) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `type` integer NOT NULL DEFAULT '0'
,  `value` decimal(15,2) NOT NULL DEFAULT '0.00'
);
CREATE TABLE `eznode_assignment` (
  `contentobject_id` integer DEFAULT NULL
,  `contentobject_version` integer DEFAULT NULL
,  `from_node_id` integer DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `is_main` integer NOT NULL DEFAULT '0'
,  `op_code` integer NOT NULL DEFAULT '0'
,  `parent_node` integer DEFAULT NULL
,  `parent_remote_id` varchar(100) NOT NULL DEFAULT ''
,  `remote_id` varchar(100) NOT NULL DEFAULT '0'
,  `sort_field` integer DEFAULT '1'
,  `sort_order` integer DEFAULT '1'
,  `priority` integer NOT NULL DEFAULT '0'
,  `is_hidden` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `eznotificationcollection` (
  `data_subject` longtext NOT NULL
,  `data_text` longtext NOT NULL
,  `event_id` integer NOT NULL DEFAULT '0'
,  `handler` varchar(255) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `transport` varchar(255) NOT NULL DEFAULT ''
);
CREATE TABLE `eznotificationcollection_item` (
  `address` varchar(255) NOT NULL DEFAULT ''
,  `collection_id` integer NOT NULL DEFAULT '0'
,  `event_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `send_date` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `eznotificationevent` (
  `data_int1` integer NOT NULL DEFAULT '0'
,  `data_int2` integer NOT NULL DEFAULT '0'
,  `data_int3` integer NOT NULL DEFAULT '0'
,  `data_int4` integer NOT NULL DEFAULT '0'
,  `data_text1` longtext NOT NULL
,  `data_text2` longtext NOT NULL
,  `data_text3` longtext NOT NULL
,  `data_text4` longtext NOT NULL
,  `event_type_string` varchar(255) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `status` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezoperation_memento` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `main` integer NOT NULL DEFAULT '0'
,  `main_key` varchar(32) NOT NULL DEFAULT ''
,  `memento_data` longtext NOT NULL
,  `memento_key` varchar(32) NOT NULL DEFAULT ''
);
CREATE TABLE `ezorder` (
  `account_identifier` varchar(100) NOT NULL DEFAULT 'default'
,  `created` integer NOT NULL DEFAULT '0'
,  `data_text_1` longtext
,  `data_text_2` longtext
,  `email` varchar(150) DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `ignore_vat` integer NOT NULL DEFAULT '0'
,  `is_archived` integer NOT NULL DEFAULT '0'
,  `is_temporary` integer NOT NULL DEFAULT '1'
,  `order_nr` integer NOT NULL DEFAULT '0'
,  `productcollection_id` integer NOT NULL DEFAULT '0'
,  `status_id` integer DEFAULT '0'
,  `status_modified` integer DEFAULT '0'
,  `status_modifier_id` integer DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezorder_item` (
  `description` varchar(255) DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `is_vat_inc` integer NOT NULL DEFAULT '0'
,  `order_id` integer NOT NULL DEFAULT '0'
,  `price` float DEFAULT NULL
,  `type` varchar(30) DEFAULT NULL
,  `vat_value` float NOT NULL DEFAULT '0'
);
CREATE TABLE `ezorder_nr_incr` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
);
CREATE TABLE `ezorder_status` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `is_active` integer NOT NULL DEFAULT '1'
,  `name` varchar(255) NOT NULL DEFAULT ''
,  `status_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezorder_status_history` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `modified` integer NOT NULL DEFAULT '0'
,  `modifier_id` integer NOT NULL DEFAULT '0'
,  `order_id` integer NOT NULL DEFAULT '0'
,  `status_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezpackage` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `install_date` integer NOT NULL DEFAULT '0'
,  `name` varchar(100) NOT NULL DEFAULT ''
,  `version` varchar(30) NOT NULL DEFAULT '0'
);
CREATE TABLE `ezpaymentobject` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `order_id` integer NOT NULL DEFAULT '0'
,  `payment_string` varchar(255) NOT NULL DEFAULT ''
,  `status` integer NOT NULL DEFAULT '0'
,  `workflowprocess_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezpdf_export` (
  `created` integer DEFAULT NULL
,  `creator_id` integer DEFAULT NULL
,  `export_classes` varchar(255) DEFAULT NULL
,  `export_structure` varchar(255) DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `intro_text` longtext
,  `modified` integer DEFAULT NULL
,  `modifier_id` integer DEFAULT NULL
,  `pdf_filename` varchar(255) DEFAULT NULL
,  `show_frontpage` integer DEFAULT NULL
,  `site_access` varchar(255) DEFAULT NULL
,  `source_node_id` integer DEFAULT NULL
,  `status` integer DEFAULT NULL
,  `sub_text` longtext
,  `title` varchar(255) DEFAULT NULL
,  `version` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezpending_actions` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `action` varchar(64) NOT NULL DEFAULT ''
,  `created` integer DEFAULT NULL
,  `param` longtext
);
CREATE TABLE `ezpolicy` (
  `function_name` varchar(255) DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `module_name` varchar(255) DEFAULT NULL
,  `original_id` integer NOT NULL DEFAULT '0'
,  `role_id` integer DEFAULT NULL
);
CREATE TABLE `ezpolicy_limitation` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `identifier` varchar(255) NOT NULL DEFAULT ''
,  `policy_id` integer DEFAULT NULL
);
CREATE TABLE `ezpolicy_limitation_value` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `limitation_id` integer DEFAULT NULL
,  `value` varchar(255) DEFAULT NULL
);
CREATE TABLE `ezpreferences` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(100) DEFAULT NULL
,  `user_id` integer NOT NULL DEFAULT '0'
,  `value` longtext
);
CREATE TABLE `ezprest_authcode` (
  `client_id` varchar(200) NOT NULL DEFAULT ''
,  `expirytime` integer NOT NULL DEFAULT '0'
,  `id` varchar(200) NOT NULL DEFAULT ''
,  `scope` varchar(200) DEFAULT NULL
,  `user_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`id`)
);
CREATE TABLE `ezprest_authorized_clients` (
  `created` integer DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `rest_client_id` integer DEFAULT NULL
,  `user_id` integer DEFAULT NULL
);
CREATE TABLE `ezprest_clients` (
  `client_id` varchar(200) DEFAULT NULL
,  `client_secret` varchar(200) DEFAULT NULL
,  `created` integer NOT NULL DEFAULT '0'
,  `description` longtext
,  `endpoint_uri` varchar(200) DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(100) DEFAULT NULL
,  `owner_id` integer NOT NULL DEFAULT '0'
,  `updated` integer NOT NULL DEFAULT '0'
,  `version` integer NOT NULL DEFAULT '0'
,  UNIQUE (`client_id`,`version`)
);
CREATE TABLE `ezprest_token` (
  `client_id` varchar(200) NOT NULL DEFAULT ''
,  `expirytime` integer NOT NULL DEFAULT '0'
,  `id` varchar(200) NOT NULL DEFAULT ''
,  `refresh_token` varchar(200) NOT NULL DEFAULT ''
,  `scope` varchar(200) DEFAULT NULL
,  `user_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`id`)
);
CREATE TABLE `ezproductcategory` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(255) NOT NULL DEFAULT ''
);
CREATE TABLE `ezproductcollection` (
  `created` integer DEFAULT NULL
,  `currency_code` varchar(4) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
);
CREATE TABLE `ezproductcollection_item` (
  `contentobject_id` integer NOT NULL DEFAULT '0'
,  `discount` float DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `is_vat_inc` integer DEFAULT NULL
,  `item_count` integer NOT NULL DEFAULT '0'
,  `name` varchar(255) NOT NULL DEFAULT ''
,  `price` float DEFAULT '0'
,  `productcollection_id` integer NOT NULL DEFAULT '0'
,  `vat_value` float DEFAULT NULL
);
CREATE TABLE `ezproductcollection_item_opt` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `item_id` integer NOT NULL DEFAULT '0'
,  `name` varchar(255) NOT NULL DEFAULT ''
,  `object_attribute_id` integer DEFAULT NULL
,  `option_item_id` integer NOT NULL DEFAULT '0'
,  `price` float NOT NULL DEFAULT '0'
,  `value` varchar(255) NOT NULL DEFAULT ''
);
CREATE TABLE `ezpublishingqueueprocesses` (
  `created` integer DEFAULT NULL
,  `ezcontentobject_version_id` integer NOT NULL DEFAULT '0'
,  `finished` integer DEFAULT NULL
,  `pid` integer DEFAULT NULL
,  `started` integer DEFAULT NULL
,  `status` integer DEFAULT NULL
,  PRIMARY KEY (`ezcontentobject_version_id`)
);
CREATE TABLE `ezrole` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `is_new` integer NOT NULL DEFAULT '0'
,  `name` varchar(255) NOT NULL DEFAULT ''
,  `value` char(1) DEFAULT NULL
,  `version` integer DEFAULT '0'
);
CREATE TABLE `ezrss_export` (
  `access_url` varchar(255) DEFAULT NULL
,  `opml_head` longtext
,  `podcast_head` longtext
,  `active` integer DEFAULT NULL
,  `created` integer DEFAULT NULL
,  `creator_id` integer DEFAULT NULL
,  `description` longtext
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `image_id` integer DEFAULT NULL
,  `main_node_only` integer NOT NULL DEFAULT '1'
,  `modified` integer DEFAULT NULL
,  `modifier_id` integer DEFAULT NULL
,  `node_id` integer DEFAULT NULL
,  `number_of_objects` integer NOT NULL DEFAULT '0'
,  `rss_version` varchar(255) DEFAULT NULL
,  `site_access` varchar(255) DEFAULT NULL
,  `status` integer NOT NULL DEFAULT '0'
,  `title` varchar(255) DEFAULT NULL
,  `url` varchar(255) DEFAULT NULL
);
CREATE TABLE `ezrss_export_item` (
  `category` varchar(255) DEFAULT NULL
,  `class_id` integer DEFAULT NULL
,  `description` varchar(255) DEFAULT NULL
,  `enclosure` varchar(255) DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `rssexport_id` integer DEFAULT NULL
,  `source_node_id` integer DEFAULT NULL
,  `status` integer NOT NULL DEFAULT '0'
,  `subnodes` integer NOT NULL DEFAULT '0'
,  `title` varchar(255) DEFAULT NULL
);
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
CREATE TABLE `ezrss_import` (
  `active` integer DEFAULT NULL
,  `class_description` varchar(255) DEFAULT NULL
,  `class_id` integer DEFAULT NULL
,  `class_title` varchar(255) DEFAULT NULL
,  `class_url` varchar(255) DEFAULT NULL
,  `created` integer DEFAULT NULL
,  `creator_id` integer DEFAULT NULL
,  `destination_node_id` integer DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `import_description` longtext NOT NULL
,  `modified` integer DEFAULT NULL
,  `modifier_id` integer DEFAULT NULL
,  `name` varchar(255) DEFAULT NULL
,  `object_owner_id` integer DEFAULT NULL
,  `status` integer NOT NULL DEFAULT '0'
,  `url` longtext
);
CREATE TABLE `ezscheduled_script` (
  `command` varchar(255) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `last_report_timestamp` integer NOT NULL DEFAULT '0'
,  `name` varchar(50) NOT NULL DEFAULT ''
,  `process_id` integer NOT NULL DEFAULT '0'
,  `progress` integer DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezsearch_object_word_link` (
  `contentclass_attribute_id` integer NOT NULL DEFAULT '0'
,  `contentclass_id` integer NOT NULL DEFAULT '0'
,  `contentobject_id` integer NOT NULL DEFAULT '0'
,  `frequency` float NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `identifier` varchar(255) NOT NULL DEFAULT ''
,  `integer_value` integer NOT NULL DEFAULT '0'
,  `next_word_id` integer NOT NULL DEFAULT '0'
,  `placement` integer NOT NULL DEFAULT '0'
,  `prev_word_id` integer NOT NULL DEFAULT '0'
,  `published` integer NOT NULL DEFAULT '0'
,  `section_id` integer NOT NULL DEFAULT '0'
,  `word_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezsearch_search_phrase` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `phrase` varchar(250) DEFAULT NULL
,  `phrase_count` integer DEFAULT '0'
,  `result_count` integer DEFAULT '0'
,  UNIQUE (`phrase`)
);
CREATE TABLE `ezsearch_word` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `object_count` integer NOT NULL DEFAULT '0'
,  `word` varchar(150) DEFAULT NULL
);
CREATE TABLE `ezsection` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `identifier` varchar(255) DEFAULT NULL
,  `locale` varchar(255) DEFAULT NULL
,  `name` varchar(255) DEFAULT NULL
,  `navigation_part_identifier` varchar(100) DEFAULT 'ezcontentnavigationpart'
);
CREATE TABLE `ezsession` (
  `data` longtext NOT NULL
,  `expiration_time` integer NOT NULL DEFAULT '0'
,  `session_key` varchar(32) NOT NULL DEFAULT ''
,  `user_hash` varchar(32) NOT NULL DEFAULT ''
,  `user_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`session_key`)
);
CREATE TABLE `ezsite_data` (
  `name` varchar(60) NOT NULL DEFAULT ''
,  `value` longtext NOT NULL
,  PRIMARY KEY (`name`)
);
CREATE TABLE `ezstarrating` (
  `contentobject_id` integer NOT NULL
,  `contentobject_attribute_id` integer NOT NULL
,  `rating_average` float NOT NULL
,  `rating_count` integer NOT NULL
,  PRIMARY KEY (`contentobject_id`,`contentobject_attribute_id`)
);
CREATE TABLE `ezstarrating_data` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `created_at` integer NOT NULL
,  `user_id` integer NOT NULL
,  `session_key` varchar(32) NOT NULL
,  `rating` float NOT NULL
,  `contentobject_id` integer NOT NULL
,  `contentobject_attribute_id` integer NOT NULL
);
CREATE TABLE `ezsubtree_notification_rule` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `node_id` integer NOT NULL DEFAULT '0'
,  `use_digest` integer DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `eztipafriend_counter` (
  `count` integer NOT NULL DEFAULT '0'
,  `node_id` integer NOT NULL DEFAULT '0'
,  `requested` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`node_id`,`requested`)
);
CREATE TABLE `eztipafriend_request` (
  `created` integer NOT NULL DEFAULT '0'
,  `email_receiver` varchar(100) NOT NULL DEFAULT ''
);
CREATE TABLE `eztrigger` (
  `connect_type` char(1) NOT NULL DEFAULT ''
,  `function_name` varchar(200) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `module_name` varchar(200) NOT NULL DEFAULT ''
,  `name` varchar(255) DEFAULT NULL
,  `workflow_id` integer DEFAULT NULL
,  UNIQUE (`module_name`,`function_name`,`connect_type`)
);
CREATE TABLE `ezurl` (
  `created` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `is_valid` integer NOT NULL DEFAULT '1'
,  `last_checked` integer NOT NULL DEFAULT '0'
,  `modified` integer NOT NULL DEFAULT '0'
,  `original_url_md5` varchar(32) NOT NULL DEFAULT ''
,  `url` longtext
);
CREATE TABLE `ezurl_object_link` (
  `contentobject_attribute_id` integer NOT NULL DEFAULT '0'
,  `contentobject_attribute_version` integer NOT NULL DEFAULT '0'
,  `url_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezurlalias` (
  `destination_url` longtext NOT NULL
,  `forward_to_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `is_imported` integer NOT NULL DEFAULT '0'
,  `is_internal` integer NOT NULL DEFAULT '1'
,  `is_wildcard` integer NOT NULL DEFAULT '0'
,  `source_md5` varchar(32) DEFAULT NULL
,  `source_url` longtext NOT NULL
);
CREATE TABLE `ezurlalias_ml` (
  `action` longtext NOT NULL
,  `action_type` varchar(32) NOT NULL DEFAULT ''
,  `alias_redirects` integer NOT NULL DEFAULT '1'
,  `id` integer NOT NULL DEFAULT '0'
,  `is_alias` integer NOT NULL DEFAULT '0'
,  `is_original` integer NOT NULL DEFAULT '0'
,  `lang_mask` integer NOT NULL DEFAULT '0'
,  `link` integer NOT NULL DEFAULT '0'
,  `parent` integer NOT NULL DEFAULT '0'
,  `text` longtext NOT NULL
,  `text_md5` varchar(32) NOT NULL DEFAULT ''
,  PRIMARY KEY (`parent`,`text_md5`)
);
CREATE TABLE `ezurlalias_ml_incr` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
);
CREATE TABLE `ezurlwildcard` (
  `destination_url` longtext NOT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `source_url` longtext NOT NULL
,  `type` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezuser` (
  `contentobject_id` integer NOT NULL DEFAULT '0'
,  `email` varchar(150) NOT NULL DEFAULT ''
,  `login` varchar(150) NOT NULL DEFAULT ''
,  `password_hash` varchar(255) DEFAULT NULL
,  `password_hash_type` integer NOT NULL DEFAULT '1'
,  PRIMARY KEY (`contentobject_id`)
);
CREATE TABLE `ezuser_accountkey` (
  `hash_key` varchar(32) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `time` integer NOT NULL DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezuser_discountrule` (
  `contentobject_id` integer DEFAULT NULL
,  `discountrule_id` integer DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(255) NOT NULL DEFAULT ''
);
CREATE TABLE `ezuser_role` (
  `contentobject_id` integer DEFAULT NULL
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `limit_identifier` varchar(255) DEFAULT ''
,  `limit_value` varchar(255) DEFAULT ''
,  `role_id` integer DEFAULT NULL
);
CREATE TABLE `ezuser_setting` (
  `is_enabled` integer NOT NULL DEFAULT '0'
,  `max_login` integer DEFAULT NULL
,  `user_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`user_id`)
);
CREATE TABLE `ezuservisit` (
  `current_visit_timestamp` integer NOT NULL DEFAULT '0'
,  `failed_login_attempts` integer NOT NULL DEFAULT '0'
,  `last_visit_timestamp` integer NOT NULL DEFAULT '0'
,  `login_count` integer NOT NULL DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`user_id`)
);
CREATE TABLE `ezvatrule` (
  `country_code` varchar(255) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `vat_type` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezvatrule_product_category` (
  `product_category_id` integer NOT NULL DEFAULT '0'
,  `vatrule_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`vatrule_id`,`product_category_id`)
);
CREATE TABLE `ezvattype` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(255) NOT NULL DEFAULT ''
,  `percentage` float DEFAULT NULL
);
CREATE TABLE `ezview_counter` (
  `count` integer NOT NULL DEFAULT '0'
,  `node_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`node_id`)
);
CREATE TABLE `ezwaituntildatevalue` (
  `contentclass_attribute_id` integer NOT NULL DEFAULT '0'
,  `contentclass_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `workflow_event_id` integer NOT NULL DEFAULT '0'
,  `workflow_event_version` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezwishlist` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `productcollection_id` integer NOT NULL DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezworkflow` (
  `created` integer NOT NULL DEFAULT '0'
,  `creator_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `is_enabled` integer NOT NULL DEFAULT '0'
,  `modified` integer NOT NULL DEFAULT '0'
,  `modifier_id` integer NOT NULL DEFAULT '0'
,  `name` varchar(255) NOT NULL DEFAULT ''
,  `version` integer NOT NULL DEFAULT '0'
,  `workflow_type_string` varchar(50) NOT NULL DEFAULT ''
);
CREATE TABLE `ezworkflow_assign` (
  `access_type` integer NOT NULL DEFAULT '0'
,  `as_tree` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `node_id` integer NOT NULL DEFAULT '0'
,  `workflow_id` integer NOT NULL DEFAULT '0'
);
CREATE TABLE `ezworkflow_event` (
  `data_int1` integer DEFAULT NULL
,  `data_int2` integer DEFAULT NULL
,  `data_int3` integer DEFAULT NULL
,  `data_int4` integer DEFAULT NULL
,  `data_text1` varchar(255) DEFAULT NULL
,  `data_text2` varchar(255) DEFAULT NULL
,  `data_text3` varchar(255) DEFAULT NULL
,  `data_text4` varchar(255) DEFAULT NULL
,  `data_text5` longtext
,  `description` varchar(50) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `placement` integer NOT NULL DEFAULT '0'
,  `version` integer NOT NULL DEFAULT '0'
,  `workflow_id` integer NOT NULL DEFAULT '0'
,  `workflow_type_string` varchar(50) NOT NULL DEFAULT ''
);
CREATE TABLE `ezworkflow_group` (
  `created` integer NOT NULL DEFAULT '0'
,  `creator_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `modified` integer NOT NULL DEFAULT '0'
,  `modifier_id` integer NOT NULL DEFAULT '0'
,  `name` varchar(255) NOT NULL DEFAULT ''
);
CREATE TABLE `ezworkflow_group_link` (
  `group_id` integer NOT NULL DEFAULT '0'
,  `group_name` varchar(255) DEFAULT NULL
,  `workflow_id` integer NOT NULL DEFAULT '0'
,  `workflow_version` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`workflow_id`,`group_id`,`workflow_version`)
);
CREATE TABLE `ezworkflow_process` (
  `activation_date` integer DEFAULT NULL
,  `content_id` integer NOT NULL DEFAULT '0'
,  `content_version` integer NOT NULL DEFAULT '0'
,  `created` integer NOT NULL DEFAULT '0'
,  `event_id` integer NOT NULL DEFAULT '0'
,  `event_position` integer NOT NULL DEFAULT '0'
,  `event_state` integer DEFAULT NULL
,  `event_status` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `last_event_id` integer NOT NULL DEFAULT '0'
,  `last_event_position` integer NOT NULL DEFAULT '0'
,  `last_event_status` integer NOT NULL DEFAULT '0'
,  `memento_key` varchar(32) DEFAULT NULL
,  `modified` integer NOT NULL DEFAULT '0'
,  `node_id` integer NOT NULL DEFAULT '0'
,  `parameters` longtext
,  `process_key` varchar(32) NOT NULL DEFAULT ''
,  `session_key` varchar(32) NOT NULL DEFAULT '0'
,  `status` integer DEFAULT NULL
,  `user_id` integer NOT NULL DEFAULT '0'
,  `workflow_id` integer NOT NULL DEFAULT '0'
);
CREATE INDEX "idx_eztipafriend_request_eztipafriend_request_created" ON "eztipafriend_request" (`created`);
CREATE INDEX "idx_eztipafriend_request_eztipafriend_request_email_rec" ON "eztipafriend_request" (`email_receiver`);
CREATE INDEX "idx_ezwaituntildatevalue_ezwaituntildateevalue_wf_ev_id_wf_ver" ON "ezwaituntildatevalue" (`workflow_event_id`,`workflow_event_version`);
CREATE INDEX "idx_ezurl_ezurl_url" ON "ezurl" (`url`);
CREATE INDEX "idx_ezorder_ezorder_is_archived" ON "ezorder" (`is_archived`);
CREATE INDEX "idx_ezorder_ezorder_is_tmp" ON "ezorder" (`is_temporary`);
CREATE INDEX "idx_ezurl_object_link_ezurl_ol_coa_id" ON "ezurl_object_link" (`contentobject_attribute_id`);
CREATE INDEX "idx_ezurl_object_link_ezurl_ol_coa_version" ON "ezurl_object_link" (`contentobject_attribute_version`);
CREATE INDEX "idx_ezurl_object_link_ezurl_ol_url_id" ON "ezurl_object_link" (`url_id`);
CREATE INDEX "idx_ezpreferences_ezpreferences_name" ON "ezpreferences" (`name`);
CREATE INDEX "idx_ezpreferences_ezpreferences_user_id_idx" ON "ezpreferences" (`user_id`,`name`);
CREATE INDEX "idx_ezbasket_ezbasket_session_id" ON "ezbasket" (`session_id`);
CREATE INDEX "idx_ezorder_status_ezorder_status_active" ON "ezorder_status" (`is_active`);
CREATE INDEX "idx_ezorder_status_ezorder_status_name" ON "ezorder_status" (`name`);
CREATE INDEX "idx_ezorder_status_ezorder_status_sid" ON "ezorder_status" (`status_id`);
CREATE INDEX "idx_ezcontentobject_name_ezcontentobject_name_cov_id" ON "ezcontentobject_name" (`content_version`);
CREATE INDEX "idx_ezcontentobject_name_ezcontentobject_name_lang_id" ON "ezcontentobject_name" (`language_id`);
CREATE INDEX "idx_ezcontentobject_name_ezcontentobject_name_name" ON "ezcontentobject_name" (`name`);
CREATE INDEX "idx_ezsearch_word_ezsearch_word_obj_count" ON "ezsearch_word" (`object_count`);
CREATE INDEX "idx_ezsearch_word_ezsearch_word_word_i" ON "ezsearch_word" (`word`);
CREATE INDEX "idx_ezpolicy_ezpolicy_original_id" ON "ezpolicy" (`original_id`);
CREATE INDEX "idx_ezpolicy_ezpolicy_role_id" ON "ezpolicy" (`role_id`);
CREATE INDEX "idx_ezurlalias_ezurlalias_desturl" ON "ezurlalias" (`destination_url`);
CREATE INDEX "idx_ezurlalias_ezurlalias_forward_to_id" ON "ezurlalias" (`forward_to_id`);
CREATE INDEX "idx_ezurlalias_ezurlalias_imp_wcard_fwd" ON "ezurlalias" (`is_imported`,`is_wildcard`,`forward_to_id`);
CREATE INDEX "idx_ezurlalias_ezurlalias_source_md5" ON "ezurlalias" (`source_md5`);
CREATE INDEX "idx_ezurlalias_ezurlalias_source_url" ON "ezurlalias" (`source_url`);
CREATE INDEX "idx_ezurlalias_ezurlalias_wcard_fwd" ON "ezurlalias" (`is_wildcard`,`forward_to_id`);
CREATE INDEX "idx_eztrigger_eztrigger_fetch" ON "eztrigger" (`name`,`module_name`,`function_name`);
CREATE INDEX "idx_ezuservisit_ezuservisit_co_visit_count" ON "ezuservisit" (`current_visit_timestamp`,`login_count`);
CREATE INDEX "idx_ezurlalias_ml_ezurlalias_ml_act_org" ON "ezurlalias_ml" (`action`,`is_original`);
CREATE INDEX "idx_ezurlalias_ml_ezurlalias_ml_actt_org_al" ON "ezurlalias_ml" (`action_type`,`is_original`,`is_alias`);
CREATE INDEX "idx_ezurlalias_ml_ezurlalias_ml_id" ON "ezurlalias_ml" (`id`);
CREATE INDEX "idx_ezurlalias_ml_ezurlalias_ml_par_act_id_lnk" ON "ezurlalias_ml" (`action`,`id`,`link`,`parent`);
CREATE INDEX "idx_ezurlalias_ml_ezurlalias_ml_par_lnk_txt" ON "ezurlalias_ml" (`parent`,`text`,`link`);
CREATE INDEX "idx_ezurlalias_ml_ezurlalias_ml_text" ON "ezurlalias_ml" (`text`,`id`,`link`);
CREATE INDEX "idx_ezurlalias_ml_ezurlalias_ml_text_lang" ON "ezurlalias_ml" (`text`,`lang_mask`,`parent`);
CREATE INDEX "idx_ezcontentclass_attribute_ezcontentclass_attr_ccid" ON "ezcontentclass_attribute" (`contentclass_id`);
CREATE INDEX "idx_ezcontentobject_trash_ezcobj_trash_co_id" ON "ezcontentobject_trash" (`contentobject_id`);
CREATE INDEX "idx_ezcontentobject_trash_ezcobj_trash_depth" ON "ezcontentobject_trash" (`depth`);
CREATE INDEX "idx_ezcontentobject_trash_ezcobj_trash_modified_subnode" ON "ezcontentobject_trash" (`modified_subnode`);
CREATE INDEX "idx_ezcontentobject_trash_ezcobj_trash_p_node_id" ON "ezcontentobject_trash" (`parent_node_id`);
CREATE INDEX "idx_ezcontentobject_trash_ezcobj_trash_path" ON "ezcontentobject_trash" (`path_string`);
CREATE INDEX "idx_ezcontentobject_trash_ezcobj_trash_path_ident" ON "ezcontentobject_trash" (`path_identification_string`);
CREATE INDEX "idx_ezuser_ezuser_login" ON "ezuser" (`login`);
CREATE INDEX "idx_ezcollab_group_ezcollab_group_depth" ON "ezcollab_group" (`depth`);
CREATE INDEX "idx_ezcollab_group_ezcollab_group_path" ON "ezcollab_group" (`path_string`);
CREATE INDEX "idx_ezkeyword_ezkeyword_keyword" ON "ezkeyword" (`keyword`);
CREATE INDEX "idx_ezpending_actions_ezpending_actions_action" ON "ezpending_actions" (`action`);
CREATE INDEX "idx_ezpending_actions_ezpending_actions_created" ON "ezpending_actions" (`created`);
CREATE INDEX "idx_ezforgot_password_ezforgot_password_user" ON "ezforgot_password" (`user_id`);
CREATE INDEX "idx_ezinfocollection_ezinfocollection_co_id_created" ON "ezinfocollection" (`contentobject_id`,`created`);
CREATE INDEX "idx_ezorder_item_ezorder_item_order_id" ON "ezorder_item" (`order_id`);
CREATE INDEX "idx_ezorder_item_ezorder_item_type" ON "ezorder_item" (`type`);
CREATE INDEX "idx_ezcontentbrowserecent_ezcontentbrowserecent_user" ON "ezcontentbrowserecent" (`user_id`);
CREATE INDEX "idx_ezproductcollection_item_opt_ezproductcollection_item_opt_item_id" ON "ezproductcollection_item_opt" (`item_id`);
CREATE INDEX "idx_ezproductcollection_item_ezproductcollection_item_contentobject_id" ON "ezproductcollection_item" (`contentobject_id`);
CREATE INDEX "idx_ezproductcollection_item_ezproductcollection_item_productcollection_id" ON "ezproductcollection_item" (`productcollection_id`);
CREATE INDEX "idx_ezkeyword_attribute_link_ezkeyword_attr_link_kid_oaid" ON "ezkeyword_attribute_link" (`keyword_id`,`objectattribute_id`);
CREATE INDEX "idx_ezkeyword_attribute_link_ezkeyword_attr_link_oaid" ON "ezkeyword_attribute_link" (`objectattribute_id`);
CREATE INDEX "idx_ezpolicy_limitation_value_ezpolicy_limitation_value_val" ON "ezpolicy_limitation_value" (`value`);
CREATE INDEX "idx_ezpolicy_limitation_value_ezpolicy_limit_value_limit_id" ON "ezpolicy_limitation_value" (`limitation_id`);
CREATE INDEX "idx_ezcobj_state_group_ezcobj_state_group_lmask" ON "ezcobj_state_group" (`language_mask`);
CREATE INDEX "idx_ezinfocollection_attribute_ezinfocollection_attr_cca_id" ON "ezinfocollection_attribute" (`contentclass_attribute_id`);
CREATE INDEX "idx_ezinfocollection_attribute_ezinfocollection_attr_co_id" ON "ezinfocollection_attribute" (`contentobject_id`);
CREATE INDEX "idx_ezinfocollection_attribute_ezinfocollection_attr_coa_id" ON "ezinfocollection_attribute" (`contentobject_attribute_id`);
CREATE INDEX "idx_ezinfocollection_attribute_ezinfocollection_attr_ic_id" ON "ezinfocollection_attribute" (`informationcollection_id`);
CREATE INDEX "idx_ezprest_authorized_clients_client_user" ON "ezprest_authorized_clients" (`rest_client_id`,`user_id`);
CREATE INDEX "idx_ezgmaplocation_latitude_longitude_key" ON "ezgmaplocation" (`latitude`,`longitude`);
CREATE INDEX "idx_ezimagefile_ezimagefile_coid" ON "ezimagefile" (`contentobject_attribute_id`);
CREATE INDEX "idx_ezimagefile_ezimagefile_file" ON "ezimagefile" (`filepath`);
CREATE INDEX "idx_ezcontentobject_attribute_ezcontentobject_attribute_co_id_ver_lang_code" ON "ezcontentobject_attribute" (`contentobject_id`,`version`,`language_code`);
CREATE INDEX "idx_ezcontentobject_attribute_ezcontentobject_attribute_language_code" ON "ezcontentobject_attribute" (`language_code`);
CREATE INDEX "idx_ezcontentobject_attribute_ezcontentobject_classattr_id" ON "ezcontentobject_attribute" (`contentclassattribute_id`);
CREATE INDEX "idx_ezcontentobject_attribute_sort_key_int" ON "ezcontentobject_attribute" (`sort_key_int`);
CREATE INDEX "idx_ezcontentobject_attribute_sort_key_string" ON "ezcontentobject_attribute" (`sort_key_string`);
CREATE INDEX "idx_ezmultipricedata_ezmultipricedata_coa_id" ON "ezmultipricedata" (`contentobject_attr_id`);
CREATE INDEX "idx_ezmultipricedata_ezmultipricedata_coa_version" ON "ezmultipricedata" (`contentobject_attr_version`);
CREATE INDEX "idx_ezmultipricedata_ezmultipricedata_currency_code" ON "ezmultipricedata" (`currency_code`);
CREATE INDEX "idx_ezcurrencydata_ezcurrencydata_code" ON "ezcurrencydata" (`code`);
CREATE INDEX "idx_ezpolicy_limitation_policy_id" ON "ezpolicy_limitation" (`policy_id`);
CREATE INDEX "idx_ezcontent_language_ezcontent_language_name" ON "ezcontent_language" (`name`);
CREATE INDEX "idx_ezworkflow_process_ezworkflow_process_process_key" ON "ezworkflow_process" (`process_key`);
CREATE INDEX "idx_ezcobj_state_ezcobj_state_lmask" ON "ezcobj_state" (`language_mask`);
CREATE INDEX "idx_ezcobj_state_ezcobj_state_priority" ON "ezcobj_state" (`priority`);
CREATE INDEX "idx_ezprest_authcode_authcode_client_id" ON "ezprest_authcode" (`client_id`);
CREATE INDEX "idx_ezstarrating_data_user_id_session_key" ON "ezstarrating_data" (`user_id`,`session_key`);
CREATE INDEX "idx_ezstarrating_data_contentobject_id_contentobject_attribute_id" ON "ezstarrating_data" (`contentobject_id`,`contentobject_attribute_id`);
CREATE INDEX "idx_ezscheduled_script_ezscheduled_script_timestamp" ON "ezscheduled_script" (`last_report_timestamp`);
CREATE INDEX "idx_ezoperation_memento_ezoperation_memento_memento_key_main" ON "ezoperation_memento" (`memento_key`,`main`);
CREATE INDEX "idx_ezenumvalue_ezenumvalue_co_cl_attr_id_co_class_att_ver" ON "ezenumvalue" (`contentclass_attribute_id`,`contentclass_attribute_version`);
CREATE INDEX "idx_ezrss_export_item_ezrss_export_rsseid" ON "ezrss_export_item" (`rssexport_id`);
CREATE INDEX "idx_ezrss_export_opml_item_ezrss_export_opml_rsseid" ON "ezrss_export_opml_item" (`rssexport_id`);
CREATE INDEX "idx_ezworkflow_event_wid_version_placement" ON "ezworkflow_event" (`workflow_id`,`version`,`placement`);
CREATE INDEX "idx_ezcontentbrowsebookmark_ezcontentbrowsebookmark_user" ON "ezcontentbrowsebookmark" (`user_id`);
CREATE INDEX "idx_ezcontentobject_link_ezco_link_from" ON "ezcontentobject_link" (`from_contentobject_id`,`from_contentobject_version`,`contentclassattribute_id`);
CREATE INDEX "idx_ezcontentobject_link_ezco_link_to_co_id" ON "ezcontentobject_link" (`to_contentobject_id`);
CREATE INDEX "idx_ezprest_token_token_client_id" ON "ezprest_token" (`client_id`);
CREATE INDEX "idx_ezorder_status_history_ezorder_status_history_mod" ON "ezorder_status_history" (`modified`);
CREATE INDEX "idx_ezorder_status_history_ezorder_status_history_oid" ON "ezorder_status_history" (`order_id`);
CREATE INDEX "idx_ezorder_status_history_ezorder_status_history_sid" ON "ezorder_status_history" (`status_id`);
CREATE INDEX "idx_ezuser_role_ezuser_role_contentobject_id" ON "ezuser_role" (`contentobject_id`);
CREATE INDEX "idx_ezuser_role_ezuser_role_role_id" ON "ezuser_role" (`role_id`);
CREATE INDEX "idx_ezsubtree_notification_rule_ezsubtree_notification_rule_user_id" ON "ezsubtree_notification_rule" (`user_id`);
CREATE INDEX "idx_ezuser_accountkey_hash_key" ON "ezuser_accountkey" (`hash_key`);
CREATE INDEX "idx_ezcontentobject_tree_ezcontentobject_tree_remote_id" ON "ezcontentobject_tree" (`remote_id`);
CREATE INDEX "idx_ezcontentobject_tree_ezcontentobject_tree_co_id" ON "ezcontentobject_tree" (`contentobject_id`);
CREATE INDEX "idx_ezcontentobject_tree_ezcontentobject_tree_depth" ON "ezcontentobject_tree" (`depth`);
CREATE INDEX "idx_ezcontentobject_tree_ezcontentobject_tree_p_node_id" ON "ezcontentobject_tree" (`parent_node_id`);
CREATE INDEX "idx_ezcontentobject_tree_ezcontentobject_tree_path" ON "ezcontentobject_tree" (`path_string`);
CREATE INDEX "idx_ezcontentobject_tree_ezcontentobject_tree_path_ident" ON "ezcontentobject_tree" (`path_identification_string`);
CREATE INDEX "idx_ezcontentobject_tree_modified_subnode" ON "ezcontentobject_tree" (`modified_subnode`);
CREATE INDEX "idx_ezsearch_search_phrase_ezsearch_search_phrase_count" ON "ezsearch_search_phrase" (`phrase_count`);
CREATE INDEX "idx_ezcontentobject_version_ezcobj_version_creator_id" ON "ezcontentobject_version" (`creator_id`);
CREATE INDEX "idx_ezcontentobject_version_ezcobj_version_status" ON "ezcontentobject_version" (`status`);
CREATE INDEX "idx_ezcontentobject_version_idx_object_version_objver" ON "ezcontentobject_version" (`contentobject_id`,`version`);
CREATE INDEX "idx_ezcontentobject_version_ezcontobj_version_obj_status" ON "ezcontentobject_version" (`contentobject_id`,`status`);
CREATE INDEX "idx_eznode_assignment_eznode_assignment_co_version" ON "eznode_assignment" (`contentobject_version`);
CREATE INDEX "idx_eznode_assignment_eznode_assignment_coid_cov" ON "eznode_assignment" (`contentobject_id`,`contentobject_version`);
CREATE INDEX "idx_eznode_assignment_eznode_assignment_is_main" ON "eznode_assignment" (`is_main`);
CREATE INDEX "idx_eznode_assignment_eznode_assignment_parent_node" ON "eznode_assignment" (`parent_node`);
CREATE INDEX "idx_ezcontentobject_ezcontentobject_classid" ON "ezcontentobject" (`contentclass_id`);
CREATE INDEX "idx_ezcontentobject_ezcontentobject_currentversion" ON "ezcontentobject" (`current_version`);
CREATE INDEX "idx_ezcontentobject_ezcontentobject_lmask" ON "ezcontentobject" (`language_mask`);
CREATE INDEX "idx_ezcontentobject_ezcontentobject_owner" ON "ezcontentobject" (`owner_id`);
CREATE INDEX "idx_ezcontentobject_ezcontentobject_pub" ON "ezcontentobject" (`published`);
CREATE INDEX "idx_ezcontentobject_ezcontentobject_status" ON "ezcontentobject" (`status`);
CREATE INDEX "idx_ezsession_expiration_time" ON "ezsession" (`expiration_time`);
CREATE INDEX "idx_ezsession_ezsession_user_id" ON "ezsession" (`user_id`);
CREATE INDEX "idx_ezsearch_object_word_link_ezsearch_object_word_link_frequency" ON "ezsearch_object_word_link" (`frequency`);
CREATE INDEX "idx_ezsearch_object_word_link_ezsearch_object_word_link_identifier" ON "ezsearch_object_word_link" (`identifier`);
CREATE INDEX "idx_ezsearch_object_word_link_ezsearch_object_word_link_integer_value" ON "ezsearch_object_word_link" (`integer_value`);
CREATE INDEX "idx_ezsearch_object_word_link_ezsearch_object_word_link_object" ON "ezsearch_object_word_link" (`contentobject_id`);
CREATE INDEX "idx_ezsearch_object_word_link_ezsearch_object_word_link_word" ON "ezsearch_object_word_link" (`word_id`);
CREATE INDEX "idx_ezcontentclass_ezcontentclass_version" ON "ezcontentclass" (`version`);
CREATE INDEX "idx_ezcontentclass_ezcontentclass_identifier" ON "ezcontentclass" (`identifier`,`version`);
