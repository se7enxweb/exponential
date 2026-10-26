---
title: Command (console, scripts, cronjobs)
audience: developer
category: standards
order: 319
summary: A unit of work that runs on its own: a console command (./console exp:...), a CLI script class or a cronjob part. 247 instances in Exponential.
keywords: design pattern, command, request handling, pattern catalogue
type: catalogue
---
# Command (console, scripts, cronjobs)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A unit of work that runs on its own: a console command (./console exp:...), a CLI script class or a cronjob part.

| | |
|---|---|
| Group | Request handling |
| Instances | 247 |
| Recognized by | Extends Command, CronjobPart or Runnable (the runnable console commands and cronjob parts), or a cronjobs/*.php file. |
| Extension point | kernel/private/classes/commands/, cronjob.ini [CronjobSettings] ExtensionDirectories[] and CronjobPart-*. |

## Classes by area

### extension/bccie (5)

[All patterns of extension/bccie](area-extension-bccie.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Extension\Bccie\Export` | class extends Command | `extension/bccie/classes/runnable/commands/php_export.php:16` | ext:bccie:export - export the collected information of a form to a CSV or SYLK file, or run the scheduled export (the cronjob parts exportcsv and exp… | – |
| `Exponential\Command\Extension\Bccie\Purge` | class extends Command | `extension/bccie/classes/runnable/commands/php_purge.php:14` | ext:bccie:purge - remove the collected information of a form, all of it or what is older than a date. | – |
| `Exponential\Command\Extension\Bccie\Status` | class extends Command | `extension/bccie/classes/runnable/commands/php_status.php:15` | ext:bccie:status - the dashboard of the admin as text: the forms with collected information, the last exports and the problems found. | – |
| `Exponential\Cronjob\Extension\Bccie\Exportcsv` | class extends CronjobPart | `extension/bccie/classes/runnable/cronjobs/exportcsv.php:16` | Cronjob part exportcsv: writes the collected information of the objects in [CieSettings] Collection[] to CSV files in [CieSettings] Directory. | – |
| `Exponential\Cronjob\Extension\Bccie\Exportsylk` | class extends CronjobPart | `extension/bccie/classes/runnable/cronjobs/exportsylk.php:16` | Cronjob part exportsylk: writes the collected information of the objects in [CieSettings] Collection[] to SYLK files in [CieSettings] Directory. | – |

### extension/bcgooglesitemaps (2)

[All patterns of extension/bcgooglesitemaps](area-extension-bcgooglesitemaps.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Cronjob\Extension\Bcgooglesitemaps\Generate` | class extends CronjobPart | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generate.php:49` | The code of extension/bcgooglesitemaps/cronjobs/generate.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Extension\Bcgooglesitemaps\Generatemultilingual` | class extends CronjobPart | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generatemultilingual.php:88` | Alert the user to how long the script execution took place * | – |

### extension/cjw_newsletter (16)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Extension\CjwNewsletter\ClassLanguages` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_class_languages.php:15` | ext:cjw_newsletter:class-languages: moves the newsletter content classes from eng-GB to eng-US (4.2.1 upgrade step, idempotent). | – |
| `Exponential\Command\Extension\CjwNewsletter\Createoutput` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_createoutput.php:276` | Format all a tags as text links remove all achor tags | – |
| `Exponential\Command\Extension\CjwNewsletter\Deliverability` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_deliverability.php:21` | ext:cjw_newsletter:deliverability - the area "deliverability" of cjw_newsletter 4.2.0 on the console. | – |
| `Exponential\Command\Extension\CjwNewsletter\Import` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_import.php:14` | ext:cjw_newsletter:import - import the rows of an uploaded CSV file (the import of the admin's "Import all"), or a file by path. | – |
| `Exponential\Command\Extension\CjwNewsletter\ImportEznewsletter` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_import_eznewsletter.php:15` | ext:cjw_newsletter:import-eznewsletter - take the lists, subscribers and subscriptions of an old eznewsletter installation over into cjw_newsletter. | – |
| `Exponential\Command\Extension\CjwNewsletter\Iniloader` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_iniloader.php:29` | The code of extension/cjw_newsletter/bin/php/iniloader.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\CjwNewsletter\Mailbox` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_mailbox.php:14` | ext:cjw_newsletter:mailbox - collect the mails of the active mail accounts and parse them (the cronjob part cjw_newsletter_mailbox, by hand). | – |
| `Exponential\Command\Extension\CjwNewsletter\Queue` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_queue.php:15` | ext:cjw_newsletter:queue - create the mail queue and send the mails (the cronjob parts cjw_newsletter_mailqueue_create and cjw_newsletter_mailqueue_p… | – |
| `Exponential\Command\Extension\CjwNewsletter\Repair` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_repair.php:14` | ext:cjw_newsletter:repair - remove the orphans the start page reports. | – |
| `Exponential\Command\Extension\CjwNewsletter\Schedule` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_schedule.php:20` | ext:cjw_newsletter:schedule - the recurring sends. | – |
| `Exponential\Command\Extension\CjwNewsletter\Statistics` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_statistics.php:21` | ext:cjw_newsletter:statistics - the newsletter statistics (cjw_newsletter 4.2.0, area N4 Statistics). | – |
| `Exponential\Command\Extension\CjwNewsletter\Status` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_status.php:14` | ext:cjw_newsletter:status - what the start page of the newsletter module shows. | – |
| `Exponential\Command\Extension\CjwNewsletter\TranslatableFields` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_translatable_fields.php:14` | ext:cjw_newsletter:translatable-fields: makes the text fields of the newsletter classes translatable (4.2.0 upgrade step, idempotent). | – |
| `Exponential\Cronjob\Extension\CjwNewsletter\CjwNewsletterMailbox` | class extends CronjobPart | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailbox.php:14` | The code of extension/cjw_newsletter/cronjobs/cjw_newsletter_mailbox.php, as a class (#207). | – |
| `Exponential\Cronjob\Extension\CjwNewsletter\CjwNewsletterMailqueueCreate` | class extends CronjobPart | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailqueue_create.php:36` | The code of extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_create.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Extension\CjwNewsletter\CjwNewsletterMailqueueProcess` | class extends CronjobPart | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailqueue_process.php:33` | The code of extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_process.php, moved into a class (#207 stage 1). | – |

### extension/enhancedselection2 (2)

[All patterns of extension/enhancedselection2](area-extension-enhancedselection2.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Extension\Enhancedselection2\MigrateToDatabase` | class extends Command | `extension/enhancedselection2/classes/runnable/commands/php_migrate_to_database.php:11` | The code of extension/enhancedselection2/bin/php/migrate_to_database.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\Enhancedselection2\Updateenhancedselection` | class extends Command | `extension/enhancedselection2/classes/runnable/commands/php_updateenhancedselection.php:10` | The code of extension/enhancedselection2/bin/php/updateenhancedselection.php, moved into a class (#207 stage 1). | – |

### extension/explayouts (3)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsSiteBundleCommand` | class | `extension/explayouts/classes/explayoutssitebundlecommand.php:4` | – | – |
| `Exponential\Command\Extension\Explayouts\Updatecomponentblockidentifiers` | class extends Command | `extension/explayouts/classes/runnable/commands/php_updatecomponentblockidentifiers.php:33` | The code of extension/explayouts/bin/php/updatecomponentblockidentifiers.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\Explayouts\Updatelinklocationreferences` | class extends Command | `extension/explayouts/classes/runnable/commands/php_updatelinklocationreferences.php:41` | The code of extension/explayouts/bin/php/updatelinklocationreferences.php, moved into a class (#207 stage 1). | – |

### extension/explayouts_ui (1)

[All patterns of extension/explayouts_ui](area-extension-explayouts-ui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Extension\ExplayoutsUi\Layouts` | class extends Command | `extension/explayouts_ui/classes/runnable/commands/php_layouts.php:17` | exp:layouts (ext:explayouts_ui:layouts) - list, export and import layouts, shared layouts and their mappings. | – |

### extension/expqueue (1)

[All patterns of extension/expqueue](area-extension-expqueue.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expQueueCommand` | class | `extension/expqueue/classes/expqueuecommand.php:17` | The commands ./console ext:expqueue: (extension/expqueue/bin/php/.php); each file only calls expQueueCommand::main( '' ). | – |

### extension/ezprestapi (1)

[All patterns of extension/ezprestapi](area-extension-ezprestapi.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Extension\Ezprestapi\Ezrestcall` | class extends Command | `extension/ezprestapi/classes/runnable/commands/php_ezrestcall.php:24` | The code of extension/ezprestapi/bin/php/ezrestcall.php, moved into a class (#207 stage 1). | – |

### extension/eztags (1)

[All patterns of extension/eztags](area-extension-eztags.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Extension\Eztags\Convertezkeyword` | class extends Command | `extension/eztags/classes/runnable/commands/php_convertezkeyword.php:10` | The code of extension/eztags/bin/php/convertezkeyword.php, moved into a class (#207 stage 1). | – |

### extension/ezupdate (1)

[All patterns of extension/ezupdate](area-extension-ezupdate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Extension\Ezupdate\Ezupdate` | class extends Command | `extension/ezupdate/classes/runnable/commands/php_ezupdate.php:25` | The code of extension/ezupdate/bin/php/ezupdate.php, moved into a class (#207 stage 1). | – |

### extension/git_manager (7)

[All patterns of extension/git_manager](area-extension-git-manager.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Extension\GitManager\BackupCreate` | class extends Command | `extension/git_manager/classes/runnable/commands/php_backup-create.php:46` | The code of extension/git_manager/bin/php/backup-create.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\GitManager\BackupDelete` | class extends Command | `extension/git_manager/classes/runnable/commands/php_backup-delete.php:34` | The code of extension/git_manager/bin/php/backup-delete.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\GitManager\BackupInfo` | class extends Command | `extension/git_manager/classes/runnable/commands/php_backup-info.php:28` | The code of extension/git_manager/bin/php/backup-info.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\GitManager\BackupList` | class extends Command | `extension/git_manager/classes/runnable/commands/php_backup-list.php:34` | The code of extension/git_manager/bin/php/backup-list.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\GitManager\Remote` | class extends Command | `extension/git_manager/classes/runnable/commands/php_remote.php:38` | The code of extension/git_manager/bin/php/remote.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\GitManager\Submodule` | class extends Command | `extension/git_manager/classes/runnable/commands/php_submodule.php:38` | The code of extension/git_manager/bin/php/submodule.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\GitManager\UpgradePolicyDumpToBackup` | class extends Command | `extension/git_manager/classes/runnable/commands/php_upgrade-policy-dump-to-backup.php:25` | The code of extension/git_manager/bin/php/upgrade-policy-dump-to-backup.php, moved into a class (#207 stage 1). | – |

### extension/sqliimport (1)

[All patterns of extension/sqliimport](area-extension-sqliimport.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expImportCommand` | class | `extension/sqliimport/classes/exp/expimportcommand.php:17` | The commands ./console ext:sqliimport: (extension/sqliimport/bin/php/.php); each file only calls expImportCommand::main( '' ). | – |

### extension/syndication (6)

[All patterns of extension/syndication](area-extension-syndication.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Extension\Syndication\Export` | class extends Command | `extension/syndication/classes/runnable/commands/php_export.php:14` | ext:syndication:export - write the export cache of the active feeds (the cronjob part export_feed, by hand). | – |
| `Exponential\Command\Extension\Syndication\Import` | class extends Command | `extension/syndication/classes/runnable/commands/php_import.php:15` | ext:syndication:import - fetch the item lists of the imports and import the waiting items (the cronjob part import_feed, by hand). | – |
| `Exponential\Command\Extension\Syndication\Install` | class extends Command | `extension/syndication/classes/runnable/commands/php_install.php:14` | ext:syndication:install - create the tables of the extension from share/db_schema.dba. | – |
| `Exponential\Command\Extension\Syndication\Status` | class extends Command | `extension/syndication/classes/runnable/commands/php_status.php:14` | ext:syndication:status - what is exported, what is imported, the last runs and the problems found. | – |
| `Exponential\Cronjob\Extension\Syndication\ExportFeed` | class extends CronjobPart | `extension/syndication/classes/runnable/cronjobs/php_export_feed.php:16` | Cronjob part export_feed: writes the export cache of every active feed. | – |
| `Exponential\Cronjob\Extension\Syndication\ImportFeed` | class extends CronjobPart | `extension/syndication/classes/runnable/cronjobs/php_import_feed.php:15` | Cronjob part import_feed: fetches the item list of every active import and imports the waiting items. | – |

### extension/xrowextract (10)

[All patterns of extension/xrowextract](area-extension-xrowextract.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Extension\Xrowextract\Archive` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_archive.php:30` | The code of extension/xrowextract/bin/php/archive.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\Xrowextract\Csv` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_csv.php:28` | The code of extension/xrowextract/bin/php/csv.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\Xrowextract\Destination` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_destination.php:31` | The code of extension/xrowextract/bin/php/destination.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\Xrowextract\History` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_history.php:23` | The code of extension/xrowextract/bin/php/history.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\Xrowextract\Import` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_import.php:30` | The code of extension/xrowextract/bin/php/import.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\Xrowextract\Job` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_job.php:27` | The code of extension/xrowextract/bin/php/job.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\Xrowextract\Package` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_package.php:46` | The code of extension/xrowextract/bin/php/package.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\Xrowextract\Requirements` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_requirements.php:29` | The code of extension/xrowextract/bin/php/requirements.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Extension\Xrowextract\Schedule` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_schedule.php:29` | The code of extension/xrowextract/bin/php/schedule.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Extension\Xrowextract\Xrowextract` | class extends CronjobPart | `extension/xrowextract/classes/runnable/cronjobs/xrowextract.php:25` | The code of extension/xrowextract/cronjobs/xrowextract.php, moved into a class (#207 stage 1). | – |

### extension/xrowmetadata (4)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Cronjob\Extension\Xrowmetadata\Archivesitemap` | class extends CronjobPart | `extension/xrowmetadata/classes/runnable/cronjobs/archivesitemap.php:11` | The code of extension/xrowmetadata/cronjobs/archivesitemap.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Extension\Xrowmetadata\Mobilesitemap` | class extends CronjobPart | `extension/xrowmetadata/classes/runnable/cronjobs/mobilesitemap.php:11` | The code of extension/xrowmetadata/cronjobs/mobilesitemap.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Extension\Xrowmetadata\Newssitemap` | class extends CronjobPart | `extension/xrowmetadata/classes/runnable/cronjobs/newssitemap.php:11` | The code of extension/xrowmetadata/cronjobs/newssitemap.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Extension\Xrowmetadata\Sitemap` | class extends CronjobPart | `extension/xrowmetadata/classes/runnable/cronjobs/sitemap.php:11` | The code of extension/xrowmetadata/cronjobs/sitemap.php, moved into a class (#207 stage 1). | – |

### kernel/classes/ini (1)

[All patterns of kernel/classes/ini](area-kernel-classes-ini.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expIniCommand` | class | `kernel/classes/ini/actions/expinicommand.php:15` | The exp:ini command without its script: splitting the command line, the help texts, finding the action in the registry, running it with an expIniComm… | – |

### kernel/private/classes/commands (103)

[All patterns of kernel/private/classes/commands](area-kernel-private-classes-commands.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Command\Kernel\Accesscheck` | class extends Command | `kernel/private/classes/commands/accesscheck.php:16` | – | – |
| `Exponential\Command\Kernel\Addanonymouspublicpolicies` | class extends Command | `kernel/private/classes/commands/addanonymouspublicpolicies.php:15` | The code of bin/php/addanonymouspublicpolicies.php. | – |
| `Exponential\Command\Kernel\Adddefaultstates` | class extends Command | `kernel/private/classes/commands/adddefaultstates.php:23` | The code of bin/php/adddefaultstates.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Addeditorpolicies` | class extends Command | `kernel/private/classes/commands/addeditorpolicies.php:15` | The code of bin/php/addeditorpolicies.php. | – |
| `Exponential\Command\Kernel\Addexphelpreadpolicy` | class extends Command | `kernel/private/classes/commands/addexphelpreadpolicy.php:16` | The code of bin/php/addexphelpreadpolicy.php. | – |
| `Exponential\Command\Kernel\Addmembermanagement` | class extends Command | `kernel/private/classes/commands/addmembermanagement.php:15` | The code of bin/php/addmembermanagement.php. | – |
| `Exponential\Command\Kernel\Addoauthauthorizepolicy` | class extends Command | `kernel/private/classes/commands/addoauthauthorizepolicy.php:15` | The code of bin/php/addoauthauthorizepolicy.php. | – |
| `Exponential\Command\Kernel\Adduserapproval` | class extends Command | `kernel/private/classes/commands/adduserapproval.php:15` | The code of bin/php/adduserapproval.php. | – |
| `Exponential\Command\Kernel\Audit` | class extends Command | `kernel/private/classes/commands/audit.php:26` | The audit command, exp:audit: the records of the audit channels and their hash chains. | – |
| `Exponential\Command\Kernel\Benchmark` | class extends Command | `kernel/private/classes/commands/benchmark.php:15` | The code of bin/php/benchmark.php (exp:benchmark). | – |
| `Exponential\Command\Kernel\Cache` | class extends Command | `kernel/private/classes/commands/cache.php:23` | The code of bin/php/cache.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Changelog2xmltext` | class extends Command | `kernel/private/classes/commands/changelog2xmltext.php:174` | The code of bin/php/changelog2xmltext.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Checkclasses` | class extends Command | `kernel/private/classes/commands/checkclasses.php:11` | The code of bin/php/checkclasses.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Checkdbfiles` | class extends Command | `kernel/private/classes/commands/checkdbfiles.php:149` | The code of bin/php/checkdbfiles.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Cleanuppolicies` | class extends Command | `kernel/private/classes/commands/cleanuppolicies.php:26` | The code of bin/php/cleanuppolicies.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Cleanuprss` | class extends Command | `kernel/private/classes/commands/cleanuprss.php:28` | The code of bin/php/cleanuprss.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Cleanupversions` | class extends Command | `kernel/private/classes/commands/cleanupversions.php:25` | The code of bin/php/cleanupversions.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Clusterize` | class extends Command | `kernel/private/classes/commands/clusterize.php:114` | The code of bin/php/clusterize.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Clusterpurge` | class extends Command | `kernel/private/classes/commands/clusterpurge.php:24` | The code of bin/php/clusterpurge.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Collaborationsampledata` | class extends Command | `kernel/private/classes/commands/collaborationsampledata.php:16` | – | – |
| `Exponential\Command\Kernel\Contentcheck` | class extends Command | `kernel/private/classes/commands/contentcheck.php:16` | – | – |
| `Exponential\Command\Kernel\Convertezenumtoezselection` | class extends Command | `kernel/private/classes/commands/convertezenumtoezselection.php:23` | The code of bin/php/convertezenumtoezselection.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Convertprice2multiprice` | class extends Command | `kernel/private/classes/commands/convertprice2multiprice.php:84` | The code of bin/php/convertprice2multiprice.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Dfscleanup` | class extends Command | `kernel/private/classes/commands/dfscleanup.php:35` | The code of bin/php/dfscleanup.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Expcontentjob` | class extends Command | `kernel/private/classes/commands/expcontentjob.php:30` | The command bin/php/expcontentjob.php (./console exp:expcontentjob): content jobs, the large subtree removes and copies that run in batches in the ba… | – |
| `Exponential\Command\Kernel\Exphelp` | class extends Command | `kernel/private/classes/commands/exphelp.php:16` | – | – |
| `Exponential\Command\Kernel\Exphelpcontent` | class extends Command | `kernel/private/classes/commands/exphelpcontent.php:16` | – | – |
| `Exponential\Command\Kernel\Exphelpexamples` | class extends Command | `kernel/private/classes/commands/exphelpexamples.php:16` | – | – |
| `Exponential\Command\Kernel\Explogs` | class extends Command | `kernel/private/classes/commands/explogs.php:21` | – | – |
| `Exponential\Command\Kernel\Exploremipsumfill` | class extends Command | `kernel/private/classes/commands/exploremipsumfill.php:20` | – | – |
| `Exponential\Command\Kernel\Explovestack` | class extends Command | `kernel/private/classes/commands/explovestack.php:16` | – | – |
| `Exponential\Command\Kernel\Ezapplytemplate` | class extends Command | `kernel/private/classes/commands/ezapplytemplate.php:24` | The code of bin/php/ezapplytemplate.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezasynchronouspublisher` | class extends Command | `kernel/private/classes/commands/ezasynchronouspublisher.php:43` | Signal handler | – |
| `Exponential\Command\Kernel\Ezcache` | class extends Command | `kernel/private/classes/commands/ezcache.php:24` | The code of bin/php/ezcache.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezcheckphptag` | class extends Command | `kernel/private/classes/commands/ezcheckphptag.php:24` | The code of bin/php/ezcheckphptag.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezchecktranslation` | class extends Command | `kernel/private/classes/commands/ezchecktranslation.php:132` | The code of bin/php/ezchecktranslation.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezcontentcache` | class extends Command | `kernel/private/classes/commands/ezcontentcache.php:24` | The code of bin/php/ezcontentcache.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezconvert2isbn13` | class extends Command | `kernel/private/classes/commands/ezconvert2isbn13.php:24` | The code of bin/php/ezconvert2isbn13.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezconvertdbcharset` | class extends Command | `kernel/private/classes/commands/ezconvertdbcharset.php:1468` | NOTE: What if other data is also in the db? | – |
| `Exponential\Command\Kernel\Ezconvertmysqltabletype` | class extends Command | `kernel/private/classes/commands/ezconvertmysqltabletype.php:231` | Connect to the database | – |
| `Exponential\Command\Kernel\Ezcreatedeactivatedusergroup` | class extends Command | `kernel/private/classes/commands/ezcreatedeactivatedusergroup.php:22` | Creates the user group for deactivated accounts of "Deactivate and move" (expUserDeactivation) ahead of first use, for the siteaccess given with -s (… | – |
| `Exponential\Command\Kernel\Ezcsvexport` | class extends Command | `kernel/private/classes/commands/ezcsvexport.php:24` | The code of bin/php/ezcsvexport.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezcsvimport` | class extends Command | `kernel/private/classes/commands/ezcsvimport.php:24` | The code of bin/php/ezcsvimport.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezexec` | class extends Command | `kernel/private/classes/commands/ezexec.php:24` | The code of bin/php/ezexec.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezflowupgrade` | class extends Command | `kernel/private/classes/commands/ezflowupgrade.php:199` | The code of bin/php/ezflowupgrade.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezgeneratetranslationcache` | class extends Command | `kernel/private/classes/commands/ezgeneratetranslationcache.php:25` | The code of bin/php/ezgeneratetranslationcache.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezimportdbafile` | class extends Command | `kernel/private/classes/commands/ezimportdbafile.php:24` | The code of bin/php/ezimportdbafile.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezpgenerateautoloads` | class extends Command | `kernel/private/classes/commands/ezpgenerateautoloads.php:25` | The code of bin/php/ezpgenerateautoloads.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezpm` | class extends Command | `kernel/private/classes/commands/ezpm.php:313` | The code of ezpm.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezpublishingbenchmark` | class extends Command | `kernel/private/classes/commands/ezpublishingbenchmark.php:24` | The code of bin/php/ezpublishingbenchmark.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezrequestrules` | class extends Command | `kernel/private/classes/commands/ezrequestrules.php:26` | The code of bin/php/ezrequestrules.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezsessiongc` | class extends Command | `kernel/private/classes/commands/ezsessiongc.php:24` | The code of bin/php/ezsessiongc.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezsqldiff` | class extends Command | `kernel/private/classes/commands/ezsqldiff.php:99` | The code of bin/php/ezsqldiff.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezsqldumpisbndata` | class extends Command | `kernel/private/classes/commands/ezsqldumpisbndata.php:24` | The code of bin/php/ezsqldumpisbndata.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezsqldumpschema` | class extends Command | `kernel/private/classes/commands/ezsqldumpschema.php:53` | The code of bin/php/ezsqldumpschema.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezsqlinsertschema` | class extends Command | `kernel/private/classes/commands/ezsqlinsertschema.php:53` | The code of bin/php/ezsqlinsertschema.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezsubtreecopy` | class extends Command | `kernel/private/classes/commands/ezsubtreecopy.php:271` | The code of bin/php/ezsubtreecopy.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezsubtreeremove` | class extends Command | `kernel/private/classes/commands/ezsubtreeremove.php:25` | The code of bin/php/ezsubtreeremove.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Eztc` | class extends Command | `kernel/private/classes/commands/eztc.php:24` | The code of bin/php/eztc.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Eztemplatecheck` | class extends Command | `kernel/private/classes/commands/eztemplatecheck.php:60` | The code of bin/php/eztemplatecheck.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezwebincommon` | class extends Command | `kernel/private/classes/commands/ezwebincommon.php:596` | The code of bin/php/ezwebincommon.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezwebininstall` | class extends Command | `kernel/private/classes/commands/ezwebininstall.php:25` | The code of bin/php/ezwebininstall.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Ezwebinupgrade` | class extends Command | `kernel/private/classes/commands/ezwebinupgrade.php:357` | The code of bin/php/ezwebinupgrade.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Flatten` | class extends Command | `kernel/private/classes/commands/flatten.php:42` | The code of bin/php/flatten.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Frankenphp` | class extends Command | `kernel/private/classes/commands/frankenphp.php:24` | The code of bin/php/frankenphp.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\I18n` | class extends Command | `kernel/private/classes/commands/i18n.php:18` | – | – |
| `Exponential\Command\Kernel\Ini` | class extends Command | `kernel/private/classes/commands/ini.php:18` | – | – |
| `Exponential\Command\Kernel\Install` | class extends Command | `kernel/private/classes/commands/install.php:29` | The code of bin/php/install.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Installcrontab` | class extends Command | `kernel/private/classes/commands/installcrontab.php:15` | The code of bin/php/installcrontab.php. | – |
| `Exponential\Command\Kernel\Kickstarter` | class extends Command | `kernel/private/classes/commands/kickstarter.php:153` | Runs this script again as a child with the same arguments and PHP settings, shows its output as it comes and copies it to var/log/kickstart.log, afte… | – |
| `Exponential\Command\Kernel\Mailbounces` | class extends Command | `kernel/private/classes/commands/mailbounces.php:16` | – | – |
| `Exponential\Command\Kernel\Mailconsent` | class extends Command | `kernel/private/classes/commands/mailconsent.php:16` | – | – |
| `Exponential\Command\Kernel\Mailgate` | class extends Command | `kernel/private/classes/commands/mailgate.php:16` | – | – |
| `Exponential\Command\Kernel\Mailpreferences` | class extends Command | `kernel/private/classes/commands/mailpreferences.php:16` | – | – |
| `Exponential\Command\Kernel\Mailstatus` | class extends Command | `kernel/private/classes/commands/mailstatus.php:16` | – | – |
| `Exponential\Command\Kernel\Mailsuppression` | class extends Command | `kernel/private/classes/commands/mailsuppression.php:16` | – | – |
| `Exponential\Command\Kernel\Maintenance` | class extends Command | `kernel/private/classes/commands/maintenance.php:34` | The code of bin/php/maintenance.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Makestaticcache` | class extends Command | `kernel/private/classes/commands/makestaticcache.php:24` | The code of bin/php/makestaticcache.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Notificationevents` | class extends Command | `kernel/private/classes/commands/notificationevents.php:17` | – | – |
| `Exponential\Command\Kernel\Notificationrun` | class extends Command | `kernel/private/classes/commands/notificationrun.php:17` | – | – |
| `Exponential\Command\Kernel\Notificationstatus` | class extends Command | `kernel/private/classes/commands/notificationstatus.php:17` | – | – |
| `Exponential\Command\Kernel\Notificationsubscriptions` | class extends Command | `kernel/private/classes/commands/notificationsubscriptions.php:17` | – | – |
| `Exponential\Command\Kernel\Phar` | class extends Command | `kernel/private/classes/commands/phar.php:39` | The code of bin/php/phar.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Preload` | class extends Command | `kernel/private/classes/commands/preload.php:35` | The preload from the shell and from cron. | – |
| `Exponential\Command\Kernel\Preloadjob` | class extends Command | `kernel/private/classes/commands/preloadjob.php:33` | The code of bin/php/preloadjob.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\PublishContent` | class extends Command | `kernel/private/classes/commands/publish_content.php:25` | The code of bin/php/publish_content.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Resetuserpassword` | class extends Command | `kernel/private/classes/commands/resetuserpassword.php:27` | The code of bin/php/resetuserpassword.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Runcronjobs` | class extends Command | `kernel/private/classes/commands/runcronjobs.php:91` | The code of runcronjobs.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Services` | class extends Command | `kernel/private/classes/commands/services.php:19` | The code of bin/php/services.php: the kernel service container on the command line. | – |
| `Exponential\Command\Kernel\Solr` | class extends Command | `kernel/private/classes/commands/solr.php:190` | Report a result for a person or as JSON, and shut down with $exit. | – |
| `Exponential\Command\Kernel\Syscard` | class extends Command | `kernel/private/classes/commands/syscard.php:17` | – | – |
| `Exponential\Command\Kernel\Systeminfo` | class extends Command | `kernel/private/classes/commands/systeminfo.php:16` | – | – |
| `Exponential\Command\Kernel\Trashpurge` | class extends Command | `kernel/private/classes/commands/trashpurge.php:24` | The code of bin/php/trashpurge.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Updateisbn13` | class extends Command | `kernel/private/classes/commands/updateisbn13.php:24` | The code of bin/php/updateisbn13.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Updateniceurls` | class extends Command | `kernel/private/classes/commands/updateniceurls.php:338` | The code of bin/php/updateniceurls.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Updatesearchindex` | class extends Command | `kernel/private/classes/commands/updatesearchindex.php:42` | The code of bin/php/updatesearchindex.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Userlogincheck` | class extends Command | `kernel/private/classes/commands/userlogincheck.php:18` | – | – |
| `Exponential\Command\Kernel\Userpasswordchangerequired` | class extends Command | `kernel/private/classes/commands/userpasswordchangerequired.php:16` | – | – |
| `Exponential\Command\Kernel\Velocity` | class extends Command | `kernel/private/classes/commands/velocity.php:262` | Print a settings listing for a person to read. | – |
| `Exponential\Command\Kernel\VelocityWarmup` | class extends Command | `kernel/private/classes/commands/velocity-warmup.php:40` | The code of bin/php/velocity-warmup.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\VerifyAliases` | class extends Command | `kernel/private/classes/commands/verify_aliases.php:43` | The code of bin/php/verify_aliases.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Warm` | class extends Command | `kernel/private/classes/commands/warm.php:34` | The code of bin/php/warm.php, moved into a class (#207 stage 1). | – |
| `Exponential\Command\Kernel\Webserver` | class extends Command | `kernel/private/classes/commands/webserver.php:170` | Report a configure result for a person or as JSON. | – |

### kernel/private/classes/cronjobs (30)

[All patterns of kernel/private/classes/cronjobs](area-kernel-private-classes-cronjobs.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Cronjob\Kernel\Audit` | class extends CronjobPart | `kernel/private/classes/cronjobs/audit.php:17` | The cronjob part audit (cronjobs/audit.php; [CronjobPart-audit] and the frequent group in cronjob.ini): the audit's scheduled work, expAuditMaintenan… | – |
| `Exponential\Cronjob\Kernel\BasketCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/basket_cleanup.php:25` | The code of cronjobs/basket_cleanup.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Cachecleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/cachecleanup.php:59` | The code of cronjobs/cachecleanup.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Cleanuprss` | class extends CronjobPart | `kernel/private/classes/cronjobs/cleanuprss.php:29` | The code of cronjobs/cleanuprss.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Clusterpurge` | class extends CronjobPart | `kernel/private/classes/cronjobs/clusterpurge.php:25` | The code of cronjobs/clusterpurge.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Contentcheck` | class extends CronjobPart | `kernel/private/classes/cronjobs/contentcheck.php:18` | The code of cronjobs/contentcheck.php: builds the reference index of the content checks and keeps it in the cache (expContentCheckReferences). | – |
| `Exponential\Cronjob\Kernel\Contentjobs` | class extends CronjobPart | `kernel/private/classes/cronjobs/contentjobs.php:21` | The cronjob part contentjobs (cronjobs/contentjobs.php, frequent group): starts the content jobs that need a worker and have none — a running job who… | – |
| `Exponential\Cronjob\Kernel\Datatablesync` | class extends CronjobPart | `kernel/private/classes/cronjobs/datatablesync.php:16` | The cronjob part datatablesync (cronjobs/datatablesync.php, frequent group; doc/eng-us/features/6.0/data-tables.md, "Synchronisation"): starts each e… | – |
| `Exponential\Cronjob\Kernel\Exphelp` | class extends CronjobPart | `kernel/private/classes/cronjobs/exphelp.php:15` | – | – |
| `Exponential\Cronjob\Kernel\Explogrotate` | class extends CronjobPart | `kernel/private/classes/cronjobs/explogrotate.php:19` | The code of cronjobs/explogrotate.php: the log rotation cronjob part ([CronjobPart-logs], in no default part, so it runs only when a site adds it to… | – |
| `Exponential\Cronjob\Kernel\Hide` | class extends CronjobPart | `kernel/private/classes/cronjobs/hide.php:25` | The code of cronjobs/hide.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\HttpcacheCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/httpcache_cleanup.php:23` | The code of cronjobs/httpcache_cleanup.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Indexcontent` | class extends CronjobPart | `kernel/private/classes/cronjobs/indexcontent.php:25` | The code of cronjobs/indexcontent.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\InternalDraftsCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/internal_drafts_cleanup.php:25` | The code of cronjobs/internal_drafts_cleanup.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Linkcheck` | class extends CronjobPart | `kernel/private/classes/cronjobs/linkcheck.php:26` | The code of cronjobs/linkcheck.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Mailbounces` | class extends CronjobPart | `kernel/private/classes/cronjobs/mailbounces.php:15` | – | – |
| `Exponential\Cronjob\Kernel\Mailpreferences` | class extends CronjobPart | `kernel/private/classes/cronjobs/mailpreferences.php:15` | – | – |
| `Exponential\Cronjob\Kernel\Notification` | class extends CronjobPart | `kernel/private/classes/cronjobs/notification.php:25` | The code of cronjobs/notification.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\OldDraftsCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/old_drafts_cleanup.php:25` | The code of cronjobs/old_drafts_cleanup.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\QuerycacheCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/querycache_cleanup.php:18` | The code of cronjobs/querycache_cleanup.php. | – |
| `Exponential\Cronjob\Kernel\Rssimport` | class extends CronjobPart | `kernel/private/classes/cronjobs/rssimport.php:407` | The code of cronjobs/rssimport.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\SessionGc` | class extends CronjobPart | `kernel/private/classes/cronjobs/session_gc.php:25` | The code of cronjobs/session_gc.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\StaticcacheCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/staticcache_cleanup.php:25` | The code of cronjobs/staticcache_cleanup.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Subtreeexpirycleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/subtreeexpirycleanup.php:25` | The code of cronjobs/subtreeexpirycleanup.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Trashpurge` | class extends CronjobPart | `kernel/private/classes/cronjobs/trashpurge.php:25` | The code of cronjobs/trashpurge.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Unlock` | class extends CronjobPart | `kernel/private/classes/cronjobs/unlock.php:93` | The code of cronjobs/unlock.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Unpublish` | class extends CronjobPart | `kernel/private/classes/cronjobs/unpublish.php:25` | The code of cronjobs/unpublish.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Updateviewcount` | class extends CronjobPart | `kernel/private/classes/cronjobs/updateviewcount.php:25` | The code of cronjobs/updateviewcount.php, moved into a class (#207 stage 1). | – |
| `Exponential\Cronjob\Kernel\Usersessions` | class extends CronjobPart | `kernel/private/classes/cronjobs/usersessions.php:15` | – | – |
| `Exponential\Cronjob\Kernel\Workflow` | class extends CronjobPart | `kernel/private/classes/cronjobs/workflow.php:25` | The code of cronjobs/workflow.php, moved into a class (#207 stage 1). | – |

### kernel/private/classes/runnable (3)

[All patterns of kernel/private/classes/runnable](area-kernel-private-classes-runnable.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `Exponential\Runnable\Command` | abstract class extends Runnable | `kernel/private/classes/runnable/command.php:36` | A CLI command. | – |
| `Exponential\Runnable\CronjobPart` | abstract class extends Runnable | `kernel/private/classes/runnable/cronjobpart.php:21` | A cronjob part. | – |
| `Exponential\Runnable\ModuleView` | abstract class extends Runnable | `kernel/private/classes/runnable/moduleview.php:23` | A module view. | – |

## Cronjob parts (49)

### extension/bccie (4)

| Cronjob | File | Description |
|---|---|---|
| `exportcsv` | `extension/bccie/classes/runnable/cronjobs/exportcsv.php` | – |
| `exportsylk` | `extension/bccie/classes/runnable/cronjobs/exportsylk.php` | – |
| `exportcsv` | `extension/bccie/cronjobs/exportcsv.php` | – |
| `exportsylk` | `extension/bccie/cronjobs/exportsylk.php` | – |

### extension/bcgooglesitemaps (4)

| Cronjob | File | Description |
|---|---|---|
| `generate` | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generate.php` | The code of extension/bcgooglesitemaps/cronjobs/generate.php, moved into a class (#207 stage 1). |
| `generatemultilingual` | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generatemultilingual.php` | The code of extension/bcgooglesitemaps/cronjobs/generatemultilingual.php, moved into a class (#207 stage 1). |
| `generate` | `extension/bcgooglesitemaps/cronjobs/generate.php` | – |
| `generatemultilingual` | `extension/bcgooglesitemaps/cronjobs/generatemultilingual.php` | – |

### extension/cjw_newsletter (6)

| Cronjob | File | Description |
|---|---|---|
| `cjw_newsletter_mailbox` | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailbox.php` | The code of extension/cjw_newsletter/cronjobs/cjw_newsletter_mailbox.php, as a class (#207). |
| `cjw_newsletter_mailqueue_create` | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailqueue_create.php` | The code of extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_create.php, moved into a class (#207 stage 1). |
| `cjw_newsletter_mailqueue_process` | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailqueue_process.php` | The code of extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_process.php, moved into a class (#207 stage 1). |
| `cjw_newsletter_mailbox` | `extension/cjw_newsletter/cronjobs/cjw_newsletter_mailbox.php` | Cronjob cjw_newsletter_mailbox.php |
| `cjw_newsletter_mailqueue_create` | `extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_create.php` | Cronjob cjw_newsletter_mailqueue_create.php |
| `cjw_newsletter_mailqueue_process` | `extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_process.php` | Cronjob cjw_newsletter_create_mailqueue.php |

### extension/cjw_tmv (1)

| Cronjob | File | Description |
|---|---|---|
| `cjw_tmv_refresh` | `extension/cjw_tmv/cronjobs/cjw_tmv_refresh.php` | Cronjob part cjw_tmv: imports and updates the TMV events of every tmv_container as content objects (cjwTmvImporter), and removes the past ones. |

### extension/expai (4)

| Cronjob | File | Description |
|---|---|---|
| `expai_exchange_rates` | `extension/expai/cronjobs/expai_exchange_rates.php` | Cronjob expai_exchange_rates: refreshes the reference rates of the European Central Bank once a day (the conversion of prices in another currency, ex… |
| `expai_prune` | `extension/expai/cronjobs/expai_prune.php` | Cronjob expai_prune: marks draft log rows whose content object was removed as "deleted"; removes old threads and conversations, blanks old request su… |
| `expai_queue` | `extension/expai/cronjobs/expai_queue.php` | Cronjob expai_queue (part "expai"): resumes the queued requests a budget held, as soon as they fit (a new month, a raised budget, requests that cost… |
| `expai_thread_prune` | `extension/expai/cronjobs/expai_thread_prune.php` | Cronjob expai_thread_prune (part "expai"): removes thread events, staged proposals, checkpoints and finished plans and follow-ups older than expai.in… |

### extension/expqueue (1)

| Cronjob | File | Description |
|---|---|---|
| `expqueue` | `extension/expqueue/cronjobs/expqueue.php` | The cron part "expqueue" (php runcronjobs.php -q expqueue, every minute): the default worker of the queue and the safety net next to a systemd servic… |

### extension/ezfind (2)

| Cronjob | File | Description |
|---|---|---|
| `ezfindexsubtree` | `extension/ezfind/cronjobs/ezfindexsubtree.php` | – |
| `ezfoptimizeindex` | `extension/ezfind/cronjobs/ezfoptimizeindex.php` | – |

### extension/ezflow (2)

| Cronjob | File | Description |
|---|---|---|
| `ezflowcleanup` | `extension/ezflow/cronjobs/ezflowcleanup.php` | – |
| `ezflowupdate` | `extension/ezflow/cronjobs/ezflowupdate.php` | – |

### extension/ezmbpaex (2)

| Cronjob | File | Description |
|---|---|---|
| `ezmbpaex_sendexpirynotifications` | `extension/ezmbpaex/cronjobs/ezmbpaex_sendexpirynotifications.php` | – |
| `ezmbpaex_updatechildren` | `extension/ezmbpaex/cronjobs/ezmbpaex_updatechildren.php` | – |

### extension/ezodf (1)

| Cronjob | File | Description |
|---|---|---|
| `ezodf_keepalive` | `extension/ezodf/cronjobs/ezodf_keepalive.php` | Cron part of ezodf: keeps the conversion server running when the administration turned it on (after a reboot or a crash), and removes work files, upl… |

### extension/ezoracle (4)

| Cronjob | File | Description |
|---|---|---|
| `ezoracle_health` | `extension/ezoracle/cronjobs/ezoracle_health.php` | – |
| `ezoracle_recyclebin` | `extension/ezoracle/cronjobs/ezoracle_recyclebin.php` | – |
| `ezoracle_sequences` | `extension/ezoracle/cronjobs/ezoracle_sequences.php` | – |
| `ezoracle_statistics` | `extension/ezoracle/cronjobs/ezoracle_statistics.php` | – |

### extension/sevenx_authentication_2fa (1)

| Cronjob | File | Description |
|---|---|---|
| `sevenx2facleanup` | `extension/sevenx_authentication_2fa/cronjobs/sevenx2facleanup.php` | – |

### extension/sqliimport (3)

| Cronjob | File | Description |
|---|---|---|
| `sqliimport_cleanup` | `extension/sqliimport/cronjobs/sqliimport_cleanup.php` | SQLi Import cleanup cronjob Will clear imported content objects caches if needed Will trigger imported content objects indexing if needed |
| `sqliimport_run` | `extension/sqliimport/cronjobs/sqliimport_run.php` | Cronjob running scheduled and "one shot" imports as configured in the admin |
| `sqliimport_schedule` | `extension/sqliimport/cronjobs/sqliimport_schedule.php` | The cron part "sqliimport" (php runcronjobs.php -q sqliimport, every minute): queues the runs of the schedules that are due and the pending imports o… |

### extension/syndication (4)

| Cronjob | File | Description |
|---|---|---|
| `php_export_feed` | `extension/syndication/classes/runnable/cronjobs/php_export_feed.php` | – |
| `php_import_feed` | `extension/syndication/classes/runnable/cronjobs/php_import_feed.php` | – |
| `syndication_export` | `extension/syndication/cronjobs/syndication_export.php` | Cronjob part export_feed of the syndication extension. |
| `syndication_import` | `extension/syndication/cronjobs/syndication_import.php` | Cronjob part import_feed of the syndication extension. |

### extension/xrowextract (2)

| Cronjob | File | Description |
|---|---|---|
| `xrowextract` | `extension/xrowextract/classes/runnable/cronjobs/xrowextract.php` | The code of extension/xrowextract/cronjobs/xrowextract.php, moved into a class (#207 stage 1). |
| `xrowextract` | `extension/xrowextract/cronjobs/xrowextract.php` | The xrowextract cronjob part (runcronjobs.php xrowextract): starts every due schedule as a background job through the same runner "Run in the backgro… |

### extension/xrowmetadata (8)

| Cronjob | File | Description |
|---|---|---|
| `archivesitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/archivesitemap.php` | The code of extension/xrowmetadata/cronjobs/archivesitemap.php, moved into a class (#207 stage 1). |
| `mobilesitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/mobilesitemap.php` | The code of extension/xrowmetadata/cronjobs/mobilesitemap.php, moved into a class (#207 stage 1). |
| `newssitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/newssitemap.php` | The code of extension/xrowmetadata/cronjobs/newssitemap.php, moved into a class (#207 stage 1). |
| `sitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/sitemap.php` | The code of extension/xrowmetadata/cronjobs/sitemap.php, moved into a class (#207 stage 1). |
| `archivesitemap` | `extension/xrowmetadata/cronjobs/archivesitemap.php` | – |
| `mobilesitemap` | `extension/xrowmetadata/cronjobs/mobilesitemap.php` | – |
| `newssitemap` | `extension/xrowmetadata/cronjobs/newssitemap.php` | – |
| `sitemap` | `extension/xrowmetadata/cronjobs/sitemap.php` | – |
