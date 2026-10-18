---
title: INI plugin point (extension point)
audience: developer
category: standards
order: 338
summary: A setting that names the class the kernel uses for a job, so an extension or a site can replace or add one without changing code. 947 instances in Exponential.
keywords: design pattern, ini-plugin-point, plugins, pattern catalogue
type: catalogue
---
# INI plugin point (extension point)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A setting that names the class the kernel uses for a job, so an extension or a site can replace or add one without changing code.

| | |
|---|---|
| Group | Plugins |
| Instances | 947 |
| Recognized by | A setting of a settings/*.ini file (or an extension's) whose value is the name of a class of the catalogue. |
| Extension point | Set the setting in an extension's settings/FILE.ini.append.php or in settings/override/. |

## Classes by area

### extension/bccie (25)

[All patterns of extension/bccie](area-extension-bccie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `bccieExportFormatOutputHandlerCP1252` | class extends bccieExportFormatOutputHandler | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerCP1252.php:11` | – | extension/bccie/settings/cie.ini [CieSettings] ExportOutputFormatHandlers[cp1252] |
| `bccieExportFormatOutputHandlerUtf8` | class extends bccieExportFormatOutputHandler | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8.php:11` | – | extension/bccie/settings/cie.ini [CieSettings] ExportOutputFormatHandlers[utf8] |
| `bccieExportFormatOutputHandlerUtf8Bom` | class extends bccieExportFormatOutputHandler | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8Bom.php:11` | – | extension/bccie/settings/cie.ini [CieSettings] ExportOutputFormatHandlers[utf8bom] |
| `bccieExportFormatOutputHandlerUtf16Le` | class extends bccieExportFormatOutputHandler | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf16Le.php:11` | – | extension/bccie/settings/cie.ini [CieSettings] ExportOutputFormatHandlers[utf16le] |
| `eZBirthdayExportHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbirthdayexporthandler.php:13` | – | extension/bccie/settings/export.ini [ezbirthday] HandlerClass |
| `eZBirthdayHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbirthdayhandler.php:11` | – | extension/bccie/settings/export.ini [ezbirthday] HandlerClass |
| `eZBooleanHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbooleanhandler.php:11` | – | extension/bccie/settings/export.ini [ezboolean] HandlerClass |
| `eZCountryHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezcountrtyhandler.php:11` | – | extension/bccie/settings/export.ini [ezcountry] HandlerClass |
| `eZDateHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezdatehandler.php:11` | – | extension/bccie/settings/export.ini [ezdate] HandlerClass |
| `eZDateTimeHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezdatetimehandler.php:11` | – | extension/bccie/settings/export.ini [ezdatetime] HandlerClass |
| `eZEmailHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezemailhandler.php:11` | – | extension/bccie/settings/export.ini [ezemail] HandlerClass |
| `ezenhancedobjectrelationHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezenhancedobjectrelationhandler.php:11` | – | extension/bccie/settings/export.ini [ezenhancedobjectrelation] HandlerClass |
| `eZFloatHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezfloathandler.php:11` | – | extension/bccie/settings/export.ini [ezfloat] HandlerClass |
| `eZIdentifierHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezidentifierhandler.php:11` | – | extension/bccie/settings/export.ini [ezidentifier] HandlerClass |
| `eZIntegerHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezintegerhandler.php:11` | – | extension/bccie/settings/export.ini [ezinteger] HandlerClass |
| `eZObjectRelationHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezobjectrelationhandler.php:11` | – | extension/bccie/settings/export.ini [ezobjectrelation] HandlerClass |
| `eZOptionHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezoptionhandler.php:11` | – | extension/bccie/settings/export.ini [ezoption] HandlerClass |
| `eZSelectionHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezselectionhandler.php:11` | – | extension/bccie/settings/export.ini [ezselection] HandlerClass |
| `eZStringHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezstringhandler.php:11` | – | extension/bccie/settings/export.ini [ezstring] HandlerClass |
| `eZTextHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/eztexthandler.php:11` | – | extension/bccie/settings/export.ini [eztext] HandlerClass |
| `eZTimeHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/eztimehandler.php:11` | – | extension/bccie/settings/export.ini [eztime] HandlerClass |
| `eZURLHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezurlhandler.php:11` | – | extension/bccie/settings/export.ini [ezurl] HandlerClass |
| `eZXMLTextHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezxmltexthandler.php:11` | – | extension/bccie/settings/export.ini [ezxmltext] HandlerClass |
| `OWEnhancedSelectionHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/owenhancedselectionhandler.php:11` | – | extension/bccie/settings/export.ini [owenhancedselection] HandlerClass |
| `smileobjectrelationlistHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/smileobjectrelationlisthandler.php:11` | – | extension/bccie/settings/export.ini [smileobjectrelationlist] HandlerClass |

### extension/birthday (1)

[All patterns of extension/birthday](area-extension-birthday.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZBirthdayCsvHandler` | class extends XrowBaseHandler | `extension/birthday/classes/parsers/ezbirthdaycsvhandler.php:29` | CSV export handler for the ezbirthday datatype. | extension/birthday/settings/csv.ini [ezbirthday] HandlerClass |

### extension/cjw_newsletter (20)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjwNewsletterAuditBranch` | class | `extension/cjw_newsletter/classes/cjwnewsletterauditbranch.php:12` | The newsletter's branch of the audit taxonomy, registered in settings/audit.ini.append.php ([AuditEventSettings] Branches[cjw_newsletter]). | extension/cjw_newsletter/settings/audit.ini [AuditEventSettings] Branches[cjw_newsletter] |
| `CjwNewsletterDeliverabilityHooks` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterdeliverabilityhooks.php:25` | The extension point handler of the area "deliverability" (cjw_newsletter.ini [ExtensionPointSettings] Handlers[]=CjwNewsletterDeliverabilityHooks, se… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterEditionFilter` | class | `extension/cjw_newsletter/extendedattributefilter/cjwnewslettereditionfilter.php:32` | This filter allowed, fetch all lists by a specific status status-variants: -›DRAFT -›PROCESS | extension/cjw_newsletter/settings/extendedattributefilter.ini [CjwNewsletterEditionFilter] ClassName |
| `CjwNewsletterEditorialHooks` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditorialhooks.php:20` | The extension point handler of the area Editorial (registered in cjw_newsletter.ini [ExtensionPointSettings] Handlers[]): the recurring sends run at… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterFilterTypeEmail` | class extends CjwNewsletterFilterType | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypeemail.php:19` | Class description here | extension/cjw_newsletter/settings/cjw_newsletter.ini [NewsletterFilterSettings] AvailableFilterTypeClassArray[] |
| `CjwNewsletterFilterTypeSalutation` | class extends CjwNewsletterFilterType | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypesalutation.php:19` | Class description here | extension/cjw_newsletter/settings/cjw_newsletter.ini [NewsletterFilterSettings] AvailableFilterTypeClassArray[] |
| `CjwNewsletterImportExportHooks` | class | `extension/cjw_newsletter/classes/importexport/cjwnewsletterimportexporthooks.php:18` | The extension point handler of the area import/export and migration ([ExtensionPointSettings] Handlers[]). | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterListFilter` | class | `extension/cjw_newsletter/extendedattributefilter/cjwnewsletterlistfilter.php:29` | This filter allowed, fetch all lists by a specific siteaccess reserved word "current_siteaccess" == take current siteaccess to match param siteaccess… | extension/cjw_newsletter/settings/extendedattributefilter.ini [CjwNewsletterListFilter] ClassName |
| `CjwNewsletterMailCategoryHandler` | class | `extension/cjw_newsletter/classes/cjwnewslettermailcategoryhandler.php:20` | The handler of the category "newsletter" of the e-mail preferences (Exponential 6.0.15 and later; settings/mailpreferences.ini.append.php of this ext… | extension/cjw_newsletter/settings/mailpreferences.ini [Category_newsletter] HandlerClass |
| `CjwNewsletterMailin` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailin.php:36` | Subscribe and unsubscribe by e-mail (cjw_newsletter.ini [MailInSettings], the addresses in cjwnl_mailin_address, every message handled in cjwnl_maili… | extension/cjw_newsletter/settings/mailpreferences.ini [BounceSettings] MessageListeners[] |
| `CjwNewsletterMailPreferences` | class | `extension/cjw_newsletter/classes/cjwnewslettermailpreferences.php:25` | The bridge between the newsletter and the e-mail preferences of Exponential 6.0.15 and later (kernel/classes/mailpreferences). | extension/cjw_newsletter/settings/mailpreferences.ini [SuppressionSettings] Listeners[] |
| `CjwNewsletterRenderingHooks` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterrenderinghooks.php:25` | The extension point handler of the rendering (area N3, cjw_newsletter.ini [ExtensionPointSettings] Handlers[]). | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterScheduleConditionNewArticles` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterscheduleconditionnewarticles.php:18` | The run goes ahead only when the article pool of the schedule has content published since its last send (the anonymous user's view of it), in either… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ScheduleSettings] ConditionHandlers[] |
| `CjwNewsletterSmsCategoryHandler` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmscategoryhandler.php:25` | The handler of the kernel mail-preference category "sms" ([Category_sms] HandlerClass in settings/mailpreferences.ini.append.php of the extension). | extension/cjw_newsletter/settings/mailpreferences.ini [Category_sms] HandlerClass |
| `CjwNewsletterSmsHooks` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmshooks.php:27` | The handler of area N5 (SMS) at the extension points (CjwNewsletterExtensionPoints), registered in cjw_newsletter.ini [ExtensionPointSettings] Handle… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterSmsTransportFile` | class extends CjwNewsletterSmsTransportBase | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransportfile.php:23` | The simulated SMS sending: nothing leaves the server, every SMS is written as one JSON file -.sms.json (to, from, text, segments, time) into Dir. | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_file] Class |
| `CjwNewsletterSmsTransportHttp` | class extends CjwNewsletterSmsTransportBase | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransporthttp.php:38` | The generic SMS transport: one HTTP request per SMS to any provider API, mapped in the INI group. | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_http] Class |
| `CjwNewsletterSmsTransportTwilio` | class extends CjwNewsletterSmsTransportHttp | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransporttwilio.php:29` | A preset of the generic HTTP transport for APIs in the style of Twilio's Messages resource: a form POST to .../Accounts/{AccountSid}/Messages.json wi… | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_twilio] Class |
| `CjwNewsletterStatisticsCategoryHandler` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticscategoryhandler.php:23` | The handler of the e-mail preference category "newsletter_statistics" (cjw_newsletter 4.2.0, area N4 Statistics): the consent to per-person counting… | extension/cjw_newsletter/settings/mailpreferences.ini [Category_newsletter_statistics] HandlerClass |
| `CjwNewsletterStatisticsHooks` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticshooks.php:31` | The extension point handler of area N4 Statistics ([ExtensionPointSettings] Handlers[]=CjwNewsletterStatisticsHooks). | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |

### extension/cjw_themes_jumper (1)

[All patterns of extension/cjw_themes_jumper](area-extension-cjw-themes-jumper.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjwJumperImagineImageFactory` | class extends eZImageFactory | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagefactory.php:10` | Image handler factory of the converter cjwImagine (image.ini [cjwImagine] Handler=cjwJumperImagineImageFactory): produces cjwJumperImagineImageHandle… | extension/cjw_themes_jumper/settings/image.ini [cjwImagine] Handler |

### extension/cjw_tmv (1)

[All patterns of extension/cjw_tmv](area-extension-cjw-tmv.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjwTmvEventsBlockHandler` | class | `extension/cjw_tmv/classes/cjwtmveventsblockhandler.php:11` | The Layouts block "tmv_events" (v2: Cjw\TmvBundle TmvEventsHandler): the events of a tmv_container, with the filter toolbar (view type list), only th… | extension/cjw_tmv/settings/siteaccess/cjw_starter/explayouts.ini [BlockDefinition_tmv_events] Handler |

### extension/enhancedselection2 (1)

[All patterns of extension/enhancedselection2](area-extension-enhancedselection2.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezfSolrDocumentFieldSckEnhancedSelection` | class extends ezfSolrDocumentFieldBase | `extension/enhancedselection2/classes/ezfsolrdocumentfieldsckenhancedselection.php:2` | – | extension/enhancedselection2/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[sckenhancedselection] |

### extension/expchangeclass (1)

[All patterns of extension/expchangeclass](area-extension-expchangeclass.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expChangeClassConverters` | class | `extension/expchangeclass/classes/converters.php:21` | – | extension/expchangeclass/settings/changeclass.ini [ezobjectrelationlist] Class; extension/expchangeclass/settings/changeclass.ini [ezstring] Class; extension/expchangeclass/settings/changeclass.ini [eztext] Class |

### extension/explayouts (46)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsAccordionBlockHandler` | class | `extension/explayouts/classes/explayoutsaccordionblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_accordion] Handler |
| `expLayoutsAdminLeftMenuBlockHandler` | class | `extension/explayouts/classes/explayoutsadminleftmenublockhandler.php:7` | The left menu block of the admin layouts. | extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_left_menu] Handler |
| `expLayoutsAdminPartBlockHandler` | class | `extension/explayouts/classes/explayoutsadminpartblockhandler.php:7` | The handler of the admin blocks that take no parameter (design admin4l): the block shows one part of the admin page, drawn by its view template desig… | extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_bookmarks] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_breadcrumb] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_clear_cache] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_content_tree] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_current_user] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_debug_area] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_footer] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_logo] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_menu_button] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_module_result] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_overlay] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_popup_menu] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_preferences] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_quick_settings] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_search] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_sidebar_controls] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_sidebar_toggles] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_tab_menu] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_theme_switch] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_toolbar_developer] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_toolbar_right] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_ui_switch] Handler |
| `expLayoutsAjaxLoadMoreServer` | class | `extension/explayouts/classes/explayoutsajaxloadmoreserver.php:7` | ezjscServer functions for loading paged collection blocks. | extension/explayouts/settings/ezjscore.ini [ezjscServer_expajaxloadmore] Class; extension/sevenx_themes_media/settings/siteaccess/site/ezjscore.ini [ezjscServer_expajaxloadmore] Class |
| `expLayoutsAlertBlockHandler` | class | `extension/explayouts/classes/explayoutsalertblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_alert] Handler |
| `expLayoutsBadgeBlockHandler` | class | `extension/explayouts/classes/explayoutsbadgeblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_badge] Handler |
| `expLayoutsButtonBlockHandler` | class | `extension/explayouts/classes/explayoutsbuttonblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_button] Handler |
| `expLayoutsCardBlockHandler` | class | `extension/explayouts/classes/explayoutscardblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_card] Handler |
| `expLayoutsCarouselBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | `extension/explayouts/classes/explayoutscarouselblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_carousel] Handler |
| `expLayoutsChildrenQueryHandler` | class | `extension/explayouts/classes/explayoutschildrenqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_children] Handler |
| `expLayoutsComponentBlockHandler` | class | `extension/explayouts/classes/explayoutscomponentblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_about] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_features] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_hero] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_lead] Handler |
| `expLayoutsContainerBlockHandler` | class | `extension/explayouts/classes/explayoutscontainerblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_column] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_four_columns] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_three_columns] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_two_columns] Handler |
| `expLayoutsContentByTopicQueryHandler` | class | `extension/explayouts/classes/explayoutscontentbytopicqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_content_by_topic] Handler |
| `expLayoutsContentComponentBlockHandler` | class | `extension/explayouts/classes/explayoutscontentcomponentblockhandler.php:17` | Handler for the content-backed component blocks (hero, features, about, logos, quote, lead). | extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_about] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_features] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_hero] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_lead] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_logos] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_quote] Handler |
| `expLayoutsDebugBarPanel` | class | `extension/explayouts/classes/explayoutsdebugbarpanel.php:16` | The Exp Layouts panel of the Exp Debug bar: which layout built the page, which rule chose it, its zones and blocks with links to edit them in the adm… | extension/explayouts/settings/debugbar.ini [Panel_explayouts] Class |
| `expLayoutsDividerBlockHandler` | class | `extension/explayouts/classes/explayoutsdividerblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_divider] Handler |
| `expLayoutsExponentialContentSearchQueryHandler` | class | `extension/explayouts/classes/explayoutsexponentialcontentsearchqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exponential_content_search] Handler |
| `expLayoutsFullViewBlockHandler` | class | `extension/explayouts/classes/explayoutsfullviewblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_full_view] Handler |
| `expLayoutsGalleryBlockHandler` | class | `extension/explayouts/classes/explayoutsgalleryblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_gallery] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_grid_gallery] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_logos] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_slider] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_sushi_bar] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_thumb_gallery] Handler |
| `expLayoutsGridBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | `extension/explayouts/classes/explayoutsgridblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_grid] Handler |
| `expLayoutsHtmlBlockHandler` | class | `extension/explayouts/classes/explayoutshtmlblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_html] Handler |
| `expLayoutsImageBlockHandler` | class | `extension/explayouts/classes/explayoutsimageblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_image] Handler |
| `expLayoutsLatestQueryHandler` | class | `extension/explayouts/classes/explayoutslatestqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_latest] Handler |
| `expLayoutsListBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | `extension/explayouts/classes/explayoutslistblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_list] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_list_accordion] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_list_zigzag] Handler; extension/sevenx_themes_media/settings/explayouts.ini [BlockDefinition_product_highlight] Handler |
| `expLayoutsManualQueryHandler` | class | `extension/explayouts/classes/explayoutsmanualqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_manual] Handler |
| `expLayoutsMapBlockHandler` | class | `extension/explayouts/classes/explayoutsmapblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_map] Handler |
| `expLayoutsMarkdownBlockHandler` | class | `extension/explayouts/classes/explayoutsmarkdownblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_markdown] Handler |
| `expLayoutsParentQueryHandler` | class | `extension/explayouts/classes/explayoutsparentqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_parent] Handler |
| `expLayoutsProgressBlockHandler` | class | `extension/explayouts/classes/explayoutsprogressblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_progress] Handler |
| `expLayoutsQuoteBlockHandler` | class | `extension/explayouts/classes/explayoutsquoteblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_quote] Handler |
| `expLayoutsRandomQueryHandler` | class | `extension/explayouts/classes/explayoutsrandomqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_random] Handler |
| `expLayoutsRelationListQueryHandler` | class | `extension/explayouts/classes/explayoutsrelationlistqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_relation_list] Handler |
| `expLayoutsReverseRelationListQueryHandler` | class | `extension/explayouts/classes/explayoutsreverserelationlistqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_reverse_relation_list] Handler |
| `expLayoutsReverseRelationProvider` | class | `extension/explayouts/classes/explayoutsreverserelationprovider.php:15` | The Layouts blocks that name content a removal or a hide takes away, for the kernel's reverse relation check (content.ini [ReverseRelationSettings] P… | extension/explayouts/settings/content.ini [ReverseRelationSettings] ProviderClassList[] |
| `expLayoutsRichTextBlockHandler` | class | `extension/explayouts/classes/explayoutsrichtextblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_rich_text] Handler |
| `expLayoutsSiblingsQueryHandler` | class | `extension/explayouts/classes/explayoutssiblingsqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_siblings] Handler |
| `expLayoutsSingleBlockHandler` | class | `extension/explayouts/classes/explayoutssingleblockhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_single] Handler |
| `expLayoutsSpacerBlockHandler` | class | `extension/explayouts/classes/explayoutsspacerblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_spacer] Handler |
| `expLayoutsSubtreeQueryHandler` | class | `extension/explayouts/classes/explayoutssubtreequeryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_subtree] Handler |
| `expLayoutsTabsBlockHandler` | class | `extension/explayouts/classes/explayoutstabsblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_tabs] Handler |
| `expLayoutsTagsQueryHandler` | class | `extension/explayouts/classes/explayoutstagsqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_tags] Handler |
| `expLayoutsTextBlockHandler` | class | `extension/explayouts/classes/explayoutstextblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_text] Handler |
| `expLayoutsTitleBlockHandler` | class | `extension/explayouts/classes/explayoutstitleblockhandler.php:2` | – | extension/cjw_themes_jumper/settings/siteaccess/cjw_starter/explayouts.ini [BlockDefinition_ezcontent_field] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_title] Handler |
| `expLayoutsTplBlockHandler` | class | `extension/explayouts/classes/explayoutstplblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_tpl_block] Handler |
| `expLayoutsVideoBlockHandler` | class | `extension/explayouts/classes/explayoutsvideoblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_video] Handler |
| `ExplBlockXHTMLXMLOutput` | class extends eZXHTMLXMLOutput | `extension/explayouts/classes/explblockxhtmlxmloutput.php:10` | Rich text output handler that escapes the expl: marker sequence. | extension/explayouts/settings/ezxml.ini [OutputSettings] HandlerClass |

### extension/explayouts_core (1)

[All patterns of extension/explayouts_core](area-extension-explayouts-core.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsCoreAuditBranch` | class | `extension/explayouts_core/classes/explayoutscoreauditbranch.php:11` | explayouts_core's branch of the audit taxonomy, registered in settings/audit.ini.append.php ([AuditEventSettings] Branches[explayouts_core]). | extension/explayouts_core/settings/audit.ini [AuditEventSettings] Branches[explayouts_core] |

### extension/expservices (69)

[All patterns of extension/expservices](area-extension-expservices.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAccountServices` | class extends expUsersBase | `extension/expservices/classes/users/expaccountservices.php:13` | expaccount: the account flows of a person: registration and activation, forgotten password and reset, changing one's own password and e-mail, and the… | extension/expservices/settings/ezjscore.ini [ezjscServer_expaccount] Class |
| `expAttributeServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expattributeservices.php:14` | Attribute services: ezjscore/call/expattribute::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expattribute] Class |
| `expAuditServices` | class extends expServiceBase | `extension/expservices/classes/misc/expauditservices.php:12` | ezjscore/call/expaudit:: - the audit system, read only (policy audit/read, narrowed to the channels of its Channel limitation): events, one event, re… | extension/expservices/settings/ezjscore.ini [ezjscServer_expaudit] Class |
| `expBasketServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expbasketservices.php:12` | ezjscore/call/expbasket::: the basket of the current session and the checkout steps. | extension/expservices/settings/ezjscore.ini [ezjscServer_expbasket] Class |
| `expBookmarkServices` | class extends expUsersBase | `extension/expservices/classes/users/expbookmarkservices.php:12` | expbookmark: the bookmarks of the current user in their tree of virtual folders. | extension/expservices/settings/ezjscore.ini [ezjscServer_expbookmark] Class |
| `expCacheServices` | class extends expServiceBase | `extension/expservices/classes/expcacheservices.php:10` | expcache: the caches (setup/managecache): what exists, their state, and clearing them. | extension/expservices/settings/ezjscore.ini [ezjscServer_expcache] Class |
| `expClassGroupServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expclassgroupservices.php:13` | Class group services: ezjscore/call/expclassgroup::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expclassgroup] Class |
| `expClassServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expclassservices.php:15` | Class services: ezjscore/call/expclass::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expclass] Class |
| `expCollaborationServices` | class extends expUsersBase | `extension/expservices/classes/users/expcollaborationservices.php:12` | expcollaboration: the collaboration items of the current user (approvals and other handler items), their messages, participants and groups, and the a… | extension/expservices/settings/ezjscore.ini [ezjscServer_expcollaboration] Class |
| `expCommentServices` | class extends expCommunityBase | `extension/expservices/classes/community/expcommentservices.php:12` | ezjscore/call/expcomment::: comments on any content (content objects of the comment class below the commented node) and the reviews of products (the… | extension/expservices/settings/ezjscore.ini [ezjscServer_expcomment] Class |
| `expContentJobServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expcontentjobservices.php:15` | Content job services: ezjscore/call/expcontentjob::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expcontentjob] Class |
| `expCronjobServices` | class extends expServiceBase | `extension/expservices/classes/expcronjobservices.php:10` | expcronjob: the cronjob parts and scripts (setup/managecronjobs), read-only. | extension/expservices/settings/ezjscore.ini [ezjscServer_expcronjob] Class |
| `expCurrencyServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expcurrencyservices.php:11` | ezjscore/call/expcurrency::: the currencies of the shop with their rates, conversion, the preferred currency of the user. | extension/expservices/settings/ezjscore.ini [ezjscServer_expcurrency] Class |
| `expDebugServices` | class extends expServiceBase | `extension/expservices/classes/expdebugservices.php:10` | expdebug: the summary of the request's debug output (as the Exp Debug bar), settings read-only. | extension/expservices/settings/ezjscore.ini [ezjscServer_expdebug] Class |
| `expDesignServices` | class extends expServiceBase | `extension/expservices/classes/misc/expdesignservices.php:12` | ezjscore/call/expdesign:: - designs, templates and template overrides (read only, policy setup/administrate): which designs exist and are in use, the… | extension/expservices/settings/ezjscore.ini [ezjscServer_expdesign] Class |
| `expDiscountServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expdiscountservices.php:12` | ezjscore/call/expdiscount::: discount groups (rules), their rules (percent with class, section or product limits) and the users and user groups that… | extension/expservices/settings/ezjscore.ini [ezjscServer_expdiscount] Class |
| `expEditorServices` | class extends expServiceBase | `extension/expservices/classes/expeditorservices.php:12` | expeditor: the online editor (ezoe) for remote clients: the registered editor engines, the engine of the current user, the file types the editor's up… | extension/expservices/settings/ezjscore.ini [ezjscServer_expeditor] Class |
| `expExtensionServices` | class extends expServiceBase | `extension/expservices/classes/expextensionservices.php:10` | expextension: the extensions of the installation, read-only. | extension/expservices/settings/ezjscore.ini [ezjscServer_expextension] Class |
| `expFeedServices` | class extends expServiceBase | `extension/expservices/classes/community/expfeedservices.php:13` | ezjscore/call/expfeed::: feeds. | extension/expservices/settings/ezjscore.ini [ezjscServer_expfeed] Class |
| `expFileServices` | class extends expAttrServiceBase | `extension/expservices/classes/media/expfileservices.php:11` | ezjscore/call/expfile:: - binary files: information, download URLs (content/download, which checks content/read itself), signed time limited links, l… | extension/expservices/settings/ezjscore.ini [ezjscServer_expfile] Class |
| `expForumServices` | class extends expCommunityBase | `extension/expservices/classes/community/expforumservices.php:12` | ezjscore/call/expforum::: forums (content objects of the forum class, kept in forum containers). | extension/expservices/settings/ezjscore.ini [ezjscServer_expforum] Class |
| `expImageServices` | class extends expAttrServiceBase | `extension/expservices/classes/media/expimageservices.php:11` | ezjscore/call/expimage:: - images and their aliases (image.ini), per node and attribute. | extension/expservices/settings/ezjscore.ini [ezjscServer_expimage] Class |
| `expInfoCollectionServices` | class extends expServiceBase | `extension/expservices/classes/community/expinfocollectionservices.php:13` | ezjscore/call/expinfocollection::: collected forms (information collection): the forms that have collections, the collections and their values, expor… | extension/expservices/settings/ezjscore.ini [ezjscServer_expinfocollection] Class |
| `expIniServices` | class extends expServiceBase | `extension/expservices/classes/expiniservices.php:11` | expini: reads the settings (INI files), read-only; secrets are masked as exp:ini masks them. | extension/expservices/settings/ezjscore.ini [ezjscServer_expini] Class |
| `expLanguageServices` | class extends expServiceBase | `extension/expservices/classes/misc/explanguageservices.php:12` | ezjscore/call/explanguage:: - content languages, locales and translation files: the languages of the installation, the priority list of this siteacce… | extension/expservices/settings/ezjscore.ini [ezjscServer_explanguage] Class |
| `expLayoutServices` | class extends expServiceBase | `extension/expservices/classes/misc/explayoutservices.php:12` | ezjscore/call/explayout:: - the layouts of the explayouts extension: layouts, zones, blocks, rules, the layout resolved for a node, and drafts (creat… | extension/expservices/settings/ezjscore.ini [ezjscServer_explayout] Class |
| `expLocationServices` | class extends expContentServiceBase | `extension/expservices/classes/content/explocationservices.php:13` | Location services: ezjscore/call/explocation::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_explocation] Class |
| `expMediaServices` | class extends expAttrServiceBase | `extension/expservices/classes/media/expmediaservices.php:11` | ezjscore/call/expmedia:: - audio and video media files (the ezmedia datatype): player settings, file information, URLs, listings and statistics. | extension/expservices/settings/ezjscore.ini [ezjscServer_expmedia] Class |
| `expNewsletterServices` | class extends expServiceBase | `extension/expservices/classes/misc/expnewsletterservices.php:13` | ezjscore/call/expnewsletter:: - the cjw_newsletter extension: lists, editions, subscriber counts, and a user's own subscriptions (subscribe and unsub… | extension/expservices/settings/ezjscore.ini [ezjscServer_expnewsletter] Class |
| `expNodeServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expnodeservices.php:15` | Node services: ezjscore/call/expnode::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expnode] Class |
| `expNotificationServices` | class extends expUsersBase | `extension/expservices/classes/users/expnotificationservices.php:12` | expnotification: the notification subscriptions of the current user (subtree rules, the collaboration types, the digest settings) and the notificatio… | extension/expservices/settings/ezjscore.ini [ezjscServer_expnotification] Class |
| `expObjectServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expobjectservices.php:14` | Object services: ezjscore/call/expobject::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expobject] Class |
| `expOrderServices` | class extends expServiceBase | `extension/expservices/classes/commerce/exporderservices.php:12` | ezjscore/call/exporder::: the orders. | extension/expservices/settings/ezjscore.ini [ezjscServer_exporder] Class |
| `expPackageServices` | class extends expServiceBase | `extension/expservices/classes/exppackageservices.php:10` | exppackage: the packages (package/list, package/read), read-only. | extension/expservices/settings/ezjscore.ini [ezjscServer_exppackage] Class |
| `expPaymentServices` | class extends expServiceBase | `extension/expservices/classes/commerce/exppaymentservices.php:13` | ezjscore/call/exppayment::: the payments of the shop: the payment objects the gateways create, their status by order, the gateways and payment workfl… | extension/expservices/settings/ezjscore.ini [ezjscServer_exppayment] Class |
| `expPdfServices` | class extends expAttrServiceBase | `extension/expservices/classes/misc/exppdfservices.php:12` | ezjscore/call/exppdf:: - PDF output: whether a node can be exported (content/pdf policy), the link that produces the PDF of a node, and the PDF expor… | extension/expservices/settings/ezjscore.ini [ezjscServer_exppdf] Class |
| `expPolicyServices` | class extends expUsersBase | `extension/expservices/classes/users/exppolicyservices.php:11` | exppolicy: policies of roles and their limitations, and the modules and functions a policy can name. | extension/expservices/settings/ezjscore.ini [ezjscServer_exppolicy] Class |
| `expPollServices` | class extends expCommunityBase | `extension/expservices/classes/community/exppollservices.php:12` | ezjscore/call/exppoll::: polls (content objects of the poll class with an option attribute that collects information): list, results, vote. | extension/expservices/settings/ezjscore.ini [ezjscServer_exppoll] Class |
| `expPreferencesServices` | class extends expUsersBase | `extension/expservices/classes/users/exppreferencesservices.php:11` | exppreferences: the preferences of the current user (the key/value store the admin uses for the tree state, page sizes, ...). | extension/expservices/settings/ezjscore.ini [ezjscServer_exppreferences] Class |
| `expProductServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expproductservices.php:11` | ezjscore/call/expproduct::: the products of the shop (content objects whose class has a price attribute): lists, search, one product with its prices… | extension/expservices/settings/ezjscore.ini [ezjscServer_expproduct] Class |
| `expRadServices` | class extends expServiceBase | `extension/expservices/classes/expradservices.php:10` | exprad: the extension point survey (setup/rad), summary level, read-only. | extension/expservices/settings/ezjscore.ini [ezjscServer_exprad] Class |
| `expRelationServices` | class extends expContentServiceBase | `extension/expservices/classes/content/exprelationservices.php:13` | Relation services: ezjscore/call/exprelation::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_exprelation] Class |
| `expReplyServices` | class extends expCommunityBase | `extension/expservices/classes/community/expreplyservices.php:11` | ezjscore/call/expreply::: the replies of a topic (content objects of the reply class below a topic). | extension/expservices/settings/ezjscore.ini [ezjscServer_expreply] Class |
| `expRoleServices` | class extends expUsersBase | `extension/expservices/classes/users/exproleservices.php:13` | exprole: roles, their editing drafts and their assignments to users and groups. | extension/expservices/settings/ezjscore.ini [ezjscServer_exprole] Class |
| `expSearchServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expsearchservices.php:14` | Search services: ezjscore/call/expsearch::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expsearch] Class |
| `expSectionServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expsectionservices.php:13` | Section services: ezjscore/call/expsection::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expsection] Class |
| `expServicesAuditBranch` | class | `extension/expservices/classes/expservicesauditbranch.php:10` | The audit taxonomy branch of expservices: personal API tokens. | extension/expservices/settings/audit.ini [AuditEventSettings] Branches[expservices] |
| `expServicesCatalog` | class extends expServiceBase | `extension/expservices/classes/expservicescatalog.php:12` | ezjscore/call/expservices::catalog \| schema:: \| version \| domains \| service:::: The discovery of the API: built from the $services of every class reg… | extension/expservices/settings/ezjscore.ini [ezjscServer_expservices] Class |
| `expSessionAdminServices` | class extends expUsersBase | `extension/expservices/classes/users/expsessionadminservices.php:12` | expsessionadmin: the sessions of the installation. | extension/expservices/settings/ezjscore.ini [ezjscServer_expsessionadmin] Class |
| `expSessionServices` | class extends expServiceBase | `extension/expservices/classes/expsessionservices.php:11` | expsession: the session login of remote apps. | extension/expservices/settings/ezjscore.ini [ezjscServer_expsession] Class |
| `expShippingServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expshippingservices.php:11` | ezjscore/call/expshipping::: what the shop knows about shipping: the handler, the simple shipping workflow cost, the shipping info of a basket or an… | extension/expservices/settings/ezjscore.ini [ezjscServer_expshipping] Class |
| `expSitemapServices` | class extends expAttrServiceBase | `extension/expservices/classes/misc/expsitemapservices.php:12` | ezjscore/call/expsitemap:: - sitemap data: the entries of a subtree as a sitemap lists them (location, last modification, change frequency, priority)… | extension/expservices/settings/ezjscore.ini [ezjscServer_expsitemap] Class |
| `expStateServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expstateservices.php:14` | Object state services: ezjscore/call/expstate::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expstate] Class |
| `expStatsServices` | class extends expAttrServiceBase | `extension/expservices/classes/misc/expstatsservices.php:11` | ezjscore/call/expstats:: - statistics: search phrases, content counts, growth, languages, users and sessions. | extension/expservices/settings/ezjscore.ini [ezjscServer_expstats] Class |
| `expSubitemsServices` | class extends expServiceBase | `extension/expservices/classes/misc/expsubitemsservices.php:13` | ezjscore/call/expsubitems:: - the subitems column catalogue and rows (the admin's "Table options"), reusing expSubitemsColumnRegistry, expSubitemsSer… | extension/expservices/settings/ezjscore.ini [ezjscServer_expsubitems_svc] Class |
| `expSystemServices` | class extends expServiceBase | `extension/expservices/classes/expsystemservices.php:10` | expsystem: what the installation is and runs on. | extension/expservices/settings/ezjscore.ini [ezjscServer_expsystem] Class |
| `expTagsServices` | class extends expAttrServiceBase | `extension/expservices/classes/media/exptagsservices.php:13` | ezjscore/call/exptags:: - the tag tree of the eztags extension: tree, search, children, synonyms, translations, related content, and writes (add, ren… | extension/expservices/settings/ezjscore.ini [ezjscServer_exptags] Class |
| `expTopicServices` | class extends expCommunityBase | `extension/expservices/classes/community/exptopicservices.php:12` | ezjscore/call/exptopic::: the topics of a forum (content objects of the topic class). | extension/expservices/settings/ezjscore.ini [ezjscServer_exptopic] Class |
| `expTranslationServices` | class extends expContentServiceBase | `extension/expservices/classes/content/exptranslationservices.php:14` | Translation services: ezjscore/call/exptranslation::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_exptranslation] Class |
| `expTrashServices` | class extends expContentServiceBase | `extension/expservices/classes/content/exptrashservices.php:13` | Trash services: ezjscore/call/exptrash::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_exptrash] Class |
| `expUrlAliasServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expurlaliasservices.php:14` | URL alias and wildcard services: ezjscore/call/expurlalias::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expurlalias] Class |
| `expUrlServices` | class extends expServiceBase | `extension/expservices/classes/misc/expurlservices.php:12` | ezjscore/call/expurl:: - the external links stored by URL attributes (ezurl): lists, search, validity, the objects that use a link, and a check of a… | extension/expservices/settings/ezjscore.ini [ezjscServer_expurl] Class |
| `expUserGroupServices` | class extends expUsersBase | `extension/expservices/classes/users/expusergroupservices.php:10` | expusergroup: the user group tree and its members. | extension/expservices/settings/ezjscore.ini [ezjscServer_expusergroup] Class |
| `expUserServices` | class extends expUsersBase | `extension/expservices/classes/users/expuserservices.php:13` | expuser: look up, list, search and administer users. | extension/expservices/settings/ezjscore.ini [ezjscServer_expuser] Class |
| `expVatServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expvatservices.php:13` | ezjscore/call/expvat::: VAT types, VAT charging rules by country and product category, the product categories the rules use, and the VAT of a product… | extension/expservices/settings/ezjscore.ini [ezjscServer_expvat] Class |
| `expVelocityServices` | class extends expServiceBase | `extension/expservices/classes/expvelocityservices.php:10` | expvelocity: the Velocity server's state, read-only (setup/system_info). | extension/expservices/settings/ezjscore.ini [ezjscServer_expvelocity] Class |
| `expVersionServices` | class extends expContentServiceBase | `extension/expservices/classes/content/expversionservices.php:13` | Version services: ezjscore/call/expversion::[::arg...] | extension/expservices/settings/ezjscore.ini [ezjscServer_expversion] Class |
| `expWishlistServices` | class extends expServiceBase | `extension/expservices/classes/commerce/expwishlistservices.php:11` | ezjscore/call/expwishlist::: the wish list of the logged-in user, and for shop administrators the wish lists of all users. | extension/expservices/settings/ezjscore.ini [ezjscServer_expwishlist] Class |
| `expWorkflowServices` | class extends expServiceBase | `extension/expservices/classes/expworkflowservices.php:10` | expworkflow: workflows, their events, triggers and processes, read-only (setup/administrate). | extension/expservices/settings/ezjscore.ini [ezjscServer_expworkflow] Class |

### extension/expui (1)

[All patterns of extension/expui](area-extension-expui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expUIServerFunctions` | class | `extension/expui/classes/expuiserverfunctions.php:28` | – | extension/expui/settings/ezjscore.ini [ezjscServer_exp] Class |

### extension/exp_oe_tiptap (3)

[All patterns of extension/exp_oe_tiptap](area-extension-exp-oe-tiptap.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOETiptapEngine` | class extends expOEEditorEngineBase | `extension/exp_oe_tiptap/classes/expoetiptapengine.php:16` | Tiptap as an engine of ezoe's editor registry: registered by settings/ezoe.ini.append.php (ezoe.ini [EditorSettings] Engines[tiptap]=expOETiptapEngin… | extension/exp_oe_tiptap/settings/ezoe.ini [EditorSettings] Engines[tiptap] |
| `expOETiptapServerFunctions` | class extends ezjscServerFunctions | `extension/exp_oe_tiptap/classes/expoetiptapserverfunctions.php:29` | ezjscore server functions of the Tiptap editor (ezjscore.ini [ezjscServer_expoetiptap]). | extension/exp_oe_tiptap/settings/ezjscore.ini [ezjscServer_expoetiptap] Class |
| `expOETiptapXMLInput` | class extends eZOEXMLInput | `extension/exp_oe_tiptap/classes/expoetiptapxmlinput.php:20` | The ezxmltext input handler of exp_oe_tiptap: ezoe's eZOEXMLInput, kept for what 0.1.0 put in place. | extension/exp_oe_tiptap/settings/ezxml.ini [InputSettings] AliasClasses[eZSimplifiedXMLInput] |

### extension/ezautosave (1)

[All patterns of extension/ezautosave](area-extension-ezautosave.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscServerFunctionsAutosave` | class extends ezjscServerFunctions | `extension/ezautosave/classes/ezjscserverfunctionsautosave.php:16` | This class handles AJAX calls for the autosave feature | extension/ezautosave/settings/ezjscore.ini [ezjscServer_ezautosave] Class |

### extension/ezfind (6)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFindAllWordsExtendedAttributeFilter` | class | `extension/ezfind/classes/extendedattributefilters/ezfindallwordsextendedattributefilter.php:25` | eZFindAllWordsExtendedAttributeFilter | extension/ezfind/settings/ezfind.ini [ExtendedAttributeFilters] FiltersList[ezfindallwords] |
| `eZFindGeoDistExtendedAttributeFilter` | class | `extension/ezfind/classes/extendedattributefilters/ezfindgeodistextendedattributefilter.php:28` | SolR Geodist extended attribute filter | extension/ezfind/settings/ezfind.ini [ExtendedAttributeFilters] FiltersList[geodist] |
| `eZFindServerCallFunctions` | class | `extension/ezfind/classes/ezfindservercallfunctions.php:7` | Implements methods called remotely by sending XHR calls | extension/ezfind/settings/ezjscore.ini [ezjscServer_ezfind] Class |
| `ezfSolrDocumentFieldDummyExample` | class extends ezfSolrDocumentFieldBase | `extension/ezfind/classes/ezfsolrdocumentfielddummyexample.php:42` | This class is provided as an example on how to write a datatype-specific indexing and querying handler. | extension/ezfind/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[dummy_example] |
| `ezfSolrDocumentFieldObjectRelation` | class extends ezfSolrDocumentFieldBase | `extension/ezfind/classes/ezfsolrdocumentfieldobjectrelation.php:14` | The ezfSolrDocumentFieldObjectRelation class handles indexing and querying for the ezobjectrelation and ezobjectrelationlist in eZFind. | extension/ezfind/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[ezobjectrelation]; extension/ezfind/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[ezobjectrelationlist] |
| `ezfSolrDocumentFieldXML` | class extends ezfSolrDocumentFieldBase | `extension/ezfind/classes/ezfsolrdocumentfieldxml.php:8` | – | extension/ezfind/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[ezmatrix]; extension/ezfind/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[ezxmltext] |

### extension/ezflow (6)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFlowKeywordsFetch` | class | `extension/ezflow/classes/fetches/ezflowkeywordsfetch.php:28` | – | extension/ezflow/settings/block.ini [Keyword] FetchClass |
| `eZFlowLatestContent` | class | `extension/ezflow/classes/fetches/ezflowlatestcontent.php:28` | – | extension/ezflow/settings/block.ini [LatestContent] FetchClass |
| `eZFlowLatestObjects` | class | `extension/ezflow/classes/fetches/ezflowlatestobjects.php:28` | – | extension/ezflow/settings/block.ini [Dynamic3Items] FetchClass |
| `eZFlowMCFetch` | class | `extension/ezflow/classes/fetches/ezflowmcfetch.php:3` | – | extension/ezflow/settings/block.ini [MultimediaCarousel] FetchClass |
| `eZFlowServerCallFunctions` | class | `extension/ezflow/classes/ezflowservercallfunctions.php:7` | Implements methods called remotely by sending XHR calls | extension/ezflow/settings/ezjscore.ini [ezjscServer_ezflow] Class |
| `eZSquidCacheManager` | class | `extension/ezflow/classes/ezsquidcachemanager.php:30` | – | extension/ezflow/settings/http.ini [HTTPCacheHandlers] Handlers[] |

### extension/ezgmaplocation (1)

[All patterns of extension/ezgmaplocation](area-extension-ezgmaplocation.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezgmlLocationFilter` | class | `extension/ezgmaplocation/classes/ezgmllocationfilter.php:32` | – | extension/ezgmaplocation/settings/extendedattributefilter.ini [ezgmlLocationFilter] ClassName |

### extension/ezjscore (9)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscCacheManager` | class | `extension/ezjscore/classes/ezjsccachemanager.php:11` | – | extension/ezjscore/settings/site.ini [Cache_ezjscore] class |
| `ezjscCssOptimizer` | class | `extension/ezjscore/classes/ezjsccssoptimizer.php:32` | – | extension/ezjscore/settings/ezjscore.ini [eZJSCore] CssOptimizer[]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [eZJSCore] CssOptimizer[]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [eZJSCore] CssOptimizer[] |
| `ezjscJavascriptOptimizer` | class | `extension/ezjscore/classes/ezjscjavascriptoptimizer.php:32` | – | extension/ezjscore/settings/ezjscore.ini [eZJSCore] JavaScriptOptimizer[]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [eZJSCore] JavaScriptOptimizer[]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [eZJSCore] JavaScriptOptimizer[] |
| `ezjscServerFunctionsAjaxUploader` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsajaxuploader.php:20` | This class handles AJAX calls for the ajax uploader component | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezajaxuploader] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezajaxuploader] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezajaxuploader] Class |
| `ezjscServerFunctionsJs` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsjs.php:36` | – | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezjsc] Class |
| `ezjscServerFunctionsNode` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsnode.php:36` | ezjscServerFunctionsNode class definition that provide node fetch functions | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjscnode] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezjscnode] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezjscnode] Class |
| `ezjscServerFunctionsPublishingQueue` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionspublishingqueue.php:18` | This class handles AJAX calls for the publishing queue system | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezpublishingqueue] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezpublishingqueue] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezpublishingqueue] Class |
| `ezpRelationAjaxUploader` | class extends ezpRelationListAjaxUploader | `extension/ezjscore/classes/ajaxuploader/ezprelationajaxuploader.php:19` | This class handles AJAX Upload for eZObjectRelation attributes | extension/ezjscore/settings/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation] |
| `ezpRelationListAjaxUploader` | class | `extension/ezjscore/classes/ajaxuploader/ezprelationlistajaxuploader.php:19` | This class handles AJAX Upload for eZObjectRelationList attributes | extension/ezjscore/settings/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist] |

### extension/ezodf (1)

[All patterns of extension/ezodf](area-extension-ezodf.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZRESTODFHandler` | class extends eZRESTBaseHandler | `extension/ezodf/classes/ezrestodfhandler.php:46` | – | extension/ezodf/settings/ezrest.ini [RESTSettings] HandlerList[] |

### extension/ezoe (7)

[All patterns of extension/ezoe](area-extension-ezoe.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOEAuditBranch` | class | `extension/ezoe/classes/expoeauditbranch.php:11` | The audit taxonomy branch of the online editor: the choice of the editor engine. | extension/ezoe/settings/audit.ini [AuditEventSettings] Branches[ezoe] |
| `expOEEditorEngine` | interface | `extension/ezoe/classes/expoeeditorengine.php:21` | An editor engine of the online editor: the thing that turns an ezxmltext attribute into an editor in the browser. | extension/ezoe/settings/rad.ini [Registry_ezoeengines] Entries[Engines] |
| `expOETinyMCE3Engine` | class extends expOEEditorEngineBase | `extension/ezoe/classes/expoetinymce3engine.php:10` | The default engine: eZ Online Editor on TinyMCE 3.5, rendered by ezxmltext_ezoe.tpl itself. | extension/ezoe/settings/ezoe.ini [EditorSettings] Engines[tinymce3] |
| `expOETinyMCE8Engine` | class extends expOEEditorEngineBase | `extension/ezoe/classes/expoetinymce8engine.php:10` | The opt-in engine: eZ Online Editor on TinyMCE 8 (GPL), rendered by ezxmltext_ezoe_tinymce8.tpl. | extension/ezoe/settings/ezoe.ini [EditorSettings] Engines[tinymce8] |
| `ezoeServerFunctions` | class extends ezjscServerFunctions | `extension/ezoe/classes/ezoeserverfunctions.php:35` | – | extension/ezoe/settings/ezjscore.ini [ezjscServer_ezoe] Class |
| `eZOEXMLInput` | class extends eZXMLInputHandler | `extension/ezoe/ezxmltext/handlers/input/ezoexmlinput.php:40` | – | extension/ezoe/settings/ezxml.ini [InputSettings] AliasClasses[eZSimplifiedXMLInput] |
| `GoogleSpell` | class extends SpellChecker | `extension/ezoe/modules/ezoe/classes/GoogleSpell.php:9` | – | extension/ezoe/settings/ezoe.ini [SpellChecker] config[general.engine] |

### extension/ezownerchange (1)

[All patterns of extension/ezownerchange](area-extension-ezownerchange.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOwnerChangeAuditBranch` | class | `extension/ezownerchange/classes/expownerchangeauditbranch.php:12` | ezownerchange's branch of the audit taxonomy, registered in settings/audit.ini.append.php ([AuditEventSettings] Branches[ezownerchange]). | extension/ezownerchange/settings/audit.ini [AuditEventSettings] Branches[ezownerchange] |

### extension/ezprestapi (1)

[All patterns of extension/ezprestapi](area-extension-ezprestapi.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezp7xRestApiProvider` | class | `extension/ezprestapi/classes/rest_provider.php:25` | The routes of the ezp provider. | extension/ezprestapi/settings/rest.ini [ApiProvider] ProviderClass[ezp]; extension/ezprestapi/settings/rest.ini [ApiProvider] ProviderClass[ezpl]; extension/ezprestapiprovider/settings/rest.ini [ApiProvider] ProviderClass[ezp]; extension/ezprestapiprovider/settings/rest.ini [ApiProvider] ProviderClass[ezpl] |

### extension/ezstarrating (2)

[All patterns of extension/ezstarrating](area-extension-ezstarrating.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezsrRatingFilter` | class | `extension/ezstarrating/classes/ezsrratingfilter.php:32` | – | extension/ezstarrating/settings/extendedattributefilter.ini [ezsrRatingFilter] ClassName |
| `ezsrServerFunctions` | class extends ezjscServerFunctions | `extension/ezstarrating/classes/ezsrserverfunctions.php:36` | – | extension/ezstarrating/settings/ezjscore.ini [ezjscServer_ezstarrating] Class |

### extension/eztags (5)

[All patterns of extension/eztags](area-extension-eztags.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezfSolrDocumentFieldeZTags` | class extends ezfSolrDocumentFieldBase | `extension/eztags/classes/ezfsolrdocumentfieldeztags.php:7` | ezfSolrDocumentFieldeZTags class implements custom indexing handler for eZ Find | extension/eztags/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[eztags] |
| `ezjscTags` | class extends ezjscServerFunctions | `extension/eztags/classes/ezjsctags.php:6` | ezjscTags class implements eZ JS Core server functions for eztags | extension/eztags/settings/ezjscore.ini [ezjscServer_ezjsctags] Class |
| `ezjscTagsChildren` | class extends ezjscServerFunctions | `extension/eztags/classes/ezjsctagschildren.php:6` | ezjscTagsChildren class implements eZ JS Core server functions for eztags children list | extension/eztags/settings/ezjscore.ini [ezjscServer_ezjsctagschildren] Class |
| `eZTagsAttributeFilter` | class | `extension/eztags/classes/eztagsattributefilter.php:6` | eZTagsAttributeFilter class implements TagsAttributeFilter extended attribute | extension/eztags/settings/extendedattributefilter.ini [TagsAttributeAndFilter] ClassName; extension/eztags/settings/extendedattributefilter.ini [TagsAttributeAndMultipleFilter] ClassName; extension/eztags/settings/extendedattributefilter.ini [TagsAttributeFilter] ClassName |
| `eZTagsTreeAttributeFilter` | class | `extension/eztags/classes/eztagstreeattributefilter.php:6` | eZTagsTreeAttributeFilter class implements TagsTreeAttributeFilter extended attribute | extension/eztags/settings/extendedattributefilter.ini [TagsTreeAttributeFilter] ClassName |

### extension/ezurlmultisite (3)

[All patterns of extension/ezurlmultisite](area-extension-ezurlmultisite.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZURLMultisiteBinaryFileHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisitebinaryfilehandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezbinaryfile] |
| `eZURLMultisiteObjectRelationHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisiteobjectrelationhandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezobjectrelation] |
| `eZURLMultisiteURLHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisiteurlhandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezstring]; extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezurl] |

### extension/ezwt (1)

[All patterns of extension/ezwt](area-extension-ezwt.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezwtServerCallFunctions` | class | `extension/ezwt/classes/ezwtservercallfunctions.php:15` | Implements methods called remotely by sending XHR calls | extension/ezwt/settings/ezjscore.ini [ezjscServer_ezwt] Class |

### extension/git_manager (1)

[All patterns of extension/git_manager](area-extension-git-manager.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `gitManagerAuditBranch` | class | `extension/git_manager/classes/gitmanagerauditbranch.php:13` | git_manager's branch of the audit taxonomy, registered in settings/audit.ini.append.php ([AuditEventSettings] Branches[git_manager]). | extension/git_manager/settings/audit.ini [AuditEventSettings] Branches[git_manager] |

### extension/sevenx_themes_media (1)

[All patterns of extension/sevenx_themes_media](area-extension-sevenx-themes-media.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sevenxThemesMediaXHTMLXMLOutput` | class extends eZXHTMLXMLOutput | `extension/sevenx_themes_media/classes/sevenxthemesmediaxhtmlxmloutput.php:11` | Extends eZXHTMLXMLOutput to register the legacy XML tag names that the imported content uses (title, itemizedlist, listitem, orderedlist, literallayo… | extension/sevenx_themes_media/settings/ezxml.ini [OutputSettings] HandlerClass |

### extension/xrowextract (30)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `XrowExtractHasChildrenFilter` | class | `extension/xrowextract/classes/xrowextracthaschildrenfilter.php:11` | Extended attribute filter "XrowExtractHasChildren": only objects whose node has at least one child (leaf content otherwise). | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractHasChildren] ClassName |
| `XrowExtractRelationFilter` | class | `extension/xrowextract/classes/xrowextractrelationfilter.php:17` | Extended attribute filter "XrowExtractRelation": only objects that relate to a given object (relation_type ignored — any relation counts, including o… | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractRelation] ClassName |
| `XrowExtractTranslationFilter` | class | `extension/xrowextract/classes/xrowextracttranslationfilter.php:24` | Extended attribute filter "XrowExtractTranslation": only objects that have a translation in the given language. | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractTranslation] ClassName |
| `XrowExtractUserStatusFilter` | class | `extension/xrowextract/classes/xrowextractuserstatusfilter.php:12` | Extended attribute filter "XrowExtractUserStatus": only user objects that are enabled, or only ones that are disabled, via the standard `ezuser_setti… | extension/xrowextract/settings/extendedattributefilter.ini [XrowExtractUserStatus] ClassName |
| `XroweZBinaryfileExportHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezbinaryfilehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezbinaryfile] HandlerClass |
| `XroweZBooleanHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezbooleanhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezboolean] HandlerClass |
| `XroweZDateHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezdatehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezdate] HandlerClass |
| `XroweZDateTimeHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezdatetimehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezdatetime] HandlerClass |
| `XroweZEmailHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezemailhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezemail] HandlerClass |
| `XroweZenhancedobjectrelationHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezenhancedobjectrelationhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezenhancedobjectrelation] HandlerClass |
| `XroweZEnumHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezenumhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezenum] HandlerClass |
| `XroweZFloatHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezfloathandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezfloat] HandlerClass |
| `XroweZIdentifierHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezidentifierhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezidentifier] HandlerClass |
| `XroweZImageExportHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezimagehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezimage] HandlerClass |
| `XroweZIntegerHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezintegerhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezinteger] HandlerClass |
| `XroweZKeywordHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezkeywordhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezkeyword] HandlerClass; extension/xrowextract/settings/csv.ini [eztags] HandlerClass |
| `XroweZMatrixExportHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezmatrixhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezmatrix] HandlerClass |
| `XroweZMediaExportHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezmediahandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezmedia] HandlerClass |
| `XroweZObjectRelationHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezobjectrelationhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezobjectrelation] HandlerClass |
| `XroweZObjectRelationListHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezobjectrelationlisthandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezobjectrelationlist] HandlerClass |
| `XroweZPriceHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezpricehandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezprice] HandlerClass |
| `XroweZSelectionHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezselectionhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezcountry] HandlerClass; extension/xrowextract/settings/csv.ini [ezselection] HandlerClass |
| `XroweZStringHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezstringhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezenhancedselection] HandlerClass; extension/xrowextract/settings/csv.ini [ezstring] HandlerClass |
| `XroweZTextHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xroweztexthandler.php:3` | – | extension/xrowextract/settings/csv.ini [eztext] HandlerClass |
| `XroweZTimeHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xroweztimehandler.php:2` | – | extension/xrowextract/settings/csv.ini [eztime] HandlerClass |
| `XroweZURLHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezurlhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezurl] HandlerClass |
| `XroweZUserHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezuserhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezuser] HandlerClass |
| `XroweZXMLTextHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezxmltexthandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezxmltext] HandlerClass |
| `XrowhmregexplineHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowhmregexplinehandler.php:3` | – | extension/xrowextract/settings/csv.ini [hmregexpline] HandlerClass |
| `XrowxrowmetadataHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowxrowmetadatahandler.php:7` | xrowmetadata (SEO and sitemap meta data): the title by default. | extension/xrowextract/settings/csv.ini [xrowmetadata] HandlerClass |

### extension/xrowmetadata (1)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezfSolrDocumentFieldxrowMetadata` | class extends ezfSolrDocumentFieldBase | `extension/xrowmetadata/classes/ezfSolrDocumentFieldxrowMetadata.php:3` | – | extension/xrowmetadata/settings/ezfind.ini [SolrFieldMapSettings] CustomMap[xrowmetadata] |

### kernel/classes (13)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContentBrowseBookmark` | class extends eZPersistentObject | `kernel/classes/ezcontentbrowsebookmark.php:35` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentClass` | class extends eZPersistentObject | `kernel/classes/ezcontentclass.php:20` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentClassAttribute` | class extends eZPersistentObject | `kernel/classes/ezcontentclassattribute.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentClassEditHandler` | class | `kernel/classes/ezcontentclassedithandler.php:15` | Handler for content class editing. | settings/site.ini [ContentSettings] ContentClassEditHandler |
| `eZContentLanguage` | class extends eZPersistentObject | `kernel/classes/ezcontentlanguage.php:12` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObject` | class extends eZPersistentObject | `kernel/classes/ezcontentobject.php:17` | Encapsulates data about and methods to work with content objects | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectAttribute` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectattribute.php:20` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectTreeNode` | class extends eZPersistentObject | `kernel/classes/ezcontentobjecttreenode.php:17` | Encapsulates data about and methods to work with content objects inside the content object tree | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectVersion` | class extends eZPersistentObject | `kernel/classes/ezcontentobjectversion.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZInformationCollectionAttribute` | class extends eZPersistentObject | `kernel/classes/ezinformationcollectionattribute.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZNodeAssignment` | class extends eZPersistentObject | `kernel/classes/eznodeassignment.php:17` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZStaticCache` | class | `kernel/classes/ezstaticcache.php:30` | The eZStaticCache class manages the static cache system. | settings/site.ini [ContentSettings] StaticCacheHandler |
| `eZWorkflow` | class extends eZPersistentObject | `kernel/classes/ezworkflow.php:18` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |

### kernel/classes/audit (12)

[All patterns of kernel/classes/audit](area-kernel-classes-audit.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAuditBzip2Format` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditbzip2format.php:11` | The bzip2 archive format (PHP extension bz2): .jsonl.bz2. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[bzip2] |
| `expAuditGzipFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditgzipformat.php:11` | The gzip archive format (zlib, always available): .jsonl.gz. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[gzip] |
| `expAuditMailSink` | class extends expAuditSinkBase | `kernel/classes/audit/sinks/expauditmailsink.php:23` | The e-mail sink (doc/bc/6.0/audit.md, "E-mail"): events at MinSeverity (critical) or matching Events[] are mailed through eZMail, one mail per alert… | settings/audit.ini [AuditSinkSettings] SinkClasses[mail] |
| `expAuditMatchRule` | class extends expAuditAlertRuleBase | `kernel/classes/audit/alerts/expauditmatchrule.php:20` | The alert rule class "match" (RuleClasses[match]=expAuditMatchRule): fires for every record matching Event (and, with Policies[], only when the role… | settings/audit.ini [AuditAlertSettings] RuleClasses[match] |
| `expAuditScheduleRule` | class extends expAuditAlertRuleBase | `kernel/classes/audit/alerts/expauditschedulerule.php:15` | The alert rule class "schedule" (RuleClasses[schedule]=expAuditScheduleRule): fires for records matching Event that happen outside business hours ([A… | settings/audit.ini [AuditAlertSettings] RuleClasses[schedule] |
| `expAuditSource` | interface | `kernel/classes/audit/console/expauditsource.php:23` | An optional source of the audit console: an extension that writes its own events (usually into a channel of its own) and tells the console's views ho… | settings/rad.ini [Registry_auditsources] Entries[Sources] |
| `expAuditSyslogSink` | class extends expAuditSinkBase | `kernel/classes/audit/sinks/expauditsyslogsink.php:29` | The syslog / journald sink (doc/bc/6.0/audit.md, "Sinks", "syslog / journald"): one RFC 5424 message per record, | settings/audit.ini [AuditSinkSettings] SinkClasses[syslog] |
| `expAuditThresholdRule` | class extends expAuditAlertRuleBase | `kernel/classes/audit/alerts/expauditthresholdrule.php:16` | The alert rule class "threshold" (RuleClasses[threshold]=expAuditThresholdRule): fires when Threshold events matching Event fall within Window second… | settings/audit.ini [AuditAlertSettings] RuleClasses[threshold] |
| `expAuditWebhookSink` | class extends expAuditSinkBase | `kernel/classes/audit/sinks/expauditwebhooksink.php:20` | The webhook sink (doc/bc/6.0/audit.md, "Webhook"): POSTs batches of records as JSON, | settings/audit.ini [AuditSinkSettings] SinkClasses[webhook] |
| `expAuditXzFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditxzformat.php:11` | The xz archive format: .jsonl.xz, through the xz binary (no PHP extension is commonly available). | settings/audit.ini [AuditArchiveSettings] FormatHandlers[xz] |
| `expAuditZipFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditzipformat.php:12` | The zip archive format (PHP extension zip): .jsonl.zip, one archive per live file holding that one file, so a single file can be restored. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[zip] |
| `expAuditZstdFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditzstdformat.php:11` | The zstd archive format: .jsonl.zst, through the PHP extension zstd when it is loaded, else the zstd binary. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[zstd] |

### kernel/classes/binaryhandlers (1)

[All patterns of kernel/classes/binaryhandlers](area-kernel-classes-binaryhandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFilePassthroughHandler` | class extends eZBinaryFileHandler | `kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php:18` | – | settings/file.ini [BinaryFileSettings] Handler |

### kernel/classes/clusterfilehandlers (1)

[All patterns of kernel/classes/clusterfilehandlers](area-kernel-classes-clusterfilehandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFSFileHandler` | class | `kernel/classes/clusterfilehandlers/ezfsfilehandler.php:12` | – | settings/file.ini [ClusteringSettings] FileHandler |

### kernel/classes/contentcheck (2)

[All patterns of kernel/classes/contentcheck](area-kernel-classes-contentcheck.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentCheckFileImage` | class | `kernel/classes/contentcheck/expcontentcheckfileimage.php:13` | The files of an image field (ezimage): the original and the aliases its stored XML names (doc/features/6.0/content-checks.md, "Missing files"). | settings/contentcheck.ini [MissingFileSettings] Handlers[ezimage] |
| `expContentCheckFileTable` | class | `kernel/classes/contentcheck/expcontentcheckfiletable.php:16` | The file of a field whose datatype keeps it in a table with the columns of ezbinaryfile: contentobject_attribute_id, version, filename, original_file… | settings/contentcheck.ini [MissingFileSettings] Handlers[ezbinaryfile]; settings/contentcheck.ini [MissingFileSettings] Handlers[ezmedia] |

### kernel/classes/contentjob (11)

[All patterns of kernel/classes/contentjob](area-kernel-classes-contentjob.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentJobAddLocation` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobaddlocation.php:22` | The content job 'addlocation': gives the objects of many selected nodes a new location under one target node, per object through the kernel's own ope… | settings/content.ini [ContentJobSettings] JobTypes[addlocation] |
| `expContentJobAuditExport` | class | `kernel/classes/contentjob/expcontentjobauditexport.php:28` | The content job 'auditexport': the export of the audit console (audit/export) in the background, for a filter too large for a download in the browser… | settings/content.ini [ContentJobSettings] JobTypes[auditexport] |
| `expContentJobCopySubtree` | class | `kernel/classes/contentjob/expcontentjobcopysubtree.php:34` | The content job 'copy': copies a subtree under a new parent in batches, level by level from the top, with the semantics of content/copysubtree and bi… | settings/content.ini [ContentJobSettings] JobTypes[copy] |
| `expContentJobHideSubtree` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobhidesubtree.php:25` | The content job 'hide' (and, through expContentJobRevealSubtree, 'reveal'): hides or reveals a node and its subtree with the semantics of eZContentOb… | settings/content.ini [ContentJobSettings] JobTypes[hide] |
| `expContentJobMoveSubtree` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobmovesubtree.php:24` | The content job 'move': moves a subtree under a new parent. | settings/content.ini [ContentJobSettings] JobTypes[move] |
| `expContentJobPackageInstall` | class | `kernel/classes/contentjob/expcontentjobpackageinstall.php:26` | The content job 'packageinstall': installs a package of the package repository in the background, as the user who asked for it, with the choices made… | settings/content.ini [ContentJobSettings] JobTypes[packageinstall] |
| `expContentJobRemoveLocation` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobremovelocation.php:23` | The content job 'removelocation': removes many selected locations (nodes), keeping their objects, through the kernel's own operation, as content/acti… | settings/content.ini [ContentJobSettings] JobTypes[removelocation] |
| `expContentJobRemoveSubtree` | class | `kernel/classes/contentjob/expcontentjobremovesubtree.php:33` | The content job 'remove': removes one or more subtrees in batches, deepest nodes first (leaves before their parents), each batch through the kernel's… | settings/content.ini [ContentJobSettings] JobTypes[remove] |
| `expContentJobRevealSubtree` | class extends expContentJobHideSubtree | `kernel/classes/contentjob/expcontentjobrevealsubtree.php:16` | The content job 'reveal': reveals a hidden node and its subtree with the semantics of eZContentObjectTreeNode::unhideSubTree() (a node hidden on its… | settings/content.ini [ContentJobSettings] JobTypes[reveal] |
| `expContentJobSectionSubtree` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobsectionsubtree.php:25` | The content job 'section': assigns a section to every object of a subtree, with the semantics of eZContentObjectTreeNode::assignSectionToSubTree() (e… | settings/content.ini [ContentJobSettings] JobTypes[section] |
| `expContentJobStateSubtree` | class extends expContentJobBatchType | `kernel/classes/contentjob/expcontentjobstatesubtree.php:22` | The content job 'state': sets an object state (of one state group) on every object of a subtree, per object through the kernel's own operation, as co… | settings/content.ini [ContentJobSettings] JobTypes[state] |

### kernel/classes/datatypes (6)

[All patterns of kernel/classes/datatypes](area-kernel-classes-datatypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZImageFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezimage/ezimagefile.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZPDFXMLOutput` | class extends eZXHTMLXMLOutput | `kernel/classes/datatypes/ezxmltext/handlers/output/ezpdfxmloutput.php:12` | – | settings/ezxml.ini [OutputSettings] AliasClasses[ezpdf] |
| `eZSimplifiedXMLInput` | class extends eZXMLInputHandler | `kernel/classes/datatypes/ezxmltext/handlers/input/ezsimplifiedxmlinput.php:12` | – | settings/ezxml.ini [InputSettings] HandlerClass |
| `eZURL` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurl.php:23` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZUser` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuser.php:21` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZXHTMLXMLOutput` | class extends eZXMLOutputHandler | `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php:12` | – | settings/ezxml.ini [OutputSettings] HandlerClass |

### kernel/classes/debugbar (1)

[All patterns of kernel/classes/debugbar](area-kernel-classes-debugbar.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expDebugBarServerFunctions` | class extends ezjscServerFunctions | `kernel/classes/debugbar/expdebugbarserverfunctions.php:28` | The ezjscore server functions of the Exp Debug bar, registered as [ezjscServer_expdebugbar] in extension/ezjscore/settings/ezjscore.ini: | extension/ezjscore/settings/ezjscore.ini [ezjscServer_expdebugbar] Class |

### kernel/classes/help (1)

[All patterns of kernel/classes/help](area-kernel-classes-help.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expHelpContentProvider` | class | `kernel/classes/help/content/exphelpcontentprovider.php:19` | Help maintained as content, delivered into the help system (exphelp): the topics the current user may see, rendered with the help's Markdown renderer… | settings/exphelp.ini [ContextHelp] Providers[] |

### kernel/classes/ini (15)

[All patterns of kernel/classes/ini](area-kernel-classes-ini.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expIniActionActions` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionactions.php:14` | exp:ini actions: the actions of the command, the built-in ones and those extensions register in ini.ini [IniCommandSettings] Actions[], with their al… | settings/ini.ini [IniCommandSettings] Actions[actions] |
| `expIniActionAdd` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionadd.php:14` | exp:ini add: appends a value to an array (Variable[]=value) in one scope's file, creating the file and the block when they are missing (unless --no-c… | settings/ini.ini [IniCommandSettings] Actions[add] |
| `expIniActionClear` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionclear.php:13` | exp:ini clear: writes the array reset line Variable[] in one scope's file, so the values the files loaded before it give are dropped and only what fo… | settings/ini.ini [IniCommandSettings] Actions[clear] |
| `expIniActionCopy` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactioncopy.php:15` | exp:ini copy: copies a setting from one scope's file to another's: a plain variable or a hash entry is set to the same value, a whole array is writte… | settings/ini.ini [IniCommandSettings] Actions[copy] |
| `expIniActionGet` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionget.php:14` | exp:ini get: the value in effect (every file merged in load order, for the current siteaccess or the one of -s) or, with a scope, the value that scop… | settings/ini.ini [IniCommandSettings] Actions[get] |
| `expIniActionList` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionlist.php:14` | exp:ini list: the blocks of an INI file, or the variables of one block, in effect (every file merged, for the current siteaccess) or as one scope's f… | settings/ini.ini [IniCommandSettings] Actions[list] |
| `expIniActionMove` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionmove.php:16` | exp:ini move: moves one block (with its comments and blank lines, in order) or every block of a file from one scope to another, typically from settin… | settings/ini.ini [IniCommandSettings] Actions[move] |
| `expIniActionMoveAll` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionmoveall.php:15` | exp:ini move-all: moves every INI file of a scope (or those --files names) into another scope, block by block, one transaction per file as exp:ini mo… | settings/ini.ini [IniCommandSettings] Actions[move-all] |
| `expIniActionRem` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionrem.php:14` | exp:ini rem (alias remove): removes a variable from one scope's file, or one value of an array, or one entry of a hash. | settings/ini.ini [IniCommandSettings] Actions[rem] |
| `expIniActionScopes` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionscopes.php:14` | exp:ini scopes: the scopes this installation has (global, default, every siteaccess, every extension and the extension siteaccess directories, and th… | settings/ini.ini [IniCommandSettings] Actions[scopes] |
| `expIniActionSet` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionset.php:14` | exp:ini set: sets a plain variable (Variable=value) or a hash entry (Variable[key]=value) in one scope's file, creating the file and the block when t… | settings/ini.ini [IniCommandSettings] Actions[set] |
| `expIniActionToggle` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactiontoggle.php:14` | exp:ini toggle: flips a switch in one scope's file the way the setting writes it: enableddisabled, truefalse, yesno, onoff, 10 (the case is kept). | settings/ini.ini [IniCommandSettings] Actions[toggle] |
| `expIniActionWhere` | class extends expIniActionBase | `kernel/classes/ini/actions/expiniactionwhere.php:14` | exp:ini where: every file that sets a variable, in the order eZINI loads them (the last one wins for a plain value, arrays add up unless a file reset… | settings/ini.ini [IniCommandSettings] Actions[where] |
| `expIniCoreScopeProvider` | class | `kernel/classes/ini/expinicorescopeprovider.php:16` | global (settings/override), default (settings/, the shipped defaults, not writable by policy) and one scope per siteaccess: every directory in settin… | settings/ini.ini [IniCommandSettings] ScopeProviders[] |
| `expIniExtensionScopeProvider` | class | `kernel/classes/ini/expiniextensionscopeprovider.php:14` | extension: for every directory in extension/ (active or not; the label says which) and extension::siteaccess: for every extension//settings/siteacces… | settings/ini.ini [IniCommandSettings] ScopeProviders[] |

### kernel/classes/mailpreferences (1)

[All patterns of kernel/classes/mailpreferences](area-kernel-classes-mailpreferences.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMailPreferencesService` | class | `kernel/classes/mailpreferences/expmailpreferencesservice.php:18` | The flows around the preferences: the double opt-in mail and its confirmation, the one-click unsubscribe, "send me a link" for people without login,… | settings/mailpreferences.ini [GateSettings] EssentialSenders[] |

### kernel/classes/notification (1)

[All patterns of kernel/classes/notification](area-kernel-classes-notification.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expNotificationMailCategoryHandler` | class | `kernel/classes/notification/expnotificationmailcategoryhandler.php:25` | The categories "content" and "collaboration" of the e-mail preferences read the notification system's own data, so nothing has to be migrated and the… | settings/mailpreferences.ini [Category_collaboration] HandlerClass; settings/mailpreferences.ini [Category_content] HandlerClass |

### kernel/classes/packagecreators (4)

[All patterns of kernel/classes/packagecreators](area-kernel-classes-packagecreators.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContentClassPackageCreator` | class extends eZPackageCreationHandler | `kernel/classes/packagecreators/ezcontentclass/ezcontentclasspackagecreator.php:19` | – | settings/package.ini [CreationSettings] HandlerAlias[ezcontentclass] |
| `eZContentObjectPackageCreator` | class extends eZPackageCreationHandler | `kernel/classes/packagecreators/ezcontentobject/ezcontentobjectpackagecreator.php:18` | – | settings/package.ini [CreationSettings] HandlerAlias[ezcontentobject] |
| `eZExtensionPackageCreator` | class extends eZPackageCreationHandler | `kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php:19` | – | settings/package.ini [CreationSettings] HandlerAlias[ezextension] |
| `eZStylePackageCreator` | class extends eZPackageCreationHandler | `kernel/classes/packagecreators/ezstyle/ezstylepackagecreator.php:21` | – | settings/package.ini [CreationSettings] HandlerAlias[ezstyle] |

### kernel/classes/packagehandlers (7)

[All patterns of kernel/classes/packagehandlers](area-kernel-classes-packagehandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContentClassPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezcontentclass/ezcontentclasspackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezcontentclass] |
| `eZContentObjectPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezcontentobject/ezcontentobjectpackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezcontentobject] |
| `eZDBPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezdb/ezdbpackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezsql] |
| `eZExtensionPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezextension/ezextensionpackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezextension] |
| `eZFilePackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[ezdesign]; settings/package.ini [PackageSettings] HandlerAlias[ezfile]; settings/package.ini [PackageSettings] HandlerAlias[ezini]; settings/package.ini [PackageSettings] HandlerAlias[eztemplate]; settings/package.ini [PackageSettings] HandlerAlias[ezthumbnail] |
| `eZINIAddonPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/eziniaddon/eziniaddonpackagehandler.php:18` | – | settings/package.ini [PackageSettings] HandlerAlias[eziniaddon] |
| `eZInstallScriptPackageHandler` | class extends eZPackageHandler | `kernel/classes/packagehandlers/ezinstallscript/ezinstallscriptpackagehandler.php:19` | – | settings/package.ini [PackageSettings] HandlerAlias[ezinstallscript] |

### kernel/classes/packageinstallers (2)

[All patterns of kernel/classes/packageinstallers](area-kernel-classes-packageinstallers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContentObjectPackageInstaller` | class extends eZPackageInstallationHandler | `kernel/classes/packageinstallers/ezcontentobject/ezcontentobjectpackageinstaller.php:18` | – | settings/package.ini [InstallerSettings] HandlerAlias[ezcontentobject] |
| `eZInstallScriptPackageInstaller` | class extends eZPackageInstallationHandler | `kernel/classes/packageinstallers/ezinstallscript/ezinstallscriptpackageinstaller.php:17` | – | settings/package.ini [InstallerSettings] HandlerAlias[ezinstallscript] |

### kernel/classes/subitems (12)

[All patterns of kernel/classes/subitems](area-kernel-classes-subitems.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSubitemsDateColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsdatecolumn.php:16` | Subitems list columns about when things happened, in the forms the built-in Published and Modified columns do not show: relative ("3 days ago"), in d… | settings/subitemscolumns.ini [Column_dayssincemodified] Class; settings/subitemscolumns.ini [Column_dayssincepublished] Class; settings/subitemscolumns.ini [Column_modifiedage] Class; settings/subitemscolumns.ini [Column_modifiediso] Class; settings/subitemscolumns.ini [Column_publishedage] Class; settings/subitemscolumns.ini [Column_publishediso] Class |
| `expSubitemsMediaColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsmediacolumn.php:20` | Subitems list columns about the images and files of the content object. | settings/subitemscolumns.ini [Column_filedownloads] Class; settings/subitemscolumns.ini [Column_filemime] Class; settings/subitemscolumns.ini [Column_filename] Class; settings/subitemscolumns.ini [Column_filesize] Class; settings/subitemscolumns.ini [Column_hasimage] Class; settings/subitemscolumns.ini [Column_imagealt] Class; settings/subitemscolumns.ini [Column_imagecount] Class; settings/subitemscolumns.ini [Column_imagedimensions] Class; settings/subitemscolumns.ini [Column_imageheight] Class; settings/subitemscolumns.ini [Column_imagemime] Class; settings/subitemscolumns.ini [Column_imagesize] Class; settings/subitemscolumns.ini [Column_imagewidth] Class |
| `expSubitemsNodeColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsnodecolumn.php:18` | Subitems list columns read from the location (the ezcontentobject_tree row) and its children. | settings/subitemscolumns.ini [Column_childclasses] Class; settings/subitemscolumns.ini [Column_childrencount] Class; settings/subitemscolumns.ini [Column_childsortfield] Class; settings/subitemscolumns.ini [Column_childsortorder] Class; settings/subitemscolumns.ini [Column_depth] Class; settings/subitemscolumns.ini [Column_hidden] Class; settings/subitemscolumns.ini [Column_invisible] Class; settings/subitemscolumns.ini [Column_iscontainer] Class; settings/subitemscolumns.ini [Column_ismain] Class; settings/subitemscolumns.ini [Column_mainnodeid] Class; settings/subitemscolumns.ini [Column_modifiedsubnode] Class; settings/subitemscolumns.ini [Column_newestchild] Class; settings/subitemscolumns.ini [Column_newestchildname] Class; settings/subitemscolumns.ini [Column_parentname] Class; settings/subitemscolumns.ini [Column_parentnodeid] Class; settings/subitemscolumns.ini [Column_pathidentification] Class; settings/subitemscolumns.ini [Column_pathstring] Class; settings/subitemscolumns.ini [Column_subtreecount] Class; settings/subitemscolumns.ini [Column_viewcount] Class |
| `expSubitemsObjectColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsobjectcolumn.php:18` | Subitems list columns read from the content object: class, section, owner, languages, states and the size of its main text. | settings/subitemscolumns.ini [Column_alwaysavailable] Class; settings/subitemscolumns.ini [Column_attributecount] Class; settings/subitemscolumns.ini [Column_classgroups] Class; settings/subitemscolumns.ini [Column_classid] Class; settings/subitemscolumns.ini [Column_classidentifier] Class; settings/subitemscolumns.ini [Column_initiallanguage] Class; settings/subitemscolumns.ini [Column_languagecodes] Class; settings/subitemscolumns.ini [Column_languagecount] Class; settings/subitemscolumns.ini [Column_languagemask] Class; settings/subitemscolumns.ini [Column_locked] Class; settings/subitemscolumns.ini [Column_missingtranslations] Class; settings/subitemscolumns.ini [Column_namelength] Class; settings/subitemscolumns.ini [Column_owner] Class; settings/subitemscolumns.ini [Column_ownerid] Class; settings/subitemscolumns.ini [Column_sectionid] Class; settings/subitemscolumns.ini [Column_sectionidentifier] Class; settings/subitemscolumns.ini [Column_textlength] Class; settings/subitemscolumns.ini [Column_wordcount] Class |
| `expSubitemsPermissionColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemspermissioncolumn.php:18` | Subitems list columns that tell what the current user may do with each item, and technical facts about it. | settings/subitemscolumns.ini [Column_cancreate] Class; settings/subitemscolumns.ini [Column_canedit] Class; settings/subitemscolumns.ini [Column_canhide] Class; settings/subitemscolumns.ini [Column_canmove] Class; settings/subitemscolumns.ini [Column_canremove] Class; settings/subitemscolumns.ini [Column_cantranslate] Class; settings/subitemscolumns.ini [Column_searchwords] Class |
| `expSubitemsReadingTimeColumn` | class extends expSubitemsColumn | `kernel/classes/subitems/columns/expsubitemsreadingtimecolumn.php:14` | The "Reading time" column of the admin subitems list: the minutes a reader needs for the item's main text, at WordsPerMinute= words a minute (default… | settings/subitemscolumns.ini [Column_readingtime] Class |
| `expSubitemsRelationColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsrelationcolumn.php:20` | Subitems list columns about what the object is related to, and what relates to it: object relations, tags (eztags) and ratings (ezstarrating). | settings/subitemscolumns.ini [Column_rating] Class; settings/subitemscolumns.ini [Column_ratingcount] Class; settings/subitemscolumns.ini [Column_relatedcount] Class; settings/subitemscolumns.ini [Column_relatednames] Class; settings/subitemscolumns.ini [Column_reverserelatedcount] Class; settings/subitemscolumns.ini [Column_reverserelatednames] Class; settings/subitemscolumns.ini [Column_tagcount] Class; settings/subitemscolumns.ini [Column_tags] Class |
| `expSubitemsSEOColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsseocolumn.php:32` | Subitems list columns for search engines and visitors: the page title in the browser, its length, and the meta title, description and keywords an xro… | settings/subitemscolumns.ini [Column_metadescription] Class; settings/subitemscolumns.ini [Column_metakeywords] Class; settings/subitemscolumns.ini [Column_metatitle] Class; settings/subitemscolumns.ini [Column_pagetitle] Class; settings/subitemscolumns.ini [Column_titlelength] Class |
| `expSubitemsServerFunctions` | class extends ezjscServerFunctions | `kernel/classes/subitems/expsubitemsserverfunctions.php:24` | The ezjscore server functions of the admin subitems list ("Table options"), registered as [ezjscServer_expsubitems] in extension/ezjscore/settings/ez… | extension/ezjscore/settings/ezjscore.ini [ezjscServer_expsubitems] Class |
| `expSubitemsURLColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsurlcolumn.php:20` | Subitems list columns about the addresses of a node: its URL alias, its system URL, the address visitors use on the public site, custom and historic… | settings/subitemscolumns.ini [Column_aliascount] Class; settings/subitemscolumns.ini [Column_allaliases] Class; settings/subitemscolumns.ini [Column_customaliascount] Class; settings/subitemscolumns.ini [Column_customaliases] Class; settings/subitemscolumns.ini [Column_historycount] Class; settings/subitemscolumns.ini [Column_locationcount] Class; settings/subitemscolumns.ini [Column_mainlocation] Class; settings/subitemscolumns.ini [Column_otherlocations] Class; settings/subitemscolumns.ini [Column_publicpath] Class; settings/subitemscolumns.ini [Column_publicurl] Class; settings/subitemscolumns.ini [Column_systemurl] Class; settings/subitemscolumns.ini [Column_urlalias] Class; settings/subitemscolumns.ini [Column_urlslug] Class |
| `expSubitemsUserColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsusercolumn.php:19` | Subitems list columns for user accounts: the rows below a user group. | settings/subitemscolumns.ini [Column_useremail] Class; settings/subitemscolumns.ini [Column_userenabled] Class; settings/subitemscolumns.ini [Column_userfailedlogins] Class; settings/subitemscolumns.ini [Column_userlastvisit] Class; settings/subitemscolumns.ini [Column_userlocked] Class; settings/subitemscolumns.ini [Column_userlogin] Class; settings/subitemscolumns.ini [Column_userlogincount] Class; settings/subitemscolumns.ini [Column_userrolecount] Class; settings/subitemscolumns.ini [Column_userroles] Class |
| `expSubitemsVersionColumn` | class extends expSubitemsFieldColumn | `kernel/classes/subitems/columns/expsubitemsversioncolumn.php:17` | Subitems list columns about the versions of the content object. | settings/subitemscolumns.ini [Column_archivedcount] Class; settings/subitemscolumns.ini [Column_contributors] Class; settings/subitemscolumns.ini [Column_draftauthors] Class; settings/subitemscolumns.ini [Column_draftcount] Class; settings/subitemscolumns.ini [Column_initialcreator] Class; settings/subitemscolumns.ini [Column_latestdraft] Class; settings/subitemscolumns.ini [Column_pendingcount] Class; settings/subitemscolumns.ini [Column_rejectedcount] Class; settings/subitemscolumns.ini [Column_version] Class; settings/subitemscolumns.ini [Column_versioncount] Class; settings/subitemscolumns.ini [Column_versioncreated] Class; settings/subitemscolumns.ini [Column_workflowprocesses] Class |

### kernel/classes/uiswitch (2)

[All patterns of kernel/classes/uiswitch](area-kernel-classes-uiswitch.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expUISwitchDesignProvider` | class extends expUISwitchProvider | `kernel/classes/uiswitch/expuiswitchdesignprovider.php:25` | The design of the administration interface (the "UI chrome"): admin4l, admin4, the classic admin design, AdminUI or another installed administration… | settings/uiswitch.ini [UISwitch_design] Class |
| `expUISwitchLanguageProvider` | class extends expUISwitchProvider | `kernel/classes/uiswitch/expuiswitchlanguageprovider.php:27` | The display language of the administration interface: what site.ini [RegionalSettings] Locale of the siteaccess sets (the translations of the interfa… | settings/uiswitch.ini [UISwitch_language] Class |

### kernel/modules (1)

[All patterns of kernel/modules](area-kernel-modules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZSearchEngine` | class | `kernel/search/plugins/ezsearchengine/ezsearchengine.php:18` | – | settings/site.ini [SearchSettings] SearchEngine |

### kernel/private (16)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContentObjectState` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstate.php:18` | Class representing a content object state | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateGroup` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstategroup.php:18` | Class respresenting a content object state group | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateGroupLanguage` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstategrouplanguage.php:21` | This class takes care of the localization of a content object state group. | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZContentObjectStateLanguage` | class extends eZPersistentObject | `kernel/private/classes/ezcontentobjectstatelanguage.php:21` | This class takes care of the localization of a content object state. | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `ezpAttributeOperatorHTMLFormatter` | class extends ezpAttributeOperatorFormatter | `kernel/private/eztemplate/ezpattributeoperatorhtmlformatter.php:12` | – | settings/template.ini [AttributeOperator] OutputFormatter[html] |
| `ezpAttributeOperatorTextFormatter` | class extends ezpAttributeOperatorFormatter | `kernel/private/eztemplate/ezpattributeoperatortextformatter.php:12` | – | settings/template.ini [AttributeOperator] OutputFormatter[text] |
| `ezpContentPublishingQueue` | class | `kernel/private/classes/ezpcontentpublishingqueue.php:19` | This class manages the content publishing queue. | settings/content.ini [PublishingSettings] AsynchronousPublishingQueueReader |
| `ezpContentXHTMLRenderer` | class extends ezpRestContentRendererInterface | `kernel/private/rest/classes/renderers/xhtml_content_renderer.php:12` | – | settings/rest.ini [OutputSettings] RendererClass[xhtml] |
| `ezpLanguageSwitcher` | class | `kernel/private/classes/ezplanguageswitcher.php:18` | Utility class for transforming URLs between siteaccesses. | settings/site.ini [RegionalSettings] LanguageSwitcherClass |
| `ezpMobileDeviceRegexpFilter` | class | `kernel/private/classes/ezpmobiledeviceregexpfilter.php:15` | Default implementation of mobile device detect interface. | settings/site.ini [SiteAccessSettings] MobileDeviceFilterClass |
| `ezpMultivariateTestHandler` | class | `kernel/private/classes/ezpmultivariatetesthandler.php:12` | – | settings/content.ini [TestingSettings] MultivariateTestingHandlerClass |
| `ezpRestAuthProvider` | class | `kernel/private/rest/classes/auth/auth_provider.php:11` | – | settings/rest.ini [ApiProvider] ProviderClass[auth] |
| `ezpRestDefaultRegexpPrefixFilter` | class extends ezpRestPrefixFilterInterface | `kernel/private/rest/classes/default_regexp_prefix_filter.php:15` | Default implementation of prefix filter interface. | settings/rest.ini [System] PrefixFilterClass |
| `ezpRestIniRouteFilter` | class extends ezpRestRouteFilterInterface | `kernel/private/rest/classes/auth/ini_route_security.php:12` | – | settings/rest.ini [RouteSettings] RouteSettingImpl |
| `ezpRestOauthAuthenticationStyle` | class extends ezpRestAuthenticationStyle | `kernel/private/rest/classes/auth/styles/oauth.php:12` | – | settings/rest.ini [Authentication] AuthenticationStyle |
| `ezpRestRoutesCacheClear` | class | `kernel/private/rest/classes/cache/clear_routes.php:15` | Clear cache handler. | settings/site.ini [Cache_restRoutes] class; settings/site.ini [Cache_restRoutes] purgeClass |

### kernel/private/classes/clusterfilehandlers (2)

[All patterns of kernel/private/classes/clusterfilehandlers](area-kernel-private-classes-clusterfilehandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDFSFileHandlerDFSBackend` | class | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs.php:12` | – | settings/file.ini [eZDFSClusteringSettings] DFSBackend |
| `eZDFSFileHandlerMySQLiBackend` | class | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php:50` | – | settings/file.ini [eZDFSClusteringSettings] DBBackend |

### kernel/private/classes/urlaliasfilters (1)

[All patterns of kernel/private/classes/urlaliasfilters](area-kernel-private-classes-urlaliasfilters.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZURLAliasFilterAppendNodeID` | class extends eZURLAliasFilter | `kernel/private/classes/urlaliasfilters/ezurlaliasfilterappendnodeid.php:18` | This class is an URL alias filter to be run with the eZURLAliasFilter system | settings/site.ini [URLTranslator] FilterClasses[] |

### lib/ezdb (4)

[All patterns of lib/ezdb](area-lib-ezdb.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMongoDB` | class extends eZDBInterface | `lib/ezdb/classes/expmongodb.php:18` | – | settings/site.ini [DatabaseSettings] ImplementationAlias[mongodb] |
| `eZMySQLiDB` | class extends eZDBInterface | `lib/ezdb/classes/ezmysqlidb.php:22` | – | settings/site.ini [DatabaseSettings] ImplementationAlias[ezmysql]; settings/site.ini [DatabaseSettings] ImplementationAlias[ezmysqli]; settings/site.ini [DatabaseSettings] ImplementationAlias[mysql]; settings/site.ini [DatabaseSettings] ImplementationAlias[mysqli] |
| `eZPostgreSQLDB` | class extends eZDBInterface | `lib/ezdb/classes/ezpostgresqldb.php:23` | – | settings/site.ini [DatabaseSettings] ImplementationAlias[ezpostgresql]; settings/site.ini [DatabaseSettings] ImplementationAlias[pgsql]; settings/site.ini [DatabaseSettings] ImplementationAlias[postgresql] |
| `eZSQLite3DB` | class extends eZDBInterface | `lib/ezdb/classes/ezsqlite3db.php:3` | – | settings/site.ini [DatabaseSettings] ImplementationAlias[sqlite3] |

### lib/ezdbschema (4)

[All patterns of lib/ezdbschema](area-lib-ezdbschema.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMongoSchema` | class extends eZDBSchemaInterface | `lib/ezdbschema/classes/expmongoschema.php:11` | MongoDB schema handler for eZDbSchema. | settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[mongo] |
| `eZMysqlSchema` | class extends eZDBSchemaInterface | `lib/ezdbschema/classes/ezmysqlschema.php:19` | – | settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[mysql]; settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[mysqli] |
| `eZPgsqlSchema` | class extends eZDBSchemaInterface | `lib/ezdbschema/classes/ezpgsqlschema.php:19` | – | settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[postgresql] |
| `eZSQLiteSchema` | class extends eZDBSchemaInterface | `lib/ezdbschema/classes/ezsqliteschema.php:3` | – | settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[sqlite3]; settings/dbschema.ini [SchemaSettings] SchemaHandlerClasses[sqlite] |

### lib/ezimage (2)

[All patterns of lib/ezimage](area-lib-ezimage.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZImageGDFactory` | class extends eZImageFactory | `lib/ezimage/classes/ezimagegdfactory.php:12` | – | settings/image.ini [GD] Handler |
| `eZImageShellFactory` | class extends eZImageFactory | `lib/ezimage/classes/ezimageshellfactory.php:12` | – | settings/image.ini [ImageMagick] Handler |

### lib/ezmath (1)

[All patterns of lib/ezmath](area-lib-ezmath.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZPHPMath` | class | `lib/ezmath/classes/mathhandlers/ezphpmath.php:17` | – | settings/shop.ini [MathSettings] MathHandler |

### lib/ezutils (3)

[All patterns of lib/ezutils](area-lib-ezutils.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFileTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezfiletransport.php:18` | – | settings/site.ini [MailSettings] TransportAlias[file] |
| `eZSendmailTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezsendmailtransport.php:20` | – | settings/site.ini [MailSettings] TransportAlias[sendmail] |
| `eZSMTPTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezsmtptransport.php:18` | – | settings/site.ini [MailSettings] TransportAlias[smtp] |

## INI plugin points (574)

### extension/AdminAid (18)

| Setting | Class | Set in |
|---|---|---|
| `[SearchDatabase] ClassList[]` | `eZContentObject` | `extension/AdminAid/settings/aid.ini.append.php:26` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectTreeNode` | `extension/AdminAid/settings/aid.ini.append.php:30` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectAttribute` | `extension/AdminAid/settings/aid.ini.append.php:34` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectVersion` | `extension/AdminAid/settings/aid.ini.append.php:38` |
| `[SearchDatabase] ClassList[]` | `eZContentClass` | `extension/AdminAid/settings/aid.ini.append.php:41` |
| `[SearchDatabase] ClassList[]` | `eZContentClassAttribute` | `extension/AdminAid/settings/aid.ini.append.php:45` |
| `[SearchDatabase] ClassList[]` | `eZUser` | `extension/AdminAid/settings/aid.ini.append.php:49` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectState` | `extension/AdminAid/settings/aid.ini.append.php:53` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateGroup` | `extension/AdminAid/settings/aid.ini.append.php:57` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateLanguage` | `extension/AdminAid/settings/aid.ini.append.php:61` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateGroupLanguage` | `extension/AdminAid/settings/aid.ini.append.php:65` |
| `[SearchDatabase] ClassList[]` | `eZContentLanguage` | `extension/AdminAid/settings/aid.ini.append.php:69` |
| `[SearchDatabase] ClassList[]` | `eZContentBrowseBookmark` | `extension/AdminAid/settings/aid.ini.append.php:73` |
| `[SearchDatabase] ClassList[]` | `eZImageFile` | `extension/AdminAid/settings/aid.ini.append.php:77` |
| `[SearchDatabase] ClassList[]` | `eZInformationCollectionAttribute` | `extension/AdminAid/settings/aid.ini.append.php:81` |
| `[SearchDatabase] ClassList[]` | `eZNodeAssignment` | `extension/AdminAid/settings/aid.ini.append.php:85` |
| `[SearchDatabase] ClassList[]` | `eZURL` | `extension/AdminAid/settings/aid.ini.append.php:88` |
| `[SearchDatabase] ClassList[]` | `eZWorkflow` | `extension/AdminAid/settings/aid.ini.append.php:96` |

### extension/bccie (42)

| Setting | Class | Set in |
|---|---|---|
| `[CieSettings] ExportOutputFormatHandlers[utf8]` | `bccieExportFormatOutputHandlerUtf8` | `extension/bccie/settings/cie.ini.append.php:90` |
| `[CieSettings] ExportOutputFormatHandlers[utf8bom]` | `bccieExportFormatOutputHandlerUtf8Bom` | `extension/bccie/settings/cie.ini.append.php:91` |
| `[CieSettings] ExportOutputFormatHandlers[utf16le]` | `bccieExportFormatOutputHandlerUtf16Le` | `extension/bccie/settings/cie.ini.append.php:92` |
| `[CieSettings] ExportOutputFormatHandlers[cp1252]` | `bccieExportFormatOutputHandlerCP1252` | `extension/bccie/settings/cie.ini.append.php:93` |
| `[ezboolean] HandlerClass` | `eZBooleanHandler` | `extension/bccie/settings/export.ini:31` |
| `[ezcountry] HandlerClass` | `eZCountryHandler` | `extension/bccie/settings/export.ini:35` |
| `[ezdate] HandlerClass` | `eZDateHandler` | `extension/bccie/settings/export.ini:39` |
| `[ezdatetime] HandlerClass` | `eZDateTimeHandler` | `extension/bccie/settings/export.ini:43` |
| `[ezemail] HandlerClass` | `eZEmailHandler` | `extension/bccie/settings/export.ini:47` |
| `[ezenhancedobjectrelation] HandlerClass` | `ezenhancedobjectrelationHandler` | `extension/bccie/settings/export.ini:51` |
| `[ezfloat] HandlerClass` | `eZFloatHandler` | `extension/bccie/settings/export.ini:58` |
| `[ezidentifier] HandlerClass` | `eZIdentifierHandler` | `extension/bccie/settings/export.ini:62` |
| `[ezinteger] HandlerClass` | `eZIntegerHandler` | `extension/bccie/settings/export.ini:66` |
| `[ezobjectrelation] HandlerClass` | `eZObjectRelationHandler` | `extension/bccie/settings/export.ini:70` |
| `[ezoption] HandlerClass` | `eZOptionHandler` | `extension/bccie/settings/export.ini:77` |
| `[ezselection] HandlerClass` | `eZSelectionHandler` | `extension/bccie/settings/export.ini:81` |
| `[ezstring] HandlerClass` | `eZStringHandler` | `extension/bccie/settings/export.ini:85` |
| `[eztext] HandlerClass` | `eZTextHandler` | `extension/bccie/settings/export.ini:89` |
| `[eztime] HandlerClass` | `eZTimeHandler` | `extension/bccie/settings/export.ini:93` |
| `[ezurl] HandlerClass` | `eZURLHandler` | `extension/bccie/settings/export.ini:97` |
| `[ezxmltext] HandlerClass` | `eZXMLTextHandler` | `extension/bccie/settings/export.ini:101` |
| `[owenhancedselection] HandlerClass` | `OWEnhancedSelectionHandler` | `extension/bccie/settings/export.ini:105` |
| `[smileobjectrelationlist] HandlerClass` | `smileobjectrelationlistHandler` | `extension/bccie/settings/export.ini:109` |
| `[ezbirthday] HandlerClass` | `eZBirthdayHandler` | `extension/bccie/settings/export.ini:113` |
| `[ezstring] HandlerClass` | `eZStringHandler` | `extension/bccie/settings/export.ini.append.php:32` |
| `[ezinteger] HandlerClass` | `eZIntegerHandler` | `extension/bccie/settings/export.ini.append.php:36` |
| `[ezxmltext] HandlerClass` | `eZXMLTextHandler` | `extension/bccie/settings/export.ini.append.php:40` |
| `[ezboolean] HandlerClass` | `eZBooleanHandler` | `extension/bccie/settings/export.ini.append.php:44` |
| `[ezidentifier] HandlerClass` | `eZIdentifierHandler` | `extension/bccie/settings/export.ini.append.php:48` |
| `[ezfloat] HandlerClass` | `eZFloatHandler` | `extension/bccie/settings/export.ini.append.php:52` |
| `[ezemail] HandlerClass` | `eZEmailHandler` | `extension/bccie/settings/export.ini.append.php:56` |
| `[ezurl] HandlerClass` | `eZURLHandler` | `extension/bccie/settings/export.ini.append.php:60` |
| `[eztext] HandlerClass` | `eZTextHandler` | `extension/bccie/settings/export.ini.append.php:64` |
| `[ezenhancedobjectrelation] HandlerClass` | `ezenhancedobjectrelationHandler` | `extension/bccie/settings/export.ini.append.php:68` |
| `[ezselection] HandlerClass` | `eZSelectionHandler` | `extension/bccie/settings/export.ini.append.php:75` |
| `[ezoption] HandlerClass` | `eZOptionHandler` | `extension/bccie/settings/export.ini.append.php:79` |
| `[ezcountry] HandlerClass` | `eZCountryHandler` | `extension/bccie/settings/export.ini.append.php:83` |
| `[ezdatetime] HandlerClass` | `eZDateTimeHandler` | `extension/bccie/settings/export.ini.append.php:87` |
| `[eztime] HandlerClass` | `eZTimeHandler` | `extension/bccie/settings/export.ini.append.php:91` |
| `[ezdate] HandlerClass` | `eZDateHandler` | `extension/bccie/settings/export.ini.append.php:95` |
| `[smileobjectrelationlist] HandlerClass` | `smileobjectrelationlistHandler` | `extension/bccie/settings/export.ini.append.php:99` |
| `[ezbirthday] HandlerClass` | `eZBirthdayExportHandler` | `extension/bccie/settings/export.ini.append.php:103` |

### extension/birthday (1)

| Setting | Class | Set in |
|---|---|---|
| `[ezbirthday] HandlerClass` | `eZBirthdayCsvHandler` | `extension/birthday/settings/csv.ini.append.php:19` |

### extension/cjw_newsletter (20)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[cjw_newsletter]` | `cjwNewsletterAuditBranch` | `extension/cjw_newsletter/settings/audit.ini.append.php:6` |
| `[NewsletterFilterSettings] AvailableFilterTypeClassArray[]` | `CjwNewsletterFilterTypeSalutation` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:128` |
| `[NewsletterFilterSettings] AvailableFilterTypeClassArray[]` | `CjwNewsletterFilterTypeEmail` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:129` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterDeliverabilityHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:186` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterEditorialHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:256` |
| `[ScheduleSettings] ConditionHandlers[]` | `CjwNewsletterScheduleConditionNewArticles` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:269` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterRenderingHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:306` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterStatisticsHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:381` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterSmsHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:423` |
| `[SmsTransport_file] Class` | `CjwNewsletterSmsTransportFile` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:469` |
| `[SmsTransport_http] Class` | `CjwNewsletterSmsTransportHttp` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:481` |
| `[SmsTransport_twilio] Class` | `CjwNewsletterSmsTransportTwilio` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:504` |
| `[ExtensionPointSettings] Handlers[]` | `CjwNewsletterImportExportHooks` | `extension/cjw_newsletter/settings/cjw_newsletter.ini:513` |
| `[CjwNewsletterListFilter] ClassName` | `CjwNewsletterListFilter` | `extension/cjw_newsletter/settings/extendedattributefilter.ini.append.php:23` |
| `[CjwNewsletterEditionFilter] ClassName` | `CjwNewsletterEditionFilter` | `extension/cjw_newsletter/settings/extendedattributefilter.ini.append.php:34` |
| `[Category_newsletter] HandlerClass` | `CjwNewsletterMailCategoryHandler` | `extension/cjw_newsletter/settings/mailpreferences.ini.append.php:8` |
| `[SuppressionSettings] Listeners[]` | `CjwNewsletterMailPreferences` | `extension/cjw_newsletter/settings/mailpreferences.ini.append.php:13` |
| `[BounceSettings] MessageListeners[]` | `CjwNewsletterMailin` | `extension/cjw_newsletter/settings/mailpreferences.ini.append.php:24` |
| `[Category_newsletter_statistics] HandlerClass` | `CjwNewsletterStatisticsCategoryHandler` | `extension/cjw_newsletter/settings/mailpreferences.ini.append.php:35` |
| `[Category_sms] HandlerClass` | `CjwNewsletterSmsCategoryHandler` | `extension/cjw_newsletter/settings/mailpreferences.ini.append.php:49` |

### extension/cjw_themes_jumper (2)

| Setting | Class | Set in |
|---|---|---|
| `[cjwImagine] Handler` | `cjwJumperImagineImageFactory` | `extension/cjw_themes_jumper/settings/image.ini.append.php:40` |
| `[BlockDefinition_ezcontent_field] Handler` | `expLayoutsTitleBlockHandler` | `extension/cjw_themes_jumper/settings/siteaccess/cjw_starter/explayouts.ini.append.php:50` |

### extension/cjw_tmv (1)

| Setting | Class | Set in |
|---|---|---|
| `[BlockDefinition_tmv_events] Handler` | `cjwTmvEventsBlockHandler` | `extension/cjw_tmv/settings/siteaccess/cjw_starter/explayouts.ini.append.php:10` |

### extension/enhancedselection2 (1)

| Setting | Class | Set in |
|---|---|---|
| `[SolrFieldMapSettings] CustomMap[sckenhancedselection]` | `ezfSolrDocumentFieldSckEnhancedSelection` | `extension/enhancedselection2/settings/ezfind.ini.append.php:4` |

### extension/expchangeclass (3)

| Setting | Class | Set in |
|---|---|---|
| `[ezstring] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:39` |
| `[eztext] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:48` |
| `[ezobjectrelationlist] Class` | `expChangeClassConverters` | `extension/expchangeclass/settings/changeclass.ini.append.php:55` |

### extension/explayouts (85)

| Setting | Class | Set in |
|---|---|---|
| `[ReverseRelationSettings] ProviderClassList[]` | `expLayoutsReverseRelationProvider` | `extension/explayouts/settings/content.ini.append.php:6` |
| `[Panel_explayouts] Class` | `expLayoutsDebugBarPanel` | `extension/explayouts/settings/debugbar.ini.append.php:10` |
| `[BlockDefinition_two_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:48` |
| `[BlockDefinition_column] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:58` |
| `[BlockDefinition_tpl_block] Handler` | `expLayoutsTplBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:66` |
| `[BlockDefinition_text] Handler` | `expLayoutsTextBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:72` |
| `[BlockDefinition_title] Handler` | `expLayoutsTitleBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:78` |
| `[BlockDefinition_html] Handler` | `expLayoutsHtmlBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:84` |
| `[BlockDefinition_rich_text] Handler` | `expLayoutsRichTextBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:90` |
| `[BlockDefinition_image] Handler` | `expLayoutsImageBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:96` |
| `[BlockDefinition_list] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:102` |
| `[BlockDefinition_single] Handler` | `expLayoutsSingleBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:114` |
| `[BlockDefinition_button] Handler` | `expLayoutsButtonBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:120` |
| `[BlockDefinition_spacer] Handler` | `expLayoutsSpacerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:126` |
| `[BlockDefinition_divider] Handler` | `expLayoutsDividerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:132` |
| `[BlockDefinition_card] Handler` | `expLayoutsCardBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:138` |
| `[BlockDefinition_accordion] Handler` | `expLayoutsAccordionBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:144` |
| `[BlockDefinition_tabs] Handler` | `expLayoutsTabsBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:150` |
| `[BlockDefinition_grid] Handler` | `expLayoutsGridBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:156` |
| `[BlockDefinition_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:163` |
| `[BlockDefinition_slider] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:170` |
| `[BlockDefinition_thumb_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:177` |
| `[BlockDefinition_grid_gallery] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:184` |
| `[BlockDefinition_sushi_bar] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:191` |
| `[BlockDefinition_list_zigzag] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:198` |
| `[BlockDefinition_list_accordion] Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:205` |
| `[BlockDefinition_quote] Handler` | `expLayoutsQuoteBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:212` |
| `[BlockDefinition_alert] Handler` | `expLayoutsAlertBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:218` |
| `[BlockDefinition_video] Handler` | `expLayoutsVideoBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:224` |
| `[BlockDefinition_badge] Handler` | `expLayoutsBadgeBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:230` |
| `[BlockDefinition_progress] Handler` | `expLayoutsProgressBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:236` |
| `[BlockDefinition_map] Handler` | `expLayoutsMapBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:242` |
| `[BlockDefinition_carousel] Handler` | `expLayoutsCarouselBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:248` |
| `[BlockDefinition_markdown] Handler` | `expLayoutsMarkdownBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:255` |
| `[BlockDefinition_full_view] Handler` | `expLayoutsFullViewBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:261` |
| `[BlockDefinition_hero] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:270` |
| `[BlockDefinition_about] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:276` |
| `[BlockDefinition_features] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:282` |
| `[BlockDefinition_lead] Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:288` |
| `[BlockDefinition_logos] Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:294` |
| `[BlockDefinition_three_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:301` |
| `[BlockDefinition_four_columns] Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:311` |
| `[BlockDefinition_exp_component_hero] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:335` |
| `[BlockDefinition_exp_component_features] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:343` |
| `[BlockDefinition_exp_component_about] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:355` |
| `[BlockDefinition_exp_component_logos] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:365` |
| `[BlockDefinition_exp_component_quote] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:372` |
| `[BlockDefinition_exp_component_lead] Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:378` |
| `[QueryType_children] Handler` | `expLayoutsChildrenQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:400` |
| `[QueryType_parent] Handler` | `expLayoutsParentQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:404` |
| `[QueryType_subtree] Handler` | `expLayoutsSubtreeQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:408` |
| `[QueryType_siblings] Handler` | `expLayoutsSiblingsQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:412` |
| `[QueryType_latest] Handler` | `expLayoutsLatestQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:416` |
| `[QueryType_random] Handler` | `expLayoutsRandomQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:420` |
| `[QueryType_manual] Handler` | `expLayoutsManualQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:424` |
| `[QueryType_exp_content_relation_list] Handler` | `expLayoutsRelationListQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:428` |
| `[QueryType_exp_content_reverse_relation_list] Handler` | `expLayoutsReverseRelationListQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:432` |
| `[QueryType_exp_content_tags] Handler` | `expLayoutsTagsQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:436` |
| `[QueryType_exponential_content_search] Handler` | `expLayoutsExponentialContentSearchQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:440` |
| `[QueryType_content_by_topic] Handler` | `expLayoutsContentByTopicQueryHandler` | `extension/explayouts/settings/explayouts.ini.append.php:444` |
| `[BlockDefinition_admin_logo] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:478` |
| `[BlockDefinition_admin_search] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:485` |
| `[BlockDefinition_admin_theme_switch] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:492` |
| `[BlockDefinition_admin_menu_button] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:499` |
| `[BlockDefinition_admin_sidebar_controls] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:506` |
| `[BlockDefinition_admin_sidebar_toggles] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:515` |
| `[BlockDefinition_admin_tab_menu] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:522` |
| `[BlockDefinition_admin_left_menu] Handler` | `expLayoutsAdminLeftMenuBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:529` |
| `[BlockDefinition_admin_content_tree] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:536` |
| `[BlockDefinition_admin_clear_cache] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:543` |
| `[BlockDefinition_admin_bookmarks] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:550` |
| `[BlockDefinition_admin_ui_switch] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:557` |
| `[BlockDefinition_admin_current_user] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:564` |
| `[BlockDefinition_admin_preferences] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:571` |
| `[BlockDefinition_admin_quick_settings] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:578` |
| `[BlockDefinition_admin_toolbar_right] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:585` |
| `[BlockDefinition_admin_toolbar_developer] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:592` |
| `[BlockDefinition_admin_breadcrumb] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:599` |
| `[BlockDefinition_admin_module_result] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:606` |
| `[BlockDefinition_admin_footer] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:613` |
| `[BlockDefinition_admin_popup_menu] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:620` |
| `[BlockDefinition_admin_overlay] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:627` |
| `[BlockDefinition_admin_debug_area] Handler` | `expLayoutsAdminPartBlockHandler` | `extension/explayouts/settings/explayouts.ini.append.php:634` |
| `[ezjscServer_expajaxloadmore] Class` | `expLayoutsAjaxLoadMoreServer` | `extension/explayouts/settings/ezjscore.ini.append.php:7` |
| `[OutputSettings] HandlerClass` | `ExplBlockXHTMLXMLOutput` | `extension/explayouts/settings/ezxml.ini.append.php:4` |

### extension/explayouts_core (1)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[explayouts_core]` | `expLayoutsCoreAuditBranch` | `extension/explayouts_core/settings/audit.ini.append.php:6` |

### extension/expservices (69)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[expservices]` | `expServicesAuditBranch` | `extension/expservices/settings/audit.ini.append.php:5` |
| `[ezjscServer_expservices] Class` | `expServicesCatalog` | `extension/expservices/settings/ezjscore.ini.append.php:7` |
| `[ezjscServer_expsession] Class` | `expSessionServices` | `extension/expservices/settings/ezjscore.ini.append.php:10` |
| `[ezjscServer_expsystem] Class` | `expSystemServices` | `extension/expservices/settings/ezjscore.ini.append.php:13` |
| `[ezjscServer_expini] Class` | `expIniServices` | `extension/expservices/settings/ezjscore.ini.append.php:16` |
| `[ezjscServer_expcache] Class` | `expCacheServices` | `extension/expservices/settings/ezjscore.ini.append.php:19` |
| `[ezjscServer_expcronjob] Class` | `expCronjobServices` | `extension/expservices/settings/ezjscore.ini.append.php:22` |
| `[ezjscServer_expextension] Class` | `expExtensionServices` | `extension/expservices/settings/ezjscore.ini.append.php:25` |
| `[ezjscServer_exppackage] Class` | `expPackageServices` | `extension/expservices/settings/ezjscore.ini.append.php:28` |
| `[ezjscServer_expworkflow] Class` | `expWorkflowServices` | `extension/expservices/settings/ezjscore.ini.append.php:31` |
| `[ezjscServer_expvelocity] Class` | `expVelocityServices` | `extension/expservices/settings/ezjscore.ini.append.php:34` |
| `[ezjscServer_exprad] Class` | `expRadServices` | `extension/expservices/settings/ezjscore.ini.append.php:37` |
| `[ezjscServer_expdebug] Class` | `expDebugServices` | `extension/expservices/settings/ezjscore.ini.append.php:40` |
| `[ezjscServer_expuser] Class` | `expUserServices` | `extension/expservices/settings/ezjscore.ini.append.php:44` |
| `[ezjscServer_expusergroup] Class` | `expUserGroupServices` | `extension/expservices/settings/ezjscore.ini.append.php:47` |
| `[ezjscServer_exprole] Class` | `expRoleServices` | `extension/expservices/settings/ezjscore.ini.append.php:50` |
| `[ezjscServer_exppolicy] Class` | `expPolicyServices` | `extension/expservices/settings/ezjscore.ini.append.php:53` |
| `[ezjscServer_expsessionadmin] Class` | `expSessionAdminServices` | `extension/expservices/settings/ezjscore.ini.append.php:56` |
| `[ezjscServer_exppreferences] Class` | `expPreferencesServices` | `extension/expservices/settings/ezjscore.ini.append.php:59` |
| `[ezjscServer_expnotification] Class` | `expNotificationServices` | `extension/expservices/settings/ezjscore.ini.append.php:62` |
| `[ezjscServer_expcollaboration] Class` | `expCollaborationServices` | `extension/expservices/settings/ezjscore.ini.append.php:65` |
| `[ezjscServer_expbookmark] Class` | `expBookmarkServices` | `extension/expservices/settings/ezjscore.ini.append.php:68` |
| `[ezjscServer_expaccount] Class` | `expAccountServices` | `extension/expservices/settings/ezjscore.ini.append.php:71` |
| `[ezjscServer_expimage] Class` | `expImageServices` | `extension/expservices/settings/ezjscore.ini.append.php:75` |
| `[ezjscServer_expfile] Class` | `expFileServices` | `extension/expservices/settings/ezjscore.ini.append.php:78` |
| `[ezjscServer_expmedia] Class` | `expMediaServices` | `extension/expservices/settings/ezjscore.ini.append.php:81` |
| `[ezjscServer_exptags] Class` | `expTagsServices` | `extension/expservices/settings/ezjscore.ini.append.php:84` |
| `[ezjscServer_explayout] Class` | `expLayoutServices` | `extension/expservices/settings/ezjscore.ini.append.php:87` |
| `[ezjscServer_expaudit] Class` | `expAuditServices` | `extension/expservices/settings/ezjscore.ini.append.php:90` |
| `[ezjscServer_expsubitems_svc] Class` | `expSubitemsServices` | `extension/expservices/settings/ezjscore.ini.append.php:93` |
| `[ezjscServer_expnewsletter] Class` | `expNewsletterServices` | `extension/expservices/settings/ezjscore.ini.append.php:96` |
| `[ezjscServer_expsitemap] Class` | `expSitemapServices` | `extension/expservices/settings/ezjscore.ini.append.php:99` |
| `[ezjscServer_expstats] Class` | `expStatsServices` | `extension/expservices/settings/ezjscore.ini.append.php:102` |
| `[ezjscServer_expdesign] Class` | `expDesignServices` | `extension/expservices/settings/ezjscore.ini.append.php:105` |
| `[ezjscServer_explanguage] Class` | `expLanguageServices` | `extension/expservices/settings/ezjscore.ini.append.php:108` |
| `[ezjscServer_expurl] Class` | `expUrlServices` | `extension/expservices/settings/ezjscore.ini.append.php:111` |
| `[ezjscServer_exppdf] Class` | `expPdfServices` | `extension/expservices/settings/ezjscore.ini.append.php:114` |
| `[ezjscServer_expproduct] Class` | `expProductServices` | `extension/expservices/settings/ezjscore.ini.append.php:118` |
| `[ezjscServer_expbasket] Class` | `expBasketServices` | `extension/expservices/settings/ezjscore.ini.append.php:121` |
| `[ezjscServer_exporder] Class` | `expOrderServices` | `extension/expservices/settings/ezjscore.ini.append.php:124` |
| `[ezjscServer_expvat] Class` | `expVatServices` | `extension/expservices/settings/ezjscore.ini.append.php:127` |
| `[ezjscServer_expcurrency] Class` | `expCurrencyServices` | `extension/expservices/settings/ezjscore.ini.append.php:130` |
| `[ezjscServer_expdiscount] Class` | `expDiscountServices` | `extension/expservices/settings/ezjscore.ini.append.php:133` |
| `[ezjscServer_expwishlist] Class` | `expWishlistServices` | `extension/expservices/settings/ezjscore.ini.append.php:136` |
| `[ezjscServer_expshipping] Class` | `expShippingServices` | `extension/expservices/settings/ezjscore.ini.append.php:139` |
| `[ezjscServer_exppayment] Class` | `expPaymentServices` | `extension/expservices/settings/ezjscore.ini.append.php:142` |
| `[ezjscServer_expinfocollection] Class` | `expInfoCollectionServices` | `extension/expservices/settings/ezjscore.ini.append.php:145` |
| `[ezjscServer_exppoll] Class` | `expPollServices` | `extension/expservices/settings/ezjscore.ini.append.php:148` |
| `[ezjscServer_expforum] Class` | `expForumServices` | `extension/expservices/settings/ezjscore.ini.append.php:151` |
| `[ezjscServer_exptopic] Class` | `expTopicServices` | `extension/expservices/settings/ezjscore.ini.append.php:154` |
| `[ezjscServer_expreply] Class` | `expReplyServices` | `extension/expservices/settings/ezjscore.ini.append.php:157` |
| `[ezjscServer_expcomment] Class` | `expCommentServices` | `extension/expservices/settings/ezjscore.ini.append.php:160` |
| `[ezjscServer_expfeed] Class` | `expFeedServices` | `extension/expservices/settings/ezjscore.ini.append.php:163` |
| `[ezjscServer_expnode] Class` | `expNodeServices` | `extension/expservices/settings/ezjscore.ini.append.php:167` |
| `[ezjscServer_expobject] Class` | `expObjectServices` | `extension/expservices/settings/ezjscore.ini.append.php:170` |
| `[ezjscServer_expclass] Class` | `expClassServices` | `extension/expservices/settings/ezjscore.ini.append.php:173` |
| `[ezjscServer_expclassgroup] Class` | `expClassGroupServices` | `extension/expservices/settings/ezjscore.ini.append.php:176` |
| `[ezjscServer_expattribute] Class` | `expAttributeServices` | `extension/expservices/settings/ezjscore.ini.append.php:179` |
| `[ezjscServer_expversion] Class` | `expVersionServices` | `extension/expservices/settings/ezjscore.ini.append.php:182` |
| `[ezjscServer_exptranslation] Class` | `expTranslationServices` | `extension/expservices/settings/ezjscore.ini.append.php:185` |
| `[ezjscServer_exprelation] Class` | `expRelationServices` | `extension/expservices/settings/ezjscore.ini.append.php:188` |
| `[ezjscServer_explocation] Class` | `expLocationServices` | `extension/expservices/settings/ezjscore.ini.append.php:191` |
| `[ezjscServer_exptrash] Class` | `expTrashServices` | `extension/expservices/settings/ezjscore.ini.append.php:194` |
| `[ezjscServer_expsection] Class` | `expSectionServices` | `extension/expservices/settings/ezjscore.ini.append.php:197` |
| `[ezjscServer_expstate] Class` | `expStateServices` | `extension/expservices/settings/ezjscore.ini.append.php:200` |
| `[ezjscServer_expurlalias] Class` | `expUrlAliasServices` | `extension/expservices/settings/ezjscore.ini.append.php:203` |
| `[ezjscServer_expsearch] Class` | `expSearchServices` | `extension/expservices/settings/ezjscore.ini.append.php:206` |
| `[ezjscServer_expcontentjob] Class` | `expContentJobServices` | `extension/expservices/settings/ezjscore.ini.append.php:209` |
| `[ezjscServer_expeditor] Class` | `expEditorServices` | `extension/expservices/settings/ezjscore.ini.append.php:212` |

### extension/expui (1)

| Setting | Class | Set in |
|---|---|---|
| `[ezjscServer_exp] Class` | `expUIServerFunctions` | `extension/expui/settings/ezjscore.ini.append.php:5` |

### extension/exp_oe_tiptap (3)

| Setting | Class | Set in |
|---|---|---|
| `[ezjscServer_expoetiptap] Class` | `expOETiptapServerFunctions` | `extension/exp_oe_tiptap/settings/ezjscore.ini.append.php:7` |
| `[EditorSettings] Engines[tiptap]` | `expOETiptapEngine` | `extension/exp_oe_tiptap/settings/ezoe.ini.append.php:8` |
| `[InputSettings] AliasClasses[eZSimplifiedXMLInput]` | `expOETiptapXMLInput` | `extension/exp_oe_tiptap/settings/ezxml.ini.append.php:7` |

### extension/ezautosave (1)

| Setting | Class | Set in |
|---|---|---|
| `[ezjscServer_ezautosave] Class` | `ezjscServerFunctionsAutosave` | `extension/ezautosave/settings/ezjscore.ini.append.php:4` |

### extension/ezfind (8)

| Setting | Class | Set in |
|---|---|---|
| `[SolrFieldMapSettings] CustomMap[dummy_example]` | `ezfSolrDocumentFieldDummyExample` | `extension/ezfind/settings/ezfind.ini:118` |
| `[SolrFieldMapSettings] CustomMap[ezobjectrelation]` | `ezfSolrDocumentFieldObjectRelation` | `extension/ezfind/settings/ezfind.ini:119` |
| `[SolrFieldMapSettings] CustomMap[ezobjectrelationlist]` | `ezfSolrDocumentFieldObjectRelation` | `extension/ezfind/settings/ezfind.ini:120` |
| `[SolrFieldMapSettings] CustomMap[ezxmltext]` | `ezfSolrDocumentFieldXML` | `extension/ezfind/settings/ezfind.ini:121` |
| `[SolrFieldMapSettings] CustomMap[ezmatrix]` | `ezfSolrDocumentFieldXML` | `extension/ezfind/settings/ezfind.ini:122` |
| `[ExtendedAttributeFilters] FiltersList[geodist]` | `eZFindGeoDistExtendedAttributeFilter` | `extension/ezfind/settings/ezfind.ini:337` |
| `[ExtendedAttributeFilters] FiltersList[ezfindallwords]` | `eZFindAllWordsExtendedAttributeFilter` | `extension/ezfind/settings/ezfind.ini:341` |
| `[ezjscServer_ezfind] Class` | `eZFindServerCallFunctions` | `extension/ezfind/settings/ezjscore.ini.append.php:4` |

### extension/ezflow (6)

| Setting | Class | Set in |
|---|---|---|
| `[Dynamic3Items] FetchClass` | `eZFlowLatestObjects` | `extension/ezflow/settings/block.ini:151` |
| `[Keyword] FetchClass` | `eZFlowKeywordsFetch` | `extension/ezflow/settings/block.ini:246` |
| `[MultimediaCarousel] FetchClass` | `eZFlowMCFetch` | `extension/ezflow/settings/block.ini:275` |
| `[LatestContent] FetchClass` | `eZFlowLatestContent` | `extension/ezflow/settings/block.ini:302` |
| `[ezjscServer_ezflow] Class` | `eZFlowServerCallFunctions` | `extension/ezflow/settings/ezjscore.ini.append.php:4` |
| `[HTTPCacheHandlers] Handlers[]` | `eZSquidCacheManager` | `extension/ezflow/settings/http.ini:5` |

### extension/ezgmaplocation (1)

| Setting | Class | Set in |
|---|---|---|
| `[ezgmlLocationFilter] ClassName` | `ezgmlLocationFilter` | `extension/ezgmaplocation/settings/extendedattributefilter.ini.append.php:5` |

### extension/ezjscore (11)

| Setting | Class | Set in |
|---|---|---|
| `[eZJSCore] JavaScriptOptimizer[]` | `ezjscJavascriptOptimizer` | `extension/ezjscore/settings/ezjscore.ini:12` |
| `[eZJSCore] CssOptimizer[]` | `ezjscCssOptimizer` | `extension/ezjscore/settings/ezjscore.ini:15` |
| `[ezjscServer_ezjsc] Class` | `ezjscServerFunctionsJs` | `extension/ezjscore/settings/ezjscore.ini:116` |
| `[ezjscServer_ezjscnode] Class` | `ezjscServerFunctionsNode` | `extension/ezjscore/settings/ezjscore.ini:122` |
| `[ezjscServer_expsubitems] Class` | `expSubitemsServerFunctions` | `extension/ezjscore/settings/ezjscore.ini:149` |
| `[ezjscServer_expdebugbar] Class` | `expDebugBarServerFunctions` | `extension/ezjscore/settings/ezjscore.ini:167` |
| `[ezjscServer_ezpublishingqueue] Class` | `ezjscServerFunctionsPublishingQueue` | `extension/ezjscore/settings/ezjscore.ini:178` |
| `[ezjscServer_ezajaxuploader] Class` | `ezjscServerFunctionsAjaxUploader` | `extension/ezjscore/settings/ezjscore.ini:181` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]` | `ezpRelationListAjaxUploader` | `extension/ezjscore/settings/ezjscore.ini:190` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelation]` | `ezpRelationAjaxUploader` | `extension/ezjscore/settings/ezjscore.ini:191` |
| `[Cache_ezjscore] class` | `ezjscCacheManager` | `extension/ezjscore/settings/site.ini.append.php:41` |

### extension/ezodf (1)

| Setting | Class | Set in |
|---|---|---|
| `[RESTSettings] HandlerList[]` | `eZRESTODFHandler` | `extension/ezodf/settings/ezrest.ini.append.php:4` |

### extension/ezoe (7)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[ezoe]` | `expOEAuditBranch` | `extension/ezoe/settings/audit.ini.append.php:6` |
| `[ezjscServer_ezoe] Class` | `ezoeServerFunctions` | `extension/ezoe/settings/ezjscore.ini.append.php:6` |
| `[EditorSettings] Engines[tinymce3]` | `expOETinyMCE3Engine` | `extension/ezoe/settings/ezoe.ini:30` |
| `[EditorSettings] Engines[tinymce8]` | `expOETinyMCE8Engine` | `extension/ezoe/settings/ezoe.ini:31` |
| `[SpellChecker] config[general.engine]` | `GoogleSpell` | `extension/ezoe/settings/ezoe.ini:336` |
| `[InputSettings] AliasClasses[eZSimplifiedXMLInput]` | `eZOEXMLInput` | `extension/ezoe/settings/ezxml.ini.append.php:11` |
| `[Registry_ezoeengines] Entries[Engines]` | `expOEEditorEngine` | `extension/ezoe/settings/rad.ini.append.php:17` |

### extension/ezownerchange (1)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[ezownerchange]` | `expOwnerChangeAuditBranch` | `extension/ezownerchange/settings/audit.ini.append.php:6` |

### extension/ezprestapi (2)

| Setting | Class | Set in |
|---|---|---|
| `[ApiProvider] ProviderClass[ezp]` | `ezp7xRestApiProvider` | `extension/ezprestapi/settings/rest.ini.append.php:8` |
| `[ApiProvider] ProviderClass[ezpl]` | `ezp7xRestApiProvider` | `extension/ezprestapi/settings/rest.ini.append.php:9` |

### extension/ezprestapiprovider (2)

| Setting | Class | Set in |
|---|---|---|
| `[ApiProvider] ProviderClass[ezp]` | `ezp7xRestApiProvider` | `extension/ezprestapiprovider/settings/rest.ini.append.php:7` |
| `[ApiProvider] ProviderClass[ezpl]` | `ezp7xRestApiProvider` | `extension/ezprestapiprovider/settings/rest.ini.append.php:8` |

### extension/ezstarrating (2)

| Setting | Class | Set in |
|---|---|---|
| `[ezsrRatingFilter] ClassName` | `ezsrRatingFilter` | `extension/ezstarrating/settings/extendedattributefilter.ini.append.php:6` |
| `[ezjscServer_ezstarrating] Class` | `ezsrServerFunctions` | `extension/ezstarrating/settings/ezjscore.ini.append.php:11` |

### extension/eztags (7)

| Setting | Class | Set in |
|---|---|---|
| `[TagsAttributeFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:4` |
| `[TagsAttributeAndFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:8` |
| `[TagsAttributeAndMultipleFilter] ClassName` | `eZTagsAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:12` |
| `[TagsTreeAttributeFilter] ClassName` | `eZTagsTreeAttributeFilter` | `extension/eztags/settings/extendedattributefilter.ini.append.php:16` |
| `[SolrFieldMapSettings] CustomMap[eztags]` | `ezfSolrDocumentFieldeZTags` | `extension/eztags/settings/ezfind.ini.append.php:5` |
| `[ezjscServer_ezjsctags] Class` | `ezjscTags` | `extension/eztags/settings/ezjscore.ini.append.php:8` |
| `[ezjscServer_ezjsctagschildren] Class` | `ezjscTagsChildren` | `extension/eztags/settings/ezjscore.ini.append.php:11` |

### extension/ezurlmultisite (4)

| Setting | Class | Set in |
|---|---|---|
| `[DataTypeSettings] HandlerList[ezurl]` | `eZURLMultisiteURLHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:79` |
| `[DataTypeSettings] HandlerList[ezstring]` | `eZURLMultisiteURLHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:80` |
| `[DataTypeSettings] HandlerList[ezbinaryfile]` | `eZURLMultisiteBinaryFileHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:81` |
| `[DataTypeSettings] HandlerList[ezobjectrelation]` | `eZURLMultisiteObjectRelationHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:82` |

### extension/ezwt (1)

| Setting | Class | Set in |
|---|---|---|
| `[ezjscServer_ezwt] Class` | `ezwtServerCallFunctions` | `extension/ezwt/settings/ezjscore.ini.append.php:4` |

### extension/git_manager (1)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[git_manager]` | `gitManagerAuditBranch` | `extension/git_manager/settings/audit.ini.append.php:5` |

### extension/sevenx_themes_media (3)

| Setting | Class | Set in |
|---|---|---|
| `[BlockDefinition_product_highlight] Handler` | `expLayoutsListBlockHandler` | `extension/sevenx_themes_media/settings/explayouts.ini.append.php:16` |
| `[OutputSettings] HandlerClass` | `sevenxThemesMediaXHTMLXMLOutput` | `extension/sevenx_themes_media/settings/ezxml.ini.append.php:4` |
| `[ezjscServer_expajaxloadmore] Class` | `expLayoutsAjaxLoadMoreServer` | `extension/sevenx_themes_media/settings/siteaccess/site/ezjscore.ini.append.php:15` |

### extension/sevenx_themes_simple (16)

| Setting | Class | Set in |
|---|---|---|
| `[eZJSCore] JavaScriptOptimizer[]` | `ezjscJavascriptOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:12` |
| `[eZJSCore] CssOptimizer[]` | `ezjscCssOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:15` |
| `[ezjscServer_ezjsc] Class` | `ezjscServerFunctionsJs` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:88` |
| `[ezjscServer_ezjscnode] Class` | `ezjscServerFunctionsNode` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:94` |
| `[ezjscServer_ezpublishingqueue] Class` | `ezjscServerFunctionsPublishingQueue` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:112` |
| `[ezjscServer_ezajaxuploader] Class` | `ezjscServerFunctionsAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:115` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]` | `ezpRelationListAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:119` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelation]` | `ezpRelationAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini:120` |
| `[eZJSCore] JavaScriptOptimizer[]` | `ezjscJavascriptOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:12` |
| `[eZJSCore] CssOptimizer[]` | `ezjscCssOptimizer` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:15` |
| `[ezjscServer_ezjsc] Class` | `ezjscServerFunctionsJs` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:88` |
| `[ezjscServer_ezjscnode] Class` | `ezjscServerFunctionsNode` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:94` |
| `[ezjscServer_ezpublishingqueue] Class` | `ezjscServerFunctionsPublishingQueue` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:112` |
| `[ezjscServer_ezajaxuploader] Class` | `ezjscServerFunctionsAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:115` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]` | `ezpRelationListAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:119` |
| `[AjaxUploader] AjaxUploadHandler[ezobjectrelation]` | `ezpRelationAjaxUploader` | `extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini:120` |

### extension/xrowextract (33)

| Setting | Class | Set in |
|---|---|---|
| `[ezstring] HandlerClass` | `XroweZStringHandler` | `extension/xrowextract/settings/csv.ini.append.php:67` |
| `[ezenhancedselection] HandlerClass` | `XroweZStringHandler` | `extension/xrowextract/settings/csv.ini.append.php:71` |
| `[ezinteger] HandlerClass` | `XroweZIntegerHandler` | `extension/xrowextract/settings/csv.ini.append.php:75` |
| `[ezxmltext] HandlerClass` | `XroweZXMLTextHandler` | `extension/xrowextract/settings/csv.ini.append.php:79` |
| `[ezidentifier] HandlerClass` | `XroweZIdentifierHandler` | `extension/xrowextract/settings/csv.ini.append.php:83` |
| `[ezfloat] HandlerClass` | `XroweZFloatHandler` | `extension/xrowextract/settings/csv.ini.append.php:87` |
| `[ezemail] HandlerClass` | `XroweZEmailHandler` | `extension/xrowextract/settings/csv.ini.append.php:91` |
| `[ezurl] HandlerClass` | `XroweZURLHandler` | `extension/xrowextract/settings/csv.ini.append.php:95` |
| `[eztext] HandlerClass` | `XroweZTextHandler` | `extension/xrowextract/settings/csv.ini.append.php:99` |
| `[ezenhancedobjectrelation] HandlerClass` | `XroweZenhancedobjectrelationHandler` | `extension/xrowextract/settings/csv.ini.append.php:103` |
| `[ezselection] HandlerClass` | `XroweZSelectionHandler` | `extension/xrowextract/settings/csv.ini.append.php:109` |
| `[ezenum] HandlerClass` | `XroweZEnumHandler` | `extension/xrowextract/settings/csv.ini.append.php:113` |
| `[ezuser] HandlerClass` | `XroweZUserHandler` | `extension/xrowextract/settings/csv.ini.append.php:117` |
| `[ezdate] HandlerClass` | `XroweZDateHandler` | `extension/xrowextract/settings/csv.ini.append.php:121` |
| `[ezboolean] HandlerClass` | `XroweZBooleanHandler` | `extension/xrowextract/settings/csv.ini.append.php:125` |
| `[ezcountry] HandlerClass` | `XroweZSelectionHandler` | `extension/xrowextract/settings/csv.ini.append.php:129` |
| `[ezimage] HandlerClass` | `XroweZImageExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:133` |
| `[ezmedia] HandlerClass` | `XroweZMediaExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:137` |
| `[ezbinaryfile] HandlerClass` | `XroweZBinaryfileExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:141` |
| `[ezmatrix] HandlerClass` | `XroweZMatrixExportHandler` | `extension/xrowextract/settings/csv.ini.append.php:145` |
| `[ezobjectrelationlist] HandlerClass` | `XroweZObjectRelationListHandler` | `extension/xrowextract/settings/csv.ini.append.php:149` |
| `[hmregexpline] HandlerClass` | `XrowhmregexplineHandler` | `extension/xrowextract/settings/csv.ini.append.php:153` |
| `[ezprice] HandlerClass` | `XroweZPriceHandler` | `extension/xrowextract/settings/csv.ini.append.php:157` |
| `[ezdatetime] HandlerClass` | `XroweZDateTimeHandler` | `extension/xrowextract/settings/csv.ini.append.php:161` |
| `[eztime] HandlerClass` | `XroweZTimeHandler` | `extension/xrowextract/settings/csv.ini.append.php:165` |
| `[ezkeyword] HandlerClass` | `XroweZKeywordHandler` | `extension/xrowextract/settings/csv.ini.append.php:169` |
| `[eztags] HandlerClass` | `XroweZKeywordHandler` | `extension/xrowextract/settings/csv.ini.append.php:173` |
| `[ezobjectrelation] HandlerClass` | `XroweZObjectRelationHandler` | `extension/xrowextract/settings/csv.ini.append.php:177` |
| `[xrowmetadata] HandlerClass` | `XrowxrowmetadataHandler` | `extension/xrowextract/settings/csv.ini.append.php:181` |
| `[XrowExtractTranslation] ClassName` | `XrowExtractTranslationFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:8` |
| `[XrowExtractHasChildren] ClassName` | `XrowExtractHasChildrenFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:15` |
| `[XrowExtractRelation] ClassName` | `XrowExtractRelationFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:23` |
| `[XrowExtractUserStatus] ClassName` | `XrowExtractUserStatusFilter` | `extension/xrowextract/settings/extendedattributefilter.ini.append.php:30` |

### extension/xrowmetadata (1)

| Setting | Class | Set in |
|---|---|---|
| `[SolrFieldMapSettings] CustomMap[xrowmetadata]` | `ezfSolrDocumentFieldxrowMetadata` | `extension/xrowmetadata/settings/ezfind.ini.append.php:4` |

### settings (219)

| Setting | Class | Set in |
|---|---|---|
| `[AuditSinkSettings] SinkClasses[syslog]` | `expAuditSyslogSink` | `settings/audit.ini:298` |
| `[AuditSinkSettings] SinkClasses[webhook]` | `expAuditWebhookSink` | `settings/audit.ini:299` |
| `[AuditSinkSettings] SinkClasses[mail]` | `expAuditMailSink` | `settings/audit.ini:300` |
| `[AuditAlertSettings] RuleClasses[threshold]` | `expAuditThresholdRule` | `settings/audit.ini:377` |
| `[AuditAlertSettings] RuleClasses[match]` | `expAuditMatchRule` | `settings/audit.ini:378` |
| `[AuditAlertSettings] RuleClasses[schedule]` | `expAuditScheduleRule` | `settings/audit.ini:379` |
| `[AuditArchiveSettings] FormatHandlers[gzip]` | `expAuditGzipFormat` | `settings/audit.ini:523` |
| `[AuditArchiveSettings] FormatHandlers[bzip2]` | `expAuditBzip2Format` | `settings/audit.ini:524` |
| `[AuditArchiveSettings] FormatHandlers[xz]` | `expAuditXzFormat` | `settings/audit.ini:525` |
| `[AuditArchiveSettings] FormatHandlers[zstd]` | `expAuditZstdFormat` | `settings/audit.ini:526` |
| `[AuditArchiveSettings] FormatHandlers[zip]` | `expAuditZipFormat` | `settings/audit.ini:527` |
| `[ContentJobSettings] JobTypes[remove]` | `expContentJobRemoveSubtree` | `settings/content.ini:547` |
| `[ContentJobSettings] JobTypes[copy]` | `expContentJobCopySubtree` | `settings/content.ini:548` |
| `[ContentJobSettings] JobTypes[move]` | `expContentJobMoveSubtree` | `settings/content.ini:549` |
| `[ContentJobSettings] JobTypes[hide]` | `expContentJobHideSubtree` | `settings/content.ini:550` |
| `[ContentJobSettings] JobTypes[reveal]` | `expContentJobRevealSubtree` | `settings/content.ini:551` |
| `[ContentJobSettings] JobTypes[section]` | `expContentJobSectionSubtree` | `settings/content.ini:552` |
| `[ContentJobSettings] JobTypes[state]` | `expContentJobStateSubtree` | `settings/content.ini:553` |
| `[ContentJobSettings] JobTypes[addlocation]` | `expContentJobAddLocation` | `settings/content.ini:554` |
| `[ContentJobSettings] JobTypes[removelocation]` | `expContentJobRemoveLocation` | `settings/content.ini:555` |
| `[ContentJobSettings] JobTypes[packageinstall]` | `expContentJobPackageInstall` | `settings/content.ini:557` |
| `[ContentJobSettings] JobTypes[auditexport]` | `expContentJobAuditExport` | `settings/content.ini:559` |
| `[PublishingSettings] AsynchronousPublishingQueueReader` | `ezpContentPublishingQueue` | `settings/content.ini:685` |
| `[TestingSettings] MultivariateTestingHandlerClass` | `ezpMultivariateTestHandler` | `settings/content.ini:704` |
| `[MissingFileSettings] Handlers[ezimage]` | `expContentCheckFileImage` | `settings/contentcheck.ini:175` |
| `[MissingFileSettings] Handlers[ezbinaryfile]` | `expContentCheckFileTable` | `settings/contentcheck.ini:176` |
| `[MissingFileSettings] Handlers[ezmedia]` | `expContentCheckFileTable` | `settings/contentcheck.ini:177` |
| `[SchemaSettings] SchemaHandlerClasses[mysql]` | `eZMysqlSchema` | `settings/dbschema.ini:22` |
| `[SchemaSettings] SchemaHandlerClasses[mysqli]` | `eZMysqlSchema` | `settings/dbschema.ini:23` |
| `[SchemaSettings] SchemaHandlerClasses[postgresql]` | `eZPgsqlSchema` | `settings/dbschema.ini:24` |
| `[SchemaSettings] SchemaHandlerClasses[sqlite]` | `eZSQLiteSchema` | `settings/dbschema.ini:25` |
| `[SchemaSettings] SchemaHandlerClasses[sqlite3]` | `eZSQLiteSchema` | `settings/dbschema.ini:26` |
| `[SchemaSettings] SchemaHandlerClasses[mongo]` | `expMongoSchema` | `settings/dbschema.ini:27` |
| `[ContextHelp] Providers[]` | `expHelpContentProvider` | `settings/exphelp.ini:606` |
| `[InputSettings] HandlerClass` | `eZSimplifiedXMLInput` | `settings/ezxml.ini:14` |
| `[OutputSettings] HandlerClass` | `eZXHTMLXMLOutput` | `settings/ezxml.ini:40` |
| `[OutputSettings] AliasClasses[ezpdf]` | `eZPDFXMLOutput` | `settings/ezxml.ini:43` |
| `[BinaryFileSettings] Handler` | `eZFilePassthroughHandler` | `settings/file.ini:13` |
| `[ClusteringSettings] FileHandler` | `eZFSFileHandler` | `settings/file.ini:57` |
| `[eZDFSClusteringSettings] DBBackend` | `eZDFSFileHandlerMySQLiBackend` | `settings/file.ini:91` |
| `[eZDFSClusteringSettings] DFSBackend` | `eZDFSFileHandlerDFSBackend` | `settings/file.ini:92` |
| `[GD] Handler` | `eZImageGDFactory` | `settings/image.ini:277` |
| `[ImageMagick] Handler` | `eZImageShellFactory` | `settings/image.ini:305` |
| `[IniCommandSettings] Actions[get]` | `expIniActionGet` | `settings/ini.ini:21` |
| `[IniCommandSettings] Actions[set]` | `expIniActionSet` | `settings/ini.ini:22` |
| `[IniCommandSettings] Actions[add]` | `expIniActionAdd` | `settings/ini.ini:23` |
| `[IniCommandSettings] Actions[rem]` | `expIniActionRem` | `settings/ini.ini:24` |
| `[IniCommandSettings] Actions[clear]` | `expIniActionClear` | `settings/ini.ini:25` |
| `[IniCommandSettings] Actions[toggle]` | `expIniActionToggle` | `settings/ini.ini:26` |
| `[IniCommandSettings] Actions[copy]` | `expIniActionCopy` | `settings/ini.ini:27` |
| `[IniCommandSettings] Actions[move]` | `expIniActionMove` | `settings/ini.ini:28` |
| `[IniCommandSettings] Actions[move-all]` | `expIniActionMoveAll` | `settings/ini.ini:29` |
| `[IniCommandSettings] Actions[where]` | `expIniActionWhere` | `settings/ini.ini:30` |
| `[IniCommandSettings] Actions[list]` | `expIniActionList` | `settings/ini.ini:31` |
| `[IniCommandSettings] Actions[scopes]` | `expIniActionScopes` | `settings/ini.ini:32` |
| `[IniCommandSettings] Actions[actions]` | `expIniActionActions` | `settings/ini.ini:33` |
| `[IniCommandSettings] ScopeProviders[]` | `expIniCoreScopeProvider` | `settings/ini.ini:50` |
| `[IniCommandSettings] ScopeProviders[]` | `expIniExtensionScopeProvider` | `settings/ini.ini:51` |
| `[GateSettings] EssentialSenders[]` | `expMailPreferencesService` | `settings/mailpreferences.ini:37` |
| `[Category_content] HandlerClass` | `expNotificationMailCategoryHandler` | `settings/mailpreferences.ini:91` |
| `[Category_collaboration] HandlerClass` | `expNotificationMailCategoryHandler` | `settings/mailpreferences.ini:103` |
| `[PackageSettings] HandlerAlias[ezcontentclass]` | `eZContentClassPackageHandler` | `settings/package.ini:74` |
| `[PackageSettings] HandlerAlias[ezcontentobject]` | `eZContentObjectPackageHandler` | `settings/package.ini:76` |
| `[PackageSettings] HandlerAlias[eziniaddon]` | `eZINIAddonPackageHandler` | `settings/package.ini:78` |
| `[PackageSettings] HandlerAlias[ezfile]` | `eZFilePackageHandler` | `settings/package.ini:80` |
| `[PackageSettings] HandlerAlias[ezini]` | `eZFilePackageHandler` | `settings/package.ini:81` |
| `[PackageSettings] HandlerAlias[ezdesign]` | `eZFilePackageHandler` | `settings/package.ini:82` |
| `[PackageSettings] HandlerAlias[eztemplate]` | `eZFilePackageHandler` | `settings/package.ini:83` |
| `[PackageSettings] HandlerAlias[ezthumbnail]` | `eZFilePackageHandler` | `settings/package.ini:84` |
| `[PackageSettings] HandlerAlias[ezsql]` | `eZDBPackageHandler` | `settings/package.ini:86` |
| `[PackageSettings] HandlerAlias[ezextension]` | `eZExtensionPackageHandler` | `settings/package.ini:88` |
| `[PackageSettings] HandlerAlias[ezinstallscript]` | `eZInstallScriptPackageHandler` | `settings/package.ini:89` |
| `[CreationSettings] HandlerAlias[ezcontentclass]` | `eZContentClassPackageCreator` | `settings/package.ini:125` |
| `[CreationSettings] HandlerAlias[ezstyle]` | `eZStylePackageCreator` | `settings/package.ini:126` |
| `[CreationSettings] HandlerAlias[ezcontentobject]` | `eZContentObjectPackageCreator` | `settings/package.ini:127` |
| `[CreationSettings] HandlerAlias[ezextension]` | `eZExtensionPackageCreator` | `settings/package.ini:128` |
| `[InstallerSettings] HandlerAlias[ezcontentobject]` | `eZContentObjectPackageInstaller` | `settings/package.ini:140` |
| `[InstallerSettings] HandlerAlias[ezinstallscript]` | `eZInstallScriptPackageInstaller` | `settings/package.ini:141` |
| `[Registry_auditsources] Entries[Sources]` | `expAuditSource` | `settings/rad.ini:77` |
| `[System] PrefixFilterClass` | `ezpRestDefaultRegexpPrefixFilter` | `settings/rest.ini:13` |
| `[ApiProvider] ProviderClass[auth]` | `ezpRestAuthProvider` | `settings/rest.ini:18` |
| `[OutputSettings] RendererClass[xhtml]` | `ezpContentXHTMLRenderer` | `settings/rest.ini:24` |
| `[Authentication] AuthenticationStyle` | `ezpRestOauthAuthenticationStyle` | `settings/rest.ini:69` |
| `[RouteSettings] RouteSettingImpl` | `ezpRestIniRouteFilter` | `settings/rest.ini:143` |
| `[MathSettings] MathHandler` | `eZPHPMath` | `settings/shop.ini:19` |
| `[Cache_restRoutes] class` | `ezpRestRoutesCacheClear` | `settings/site.ini:58` |
| `[Cache_restRoutes] purgeClass` | `ezpRestRoutesCacheClear` | `settings/site.ini:59` |
| `[DatabaseSettings] ImplementationAlias[mysql]` | `eZMySQLiDB` | `settings/site.ini:122` |
| `[DatabaseSettings] ImplementationAlias[mysqli]` | `eZMySQLiDB` | `settings/site.ini:123` |
| `[DatabaseSettings] ImplementationAlias[postgresql]` | `eZPostgreSQLDB` | `settings/site.ini:124` |
| `[DatabaseSettings] ImplementationAlias[ezmysql]` | `eZMySQLiDB` | `settings/site.ini:125` |
| `[DatabaseSettings] ImplementationAlias[ezmysqli]` | `eZMySQLiDB` | `settings/site.ini:126` |
| `[DatabaseSettings] ImplementationAlias[ezpostgresql]` | `eZPostgreSQLDB` | `settings/site.ini:127` |
| `[DatabaseSettings] ImplementationAlias[pgsql]` | `eZPostgreSQLDB` | `settings/site.ini:128` |
| `[DatabaseSettings] ImplementationAlias[sqlite3]` | `eZSQLite3DB` | `settings/site.ini:129` |
| `[DatabaseSettings] ImplementationAlias[mongodb]` | `expMongoDB` | `settings/site.ini:130` |
| `[URLTranslator] FilterClasses[]` | `eZURLAliasFilterAppendNodeID` | `settings/site.ini:524` |
| `[SearchSettings] SearchEngine` | `eZSearchEngine` | `settings/site.ini:720` |
| `[SiteAccessSettings] MobileDeviceFilterClass` | `ezpMobileDeviceRegexpFilter` | `settings/site.ini:1230` |
| `[RegionalSettings] LanguageSwitcherClass` | `ezpLanguageSwitcher` | `settings/site.ini:1520` |
| `[ContentSettings] StaticCacheHandler` | `eZStaticCache` | `settings/site.ini:1847` |
| `[ContentSettings] ContentClassEditHandler` | `eZContentClassEditHandler` | `settings/site.ini:1856` |
| `[MailSettings] TransportAlias[file]` | `eZFileTransport` | `settings/site.ini:1887` |
| `[MailSettings] TransportAlias[sendmail]` | `eZSendmailTransport` | `settings/site.ini:1888` |
| `[MailSettings] TransportAlias[smtp]` | `eZSMTPTransport` | `settings/site.ini:1889` |
| `[Column_parentnodeid] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:172` |
| `[Column_depth] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:183` |
| `[Column_pathstring] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:194` |
| `[Column_pathidentification] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:205` |
| `[Column_mainnodeid] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:216` |
| `[Column_childsortfield] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:227` |
| `[Column_childsortorder] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:236` |
| `[Column_hidden] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:245` |
| `[Column_invisible] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:254` |
| `[Column_iscontainer] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:264` |
| `[Column_childrencount] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:273` |
| `[Column_subtreecount] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:283` |
| `[Column_childclasses] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:293` |
| `[Column_newestchildname] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:302` |
| `[Column_classidentifier] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:314` |
| `[Column_classid] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:325` |
| `[Column_classgroups] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:336` |
| `[Column_sectionidentifier] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:345` |
| `[Column_sectionid] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:356` |
| `[Column_wordcount] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:367` |
| `[Column_textlength] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:377` |
| `[Column_readingtime] Class` | `expSubitemsReadingTimeColumn` | `settings/subitemscolumns.ini:387` |
| `[Column_namelength] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:397` |
| `[Column_rating] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:407` |
| `[Column_ratingcount] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:417` |
| `[Column_version] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:430` |
| `[Column_versioncount] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:440` |
| `[Column_draftcount] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:450` |
| `[Column_draftauthors] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:460` |
| `[Column_latestdraft] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:469` |
| `[Column_archivedcount] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:478` |
| `[Column_versioncreated] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:488` |
| `[Column_parentname] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:500` |
| `[Column_ismain] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:509` |
| `[Column_locationcount] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:518` |
| `[Column_otherlocations] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:528` |
| `[Column_mainlocation] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:537` |
| `[Column_urlalias] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:550` |
| `[Column_allaliases] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:561` |
| `[Column_systemurl] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:571` |
| `[Column_publicpath] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:581` |
| `[Column_publicurl] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:593` |
| `[Column_urlslug] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:606` |
| `[Column_aliascount] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:616` |
| `[Column_customaliases] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:626` |
| `[Column_customaliascount] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:635` |
| `[Column_historycount] Class` | `expSubitemsURLColumn` | `settings/subitemscolumns.ini:645` |
| `[Column_pagetitle] Class` | `expSubitemsSEOColumn` | `settings/subitemscolumns.ini:658` |
| `[Column_titlelength] Class` | `expSubitemsSEOColumn` | `settings/subitemscolumns.ini:673` |
| `[Column_metatitle] Class` | `expSubitemsSEOColumn` | `settings/subitemscolumns.ini:685` |
| `[Column_metadescription] Class` | `expSubitemsSEOColumn` | `settings/subitemscolumns.ini:694` |
| `[Column_metakeywords] Class` | `expSubitemsSEOColumn` | `settings/subitemscolumns.ini:703` |
| `[Column_modifiedsubnode] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:715` |
| `[Column_publishedage] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:725` |
| `[Column_modifiedage] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:735` |
| `[Column_dayssincepublished] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:745` |
| `[Column_dayssincemodified] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:756` |
| `[Column_publishediso] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:767` |
| `[Column_modifiediso] Class` | `expSubitemsDateColumn` | `settings/subitemscolumns.ini:778` |
| `[Column_newestchild] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:789` |
| `[Column_owner] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:801` |
| `[Column_ownerid] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:810` |
| `[Column_initialcreator] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:821` |
| `[Column_contributors] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:830` |
| `[Column_relatedcount] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:842` |
| `[Column_relatednames] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:852` |
| `[Column_reverserelatedcount] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:863` |
| `[Column_reverserelatednames] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:873` |
| `[Column_tags] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:883` |
| `[Column_tagcount] Class` | `expSubitemsRelationColumn` | `settings/subitemscolumns.ini:892` |
| `[Column_initiallanguage] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:905` |
| `[Column_languagecount] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:914` |
| `[Column_languagecodes] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:924` |
| `[Column_missingtranslations] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:934` |
| `[Column_alwaysavailable] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:943` |
| `[Column_locked] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:963` |
| `[Column_pendingcount] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:972` |
| `[Column_rejectedcount] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:982` |
| `[Column_workflowprocesses] Class` | `expSubitemsVersionColumn` | `settings/subitemscolumns.ini:992` |
| `[Column_canedit] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1002` |
| `[Column_canremove] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1011` |
| `[Column_canmove] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1020` |
| `[Column_canhide] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1029` |
| `[Column_cantranslate] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1038` |
| `[Column_cancreate] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1047` |
| `[Column_userlogin] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1060` |
| `[Column_useremail] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1072` |
| `[Column_userenabled] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1084` |
| `[Column_userlocked] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1095` |
| `[Column_userlastvisit] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1106` |
| `[Column_userlogincount] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1117` |
| `[Column_userfailedlogins] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1129` |
| `[Column_userroles] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1141` |
| `[Column_userrolecount] Class` | `expSubitemsUserColumn` | `settings/subitemscolumns.ini:1152` |
| `[Column_hasimage] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1167` |
| `[Column_imagecount] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1176` |
| `[Column_imagedimensions] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1186` |
| `[Column_imagewidth] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1195` |
| `[Column_imageheight] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1205` |
| `[Column_imagesize] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1215` |
| `[Column_imagemime] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1225` |
| `[Column_imagealt] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1234` |
| `[Column_filename] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1243` |
| `[Column_filesize] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1253` |
| `[Column_filemime] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1263` |
| `[Column_filedownloads] Class` | `expSubitemsMediaColumn` | `settings/subitemscolumns.ini:1272` |
| `[Column_searchwords] Class` | `expSubitemsPermissionColumn` | `settings/subitemscolumns.ini:1285` |
| `[Column_viewcount] Class` | `expSubitemsNodeColumn` | `settings/subitemscolumns.ini:1295` |
| `[Column_languagemask] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:1305` |
| `[Column_attributecount] Class` | `expSubitemsObjectColumn` | `settings/subitemscolumns.ini:1315` |
| `[AttributeOperator] OutputFormatter[html]` | `ezpAttributeOperatorHTMLFormatter` | `settings/template.ini:111` |
| `[AttributeOperator] OutputFormatter[text]` | `ezpAttributeOperatorTextFormatter` | `settings/template.ini:112` |
| `[UISwitch_language] Class` | `expUISwitchLanguageProvider` | `settings/uiswitch.ini:25` |
| `[UISwitch_design] Class` | `expUISwitchDesignProvider` | `settings/uiswitch.ini:97` |
