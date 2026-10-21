---
title: Module view (Page Controller)
audience: developer
category: standards
order: 316
summary: Each view is a page controller: the front controller dispatches /MODULE/VIEW to the view's script, which reads the request and renders a template. 921 instances in Exponential.
keywords: design pattern, module-view, request handling, pattern catalogue
type: catalogue
---
# Module view (Page Controller)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Each view is a page controller: the front controller dispatches /MODULE/VIEW to the view's script, which reads the request and renders a template.

| | |
|---|---|
| Group | Request handling |
| Instances | 921 |
| Recognized by | An entry of $ViewList in a module.php, or a class that extends ModuleView or Page (the runnable form of a view). |
| Extension point | module.ini [ModuleSettings] ExtensionRepositories[] and ModuleList[]; views declare the policy functions they need. |

## Classes by area

### extension/bccie (3)

[All patterns of extension/bccie](area-extension-bccie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Bccie\Bccie\Doexport` | class extends ModuleView | `extension/bccie/classes/runnable/views/bccie/doexport.php:22` | The code of extension/bccie/modules/bccie/doexport.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Bccie\Bccie\Export` | class extends ModuleView | `extension/bccie/classes/runnable/views/bccie/export.php:22` | The code of extension/bccie/modules/bccie/export.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Bccie\Bccie\Overview` | class extends ModuleView | `extension/bccie/classes/runnable/views/bccie/overview.php:22` | The code of extension/bccie/modules/bccie/overview.php, moved into a class (#207 stage 1), rebuilt in 1.1.12 as a dashboard with a list. | – |

### extension/bcgooglesitemaps (1)

[All patterns of extension/bcgooglesitemaps](area-extension-bcgooglesitemaps.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Bcgooglesitemaps\Googlesitemapdynamic\Sitemap` | class extends ModuleView | `extension/bcgooglesitemaps/classes/runnable/views/googlesitemapdynamic/sitemap.php:23` | The code of extension/bcgooglesitemaps/modules/googlesitemapdynamic/sitemap.php, moved into a class (#207 stage 1). | – |

### extension/cjw_newsletter (57)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\CjwNewsletter\Newsletter\AbTest` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/ab_test.php:16` | The A/B subject test of a send (cjw_newsletter 4.2.0, area N4 Statistics): newsletter/ab_test/. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Approval` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/approval.php:14` | The approval of an edition version: its state and history, "Ask for approval" for the editors, Approve and Reject (with a comment) for the users with… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Archive` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/archive.php:28` | The code of extension/cjw_newsletter/modules/newsletter/archive.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ArticlePool` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/article_pool.php:18` | The article picker of an edition: the articles of the pool of the edition's list, filtered by class, date, section, tag and state; the chosen ones ar… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ArticlePoolEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/article_pool_edit.php:14` | Create or edit an article pool: the nodes searched, the classes, sections, tags and states, the age, how many articles the auto-fill takes and their… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ArticlePoolList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/article_pool_list.php:13` | The article pools: the global default and the pools of the lists, with remove (after a confirmation). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\BlacklistItemAdd` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/blacklist_item_add.php:26` | The code of extension/cjw_newsletter/modules/newsletter/blacklist_item_add.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\BlacklistItemList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/blacklist_item_list.php:24` | The code of extension/cjw_newsletter/modules/newsletter/blacklist_item_list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\BlacklistItemRemove` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/blacklist_item_remove.php:26` | The code of extension/cjw_newsletter/modules/newsletter/blacklist_item_remove.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Configure` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/configure.php:26` | The code of extension/cjw_newsletter/modules/newsletter/configure.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ImportList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/import_list.php:24` | The code of extension/cjw_newsletter/modules/newsletter/import_list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ImportMapping` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/import_mapping.php:16` | newsletter/import_mapping: a CSV import with a column mapping. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ImportView` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/import_view.php:24` | The code of extension/cjw_newsletter/modules/newsletter/import_view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Index` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/index.php:24` | The code of extension/cjw_newsletter/modules/newsletter/index.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\InterestEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/interest_edit.php:13` | Creates (newsletter/interest_edit/0) or edits an interest. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\InterestList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/interest_list.php:14` | The interests subscribers can pick (cjwnl_interest): the list with the newsletter list, the source, the tag, how many subscribers picked it, and remo… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Job` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/job.php:14` | The state of a background run as JSON: status (starting, running, done, failed), the result and the last log lines. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailboxEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailbox_edit.php:26` | The code of extension/cjw_newsletter/modules/newsletter/mailbox_edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailboxItemList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailbox_item_list.php:24` | The code of extension/cjw_newsletter/modules/newsletter/mailbox_item_list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailboxItemView` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailbox_item_view.php:64` | Passthrough file, and exit cleanly | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailboxList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailbox_list.php:26` | The code of extension/cjw_newsletter/modules/newsletter/mailbox_list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailinAddressEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailin_address_edit.php:14` | newsletter/mailin_address_edit/: create, edit or remove a mail-in address (cjw_newsletter 4.2.0, area deliverability). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MailinAddressList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/mailin_address_list.php:14` | newsletter/mailin_address_list: the mail-in addresses and the last messages they got (cjw_newsletter 4.2.0, area deliverability). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\MigrationLog` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/migration_log.php:14` | newsletter/migration_log[/]: the runs of ext:cjw_newsletter:import-eznewsletter, and the rows of one run (filtered by (action)/ and (table)/, paged w… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\O` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/o.php:16` | The open pixel of a tracked newsletter (cjw_newsletter 4.2.0, area N4 Statistics): newsletter/o//. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Preview` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/preview.php:27` | The code of extension/cjw_newsletter/modules/newsletter/preview.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\PreviewArchive` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/preview_archive.php:28` | The code of extension/cjw_newsletter/modules/newsletter/preview_archive.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\PreviewAs` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/preview_as.php:17` | The preview of an edition as one subscriber gets it: his language, the conditional parts, the interests block and the placeholders, in a skin the lis… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\R` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/r.php:19` | The click redirect of a tracked newsletter (cjw_newsletter 4.2.0, area N4 Statistics): newsletter/r///. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Report` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/report.php:16` | The statistics of the newsletter sends (cjw_newsletter 4.2.0, area N4 Statistics): newsletter/report lists the sends with their numbers, newsletter/r… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ScheduleEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/schedule_edit.php:15` | Create or edit a recurring send: the list, the mode (copy a template edition or send the latest unsent edition), when (chosen weekdays, weekly, month… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\ScheduleList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/schedule_list.php:14` | The recurring sends: the list with the next and the last run, pause, resume, run now (also as a dry run), remove, and the last runs of all schedules. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Send` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/send.php:24` | The code of extension/cjw_newsletter/modules/newsletter/send.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SendAbort` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/send_abort.php:24` | The code of extension/cjw_newsletter/modules/newsletter/send_abort.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Settings` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/settings.php:24` | The code of extension/cjw_newsletter/modules/newsletter/settings.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SkinPreview` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/skin_preview.php:14` | The skins: their names, descriptions and preview images, and one skin rendered with an edition (the newest one, or /(edition)/) in the HTML or the te… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SmsConfirm` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/sms_confirm.php:17` | The public page where a subscriber enters the confirmation code of their mobile number: newsletter/sms_confirm/ | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SmsInbound` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/sms_inbound.php:18` | The endpoint an SMS provider calls with an incoming SMS: newsletter/sms_inbound/. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SmsSend` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/sms_send.php:17` | Sends an edition by SMS: newsletter/sms_send/. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\StatisticsExport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/statistics_export.php:18` | The statistics as CSV (cjw_newsletter 4.2.0, area N4 Statistics): newsletter/statistics_export one row per send newsletter/statistics_export/ the row… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Subscribe` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscribe.php:24` | The code of extension/cjw_newsletter/modules/newsletter/subscribe.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscribeInfomail` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscribe_infomail.php:24` | The code of extension/cjw_newsletter/modules/newsletter/subscribe_infomail.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriberExport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscriber_export.php:15` | newsletter/subscriber_export/: the subscribers of a list as a CSV file, with filters (status, a date range) and a choice of columns. | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriptionList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscription_list.php:24` | The code of extension/cjw_newsletter/modules/newsletter/subscription_list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriptionListCsvexport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscription_list_csvexport.php:102` | fetch data for csv export | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriptionListCsvimport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscription_list_csvimport.php:27` | The code of extension/cjw_newsletter/modules/newsletter/subscription_list_csvimport.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SubscriptionView` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/subscription_view.php:24` | The code of extension/cjw_newsletter/modules/newsletter/subscription_view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\SuppressionImport` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/suppression_import.php:14` | newsletter/suppression_import: a CSV file of addresses into the kernel suppression list, checked first (dry run) (cjw_newsletter 4.2.0, area delivera… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\TestGroupEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/test_group_edit.php:13` | newsletter/test_group_edit/: create, edit or remove a test group (cjw_newsletter 4.2.0, area deliverability). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\TestGroupList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/test_group_list.php:13` | newsletter/test_group_list: the test groups of the lists (cjw_newsletter 4.2.0, area deliverability). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Throttle` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/throttle.php:14` | newsletter/throttle: the rate limits, pauses and batches of the transports, and the items that wait for a soft-bounce retry (cjw_newsletter 4.2.0, ar… | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\Unsubscribe` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/unsubscribe.php:24` | The code of extension/cjw_newsletter/modules/newsletter/unsubscribe.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\UserCreate` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/user_create.php:26` | The code of extension/cjw_newsletter/modules/newsletter/user_create.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\UserEdit` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/user_edit.php:26` | The code of extension/cjw_newsletter/modules/newsletter/user_edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\UserList` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/user_list.php:24` | The code of extension/cjw_newsletter/modules/newsletter/user_list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\UserRemove` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/user_remove.php:24` | The code of extension/cjw_newsletter/modules/newsletter/user_remove.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\CjwNewsletter\Newsletter\UserView` | class extends ModuleView | `extension/cjw_newsletter/classes/runnable/views/newsletter/user_view.php:24` | The code of extension/cjw_newsletter/modules/newsletter/user_view.php, moved into a class (#207 stage 1). | – |

### extension/expchangeclass (3)

[All patterns of extension/expchangeclass](area-extension-expchangeclass.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Expchangeclass\Changeclass\Action` | class extends ModuleView | `extension/expchangeclass/classes/runnable/views/changeclass/action.php:43` | The code of extension/expchangeclass/modules/changeclass/action.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Expchangeclass\Changeclass\MapAttributes` | class extends ModuleView | `extension/expchangeclass/classes/runnable/views/changeclass/map_attributes.php:10` | The code of extension/expchangeclass/modules/changeclass/map_attributes.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Expchangeclass\Changeclass\SelectClass` | class extends ModuleView | `extension/expchangeclass/classes/runnable/views/changeclass/select_class.php:10` | The code of extension/expchangeclass/modules/changeclass/select_class.php, moved into a class (#207 stage 1). | – |

### extension/explayouts (9)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Explayouts\Explayouts\BlockEdit` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/block_edit.php:10` | The code of extension/explayouts/modules/explayouts/block_edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\Dashboard` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/dashboard.php:22` | The code of extension/explayouts/modules/explayouts/dashboard.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\LayoutEdit` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/layout_edit.php:77` | The code of extension/explayouts/modules/explayouts/layout_edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\LayoutList` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/layout_list.php:10` | The code of extension/explayouts/modules/explayouts/layout_list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\Preview` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/preview.php:10` | The code of extension/explayouts/modules/explayouts/preview.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\RuleEdit` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/rule_edit.php:10` | The code of extension/explayouts/modules/explayouts/rule_edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\RuleList` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/rule_list.php:10` | The code of extension/explayouts/modules/explayouts/rule_list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\Setup` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/setup.php:10` | The code of extension/explayouts/modules/explayouts/setup.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Explayouts\Explayouts\TemplateEditor` | class extends ModuleView | `extension/explayouts/classes/runnable/views/explayouts/template_editor.php:27` | The code of extension/explayouts/modules/explayouts/template_editor.php, moved into a class (#207 stage 1). | – |

### extension/explayouts_content_browser_ui (1)

[All patterns of extension/explayouts_content_browser_ui](area-extension-explayouts-content-browser-ui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\ExplayoutsContentBrowserUi\ExplayoutsContentBrowserUi\Browser` | class extends ModuleView | `extension/explayouts_content_browser_ui/classes/runnable/views/explayouts_content_browser_ui/browser.php:10` | The code of extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui/browser.php, moved into a class (#207 stage 1). | – |

### extension/explayouts_ui (14)

[All patterns of extension/explayouts_ui](area-extension-explayouts-ui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\BlockEdit` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/block_edit.php:10` | The code of extension/explayouts_ui/modules/explayouts_ui/block_edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Components` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/components.php:42` | Converts a view_type identifier like "features_style_2" into a human readable style label. | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Dashboard` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/dashboard.php:22` | The code of extension/explayouts_ui/modules/explayouts_ui/dashboard.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutCreate` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_create.php:10` | The code of extension/explayouts_ui/modules/explayouts_ui/layout_create.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutEdit` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_edit.php:77` | The code of extension/explayouts_ui/modules/explayouts_ui/layout_edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\LayoutList` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/layout_list.php:91` | The code of extension/explayouts_ui/modules/explayouts_ui/layout_list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Preview` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/preview.php:105` | Pick a node to preview the layout against. | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\RuleEdit` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/rule_edit.php:10` | The code of extension/explayouts_ui/modules/explayouts_ui/rule_edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\RuleList` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/rule_list.php:10` | The code of extension/explayouts_ui/modules/explayouts_ui/rule_list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\Setup` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/setup.php:10` | The code of extension/explayouts_ui/modules/explayouts_ui/setup.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\SharedLayoutsList` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/shared_layouts_list.php:91` | The code of extension/explayouts_ui/modules/explayouts_ui/shared_layouts_list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TemplateEditor` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/template_editor.php:40` | explayouts_ui/template_editor: the template editor of the layout templates, inside the administration's page (top menu, breadcrumb, the Layouts left… | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TransferExport` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/transfer_export.php:16` | explayouts_ui/transfer_export: exports layouts, shared layouts and rules into a layouts transfer file (expLayoutsCoreTransferService) and sends it as… | – |
| `Exponential\View\Extension\ExplayoutsUi\ExplayoutsUi\TransferImport` | class extends ModuleView | `extension/explayouts_ui/classes/runnable/views/explayouts_ui/transfer_import.php:18` | explayouts_ui/transfer_import: imports a layouts transfer file (expLayoutsCoreTransferService). | – |

### extension/explayouts_ui_api (4)

[All patterns of extension/explayouts_ui_api](area-extension-explayouts-ui-api.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\App` | class extends ModuleView | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/app.php:10` | The code of extension/explayouts_ui_api/modules/explayouts_ui_api/app.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Blocks` | class extends ModuleView | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/blocks.php:38` | The code of extension/explayouts_ui_api/modules/explayouts_ui_api/blocks.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Layouts` | class extends ModuleView | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/layouts.php:31` | The code of extension/explayouts_ui_api/modules/explayouts_ui_api/layouts.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Rules` | class extends ModuleView | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/rules.php:50` | The code of extension/explayouts_ui_api/modules/explayouts_ui_api/rules.php, moved into a class (#207 stage 1). | – |

### extension/expui (1)

[All patterns of extension/expui](area-extension-expui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Expui\Expui\Test` | class extends ModuleView | `extension/expui/classes/runnable/views/expui/test.php:23` | The code of extension/expui/modules/expui/test.php, moved into a class (#207 stage 1). | – |

### extension/ezie (13)

[All patterns of extension/ezie](area-extension-ezie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezie\Ezie\FilterBrightness` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_brightness.php:23` | The code of extension/ezie/modules/ezie/filter_brightness.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterBw` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_bw.php:23` | The code of extension/ezie/modules/ezie/filter_bw.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterContrast` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_contrast.php:23` | The code of extension/ezie/modules/ezie/filter_contrast.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\FilterSepia` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/filter_sepia.php:23` | The code of extension/ezie/modules/ezie/filter_sepia.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\NoSaveAndQuit` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/no_save_and_quit.php:26` | The code of extension/ezie/modules/ezie/no_save_and_quit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\Prepare` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/prepare.php:24` | The code of extension/ezie/modules/ezie/prepare.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\SaveAndQuit` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/save_and_quit.php:27` | The code of extension/ezie/modules/ezie/save_and_quit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolCrop` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_crop.php:23` | The code of extension/ezie/modules/ezie/tool_crop.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolFlipHor` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_flip_hor.php:23` | The code of extension/ezie/modules/ezie/tool_flip_hor.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolFlipVer` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_flip_ver.php:23` | The code of extension/ezie/modules/ezie/tool_flip_ver.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolPixelate` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_pixelate.php:23` | The code of extension/ezie/modules/ezie/tool_pixelate.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolRotation` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_rotation.php:23` | The code of extension/ezie/modules/ezie/tool_rotation.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezie\Ezie\ToolWatermark` | class extends ModuleView | `extension/ezie/classes/runnable/views/ezie/tool_watermark.php:23` | The code of extension/ezie/modules/ezie/tool_watermark.php, moved into a class (#207 stage 1). | – |

### extension/ezjscore (3)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezjscore\Ezjscore\Call` | class extends ModuleView | `extension/ezjscore/classes/runnable/views/ezjscore/call.php:89` | The code of extension/ezjscore/modules/ezjscore/call.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezjscore\Ezjscore\Hello` | class extends ModuleView | `extension/ezjscore/classes/runnable/views/ezjscore/hello.php:43` | The code of extension/ezjscore/modules/ezjscore/hello.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezjscore\Ezjscore\Run` | class extends ModuleView | `extension/ezjscore/classes/runnable/views/ezjscore/run.php:79` | The code of extension/ezjscore/modules/ezjscore/run.php, moved into a class (#207 stage 1). | – |

### extension/ezmultiupload (2)

[All patterns of extension/ezmultiupload](area-extension-ezmultiupload.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezmultiupload\Ezmultiupload\Service` | class extends ModuleView | `extension/ezmultiupload/classes/runnable/views/ezmultiupload/service.php:23` | ezmultiupload/service: the checks and actions of the upload page, answered as JSON. | – |
| `Exponential\View\Extension\Ezmultiupload\Ezmultiupload\Upload` | class extends ModuleView | `extension/ezmultiupload/classes/runnable/views/ezmultiupload/upload.php:23` | The code of extension/ezmultiupload/modules/ezmultiupload/upload.php, moved into a class (#207 stage 1). | – |

### extension/ezodf (2)

[All patterns of extension/ezodf](area-extension-ezodf.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezodf\Ezodf\Export` | class extends ModuleView | `extension/ezodf/classes/runnable/views/ezodf/export.php:71` | The code of extension/ezodf/modules/ezodf/export.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezodf\Ezodf\Import` | class extends ModuleView | `extension/ezodf/classes/runnable/views/ezodf/import.php:44` | The code of extension/ezodf/modules/ezodf/import.php, moved into a class (#207 stage 1). | – |

### extension/ezoe (7)

[All patterns of extension/ezoe](area-extension-ezoe.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezoe\Ezoe\Dialog` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/dialog.php:10` | The code of extension/ezoe/modules/ezoe/dialog.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezoe\Ezoe\EmbedView` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/embed_view.php:10` | The code of extension/ezoe/modules/ezoe/embed_view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezoe\Ezoe\Load` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/load.php:10` | The code of extension/ezoe/modules/ezoe/load.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezoe\Ezoe\Relations` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/relations.php:10` | The code of extension/ezoe/modules/ezoe/relations.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezoe\Ezoe\SpellcheckRpc` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/spellcheck_rpc.php:58` | Returns an request value by name without magic quoting. | – |
| `Exponential\View\Extension\Ezoe\Ezoe\Tags` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/tags.php:11` | The code of extension/ezoe/modules/ezoe/tags.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezoe\Ezoe\Upload` | class extends ModuleView | `extension/ezoe/classes/runnable/views/ezoe/upload.php:10` | The code of extension/ezoe/modules/ezoe/upload.php, moved into a class (#207 stage 1). | – |

### extension/ezpaypal (1)

[All patterns of extension/ezpaypal](area-extension-ezpaypal.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezpaypal\Paypal\NotifyUrl` | class extends ModuleView | `extension/ezpaypal/classes/runnable/views/paypal/notify_url.php:38` | The code of extension/ezpaypal/modules/paypal/notify_url.php, moved into a class (#207 stage 1). | – |

### extension/eztags (17)

[All patterns of extension/eztags](area-extension-eztags.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Eztags\Tags\Add` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/add.php:15` | The code of extension/eztags/modules/tags/add.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Addsynonym` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/addsynonym.php:15` | The code of extension/eztags/modules/tags/addsynonym.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Dashboard` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/dashboard.php:10` | The code of extension/eztags/modules/tags/dashboard.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Delete` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/delete.php:15` | The code of extension/eztags/modules/tags/delete.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Deletesynonym` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/deletesynonym.php:15` | The code of extension/eztags/modules/tags/deletesynonym.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Deletetags` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/deletetags.php:15` | The code of extension/eztags/modules/tags/deletetags.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Edit` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/edit.php:15` | The code of extension/eztags/modules/tags/edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Editsynonym` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/editsynonym.php:15` | The code of extension/eztags/modules/tags/editsynonym.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Id` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/id.php:15` | The code of extension/eztags/modules/tags/id.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\ListObjects` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/list_objects.php:15` | The code of extension/eztags/modules/tags/list_objects.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Makesynonym` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/makesynonym.php:15` | The code of extension/eztags/modules/tags/makesynonym.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Merge` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/merge.php:15` | The code of extension/eztags/modules/tags/merge.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Movetags` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/movetags.php:15` | The code of extension/eztags/modules/tags/movetags.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Search` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/search.php:10` | The code of extension/eztags/modules/tags/search.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Translation` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/translation.php:15` | The code of extension/eztags/modules/tags/translation.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\Treemenu` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/treemenu.php:15` | The code of extension/eztags/modules/tags/treemenu.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Eztags\Tags\View` | class extends ModuleView | `extension/eztags/classes/runnable/views/tags/view.php:15` | The code of extension/eztags/modules/tags/view.php, moved into a class (#207 stage 1). | – |

### extension/ezupdate (8)

[All patterns of extension/ezupdate](area-extension-ezupdate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezupdate\Update\Browse` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/browse.php:22` | The code of extension/ezupdate/modules/update/browse.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Dashboard` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/dashboard.php:23` | The code of extension/ezupdate/modules/update/dashboard.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Fund` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/fund.php:27` | The code of extension/ezupdate/modules/update/fund.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Installed` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/installed.php:24` | The code of extension/ezupdate/modules/update/installed.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Job` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/job.php:23` | The code of extension/ezupdate/modules/update/job.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Package` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/package.php:22` | The code of extension/ezupdate/modules/update/package.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Packages` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/packages.php:22` | The code of extension/ezupdate/modules/update/packages.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezupdate\Update\Servers` | class extends ModuleView | `extension/ezupdate/classes/runnable/views/update/servers.php:22` | The code of extension/ezupdate/modules/update/servers.php, moved into a class (#207 stage 1). | – |

### extension/ezwt (2)

[All patterns of extension/ezwt](area-extension-ezwt.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezwt\Websitetoolbar\Sort` | class extends ModuleView | `extension/ezwt/classes/runnable/views/websitetoolbar/sort.php:34` | The code of extension/ezwt/modules/websitetoolbar/sort.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Ezwt\Websitetoolbar\Websitetoolbar` | class extends ModuleView | `extension/ezwt/classes/runnable/views/websitetoolbar/websitetoolbar.php:33` | The code of extension/ezwt/modules/websitetoolbar/websitetoolbar.php, moved into a class (#207 stage 1). | – |

### extension/git_manager (5)

[All patterns of extension/git_manager](area-extension-git-manager.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\GitManager\GitManager\Backup` | class extends ModuleView | `extension/git_manager/classes/runnable/views/git_manager/backup.php:34` | The view git_manager/backup: the backups, how fresh they are, and the forms that make and remove them. | – |
| `Exponential\View\Extension\GitManager\GitManager\CommitDetails` | class extends ModuleView | `extension/git_manager/classes/runnable/views/git_manager/commit_details.php:84` | "git log -1 -p" output as the page shows it: the header fields, the message, and each changed file with its lines typed (add, del, hunk, meta, contex… | – |
| `Exponential\View\Extension\GitManager\GitManager\Dashboard` | class extends ModuleView | `extension/git_manager/classes/runnable/views/git_manager/dashboard.php:19` | The code of extension/git_manager/modules/git_manager/dashboard.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\GitManager\GitManager\Download` | class extends ModuleView | `extension/git_manager/classes/runnable/views/git_manager/download.php:30` | The view git_manager/download//: one archive of a backup. | – |
| `Exponential\View\Extension\GitManager\GitManager\Dump` | class extends ModuleView | `extension/git_manager/classes/runnable/views/git_manager/dump.php:23` | The code of extension/git_manager/modules/git_manager/dump.php, moved into a class (#207 stage 1). | – |

### extension/powercontent (1)

[All patterns of extension/powercontent](area-extension-powercontent.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Powercontent\Powercontent\Action` | class extends ModuleView | `extension/powercontent/classes/runnable/views/powercontent/action.php:10` | The code of extension/powercontent/modules/powercontent/action.php, moved into a class (#207 stage 1). | – |

### extension/sevenx_dse (2)

[All patterns of extension/sevenx_dse](area-extension-sevenx-dse.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\SevenxDse\Dse\Adminneo` | class extends ModuleView | `extension/sevenx_dse/classes/runnable/views/dse/adminneo.php:28` | The code of extension/sevenx_dse/modules/dse/adminneo.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\SevenxDse\Dse\Dashboard` | class extends ModuleView | `extension/sevenx_dse/classes/runnable/views/dse/dashboard.php:50` | The code of extension/sevenx_dse/modules/dse/dashboard.php, moved into a class (#207 stage 1). | – |

### extension/sevenx_themes_media (2)

[All patterns of extension/sevenx_themes_media](area-extension-sevenx-themes-media.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\SevenxThemesMedia\InfoCollection\Submit` | class extends ModuleView | `extension/sevenx_themes_media/classes/runnable/views/info-collection/submit.php:10` | The code of extension/sevenx_themes_media/modules/info-collection/submit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\SevenxThemesMedia\InfoCollection\Viewmodal` | class extends ModuleView | `extension/sevenx_themes_media/classes/runnable/views/info-collection/viewmodal.php:10` | The code of extension/sevenx_themes_media/modules/info-collection/viewmodal.php, moved into a class (#207 stage 1). | – |

### extension/xrowextract (15)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Xrowextract\Xrowextract\Archive` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/archive.php:19` | The code of extension/xrowextract/modules/xrowextract/archive.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Browse` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/browse.php:42` | The code of extension/xrowextract/modules/xrowextract/browse.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\BrowseFile` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/browse_file.php:30` | The code of extension/xrowextract/modules/xrowextract/browse_file.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Compare` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/compare.php:25` | The code of extension/xrowextract/modules/xrowextract/compare.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Csv` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/csv.php:95` | Read an export back the way a spreadsheet does (same separator and quoting) for the preview table: header, rows with a flag per cell, and what the pr… | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Destinations` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/destinations.php:19` | The code of extension/xrowextract/modules/xrowextract/destinations.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\History` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/history.php:20` | The code of extension/xrowextract/modules/xrowextract/history.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Import` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/import.php:21` | The code of extension/xrowextract/modules/xrowextract/import.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\JobDownload` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/job_download.php:22` | The code of extension/xrowextract/modules/xrowextract/job_download.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Jobs` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/jobs.php:19` | The code of extension/xrowextract/modules/xrowextract/jobs.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\JobStatus` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/job_status.php:19` | The code of extension/xrowextract/modules/xrowextract/job_status.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Package` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/package.php:24` | The code of extension/xrowextract/modules/xrowextract/package.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Schedules` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/schedules.php:21` | The code of extension/xrowextract/modules/xrowextract/schedules.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\SubtreeClasses` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/subtree_classes.php:13` | The code of extension/xrowextract/modules/xrowextract/subtree_classes.php: the classes below a node for the content tree's Advanced › Export › "Subtr… | – |
| `Exponential\View\Extension\Xrowextract\Xrowextract\UploadChunk` | class extends ModuleView | `extension/xrowextract/classes/runnable/views/xrowextract/upload_chunk.php:23` | The code of extension/xrowextract/modules/xrowextract/upload_chunk.php, moved into a class (#207 stage 1). | – |

### extension/xrowmetadata (2)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Extension\Xrowmetadata\Sitemaps\Index` | class extends ModuleView | `extension/xrowmetadata/classes/runnable/views/sitemaps/index.php:65` | The code of extension/xrowmetadata/modules/sitemaps/index.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Extension\Xrowmetadata\Sitemaps\Robots` | class extends ModuleView | `extension/xrowmetadata/classes/runnable/views/sitemaps/robots.php:10` | The code of extension/xrowmetadata/modules/sitemaps/robots.php, moved into a class (#207 stage 1). | – |

### kernel/private/classes/views (255)

[All patterns of kernel/private/classes/views](area-kernel-private-classes-views.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\View\Kernel\Audit\Alerts` | class extends ModuleView | `kernel/private/classes/views/audit/alerts.php:17` | The audit/alerts view (doc/eng-us/bc/6.0/audit.md, "Views", "Alerts (F5)"): the alerts that fired (system.audit.alert records: rule, group, count, window, t… | – |
| `Exponential\View\Kernel\Audit\Archives` | class extends ModuleView | `kernel/private/classes/views/audit/archives.php:17` | The audit/archives view (doc/eng-us/bc/6.0/audit.md, "Views", "Rotation, archives and retention"): per channel its live files (count, size, oldest), its arc… | – |
| `Exponential\View\Kernel\Audit\Charts` | class extends ModuleView | `kernel/private/classes/views/audit/charts.php:16` | The audit/charts view (doc/eng-us/bc/6.0/audit.md, "Views"): events per day per channel, refusals and failures per day, logins against failed logins per day… | – |
| `Exponential\View\Kernel\Audit\Console` | class extends ModuleView | `kernel/private/classes/views/audit/console.php:17` | The audit/console view (doc/eng-us/bc/6.0/audit.md, "Views"): the timeline of the audit records, newest first, with filters, search and paging, and the stat… | – |
| `Exponential\View\Kernel\Audit\Dashboard` | class extends ModuleView | `kernel/private/classes/views/audit/dashboard.php:22` | The audit/dashboard view (doc/eng-us/bc/6.0/audit.md, "The console", "Views"): the summary that binds the audit's views together, in cards that each link in… | – |
| `Exponential\View\Kernel\Audit\Event` | class extends ModuleView | `kernel/private/classes/views/audit/event.php:17` | The audit/event view (doc/eng-us/bc/6.0/audit.md, "Views"): one record in full: when, who, the request, object and target with links into the admin, before… | – |
| `Exponential\View\Kernel\Audit\Export` | class extends ModuleView | `kernel/private/classes/views/audit/export.php:28` | The audit/export view (doc/eng-us/bc/6.0/audit.md, "Views"): the records of the console's filter as a download, newest first: /(format)/csv (one row per rec… | – |
| `Exponential\View\Kernel\Audit\Recent` | class extends ModuleView | `kernel/private/classes/views/audit/recent.php:17` | The audit/recent view: the latest 100 audit events of every channel the user may read (or of one, /(channel)/access) with name, time, actor, object,… | – |
| `Exponential\View\Kernel\Audit\Settings` | class extends ModuleView | `kernel/private/classes/views/audit/settings.php:18` | The audit/settings view (doc/eng-us/bc/6.0/audit.md, "Views", "The settings reference"): the effective audit.ini of this siteaccess, every value with the fi… | – |
| `Exponential\View\Kernel\Class\Classlist` | class extends ModuleView | `kernel/private/classes/views/class/classlist.php:21` | The code of kernel/class/classlist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Class\Copy` | class extends ModuleView | `kernel/private/classes/views/class/copy.php:21` | The code of kernel/class/copy.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Class\Edit` | class extends ModuleView | `kernel/private/classes/views/class/edit.php:21` | The code of kernel/class/edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Class\Groupedit` | class extends ModuleView | `kernel/private/classes/views/class/groupedit.php:21` | The code of kernel/class/groupedit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Class\Grouplist` | class extends ModuleView | `kernel/private/classes/views/class/grouplist.php:21` | The code of kernel/class/grouplist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Class\Removeclass` | class extends ModuleView | `kernel/private/classes/views/class/removeclass.php:21` | The code of kernel/class/removeclass.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Class\Removegroup` | class extends ModuleView | `kernel/private/classes/views/class/removegroup.php:21` | The code of kernel/class/removegroup.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Class\Translation` | class extends ModuleView | `kernel/private/classes/views/class/translation.php:21` | The code of kernel/class/translation.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Class\View` | class extends ModuleView | `kernel/private/classes/views/class/view.php:21` | The code of kernel/class/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Collaboration\Action` | class extends ModuleView | `kernel/private/classes/views/collaboration/action.php:21` | The code of kernel/collaboration/action.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Collaboration\Group` | class extends ModuleView | `kernel/private/classes/views/collaboration/group.php:21` | The code of kernel/collaboration/group.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Collaboration\Item` | class extends ModuleView | `kernel/private/classes/views/collaboration/item.php:21` | The code of kernel/collaboration/item.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Collaboration\View` | class extends ModuleView | `kernel/private/classes/views/collaboration/view.php:21` | The code of kernel/collaboration/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Contentcheck\Brokenrelations` | class extends ModuleView | `kernel/private/classes/views/contentcheck/brokenrelations.php:18` | The contentcheck/brokenrelations view (doc/eng-us/features/6.0/content-checks.md, "Broken relations"): visible, published content below a node (contentcheck… | – |
| `Exponential\View\Kernel\Contentcheck\Fixfiles` | class extends ModuleView | `kernel/private/classes/views/contentcheck/fixfiles.php:23` | The contentcheck/fixfiles view (doc/eng-us/features/6.0/content-checks.md, "Resolving"): POST only. | – |
| `Exponential\View\Kernel\Contentcheck\Scope` | class extends ModuleView | `kernel/private/classes/views/contentcheck/scope.php:23` | The contentcheck/scope view (doc/eng-us/features/6.0/content-checks.md, "Where a check starts"): chooses the start location of a check with the shared node… | – |
| `Exponential\View\Kernel\Contentcheck\Storage` | class extends ModuleView | `kernel/private/classes/views/contentcheck/storage.php:22` | The contentcheck/storage view (doc/eng-us/features/6.0/content-checks.md, "Storage files"): the result of the last scan of var//storage, in three lists (GET… | – |
| `Exponential\View\Kernel\Contentcheck\Storageaction` | class extends ModuleView | `kernel/private/classes/views/contentcheck/storageaction.php:26` | The contentcheck/storageaction view (doc/eng-us/features/6.0/content-checks.md, "Storage files"): POST only, from the forms of contentcheck/storage. | – |
| `Exponential\View\Kernel\Contentcheck\Trash` | class extends ModuleView | `kernel/private/classes/views/contentcheck/trash.php:22` | The contentcheck/trash view (doc/eng-us/features/6.0/content-checks.md, "Moving to the trash"): POST only. | – |
| `Exponential\View\Kernel\Contentcheck\Unreferenced` | class extends ModuleView | `kernel/private/classes/views/contentcheck/unreferenced.php:16` | The contentcheck/unreferenced view (doc/eng-us/features/6.0/content-checks.md, "Unreferenced objects"): the published objects below a node (contentcheck/unr… | – |
| `Exponential\View\Kernel\Contentfinder\Action` | class extends ModuleView | `kernel/private/classes/views/contentfinder/action.php:21` | The contentfinder/action view (doc/eng-us/features/6.0/content-finder.md, "Actions"): POST only. | – |
| `Exponential\View\Kernel\Contentfinder\Search` | class extends ModuleView | `kernel/private/classes/views/contentfinder/search.php:16` | The contentfinder/search view (doc/eng-us/features/6.0/content-finder.md): one search field and a type selector, read from the query string (SearchText, Sea… | – |
| `Exponential\View\Kernel\Content\Action` | class extends ModuleView | `kernel/private/classes/views/content/action.php:21` | The code of kernel/content/action.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Advancedsearch` | class extends ModuleView | `kernel/private/classes/views/content/advancedsearch.php:51` | The code of kernel/content/advancedsearch.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Bookmark` | class extends ModuleView | `kernel/private/classes/views/content/bookmark.php:29` | My bookmarks: the bookmarks of the current user grouped by folder, with a folder (folder), an order (sort), a search (/(q)/) and paging (offset) kept i… | – |
| `Exponential\View\Kernel\Content\Browse` | class extends ModuleView | `kernel/private/classes/views/content/browse.php:21` | The code of kernel/content/browse.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Collectedinfo` | class extends ModuleView | `kernel/private/classes/views/content/collectedinfo.php:21` | The code of kernel/content/collectedinfo.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Collectinformation` | class extends ModuleView | `kernel/private/classes/views/content/collectinformation.php:21` | The code of kernel/content/collectinformation.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Copy` | class extends ModuleView | `kernel/private/classes/views/content/copy.php:171` | The code of kernel/content/copy.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Copysubtree` | class extends ModuleView | `kernel/private/classes/views/content/copysubtree.php:859` | The code of kernel/content/copysubtree.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Dashboard` | class extends ModuleView | `kernel/private/classes/views/content/dashboard.php:21` | The code of kernel/content/dashboard.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Download` | class extends ModuleView | `kernel/private/classes/views/content/download.php:21` | The code of kernel/content/download.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Draft` | class extends ModuleView | `kernel/private/classes/views/content/draft.php:27` | The code of kernel/content/draft.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Edit` | class extends ModuleView | `kernel/private/classes/views/content/edit.php:386` | The code of kernel/content/edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Hide` | class extends ModuleView | `kernel/private/classes/views/content/hide.php:21` | The code of kernel/content/hide.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\History` | class extends ModuleView | `kernel/private/classes/views/content/history.php:27` | The code of kernel/content/history.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Job` | class extends ModuleView | `kernel/private/classes/views/content/job.php:19` | The content/job/ view: the progress page of a content job (a large subtree remove or copy that runs in the background, see kernel/classes/contentjob/… | – |
| `Exponential\View\Kernel\Content\Jobs` | class extends ModuleView | `kernel/private/classes/views/content/jobs.php:19` | The content/jobs view: the list of content jobs (large subtree removes and copies running in the background) with who started them, the operation, so… | – |
| `Exponential\View\Kernel\Content\Keyword` | class extends ModuleView | `kernel/private/classes/views/content/keyword.php:21` | The code of kernel/content/keyword.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Move` | class extends ModuleView | `kernel/private/classes/views/content/move.php:21` | The code of kernel/content/move.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Multiedit` | class extends ModuleView | `kernel/private/classes/views/content/multiedit.php:10` | The code of kernel/content/multiedit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Newcontent` | class extends ModuleView | `kernel/private/classes/views/content/newcontent.php:21` | The code of kernel/content/newcontent.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Pdf` | class extends ModuleView | `kernel/private/classes/views/content/pdf.php:147` | The code of kernel/content/pdf.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Pendinglist` | class extends ModuleView | `kernel/private/classes/views/content/pendinglist.php:28` | The code of kernel/content/pendinglist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Queued` | class extends ModuleView | `kernel/private/classes/views/content/queued.php:24` | The code of kernel/content/queued.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Removeassignment` | class extends ModuleView | `kernel/private/classes/views/content/removeassignment.php:21` | The code of kernel/content/removeassignment.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Removeeditversion` | class extends ModuleView | `kernel/private/classes/views/content/removeeditversion.php:21` | The code of kernel/content/removeeditversion.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Removenode` | class extends ModuleView | `kernel/private/classes/views/content/removenode.php:21` | The code of kernel/content/removenode.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Removeobject` | class extends ModuleView | `kernel/private/classes/views/content/removeobject.php:21` | The code of kernel/content/removeobject.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Restore` | class extends ModuleView | `kernel/private/classes/views/content/restore.php:21` | The code of kernel/content/restore.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Reverserelatedlist` | class extends ModuleView | `kernel/private/classes/views/content/reverserelatedlist.php:21` | The code of kernel/content/reverserelatedlist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Search` | class extends ModuleView | `kernel/private/classes/views/content/search.php:51` | The code of kernel/content/search.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Subitemsexport` | class extends ModuleView | `kernel/private/classes/views/content/subitemsexport.php:18` | The view content/subitemsexport/: the admin subitems list of a node as a CSV download. | – |
| `Exponential\View\Kernel\Content\Tipafriend` | class extends ModuleView | `kernel/private/classes/views/content/tipafriend.php:21` | The code of kernel/content/tipafriend.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Translation` | class extends ModuleView | `kernel/private/classes/views/content/translation.php:21` | The code of kernel/content/translation.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Translations` | class extends ModuleView | `kernel/private/classes/views/content/translations.php:27` | The code of kernel/content/translations.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Trash` | class extends ModuleView | `kernel/private/classes/views/content/trash.php:21` | The code of kernel/content/trash.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Treemenu` | class extends ModuleView | `kernel/private/classes/views/content/treemenu.php:21` | The code of kernel/content/treemenu.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Upload` | class extends ModuleView | `kernel/private/classes/views/content/upload.php:21` | The code of kernel/content/upload.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Urlalias` | class extends ModuleView | `kernel/private/classes/views/content/urlalias.php:22` | The code of kernel/content/urlalias.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\UrlaliasGlobal` | class extends ModuleView | `kernel/private/classes/views/content/urlalias_global.php:21` | The code of kernel/content/urlalias_global.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\UrlaliasWildcard` | class extends ModuleView | `kernel/private/classes/views/content/urlalias_wildcard.php:21` | The code of kernel/content/urlalias_wildcard.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\Versionview` | class extends ModuleView | `kernel/private/classes/views/content/versionview.php:21` | The code of kernel/content/versionview.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Content\View` | class extends ModuleView | `kernel/private/classes/views/content/view.php:21` | The code of kernel/content/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Csp\Report` | class extends ModuleView | `kernel/private/classes/views/csp/report.php:29` | Takes a report of the Content Security Policy from a browser and writes its violations to var/log/csp.log (expContentSecurityPolicyReport). | – |
| `Exponential\View\Kernel\Error\View` | class extends ModuleView | `kernel/private/classes/views/error/view.php:21` | The code of kernel/error/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Exphelp\Asset` | class extends ModuleView | `kernel/private/classes/views/exphelp/asset.php:15` | exphelp/asset/ (doc/eng-us/features/6.0/help-system.md): an image below a help root. | – |
| `Exponential\View\Kernel\Exphelp\Contents` | class extends ModuleView | `kernel/private/classes/views/exphelp/contents.php:17` | exphelp/contents (doc/eng-us/features/6.0/help-system.md): every page of the help as one tree, all folders open, with a filter box (exphelp.js) that narrows… | – |
| `Exponential\View\Kernel\Exphelp\Context` | class extends ModuleView | `kernel/private/classes/views/exphelp/context.php:21` | exphelp/context/ and exphelp/context/manual/ (doc/eng-us/features/6.0/help-content.md, "Context help by help_id"): a help topic, or a manual's table of cont… | – |
| `Exponential\View\Kernel\Exphelp\Dashboard` | class extends ModuleView | `kernel/private/classes/views/exphelp/dashboard.php:17` | exphelp/dashboard (doc/eng-us/features/6.0/help-system.md): the help start page. | – |
| `Exponential\View\Kernel\Exphelp\Manual` | class extends ModuleView | `kernel/private/classes/views/exphelp/manual.php:18` | exphelp/manual/ (doc/eng-us/features/6.0/help-content.md): a manual of help maintained as content, its metadata and table of contents (chapters and topics t… | – |
| `Exponential\View\Kernel\Exphelp\Pdf` | class extends ModuleView | `kernel/private/classes/views/exphelp/pdf.php:18` | exphelp/pdf/ (doc/eng-us/features/6.0/help-system.md, "Print and PDF"): the help page as a PDF file to download. | – |
| `Exponential\View\Kernel\Exphelp\Report` | class extends ModuleView | `kernel/private/classes/views/exphelp/report.php:18` | exphelp/report (doc/eng-us/features/6.0/help-system.md, "Reporting an error"): which tracker, how to write a good report, a prefilled issue, the safe diagno… | – |
| `Exponential\View\Kernel\Exphelp\Search` | class extends ModuleView | `kernel/private/classes/views/exphelp/search.php:31` | exphelp/search (doc/eng-us/features/6.0/help-system.md, "Search"): the search and browse page of the help. | – |
| `Exponential\View\Kernel\Exphelp\Topic` | class extends ModuleView | `kernel/private/classes/views/exphelp/topic.php:21` | exphelp/topic/ (doc/eng-us/features/6.0/help-content.md): one help topic maintained as content, rendered. | – |
| `Exponential\View\Kernel\Exphelp\Viewer` | class extends ModuleView | `kernel/private/classes/views/exphelp/viewer.php:19` | exphelp/viewer/ (doc/eng-us/features/6.0/help-system.md): one help page, rendered. | – |
| `Exponential\View\Kernel\Ezinfo\About` | class extends ModuleView | `kernel/private/classes/views/ezinfo/about.php:267` | The code of kernel/ezinfo/about.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Ezinfo\Copyright` | class extends ModuleView | `kernel/private/classes/views/ezinfo/copyright.php:21` | The code of kernel/ezinfo/copyright.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Ezinfo\Isalive` | class extends ModuleView | `kernel/private/classes/views/ezinfo/isalive.php:21` | The code of kernel/ezinfo/isalive.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Infocollector\Collectionlist` | class extends ModuleView | `kernel/private/classes/views/infocollector/collectionlist.php:21` | The code of kernel/infocollector/collectionlist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Infocollector\Export` | class extends ModuleView | `kernel/private/classes/views/infocollector/export.php:20` | The view infocollector/export/: everything one object (a form, a poll, a feedback page) has collected, as a CSV download: the collection's ID, when i… | – |
| `Exponential\View\Kernel\Infocollector\Overview` | class extends ModuleView | `kernel/private/classes/views/infocollector/overview.php:28` | Setup › Collected information (infocollector/overview): the objects (forms, polls, feedback pages) that have collected information, with figures, a s… | – |
| `Exponential\View\Kernel\Infocollector\View` | class extends ModuleView | `kernel/private/classes/views/infocollector/view.php:21` | The code of kernel/infocollector/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Layout\Set` | class extends ModuleView | `kernel/private/classes/views/layout/set.php:21` | The code of kernel/layout/set.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Mailpreferences\Admin` | class extends Page | `kernel/private/classes/views/mailpreferences/admin.php:27` | The administrator's pages of the e-mail preferences (policy mailpreferences/administrate): | – |
| `Exponential\View\Kernel\Mailpreferences\Confirm` | class extends Page | `kernel/private/classes/views/mailpreferences/confirm.php:20` | The link of a double opt-in mail (a category, or a new e-mail address). | – |
| `Exponential\View\Kernel\Mailpreferences\Export` | class extends Page | `kernel/private/classes/views/mailpreferences/export.php:20` | "Download my e-mail data": everything stored about a person's e-mail preferences, as JSON or CSV (the categories and their state, pending confirmatio… | – |
| `Exponential\View\Kernel\Mailpreferences\Manage` | class extends Page | `kernel/private/classes/views/mailpreferences/manage.php:19` | The preference page by a personal link (the "Manage" link of every optional e-mail, or "send me a link"): the same page as mailpreferences/settings,… | – |
| `Exponential\View\Kernel\Mailpreferences\Page` | abstract class extends ModuleView | `kernel/private/classes/views/mailpreferences/page.php:19` | What the views of the mailpreferences module share: the variables of the including function, rendering a page of design:mailpreferences/, the answer… | – |
| `Exponential\View\Kernel\Mailpreferences\Request` | class extends Page | `kernel/private/classes/views/mailpreferences/request.php:20` | "Send me a link": a person without an account, or not signed in, types an address and gets a personal link to the preference page. | – |
| `Exponential\View\Kernel\Mailpreferences\Settings` | class extends Page | `kernel/private/classes/views/mailpreferences/settings.php:20` | The e-mail preferences of the signed-in user: the main switch, the categories and their frequencies, essential mail (not switchable), the consent his… | – |
| `Exponential\View\Kernel\Mailpreferences\Unsubscribe` | class extends Page | `kernel/private/classes/views/mailpreferences/unsubscribe.php:25` | The unsubscribe link of an optional e-mail. | – |
| `Exponential\View\Kernel\Notification\Addtonotification` | class extends ModuleView | `kernel/private/classes/views/notification/addtonotification.php:21` | The code of kernel/notification/addtonotification.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Notification\Job` | class extends ModuleView | `kernel/private/classes/views/notification/job.php:17` | The status page's "Run now" back end (expNotificationJob), answering JSON: POST notification/job with Action=start (and DryRun=1) -› { "id": "..." }… | – |
| `Exponential\View\Kernel\Notification\Runfilter` | class extends ModuleView | `kernel/private/classes/views/notification/runfilter.php:21` | The code of kernel/notification/runfilter.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Notification\Settings` | class extends ModuleView | `kernel/private/classes/views/notification/settings.php:21` | The code of kernel/notification/settings.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Notification\Status` | class extends ModuleView | `kernel/private/classes/views/notification/status.php:13` | – | – |
| `Exponential\View\Kernel\Package\Compare` | class extends ModuleView | `kernel/private/classes/views/package/compare.php:21` | The code of kernel/package/compare.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Package\Create` | class extends ModuleView | `kernel/private/classes/views/package/create.php:21` | The code of kernel/package/create.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Package\Export` | class extends ModuleView | `kernel/private/classes/views/package/export.php:27` | package/export/[/]: the package as an .ezpkg archive (gzip compressed tar), built in the user's own export directory, sent in pieces by eZPackageDown… | – |
| `Exponential\View\Kernel\Package\Install` | class extends ModuleView | `kernel/private/classes/views/package/install.php:21` | The code of kernel/package/install.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Package\ListView` | class extends ModuleView | `kernel/private/classes/views/package/list.php:32` | package/list[/][/(search)/..][/(type)/..][/(state)/..][/(sort)/..][/(dir)/..][/offset/N] | – |
| `Exponential\View\Kernel\Package\Uninstall` | class extends ModuleView | `kernel/private/classes/views/package/uninstall.php:21` | The code of kernel/package/uninstall.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Package\Upload` | class extends ModuleView | `kernel/private/classes/views/package/upload.php:31` | package/upload: imports an uploaded package archive into its repository (the one its vendor names, "local" without one). | – |
| `Exponential\View\Kernel\Package\View` | class extends ModuleView | `kernel/private/classes/views/package/view.php:21` | The code of kernel/package/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Package\Viewfile` | class extends ModuleView | `kernel/private/classes/views/package/viewfile.php:21` | The code of kernel/package/viewfile.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Pdf\Edit` | class extends ModuleView | `kernel/private/classes/views/pdf/edit.php:55` | The code of kernel/pdf/edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Pdf\ListView` | class extends ModuleView | `kernel/private/classes/views/pdf/list.php:25` | The code of kernel/pdf/list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Role\Assign` | class extends ModuleView | `kernel/private/classes/views/role/assign.php:21` | The code of kernel/role/assign.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Role\Copy` | class extends ModuleView | `kernel/private/classes/views/role/copy.php:21` | The code of kernel/role/copy.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Role\Edit` | class extends ModuleView | `kernel/private/classes/views/role/edit.php:21` | The code of kernel/role/edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Role\ListView` | class extends ModuleView | `kernel/private/classes/views/role/list.php:21` | The code of kernel/role/list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Role\Policyedit` | class extends ModuleView | `kernel/private/classes/views/role/policyedit.php:70` | Applies the POST submitted limitations as found in the dropdowns | – |
| `Exponential\View\Kernel\Role\View` | class extends ModuleView | `kernel/private/classes/views/role/view.php:21` | The code of kernel/role/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Rss\EditExport` | class extends ModuleView | `kernel/private/classes/views/rss/edit_export.php:21` | The code of kernel/rss/edit_export.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Rss\EditImport` | class extends ModuleView | `kernel/private/classes/views/rss/edit_import.php:114` | The code of kernel/rss/edit_import.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Rss\Feed` | class extends ModuleView | `kernel/private/classes/views/rss/feed.php:21` | The code of kernel/rss/feed.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Rss\ListView` | class extends ModuleView | `kernel/private/classes/views/rss/list.php:21` | The code of kernel/rss/list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Search\Stats` | class extends ModuleView | `kernel/private/classes/views/search/stats.php:22` | The code of kernel/search/stats.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Section\Assign` | class extends ModuleView | `kernel/private/classes/views/section/assign.php:21` | The code of kernel/section/assign.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Section\Edit` | class extends ModuleView | `kernel/private/classes/views/section/edit.php:21` | The code of kernel/section/edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Section\ListView` | class extends ModuleView | `kernel/private/classes/views/section/list.php:22` | The code of kernel/section/list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Section\View` | class extends ModuleView | `kernel/private/classes/views/section/view.php:21` | The code of kernel/section/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Settings\Edit` | class extends ModuleView | `kernel/private/classes/views/settings/edit.php:57` | The code of kernel/settings/edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Settings\View` | class extends ModuleView | `kernel/private/classes/views/settings/view.php:26` | The code of kernel/settings/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Cache` | class extends ModuleView | `kernel/private/classes/views/setup/cache.php:32` | Setup › Caches. | – |
| `Exponential\View\Kernel\Setup\Cachetoolbar` | class extends ModuleView | `kernel/private/classes/views/setup/cachetoolbar.php:21` | The code of kernel/setup/cachetoolbar.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Contentextension` | class extends ModuleView | `kernel/private/classes/views/setup/contentextension.php:10` | The code of kernel/setup/contentextension.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Cronjobs` | class extends ModuleView | `kernel/private/classes/views/setup/cronjobs.php:28` | The code of kernel/setup/cronjobs.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Cronjobsstream` | class extends ModuleView | `kernel/private/classes/views/setup/cronjobsstream.php:48` | The code of kernel/setup/cronjobsstream.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Datatype` | class extends ModuleView | `kernel/private/classes/views/setup/datatype.php:10` | The code of kernel/setup/datatype.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Designextension` | class extends ModuleView | `kernel/private/classes/views/setup/designextension.php:10` | The code of kernel/setup/designextension.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Extensions` | class extends ModuleView | `kernel/private/classes/views/setup/extensions.php:77` | The code of kernel/setup/extensions.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Ezsetup` | class extends ModuleView | `kernel/private/classes/views/setup/ezsetup.php:21` | The code of kernel/setup/ezsetup.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Handlerextension` | class extends ModuleView | `kernel/private/classes/views/setup/handlerextension.php:10` | The code of kernel/setup/handlerextension.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Info` | class extends ModuleView | `kernel/private/classes/views/setup/info.php:21` | The code of kernel/setup/info.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Kerneloverride` | class extends ModuleView | `kernel/private/classes/views/setup/kerneloverride.php:10` | The code of kernel/setup/kerneloverride.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Languages` | class extends ModuleView | `kernel/private/classes/views/setup/languages.php:17` | Setup › Interface languages (setup/languages): every locale with the translation the administration shows in it, the chain of translations it falls b… | – |
| `Exponential\View\Kernel\Setup\Logs` | class extends ModuleView | `kernel/private/classes/views/setup/logs.php:20` | Setup › Log files (setup/logs; doc/eng-us/features/6.0/log-viewer.md): the page of the log viewer. | – |
| `Exponential\View\Kernel\Setup\Logsaction` | class extends ModuleView | `kernel/private/classes/views/setup/logsaction.php:21` | The actions of the log viewer (setup/logsaction; doc/eng-us/features/6.0/log-viewer.md, "Actions"). | – |
| `Exponential\View\Kernel\Setup\Logsdigest` | class extends ModuleView | `kernel/private/classes/views/setup/logsdigest.php:22` | Setup › Log files › Error digest (setup/logsdigest; doc/eng-us/features/6.0/log-viewer.md, "Error digest"): the distinct errors and warnings of the last hou… | – |
| `Exponential\View\Kernel\Setup\Logsfeed` | class extends ModuleView | `kernel/private/classes/views/setup/logsfeed.php:25` | The feed of the log viewer (setup/logsfeed; doc/eng-us/features/6.0/log-viewer.md, "The feed"): JSON for the page's script, one short request each, never he… | – |
| `Exponential\View\Kernel\Setup\Logshealth` | class extends ModuleView | `kernel/private/classes/views/setup/logshealth.php:17` | Setup › Log files › Health (setup/logshealth; doc/eng-us/features/6.0/log-viewer.md, "Health"): the disk the logs and their archives use against [HealthSett… | – |
| `Exponential\View\Kernel\Setup\Logsmanage` | class extends ModuleView | `kernel/private/classes/views/setup/logsmanage.php:24` | Setup › Log files › Manage (setup/logsmanage; doc/eng-us/features/6.0/log-viewer.md, "Managing logs"): every log with its size, estimated lines, last write,… | – |
| `Exponential\View\Kernel\Setup\Maintenance` | class extends ModuleView | `kernel/private/classes/views/setup/maintenance.php:19` | The code of kernel/setup/maintenance.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Menuconfig` | class extends ModuleView | `kernel/private/classes/views/setup/menuconfig.php:21` | The code of kernel/setup/menuconfig.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Moduleextension` | class extends ModuleView | `kernel/private/classes/views/setup/moduleextension.php:10` | The code of kernel/setup/moduleextension.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Modulewizard` | class extends ModuleView | `kernel/private/classes/views/setup/modulewizard.php:10` | The code of kernel/setup/modulewizard.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Preload` | class extends ModuleView | `kernel/private/classes/views/setup/preload.php:36` | Setup › Preload: what is running, the form that starts a run or shows what it would do (the dry run), and the last runs. | – |
| `Exponential\View\Kernel\Setup\Preloadjob` | class extends ModuleView | `kernel/private/classes/views/setup/preloadjob.php:37` | Setup › Preload's back end, answering JSON: POST Action=start, SiteAccess, MaxPages, MaxDepth[, Images] -› { "id": "..." }, 409 while a run is going… | – |
| `Exponential\View\Kernel\Setup\Preloadstream` | class extends ModuleView | `kernel/private/classes/views/setup/preloadstream.php:37` | The older progress stream of Setup › Preload, kept for anything that opens it. | – |
| `Exponential\View\Kernel\Setup\Rad` | class extends ModuleView | `kernel/private/classes/views/setup/rad.php:23` | The code of kernel/setup/rad.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Radsurvey` | class extends ModuleView | `kernel/private/classes/views/setup/radsurvey.php:10` | The code of kernel/setup/radsurvey.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Session` | class extends ModuleView | `kernel/private/classes/views/setup/session.php:58` | Setup › Sessions (setup/session). | – |
| `Exponential\View\Kernel\Setup\Settingsextension` | class extends ModuleView | `kernel/private/classes/views/setup/settingsextension.php:10` | The code of kernel/setup/settingsextension.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Settingstoolbar` | class extends ModuleView | `kernel/private/classes/views/setup/settingstoolbar.php:21` | The code of kernel/setup/settingstoolbar.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Setupmenu` | class extends ModuleView | `kernel/private/classes/views/setup/setupmenu.php:21` | The code of kernel/setup/setupmenu.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Sitedata` | class extends ModuleView | `kernel/private/classes/views/setup/sitedata.php:19` | Setup › Site data (setup/sitedata): the rows of ezsite_data (eZSiteData), such as Exponential-version, read only, by name, with a filter (a whole nam… | – |
| `Exponential\View\Kernel\Setup\Staticcachestream` | class extends ModuleView | `kernel/private/classes/views/setup/staticcachestream.php:28` | The code of kernel/setup/staticcachestream.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Systemupgrade` | class extends ModuleView | `kernel/private/classes/views/setup/systemupgrade.php:32` | The upgrade check (setup/systemupgrade): the file consistency check and the database consistency check. | – |
| `Exponential\View\Kernel\Setup\Templatecreate` | class extends ModuleView | `kernel/private/classes/views/setup/templatecreate.php:21` | The code of kernel/setup/templatecreate.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Templateedit` | class extends ModuleView | `kernel/private/classes/views/setup/templateedit.php:21` | The code of kernel/setup/templateedit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Templatelist` | class extends ModuleView | `kernel/private/classes/views/setup/templatelist.php:21` | The code of kernel/setup/templatelist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Templateoperator` | class extends ModuleView | `kernel/private/classes/views/setup/templateoperator.php:10` | The code of kernel/setup/templateoperator.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Templateview` | class extends ModuleView | `kernel/private/classes/views/setup/templateview.php:21` | The code of kernel/setup/templateview.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Toolbar` | class extends ModuleView | `kernel/private/classes/views/setup/toolbar.php:21` | The code of kernel/setup/toolbar.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Toolbarlist` | class extends ModuleView | `kernel/private/classes/views/setup/toolbarlist.php:21` | The code of kernel/setup/toolbarlist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Setup\Workflowevent` | class extends ModuleView | `kernel/private/classes/views/setup/workflowevent.php:10` | The code of kernel/setup/workflowevent.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Add` | class extends ModuleView | `kernel/private/classes/views/shop/add.php:21` | The code of kernel/shop/add.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Archivelist` | class extends ModuleView | `kernel/private/classes/views/shop/archivelist.php:21` | The code of kernel/shop/archivelist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Archiveorder` | class extends ModuleView | `kernel/private/classes/views/shop/archiveorder.php:21` | The code of kernel/shop/archiveorder.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Basket` | class extends ModuleView | `kernel/private/classes/views/shop/basket.php:21` | The code of kernel/shop/basket.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Checkout` | class extends ModuleView | `kernel/private/classes/views/shop/checkout.php:21` | The code of kernel/shop/checkout.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Confirmorder` | class extends ModuleView | `kernel/private/classes/views/shop/confirmorder.php:21` | The code of kernel/shop/confirmorder.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Currencylist` | class extends ModuleView | `kernel/private/classes/views/shop/currencylist.php:21` | The code of kernel/shop/currencylist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Customerlist` | class extends ModuleView | `kernel/private/classes/views/shop/customerlist.php:21` | The code of kernel/shop/customerlist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Customerorderview` | class extends ModuleView | `kernel/private/classes/views/shop/customerorderview.php:22` | The code of kernel/shop/customerorderview.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Dashboard` | class extends ModuleView | `kernel/private/classes/views/shop/dashboard.php:19` | The code of kernel/shop/dashboard.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Discountgroup` | class extends ModuleView | `kernel/private/classes/views/shop/discountgroup.php:21` | The code of kernel/shop/discountgroup.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Discountgroupedit` | class extends ModuleView | `kernel/private/classes/views/shop/discountgroupedit.php:21` | The code of kernel/shop/discountgroupedit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Discountgroupmembershipview` | class extends ModuleView | `kernel/private/classes/views/shop/discountgroupmembershipview.php:21` | The code of kernel/shop/discountgroupmembershipview.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Discountruleedit` | class extends ModuleView | `kernel/private/classes/views/shop/discountruleedit.php:21` | The code of kernel/shop/discountruleedit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Editcurrency` | class extends ModuleView | `kernel/private/classes/views/shop/editcurrency.php:56` | Validates currency data: - checks that custom_rate_value & rate_factor are provided and are floats. | – |
| `Exponential\View\Kernel\Shop\Editvatrule` | class extends ModuleView | `kernel/private/classes/views/shop/editvatrule.php:122` | Check entered data. | – |
| `Exponential\View\Kernel\Shop\Orderlist` | class extends ModuleView | `kernel/private/classes/views/shop/orderlist.php:21` | The code of kernel/shop/orderlist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Orderreceipt` | class extends ModuleView | `kernel/private/classes/views/shop/orderreceipt.php:26` | The code of kernel/shop/orderreceipt.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Orderreciept` | class extends ModuleView | `kernel/private/classes/views/shop/orderreciept.php:22` | The code of kernel/shop/orderreciept.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Orderstatistics` | class extends ModuleView | `kernel/private/classes/views/shop/orderstatistics.php:21` | The code of kernel/shop/orderstatistics.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Orderview` | class extends ModuleView | `kernel/private/classes/views/shop/orderview.php:21` | The code of kernel/shop/orderview.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Preferredcurrency` | class extends ModuleView | `kernel/private/classes/views/shop/preferredcurrency.php:21` | The code of kernel/shop/preferredcurrency.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Productcategories` | class extends ModuleView | `kernel/private/classes/views/shop/productcategories.php:90` | Generate a unique category name. | – |
| `Exponential\View\Kernel\Shop\Productsoverview` | class extends ModuleView | `kernel/private/classes/views/shop/productsoverview.php:21` | The code of kernel/shop/productsoverview.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Register` | class extends ModuleView | `kernel/private/classes/views/shop/register.php:21` | The code of kernel/shop/register.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Removeorder` | class extends ModuleView | `kernel/private/classes/views/shop/removeorder.php:21` | The code of kernel/shop/removeorder.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Setpreferredcurrency` | class extends ModuleView | `kernel/private/classes/views/shop/setpreferredcurrency.php:21` | The code of kernel/shop/setpreferredcurrency.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Setstatus` | class extends ModuleView | `kernel/private/classes/views/shop/setstatus.php:21` | The code of kernel/shop/setstatus.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Setusercountry` | class extends ModuleView | `kernel/private/classes/views/shop/setusercountry.php:21` | The code of kernel/shop/setusercountry.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Status` | class extends ModuleView | `kernel/private/classes/views/shop/status.php:21` | The code of kernel/shop/status.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Unarchiveorder` | class extends ModuleView | `kernel/private/classes/views/shop/unarchiveorder.php:21` | The code of kernel/shop/unarchiveorder.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Updatebasket` | class extends ModuleView | `kernel/private/classes/views/shop/updatebasket.php:21` | The code of kernel/shop/updatebasket.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Userregister` | class extends ModuleView | `kernel/private/classes/views/shop/userregister.php:21` | The code of kernel/shop/userregister.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Shop\Vatrules` | class extends ModuleView | `kernel/private/classes/views/shop/vatrules.php:161` | Auxiliary function used to sort VAT rules. | – |
| `Exponential\View\Kernel\Shop\Vattype` | class extends ModuleView | `kernel/private/classes/views/shop/vattype.php:142` | Determine dependent VAT rules and products for the given VAT types. | – |
| `Exponential\View\Kernel\Shop\Wishlist` | class extends ModuleView | `kernel/private/classes/views/shop/wishlist.php:21` | The code of kernel/shop/wishlist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\State\Assign` | class extends ModuleView | `kernel/private/classes/views/state/assign.php:21` | The code of kernel/state/assign.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\State\Edit` | class extends ModuleView | `kernel/private/classes/views/state/edit.php:21` | The code of kernel/state/edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\State\Group` | class extends ModuleView | `kernel/private/classes/views/state/group.php:21` | The code of kernel/state/group.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\State\GroupEdit` | class extends ModuleView | `kernel/private/classes/views/state/group_edit.php:21` | The code of kernel/state/group_edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\State\Groups` | class extends ModuleView | `kernel/private/classes/views/state/groups.php:21` | The code of kernel/state/groups.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\State\View` | class extends ModuleView | `kernel/private/classes/views/state/view.php:21` | The code of kernel/state/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Trigger\ListView` | class extends ModuleView | `kernel/private/classes/views/trigger/list.php:37` | The code of kernel/trigger/list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Uiswitch\Set` | class extends ModuleView | `kernel/private/classes/views/uiswitch/set.php:20` | The uiswitch/set view (doc/eng-us/features/6.0/ui-switch.md): changes one of the signed-in user's interface choices on the current siteaccess and goes back… | – |
| `Exponential\View\Kernel\Uiswitch\State` | class extends ModuleView | `kernel/private/classes/views/uiswitch/state.php:14` | The uiswitch/state view: JSON of what the signed-in user may choose on the current siteaccess, for scripts (expUISwitch::toolbar()). | – |
| `Exponential\View\Kernel\Url\Edit` | class extends ModuleView | `kernel/private/classes/views/url/edit.php:21` | The code of kernel/url/edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Url\ListView` | class extends ModuleView | `kernel/private/classes/views/url/list.php:22` | The code of kernel/url/list.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Url\View` | class extends ModuleView | `kernel/private/classes/views/url/view.php:21` | The code of kernel/url/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\User\Activate` | class extends ModuleView | `kernel/private/classes/views/user/activate.php:21` | The code of kernel/user/activate.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\User\Deactivate` | class extends ModuleView | `kernel/private/classes/views/user/deactivate.php:30` | user/deactivate: "Deactivate and move" and "Reactivate and move back" for the accounts sent by POST (see expUserDeactivation): UserIDArray[] from Che… | – |
| `Exponential\View\Kernel\User\Edit` | class extends ModuleView | `kernel/private/classes/views/user/edit.php:21` | The code of kernel/user/edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\User\Forgotpassword` | class extends ModuleView | `kernel/private/classes/views/user/forgotpassword.php:21` | The code of kernel/user/forgotpassword.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\User\Hygiene` | class extends ModuleView | `kernel/private/classes/views/user/hygiene.php:34` | user/hygiene, "Check user accounts": the user accounts of the installation, or those below a user group, narrowed by one filter and a search (GET fil… | – |
| `Exponential\View\Kernel\User\Login` | class extends ModuleView | `kernel/private/classes/views/user/login.php:21` | The code of kernel/user/login.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\User\Logout` | class extends ModuleView | `kernel/private/classes/views/user/logout.php:21` | The code of kernel/user/logout.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\User\Megamenu` | class extends ModuleView | `kernel/private/classes/views/user/megamenu.php:40` | The groups and links of a top tab's menu as an HTML fragment, without the page layout, for admin4's mega menu (menu.ini [TopAdminMenu] MegaMenuTabs[]… | – |
| `Exponential\View\Kernel\User\Password` | class extends ModuleView | `kernel/private/classes/views/user/password.php:24` | The code of kernel/user/password.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\User\Preferences` | class extends ModuleView | `kernel/private/classes/views/user/preferences.php:21` | The code of kernel/user/preferences.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\User\Register` | class extends ModuleView | `kernel/private/classes/views/user/register.php:195` | The code of kernel/user/register.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\User\Registration` | class extends ModuleView | `kernel/private/classes/views/user/registration.php:30` | user/registration/: the page the signed links of the administrator's "New user registered" mail open (expRegistrationAdminAction, doc/eng-us/features/6.0/re… | – |
| `Exponential\View\Kernel\User\Setting` | class extends ModuleView | `kernel/private/classes/views/user/setting.php:21` | The code of kernel/user/setting.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\User\Success` | class extends ModuleView | `kernel/private/classes/views/user/success.php:21` | The code of kernel/user/success.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\User\Unactivated` | class extends ModuleView | `kernel/private/classes/views/user/unactivated.php:31` | user/unactivated: the users who registered and never activated their account (expUnactivatedUsers). | – |
| `Exponential\View\Kernel\Visual\Menuconfig` | class extends ModuleView | `kernel/private/classes/views/visual/menuconfig.php:33` | Design › Menus (classic): the menu.ini [SelectedMenu] settings of a siteaccess, which only the page layouts of the classic designs read. | – |
| `Exponential\View\Kernel\Visual\Templatecreate` | class extends ModuleView | `kernel/private/classes/views/visual/templatecreate.php:337` | The code of kernel/visual/templatecreate.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Visual\Templateedit` | class extends ModuleView | `kernel/private/classes/views/visual/templateedit.php:21` | The code of kernel/visual/templateedit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Visual\Templatelist` | class extends ModuleView | `kernel/private/classes/views/visual/templatelist.php:21` | The code of kernel/visual/templatelist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Visual\Templateview` | class extends ModuleView | `kernel/private/classes/views/visual/templateview.php:21` | The code of kernel/visual/templateview.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Visual\Toolbar` | class extends ModuleView | `kernel/private/classes/views/visual/toolbar.php:39` | The code of kernel/visual/toolbar.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Visual\Toolbarlist` | class extends ModuleView | `kernel/private/classes/views/visual/toolbarlist.php:21` | The code of kernel/visual/toolbarlist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Workflow\Edit` | class extends ModuleView | `kernel/private/classes/views/workflow/edit.php:21` | The code of kernel/workflow/edit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Workflow\Groupedit` | class extends ModuleView | `kernel/private/classes/views/workflow/groupedit.php:21` | The code of kernel/workflow/groupedit.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Workflow\Grouplist` | class extends ModuleView | `kernel/private/classes/views/workflow/grouplist.php:73` | The code of kernel/workflow/grouplist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Workflow\Process` | class extends ModuleView | `kernel/private/classes/views/workflow/process.php:21` | The code of kernel/workflow/process.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Workflow\Processlist` | class extends ModuleView | `kernel/private/classes/views/workflow/processlist.php:22` | The code of kernel/workflow/processlist.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Workflow\Run` | class extends ModuleView | `kernel/private/classes/views/workflow/run.php:21` | The code of kernel/workflow/run.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Workflow\View` | class extends ModuleView | `kernel/private/classes/views/workflow/view.php:21` | The code of kernel/workflow/view.php, moved into a class (#207 stage 1). | – |
| `Exponential\View\Kernel\Workflow\Workflowlist` | class extends ModuleView | `kernel/private/classes/views/workflow/workflowlist.php:56` | Removes a workflow and everything that belongs to it. | – |

## Module views (491)

### extension/AdminAid (6)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/aid/user_switch` | `user_switch.php` | user_switch | `extension/AdminAid/modules/aid/module.php:18` |
| `/aid/object_list` | `object_list.php` | object_view | `extension/AdminAid/modules/aid/module.php:19` |
| `/aid/object_view` | `object_view.php` | object_view | `extension/AdminAid/modules/aid/module.php:20` |
| `/aid/class_translation` | `class_translation.php` | class_translate | `extension/AdminAid/modules/aid/module.php:21` |
| `/aid/class_attribute_select` | `class_attribute_select.php` | class_translate | `extension/AdminAid/modules/aid/module.php:22` |
| `/aid/search` | `search.php` | search | `extension/AdminAid/modules/aid/module.php:23` |

### extension/bccie (5)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/bccie/overview` | `overview.php` | read | `extension/bccie/modules/bccie/module.php:27` |
| `/bccie/export` | `export.php` | read | `extension/bccie/modules/bccie/module.php:40` |
| `/bccie/doexport` | `doexport.php` | read | `extension/bccie/modules/bccie/module.php:49` |
| `/bccie/download` | `download.php` | read | `extension/bccie/modules/bccie/module.php:59` |
| `/bccie/job` | `job.php` | read | `extension/bccie/modules/bccie/module.php:69` |

### extension/bcgooglesitemaps (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/googlesitemapdynamic/sitemap` | `sitemap.php` | sitemap | `extension/bcgooglesitemaps/modules/googlesitemapdynamic/module.php:15` |

### extension/cjw_newsletter (57)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/newsletter/index` | `index.php` | index | `extension/cjw_newsletter/modules/newsletter/module.php:17` |
| `/newsletter/job` | `job.php` | index | `extension/cjw_newsletter/modules/newsletter/module.php:23` |
| `/newsletter/settings` | `settings.php` | settings | `extension/cjw_newsletter/modules/newsletter/module.php:29` |
| `/newsletter/mailbox_item_list` | `mailbox_item_list.php` | mailbox_item_list | `extension/cjw_newsletter/modules/newsletter/module.php:35` |
| `/newsletter/mailbox_list` | `mailbox_list.php` | mailbox_list | `extension/cjw_newsletter/modules/newsletter/module.php:42` |
| `/newsletter/mailbox_edit` | `mailbox_edit.php` | mailbox_edit | `extension/cjw_newsletter/modules/newsletter/module.php:49` |
| `/newsletter/mailbox_item_view` | `mailbox_item_view.php` | mailbox_item_view | `extension/cjw_newsletter/modules/newsletter/module.php:56` |
| `/newsletter/blacklist_item_list` | `blacklist_item_list.php` | blacklist_item | `extension/cjw_newsletter/modules/newsletter/module.php:63` |
| `/newsletter/blacklist_item_add` | `blacklist_item_add.php` | blacklist_item | `extension/cjw_newsletter/modules/newsletter/module.php:70` |
| `/newsletter/blacklist_item_remove` | `blacklist_item_remove.php` | blacklist_item | `extension/cjw_newsletter/modules/newsletter/module.php:77` |
| `/newsletter/import_list` | `import_list.php` | import_list | `extension/cjw_newsletter/modules/newsletter/module.php:84` |
| `/newsletter/import_view` | `import_view.php` | import_view | `extension/cjw_newsletter/modules/newsletter/module.php:91` |
| `/newsletter/user_list` | `user_list.php` | user_list | `extension/cjw_newsletter/modules/newsletter/module.php:98` |
| `/newsletter/user_view` | `user_view.php` | user_view | `extension/cjw_newsletter/modules/newsletter/module.php:105` |
| `/newsletter/user_remove` | `user_remove.php` | user_remove | `extension/cjw_newsletter/modules/newsletter/module.php:111` |
| `/newsletter/user_edit` | `user_edit.php` | user_edit | `extension/cjw_newsletter/modules/newsletter/module.php:122` |
| `/newsletter/user_create` | `user_create.php` | user_create, user_edit | `extension/cjw_newsletter/modules/newsletter/module.php:135` |
| `/newsletter/subscription_list` | `subscription_list.php` | subscription_list | `extension/cjw_newsletter/modules/newsletter/module.php:148` |
| `/newsletter/subscription_view` | `subscription_view.php` | subscription_view | `extension/cjw_newsletter/modules/newsletter/module.php:154` |
| `/newsletter/subscription_list_csvimport` | `subscription_list_csvimport.php` | subscription_list_csvimport | `extension/cjw_newsletter/modules/newsletter/module.php:160` |
| `/newsletter/subscription_list_csvexport` | `subscription_list_csvexport.php` | subscription_list_csvexport | `extension/cjw_newsletter/modules/newsletter/module.php:166` |
| `/newsletter/subscribe` | `subscribe.php` | subscribe | `extension/cjw_newsletter/modules/newsletter/module.php:173` |
| `/newsletter/subscribe_infomail` | `subscribe_infomail.php` | subscribe | `extension/cjw_newsletter/modules/newsletter/module.php:185` |
| `/newsletter/configure` | `configure.php` | configure | `extension/cjw_newsletter/modules/newsletter/module.php:199` |
| `/newsletter/unsubscribe` | `unsubscribe.php` | unsubscribe | `extension/cjw_newsletter/modules/newsletter/module.php:211` |
| `/newsletter/preview` | `preview.php` | preview | `extension/cjw_newsletter/modules/newsletter/module.php:224` |
| `/newsletter/preview_archive` | `preview_archive.php` | preview | `extension/cjw_newsletter/modules/newsletter/module.php:230` |
| `/newsletter/send` | `send.php` | send | `extension/cjw_newsletter/modules/newsletter/module.php:236` |
| `/newsletter/send_abort` | `send_abort.php` | send | `extension/cjw_newsletter/modules/newsletter/module.php:252` |
| `/newsletter/archive` | `archive.php` | archive | `extension/cjw_newsletter/modules/newsletter/module.php:258` |
| `/newsletter/test_group_list` | `test_group_list.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:267` |
| `/newsletter/test_group_edit` | `test_group_edit.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:274` |
| `/newsletter/mailin_address_list` | `mailin_address_list.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:281` |
| `/newsletter/mailin_address_edit` | `mailin_address_edit.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:288` |
| `/newsletter/throttle` | `throttle.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:295` |
| `/newsletter/suppression_import` | `suppression_import.php` | deliverability | `extension/cjw_newsletter/modules/newsletter/module.php:302` |
| `/newsletter/schedule_list` | `schedule_list.php` | editorial | `extension/cjw_newsletter/modules/newsletter/module.php:313` |
| `/newsletter/schedule_edit` | `schedule_edit.php` | editorial | `extension/cjw_newsletter/modules/newsletter/module.php:320` |
| `/newsletter/article_pool_list` | `article_pool_list.php` | editorial | `extension/cjw_newsletter/modules/newsletter/module.php:327` |
| `/newsletter/article_pool_edit` | `article_pool_edit.php` | editorial | `extension/cjw_newsletter/modules/newsletter/module.php:334` |
| `/newsletter/article_pool` | `article_pool.php` | editorial | `extension/cjw_newsletter/modules/newsletter/module.php:341` |
| `/newsletter/approval` | `approval.php` | – | `extension/cjw_newsletter/modules/newsletter/module.php:348` |
| `/newsletter/preview_as` | `preview_as.php` | preview | `extension/cjw_newsletter/modules/newsletter/module.php:360` |
| `/newsletter/interest_list` | `interest_list.php` | rendering | `extension/cjw_newsletter/modules/newsletter/module.php:367` |
| `/newsletter/interest_edit` | `interest_edit.php` | rendering | `extension/cjw_newsletter/modules/newsletter/module.php:374` |
| `/newsletter/skin_preview` | `skin_preview.php` | rendering | `extension/cjw_newsletter/modules/newsletter/module.php:381` |
| `/newsletter/r` | `r.php` | track | `extension/cjw_newsletter/modules/newsletter/module.php:392` |
| `/newsletter/o` | `o.php` | track | `extension/cjw_newsletter/modules/newsletter/module.php:399` |
| `/newsletter/report` | `report.php` | statistics | `extension/cjw_newsletter/modules/newsletter/module.php:406` |
| `/newsletter/statistics_export` | `statistics_export.php` | statistics | `extension/cjw_newsletter/modules/newsletter/module.php:413` |
| `/newsletter/ab_test` | `ab_test.php` | statistics | `extension/cjw_newsletter/modules/newsletter/module.php:420` |
| `/newsletter/sms_send` | `sms_send.php` | sms | `extension/cjw_newsletter/modules/newsletter/module.php:431` |
| `/newsletter/sms_confirm` | `sms_confirm.php` | sms_public | `extension/cjw_newsletter/modules/newsletter/module.php:438` |
| `/newsletter/sms_inbound` | `sms_inbound.php` | sms_public | `extension/cjw_newsletter/modules/newsletter/module.php:445` |
| `/newsletter/import_mapping` | `import_mapping.php` | import_export | `extension/cjw_newsletter/modules/newsletter/module.php:456` |
| `/newsletter/subscriber_export` | `subscriber_export.php` | import_export | `extension/cjw_newsletter/modules/newsletter/module.php:463` |
| `/newsletter/migration_log` | `migration_log.php` | import_export | `extension/cjw_newsletter/modules/newsletter/module.php:470` |

### extension/cjw_themes_jumper (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/cjw_paging/page` | `page.php` | – | `extension/cjw_themes_jumper/modules/cjw_paging/module.php:16` |
| `/osm_proxy/tiles` | `tiles.php` | tiles | `extension/cjw_themes_jumper/modules/osm_proxy/module.php:16` |

### extension/expchangeclass (3)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/changeclass/select_class` | `select_class.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:34` |
| `/changeclass/map_attributes` | `map_attributes.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:38` |
| `/changeclass/action` | `action.php` | convert | `extension/expchangeclass/modules/changeclass/module.php:42` |

### extension/explayouts (9)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/explayouts/dashboard` | `dashboard.php` | read | `extension/explayouts/modules/explayouts/module.php:7` |
| `/explayouts/setup` | `setup.php` | edit | `extension/explayouts/modules/explayouts/module.php:14` |
| `/explayouts/template_editor` | `template_editor.php` | read, edit | `extension/explayouts/modules/explayouts/module.php:21` |
| `/explayouts/layout_list` | `layout_list.php` | read | `extension/explayouts/modules/explayouts/module.php:28` |
| `/explayouts/layout_edit` | `layout_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:35` |
| `/explayouts/layout_preview` | `preview.php` | read | `extension/explayouts/modules/explayouts/module.php:42` |
| `/explayouts/block_edit` | `block_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:49` |
| `/explayouts/rule_list` | `rule_list.php` | read | `extension/explayouts/modules/explayouts/module.php:56` |
| `/explayouts/rule_edit` | `rule_edit.php` | edit | `extension/explayouts/modules/explayouts/module.php:63` |

### extension/explayouts_content_browser_ui (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/explayouts_content_browser_ui/browser` | `browser.php` | read | `extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui/module.php:7` |

### extension/explayouts_ui (14)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/explayouts_ui/layout_list` | `layout_list.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:7` |
| `/explayouts_ui/layout_create` | `layout_create.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:14` |
| `/explayouts_ui/layout_edit` | `layout_edit.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:21` |
| `/explayouts_ui/rule_list` | `rule_list.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:28` |
| `/explayouts_ui/shared_layouts_list` | `shared_layouts_list.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:35` |
| `/explayouts_ui/rule_edit` | `rule_edit.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:42` |
| `/explayouts_ui/block_edit` | `block_edit.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:49` |
| `/explayouts_ui/dashboard` | `dashboard.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:56` |
| `/explayouts_ui/layout_preview` | `preview.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:63` |
| `/explayouts_ui/setup` | `setup.php` | edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:70` |
| `/explayouts_ui/template_editor` | `template_editor.php` | read, edit | `extension/explayouts_ui/modules/explayouts_ui/module.php:77` |
| `/explayouts_ui/transfer_export` | `transfer_export.php` | transfer_export | `extension/explayouts_ui/modules/explayouts_ui/module.php:86` |
| `/explayouts_ui/transfer_import` | `transfer_import.php` | transfer_import | `extension/explayouts_ui/modules/explayouts_ui/module.php:93` |
| `/explayouts_ui/components` | `components.php` | read | `extension/explayouts_ui/modules/explayouts_ui/module.php:100` |

### extension/explayouts_ui_api (4)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/explayouts_ui_api/layouts` | `layouts.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:7` |
| `/explayouts_ui_api/rules` | `rules.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:14` |
| `/explayouts_ui_api/blocks` | `blocks.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:21` |
| `/explayouts_ui_api/app` | `app.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:28` |

### extension/exp_oe_tiptap (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/exp_oe_tiptap/switch` | `switch.php` | switch | `extension/exp_oe_tiptap/modules/exp_oe_tiptap/module.php:16` |

### extension/ezauthorize (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/refund/refund` | `order.php` | – | `extension/ezauthorize/modules/refund/module.php:27` |
| `/refund/refund` | `order.php` | refund | `extension/ezauthorize/modules/refund/module.php:35` |

### extension/ezfind (3)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezfind/elevate` | `elevate.php` | elevate | `extension/ezfind/modules/ezfind/module.php:11` |
| `/ezfind/elevation_detail` | `elevation_detail.php` | elevate | `extension/ezfind/modules/ezfind/module.php:24` |
| `/ezfind/remove_elevation` | `remove_elevation.php` | elevate | `extension/ezfind/modules/ezfind/module.php:37` |

### extension/ezflow (8)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezflow/get` | `get.php` | edit | `extension/ezflow/modules/ezflow/module.php:32` |
| `/ezflow/timeline` | `timeline.php` | timeline | `extension/ezflow/modules/ezflow/module.php:35` |
| `/ezflow/preview` | `preview.php` | timeline | `extension/ezflow/modules/ezflow/module.php:39` |
| `/ezflow/zone` | `zone.php` | edit | `extension/ezflow/modules/ezflow/module.php:43` |
| `/ezflow/request` | `request.php` | edit | `extension/ezflow/modules/ezflow/module.php:47` |
| `/ezflow/push` | `push.php` | edit | `extension/ezflow/modules/ezflow/module.php:52` |
| `/ezflow/block` | `block.php` | call | `extension/ezflow/modules/ezflow/module.php:58` |
| `/flash/embed` | `embed.php` | – | `extension/ezflow/modules/flash/module.php:31` |

### extension/ezie (13)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezie/prepare` | `prepare.php` | – | `extension/ezie/modules/ezie/module.php:16` |
| `/ezie/filter_bw` | `filter_bw.php` | – | `extension/ezie/modules/ezie/module.php:21` |
| `/ezie/filter_sepia` | `filter_sepia.php` | – | `extension/ezie/modules/ezie/module.php:22` |
| `/ezie/filter_contrast` | `filter_contrast.php` | – | `extension/ezie/modules/ezie/module.php:23` |
| `/ezie/filter_brightness` | `filter_brightness.php` | – | `extension/ezie/modules/ezie/module.php:24` |
| `/ezie/tool_flip_hor` | `tool_flip_hor.php` | – | `extension/ezie/modules/ezie/module.php:27` |
| `/ezie/tool_flip_ver` | `tool_flip_ver.php` | – | `extension/ezie/modules/ezie/module.php:28` |
| `/ezie/tool_rotation` | `tool_rotation.php` | – | `extension/ezie/modules/ezie/module.php:29` |
| `/ezie/tool_pixelate` | `tool_pixelate.php` | – | `extension/ezie/modules/ezie/module.php:30` |
| `/ezie/tool_crop` | `tool_crop.php` | – | `extension/ezie/modules/ezie/module.php:31` |
| `/ezie/tool_watermark` | `tool_watermark.php` | – | `extension/ezie/modules/ezie/module.php:32` |
| `/ezie/no_save_and_quit` | `no_save_and_quit.php` | – | `extension/ezie/modules/ezie/module.php:35` |
| `/ezie/save_and_quit` | `save_and_quit.php` | – | `extension/ezie/modules/ezie/module.php:36` |

### extension/ezjscore (3)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezjscore/hello` | `hello.php` | – | `extension/ezjscore/modules/ezjscore/module.php:35` |
| `/ezjscore/call` | `call.php` | call | `extension/ezjscore/modules/ezjscore/module.php:40` |
| `/ezjscore/run` | `run.php` | run | `extension/ezjscore/modules/ezjscore/module.php:46` |

### extension/ezmbpaex (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/userpaex/password` | `password.php` | password | `extension/ezmbpaex/modules/userpaex/module.php:16` |
| `/userpaex/forgotpassword` | `forgotpassword.php` | password | `extension/ezmbpaex/modules/userpaex/module.php:23` |

### extension/ezmultiupload (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezmultiupload/upload` | `upload.php` | – | `extension/ezmultiupload/modules/ezmultiupload/module.php:15` |
| `/ezmultiupload/service` | `service.php` | – | `extension/ezmultiupload/modules/ezmultiupload/module.php:20` |

### extension/ezodf (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezodf/import` | `import.php` | import | `extension/ezodf/modules/ezodf/module.php:34` |
| `/ezodf/export` | `export.php` | export | `extension/ezodf/modules/ezodf/module.php:42` |

### extension/ezoe (8)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezoe/relations` | `relations.php` | editor | `extension/ezoe/modules/ezoe/module.php:34` |
| `/ezoe/upload` | `upload.php` | editor | `extension/ezoe/modules/ezoe/module.php:41` |
| `/ezoe/tags` | `tags.php` | editor | `extension/ezoe/modules/ezoe/module.php:48` |
| `/ezoe/dialog` | `dialog.php` | editor | `extension/ezoe/modules/ezoe/module.php:55` |
| `/ezoe/embed_view` | `embed_view.php` | editor | `extension/ezoe/modules/ezoe/module.php:62` |
| `/ezoe/load` | `load.php` | editor | `extension/ezoe/modules/ezoe/module.php:68` |
| `/ezoe/spellcheck_rpc` | `spellcheck_rpc.php` | editor | `extension/ezoe/modules/ezoe/module.php:74` |
| `/ezoe/engine` | `engine.php` | editor | `extension/ezoe/modules/ezoe/module.php:146` |

### extension/ezownerchange (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/owner/change` | `change.php` | change | `extension/ezownerchange/modules/owner/module.php:22` |

### extension/ezpaypal (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/paypal/notify_url` | `notify_url.php` | – | `extension/ezpaypal/modules/paypal/module.php:33` |

### extension/ezpm (12)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/pm/message` | `view_message.php` | – | `extension/ezpm/modules/pm/module.php:7` |
| `/pm/list` | `list.php` | – | `extension/ezpm/modules/pm/module.php:11` |
| `/pm/list_inbox` | `list.php` | – | `extension/ezpm/modules/pm/module.php:15` |
| `/pm/list_sent` | `list_sent.php` | – | `extension/ezpm/modules/pm/module.php:19` |
| `/pm/list_drafts` | `list_drafts.php` | – | `extension/ezpm/modules/pm/module.php:23` |
| `/pm/create` | `create.php` | – | `extension/ezpm/modules/pm/module.php:27` |
| `/pm/reply` | `reply.php` | – | `extension/ezpm/modules/pm/module.php:31` |
| `/pm/edit` | `edit.php` | – | `extension/ezpm/modules/pm/module.php:35` |
| `/pm/add` | `add.php` | – | `extension/ezpm/modules/pm/module.php:39` |
| `/pm/action` | `action.php` | – | `extension/ezpm/modules/pm/module.php:46` |
| `/pm/blacklist` | `blacklist.php` | – | `extension/ezpm/modules/pm/module.php:59` |
| `/pm/contacts` | `contact_list.php` | – | `extension/ezpm/modules/pm/module.php:63` |

### extension/eztags (17)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/tags/treemenu` | `treemenu.php` | read | `extension/eztags/modules/tags/module.php:7` |
| `/tags/dashboard` | `dashboard.php` | dashboard | `extension/eztags/modules/tags/module.php:13` |
| `/tags/id` | `id.php` | id | `extension/eztags/modules/tags/module.php:20` |
| `/tags/list_objects` | `list_objects.php` | id | `extension/eztags/modules/tags/module.php:27` |
| `/tags/view` | `view.php` | view | `extension/eztags/modules/tags/module.php:34` |
| `/tags/add` | `add.php` | add | `extension/eztags/modules/tags/module.php:41` |
| `/tags/addsynonym` | `addsynonym.php` | addsynonym | `extension/eztags/modules/tags/module.php:47` |
| `/tags/edit` | `edit.php` | edit | `extension/eztags/modules/tags/module.php:53` |
| `/tags/movetags` | `movetags.php` | edit | `extension/eztags/modules/tags/module.php:59` |
| `/tags/translation` | `translation.php` | edit | `extension/eztags/modules/tags/module.php:65` |
| `/tags/editsynonym` | `editsynonym.php` | editsynonym | `extension/eztags/modules/tags/module.php:71` |
| `/tags/delete` | `delete.php` | delete | `extension/eztags/modules/tags/module.php:77` |
| `/tags/deletetags` | `deletetags.php` | delete | `extension/eztags/modules/tags/module.php:83` |
| `/tags/deletesynonym` | `deletesynonym.php` | deletesynonym | `extension/eztags/modules/tags/module.php:89` |
| `/tags/makesynonym` | `makesynonym.php` | makesynonym | `extension/eztags/modules/tags/module.php:95` |
| `/tags/merge` | `merge.php` | merge | `extension/eztags/modules/tags/module.php:101` |
| `/tags/search` | `search.php` | search | `extension/eztags/modules/tags/module.php:107` |

### extension/ezwt (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/websitetoolbar/sort` | `sort.php` | use | `extension/ezwt/modules/websitetoolbar/module.php:35` |

### extension/nxc_powercontent (40)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/content/edit` | `edit.php` | edit or create | `extension/nxc_powercontent/modules/content/module.php:14` |
| `/content/removenode` | `removenode.php` | edit | `extension/nxc_powercontent/modules/content/module.php:49` |
| `/content/removeassignment` | `removeassignment.php` | edit | `extension/nxc_powercontent/modules/content/module.php:58` |
| `/content/pdf` | `pdf.php` | pdf | `extension/nxc_powercontent/modules/content/module.php:68` |
| `/content/view` | `view.php` | read | `extension/nxc_powercontent/modules/content/module.php:80` |
| `/content/copy` | `copy.php` | read | `extension/nxc_powercontent/modules/content/module.php:92` |
| `/content/copysubtree` | `copysubtree.php` | create | `extension/nxc_powercontent/modules/content/module.php:103` |
| `/content/versionview` | `versionview.php` | versionread | `extension/nxc_powercontent/modules/content/module.php:117` |
| `/content/restore` | `restore.php` | restore | `extension/nxc_powercontent/modules/content/module.php:134` |
| `/content/search` | `search.php` | read | `extension/nxc_powercontent/modules/content/module.php:145` |
| `/content/urlalias` | `urlalias.php` | edit | `extension/nxc_powercontent/modules/content/module.php:152` |
| `/content/urltranslator` | `urlalias_global.php` | urltranslator | `extension/nxc_powercontent/modules/content/module.php:166` |
| `/content/urlwildcards` | `urlalias_wildcard.php` | urltranslator | `extension/nxc_powercontent/modules/content/module.php:186` |
| `/content/advancedsearch` | `advancedsearch.php` | read | `extension/nxc_powercontent/modules/content/module.php:203` |
| `/content/browse` | `browse.php` | read | `extension/nxc_powercontent/modules/content/module.php:210` |
| `/content/upload` | `upload.php` | create | `extension/nxc_powercontent/modules/content/module.php:218` |
| `/content/removeobject` | `removeobject.php` | read | `extension/nxc_powercontent/modules/content/module.php:228` |
| `/content/removeuserobject` | `removeobject.php` | read | `extension/nxc_powercontent/modules/content/module.php:234` |
| `/content/removemediaobject` | `removeobject.php` | read | `extension/nxc_powercontent/modules/content/module.php:240` |
| `/content/removeeditversion` | `removeeditversion.php` | read | `extension/nxc_powercontent/modules/content/module.php:246` |
| `/content/download` | `download.php` | read | `extension/nxc_powercontent/modules/content/module.php:253` |
| `/content/action` | `action.php` | read | `extension/nxc_powercontent/modules/content/module.php:260` |
| `/content/collectinformation` | `collectinformation.php` | read | `extension/nxc_powercontent/modules/content/module.php:328` |
| `/content/draft` | `draft.php` | edit | `extension/nxc_powercontent/modules/content/module.php:338` |
| `/content/history` | `history.php` | read, edit | `extension/nxc_powercontent/modules/content/module.php:345` |
| `/content/trash` | `trash.php` | restore | `extension/nxc_powercontent/modules/content/module.php:360` |
| `/content/translations` | `translations.php` | translations | `extension/nxc_powercontent/modules/content/module.php:367` |
| `/content/tipafriend` | `tipafriend.php` | tipafriend, read | `extension/nxc_powercontent/modules/content/module.php:383` |
| `/content/keyword` | `keyword.php` | read | `extension/nxc_powercontent/modules/content/module.php:389` |
| `/content/collectedinfo` | `collectedinfo.php` | read | `extension/nxc_powercontent/modules/content/module.php:396` |
| `/content/bookmark` | `bookmark.php` | bookmark | `extension/nxc_powercontent/modules/content/module.php:402` |
| `/content/pendinglist` | `pendinglist.php` | pendinglist | `extension/nxc_powercontent/modules/content/module.php:413` |
| `/content/new` | `newcontent.php` | read | `extension/nxc_powercontent/modules/content/module.php:420` |
| `/content/hide` | `hide.php` | hide | `extension/nxc_powercontent/modules/content/module.php:426` |
| `/content/move` | `move.php` | edit | `extension/nxc_powercontent/modules/content/module.php:432` |
| `/content/reverserelatedlist` | `reverserelatedlist.php` | reverserelatedlist | `extension/nxc_powercontent/modules/content/module.php:438` |
| `/content/translation` | `translation.php` | read | `extension/nxc_powercontent/modules/content/module.php:445` |
| `/content/treemenu` | `treemenu.php` | read | `extension/nxc_powercontent/modules/content/module.php:474` |
| `/content/dashboard` | `dashboard.php` | dashboard | `extension/nxc_powercontent/modules/content/module.php:481` |
| `/content/queued` | `queued.php` | edit | `extension/nxc_powercontent/modules/content/module.php:488` |

### extension/powercontent (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/powercontent/action` | `action.php` | – | `extension/powercontent/modules/powercontent/module.php:6` |

### extension/sevenx_authentication_2fa (4)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/user2fa/verify` | `verify.php` | verify | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:17` |
| `/user2fa/setup` | `setup.php` | setup | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:25` |
| `/user2fa/callback` | `callback.php` | callback | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:33` |
| `/user2fa/oauth` | `oauth.php` | oauth | `extension/sevenx_authentication_2fa/modules/user2fa/module.php:41` |

### extension/sevenx_themes_media (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/info-collection/view-modal` | `viewmodal.php` | read | `extension/sevenx_themes_media/modules/info-collection/module.php:6` |
| `/info-collection/submit` | `submit.php` | read | `extension/sevenx_themes_media/modules/info-collection/module.php:13` |

### extension/syndication (13)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/syndication/menu` | `menu.php` | menu | `extension/syndication/modules/syndication/module.php:43` |
| `/syndication/list` | `list.php` | view_export | `extension/syndication/modules/syndication/module.php:48` |
| `/syndication/import_list` | `import_list.php` | view_export | `extension/syndication/modules/syndication/module.php:54` |
| `/syndication/import_edit` | `import_edit.php` | edit_import | `extension/syndication/modules/syndication/module.php:60` |
| `/syndication/pending_edit` | `pending_edit.php` | import_object_status | `extension/syndication/modules/syndication/module.php:67` |
| `/syndication/edit` | `edit.php` | edit_export | `extension/syndication/modules/syndication/module.php:75` |
| `/syndication/add_feed_source` | `add_feed_source.php` | edit_export | `extension/syndication/modules/syndication/module.php:81` |
| `/syndication/list_source_filter` | `list_source_filter.php` | edit_export | `extension/syndication/modules/syndication/module.php:88` |
| `/syndication/edit_source_filter` | `edit_source_filter.php` | edit_export | `extension/syndication/modules/syndication/module.php:94` |
| `/syndication/edit_import_filter` | `edit_import_filter.php` | edit_import | `extension/syndication/modules/syndication/module.php:100` |
| `/syndication/import_info` | `import_info.php` | view_import | `extension/syndication/modules/syndication/module.php:108` |
| `/syndication/feed_info` | `feed_info.php` | view_export_info | `extension/syndication/modules/syndication/module.php:115` |
| `/syndication/job` | `job.php` | menu | `extension/syndication/modules/syndication/module.php:122` |

### extension/xrowextract (9)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/xrowextract/csv` | `csv.php` | csv | `extension/xrowextract/modules/xrowextract/module.php:6` |
| `/xrowextract/upload_chunk` | `upload_chunk.php` | import | `extension/xrowextract/modules/xrowextract/module.php:56` |
| `/xrowextract/package` | `package.php` | import | `extension/xrowextract/modules/xrowextract/module.php:65` |
| `/xrowextract/schedules` | `schedules.php` | schedule | `extension/xrowextract/modules/xrowextract/module.php:74` |
| `/xrowextract/destinations` | `destinations.php` | destinations | `extension/xrowextract/modules/xrowextract/module.php:80` |
| `/xrowextract/history` | `history.php` | history | `extension/xrowextract/modules/xrowextract/module.php:86` |
| `/xrowextract/browse` | `browse.php` | import | `extension/xrowextract/modules/xrowextract/module.php:96` |
| `/xrowextract/compare` | `compare.php` | import | `extension/xrowextract/modules/xrowextract/module.php:105` |
| `/xrowextract/browse_file` | `browse_file.php` | import | `extension/xrowextract/modules/xrowextract/module.php:111` |

### extension/xrowmetadata (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/sitemaps/index` | `index.php` | – | `extension/xrowmetadata/modules/sitemaps/module.php:9` |
| `/sitemaps/robots` | `robots.php` | – | `extension/xrowmetadata/modules/sitemaps/module.php:12` |

### kernel/modules (234)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/apikey/list` | `list.php` | – | `kernel/apikey/module.php:24` |
| `/audit/dashboard` | `dashboard.php` | read | `kernel/audit/module.php:27` |
| `/audit/console` | `console.php` | read | `kernel/audit/module.php:33` |
| `/audit/event` | `event.php` | read | `kernel/audit/module.php:40` |
| `/audit/charts` | `charts.php` | read | `kernel/audit/module.php:47` |
| `/audit/alerts` | `alerts.php` | read | `kernel/audit/module.php:54` |
| `/audit/export` | `export.php` | read | `kernel/audit/module.php:61` |
| `/audit/archives` | `archives.php` | manage | `kernel/audit/module.php:68` |
| `/audit/settings` | `settings.php` | manage | `kernel/audit/module.php:75` |
| `/audit/recent` | `recent.php` | read | `kernel/audit/module.php:81` |
| `/class/edit` | `edit.php` | – | `kernel/class/module.php:13` |
| `/class/view` | `view.php` | – | `kernel/class/module.php:20` |
| `/class/copy` | `copy.php` | – | `kernel/class/module.php:26` |
| `/class/down` | `edit.php` | – | `kernel/class/module.php:31` |
| `/class/up` | `edit.php` | – | `kernel/class/module.php:35` |
| `/class/removeclass` | `removeclass.php` | – | `kernel/class/module.php:39` |
| `/class/removegroup` | `removegroup.php` | – | `kernel/class/module.php:43` |
| `/class/classlist` | `classlist.php` | – | `kernel/class/module.php:47` |
| `/class/grouplist` | `grouplist.php` | – | `kernel/class/module.php:51` |
| `/class/groupedit` | `groupedit.php` | – | `kernel/class/module.php:55` |
| `/class/translation` | `translation.php` | – | `kernel/class/module.php:60` |
| `/collaboration/action` | `action.php` | – | `kernel/collaboration/module.php:13` |
| `/collaboration/view` | `view.php` | – | `kernel/collaboration/module.php:24` |
| `/collaboration/item` | `item.php` | – | `kernel/collaboration/module.php:34` |
| `/collaboration/group` | `group.php` | – | `kernel/collaboration/module.php:40` |
| `/content/multiedit` | `multiedit.php` | edit | `kernel/content/module.php:23` |
| `/content/edit` | `edit.php` | edit or create | `kernel/content/module.php:34` |
| `/content/removenode` | `removenode.php` | edit | `kernel/content/module.php:70` |
| `/content/removeassignment` | `removeassignment.php` | edit | `kernel/content/module.php:79` |
| `/content/pdf` | `pdf.php` | pdf | `kernel/content/module.php:89` |
| `/content/view` | `view.php` | read | `kernel/content/module.php:101` |
| `/content/copy` | `copy.php` | read | `kernel/content/module.php:113` |
| `/content/copysubtree` | `copysubtree.php` | create | `kernel/content/module.php:124` |
| `/content/versionview` | `versionview.php` | versionread | `kernel/content/module.php:138` |
| `/content/restore` | `restore.php` | restore | `kernel/content/module.php:158` |
| `/content/search` | `search.php` | read | `kernel/content/module.php:169` |
| `/content/urlalias` | `urlalias.php` | edit | `kernel/content/module.php:176` |
| `/content/urltranslator` | `urlalias_global.php` | urltranslator | `kernel/content/module.php:190` |
| `/content/urlwildcards` | `urlalias_wildcard.php` | urltranslator | `kernel/content/module.php:210` |
| `/content/advancedsearch` | `advancedsearch.php` | read | `kernel/content/module.php:227` |
| `/content/browse` | `browse.php` | read | `kernel/content/module.php:234` |
| `/content/upload` | `upload.php` | create | `kernel/content/module.php:242` |
| `/content/removeobject` | `removeobject.php` | read | `kernel/content/module.php:252` |
| `/content/removeuserobject` | `removeobject.php` | read | `kernel/content/module.php:258` |
| `/content/removemediaobject` | `removeobject.php` | read | `kernel/content/module.php:264` |
| `/content/removeeditversion` | `removeeditversion.php` | read | `kernel/content/module.php:270` |
| `/content/download` | `download.php` | read | `kernel/content/module.php:277` |
| `/content/action` | `action.php` | read | `kernel/content/module.php:284` |
| `/content/collectinformation` | `collectinformation.php` | read | `kernel/content/module.php:356` |
| `/content/draft` | `draft.php` | edit | `kernel/content/module.php:366` |
| `/content/history` | `history.php` | read or edit | `kernel/content/module.php:373` |
| `/content/trash` | `trash.php` | restore | `kernel/content/module.php:389` |
| `/content/translations` | `translations.php` | translations | `kernel/content/module.php:396` |
| `/content/tipafriend` | `tipafriend.php` | tipafriend, read | `kernel/content/module.php:412` |
| `/content/keyword` | `keyword.php` | read | `kernel/content/module.php:418` |
| `/content/collectedinfo` | `collectedinfo.php` | read | `kernel/content/module.php:425` |
| `/content/bookmark` | `bookmark.php` | bookmark | `kernel/content/module.php:431` |
| `/content/pendinglist` | `pendinglist.php` | pendinglist | `kernel/content/module.php:442` |
| `/content/new` | `newcontent.php` | read | `kernel/content/module.php:449` |
| `/content/hide` | `hide.php` | hide | `kernel/content/module.php:455` |
| `/content/move` | `move.php` | edit | `kernel/content/module.php:463` |
| `/content/reverserelatedlist` | `reverserelatedlist.php` | reverserelatedlist | `kernel/content/module.php:469` |
| `/content/translation` | `translation.php` | read | `kernel/content/module.php:476` |
| `/content/treemenu` | `treemenu.php` | read | `kernel/content/module.php:505` |
| `/content/subitemsexport` | `subitemsexport.php` | read | `kernel/content/module.php:513` |
| `/content/dashboard` | `dashboard.php` | dashboard | `kernel/content/module.php:519` |
| `/content/queued` | `queued.php` | edit | `kernel/content/module.php:526` |
| `/content/job` | `job.php` | read | `kernel/content/module.php:535` |
| `/content/jobs` | `jobs.php` | read | `kernel/content/module.php:542` |
| `/contentcheck/unreferenced` | `unreferenced.php` | unreferenced | `kernel/contentcheck/module.php:26` |
| `/contentcheck/trash` | `trash.php` | unreferenced, trash | `kernel/contentcheck/module.php:34` |
| `/contentcheck/brokenrelations` | `brokenrelations.php` | brokenrelations | `kernel/contentcheck/module.php:42` |
| `/contentcheck/fixfiles` | `fixfiles.php` | brokenrelations, fixfiles | `kernel/contentcheck/module.php:49` |
| `/contentcheck/scope` | `scope.php` | – | `kernel/contentcheck/module.php:57` |
| `/contentcheck/storage` | `storage.php` | storage | `kernel/contentcheck/module.php:64` |
| `/contentcheck/storageaction` | `storageaction.php` | storage | `kernel/contentcheck/module.php:72` |
| `/contentfinder/search` | `search.php` | search | `kernel/contentfinder/module.php:24` |
| `/contentfinder/action` | `action.php` | search, delete | `kernel/contentfinder/module.php:31` |
| `/csp/report` | `report.php` | – | `kernel/csp/module.php:22` |
| `/error/view` | `view.php` | – | `kernel/error/module.php:13` |
| `/exphelp/dashboard` | `dashboard.php` | read | `kernel/exphelp/module.php:28` |
| `/exphelp/viewer` | `viewer.php` | read | `kernel/exphelp/module.php:35` |
| `/exphelp/contents` | `contents.php` | read | `kernel/exphelp/module.php:42` |
| `/exphelp/search` | `search.php` | read | `kernel/exphelp/module.php:51` |
| `/exphelp/asset` | `asset.php` | read | `kernel/exphelp/module.php:65` |
| `/exphelp/pdf` | `pdf.php` | read | `kernel/exphelp/module.php:72` |
| `/exphelp/report` | `report.php` | read | `kernel/exphelp/module.php:79` |
| `/exphelp/topic` | `topic.php` | read | `kernel/exphelp/module.php:87` |
| `/exphelp/manual` | `manual.php` | read | `kernel/exphelp/module.php:94` |
| `/exphelp/context` | `context.php` | context | `kernel/exphelp/module.php:102` |
| `/ezinfo/copyright` | `copyright.php` | read | `kernel/ezinfo/module.php:14` |
| `/ezinfo/about` | `about.php` | read | `kernel/ezinfo/module.php:21` |
| `/ezinfo/is_alive` | `isalive.php` | read | `kernel/ezinfo/module.php:27` |
| `/infocollector/overview` | `overview.php` | read | `kernel/infocollector/module.php:13` |
| `/infocollector/collectionlist` | `collectionlist.php` | read | `kernel/infocollector/module.php:23` |
| `/infocollector/view` | `view.php` | read | `kernel/infocollector/module.php:34` |
| `/infocollector/export` | `export.php` | read | `kernel/infocollector/module.php:43` |
| `/layout/set` | `set.php` | – | `kernel/layout/module.php:14` |
| `/mailpreferences/settings` | `settings.php` | – | `kernel/mailpreferences/module.php:22` |
| `/mailpreferences/manage` | `manage.php` | – | `kernel/mailpreferences/module.php:30` |
| `/mailpreferences/unsubscribe` | `unsubscribe.php` | – | `kernel/mailpreferences/module.php:37` |
| `/mailpreferences/confirm` | `confirm.php` | – | `kernel/mailpreferences/module.php:43` |
| `/mailpreferences/request` | `request.php` | – | `kernel/mailpreferences/module.php:49` |
| `/mailpreferences/export` | `export.php` | – | `kernel/mailpreferences/module.php:55` |
| `/mailpreferences/admin` | `admin.php` | administrate | `kernel/mailpreferences/module.php:62` |
| `/notification/settings` | `settings.php` | use | `kernel/notification/module.php:15` |
| `/notification/runfilter` | `runfilter.php` | administrate | `kernel/notification/module.php:23` |
| `/notification/status` | `status.php` | administrate | `kernel/notification/module.php:31` |
| `/notification/job` | `job.php` | administrate | `kernel/notification/module.php:39` |
| `/notification/addtonotification` | `addtonotification.php` | use | `kernel/notification/module.php:46` |
| `/package/list` | `list.php` | list | `kernel/package/module.php:13` |
| `/package/upload` | `upload.php` | import | `kernel/package/module.php:29` |
| `/package/create` | `create.php` | create | `kernel/package/module.php:37` |
| `/package/viewfile` | `viewfile.php` | read | `kernel/package/module.php:84` |
| `/package/compare` | `compare.php` | read | `kernel/package/module.php:95` |
| `/package/install` | `install.php` | install | `kernel/package/module.php:108` |
| `/package/uninstall` | `uninstall.php` | install | `kernel/package/module.php:132` |
| `/pdf/edit` | `edit.php` | edit | `kernel/pdf/module.php:13` |
| `/pdf/list` | `list.php` | edit | `kernel/pdf/module.php:48` |
| `/role/list` | `list.php` | – | `kernel/role/module.php:13` |
| `/role/edit` | `edit.php` | – | `kernel/role/module.php:19` |
| `/role/copy` | `copy.php` | – | `kernel/role/module.php:24` |
| `/role/policyedit` | `policyedit.php` | – | `kernel/role/module.php:29` |
| `/role/view` | `view.php` | – | `kernel/role/module.php:34` |
| `/role/assign` | `assign.php` | – | `kernel/role/module.php:39` |
| `/rss/list` | `list.php` | edit | `kernel/rss/module.php:13` |
| `/rss/edit_export` | `edit_export.php` | edit | `kernel/rss/module.php:28` |
| `/rss/edit_import` | `edit_import.php` | edit | `kernel/rss/module.php:41` |
| `/rss/feed` | `feed.php` | feed | `kernel/rss/module.php:55` |
| `/search/stats` | `stats.php` | – | `kernel/search/module.php:15` |
| `/section/list` | `list.php` | view or edit or assign | `kernel/section/module.php:13` |
| `/section/view` | `view.php` | view or assign | `kernel/section/module.php:20` |
| `/section/edit` | `edit.php` | edit | `kernel/section/module.php:28` |
| `/section/assign` | `assign.php` | assign | `kernel/section/module.php:35` |
| `/settings/view` | `view.php` | – | `kernel/settings/module.php:14` |
| `/settings/edit` | `edit.php` | – | `kernel/settings/module.php:18` |
| `/setup/init` | `ezsetup.php` | install | `kernel/setup/module.php:18` |
| `/setup/cache` | `cache.php` | managecache | `kernel/setup/module.php:25` |
| `/setup/cronjobsstream` | `cronjobsstream.php` | managecronjobs | `kernel/setup/module.php:303` |
| `/setup/staticcachestream` | `staticcachestream.php` | managecache | `kernel/setup/module.php:315` |
| `/setup/systemupgrade` | `systemupgrade.php` | setup | `kernel/setup/module.php:326` |
| `/setup/logs` | `logs.php` | logs | `kernel/setup/module.php:342` |
| `/setup/logsmanage` | `logsmanage.php` | managelogs | `kernel/setup/module.php:357` |
| `/setup/logshealth` | `logshealth.php` | logs | `kernel/setup/module.php:367` |
| `/setup/logsdigest` | `logsdigest.php` | logs | `kernel/setup/module.php:376` |
| `/setup/logsfeed` | `logsfeed.php` | logs | `kernel/setup/module.php:384` |
| `/setup/logsaction` | `logsaction.php` | logs | `kernel/setup/module.php:391` |
| `/setup/toolbarlist` | `toolbarlist.php` | setup | `kernel/setup/module.php:400` |
| `/setup/toolbar` | `toolbar.php` | setup | `kernel/setup/module.php:406` |
| `/setup/menuconfig` | `menuconfig.php` | setup | `kernel/setup/module.php:414` |
| `/setup/templatelist` | `templatelist.php` | setup | `kernel/setup/module.php:422` |
| `/setup/templateview` | `templateview.php` | setup | `kernel/setup/module.php:429` |
| `/setup/templateedit` | `templateedit.php` | setup | `kernel/setup/module.php:439` |
| `/setup/templatecreate` | `templatecreate.php` | setup | `kernel/setup/module.php:448` |
| `/shop/add` | `add.php` | buy | `kernel/shop/module.php:14` |
| `/shop/orderview` | `orderview.php` | buy | `kernel/shop/module.php:20` |
| `/shop/orderreceipt` | `orderreceipt.php` | – | `kernel/shop/module.php:28` |
| `/shop/orderreciept` | `orderreciept.php` | – | `kernel/shop/module.php:35` |
| `/shop/updatebasket` | `updatebasket.php` | buy | `kernel/shop/module.php:41` |
| `/shop/basket` | `basket.php` | buy | `kernel/shop/module.php:47` |
| `/shop/register` | `register.php` | buy | `kernel/shop/module.php:69` |
| `/shop/userregister` | `userregister.php` | buy | `kernel/shop/module.php:79` |
| `/shop/wishlist` | `wishlist.php` | buy | `kernel/shop/module.php:89` |
| `/shop/dashboard` | `dashboard.php` | administrate | `kernel/shop/module.php:98` |
| `/shop/orderlist` | `orderlist.php` | administrate | `kernel/shop/module.php:104` |
| `/shop/archivelist` | `archivelist.php` | administrate | `kernel/shop/module.php:111` |
| `/shop/removeorder` | `removeorder.php` | administrate | `kernel/shop/module.php:118` |
| `/shop/archiveorder` | `archiveorder.php` | administrate | `kernel/shop/module.php:125` |
| `/shop/unarchiveorder` | `unarchiveorder.php` | administrate | `kernel/shop/module.php:132` |
| `/shop/customerlist` | `customerlist.php` | administrate | `kernel/shop/module.php:140` |
| `/shop/customerorderview` | `customerorderview.php` | administrate | `kernel/shop/module.php:147` |
| `/shop/statistics` | `orderstatistics.php` | administrate | `kernel/shop/module.php:153` |
| `/shop/confirmorder` | `confirmorder.php` | buy | `kernel/shop/module.php:159` |
| `/shop/checkout` | `checkout.php` | buy | `kernel/shop/module.php:165` |
| `/shop/vattype` | `vattype.php` | setup | `kernel/shop/module.php:171` |
| `/shop/vatrules` | `vatrules.php` | setup | `kernel/shop/module.php:184` |
| `/shop/editvatrule` | `editvatrule.php` | setup | `kernel/shop/module.php:190` |
| `/shop/productcategories` | `productcategories.php` | setup | `kernel/shop/module.php:208` |
| `/shop/discountgroup` | `discountgroup.php` | setup | `kernel/shop/module.php:221` |
| `/shop/discountgroupedit` | `discountgroupedit.php` | setup | `kernel/shop/module.php:227` |
| `/shop/discountruleedit` | `discountruleedit.php` | setup | `kernel/shop/module.php:234` |
| `/shop/discountgroupview` | `discountgroupmembershipview.php` | setup | `kernel/shop/module.php:242` |
| `/shop/status` | `status.php` | edit_status | `kernel/shop/module.php:249` |
| `/shop/setstatus` | `setstatus.php` | setstatus | `kernel/shop/module.php:255` |
| `/shop/currencylist` | `currencylist.php` | setup | `kernel/shop/module.php:261` |
| `/shop/editcurrency` | `editcurrency.php` | setup | `kernel/shop/module.php:278` |
| `/shop/preferredcurrency` | `preferredcurrency.php` | buy | `kernel/shop/module.php:291` |
| `/shop/productsoverview` | `productsoverview.php` | administrate | `kernel/shop/module.php:297` |
| `/shop/setpreferredcurrency` | `setpreferredcurrency.php` | buy | `kernel/shop/module.php:311` |
| `/shop/setusercountry` | `setusercountry.php` | buy | `kernel/shop/module.php:320` |
| `/state/assign` | `assign.php` | assign | `kernel/state/module.php:15` |
| `/state/groups` | `groups.php` | administrate | `kernel/state/module.php:26` |
| `/state/group` | `group.php` | administrate | `kernel/state/module.php:37` |
| `/state/group_edit` | `group_edit.php` | administrate | `kernel/state/module.php:50` |
| `/state/view` | `view.php` | administrate | `kernel/state/module.php:60` |
| `/state/edit` | `edit.php` | administrate | `kernel/state/module.php:68` |
| `/trigger/list` | `list.php` | – | `kernel/trigger/module.php:13` |
| `/uiswitch/set` | `set.php` | – | `kernel/uiswitch/module.php:24` |
| `/url/list` | `list.php` | – | `kernel/url/module.php:13` |
| `/url/view` | `view.php` | – | `kernel/url/module.php:22` |
| `/url/edit` | `edit.php` | – | `kernel/url/module.php:28` |
| `/user/logout` | `logout.php` | login | `kernel/user/module.php:14` |
| `/user/login` | `login.php` | login | `kernel/user/module.php:20` |
| `/user/setting` | `setting.php` | preferences | `kernel/user/module.php:34` |
| `/user/preferences` | `preferences.php` | login | `kernel/user/module.php:40` |
| `/user/megamenu` | `megamenu.php` | login | `kernel/user/module.php:52` |
| `/user/password` | `password.php` | password | `kernel/user/module.php:58` |
| `/user/forgotpassword` | `forgotpassword.php` | password | `kernel/user/module.php:65` |
| `/user/edit` | `edit.php` | login | `kernel/user/module.php:76` |
| `/user/register` | `register.php` | register | `kernel/user/module.php:86` |
| `/user/activate` | `activate.php` | login | `kernel/user/module.php:96` |
| `/user/success` | `success.php` | register | `kernel/user/module.php:103` |
| `/user/unactivated` | `unactivated.php` | activation | `kernel/user/module.php:110` |
| `/user/hygiene` | `hygiene.php` | hygiene | `kernel/user/module.php:135` |
| `/user/deactivate` | `deactivate.php` | deactivate | `kernel/user/module.php:148` |
| `/user/registration` | `registration.php` | login | `kernel/user/module.php:161` |
| `/visual/toolbarlist` | `toolbarlist.php` | – | `kernel/visual/module.php:15` |
| `/visual/toolbar` | `toolbar.php` | – | `kernel/visual/module.php:20` |
| `/visual/menuconfig` | `menuconfig.php` | – | `kernel/visual/module.php:33` |
| `/visual/templatelist` | `templatelist.php` | – | `kernel/visual/module.php:42` |
| `/visual/templateview` | `templateview.php` | – | `kernel/visual/module.php:48` |
| `/visual/templateedit` | `templateedit.php` | – | `kernel/visual/module.php:57` |
| `/visual/templatecreate` | `templatecreate.php` | – | `kernel/visual/module.php:66` |
| `/workflow/view` | `view.php` | – | `kernel/workflow/module.php:13` |
| `/workflow/edit` | `edit.php` | – | `kernel/workflow/module.php:17` |
| `/workflow/groupedit` | `groupedit.php` | – | `kernel/workflow/module.php:22` |
| `/workflow/down` | `edit.php` | – | `kernel/workflow/module.php:27` |
| `/workflow/up` | `edit.php` | – | `kernel/workflow/module.php:31` |
| `/workflow/workflowlist` | `workflowlist.php` | – | `kernel/workflow/module.php:35` |
| `/workflow/grouplist` | `grouplist.php` | – | `kernel/workflow/module.php:39` |
| `/workflow/process` | `process.php` | – | `kernel/workflow/module.php:43` |
| `/workflow/run` | `run.php` | – | `kernel/workflow/module.php:47` |
| `/workflow/event` | `event.php` | – | `kernel/workflow/module.php:51` |
| `/workflow/processlist` | `processlist.php` | – | `kernel/workflow/module.php:55` |

### kernel/private (8)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/oauth/authorize` | `authorize.php` | – | `kernel/private/modules/oauth/module.php:21` |
| `/oauthadmin/list` | `list.php` | – | `kernel/private/modules/oauthadmin/module.php:21` |
| `/oauthadmin/edit` | `edit.php` | – | `kernel/private/modules/oauthadmin/module.php:26` |
| `/oauthadmin/action` | `action.php` | – | `kernel/private/modules/oauthadmin/module.php:37` |
| `/oauthadmin/view` | `view.php` | – | `kernel/private/modules/oauthadmin/module.php:46` |
| `/oauthadmin/keys` | `keys.php` | – | `kernel/private/modules/oauthadmin/module.php:54` |
| `/oauthadmin/keyaction` | `keyaction.php` | – | `kernel/private/modules/oauthadmin/module.php:61` |
| `/switchlanguage/to` | `to.php` | – | `kernel/private/modules/switchlanguage/module.php:16` |
