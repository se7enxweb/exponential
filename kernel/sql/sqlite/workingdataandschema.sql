PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE `ezapprove_items` (
  `collaboration_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `workflow_process_id` integer NOT NULL DEFAULT '0'
);
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
INSERT INTO ezcobj_state VALUES(2,2,1,'not_locked',3,0);
INSERT INTO ezcobj_state VALUES(2,2,2,'locked',3,1);
CREATE TABLE `ezcobj_state_group` (
  `default_language_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `identifier` varchar(45) NOT NULL DEFAULT ''
,  `language_mask` integer NOT NULL DEFAULT '0'
,  UNIQUE (`identifier`)
);
INSERT INTO ezcobj_state_group VALUES(2,2,'ez_lock',3);
CREATE TABLE `ezcobj_state_group_language` (
  `contentobject_state_group_id` integer NOT NULL DEFAULT '0'
,  `description` longtext NOT NULL
,  `language_id` integer NOT NULL DEFAULT '0'
,  `name` varchar(45) NOT NULL DEFAULT ''
,  `real_language_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`contentobject_state_group_id`,`real_language_id`)
);
INSERT INTO ezcobj_state_group_language VALUES(2,'',3,'Lock',2);
CREATE TABLE `ezcobj_state_language` (
  `contentobject_state_id` integer NOT NULL DEFAULT '0'
,  `description` longtext NOT NULL
,  `language_id` integer NOT NULL DEFAULT '0'
,  `name` varchar(45) NOT NULL DEFAULT ''
,  PRIMARY KEY (`contentobject_state_id`,`language_id`)
);
INSERT INTO ezcobj_state_language VALUES(1,'',3,'Not locked');
INSERT INTO ezcobj_state_language VALUES(2,'',3,'Locked');
CREATE TABLE `ezcobj_state_link` (
  `contentobject_id` integer NOT NULL DEFAULT '0'
,  `contentobject_state_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`contentobject_id`,`contentobject_state_id`)
);
INSERT INTO ezcobj_state_link VALUES(4,1);
INSERT INTO ezcobj_state_link VALUES(10,1);
INSERT INTO ezcobj_state_link VALUES(11,1);
INSERT INTO ezcobj_state_link VALUES(12,1);
INSERT INTO ezcobj_state_link VALUES(13,1);
INSERT INTO ezcobj_state_link VALUES(14,1);
INSERT INTO ezcobj_state_link VALUES(41,1);
INSERT INTO ezcobj_state_link VALUES(42,1);
INSERT INTO ezcobj_state_link VALUES(45,1);
INSERT INTO ezcobj_state_link VALUES(49,1);
INSERT INTO ezcobj_state_link VALUES(50,1);
INSERT INTO ezcobj_state_link VALUES(51,1);
INSERT INTO ezcobj_state_link VALUES(52,1);
INSERT INTO ezcobj_state_link VALUES(54,1);
INSERT INTO ezcobj_state_link VALUES(56,1);
INSERT INTO ezcobj_state_link VALUES(57,1);
INSERT INTO ezcobj_state_link VALUES(59,1);
INSERT INTO ezcobj_state_link VALUES(60,1);
INSERT INTO ezcobj_state_link VALUES(61,1);
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
INSERT INTO ezcontent_language VALUES(0,2,'eng-GB','English (United Kingdom)');
INSERT INTO ezcontent_language VALUES(0,4,'eng-US','English (American)');
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
INSERT INTO ezcontentbrowserecent VALUES(1704585628,1,'eZ Publish',2,14);
INSERT INTO ezcontentbrowserecent VALUES(1704475234,2,'Images',51,14);
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
INSERT INTO ezcontentclass VALUES(1,'<short_name|name>',1024392098,14,1,'folder',2,1,7,1704578550,14,'a3d405b81be900468eb153d774f4f0d2','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:6:"Folder";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(1,'<name>',1024392098,14,3,'user_group',2,1,7,1704579152,14,'25b4268cdcd01921b808a0d854b877ef','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:10:"User Group";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(1,'<first_name> <last_name>',1024392098,14,4,'user',2,0,7,1704579098,14,'40faa822edc579b02c25f6bb7beec3ad','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:4:"User";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(1,'<name>',1081858024,14,14,'common_ini_settings',2,0,7,1704579643,14,'ffedf2e73b1ea0c3e630e42e2db9c900','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:19:"Common INI Settings";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(1,'<title>',1081858045,14,15,'template_look',2,0,7,1704579534,14,'59b43cd9feaaf0e45ac974fb4bbd3f92','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:13:"Template Look";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(1,'<short_title|title>',1186536125,14,16,'article',4,1,5,1704578371,14,'c15b600eb9198b1924063b5a68758232','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:7:"Article";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(1,'<name>',1186536126,14,23,'frontpage',4,1,5,1704581347,14,'e36c458e3e4a81298a0945f53a2c81f4','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:10:"Front Page";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<short_title|title>',1704585486,14,45,'article_mainpage',2,1,3,1704585486,14,'feaf24c0edae665e7ddaae1bc2b3fe5b','a:0:{}','a:2:{s:6:"eng-GB";s:19:"Article (main-page)";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<title|index_title>',1704585486,14,46,'article_subpage',2,0,3,1704585486,14,'68f305a18c76d9d03df36b810f290732','a:0:{}','a:2:{s:6:"eng-GB";s:18:"Article (sub-page)";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585486,14,47,'blog',2,1,3,1704585486,14,'3a6f9c1f075b3bf49d7345576b196fe8','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Blog";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<title>',1704585486,14,48,'blog_post',2,1,3,1704585486,14,'7ecb961056b7cbb30f22a91357e0a007','a:0:{}','a:2:{s:6:"eng-GB";s:9:"Blog post";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585486,14,49,'product',2,0,3,1704585486,14,'77f3ede996a3a39c7159cc69189c5307','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Product";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585486,14,50,'feedback_form',2,1,3,1704585486,14,'df0257b8fc55f6b8ab179d6fb915455e','a:0:{}','a:2:{s:6:"eng-GB";s:13:"Feedback form";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<title>',1704585486,14,51,'documentation_page',2,1,3,1704585486,14,'d4a05eed0402e4d70fedfda2023f1aa2','a:0:{}','a:2:{s:6:"eng-GB";s:18:"Documentation page";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<header>',1704585486,14,52,'infobox',2,0,3,1704585486,14,'0b4e8accad5bec5ba2d430acb25c1ff6','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Infobox";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585486,14,53,'multicalendar',2,0,3,1704585486,14,'99aec4e5682414517ed929ecd969439f','a:0:{}','a:2:{s:6:"eng-GB";s:13:"Multicalendar";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585486,14,54,'poll',2,0,3,1704585486,14,'232937a3a2eacbbf24e2601aebe16522','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Poll";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585486,14,55,'file',2,0,3,1704585486,14,'637d58bfddf164627bdfd265733280a0','a:0:{}','a:2:{s:6:"eng-GB";s:4:"File";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585486,14,56,'flash',2,0,3,1704585486,14,'6cd17b98a41ee9355371a376e8868ee0','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Flash";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585486,14,57,'image',2,0,3,1704585486,14,'f6df12aa74e36230eb675f364fccd25a','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Image";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585487,14,58,'link',2,0,3,1704585487,14,'74ec6507063150bc813549b22534ad48','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Link";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585487,14,59,'quicktime',2,0,3,1704585487,14,'16d7b371979d6ba37894cc8dc306f38f','a:0:{}','a:2:{s:6:"eng-GB";s:9:"Quicktime";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585487,14,60,'windows_media',2,0,3,1704585487,14,'223dd2551e85b63b55a72d02363faab6','a:0:{}','a:2:{s:6:"eng-GB";s:13:"Windows media";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585487,14,61,'real_video',2,0,3,1704585487,14,'dba67bc20a4301aa04cc74e411310dfc','a:0:{}','a:2:{s:6:"eng-GB";s:10:"Real video";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585487,14,62,'gallery',2,1,3,1704585487,14,'6a320cdc3e274841b82fcd63a86f80d1','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Gallery";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<short_title|title>',1704585487,14,63,'geo_article',2,1,3,1704585487,14,'a98ae5ac95365b958b01fb88dfab3330','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Geo Article";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585487,14,64,'forum',2,1,3,1704585487,14,'b241f924b96b267153f5f55904e0675a','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Forum";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<subject>',1704585487,14,65,'forum_topic',2,1,3,1704585487,14,'71f99c516743a33562c3893ef98c9b60','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Forum topic";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<subject>',1704585487,14,66,'forum_reply',2,0,3,1704585487,14,'80ee42a66b2b8b6ee15f5c5f4b361562','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Forum reply";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<short_title|title>',1704585487,14,67,'event',2,0,3,1704585487,14,'563cb5edc2adfd2b240efa456c81525f','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Event";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<short_title|title>',1704585487,14,68,'event_calendar',2,1,3,1704585487,14,'020cbeb6382c8c89dcec2cd406fb47a8','a:0:{}','a:2:{s:6:"eng-GB";s:14:"Event calendar";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585487,14,69,'banner',2,0,3,1704585487,14,'9cb558e25fd946246bbb32950c00228e','a:0:{}','a:2:{s:6:"eng-GB";s:6:"Banner";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<title>',1704585487,14,70,'forums',2,1,3,1704585487,14,'60a921e54c1efbb9456bd2283d9e66cb','a:0:{}','a:2:{s:6:"eng-GB";s:6:"Forums";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
INSERT INTO ezcontentclass VALUES(0,'<name>',1704585487,14,71,'silverlight',2,0,3,1704585487,14,'8ab17aae77dd4f24b5a8e835784e96e7','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Silverlight";s:16:"always-available";s:6:"eng-GB";}',1,1,'',0);
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
INSERT INTO ezcontentclass_attribute VALUES(1,'',1,0.0,0.0,0.0,0.0,255,0,0,0,'Folder','','','','','ezstring',4,'name',0,1,1,1,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:4:"Name";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',3,0.0,0.0,0.0,0.0,255,0,0,0,'','','','','','ezstring',6,'name',0,1,1,1,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:4:"Name";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',3,0.0,0.0,0.0,0.0,255,0,0,0,'','','','','','ezstring',7,'description',0,0,1,2,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:11:"Description";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',4,0.0,0.0,0.0,0.0,255,0,0,0,'','','','','','ezstring',8,'first_name',0,1,1,1,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:10:"First Name";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',4,0.0,0.0,0.0,0.0,255,0,0,0,'','','','','','ezstring',9,'last_name',0,1,1,2,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:9:"Last Name";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',4,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezuser',12,'user_account',0,1,1,3,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:12:"User Account";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',1,0.0,0.0,0.0,0.0,5,0,0,0,'','','','','','ezxmltext',119,'short_description',0,0,1,3,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:17:"Short Description";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',1,0.0,0.0,0.0,0.0,100,0,0,0,'','','','','','ezstring',155,'short_name',0,0,1,2,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:10:"Short Name";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',1,0.0,0.0,0.0,0.0,20,0,0,0,'','','','','','ezxmltext',156,'description',0,0,1,4,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:11:"Description";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',1,0.0,0.0,0.0,0.0,0,0,1,0,'','','','','','ezboolean',158,'show_children',0,0,0,5,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:13:"Show Children";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',14,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',159,'name',0,0,1,1,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:4:"Name";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,1,0,0,0,'site.ini','SiteSettings','IndexPage','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',160,'indexpage',0,0,0,2,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:10:"Index Page";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,1,0,0,0,'site.ini','SiteSettings','DefaultPage','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',161,'defaultpage',0,0,0,3,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:12:"Default Page";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,2,0,0,0,'site.ini','DebugSettings','DebugOutput','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',162,'debugoutput',0,0,0,4,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:12:"Debug Output";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,2,0,0,0,'site.ini','DebugSettings','DebugByIP','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',163,'debugbyip',0,0,0,5,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:9:"DebugByIP";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,6,0,0,0,'site.ini','DebugSettings','DebugIPList','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',164,'debugiplist',0,0,0,6,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:11:"DebugIPList";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,2,0,0,0,'site.ini','DebugSettings','DebugRedirection','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',165,'debugredirection',0,0,0,7,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:17:"Debug Redirection";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,2,0,0,0,'site.ini','ContentSettings','ViewCaching','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',166,'viewcaching',0,0,0,8,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:12:"View Caching";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,2,0,0,0,'site.ini','TemplateSettings','TemplateCache','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',167,'templatecache',0,0,0,9,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:14:"Template Cache";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,2,0,0,0,'site.ini','TemplateSettings','TemplateCompile','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',168,'templatecompile',0,0,0,10,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:16:"Template Compile";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,6,0,0,0,'image.ini','small','Filters','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',169,'imagesmall',0,0,0,11,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:11:"Image Small";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,6,0,0,0,'image.ini','medium','Filters','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',170,'imagemedium',0,0,0,12,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:12:"Image Medium";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',14,0.0,0.0,0.0,0.0,6,0,0,0,'image.ini','large','Filters','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',171,'imagelarge',0,0,0,13,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:11:"Image Large";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',15,0.0,0.0,0.0,0.0,1,0,0,0,'site.ini','SiteSettings','SiteName','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',172,'title',0,0,0,1,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:5:"Title";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',15,0.0,0.0,0.0,0.0,6,0,0,0,'site.ini','SiteSettings','MetaDataArray','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',173,'meta_data',0,0,0,4,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:9:"Meta Data";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezimage',174,'image',0,0,0,6,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:5:"Image";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'sitestyle','','','','','ezpackage',175,'sitestyle',0,0,0,8,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:9:"SiteStyle";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',15,0.0,0.0,0.0,0.0,1,0,0,0,'site.ini','MailSettings','AdminEmail','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',177,'email',0,0,0,10,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:5:"Email";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',15,0.0,0.0,0.0,0.0,1,0,0,0,'site.ini','SiteSettings','SiteURL','1','override;ezwebin_site_user;eng;ezwebin_site_admin','ezinisetting',178,'siteurl',0,0,0,12,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:8:"Site Url";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',4,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','eztext',179,'signature',0,0,1,4,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:9:"Signature";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',4,0.0,0.0,0.0,0.0,1,0,0,0,'','','','','','ezimage',180,'image',0,0,0,5,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:5:"Image";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',1,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',181,'tags',0,0,0,6,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:4:"Tags";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',1,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezdatetime',182,'publish_date',0,0,1,7,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:12:"Publish Date";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',16,0.0,0.0,0.0,0.0,255,0,0,0,'New article','','','','','ezstring',183,'title',0,1,1,1,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:5:"Title";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',16,0.0,0.0,0.0,0.0,255,0,0,0,'','','','','','ezstring',184,'short_title',0,0,1,2,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:11:"Short Title";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',16,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezauthor',185,'author',0,0,0,3,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:6:"Author";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',16,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',186,'intro',0,0,1,4,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:5:"Intro";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',16,0.0,0.0,0.0,0.0,20,0,0,0,'','','','','','ezxmltext',187,'body',0,0,1,5,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:4:"Body";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',16,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezboolean',188,'enable_comments',0,0,0,6,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:15:"Enable Comments";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',16,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezimage',189,'image',0,0,0,7,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:5:"Image";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',16,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',190,'caption',0,0,1,8,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:7:"Caption";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',16,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezdatetime',191,'publish_date',0,0,1,9,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:12:"Publish Date";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',16,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezdatetime',192,'unpublish_date',0,0,1,10,'a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:14:"Unpublish Date";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',23,0.0,0.0,0.0,0.0,0,0,0,0,'','','','',replace('<?xml version="1.0" encoding="utf-8"?>\n<related-object><constraints/></related-object>\n','\n',char(10)),'ezobjectrelation',236,'billboard',0,0,0,2,'a:0:{}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:9:"Billboard";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',23,0.0,0.0,0.0,0.0,20,0,0,0,'','','','','','ezxmltext',237,'left_column',0,0,1,3,'a:0:{}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:11:"Left Column";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',23,0.0,0.0,0.0,0.0,20,0,0,0,'','','','','','ezxmltext',238,'center_column',0,0,1,4,'a:0:{}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:13:"Center Column";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',23,0.0,0.0,0.0,0.0,20,0,0,0,'','','','','','ezxmltext',239,'right_column',0,0,1,5,'a:0:{}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:12:"Right Column";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',23,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',240,'bottom_column',0,0,1,6,'a:0:{}','a:1:{s:6:"eng-US";s:0:"";}','a:1:{s:6:"eng-US";s:13:"Bottom Column";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezurl',322,'site_map_url',0,0,0,2,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:11:"Sitemap URL";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezurl',323,'tag_cloud_url',0,0,0,3,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:12:"TagCloud URL";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',324,'login_label',0,0,0,5,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:11:"Login Label";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',325,'logout_label',0,0,0,7,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:12:"Logout Label";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',326,'my_profile_label',0,0,0,9,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:16:"My Profile Label";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',327,'register_user_label',0,0,0,11,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:19:"Register User Label";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',328,'rss_feed',0,0,0,13,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:8:"RSS Feed";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',329,'shopping_basket_label',0,0,0,14,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:0:"";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',330,'site_settings_label',0,0,0,15,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:19:"Site Settings Label";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','',replace('<?xml version="1.0" encoding="utf-8"?>\n<ezmatrix/>\n','\n',char(10)),'ezmatrix',331,'language_settings',0,0,0,16,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:17:"Language Settings";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','eztext',332,'footer_text',0,0,0,17,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:11:"Footer Text";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezboolean',333,'hide_powered_by',0,0,0,18,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:15:"Hide Powered By";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',15,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','eztext',334,'footer_script',0,0,0,19,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:13:"Footer Script";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',3,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezboolean',335,'website_toolbar_access',0,0,1,3,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:1:{s:6:"eng-US";s:22:"Website Toolbar Access";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',23,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',370,'name',0,1,1,1,'a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:0:"";s:16:"always-available";s:6:"eng-US";}','a:2:{s:6:"eng-US";s:4:"Name";s:16:"always-available";s:6:"eng-US";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',371,'title',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',372,'short_title',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Short title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',373,'index_title',0,0,1,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Index title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezauthor',374,'author',0,1,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:6:"Author";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',375,'intro',0,1,1,5,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Summary";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,15,0,0,0,'','','','','','ezxmltext',376,'body',0,0,1,6,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Body";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezimage',377,'image',0,0,0,7,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Image";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,5,0,0,0,'','','','','','ezxmltext',378,'caption',0,0,1,8,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:15:"Caption (Image)";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,1,0,0,0,'','','','','','ezdatetime',379,'publish_date',0,0,1,9,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:12:"Publish date";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezdatetime',380,'unpublish_date',0,0,1,10,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:14:"Unpublish date";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',381,'tags',0,0,1,11,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',45,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezboolean',382,'enable_comments',0,0,1,12,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:15:"Enable comments";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',46,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',383,'title',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',46,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',384,'index_title',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Index title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',46,0.0,0.0,0.0,0.0,15,0,0,0,'','','','','','ezxmltext',385,'body',0,0,1,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"body";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',46,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',386,'tags',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',47,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',387,'name',0,0,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',47,0.0,0.0,0.0,0.0,5,0,0,0,'','','','','','ezxmltext',388,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',47,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',389,'tags',0,0,1,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',48,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',390,'title',0,0,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',48,0.0,0.0,0.0,0.0,25,0,0,0,'','','','','','ezxmltext',391,'body',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Body";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',48,0.0,0.0,0.0,0.0,1,0,0,0,'','','','','','ezdatetime',392,'publication_date',0,0,1,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:16:"Publication date";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',48,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezdatetime',393,'unpublish_date',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:14:"Unpublish date";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',48,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',394,'tags',0,0,1,5,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',48,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezboolean',395,'enable_comments',0,0,1,6,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:15:"Enable comments";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',49,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',396,'name',0,0,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',49,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',397,'product_number',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:14:"Product number";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',49,0.0,0.0,0.0,0.0,5,0,0,0,'','','','','','ezxmltext',398,'short_description',0,0,1,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:17:"Short description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',49,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',399,'description',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',49,1.0,0.0,0.0,0.0,1,0,0,0,'','','','','','ezprice',400,'price',0,0,0,5,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Price";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',49,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezimage',401,'image',0,0,0,6,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Image";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',49,0.0,0.0,0.0,0.0,5,0,0,0,'','','','','','ezxmltext',402,'caption',0,0,1,7,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:15:"Caption (Image)";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',49,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezmultioption',403,'additional_options',0,0,1,8,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:18:"Additional options";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',49,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',404,'tags',0,0,1,9,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',50,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',405,'name',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',50,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',406,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',50,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',407,'sender_name',1,1,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Sender name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',50,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',408,'subject',1,1,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Subject";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',50,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','eztext',409,'message',1,1,1,5,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Message";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',50,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezemail',410,'email',1,1,0,6,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Email";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',50,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezemail',411,'recipient',0,0,0,7,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:9:"Recipient";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',51,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',412,'title',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',51,0.0,0.0,0.0,0.0,20,0,0,0,'','','','','','ezxmltext',413,'body',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Body";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',51,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',414,'tags',0,0,1,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',51,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezboolean',415,'show_children',0,0,0,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:17:"Display sub items";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',52,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',416,'header',0,1,0,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:6:"Header";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',52,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezimage',417,'image',0,0,0,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Image";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',52,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',418,'image_url',0,0,1,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"URL (image)";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',52,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',419,'content',0,0,0,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Content";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',52,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezurl',420,'url',0,0,0,5,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:3:"URL";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',53,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',421,'name',0,0,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',53,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',422,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',53,0.0,0.0,0.0,0.0,0,0,0,0,'','','','',replace('<?xml version="1.0" encoding="utf-8"?>\n<related-objects><constraints><allowed-class contentclass-identifier="event_calendar"/></constraints><type value="2"/><selection_type value="0"/><object_class value=""/><contentobject-placement/></related-objects>\n','\n',char(10)),'ezobjectrelationlist',423,'calendars',0,0,1,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:9:"Calendars";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',54,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',424,'name',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',54,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',425,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',54,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezoption',426,'question',1,1,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:8:"Question";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',55,0.0,0.0,0.0,0.0,0,0,0,0,'New file','','','','','ezstring',427,'name',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',55,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',428,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',55,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezbinaryfile',429,'file',0,1,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"File";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',55,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',430,'tags',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',56,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',431,'name',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',56,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',432,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',56,0.0,0.0,0.0,0.0,0,0,0,0,'flash','','','','','ezmedia',433,'file',0,1,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"File";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',56,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',434,'tags',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',57,0.0,0.0,0.0,0.0,150,0,0,0,'','','','','','ezstring',435,'name',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',57,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',436,'caption',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Caption";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',57,0.0,0.0,0.0,0.0,2,0,0,0,'','','','','','ezimage',437,'image',0,0,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Image";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',57,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',438,'tags',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',58,0.0,0.0,0.0,0.0,255,0,0,0,'','','','','','ezstring',439,'name',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',58,0.0,0.0,0.0,0.0,20,0,0,0,'','','','','','ezxmltext',440,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',58,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezurl',441,'location',0,0,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:8:"Location";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',58,0.0,0.0,0.0,0.0,0,0,1,0,'','','','','','ezboolean',442,'open_in_new_window',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:18:"Open in new window";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',59,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',443,'name',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',59,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',444,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',59,0.0,0.0,0.0,0.0,0,0,0,0,'quick_time','','','','','ezmedia',445,'file',0,1,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"File";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',59,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',446,'tags',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',60,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',447,'name',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',60,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',448,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',60,0.0,0.0,0.0,0.0,0,0,0,0,'windows_media_player','','','','','ezmedia',449,'file',0,1,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"File";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',60,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',450,'tags',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',61,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',451,'name',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',61,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',452,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',61,0.0,0.0,0.0,0.0,0,0,0,0,'real_player','','','','','ezmedia',453,'file',0,1,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"File";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',61,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',454,'tags',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',62,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',455,'name',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',62,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',456,'short_description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:17:"Short description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',62,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',457,'description',0,0,1,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',62,0.0,0.0,0.0,0.0,0,0,0,0,'','','','',replace('<?xml version="1.0" encoding="utf-8"?>\n<related-object><constraints/></related-object>\n','\n',char(10)),'ezobjectrelation',458,'image',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Image";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',63,0.0,0.0,0.0,0.0,255,0,0,0,'New article','','','','','ezstring',459,'title',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',63,0.0,0.0,0.0,0.0,255,0,0,0,'','','','','','ezstring',460,'short_title',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Short title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',63,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezauthor',461,'author',0,0,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:6:"Author";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',63,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',462,'intro',0,1,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Summary";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',63,0.0,0.0,0.0,0.0,20,0,0,0,'','','','','','ezxmltext',463,'body',0,0,1,5,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Body";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',63,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezboolean',464,'enable_comments',0,0,0,6,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:15:"Enable comments";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',63,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezimage',465,'image',0,0,0,7,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Image";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',63,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',466,'caption',0,0,1,8,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:15:"Caption (Image)";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',63,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezdatetime',467,'publish_date',0,0,1,9,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:12:"Publish date";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',63,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezdatetime',468,'unpublish_date',0,0,1,10,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:14:"Unpublish date";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',63,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',469,'tags',0,0,1,11,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',63,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezgmaplocation',470,'location',0,0,1,12,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:8:"Location";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',64,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',471,'name',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',64,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',472,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',65,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',473,'subject',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Subject";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',65,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','eztext',474,'message',0,1,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Message";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',65,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezboolean',475,'sticky',0,0,1,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:6:"Sticky";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',65,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezsubtreesubscription',476,'notify_me',0,0,0,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:23:"Notify me about updates";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',66,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',477,'subject',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Subject";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',66,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','eztext',478,'message',0,1,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:7:"Message";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',67,0.0,0.0,0.0,0.0,55,0,0,0,'','','','','','ezstring',479,'title',0,0,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:10:"Full title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',67,0.0,0.0,0.0,0.0,19,0,0,0,'','','','','','ezstring',480,'short_title',0,1,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Short title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',67,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',481,'text',0,0,1,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Text";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',67,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',482,'category',0,0,1,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:8:"Category";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',67,0.0,0.0,0.0,0.0,1,0,0,0,'','','','','','ezdatetime',483,'from_time',0,1,0,5,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:9:"From Time";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',67,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezdatetime',484,'to_time',0,0,0,6,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:7:"To Time";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',68,0.0,0.0,0.0,0.0,65,0,0,0,'','','','','','ezstring',485,'title',0,1,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:10:"Full Title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',68,0.0,0.0,0.0,0.0,25,0,0,0,'','','','','','ezstring',486,'short_title',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Short Title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(0,'',68,0.0,0.0,0.0,0.0,0,0,0,0,'','','','',replace('<?xml version="1.0" encoding="utf-8"?>\n<ezselection><options><option id="0" name="Calendar"/><option id="1" name="Program"/></options></ezselection>\n','\n',char(10)),'ezselection',487,'view',0,1,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"View";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',69,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',488,'name',0,1,0,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',69,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',489,'url',0,0,0,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:3:"URL";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',69,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezimage',490,'image',0,1,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Image";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',69,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','eztext',491,'image_map',0,0,0,4,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:9:"Image map";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',69,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezkeyword',492,'tags',0,0,1,5,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Tags";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',70,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',493,'title',0,0,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:5:"Title";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',70,0.0,0.0,0.0,0.0,10,0,0,0,'','','','','','ezxmltext',494,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',71,0.0,0.0,0.0,0.0,0,0,0,0,'','','','','','ezstring',495,'name',0,0,1,1,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"Name";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',71,0.0,0.0,0.0,0.0,5,0,0,0,'','','','','','ezxmltext',496,'description',0,0,1,2,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:11:"Description";s:16:"always-available";s:6:"eng-GB";}',0);
INSERT INTO ezcontentclass_attribute VALUES(1,'',71,0.0,0.0,0.0,0.0,0,0,0,0,'silverlight','','','','','ezmedia',497,'file',0,0,0,3,'a:0:{}','a:0:{}','a:2:{s:6:"eng-GB";s:4:"File";s:16:"always-available";s:6:"eng-GB";}',0);
CREATE TABLE `ezcontentclass_classgroup` (
  `contentclass_id` integer NOT NULL DEFAULT '0'
,  `contentclass_version` integer NOT NULL DEFAULT '0'
,  `group_id` integer NOT NULL DEFAULT '0'
,  `group_name` varchar(255) DEFAULT NULL
,  PRIMARY KEY (`contentclass_id`,`contentclass_version`,`group_id`)
);
INSERT INTO ezcontentclass_classgroup VALUES(42,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(16,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(1,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(4,0,2,'Users');
INSERT INTO ezcontentclass_classgroup VALUES(3,0,2,'Users');
INSERT INTO ezcontentclass_classgroup VALUES(15,0,4,'Setup');
INSERT INTO ezcontentclass_classgroup VALUES(14,0,4,'Setup');
INSERT INTO ezcontentclass_classgroup VALUES(23,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(45,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(46,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(47,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(48,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(49,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(50,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(51,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(52,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(53,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(54,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(55,0,3,'Media');
INSERT INTO ezcontentclass_classgroup VALUES(56,0,3,'Media');
INSERT INTO ezcontentclass_classgroup VALUES(57,0,3,'Media');
INSERT INTO ezcontentclass_classgroup VALUES(58,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(59,0,3,'Media');
INSERT INTO ezcontentclass_classgroup VALUES(60,0,3,'Media');
INSERT INTO ezcontentclass_classgroup VALUES(61,0,3,'Media');
INSERT INTO ezcontentclass_classgroup VALUES(62,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(63,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(64,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(65,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(66,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(67,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(68,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(69,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(70,0,1,'Content');
INSERT INTO ezcontentclass_classgroup VALUES(71,0,3,'Media');
CREATE TABLE `ezcontentclass_name` (
  `contentclass_id` integer NOT NULL DEFAULT '0'
,  `contentclass_version` integer NOT NULL DEFAULT '0'
,  `language_id` integer NOT NULL DEFAULT '0'
,  `language_locale` varchar(20) NOT NULL DEFAULT ''
,  `name` varchar(255) NOT NULL DEFAULT ''
,  PRIMARY KEY (`contentclass_id`,`contentclass_version`,`language_id`)
);
INSERT INTO ezcontentclass_name VALUES(1,0,3,'eng-US','Folder');
INSERT INTO ezcontentclass_name VALUES(3,0,3,'eng-US','User Group');
INSERT INTO ezcontentclass_name VALUES(4,0,3,'eng-US','User');
INSERT INTO ezcontentclass_name VALUES(14,0,3,'eng-US','Common INI Settings');
INSERT INTO ezcontentclass_name VALUES(15,0,3,'eng-US','Template Look');
INSERT INTO ezcontentclass_name VALUES(16,0,3,'eng-GB','Article');
INSERT INTO ezcontentclass_name VALUES(23,0,3,'eng-GB','Frontpage');
INSERT INTO ezcontentclass_name VALUES(42,0,3,'eng-GB','Banner');
INSERT INTO ezcontentclass_name VALUES(23,0,5,'eng-US','Front Page');
INSERT INTO ezcontentclass_name VALUES(16,0,5,'eng-US','Article');
INSERT INTO ezcontentclass_name VALUES(42,0,4,'eng-US','Banner');
INSERT INTO ezcontentclass_name VALUES(45,0,3,'eng-GB','Article (main-page)');
INSERT INTO ezcontentclass_name VALUES(46,0,3,'eng-GB','Article (sub-page)');
INSERT INTO ezcontentclass_name VALUES(47,0,3,'eng-GB','Blog');
INSERT INTO ezcontentclass_name VALUES(48,0,3,'eng-GB','Blog post');
INSERT INTO ezcontentclass_name VALUES(49,0,3,'eng-GB','Product');
INSERT INTO ezcontentclass_name VALUES(50,0,3,'eng-GB','Feedback form');
INSERT INTO ezcontentclass_name VALUES(51,0,3,'eng-GB','Documentation page');
INSERT INTO ezcontentclass_name VALUES(52,0,3,'eng-GB','Infobox');
INSERT INTO ezcontentclass_name VALUES(53,0,3,'eng-GB','Multicalendar');
INSERT INTO ezcontentclass_name VALUES(54,0,3,'eng-GB','Poll');
INSERT INTO ezcontentclass_name VALUES(55,0,3,'eng-GB','File');
INSERT INTO ezcontentclass_name VALUES(56,0,3,'eng-GB','Flash');
INSERT INTO ezcontentclass_name VALUES(57,0,3,'eng-GB','Image');
INSERT INTO ezcontentclass_name VALUES(58,0,3,'eng-GB','Link');
INSERT INTO ezcontentclass_name VALUES(59,0,3,'eng-GB','Quicktime');
INSERT INTO ezcontentclass_name VALUES(60,0,3,'eng-GB','Windows media');
INSERT INTO ezcontentclass_name VALUES(61,0,3,'eng-GB','Real video');
INSERT INTO ezcontentclass_name VALUES(62,0,3,'eng-GB','Gallery');
INSERT INTO ezcontentclass_name VALUES(63,0,3,'eng-GB','Geo Article');
INSERT INTO ezcontentclass_name VALUES(64,0,3,'eng-GB','Forum');
INSERT INTO ezcontentclass_name VALUES(65,0,3,'eng-GB','Forum topic');
INSERT INTO ezcontentclass_name VALUES(66,0,3,'eng-GB','Forum reply');
INSERT INTO ezcontentclass_name VALUES(67,0,3,'eng-GB','Event');
INSERT INTO ezcontentclass_name VALUES(68,0,3,'eng-GB','Event calendar');
INSERT INTO ezcontentclass_name VALUES(69,0,3,'eng-GB','Banner');
INSERT INTO ezcontentclass_name VALUES(70,0,3,'eng-GB','Forums');
INSERT INTO ezcontentclass_name VALUES(71,0,3,'eng-GB','Silverlight');
CREATE TABLE `ezcontentclassgroup` (
  `created` integer NOT NULL DEFAULT '0'
,  `creator_id` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `modified` integer NOT NULL DEFAULT '0'
,  `modifier_id` integer NOT NULL DEFAULT '0'
,  `name` varchar(255) DEFAULT NULL
);
INSERT INTO ezcontentclassgroup VALUES(1031216928,14,1,1033922106,14,'Content');
INSERT INTO ezcontentclassgroup VALUES(1031216941,14,2,1033922113,14,'Users');
INSERT INTO ezcontentclassgroup VALUES(1032009743,14,3,1033922120,14,'Media');
INSERT INTO ezcontentclassgroup VALUES(1081858024,14,4,1081858024,14,'Setup');
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
INSERT INTO ezcontentobject VALUES(3,1,4,2,4,1033917596,'Users',14,1033917596,'f5c88a2209584891056f987fd965b0ba',2,1);
INSERT INTO ezcontentobject VALUES(4,2,10,2,4,1072180405,'Anonymous User',14,1033920665,'faaeb9be3bd98ed09f606fc16d144eca',2,1);
INSERT INTO ezcontentobject VALUES(3,1,11,2,4,1033920746,'Guest accounts',14,1033920746,'5f7f0bdb3381d6a461d8c29ff53d908f',2,1);
INSERT INTO ezcontentobject VALUES(3,1,12,2,4,1033920775,'Administrator users',14,1033920775,'9b47a45624b023b1a76c73b74d704acf',2,1);
INSERT INTO ezcontentobject VALUES(3,1,13,2,4,1033920794,'Editors',14,1033920794,'3c160cca19fb135f83bd02d911f04db2',2,1);
INSERT INTO ezcontentobject VALUES(4,4,14,2,6,1704475235,'Administrator User',14,1033920830,'1bb4fe25487f05527efa8bfd394cecc7',2,1);
INSERT INTO ezcontentobject VALUES(1,1,41,2,4,1060695457,'Media',14,1060695457,'a6e35cbcb7cd6ae4b691f3eee30cd262',3,1);
INSERT INTO ezcontentobject VALUES(3,1,42,2,4,1072180330,'Anonymous Users',14,1072180330,'15b256dbea2ae72418ff5facc999e8f9',2,1);
INSERT INTO ezcontentobject VALUES(1,1,45,2,4,1079684190,'Setup',14,1079684190,'241d538ce310074e602f29f49e44e938',4,1);
INSERT INTO ezcontentobject VALUES(1,1,49,2,4,1080220197,'Images',14,1080220197,'e7ff633c6b8e0fd3531e74c6e712bead',3,1);
INSERT INTO ezcontentobject VALUES(1,1,50,2,4,1080220220,'Files',14,1080220220,'732a5acd01b51a6fe6eab448ad4138a9',3,1);
INSERT INTO ezcontentobject VALUES(1,1,51,2,4,1080220233,'Multimedia',14,1080220233,'09082deb98662a104f325aaa8c4933d3',3,1);
INSERT INTO ezcontentobject VALUES(14,1,52,2,2,1082016591,'Common INI settings',14,1082016591,'27437f3547db19cf81a33c92578b2c89',4,1);
INSERT INTO ezcontentobject VALUES(15,2,54,2,2,1301062376,'',14,1082016652,'8b8b22fe3c6061ed500fbd2b377b885f',5,1);
INSERT INTO ezcontentobject VALUES(1,1,56,2,4,1103023132,'Design',14,1103023132,'08799e609893f7aba22f10cb466d9cc8',5,1);
INSERT INTO ezcontentobject VALUES(23,13,57,4,5,1704581417,'Home',14,1193906012,'8a9c9c761004866fb458d89910f52bee',1,1);
INSERT INTO ezcontentobject VALUES(16,2,59,4,5,1704585741,'Testing 1234 SQLite has hit the foor!',14,1704579784,'c722fd2e3cf9ec072d0402881110f996',1,1);
INSERT INTO ezcontentobject VALUES(58,1,60,4,4,1704585539,'SQLite.org',14,1704585539,'95e0bf89e9661119377e3ddf672b8de0',1,1);
INSERT INTO ezcontentobject VALUES(58,1,61,4,4,1704585628,'Share',14,1704585628,'dfc054e2bb689abf0e6b2702703a7dad',1,1);
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
INSERT INTO ezcontentobject_attribute VALUES(0,7,4,NULL,NULL,'Main group','ezstring',7,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,6,4,NULL,NULL,'Users','ezstring',8,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,8,10,0.0,0,'Anonymous','ezstring',19,'eng-US',5,0,'anonymous',2);
INSERT INTO ezcontentobject_attribute VALUES(0,9,10,0.0,0,'User','ezstring',20,'eng-US',5,0,'user',2);
INSERT INTO ezcontentobject_attribute VALUES(0,12,10,0.0,0,'','ezuser',21,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,6,11,0.0,0,'Guest accounts','ezstring',22,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,7,11,0.0,0,'','ezstring',23,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,6,12,0.0,0,'Administrator users','ezstring',24,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,7,12,0.0,0,'','ezstring',25,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,6,13,0.0,0,'Editors','ezstring',26,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,7,13,0.0,0,'','ezstring',27,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,8,14,0.0,0,'Administrator','ezstring',28,'eng-US',5,0,'administrator',4);
INSERT INTO ezcontentobject_attribute VALUES(0,9,14,0.0,0,'User','ezstring',29,'eng-US',5,0,'user',4);
INSERT INTO ezcontentobject_attribute VALUES(213,12,14,0.0,0,'','ezuser',30,'eng-US',5,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,4,41,0.0,0,'Media','ezstring',98,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,41,0.0,1045487555,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section xmlns:image=\"http://ez.no/namespaces/ezpublish3/image/\"\n         xmlns:xhtml=\"http://ez.no/namespaces/ezpublish3/xhtml/\"\n         xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\" />','ezxmltext',99,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,6,42,0.0,0,'Anonymous Users','ezstring',100,'eng-US',5,0,'anonymous users',1);
INSERT INTO ezcontentobject_attribute VALUES(0,7,42,0.0,0,'User group for the anonymous user','ezstring',101,'eng-US',5,0,'user group for the anonymous user',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,41,0.0,0,'','ezstring',103,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,41,0.0,1045487555,'','ezxmltext',105,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,41,0.0,0,'','ezboolean',109,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,45,0.0,0,'Setup','ezstring',123,'eng-US',5,0,'setup',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,45,0.0,0,'','ezstring',124,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,45,0.0,1045487555,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section xmlns:image=\"http://ez.no/namespaces/ezpublish3/image/\"\n         xmlns:xhtml=\"http://ez.no/namespaces/ezpublish3/xhtml/\"\n         xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\" />','ezxmltext',125,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,45,0.0,1045487555,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section xmlns:image=\"http://ez.no/namespaces/ezpublish3/image/\"\n         xmlns:xhtml=\"http://ez.no/namespaces/ezpublish3/xhtml/\"\n         xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\" />','ezxmltext',126,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,45,0.0,0,'','ezboolean',128,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,49,0.0,0,'Images','ezstring',142,'eng-US',5,0,'images',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,49,0.0,0,'','ezstring',143,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,49,0.0,1045487555,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section xmlns:image=\"http://ez.no/namespaces/ezpublish3/image/\"\n         xmlns:xhtml=\"http://ez.no/namespaces/ezpublish3/xhtml/\"\n         xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\" />','ezxmltext',144,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,49,0.0,1045487555,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section xmlns:image=\"http://ez.no/namespaces/ezpublish3/image/\"\n         xmlns:xhtml=\"http://ez.no/namespaces/ezpublish3/xhtml/\"\n         xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\" />','ezxmltext',145,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,49,0.0,1,'','ezboolean',146,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,50,0.0,0,'Files','ezstring',147,'eng-US',5,0,'files',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,50,0.0,0,'','ezstring',148,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,50,0.0,1045487555,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section xmlns:image=\"http://ez.no/namespaces/ezpublish3/image/\"\n         xmlns:xhtml=\"http://ez.no/namespaces/ezpublish3/xhtml/\"\n         xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\" />','ezxmltext',149,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,50,0.0,1045487555,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section xmlns:image=\"http://ez.no/namespaces/ezpublish3/image/\"\n         xmlns:xhtml=\"http://ez.no/namespaces/ezpublish3/xhtml/\"\n         xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\" />','ezxmltext',150,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,50,0.0,1,'','ezboolean',151,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,51,0.0,0,'Multimedia','ezstring',152,'eng-US',5,0,'multimedia',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,51,0.0,0,'','ezstring',153,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,51,0.0,1045487555,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section xmlns:image=\"http://ez.no/namespaces/ezpublish3/image/\"\n         xmlns:xhtml=\"http://ez.no/namespaces/ezpublish3/xhtml/\"\n         xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\" />','ezxmltext',154,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,51,0.0,1045487555,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section xmlns:image=\"http://ez.no/namespaces/ezpublish3/image/\"\n         xmlns:xhtml=\"http://ez.no/namespaces/ezpublish3/xhtml/\"\n         xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\" />','ezxmltext',155,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,51,0.0,1,'','ezboolean',156,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,159,52,0.0,0,'Common INI settings','ezstring',157,'eng-US',4,0,'common ini settings',1);
INSERT INTO ezcontentobject_attribute VALUES(0,160,52,0.0,0,'/content/view/full/2/','ezinisetting',158,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,161,52,0.0,0,'/content/view/full/2','ezinisetting',159,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,162,52,0.0,0,'disabled','ezinisetting',160,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,163,52,0.0,0,'disabled','ezinisetting',161,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,164,52,0.0,0,'','ezinisetting',162,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,165,52,0.0,0,'enabled','ezinisetting',163,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,166,52,0.0,0,'disabled','ezinisetting',164,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,167,52,0.0,0,'enabled','ezinisetting',165,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,168,52,0.0,0,'enabled','ezinisetting',166,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,169,52,0.0,0,'=geometry/scale=100;100','ezinisetting',167,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,170,52,0.0,0,'=geometry/scale=200;200','ezinisetting',168,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,171,52,0.0,0,'=geometry/scale=300;300','ezinisetting',169,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,172,54,0.0,0,'Plain site','ezinisetting',170,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,173,54,0.0,0,'author=eZ Systems\ncopyright=eZ Systems\ndescription=Content Management System\nkeywords=cms, publish, e-commerce, content management, development framework','ezinisetting',171,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,174,54,0.0,0,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<ezimage serial_number=\"1\" is_valid=\"\" filename=\"\" suffix=\"\" basename=\"\" dirpath=\"\" url=\"\" original_filename=\"\" mime_type=\"\" width=\"\" height=\"\" alternative_text=\"\" alias_key=\"1293033771\" timestamp=\"1082016632\"><original attribute_id=\"172\" attribute_version=\"2\" attribute_language=\"eng-GB\"/></ezimage>\n','ezimage',172,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,175,54,0.0,0,'0','ezpackage',173,'eng-US',4,0,'0',2);
INSERT INTO ezcontentobject_attribute VALUES(0,177,54,0.0,0,'nospam@ez.no','ezinisetting',175,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,178,54,0.0,0,'ez.no','ezinisetting',176,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,179,10,0.0,0,'','eztext',177,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,179,14,0.0,0,'','eztext',178,'eng-US',5,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,180,10,0.0,0,'','ezimage',179,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,180,14,0.0,0,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<ezimage serial_number=\"1\" is_valid=\"\" filename=\"\" suffix=\"\" basename=\"\" dirpath=\"\" url=\"\" original_filename=\"\" mime_type=\"\" width=\"\" height=\"\" alternative_text=\"\" alias_key=\"1293033771\" timestamp=\"1301057722\"><original attribute_id=\"180\" attribute_version=\"3\" attribute_language=\"eng-GB\"/></ezimage>\n','ezimage',180,'eng-US',5,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,4,56,0.0,NULL,'Design','ezstring',181,'eng-US',5,0,'design',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,56,0.0,NULL,'','ezstring',182,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,56,0.0,1045487555,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section xmlns:image=\"http://ez.no/namespaces/ezpublish3/image/\"\n         xmlns:xhtml=\"http://ez.no/namespaces/ezpublish3/xhtml/\"\n         xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\" />','ezxmltext',183,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,56,0.0,1045487555,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<section xmlns:image=\"http://ez.no/namespaces/ezpublish3/image/\"\n         xmlns:xhtml=\"http://ez.no/namespaces/ezpublish3/xhtml/\"\n         xmlns:custom=\"http://ez.no/namespaces/ezpublish3/custom/\" />','ezxmltext',184,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,56,0.0,1,'','ezboolean',185,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,41,0.0,NULL,'','ezkeyword',187,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,45,0.0,NULL,'','ezkeyword',188,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,49,0.0,NULL,'','ezkeyword',189,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,50,0.0,NULL,'','ezkeyword',190,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,51,0.0,NULL,'','ezkeyword',191,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,56,0.0,NULL,'','ezkeyword',192,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,41,0.0,NULL,'','ezdatetime',194,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,45,0.0,NULL,'','ezdatetime',195,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,49,0.0,NULL,'','ezdatetime',196,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,50,0.0,NULL,'','ezdatetime',197,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,51,0.0,NULL,'','ezdatetime',198,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,56,0.0,NULL,'','ezdatetime',199,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,8,14,0.0,NULL,'Administrator','ezstring',211,'eng-GB',2,0,'administrator',4);
INSERT INTO ezcontentobject_attribute VALUES(0,9,14,0.0,NULL,'User','ezstring',212,'eng-GB',2,0,'user',4);
INSERT INTO ezcontentobject_attribute VALUES(0,12,14,0.0,NULL,'','ezuser',213,'eng-GB',2,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,179,14,0.0,NULL,'','eztext',214,'eng-GB',2,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,180,14,0.0,NULL,'<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<ezimage serial_number=\"\" is_valid=\"\" filename=\"\" suffix=\"\" basename=\"\" dirpath=\"\" url=\"\" original_filename=\"\" mime_type=\"\" width=\"\" height=\"\" alternative_text=\"\" alias_key=\"1293033771\" timestamp=\"1704475235\"/>\n','ezimage',215,'eng-GB',2,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,329,54,0.0,0,'','ezurl',216,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,330,54,0.0,0,'','ezurl',217,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,331,54,0.0,NULL,'','ezstring',218,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,332,54,0.0,NULL,'','ezstring',219,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,333,54,0.0,NULL,'','ezstring',220,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,334,54,0.0,NULL,'','ezstring',221,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,335,54,0.0,NULL,'','ezstring',222,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,336,54,0.0,NULL,'','ezstring',223,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,337,54,0.0,NULL,'','ezstring',224,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,338,54,0.0,NULL,'','eztext',225,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,339,54,0.0,0,'','ezboolean',226,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,340,54,0.0,NULL,'','eztext',227,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,4,41,NULL,NULL,'Folder','ezstring',235,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,45,NULL,NULL,'Folder','ezstring',236,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,49,NULL,NULL,'Folder','ezstring',237,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,50,NULL,NULL,'Folder','ezstring',238,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,51,NULL,NULL,'Folder','ezstring',239,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,56,NULL,NULL,'Folder','ezstring',240,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,41,NULL,NULL,NULL,'ezstring',241,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,45,NULL,NULL,NULL,'ezstring',242,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,49,NULL,NULL,NULL,'ezstring',243,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,50,NULL,NULL,NULL,'ezstring',244,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,51,NULL,NULL,NULL,'ezstring',245,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,56,NULL,NULL,NULL,'ezstring',246,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,41,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',247,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,45,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',248,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,49,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',249,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,50,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',250,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,51,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',251,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,56,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',252,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,41,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',253,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,45,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',254,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,49,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',255,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,50,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',256,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,51,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',257,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,56,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',258,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,41,NULL,1,NULL,'ezboolean',259,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,45,NULL,1,NULL,'ezboolean',260,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,49,NULL,1,NULL,'ezboolean',261,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,50,NULL,1,NULL,'ezboolean',262,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,51,NULL,1,NULL,'ezboolean',263,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,56,NULL,1,NULL,'ezboolean',264,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,41,NULL,NULL,NULL,'ezkeyword',265,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,45,NULL,NULL,NULL,'ezkeyword',266,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,49,NULL,NULL,NULL,'ezkeyword',267,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,50,NULL,NULL,NULL,'ezkeyword',268,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,51,NULL,NULL,NULL,'ezkeyword',269,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,56,NULL,NULL,NULL,'ezkeyword',270,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,41,NULL,NULL,NULL,'ezdatetime',271,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,45,NULL,NULL,NULL,'ezdatetime',272,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,49,NULL,NULL,NULL,'ezdatetime',273,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,50,NULL,NULL,NULL,'ezdatetime',274,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,51,NULL,NULL,NULL,'ezdatetime',275,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,56,NULL,NULL,NULL,'ezdatetime',276,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,242,57,0.0,NULL,'','ezkeyword',304,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,4,41,NULL,NULL,'Folder','ezstring',309,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,45,NULL,NULL,'Folder','ezstring',310,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,49,NULL,NULL,'Folder','ezstring',311,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,50,NULL,NULL,'Folder','ezstring',312,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,51,NULL,NULL,'Folder','ezstring',313,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,56,NULL,NULL,'Folder','ezstring',314,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,41,NULL,NULL,NULL,'ezstring',315,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,45,NULL,NULL,NULL,'ezstring',316,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,49,NULL,NULL,NULL,'ezstring',317,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,50,NULL,NULL,NULL,'ezstring',318,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,51,NULL,NULL,NULL,'ezstring',319,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,56,NULL,NULL,NULL,'ezstring',320,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,41,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',321,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,45,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',322,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,49,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',323,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,50,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',324,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,51,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',325,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,56,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',326,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,41,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',327,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,45,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',328,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,49,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',329,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,50,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',330,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,51,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',331,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,56,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',332,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,41,NULL,1,NULL,'ezboolean',333,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,45,NULL,1,NULL,'ezboolean',334,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,49,NULL,1,NULL,'ezboolean',335,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,50,NULL,1,NULL,'ezboolean',336,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,51,NULL,1,NULL,'ezboolean',337,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,56,NULL,1,NULL,'ezboolean',338,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,41,NULL,NULL,NULL,'ezkeyword',339,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,45,NULL,NULL,NULL,'ezkeyword',340,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,49,NULL,NULL,NULL,'ezkeyword',341,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,50,NULL,NULL,NULL,'ezkeyword',342,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,51,NULL,NULL,NULL,'ezkeyword',343,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,56,NULL,NULL,NULL,'ezkeyword',344,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,41,NULL,NULL,NULL,'ezdatetime',345,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,45,NULL,NULL,NULL,'ezdatetime',346,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,49,NULL,NULL,NULL,'ezdatetime',347,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,50,NULL,NULL,NULL,'ezdatetime',348,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,51,NULL,NULL,NULL,'ezdatetime',349,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,56,NULL,NULL,NULL,'ezdatetime',350,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,242,57,NULL,NULL,NULL,'ezkeyword',357,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,4,41,NULL,NULL,'Folder','ezstring',358,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,45,NULL,NULL,'Folder','ezstring',359,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,49,NULL,NULL,'Folder','ezstring',360,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,50,NULL,NULL,'Folder','ezstring',361,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,51,NULL,NULL,'Folder','ezstring',362,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,4,56,NULL,NULL,'Folder','ezstring',363,'eng-US',5,0,'folder',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,41,NULL,NULL,NULL,'ezstring',364,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,45,NULL,NULL,NULL,'ezstring',365,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,49,NULL,NULL,NULL,'ezstring',366,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,50,NULL,NULL,NULL,'ezstring',367,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,51,NULL,NULL,NULL,'ezstring',368,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,155,56,NULL,NULL,NULL,'ezstring',369,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,41,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',370,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,45,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',371,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,49,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',372,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,50,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',373,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,51,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',374,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,119,56,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',375,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,41,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',376,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,45,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',377,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,49,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',378,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,50,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',379,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,51,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',380,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,156,56,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',381,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,41,NULL,1,NULL,'ezboolean',382,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,45,NULL,1,NULL,'ezboolean',383,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,49,NULL,1,NULL,'ezboolean',384,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,50,NULL,1,NULL,'ezboolean',385,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,51,NULL,1,NULL,'ezboolean',386,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,158,56,NULL,1,NULL,'ezboolean',387,'eng-US',5,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,41,NULL,NULL,NULL,'ezkeyword',388,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,45,NULL,NULL,NULL,'ezkeyword',389,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,49,NULL,NULL,NULL,'ezkeyword',390,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,50,NULL,NULL,NULL,'ezkeyword',391,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,51,NULL,NULL,NULL,'ezkeyword',392,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,181,56,NULL,NULL,NULL,'ezkeyword',393,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,41,NULL,NULL,NULL,'ezdatetime',394,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,45,NULL,NULL,NULL,'ezdatetime',395,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,49,NULL,NULL,NULL,'ezdatetime',396,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,50,NULL,NULL,NULL,'ezdatetime',397,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,51,NULL,NULL,NULL,'ezdatetime',398,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,182,56,NULL,NULL,NULL,'ezdatetime',399,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,241,57,0.0,1045487555,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',406,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,8,10,NULL,NULL,NULL,'ezstring',407,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,8,14,NULL,NULL,NULL,'ezstring',408,'eng-GB',2,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,8,14,NULL,NULL,NULL,'ezstring',409,'eng-US',5,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,9,10,NULL,NULL,NULL,'ezstring',410,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,9,14,NULL,NULL,NULL,'ezstring',411,'eng-GB',2,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,9,14,NULL,NULL,NULL,'ezstring',412,'eng-US',5,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,12,10,0.0,NULL,'','ezuser',413,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,12,14,0.0,NULL,'','ezuser',414,'eng-GB',2,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,12,14,0.0,NULL,'','ezuser',415,'eng-US',4,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,179,10,NULL,NULL,NULL,'eztext',416,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,179,14,NULL,NULL,NULL,'eztext',417,'eng-GB',2,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,179,14,NULL,NULL,NULL,'eztext',418,'eng-US',5,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,180,10,NULL,NULL,NULL,'ezimage',419,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,180,14,NULL,NULL,NULL,'ezimage',420,'eng-GB',2,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,180,14,NULL,NULL,NULL,'ezimage',421,'eng-US',5,0,'',4);
INSERT INTO ezcontentobject_attribute VALUES(0,6,4,NULL,NULL,NULL,'ezstring',422,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,6,11,NULL,NULL,NULL,'ezstring',423,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,6,12,NULL,NULL,NULL,'ezstring',424,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,6,13,NULL,NULL,NULL,'ezstring',425,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,6,42,NULL,NULL,NULL,'ezstring',426,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,7,4,NULL,NULL,NULL,'ezstring',427,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,7,11,NULL,NULL,NULL,'ezstring',428,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,7,12,NULL,NULL,NULL,'ezstring',429,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,7,13,NULL,NULL,NULL,'ezstring',430,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,7,42,NULL,NULL,NULL,'ezstring',431,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,335,4,NULL,0,NULL,'ezboolean',432,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,335,11,NULL,0,NULL,'ezboolean',433,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,335,12,NULL,0,NULL,'ezboolean',434,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,335,13,NULL,0,NULL,'ezboolean',435,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,335,42,NULL,0,NULL,'ezboolean',436,'eng-US',5,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,172,54,0.0,NULL,'7x Demo : Website Interface LS','ezinisetting',437,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,322,54,NULL,NULL,NULL,'ezurl',438,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,323,54,NULL,NULL,NULL,'ezurl',439,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,173,54,0.0,NULL,replace('author=eZ Systems\ncopyright=eZ Systems\ndescription=Content Management System\nkeywords=cms, publish, e-commerce, content management, development framework\n','\n',char(10)),'ezinisetting',440,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,324,54,NULL,NULL,NULL,'ezstring',441,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,174,54,NULL,NULL,NULL,'ezimage',442,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,325,54,NULL,NULL,NULL,'ezstring',443,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,175,54,0.0,NULL,'','ezpackage',444,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,326,54,NULL,NULL,NULL,'ezstring',445,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,177,54,0.0,NULL,'info@se7enx.com','ezinisetting',446,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,327,54,NULL,NULL,NULL,'ezstring',447,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,178,54,0.0,NULL,'alpha.se7enx.com','ezinisetting',448,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,328,54,NULL,NULL,NULL,'ezstring',449,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,329,54,NULL,NULL,NULL,'ezstring',450,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,330,54,NULL,NULL,NULL,'ezstring',451,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,331,54,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<ezmatrix><name/><columns number="0"/><rows number="0"/></ezmatrix>\n','\n',char(10)),'ezmatrix',452,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,332,54,NULL,NULL,NULL,'eztext',453,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,333,54,NULL,0,NULL,'ezboolean',454,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,334,54,NULL,NULL,NULL,'eztext',455,'eng-US',4,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,159,52,NULL,NULL,NULL,'ezstring',456,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,160,52,0.0,NULL,'/content/view/full/2/','ezinisetting',457,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,161,52,0.0,NULL,'/content/view/full/2/','ezinisetting',458,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,162,52,0.0,NULL,'enabled','ezinisetting',459,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,163,52,0.0,NULL,'disabled','ezinisetting',460,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,164,52,0.0,NULL,'','ezinisetting',461,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,165,52,0.0,NULL,'disabled','ezinisetting',462,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,166,52,0.0,NULL,'enabled','ezinisetting',463,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,167,52,0.0,NULL,'enabled','ezinisetting',464,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,168,52,0.0,NULL,'enabled','ezinisetting',465,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,169,52,0.0,NULL,replace('=geometry/scaledownonly=100;160\n','\n',char(10)),'ezinisetting',466,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,170,52,0.0,NULL,replace('=geometry/scaledownonly=200;290\n','\n',char(10)),'ezinisetting',467,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,171,52,0.0,NULL,replace('=geometry/scaledownonly=360;440\n','\n',char(10)),'ezinisetting',468,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,183,59,0.0,NULL,'Testing 1234 SQLite has hit the foor!','ezstring',469,'eng-US',5,0,'testing 1234 sqlite has hit the foor!',2);
INSERT INTO ezcontentobject_attribute VALUES(0,184,59,0.0,NULL,'','ezstring',470,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,185,59,0.0,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<ezauthor><authors><author id="0" name="Administrator User" email="info@se7enx.com"/></authors></ezauthor>\n','\n',char(10)),'ezauthor',471,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,186,59,0.0,1045487555,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>Hello this is exciting your viewing eZ Publish powered by a brand new sqlite database!</paragraph></section>\n','\n',char(10)),'ezxmltext',472,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,187,59,0.0,1045487555,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>We are hoping to silently include this support as experimental but tested as stable into the next version of eZ Publish very soon.</paragraph></section>\n','\n',char(10)),'ezxmltext',473,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(474,188,59,0.0,1,'','ezboolean',474,'eng-US',5,1,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,189,59,0.0,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<ezimage serial_number="1" is_valid="1" filename="Testing-1234-SQLite-has-hit-the-foor.jpg" suffix="jpg" basename="Testing-1234-SQLite-has-hit-the-foor" dirpath="var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US" url="var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US/Testing-1234-SQLite-has-hit-the-foor.jpg" original_filename="7x_logo_wide.jpg" mime_type="image/jpeg" width="386" height="292" alternative_text="" alias_key="1293033771" timestamp="1704585740"><original attribute_id="475" attribute_version="2" attribute_language="eng-US"/><information Height="292" Width="386" IsColor="1" ByteOrderMotorola="1" Thumbnail.FileType="2" Thumbnail.MimeType="image/jpeg"><array name="ifd0"><item key="XResolution" base64="1">NzIvMQ==</item><item key="YResolution" base64="1">NzIvMQ==</item><item key="ResolutionUnit" base64="1">Mg==</item><item key="YCbCrPositioning" base64="1">MQ==</item><item key="Exif_IFD_Pointer" base64="1">OTA=</item></array><array name="exif"><item key="ExifVersion" base64="1">MDIyMQ==</item><item key="ComponentsConfiguration" base64="1">AQIDAA==</item><item key="FlashPixVersion" base64="1">MDEwMA==</item><item key="ColorSpace" base64="1">MQ==</item><item key="ExifImageWidth" base64="1">Mzg2</item><item key="ExifImageLength" base64="1">Mjky</item><item key="SceneCaptureType" base64="1">MA==</item></array></information><alias name="reference" filename="Testing-1234-SQLite-has-hit-the-foor_reference.jpg" suffix="jpg" dirpath="var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US" url="var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US/Testing-1234-SQLite-has-hit-the-foor_reference.jpg" mime_type="image/jpeg" width="386" height="292" alias_key="2605465115" timestamp="1704585741" is_valid="1"/><alias name="small" filename="Testing-1234-SQLite-has-hit-the-foor_small.jpg" suffix="jpg" dirpath="var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US" url="var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US/Testing-1234-SQLite-has-hit-the-foor_small.jpg" mime_type="image/jpeg" width="100" height="76" alias_key="2343348577" timestamp="1704585741" is_valid="1"/><alias name="medium" filename="Testing-1234-SQLite-has-hit-the-foor_medium.jpg" suffix="jpg" dirpath="var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US" url="var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US/Testing-1234-SQLite-has-hit-the-foor_medium.jpg" mime_type="image/jpeg" width="200" height="151" alias_key="3736024005" timestamp="1704585746" is_valid="1"/></ezimage>\n','\n',char(10)),'ezimage',475,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,190,59,0.0,1045487555,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',476,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,191,59,0.0,NULL,'','ezdatetime',477,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,192,59,0.0,NULL,'','ezdatetime',478,'eng-US',5,0,'',2);
INSERT INTO ezcontentobject_attribute VALUES(0,235,57,NULL,NULL,NULL,'ezstring',479,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,236,57,NULL,NULL,NULL,'ezobjectrelation',481,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,237,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',483,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,238,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',485,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,239,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',487,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,240,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',489,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,235,57,NULL,NULL,NULL,'ezstring',491,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,236,57,NULL,NULL,NULL,'ezobjectrelation',493,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,237,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',495,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,238,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',497,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,239,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',499,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,240,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',501,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,235,57,NULL,NULL,NULL,'ezstring',503,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,235,57,NULL,NULL,NULL,'ezstring',504,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,236,57,NULL,NULL,NULL,'ezobjectrelation',506,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,236,57,NULL,NULL,NULL,'ezobjectrelation',507,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,237,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',509,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,237,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',510,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,238,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',512,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,238,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',513,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,239,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',515,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,239,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',516,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,240,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',518,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,240,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',519,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,370,57,NULL,NULL,NULL,'ezstring',521,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,370,57,NULL,NULL,NULL,'ezstring',522,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,236,57,NULL,NULL,NULL,'ezobjectrelation',523,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,236,57,NULL,NULL,NULL,'ezobjectrelation',524,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,237,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',525,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,237,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',526,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,238,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',527,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,238,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',528,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,239,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',529,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,239,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',530,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,240,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',531,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,240,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',532,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,370,57,NULL,NULL,NULL,'ezstring',533,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,370,57,NULL,NULL,NULL,'ezstring',534,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,236,57,NULL,NULL,NULL,'ezobjectrelation',535,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,236,57,NULL,NULL,NULL,'ezobjectrelation',536,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,237,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',537,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,237,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',538,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,238,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',539,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,238,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',540,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,239,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',541,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,239,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',542,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,240,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',543,'eng-US',5,0,'',7);
INSERT INTO ezcontentobject_attribute VALUES(0,240,57,NULL,NULL,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',544,'eng-US',5,0,'',9);
INSERT INTO ezcontentobject_attribute VALUES(0,370,57,0.0,NULL,'Home','ezstring',545,'eng-US',5,0,'home',13);
INSERT INTO ezcontentobject_attribute VALUES(0,236,57,0.0,NULL,'','ezobjectrelation',546,'eng-US',5,0,'',13);
INSERT INTO ezcontentobject_attribute VALUES(0,237,57,0.0,1045487555,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>Welcome to the wonders of eZ Publish powered by SQLite!</paragraph><paragraph xmlns:tmp="http://ez.no/namespaces/ezpublish3/temporary/"><ul><li><paragraph xmlns:tmp="http://ez.no/namespaces/ezpublish3/temporary/"><embed class="highlighted_object" view="embed" size="medium" node_id="61" custom:offset="0" custom:limit="5"/></paragraph></li></ul></paragraph></section>\n','\n',char(10)),'ezxmltext',547,'eng-US',5,0,'',13);
INSERT INTO ezcontentobject_attribute VALUES(0,238,57,0.0,1045487555,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',548,'eng-US',5,0,'',13);
INSERT INTO ezcontentobject_attribute VALUES(0,239,57,0.0,1045487555,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',549,'eng-US',5,0,'',13);
INSERT INTO ezcontentobject_attribute VALUES(0,240,57,0.0,1045487555,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"/>\n','\n',char(10)),'ezxmltext',550,'eng-US',5,0,'',13);
INSERT INTO ezcontentobject_attribute VALUES(0,439,60,0.0,NULL,'SQLite.org','ezstring',551,'eng-US',4,0,'sqlite.org',1);
INSERT INTO ezcontentobject_attribute VALUES(0,440,60,0.0,1045487555,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>SQLite.org</paragraph></section>\n','\n',char(10)),'ezxmltext',552,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,441,60,0.0,34,'SQLite.org','ezurl',553,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,442,60,0.0,1,'','ezboolean',554,'eng-US',4,1,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,439,61,0.0,NULL,'Share','ezstring',555,'eng-US',4,0,'share',1);
INSERT INTO ezcontentobject_attribute VALUES(0,440,61,0.0,1045487555,replace('<?xml version="1.0" encoding="utf-8"?>\n<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph>Share eZ Publish</paragraph></section>\n','\n',char(10)),'ezxmltext',556,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,441,61,0.0,35,'Share','ezurl',557,'eng-US',4,0,'',1);
INSERT INTO ezcontentobject_attribute VALUES(0,442,61,0.0,0,'','ezboolean',558,'eng-US',4,0,'',1);
CREATE TABLE `ezcontentobject_link` (
  `contentclassattribute_id` integer NOT NULL DEFAULT '0'
,  `from_contentobject_id` integer NOT NULL DEFAULT '0'
,  `from_contentobject_version` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `relation_type` integer NOT NULL DEFAULT '1'
,  `to_contentobject_id` integer NOT NULL DEFAULT '0'
);
INSERT INTO ezcontentobject_link VALUES(0,57,11,3,2,59);
INSERT INTO ezcontentobject_link VALUES(0,57,13,6,2,59);
CREATE TABLE `ezcontentobject_name` (
  `content_translation` varchar(20) NOT NULL DEFAULT ''
,  `content_version` integer NOT NULL DEFAULT '0'
,  `contentobject_id` integer NOT NULL DEFAULT '0'
,  `language_id` integer NOT NULL DEFAULT '0'
,  `name` varchar(255) DEFAULT NULL
,  `real_translation` varchar(20) DEFAULT NULL
,  PRIMARY KEY (`contentobject_id`,`content_version`,`content_translation`)
);
INSERT INTO ezcontentobject_name VALUES('eng-US',1,4,5,'Users','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',2,10,5,'Anonymous User','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,11,5,'Guest accounts','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,12,5,'Administrator users','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,13,5,'Editors','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',3,14,5,'Administrator User','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-GB',4,14,2,'Administrator User','eng-GB');
INSERT INTO ezcontentobject_name VALUES('eng-US',4,14,4,'Administrator User','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,41,5,'Media','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,42,5,'Anonymous Users','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,45,5,'Setup','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,49,5,'Images','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,50,5,'Files','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,51,5,'Multimedia','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,52,4,'Common INI settings','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-GB',2,54,2,'','eng-GB');
INSERT INTO ezcontentobject_name VALUES('eng-US',2,54,4,'Plain site','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,56,5,'Design','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',5,57,5,'','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',6,57,5,'Home','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',7,57,5,'Home','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',8,57,5,'','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',9,57,5,'Home','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,59,5,'Testing 1234 SQLite has hit the foor!','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',10,57,5,'','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',11,57,5,'','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',12,57,5,'Home','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',13,57,5,'Home','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,60,4,'SQLite.org','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',1,61,4,'Share','eng-US');
INSERT INTO ezcontentobject_name VALUES('eng-US',2,59,5,'Testing 1234 SQLite has hit the foor!','eng-US');
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
INSERT INTO ezcontentobject_tree VALUES(0,1,1,0,0,0,1,1704585741,1,1,'','/1/',0,'629709ba256fe317c3ddcee35453a96a',1,1);
INSERT INTO ezcontentobject_tree VALUES(57,1,13,1,0,0,2,1704585741,2,1,'','/1/2/',0,'f3e90596361e31d496d4026eb624c983',8,1);
INSERT INTO ezcontentobject_tree VALUES(4,1,1,1,0,0,5,1704475235,5,1,'users','/1/5/',0,'3f6d92f8044aed134f32153517850f5a',1,1);
INSERT INTO ezcontentobject_tree VALUES(11,1,1,2,0,0,12,1081860719,12,5,'users/guest_accounts','/1/5/12/',0,'602dcf84765e56b7f999eaafd3821dd3',1,1);
INSERT INTO ezcontentobject_tree VALUES(12,1,1,2,0,0,13,1704475235,13,5,'users/administrator_users','/1/5/13/',0,'769380b7aa94541679167eab817ca893',1,1);
INSERT INTO ezcontentobject_tree VALUES(13,1,1,2,0,0,14,1081860719,14,5,'users/editors','/1/5/14/',0,'f7dda2854fc68f7c8455d9cb14bd04a9',1,1);
INSERT INTO ezcontentobject_tree VALUES(14,1,4,3,0,0,15,1704475235,15,13,'users/administrator_users/administrator_user','/1/5/13/15/',0,'e5161a99f733200b9ed4e80f9c16187b',1,1);
INSERT INTO ezcontentobject_tree VALUES(41,1,1,1,0,0,43,1704579678,43,1,'media','/1/43/',0,'75c715a51699d2d309a924eca6a95145',9,1);
INSERT INTO ezcontentobject_tree VALUES(42,1,1,2,0,0,44,1081860719,44,5,'users/anonymous_users','/1/5/44/',0,'4fdf0072da953bb276c0c7e0141c5c9b',9,1);
INSERT INTO ezcontentobject_tree VALUES(10,1,2,3,0,0,45,1081860719,45,44,'users/anonymous_users/anonymous_user','/1/5/44/45/',0,'2cf8343bee7b482bab82b269d8fecd76',9,1);
INSERT INTO ezcontentobject_tree VALUES(45,1,1,1,0,0,48,1184592117,48,1,'setup2','/1/48/',0,'182ce1b5af0c09fa378557c462ba2617',9,1);
INSERT INTO ezcontentobject_tree VALUES(49,1,1,2,0,0,51,1704579678,51,43,'media/images','/1/43/51/',0,'1b26c0454b09bb49dfb1b9190ffd67cb',9,1);
INSERT INTO ezcontentobject_tree VALUES(50,1,1,2,0,0,52,1081860720,52,43,'media/files','/1/43/52/',0,'0b113a208f7890f9ad3c24444ff5988c',9,1);
INSERT INTO ezcontentobject_tree VALUES(51,1,1,2,0,0,53,1081860720,53,43,'media/multimedia','/1/43/53/',0,'4f18b82c75f10aad476cae5adf98c11f',9,1);
INSERT INTO ezcontentobject_tree VALUES(52,1,1,2,0,0,54,1184592117,54,48,'setup2/common_ini_settings','/1/48/54/',0,'fa9f3cff9cf90ecfae335718dcbddfe2',1,1);
INSERT INTO ezcontentobject_tree VALUES(54,1,2,2,0,0,56,1704475235,56,58,'design/plain_site','/1/58/56/',0,'772da20ecf88b3035d73cbdfcea0f119',1,1);
INSERT INTO ezcontentobject_tree VALUES(56,1,1,1,0,0,58,1704475235,58,1,'design','/1/58/',0,'79f2d67372ab56f59b5d65bb9e0ca3b9',2,0);
INSERT INTO ezcontentobject_tree VALUES(59,1,2,2,0,0,61,1704585741,61,2,'testing_1234_sqlite_has_hit_the_foor','/1/2/61/',0,'014d5ee6adb7533080214510e9678e6e',1,1);
INSERT INTO ezcontentobject_tree VALUES(60,1,1,2,0,0,62,1704585539,62,2,'sqlite_org','/1/2/62/',0,'6ce39bca9dab75fc1a557398a129b335',1,1);
INSERT INTO ezcontentobject_tree VALUES(61,1,1,2,0,0,63,1704585628,63,2,'share','/1/2/63/',0,'c56915e9299057c1ea1aaf326b007803',1,1);
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
INSERT INTO ezcontentobject_version VALUES(4,0,14,4,2,4,0,1,0,1,1);
INSERT INTO ezcontentobject_version VALUES(11,1033920737,14,439,2,4,1033920746,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(12,1033920760,14,440,2,4,1033920775,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(13,1033920786,14,441,2,4,1033920794,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(41,1060695450,14,472,2,4,1060695457,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(42,1072180278,14,473,2,4,1072180330,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(10,1072180337,14,474,2,4,1072180405,1,0,2,0);
INSERT INTO ezcontentobject_version VALUES(45,1079684084,14,477,2,4,1079684190,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(49,1080220181,14,488,2,4,1080220197,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(50,1080220211,14,489,2,4,1080220220,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(51,1080220225,14,490,2,4,1080220233,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(52,1082016497,14,491,2,3,1082016591,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(56,1103023120,14,495,2,4,1103023120,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(14,1301061783,14,499,2,4,1301062024,3,0,3,0);
INSERT INTO ezcontentobject_version VALUES(54,1301062300,14,500,2,3,1301062375,1,0,2,0);
INSERT INTO ezcontentobject_version VALUES(14,1704475235,14,506,2,7,1704475235,1,0,4,0);
INSERT INTO ezcontentobject_version VALUES(57,1704490510,14,510,4,5,1704490519,3,0,5,0);
INSERT INTO ezcontentobject_version VALUES(57,1704490920,14,512,4,5,1704490925,3,0,6,0);
INSERT INTO ezcontentobject_version VALUES(57,1704490931,14,513,4,5,1704490946,3,0,7,0);
INSERT INTO ezcontentobject_version VALUES(57,1704578880,14,515,4,0,1704578880,3,0,8,0);
INSERT INTO ezcontentobject_version VALUES(57,1704578892,14,516,4,5,1704578912,3,0,9,0);
INSERT INTO ezcontentobject_version VALUES(59,1704579760,14,517,4,5,1704579784,3,0,1,0);
INSERT INTO ezcontentobject_version VALUES(57,1704580934,14,519,4,5,1704580951,3,0,10,0);
INSERT INTO ezcontentobject_version VALUES(57,1704580961,14,520,4,5,1704580989,3,0,11,0);
INSERT INTO ezcontentobject_version VALUES(57,1704581352,14,522,4,5,1704581364,3,0,12,0);
INSERT INTO ezcontentobject_version VALUES(57,1704581394,14,523,4,5,1704581417,1,0,13,0);
INSERT INTO ezcontentobject_version VALUES(60,1704585518,14,524,4,5,1704585539,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(61,1704585564,14,525,4,5,1704585628,1,0,1,0);
INSERT INTO ezcontentobject_version VALUES(59,1704585641,14,526,4,5,1704585740,1,0,2,0);
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
INSERT INTO ezimagefile VALUES(209,'var/ezwebin_site/storage/images/media/images/ez-logo/209-1-eng-US/eZ-Logo.gif',2);
INSERT INTO ezimagefile VALUES(475,'var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US/Testing-1234-SQLite-has-hit-the-foor.jpg',4);
INSERT INTO ezimagefile VALUES(475,'var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US/Testing-1234-SQLite-has-hit-the-foor_reference.jpg',5);
INSERT INTO ezimagefile VALUES(475,'var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US/Testing-1234-SQLite-has-hit-the-foor_small.jpg',6);
INSERT INTO ezimagefile VALUES(475,'var/ezwebin_site/storage/images/testing-1234-sqlite-has-hit-the-foor/475-2-eng-US/Testing-1234-SQLite-has-hit-the-foor_medium.jpg',7);
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
INSERT INTO ezisbn_group VALUES('English language',0,1);
INSERT INTO ezisbn_group VALUES('English language',1,2);
INSERT INTO ezisbn_group VALUES('French language',2,3);
INSERT INTO ezisbn_group VALUES('German language',3,4);
INSERT INTO ezisbn_group VALUES('Japan',4,5);
INSERT INTO ezisbn_group VALUES('Russian Federation and former USSR',5,6);
INSERT INTO ezisbn_group VALUES('Iran',600,7);
INSERT INTO ezisbn_group VALUES('Kazakhstan',601,8);
INSERT INTO ezisbn_group VALUES('Indonesia',602,9);
INSERT INTO ezisbn_group VALUES('Saudi Arabia',603,10);
INSERT INTO ezisbn_group VALUES('Vietnam',604,11);
INSERT INTO ezisbn_group VALUES('Turkey',605,12);
INSERT INTO ezisbn_group VALUES('Romania',606,13);
INSERT INTO ezisbn_group VALUES('Mexico',607,14);
INSERT INTO ezisbn_group VALUES('Macedonia',608,15);
INSERT INTO ezisbn_group VALUES('Lithuania',609,16);
INSERT INTO ezisbn_group VALUES('Thailand',611,17);
INSERT INTO ezisbn_group VALUES('Peru',612,18);
INSERT INTO ezisbn_group VALUES('Mauritius',613,19);
INSERT INTO ezisbn_group VALUES('Lebanon',614,20);
INSERT INTO ezisbn_group VALUES('Hungary',615,21);
INSERT INTO ezisbn_group VALUES('Thailand',616,22);
INSERT INTO ezisbn_group VALUES('Ukraine',617,23);
INSERT INTO ezisbn_group VALUES('China, People''s Republic',7,24);
INSERT INTO ezisbn_group VALUES('Czech Republic and Slovakia',80,25);
INSERT INTO ezisbn_group VALUES('India',81,26);
INSERT INTO ezisbn_group VALUES('Norway',82,27);
INSERT INTO ezisbn_group VALUES('Poland',83,28);
INSERT INTO ezisbn_group VALUES('Spain',84,29);
INSERT INTO ezisbn_group VALUES('Brazil',85,30);
INSERT INTO ezisbn_group VALUES('Serbia and Montenegro',86,31);
INSERT INTO ezisbn_group VALUES('Denmark',87,32);
INSERT INTO ezisbn_group VALUES('Italy',88,33);
INSERT INTO ezisbn_group VALUES('Korea, Republic',89,34);
INSERT INTO ezisbn_group VALUES('Netherlands',90,35);
INSERT INTO ezisbn_group VALUES('Sweden',91,36);
INSERT INTO ezisbn_group VALUES('International NGO Publishers and EC Organizations',92,37);
INSERT INTO ezisbn_group VALUES('India',93,38);
INSERT INTO ezisbn_group VALUES('Netherlands',94,39);
INSERT INTO ezisbn_group VALUES('Argentina',950,40);
INSERT INTO ezisbn_group VALUES('Finland',951,41);
INSERT INTO ezisbn_group VALUES('Finland',952,42);
INSERT INTO ezisbn_group VALUES('Croatia',953,43);
INSERT INTO ezisbn_group VALUES('Bulgaria',954,44);
INSERT INTO ezisbn_group VALUES('Sri Lanka',955,45);
INSERT INTO ezisbn_group VALUES('Chile',956,46);
INSERT INTO ezisbn_group VALUES('Taiwan',957,47);
INSERT INTO ezisbn_group VALUES('Colombia',958,48);
INSERT INTO ezisbn_group VALUES('Cuba',959,49);
INSERT INTO ezisbn_group VALUES('Greece',960,50);
INSERT INTO ezisbn_group VALUES('Slovenia',961,51);
INSERT INTO ezisbn_group VALUES('Hong Kong, China',962,52);
INSERT INTO ezisbn_group VALUES('Hungary',963,53);
INSERT INTO ezisbn_group VALUES('Iran',964,54);
INSERT INTO ezisbn_group VALUES('Israel',965,55);
INSERT INTO ezisbn_group VALUES('Ukraine',966,56);
INSERT INTO ezisbn_group VALUES('Malaysia',967,57);
INSERT INTO ezisbn_group VALUES('Mexico',968,58);
INSERT INTO ezisbn_group VALUES('Pakistan',969,59);
INSERT INTO ezisbn_group VALUES('Mexico',970,60);
INSERT INTO ezisbn_group VALUES('Philippines',971,61);
INSERT INTO ezisbn_group VALUES('Portugal',972,62);
INSERT INTO ezisbn_group VALUES('Romania',973,63);
INSERT INTO ezisbn_group VALUES('Thailand',974,64);
INSERT INTO ezisbn_group VALUES('Turkey',975,65);
INSERT INTO ezisbn_group VALUES('Caribbean Community',976,66);
INSERT INTO ezisbn_group VALUES('Egypt',977,67);
INSERT INTO ezisbn_group VALUES('Nigeria',978,68);
INSERT INTO ezisbn_group VALUES('Indonesia',979,69);
INSERT INTO ezisbn_group VALUES('Venezuela',980,70);
INSERT INTO ezisbn_group VALUES('Singapore',981,71);
INSERT INTO ezisbn_group VALUES('South Pacific',982,72);
INSERT INTO ezisbn_group VALUES('Malaysia',983,73);
INSERT INTO ezisbn_group VALUES('Bangladesh',984,74);
INSERT INTO ezisbn_group VALUES('Belarus',985,75);
INSERT INTO ezisbn_group VALUES('Taiwan',986,76);
INSERT INTO ezisbn_group VALUES('Argentina',987,77);
INSERT INTO ezisbn_group VALUES('Hong Kong, China',988,78);
INSERT INTO ezisbn_group VALUES('Portugal',989,79);
INSERT INTO ezisbn_group VALUES('Qatar',9927,80);
INSERT INTO ezisbn_group VALUES('Albania',9928,81);
INSERT INTO ezisbn_group VALUES('Guatemala',9929,82);
INSERT INTO ezisbn_group VALUES('Costa Rica',9930,83);
INSERT INTO ezisbn_group VALUES('Algeria',9931,84);
INSERT INTO ezisbn_group VALUES('Lao People''s Democratic Republic',9932,85);
INSERT INTO ezisbn_group VALUES('Syria',9933,86);
INSERT INTO ezisbn_group VALUES('Latvia',9934,87);
INSERT INTO ezisbn_group VALUES('Iceland',9935,88);
INSERT INTO ezisbn_group VALUES('Afghanistan',9936,89);
INSERT INTO ezisbn_group VALUES('Nepal',9937,90);
INSERT INTO ezisbn_group VALUES('Tunisia',9938,91);
INSERT INTO ezisbn_group VALUES('Armenia',9939,92);
INSERT INTO ezisbn_group VALUES('Montenegro',9940,93);
INSERT INTO ezisbn_group VALUES('Georgia',9941,94);
INSERT INTO ezisbn_group VALUES('Ecuador',9942,95);
INSERT INTO ezisbn_group VALUES('Uzbekistan',9943,96);
INSERT INTO ezisbn_group VALUES('Turkey',9944,97);
INSERT INTO ezisbn_group VALUES('Dominican Republic',9945,98);
INSERT INTO ezisbn_group VALUES('Korea, P.D.R.',9946,99);
INSERT INTO ezisbn_group VALUES('Algeria',9947,100);
INSERT INTO ezisbn_group VALUES('United Arab Emirates',9948,101);
INSERT INTO ezisbn_group VALUES('Estonia',9949,102);
INSERT INTO ezisbn_group VALUES('Palestine',9950,103);
INSERT INTO ezisbn_group VALUES('Kosova',9951,104);
INSERT INTO ezisbn_group VALUES('Azerbaijan',9952,105);
INSERT INTO ezisbn_group VALUES('Lebanon',9953,106);
INSERT INTO ezisbn_group VALUES('Morocco',9954,107);
INSERT INTO ezisbn_group VALUES('Lithuania',9955,108);
INSERT INTO ezisbn_group VALUES('Cameroon',9956,109);
INSERT INTO ezisbn_group VALUES('Jordan',9957,110);
INSERT INTO ezisbn_group VALUES('Bosnia and Herzegovina',9958,111);
INSERT INTO ezisbn_group VALUES('Libya',9959,112);
INSERT INTO ezisbn_group VALUES('Saudi Arabia',9960,113);
INSERT INTO ezisbn_group VALUES('Algeria',9961,114);
INSERT INTO ezisbn_group VALUES('Panama',9962,115);
INSERT INTO ezisbn_group VALUES('Cyprus',9963,116);
INSERT INTO ezisbn_group VALUES('Ghana',9964,117);
INSERT INTO ezisbn_group VALUES('Kazakhstan',9965,118);
INSERT INTO ezisbn_group VALUES('Kenya',9966,119);
INSERT INTO ezisbn_group VALUES('Kyrgyz Republic',9967,120);
INSERT INTO ezisbn_group VALUES('Costa Rica',9968,121);
INSERT INTO ezisbn_group VALUES('Uganda',9970,122);
INSERT INTO ezisbn_group VALUES('Singapore',9971,123);
INSERT INTO ezisbn_group VALUES('Peru',9972,124);
INSERT INTO ezisbn_group VALUES('Tunisia',9973,125);
INSERT INTO ezisbn_group VALUES('Uruguay',9974,126);
INSERT INTO ezisbn_group VALUES('Moldova',9975,127);
INSERT INTO ezisbn_group VALUES('Tanzania',9976,128);
INSERT INTO ezisbn_group VALUES('Costa Rica',9977,129);
INSERT INTO ezisbn_group VALUES('Ecuador',9978,130);
INSERT INTO ezisbn_group VALUES('Iceland',9979,131);
INSERT INTO ezisbn_group VALUES('Papua New Guinea',9980,132);
INSERT INTO ezisbn_group VALUES('Morocco',9981,133);
INSERT INTO ezisbn_group VALUES('Zambia',9982,134);
INSERT INTO ezisbn_group VALUES('Gambia',9983,135);
INSERT INTO ezisbn_group VALUES('Latvia',9984,136);
INSERT INTO ezisbn_group VALUES('Estonia',9985,137);
INSERT INTO ezisbn_group VALUES('Lithuania',9986,138);
INSERT INTO ezisbn_group VALUES('Tanzania',9987,139);
INSERT INTO ezisbn_group VALUES('Ghana',9988,140);
INSERT INTO ezisbn_group VALUES('Macedonia',9989,141);
INSERT INTO ezisbn_group VALUES('Bahrain',99901,142);
INSERT INTO ezisbn_group VALUES('Gabon',99902,143);
INSERT INTO ezisbn_group VALUES('Mauritius',99903,144);
INSERT INTO ezisbn_group VALUES('Netherlands Antilles and Aruba',99904,145);
INSERT INTO ezisbn_group VALUES('Bolivia',99905,146);
INSERT INTO ezisbn_group VALUES('Kuwait',99906,147);
INSERT INTO ezisbn_group VALUES('Malawi',99908,148);
INSERT INTO ezisbn_group VALUES('Malta',99909,149);
INSERT INTO ezisbn_group VALUES('Sierra Leone',99910,150);
INSERT INTO ezisbn_group VALUES('Lesotho',99911,151);
INSERT INTO ezisbn_group VALUES('Botswana',99912,152);
INSERT INTO ezisbn_group VALUES('Andorra',99913,153);
INSERT INTO ezisbn_group VALUES('Suriname',99914,154);
INSERT INTO ezisbn_group VALUES('Maldives',99915,155);
INSERT INTO ezisbn_group VALUES('Namibia',99916,156);
INSERT INTO ezisbn_group VALUES('Brunei Darussalam',99917,157);
INSERT INTO ezisbn_group VALUES('Faroe Islands',99918,158);
INSERT INTO ezisbn_group VALUES('Benin',99919,159);
INSERT INTO ezisbn_group VALUES('Andorra',99920,160);
INSERT INTO ezisbn_group VALUES('Qatar',99921,161);
INSERT INTO ezisbn_group VALUES('Guatemala',99922,162);
INSERT INTO ezisbn_group VALUES('El Salvador',99923,163);
INSERT INTO ezisbn_group VALUES('Nicaragua',99924,164);
INSERT INTO ezisbn_group VALUES('Paraguay',99925,165);
INSERT INTO ezisbn_group VALUES('Honduras',99926,166);
INSERT INTO ezisbn_group VALUES('Albania',99927,167);
INSERT INTO ezisbn_group VALUES('Georgia',99928,168);
INSERT INTO ezisbn_group VALUES('Mongolia',99929,169);
INSERT INTO ezisbn_group VALUES('Armenia',99930,170);
INSERT INTO ezisbn_group VALUES('Seychelles',99931,171);
INSERT INTO ezisbn_group VALUES('Malta',99932,172);
INSERT INTO ezisbn_group VALUES('Nepal',99933,173);
INSERT INTO ezisbn_group VALUES('Dominican Republic',99934,174);
INSERT INTO ezisbn_group VALUES('Haiti',99935,175);
INSERT INTO ezisbn_group VALUES('Bhutan',99936,176);
INSERT INTO ezisbn_group VALUES('Macau',99937,177);
INSERT INTO ezisbn_group VALUES('Srpska, Republic of',99938,178);
INSERT INTO ezisbn_group VALUES('Guatemala',99939,179);
INSERT INTO ezisbn_group VALUES('Georgia',99940,180);
INSERT INTO ezisbn_group VALUES('Armenia',99941,181);
INSERT INTO ezisbn_group VALUES('Sudan',99942,182);
INSERT INTO ezisbn_group VALUES('Albania',99943,183);
INSERT INTO ezisbn_group VALUES('Ethiopia',99944,184);
INSERT INTO ezisbn_group VALUES('Namibia',99945,185);
INSERT INTO ezisbn_group VALUES('Nepal',99946,186);
INSERT INTO ezisbn_group VALUES('Tajikistan',99947,187);
INSERT INTO ezisbn_group VALUES('Eritrea',99948,188);
INSERT INTO ezisbn_group VALUES('Mauritius',99949,189);
INSERT INTO ezisbn_group VALUES('Cambodia',99950,190);
INSERT INTO ezisbn_group VALUES('Congo',99951,191);
INSERT INTO ezisbn_group VALUES('Mali',99952,192);
INSERT INTO ezisbn_group VALUES('Paraguay',99953,193);
INSERT INTO ezisbn_group VALUES('Bolivia',99954,194);
INSERT INTO ezisbn_group VALUES('Srpska, Republic of',99955,195);
INSERT INTO ezisbn_group VALUES('Albania',99956,196);
INSERT INTO ezisbn_group VALUES('Malta',99957,197);
INSERT INTO ezisbn_group VALUES('Bahrain',99958,198);
INSERT INTO ezisbn_group VALUES('Luxembourg',99959,199);
INSERT INTO ezisbn_group VALUES('Malawi',99960,200);
INSERT INTO ezisbn_group VALUES('El Salvador',99961,201);
INSERT INTO ezisbn_group VALUES('Mongolia',99962,202);
INSERT INTO ezisbn_group VALUES('Cambodia',99963,203);
INSERT INTO ezisbn_group VALUES('Nicaragua',99964,204);
INSERT INTO ezisbn_group VALUES('Macau',99965,205);
INSERT INTO ezisbn_group VALUES('Kuwait',99966,206);
INSERT INTO ezisbn_group VALUES('Paraguay',99967,207);
INSERT INTO ezisbn_group VALUES('Botswana',99968,208);
INSERT INTO ezisbn_group VALUES('France',10,209);
CREATE TABLE `ezisbn_group_range` (
  `from_number` integer NOT NULL DEFAULT '0'
,  `group_from` varchar(32) NOT NULL DEFAULT ''
,  `group_length` integer NOT NULL DEFAULT '0'
,  `group_to` varchar(32) NOT NULL DEFAULT ''
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `to_number` integer NOT NULL DEFAULT '0'
);
INSERT INTO ezisbn_group_range VALUES(0,'0',1,'5',1,59999);
INSERT INTO ezisbn_group_range VALUES(60000,'600',3,'649',2,64999);
INSERT INTO ezisbn_group_range VALUES(70000,'7',1,'7',3,79999);
INSERT INTO ezisbn_group_range VALUES(80000,'80',2,'94',4,94999);
INSERT INTO ezisbn_group_range VALUES(95000,'950',3,'989',5,98999);
INSERT INTO ezisbn_group_range VALUES(99000,'9900',4,'9989',6,99899);
INSERT INTO ezisbn_group_range VALUES(99900,'99900',5,'99999',7,99999);
INSERT INTO ezisbn_group_range VALUES(10000,'10',2,'10',8,10999);
CREATE TABLE `ezisbn_registrant_range` (
  `from_number` integer NOT NULL DEFAULT '0'
,  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `isbn_group_id` integer NOT NULL DEFAULT '0'
,  `registrant_from` varchar(32) NOT NULL DEFAULT ''
,  `registrant_length` integer NOT NULL DEFAULT '0'
,  `registrant_to` varchar(32) NOT NULL DEFAULT ''
,  `to_number` integer NOT NULL DEFAULT '0'
);
INSERT INTO ezisbn_registrant_range VALUES(0,1,1,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,2,1,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,3,1,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,4,1,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,5,1,'900000',6,'949999',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,6,1,'9500000',7,'9999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,7,2,'00',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,8,2,'100',3,'399',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,9,2,'4000',4,'5499',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,10,2,'55000',5,'86979',86979);
INSERT INTO ezisbn_registrant_range VALUES(86980,11,2,'869800',6,'998999',99899);
INSERT INTO ezisbn_registrant_range VALUES(99900,12,2,'9990000',7,'9999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,13,3,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,14,3,'200',3,'349',34999);
INSERT INTO ezisbn_registrant_range VALUES(35000,15,3,'35000',5,'39999',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,16,3,'400',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,17,3,'7000',4,'8399',83999);
INSERT INTO ezisbn_registrant_range VALUES(84000,18,3,'84000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,19,3,'900000',6,'949999',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,20,3,'9500000',7,'9999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,21,4,'00',2,'02',2999);
INSERT INTO ezisbn_registrant_range VALUES(3000,22,4,'030',3,'033',3399);
INSERT INTO ezisbn_registrant_range VALUES(3400,23,4,'0340',4,'0369',3699);
INSERT INTO ezisbn_registrant_range VALUES(3700,24,4,'03700',5,'03999',3999);
INSERT INTO ezisbn_registrant_range VALUES(4000,25,4,'04',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,26,4,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,27,4,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,28,4,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,29,4,'900000',6,'949999',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,30,4,'9500000',7,'9539999',95399);
INSERT INTO ezisbn_registrant_range VALUES(95400,31,4,'95400',5,'96999',96999);
INSERT INTO ezisbn_registrant_range VALUES(97000,32,4,'9700000',7,'9899999',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,33,4,'99000',5,'99499',99499);
INSERT INTO ezisbn_registrant_range VALUES(99500,34,4,'99500',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,35,5,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,36,5,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,37,5,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,38,5,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,39,5,'900000',6,'949999',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,40,5,'9500000',7,'9999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,41,6,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,42,6,'200',3,'420',42099);
INSERT INTO ezisbn_registrant_range VALUES(42100,43,6,'4210',4,'4299',42999);
INSERT INTO ezisbn_registrant_range VALUES(43000,44,6,'430',3,'430',43099);
INSERT INTO ezisbn_registrant_range VALUES(43100,45,6,'4310',4,'4399',43999);
INSERT INTO ezisbn_registrant_range VALUES(44000,46,6,'440',3,'440',44099);
INSERT INTO ezisbn_registrant_range VALUES(44100,47,6,'4410',4,'4499',44999);
INSERT INTO ezisbn_registrant_range VALUES(45000,48,6,'450',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,49,6,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,50,6,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,51,6,'900000',6,'909999',90999);
INSERT INTO ezisbn_registrant_range VALUES(91000,52,6,'91000',5,'91999',91999);
INSERT INTO ezisbn_registrant_range VALUES(92000,53,6,'9200',4,'9299',92999);
INSERT INTO ezisbn_registrant_range VALUES(93000,54,6,'93000',5,'94999',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,55,6,'9500000',7,'9500999',95009);
INSERT INTO ezisbn_registrant_range VALUES(95010,56,6,'9501',4,'9799',97999);
INSERT INTO ezisbn_registrant_range VALUES(98000,57,6,'98000',5,'98999',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,58,6,'9900000',7,'9909999',99099);
INSERT INTO ezisbn_registrant_range VALUES(99100,59,6,'9910',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,60,7,'00',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,61,7,'100',3,'499',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,62,7,'5000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,63,7,'90000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,64,8,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,65,8,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,66,8,'7000',4,'7999',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,67,8,'80000',5,'84999',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,68,8,'85',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,69,9,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,70,9,'200',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,71,9,'8000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,72,9,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,73,10,'00',2,'04',4999);
INSERT INTO ezisbn_registrant_range VALUES(5000,74,10,'05',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,75,10,'500',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,76,10,'8000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,77,10,'90000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,78,11,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,79,11,'50',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,80,11,'900',3,'979',97999);
INSERT INTO ezisbn_registrant_range VALUES(98000,81,11,'9800',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(1000,82,12,'01',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,83,12,'100',3,'399',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,84,12,'4000',4,'5999',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,85,12,'60000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,86,12,'90',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,87,13,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,88,13,'10',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,89,13,'500',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,90,13,'8000',4,'9199',91999);
INSERT INTO ezisbn_registrant_range VALUES(92000,91,13,'92000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,92,14,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,93,14,'400',3,'749',74999);
INSERT INTO ezisbn_registrant_range VALUES(75000,94,14,'7500',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,95,14,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,96,15,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,97,15,'10',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,98,15,'200',3,'449',44999);
INSERT INTO ezisbn_registrant_range VALUES(45000,99,15,'4500',4,'6499',64999);
INSERT INTO ezisbn_registrant_range VALUES(65000,100,15,'65000',5,'69999',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,101,15,'7',1,'9',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,102,16,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,103,16,'400',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,104,16,'8000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,105,16,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,106,18,'00',2,'29',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,107,18,'300',3,'399',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,108,18,'4000',4,'4499',44999);
INSERT INTO ezisbn_registrant_range VALUES(45000,109,18,'45000',5,'49999',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,110,18,'50',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,111,19,'0',1,'9',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,112,20,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,113,20,'400',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,114,20,'8000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,115,20,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,116,21,'00',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,117,21,'100',3,'499',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,118,21,'5000',4,'7999',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,119,21,'80000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(0,120,22,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,121,22,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,122,22,'7000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,123,22,'90000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,124,23,'00',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,125,23,'500',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,126,23,'7000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,127,23,'90000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,128,24,'00',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,129,24,'100',3,'499',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,130,24,'5000',4,'7999',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,131,24,'80000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,132,24,'900000',6,'999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,133,25,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,134,25,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,135,25,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,136,25,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,137,25,'900000',6,'999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,138,26,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,139,26,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,140,26,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,141,26,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,142,26,'900000',6,'999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,143,27,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,144,27,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,145,27,'7000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,146,27,'90000',5,'98999',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,147,27,'990000',6,'999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,148,28,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,149,28,'200',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,150,28,'60000',5,'69999',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,151,28,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,152,28,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,153,28,'900000',6,'999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,154,29,'00',2,'14',14999);
INSERT INTO ezisbn_registrant_range VALUES(15000,155,29,'15000',5,'19999',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,156,29,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,157,29,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,158,29,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,159,29,'9000',4,'9199',91999);
INSERT INTO ezisbn_registrant_range VALUES(92000,160,29,'920000',6,'923999',92399);
INSERT INTO ezisbn_registrant_range VALUES(92400,161,29,'92400',5,'92999',92999);
INSERT INTO ezisbn_registrant_range VALUES(93000,162,29,'930000',6,'949999',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,163,29,'95000',5,'96999',96999);
INSERT INTO ezisbn_registrant_range VALUES(97000,164,29,'9700',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,165,30,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,166,30,'200',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,167,30,'60000',5,'69999',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,168,30,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,169,30,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,170,30,'900000',6,'979999',97999);
INSERT INTO ezisbn_registrant_range VALUES(98000,171,30,'98000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,172,31,'00',2,'29',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,173,31,'300',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,174,31,'6000',4,'7999',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,175,31,'80000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,176,31,'900000',6,'999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,177,32,'00',2,'29',29999);
INSERT INTO ezisbn_registrant_range VALUES(40000,178,32,'400',3,'649',64999);
INSERT INTO ezisbn_registrant_range VALUES(70000,179,32,'7000',4,'7999',79999);
INSERT INTO ezisbn_registrant_range VALUES(85000,180,32,'85000',5,'94999',94999);
INSERT INTO ezisbn_registrant_range VALUES(97000,181,32,'970000',6,'999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,182,33,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,183,33,'200',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,184,33,'6000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,185,33,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,186,33,'900000',6,'949999',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,187,33,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,188,34,'00',2,'24',24999);
INSERT INTO ezisbn_registrant_range VALUES(25000,189,34,'250',3,'549',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,190,34,'5500',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,191,34,'85000',5,'94999',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,192,34,'950000',6,'969999',96999);
INSERT INTO ezisbn_registrant_range VALUES(97000,193,34,'97000',5,'98999',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,194,34,'990',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,195,35,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,196,35,'200',3,'499',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,197,35,'5000',4,'6999',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,198,35,'70000',5,'79999',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,199,35,'800000',6,'849999',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,200,35,'8500',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,201,35,'90',2,'90',90999);
INSERT INTO ezisbn_registrant_range VALUES(91000,202,35,'910000',6,'939999',93999);
INSERT INTO ezisbn_registrant_range VALUES(94000,203,35,'94',2,'94',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,204,35,'950000',6,'999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,205,36,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,206,36,'20',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,207,36,'500',3,'649',64999);
INSERT INTO ezisbn_registrant_range VALUES(70000,208,36,'7000',4,'7999',79999);
INSERT INTO ezisbn_registrant_range VALUES(85000,209,36,'85000',5,'94999',94999);
INSERT INTO ezisbn_registrant_range VALUES(97000,210,36,'970000',6,'999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,211,37,'0',1,'5',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,212,37,'60',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,213,37,'800',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,214,37,'9000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,215,37,'95000',5,'98999',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,216,37,'990000',6,'999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,217,38,'00',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,218,38,'100',3,'499',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,219,38,'5000',4,'7999',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,220,38,'80000',5,'94999',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,221,38,'950000',6,'999999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,222,39,'000',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,223,39,'6000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,224,39,'90000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,225,40,'00',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,226,40,'500',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,227,40,'9000',4,'9899',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,228,40,'99000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,229,41,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,230,41,'20',2,'54',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,231,41,'550',3,'889',88999);
INSERT INTO ezisbn_registrant_range VALUES(89000,232,41,'8900',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,233,41,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,234,42,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,235,42,'200',3,'499',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,236,42,'5000',4,'5999',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,237,42,'60',2,'65',65999);
INSERT INTO ezisbn_registrant_range VALUES(66000,238,42,'6600',4,'6699',66999);
INSERT INTO ezisbn_registrant_range VALUES(67000,239,42,'67000',5,'69999',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,240,42,'7000',4,'7999',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,241,42,'80',2,'94',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,242,42,'9500',4,'9899',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,243,42,'99000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,244,43,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,245,43,'10',2,'14',14999);
INSERT INTO ezisbn_registrant_range VALUES(15000,246,43,'150',3,'549',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,247,43,'55000',5,'59999',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,248,43,'6000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,249,43,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,250,44,'00',2,'28',28999);
INSERT INTO ezisbn_registrant_range VALUES(29000,251,44,'2900',4,'2999',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,252,44,'300',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,253,44,'8000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,254,44,'90000',5,'92999',92999);
INSERT INTO ezisbn_registrant_range VALUES(93000,255,44,'9300',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,256,45,'0000',4,'1999',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,257,45,'20',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,258,45,'50000',5,'54999',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,259,45,'550',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,260,45,'8000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,261,45,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,262,46,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,263,46,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,264,46,'7000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,265,47,'00',2,'02',2999);
INSERT INTO ezisbn_registrant_range VALUES(3000,266,47,'0300',4,'0499',4999);
INSERT INTO ezisbn_registrant_range VALUES(5000,267,47,'05',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,268,47,'2000',4,'2099',20999);
INSERT INTO ezisbn_registrant_range VALUES(21000,269,47,'21',2,'27',27999);
INSERT INTO ezisbn_registrant_range VALUES(28000,270,47,'28000',5,'30999',30999);
INSERT INTO ezisbn_registrant_range VALUES(31000,271,47,'31',2,'43',43999);
INSERT INTO ezisbn_registrant_range VALUES(44000,272,47,'440',3,'819',81999);
INSERT INTO ezisbn_registrant_range VALUES(82000,273,47,'8200',4,'9699',96999);
INSERT INTO ezisbn_registrant_range VALUES(97000,274,47,'97000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,275,48,'00',2,'56',56999);
INSERT INTO ezisbn_registrant_range VALUES(57000,276,48,'57000',5,'59999',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,277,48,'600',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,278,48,'8000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,279,48,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,280,49,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,281,49,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,282,49,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,283,49,'85000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,284,50,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,285,50,'200',3,'659',65999);
INSERT INTO ezisbn_registrant_range VALUES(66000,286,50,'6600',4,'6899',68999);
INSERT INTO ezisbn_registrant_range VALUES(69000,287,50,'690',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,288,50,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,289,50,'85000',5,'92999',92999);
INSERT INTO ezisbn_registrant_range VALUES(93000,290,50,'93',2,'93',93999);
INSERT INTO ezisbn_registrant_range VALUES(94000,291,50,'9400',4,'9799',97999);
INSERT INTO ezisbn_registrant_range VALUES(98000,292,50,'98000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,293,51,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,294,51,'200',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,295,51,'6000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,296,51,'90000',5,'94999',94999);
INSERT INTO ezisbn_registrant_range VALUES(0,297,52,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,298,52,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,299,52,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,300,52,'85000',5,'86999',86999);
INSERT INTO ezisbn_registrant_range VALUES(87000,301,52,'8700',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,302,52,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,303,53,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,304,53,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,305,53,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,306,53,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,307,53,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,308,54,'00',2,'14',14999);
INSERT INTO ezisbn_registrant_range VALUES(15000,309,54,'150',3,'249',24999);
INSERT INTO ezisbn_registrant_range VALUES(25000,310,54,'2500',4,'2999',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,311,54,'300',3,'549',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,312,54,'5500',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,313,54,'90000',5,'96999',96999);
INSERT INTO ezisbn_registrant_range VALUES(97000,314,54,'970',3,'989',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,315,54,'9900',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,316,55,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,317,55,'200',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(70000,318,55,'7000',4,'7999',79999);
INSERT INTO ezisbn_registrant_range VALUES(90000,319,55,'90000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,320,56,'00',2,'14',14999);
INSERT INTO ezisbn_registrant_range VALUES(15000,321,56,'1500',4,'1699',16999);
INSERT INTO ezisbn_registrant_range VALUES(17000,322,56,'170',3,'199',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,323,56,'2000',4,'2999',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,324,56,'300',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,325,56,'7000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,326,56,'90000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,327,57,'00',2,'00',999);
INSERT INTO ezisbn_registrant_range VALUES(1000,328,57,'0100',4,'0999',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,329,57,'10000',5,'19999',19999);
INSERT INTO ezisbn_registrant_range VALUES(30000,330,57,'300',3,'499',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,331,57,'5000',4,'5999',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,332,57,'60',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,333,57,'900',3,'989',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,334,57,'9900',4,'9989',99899);
INSERT INTO ezisbn_registrant_range VALUES(99900,335,57,'99900',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(1000,336,58,'01',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,337,58,'400',3,'499',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,338,58,'5000',4,'7999',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,339,58,'800',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,340,58,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,341,59,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,342,59,'20',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,343,59,'400',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,344,59,'8000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(1000,345,60,'01',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,346,60,'600',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,347,60,'9000',4,'9099',90999);
INSERT INTO ezisbn_registrant_range VALUES(91000,348,60,'91000',5,'96999',96999);
INSERT INTO ezisbn_registrant_range VALUES(97000,349,60,'9700',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,350,61,'000',3,'015',1599);
INSERT INTO ezisbn_registrant_range VALUES(1600,351,61,'0160',4,'0199',1999);
INSERT INTO ezisbn_registrant_range VALUES(2000,352,61,'02',2,'02',2999);
INSERT INTO ezisbn_registrant_range VALUES(3000,353,61,'0300',4,'0599',5999);
INSERT INTO ezisbn_registrant_range VALUES(6000,354,61,'06',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,355,61,'10',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,356,61,'500',3,'849',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,357,61,'8500',4,'9099',90999);
INSERT INTO ezisbn_registrant_range VALUES(91000,358,61,'91000',5,'98999',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,359,61,'9900',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,360,62,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,361,62,'20',2,'54',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,362,62,'550',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,363,62,'8000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,364,62,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,365,63,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,366,63,'100',3,'169',16999);
INSERT INTO ezisbn_registrant_range VALUES(17000,367,63,'1700',4,'1999',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,368,63,'20',2,'54',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,369,63,'550',3,'759',75999);
INSERT INTO ezisbn_registrant_range VALUES(76000,370,63,'7600',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,371,63,'85000',5,'88999',88999);
INSERT INTO ezisbn_registrant_range VALUES(89000,372,63,'8900',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,373,63,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,374,64,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,375,64,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,376,64,'7000',4,'8499',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,377,64,'85000',5,'89999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,378,64,'90000',5,'94999',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,379,64,'9500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,380,65,'00000',5,'01999',1999);
INSERT INTO ezisbn_registrant_range VALUES(2000,381,65,'02',2,'24',24999);
INSERT INTO ezisbn_registrant_range VALUES(25000,382,65,'250',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,383,65,'6000',4,'9199',91999);
INSERT INTO ezisbn_registrant_range VALUES(92000,384,65,'92000',5,'98999',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,385,65,'990',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,386,66,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,387,66,'40',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,388,66,'600',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,389,66,'8000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,390,66,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,391,67,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,392,67,'200',3,'499',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,393,67,'5000',4,'6999',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,394,67,'700',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,395,68,'000',3,'199',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,396,68,'2000',4,'2999',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,397,68,'30000',5,'79999',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,398,68,'8000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,399,68,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,400,69,'000',3,'099',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,401,69,'1000',4,'1499',14999);
INSERT INTO ezisbn_registrant_range VALUES(15000,402,69,'15000',5,'19999',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,403,69,'20',2,'29',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,404,69,'3000',4,'3999',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,405,69,'400',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,406,69,'8000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,407,69,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,408,70,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,409,70,'200',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,410,70,'6000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,411,71,'00',2,'11',11999);
INSERT INTO ezisbn_registrant_range VALUES(12000,412,71,'1200',4,'1999',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,413,71,'200',3,'289',28999);
INSERT INTO ezisbn_registrant_range VALUES(29000,414,71,'2900',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,415,72,'00',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,416,72,'100',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,417,72,'70',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,418,72,'9000',4,'9799',97999);
INSERT INTO ezisbn_registrant_range VALUES(98000,419,72,'98000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,420,73,'00',2,'01',1999);
INSERT INTO ezisbn_registrant_range VALUES(2000,421,73,'020',3,'199',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,422,73,'2000',4,'3999',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,423,73,'40000',5,'44999',44999);
INSERT INTO ezisbn_registrant_range VALUES(45000,424,73,'45',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,425,73,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,426,73,'800',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,427,73,'9000',4,'9899',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,428,73,'99000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,429,74,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,430,74,'400',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,431,74,'8000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,432,74,'90000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,433,75,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,434,75,'400',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,435,75,'6000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,436,75,'90000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,437,76,'00',2,'11',11999);
INSERT INTO ezisbn_registrant_range VALUES(12000,438,76,'120',3,'559',55999);
INSERT INTO ezisbn_registrant_range VALUES(56000,439,76,'5600',4,'7999',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,440,76,'80000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,441,77,'00',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,442,77,'1000',4,'1999',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,443,77,'20000',5,'29999',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,444,77,'30',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,445,77,'500',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,446,77,'9000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,447,77,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,448,78,'00',2,'14',14999);
INSERT INTO ezisbn_registrant_range VALUES(15000,449,78,'15000',5,'16999',16999);
INSERT INTO ezisbn_registrant_range VALUES(17000,450,78,'17000',5,'19999',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,451,78,'200',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,452,78,'8000',4,'9699',96999);
INSERT INTO ezisbn_registrant_range VALUES(97000,453,78,'97000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,454,79,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,455,79,'20',2,'54',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,456,79,'550',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,457,79,'8000',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,458,79,'95000',5,'99999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,459,80,'00',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,460,80,'100',3,'399',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,461,80,'4000',4,'4999',49999);
INSERT INTO ezisbn_registrant_range VALUES(0,462,81,'00',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,463,81,'100',3,'399',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,464,81,'4000',4,'4999',49999);
INSERT INTO ezisbn_registrant_range VALUES(0,465,82,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,466,82,'40',2,'54',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,467,82,'550',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,468,82,'8000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,469,83,'00',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,470,83,'500',3,'939',93999);
INSERT INTO ezisbn_registrant_range VALUES(94000,471,83,'9400',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,472,84,'00',2,'29',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,473,84,'300',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,474,84,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,475,85,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,476,85,'400',3,'849',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,477,85,'8500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,478,86,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,479,86,'10',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,480,86,'400',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,481,86,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,482,87,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,483,87,'10',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,484,87,'500',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,485,87,'8000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,486,88,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,487,88,'10',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,488,88,'400',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,489,88,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,490,89,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,491,89,'20',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,492,89,'400',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,493,89,'8000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,494,90,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,495,90,'30',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,496,90,'500',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,497,90,'8000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,498,91,'00',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,499,91,'800',3,'949',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,500,91,'9500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,501,92,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,502,92,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,503,92,'800',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,504,92,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,505,93,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,506,93,'20',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,507,93,'500',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,508,93,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,509,94,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,510,94,'10',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,511,94,'400',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,512,94,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,513,95,'00',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,514,95,'900',3,'984',98499);
INSERT INTO ezisbn_registrant_range VALUES(98500,515,95,'9850',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,516,96,'00',2,'29',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,517,96,'300',3,'399',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,518,96,'4000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,519,97,'0000',4,'0999',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,520,97,'100',3,'499',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,521,97,'5000',4,'5999',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,522,97,'60',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,523,97,'700',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,524,97,'80',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,525,97,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,526,98,'00',2,'00',999);
INSERT INTO ezisbn_registrant_range VALUES(1000,527,98,'010',3,'079',7999);
INSERT INTO ezisbn_registrant_range VALUES(8000,528,98,'08',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,529,98,'400',3,'569',56999);
INSERT INTO ezisbn_registrant_range VALUES(57000,530,98,'57',2,'57',57999);
INSERT INTO ezisbn_registrant_range VALUES(58000,531,98,'580',3,'849',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,532,98,'8500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,533,99,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,534,99,'20',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,535,99,'400',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,536,99,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,537,100,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,538,100,'20',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,539,100,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,540,101,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,541,101,'400',3,'849',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,542,101,'8500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,543,102,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,544,102,'10',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,545,102,'400',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,546,102,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,547,103,'00',2,'29',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,548,103,'300',3,'849',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,549,103,'8500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,550,104,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,551,104,'400',3,'849',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,552,104,'8500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,553,105,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,554,105,'20',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,555,105,'400',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,556,105,'8000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,557,106,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,558,106,'10',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,559,106,'400',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,560,106,'60',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,561,106,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,562,107,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,563,107,'20',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,564,107,'400',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,565,107,'8000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,566,108,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,567,108,'400',3,'929',92999);
INSERT INTO ezisbn_registrant_range VALUES(93000,568,108,'9300',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,569,109,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,570,109,'10',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,571,109,'400',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,572,109,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,573,110,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,574,110,'400',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,575,110,'70',2,'84',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,576,110,'8500',4,'8799',87999);
INSERT INTO ezisbn_registrant_range VALUES(88000,577,110,'88',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,578,111,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,579,111,'10',2,'18',18999);
INSERT INTO ezisbn_registrant_range VALUES(19000,580,111,'1900',4,'1999',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,581,111,'20',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,582,111,'500',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,583,111,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,584,112,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,585,112,'20',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,586,112,'800',3,'949',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,587,112,'9500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,588,113,'00',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,589,113,'600',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,590,113,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,591,114,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,592,114,'30',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,593,114,'700',3,'949',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,594,114,'9500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,595,115,'00',2,'54',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,596,115,'5500',4,'5599',55999);
INSERT INTO ezisbn_registrant_range VALUES(56000,597,115,'56',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,598,115,'600',3,'849',84999);
INSERT INTO ezisbn_registrant_range VALUES(85000,599,115,'8500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,600,116,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,601,116,'30',2,'54',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,602,116,'550',3,'734',73499);
INSERT INTO ezisbn_registrant_range VALUES(73500,603,116,'7350',4,'7499',74999);
INSERT INTO ezisbn_registrant_range VALUES(75000,604,116,'7500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,605,117,'0',1,'6',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,606,117,'70',2,'94',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,607,117,'950',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,608,118,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,609,118,'400',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,610,118,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,611,119,'000',3,'149',14999);
INSERT INTO ezisbn_registrant_range VALUES(15000,612,119,'1500',4,'1999',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,613,119,'20',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,614,119,'7000',4,'7499',74999);
INSERT INTO ezisbn_registrant_range VALUES(75000,615,119,'750',3,'959',95999);
INSERT INTO ezisbn_registrant_range VALUES(96000,616,119,'9600',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,617,120,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,618,120,'400',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,619,120,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,620,121,'00',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,621,121,'500',3,'939',93999);
INSERT INTO ezisbn_registrant_range VALUES(94000,622,121,'9400',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,623,122,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,624,122,'400',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,625,122,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,626,123,'0',1,'5',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,627,123,'60',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,628,123,'900',3,'989',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,629,123,'9900',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,630,124,'00',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,631,124,'1',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,632,124,'200',3,'249',24999);
INSERT INTO ezisbn_registrant_range VALUES(25000,633,124,'2500',4,'2999',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,634,124,'30',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,635,124,'600',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,636,124,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,637,125,'00',2,'05',5999);
INSERT INTO ezisbn_registrant_range VALUES(6000,638,125,'060',3,'089',8999);
INSERT INTO ezisbn_registrant_range VALUES(9000,639,125,'0900',4,'0999',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,640,125,'10',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,641,125,'700',3,'969',96999);
INSERT INTO ezisbn_registrant_range VALUES(97000,642,125,'9700',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,643,126,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,644,126,'30',2,'54',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,645,126,'550',3,'749',74999);
INSERT INTO ezisbn_registrant_range VALUES(75000,646,126,'7500',4,'9499',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,647,126,'95',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,648,127,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,649,127,'100',3,'399',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,650,127,'4000',4,'4499',44999);
INSERT INTO ezisbn_registrant_range VALUES(45000,651,127,'45',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,652,127,'900',3,'949',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,653,127,'9500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,654,128,'0',1,'5',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,655,128,'60',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,656,128,'900',3,'989',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,657,128,'9900',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,658,129,'00',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,659,129,'900',3,'989',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,660,129,'9900',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,661,130,'00',2,'29',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,662,130,'300',3,'399',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,663,130,'40',2,'94',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,664,130,'950',3,'989',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,665,130,'9900',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,666,131,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,667,131,'50',2,'64',64999);
INSERT INTO ezisbn_registrant_range VALUES(65000,668,131,'650',3,'659',65999);
INSERT INTO ezisbn_registrant_range VALUES(66000,669,131,'66',2,'75',75999);
INSERT INTO ezisbn_registrant_range VALUES(76000,670,131,'760',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,671,131,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,672,132,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,673,132,'40',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,674,132,'900',3,'989',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,675,132,'9900',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,676,133,'00',2,'09',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,677,133,'100',3,'159',15999);
INSERT INTO ezisbn_registrant_range VALUES(16000,678,133,'1600',4,'1999',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,679,133,'20',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,680,133,'800',3,'949',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,681,133,'9500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,682,134,'00',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,683,134,'800',3,'989',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,684,134,'9900',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(80000,685,135,'80',2,'94',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,686,135,'950',3,'989',98999);
INSERT INTO ezisbn_registrant_range VALUES(99000,687,135,'9900',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,688,136,'00',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,689,136,'500',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,690,136,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,691,137,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,692,137,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,693,137,'800',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,694,137,'9000',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,695,138,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,696,138,'400',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,697,138,'9000',4,'9399',93999);
INSERT INTO ezisbn_registrant_range VALUES(94000,698,138,'940',3,'969',96999);
INSERT INTO ezisbn_registrant_range VALUES(97000,699,138,'97',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,700,139,'00',2,'39',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,701,139,'400',3,'879',87999);
INSERT INTO ezisbn_registrant_range VALUES(88000,702,139,'8800',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,703,140,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,704,140,'30',2,'54',54999);
INSERT INTO ezisbn_registrant_range VALUES(55000,705,140,'550',3,'749',74999);
INSERT INTO ezisbn_registrant_range VALUES(75000,706,140,'7500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,707,141,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,708,141,'100',3,'199',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,709,141,'2000',4,'2999',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,710,141,'30',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,711,141,'600',3,'949',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,712,141,'9500',4,'9999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,713,142,'00',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,714,142,'500',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,715,142,'80',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,716,144,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,717,144,'20',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,718,144,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,719,145,'0',1,'5',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,720,145,'60',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,721,145,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,722,146,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,723,146,'40',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,724,146,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,725,147,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,726,147,'30',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,727,147,'600',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,728,147,'70',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,729,147,'90',2,'94',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,730,147,'950',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,731,148,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,732,148,'10',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,733,148,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,734,149,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,735,149,'40',2,'94',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,736,149,'950',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,737,150,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,738,150,'30',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,739,150,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,740,151,'00',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,741,151,'600',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,742,152,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,743,152,'400',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,744,152,'60',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,745,152,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,746,153,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,747,153,'30',2,'35',35999);
INSERT INTO ezisbn_registrant_range VALUES(60000,748,153,'600',3,'604',60499);
INSERT INTO ezisbn_registrant_range VALUES(0,749,154,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,750,154,'50',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,751,154,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,752,155,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,753,155,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,754,155,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,755,156,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,756,156,'30',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,757,156,'700',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,758,157,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,759,157,'30',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,760,157,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,761,158,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,762,158,'40',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,763,158,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,764,159,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,765,159,'300',3,'399',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,766,159,'40',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(90000,767,159,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,768,160,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,769,160,'50',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,770,160,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,771,161,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,772,161,'20',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,773,161,'700',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,774,161,'8',1,'8',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,775,161,'90',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,776,162,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,777,162,'40',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,778,162,'700',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,779,163,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,780,163,'20',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,781,163,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,782,164,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,783,164,'20',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,784,164,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,785,165,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,786,165,'40',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,787,165,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,788,166,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,789,166,'10',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,790,166,'600',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,791,167,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,792,167,'30',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,793,167,'600',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,794,168,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,795,168,'10',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,796,168,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,797,169,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,798,169,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,799,169,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,800,170,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,801,170,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,802,170,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,803,171,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,804,171,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,805,171,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,806,172,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,807,172,'10',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,808,172,'600',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,809,172,'7',1,'7',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,810,172,'80',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,811,173,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,812,173,'30',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,813,173,'600',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,814,174,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,815,174,'20',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,816,174,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,817,175,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,818,175,'30',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,819,175,'600',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,820,175,'7',1,'8',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,821,175,'90',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,822,176,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,823,176,'10',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,824,176,'600',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,825,177,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,826,177,'20',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,827,177,'600',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,828,178,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,829,178,'20',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,830,178,'600',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,831,178,'90',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,832,179,'0',1,'5',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,833,179,'60',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,834,179,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,835,180,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,836,180,'10',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,837,180,'700',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,838,181,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,839,181,'30',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,840,181,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,841,182,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,842,182,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,843,182,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,844,183,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,845,183,'30',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,846,183,'600',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,847,184,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,848,184,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,849,184,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,850,185,'0',1,'5',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,851,185,'60',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,852,185,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,853,186,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,854,186,'30',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,855,186,'600',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,856,187,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,857,187,'30',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,858,187,'700',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,859,188,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,860,188,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,861,188,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,862,189,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,863,189,'20',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,864,189,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,865,190,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,866,190,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,867,190,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,868,192,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,869,192,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,870,192,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,871,193,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,872,193,'30',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,873,193,'800',3,'939',93999);
INSERT INTO ezisbn_registrant_range VALUES(94000,874,193,'94',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,875,194,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,876,194,'30',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,877,194,'700',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,878,195,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,879,195,'20',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,880,195,'600',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,881,195,'80',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,882,195,'90',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,883,196,'00',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,884,196,'600',3,'859',85999);
INSERT INTO ezisbn_registrant_range VALUES(86000,885,196,'86',2,'99',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,886,197,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,887,197,'20',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,888,197,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,889,198,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,890,198,'50',2,'94',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,891,198,'950',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,892,199,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,893,199,'30',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,894,199,'600',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,895,200,'0',1,'0',9999);
INSERT INTO ezisbn_registrant_range VALUES(10000,896,200,'10',2,'94',94999);
INSERT INTO ezisbn_registrant_range VALUES(95000,897,200,'950',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,898,201,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,899,201,'40',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,900,201,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,901,202,'0',1,'4',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,902,202,'50',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,903,202,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,904,203,'00',2,'49',49999);
INSERT INTO ezisbn_registrant_range VALUES(50000,905,203,'500',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,906,204,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,907,204,'20',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,908,204,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,909,205,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,910,205,'40',2,'79',79999);
INSERT INTO ezisbn_registrant_range VALUES(80000,911,205,'800',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,912,206,'0',1,'2',29999);
INSERT INTO ezisbn_registrant_range VALUES(30000,913,206,'30',2,'69',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,914,206,'700',3,'799',79999);
INSERT INTO ezisbn_registrant_range VALUES(0,915,207,'0',1,'1',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,916,207,'20',2,'59',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,917,207,'600',3,'899',89999);
INSERT INTO ezisbn_registrant_range VALUES(0,918,208,'0',1,'3',39999);
INSERT INTO ezisbn_registrant_range VALUES(40000,919,208,'400',3,'599',59999);
INSERT INTO ezisbn_registrant_range VALUES(60000,920,208,'60',2,'89',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,921,208,'900',3,'999',99999);
INSERT INTO ezisbn_registrant_range VALUES(0,922,209,'00',2,'19',19999);
INSERT INTO ezisbn_registrant_range VALUES(20000,923,209,'200',3,'699',69999);
INSERT INTO ezisbn_registrant_range VALUES(70000,924,209,'7000',4,'8999',89999);
INSERT INTO ezisbn_registrant_range VALUES(90000,925,209,'90000',5,'97599',97599);
INSERT INTO ezisbn_registrant_range VALUES(97600,926,209,'976000',6,'999999',99999);
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
INSERT INTO eznode_assignment VALUES(8,2,0,4,1,2,5,'','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(42,1,0,5,1,2,5,'','0',9,1,0,0);
INSERT INTO eznode_assignment VALUES(10,2,-1,6,1,2,44,'','0',9,1,0,0);
INSERT INTO eznode_assignment VALUES(4,1,0,7,1,2,1,'','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(12,1,0,8,1,2,5,'','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(13,1,0,9,1,2,5,'','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(41,1,0,11,1,2,1,'','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(11,1,0,12,1,2,5,'','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(45,1,-1,16,1,2,1,'','0',9,1,0,0);
INSERT INTO eznode_assignment VALUES(49,1,0,27,1,2,43,'','0',9,1,0,0);
INSERT INTO eznode_assignment VALUES(50,1,0,28,1,2,43,'','0',9,1,0,0);
INSERT INTO eznode_assignment VALUES(51,1,0,29,1,2,43,'','0',9,1,0,0);
INSERT INTO eznode_assignment VALUES(52,1,0,30,1,2,48,'','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(56,1,0,34,1,2,1,'','0',2,0,0,0);
INSERT INTO eznode_assignment VALUES(14,3,-1,38,1,2,13,'','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(54,2,-1,39,1,2,58,'','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(14,4,-1,45,1,2,13,'e5161a99f733200b9ed4e80f9c16187b','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(57,5,-1,49,1,2,1,'f3e90596361e31d496d4026eb624c983','0',8,1,0,0);
INSERT INTO eznode_assignment VALUES(57,6,-1,51,1,2,1,'f3e90596361e31d496d4026eb624c983','0',8,1,0,0);
INSERT INTO eznode_assignment VALUES(57,7,-1,52,1,2,1,'f3e90596361e31d496d4026eb624c983','0',8,1,0,0);
INSERT INTO eznode_assignment VALUES(57,8,-1,54,1,2,1,'f3e90596361e31d496d4026eb624c983','0',8,1,0,0);
INSERT INTO eznode_assignment VALUES(57,9,-1,55,1,2,1,'f3e90596361e31d496d4026eb624c983','0',8,1,0,0);
INSERT INTO eznode_assignment VALUES(59,1,0,56,1,2,2,'014d5ee6adb7533080214510e9678e6e','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(57,10,-1,58,1,2,1,'f3e90596361e31d496d4026eb624c983','0',8,1,0,0);
INSERT INTO eznode_assignment VALUES(57,11,-1,59,1,2,1,'f3e90596361e31d496d4026eb624c983','0',8,1,0,0);
INSERT INTO eznode_assignment VALUES(57,12,-1,61,1,2,1,'f3e90596361e31d496d4026eb624c983','0',8,1,0,0);
INSERT INTO eznode_assignment VALUES(57,13,-1,62,1,2,1,'f3e90596361e31d496d4026eb624c983','0',8,1,0,0);
INSERT INTO eznode_assignment VALUES(60,1,0,63,1,2,2,'6ce39bca9dab75fc1a557398a129b335','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(61,1,0,64,1,2,2,'c56915e9299057c1ea1aaf326b007803','0',1,1,0,0);
INSERT INTO eznode_assignment VALUES(59,2,-1,65,1,2,2,'014d5ee6adb7533080214510e9678e6e','0',1,1,0,0);
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
INSERT INTO eznotificationevent VALUES(57,1,0,0,'','','','','ezpublish',1,0);
INSERT INTO eznotificationevent VALUES(58,1,0,0,'','','','','ezpublish',2,0);
INSERT INTO eznotificationevent VALUES(14,4,0,0,'','','','','ezpublish',3,0);
INSERT INTO eznotificationevent VALUES(57,2,0,0,'','','','','ezpublish',4,0);
INSERT INTO eznotificationevent VALUES(57,4,0,0,'','','','','ezpublish',5,0);
INSERT INTO eznotificationevent VALUES(57,5,0,0,'','','','','ezpublish',6,0);
INSERT INTO eznotificationevent VALUES(57,6,0,0,'','','','','ezpublish',7,0);
INSERT INTO eznotificationevent VALUES(57,7,0,0,'','','','','ezpublish',8,0);
INSERT INTO eznotificationevent VALUES(57,8,0,0,'','','','','ezpublish',9,0);
INSERT INTO eznotificationevent VALUES(57,9,0,0,'','','','','ezpublish',10,0);
INSERT INTO eznotificationevent VALUES(59,1,0,0,'','','','','ezpublish',11,0);
INSERT INTO eznotificationevent VALUES(57,10,0,0,'','','','','ezpublish',12,0);
INSERT INTO eznotificationevent VALUES(57,11,0,0,'','','','','ezpublish',13,0);
INSERT INTO eznotificationevent VALUES(57,12,0,0,'','','','','ezpublish',14,0);
INSERT INTO eznotificationevent VALUES(57,13,0,0,'','','','','ezpublish',15,0);
INSERT INTO eznotificationevent VALUES(60,1,0,0,'','','','','ezpublish',16,0);
INSERT INTO eznotificationevent VALUES(61,1,0,0,'','','','','ezpublish',17,0);
INSERT INTO eznotificationevent VALUES(59,2,0,0,'','','','','ezpublish',18,0);
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
INSERT INTO ezorder_status VALUES(1,1,'Pending',1);
INSERT INTO ezorder_status VALUES(2,1,'Processing',2);
INSERT INTO ezorder_status VALUES(3,1,'Delivered',3);
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
INSERT INTO ezpackage VALUES(1,1301057838,'plain_site_data','1.0-1');
INSERT INTO ezpackage VALUES(2,1704475232,'ezwt_extension','5.3-0');
INSERT INTO ezpackage VALUES(3,1704475232,'ezstarrating_extension','5.3-0');
INSERT INTO ezpackage VALUES(4,1704475232,'ezgmaplocation_extension','5.3-0');
INSERT INTO ezpackage VALUES(5,1704475232,'ezwebin_extension','5.3-0');
INSERT INTO ezpackage VALUES(7,1704475235,'ezwebin_democontent_clean','5.3-0');
INSERT INTO ezpackage VALUES(8,1704585487,'ezwebin_classes','5.3-0');
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
INSERT INTO ezpolicy VALUES('*',308,'*',0,2);
INSERT INTO ezpolicy VALUES('*',317,'content',0,3);
INSERT INTO ezpolicy VALUES('login',319,'user',0,3);
INSERT INTO ezpolicy VALUES('*',330,'ezoe',0,3);
INSERT INTO ezpolicy VALUES('*',332,'ezoe',0,3);
INSERT INTO ezpolicy VALUES('pdf',336,'content',0,1);
INSERT INTO ezpolicy VALUES('read',337,'content',0,1);
INSERT INTO ezpolicy VALUES('feed',338,'rss',0,1);
INSERT INTO ezpolicy VALUES('login',341,'user',0,1);
CREATE TABLE `ezpolicy_limitation` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `identifier` varchar(255) NOT NULL DEFAULT ''
,  `policy_id` integer DEFAULT NULL
);
INSERT INTO ezpolicy_limitation VALUES(256,'Section',336);
INSERT INTO ezpolicy_limitation VALUES(257,'Section',337);
CREATE TABLE `ezpolicy_limitation_value` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `limitation_id` integer DEFAULT NULL
,  `value` varchar(255) DEFAULT NULL
);
INSERT INTO ezpolicy_limitation_value VALUES(482,256,'1');
INSERT INTO ezpolicy_limitation_value VALUES(483,257,'1');
CREATE TABLE `ezpreferences` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(100) DEFAULT NULL
,  `user_id` integer NOT NULL DEFAULT '0'
,  `value` longtext
);
INSERT INTO ezpreferences VALUES(1,'admin_navigation_content',14,'1');
INSERT INTO ezpreferences VALUES(2,'admin_navigation_roles',14,'1');
INSERT INTO ezpreferences VALUES(3,'admin_navigation_policies',14,'1');
INSERT INTO ezpreferences VALUES(4,'admin_list_limit',14,'2');
INSERT INTO ezpreferences VALUES(5,'admin_treemenu',14,'1');
INSERT INTO ezpreferences VALUES(6,'admin_bookmark_menu',14,'1');
INSERT INTO ezpreferences VALUES(7,'admin_navigation_class_translations',14,'1');
INSERT INTO ezpreferences VALUES(8,'admin_navigation_class_groups',14,'0');
INSERT INTO ezpreferences VALUES(9,'admin_right_menu_show',14,'1');
INSERT INTO ezpreferences VALUES(10,'admin_clearcache_type',14,'All');
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
INSERT INTO ezrole VALUES(1,0,'Anonymous','',0);
INSERT INTO ezrole VALUES(2,0,'Administrator','*',0);
INSERT INTO ezrole VALUES(3,0,'Editor','',0);
CREATE TABLE `ezrss_export` (
  `access_url` varchar(255) DEFAULT NULL
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
INSERT INTO ezsearch_object_word_link VALUES(6,3,4,0.0,4663,'name',0,951,0,0,1033917596,2,930);
INSERT INTO ezsearch_object_word_link VALUES(7,3,4,0.0,4664,'description',0,952,1,930,1033917596,2,951);
INSERT INTO ezsearch_object_word_link VALUES(7,3,4,0.0,4665,'description',0,0,2,951,1033917596,2,952);
INSERT INTO ezsearch_object_word_link VALUES(8,4,10,0.0,4666,'first_name',0,954,0,0,1033920665,2,953);
INSERT INTO ezsearch_object_word_link VALUES(9,4,10,0.0,4667,'last_name',0,953,1,953,1033920665,2,954);
INSERT INTO ezsearch_object_word_link VALUES(12,4,10,0.0,4668,'user_account',0,955,2,954,1033920665,2,953);
INSERT INTO ezsearch_object_word_link VALUES(12,4,10,0.0,4669,'user_account',0,927,3,953,1033920665,2,955);
INSERT INTO ezsearch_object_word_link VALUES(12,4,10,0.0,4670,'user_account',0,0,4,955,1033920665,2,927);
INSERT INTO ezsearch_object_word_link VALUES(6,3,11,0.0,4671,'name',0,957,0,0,1033920746,2,956);
INSERT INTO ezsearch_object_word_link VALUES(6,3,11,0.0,4672,'name',0,0,1,956,1033920746,2,957);
INSERT INTO ezsearch_object_word_link VALUES(6,3,12,0.0,4673,'name',0,930,0,0,1033920775,2,958);
INSERT INTO ezsearch_object_word_link VALUES(6,3,12,0.0,4674,'name',0,0,1,958,1033920775,2,930);
INSERT INTO ezsearch_object_word_link VALUES(6,3,13,0.0,4675,'name',0,0,0,0,1033920794,2,959);
INSERT INTO ezsearch_object_word_link VALUES(4,1,41,0.0,4681,'name',0,0,0,0,1060695457,3,961);
INSERT INTO ezsearch_object_word_link VALUES(6,3,42,0.0,4682,'name',0,930,0,0,1072180330,2,953);
INSERT INTO ezsearch_object_word_link VALUES(6,3,42,0.0,4683,'name',0,954,1,953,1072180330,2,930);
INSERT INTO ezsearch_object_word_link VALUES(7,3,42,0.0,4684,'description',0,952,2,930,1072180330,2,954);
INSERT INTO ezsearch_object_word_link VALUES(7,3,42,0.0,4685,'description',0,816,3,954,1072180330,2,952);
INSERT INTO ezsearch_object_word_link VALUES(7,3,42,0.0,4686,'description',0,814,4,952,1072180330,2,816);
INSERT INTO ezsearch_object_word_link VALUES(7,3,42,0.0,4687,'description',0,953,5,816,1072180330,2,814);
INSERT INTO ezsearch_object_word_link VALUES(7,3,42,0.0,4688,'description',0,954,6,814,1072180330,2,953);
INSERT INTO ezsearch_object_word_link VALUES(7,3,42,0.0,4689,'description',0,0,7,953,1072180330,2,954);
INSERT INTO ezsearch_object_word_link VALUES(4,1,45,0.0,4690,'name',0,0,0,0,1079684190,4,812);
INSERT INTO ezsearch_object_word_link VALUES(4,1,49,0.0,4691,'name',0,0,0,0,1080220197,3,962);
INSERT INTO ezsearch_object_word_link VALUES(4,1,50,0.0,4692,'name',0,0,0,0,1080220220,3,963);
INSERT INTO ezsearch_object_word_link VALUES(4,1,51,0.0,4693,'name',0,0,0,0,1080220233,3,964);
INSERT INTO ezsearch_object_word_link VALUES(159,14,52,0.0,4694,'name',0,965,0,0,1082016591,4,877);
INSERT INTO ezsearch_object_word_link VALUES(159,14,52,0.0,4695,'name',0,966,1,877,1082016591,4,965);
INSERT INTO ezsearch_object_word_link VALUES(159,14,52,0.0,4696,'name',0,0,2,965,1082016591,4,966);
INSERT INTO ezsearch_object_word_link VALUES(176,15,54,0.0,4697,'id',0,0,0,0,1082016652,5,967);
INSERT INTO ezsearch_object_word_link VALUES(4,1,56,0.0,4698,'name',0,0,0,0,1103023132,5,968);
INSERT INTO ezsearch_object_word_link VALUES(8,4,14,0.0,4951,'first_name',0,958,0,0,1033920830,2,958);
INSERT INTO ezsearch_object_word_link VALUES(8,4,14,0.0,4952,'first_name',0,954,1,958,1033920830,2,958);
INSERT INTO ezsearch_object_word_link VALUES(9,4,14,0.0,4953,'last_name',0,954,2,958,1033920830,2,954);
INSERT INTO ezsearch_object_word_link VALUES(9,4,14,0.0,4954,'last_name',0,960,3,954,1033920830,2,954);
INSERT INTO ezsearch_object_word_link VALUES(12,4,14,0.0,4955,'user_account',0,1051,4,954,1033920830,2,960);
INSERT INTO ezsearch_object_word_link VALUES(12,4,14,0.0,4956,'user_account',0,1052,5,960,1033920830,2,1051);
INSERT INTO ezsearch_object_word_link VALUES(12,4,14,0.0,4957,'user_account',0,960,6,1051,1033920830,2,1052);
INSERT INTO ezsearch_object_word_link VALUES(12,4,14,0.0,4958,'user_account',0,1051,7,1052,1033920830,2,960);
INSERT INTO ezsearch_object_word_link VALUES(12,4,14,0.0,4959,'user_account',0,1052,8,960,1033920830,2,1051);
INSERT INTO ezsearch_object_word_link VALUES(12,4,14,0.0,4960,'user_account',0,0,9,1051,1033920830,2,1052);
INSERT INTO ezsearch_object_word_link VALUES(370,23,57,0.0,5029,'name',0,1111,0,0,1193906012,1,1110);
INSERT INTO ezsearch_object_word_link VALUES(237,23,57,0.0,5030,'left_column',0,1112,1,1110,1193906012,1,1111);
INSERT INTO ezsearch_object_word_link VALUES(237,23,57,0.0,5031,'left_column',0,814,2,1111,1193906012,1,1112);
INSERT INTO ezsearch_object_word_link VALUES(237,23,57,0.0,5032,'left_column',0,1113,3,1112,1193906012,1,814);
INSERT INTO ezsearch_object_word_link VALUES(237,23,57,0.0,5033,'left_column',0,1114,4,814,1193906012,1,1113);
INSERT INTO ezsearch_object_word_link VALUES(237,23,57,0.0,5034,'left_column',0,1115,5,1113,1193906012,1,1114);
INSERT INTO ezsearch_object_word_link VALUES(237,23,57,0.0,5035,'left_column',0,1116,6,1114,1193906012,1,1115);
INSERT INTO ezsearch_object_word_link VALUES(237,23,57,0.0,5036,'left_column',0,1117,7,1115,1193906012,1,1116);
INSERT INTO ezsearch_object_word_link VALUES(237,23,57,0.0,5037,'left_column',0,1118,8,1116,1193906012,1,1117);
INSERT INTO ezsearch_object_word_link VALUES(237,23,57,0.0,5038,'left_column',0,1076,9,1117,1193906012,1,1118);
INSERT INTO ezsearch_object_word_link VALUES(237,23,57,0.0,5039,'left_column',0,0,10,1118,1193906012,1,1076);
INSERT INTO ezsearch_object_word_link VALUES(439,58,60,0.0,5040,'name',0,1119,0,0,1704585539,1,1119);
INSERT INTO ezsearch_object_word_link VALUES(440,58,60,0.0,5041,'description',0,1120,1,1119,1704585539,1,1119);
INSERT INTO ezsearch_object_word_link VALUES(442,58,60,0.0,5042,'open_in_new_window',1,0,2,1119,1704585539,1,1120);
INSERT INTO ezsearch_object_word_link VALUES(439,58,61,0.0,5043,'name',0,1121,0,0,1704585628,1,1121);
INSERT INTO ezsearch_object_word_link VALUES(440,58,61,0.0,5044,'description',0,1115,1,1121,1704585628,1,1121);
INSERT INTO ezsearch_object_word_link VALUES(440,58,61,0.0,5045,'description',0,1116,2,1121,1704585628,1,1115);
INSERT INTO ezsearch_object_word_link VALUES(440,58,61,0.0,5046,'description',0,1082,3,1115,1704585628,1,1116);
INSERT INTO ezsearch_object_word_link VALUES(442,58,61,0.0,5047,'open_in_new_window',0,0,4,1116,1704585628,1,1082);
INSERT INTO ezsearch_object_word_link VALUES(183,16,59,0.0,5048,'title',0,1123,0,0,1704579784,1,1122);
INSERT INTO ezsearch_object_word_link VALUES(183,16,59,0.0,5049,'title',0,1076,1,1122,1704579784,1,1123);
INSERT INTO ezsearch_object_word_link VALUES(183,16,59,0.0,5050,'title',0,1124,2,1123,1704579784,1,1076);
INSERT INTO ezsearch_object_word_link VALUES(183,16,59,0.0,5051,'title',0,1125,3,1076,1704579784,1,1124);
INSERT INTO ezsearch_object_word_link VALUES(183,16,59,0.0,5052,'title',0,814,4,1124,1704579784,1,1125);
INSERT INTO ezsearch_object_word_link VALUES(183,16,59,0.0,5053,'title',0,1126,5,1125,1704579784,1,814);
INSERT INTO ezsearch_object_word_link VALUES(183,16,59,0.0,5054,'title',0,1127,6,814,1704579784,1,1126);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5055,'intro',0,1128,7,1126,1704579784,1,1127);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5056,'intro',0,1129,8,1127,1704579784,1,1128);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5057,'intro',0,1130,9,1128,1704579784,1,1129);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5058,'intro',0,1131,10,1129,1704579784,1,1130);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5059,'intro',0,1132,11,1130,1704579784,1,1131);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5060,'intro',0,1115,12,1131,1704579784,1,1132);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5061,'intro',0,1116,13,1132,1704579784,1,1115);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5062,'intro',0,1117,14,1115,1704579784,1,1116);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5063,'intro',0,1118,15,1116,1704579784,1,1117);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5064,'intro',0,1133,16,1117,1704579784,1,1118);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5065,'intro',0,1134,17,1118,1704579784,1,1133);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5066,'intro',0,1135,18,1133,1704579784,1,1134);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5067,'intro',0,1076,19,1134,1704579784,1,1135);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5068,'intro',0,1136,20,1135,1704579784,1,1076);
INSERT INTO ezsearch_object_word_link VALUES(186,16,59,0.0,5069,'intro',0,1137,21,1076,1704579784,1,1136);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5070,'body',0,1138,22,1136,1704579784,1,1137);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5071,'body',0,1139,23,1137,1704579784,1,1138);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5072,'body',0,1112,24,1138,1704579784,1,1139);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5073,'body',0,1140,25,1139,1704579784,1,1112);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5074,'body',0,1141,26,1112,1704579784,1,1140);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5075,'body',0,1128,27,1140,1704579784,1,1141);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5076,'body',0,1142,28,1141,1704579784,1,1128);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5077,'body',0,1143,29,1128,1704579784,1,1142);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5078,'body',0,1144,30,1142,1704579784,1,1143);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5079,'body',0,1145,31,1143,1704579784,1,1144);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5080,'body',0,1146,32,1144,1704579784,1,1145);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5081,'body',0,1143,33,1145,1704579784,1,1146);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5082,'body',0,1147,34,1146,1704579784,1,1143);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5083,'body',0,1148,35,1143,1704579784,1,1147);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5084,'body',0,814,36,1147,1704579784,1,1148);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5085,'body',0,1149,37,1148,1704579784,1,814);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5086,'body',0,1150,38,814,1704579784,1,1149);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5087,'body',0,1114,39,1149,1704579784,1,1150);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5088,'body',0,1115,40,1150,1704579784,1,1114);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5089,'body',0,1116,41,1114,1704579784,1,1115);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5090,'body',0,1151,42,1115,1704579784,1,1116);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5091,'body',0,1152,43,1116,1704579784,1,1151);
INSERT INTO ezsearch_object_word_link VALUES(187,16,59,0.0,5092,'body',0,1082,44,1151,1704579784,1,1152);
INSERT INTO ezsearch_object_word_link VALUES(191,16,59,0.0,5093,'publish_date',0,1082,45,1152,1704579784,1,1082);
INSERT INTO ezsearch_object_word_link VALUES(192,16,59,0.0,5094,'unpublish_date',0,0,46,1082,1704579784,1,1082);
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
INSERT INTO ezsearch_word VALUES(812,1,'setup');
INSERT INTO ezsearch_word VALUES(814,3,'the');
INSERT INTO ezsearch_word VALUES(816,1,'for');
INSERT INTO ezsearch_word VALUES(877,1,'common');
INSERT INTO ezsearch_word VALUES(927,1,'ez.no');
INSERT INTO ezsearch_word VALUES(930,3,'users');
INSERT INTO ezsearch_word VALUES(951,1,'main');
INSERT INTO ezsearch_word VALUES(952,2,'group');
INSERT INTO ezsearch_word VALUES(953,2,'anonymous');
INSERT INTO ezsearch_word VALUES(954,3,'user');
INSERT INTO ezsearch_word VALUES(955,1,'nospam');
INSERT INTO ezsearch_word VALUES(956,1,'guest');
INSERT INTO ezsearch_word VALUES(957,1,'accounts');
INSERT INTO ezsearch_word VALUES(958,2,'administrator');
INSERT INTO ezsearch_word VALUES(959,1,'editors');
INSERT INTO ezsearch_word VALUES(960,1,'admin');
INSERT INTO ezsearch_word VALUES(961,1,'media');
INSERT INTO ezsearch_word VALUES(962,1,'images');
INSERT INTO ezsearch_word VALUES(963,1,'files');
INSERT INTO ezsearch_word VALUES(964,1,'multimedia');
INSERT INTO ezsearch_word VALUES(965,1,'ini');
INSERT INTO ezsearch_word VALUES(966,1,'settings');
INSERT INTO ezsearch_word VALUES(967,1,'sitestyle_identifier');
INSERT INTO ezsearch_word VALUES(968,1,'design');
INSERT INTO ezsearch_word VALUES(1051,1,'info');
INSERT INTO ezsearch_word VALUES(1052,1,'se7enx.com');
INSERT INTO ezsearch_word VALUES(1076,2,'sqlite');
INSERT INTO ezsearch_word VALUES(1082,2,'0');
INSERT INTO ezsearch_word VALUES(1110,1,'home');
INSERT INTO ezsearch_word VALUES(1111,1,'welcome');
INSERT INTO ezsearch_word VALUES(1112,2,'to');
INSERT INTO ezsearch_word VALUES(1113,1,'wonders');
INSERT INTO ezsearch_word VALUES(1114,2,'of');
INSERT INTO ezsearch_word VALUES(1115,3,'ez');
INSERT INTO ezsearch_word VALUES(1116,3,'publish');
INSERT INTO ezsearch_word VALUES(1117,2,'powered');
INSERT INTO ezsearch_word VALUES(1118,2,'by');
INSERT INTO ezsearch_word VALUES(1119,1,'sqlite.org');
INSERT INTO ezsearch_word VALUES(1120,1,'1');
INSERT INTO ezsearch_word VALUES(1121,1,'share');
INSERT INTO ezsearch_word VALUES(1122,1,'testing');
INSERT INTO ezsearch_word VALUES(1123,1,'1234');
INSERT INTO ezsearch_word VALUES(1124,1,'has');
INSERT INTO ezsearch_word VALUES(1125,1,'hit');
INSERT INTO ezsearch_word VALUES(1126,1,'foor');
INSERT INTO ezsearch_word VALUES(1127,1,'hello');
INSERT INTO ezsearch_word VALUES(1128,1,'this');
INSERT INTO ezsearch_word VALUES(1129,1,'is');
INSERT INTO ezsearch_word VALUES(1130,1,'exciting');
INSERT INTO ezsearch_word VALUES(1131,1,'your');
INSERT INTO ezsearch_word VALUES(1132,1,'viewing');
INSERT INTO ezsearch_word VALUES(1133,1,'a');
INSERT INTO ezsearch_word VALUES(1134,1,'brand');
INSERT INTO ezsearch_word VALUES(1135,1,'new');
INSERT INTO ezsearch_word VALUES(1136,1,'database');
INSERT INTO ezsearch_word VALUES(1137,1,'we');
INSERT INTO ezsearch_word VALUES(1138,1,'are');
INSERT INTO ezsearch_word VALUES(1139,1,'hoping');
INSERT INTO ezsearch_word VALUES(1140,1,'silently');
INSERT INTO ezsearch_word VALUES(1141,1,'include');
INSERT INTO ezsearch_word VALUES(1142,1,'support');
INSERT INTO ezsearch_word VALUES(1143,1,'as');
INSERT INTO ezsearch_word VALUES(1144,1,'experimental');
INSERT INTO ezsearch_word VALUES(1145,1,'but');
INSERT INTO ezsearch_word VALUES(1146,1,'tested');
INSERT INTO ezsearch_word VALUES(1147,1,'stable');
INSERT INTO ezsearch_word VALUES(1148,1,'into');
INSERT INTO ezsearch_word VALUES(1149,1,'next');
INSERT INTO ezsearch_word VALUES(1150,1,'version');
INSERT INTO ezsearch_word VALUES(1151,1,'very');
INSERT INTO ezsearch_word VALUES(1152,1,'soon');
CREATE TABLE `ezsection` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `identifier` varchar(255) DEFAULT NULL
,  `locale` varchar(255) DEFAULT NULL
,  `name` varchar(255) DEFAULT NULL
,  `navigation_part_identifier` varchar(100) DEFAULT 'ezcontentnavigationpart'
);
INSERT INTO ezsection VALUES(1,'standard','','Standard','ezcontentnavigationpart');
INSERT INTO ezsection VALUES(2,'users','','Users','ezusernavigationpart');
INSERT INTO ezsection VALUES(3,'media','','Media','ezmedianavigationpart');
INSERT INTO ezsection VALUES(4,'setup','','Setup','ezsetupnavigationpart');
INSERT INTO ezsection VALUES(5,'design','','Design','ezvisualnavigationpart');
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
INSERT INTO ezsite_data VALUES('ezpublish-release','1');
INSERT INTO ezsite_data VALUES('ezpublish-version','6.0.0stable');
INSERT INTO ezsite_data VALUES('ezwebin','1.5.0');
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
INSERT INTO ezurl VALUES(1704475234,23,1,0,1704475234,'8c230a505af1f73ea03e4fe84a679dd0','/user/login');
INSERT INTO ezurl VALUES(1704475234,24,1,0,1704475234,'0f7832c9acb75904844e5a90fc6656c9','http://ez.no/doc/ez_publish/user_manual/4_0/the_administration_interface/the_user_accounts_tab');
INSERT INTO ezurl VALUES(1704475234,25,1,0,1704475234,'4203ecdda5a478da618f952083930d96','http://ez.no/doc/ez_publish/technical_manual/4_x/templates');
INSERT INTO ezurl VALUES(1704475234,26,1,0,1704475234,'b4aaa0666ec4fd7a5fb55cadd659ef5f','http://ez.no/doc/ez_publish/technical_manual/4_x/installation/extensions');
INSERT INTO ezurl VALUES(1704475234,27,1,0,1704475234,'b6536bac63173e8156cd9e8b0aca0799','http://ez.no/doc/ez_publish/user_manual/4_0/daily_tasks');
INSERT INTO ezurl VALUES(1704475234,28,1,0,1704475234,'1fcaadce4a651227c39cee611f8f901e','http://ez.no/doc');
INSERT INTO ezurl VALUES(1704475234,29,1,0,1704475234,'2d42bb92061125f730323e229e752526','http://ez.no/developer/forum');
INSERT INTO ezurl VALUES(1704475234,30,1,0,1704475234,'9fd5c2ca7e61e4e632fc5087ab478c48','http://projects.ez.no/');
INSERT INTO ezurl VALUES(1704475234,31,1,0,1704475234,'81e45759f679121daf6af50ab2ac73b6','http://ez.no/support_and_services/training');
INSERT INTO ezurl VALUES(1704475234,32,1,0,1704475234,'807ecb77fea14245dfb1141855588864','http://ez.no/support_and_services/expert_consulting');
INSERT INTO ezurl VALUES(1704475234,33,1,0,1704475234,'73bbbe9943f1196c0282d5ed8467e003','http://ez.no/support_and_services/ez_publish_premium');
INSERT INTO ezurl VALUES(1704585539,34,1,0,1704585539,'6960087bfae5a616f8c4adda4b7dd777','https://SQLite.org');
INSERT INTO ezurl VALUES(1704585628,35,1,0,1704585628,'287295c5c1e565ee2b6daec537f38ddd','https://share.se7enx.com');
CREATE TABLE `ezurl_object_link` (
  `contentobject_attribute_id` integer NOT NULL DEFAULT '0'
,  `contentobject_attribute_version` integer NOT NULL DEFAULT '0'
,  `url_id` integer NOT NULL DEFAULT '0'
);
INSERT INTO ezurl_object_link VALUES(203,1,23);
INSERT INTO ezurl_object_link VALUES(203,1,24);
INSERT INTO ezurl_object_link VALUES(203,1,25);
INSERT INTO ezurl_object_link VALUES(203,1,26);
INSERT INTO ezurl_object_link VALUES(203,1,27);
INSERT INTO ezurl_object_link VALUES(203,1,28);
INSERT INTO ezurl_object_link VALUES(203,1,29);
INSERT INTO ezurl_object_link VALUES(203,1,30);
INSERT INTO ezurl_object_link VALUES(203,1,31);
INSERT INTO ezurl_object_link VALUES(203,1,32);
INSERT INTO ezurl_object_link VALUES(203,1,33);
INSERT INTO ezurl_object_link VALUES(203,1,23);
INSERT INTO ezurl_object_link VALUES(203,1,24);
INSERT INTO ezurl_object_link VALUES(203,1,25);
INSERT INTO ezurl_object_link VALUES(203,1,26);
INSERT INTO ezurl_object_link VALUES(203,1,27);
INSERT INTO ezurl_object_link VALUES(203,1,28);
INSERT INTO ezurl_object_link VALUES(203,1,29);
INSERT INTO ezurl_object_link VALUES(203,1,30);
INSERT INTO ezurl_object_link VALUES(203,1,31);
INSERT INTO ezurl_object_link VALUES(203,1,32);
INSERT INTO ezurl_object_link VALUES(203,1,33);
INSERT INTO ezurl_object_link VALUES(203,1,23);
INSERT INTO ezurl_object_link VALUES(203,1,24);
INSERT INTO ezurl_object_link VALUES(203,1,25);
INSERT INTO ezurl_object_link VALUES(203,1,26);
INSERT INTO ezurl_object_link VALUES(203,1,27);
INSERT INTO ezurl_object_link VALUES(203,1,28);
INSERT INTO ezurl_object_link VALUES(203,1,29);
INSERT INTO ezurl_object_link VALUES(203,1,30);
INSERT INTO ezurl_object_link VALUES(203,1,31);
INSERT INTO ezurl_object_link VALUES(203,1,32);
INSERT INTO ezurl_object_link VALUES(203,1,33);
INSERT INTO ezurl_object_link VALUES(553,1,34);
INSERT INTO ezurl_object_link VALUES(557,1,35);
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
INSERT INTO ezurlalias VALUES('content/view/full/2',0,12,1,1,0,'d41d8cd98f00b204e9800998ecf8427e','');
INSERT INTO ezurlalias VALUES('content/view/full/5',0,13,1,1,0,'9bc65c2abec141778ffaa729489f3e87','users');
INSERT INTO ezurlalias VALUES('content/view/full/12',0,15,1,1,0,'02d4e844e3a660857a3f81585995ffe1','users/guest_accounts');
INSERT INTO ezurlalias VALUES('content/view/full/13',0,16,1,1,0,'1b1d79c16700fd6003ea7be233e754ba','users/administrator_users');
INSERT INTO ezurlalias VALUES('content/view/full/14',0,17,1,1,0,'0bb9dd665c96bbc1cf36b79180786dea','users/editors');
INSERT INTO ezurlalias VALUES('content/view/full/15',0,18,1,1,0,'f1305ac5f327a19b451d82719e0c3f5d','users/administrator_users/administrator_user');
INSERT INTO ezurlalias VALUES('content/view/full/43',0,20,1,1,0,'62933a2951ef01f4eafd9bdf4d3cd2f0','media');
INSERT INTO ezurlalias VALUES('content/view/full/44',0,21,1,1,0,'3ae1aac958e1c82013689d917d34967a','users/anonymous_users');
INSERT INTO ezurlalias VALUES('content/view/full/45',0,22,1,1,0,'aad93975f09371695ba08292fd9698db','users/anonymous_users/anonymous_user');
INSERT INTO ezurlalias VALUES('content/view/full/48',0,25,1,1,0,'a0f848942ce863cf53c0fa6cc684007d','setup');
INSERT INTO ezurlalias VALUES('content/view/full/50',0,27,1,1,0,'c60212835de76414f9bfd21eecb8f221','foo_bar_folder/images/vbanner');
INSERT INTO ezurlalias VALUES('content/view/full/51',0,28,1,1,0,'38985339d4a5aadfc41ab292b4527046','media/images');
INSERT INTO ezurlalias VALUES('content/view/full/52',0,29,1,1,0,'ad5a8c6f6aac3b1b9df267fe22e7aef6','media/files');
INSERT INTO ezurlalias VALUES('content/view/full/53',0,30,1,1,0,'562a0ac498571c6c3529173184a2657c','media/multimedia');
INSERT INTO ezurlalias VALUES('content/view/full/54',0,31,1,1,0,'e501fe6c81ed14a5af2b322d248102d8','setup/common_ini_settings');
INSERT INTO ezurlalias VALUES('content/view/full/56',0,32,1,1,0,'2dd3db5dc7122ea5f3ee539bb18fe97d','design/ez_publish');
INSERT INTO ezurlalias VALUES('content/view/full/58',0,33,1,1,0,'31c13f47ad87dd7baa2d558a91e0fbb9','design');
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
INSERT INTO ezurlalias_ml VALUES('nop:','nop',1,14,0,0,1,14,0,'foo_bar_folder','0288b6883046492fa92e4a84eb67acc9');
INSERT INTO ezurlalias_ml VALUES('eznode:59','eznode',1,40,0,0,5,38,0,'Home','106a6c241b8797f52e1e77317b96a201');
INSERT INTO ezurlalias_ml VALUES('eznode:59','eznode',1,38,0,1,4,38,0,'eZ-Publish','10e4c3cb527fb9963258469986c16240');
INSERT INTO ezurlalias_ml VALUES('eznode:58','eznode',1,25,0,1,3,25,0,'Design','31c13f47ad87dd7baa2d558a91e0fbb9');
INSERT INTO ezurlalias_ml VALUES('eznode:48','eznode',1,13,0,1,3,13,0,'Setup2','475e97c0146bfb1c490339546d9e72ee');
INSERT INTO ezurlalias_ml VALUES('nop:','nop',1,17,0,0,1,17,0,'media2','50e2736330de124f6edea9b008556fe6');
INSERT INTO ezurlalias_ml VALUES('eznode:43','eznode',1,9,0,1,3,9,0,'Media','62933a2951ef01f4eafd9bdf4d3cd2f0');
INSERT INTO ezurlalias_ml VALUES('nop:','nop',1,21,0,0,1,21,0,'setup3','732cefcf28bf4547540609fb1a786a30');
INSERT INTO ezurlalias_ml VALUES('nop:','nop',1,3,0,0,1,3,0,'users2','86425c35a33507d479f71ade53a669aa');
INSERT INTO ezurlalias_ml VALUES('eznode:5','eznode',1,2,0,1,3,2,0,'Users','9bc65c2abec141778ffaa729489f3e87');
INSERT INTO ezurlalias_ml VALUES('eznode:2','eznode',1,1,0,1,7,1,0,'','d41d8cd98f00b204e9800998ecf8427e');
INSERT INTO ezurlalias_ml VALUES('eznode:14','eznode',1,6,0,1,3,6,2,'Editors','a147e136bfa717592f2bd70bd4b53b17');
INSERT INTO ezurlalias_ml VALUES('eznode:44','eznode',1,10,0,1,3,10,2,'Anonymous-Users','c2803c3fa1b0b5423237b4e018cae755');
INSERT INTO ezurlalias_ml VALUES('eznode:12','eznode',1,4,0,1,3,4,2,'Guest-accounts','e57843d836e3af8ab611fde9e2139b3a');
INSERT INTO ezurlalias_ml VALUES('eznode:13','eznode',1,5,0,1,3,5,2,'Administrator-users','f89fad7f8a3abc8c09e1deb46a420007');
INSERT INTO ezurlalias_ml VALUES('nop:','nop',1,11,0,0,1,11,3,'anonymous_users2','505e93077a6dde9034ad97a14ab022b1');
INSERT INTO ezurlalias_ml VALUES('eznode:12','eznode',1,26,0,0,1,4,3,'guest_accounts','70bb992820e73638731aa8de79b3329e');
INSERT INTO ezurlalias_ml VALUES('eznode:14','eznode',1,29,0,0,1,6,3,'editors','a147e136bfa717592f2bd70bd4b53b17');
INSERT INTO ezurlalias_ml VALUES('nop:','nop',1,7,0,0,1,7,3,'administrator_users2','a7da338c20bf65f9f789c87296379c2a');
INSERT INTO ezurlalias_ml VALUES('eznode:13','eznode',1,27,0,0,1,5,3,'administrator_users','aeb8609aa933b0899aa012c71139c58c');
INSERT INTO ezurlalias_ml VALUES('eznode:44','eznode',1,30,0,0,1,10,3,'anonymous_users','e9e5ad0c05ee1a43715572e5cc545926');
INSERT INTO ezurlalias_ml VALUES('eznode:15','eznode',1,8,0,1,6,8,5,'Administrator-User','5a9d7b0ec93173ef4fedee023209cb61');
INSERT INTO ezurlalias_ml VALUES('eznode:15','eznode',1,28,0,0,0,8,7,'administrator_user','a3cca2de936df1e2f805710399989971');
INSERT INTO ezurlalias_ml VALUES('eznode:53','eznode',1,20,0,1,3,20,9,'Multimedia','2e5bc8831f7ae6a29530e7f1bbf2de9c');
INSERT INTO ezurlalias_ml VALUES('eznode:52','eznode',1,19,0,1,3,19,9,'Files','45b963397aa40d4a0063e0d85e4fe7a1');
INSERT INTO ezurlalias_ml VALUES('eznode:51','eznode',1,18,0,1,3,18,9,'Images','59b514174bffe4ae402b3d63aad79fe0');
INSERT INTO ezurlalias_ml VALUES('eznode:45','eznode',1,12,0,1,3,12,10,'Anonymous-User','ccb62ebca03a31272430bc414bd5cd5b');
INSERT INTO ezurlalias_ml VALUES('eznode:45','eznode',1,31,0,0,1,12,11,'anonymous_user','c593ec85293ecb0e02d50d4c5c6c20eb');
INSERT INTO ezurlalias_ml VALUES('eznode:54','eznode',1,22,0,1,2,22,13,'Common-INI-settings','4434993ac013ae4d54bb1f51034d6401');
INSERT INTO ezurlalias_ml VALUES('nop:','nop',1,15,0,0,1,15,14,'images','59b514174bffe4ae402b3d63aad79fe0');
INSERT INTO ezurlalias_ml VALUES('eznode:50','eznode',1,16,0,1,2,16,15,'vbanner','c54e2d1b93642e280bdc5d99eab2827d');
INSERT INTO ezurlalias_ml VALUES('eznode:53','eznode',1,34,0,0,1,20,17,'multimedia','2e5bc8831f7ae6a29530e7f1bbf2de9c');
INSERT INTO ezurlalias_ml VALUES('eznode:52','eznode',1,33,0,0,1,19,17,'files','45b963397aa40d4a0063e0d85e4fe7a1');
INSERT INTO ezurlalias_ml VALUES('eznode:51','eznode',1,32,0,0,1,18,17,'images','59b514174bffe4ae402b3d63aad79fe0');
INSERT INTO ezurlalias_ml VALUES('eznode:54','eznode',1,35,0,0,1,22,21,'common_ini_settings','e59d6834e86cee752ed841f9cd8d5baf');
INSERT INTO ezurlalias_ml VALUES('eznode:56','eznode',1,37,0,0,2,24,25,'eZ-publish','10e4c3cb527fb9963258469986c16240');
INSERT INTO ezurlalias_ml VALUES('eznode:56','eznode',1,24,0,1,2,24,25,'Plain-site','49a39d99a955d95aa5d636275656a07a');
INSERT INTO ezurlalias_ml VALUES('eznode:61','eznode',1,41,0,1,5,41,0,'Testing-1234-SQLite-has-hit-the-foor','f80555abd2b9fb9effee8f4fda4f3717');
INSERT INTO ezurlalias_ml VALUES('eznode:62','eznode',1,42,0,1,4,42,0,'SQLite.org','dadc0270253355d296cfab111d1c5bdb');
INSERT INTO ezurlalias_ml VALUES('eznode:63','eznode',1,43,0,1,4,43,0,'Share','85e47ac07ac9d6416168a97e33fa969a');
CREATE TABLE `ezurlalias_ml_incr` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
);
INSERT INTO ezurlalias_ml_incr VALUES(1);
INSERT INTO ezurlalias_ml_incr VALUES(2);
INSERT INTO ezurlalias_ml_incr VALUES(3);
INSERT INTO ezurlalias_ml_incr VALUES(4);
INSERT INTO ezurlalias_ml_incr VALUES(5);
INSERT INTO ezurlalias_ml_incr VALUES(6);
INSERT INTO ezurlalias_ml_incr VALUES(7);
INSERT INTO ezurlalias_ml_incr VALUES(8);
INSERT INTO ezurlalias_ml_incr VALUES(9);
INSERT INTO ezurlalias_ml_incr VALUES(10);
INSERT INTO ezurlalias_ml_incr VALUES(11);
INSERT INTO ezurlalias_ml_incr VALUES(12);
INSERT INTO ezurlalias_ml_incr VALUES(13);
INSERT INTO ezurlalias_ml_incr VALUES(14);
INSERT INTO ezurlalias_ml_incr VALUES(15);
INSERT INTO ezurlalias_ml_incr VALUES(16);
INSERT INTO ezurlalias_ml_incr VALUES(17);
INSERT INTO ezurlalias_ml_incr VALUES(18);
INSERT INTO ezurlalias_ml_incr VALUES(19);
INSERT INTO ezurlalias_ml_incr VALUES(20);
INSERT INTO ezurlalias_ml_incr VALUES(21);
INSERT INTO ezurlalias_ml_incr VALUES(22);
INSERT INTO ezurlalias_ml_incr VALUES(24);
INSERT INTO ezurlalias_ml_incr VALUES(25);
INSERT INTO ezurlalias_ml_incr VALUES(26);
INSERT INTO ezurlalias_ml_incr VALUES(27);
INSERT INTO ezurlalias_ml_incr VALUES(28);
INSERT INTO ezurlalias_ml_incr VALUES(29);
INSERT INTO ezurlalias_ml_incr VALUES(30);
INSERT INTO ezurlalias_ml_incr VALUES(31);
INSERT INTO ezurlalias_ml_incr VALUES(32);
INSERT INTO ezurlalias_ml_incr VALUES(33);
INSERT INTO ezurlalias_ml_incr VALUES(34);
INSERT INTO ezurlalias_ml_incr VALUES(35);
INSERT INTO ezurlalias_ml_incr VALUES(36);
INSERT INTO ezurlalias_ml_incr VALUES(37);
INSERT INTO ezurlalias_ml_incr VALUES(38);
INSERT INTO ezurlalias_ml_incr VALUES(39);
INSERT INTO ezurlalias_ml_incr VALUES(40);
INSERT INTO ezurlalias_ml_incr VALUES(41);
INSERT INTO ezurlalias_ml_incr VALUES(42);
INSERT INTO ezurlalias_ml_incr VALUES(43);
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
INSERT INTO ezuser VALUES(10,'nospam@ez.no','anonymous','$2y$10$ucfC921pDYoruiPZdod7hO2oiGbsHQ/5OmRqRui7v5Txc.Oaq15rW',7);
INSERT INTO ezuser VALUES(14,'info@se7enx.com','admin','$2y$10$CbS2/DYTtb/wBtzVSQu6S.3DOkDC/XoWJLGT8C2Fhf8oDDNHe86Dm',7);
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
INSERT INTO ezuser_role VALUES(12,25,'','',2);
INSERT INTO ezuser_role VALUES(11,28,'','',1);
INSERT INTO ezuser_role VALUES(42,31,'','',1);
INSERT INTO ezuser_role VALUES(13,32,'Subtree','/1/2/',3);
INSERT INTO ezuser_role VALUES(13,33,'Subtree','/1/43/',3);
CREATE TABLE `ezuser_setting` (
  `is_enabled` integer NOT NULL DEFAULT '0'
,  `max_login` integer DEFAULT NULL
,  `user_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`user_id`)
);
INSERT INTO ezuser_setting VALUES(1,1000,10);
INSERT INTO ezuser_setting VALUES(1,10,14);
CREATE TABLE `ezuservisit` (
  `current_visit_timestamp` integer NOT NULL DEFAULT '0'
,  `failed_login_attempts` integer NOT NULL DEFAULT '0'
,  `last_visit_timestamp` integer NOT NULL DEFAULT '0'
,  `login_count` integer NOT NULL DEFAULT '0'
,  `user_id` integer NOT NULL DEFAULT '0'
,  PRIMARY KEY (`user_id`)
);
INSERT INTO ezuservisit VALUES(1704476021,0,1704475231,1,14);
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
INSERT INTO ezvattype VALUES(1,'Std',0.0);
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
INSERT INTO ezworkflow_group VALUES(1024392098,14,1,1024392098,14,'Standard');
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
DELETE FROM sqlite_sequence;
INSERT INTO sqlite_sequence VALUES('ezcobj_state',2);
INSERT INTO sqlite_sequence VALUES('ezcobj_state_group',2);
INSERT INTO sqlite_sequence VALUES('ezcontentbrowserecent',2);
INSERT INTO sqlite_sequence VALUES('ezcontentclass',71);
INSERT INTO sqlite_sequence VALUES('ezcontentclass_attribute',497);
INSERT INTO sqlite_sequence VALUES('ezcontentclassgroup',4);
INSERT INTO sqlite_sequence VALUES('ezcontentobject',61);
INSERT INTO sqlite_sequence VALUES('ezcontentobject_attribute',558);
INSERT INTO sqlite_sequence VALUES('ezcontentobject_link',6);
INSERT INTO sqlite_sequence VALUES('ezcontentobject_tree',63);
INSERT INTO sqlite_sequence VALUES('ezcontentobject_version',526);
INSERT INTO sqlite_sequence VALUES('ezimagefile',7);
INSERT INTO sqlite_sequence VALUES('ezisbn_group',209);
INSERT INTO sqlite_sequence VALUES('ezisbn_group_range',8);
INSERT INTO sqlite_sequence VALUES('ezisbn_registrant_range',926);
INSERT INTO sqlite_sequence VALUES('eznode_assignment',65);
INSERT INTO sqlite_sequence VALUES('eznotificationevent',18);
INSERT INTO sqlite_sequence VALUES('ezorder_status',3);
INSERT INTO sqlite_sequence VALUES('ezpackage',8);
INSERT INTO sqlite_sequence VALUES('ezpolicy',341);
INSERT INTO sqlite_sequence VALUES('ezpolicy_limitation',260);
INSERT INTO sqlite_sequence VALUES('ezpolicy_limitation_value',486);
INSERT INTO sqlite_sequence VALUES('ezpreferences',10);
INSERT INTO sqlite_sequence VALUES('ezrole',4);
INSERT INTO sqlite_sequence VALUES('ezsearch_object_word_link',5094);
INSERT INTO sqlite_sequence VALUES('ezsearch_word',1152);
INSERT INTO sqlite_sequence VALUES('ezsection',5);
INSERT INTO sqlite_sequence VALUES('ezurl',35);
INSERT INTO sqlite_sequence VALUES('ezurlalias',33);
INSERT INTO sqlite_sequence VALUES('ezurlalias_ml_incr',43);
INSERT INTO sqlite_sequence VALUES('ezuser_role',33);
INSERT INTO sqlite_sequence VALUES('ezvattype',1);
INSERT INTO sqlite_sequence VALUES('ezworkflow_group',1);
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
COMMIT;
