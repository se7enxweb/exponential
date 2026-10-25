---
title: Policy function (access control)
audience: developer
category: standards
order: 317
summary: A permission a role can grant, optionally limited (by class, section, subtree, siteaccess ...). Views name the functions they need. 259 instances in Exponential.
keywords: design pattern, policy-function, request handling, pattern catalogue
type: catalogue
---
# Policy function (access control)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A permission a role can grant, optionally limited (by class, section, subtree, siteaccess ...). Views name the functions they need.

| | |
|---|---|
| Group | Request handling |
| Instances | 259 |
| Recognized by | An entry of $FunctionList in a module.php. |
| Extension point | module.php $FunctionList; limitations are functions that list the allowed values. |

## Policy functions (259)

### extension/AdminAid (4)

| Function | Limitations | Defined in |
|---|---|---|
| `aid/user_switch` | – | `extension/AdminAid/modules/aid/module.php:27` |
| `aid/object_view` | – | `extension/AdminAid/modules/aid/module.php:28` |
| `aid/class_translate` | – | `extension/AdminAid/modules/aid/module.php:29` |
| `aid/search` | – | `extension/AdminAid/modules/aid/module.php:30` |

### extension/bccie (2)

| Function | Limitations | Defined in |
|---|---|---|
| `bccie/read` | – | `extension/bccie/modules/bccie/module.php:79` |
| `bccie/remove` | – | `extension/bccie/modules/bccie/module.php:80` |

### extension/bcgooglesitemaps (1)

| Function | Limitations | Defined in |
|---|---|---|
| `googlesitemapdynamic/sitemap` | – | `extension/bcgooglesitemaps/modules/googlesitemapdynamic/module.php:19` |

### extension/cjw_newsletter (35)

| Function | Limitations | Defined in |
|---|---|---|
| `newsletter/subscribe` | – | `extension/cjw_newsletter/modules/newsletter/module.php:478` |
| `newsletter/configure` | – | `extension/cjw_newsletter/modules/newsletter/module.php:479` |
| `newsletter/unsubscribe` | – | `extension/cjw_newsletter/modules/newsletter/module.php:480` |
| `newsletter/subscription_list_csvimport` | – | `extension/cjw_newsletter/modules/newsletter/module.php:481` |
| `newsletter/subscription_list_csvimport_import` | – | `extension/cjw_newsletter/modules/newsletter/module.php:482` |
| `newsletter/subscription_list_csvexport` | – | `extension/cjw_newsletter/modules/newsletter/module.php:483` |
| `newsletter/subscription_list` | – | `extension/cjw_newsletter/modules/newsletter/module.php:484` |
| `newsletter/subscription_view` | – | `extension/cjw_newsletter/modules/newsletter/module.php:485` |
| `newsletter/user_list` | – | `extension/cjw_newsletter/modules/newsletter/module.php:486` |
| `newsletter/user_view` | – | `extension/cjw_newsletter/modules/newsletter/module.php:487` |
| `newsletter/user_remove` | – | `extension/cjw_newsletter/modules/newsletter/module.php:488` |
| `newsletter/user_edit` | – | `extension/cjw_newsletter/modules/newsletter/module.php:489` |
| `newsletter/user_create` | – | `extension/cjw_newsletter/modules/newsletter/module.php:490` |
| `newsletter/preview` | – | `extension/cjw_newsletter/modules/newsletter/module.php:491` |
| `newsletter/archive` | – | `extension/cjw_newsletter/modules/newsletter/module.php:492` |
| `newsletter/index` | – | `extension/cjw_newsletter/modules/newsletter/module.php:493` |
| `newsletter/settings` | – | `extension/cjw_newsletter/modules/newsletter/module.php:494` |
| `newsletter/send` | – | `extension/cjw_newsletter/modules/newsletter/module.php:495` |
| `newsletter/mailbox_item_list` | – | `extension/cjw_newsletter/modules/newsletter/module.php:496` |
| `newsletter/mailbox_item_view` | – | `extension/cjw_newsletter/modules/newsletter/module.php:497` |
| `newsletter/mailbox_list` | – | `extension/cjw_newsletter/modules/newsletter/module.php:498` |
| `newsletter/mailbox_edit` | – | `extension/cjw_newsletter/modules/newsletter/module.php:499` |
| `newsletter/blacklist_item` | – | `extension/cjw_newsletter/modules/newsletter/module.php:500` |
| `newsletter/import_list` | – | `extension/cjw_newsletter/modules/newsletter/module.php:501` |
| `newsletter/import_view` | – | `extension/cjw_newsletter/modules/newsletter/module.php:502` |
| `newsletter/admin` | – | `extension/cjw_newsletter/modules/newsletter/module.php:504` |
| `newsletter/deliverability` | – | `extension/cjw_newsletter/modules/newsletter/module.php:507` |
| `newsletter/editorial` | – | `extension/cjw_newsletter/modules/newsletter/module.php:510` |
| `newsletter/approve` | – | `extension/cjw_newsletter/modules/newsletter/module.php:511` |
| `newsletter/rendering` | – | `extension/cjw_newsletter/modules/newsletter/module.php:514` |
| `newsletter/statistics` | – | `extension/cjw_newsletter/modules/newsletter/module.php:517` |
| `newsletter/track` | – | `extension/cjw_newsletter/modules/newsletter/module.php:518` |
| `newsletter/sms` | – | `extension/cjw_newsletter/modules/newsletter/module.php:521` |
| `newsletter/sms_public` | – | `extension/cjw_newsletter/modules/newsletter/module.php:522` |
| `newsletter/import_export` | – | `extension/cjw_newsletter/modules/newsletter/module.php:525` |

### extension/cjw_themes_jumper (1)

| Function | Limitations | Defined in |
|---|---|---|
| `osm_proxy/tiles` | – | `extension/cjw_themes_jumper/modules/osm_proxy/module.php:23` |

### extension/expai (13)

| Function | Limitations | Defined in |
|---|---|---|
| `expai/write` | – | `extension/expai/modules/expai/module.php:121` |
| `expai/log` | – | `extension/expai/modules/expai/module.php:122` |
| `expai/settings` | – | `extension/expai/modules/expai/module.php:123` |
| `expai/model_use` | Provider | `extension/expai/modules/expai/module.php:124` |
| `expai/model_manage` | – | `extension/expai/modules/expai/module.php:125` |
| `expai/lane_fast` | – | `extension/expai/modules/expai/module.php:126` |
| `expai/mcp` | Server | `extension/expai/modules/expai/module.php:127` |
| `expai/chain` | – | `extension/expai/modules/expai/module.php:129` |
| `expai/vector` | – | `extension/expai/modules/expai/module.php:133` |
| `expai/web` | – | `extension/expai/modules/expai/module.php:140` |
| `expai/translation_sync` | – | `extension/expai/modules/expai/module.php:145` |
| `expai/services` | – | `extension/expai/modules/expai/module.php:149` |
| `expai/summary` | – | `extension/expai/modules/expai/module.php:153` |

### extension/expchangeclass (1)

| Function | Limitations | Defined in |
|---|---|---|
| `changeclass/convert` | – | `extension/expchangeclass/modules/changeclass/module.php:31` |

### extension/expimageeditor (1)

| Function | Limitations | Defined in |
|---|---|---|
| `expimageeditor/edit` | – | `extension/expimageeditor/modules/expimageeditor/module.php:20` |

### extension/explayouts (2)

| Function | Limitations | Defined in |
|---|---|---|
| `explayouts/read` | – | `extension/explayouts/modules/explayouts/module.php:71` |
| `explayouts/edit` | – | `extension/explayouts/modules/explayouts/module.php:72` |

### extension/explayouts_content_browser_ui (1)

| Function | Limitations | Defined in |
|---|---|---|
| `explayouts_content_browser_ui/read` | – | `extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui/module.php:15` |

### extension/explayouts_ui (4)

| Function | Limitations | Defined in |
|---|---|---|
| `explayouts_ui/read` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:108` |
| `explayouts_ui/edit` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:109` |
| `explayouts_ui/transfer_export` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:110` |
| `explayouts_ui/transfer_import` | – | `extension/explayouts_ui/modules/explayouts_ui/module.php:111` |

### extension/explayouts_ui_api (2)

| Function | Limitations | Defined in |
|---|---|---|
| `explayouts_ui_api/read` | – | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:36` |
| `explayouts_ui_api/edit` | – | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:39` |

### extension/expqueue (2)

| Function | Limitations | Defined in |
|---|---|---|
| `expqueue/view` | – | `extension/expqueue/modules/expqueue/module.php:48` |
| `expqueue/manage` | – | `extension/expqueue/modules/expqueue/module.php:49` |

### extension/exp_oe_tiptap (2)

| Function | Limitations | Defined in |
|---|---|---|
| `exp_oe_tiptap/switch` | – | `extension/exp_oe_tiptap/modules/exp_oe_tiptap/module.php:26` |
| `exp_oe_tiptap/ai` | – | `extension/exp_oe_tiptap/modules/exp_oe_tiptap/module.php:28` |

### extension/ezfind (1)

| Function | Limitations | Defined in |
|---|---|---|
| `ezfind/elevate` | – | `extension/ezfind/modules/ezfind/module.php:46` |

### extension/ezflow (4)

| Function | Limitations | Defined in |
|---|---|---|
| `ezflow/timeline` | – | `extension/ezflow/modules/ezflow/module.php:63` |
| `ezflow/edit` | – | `extension/ezflow/modules/ezflow/module.php:64` |
| `ezflow/call` | – | `extension/ezflow/modules/ezflow/module.php:65` |
| `ezflow/changelayout` | – | `extension/ezflow/modules/ezflow/module.php:66` |

### extension/ezjscore (2)

| Function | Limitations | Defined in |
|---|---|---|
| `ezjscore/run` | – | `extension/ezjscore/modules/ezjscore/module.php:70` |
| `ezjscore/call` | FunctionList | `extension/ezjscore/modules/ezjscore/module.php:71` |

### extension/ezmbpaex (2)

| Function | Limitations | Defined in |
|---|---|---|
| `userpaex/password` | – | `extension/ezmbpaex/modules/userpaex/module.php:37` |
| `userpaex/editpaex` | – | `extension/ezmbpaex/modules/userpaex/module.php:38` |

### extension/ezodf (3)

| Function | Limitations | Defined in |
|---|---|---|
| `ezodf/import` | – | `extension/ezodf/modules/ezodf/module.php:94` |
| `ezodf/export` | – | `extension/ezodf/modules/ezodf/module.php:95` |
| `ezodf/converter` | – | `extension/ezodf/modules/ezodf/module.php:97` |

### extension/ezoe (7)

| Function | Limitations | Defined in |
|---|---|---|
| `ezoe/relations` | Class, Section, Owner, Node, Subtree | `extension/ezoe/modules/ezoe/module.php:123` |
| `ezoe/editor` | Class, Section, Owner, Node, Subtree | `extension/ezoe/modules/ezoe/module.php:129` |
| `ezoe/relations` | – | `extension/ezoe/modules/ezoe/module.php:137` |
| `ezoe/editor` | – | `extension/ezoe/modules/ezoe/module.php:138` |
| `ezoe/search` | – | `extension/ezoe/modules/ezoe/module.php:139` |
| `ezoe/browse` | – | `extension/ezoe/modules/ezoe/module.php:140` |
| `ezoe/disable_editor` | – | `extension/ezoe/modules/ezoe/module.php:141` |

### extension/ezownerchange (2)

| Function | Limitations | Defined in |
|---|---|---|
| `owner/change` | – | `extension/ezownerchange/modules/owner/module.php:39` |
| `owner/subtree` | – | `extension/ezownerchange/modules/owner/module.php:40` |

### extension/ezpm (6)

| Function | Limitations | Defined in |
|---|---|---|
| `pm/list` | – | `extension/ezpm/modules/pm/module.php:67` |
| `pm/blacklist` | – | `extension/ezpm/modules/pm/module.php:68` |
| `pm/contacts` | – | `extension/ezpm/modules/pm/module.php:69` |
| `pm/send_message` | – | `extension/ezpm/modules/pm/module.php:70` |
| `pm/add_contact` | – | `extension/ezpm/modules/pm/module.php:71` |
| `pm/stats` | – | `extension/ezpm/modules/pm/module.php:72` |

### extension/eztags (13)

| Function | Limitations | Defined in |
|---|---|---|
| `tags/read` | – | `extension/eztags/modules/tags/module.php:125` |
| `tags/dashboard` | – | `extension/eztags/modules/tags/module.php:126` |
| `tags/id` | – | `extension/eztags/modules/tags/module.php:127` |
| `tags/view` | – | `extension/eztags/modules/tags/module.php:128` |
| `tags/add` | Tag | `extension/eztags/modules/tags/module.php:129` |
| `tags/addsynonym` | – | `extension/eztags/modules/tags/module.php:130` |
| `tags/edit` | – | `extension/eztags/modules/tags/module.php:131` |
| `tags/editsynonym` | – | `extension/eztags/modules/tags/module.php:132` |
| `tags/delete` | – | `extension/eztags/modules/tags/module.php:133` |
| `tags/deletesynonym` | – | `extension/eztags/modules/tags/module.php:134` |
| `tags/makesynonym` | – | `extension/eztags/modules/tags/module.php:135` |
| `tags/merge` | – | `extension/eztags/modules/tags/module.php:136` |
| `tags/search` | – | `extension/eztags/modules/tags/module.php:137` |

### extension/ezwt (1)

| Function | Limitations | Defined in |
|---|---|---|
| `websitetoolbar/use` | – | `extension/ezwt/modules/websitetoolbar/module.php:44` |

### extension/hcaptcha (1)

| Function | Limitations | Defined in |
|---|---|---|
| `hcaptcha/bypass_captcha` | – | `extension/hcaptcha/modules/hcaptcha/module.php:8` |

### extension/nxc_powercontent (23)

| Function | Limitations | Defined in |
|---|---|---|
| `content/bookmark` | – | `extension/nxc_powercontent/modules/content/module.php:605` |
| `content/move` | – | `extension/nxc_powercontent/modules/content/module.php:607` |
| `content/read` | Class, Section, Owner, Group, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:609` |
| `content/diff` | Class, Section, Owner, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:616` |
| `content/view_embed` | Class, Section, Owner, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:621` |
| `content/create` | Class, Section, ParentOwner, ParentGroup, ParentClass, ParentDepth, Subtree, Language | `extension/nxc_powercontent/modules/content/module.php:626` |
| `content/edit` | Class, Section, Owner, Group, Node, Subtree, Language | `extension/nxc_powercontent/modules/content/module.php:636` |
| `content/publish` | Class, Section, Owner, Group, Node, Subtree, Language | `extension/nxc_powercontent/modules/content/module.php:645` |
| `content/manage_locations` | Class, Section, Owner, Subtree | `extension/nxc_powercontent/modules/content/module.php:654` |
| `content/hide` | Class, Section, Owner, Group, Node, Subtree, Language | `extension/nxc_powercontent/modules/content/module.php:660` |
| `content/reverserelatedlist` | – | `extension/nxc_powercontent/modules/content/module.php:668` |
| `content/translate` | Class, Section, Owner, Node, Subtree, Language | `extension/nxc_powercontent/modules/content/module.php:670` |
| `content/remove` | Class, Section, Owner, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:676` |
| `content/versionread` | Class, Section, Owner, Status, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:684` |
| `content/versionremove` | Class, Section, Owner, Status, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:691` |
| `content/pdf` | Class, Section, Owner, Node, Subtree | `extension/nxc_powercontent/modules/content/module.php:698` |
| `content/translations` | – | `extension/nxc_powercontent/modules/content/module.php:704` |
| `content/urltranslator` | – | `extension/nxc_powercontent/modules/content/module.php:705` |
| `content/pendinglist` | – | `extension/nxc_powercontent/modules/content/module.php:706` |
| `content/restore` | – | `extension/nxc_powercontent/modules/content/module.php:707` |
| `content/cleantrash` | – | `extension/nxc_powercontent/modules/content/module.php:708` |
| `content/tipafriend` | – | `extension/nxc_powercontent/modules/content/module.php:709` |
| `content/dashboard` | – | `extension/nxc_powercontent/modules/content/module.php:710` |

### extension/recaptcha (1)

| Function | Limitations | Defined in |
|---|---|---|
| `recaptcha/bypass_captcha` | – | `extension/recaptcha/modules/recaptcha/module.php:8` |

### extension/sevenx_authentication_2fa (4)

| Function | Limitations | Defined in |
|---|---|---|
| `user2fa/verify` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:50` |
| `user2fa/setup` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:51` |
| `user2fa/callback` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:52` |
| `user2fa/oauth` | – | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:53` |

### extension/sevenx_themes_media (1)

| Function | Limitations | Defined in |
|---|---|---|
| `info-collection/read` | – | `extension/sevenx_themes_media/modules/info-collection/module.php:21` |

### extension/sqliimport (4)

| Function | Limitations | Defined in |
|---|---|---|
| `sqliimport/manage` | SQLIImport_Type | `extension/sqliimport/modules/sqliimport/module.php:222` |
| `sqliimport/run` | SQLIImport_Type | `extension/sqliimport/modules/sqliimport/module.php:223` |
| `sqliimport/listimports` | – | `extension/sqliimport/modules/sqliimport/module.php:225` |
| `sqliimport/manageimports` | SQLIImport_Type | `extension/sqliimport/modules/sqliimport/module.php:226` |

### extension/syndication (11)

| Function | Limitations | Defined in |
|---|---|---|
| `syndication/import_object_status` | – | `extension/syndication/modules/syndication/module.php:133` |
| `syndication/view_export` | – | `extension/syndication/modules/syndication/module.php:135` |
| `syndication/edit_export` | – | `extension/syndication/modules/syndication/module.php:137` |
| `syndication/remove_feed` | – | `extension/syndication/modules/syndication/module.php:139` |
| `syndication/create_feed` | – | `extension/syndication/modules/syndication/module.php:141` |
| `syndication/menu` | – | `extension/syndication/modules/syndication/module.php:143` |
| `syndication/view_import` | – | `extension/syndication/modules/syndication/module.php:145` |
| `syndication/edit_import` | – | `extension/syndication/modules/syndication/module.php:147` |
| `syndication/view_export_info` | – | `extension/syndication/modules/syndication/module.php:149` |
| `syndication/create_import` | – | `extension/syndication/modules/syndication/module.php:151` |
| `syndication/fetch_feed` | FeedID | `extension/syndication/modules/syndication/module.php:153` |

### extension/xrowextract (8)

| Function | Limitations | Defined in |
|---|---|---|
| `xrowextract/schedule` | – | `extension/xrowextract/modules/xrowextract/module.php:117` |
| `xrowextract/destinations` | – | `extension/xrowextract/modules/xrowextract/module.php:119` |
| `xrowextract/history` | – | `extension/xrowextract/modules/xrowextract/module.php:121` |
| `xrowextract/csv` | – | `extension/xrowextract/modules/xrowextract/module.php:122` |
| `xrowextract/password_hash` | – | `extension/xrowextract/modules/xrowextract/module.php:124` |
| `xrowextract/jobs` | – | `extension/xrowextract/modules/xrowextract/module.php:126` |
| `xrowextract/all_jobs` | – | `extension/xrowextract/modules/xrowextract/module.php:128` |
| `xrowextract/import` | – | `extension/xrowextract/modules/xrowextract/module.php:130` |

### kernel/modules (93)

| Function | Limitations | Defined in |
|---|---|---|
| `apikey/create` | values | `kernel/apikey/module.php:47` |
| `audit/read` | – | `kernel/audit/module.php:90` |
| `audit/manage` | – | `kernel/audit/module.php:121` |
| `content/bookmark` | – | `kernel/content/module.php:664` |
| `content/move` | – | `kernel/content/module.php:666` |
| `content/read` | Class, Section, Owner, Group, Node, Subtree | `kernel/content/module.php:668` |
| `content/diff` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:675` |
| `content/view_embed` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:680` |
| `content/create` | Class, Section, ParentOwner, ParentGroup, ParentClass, ParentDepth, Subtree, Language | `kernel/content/module.php:685` |
| `content/edit` | Class, Section, Owner, Group, Node, Subtree, Language | `kernel/content/module.php:695` |
| `content/publish` | Class, Section, Owner, Group, Node, Subtree, Language | `kernel/content/module.php:704` |
| `content/manage_locations` | Class, Section, Owner, Subtree | `kernel/content/module.php:713` |
| `content/hide` | Class, Section, Owner, Group, Node, Subtree, Language | `kernel/content/module.php:719` |
| `content/reverserelatedlist` | – | `kernel/content/module.php:727` |
| `content/translate` | Class, Section, Owner, Node, Subtree, Language | `kernel/content/module.php:729` |
| `content/remove` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:735` |
| `content/versionread` | Class, Section, Owner, Status, Node, Subtree | `kernel/content/module.php:743` |
| `content/versionremove` | Class, Section, Owner, Status, Node, Subtree | `kernel/content/module.php:750` |
| `content/pdf` | Class, Section, Owner, Node, Subtree | `kernel/content/module.php:757` |
| `content/translations` | – | `kernel/content/module.php:763` |
| `content/urltranslator` | – | `kernel/content/module.php:764` |
| `content/pendinglist` | – | `kernel/content/module.php:765` |
| `content/restore` | – | `kernel/content/module.php:766` |
| `content/cleantrash` | – | `kernel/content/module.php:767` |
| `content/tipafriend` | – | `kernel/content/module.php:768` |
| `content/dashboard` | – | `kernel/content/module.php:769` |
| `content/view_system_url` | – | `kernel/content/module.php:773` |
| `content/jobs` | – | `kernel/content/module.php:775` |
| `content/publish_without_notification` | – | `kernel/content/module.php:779` |
| `contentcheck/unreferenced` | – | `kernel/contentcheck/module.php:89` |
| `contentcheck/trash` | – | `kernel/contentcheck/module.php:91` |
| `contentcheck/brokenrelations` | – | `kernel/contentcheck/module.php:93` |
| `contentcheck/fixfiles` | – | `kernel/contentcheck/module.php:96` |
| `contentcheck/storage` | – | `kernel/contentcheck/module.php:98` |
| `contentcheck/quarantine` | – | `kernel/contentcheck/module.php:100` |
| `contentcheck/permissions` | – | `kernel/contentcheck/module.php:102` |
| `contentfinder/search` | – | `kernel/contentfinder/module.php:41` |
| `contentfinder/delete` | – | `kernel/contentfinder/module.php:43` |
| `datatable/read` | – | `kernel/datatable/module.php:82` |
| `datatable/manage` | – | `kernel/datatable/module.php:84` |
| `datatable/connections` | – | `kernel/datatable/module.php:86` |
| `datatable/sync` | – | `kernel/datatable/module.php:88` |
| `exphelp/read` | – | `kernel/exphelp/module.php:151` |
| `exphelp/context` | – | `kernel/exphelp/module.php:153` |
| `ezinfo/read` | – | `kernel/ezinfo/module.php:33` |
| `infocollector/read` | – | `kernel/infocollector/module.php:50` |
| `mailpreferences/administrate` | – | `kernel/mailpreferences/module.php:72` |
| `mailpreferences/export` | – | `kernel/mailpreferences/module.php:73` |
| `notification/use` | – | `kernel/notification/module.php:53` |
| `notification/administrate` | – | `kernel/notification/module.php:54` |
| `package/read` | Type | `kernel/package/module.php:179` |
| `package/list` | Type | `kernel/package/module.php:180` |
| `package/create` | Type, CreatorType, Role | `kernel/package/module.php:181` |
| `package/edit` | Type | `kernel/package/module.php:184` |
| `package/remove` | Type | `kernel/package/module.php:185` |
| `package/install` | Type | `kernel/package/module.php:186` |
| `package/import` | Type | `kernel/package/module.php:187` |
| `package/export` | Type | `kernel/package/module.php:188` |
| `package/export_extension` | – | `kernel/package/module.php:191` |
| `pdf/create` | – | `kernel/pdf/module.php:60` |
| `pdf/edit` | – | `kernel/pdf/module.php:61` |
| `rss/feed` | – | `kernel/rss/module.php:62` |
| `rss/edit` | – | `kernel/rss/module.php:63` |
| `secrets/manage` | – | `kernel/secrets/module.php:28` |
| `section/assign` | Class, Section, Owner, NewSection | `kernel/section/module.php:79` |
| `section/edit` | – | `kernel/section/module.php:83` |
| `section/view` | – | `kernel/section/module.php:84` |
| `setup/administrate` | – | `kernel/setup/module.php:485` |
| `setup/install` | – | `kernel/setup/module.php:486` |
| `setup/logs` | – | `kernel/setup/module.php:488` |
| `setup/managelogs` | – | `kernel/setup/module.php:489` |
| `setup/unmasklogs` | – | `kernel/setup/module.php:490` |
| `setup/managecache` | – | `kernel/setup/module.php:491` |
| `setup/managecronjobs` | – | `kernel/setup/module.php:492` |
| `setup/preload` | – | `kernel/setup/module.php:493` |
| `setup/setup` | – | `kernel/setup/module.php:494` |
| `setup/system_info` | – | `kernel/setup/module.php:495` |
| `shop/setup` | – | `kernel/shop/module.php:350` |
| `shop/administrate` | – | `kernel/shop/module.php:351` |
| `shop/buy` | – | `kernel/shop/module.php:352` |
| `shop/edit_status` | – | `kernel/shop/module.php:353` |
| `shop/setstatus` | FromStatus, ToStatus | `kernel/shop/module.php:354` |
| `state/administrate` | – | `kernel/state/module.php:132` |
| `state/assign` | Class, Section, Owner, Group, Node, Subtree | `kernel/state/module.php:134` |
| `user/login` | SiteAccess | `kernel/user/module.php:204` |
| `user/password` | – | `kernel/user/module.php:205` |
| `user/preferences` | – | `kernel/user/module.php:206` |
| `user/register` | – | `kernel/user/module.php:207` |
| `user/selfedit` | – | `kernel/user/module.php:208` |
| `user/activation` | – | `kernel/user/module.php:209` |
| `user/hygiene` | – | `kernel/user/module.php:210` |
| `user/deactivate` | – | `kernel/user/module.php:211` |
| `user/sessions` | – | `kernel/user/module.php:213` |

### kernel/private (1)

| Function | Limitations | Defined in |
|---|---|---|
| `oauth/authorize` | – | `kernel/private/modules/oauth/module.php:29` |
