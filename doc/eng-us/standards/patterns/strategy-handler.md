---
title: Strategy / handler
audience: developer
category: standards
order: 324
summary: Interchangeable implementations of one job, chosen by a setting: cluster file handlers, image handlers, login handlers, search engines, mail transports, cache backends ... 379 instances in Exponential.
keywords: design pattern, strategy-handler, behavior, pattern catalogue
type: catalogue
---
# Strategy / handler

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Interchangeable implementations of one job, chosen by a setting: cluster file handlers, image handlers, login handlers, search engines, mail transports, cache backends ...

| | |
|---|---|
| Group | Behavior |
| Instances | 379 |
| Recognized by | Class name ends in Handler, Backend, Driver, Transport, Engine or Strategy, or implements an interface whose name does. |
| Extension point | The INI setting that names the handler (see the INI plugin points page). |

## Classes by area

### extension/bccie (24)

[All patterns of extension/bccie](area-extension-bccie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `BaseHandler` | class | `extension/bccie/classes/basehandler.php:11` | – | – |
| `bccieExportFormatOutputHandler` | class | `extension/bccie/classes/bccieExportFormatOutputHandler.php:11` | – | – |
| `eZBirthdayExportHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbirthdayexporthandler.php:13` | – | extension/bccie/settings/export.ini [ezbirthday] HandlerClass |
| `eZBirthdayHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbirthdayhandler.php:11` | – | extension/bccie/settings/export.ini [ezbirthday] HandlerClass |
| `eZBooleanHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezbooleanhandler.php:11` | – | extension/bccie/settings/export.ini [ezboolean] HandlerClass |
| `eZCountryHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezcountrtyhandler.php:11` | – | extension/bccie/settings/export.ini [ezcountry] HandlerClass |
| `eZDateHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezdatehandler.php:11` | – | extension/bccie/settings/export.ini [ezdate] HandlerClass |
| `eZDateTimeHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezdatetimehandler.php:11` | – | extension/bccie/settings/export.ini [ezdatetime] HandlerClass |
| `eZEmailHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezemailhandler.php:11` | – | extension/bccie/settings/export.ini [ezemail] HandlerClass |
| `ezenhancedobjectrelationHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezenhancedobjectrelationhandler.php:11` | – | extension/bccie/settings/export.ini [ezenhancedobjectrelation] HandlerClass |
| `eZExampleCustomHandler` | class extends BaseHandler | `extension/bccie/classes/handlers/ezexamplecustomhandler.php:11` | – | – |
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

### extension/cjw_newsletter (13)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterApprovalCollaborationHandler` | class extends eZCollaborationItemHandler | `extension/cjw_newsletter/collaboration/cjwnewsletterapproval/cjwnewsletterapprovalcollaborationhandler.php:23` | The approval of a newsletter edition in the collaboration inbox. | – |
| `CjwNewsletterGateTransport` | class extends eZMailTransport | `extension/cjw_newsletter/classes/cjwnewslettergatetransport.php:14` | The transport the mail gate of the e-mail preferences hands a newsletter mail to: it sends the wrapped ezcMail with the newsletter's own transport (S… | – |
| `CjwNewsletterHandler` | class extends eZNotificationEventHandler | `extension/cjw_newsletter/notification/handler/cjwnewsletter/cjwnewsletterhandler.php:21` | The newsletter card of the notification settings pages (notification/settings, cjw_newsletter 4.2.0): the newsletters of the signed-in user, their la… | – |
| `CjwNewsletterMailCategoryHandler` | class | `extension/cjw_newsletter/classes/cjwnewslettermailcategoryhandler.php:20` | The handler of the category "newsletter" of the e-mail preferences (Exponential 6.0.15 and later; settings/mailpreferences.ini.append.php of this ext… | extension/cjw_newsletter/settings/mailpreferences.ini [Category_newsletter] HandlerClass |
| `CjwNewsletterSmsCategoryHandler` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmscategoryhandler.php:25` | The handler of the kernel mail-preference category "sms" ([Category_sms] HandlerClass in settings/mailpreferences.ini.append.php of the extension). | extension/cjw_newsletter/settings/mailpreferences.ini [Category_sms] HandlerClass |
| `CjwNewsletterSmsTransport` | interface | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransport.php:25` | A way to send SMS and to read the SMS a provider hands back (the provider-neutral part of the SMS channel). | – |
| `CjwNewsletterSmsTransportBase` | abstract class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransportbase.php:29` | What the shipped SMS transports share: the settings with their preset, the generic check of an inbound request (an HMAC of the body with a shared sec… | – |
| `CjwNewsletterSmsTransportFile` | class extends CjwNewsletterSmsTransportBase | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransportfile.php:23` | The simulated SMS sending: nothing leaves the server, every SMS is written as one JSON file -.sms.json (to, from, text, segments, time) into Dir. | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_file] Class |
| `CjwNewsletterSmsTransportHttp` | class extends CjwNewsletterSmsTransportBase | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransporthttp.php:38` | The generic SMS transport: one HTTP request per SMS to any provider API, mapped in the INI group. | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_http] Class |
| `CjwNewsletterSmsTransportTwilio` | class extends CjwNewsletterSmsTransportHttp | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransporttwilio.php:29` | A preset of the generic HTTP transport for APIs in the style of Twilio's Messages resource: a form POST to .../Accounts/{AccountSid}/Messages.json wi… | extension/cjw_newsletter/settings/cjw_newsletter.ini [SmsTransport_twilio] Class |
| `CjwNewsletterStatisticsCategoryHandler` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticscategoryhandler.php:23` | The handler of the e-mail preference category "newsletter_statistics" (cjw_newsletter 4.2.0, area N4 Statistics): the consent to per-person counting… | extension/cjw_newsletter/settings/mailpreferences.ini [Category_newsletter_statistics] HandlerClass |
| `CjwNewsletterTransport` | class | `extension/cjw_newsletter/classes/cjwnewslettertransport.php:17` | Generate output and mail | – |
| `CjwNewsletterTransportFile` | class | `extension/cjw_newsletter/classes/cjwnewslettertransportfile.php:17` | Storing a mail to a file | – |

### extension/cjw_themes_jumper (1)

[All patterns of extension/cjw_themes_jumper](area-extension-cjw-themes-jumper.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjwJumperImagineImageHandler` | class extends eZImageHandler | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagehandler.php:28` | Image converter for the cjw_* aliases of the design starter: draws them exactly the way the Nexus v2 starter draws its own aliases (Imagine 1.x with… | – |

### extension/cjw_tmv (1)

[All patterns of extension/cjw_tmv](area-extension-cjw-tmv.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjwTmvEventsBlockHandler` | class | `extension/cjw_tmv/classes/cjwtmveventsblockhandler.php:11` | The Layouts block "tmv_events" (v2: Cjw\TmvBundle TmvEventsHandler): the events of a tmv_container, with the filter toolbar (view type list), only th… | extension/cjw_tmv/settings/siteaccess/cjw_starter/explayouts.ini [BlockDefinition_tmv_events] Handler |

### extension/expai (18)

[All patterns of extension/expai](area-extension-expai.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAiAltTextJobHandler` | class extends expQueueHandlerBase | `extension/expai/classes/alttext/expaialttextjobhandler.php:19` | The job type expai.alttext (expAiAltText::queue(), the "Missing alt text" report): asks a vision model for the alt text of one image and writes it in… | extension/expai/settings/expqueue.ini [JobType_expai.alttext] Handler |
| `expAiAnthropicProvider` | class extends expAiProviderBase | `extension/expai/classes/expaianthropicprovider.php:16` | The Anthropic Messages API (POST {Endpoint}, default https://api.anthropic.com/v1/messages) with tool use, adaptive thinking at the configured effort… | – |
| `expAiBatchPollHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaibatchpollhandler.php:24` | The job type expai.batch.poll (class system): asks the provider for the state of one batch every PollSeconds (deferred in between, without using up a… | extension/expai/settings/expqueue.ini [JobType_expai.batch.poll] Handler |
| `expAiBatchSubmitHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaibatchsubmithandler.php:20` | The job type expai.batch.submit (class system): collects the waiting expai.request jobs of one model and batch lane, at most MaxRequestsPerBatch, sub… | extension/expai/settings/expqueue.ini [JobType_expai.batch.submit] Handler |
| `expAiChainEngine` | class | `extension/expai/classes/chain/expaichainengine.php:24` | Runs AI chains (doc/chains.md) through the job queue. | – |
| `expAiExchangeRatesHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaiexchangerateshandler.php:15` | The job type expai.exchange.rates (class system, no model call, #392): fetches the reference rates of the European Central Bank once a day (expAiExch… | extension/expai/settings/expqueue.ini [JobType_expai.exchange.rates] Handler |
| `expAiModelTestHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaimodeltesthandler.php:16` | The job type expai.model.test: the "Test prompt" (action test) and "Load model" (action load) of expai/models, run by the worker as the user who clic… | extension/expai/settings/expqueue.ini [JobType_expai.model.test] Handler |
| `expAiOllamaNativeProvider` | class | `extension/expai/classes/expaiollamanativeprovider.php:20` | A model of a local Ollama server (a provider with Managed=ollama) spoken to through Ollama's own API on the loopback address instead of its OpenAI co… | – |
| `expAiOpenAiChatProvider` | class extends expAiProviderBase | `extension/expai/classes/expaiopenaichatprovider.php:36` | The OpenAI Chat Completions API and every server that speaks it ([Provider_] Type=openai_chat): OpenAI, Hugging Face Inference Providers (router), Op… | – |
| `expAiPromptedToolProvider` | class | `extension/expai/classes/expaipromptedtoolprovider.php:21` | Tool use for models without native tool calling ([Model_] ToolUse=prompted): the tools are described in the instructions ("tool protocol") and the mo… | – |
| `expAiProvider` | interface | `extension/expai/classes/expaiprovider.php:16` | A model backend of expai. | extension/expai/settings/rad.ini [Registry_expai_provider_types] Implements |
| `expAiProviderBase` | abstract class | `extension/expai/classes/expaiproviderbase.php:16` | What every built-in provider shares: its settings (the [Provider_] values merged with the [Model_] values, see expAiProviderRegistry::settingsFor()),… | – |
| `expAiRequestHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expairequesthandler.php:28` | The job type expai.request: one call to a model on a lane (expAiQueue::request() queues it). | extension/expai/settings/expqueue.ini [JobType_expai.request] Handler |
| `expAiThreadItemHandler` | class extends expQueueHandlerBase | `extension/expai/classes/vector/expaithreaditemhandler.php:22` | The job type expai.thread.item: one item of an approved plan as a child run of the thread (doc/parallel-runs.md), side by side with the other items. | extension/expai/settings/expqueue.ini [JobType_expai.thread.item] Handler |
| `expAiThreadTurnHandler` | class extends expQueueHandlerBase | `extension/expai/classes/vector/expaithreadturnhandler.php:23` | The job type expai.thread.turn: one turn of a thread (the editor's message and every step until the model answers in words) run by the queue worker i… | extension/expai/settings/expqueue.ini [JobType_expai.thread.turn] Handler |
| `expAiTranslationCheckHandler` | class extends expQueueHandlerBase | `extension/expai/classes/queue/expaitranslationcheckhandler.php:15` | The job type expai.translation.check (class system, no model call): after a source-language version was published, decides for each translation of th… | extension/expai/settings/expqueue.ini [JobType_expai.translation.check] Handler |
| `expAiUsageRecorder` | class | `extension/expai/classes/expaiusagerecorder.php:16` | A provider that records every request of the provider it wraps in expai_usage_log (expAiUsage) and enforces [Limits] (expAiLimits) before each reques… | – |
| `expAiWebSearchProvider` | abstract class | `extension/expai/classes/web/expaiwebsearchprovider.php:16` | A web search service for web_search. | extension/expai/settings/rad.ini [Registry_expai_search_providers] Entries[Class] |

### extension/explayouts (50)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsAbstractContentBlockHandler` | abstract class | `extension/explayouts/classes/explayoutsabstractcontentblockhandler.php:3` | – | – |
| `expLayoutsAccordionBlockHandler` | class | `extension/explayouts/classes/explayoutsaccordionblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_accordion] Handler |
| `expLayoutsAdminLeftMenuBlockHandler` | class | `extension/explayouts/classes/explayoutsadminleftmenublockhandler.php:7` | The left menu block of the admin layouts. | extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_left_menu] Handler |
| `expLayoutsAdminPartBlockHandler` | class | `extension/explayouts/classes/explayoutsadminpartblockhandler.php:7` | The handler of the admin blocks that take no parameter (design admin4l): the block shows one part of the admin page, drawn by its view template desig… | extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_bookmarks] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_breadcrumb] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_clear_cache] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_content_tree] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_current_user] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_debug_area] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_footer] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_logo] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_menu_button] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_module_result] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_overlay] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_popup_menu] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_preferences] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_quick_settings] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_search] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_sidebar_controls] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_sidebar_toggles] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_tab_menu] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_theme_switch] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_toolbar_developer] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_toolbar_right] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_admin_ui_switch] Handler |
| `expLayoutsAlertBlockHandler` | class | `extension/explayouts/classes/explayoutsalertblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_alert] Handler |
| `expLayoutsBadgeBlockHandler` | class | `extension/explayouts/classes/explayoutsbadgeblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_badge] Handler |
| `expLayoutsButtonBlockHandler` | class | `extension/explayouts/classes/explayoutsbuttonblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_button] Handler |
| `expLayoutsCardBlockHandler` | class | `extension/explayouts/classes/explayoutscardblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_card] Handler |
| `expLayoutsCarouselBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | `extension/explayouts/classes/explayoutscarouselblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_carousel] Handler |
| `expLayoutsChildrenQueryHandler` | class | `extension/explayouts/classes/explayoutschildrenqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_children] Handler |
| `expLayoutsComponentBlockHandler` | class | `extension/explayouts/classes/explayoutscomponentblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_about] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_hero] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_lead] Handler |
| `expLayoutsContainerBlockHandler` | class | `extension/explayouts/classes/explayoutscontainerblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_column] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_four_columns] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_three_columns] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_two_columns] Handler |
| `expLayoutsContentByTopicQueryHandler` | class | `extension/explayouts/classes/explayoutscontentbytopicqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_content_by_topic] Handler |
| `expLayoutsContentComponentBlockHandler` | class | `extension/explayouts/classes/explayoutscontentcomponentblockhandler.php:17` | Handler for the content-backed component blocks (hero, features, about, logos, quote, lead). | extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_about] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_features] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_hero] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_lead] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_logos] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_exp_component_quote] Handler |
| `expLayoutsDividerBlockHandler` | class | `extension/explayouts/classes/explayoutsdividerblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_divider] Handler |
| `expLayoutsExpContentProvider` | class | `extension/explayouts/classes/explayoutsexpcontentprovider.php:4` | – | – |
| `expLayoutsExponentialContentSearchQueryHandler` | class | `extension/explayouts/classes/explayoutsexponentialcontentsearchqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exponential_content_search] Handler |
| `expLayoutsExpSiteApiContentProvider` | class | `extension/explayouts/classes/explayoutsexpsiteapicontentprovider.php:3` | – | – |
| `expLayoutsFeaturesBlockHandler` | class extends expLayoutsComponentBlockHandler | `extension/explayouts/classes/explayoutsfeaturesblockhandler.php:8` | The features block: the fields of the other component blocks (title, text, image, link) and a list of features, one per line as "Title\|Text", which t… | extension/explayouts/settings/explayouts.ini [BlockDefinition_features] Handler |
| `expLayoutsFullViewBlockHandler` | class | `extension/explayouts/classes/explayoutsfullviewblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_full_view] Handler |
| `expLayoutsGalleryBlockHandler` | class | `extension/explayouts/classes/explayoutsgalleryblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_gallery] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_grid_gallery] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_logos] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_slider] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_sushi_bar] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_thumb_gallery] Handler |
| `expLayoutsGridBlockHandler` | class extends expLayoutsAbstractContentBlockHandler | `extension/explayouts/classes/explayoutsgridblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_grid] Handler |
| `expLayoutsHtmlBlockHandler` | class | `extension/explayouts/classes/explayoutshtmlblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_html] Handler |
| `expLayoutsImageBlockHandler` | class | `extension/explayouts/classes/explayoutsimageblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_image] Handler |
| `expLayoutsLanguageResolver` | class | `extension/explayouts/classes/explayoutslanguageresolver.php:6` | Exponential 4 language resolver compatible with the Exp Site API LanguageResolver pattern. | – |
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
| `expLayoutsResolver` | class | `extension/explayouts/classes/explayoutsresolver.php:2` | – | – |
| `expLayoutsReverseRelationListQueryHandler` | class | `extension/explayouts/classes/explayoutsreverserelationlistqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_reverse_relation_list] Handler |
| `expLayoutsReverseRelationProvider` | class | `extension/explayouts/classes/explayoutsreverserelationprovider.php:15` | The Layouts blocks that name content a removal or a hide takes away, for the kernel's reverse relation check (content.ini [ReverseRelationSettings] P… | extension/explayouts/settings/content.ini [ReverseRelationSettings] ProviderClassList[] |
| `expLayoutsRichTextBlockHandler` | class | `extension/explayouts/classes/explayoutsrichtextblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_rich_text] Handler |
| `expLayoutsSiblingsQueryHandler` | class | `extension/explayouts/classes/explayoutssiblingsqueryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_siblings] Handler |
| `expLayoutsSingleBlockHandler` | class | `extension/explayouts/classes/explayoutssingleblockhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_single] Handler |
| `expLayoutsSiteBundleContextProvider` | class | `extension/explayouts/classes/explayoutssitebundlecontextprovider.php:4` | – | – |
| `expLayoutsSpacerBlockHandler` | class | `extension/explayouts/classes/explayoutsspacerblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_spacer] Handler |
| `expLayoutsSubtreeQueryHandler` | class | `extension/explayouts/classes/explayoutssubtreequeryhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [QueryType_subtree] Handler |
| `expLayoutsTabsBlockHandler` | class | `extension/explayouts/classes/explayoutstabsblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_tabs] Handler |
| `expLayoutsTagsQueryHandler` | class | `extension/explayouts/classes/explayoutstagsqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_tags] Handler |
| `expLayoutsTextBlockHandler` | class | `extension/explayouts/classes/explayoutstextblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_text] Handler |
| `expLayoutsTitleBlockHandler` | class | `extension/explayouts/classes/explayoutstitleblockhandler.php:2` | – | extension/cjw_themes_jumper/settings/siteaccess/cjw_starter/explayouts.ini [BlockDefinition_ezcontent_field] Handler; extension/explayouts/settings/explayouts.ini [BlockDefinition_title] Handler |
| `expLayoutsTplBlockHandler` | class | `extension/explayouts/classes/explayoutstplblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_tpl_block] Handler |
| `expLayoutsVideoBlockHandler` | class | `extension/explayouts/classes/explayoutsvideoblockhandler.php:2` | – | extension/explayouts/settings/explayouts.ini [BlockDefinition_video] Handler |

### extension/explayouts_api (4)

[All patterns of extension/explayouts_api](area-extension-explayouts-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsEzContentContextProvider` | class | `extension/explayouts_api/classes/explayoutsezcontentcontextprovider.php:2` | – | – |
| `expLayoutsEzContentProvider` | class | `extension/explayouts_api/classes/explayoutsezcontentprovider.php:2` | – | – |
| `expLayoutsEzRemoteIdConverter` | class | `extension/explayouts_api/classes/explayoutsezremoteidconverter.php:2` | – | – |
| `expLayoutsEzValueConverter` | class | `extension/explayouts_api/classes/explayoutsezvalueconverter.php:2` | – | – |

### extension/explayouts_content_browser (1)

[All patterns of extension/explayouts_content_browser](area-extension-explayouts-content-browser.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsContentBrowserProvider` | class | `extension/explayouts_content_browser/classes/explayoutscontentbrowserprovider.php:3` | – | – |

### extension/explayouts_content_browser_core (1)

[All patterns of extension/explayouts_content_browser_core](area-extension-explayouts-content-browser-core.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsContentBrowserCoreBackend` | class | `extension/explayouts_content_browser_core/classes/explayoutscontentbrowsercorebackend.php:2` | – | – |

### extension/explayouts_site_api (1)

[All patterns of extension/explayouts_site_api](area-extension-explayouts-site-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsSiteApiValueConverter` | class | `extension/explayouts_site_api/classes/explayoutssiteapivalueconverter.php:2` | – | – |

### extension/explayouts_standard (22)

[All patterns of extension/explayouts_standard](area-extension-explayouts-standard.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsStandardAccordionBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardaccordionblockhandler.php:3` | – | – |
| `expLayoutsStandardAlertBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardalertblockhandler.php:3` | – | – |
| `expLayoutsStandardBadgeBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardbadgeblockhandler.php:3` | – | – |
| `expLayoutsStandardButtonBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardbuttonblockhandler.php:3` | – | – |
| `expLayoutsStandardCardBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardcardblockhandler.php:3` | – | – |
| `expLayoutsStandardCarouselBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardcarouselblockhandler.php:3` | – | – |
| `expLayoutsStandardDividerBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandarddividerblockhandler.php:3` | – | – |
| `expLayoutsStandardGalleryBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardgalleryblockhandler.php:3` | – | – |
| `expLayoutsStandardGridBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardgridblockhandler.php:3` | – | – |
| `expLayoutsStandardHtmlSnippetBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardhtmlsnippetblockhandler.php:2` | – | – |
| `expLayoutsStandardImageBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardimageblockhandler.php:3` | – | – |
| `expLayoutsStandardListBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardlistblockhandler.php:3` | – | – |
| `expLayoutsStandardMapBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardmapblockhandler.php:3` | – | – |
| `expLayoutsStandardMarkdownBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardmarkdownblockhandler.php:2` | – | – |
| `expLayoutsStandardProgressBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardprogressblockhandler.php:3` | – | – |
| `expLayoutsStandardQuoteBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardquoteblockhandler.php:3` | – | – |
| `expLayoutsStandardSingleBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardsingleblockhandler.php:3` | – | – |
| `expLayoutsStandardSpacerBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardspacerblockhandler.php:3` | – | – |
| `expLayoutsStandardTabsBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardtabsblockhandler.php:3` | – | – |
| `expLayoutsStandardTextBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardtextblockhandler.php:3` | – | – |
| `expLayoutsStandardTitleBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardtitleblockhandler.php:3` | – | – |
| `expLayoutsStandardVideoBlockHandler` | class | `extension/explayouts_standard/classes/explayoutsstandardvideoblockhandler.php:3` | – | – |

### extension/expqueue (5)

[All patterns of extension/expqueue](area-extension-expqueue.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expQueueFailHandler` | class extends expQueueHandlerBase | `extension/expqueue/classes/handlers/expqueuefailhandler.php:16` | expqueue.fail: fails on purpose, to try retries, backoff, the dead letter and the rate-limit adaptation. | extension/expqueue/settings/expqueue.ini [JobType_expqueue.fail] Handler |
| `expQueueHandler` | interface | `extension/expqueue/classes/expqueuehandler.php:27` | A job handler: the code that runs the jobs of one type. | extension/expqueue/settings/rad.ini [Registry_expqueue_job_types] Entries[Handler] |
| `expQueueHandlerBase` | abstract class | `extension/expqueue/classes/expqueuehandlerbase.php:12` | A base for handlers with the optional parts: validate() is called when a job is queued (a wrong payload is refused there and then, not at 3 a.m. | – |
| `expQueueNoopHandler` | class extends expQueueHandlerBase | `extension/expqueue/classes/handlers/expqueuenoophandler.php:12` | expqueue.noop: does nothing and is done. | extension/expqueue/settings/expqueue.ini [JobType_expqueue.noop] Handler |
| `expQueueSleepHandler` | class extends expQueueHandlerBase | `extension/expqueue/classes/handlers/expqueuesleephandler.php:16` | expqueue.sleep: waits, in steps, reporting progress and honouring a cancel between the steps. | extension/expqueue/settings/expqueue.ini [JobType_expqueue.sleep] Handler |

### extension/exp_oe_tiptap (5)

[All patterns of extension/exp_oe_tiptap](area-extension-exp-oe-tiptap.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOETiptapAIAnthropicProvider` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaianthropicprovider.php:15` | The Anthropic Messages API: POST {Endpoint} (default https://api.anthropic.com/v1/messages) with the headers x-api-key and anthropic-version and { mo… | – |
| `expOETiptapAIExpaiProvider` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiexpaiprovider.php:32` | The AI commands through the extension expai (Exponential Intelligence), [AISettings] Provider=expai: the request goes to the model expai is set up wi… | – |
| `expOETiptapAIOpenAIProvider` | class | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiopenaiprovider.php:16` | Any provider speaking the OpenAI chat completions API: POST {Endpoint} with { model, messages: [ system, user ], max_tokens, temperature }, answer ch… | – |
| `expOETiptapAIProvider` | interface | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiprovider.php:16` | A text generation backend of the AI hooks. | – |
| `expOETiptapEngine` | class extends expOEEditorEngineBase | `extension/exp_oe_tiptap/classes/expoetiptapengine.php:16` | Tiptap as an engine of ezoe's editor registry: registered by settings/ezoe.ini.append.php (ezoe.ini [EditorSettings] Engines[tiptap]=expOETiptapEngin… | extension/exp_oe_tiptap/settings/ezoe.ini [EditorSettings] Engines[tiptap] |

### extension/ezauthorize (1)

[All patterns of extension/ezauthorize](area-extension-ezauthorize.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCustomShopAccountHandler` | class | `extension/ezauthorize/shopaccounthandlers/ezcustomshopaccounthandler.php:5` | – | – |

### extension/ezfind (3)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezfIndexParentName` | class | `extension/ezfind/classes/indexplugins/ezfindexparentname.php:16` | This class adds the name of the parent of the main node as de dedicated field. | – |
| `ezfIndexPlugin` | interface | `extension/ezfind/classes/indexplugins/ezfindexplugin.php:18` | Description of ezfIndexPlugin. | – |
| `eZSolr` | class | `extension/ezfind/search/plugins/ezsolr/ezsolr.php:11` | Solr search plugin for Exponential | – |

### extension/ezie (3)

[All patterns of extension/ezie](area-extension-ezie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZIEEzcGDHandler` | class extends ezcImageGdHandler | `extension/ezie/classes/handlers/gd.php:11` | – | – |
| `eZIEezcImageConverter` | class | `extension/ezie/classes/image_converter.php:12` | – | – |
| `eZIEEzcImageMagickHandler` | class extends ezcImageImagemagickHandler | `extension/ezie/classes/handlers/magick.php:13` | Implements the methods used by the editor (rotate, flip, etc) for image magick. | – |

### extension/ezjscore (2)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRelationAjaxUploader` | class extends ezpRelationListAjaxUploader | `extension/ezjscore/classes/ajaxuploader/ezprelationajaxuploader.php:19` | This class handles AJAX Upload for eZObjectRelation attributes | extension/ezjscore/settings/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelation] |
| `ezpRelationListAjaxUploader` | class | `extension/ezjscore/classes/ajaxuploader/ezprelationlistajaxuploader.php:19` | This class handles AJAX Upload for eZObjectRelationList attributes | extension/ezjscore/settings/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist]; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [AjaxUploader] AjaxUploadHandler[ezobjectrelationlist] |

### extension/ezmultiupload (1)

[All patterns of extension/ezmultiupload](area-extension-ezmultiupload.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZMultiuploadHandler` | class | `extension/ezmultiupload/classes/ezmultiuploadhandler.php:16` | eZMultiuploadHandler class | – |

### extension/ezodf (6)

[All patterns of extension/ezodf](area-extension-ezodf.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOdfConverter` | class | `extension/ezodf/classes/converter/expodfconverter.php:29` | Converts documents. | – |
| `expOdfPhpConverter` | class | `extension/ezodf/classes/converter/expodfphpconverter.php:25` | The last fallback of the converter: PHP only, no LibreOffice. | – |
| `expOdfQueueHandler` | class extends expQueueHandlerBase | `extension/ezodf/classes/converter/expodfqueuehandler.php:14` | The expqueue handler of ezodf's jobs (expqueue.ini [JobType_ezodf.*]); the work itself is in expOdfJobs::run(). | extension/ezodf/settings/expqueue.ini [JobType_ezodf.export] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.import] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.install] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.server] Handler; extension/ezodf/settings/expqueue.ini [JobType_ezodf.test] Handler |
| `eZOOConverter` | class | `extension/ezodf/classes/ezooconverter.php:41` | – | – |
| `eZOpenofficeUploadHandler` | class extends eZContentUploadHandler | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php:41` | – | – |
| `eZRESTODFHandler` | class extends eZRESTBaseHandler | `extension/ezodf/classes/ezrestodfhandler.php:46` | – | extension/ezodf/settings/ezrest.ini [RESTSettings] HandlerList[] |

### extension/ezoe (4)

[All patterns of extension/ezoe](area-extension-ezoe.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOEEditorEngine` | interface | `extension/ezoe/classes/expoeeditorengine.php:21` | An editor engine of the online editor: the thing that turns an ezxmltext attribute into an editor in the browser. | extension/ezoe/settings/rad.ini [Registry_ezoeengines] Entries[Engines] |
| `expOEEditorEngineBase` | abstract class | `extension/ezoe/classes/expoeeditorenginebase.php:14` | Convenience base of editor engines: reads the engine's own ezoe.ini block [Engine_] (ToolbarMap[]=, ExternalPlugins[]=, Scripts[], Styles[], Version,… | – |
| `expOETinyMCE3Engine` | class extends expOEEditorEngineBase | `extension/ezoe/classes/expoetinymce3engine.php:10` | The default engine: eZ Online Editor on TinyMCE 3.5, rendered by ezxmltext_ezoe.tpl itself. | extension/ezoe/settings/ezoe.ini [EditorSettings] Engines[tinymce3] |
| `expOETinyMCE8Engine` | class extends expOEEditorEngineBase | `extension/ezoe/classes/expoetinymce8engine.php:10` | The opt-in engine: eZ Online Editor on TinyMCE 8 (GPL), rendered by ezxmltext_ezoe_tinymce8.tpl. | extension/ezoe/settings/ezoe.ini [EditorSettings] Engines[tinymce8] |

### extension/ezoracle (2)

[All patterns of extension/ezoracle](area-extension-ezoracle.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDBFileHandlerOracleBackend` | class | `extension/ezoracle/clusterfilehandlers/dbbackends/oracle.php:68` | – | – |
| `eZDFSFileHandlerOracleBackend` | class | `extension/ezoracle/clusterfilehandlers/dfsbackends/oracle.php:55` | – | – |

### extension/ezpostgresqlcluster (1)

[All patterns of extension/ezpostgresqlcluster](area-extension-ezpostgresqlcluster.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDFSFileHandlerPostgresqlBackend` | class | `extension/ezpostgresqlcluster/clustering/dfs/ezpostsgresqlbackend.php:16` | This class allows DFS based clustering using PostgreSQL | – |

### extension/ezprestapi (1)

[All patterns of extension/ezprestapi](area-extension-ezprestapi.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezp7xRestApiProvider` | class | `extension/ezprestapi/classes/rest_provider.php:25` | The routes of the ezp provider. | extension/ezprestapi/settings/rest.ini [ApiProvider] ProviderClass[ezp]; extension/ezprestapi/settings/rest.ini [ApiProvider] ProviderClass[ezpl]; extension/ezprestapiprovider/settings/rest.ini [ApiProvider] ProviderClass[ezp]; extension/ezprestapiprovider/settings/rest.ini [ApiProvider] ProviderClass[ezpl] |

### extension/ezprestapiprovider (1)

[All patterns of extension/ezprestapiprovider](area-extension-ezprestapiprovider.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRestApiProvider` | class extends ezp7xRestApiProvider | `extension/ezprestapiprovider/classes/rest_provider.php:19` | Deprecated: the content provider of REST API v1 now lives in the ezprestapi extension, which serves the reads at v1 and v2 and the writes at v2 from… | – |

### extension/ezurlmultisite (4)

[All patterns of extension/ezurlmultisite](area-extension-ezurlmultisite.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZURLMultisiteBinaryFileHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisitebinaryfilehandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezbinaryfile] |
| `eZURLMultisiteDataTypeHandler` | interface | `extension/ezurlmultisite/classes/ezurlmultisitedatatypehandler.php:3` | – | – |
| `eZURLMultisiteObjectRelationHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisiteobjectrelationhandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezobjectrelation] |
| `eZURLMultisiteURLHandler` | class | `extension/ezurlmultisite/classes/ezurlmultisiteurlhandler.php:3` | – | extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezstring]; extension/ezurlmultisite/settings/ezurlmultisite.ini [DataTypeSettings] HandlerList[ezurl] |

### extension/sevenx_authentication_2fa (1)

[All patterns of extension/sevenx_authentication_2fa](area-extension-sevenx-authentication-2fa.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sevenxAuthentication2faSignIn` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fasignin.php:42` | – | extension/sevenx_authentication_2fa/settings/site.ini [SignInSecondFactorSettings] Handlers[] |

### extension/sevenx_dse (11)

[All patterns of extension/sevenx_dse](area-extension-sevenx-dse.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `AdminNeo\ClickHouseDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/clickhouse.inc.php:196` | – | – |
| `AdminNeo\Driver` | abstract class | `extension/sevenx_dse/adminneo/core/Driver.php:5` | – | – |
| `AdminNeo\ElasticDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/elastic.inc.php:159` | – | – |
| `AdminNeo\MongoDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/mongo.inc.php:204` | – | – |
| `AdminNeo\MsSqlDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:310` | – | – |
| `AdminNeo\MySqlDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/mysql.inc.php:242` | – | – |
| `AdminNeo\OracleDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/oracle.inc.php:215` | – | – |
| `AdminNeo\PgSqlDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/pgsql.inc.php:279` | – | – |
| `AdminNeo\Plugin` | abstract class | `extension/sevenx_dse/adminneo/core/Plugin.php:13` | Plugin parent class. | – |
| `AdminNeo\SimpleDbDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/simpledb.inc.php:183` | – | – |
| `AdminNeo\SqliteDriver` | class extends Driver | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:174` | – | – |

### extension/sqliimport (29)

[All patterns of extension/sqliimport](area-extension-sqliimport.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expImportAiExpaiTransport` | class | `extension/sqliimport/classes/exp/ai/expimportaiexpaitransport.php:25` | The AI assist through the extension expai (Exponential Intelligence), the only way it reaches a model. | – |
| `expImportAiTransport` | interface | `extension/sqliimport/classes/exp/ai/expimportaitransport.php:16` | The way the AI assist of 7x SQLI Import (expImportAi) reaches a model. | – |
| `expImportBoltSocket` | class | `extension/sqliimport/classes/exp/nosql/expimportbolttransport.php:35` | A TCP connection, with TLS (certificate and name verified) for bolt+s:// and neo4j+s://. | – |
| `expImportBoltTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportbolttransport.php:13` | The byte stream the Bolt client (expImportBoltClient) talks over: a socket (expImportBoltSocket) or a test double. | – |
| `expImportCassandraClient` | class | `extension/sqliimport/classes/exp/nosql/expimportcassandraclient.php:23` | A minimal client of the CQL native protocol, version 4 (Cassandra 2.2 and later, ScyllaDB), over a socket (driver "socket"), for a PHP without the ex… | – |
| `expImportCassandraExtension` | class | `extension/sqliimport/classes/exp/nosql/expimportcassandraextension.php:15` | The Cassandra/ScyllaDB source's connection through the PHP extension cassandra of DataStax (Cassandra\Cluster, Cassandra\SimpleStatement). | – |
| `expImportCassandraTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportcassandratransport.php:21` | The connection the Cassandra/ScyllaDB source reads through: the PHP extension cassandra of DataStax (expImportCassandraExtension) where it is install… | – |
| `expImportDataTableHandler` | class | `extension/sqliimport/classes/exp/handlers/expimportdatatablehandler.php:23` | The target "A data table": each record becomes a row of a persistent class (a module's own table, Exponential 6.0.15, Setup › Data tables), written t… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.handler.datatable] Class |
| `expImportImapClient` | class | `extension/sqliimport/classes/exp/mail/expimportimapclient.php:20` | A small IMAP4rev1 client (RFC 3501) for PHP without the imap extension: it speaks just what the IMAP source needs, over TLS from the start (port 993)… | – |
| `expImportImapExtension` | class | `extension/sqliimport/classes/exp/mail/expimportimapextension.php:14` | The IMAP transport of the PHP extension imap (imap_open). | – |
| `expImportImapTransport` | interface | `extension/sqliimport/classes/exp/mail/expimportimaptransport.php:15` | The connection to an IMAP mailbox the IMAP source reads: the PHP extension imap where it is installed (expImportImapExtension), else a small client o… | – |
| `expImportJobHandler` | class extends expQueueHandlerBase | `extension/sqliimport/classes/exp/engine/expimportjobhandler.php:19` | The jobs of a run in the job queue (expqueue.ini [JobType_sqliimport.*]): sqliimport.run, sqliimport.chunk and sqliimport.finish (see expImportRunner… | extension/sqliimport/settings/expqueue.ini [JobType_sqliimport.chunk] Handler; extension/sqliimport/settings/expqueue.ini [JobType_sqliimport.finish] Handler; extension/sqliimport/settings/expqueue.ini [JobType_sqliimport.run] Handler |
| `expImportLdapExtension` | class | `extension/sqliimport/classes/exp/ldap/expimportldapextension.php:15` | The LDAP transport of the PHP extension ldap: LDAPv3, referrals not followed, the certificate of ldaps:// and StartTLS demanded, searches with ldap_s… | – |
| `expImportLdapTransport` | interface | `extension/sqliimport/classes/exp/ldap/expimportldaptransport.php:15` | What the LDAP source needs of a directory server: connect, StartTLS, bind and search, nothing that writes. | – |
| `expImportLegacyHandlerAdapter` | class | `extension/sqliimport/classes/exp/handlers/expimportlegacyhandleradapter.php:21` | Runs a handler of sqliimport 1.x (ISQLIImportHandler extending SQLIImportAbstractHandler) in the 2.x engine, untouched: the handler is built with its… | – |
| `expImportMappingHandler` | class | `extension/sqliimport/classes/exp/handlers/expimportmappinghandler.php:23` | The no-code handler "mapping": it applies the import's mapping (expImportMapper) to each record and publishes the result with the content API of 1.x… | – |
| `expImportMongoDbExtension` | class | `extension/sqliimport/classes/exp/nosql/expimportmongodbextension.php:15` | The MongoDB source's connection through the PHP extension mongodb (MongoDB\Driver\Manager, without the composer library). | – |
| `expImportMongoDbTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportmongodbtransport.php:15` | The connection the MongoDB source reads through: the PHP extension mongodb (expImportMongoDbExtension), or a test double. | – |
| `expImportRedisClient` | class | `extension/sqliimport/classes/exp/nosql/expimportredisclient.php:15` | A minimal Redis client over a socket (driver "socket"), for a PHP without the extension redis: commands as RESP arrays of bulk strings, answers in RE… | – |
| `expImportRedisExtension` | class | `extension/sqliimport/classes/exp/nosql/expimportredisextension.php:15` | The Redis source's connection through the PHP extension redis (phpredis, driver "phpredis"). | – |
| `expImportRedisTransport` | interface | `extension/sqliimport/classes/exp/nosql/expimportredistransport.php:15` | The connection the Redis source reads through: the PHP extension redis (expImportRedisExtension), else a small RESP client of its own over a socket (… | – |
| `expImportUndoHandler` | class | `extension/sqliimport/classes/exp/handlers/expimportundohandler.php:22` | Takes the changes of an earlier run back (an undo run, run_kind undo). | – |
| `ISQLIDiffHandler` | interface | `extension/sqliimport/classes/content/diffhandlers/isqlidiffhandler.php:27` | Interface for diff handlers. | – |
| `ISQLIImportHandler` | interface | `extension/sqliimport/classes/sourcehandlers/isqliimporthandler.php:11` | Interface for import handlers Must be implemented by all import handlers | – |
| `SQLIBinaryFileDiffHandler` | class | `extension/sqliimport/classes/content/diffhandlers/sqlibinaryfilediffhandler.php:17` | Diff handler for ezbinaryfile attributes. | extension/sqliimport/settings/sqliimport.ini [ContentSettings] DiffHandlerMap[ezbinaryfile] |
| `SQLIDefaultDiffHandler` | class | `extension/sqliimport/classes/content/diffhandlers/sqlidefaultdiffhandler.php:18` | Default diff handler for content fields. | extension/sqliimport/settings/sqliimport.ini [ContentSettings] DefaultDiffHandler |
| `SQLIImageDiffHandler` | class | `extension/sqliimport/classes/content/diffhandlers/sqliimagediffhandler.php:17` | Diff handler for ezimage attributes. | extension/sqliimport/settings/sqliimport.ini [ContentSettings] DiffHandlerMap[ezimage] |
| `SQLIImportAbstractHandler` | abstract class | `extension/sqliimport/classes/sourcehandlers/sqliimportabstracthandler.php:13` | Abstract class for all import handlers All import handlers must inherit from this class | – |
| `SQLIRSSImportHandler` | class extends SQLIImportAbstractHandler | `extension/sqliimport/classes/sourcehandlers/sqlirssimporthandler.php:12` | – | extension/sqliimport/settings/sqliimport.ini [rssimporthandler-HandlerSettings] ClassName |

### extension/xrowextract (29)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `XrowBaseHandler` | class | `extension/xrowextract/classes/xrowbasehandler.php:3` | – | – |
| `XrowExtractTransport` | abstract class | `extension/xrowextract/classes/transports/xrowextracttransport.php:10` | One way of delivering a file to (or, for a scheduled import, fetching one from) a destination. | – |
| `XroweZBinaryfileExportHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezbinaryfilehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezbinaryfile] HandlerClass |
| `XroweZBooleanHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezbooleanhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezboolean] HandlerClass |
| `XroweZDateHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezdatehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezdate] HandlerClass |
| `XroweZDateTimeHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezdatetimehandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezdatetime] HandlerClass |
| `XroweZEmailHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezemailhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezemail] HandlerClass |
| `XroweZenhancedobjectrelationHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezenhancedobjectrelationhandler.php:3` | – | extension/xrowextract/settings/csv.ini [ezenhancedobjectrelation] HandlerClass |
| `XroweZEnumHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezenumhandler.php:2` | – | extension/xrowextract/settings/csv.ini [ezenum] HandlerClass |
| `XroweZExampleHandler` | class extends XrowBaseHandler | `extension/xrowextract/classes/parsers/xrowezexamplehandler.php:2` | – | – |
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
| `xrowSitemapConverter` | class | `extension/xrowmetadata/classes/plugins/standardplugin.php:3` | – | – |

### kernel/classes (14)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZBinaryFileHandler` | class | `kernel/classes/ezbinaryfilehandler.php:23` | – | – |
| `eZClusterFileHandler` | class | `kernel/classes/ezclusterfilehandler.php:11` | – | – |
| `eZCollaborationItemHandler` | class | `kernel/classes/ezcollaborationitemhandler.php:18` | – | – |
| `eZCollaborationViewHandler` | class | `kernel/classes/ezcollaborationviewhandler.php:18` | – | – |
| `eZConfirmOrderHandler` | class | `kernel/classes/ezconfirmorderhandler.php:18` | – | – |
| `eZContentClassEditHandler` | class | `kernel/classes/ezcontentclassedithandler.php:15` | Handler for content class editing. | settings/site.ini [ContentSettings] ContentClassEditHandler |
| `eZContentObjectAssignmentHandler` | class | `kernel/classes/ezcontentobjectassignmenthandler.php:18` | – | – |
| `eZContentObjectEditHandler` | class | `kernel/classes/ezcontentobjectedithandler.php:19` | – | – |
| `eZContentUploadHandler` | class | `kernel/classes/ezcontentuploadhandler.php:28` | – | – |
| `eZNamePatternResolver` | class | `kernel/classes/eznamepatternresolver.php:40` | eZNamePatternResolver is a utility class for resolving object name and url alias patterns. | – |
| `eZPackageCreationHandler` | class | `kernel/classes/ezpackagecreationhandler.php:21` | – | – |
| `eZPackageHandler` | class | `kernel/classes/ezpackagehandler.php:18` | – | – |
| `eZPackageInstallationHandler` | class | `kernel/classes/ezpackageinstallationhandler.php:19` | – | – |
| `eZShopAccountHandler` | class | `kernel/classes/ezshopaccounthandler.php:12` | – | – |

### kernel/classes/audit (7)

[All patterns of kernel/classes/audit](area-kernel-classes-audit.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAuditBzip2Format` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditbzip2format.php:11` | The bzip2 archive format (PHP extension bz2): .jsonl.bz2. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[bzip2] |
| `expAuditFormatBase` | abstract class | `kernel/classes/audit/format/expauditformatbase.php:14` | Shared code of the archive format handlers (doc/eng-us/bc/6.0/audit.md, "Rotation, archives and retention"): streaming compression through a PHP stre… | – |
| `expAuditFormatHandler` | interface | `kernel/classes/audit/expauditformathandler.php:14` | An archive format (gzip, bzip2, xz, zstd, zip), registered in audit.ini: [AuditArchiveSettings] FormatHandlers[]= Counted by the RAD survey (registry… | – |
| `expAuditGzipFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditgzipformat.php:11` | The gzip archive format (zlib, always available): .jsonl.gz. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[gzip] |
| `expAuditXzFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditxzformat.php:11` | The xz archive format: .jsonl.xz, through the xz binary (no PHP extension is commonly available). | settings/audit.ini [AuditArchiveSettings] FormatHandlers[xz] |
| `expAuditZipFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditzipformat.php:12` | The zip archive format (PHP extension zip): .jsonl.zip, one archive per live file holding that one file, so a single file can be restored. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[zip] |
| `expAuditZstdFormat` | class extends expAuditFormatBase | `kernel/classes/audit/format/expauditzstdformat.php:11` | The zstd archive format: .jsonl.zst, through the PHP extension zstd when it is loaded, else the zstd binary. | settings/audit.ini [AuditArchiveSettings] FormatHandlers[zstd] |

### kernel/classes/basketinfohandlers (1)

[All patterns of kernel/classes/basketinfohandlers](area-kernel-classes-basketinfohandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDefaultBasketInfoHandler` | class | `kernel/classes/basketinfohandlers/ezdefaultbasketinfohandler.php:12` | – | – |

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

### kernel/classes/collaborationhandlers (1)

[All patterns of kernel/classes/collaborationhandlers](area-kernel-classes-collaborationhandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZApproveCollaborationHandler` | class extends eZCollaborationItemHandler | `kernel/classes/collaborationhandlers/ezapprove/ezapprovecollaborationhandler.php:25` | – | – |

### kernel/classes/confirmorderhandlers (1)

[All patterns of kernel/classes/confirmorderhandlers](area-kernel-classes-confirmorderhandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDefaultConfirmOrderHandler` | class | `kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php:18` | – | – |

### kernel/classes/contentcheck (3)

[All patterns of kernel/classes/contentcheck](area-kernel-classes-contentcheck.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentCheckFileHandler` | interface | `kernel/classes/contentcheck/expcontentcheckfilehandler.php:15` | Says which storage files a file-backed field stands for (doc/eng-us/features/6.0/content-checks.md, "Missing files"). | – |
| `expContentCheckFileImage` | class | `kernel/classes/contentcheck/expcontentcheckfileimage.php:13` | The files of an image field (ezimage): the original and the aliases its stored XML names (doc/eng-us/features/6.0/content-checks.md, "Missing files"). | settings/contentcheck.ini [MissingFileSettings] Handlers[ezimage] |
| `expContentCheckFileTable` | class | `kernel/classes/contentcheck/expcontentcheckfiletable.php:16` | The file of a field whose datatype keeps it in a table with the columns of ezbinaryfile: contentobject_attribute_id, version, filename, original_file… | settings/contentcheck.ini [MissingFileSettings] Handlers[ezbinaryfile]; settings/contentcheck.ini [MissingFileSettings] Handlers[ezmedia] |

### kernel/classes/datatypes (5)

[All patterns of kernel/classes/datatypes](area-kernel-classes-datatypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZImageAliasHandler` | class | `kernel/classes/datatypes/ezimage/ezimagealiashandler.php:28` | – | – |
| `eZISBN10To13Converter` | class | `kernel/classes/datatypes/ezisbn/ezisbn10to13converter.php:12` | – | – |
| `eZUserLoginHandler` | class | `kernel/classes/datatypes/ezuser/ezuserloginhandler.php:19` | – | – |
| `eZXMLInputHandler` | class | `kernel/classes/datatypes/ezxmltext/ezxmlinputhandler.php:19` | – | – |
| `eZXMLOutputHandler` | class | `kernel/classes/datatypes/ezxmltext/ezxmloutputhandler.php:20` | – | – |

### kernel/classes/help (2)

[All patterns of kernel/classes/help](area-kernel-classes-help.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expHelpContentEditionProvider` | class extends expHelpContentProvider | `kernel/classes/help/content/exphelpcontenteditionprovider.php:16` | What goes into a stored edition of a manual (expHelpContentEbooks): every Released object, whoever builds the book, and for a public manual only what… | – |
| `expHelpContentProvider` | class | `kernel/classes/help/content/exphelpcontentprovider.php:19` | Help maintained as content, delivered into the help system (exphelp): the topics the current user may see, rendered with the help's Markdown renderer… | settings/exphelp.ini [ContextHelp] Providers[] |

### kernel/classes/ini (3)

[All patterns of kernel/classes/ini](area-kernel-classes-ini.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expIniCoreScopeProvider` | class | `kernel/classes/ini/expinicorescopeprovider.php:16` | global (settings/override), default (settings/, the shipped defaults, not writable by policy) and one scope per siteaccess: every directory in settin… | settings/ini.ini [IniCommandSettings] ScopeProviders[] |
| `expIniExtensionScopeProvider` | class | `kernel/classes/ini/expiniextensionscopeprovider.php:14` | extension: for every directory in extension/ (active or not; the label says which) and extension::siteaccess: for every extension//settings/siteacces… | settings/ini.ini [IniCommandSettings] ScopeProviders[] |
| `expIniScopeProvider` | interface | `kernel/classes/ini/expiniscopeprovider.php:20` | A source of INI scopes (places an INI file can be written). | – |

### kernel/classes/mailpreferences (1)

[All patterns of kernel/classes/mailpreferences](area-kernel-classes-mailpreferences.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMailCategoryHandler` | interface | `kernel/classes/mailpreferences/expmailcategoryhandler.php:26` | The optional PHP class of a category ([Category_] HandlerClass): it reads data that existed before the preference system (subtree notifications, dige… | – |

### kernel/classes/notification (7)

[All patterns of kernel/classes/notification](area-kernel-classes-notification.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expNotificationMailCategoryHandler` | class | `kernel/classes/notification/expnotificationmailcategoryhandler.php:25` | The categories "content" and "collaboration" of the e-mail preferences read the notification system's own data, so nothing has to be migrated and the… | settings/mailpreferences.ini [Category_collaboration] HandlerClass; settings/mailpreferences.ini [Category_content] HandlerClass |
| `eZCollaborationNotificationHandler` | class extends eZNotificationEventHandler | `kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationhandler.php:18` | – | – |
| `eZGeneralDigestHandler` | class extends eZNotificationEventHandler | `kernel/classes/notification/handler/ezgeneraldigest/ezgeneraldigesthandler.php:17` | – | – |
| `eZMailNotificationTransport` | class extends eZNotificationTransport | `kernel/classes/notification/ezmailnotificationtransport.php:18` | – | – |
| `eZNotificationEventHandler` | class | `kernel/classes/notification/eznotificationeventhandler.php:18` | – | – |
| `eZNotificationTransport` | class | `kernel/classes/notification/eznotificationtransport.php:17` | – | – |
| `eZSubTreeHandler` | class extends eZNotificationEventHandler | `kernel/classes/notification/handler/ezsubtree/ezsubtreehandler.php:18` | – | – |

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

### kernel/classes/shopaccounthandlers (4)

[All patterns of kernel/classes/shopaccounthandlers](area-kernel-classes-shopaccounthandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `bcusauserShopAccountHandler` | class extends eZUserShopAccountHandler | `kernel/classes/shopaccounthandlers/bcusausershopaccounthandler.php:21` | Shop account handler for the US address model: the address line is stored as rather than , and a telephone number is collected and kept as . | – |
| `eZDefaultShopAccountHandler` | class | `kernel/classes/shopaccounthandlers/ezdefaultshopaccounthandler.php:12` | – | – |
| `eZSimpleShopAccountHandler` | class | `kernel/classes/shopaccounthandlers/ezsimpleshopaccounthandler.php:12` | – | – |
| `eZUserShopAccountHandler` | class | `kernel/classes/shopaccounthandlers/ezusershopaccounthandler.php:12` | – | – |

### kernel/classes/syscard (18)

[All patterns of kernel/classes/syscard](area-kernel-classes-syscard.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSysCardAboutProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardaboutprovider.php:19` | The project information of exp:about: what Exponential is, the Foundation and 7x, Velocity, the license, the links, how to contribute, the partner pr… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardBaseProvider` | abstract class | `kernel/classes/syscard/expsyscardbaseprovider.php:23` | The usual provider: set the properties, write collect(), and use fact() and the helpers. | – |
| `expSysCardCacheProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardcacheprovider.php:15` | The caches: which are on (view cache, template compilation, the static cache), and for exp:info the size of the cache directory and of each cache in… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardComposerProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardcomposerprovider.php:16` | Composer: how many packages vendor/ has (vendor/composer/installed.json) and, when the ezupdate extension is active, how many of them do not agree be… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardCronProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardcronprovider.php:14` | The cronjobs: when one last ran (the newest cronjob log or Setup › Cronjobs run) and, for exp:info, every part of cronjob.ini with its scripts. | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardDatabaseProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscarddatabaseprovider.php:15` | The database: its type, the server's version (when syscard.ini [Database] AskVersion=enabled: one connection, one question) and the host it is on. | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardExponentialProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardexponentialprovider.php:14` | Exponential itself: the version and its line, and, for a git checkout, the branch and the commit. | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardExtensionsProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardextensionsprovider.php:14` | exp:info: every active extension with its version, from its extension.xml or ezinfo.php, then its composer.json (the same sources as the about page). | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardHelpProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardhelpprovider.php:15` | The help: how many pages it serves, its languages, and whether its index is current (an index older than the pages is a hint: ./console exp:help inde… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardHostProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardhostprovider.php:14` | The machine: operating system, kernel, host name, uptime, load, CPU, memory, the disk of the installation (exp:info: every local disk), the shell and… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardPhpProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardphpprovider.php:17` | PHP: version and SAPI, how long the branch has security support (syscard.ini [Php] SecuritySupportEnds[]), OPcache and its JIT, the extensions that m… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardProvider` | interface | `kernel/classes/syscard/expsyscardprovider.php:19` | A provider of the system card: one class per group of facts (the machine, PHP, the engine, the database ...). | – |
| `expSysCardQueueProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardqueueprovider.php:14` | exp:info: the job queue of the expqueue extension, when it is active: its service, how many workers are alive and the last action on it (expQueueServ… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardSettingsProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardsettingsprovider.php:15` | exp:info: the INI settings of an allowlist (syscard.ini [Inventory] Settings[]=//). | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardSiteAccessProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardsiteaccessprovider.php:14` | exp:info: how a request finds its siteaccess (site.ini [SiteAccessSettings] MatchOrder: uri, host, port ...) and, for each siteaccess of AvailableSit… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardSiteProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardsiteprovider.php:14` | The site: the siteaccesses and the default one, the active extensions, demo mode, debug output and the mail transport (its type only: file, sendmail,… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardVelocityProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardvelocityprovider.php:17` | Exponential Velocity, the application server of 6.0.15: its version, whether it runs, the live and configured workers, the ports and TLS, the environ… | settings/syscard.ini [Providers] ProviderClasses[] |
| `expSysCardWebServerProvider` | class extends expSysCardBaseProvider | `kernel/classes/syscard/providers/expsyscardwebserverprovider.php:15` | The other web servers on the machine: Apache, nginx, Caddy, FrankenPHP, and PHP-FPM with its masters (one per PHP version) and pool workers (exp:info… | settings/syscard.ini [Providers] ProviderClasses[] |

### kernel/classes/transfer (1)

[All patterns of kernel/classes/transfer](area-kernel-classes-transfer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expKernelTransferQueueHandler` | class extends expQueueHandlerBase | `kernel/classes/transfer/expkerneltransferqueuehandler.php:37` | The expqueue handler of transfer runs (expKernelTransferRunner, doc/eng-us/features/6.0/transfer-runs.md). | extension/bccie/settings/expqueue.ini [JobType_bccie.export] Handler; extension/xrowextract/settings/expqueue.ini [JobType_xrowextract.run] Handler |

### kernel/classes/uiswitch (3)

[All patterns of kernel/classes/uiswitch](area-kernel-classes-uiswitch.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expUISwitchDesignProvider` | class extends expUISwitchProvider | `kernel/classes/uiswitch/expuiswitchdesignprovider.php:25` | The design of the administration interface (the "UI chrome"): admin4l, admin4, the classic admin design, AdminUI or another installed administration… | settings/uiswitch.ini [UISwitch_design] Class |
| `expUISwitchLanguageProvider` | class extends expUISwitchProvider | `kernel/classes/uiswitch/expuiswitchlanguageprovider.php:27` | The display language of the administration interface: what site.ini [RegionalSettings] Locale of the siteaccess sets (the translations of the interfa… | settings/uiswitch.ini [UISwitch_language] Class |
| `expUISwitchProvider` | abstract class | `kernel/classes/uiswitch/expuiswitchprovider.php:18` | The common part of a UI switch provider: its settings group uiswitch.ini [UISwitch_], the allow-list from AllowedValues[] (each value checked by the… | – |

### kernel/classes/user (1)

[All patterns of kernel/classes/user](area-kernel-classes-user.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSignInSecondFactorHandler` | interface | `kernel/classes/user/expsigninsecondfactorhandler.php:18` | What a two-step sign-in extension tells the kernel about sign-ins outside the web login form (REST with a password, WebDAV, SOAP, the expservices log… | – |

### kernel/classes/vathandlers (1)

[All patterns of kernel/classes/vathandlers](area-kernel-classes-vathandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDefaultVATHandler` | class | `kernel/classes/vathandlers/ezdefaultvathandler.php:21` | – | – |

### kernel/modules (4)

[All patterns of kernel/modules](area-kernel-modules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCurrencyConverter` | class | `kernel/shop/classes/ezcurrencyconverter.php:20` | – | – |
| `eZECBHandler` | class extends eZExchangeRatesUpdateHandler | `kernel/shop/classes/exchangeratehandlers/ezecb/ezecbhandler.php:12` | – | – |
| `eZExchangeRatesUpdateHandler` | class | `kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php:12` | – | – |
| `eZSearchEngine` | class | `kernel/search/plugins/ezsearchengine/ezsearchengine.php:18` | – | settings/site.ini [SearchSettings] SearchEngine |

### kernel/private (13)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpContentLimitationHandler` | interface | `kernel/private/interfaces/ezpcontentlimitationhandler.php:29` | Evaluates a policy limitation of the content module that the kernel does not know itself. | – |
| `ezpContentLimitationSolrHandler` | interface extends ezpContentLimitationHandler | `kernel/private/interfaces/ezpcontentlimitationsolrhandler.php:23` | A content limitation handler that can also restrict a search in a search engine that filters by the policies of the user itself, such as eZ Find with… | – |
| `ezpDatabaseBasedClusterFileHandler` | interface | `kernel/private/interfaces/ezpdatabasebasedclusterfilehandler.php:20` | This interface describes a database based cluster file handler | – |
| `ezpKernel` | class | `kernel/private/classes/ezpkernel.php:16` | Base Exponential kernel class. | – |
| `ezpKernelHandler` | interface | `kernel/private/interfaces/ezpkernelhandler.php:14` | Interface for kernel handlers | – |
| `ezpKernelRest` | class | `kernel/private/rest/classes/ezpkernelrest.php:11` | – | – |
| `ezpKernelTreeMenu` | class | `kernel/private/classes/ezpkerneltreemenu.php:11` | – | – |
| `ezpKernelWeb` | class | `kernel/private/classes/ezpkernelweb.php:16` | Provides a kernel handler in web context | – |
| `ezpMultivariateTestHandler` | class | `kernel/private/classes/ezpmultivariatetesthandler.php:12` | – | settings/content.ini [TestingSettings] MultivariateTestingHandlerClass |
| `ezpRestAuthProvider` | class | `kernel/private/rest/classes/auth/auth_provider.php:11` | – | settings/rest.ini [ApiProvider] ProviderClass[auth] |
| `ezpRestProvider` | class | `kernel/private/rest/classes/rest_provider.php:12` | – | – |
| `ezpSearchEngine` | interface | `kernel/private/interfaces/ezpsearchengine.php:18` | This interface is used as the basis for the different search engine implementation | – |
| `ezpWebBasedKernelHandler` | interface extends ezpKernelHandler | `kernel/private/interfaces/ezpwebbasedkernelhandler.php:14` | Interface for web-based kernel handlers | – |

### kernel/private/classes/clusterfilehandlers (3)

[All patterns of kernel/private/classes/clusterfilehandlers](area-kernel-private-classes-clusterfilehandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDFSFileHandler` | class | `kernel/private/classes/clusterfilehandlers/ezdfsfilehandler.php:27` | Handles file operations for Distributed File Systems (f.e. | – |
| `eZDFSFileHandlerDFSBackend` | class | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs.php:12` | – | settings/file.ini [eZDFSClusteringSettings] DFSBackend |
| `eZDFSFileHandlerMySQLiBackend` | class | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php:50` | – | settings/file.ini [eZDFSClusteringSettings] DBBackend |

### kernel/private/classes/requestrules (3)

[All patterns of kernel/private/classes/requestrules](area-kernel-private-classes-requestrules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRequestFactProvider` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:90` | Adds facts to every request's context before the rules run. | – |
| `ezpRequestRuleEngine` | class | `kernel/private/classes/requestrules/ezprequestruleengine.php:32` | The request rules: an ordered list of rules, each a set of conditions and an action, asked about every module view the kernel is about to run. | – |
| `ezpRequestRuleProvider` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:76` | Adds rules from PHP: an extension whose rules are computed (from a database table, from another INI file) rather than written in requestrules.ini. | – |

### kernel/private/classes/webdav (1)

[All patterns of kernel/private/classes/webdav](area-kernel-private-classes-webdav.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZWebDAVContentBackend` | class extends ezcWebdavSimpleBackend | `kernel/private/classes/webdav/ezwebdavcontentbackend.php:30` | WebDAV backend for Exponential, based on eZ Components Webdav component. | – |

### lib/ezdiff (4)

[All patterns of lib/ezdiff](area-lib-ezdiff.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDiffContainerObjectEngine` | class extends eZDiffEngine | `lib/ezdiff/classes/ezdiffcontainerobjectengine.php:21` | – | – |
| `eZDiffEngine` | class | `lib/ezdiff/classes/ezdiffengine.php:21` | – | – |
| `eZDiffTextEngine` | class extends eZDiffEngine | `lib/ezdiff/classes/ezdifftextengine.php:21` | – | – |
| `eZDiffXMLTextEngine` | class extends eZDiffEngine | `lib/ezdiff/classes/ezdiffxmltextengine.php:18` | – | – |

### lib/ezfile (8)

[All patterns of lib/ezfile](area-lib-ezfile.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZBZIP2Handler` | class extends eZCompressionHandler | `lib/ezfile/classes/ezbzip2compressionhandler.php:20` | – | – |
| `eZCompressionHandler` | class extends eZFileHandler | `lib/ezfile/classes/ezcompressionhandler.php:34` | – | – |
| `eZFileHandler` | class | `lib/ezfile/classes/ezfilehandler.php:56` | – | – |
| `eZForwardCompressionHandler` | class extends eZCompressionHandler | `lib/ezfile/classes/ezforwardcompressionhandler.php:20` | – | – |
| `eZGZIPCompressionHandler` | class extends eZForwardCompressionHandler | `lib/ezfile/classes/ezgzipcompressionhandler.php:22` | – | – |
| `eZGZIPShellCompressionHandler` | class extends eZCompressionHandler | `lib/ezfile/classes/ezgzipshellcompressionhandler.php:22` | – | – |
| `eZGZIPZLIBCompressionHandler` | class extends eZCompressionHandler | `lib/ezfile/classes/ezgzipzlibcompressionhandler.php:20` | – | – |
| `eZNoCompressionHandler` | class extends eZCompressionHandler | `lib/ezfile/classes/eznocompressionhandler.php:18` | – | – |

### lib/ezi18n (2)

[All patterns of lib/ezi18n](area-lib-ezi18n.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expI18nFallbackResolver` | class | `lib/ezi18n/classes/expi18nfallbackresolver.php:32` | Which translations a locale falls back to, in order: the one source of truth for the translator (eZTSTranslator), the interface language choice (expU… | – |
| `eZTranslatorHandler` | class | `lib/ezi18n/classes/eztranslatorhandler.php:19` | – | – |

### lib/ezimage (3)

[All patterns of lib/ezimage](area-lib-ezimage.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZImageGDHandler` | class extends eZImageHandler | `lib/ezimage/classes/ezimagegdhandler.php:24` | – | – |
| `eZImageHandler` | class | `lib/ezimage/classes/ezimagehandler.php:19` | – | – |
| `eZImageShellHandler` | class extends eZImageHandler | `lib/ezimage/classes/ezimageshellhandler.php:19` | – | – |

### lib/ezsession (2)

[All patterns of lib/ezsession](area-lib-ezsession.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpSessionHandler` | abstract class | `lib/ezsession/classes/ezpsessionhandler.php:27` | Abstract session handler class to extend | – |
| `ezpSessionHandlerAdapter` | class | `lib/ezsession/classes/ezpsessionhandleradapter.php:24` | Gives an ezpSessionHandler to PHP as an object implementing SessionHandlerInterface. | – |

### lib/ezutils (7)

[All patterns of lib/ezutils](area-lib-ezutils.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZExpiryHandler` | class | `lib/ezutils/classes/ezexpiryhandler.php:16` | Keeps track of expiry keys and their timestamps | – |
| `eZFileTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezfiletransport.php:18` | – | settings/site.ini [MailSettings] TransportAlias[file] |
| `eZFunctionHandler` | class | `lib/ezutils/classes/ezfunctionhandler.php:18` | – | – |
| `eZMailTransport` | class | `lib/ezutils/classes/ezmailtransport.php:18` | – | – |
| `eZOperationHandler` | class | `lib/ezutils/classes/ezoperationhandler.php:18` | – | – |
| `eZSendmailTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezsendmailtransport.php:20` | – | settings/site.ini [MailSettings] TransportAlias[sendmail] |
| `eZSMTPTransport` | class extends eZMailTransport | `lib/ezutils/classes/ezsmtptransport.php:18` | – | settings/site.ini [MailSettings] TransportAlias[smtp] |
