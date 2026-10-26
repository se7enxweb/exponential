---
title: Value object / struct
audience: developer
category: standards
order: 334
summary: A small object that carries data and has no identity of its own. 106 instances in Exponential.
keywords: design pattern, value-object, structure, pattern catalogue
type: catalogue
---
# Value object / struct

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A small object that carries data and has no identity of its own.

| | |
|---|---|
| Group | Structure |
| Instances | 106 |
| Recognized by | Class name ends in Struct, Value, Result, Item, Info or Definition and the class has few methods, or extends ezcBaseStruct. |

## Classes by area

### extension/AdminAid (1)

[All patterns of extension/AdminAid](area-extension-adminaid.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezAdminAidInfo` | class | `extension/AdminAid/ezinfo.php:27` | – | – |

### extension/autorss (1)

[All patterns of extension/autorss](area-extension-autorss.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `autorssInfo` | class | `extension/autorss/ezinfo.php:3` | – | – |

### extension/bccie (1)

[All patterns of extension/bccie](area-extension-bccie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `bccieInfo` | class | `extension/bccie/ezinfo.php:10` | – | – |

### extension/bcgooglesitemaps (1)

[All patterns of extension/bcgooglesitemaps](area-extension-bcgooglesitemaps.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `bcGoogleSitemapsInfo` | class | `extension/bcgooglesitemaps/ezinfo.php:11` | – | – |

### extension/bcwebsitestatistics (1)

[All patterns of extension/bcwebsitestatistics](area-extension-bcwebsitestatistics.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `bcwebsitestatisticsInfo` | class | `extension/bcwebsitestatistics/ezinfo.php:3` | – | – |

### extension/birthday (1)

[All patterns of extension/birthday](area-extension-birthday.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `birthdayInfo` | class | `extension/birthday/ezinfo.php:6` | The extension's information for the about page and the upgrade checks. | – |

### extension/cjw_newsletter (1)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjw_newsletterInfo` | class | `extension/cjw_newsletter/ezinfo.php:16` | The name, version, copyright, license and project page of the extension, shown on the about page. | – |

### extension/cjw_themes_jumper (1)

[All patterns of extension/cjw_themes_jumper](area-extension-cjw-themes-jumper.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjw_themes_jumperInfo` | class | `extension/cjw_themes_jumper/ezinfo.php:7` | – | – |

### extension/cjw_tmv (1)

[All patterns of extension/cjw_tmv](area-extension-cjw-tmv.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjw_tmvInfo` | class | `extension/cjw_tmv/ezinfo.php:7` | – | – |

### extension/enhancedezbinaryfile (1)

[All patterns of extension/enhancedezbinaryfile](area-extension-enhancedezbinaryfile.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `enhancedezbinaryfileInfo` | class | `extension/enhancedezbinaryfile/ezinfo.php:40` | – | – |

### extension/enhancedselection2 (1)

[All patterns of extension/enhancedselection2](area-extension-enhancedselection2.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `enhancedselection2Info` | class | `extension/enhancedselection2/ezinfo.php:14` | Enhanced Selection extension | – |

### extension/expai (2)

[All patterns of extension/expai](area-extension-expai.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expaiInfo` | class | `extension/expai/ezinfo.php:7` | – | – |
| `expAiToolGetClassDefinition` | class extends expAiTool | `extension/expai/classes/tools/expaitoolgetclassdefinition.php:9` | get_class_definition: the fields of a content class | extension/expai/settings/expai.ini [Tool_get_class_definition] Class |

### extension/expchangeclass (1)

[All patterns of extension/expchangeclass](area-extension-expchangeclass.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expchangeclassInfo` | class | `extension/expchangeclass/ezinfo.php:14` | – | – |

### extension/exphelpsite (1)

[All patterns of extension/exphelpsite](area-extension-exphelpsite.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `exphelpsiteInfo` | class | `extension/exphelpsite/ezinfo.php:11` | The extension exphelpsite: the node templates of help maintained as content for a site's public pages (doc/eng-us/features/6.0/help-content.md, "Help… | – |

### extension/expimageeditor (1)

[All patterns of extension/expimageeditor](area-extension-expimageeditor.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expimageeditorInfo` | class | `extension/expimageeditor/ezinfo.php:9` | – | – |

### extension/explayouts (2)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsExpItem` | class | `extension/explayouts/classes/explayoutsexpitem.php:4` | – | – |
| `explayoutsInfo` | class | `extension/explayouts/ezinfo.php:12` | – | – |

### extension/explayouts_api (1)

[All patterns of extension/explayouts_api](area-extension-explayouts-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `explayouts_apiInfo` | class | `extension/explayouts_api/ezinfo.php:12` | – | – |

### extension/explayouts_content_browser (2)

[All patterns of extension/explayouts_content_browser](area-extension-explayouts-content-browser.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsContentBrowserItem` | class | `extension/explayouts_content_browser/classes/explayoutscontentbrowseritem.php:2` | – | – |
| `explayouts_content_browserInfo` | class | `extension/explayouts_content_browser/ezinfo.php:12` | – | – |

### extension/explayouts_content_browser_core (1)

[All patterns of extension/explayouts_content_browser_core](area-extension-explayouts-content-browser-core.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `explayouts_content_browser_coreInfo` | class | `extension/explayouts_content_browser_core/ezinfo.php:12` | – | – |

### extension/explayouts_content_browser_ui (1)

[All patterns of extension/explayouts_content_browser_ui](area-extension-explayouts-content-browser-ui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `explayouts_content_browser_uiInfo` | class | `extension/explayouts_content_browser_ui/ezinfo.php:12` | – | – |

### extension/explayouts_core (1)

[All patterns of extension/explayouts_core](area-extension-explayouts-core.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `explayouts_coreInfo` | class | `extension/explayouts_core/ezinfo.php:12` | – | – |

### extension/explayouts_relation_list_query (1)

[All patterns of extension/explayouts_relation_list_query](area-extension-explayouts-relation-list-query.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `explayouts_relation_list_queryInfo` | class | `extension/explayouts_relation_list_query/ezinfo.php:12` | – | – |

### extension/explayouts_site_api (1)

[All patterns of extension/explayouts_site_api](area-extension-explayouts-site-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `explayouts_site_apiInfo` | class | `extension/explayouts_site_api/ezinfo.php:12` | – | – |

### extension/explayouts_standard (1)

[All patterns of extension/explayouts_standard](area-extension-explayouts-standard.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `explayouts_standardInfo` | class | `extension/explayouts_standard/ezinfo.php:12` | – | – |

### extension/explayouts_tags_query (1)

[All patterns of extension/explayouts_tags_query](area-extension-explayouts-tags-query.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `explayouts_tags_queryInfo` | class | `extension/explayouts_tags_query/ezinfo.php:12` | – | – |

### extension/explayouts_ui (1)

[All patterns of extension/explayouts_ui](area-extension-explayouts-ui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `explayouts_uiInfo` | class | `extension/explayouts_ui/ezinfo.php:12` | – | – |

### extension/explayouts_ui_api (1)

[All patterns of extension/explayouts_ui_api](area-extension-explayouts-ui-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `explayouts_ui_apiInfo` | class | `extension/explayouts_ui_api/ezinfo.php:12` | – | – |

### extension/expquery_translator (1)

[All patterns of extension/expquery_translator](area-extension-expquery-translator.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expquery_translatorInfo` | class | `extension/expquery_translator/ezinfo.php:12` | – | – |

### extension/expqueue (1)

[All patterns of extension/expqueue](area-extension-expqueue.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expqueueInfo` | class | `extension/expqueue/ezinfo.php:7` | – | – |

### extension/expservices (1)

[All patterns of extension/expservices](area-extension-expservices.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expservicesInfo` | class | `extension/expservices/ezinfo.php:7` | – | – |

### extension/expsite_api (1)

[All patterns of extension/expsite_api](area-extension-expsite-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expsite_apiInfo` | class | `extension/expsite_api/ezinfo.php:12` | – | – |

### extension/expsite_app (1)

[All patterns of extension/expsite_app](area-extension-expsite-app.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expsite_appInfo` | class | `extension/expsite_app/ezinfo.php:12` | – | – |

### extension/expsite_core (1)

[All patterns of extension/expsite_core](area-extension-expsite-core.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expsite_coreInfo` | class | `extension/expsite_core/ezinfo.php:12` | – | – |

### extension/expsite_data_media (1)

[All patterns of extension/expsite_data_media](area-extension-expsite-data-media.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expsite_data_mediaInfo` | class | `extension/expsite_data_media/ezinfo.php:12` | – | – |

### extension/expsite_installer (1)

[All patterns of extension/expsite_installer](area-extension-expsite-installer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expsite_installerInfo` | class | `extension/expsite_installer/ezinfo.php:12` | – | – |

### extension/expui (1)

[All patterns of extension/expui](area-extension-expui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expuiInfo` | class | `extension/expui/ezinfo.php:7` | – | – |

### extension/exp_adminui (1)

[All patterns of extension/exp_adminui](area-extension-exp-adminui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `exp_adminuiInfo` | class | `extension/exp_adminui/ezinfo.php:7` | – | – |

### extension/exp_enhanced_link (1)

[All patterns of extension/exp_enhanced_link](area-extension-exp-enhanced-link.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `exp_enhanced_linkInfo` | class | `extension/exp_enhanced_link/ezinfo.php:12` | – | – |

### extension/exp_oe_tiptap (1)

[All patterns of extension/exp_oe_tiptap](area-extension-exp-oe-tiptap.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `exp_oe_tiptapInfo` | class | `extension/exp_oe_tiptap/ezinfo.php:7` | – | – |

### extension/ezauthorize (1)

[All patterns of extension/ezauthorize](area-extension-ezauthorize.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZAuthorizeInfo` | class | `extension/ezauthorize/classes/ezauthorizeinfo.php:11` | – | – |

### extension/ezautosave (1)

[All patterns of extension/ezautosave](area-extension-ezautosave.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezautosaveInfo` | class | `extension/ezautosave/ezinfo.php:7` | – | – |

### extension/ezdemo (1)

[All patterns of extension/ezdemo](area-extension-ezdemo.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezdemoInfo` | class | `extension/ezdemo/ezinfo.php:12` | – | – |

### extension/ezfind (2)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezfindInfo` | class | `extension/ezfind/ezinfo.php:7` | – | – |
| `ezfSearchResultInfo` | class | `extension/ezfind/classes/ezfsearchresultinfo.php:12` | ezfSearchResultInfo contains additional search result information. | – |

### extension/ezflow (1)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezflowInfo` | class | `extension/ezflow/ezinfo.php:12` | – | – |

### extension/ezgmaplocation (1)

[All patterns of extension/ezgmaplocation](area-extension-ezgmaplocation.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezgmaplocationInfo` | class | `extension/ezgmaplocation/ezinfo.php:7` | – | – |

### extension/ezie (1)

[All patterns of extension/ezie](area-extension-ezie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezieInfo` | class | `extension/ezie/ezinfo.php:11` | – | – |

### extension/ezjscore (1)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscoreInfo` | class | `extension/ezjscore/ezinfo.php:7` | – | – |

### extension/ezmbpaex (1)

[All patterns of extension/ezmbpaex](area-extension-ezmbpaex.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezmbpaexInfo` | class | `extension/ezmbpaex/ezinfo.php:7` | – | – |

### extension/ezmultiupload (1)

[All patterns of extension/ezmultiupload](area-extension-ezmultiupload.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezmultiuploadInfo` | class | `extension/ezmultiupload/ezinfo.php:7` | – | – |

### extension/ezodf (1)

[All patterns of extension/ezodf](area-extension-ezodf.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezodfInfo` | class | `extension/ezodf/ezinfo.php:12` | – | – |

### extension/ezoracle (1)

[All patterns of extension/ezoracle](area-extension-ezoracle.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezoracleInfo` | class | `extension/ezoracle/ezinfo.php:11` | – | – |

### extension/ezownerchange (1)

[All patterns of extension/ezownerchange](area-extension-ezownerchange.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezownerchangeInfo` | class | `extension/ezownerchange/ezinfo.php:10` | The about information of ezownerchange (ezinfo/about). | – |

### extension/ezpaypal (1)

[All patterns of extension/ezpaypal](area-extension-ezpaypal.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpaypalInfo` | class | `extension/ezpaypal/ezinfo.php:2` | – | – |

### extension/ezpm (1)

[All patterns of extension/ezpm](area-extension-ezpm.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpmInfo` | class | `extension/ezpm/ezinfo.php:25` | – | – |

### extension/ezprestapi (1)

[All patterns of extension/ezprestapi](area-extension-ezprestapi.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezprestapiInfo` | class | `extension/ezprestapi/ezinfo.php:10` | – | – |

### extension/ezssp (1)

[All patterns of extension/ezssp](area-extension-ezssp.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezsspInfo` | class | `extension/ezssp/ezinfo.php:2` | – | – |

### extension/ezstarrating (1)

[All patterns of extension/ezstarrating](area-extension-ezstarrating.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezstarratingInfo` | class | `extension/ezstarrating/ezinfo.php:7` | – | – |

### extension/eztags (1)

[All patterns of extension/eztags](area-extension-eztags.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eztagsInfo` | class | `extension/eztags/ezinfo.php:11` | – | – |

### extension/ezupdate (1)

[All patterns of extension/ezupdate](area-extension-ezupdate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezupdateInfo` | class | `extension/ezupdate/ezinfo.php:7` | – | – |

### extension/ezurlmultisite (1)

[All patterns of extension/ezurlmultisite](area-extension-ezurlmultisite.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezurlmultisiteInfo` | class | `extension/ezurlmultisite/ezinfo.php:10` | – | – |

### extension/ezwebin (1)

[All patterns of extension/ezwebin](area-extension-ezwebin.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezwebinInfo` | class | `extension/ezwebin/ezinfo.php:12` | – | – |

### extension/ezwt (1)

[All patterns of extension/ezwt](area-extension-ezwt.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezwtInfo` | class | `extension/ezwt/ezinfo.php:7` | – | – |

### extension/git_manager (2)

[All patterns of extension/git_manager](area-extension-git-manager.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Extension\GitManager\BackupInfo` | class extends Command | `extension/git_manager/classes/runnable/commands/php_backup-info.php:28` | The code of extension/git_manager/bin/php/backup-info.php, moved into a class (#207 stage 1). | – |
| `git_managerInfo` | class | `extension/git_manager/ezinfo.php:9` | – | – |

### extension/hcaptcha (1)

[All patterns of extension/hcaptcha](area-extension-hcaptcha.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `hcaptchaInfo` | class | `extension/hcaptcha/ezinfo.php:22` | hCaptcha extension for Exponential Written by 7x Based on works by: Bruce Morrison | – |

### extension/ngclasslist (1)

[All patterns of extension/ngclasslist](area-extension-ngclasslist.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ngclasslistInfo` | class | `extension/ngclasslist/ezinfo.php:3` | – | – |

### extension/nxc_powercontent (1)

[All patterns of extension/nxc_powercontent](area-extension-nxc-powercontent.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `nxc_powercontentInfo` | class | `extension/nxc_powercontent/ezinfo.php:8` | – | – |

### extension/owsimpleoperator (1)

[All patterns of extension/owsimpleoperator](area-extension-owsimpleoperator.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `owSimpleOperatorInfo` | class | `extension/owsimpleoperator/ezinfo.php:11` | – | – |

### extension/powercontent (1)

[All patterns of extension/powercontent](area-extension-powercontent.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `powercontentInfo` | class | `extension/powercontent/ezinfo.php:2` | – | – |

### extension/recaptcha (1)

[All patterns of extension/recaptcha](area-extension-recaptcha.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `recaptchaInfo` | class | `extension/recaptcha/ezinfo.php:22` | reCAPTCHA extension for Exponential Written by Bruce Morrison | – |

### extension/sevenx_authentication_2fa (1)

[All patterns of extension/sevenx_authentication_2fa](area-extension-sevenx-authentication-2fa.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sevenx_authentication_2faInfo` | class | `extension/sevenx_authentication_2fa/ezinfo.php:12` | – | – |

### extension/sevenx_dse (9)

[All patterns of extension/sevenx_dse](area-extension-sevenx-dse.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `AdminNeo\ClickHouseResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/clickhouse.inc.php:130` | – | – |
| `AdminNeo\ElasticResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/elastic.inc.php:123` | – | – |
| `AdminNeo\MongoResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/mongo.inc.php:114` | – | – |
| `AdminNeo\MySqlResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/mysql.inc.php:145` | – | – |
| `AdminNeo\PgSqlResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/pgsql.inc.php:149` | – | – |
| `AdminNeo\Result` | abstract class | `extension/sevenx_dse/adminneo/core/Result.php:7` | – | – |
| `AdminNeo\SimpleDbResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/simpledb.inc.php:96` | – | – |
| `AdminNeo\SqLiteResult` | class extends Result | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:65` | – | – |
| `sevenx_dseInfo` | class | `extension/sevenx_dse/ezinfo.php:7` | – | – |

### extension/sevenx_themes_media (1)

[All patterns of extension/sevenx_themes_media](area-extension-sevenx-themes-media.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sevenx_themes_mediaInfo` | class | `extension/sevenx_themes_media/ezinfo.php:13` | – | – |

### extension/sevenx_themes_simple (1)

[All patterns of extension/sevenx_themes_simple](area-extension-sevenx-themes-simple.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sevenx_themes_simpleInfo` | class | `extension/sevenx_themes_simple/ezinfo.php:11` | – | – |

### extension/sevenx_themes_super (1)

[All patterns of extension/sevenx_themes_super](area-extension-sevenx-themes-super.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `SevenxthemessuperInfo` | class | `extension/sevenx_themes_super/ezinfo.php:13` | What the admin interface reads about this extension. | – |

### extension/sqliimport (1)

[All patterns of extension/sqliimport](area-extension-sqliimport.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sqliimportInfo` | class | `extension/sqliimport/ezinfo.php:7` | – | – |

### extension/str_replace (1)

[All patterns of extension/str_replace](area-extension-str-replace.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `str_replaceInfo` | class | `extension/str_replace/ezinfo.php:2` | – | – |

### extension/swark (1)

[All patterns of extension/swark](area-extension-swark.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `SwarkInfo` | class | `extension/swark/ezinfo.php:22` | – | – |

### extension/syndication (1)

[All patterns of extension/syndication](area-extension-syndication.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `syndicationInfo` | class | `extension/syndication/ezinfo.php:42` | – | – |

### extension/xrowextract (1)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `xrowextractInfo` | class | `extension/xrowextract/ezinfo.php:10` | – | – |

### extension/xrowmetadata (10)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `xrowMetaData` | class extends ezcBaseStruct | `extension/xrowmetadata/classes/structs/xrowmetadata.php:3` | – | – |
| `xrowmetadataInfo` | class | `extension/xrowmetadata/ezinfo.php:8` | – | – |
| `xrowSitemapItem` | abstract class extends ezcBaseStruct | `extension/xrowmetadata/classes/structs/xrowsitemapitem.php:3` | – | – |
| `xrowSitemapItemAlternateLink` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapitemalternatelink.php:3` | – | – |
| `xrowSitemapItemFrequency` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapitemfrequency.php:3` | – | – |
| `xrowSitemapItemImage` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapitemimage.php:3` | – | – |
| `xrowSitemapItemModified` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapitemmodified.php:3` | – | – |
| `xrowSitemapItemNews` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapnewsitem.php:3` | – | – |
| `xrowSitemapItemPriority` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapitempriority.php:3` | – | – |
| `xrowSitemapItemVideo` | class extends xrowSitemapItem | `extension/xrowmetadata/classes/structs/xrowsitemapvideoitem.php:3` | – | – |

### kernel/private (2)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpAutoloadFileFindContext` | class extends ezcBaseStruct | `kernel/private/structs/ezpautoloadfilefindcontext.php:17` | Struct which defines the information collected by the file walker for locating files. | – |
| `ezpRestMvcResult` | class extends ezcMvcResult | `kernel/private/rest/classes/response/result.php:15` | This struct contains the result data to be returned by eZP REST controllers | – |

### kernel/private/classes/views (1)

[All patterns of kernel/private/classes/views](area-kernel-private-classes-views.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Kernel\Collaboration\Item` | class extends ModuleView | `kernel/private/classes/views/collaboration/item.php:21` | The code of kernel/collaboration/item.php, moved into a class (#207 stage 1). | – |

### lib/ezi18n (1)

[All patterns of lib/ezi18n](area-lib-ezi18n.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCharsetInfo` | class | `lib/ezi18n/classes/ezcharsetinfo.php:25` | – | – |
