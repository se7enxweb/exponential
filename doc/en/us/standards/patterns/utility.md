---
title: Utility (static helper)
audience: developer
category: standards
order: 337
summary: A class of static functions grouped by subject; no instances. 540 instances in Exponential.
keywords: design pattern, utility, structure, pattern catalogue
type: catalogue
---
# Utility (static helper)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A class of static functions grouped by subject; no instances.

| | |
|---|---|
| Group | Structure |
| Instances | 540 |
| Recognized by | A concrete class whose methods are all static (at least two). |

## Classes by area

### extension/bccie (5)

[All patterns of extension/bccie](area-extension-bccie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `bccieExportPage` | class | `extension/bccie/classes/bccieExportPage.php:15` | The export page of one form: the options form, the download, the start of an export in the background and the removal of the collected information. | – |
| `bccieExportUtils` | class | `extension/bccie/classes/bccieExportUtils.php:11` | – | – |
| `bccieJob` | class | `extension/bccie/classes/bccieJob.php:15` | An export in the background, started from the admin: the same console command the shell uses (ext:bccie:export), started with expProcessTools like th… | – |
| `bccieRunner` | class | `extension/bccie/classes/bccieRunner.php:15` | The work of the exports as methods, so that the admin, the console commands and the cronjob parts do the same thing: validate the options, write an e… | – |
| `bccieUI` | class | `extension/bccie/classes/bccieUI.php:13` | Helpers shared by the module views: flash notices and the parameters of a list view (paging, sorting, filter). | – |

### extension/cjw_newsletter (62)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterAbTest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabtest.php:20` | The A/B subject test of a send: samples, criterion, wait time, winner. | – |
| `CjwNewsletterAbTester` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabtester.php:27` | The A/B subject test of a send (cjw_newsletter 4.2.0, area N4 Statistics). | – |
| `CjwNewsletterAbVariant` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterabvariant.php:20` | One subject variant of an A/B test and its counts. | – |
| `CjwNewsletterApprovalFlow` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterapprovalflow.php:23` | The optional approval of editions before they are sent, per list (cjwnl_list.approval_required). | – |
| `CjwNewsletterArticlePoolFinder` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterarticlepoolfinder.php:25` | Finds the articles of an article pool: the published content under the pool's nodes that matches its classes, sections, tags, states and age, with th… | – |
| `CjwNewsletterBounce` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterbounce.php:32` | What a bounce means for the newsletter, and what is done about it (cjw_newsletter.ini [DeliverabilitySettings]). | – |
| `CjwNewsletterClassInstaller` | class | `extension/cjw_newsletter/classes/cjwnewsletterclassinstaller.php:18` | Installs the newsletter content classes from the packages shipped in extension/cjw_newsletter/packages into one content class group. | – |
| `CjwNewsletterClassLanguages` | class | `extension/cjw_newsletter/classes/cjwnewsletterclasslanguages.php:28` | Moves the newsletter content classes from one language to another (cjw_newsletter 4.2.1: eng-GB to eng-US). | – |
| `CjwNewsletterConditions` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterconditions.php:39` | The "newsletter condition" of an edition: a part of the text that only some subscribers get. | – |
| `CjwNewsletterDashboard` | class | `extension/cjw_newsletter/classes/cjwnewsletterdashboard.php:15` | – | – |
| `CjwNewsletterDeliverability` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterdeliverability.php:26` | The batches of the mail queue and the items that are due (cjw_newsletter.ini [ThrottleSettings]). | – |
| `CjwNewsletterDeliverabilityHooks` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterdeliverabilityhooks.php:25` | The extension point handler of the area "deliverability" (cjw_newsletter.ini [ExtensionPointSettings] Handlers[]=CjwNewsletterDeliverabilityHooks, se… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterEditionBuilder` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditionbuilder.php:24` | Makes and fills editions: finds the latest unsent edition of a list, copies a template edition under its list, and takes articles from a pool into an… | – |
| `CjwNewsletterEditionSendOutput` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewslettereditionsendoutput.php:20` | The rendered output of a send in one language (one edition, many translations). | – |
| `CjwNewsletterEditorialFetch` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditorialfetch.php:27` | The template fetch functions of the area Editorial (modules/newsletter/function_definition.php): | – |
| `CjwNewsletterEditorialHooks` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditorialhooks.php:20` | The extension point handler of the area Editorial (registered in cjw_newsletter.ini [ExtensionPointSettings] Handlers[]): the recurring sends run at… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterEditorialUI` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditorialui.php:18` | Small helpers of the admin views of the area Editorial: the lists and editions to choose from, the path, the reading of posted values. | – |
| `CjwNewsletterExtensionPoints` | class | `extension/cjw_newsletter/classes/cjwnewsletterextensionpoints.php:46` | The places where the feature areas of cjw_newsletter hook into the shared code (the queue runner, the send form, the list attribute, the dashboard) w… | – |
| `CjwNewsletterImportConsent` | class extends CjwNewsletterMailPreferences | `extension/cjw_newsletter/classes/importexport/cjwnewsletterimportconsent.php:25` | The consent of imported subscriptions in the kernel's consent log, with the source "import". | – |
| `CjwNewsletterImportMapping` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewsletterimportmapping.php:20` | A stored column mapping of a CSV import (column -› field), delimiter, header, consent source. | – |
| `CjwNewsletterInterest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewsletterinterest.php:20` | An interest subscribers can pick (a topic of a list, or an eztags tag). | – |
| `CjwNewsletterInterestBlock` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterinterestblock.php:20` | The edition block "articles for your interests": a skin prints {cjwnl_interests_block( 5, $heading, $accent )}, the output keeps the marker [[cjwnl:i… | – |
| `CjwNewsletterInterests` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterinterests.php:20` | The interests subscribers pick (cjwnl_interest, cjwnl_user_interest). | – |
| `CjwNewsletterJob` | class | `extension/cjw_newsletter/classes/cjwnewsletterjob.php:16` | – | – |
| `CjwNewsletterLink` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlink.php:20` | A link of a send that the click redirect knows (only stored URLs are redirected to). | – |
| `CjwNewsletterLinkClick` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterlinkclick.php:20` | One click on a link; per person (send item) only with consent, else the item is 0. | – |
| `CjwNewsletterMailin` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailin.php:36` | Subscribe and unsubscribe by e-mail (cjw_newsletter.ini [MailInSettings], the addresses in cjwnl_mailin_address, every message handled in cjwnl_maili… | extension/cjw_newsletter/settings/mailpreferences.ini [BounceSettings] MessageListeners[] |
| `CjwNewsletterMailinAddress` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinaddress.php:20` | An address (or a plus-address tag) of a list that takes subscribe and unsubscribe mails. | – |
| `CjwNewsletterMailinMessage` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettermailinmessage.php:20` | One mail that came in at a mail-in address, and what was done with it. | – |
| `CjwNewsletterMailPreferences` | class | `extension/cjw_newsletter/classes/cjwnewslettermailpreferences.php:25` | The bridge between the newsletter and the e-mail preferences of Exponential 6.0.15 and later (kernel/classes/mailpreferences). | extension/cjw_newsletter/settings/mailpreferences.ini [SuppressionSettings] Listeners[] |
| `CjwNewsletterMappedImport` | class | `extension/cjw_newsletter/classes/importexport/cjwnewslettermappedimport.php:32` | A CSV import with a column mapping into one newsletter list (cjw_newsletter 4.2.0). | – |
| `CjwNewsletterMigrationLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/importexport/cjwnewslettermigrationlog.php:20` | One row the eznewsletter importer read, and what it did with it (or would do, in a dry run). | – |
| `CjwNewsletterOpen` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletteropen.php:20` | One open of a send; per person only with consent. | – |
| `CjwNewsletterPlaceholders` | class | `extension/cjw_newsletter/classes/cjwnewsletterplaceholders.php:30` | Replaces the placeholders of an edition ([[name]], [[first_name]] ..., #_hash_unsubscribe_# ...) for one recipient. | – |
| `CjwNewsletterPlainText` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterplaintext.php:15` | The text part of a newsletter: rich text as plain text, wrapping, and the tidying of a rendered text part. | – |
| `CjwNewsletterRendering` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterrendering.php:17` | What the rendering of cjw_newsletter 4.2.0 shares: the skins (allowed per list, chosen per send), the languages of a list and of a subscriber, the ou… | – |
| `CjwNewsletterRenderingHooks` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterrenderinghooks.php:25` | The extension point handler of the rendering (area N3, cjw_newsletter.ini [ExtensionPointSettings] Handlers[]). | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterRunner` | class | `extension/cjw_newsletter/classes/cjwnewsletterrunner.php:16` | – | – |
| `CjwNewsletterScheduleConditionNewArticles` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterscheduleconditionnewarticles.php:18` | The run goes ahead only when the article pool of the schedule has content published since its last send (the anonymous user's view of it), in either… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ScheduleSettings] ConditionHandlers[] |
| `CjwNewsletterScheduleLog` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedulelog.php:20` | One run of a schedule: sent, skipped (empty, condition) or failed. | – |
| `CjwNewsletterScheduleRunner` | class | `extension/cjw_newsletter/classes/editorial/cjwnewsletterschedulerunner.php:32` | Runs the recurring sends whose time has come (cjwnl_schedule): from the queue-create part of the cronjob (the extension point queueCreateBefore, when… | – |
| `CjwNewsletterSendBatch` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettersendbatch.php:20` | One batch of a send: which items it took, how far it got (resumable), its counts. | – |
| `CjwNewsletterSms` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersms.php:25` | The SMS channel of cjw_newsletter: settings, the transports, phone numbers (E.164), the confirmation code (the double opt-in of a number), the STOP k… | – |
| `CjwNewsletterSmsCode` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmscode.php:20` | A confirmation code sent by SMS (stored as a hash), with attempts and expiry. | – |
| `CjwNewsletterSmsHooks` | class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmshooks.php:27` | The handler of area N5 (SMS) at the extension points (CjwNewsletterExtensionPoints), registered in cjw_newsletter.ini [ExtensionPointSettings] Handle… | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterSmsInbound` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmsinbound.php:20` | One SMS that came in (a STOP keyword, a code), and what was done with it. | – |
| `CjwNewsletterSmsMessage` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/sms/cjwnewslettersmsmessage.php:20` | One SMS of an SMS edition to one newsletter user. | – |
| `CjwNewsletterStatisticsHooks` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticshooks.php:31` | The extension point handler of area N4 Statistics ([ExtensionPointSettings] Handlers[]=CjwNewsletterStatisticsHooks). | extension/cjw_newsletter/settings/cjw_newsletter.ini [ExtensionPointSettings] Handlers[] |
| `CjwNewsletterStatisticsReport` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticsreport.php:18` | The numbers of the statistics pages (cjw_newsletter 4.2.0, area N4 Statistics): the report of a send, the dashboard block, the article box and the CS… | – |
| `CjwNewsletterStatisticsRetention` | class | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstatisticsretention.php:25` | How long per-person statistics are kept (cjw_newsletter 4.2.0, area N4 Statistics). | – |
| `CjwNewsletterStatTotal` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/statistics/cjwnewsletterstattotal.php:20` | Anonymous totals per send, link, type and day; kept after the per-person rows are removed. | – |
| `CjwNewsletterSubscriberExport` | class | `extension/cjw_newsletter/classes/importexport/cjwnewslettersubscriberexport.php:22` | The subscribers of one list as CSV, with filters (subscription status, a date range on a chosen date) and a choice of columns. | – |
| `CjwNewsletterSuppressionImport` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewslettersuppressionimport.php:24` | Imports a CSV file of addresses into the kernel suppression list (expMailSuppression): a do-not-contact list, the bounces of an earlier mail system,… | – |
| `CjwNewsletterTestGroup` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewslettertestgroup.php:20` | A named group of test addresses for test sends of a list (or of every list). | – |
| `CjwNewsletterTestSend` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewslettertestsend.php:24` | Test sends to a named group of addresses (cjwnl_test_group, cjw_newsletter.ini [TestSendSettings]). | – |
| `CjwNewsletterThrottle` | class | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterthrottle.php:30` | The rate limits of the transports (cjw_newsletter.ini [ThrottleSettings]): how many mails (or SMS) a transport may send in the current minute and the… | – |
| `CjwNewsletterThrottleState` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/deliverability/cjwnewsletterthrottlestate.php:20` | The sending rate of one transport in the current minute or hour window, and a pause. | – |
| `CjwNewsletterTracking` | class | `extension/cjw_newsletter/classes/statistics/cjwnewslettertracking.php:33` | Open and click tracking of the newsletter editions (cjw_newsletter 4.2.0, area N4 Statistics). | – |
| `CjwNewsletterTranslatableFields` | class | `extension/cjw_newsletter/classes/rendering/cjwnewslettertranslatablefields.php:21` | The text fields of the newsletter classes are translatable (cjw_newsletter 4.2.0): one edition with translations, each subscriber gets his language. | – |
| `CjwNewsletterUI` | class | `extension/cjw_newsletter/classes/cjwnewsletterui.php:15` | – | – |
| `CjwNewsletterUserInterest` | class extends eZPersistentObject | `extension/cjw_newsletter/classes/rendering/cjwnewsletteruserinterest.php:20` | An interest a newsletter user picked. | – |
| `cjw_newsletterInfo` | class | `extension/cjw_newsletter/ezinfo.php:16` | Class description here | – |

### extension/cjw_themes_jumper (2)

[All patterns of extension/cjw_themes_jumper](area-extension-cjw-themes-jumper.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjwJumperFormHoneypot` | class | `extension/cjw_themes_jumper/classes/cjwjumperformhoneypot.php:20` | The honeypot of the feedback forms of the design starter (spam protection, together with the form token of ezformtoken). | – |
| `cjwOsmTileProxy` | class | `extension/cjw_themes_jumper/classes/cjwosmtileproxy.php:13` | The map tiles of the osm_proxy module: validation, the local tile cache and the fetch from the tile server. | – |

### extension/cjw_tmv (2)

[All patterns of extension/cjw_tmv](area-extension-cjw-tmv.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `cjwTmvFunctionCollection` | class | `extension/cjw_tmv/classes/cjwtmvfunctioncollection.php:10` | The fetch functions of the cjw_tmv module (modules/cjw_tmv/function_definition.php). | – |
| `cjwTmvHtml` | class | `extension/cjw_tmv/classes/cjwtmvhtml.php:12` | Cleans the HTML of a TMV event text before it is cached and drawn: only paragraphs, line breaks, emphasis, lists, small headings and links (http, htt… | – |

### extension/enhancedselection2 (1)

[All patterns of extension/enhancedselection2](area-extension-enhancedselection2.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `SckEnhancedSelection` | class extends eZPersistentObject | `extension/enhancedselection2/classes/sckenhancedselection.php:3` | – | – |

### extension/expchangeclass (2)

[All patterns of extension/expchangeclass](area-extension-expchangeclass.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `conversionFunctions` | class | `extension/expchangeclass/classes/functions.php:39` | – | – |
| `expChangeClassConverters` | class | `extension/expchangeclass/classes/converters.php:21` | – | extension/expchangeclass/settings/changeclass.ini [ezobjectrelationlist] Class; extension/expchangeclass/settings/changeclass.ini [ezstring] Class; extension/expchangeclass/settings/changeclass.ini [eztext] Class |

### extension/explayouts (51)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsAjaxLoadMoreServer` | class | `extension/explayouts/classes/explayoutsajaxloadmoreserver.php:7` | ezjscServer functions for loading paged collection blocks. | extension/explayouts/settings/ezjscore.ini [ezjscServer_expajaxloadmore] Class; extension/sevenx_themes_media/settings/siteaccess/site/ezjscore.ini [ezjscServer_expajaxloadmore] Class |
| `expLayoutsBlockHandlerFactory` | class | `extension/explayouts/classes/explayoutsblockhandlerfactory.php:2` | – | – |
| `expLayoutsBlockParameter` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblockparameter.php:2` | – | – |
| `expLayoutsCollection` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollection.php:2` | – | – |
| `expLayoutsCollectionItem` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionitem.php:2` | – | – |
| `expLayoutsCollectionQuery` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionquery.php:2` | – | – |
| `expLayoutsDynamicCollection` | class | `extension/explayouts/classes/explayoutsdynamiccollection.php:15` | Executes 'dynamic' collections (collection queries imported from Netgen Layouts). | – |
| `expLayoutsExponential` | class | `extension/explayouts/classes/explayoutsexponential.php:4` | – | – |
| `expLayoutsExponentialorter` | class | `extension/explayouts/classes/explayoutsexporter.php:2` | – | – |
| `expLayoutsExpRelationListQuery` | class | `extension/explayouts/classes/explayoutsexprelationlistquery.php:6` | Exponential 4 port of Netgen Layouts Exp RelationListQuery handler. | – |
| `expLayoutsExpReverseRelationListQuery` | class | `extension/explayouts/classes/explayoutsexpreverserelationlistquery.php:6` | Exponential 4 port of Netgen Layouts Exp ReverseRelationListQuery handler. | – |
| `expLayoutsExpSiteApi` | class | `extension/explayouts/classes/explayoutsexpsiteapi.php:3` | – | – |
| `expLayoutsExpTagsQuery` | class | `extension/explayouts/classes/explayoutsexptagsquery.php:6` | Exponential 4 port of Netgen Layouts Exp TagsQuery handler. | – |
| `expLayoutsExpUtils` | class | `extension/explayouts/classes/explayoutsexputils.php:4` | – | – |
| `expLayoutsFixtures` | class | `extension/explayouts/classes/explayoutsfixtures.php:2` | – | – |
| `expLayoutsImporter` | class | `extension/explayouts/classes/explayoutsimporter.php:2` | – | – |
| `expLayoutsInstall` | class | `extension/explayouts/classes/explayoutsinstall.php:2` | – | – |
| `expLayoutsLanguageResolver` | class | `extension/explayouts/classes/explayoutslanguageresolver.php:6` | Exponential 4 language resolver compatible with the Exp Site API LanguageResolver pattern. | – |
| `expLayoutsLayoutType` | class | `extension/explayouts/classes/explayoutslayouttype.php:2` | – | – |
| `expLayoutsLinkParameter` | class | `extension/explayouts/classes/explayoutslinkparameter.php:23` | The link parameter shared by the blocks that can carry one. | – |
| `expLayoutsMailHelper` | class | `extension/explayouts/classes/explayoutsmailhelper.php:6` | Exponential 4 port of Netgen SiteBundle Helper/MailHelper. | – |
| `expLayoutsQueryHandlerFactory` | class | `extension/explayouts/classes/explayoutsqueryhandlerfactory.php:2` | – | – |
| `expLayoutsQueryType` | class | `extension/explayouts/classes/explayoutsquerytype.php:9` | Exponential 4 query builders compatible with the Exp Site API QueryType pattern. | – |
| `expLayoutsRelationListQueryHandler` | class | `extension/explayouts/classes/explayoutsrelationlistqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_relation_list] Handler |
| `expLayoutsRenderer` | class | `extension/explayouts/classes/explayoutsrenderer.php:2` | – | – |
| `expLayoutsResolver` | class | `extension/explayouts/classes/explayoutsresolver.php:2` | – | – |
| `expLayoutsReverseRelationListQueryHandler` | class | `extension/explayouts/classes/explayoutsreverserelationlistqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_reverse_relation_list] Handler |
| `expLayoutsSiteAPI` | class | `extension/explayouts/classes/explayoutssiteapi.php:10` | Exponential 4 / Exponential Site API compatibility layer. | – |
| `expLayoutsSiteBundleCommand` | class | `extension/explayouts/classes/explayoutssitebundlecommand.php:4` | – | – |
| `expLayoutsSiteBundleContentForms` | class | `extension/explayouts/classes/explayoutssitebundlecontentforms.php:4` | – | – |
| `expLayoutsSiteBundleController` | class | `extension/explayouts/classes/explayoutssitebundlecontroller.php:4` | – | – |
| `expLayoutsSiteBundleCore` | class | `extension/explayouts/classes/explayoutssitebundlecore.php:4` | – | – |
| `expLayoutsSiteBundleDataCollector` | class | `extension/explayouts/classes/explayoutssitebundledatacollector.php:4` | – | – |
| `expLayoutsSiteBundleDebug` | class | `extension/explayouts/classes/explayoutssitebundledebug.php:4` | – | – |
| `expLayoutsSiteBundleDependencyInjection` | class | `extension/explayouts/classes/explayoutssitebundledependencyinjection.php:4` | – | – |
| `expLayoutsSiteBundleEvent` | class | `extension/explayouts/classes/explayoutssitebundleevent.php:4` | – | – |
| `expLayoutsSiteBundleEventListener` | class | `extension/explayouts/classes/explayoutssitebundleeventlistener.php:4` | – | – |
| `expLayoutsSiteBundleImagine` | class | `extension/explayouts/classes/explayoutssitebundleimagine.php:4` | – | – |
| `expLayoutsSiteBundleInfoCollection` | class | `extension/explayouts/classes/explayoutssitebundleinfocollection.php:4` | – | – |
| `expLayoutsSiteBundleLayouts` | class | `extension/explayouts/classes/explayoutssitebundlelayouts.php:4` | – | – |
| `expLayoutsSiteBundleMenu` | class | `extension/explayouts/classes/explayoutssitebundlemenu.php:4` | – | – |
| `expLayoutsSiteBundlePagerfanta` | class | `extension/explayouts/classes/explayoutssitebundlepagerfanta.php:4` | – | – |
| `expLayoutsSiteBundleQueryType` | class | `extension/explayouts/classes/explayoutssitebundlequerytype.php:4` | – | – |
| `expLayoutsSiteBundleRelation` | class | `extension/explayouts/classes/explayoutssitebundlerelation.php:4` | – | – |
| `expLayoutsSiteBundleRichText` | class | `extension/explayouts/classes/explayoutssitebundlerichtext.php:4` | – | – |
| `expLayoutsSiteBundleSecurity` | class | `extension/explayouts/classes/explayoutssitebundlesecurity.php:4` | – | – |
| `expLayoutsSiteBundleTopic` | class | `extension/explayouts/classes/explayoutssitebundletopic.php:4` | – | – |
| `expLayoutsTagsQueryHandler` | class | `extension/explayouts/classes/explayoutstagsqueryhandler.php:3` | – | extension/explayouts/settings/explayouts.ini [QueryType_exp_content_tags] Handler |
| `expLayoutsTemplateEditor` | class | `extension/explayouts/classes/explayoutstemplateeditor.php:2` | – | – |
| `ExplBlockDebug` | class | `extension/explayouts/event/explblockdebug.php:15` | response/output event listener that renders an expl block debug overlay when the page is requested with ?expl_debug=1. | – |
| `ExplBlockParser` | class | `extension/explayouts/classes/explblockparser.php:15` | ExplBlockParser — pure parser for {explblock} comment markers. | – |

### extension/explayouts_core (2)

[All patterns of extension/explayouts_core](area-extension-explayouts-core.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsCoreTransferDiff` | class | `extension/explayouts_core/classes/explayoutscoretransferdiff.php:23` | Compares the documents of a layouts transfer archive with the same documents exported from the target installation, and plans what an import does wit… | – |
| `expLayoutsCoreTransferFormat` | class | `extension/explayouts_core/classes/explayoutscoretransferformat.php:19` | The layouts transfer file: a ZIP holding JSON documents and a manifest. | – |

### extension/explayouts_relation_list_query (1)

[All patterns of extension/explayouts_relation_list_query](area-extension-explayouts-relation-list-query.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsRelationListQueryFactory` | class | `extension/explayouts_relation_list_query/classes/explayoutsrelationlistqueryfactory.php:2` | – | – |

### extension/explayouts_ui (2)

[All patterns of extension/explayouts_ui](area-extension-explayouts-ui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsUiRuleInput` | class | `extension/explayouts_ui/classes/explayoutsuiruleinput.php:9` | The targets and conditions of a mapping as posted by the rule forms (TargetType[]/TargetValue[], ConditionType[]/ConditionValue[]): only the types th… | – |
| `expLayoutsUiTemplateEditor` | class extends expLayoutsTemplateEditor | `extension/explayouts_ui/classes/explayoutsuitemplateeditor.php:17` | The template editor of explayouts_ui (explayouts_ui/template_editor), on top of explayouts' expLayoutsTemplateEditor (which decides which folders may… | – |

### extension/explayouts_ui_api (1)

[All patterns of extension/explayouts_ui_api](area-extension-explayouts-ui-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsUIApplicationApi` | class | `extension/explayouts_ui_api/classes/explayoutsuiapplicationapi.php:2` | – | – |

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
| `expCommerceExport` | class | `extension/expservices/classes/commerce/expcommerceexport.php:11` | Plain-array encoders of the shop objects, shared by the commerce services. | – |
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

### extension/expsite_api (1)

[All patterns of extension/expsite_api](area-extension-expsite-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSiteApi` | class | `extension/expsite_api/classes/expsiteapi.php:2` | – | – |

### extension/expsite_installer (1)

[All patterns of extension/expsite_installer](area-extension-expsite-installer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSiteInstaller` | class | `extension/expsite_installer/classes/expsiteinstaller.php:6` | Super installer for hourly site developers: one command to set everything up. | – |

### extension/expui (1)

[All patterns of extension/expui](area-extension-expui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expUIServerFunctions` | class | `extension/expui/classes/expuiserverfunctions.php:28` | – | extension/expui/settings/ezjscore.ini [ezjscServer_exp] Class |

### extension/exp_oe_tiptap (2)

[All patterns of extension/exp_oe_tiptap](area-extension-exp-oe-tiptap.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOETiptapEditor` | class | `extension/exp_oe_tiptap/classes/expoetiptapeditor.php:23` | Tiptap as an engine of ezoe's editor registry, and what the edit template needs to know about it. | – |
| `expOETiptapServerFunctions` | class extends ezjscServerFunctions | `extension/exp_oe_tiptap/classes/expoetiptapserverfunctions.php:29` | ezjscore server functions of the Tiptap editor (ezjscore.ini [ezjscServer_expoetiptap]). | extension/exp_oe_tiptap/settings/ezjscore.ini [ezjscServer_expoetiptap] Class |

### extension/ezautosave (1)

[All patterns of extension/ezautosave](area-extension-ezautosave.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscServerFunctionsAutosave` | class extends ezjscServerFunctions | `extension/ezautosave/classes/ezjscserverfunctionsautosave.php:16` | This class handles AJAX calls for the autosave feature | extension/ezautosave/settings/ezjscore.ini [ezjscServer_ezautosave] Class |

### extension/ezfind (3)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFindQueryText` | class | `extension/ezfind/classes/ezfindquerytext.php:29` | eZFindQueryText | – |
| `eZFindServerCallFunctions` | class | `extension/ezfind/classes/ezfindservercallfunctions.php:7` | Implements methods called remotely by sending XHR calls | extension/ezfind/settings/ezjscore.ini [ezjscServer_ezfind] Class |
| `ezfSolrUtils` | class | `extension/ezfind/classes/ezfsolrutils.php:16` | class ezfSolrUtils contains various methods to manipulate one or more Solr indexes with higher level functions | – |

### extension/ezflow (4)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFlowOperations` | class | `extension/ezflow/classes/ezflowoperations.php:28` | – | – |
| `eZFlowPool` | class | `extension/ezflow/classes/ezflowpool.php:28` | – | – |
| `eZFlowServerCallFunctions` | class | `extension/ezflow/classes/ezflowservercallfunctions.php:7` | Implements methods called remotely by sending XHR calls | extension/ezflow/settings/ezjscore.ini [ezjscServer_ezflow] Class |
| `eZSquidCacheManager` | class | `extension/ezflow/classes/ezsquidcachemanager.php:30` | – | extension/ezflow/settings/http.ini [HTTPCacheHandlers] Handlers[] |

### extension/ezformtoken (1)

[All patterns of extension/ezformtoken](area-extension-ezformtoken.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezxFormToken` | class | `extension/ezformtoken/event/ezxformtoken.php:23` | This class listens to interal kernel events in Exponential to validate forms using pr session tokens | – |

### extension/ezie (2)

[All patterns of extension/ezie](area-extension-ezie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZIEImageToolResize` | class extends eZIEImageAction | `extension/ezie/classes/image_tool_resize.php:11` | – | – |
| `eZIEImageToolWatermark` | class extends eZIEImageAction | `extension/ezie/classes/image_tool_watermark.php:11` | – | – |

### extension/ezjscore (3)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscServerFunctionsAjaxUploader` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsajaxuploader.php:20` | This class handles AJAX calls for the ajax uploader component | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezajaxuploader] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezajaxuploader] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezajaxuploader] Class |
| `ezjscServerFunctionsJs` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsjs.php:36` | – | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezjsc] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezjsc] Class |
| `ezjscServerFunctionsNode` | class extends ezjscServerFunctions | `extension/ezjscore/classes/ezjscserverfunctionsnode.php:36` | ezjscServerFunctionsNode class definition that provide node fetch functions | extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjscnode] Class; extension/sevenx_themes_simple/settings/siteaccess/sevenx_site_user/ezjscore.ini [ezjscServer_ezjscnode] Class; extension/sevenx_themes_simple/settings/siteaccess/site/ezjscore.ini [ezjscServer_ezjscnode] Class |

### extension/ezmultiupload (2)

[All patterns of extension/ezmultiupload](area-extension-ezmultiupload.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMultiuploadFunctionCollection` | class | `extension/ezmultiupload/classes/expmultiuploadfunctioncollection.php:10` | The template fetch functions of ezmultiupload (modules/ezmultiupload/function_definition.php). | – |
| `expMultiuploadService` | class | `extension/ezmultiupload/classes/expmultiuploadservice.php:20` | The server side of the upload page: checks before an upload (the target location, the content class each file becomes, the create permission for it t… | – |

### extension/ezoe (3)

[All patterns of extension/ezoe](area-extension-ezoe.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOEEditor` | class | `extension/ezoe/classes/expoeeditor.php:18` | The editor engine registry and the helpers every caller shares: the templates, the ezoe module, the ezjscore functions and the expservices class all… | – |
| `expOEUrlFetcher` | class | `extension/ezoe/classes/expoeurlfetcher.php:23` | Fetches a file from a URL for the upload tab of the online editor ("From a URL"). | – |
| `ezoeServerFunctions` | class extends ezjscServerFunctions | `extension/ezoe/classes/ezoeserverfunctions.php:35` | – | extension/ezoe/settings/ezjscore.ini [ezjscServer_ezoe] Class |

### extension/ezprestapi (1)

[All patterns of extension/ezprestapi](area-extension-ezprestapi.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRestContentModel` | class extends ezpRestModel | `extension/ezprestapi/classes/models/rest_content_model.php:13` | Model class containing backend method for REST content controller | – |

### extension/ezstarrating (1)

[All patterns of extension/ezstarrating](area-extension-ezstarrating.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezsrServerFunctions` | class extends ezjscServerFunctions | `extension/ezstarrating/classes/ezsrserverfunctions.php:36` | – | extension/ezstarrating/settings/ezjscore.ini [ezjscServer_ezstarrating] Class |

### extension/eztags (2)

[All patterns of extension/eztags](area-extension-eztags.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscTags` | class extends ezjscServerFunctions | `extension/eztags/classes/ezjsctags.php:6` | ezjscTags class implements eZ JS Core server functions for eztags | extension/eztags/settings/ezjscore.ini [ezjscServer_ezjsctags] Class |
| `eZTagsFunctionCollection` | class | `extension/eztags/modules/tags/eztagsfunctioncollection.php:9` | eZTagsFunctionCollection class implements fetch functions for eztags | – |

### extension/ezurlmultisite (1)

[All patterns of extension/ezurlmultisite](area-extension-ezurlmultisite.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZURLMultisite` | class | `extension/ezurlmultisite/classes/ezurlmultisite.php:27` | Cross-siteaccess link resolution for the ezurl template operator. | – |

### extension/git_manager (1)

[All patterns of extension/git_manager](area-extension-git-manager.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `GitManagerLog` | class | `extension/git_manager/classes/gitmanagerlog.php:17` | The commit log of git_manager/dashboard, one page at a time: the view parameters that say which page and which commits, the paging maths, and the arg… | – |

### extension/hcaptcha (1)

[All patterns of extension/hcaptcha](area-extension-hcaptcha.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `hCaptchaLibrary` | class | `extension/hcaptcha/classes/hcaptchalib.php:44` | The hCAPTCHA server URL's | – |

### extension/nxc_powercontent (1)

[All patterns of extension/nxc_powercontent](area-extension-nxc-powercontent.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZContentFunctionCollection` | class | `extension/nxc_powercontent/modules/content/ezcontentfunctioncollection.php:20` | – | – |

### extension/owsimpleoperator (1)

[All patterns of extension/owsimpleoperator](area-extension-owsimpleoperator.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `OWEzpAutoloader` | class extends ezpAutoloader | `extension/owsimpleoperator/autoloads/owezpautoloader.php:6` | – | – |

### extension/sevenx_authentication_2fa (6)

[All patterns of extension/sevenx_authentication_2fa](area-extension-sevenx-authentication-2fa.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZsevenxUser2faUser` | class extends eZUser | `extension/sevenx_authentication_2fa/login_handler/ezsevenxuser2fauser.php:29` | – | – |
| `sevenxAuthentication2faAttempts` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faattempts.php:27` | – | – |
| `sevenxAuthentication2faCrypto` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2facrypto.php:27` | – | – |
| `sevenxAuthentication2faEmail` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faemail.php:21` | – | – |
| `sevenxAuthentication2faRedirect` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faredirect.php:29` | – | – |
| `sevenxAuthentication2faTOTP` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fatotp.php:24` | – | – |

### extension/sevenx_dse (4)

[All patterns of extension/sevenx_dse](area-extension-sevenx-dse.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `AdminNeo\Drivers` | class | `extension/sevenx_dse/adminneo/core/Drivers.php:5` | – | – |
| `AdminNeo\FileModes` | class | `extension/sevenx_dse/adminneo/core/FileModes.php:11` | Gives the files and directories AdminNeo creates (compiled assets, lock files) their modes within the limits the Exponential kernel sets (EZP_FILE_MO… | – |
| `AdminNeo\Hash` | class | `extension/sevenx_dse/adminneo/core/Hash.php:5` | – | – |
| `AdminNeo\Random` | class | `extension/sevenx_dse/adminneo/core/Random.php:7` | – | – |

### extension/sevenx_themes_media (1)

[All patterns of extension/sevenx_themes_media](area-extension-sevenx-themes-media.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sevenxThemesMediaYouTube` | class | `extension/sevenx_themes_media/classes/sevenxthemesmediayoutube.php:22` | YouTube without contacting Google before the visitor chooses to play. | – |

### extension/syndication (4)

[All patterns of extension/syndication](area-extension-syndication.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZSyndicationInstaller` | class | `extension/syndication/classes/ezsyndicationinstaller.php:17` | – | – |
| `eZSyndicationJob` | class | `extension/syndication/classes/ezsyndicationjob.php:16` | – | – |
| `eZSyndicationRunner` | class | `extension/syndication/classes/ezsyndicationrunner.php:15` | – | – |
| `eZSyndicationUI` | class | `extension/syndication/classes/ezsyndicationui.php:15` | – | – |

### extension/xrowextract (18)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `XrowExtractArchive` | class | `extension/xrowextract/classes/xrowextractarchive.php:12` | The site archive: the objects of many classes below many nodes, one CSV file per class, with a manifest, packed as zip, tar.gz, tar.bz2, tar.xz, 7z o… | – |
| `XrowExtractCatalogue` | class | `extension/xrowextract/classes/xrowextractcatalogue.php:13` | The column catalogue beyond the class attributes as they are: | – |
| `XrowExtractColumns` | class | `extension/xrowextract/classes/xrowextractcolumns.php:8` | The columns of an export and their values: the special (non class attribute) columns, a class's attribute columns, and one object as a row of CSV cel… | – |
| `XrowExtractCron` | class | `extension/xrowextract/classes/xrowextractcron.php:16` | When a schedule runs: the simple choices of the Schedules page (hourly at minute M, daily at HH:MM, weekly on a day at HH:MM, monthly on a day at HH:… | – |
| `XrowExtractEntry` | class | `extension/xrowextract/classes/xrowextractentry.php:12` | The entry parameters of xrowextract/csv: the content tree's Advanced › Export menu opens the CSV form pre-filled for a node with xrowextract/csv/(nod… | – |
| `XrowExtractFetchAlias` | class | `extension/xrowextract/classes/xrowextractfetchalias.php:18` | Named fetches from fetchalias.ini usable in the extract view and the CLI: a "tree" / "list" / "tree_count" / "list_count" alias's Constant values rea… | – |
| `XrowExtractFunctionCollection` | class | `extension/xrowextract/classes/xrowextractfunctioncollection.php:4` | The xrowextract module's template fetch functions (modules/xrowextract/function_definition.php). | – |
| `XrowExtractImport` | class | `extension/xrowextract/classes/xrowextractimport.php:22` | The other direction of the export: reads a CSV or JSON file written by XrowExtractWriter (any column set, in particular "migration") back into conten… | – |
| `XrowExtractJob` | class | `extension/xrowextract/classes/xrowextractjob.php:16` | A background export job: one folder per job below the var directory (var//xrowextract-jobs//), holding job.json (state), progress.json (written by th… | – |
| `XrowExtractManifest` | class | `extension/xrowextract/classes/xrowextractmanifest.php:20` | The typed column manifest of an export: what every column of a file is (its id, the name in the file, the datatype, the chosen format and language),… | – |
| `XrowExtractNotifier` | class | `extension/xrowextract/classes/xrowextractnotifier.php:13` | What a schedule's owner (and whoever else it names) hears about a run: - e-mail on failure (a failed or skipped run, or a failed delivery): always to… | – |
| `XrowExtractPackage` | class | `extension/xrowextract/classes/xrowextractpackage.php:18` | Content-package (.ezpkg) support for xrowextract/package: inspecting a package's content classes and content objects before anything is written (the… | – |
| `XrowExtractPreset` | class | `extension/xrowextract/classes/xrowextractpreset.php:25` | A saved export preset: a complete, named CSV or archive export definition (scope, node(s), class or sets, columns, languages, every filter, sort, out… | – |
| `XrowExtractRequirements` | class | `extension/xrowextract/classes/xrowextractrequirements.php:19` | What the extension needs from PHP and the server, in one place: each requirement (a PHP version, a PHP extension, a function a hosting may disable, a… | – |
| `XrowExtractScheduler` | class | `extension/xrowextract/classes/xrowextractscheduler.php:15` | Runs schedules: finds the due ones (the cronjob part, cronjobs/xrowextract.php, or bin/php/schedule.php --cron), turns each into an ordinary backgrou… | – |
| `XrowExtractSchema` | class | `extension/xrowextract/classes/xrowextractschema.php:11` | The extension's own tables (xrowextract_schedule, xrowextract_destination, xrowextract_history), created lazily and idempotently the first time one o… | – |
| `XrowExtractSecrets` | class | `extension/xrowextract/classes/xrowextractsecrets.php:15` | Destination credentials at rest: libsodium secretbox (XSalsa20-Poly1305) with a 32 byte key kept in a key file outside the database. | – |
| `XrowExtractUpload` | class | `extension/xrowextract/classes/xrowextractupload.php:22` | A chunked upload: one folder per upload (var//xrowextract/uploads//), holding meta.json (owner, original name, total size, bytes received so far, whe… | – |

### extension/xrowmetadata (7)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `xrowMetaDataFunctions` | class | `extension/xrowmetadata/classes/xrowmetadatafunctions.php:3` | – | – |
| `xrowMetaDataLicense` | class | `extension/xrowmetadata/classes/xrowmetadatalicense.php:27` | The content licenses of xrowmetadata: the catalogue, the installation's allowlist, and which license a page has. | – |
| `xrowMetaDataLicenseHeader` | class | `extension/xrowmetadata/classes/xrowmetadatalicenseheader.php:28` | The license of a page as HTTP response headers: | – |
| `xrowMetaDataLicensePicker` | class | `extension/xrowmetadata/classes/xrowmetadatalicensepicker.php:15` | What the edit forms need to choose a license: the catalogue (xrowMetaDataLicense) as option groups for the picker, the inherited license (class defau… | – |
| `xrowMetaDataOpenGraphImage` | class | `extension/xrowmetadata/classes/xrowmetadataopengraphimage.php:16` | The Open Graph image of a metadata attribute: which object it is, whether that object can still be shown, and the picture of it that og:image and twi… | – |
| `xrowSitemapTools` | class | `extension/xrowmetadata/classes/xrowsitemaptools.php:3` | – | – |
| `xrowSiteUpdater` | class | `extension/xrowmetadata/classes/xrowsiteupdater.php:3` | – | – |

### kernel/classes (79)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAdminPagination` | class | `kernel/classes/expadminpagination.php:19` | One place to ask how many rows a list in the administration interface shows. | – |
| `expBenchmark` | class | `kernel/classes/expbenchmark.php:28` | The statistics, the report formats and the regression check of exp:benchmark. | – |
| `expBenchmarkHttp` | class | `kernel/classes/expbenchmarkhttp.php:20` | The HTTP side of exp:benchmark: requests a list of URLs, several at a time, with PHP's own curl (curl_multi), so no external program is needed. | – |
| `expBenchmarkKernel` | class | `kernel/classes/expbenchmarkkernel.php:29` | The in-process side of exp:benchmark: what the kernel spends on the parts of a page, without HTTP, a web server or a response cache in between. | – |
| `expBenchmarkMicro` | class | `kernel/classes/expbenchmarkmicro.php:33` | The micro mode of exp:benchmark: pure-PHP hot paths of the kernel, timed in this process without HTTP, a web server or a database, so it runs on a CI… | – |
| `expBookmarkPage` | class | `kernel/classes/expbookmarkpage.php:18` | What the bookmark page (content/bookmark) shows, worked out from plain arrays: the folders with their counts, which bookmarks a folder, a search and… | – |
| `expCacheWarm` | class | `kernel/classes/expcachewarm.php:28` | Keeps the response cache full, so that no visitor is the one who pays for a page to be rendered. | – |
| `expCollaborationGroupManager` | class | `kernel/classes/expcollaborationgroupmanager.php:14` | Creates, renames and deletes the collaboration groups of one user and moves items between them. | – |
| `expCollaborationInbox` | class | `kernel/classes/expcollaborationinbox.php:15` | The collaboration summary as an inbox: every item of the current user with its state (waiting, approved, denied), its role for the user (approver or… | – |
| `expContentDraftList` | class | `kernel/classes/expcontentdraftlist.php:20` | What the drafts page (content/draft, "My drafts") lists, worked out from plain arrays: the filters of the address (translation, class, age, search, o… | – |
| `expContentHistoryList` | class | `kernel/classes/expcontenthistorylist.php:22` | What the versions page of an object (content/history) lists, worked out from plain arrays: the filters of the address (status, translation, creator,… | – |
| `expContentLanguageStates` | class | `kernel/classes/expcontentlanguagestates.php:26` | The state of each language of one content object, for the language badges of admin4 (design:parts/ language_state_badge.tpl): the main language (the… | – |
| `expContentPendingList` | class | `kernel/classes/expcontentpendinglist.php:21` | What the pending page (content/pendinglist, "My pending items") lists, worked out from plain arrays: the versions the user sent for publishing that a… | – |
| `expCronjobPause` | class | `kernel/classes/expcronjobpause.php:27` | Pauses cronjob parts: one part, or all of them, until someone resumes them. | – |
| `expErrorHeaderList` | class | `kernel/classes/experrorheaderlist.php:25` | The response headers of an error page, from error.ini [HTTPError-]: | – |
| `expImageUploadValidator` | class | `kernel/classes/expimageuploadvalidator.php:35` | Decides whether an uploaded file is an image the site accepts, by what the file contains rather than by its name. | – |
| `ExpInstallationDetailsOutputFilter` | class | `kernel/classes/expinstallationdetailsoutputfilter.php:15` | Installation name output filter. | – |
| `expMaintenance` | class | `kernel/classes/expmaintenance.php:29` | Maintenance mode: while var/maintenance.json exists, every request to the front controllers is answered with a maintenance page (503, Retry-After, ne… | – |
| `expManagedConfiguration` | class | `kernel/classes/expmanagedconfiguration.php:25` | site.ini [ConfigurationSettings] ManagedByDeployment=enabled: settings, templates and code come from the deployment only, and the admin interface doe… | – |
| `expNodeQuickPick` | class | `kernel/classes/expnodequickpick.php:18` | Quick location buttons (doc/features/6.0/quick-location-buttons.md): a few well-known places of the content tree, found from a short key (Websites, M… | – |
| `expNotificationJob` | class | `kernel/classes/expnotificationjob.php:17` | A notification run for the status page ("Run now"), in the background. | – |
| `expNotificationService` | class | `kernel/classes/expnotificationservice.php:26` | The one place that knows how the notification system is run and looked at: the cronjob part, the exp:notification:* commands, the status page and its… | – |
| `expPackageConflictReport` | class | `kernel/classes/exppackageconflictreport.php:22` | What installing a package would meet on this installation, by remote id, without writing anything: for every content object of the package whether an… | – |
| `expPackageJson` | class | `kernel/classes/exppackagejson.php:34` | The JSON serialization of a package (doc/bc/6.0/package-json-format.md). | – |
| `expPhar` | class | `kernel/classes/expphar.php:21` | Packages the engine -- the kernel and library class files -- into a phar, so the runtime can load them from one archive instead of from a thousand se… | – |
| `expPolicyOmitList` | class | `kernel/classes/exppolicyomitlist.php:25` | The modules and views that skip the policy check: site.ini [RoleSettings] PolicyOmitList[] without the entries of [RoleSettings] PolicyOmitListExclud… | – |
| `expPreloadJob` | class | `kernel/classes/exppreloadjob.php:27` | A cache preload run: started in the background from Setup › Preload, or in the foreground from the shell (bin/php/preload.php), with one lock, one st… | – |
| `expProcessTools` | class | `kernel/classes/expprocesstools.php:22` | Finds the programs a background job needs: the PHP command line and setsid. | – |
| `expSecretRule` | class | `kernel/classes/expsecretrule.php:29` | The one rule of what is a secret, shared by everything that shows or records settings and values: exp:ini and expIniEditor, the audit log (through ex… | – |
| `expSetupLog` | class | `kernel/classes/expsetuplog.php:31` | var/log/setup.log: one readable record per installation run. | – |
| `expSetupWizardGuard` | class | `kernel/classes/expsetupwizardguard.php:26` | Decides what a request to the web setup wizard (setup/init) may do, before the wizard shows a page or touches maintenance mode. | – |
| `expSiteAccessOverview` | class | `kernel/classes/expsiteaccessoverview.php:31` | Every siteaccess of the installation, how a request reaches it and where it is: the siteaccess part of Setup › System information (#552). | – |
| `expSystemReportMask` | class | `kernel/classes/expsystemreportmask.php:29` | What Setup › System information, its text report and exp:system:info may show of a value. | – |
| `expTestLanguage` | class | `kernel/classes/exptestlanguage.php:17` | Test languages: locales marked [LanguageSettings] TestLanguage=true in share/locale (the shipped one is Lorem Ipsum, qlo-ZZ), and the lorem ipsum tex… | – |
| `expUserLoginIntegrity` | class | `kernel/classes/expuserloginintegrity.php:35` | One account per login: finds the logins more than one ezuser row has, adds the unique index on ezuser.login once there are none, and stores an accoun… | – |
| `expViewAccess` | class | `kernel/classes/expviewaccess.php:25` | Whether a user can open an address of this siteaccess, decided the way the kernel decides a request (ezpKernelWeb): the URL alias (and wildcard) tran… | – |
| `eZAudit` | class | `kernel/classes/ezaudit.php:20` | The 4.x audit API, kept so every existing call keeps working: eZAudit::writeAudit( $name, $attributes ) is recorded by expAudit (kernel/classes/audit… | – |
| `eZCache` | class | `kernel/classes/ezcache.php:22` | eZCache ezcache.php | – |
| `eZCacheTrash` | class | `kernel/classes/ezcachetrash.php:38` | Removes a cache directory by renaming it out of the way first. | – |
| `eZClusterFileHandler` | class | `kernel/classes/ezclusterfilehandler.php:11` | – | – |
| `eZCollaborationProfile` | class extends eZPersistentObject | `kernel/classes/ezcollaborationprofile.php:18` | – | – |
| `eZContentCache` | class | `kernel/classes/ezcontentcache.php:18` | – | – |
| `eZContentCacheManager` | class | `kernel/classes/ezcontentcachemanager.php:26` | – | – |
| `eZContentClassName` | class extends eZPersistentObject | `kernel/classes/ezcontentclassname.php:12` | – | – |
| `eZContentFunctions` | class | `kernel/classes/ezcontentfunctions.php:12` | – | – |
| `eZContentObjectTreeNodeOperations` | class | `kernel/classes/ezcontentobjecttreenodeoperations.php:22` | – | – |
| `eZDBGarbageCollector` | class | `kernel/classes/ezdbgarbagecollector.php:33` | – | – |
| `eZDiscountSubRuleValue` | class extends eZPersistentObject | `kernel/classes/ezdiscountsubrulevalue.php:17` | – | – |
| `eZHTTPHeader` | class | `kernel/classes/ezhttpheader.php:18` | – | – |
| `eZNavigationPart` | class | `kernel/classes/eznavigationpart.php:32` | – | – |
| `eZNodeviewfunctions` | class | `kernel/classes/eznodeviewfunctions.php:18` | – | – |
| `eZPackageCatalog` | class | `kernel/classes/ezpackagecatalog.php:23` | What the package pages say about the packages on disk: every repository with its counts, one card per package (name, version, type, summary, maintain… | – |
| `eZPackageComparison` | class | `kernel/classes/ezpackagecomparison.php:36` | Compares what a package carries with what the site has, for package/compare/. | – |
| `eZPackageComparisonDiff` | class | `kernel/classes/ezpackagecomparisondiff.php:20` | A word level difference of two texts, for the package comparison viewer (package/compare, eZPackageComparison): which words were removed from the fir… | – |
| `eZPackageComparisonImport` | class | `kernel/classes/ezpackagecomparisonimport.php:34` | Imports what a package brings for single items of its comparison with the site (eZPackageComparison, package/compare): a new object is created where… | – |
| `eZPackageDownload` | class | `kernel/classes/ezpackagedownload.php:25` | Sends a file of the package module (an exported archive, a file out of a package) to the browser in pieces. | – |
| `eZPackageFileBrowser` | class | `kernel/classes/ezpackagefilebrowser.php:17` | Read-only support for the package contents browser on package/view/full/: every file a package's own directory carries, paginated and filtered, and o… | – |
| `eZPackageLicense` | class | `kernel/classes/ezpackagelicense.php:28` | – | – |
| `eZPackageRemovalPlan` | class | `kernel/classes/ezpackageremovalplan.php:20` | What removing a selection of packages would do, before it is done: per package its directory, files and size, whether it is installed (its install re… | – |
| `eZPackageRequestGuard` | class | `kernel/classes/ezpackagerequestguard.php:16` | The checks every package view applies to what a request names before anything is read from disk: a package name, a repository, a view mode and a file… | – |
| `eZPackageUploadInspector` | class | `kernel/classes/ezpackageuploadinspector.php:31` | Looks at a package archive before anything of it is written into a repository. | – |
| `eZPackageVersion` | class | `kernel/classes/ezpackageversion.php:46` | – | – |
| `eZPendingActions` | class extends eZPersistentObject | `kernel/classes/ezpendingactions.php:12` | – | – |
| `eZPreferences` | class | `kernel/classes/ezpreferences.php:22` | – | – |
| `eZProductCategory` | class extends eZPersistentObject | `kernel/classes/ezproductcategory.php:18` | – | – |
| `eZRedirectManager` | class | `kernel/classes/ezredirectmanager.php:22` | – | – |
| `eZRSSExportItem` | class extends eZPersistentObject | `kernel/classes/ezrssexportitem.php:19` | – | – |
| `eZRunCronjobs` | class | `kernel/classes/ezruncronjobs.php:15` | – | – |
| `eZSearch` | class | `kernel/classes/ezsearch.php:19` | – | – |
| `eZSearchLog` | class | `kernel/classes/ezsearchlog.php:18` | – | – |
| `eZShippingManager` | class | `kernel/classes/ezshippingmanager.php:17` | – | – |
| `eZSiteAccess` | class | `kernel/classes/ezsiteaccess.php:17` | Provides functions for siteaccess handling | – |
| `eZSiteData` | class extends eZPersistentObject | `kernel/classes/ezsitedata.php:21` | eZPersistentObject implementation for ezsite_data table. | – |
| `eZSSLZone` | class | `kernel/classes/ezsslzone.php:27` | – | – |
| `eZSubtreeCache` | class | `kernel/classes/ezsubtreecache.php:18` | – | – |
| `eZTipafriendCounter` | class extends eZPersistentObject | `kernel/classes/eztipafriendcounter.php:18` | – | – |
| `eZTipafriendRequest` | class extends eZPersistentObject | `kernel/classes/eztipafriendrequest.php:17` | – | – |
| `eZVATManager` | class | `kernel/classes/ezvatmanager.php:18` | – | – |
| `eZWorkflowGroupLink` | class extends eZPersistentObject | `kernel/classes/ezworkflowgrouplink.php:18` | – | – |

### kernel/classes/apikey (2)

[All patterns of kernel/classes/apikey](area-kernel-classes-apikey.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expApiKeyFunctionCollection` | class | `kernel/classes/apikey/expapikeyfunctioncollection.php:13` | The fetch functions of the apikey module (kernel/apikey/function_definition.php). | – |
| `expApiKeySchema` | class | `kernel/classes/apikey/expapikeyschema.php:16` | The expapikey table on any engine: whether it exists, and creating it from its definition in share/db_schema.dba through the engine's schema handler… | – |

### kernel/classes/audit (18)

[All patterns of kernel/classes/audit](area-kernel-classes-audit.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAudit` | class | `kernel/classes/audit/expaudit.php:26` | The audit: what happens in Exponential, recorded as hash-chained JSON lines per channel (doc/bc/6.0/audit.md, "The developer API (Z3)"). | – |
| `expAuditAlertEvaluator` | class | `kernel/classes/audit/alerts/expauditalertevaluator.php:23` | Evaluates the alert rules (doc/bc/6.0/audit.md, "Alerts (F5)") and fires system.audit.alert. | – |
| `expAuditBridge` | class | `kernel/classes/audit/bridge/expauditbridge.php:24` | The ezpEvent bridge of the audit (doc/bc/6.0/audit.md, "The ezpEvent bridge", Z3): kernel and extension events that already exist (ezpEvent::getInsta… | – |
| `expAuditConfig` | class | `kernel/classes/audit/expauditconfig.php:21` | The audit settings (settings/audit.ini, doc/bc/6.0/audit.md "The settings reference") read once into one array. | – |
| `expAuditConsole` | class | `kernel/classes/audit/console/expauditconsole.php:16` | What the views of the audit module share (doc/bc/6.0/audit.md, "The console: module audit"): the channels the current user may read (audit/read and i… | – |
| `expAuditFormatRegistry` | class | `kernel/classes/audit/format/expauditformatregistry.php:16` | The archive format registry: [AuditArchiveSettings] FormatHandlers[]= (RAD survey registry "auditformats"). | – |
| `expAuditFunctionCollection` | class | `kernel/classes/audit/console/expauditfunctioncollection.php:22` | The fetch functions of the audit module (kernel/audit/function_definition.php; doc/bc/6.0/audit.md, "Template operator and fetch"): | – |
| `expAuditGuard` | class | `kernel/classes/audit/expauditguard.php:32` | [AuditSettings] OnWriteFailure (doc/bc/6.0/audit.md, "When the audit cannot write"): what happens to an action whose audit record cannot be written. | – |
| `expAuditHook` | class | `kernel/classes/audit/hook/expaudithook.php:25` | The kernel's audit call sites (doc/bc/6.0/audit.md, "The event catalogue", stage 3): one guarded way to record an event from a hook point, and the de… | – |
| `expAuditIndexRow` | class | `kernel/classes/audit/index/expauditindexrow.php:12` | One audit record as a row of expaudit_event (doc/bc/6.0/audit.md, "Tables"): the columns the console filters on, the record itself (as written, so th… | – |
| `expAuditIndexSchema` | class | `kernel/classes/audit/index/expauditindexschema.php:18` | The audit index tables (doc/bc/6.0/audit.md, "The index"): their definition in the generic schema format of share/db_schema.dba, creating them on an… | – |
| `expAuditIndexSettings` | class | `kernel/classes/audit/index/expauditindexsettings.php:12` | The settings of the audit index and console (audit.ini [AuditIndexSettings], [AuditConsoleSettings] and [AuditPrivacySettings] PseudonymiseAfterDays)… | – |
| `expAuditJson` | class | `kernel/classes/audit/expauditjson.php:16` | The canonical JSON of audit records (doc/bc/6.0/audit.md, "Canonical JSON"). | – |
| `expAuditMailRecipients` | class | `kernel/classes/audit/sinks/expauditmailrecipients.php:30` | Who gets the audit's alert mail (doc/bc/6.0/audit.md, "E-mail"; owner decision 2026-10-02). | – |
| `expAuditReauth` | class | `kernel/classes/audit/console/expauditreauth.php:26` | Password re-entry before the audit's manage actions (doc/bc/6.0/audit.md, Q9; [AuditConsoleSettings] ReauthForManage, ReauthMinutes). | – |
| `expAuditSinkRegistry` | class | `kernel/classes/audit/sinks/expauditsinkregistry.php:23` | The sink registry and delivery (doc/bc/6.0/audit.md, "Sinks (Q7)", "The sink registry"). | – |
| `expAuditSourceRegistry` | class | `kernel/classes/audit/console/expauditsourceregistry.php:16` | The registry of the audit console's sources (expAuditSource), from audit.ini [AuditSourceSettings] Sources[]: which are registered, which work here,… | – |
| `expAuditTaxonomy` | class | `kernel/classes/audit/expaudittaxonomy.php:21` | The audit taxonomy registry (doc/bc/6.0/audit.md, "Taxonomy", "Naming rules", "The event catalogue"). | – |

### kernel/classes/classicmenu (2)

[All patterns of kernel/classes/classicmenu](area-kernel-classes-classicmenu.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expClassicMenuSettings` | class | `kernel/classes/classicmenu/expclassicmenusettings.php:22` | Reads and writes the classic menu settings of one siteaccess: menu.ini [SelectedMenu] CurrentMenu, TopMenu and LeftMenu, which the page layouts of th… | – |
| `expClassicMenuSiteAccessInspector` | class | `kernel/classes/classicmenu/expclassicmenusiteaccessinspector.php:28` | Tells for a siteaccess whether its pages use the classic menu settings (menu.ini [SelectedMenu]). | – |

### kernel/classes/contentcheck (9)

[All patterns of kernel/classes/contentcheck](area-kernel-classes-contentcheck.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentCheck` | class | `kernel/classes/contentcheck/expcontentcheck.php:19` | What the two content checks share (doc/features/6.0/content-checks.md): their settings, where they answer, the policy check, the form tokens of the t… | – |
| `expContentCheckBrokenRelations` | class | `kernel/classes/contentcheck/expcontentcheckbrokenrelations.php:38` | Visible, published content that points at something a visitor cannot reach (doc/features/6.0/content-checks.md, "Broken relations"). | – |
| `expContentCheckFileFix` | class | `kernel/classes/contentcheck/expcontentcheckfilefix.php:25` | The resolutions of the missing files list (doc/features/6.0/content-checks.md, "Resolving"): each for one field version or for many, always planned f… | – |
| `expContentCheckFiles` | class | `kernel/classes/contentcheck/expcontentcheckfiles.php:26` | Missing files and image aliases (doc/features/6.0/content-checks.md, "Missing files"): the file-backed fields of the content below a node (images, fi… | – |
| `expContentCheckQuarantine` | class | `kernel/classes/contentcheck/expcontentcheckquarantine.php:25` | The actions of the storage check (doc/features/6.0/content-checks.md, "Quarantine"). | – |
| `expContentCheckScope` | class | `kernel/classes/contentcheck/expcontentcheckscope.php:24` | The start location of the content checks (doc/features/6.0/content-checks.md, "Where a check starts"): any node, from the whole tree (node 1) to one… | – |
| `expContentCheckStorage` | class | `kernel/classes/contentcheck/expcontentcheckstorage.php:28` | The storage check (doc/features/6.0/content-checks.md, "Storage files"): the files below var//storage that no content uses (orphan files), and the fi… | – |
| `expContentCheckTrash` | class | `kernel/classes/contentcheck/expcontentchecktrash.php:23` | Moves unreferenced objects to the trash (doc/features/6.0/content-checks.md, "Moving to the trash"): the list posts the ticked objects, a confirmatio… | – |
| `expContentCheckUnreferenced` | class | `kernel/classes/contentcheck/expcontentcheckunreferenced.php:31` | The assets below a node that nothing uses (doc/features/6.0/content-checks.md, "Unreferenced objects"): published objects of an asset class ([Unrefer… | – |

### kernel/classes/contentfinder (2)

[All patterns of kernel/classes/contentfinder](area-kernel-classes-contentfinder.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentFinderAction` | class | `kernel/classes/contentfinder/expcontentfinderaction.php:33` | The actions of the content finder (doc/features/6.0/content-finder.md, "Actions"): move an object to the trash, delete it permanently, purge it from… | – |
| `expContentFinderInput` | class | `kernel/classes/contentfinder/expcontentfinderinput.php:28` | What the content finder (module contentfinder, doc/features/6.0/content-finder.md) makes of its one search field: the kinds of id or path the text ca… | – |

### kernel/classes/contentjob (2)

[All patterns of kernel/classes/contentjob](area-kernel-classes-contentjob.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentJobLock` | class | `kernel/classes/contentjob/expcontentjoblock.php:30` | Subtree locks of the content jobs. | – |
| `expContentJobStore` | class | `kernel/classes/contentjob/expcontentjobstore.php:18` | The store of the content jobs: one JSON file per job in var//jobs/content/, written atomically (a temporary file in the same directory, flushed to di… | – |

### kernel/classes/datatypes (9)

[All patterns of kernel/classes/datatypes](area-kernel-classes-datatypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expCountryGroups` | class | `kernel/classes/datatypes/ezcountry/expcountrygroups.php:24` | The country list as people pick from it: first the groups of settings/countrygroups.ini (by default one group of the most used countries, in ranked o… | – |
| `eZImageFile` | class extends eZPersistentObject | `kernel/classes/datatypes/ezimage/ezimagefile.php:19` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZISBNGroup` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngroup.php:24` | – | – |
| `eZISBNGroupRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbngrouprange.php:26` | – | – |
| `eZISBNRegistrantRange` | class extends eZPersistentObject | `kernel/classes/datatypes/ezisbn/ezisbnregistrantrange.php:30` | – | – |
| `eZLDAPUser` | class extends eZUser | `kernel/classes/datatypes/ezuser/ezldapuser.php:20` | – | – |
| `eZURLObjectLink` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurlobjectlink.php:19` | – | – |
| `eZUserAccountKey` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuseraccountkey.php:19` | – | – |
| `eZUserLoginHandler` | class | `kernel/classes/datatypes/ezuser/ezuserloginhandler.php:19` | – | – |

### kernel/classes/debugbar (4)

[All patterns of kernel/classes/debugbar](area-kernel-classes-debugbar.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expDebugBarIPList` | class | `kernel/classes/debugbar/expdebugbariplist.php:28` | One line of DebugIPList[] is an entry: | – |
| `expDebugBarServerFunctions` | class extends ezjscServerFunctions | `kernel/classes/debugbar/expdebugbarserverfunctions.php:28` | The ezjscore server functions of the Exp Debug bar, registered as [ezjscServer_expdebugbar] in extension/ezjscore/settings/ezjscore.ini: | extension/ezjscore/settings/ezjscore.ini [ezjscServer_expdebugbar] Class |
| `expDebugBarSummary` | class | `kernel/classes/debugbar/expdebugbarsummary.php:20` | Collects what the request cost, from what eZDebug already measured: page time, SQL statements and their time (the *_query accumulators of the databas… | – |
| `expRenderStats` | class | `kernel/classes/debugbar/exprenderstats.php:19` | Collects, for the request being served, what the debug bar's Rendering tab shows: the controller (module, view, script and view class), what the cont… | – |

### kernel/classes/help (10)

[All patterns of kernel/classes/help](area-kernel-classes-help.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expHelp` | class | `kernel/classes/help/exphelp.php:11` | The help system's shared helpers (doc/features/6.0/help-system.md): its settings, whether it is on, the install root it reads from, and the URLs of i… | – |
| `expHelpContentSchema` | class | `kernel/classes/help/content/exphelpcontentschema.php:20` | The content model of help maintained as content (doc/specifications/6.0/help-system.md, doc/features/6.0/help-content.md): the eight help_* content c… | – |
| `expHelpFrontMatter` | class | `kernel/classes/help/exphelpfrontmatter.php:28` | The front matter of a help page (doc/features/6.0/help-system.md, "Front matter"): a block at the very top of a Markdown file between two lines of "-… | – |
| `expHelpFunctionCollection` | class | `kernel/classes/help/exphelpfunctioncollection.php:10` | The exphelp fetch functions (kernel/exphelp/function_definition.php). | – |
| `expHelpHighlighter` | class | `kernel/classes/help/exphelphighlighter.php:29` | Server-side syntax highlighting of the code blocks of the help pages (exphelp, doc/features/6.0/help-system.md), without JavaScript or an external li… | – |
| `expHelpLicense` | class | `kernel/classes/help/exphelplicense.php:17` | The license of the documentation the help shows (exphelp.ini [License], doc/README-LICENSE.md). | – |
| `expHelpLinks` | class | `kernel/classes/help/exphelplinks.php:12` | The link and image resolvers the viewer gives expHelpMarkdownRenderer (doc/features/6.0/help-system.md): a relative link to an allowed page becomes i… | – |
| `expHelpPdf` | class | `kernel/classes/help/exphelppdf.php:18` | A help page as a PDF file (doc/features/6.0/help-system.md, "Print and PDF"): the view exphelp/pdf/ and `./console exp:help pdf ` ask here. | – |
| `expHelpPdfFontSubset` | class | `kernel/classes/help/exphelppdffontsubset.php:17` | Cuts a TrueType font down to the glyphs a PDF uses (doc/features/6.0/help-system.md, "Print and PDF"). | – |
| `expHelpReadPolicyGrant` | class | `kernel/classes/help/exphelpreadpolicygrant.php:22` | Plans and applies the grant of the policy exphelp/read (bin/php/addexphelpreadpolicy.php): reading the help system (doc/features/6.0/help-system.md). | – |

### kernel/classes/ini (4)

[All patterns of kernel/classes/ini](area-kernel-classes-ini.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expIniCommand` | class | `kernel/classes/ini/actions/expinicommand.php:15` | The exp:ini command without its script: splitting the command line, the help texts, finding the action in the registry, running it with an expIniComm… | – |
| `expIniLocator` | class | `kernel/classes/ini/expinilocator.php:25` | Every file that sets a variable, in the order eZINI loads them for a siteaccess, and the effective value. | – |
| `expSettingsPage` | class | `kernel/classes/ini/expsettingspage.php:18` | The rows, figures, searches and comparisons of the settings pages, and what a write is followed by. | – |
| `expSettingsTarget` | class | `kernel/classes/ini/expsettingstarget.php:19` | The checks between a request and a settings file. | – |

### kernel/classes/logviewer (5)

[All patterns of kernel/classes/logviewer](area-kernel-classes-logviewer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLogActions` | class | `kernel/classes/logviewer/explogactions.php:13` | The actions of the log viewer besides reading (doc/features/6.0/log-viewer.md, "Actions"): download a range, rotate now, empty a file, remove an arch… | – |
| `expLogBundle` | class | `kernel/classes/logviewer/explogbundle.php:17` | "Download all logs" (Setup › Manage logs; doc/features/6.0/log-viewer.md, "Downloading every log"): one archive of every log the viewer lists, option… | – |
| `expLogHealth` | class | `kernel/classes/logviewer/exploghealth.php:17` | The health of the logs (Setup › Log files › Health, setup/logshealth; doc/features/6.0/log-viewer.md, "Health"): the disk the logs and their archives… | – |
| `expLogParser` | class | `kernel/classes/logviewer/explogparser.php:25` | Reads the lines of a log file as entries (doc/features/6.0/log-viewer.md, "The line format"). | – |
| `expLogPermalink` | class | `kernel/classes/logviewer/explogpermalink.php:23` | The permalinks of the log viewer (doc/features/6.0/log-viewer.md, "Permalinks"): every view of a log as a path without a query string, short enough t… | – |

### kernel/classes/mail (1)

[All patterns of kernel/classes/mail](area-kernel-classes-mail.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMailTemplateBody` | class | `kernel/classes/mail/expmailtemplatebody.php:16` | The body of a mail made from a template: the template's output, and what the template set at root scope. | – |

### kernel/classes/mailpreferences (10)

[All patterns of kernel/classes/mailpreferences](area-kernel-classes-mailpreferences.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expMailAddressChange` | class | `kernel/classes/mailpreferences/expmailaddresschange.php:20` | A new e-mail address for an account takes effect only once it is confirmed (double opt-in, mailpreferences.ini [EmailChangeSettings] Confirm=enabled,… | – |
| `expMailBounceReader` | class | `kernel/classes/mailpreferences/expmailbouncereader.php:36` | Reads the mailbox that bounces and complaints arrive in (mailpreferences.ini [BounceSettings], set in settings/override/mailpreferences.ini.append.ph… | – |
| `expMailCategoryRow` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailcategoryrow.php:15` | A category made or changed in the admin's category manager (table expmail_category). | – |
| `expMailGate` | class | `kernel/classes/mailpreferences/expmailgate.php:34` | The central mail gate. | – |
| `expMailPreferenceRow` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailpreferencerow.php:14` | One stored preference (table expmail_preference): a recipient (recipient_key 'u:' or 'a:'), a category, its state (on, off, pending) and frequency. | – |
| `expMailPreferencesService` | class | `kernel/classes/mailpreferences/expmailpreferencesservice.php:18` | The flows around the preferences: the double opt-in mail and its confirmation, the one-click unsubscribe, "send me a link" for people without login,… | settings/mailpreferences.ini [GateSettings] EssentialSenders[] |
| `expMailSecret` | class | `kernel/classes/mailpreferences/expmailsecret.php:20` | The site secret of the e-mail preference system: the key of the links in mail (expMailToken) and the salt of the address hashes (expMailSuppression,… | – |
| `expMailSenderDetails` | class | `kernel/classes/mailpreferences/expmailsenderdetails.php:27` | Who sends the optional mail: the organisation name and the postal address the mail gate puts in the footer of every optional mail (CAN-SPAM, CASL and… | – |
| `expMailSuppression` | class extends eZPersistentObject | `kernel/classes/mailpreferences/expmailsuppression.php:22` | The suppression list (table expmail_suppression): addresses no optional mail goes to (hard bounces, complaints, "unsubscribe from all", legal request… | – |
| `expMailToken` | class | `kernel/classes/mailpreferences/expmailtoken.php:24` | The links in mail that work without login: unsubscribe (one click, RFC 8058), manage (the preference page) and confirm (a double opt-in). | – |

### kernel/classes/notification (3)

[All patterns of kernel/classes/notification](area-kernel-classes-notification.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZNotificationCollectionItem` | class extends eZPersistentObject | `kernel/classes/notification/eznotificationcollectionitem.php:18` | – | – |
| `eZNotificationEventFilter` | class | `kernel/classes/notification/eznotificationeventfilter.php:17` | – | – |
| `eZNotificationSchedule` | class | `kernel/classes/notification/eznotificationschedule.php:18` | – | – |

### kernel/classes/pdfexport (4)

[All patterns of kernel/classes/pdfexport](area-kernel-classes-pdfexport.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expPDFExportFile` | class | `kernel/classes/pdfexport/exppdfexportfile.php:19` | The file of a PDF export that is generated once: its name, where it is kept, what is known about it, sending it and removing it. | – |
| `expPDFExportForm` | class | `kernel/classes/pdfexport/exppdfexportform.php:19` | Reads and checks the form of pdf/edit. | – |
| `expPDFExportGenerator` | class | `kernel/classes/pdfexport/exppdfexportgenerator.php:27` | Generates the PDF of an export: into its stored file, or into a temporary file that is then sent and removed. | – |
| `expPDFExportInfo` | class | `kernel/classes/pdfexport/exppdfexportinfo.php:19` | What the PDF export pages (pdf/list and its removal confirmation) say about an export beyond its row: how it is produced, whether its source node sti… | – |

### kernel/classes/requestlog (1)

[All patterns of kernel/classes/requestlog](area-kernel-classes-requestlog.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expRequestLog` | class | `kernel/classes/requestlog/exprequestlog.php:32` | The request log: at most one line per web request for a page that does not exist (404.log), with the reason the kernel found, and the hand-over to th… | – |

### kernel/classes/role (1)

[All patterns of kernel/classes/role](area-kernel-classes-role.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expRolePage` | class | `kernel/classes/role/exprolepage.php:19` | What the role pages (role/list, role/view, role/edit) work out besides the role itself: the rows of the role list with their search and order, what e… | – |

### kernel/classes/subitems (4)

[All patterns of kernel/classes/subitems](area-kernel-classes-subitems.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSubitemsColumnHandlers` | class | `kernel/classes/subitems/columns/expsubitemscolumnhandlers.php:16` | Example Handler= columns of the admin subitems list: plain static methods an INI block names as Handler=expSubitemsColumnHandlers::. | – |
| `expSubitemsCSVExport` | class | `kernel/classes/subitems/expsubitemscsvexport.php:14` | The CSV of the admin subitems list (content/subitemsexport/). | – |
| `expSubitemsPreference` | class | `kernel/classes/subitems/expsubitemspreference.php:20` | The per-user choice of the admin subitems list: visible columns in order and the chosen preset per navigation part, the page size, and the user's own… | – |
| `expSubitemsServerFunctions` | class extends ezjscServerFunctions | `kernel/classes/subitems/expsubitemsserverfunctions.php:24` | The ezjscore server functions of the admin subitems list ("Table options"), registered as [ezjscServer_expsubitems] in extension/ezjscore/settings/ez… | extension/ezjscore/settings/ezjscore.ini [ezjscServer_expsubitems] Class |

### kernel/classes/uiswitch (1)

[All patterns of kernel/classes/uiswitch](area-kernel-classes-uiswitch.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expUISwitch` | class | `kernel/classes/uiswitch/expuiswitch.php:28` | The UI switch framework: a registry of providers (uiswitch.ini [UISwitchSettings] ProviderList[]), the user's choices kept in the session, and the ea… | – |

### kernel/classes/user (5)

[All patterns of kernel/classes/user](area-kernel-classes-user.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expRegistrationAdminAction` | class | `kernel/classes/user/expregistrationadminaction.php:23` | The signed links of the administrator's "New user registered" mail (design:user/registrationfeedback.tpl) and the actions behind them (user/registrat… | – |
| `expUnactivatedUsers` | class | `kernel/classes/user/expunactivatedusers.php:22` | The users who registered and never activated their account, for the page user/unactivated: how many there are, a page of them searched by login, e-ma… | – |
| `expUserAccountStatus` | class | `kernel/classes/user/expuseraccountstatus.php:21` | The account status the administration marks on user objects: enabled, disabled (Setup › Users, ezuser_setting.is_enabled = 0) or locked (more failed… | – |
| `expUserActivation` | class | `kernel/classes/user/expuseractivation.php:25` | What follows the enabling of an account, for user/activate (the link the user clicks) and the Activate button of user/unactivated. | – |
| `expUserDeactivation` | class | `kernel/classes/user/expuserdeactivation.php:36` | "Deactivate and move" and "Reactivate and move back": instead of removing a user account (users never go to the trash, so removing one cannot be undo… | – |

### kernel/common (2)

[All patterns of kernel/common](area-kernel-common.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpI18n` | class | `kernel/common/ezpi18n.php:12` | – | – |
| `eZTemplatesStatisticsReporter` | class | `kernel/common/eztemplatesstatisticsreporter.php:19` | – | – |

### kernel/modules (26)

[All patterns of kernel/modules](area-kernel-modules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentExtensionWizard` | class extends expExtensionWizard | `kernel/setup/expcontentextensionwizard.php:27` | The content extension wizard: what authors, editors and translators see. | – |
| `expCronjobRunner` | class | `kernel/setup/expcronjobrunner.php:38` | Lists the cronjob parts this installation defines and launches one from the administration interface, leaving it to run after the request has finishe… | – |
| `expDatatypeWizard` | class extends expExtensionWizard | `kernel/setup/expdatatypewizard.php:22` | The datatype wizard: builds a working content datatype extension. | – |
| `expDesignExtensionWizard` | class extends expExtensionWizard | `kernel/setup/expdesignextensionwizard.php:21` | Builds a design extension: the directories, the settings and the templates that a working design needs, ready to switch on. | – |
| `expHandlerWizard` | class extends expExtensionWizard | `kernel/setup/exphandlerwizard.php:29` | Builds a handler: a class named by an ini setting that the kernel loads in place of its own. | – |
| `expKernelOverrideWizard` | class extends expExtensionWizard | `kernel/setup/expkerneloverridewizard.php:24` | The kernel override wizard: replacing a kernel class outright. | – |
| `expModuleExtensionWizard` | class extends expExtensionWizard | `kernel/setup/expmoduleextensionwizard.php:31` | Builds a module extension over tables that already exist. | – |
| `expModuleWizard` | class extends expExtensionWizard | `kernel/setup/expmodulewizard.php:28` | The module wizard: a module, its views, and the policies that reach them. | – |
| `expRADCatalogue` | class | `kernel/setup/expradcatalogue.php:29` | Every point this system can be extended at, and what it takes to extend it. | – |
| `expRADHealth` | class | `kernel/setup/expradhealth.php:23` | What is configured on this installation and cannot work. | – |
| `expRADSurvey` | class | `kernel/setup/expradsurvey.php:26` | A live survey of what this installation can be extended at. | – |
| `expSettingsExtensionWizard` | class extends expExtensionWizard | `kernel/setup/expsettingsextensionwizard.php:26` | The settings extension wizard: an extension that is mostly ini files. | – |
| `expTemplateExtensionWizard` | class extends expExtensionWizard | `kernel/setup/exptemplateextensionwizard.php:25` | The template extension wizard: everything a template can be taught to call. | – |
| `expWorkflowEventWizard` | class extends expExtensionWizard | `kernel/setup/expworkfloweventwizard.php:27` | Builds a workflow event type: a step a workflow can take when something is published, moved, removed, registered or bought. | – |
| `eZCheckClasses` | class | `kernel/setup/expclassloadcheck.php:26` | The parts of the check worth having in one place. | – |
| `eZClassFunctions` | class | `kernel/class/ezclassfunctions.php:14` | – | – |
| `eZInfocollectorFunctionCollection` | class | `kernel/infocollector/ezinfocollectorfunctioncollection.php:20` | – | – |
| `eZMultiEdit` | class | `kernel/content/multiedit_functions.php:16` | The working parts of content/multiedit. | – |
| `eZMultiPriceData` | class extends eZPersistentObject | `kernel/shop/classes/ezmultipricedata.php:12` | – | – |
| `eZRSSEditFunction` | class | `kernel/rss/edit_functions.php:14` | – | – |
| `eZRSSFunctionCollection` | class | `kernel/rss/ezrssfunctioncollection.php:14` | – | – |
| `eZRSSListPager` | class | `kernel/rss/ezrsslistpager.php:24` | Works out one page of a list, and the links that reach the other pages. | – |
| `eZShopFunctions` | class | `kernel/shop/classes/ezshopfunctions.php:12` | – | – |
| `eZShopReceipt` | class | `kernel/shop/classes/ezshopreceipt.php:35` | A permanent, bookmarkable address for a placed order: its receipt. | – |
| `eZUserOperationCollection` | class | `kernel/user/ezuseroperationcollection.php:20` | – | – |
| `eZWorkflowFunctions` | class | `kernel/workflow/ezworkflowfunctions.php:14` | – | – |

### kernel/private (18)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expApiKeyRest` | class | `kernel/private/rest/classes/auth/apikey_rest.php:24` | How the REST layer resolves a personal API key (doc/guides/api-keys.md, "For developers"). | – |
| `expContentAccessReport` | class | `kernel/private/classes/expcontentaccessreport.php:23` | Answers "may this user do this with this object or node, and if not, which limitation refuses it": the access checks of the kernel made for another u… | – |
| `expContentSecurityPolicy` | class | `kernel/private/classes/expcontentsecuritypolicy.php:39` | The Content Security Policy of a page, from site.ini [ContentSecurityPolicy]. | – |
| `expOAuthAuthorizePolicy` | class | `kernel/private/rest/classes/oauth/authorize_policy.php:21` | Decides whether a signed in user may authorize an OAuth client in oauth/authorize. | – |
| `expOAuthAuthorizePolicyGrant` | class | `kernel/private/rest/classes/oauth/authorize_policy_grant.php:26` | Plans and applies the upgrade grant of the policy oauth/authorize (bin/php/addoauthauthorizepolicy.php). | – |
| `expRestContentPermission` | class | `kernel/private/rest/classes/auth/content_permission.php:36` | The current user's rights on the content a REST call reads or writes, checked the way the content module checks them (doc/guides/api-keys.md, "Permis… | – |
| `ezpAttributeOperatorManager` | class | `kernel/private/eztemplate/ezpattributeoperatormanager.php:12` | – | – |
| `ezpContentLimitation` | class | `kernel/private/classes/ezpcontentlimitation.php:26` | Asks the handler an extension registered for a content policy limitation the kernel does not know (site.ini [RoleSettings] LimitationHandlers[]=, see… | – |
| `ezpContentPublishingQueue` | class | `kernel/private/classes/ezpcontentpublishingqueue.php:19` | This class manages the content publishing queue. | settings/content.ini [PublishingSettings] AsynchronousPublishingQueueReader |
| `ezpContentRepository` | class | `kernel/private/api/content/repository.php:16` | This class handles querying of content repository | – |
| `ezpFormTokenRefusal` | class | `kernel/private/classes/ezpformtokenrefusal.php:27` | Turns an ezpFormTokenException -- a POST whose form token was missing or wrong -- into a refusal: HTTP 403, Cache-Control: no-store, the security hea… | – |
| `ezpOauthUtility` | class extends ezpRestModel | `kernel/private/rest/classes/oauth/utility.php:17` | Functionality for working against the draft 10 of the oauth2 spec. | – |
| `ezpRestContentRenderer` | class | `kernel/private/rest/classes/content_renderer.php:12` | – | – |
| `ezpRestDbConfig` | class | `kernel/private/rest/classes/lazy.php:12` | – | – |
| `ezpRestProvider` | class | `kernel/private/rest/classes/rest_provider.php:12` | – | – |
| `ezpRestRoutesCacheClear` | class | `kernel/private/rest/classes/cache/clear_routes.php:15` | Clear cache handler. | settings/site.ini [Cache_restRoutes] class; settings/site.ini [Cache_restRoutes] purgeClass |
| `ezpSiteAccessMenu` | class | `kernel/private/classes/ezpsiteaccessmenu.php:48` | The administration's site menu: the siteaccesses of this installation with their front page addresses, for the globe button in the admin4 header. | – |
| `ezpSiteAccessURL` | class | `kernel/private/classes/ezpsiteaccessurl.php:36` | The address of a siteaccess's front page, worked out from the siteaccess settings and the request the visitor is on: the admin's "open the site" link… | – |

### kernel/private/classes/clusterfilehandlers (1)

[All patterns of kernel/private/classes/clusterfilehandlers](area-kernel-private-classes-clusterfilehandlers.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDFSFileHandlerBackendFactory` | class | `kernel/private/classes/clusterfilehandlers/ezdfsfilehandlerbackendfactory.php:14` | Instantiates the DFSBackend based on INI settings | – |

### kernel/private/classes/commands (1)

[All patterns of kernel/private/classes/commands](area-kernel-private-classes-commands.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CommandLineArguments` | class | `kernel/private/classes/commands/ezconvertdbcharset.php:31` | Class used to store some of the command line arguments | – |

### kernel/private/classes/httpcache (1)

[All patterns of kernel/private/classes/httpcache](area-kernel-private-classes-httpcache.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpHttpCacheListener` | class | `kernel/private/classes/httpcache/ezphttpcachelistener.php:27` | The kernel side of the role-aware HTTP cache: stores rendered content views for the early exit and Velocity's parent to serve, and turns the kernel's… | – |

### kernel/private/classes/requestrules (1)

[All patterns of kernel/private/classes/requestrules](area-kernel-private-classes-requestrules.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpRequestRuleKernel` | class | `kernel/private/classes/requestrules/ezprequestrulekernel.php:18` | Where the request rules meet the kernel: the facts of a real request (context()) and the response for a decision (moduleResult()). | – |

### kernel/private/classes/services (8)

[All patterns of kernel/private/classes/services](area-kernel-private-classes-services.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Service\ContentJobDetails` | class | `kernel/private/classes/services/contentjobdetails.php:17` | What the content job pages show about a job beyond its progress: who started it (and on whose behalf), the times, the duration and an estimate of the… | – |
| `Exponential\Service\DraftsCleanup` | class | `kernel/private/classes/services/draftscleanup.php:22` | Removing drafts older than their configured lifetime: one service for the two cronjob parts that did it with the same code and different settings, cr… | – |
| `Exponential\Service\MailPreferencesPage` | class | `kernel/private/classes/services/mailpreferencespage.php:27` | What the views of the mailpreferences module share: the preference page (signed in, by a personal link, or an administrator for a user), the boxes on… | – |
| `Exponential\Service\ReverseRelationCheck` | class | `kernel/private/classes/services/reverserelationcheck.php:22` | Which other objects point at the content a removal or a hide is about to take away: the reverse relations (ezcontentobject_link) of the objects that… | – |
| `Exponential\Service\SessionGarbageCollector` | class | `kernel/private/classes/services/sessiongarbagecollector.php:20` | Removing expired sessions: one service for the sessions view (setup/session, "Remove timed out sessions"), the command (bin/php/ezsessiongc.php) and… | – |
| `Exponential\Service\Trash` | class | `kernel/private/classes/services/trash.php:23` | Emptying the trash: one service for the trash view (content/trash), the command (bin/php/trashpurge.php) and the cronjob part (cronjobs/trashpurge.ph… | – |
| `Exponential\Service\TrashList` | class | `kernel/private/classes/services/trashlist.php:27` | What the trash view (content/trash) shows about each item, its filters and its summary line. | – |
| `Exponential\Service\TrashRecord` | class | `kernel/private/classes/services/trashrecord.php:30` | The file where who moved an object to the trash, and from where, was kept before the trash rows had the columns trashed_by and trashed_via. | – |

### lib (1)

[All patterns of lib](area-lib.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ExponentialSDK` | class | `lib/version.php:21` | – | – |

### lib/ezdb (2)

[All patterns of lib/ezdb](area-lib-ezdb.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDBQueryCache` | class | `lib/ezdb/classes/ezdbquerycache.php:37` | A result cache for the SQL drivers (MySQL/MariaDB, PostgreSQL, SQLite): the rows a SELECT returned, answered again until a write to one of the tables… | – |
| `eZDBTool` | class | `lib/ezdb/classes/ezdbtool.php:18` | – | – |

### lib/ezdbschema (2)

[All patterns of lib/ezdbschema](area-lib-ezdbschema.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDbSchema` | class | `lib/ezdbschema/classes/ezdbschema.php:19` | – | – |
| `eZDbSchemaChecker` | class | `lib/ezdbschema/classes/ezdbschemachecker.php:19` | – | – |

### lib/ezfile (4)

[All patterns of lib/ezfile](area-lib-ezfile.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLogRotator` | class | `lib/ezfile/classes/explogrotator.php:28` | The one implementation of log rotation in the kernel (doc/features/6.0/log-rotation.md). | – |
| `eZDir` | class | `lib/ezfile/classes/ezdir.php:17` | – | – |
| `eZFile` | class | `lib/ezfile/classes/ezfile.php:18` | – | – |
| `eZLog` | class | `lib/ezfile/classes/ezlog.php:21` | – | – |

### lib/ezi18n (3)

[All patterns of lib/ezi18n](area-lib-ezi18n.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCharsetInfo` | class | `lib/ezi18n/classes/ezcharsetinfo.php:25` | – | – |
| `eZTranslationCache` | class | `lib/ezi18n/classes/eztranslationcache.php:18` | – | – |
| `eZUTF8Codec` | class | `lib/ezi18n/classes/ezutf8codec.php:22` | – | – |

### lib/ezimage (1)

[All patterns of lib/ezimage](area-lib-ezimage.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSvgSanitizer` | class | `lib/ezimage/classes/expsvgsanitizer.php:51` | Makes an SVG image safe to store and serve from the site's own origin, by allow-list. | – |

### lib/ezlocale (1)

[All patterns of lib/ezlocale](area-lib-ezlocale.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZDateUtils` | class | `lib/ezlocale/classes/ezdateutils.php:18` | – | – |

### lib/eztemplate (5)

[All patterns of lib/eztemplate](area-lib-eztemplate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTemplateCacheBlock` | class | `lib/eztemplate/classes/eztemplatecacheblock.php:18` | – | – |
| `eZTemplateCompiler` | class | `lib/eztemplate/classes/eztemplatecompiler.php:49` | – | – |
| `eZTemplateNodeTool` | class | `lib/eztemplate/classes/eztemplatenodetool.php:19` | – | – |
| `eZTemplateOptimizer` | class | `lib/eztemplate/classes/eztemplateoptimizer.php:18` | – | – |
| `eZTemplateTreeCache` | class | `lib/eztemplate/classes/eztemplatetreecache.php:18` | – | – |

### lib/ezutils (14)

[All patterns of lib/ezutils](area-lib-ezutils.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expDebugBarReport` | class | `lib/ezutils/classes/expdebugbarreport.php:30` | The HTML debug report as the "Exp Debug" bar: a bar pinned to the bottom of the window with a live summary of the request, and a tabbed panel above i… | – |
| `expInfo` | class | `lib/ezutils/classes/expinfo.php:21` | – | – |
| `eZDateTimeValidator` | class extends eZInputValidator | `lib/ezutils/classes/ezdatetimevalidator.php:18` | – | – |
| `eZDebugSetting` | class | `lib/ezutils/classes/ezdebugsetting.php:39` | Provides conditional debug output | – |
| `eZExecution` | class | `lib/ezutils/classes/ezexecution.php:34` | – | – |
| `eZExtension` | class | `lib/ezutils/classes/ezextension.php:18` | – | – |
| `eZFunctionHandler` | class | `lib/ezutils/classes/ezfunctionhandler.php:18` | – | – |
| `eZMath` | class | `lib/ezutils/classes/ezmath.php:21` | – | – |
| `eZOperationHandler` | class | `lib/ezutils/classes/ezoperationhandler.php:18` | – | – |
| `ezpRepairQueue` | class | `lib/ezutils/classes/ezprepairqueue.php:37` | Repairs an installation whose Composer libraries are missing, from the error page that says so (eZExecution::renderErrorCasePage('dependencies')), th… | – |
| `eZStringUtils` | class | `lib/ezutils/classes/ezstringutils.php:18` | – | – |
| `eZTextTool` | class | `lib/ezutils/classes/eztexttool.php:19` | – | – |
| `eZTimestamp` | class | `lib/ezutils/classes/eztimestamp.php:12` | – | – |
| `eZTrustedProxy` | class | `lib/ezutils/classes/eztrustedproxy.php:30` | Decides which forwarded request headers can be believed. | – |
