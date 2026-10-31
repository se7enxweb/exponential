---
title: Utility (static helper)
audience: developer
category: standards
order: 337
summary: A class of static functions grouped by subject; no instances. 730 instances in Exponential.
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
| Instances | 730 |
| Recognized by | A concrete class whose methods are all static (at least two). |

## Classes by area

### extension/bccie (6)

[All patterns of extension/bccie](area-extension-bccie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `bccieExportPage` | class | `extension/bccie/classes/bccieExportPage.php:15` | The export page of one form: the options form, the download, the start of an export in the background and the removal of the collected information. | – |
| `bccieExportUtils` | class | `extension/bccie/classes/bccieExportUtils.php:11` | – | – |
| `bccieJob` | class | `extension/bccie/classes/bccieJob.php:15` | An export in the background, started from the admin: the same console command the shell uses (ext:bccie:export), started with expProcessTools like th… | – |
| `bccieRunner` | class | `extension/bccie/classes/bccieRunner.php:15` | The work of the exports as methods, so that the admin, the console commands and the cronjob parts do the same thing: validate the options, write an e… | – |
| `bccieTransfer` | class | `extension/bccie/classes/bccieTransfer.php:26` | CIE and the kernel's transfer runs (Exponential 6.0.15 and later, the kernel's doc/eng-us/features/6.0/transfer-runs.md): one engine and one run hist… | – |
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
| `cjw_newsletterInfo` | class | `extension/cjw_newsletter/ezinfo.php:16` | The name, version, copyright, license and project page of the extension, shown on the about page. | – |

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

### extension/expai (56)

[All patterns of extension/expai](area-extension-expai.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAiAltText` | class | `extension/expai/classes/alttext/expaialttext.php:21` | Alt text for images (doc/alt-text.md): a vision model looks at the stored image of an ezimage field, with the object's title and text around it, and… | – |
| `expAiAltTextFunctionCollection` | class | `extension/expai/classes/alttext/expaialttextfunctioncollection.php:14` | fetch( 'expai', 'alt_text_model', hash( 'object', $object ) ): the vision model the "Write alt text" button uses for the images of this object, or wh… | – |
| `expAiApprovalCheck` | class | `extension/expai/classes/expaiapprovalcheck.php:19` | What "Send for approval" will do with a draft: the triggers before content/publish and their workflows, with the conditions of each approve event (ev… | – |
| `expAiAudit` | class | `extension/expai/classes/audit/expaiaudit.php:23` | What Exponential Intelligence records in the audit trail (doc/audit-trail.md): one record per model request, per draft written and per change of a dr… | – |
| `expAiChainCriteria` | class | `extension/expai/classes/chain/expaichaincriteria.php:23` | The pass criteria of a chain step ([ChainStep_.] PassCriteria[]): each entry is one comparison " ", all of them must hold. | – |
| `expAiChainPrompt` | class | `extension/expai/classes/chain/expaichainprompt.php:21` | The prompts of chain steps. | – |
| `expAiCompactPrompt` | class | `extension/expai/classes/expaicompactprompt.php:29` | The compact prompt profile for small and local models (doc/local-models.md "Making a local model usable"). | – |
| `expAiCost` | class | `extension/expai/classes/expaicost.php:24` | The one place costs are computed: stored token counts (input, output, cache write, cache read) x the price table of expai.ini [Pricing], at read time… | – |
| `expAiDashboard` | class | `extension/expai/classes/expaidashboard.php:15` | What expai/dashboard and the dashboard block show for the current user: their drafts from the log, the versions written with Exponential Intelligence… | – |
| `expAiDiff` | class | `extension/expai/classes/diff/expaidiff.php:27` | The diff of two versions or languages of an object, per attribute, at block and sentence level (doc/diff.md). | – |
| `expAiDraftRevertServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/diff/expaidraftrevertserverfunctions.php:20` | "What changed" of a draft written directly (ezjscore.ini [ezjscServer_expairevert], doc/review.md): | extension/expai/settings/ezjscore.ini [ezjscServer_expairevert] Class |
| `expAiEditForm` | class | `extension/expai/classes/expaieditform.php:15` | The content edit form side: field actions and "fill the fields from a brief". | – |
| `expAiEditPresets` | class extends expAiEditForm | `extension/expai/classes/fieldactions/expaieditpresets.php:25` | Editor presets and the Improve menu of the content edit form (doc/presets.md): | – |
| `expAiExchangeRates` | class | `extension/expai/classes/expaiexchangerates.php:24` | The reference rates of the European Central Bank for converting the prices and reported costs of a provider that prices in another currency (Melious:… | – |
| `expAiHelpLinks` | class | `extension/expai/classes/expaihelplinks.php:21` | The addresses of the extension's documentation. | – |
| `expAiJsonSchema` | class | `extension/expai/classes/expaijsonschema.php:15` | A small check of tool input against the tool's JSON schema (the subset expai's tools use: type, required, properties, additionalProperties false, ite… | – |
| `expAiLimits` | class | `extension/expai/classes/expailimits.php:34` | The limits of expai.ini [Limits], checked before every request to the model (expAiUsageRecorder) and shown on the dashboard. | – |
| `expAiMcp` | class | `extension/expai/classes/mcp/expaimcp.php:22` | expai as an MCP client, two layers: | – |
| `expAiModelFallback` | class | `extension/expai/classes/expaimodelfallback.php:35` | The fallback to the site default model ([AISettings] FallbackToDefaultModel, default enabled): when the model a request asked for (the user's choice,… | – |
| `expAiModelServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/expaimodelserverfunctions.php:24` | The ezjscore functions of the "Test prompt" and "Load model" actions of expai/models (ezjscore.ini [ezjscServer_expaimodel]; the caller needs ezjscor… | extension/expai/settings/ezjscore.ini [ezjscServer_expaimodel] Class |
| `expAiModelStats` | class | `extension/expai/classes/expaimodelstats.php:25` | The track record of each model for the "Models" card of expai/dashboard: from the usage log of a period (one user or the site) the requests, the succ… | – |
| `expAiModelTest` | class | `extension/expai/classes/expaimodeltest.php:25` | The "Test prompt" and "Load model" actions of expai/models as jobs of the job queue (job type expai.model.test, classes/queue/expaimodeltesthandler.p… | – |
| `expAiObjects` | class | `extension/expai/classes/expaiobjects.php:16` | Content objects by an id expai stored earlier (the draft log, staged changes, checkpoints, chain runs, queued jobs): such an object may have been rem… | – |
| `expAiPlanBatch` | class | `extension/expai/classes/vector/expaiplanbatch.php:22` | An approved plan on a batch lane (doc/parallel-runs.md "On a batch lane"): when the thread's lane is a batch lane (Anthropic Message Batches, the Ope… | – |
| `expAiPlanner` | class | `extension/expai/classes/vector/expaiplanner.php:22` | Plan mode (doc/plan-mode.md): the plan propose_plan stores, its validation against the thread's scope and the editor's permissions, the editor's appr… | – |
| `expAiPolicyMigration` | class | `extension/expai/classes/expaipolicymigration.php:15` | Renames the policy functions of expai that pre-release builds called differently (RENAMED), in every role, with their limitations (they belong to the… | – |
| `expAiPrompts` | class | `extension/expai/classes/expaiprompts.php:15` | The instructions (system prompts) of the assistant. | – |
| `expAiProviderHealth` | class | `extension/expai/classes/expaiproviderhealth.php:24` | Whether a provider can be reached at the moment, for the model pickers (a stopped or unreachable model is shown and cannot be chosen) and for the fal… | – |
| `expAiPublisher` | class | `extension/expai/classes/expaipublisher.php:15` | What the editor does with the drafts the assistant wrote: keep them ("Save as drafts"), send them ("Send for approval": the regular content/publish o… | – |
| `expAiQueue` | class | `extension/expai/classes/queue/expaiqueue.php:25` | Queues requests to a model through the extension expqueue (job type expai.request): the lane is chosen (expAiLanePolicy), the cost estimated and rese… | – |
| `expAiQueueKick` | class | `extension/expai/classes/queue/expaiqueuekick.php:39` | Starts detached queue workers right after a job is queued, so the job does not wait for the cron part of expqueue (doc/parallel-runs.md "Workers"). | – |
| `expAiRunWatch` | class extends ezjscServerFunctions | `extension/expai/classes/expairunwatch.php:20` | "Watch this run" of the dashboard's "Start writing": the request opens the Intelligence tab of the chosen location, where it becomes a thread when th… | extension/expai/settings/ezjscore.ini [ezjscServer_expaiwatch] Class |
| `expAiSchema` | class | `extension/expai/classes/expaischema.php:24` | The two tables of expai, defined once here in the kernel's generic schema format. | – |
| `expAiScript` | class | `extension/expai/classes/expaiscript.php:22` | The siteaccess of a command line script of expai (bin/php). | – |
| `expAiSegmenterRegistry` | class | `extension/expai/classes/diff/expaisegmenterregistry.php:15` | The segmenter of a datatype: expai.ini [DiffSettings] Segmenters[]=, else FallbackSegmenter (expAiSegmenterGeneric). | – |
| `expAiSentences` | class | `extension/expai/classes/diff/expaisentences.php:21` | Text normalisation for hashing and a language-aware sentence splitter (doc/diff.md). | – |
| `expAiServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/expaiserverfunctions.php:24` | The ezjscore server functions of expai (ezjscore.ini [ezjscServer_expai]): | extension/expai/settings/ezjscore.ini [ezjscServer_expai] Class |
| `expAiServiceTools` | class | `extension/expai/classes/tools/expaiservicetools.php:28` | Service tools: the read services of the extension expservices that the assistant may call (doc/service-tools.md). | – |
| `expAiSlowModel` | class | `extension/expai/classes/vector/expaislowmodel.php:23` | A running turn on a slow model (doc/parallel-runs.md "Slow models"): Vector and the Intelligence tab show "Running on - about N s per step (step k)"… | – |
| `expAiText` | class | `extension/expai/classes/expaitext.php:14` | Text helpers: shortening, the HTML subset the assistant writes rich text in, and a plain HTML view of stored ezxmltext for the assistant to read. | – |
| `expAiThreadAccess` | class | `extension/expai/classes/vector/expaithreadaccess.php:23` | Who may see and use a thread, and what the policy expai/vector allows (doc/permissions.md, doc/threads.md). | – |
| `expAiThreadFanout` | class | `extension/expai/classes/vector/expaithreadfanout.php:27` | Parallel runs of an approved plan (doc/parallel-runs.md). | – |
| `expAiThreadMigration` | class | `extension/expai/classes/vector/expaithreadmigration.php:17` | Turns the panel conversations of an earlier expai into threads (origin "panel", scope: the conversation's node, review "direct"), with a lead run eac… | – |
| `expAiThreadPrompts` | class | `extension/expai/classes/vector/expaithreadprompts.php:15` | The texts a Vector thread adds for the model (English, as the rest of the instructions): the scope of the thread, plan mode (read tools and propose_p… | – |
| `expAiThreadRetention` | class | `extension/expai/classes/vector/expaithreadretention.php:36` | The retention of thread data (owner decision 2026-10-11: events, proposals and checkpoints are kept 30 days), prune() run by the cronjob part "expai"… | – |
| `expAiThreadRunner` | class | `extension/expai/classes/vector/expaithreadrunner.php:23` | Background runs of threads (doc/vector.md "Background runs"). | – |
| `expAiThreadSchema` | class | `extension/expai/classes/vector/expaithreadschema.php:27` | The tables of threads and Vector (doc/threads.md, doc/database.md), in the kernel's generic schema format. | – |
| `expAiTranslationSyncServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/diff/expaitranslationsyncserverfunctions.php:23` | The ezjscore functions of the "Translations" section of the Intelligence tab (ezjscore.ini [ezjscServer_expaitranslation]; the caller needs ezjscore/… | extension/expai/settings/ezjscore.ini [ezjscServer_expaitranslation] Class |
| `expAiTurnRecord` | class | `extension/expai/classes/vector/expaiturnrecord.php:18` | The record of each turn of a thread (doc/parallel-runs.md "The record of a turn"), from the usage log: the model that actually answered (and the one… | – |
| `expAiUsage` | class | `extension/expai/classes/expaiusage.php:14` | Usage tracking: recording a request (expAiUsageRecorder calls record()), the daily token limit, prices and cost, the filtered list of expai/log with… | – |
| `expAiVector` | class | `extension/expai/classes/vector/expaivector.php:17` | Vector (expai/vector, doc/vector.md): what the workspace needs from the rest of expai. | – |
| `expAiVectorServerFunctions` | class extends ezjscServerFunctions | `extension/expai/classes/vector/expaivectorserverfunctions.php:23` | The ezjscore functions of threads, Vector and the keyboard (ezjscore.ini [ezjscServer_expaivector]; the caller needs ezjscore/call with the function… | extension/expai/settings/ezjscore.ini [ezjscServer_expaivector] Class |
| `expAiVectorStream` | class | `extension/expai/classes/vector/expaivectorstream.php:19` | The transport of thread events (modules/expai/stream.php): which one applies, the stream slots, the stream loop. | – |
| `expAiVectorStrings` | class | `extension/expai/classes/vector/expaivectorstrings.php:14` | The texts of the command palette and the shortcut sheet (context extension/expai/keyboard). | – |
| `expAiWebBudget` | class | `extension/expai/classes/expaiwebbudget.php:26` | The time a web request may spend on one model call. | – |
| `expAiWebUrl` | class | `extension/expai/classes/web/expaiweburl.php:16` | URLs of the web tools: parsing (http and https only, no user name or password, ports 80 and 443 only, at most 2048 characters), host normalization (l… | – |

### extension/expchangeclass (2)

[All patterns of extension/expchangeclass](area-extension-expchangeclass.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `conversionFunctions` | class | `extension/expchangeclass/classes/functions.php:39` | – | – |
| `expChangeClassConverters` | class | `extension/expchangeclass/classes/converters.php:21` | – | extension/expchangeclass/settings/changeclass.ini [ezobjectrelationlist] Class; extension/expchangeclass/settings/changeclass.ini [ezstring] Class; extension/expchangeclass/settings/changeclass.ini [eztext] Class |

### extension/expimageeditor (1)

[All patterns of extension/expimageeditor](area-extension-expimageeditor.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expImageEditorStore` | class | `extension/expimageeditor/classes/expimageeditorstore.php:31` | What the save view of the image editor does on the server: the editor works in the browser and posts the finished image; this class checks who posts… | – |

### extension/explayouts (53)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsAjaxLoadMoreServer` | class | `extension/explayouts/classes/explayoutsajaxloadmoreserver.php:7` | ezjscServer functions for loading paged collection blocks. | extension/explayouts/settings/ezjscore.ini [ezjscServer_expajaxloadmore] Class; extension/sevenx_themes_media/settings/siteaccess/site/ezjscore.ini [ezjscServer_expajaxloadmore] Class |
| `expLayoutsBlockHandlerFactory` | class | `extension/explayouts/classes/explayoutsblockhandlerfactory.php:2` | – | – |
| `expLayoutsBlockParameter` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutsblockparameter.php:2` | – | – |
| `expLayoutsCollection` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollection.php:2` | – | – |
| `expLayoutsCollectionItem` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionitem.php:2` | – | – |
| `expLayoutsCollectionQuery` | class extends eZPersistentObject | `extension/explayouts/classes/explayoutscollectionquery.php:2` | – | – |
| `expLayoutsDynamicCollection` | class | `extension/explayouts/classes/explayoutsdynamiccollection.php:15` | Executes 'dynamic' collections (collection queries imported from the Nexus reference). | – |
| `expLayoutsExponential` | class | `extension/explayouts/classes/explayoutsexponential.php:4` | – | – |
| `expLayoutsExporter` | class | `extension/explayouts/classes/explayoutsexporter.php:7` | The export of a layout (version 1). | – |
| `expLayoutsExpRelationListQuery` | class | `extension/explayouts/classes/explayoutsexprelationlistquery.php:6` | Exponential Layouts port of the upstream RelationListQuery handler. | – |
| `expLayoutsExpReverseRelationListQuery` | class | `extension/explayouts/classes/explayoutsexpreverserelationlistquery.php:6` | Exponential Layouts port of the upstream ReverseRelationListQuery handler. | – |
| `expLayoutsExpSiteApi` | class | `extension/explayouts/classes/explayoutsexpsiteapi.php:3` | – | – |
| `expLayoutsExpTagsQuery` | class | `extension/explayouts/classes/explayoutsexptagsquery.php:6` | Exponential Layouts port of the upstream TagsQuery handler. | – |
| `expLayoutsExpUtils` | class | `extension/explayouts/classes/explayoutsexputils.php:4` | – | – |
| `expLayoutsFixtures` | class | `extension/explayouts/classes/explayoutsfixtures.php:2` | – | – |
| `expLayoutsImporter` | class | `extension/explayouts/classes/explayoutsimporter.php:11` | Imports a layout export, the one of this extension (expLayoutsExporter, "version": 1, parameters as a map of name and value) and the one of the layou… | – |
| `expLayoutsInstall` | class | `extension/explayouts/classes/explayoutsinstall.php:2` | – | – |
| `expLayoutsLanguageResolver` | class | `extension/explayouts/classes/explayoutslanguageresolver.php:6` | Exponential 4 language resolver compatible with the Exp Site API LanguageResolver pattern. | – |
| `expLayoutsLayoutType` | class | `extension/explayouts/classes/explayoutslayouttype.php:2` | – | – |
| `expLayoutsLinkParameter` | class | `extension/explayouts/classes/explayoutslinkparameter.php:23` | The link parameter shared by the blocks that can carry one. | – |
| `expLayoutsMailHelper` | class | `extension/explayouts/classes/explayoutsmailhelper.php:6` | Exponential 4 port of Netgen SiteBundle Helper/MailHelper. | – |
| `expLayoutsMarkdown` | class | `extension/explayouts/classes/explayoutsmarkdown.php:22` | Markdown of the markdown block, as HTML. | – |
| `expLayoutsPreviewSiteAccess` | class | `extension/explayouts/classes/explayoutspreviewsiteaccess.php:25` | The public siteaccess an administration renders layouts in: the layout preview (explayouts_ui/layout_preview) and the Load more of a list block calle… | – |
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

### extension/expqueue (7)

[All patterns of extension/expqueue](area-extension-expqueue.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expQueue` | class | `extension/expqueue/classes/expqueue.php:26` | The job queue: what producers call (enqueue, groups, cancel, pause/resume, retry, results) and the state changes the workers and the admin make. | – |
| `expQueueAdmin` | class | `extension/expqueue/classes/expqueueadmin.php:14` | Helpers of the admin module expqueue: the page result with its path, the rows the templates show (every label and duration prepared here, so the temp… | – |
| `expQueueLock` | class | `extension/expqueue/classes/expqueuelock.php:25` | The dispatcher lock and the queue's directory. | – |
| `expQueueResource` | class | `extension/expqueue/classes/expqueueresource.php:22` | The concurrency limiter of a resource (table expqueue_resource): a key such as "anthropic:standard" or "imaging" that jobs name with the option resou… | – |
| `expQueueSchema` | class | `extension/expqueue/classes/expqueueschema.php:28` | The tables of expqueue, defined once here in the kernel's generic schema format. | – |
| `expQueueService` | class | `extension/expqueue/classes/expqueueservice.php:22` | The systemd service of the queue's workers: ./console ext:expqueue:service install\|start\|stop\|status\|uninstall. | – |
| `expQueueStats` | class | `extension/expqueue/classes/expqueuestats.php:15` | The figures of the queue for the dashboard (expqueue/dashboard) and ./console ext:expqueue:status: counts per state, per class and per resource, the… | – |

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
| `expUIServerFunctions` | class | `extension/expui/classes/expuiserverfunctions.php:30` | – | extension/expui/settings/ezjscore.ini [ezjscServer_exp] Class |

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

### extension/ezodf (23)

[All patterns of extension/ezodf](area-extension-ezodf.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expOdfAiAssist` | class | `extension/ezodf/classes/converter/expodfaiassist.php:20` | AI assist of the import (expai, opt-in): for an imported document, a summary for the intro and keywords, as suggestions a person applies on the batch… | – |
| `expOdfCleanup` | class | `extension/ezodf/classes/converter/expodfcleanup.php:15` | Removes what ezodf leaves behind: work and spool directories of conversions (older than an hour: a conversion never takes that long), uploaded batche… | – |
| `expOdfConverter` | class | `extension/ezodf/classes/converter/expodfconverter.php:29` | Converts documents. | – |
| `expOdfConverterAdmin` | class | `extension/ezodf/classes/converter/expodfconverteradmin.php:14` | The data and the actions of the Document converter page (ezodf/converter) and its live state (ezodf/converterstatus). | – |
| `expOdfConverterTest` | class | `extension/ezodf/classes/converter/expodfconvertertest.php:15` | The test conversions of the Document converter page: each bundled sample (share/samples) is converted with the converter as it is set up (its backend… | – |
| `expOdfDocumentsAdmin` | class | `extension/ezodf/classes/converter/expodfdocumentsadmin.php:13` | The data of the Import documents, batch and runs pages (ezodf/import, ezodf/batch, ezodf/runs). | – |
| `expOdfExporter` | class | `extension/ezodf/classes/converter/expodfexporter.php:18` | Items out as documents. | – |
| `expOdfFilterCatalogue` | class | `extension/ezodf/classes/converter/expodffiltercatalogue.php:16` | The import and export filters of a LibreOffice installation, read from its share/registry/*.xcd files (the type detection's Filters and Types), so th… | – |
| `expOdfImportDocumentFeedSource` | class extends expOdfImportDocumentUrlsSource | `extension/ezodf/classes/sqliimport/expodfimportdocumentfeedsource.php:18` | "Documents of a feed" of 7x SQLI Import: an RSS or Atom feed whose items link to documents (enclosures, or links ending in a document extension: a pu… | extension/ezodf/settings/sqliimport.ini [Source_document_feed] Class |
| `expOdfImportDocumentUrlsSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportdocumenturlssource.php:16` | "Documents from URLs" of 7x SQLI Import: a list of addresses of documents (one per line). | extension/ezodf/settings/sqliimport.ini [Source_document_urls] Class |
| `expOdfImportGoogleDriveSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportgoogledrivesource.php:33` | "Documents in a Google Drive folder" of 7x SQLI Import: the documents of a Drive folder (in My Drive or a shared drive) and of its sub folders down t… | extension/ezodf/settings/sqliimport.ini [Source_google_drive] Class |
| `expOdfImportHttp` | class | `extension/ezodf/classes/sqliimport/expodfimporthttp.php:27` | The HTTP requests of the remote document sources of 7x SQLI Import (WebDAV and Nextcloud, Google Drive, OneDrive and SharePoint), with the address po… | – |
| `expOdfImportMailboxDocumentsSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportmailboxdocumentssource.php:31` | "Documents attached to e-mails (IMAP)" of 7x SQLI Import: the document attachments of the messages of an IMAP mailbox (a submissions address, a press… | extension/ezodf/settings/sqliimport.ini [Source_mailbox_documents] Class |
| `expOdfImportOneDriveSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportonedrivesource.php:36` | "Documents in a OneDrive or SharePoint folder" of 7x SQLI Import: the documents of a folder of a OneDrive for Business drive or a SharePoint document… | extension/ezodf/settings/sqliimport.ini [Source_onedrive] Class |
| `expOdfImportWebdavSource` | class extends expOdfImportDocumentSource | `extension/ezodf/classes/sqliimport/expodfimportwebdavsource.php:33` | "Documents in a WebDAV or Nextcloud folder" of 7x SQLI Import: the documents of a folder on a WebDAV server (Nextcloud, ownCloud, a SharePoint WebDAV… | extension/ezodf/settings/sqliimport.ini [Source_webdav] Class |
| `expOdfJobs` | class | `extension/ezodf/classes/converter/expodfjobs.php:24` | ezodf's background work, started from the administration and never run inside a web request: | – |
| `expOdfPaths` | class | `extension/ezodf/classes/converter/expodfpaths.php:24` | Where ezodf 7 keeps its files, all below the site's var directory (var//ezodf/): | – |
| `expOdfPhpConverter` | class | `extension/ezodf/classes/converter/expodfphpconverter.php:25` | The last fallback of the converter: PHP only, no LibreOffice. | – |
| `expOdfRemoteFetcher` | class | `extension/ezodf/classes/converter/expodfremotefetcher.php:24` | Downloads a document from a URL an editor gave, without letting the URL reach the server's own network (server-side request forgery): | – |
| `expOdfRemoteServer` | class | `extension/ezodf/classes/converter/expodfremoteserver.php:22` | A conversion service elsewhere, set on the Document converter page (converter.remote_url, remote_kind, an optional user and password for HTTP basic a… | – |
| `expOdfSandbox` | class | `extension/ezodf/classes/converter/expodfsandbox.php:23` | Runs LibreOffice (and ezodf's worker script) in a sandbox: | – |
| `expOdfServer` | class | `extension/ezodf/classes/converter/expodfserver.php:23` | The conversion server: one or more long-lived headless LibreOffice processes, each supervised by ezodf's worker script (bin/ezodfworker.py) inside th… | – |
| `expOdfSettings` | class | `extension/ezodf/classes/converter/expodfsettings.php:22` | The settings of ezodf 7 as the administration changes them: the settings pages write var//ezodf/settings.json (the extension's own store, never setti… | – |

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

### extension/sevenx_authentication_2fa (7)

[All patterns of extension/sevenx_authentication_2fa](area-extension-sevenx-authentication-2fa.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZsevenxUser2faUser` | class extends eZUser | `extension/sevenx_authentication_2fa/login_handler/ezsevenxuser2fauser.php:32` | – | – |
| `sevenxAuthentication2faAttempts` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faattempts.php:27` | – | – |
| `sevenxAuthentication2faCrypto` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2facrypto.php:27` | – | – |
| `sevenxAuthentication2faEmail` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2faemail.php:21` | – | – |
| `sevenxAuthentication2faPolicy` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fapolicy.php:37` | – | – |
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

### extension/sqliimport (50)

[All patterns of extension/sqliimport](area-extension-sqliimport.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expImport` | class | `extension/sqliimport/classes/exp/expimport.php:13` | The small shared helpers of the 2.x engine: time, JSON, settings, the storage directory and the audit trail. | – |
| `expImportAdmin` | class | `extension/sqliimport/classes/exp/expimportadmin.php:15` | What the admin pages of the Imports tab show: the figures of the dashboard, the imports with their last run and next schedule, the runs filtered by v… | – |
| `expImportAi` | class | `extension/sqliimport/classes/exp/ai/expimportai.php:35` | The AI assist of the import editor and the run page, through the extension expai (expImportAiExpaiTransport): | – |
| `expImportApiHttp` | class | `extension/sqliimport/classes/exp/sources/expimportapihttp.php:18` | The JSON requests of the sources that read a hosted service's API with a token (Airtable, Notion, Supabase, Baserow, Salesforce, HubSpot): a pause be… | – |
| `expImportArangoDbSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportarangodbsource.php:23` | The results of an AQL query against ArangoDB, read through its HTTP cursor API. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.arangodb] Arguments[] |
| `expImportAwsSigV4` | class | `extension/sqliimport/classes/exp/sources/expimportawssigv4.php:17` | AWS Signature Version 4 for a request to an AWS service (no SDK): the canonical request, the string to sign, the signing key derived from the secret… | – |
| `expImportClickHouseSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportclickhousesource.php:27` | The rows of a SELECT against ClickHouse, read through its HTTP interface (port 8123, or 8443 with TLS) in the format JSONEachRow. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.clickhouse] Arguments[] |
| `expImportCloudHttp` | class | `extension/sqliimport/classes/exp/sources/expimportcloudhttp.php:18` | The JSON requests of the sources that read a cloud database over its HTTPS API (Amazon DynamoDB, Firestore, BigQuery, Firebase Realtime Database): th… | – |
| `expImportCommerce` | class | `extension/sqliimport/classes/exp/sources/expimportcommerce.php:14` | What the product feed sources share: prices as decimal strings with their currency, lists written in one cell, and the mapping preset to the class pr… | – |
| `expImportCron` | class | `extension/sqliimport/classes/exp/expimportcron.php:28` | When a schedule runs next. | – |
| `expImportCssSelector` | class | `extension/sqliimport/classes/exp/crawl/expimportcssselector.php:21` | The selectors of the "picks" of the crawl sources: a practical subset of CSS turned into XPath, or XPath itself (written "xpath:"), and the value to… | – |
| `expImportDataTableFields` | class | `extension/sqliimport/classes/exp/expimportdatatablefields.php:18` | The fields of a new data table, guessed from the sample of a source (the kernel's "Module table" wizard asks for them through expImportDataTableField… | – |
| `expImportDataTables` | class | `extension/sqliimport/classes/exp/expimportdatatables.php:16` | The data tables of Exponential 6.0.15 (Setup › Data tables) as 7x SQLI Import offers them: the catalogue's group "Data tables" (import into a table,… | – |
| `expImportDocumentHttp` | class | `extension/sqliimport/classes/exp/nosql/expimportdocumenthttp.php:21` | What the document stores read over HTTP have in common (CouchDB, the Couchbase Query service, Azure Cosmos DB): the check of the server address (http… | – |
| `expImportExternalFile` | class | `extension/sqliimport/classes/exp/expimportexternalfile.php:25` | A file another tool hands to Imports: "Re-import this file" of an export, the file of a CIE export. | – |
| `expImportFrontMatter` | class | `extension/sqliimport/classes/exp/expimportfrontmatter.php:15` | Front matter of Markdown files (Jekyll, Hugo, Obsidian, Eleventy ...): YAML between "---" lines or TOML between "+++" lines at the start of the file,… | – |
| `expImportFunctionCollection` | class | `extension/sqliimport/classes/exp/expimportfunctioncollection.php:11` | The template fetch functions of the module sqliimport (modules/sqliimport/function_definition.php). | – |
| `expImportGoogleAuth` | class | `extension/sqliimport/classes/exp/sources/expimportgoogleauth.php:21` | The access token of a Google Cloud service account, for the sources that read Firestore, BigQuery and the Firebase Realtime Database: the service acc… | – |
| `expImportGoogleSheetsSource` | class extends expImportCsvSource | `extension/sqliimport/classes/exp/sources/expimportgooglesheetssource.php:15` | A Google Sheets spreadsheet by its address: the link of the sheet (shared so that anyone with the link can view) or its "Publish to the web" link. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.google_sheets] Arguments[] |
| `expImportInfluxDbSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportinfluxdbsource.php:29` | The rows of a query against InfluxDB: Flux through the API of InfluxDB 2 and Cloud (POST /api/v2/query, the answer in annotated CSV), or InfluxQL thr… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.influxdb] Arguments[] |
| `expImportLegacy` | class | `extension/sqliimport/classes/exp/expimportlegacy.php:24` | What the 2.x pages do with the import requests (sqliimport_item, SQLIImportItem) and the scheduled imports (sqliimport_scheduled, SQLIScheduledImport… | – |
| `expImportMailMessage` | class | `extension/sqliimport/classes/exp/mail/expimportmailmessage.php:16` | An e-mail message (RFC 5322 with MIME) as a record: the message ID, sender and recipients, the subject and the names of the headers decoded to UTF-8… | – |
| `expImportMapper` | class | `extension/sqliimport/classes/exp/expimportmapper.php:30` | The no-code mapping: which source field fills which attribute of a content class, how a record finds the object it updates, and where a new object is… | – |
| `expImportMigration` | class | `extension/sqliimport/classes/exp/engine/expimportmigration.php:25` | The migration of an installation of sqliimport 1.x (./console ext:sqliimport:migrate-1x): | – |
| `expImportNeo4jSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportneo4jsource.php:33` | The rows of a Cypher query against Neo4j or Memgraph. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.neo4j] Arguments[] |
| `expImportNotifier` | class | `extension/sqliimport/classes/exp/engine/expimportnotifier.php:22` | Who hears of a run that ended, by the import's settings (Processing step, behaviour): | – |
| `expImportOptionSchema` | class | `extension/sqliimport/classes/exp/expimportoptionschema.php:28` | Typed options. | – |
| `expImportPlatformHttp` | class | `extension/sqliimport/classes/exp/sources/expimportplatformhttp.php:20` | The HTTP requests of the sources that read a site's API themselves (Drupal JSON:API, another Exponential site). | – |
| `expImportPreview` | class | `extension/sqliimport/classes/exp/engine/expimportpreview.php:21` | The sample and the preview of the import editor, in the request itself (no job, no run stored): | – |
| `expImportPrice` | class | `extension/sqliimport/classes/exp/expimportprice.php:17` | The value of a price attribute (ezprice) as the kernel reads and writes it: "price\|VAT type id\|1 or 0" (VAT included). | – |
| `expImportReport` | class | `extension/sqliimport/classes/exp/expimportreport.php:18` | The CSV report of a run: one line per record with what was done to which object. | – |
| `expImportRestApiHttp` | class extends expImportPlatformHttp | `extension/sqliimport/classes/exp/sources/expimportrestapihttp.php:20` | The requests of the REST API source (rest_api) on the network. | – |
| `expImportRunner` | class | `extension/sqliimport/classes/exp/engine/expimportrunner.php:23` | Starts, cancels and watches runs. | – |
| `expImportScheduler` | class | `extension/sqliimport/classes/exp/engine/expimportscheduler.php:15` | The work of the cron part "sqliimport" (every minute): start the runs of the schedules that are due, end runs whose jobs ended without them, hand the… | – |
| `expImportSchema` | class | `extension/sqliimport/classes/exp/expimportschema.php:35` | The tables of 7x SQLI Import, defined once here in the kernel's generic schema format. | – |
| `expImportSecrets` | class | `extension/sqliimport/classes/exp/expimportsecrets.php:25` | Secrets of sources (database passwords, mailbox passwords, API keys). | – |
| `expImportServerFunctions` | class extends ezjscServerFunctions | `extension/sqliimport/classes/exp/expimportserverfunctions.php:22` | The ezjscore server functions of the Imports tab (ezjscore.ini [ezjscServer_sqliimport]), called by javascript/sqliimport_admin.js through Exp.io: | extension/sqliimport/settings/ezjscore.ini [ezjscServer_sqliimport] Class |
| `expImportSnowflakeSource` | class extends expImportHttpQuerySource | `extension/sqliimport/classes/exp/sources/expimportsnowflakesource.php:26` | The rows of a query against Snowflake, read through the SQL API v2 (POST /api/v2/statements) with key-pair authentication: a JWT signed with the user… | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.source.snowflake] Arguments[] |
| `expImportSourceFiles` | class | `extension/sqliimport/classes/exp/engine/expimportsourcefiles.php:21` | Where the data of a run comes from, made into a local file once per run (the snapshot every chunk reads): | – |
| `expImportStatistics` | class | `extension/sqliimport/classes/exp/expimportstatistics.php:27` | The statistics of the run history: the charts of the dashboard (sqliimport/list) and the Statistics page (sqliimport/statistics) with its periods, fi… | – |
| `expImportTemplates` | class | `extension/sqliimport/classes/exp/expimporttemplates.php:28` | Templates of imports: an import definition without its source data (no file, address or server path, no schedules, trigger, run user or notification… | – |
| `expImportTransfer` | class | `extension/sqliimport/classes/exp/expimporttransfer.php:38` | Import definitions as a JSON file, to move them between installations (staging to live), keep them with a project or share them: "Download as JSON" o… | – |
| `expImportTransferRuns` | class | `extension/sqliimport/classes/exp/engine/expimporttransferruns.php:20` | Imports in the run history of Exponential's import and export tools (kernel 6.0.15 and later, expKernelTransferHistory, table exp_transfer_run): ever… | – |
| `expImportTransforms` | class | `extension/sqliimport/classes/exp/expimporttransforms.php:18` | The transforms of a mapping: the built-in ones and those of kernel services tagged sqliimport.transform. | – |
| `expImportTriggerAuth` | class | `extension/sqliimport/classes/exp/expimporttriggerauth.php:86` | The checks of the webhook / API trigger of an import, without a database (expImportTriggerEndpoint does the request): | – |
| `expImportTriggerKey` | class extends eZPersistentObject | `extension/sqliimport/classes/exp/model/expimporttrigger.php:89` | An API key of a trigger (sqliimport_trigger_key): its label, its key id (the part of the key that finds it) and the HMAC of the key (expImportTrigger… | – |
| `SQLIContentUtils` | class | `extension/sqliimport/classes/content/sqlicontentutils.php:15` | Utility class aggregating several helper methods related to content | – |
| `SQLIImportLogger` | class | `extension/sqliimport/classes/sqliimportlogger.php:12` | SQLIImportLogger Class for logging and/or display messages during import process | – |
| `SQLIImportToken` | class extends eZPersistentObject | `extension/sqliimport/classes/sqliimporttoken.php:12` | Token class for SQLI Import Handles import status | – |
| `SQLIImportUtils` | class | `extension/sqliimport/classes/sqliimportutils.php:11` | SQLIImportUtils | – |

### extension/syndication (4)

[All patterns of extension/syndication](area-extension-syndication.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZSyndicationInstaller` | class | `extension/syndication/classes/ezsyndicationinstaller.php:17` | – | – |
| `eZSyndicationJob` | class | `extension/syndication/classes/ezsyndicationjob.php:16` | – | – |
| `eZSyndicationRunner` | class | `extension/syndication/classes/ezsyndicationrunner.php:15` | – | – |
| `eZSyndicationUI` | class | `extension/syndication/classes/ezsyndicationui.php:15` | – | – |

### extension/xrowextract (20)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `XrowExtractArchive` | class | `extension/xrowextract/classes/xrowextractarchive.php:12` | The site archive: the objects of many classes below many nodes, one CSV file per class, with a manifest, packed as zip, tar.gz, tar.bz2, tar.xz, 7z o… | – |
| `XrowExtractCatalogue` | class | `extension/xrowextract/classes/xrowextractcatalogue.php:13` | The column catalogue beyond the class attributes as they are: | – |
| `XrowExtractColumns` | class | `extension/xrowextract/classes/xrowextractcolumns.php:8` | The columns of an export and their values: the special (non class attribute) columns, a class's attribute columns, and one object as a row of CSV cel… | – |
| `XrowExtractCron` | class | `extension/xrowextract/classes/xrowextractcron.php:16` | When a schedule runs: the simple choices of the Schedules page (hourly at minute M, daily at HH:MM, weekly on a day at HH:MM, monthly on a day at HH:… | – |
| `XrowExtractEntry` | class | `extension/xrowextract/classes/xrowextractentry.php:14` | The entry parameters of xrowextract/csv: the content tree's Advanced › Export menu opens the CSV form pre-filled for a node with xrowextract/csv/(nod… | – |
| `XrowExtractFetchAlias` | class | `extension/xrowextract/classes/xrowextractfetchalias.php:18` | Named fetches from fetchalias.ini usable in the extract view and the CLI: a "tree" / "list" / "tree_count" / "list_count" alias's Constant values rea… | – |
| `XrowExtractFunctionCollection` | class | `extension/xrowextract/classes/xrowextractfunctioncollection.php:4` | The xrowextract module's template fetch functions (modules/xrowextract/function_definition.php). | – |
| `XrowExtractImport` | class | `extension/xrowextract/classes/xrowextractimport.php:22` | The other direction of the export: reads a CSV or JSON file written by XrowExtractWriter (any column set, in particular "migration") back into conten… | – |
| `XrowExtractJob` | class | `extension/xrowextract/classes/xrowextractjob.php:16` | A background export job: one folder per job below the var directory (var//xrowextract-jobs//), holding job.json (state), progress.json (written by th… | – |
| `XrowExtractManifest` | class | `extension/xrowextract/classes/xrowextractmanifest.php:20` | The typed column manifest of an export: what every column of a file is (its id, the name in the file, the datatype, the chosen format and language),… | – |
| `XrowExtractNotifier` | class | `extension/xrowextract/classes/xrowextractnotifier.php:13` | What a schedule's owner (and whoever else it names) hears about a run: - e-mail on failure (a failed or skipped run, or a failed delivery): always to… | – |
| `XrowExtractPackage` | class | `extension/xrowextract/classes/xrowextractpackage.php:18` | Content-package (.ezpkg) support for xrowextract/package: inspecting a package's content classes and content objects before anything is written (the… | – |
| `XrowExtractPreset` | class | `extension/xrowextract/classes/xrowextractpreset.php:25` | A saved export preset: a complete, named CSV or archive export definition (scope, node(s), class or sets, columns, languages, every filter, sort, out… | – |
| `XrowExtractReimport` | class | `extension/xrowextract/classes/xrowextractreimport.php:13` | "Re-import this file" on the Jobs and History pages: the file of a finished class export (a CSV, JSON or XML job of the One class view, the command l… | – |
| `XrowExtractRequirements` | class | `extension/xrowextract/classes/xrowextractrequirements.php:19` | What the extension needs from PHP and the server, in one place: each requirement (a PHP version, a PHP extension, a function a hosting may disable, a… | – |
| `XrowExtractScheduler` | class | `extension/xrowextract/classes/xrowextractscheduler.php:15` | Runs schedules: finds the due ones (the cronjob part, cronjobs/xrowextract.php, or bin/php/schedule.php --cron), turns each into an ordinary backgrou… | – |
| `XrowExtractSchema` | class | `extension/xrowextract/classes/xrowextractschema.php:11` | The extension's own tables (xrowextract_schedule, xrowextract_destination, xrowextract_history), created lazily and idempotently the first time one o… | – |
| `XrowExtractSecrets` | class | `extension/xrowextract/classes/xrowextractsecrets.php:15` | Destination credentials at rest: libsodium secretbox (XSalsa20-Poly1305) with a 32 byte key kept in a key file outside the database. | – |
| `XrowExtractTransfer` | class | `extension/xrowextract/classes/xrowextracttransfer.php:20` | Export's part of Exponential's transfer runs (6.0.15 and later): one engine and one run history for every import and export tool of the installation. | – |
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

### kernel/classes (89)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAdminPagination` | class | `kernel/classes/expadminpagination.php:19` | One place to ask how many rows a list in the administration interface shows. | – |
| `expAnonymousPublicPolicies` | class | `kernel/classes/expanonymouspublicpolicies.php:29` | The policies a visitor who is not signed in needs on a public site, beyond reading content: registering, reading the RSS feeds, the tag pages of ezta… | – |
| `expBenchmark` | class | `kernel/classes/expbenchmark.php:28` | The statistics, the report formats and the regression check of exp:benchmark. | – |
| `expBenchmarkHttp` | class | `kernel/classes/expbenchmarkhttp.php:20` | The HTTP side of exp:benchmark: requests a list of URLs, several at a time, with PHP's own curl (curl_multi), so no external program is needed. | – |
| `expBenchmarkKernel` | class | `kernel/classes/expbenchmarkkernel.php:29` | The in-process side of exp:benchmark: what the kernel spends on the parts of a page, without HTTP, a web server or a response cache in between. | – |
| `expBenchmarkMicro` | class | `kernel/classes/expbenchmarkmicro.php:33` | The micro mode of exp:benchmark: pure-PHP hot paths of the kernel, timed in this process without HTTP, a web server or a database, so it runs on a CI… | – |
| `expBookmarkPage` | class | `kernel/classes/expbookmarkpage.php:18` | What the bookmark page (content/bookmark) shows, worked out from plain arrays: the folders with their counts, which bookmarks a folder, a search and… | – |
| `expBookmarkView` | class extends eZPersistentObject | `kernel/classes/expbookmarkview.php:39` | A bookmark of a system page: a page of a module view (setup/systemupgrade, exphelp/dashboard, audit/console/(user)/14 ...) instead of a node. | – |
| `expCacheWarm` | class | `kernel/classes/expcachewarm.php:28` | Keeps the response cache full, so that no visitor is the one who pays for a page to be rendered. | – |
| `expCollaborationGroupManager` | class | `kernel/classes/expcollaborationgroupmanager.php:14` | Creates, renames and deletes the collaboration groups of one user and moves items between them. | – |
| `expCollaborationInbox` | class | `kernel/classes/expcollaborationinbox.php:15` | The collaboration summary as an inbox: every item of the current user with its state (waiting, approved, denied), its role for the user (approver or… | – |
| `expContentDraftList` | class | `kernel/classes/expcontentdraftlist.php:20` | What the drafts page (content/draft, "My drafts") lists, worked out from plain arrays: the filters of the address (translation, class, age, search, o… | – |
| `expContentHistoryList` | class | `kernel/classes/expcontenthistorylist.php:22` | What the versions page of an object (content/history) lists, worked out from plain arrays: the filters of the address (status, translation, creator,… | – |
| `expContentLanguageStates` | class | `kernel/classes/expcontentlanguagestates.php:26` | The state of each language of one content object, for the language badges of admin4 (design:parts/ language_state_badge.tpl): the main language (the… | – |
| `expContentPendingList` | class | `kernel/classes/expcontentpendinglist.php:21` | What the pending page (content/pendinglist, "My pending items") lists, worked out from plain arrays: the versions the user sent for publishing that a… | – |
| `expCronjobPause` | class | `kernel/classes/expcronjobpause.php:27` | Pauses cronjob parts: one part, or all of them, until someone resumes them. | – |
| `expDemoMode` | class | `kernel/classes/expdemomode.php:38` | Demo mode: the environment variable EXP_DEMO_MODE=1 marks an installation as a public demo. | – |
| `expEditorPolicies` | class | `kernel/classes/expeditorpolicies.php:39` | The policies an editor needs for everyday work and the Editor role of a new installation lacks: hiding and revealing, the trash (looking into it and… | – |
| `expErrorHeaderList` | class | `kernel/classes/experrorheaderlist.php:25` | The response headers of an error page, from error.ini [HTTPError-]: | – |
| `expImageUploadValidator` | class | `kernel/classes/expimageuploadvalidator.php:35` | Decides whether an uploaded file is an image the site accepts, by what the file contains rather than by its name. | – |
| `ExpInstallationDetailsOutputFilter` | class | `kernel/classes/expinstallationdetailsoutputfilter.php:15` | Installation name output filter. | – |
| `expInstallDefaultExtensions` | class | `kernel/classes/expinstalldefaultextensions.php:33` | The extensions every new installation gets set up, whatever site package it is made from: two-step sign-in (sevenx_authentication_2fa) and imports (s… | – |
| `expKernelServiceCompiler` | class | `kernel/classes/expkernelservicecompiler.php:33` | Builds, checks and removes the compiled kernel service container (var/cache/kernelservices/). | – |
| `expKernelServices` | class | `kernel/classes/expkernelservices.php:44` | The kernel service container: the static facade, and the compiled boot of the web kernel. | – |
| `expMaintenance` | class | `kernel/classes/expmaintenance.php:29` | Maintenance mode: while var/maintenance.json exists, every request to the front controllers is answered with a maintenance page (503, Retry-After, ne… | – |
| `expManagedConfiguration` | class | `kernel/classes/expmanagedconfiguration.php:25` | site.ini [ConfigurationSettings] ManagedByDeployment=enabled: settings, templates and code come from the deployment only, and the admin interface doe… | – |
| `expMemberManagement` | class | `kernel/classes/expmembermanagement.php:27` | Member management: user groups that manage the accounts of other user groups, set up from membermanagement.ini (doc/eng-us/bc/6.0/member-management.m… | – |
| `expNodeQuickPick` | class | `kernel/classes/expnodequickpick.php:18` | Quick location buttons (doc/eng-us/features/6.0/quick-location-buttons.md): a few well-known places of the content tree, found from a short key (Webs… | – |
| `expNotificationJob` | class | `kernel/classes/expnotificationjob.php:17` | A notification run for the status page ("Run now"), in the background. | – |
| `expNotificationService` | class | `kernel/classes/expnotificationservice.php:26` | The one place that knows how the notification system is run and looked at: the cronjob part, the exp:notification:* commands, the status page and its… | – |
| `expPackageArchive` | class | `kernel/classes/exppackagearchive.php:41` | Opens a package archive (.ezpkg: a gzip compressed tar archive) for reading, after a bounded scan of its tar headers. | – |
| `expPackageConflictReport` | class | `kernel/classes/exppackageconflictreport.php:22` | What installing a package would meet on this installation, by remote id, without writing anything: for every content object of the package whether an… | – |
| `expPackageJson` | class | `kernel/classes/exppackagejson.php:34` | The JSON serialization of a package (doc/eng-us/bc/6.0/package-json-format.md). | – |
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
| `expViewParameters` | class | `kernel/classes/expviewparameters.php:25` | The options of an administration view or endpoint are view parameters, /(name)/value, never GET query parameters (as the help center's, doc/eng-us/fe… | – |
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
| `eZPackageUploadInspector` | class | `kernel/classes/ezpackageuploadinspector.php:33` | Looks at a package archive before anything of it is written into a repository. | – |
| `eZPackageVersion` | class | `kernel/classes/ezpackageversion.php:55` | – | – |
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
| `expAudit` | class | `kernel/classes/audit/expaudit.php:26` | The audit: what happens in Exponential, recorded as hash-chained JSON lines per channel (doc/eng-us/bc/6.0/audit.md, "The developer API (Z3)"). | – |
| `expAuditAlertEvaluator` | class | `kernel/classes/audit/alerts/expauditalertevaluator.php:23` | Evaluates the alert rules (doc/eng-us/bc/6.0/audit.md, "Alerts (F5)") and fires system.audit.alert. | – |
| `expAuditBridge` | class | `kernel/classes/audit/bridge/expauditbridge.php:24` | The ezpEvent bridge of the audit (doc/eng-us/bc/6.0/audit.md, "The ezpEvent bridge", Z3): kernel and extension events that already exist (ezpEvent::g… | – |
| `expAuditConfig` | class | `kernel/classes/audit/expauditconfig.php:21` | The audit settings (settings/audit.ini, doc/eng-us/bc/6.0/audit.md "The settings reference") read once into one array. | – |
| `expAuditConsole` | class | `kernel/classes/audit/console/expauditconsole.php:16` | What the views of the audit module share (doc/eng-us/bc/6.0/audit.md, "The console: module audit"): the channels the current user may read (audit/rea… | – |
| `expAuditFormatRegistry` | class | `kernel/classes/audit/format/expauditformatregistry.php:16` | The archive format registry: [AuditArchiveSettings] FormatHandlers[]= (RAD survey registry "auditformats"). | – |
| `expAuditFunctionCollection` | class | `kernel/classes/audit/console/expauditfunctioncollection.php:22` | The fetch functions of the audit module (kernel/audit/function_definition.php; doc/eng-us/bc/6.0/audit.md, "Template operator and fetch"): | – |
| `expAuditGuard` | class | `kernel/classes/audit/expauditguard.php:32` | [AuditSettings] OnWriteFailure (doc/eng-us/bc/6.0/audit.md, "When the audit cannot write"): what happens to an action whose audit record cannot be wr… | – |
| `expAuditHook` | class | `kernel/classes/audit/hook/expaudithook.php:25` | The kernel's audit call sites (doc/eng-us/bc/6.0/audit.md, "The event catalogue", stage 3): one guarded way to record an event from a hook point, and… | – |
| `expAuditIndexRow` | class | `kernel/classes/audit/index/expauditindexrow.php:12` | One audit record as a row of expaudit_event (doc/eng-us/bc/6.0/audit.md, "Tables"): the columns the console filters on, the record itself (as written… | – |
| `expAuditIndexSchema` | class | `kernel/classes/audit/index/expauditindexschema.php:18` | The audit index tables (doc/eng-us/bc/6.0/audit.md, "The index"): their definition in the generic schema format of share/db_schema.dba, creating them… | – |
| `expAuditIndexSettings` | class | `kernel/classes/audit/index/expauditindexsettings.php:12` | The settings of the audit index and console (audit.ini [AuditIndexSettings], [AuditConsoleSettings] and [AuditPrivacySettings] PseudonymiseAfterDays)… | – |
| `expAuditJson` | class | `kernel/classes/audit/expauditjson.php:16` | The canonical JSON of audit records (doc/eng-us/bc/6.0/audit.md, "Canonical JSON"). | – |
| `expAuditMailRecipients` | class | `kernel/classes/audit/sinks/expauditmailrecipients.php:30` | Who gets the audit's alert mail (doc/eng-us/bc/6.0/audit.md, "E-mail"; owner decision 2026-10-02). | – |
| `expAuditReauth` | class | `kernel/classes/audit/console/expauditreauth.php:26` | Password re-entry before the audit's manage actions (doc/eng-us/bc/6.0/audit.md, Q9; [AuditConsoleSettings] ReauthForManage, ReauthMinutes). | – |
| `expAuditSinkRegistry` | class | `kernel/classes/audit/sinks/expauditsinkregistry.php:23` | The sink registry and delivery (doc/eng-us/bc/6.0/audit.md, "Sinks (Q7)", "The sink registry"). | – |
| `expAuditSourceRegistry` | class | `kernel/classes/audit/console/expauditsourceregistry.php:16` | The registry of the audit console's sources (expAuditSource), from audit.ini [AuditSourceSettings] Sources[]: which are registered, which work here,… | – |
| `expAuditTaxonomy` | class | `kernel/classes/audit/expaudittaxonomy.php:21` | The audit taxonomy registry (doc/eng-us/bc/6.0/audit.md, "Taxonomy", "Naming rules", "The event catalogue"). | – |

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
| `expContentCheck` | class | `kernel/classes/contentcheck/expcontentcheck.php:19` | What the two content checks share (doc/eng-us/features/6.0/content-checks.md): their settings, where they answer, the policy check, the form tokens o… | – |
| `expContentCheckBrokenRelations` | class | `kernel/classes/contentcheck/expcontentcheckbrokenrelations.php:38` | Visible, published content that points at something a visitor cannot reach (doc/eng-us/features/6.0/content-checks.md, "Broken relations"). | – |
| `expContentCheckFileFix` | class | `kernel/classes/contentcheck/expcontentcheckfilefix.php:25` | The resolutions of the missing files list (doc/eng-us/features/6.0/content-checks.md, "Resolving"): each for one field version or for many, always pl… | – |
| `expContentCheckFiles` | class | `kernel/classes/contentcheck/expcontentcheckfiles.php:26` | Missing files and image aliases (doc/eng-us/features/6.0/content-checks.md, "Missing files"): the file-backed fields of the content below a node (ima… | – |
| `expContentCheckQuarantine` | class | `kernel/classes/contentcheck/expcontentcheckquarantine.php:25` | The actions of the storage check (doc/eng-us/features/6.0/content-checks.md, "Quarantine"). | – |
| `expContentCheckScope` | class | `kernel/classes/contentcheck/expcontentcheckscope.php:24` | The start location of the content checks (doc/eng-us/features/6.0/content-checks.md, "Where a check starts"): any node, from the whole tree (node 1)… | – |
| `expContentCheckStorage` | class | `kernel/classes/contentcheck/expcontentcheckstorage.php:28` | The storage check (doc/eng-us/features/6.0/content-checks.md, "Storage files"): the files below var//storage that no content uses (orphan files), and… | – |
| `expContentCheckTrash` | class | `kernel/classes/contentcheck/expcontentchecktrash.php:23` | Moves unreferenced objects to the trash (doc/eng-us/features/6.0/content-checks.md, "Moving to the trash"): the list posts the ticked objects, a conf… | – |
| `expContentCheckUnreferenced` | class | `kernel/classes/contentcheck/expcontentcheckunreferenced.php:31` | The assets below a node that nothing uses (doc/eng-us/features/6.0/content-checks.md, "Unreferenced objects"): published objects of an asset class ([… | – |

### kernel/classes/contentfinder (2)

[All patterns of kernel/classes/contentfinder](area-kernel-classes-contentfinder.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentFinderAction` | class | `kernel/classes/contentfinder/expcontentfinderaction.php:33` | The actions of the content finder (doc/eng-us/features/6.0/content-finder.md, "Actions"): move an object to the trash, delete it permanently, purge i… | – |
| `expContentFinderInput` | class | `kernel/classes/contentfinder/expcontentfinderinput.php:28` | What the content finder (module contentfinder, doc/eng-us/features/6.0/content-finder.md) makes of its one search field: the kinds of id or path the… | – |

### kernel/classes/contentjob (2)

[All patterns of kernel/classes/contentjob](area-kernel-classes-contentjob.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentJobLock` | class | `kernel/classes/contentjob/expcontentjoblock.php:30` | Subtree locks of the content jobs. | – |
| `expContentJobStore` | class | `kernel/classes/contentjob/expcontentjobstore.php:18` | The store of the content jobs: one JSON file per job in var//jobs/content/, written atomically (a temporary file in the same directory, flushed to di… | – |

### kernel/classes/datatable (9)

[All patterns of kernel/classes/datatable](area-kernel-classes-datatable.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expDataConnections` | class | `kernel/classes/datatable/expdataconnections.php:35` | The named database connections of the installation (doc/eng-us/features/6.0/data-tables.md, "Connections"): | – |
| `expDataSync` | class | `kernel/classes/datatable/sync/expdatasync.php:39` | The synchronisation between content and data tables (doc/eng-us/features/6.0/data-tables.md, "Synchronisation"): | – |
| `expDataSyncListener` | class | `kernel/classes/datatable/sync/expdatasynclistener.php:20` | The live mirror of the data table synchronisations (doc/eng-us/features/6.0/data-tables.md, "Live mirror"): the kernel notifies content/publish, cont… | – |
| `expDataTableDescriber` | class | `kernel/classes/datatable/expdatatabledescriber.php:25` | A data table as its database has it (doc/eng-us/features/6.0/data-tables.md, "Describe"): whether the table exists, its columns from the engine's own… | – |
| `expDataTableFieldSources` | class | `kernel/classes/datatable/expdatatablefieldsources.php:17` | The field sources of the module table wizard: the kernel's (a content class, an existing table on a connection) and the kernel services tagged datata… | – |
| `expDataTableFunctionCollection` | class | `kernel/classes/datatable/expdatatablefunctioncollection.php:28` | The template fetch functions of the datatable module (kernel/datatable/function_definition.php, doc/eng-us/features/6.0/data-tables.md, "In templates… | – |
| `expDataTableRelation` | class | `kernel/classes/datatable/expdatatablerelation.php:25` | Relations between data tables and content (doc/eng-us/features/6.0/data-tables.md, "Relations"): what a relation column of a row points at (a content… | – |
| `expDataTableSchema` | class | `kernel/classes/datatable/expdatatableschema.php:30` | The schema of data tables on any connection (doc/eng-us/features/6.0/data-tables.md, "Schema"): a .dba schema, or one made from a persistent class's… | – |
| `expKernelTableSchema` | class | `kernel/classes/datatable/expkerneltableschema.php:25` | Tables of a .dba schema on any engine: whether a table exists (asked of the engine's own catalogue, never inferred from a query result: MongoDB answe… | – |

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

### kernel/classes/help (16)

[All patterns of kernel/classes/help](area-kernel-classes-help.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expHelp` | class | `kernel/classes/help/exphelp.php:11` | The help system's shared helpers (doc/eng-us/features/6.0/help-system.md): its settings, whether it is on, the install root it reads from, and the UR… | – |
| `expHelpContentFeedback` | class | `kernel/classes/help/content/exphelpcontentfeedback.php:21` | "Was this helpful?" on each help topic (doc/eng-us/specifications/6.0/help-system.md, phase 7; ISO/IEC/IEEE 26514, usability of the information): the… | – |
| `expHelpContentHelpIds` | class | `kernel/classes/help/content/exphelpcontenthelpids.php:21` | The help_ids that were Released (doc/eng-us/specifications/6.0/help-style-guide.md, section 7: "A help_id is never renamed and never reused"): the ta… | – |
| `expHelpContentPackage` | class | `kernel/classes/help/content/exphelpcontentpackage.php:23` | The help classes as a content class package (doc/eng-us/specifications/6.0/help-content-package.md, CJW #16564): the eight help_* classes of expHelpC… | – |
| `expHelpContentReview` | class | `kernel/classes/help/content/exphelpcontentreview.php:23` | Overdue reviews of help maintained as content (doc/eng-us/specifications/6.0/help-system.md, phase 7 "a list of outdated topics (review date passed)… | – |
| `expHelpContentSchema` | class | `kernel/classes/help/content/exphelpcontentschema.php:20` | The content model of help maintained as content (doc/eng-us/specifications/6.0/help-system.md, doc/eng-us/features/6.0/help-content.md): the eight he… | – |
| `expHelpContentTables` | class | `kernel/classes/help/content/exphelpcontenttables.php:21` | The two tables of help maintained as content (doc/eng-us/features/6.0/help-content.md) on any engine: whether they exist, and creating them from thei… | – |
| `expHelpContentWorkflow` | class | `kernel/classes/help/content/exphelpcontentworkflow.php:23` | The review workflow of help maintained as content (doc/eng-us/specifications/6.0/help-system.md, "Status" and "Permissions"; style guide section 8, "… | – |
| `expHelpFrontMatter` | class | `kernel/classes/help/exphelpfrontmatter.php:28` | The front matter of a help page (doc/eng-us/features/6.0/help-system.md, "Front matter"): a block at the very top of a Markdown file between two line… | – |
| `expHelpFunctionCollection` | class | `kernel/classes/help/exphelpfunctioncollection.php:10` | The exphelp fetch functions (kernel/exphelp/function_definition.php). | – |
| `expHelpHighlighter` | class | `kernel/classes/help/exphelphighlighter.php:29` | Server-side syntax highlighting of the code blocks of the help pages (exphelp, doc/eng-us/features/6.0/help-system.md), without JavaScript or an exte… | – |
| `expHelpLicense` | class | `kernel/classes/help/exphelplicense.php:17` | The license of the documentation the help shows (exphelp.ini [License], doc/eng-us/README-LICENSE.md). | – |
| `expHelpLinks` | class | `kernel/classes/help/exphelplinks.php:12` | The link and image resolvers the viewer gives expHelpMarkdownRenderer (doc/eng-us/features/6.0/help-system.md): a relative link to an allowed page be… | – |
| `expHelpPdf` | class | `kernel/classes/help/exphelppdf.php:18` | A help page as a PDF file (doc/eng-us/features/6.0/help-system.md, "Print and PDF"): the view exphelp/pdf/ and `./console exp:help pdf ` ask here. | – |
| `expHelpPdfFontSubset` | class | `kernel/classes/help/exphelppdffontsubset.php:17` | Cuts a TrueType font down to the glyphs a PDF uses (doc/eng-us/features/6.0/help-system.md, "Print and PDF"). | – |
| `expHelpReadPolicyGrant` | class | `kernel/classes/help/exphelpreadpolicygrant.php:22` | Plans and applies the grant of the policy exphelp/read (bin/php/addexphelpreadpolicy.php): reading the help system (doc/eng-us/features/6.0/help-syst… | – |

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
| `expLogActions` | class | `kernel/classes/logviewer/explogactions.php:13` | The actions of the log viewer besides reading (doc/eng-us/features/6.0/log-viewer.md, "Actions"): download a range, rotate now, empty a file, remove… | – |
| `expLogBundle` | class | `kernel/classes/logviewer/explogbundle.php:17` | "Download all logs" (Setup › Manage logs; doc/eng-us/features/6.0/log-viewer.md, "Downloading every log"): one archive of every log the viewer lists,… | – |
| `expLogHealth` | class | `kernel/classes/logviewer/exploghealth.php:17` | The health of the logs (Setup › Log files › Health, setup/logshealth; doc/eng-us/features/6.0/log-viewer.md, "Health"): the disk the logs and their a… | – |
| `expLogParser` | class | `kernel/classes/logviewer/explogparser.php:25` | Reads the lines of a log file as entries (doc/eng-us/features/6.0/log-viewer.md, "The line format"). | – |
| `expLogPermalink` | class | `kernel/classes/logviewer/explogpermalink.php:25` | The permalinks of the log viewer (doc/eng-us/features/6.0/log-viewer.md, "Permalinks"): every view of a log as a path without a query string, short e… | – |

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

### kernel/classes/passkey (1)

[All patterns of kernel/classes/passkey](area-kernel-classes-passkey.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expPasskeySchema` | class | `kernel/classes/passkey/exppasskeyschema.php:17` | The exppasskey table on any engine: whether it exists, and creating it from its definition in share/db_schema.dba through the engine's schema handler… | – |

### kernel/classes/pdfexport (4)

[All patterns of kernel/classes/pdfexport](area-kernel-classes-pdfexport.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expPDFExportFile` | class | `kernel/classes/pdfexport/exppdfexportfile.php:19` | The file of a PDF export that is generated once: its name, where it is kept, what is known about it, sending it and removing it. | – |
| `expPDFExportForm` | class | `kernel/classes/pdfexport/exppdfexportform.php:19` | Reads and checks the form of pdf/edit. | – |
| `expPDFExportGenerator` | class | `kernel/classes/pdfexport/exppdfexportgenerator.php:27` | Generates the PDF of an export: into its stored file, or into a temporary file that is then sent and removed. | – |
| `expPDFExportInfo` | class | `kernel/classes/pdfexport/exppdfexportinfo.php:19` | What the PDF export pages (pdf/list and its removal confirmation) say about an export beyond its row: how it is produced, whether its source node sti… | – |

### kernel/classes/reauth (3)

[All patterns of kernel/classes/reauth](area-kernel-classes-reauth.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expReauth` | class | `kernel/classes/reauth/expreauth.php:37` | "Verify it's you" (doc/eng-us/guides/verify-its-you.md): before a sensitive action the signed-in user confirms who they are again, with their passwor… | – |
| `expReauthRules` | class | `kernel/classes/reauth/expreauthrules.php:28` | The rules of "Verify it's you" (site.ini [ReauthSettings], [ReauthGroup_] Rules[]) and the decision whether a request matches one (doc/eng-us/guides/… | – |
| `expReauthSettings` | class | `kernel/classes/reauth/expreauthsettings.php:18` | The settings of "Verify it's you" as the page setup/reauth edits them (doc/eng-us/guides/verify-its-you.md): the switch of the whole feature, the min… | – |

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

### kernel/classes/secrets (2)

[All patterns of kernel/classes/secrets](area-kernel-classes-secrets.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSecretKey` | class | `kernel/classes/secrets/expsecretkey.php:31` | The key of the kernel's secret store (expSecrets, doc/eng-us/features/6.0/secret-store.md): | – |
| `expSecrets` | class | `kernel/classes/secrets/expsecrets.php:34` | The kernel's secret store (doc/eng-us/features/6.0/secret-store.md): one place for every password, token and API key a tool needs (the data table con… | – |

### kernel/classes/subitems (4)

[All patterns of kernel/classes/subitems](area-kernel-classes-subitems.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSubitemsColumnHandlers` | class | `kernel/classes/subitems/columns/expsubitemscolumnhandlers.php:16` | Example Handler= columns of the admin subitems list: plain static methods an INI block names as Handler=expSubitemsColumnHandlers::. | – |
| `expSubitemsCSVExport` | class | `kernel/classes/subitems/expsubitemscsvexport.php:14` | The CSV of the admin subitems list (content/subitemsexport/). | – |
| `expSubitemsPreference` | class | `kernel/classes/subitems/expsubitemspreference.php:20` | The per-user choice of the admin subitems list: visible columns in order and the chosen preset per navigation part, the page size, and the user's own… | – |
| `expSubitemsServerFunctions` | class extends ezjscServerFunctions | `kernel/classes/subitems/expsubitemsserverfunctions.php:24` | The ezjscore server functions of the admin subitems list ("Table options"), registered as [ezjscServer_expsubitems] in extension/ezjscore/settings/ez… | extension/ezjscore/settings/ezjscore.ini [ezjscServer_expsubitems] Class |

### kernel/classes/syscard (1)

[All patterns of kernel/classes/syscard](area-kernel-classes-syscard.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expSysCardColor` | class | `kernel/classes/syscard/expsyscardcolor.php:21` | The colors of the system card on a terminal: which color depth the terminal has, and the ANSI codes of a color at that depth. | – |

### kernel/classes/transfer (3)

[All patterns of kernel/classes/transfer](area-kernel-classes-transfer.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expKernelTransferHistory` | class | `kernel/classes/transfer/expkerneltransferhistory.php:38` | The run history of the import and export tools (doc/eng-us/features/6.0/transfer-runs.md): the writer every tool calls for its runs, and the reader o… | – |
| `expKernelTransferRunner` | class | `kernel/classes/transfer/expkerneltransferrunner.php:36` | One way for every import and export tool to run its work in the background (doc/eng-us/features/6.0/transfer-runs.md), on whichever engine the instal… | – |
| `expKernelTransferRunSchema` | class | `kernel/classes/transfer/expkerneltransferrunschema.php:21` | The table exp_transfer_run on any engine: whether it exists, and creating it from its definition in share/db_schema.dba through the engine's schema h… | – |

### kernel/classes/uiswitch (1)

[All patterns of kernel/classes/uiswitch](area-kernel-classes-uiswitch.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expUISwitch` | class | `kernel/classes/uiswitch/expuiswitch.php:28` | The UI switch framework: a registry of providers (uiswitch.ini [UISwitchSettings] ProviderList[]), the user's choices kept in the session, and the ea… | – |

### kernel/classes/user (10)

[All patterns of kernel/classes/user](area-kernel-classes-user.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expPasswordChangeRequired` | class | `kernel/classes/user/exppasswordchangerequired.php:35` | "Change password at next sign-in": an administrator marks an account, and after its next sign-in the user is sent to the password change form (user/p… | – |
| `expRegistrationAdminAction` | class | `kernel/classes/user/expregistrationadminaction.php:23` | The signed links of the administrator's "New user registered" mail (design:user/registrationfeedback.tpl) and the actions behind them (user/registrat… | – |
| `expSignInSecondFactor` | class | `kernel/classes/user/expsigninsecondfactor.php:37` | The second step of signing in on the paths that do not go through the web login form: the REST interface with a password (ezpRestBasicAuthStyle), Web… | – |
| `expUnactivatedUsers` | class | `kernel/classes/user/expunactivatedusers.php:22` | The users who registered and never activated their account, for the page user/unactivated: how many there are, a page of them searched by login, e-ma… | – |
| `expUserAccountStatus` | class | `kernel/classes/user/expuseraccountstatus.php:21` | The account status the administration marks on user objects: enabled, disabled (Setup › Users, ezuser_setting.is_enabled = 0) or locked (more failed… | – |
| `expUserActivation` | class | `kernel/classes/user/expuseractivation.php:25` | What follows the enabling of an account, for user/activate (the link the user clicks) and the Activate button of user/unactivated. | – |
| `expUserAgent` | class | `kernel/classes/user/expuseragent.php:30` | What a person reads about a signed-in session (user/sessions): the browser, the operating system and the kind of device a User-Agent header names, an… | – |
| `expUserApproval` | class | `kernel/classes/user/expuserapproval.php:30` | Approval of new accounts in chosen user groups (doc/eng-us/bc/6.0/user-account-approval.md): the workflow event "Approve user accounts" (expUserAppro… | – |
| `expUserDeactivation` | class | `kernel/classes/user/expuserdeactivation.php:36` | "Deactivate and move" and "Reactivate and move back": instead of removing a user account (users never go to the trash, so removing one cannot be undo… | – |
| `expUserSessionTracker` | class | `kernel/classes/user/expusersessiontracker.php:44` | The signed-in sessions of a user ("Your devices", user/sessions): every sign-in gets a row in expuser_session (expUserSession), the user sees where t… | – |

### kernel/classes/webauthn (4)

[All patterns of kernel/classes/webauthn](area-kernel-classes-webauthn.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expWebAuthn` | class | `kernel/classes/webauthn/expwebauthn.php:34` | The server side of WebAuthn (W3C Web Authentication, Level 2/3) for passkeys: the options a browser is given, and the checks of what it sends back wh… | – |
| `expWebAuthnCbor` | class | `kernel/classes/webauthn/expwebauthncbor.php:20` | The part of CBOR (RFC 8949) that WebAuthn needs: decoding the attestation object of a new passkey and the COSE public key inside its authenticator da… | – |
| `expWebAuthnChallenge` | class | `kernel/classes/webauthn/expwebauthnchallenge.php:19` | The WebAuthn challenges a session was given: each request for options gets a new random challenge, kept on the server (in the session) with what it i… | – |
| `expWebAuthnCose` | class | `kernel/classes/webauthn/expwebauthncose.php:26` | The public key of a passkey as COSE (RFC 9052/9053) and the check of a signature made with it. | – |

### kernel/common (2)

[All patterns of kernel/common](area-kernel-common.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpI18n` | class | `kernel/common/ezpi18n.php:12` | – | – |
| `eZTemplatesStatisticsReporter` | class | `kernel/common/eztemplatesstatisticsreporter.php:19` | – | – |

### kernel/modules (28)

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
| `expModuleTableCode` | class | `kernel/setup/expmoduletablecode.php:22` | The files of a module table (expModuleTableWizard): the persistent class, the module with its list, edit, remove and JSON views and its policies, the… | – |
| `expModuleTableWizard` | class extends expExtensionWizard | `kernel/setup/expmoduletablewizard.php:34` | The module table wizard (Setup › RAD › Module table, setup/moduletable; doc/eng-us/features/6.0/data-tables.md, "The module table wizard"): a NEW dat… | – |
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

### kernel/private (19)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expApiKeyRest` | class | `kernel/private/rest/classes/auth/apikey_rest.php:24` | How the REST layer resolves a personal API key (doc/eng-us/guides/api-keys.md, "For developers"). | – |
| `expContentAccessReport` | class | `kernel/private/classes/expcontentaccessreport.php:23` | Answers "may this user do this with this object or node, and if not, which limitation refuses it": the access checks of the kernel made for another u… | – |
| `expContentSecurityPolicy` | class | `kernel/private/classes/expcontentsecuritypolicy.php:39` | The Content Security Policy of a page, from site.ini [ContentSecurityPolicy]. | – |
| `expOAuthAuthorizePolicy` | class | `kernel/private/rest/classes/oauth/authorize_policy.php:21` | Decides whether a signed in user may authorize an OAuth client in oauth/authorize. | – |
| `expOAuthAuthorizePolicyGrant` | class | `kernel/private/rest/classes/oauth/authorize_policy_grant.php:26` | Plans and applies the upgrade grant of the policy oauth/authorize (bin/php/addoauthauthorizepolicy.php). | – |
| `expOAuthPkce` | class | `kernel/private/rest/classes/oauth/pkce.php:24` | Proof Key for Code Exchange (PKCE, RFC 7636) for the OAuth authorization code flow. | – |
| `expRestContentPermission` | class | `kernel/private/rest/classes/auth/content_permission.php:36` | The current user's rights on the content a REST call reads or writes, checked the way the content module checks them (doc/eng-us/guides/api-keys.md,… | – |
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
| `ezpSiteAccessURL` | class | `kernel/private/classes/ezpsiteaccessurl.php:44` | The address of a siteaccess's front page, worked out from the siteaccess settings and the request the visitor is on: the admin's "open the site" link… | – |

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
| `expLogRotator` | class | `lib/ezfile/classes/explogrotator.php:28` | The one implementation of log rotation in the kernel (doc/eng-us/features/6.0/log-rotation.md). | – |
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
