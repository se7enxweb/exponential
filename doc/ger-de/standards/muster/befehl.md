---
title: Befehl (Konsole, Skripte, Cronjobs)
audience: developer
category: standards
order: 319
summary: "Eine Arbeitseinheit, die eigenständig läuft: ein Konsolenbefehl (./console exp:...), eine CLI-Skriptklasse oder ein Cronjob-Teil. 221 Instanzen in Exponential."
keywords: Entwurfsmuster, Befehl, Anfrageverarbeitung, Musterkatalog, design pattern, command, pattern catalogue
type: catalogue
---
# Befehl (Konsole, Skripte, Cronjobs)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Eine Arbeitseinheit, die eigenständig läuft: ein Konsolenbefehl (./console exp:...), eine CLI-Skriptklasse oder ein Cronjob-Teil.

|  |  |
| --- | --- |
| Gruppe | Verarbeitung von Anfragen |
| Instanzen | 221 |
| Erkannt an | Erweitert Command, CronjobPart oder Runnable (die ausführbaren Konsolenbefehle und Cronjob-Teile), oder eine Datei cronjobs/*.php. |
| Erweiterungspunkt | kernel/private/classes/commands/, cronjob.ini [CronjobSettings] ExtensionDirectories[] und CronjobPart-*. |

## Klassen nach Bereich

### extension/bccie (5)

[Alle Muster von extension/bccie](bereich-extension-bccie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Bccie\Export` | class extends Command | `extension/bccie/classes/runnable/commands/php_export.php:16` | ext:bccie:export - die gesammelten Informationen eines Formulars in eine CSV- oder SYLK-Datei exportieren oder den geplanten Export ausführen (die Cronjob-Teile exportcsv und exp… | – |
| `Exponential\Command\Extension\Bccie\Purge` | class extends Command | `extension/bccie/classes/runnable/commands/php_purge.php:14` | ext:bccie:purge - die gesammelten Informationen eines Formulars entfernen, alle oder die, die älter als ein Datum sind. | – |
| `Exponential\Command\Extension\Bccie\Status` | class extends Command | `extension/bccie/classes/runnable/commands/php_status.php:15` | ext:bccie:status - das Dashboard der Admin als Text: die Formulare mit gesammelten Informationen, die letzten Exporte und die gefundenen Probleme. | – |
| `Exponential\Cronjob\Extension\Bccie\Exportcsv` | class extends CronjobPart | `extension/bccie/classes/runnable/cronjobs/exportcsv.php:16` | Cronjob-Teil exportcsv: schreibt die gesammelten Informationen der Objekte in [CieSettings] Collection[] als CSV-Dateien in [CieSettings] Directory. | – |
| `Exponential\Cronjob\Extension\Bccie\Exportsylk` | class extends CronjobPart | `extension/bccie/classes/runnable/cronjobs/exportsylk.php:16` | Cronjob-Teil exportsylk: schreibt die gesammelten Informationen der Objekte in [CieSettings] Collection[] als SYLK-Dateien in [CieSettings] Directory. | – |

### extension/bcgooglesitemaps (2)

[Alle Muster von extension/bcgooglesitemaps](bereich-extension-bcgooglesitemaps.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Cronjob\Extension\Bcgooglesitemaps\Generate` | class extends CronjobPart | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generate.php:49` | Der Code von extension/bcgooglesitemaps/cronjobs/generate.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Extension\Bcgooglesitemaps\Generatemultilingual` | class extends CronjobPart | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generatemultilingual.php:88` | Den Benutzer darauf hinweisen, wie lange die Skriptausführung gedauert hat * | – |

### extension/cjw_newsletter (16)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\CjwNewsletter\ClassLanguages` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_class_languages.php:15` | ext:cjw_newsletter:class-languages: verschiebt die Newsletter-Inhaltsklassen von eng-GB nach eng-US (Upgrade-Schritt 4.2.1, idempotent). | – |
| `Exponential\Command\Extension\CjwNewsletter\Createoutput` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_createoutput.php:276` | Alle a-Tags als Textlinks formatieren, alle Anker-Tags entfernen | – |
| `Exponential\Command\Extension\CjwNewsletter\Deliverability` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_deliverability.php:21` | ext:cjw_newsletter:deliverability - der Bereich "deliverability" von cjw_newsletter 4.2.0 auf der Konsole. | – |
| `Exponential\Command\Extension\CjwNewsletter\Import` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_import.php:14` | ext:cjw_newsletter:import - die Zeilen einer hochgeladenen CSV-Datei importieren (der Import von "Import all" der Admin) oder eine Datei per Pfad. | – |
| `Exponential\Command\Extension\CjwNewsletter\ImportEznewsletter` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_import_eznewsletter.php:15` | ext:cjw_newsletter:import-eznewsletter - die Listen, Abonnenten und Abonnements einer alten eznewsletter-Installation in cjw_newsletter übernehmen. | – |
| `Exponential\Command\Extension\CjwNewsletter\Iniloader` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_iniloader.php:29` | Der Code von extension/cjw_newsletter/bin/php/iniloader.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\CjwNewsletter\Mailbox` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_mailbox.php:14` | ext:cjw_newsletter:mailbox - die Mails der aktiven Mailkonten abholen und auswerten (der Cronjob-Teil cjw_newsletter_mailbox, von Hand). | – |
| `Exponential\Command\Extension\CjwNewsletter\Queue` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_queue.php:15` | ext:cjw_newsletter:queue - die Mail-Warteschlange anlegen und die Mails senden (die Cronjob-Teile cjw_newsletter_mailqueue_create und cjw_newsletter_mailqueue_p… | – |
| `Exponential\Command\Extension\CjwNewsletter\Repair` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_repair.php:14` | ext:cjw_newsletter:repair - die verwaisten Einträge entfernen, die die Startseite meldet. | – |
| `Exponential\Command\Extension\CjwNewsletter\Schedule` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_schedule.php:20` | ext:cjw_newsletter:schedule - die wiederkehrenden Versände. | – |
| `Exponential\Command\Extension\CjwNewsletter\Statistics` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_statistics.php:21` | ext:cjw_newsletter:statistics - die Newsletter-Statistik (cjw_newsletter 4.2.0, Bereich N4 Statistics). | – |
| `Exponential\Command\Extension\CjwNewsletter\Status` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_status.php:14` | ext:cjw_newsletter:status - was die Startseite des Newsletter-Moduls zeigt. | – |
| `Exponential\Command\Extension\CjwNewsletter\TranslatableFields` | class extends Command | `extension/cjw_newsletter/classes/runnable/commands/php_translatable_fields.php:14` | ext:cjw_newsletter:translatable-fields: macht die Textfelder der Newsletter-Klassen übersetzbar (Upgrade-Schritt 4.2.0, idempotent). | – |
| `Exponential\Cronjob\Extension\CjwNewsletter\CjwNewsletterMailbox` | class extends CronjobPart | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailbox.php:14` | Der Code von extension/cjw_newsletter/cronjobs/cjw_newsletter_mailbox.php, als Klasse (#207). | – |
| `Exponential\Cronjob\Extension\CjwNewsletter\CjwNewsletterMailqueueCreate` | class extends CronjobPart | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailqueue_create.php:36` | Der Code von extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_create.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Extension\CjwNewsletter\CjwNewsletterMailqueueProcess` | class extends CronjobPart | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailqueue_process.php:33` | Der Code von extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_process.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/enhancedselection2 (2)

[Alle Muster von extension/enhancedselection2](bereich-extension-enhancedselection2.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Enhancedselection2\MigrateToDatabase` | class extends Command | `extension/enhancedselection2/classes/runnable/commands/php_migrate_to_database.php:11` | Der Code von extension/enhancedselection2/bin/php/migrate_to_database.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\Enhancedselection2\Updateenhancedselection` | class extends Command | `extension/enhancedselection2/classes/runnable/commands/php_updateenhancedselection.php:10` | Der Code von extension/enhancedselection2/bin/php/updateenhancedselection.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/explayouts (3)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsSiteBundleCommand` | class | `extension/explayouts/classes/explayoutssitebundlecommand.php:4` | – | – |
| `Exponential\Command\Extension\Explayouts\Updatecomponentblockidentifiers` | class extends Command | `extension/explayouts/classes/runnable/commands/php_updatecomponentblockidentifiers.php:33` | Der Code von extension/explayouts/bin/php/updatecomponentblockidentifiers.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\Explayouts\Updatelinklocationreferences` | class extends Command | `extension/explayouts/classes/runnable/commands/php_updatelinklocationreferences.php:41` | Der Code von extension/explayouts/bin/php/updatelinklocationreferences.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/explayouts_ui (1)

[Alle Muster von extension/explayouts_ui](bereich-extension-explayouts-ui.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\ExplayoutsUi\Layouts` | class extends Command | `extension/explayouts_ui/classes/runnable/commands/php_layouts.php:17` | exp:layouts (ext:explayouts_ui:layouts) - Layouts, geteilte Layouts und ihre Zuordnungen auflisten, exportieren und importieren. | – |

### extension/ezprestapi (1)

[Alle Muster von extension/ezprestapi](bereich-extension-ezprestapi.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Ezprestapi\Ezrestcall` | class extends Command | `extension/ezprestapi/classes/runnable/commands/php_ezrestcall.php:24` | Der Code von extension/ezprestapi/bin/php/ezrestcall.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/eztags (1)

[Alle Muster von extension/eztags](bereich-extension-eztags.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Eztags\Convertezkeyword` | class extends Command | `extension/eztags/classes/runnable/commands/php_convertezkeyword.php:10` | Der Code von extension/eztags/bin/php/convertezkeyword.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/ezupdate (1)

[Alle Muster von extension/ezupdate](bereich-extension-ezupdate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Ezupdate\Ezupdate` | class extends Command | `extension/ezupdate/classes/runnable/commands/php_ezupdate.php:25` | Der Code von extension/ezupdate/bin/php/ezupdate.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/git_manager (7)

[Alle Muster von extension/git_manager](bereich-extension-git-manager.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\GitManager\BackupCreate` | class extends Command | `extension/git_manager/classes/runnable/commands/php_backup-create.php:46` | Der Code von extension/git_manager/bin/php/backup-create.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\GitManager\BackupDelete` | class extends Command | `extension/git_manager/classes/runnable/commands/php_backup-delete.php:34` | Der Code von extension/git_manager/bin/php/backup-delete.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\GitManager\BackupInfo` | class extends Command | `extension/git_manager/classes/runnable/commands/php_backup-info.php:28` | Der Code von extension/git_manager/bin/php/backup-info.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\GitManager\BackupList` | class extends Command | `extension/git_manager/classes/runnable/commands/php_backup-list.php:34` | Der Code von extension/git_manager/bin/php/backup-list.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\GitManager\Remote` | class extends Command | `extension/git_manager/classes/runnable/commands/php_remote.php:38` | Der Code von extension/git_manager/bin/php/remote.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\GitManager\Submodule` | class extends Command | `extension/git_manager/classes/runnable/commands/php_submodule.php:38` | Der Code von extension/git_manager/bin/php/submodule.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\GitManager\UpgradePolicyDumpToBackup` | class extends Command | `extension/git_manager/classes/runnable/commands/php_upgrade-policy-dump-to-backup.php:25` | Der Code von extension/git_manager/bin/php/upgrade-policy-dump-to-backup.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/syndication (6)

[Alle Muster von extension/syndication](bereich-extension-syndication.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Syndication\Export` | class extends Command | `extension/syndication/classes/runnable/commands/php_export.php:14` | ext:syndication:export - den Export-Cache der aktiven Feeds schreiben (der Cronjob-Teil export_feed, von Hand). | – |
| `Exponential\Command\Extension\Syndication\Import` | class extends Command | `extension/syndication/classes/runnable/commands/php_import.php:15` | ext:syndication:import - die Elementlisten der Importe abrufen und die wartenden Elemente importieren (der Cronjob-Teil import_feed, von Hand). | – |
| `Exponential\Command\Extension\Syndication\Install` | class extends Command | `extension/syndication/classes/runnable/commands/php_install.php:14` | ext:syndication:install - die Tabellen der Extension aus share/db_schema.dba anlegen. | – |
| `Exponential\Command\Extension\Syndication\Status` | class extends Command | `extension/syndication/classes/runnable/commands/php_status.php:14` | ext:syndication:status - was exportiert wird, was importiert wird, die letzten Läufe und die gefundenen Probleme. | – |
| `Exponential\Cronjob\Extension\Syndication\ExportFeed` | class extends CronjobPart | `extension/syndication/classes/runnable/cronjobs/php_export_feed.php:16` | Cronjob-Teil export_feed: schreibt den Export-Cache jedes aktiven Feeds. | – |
| `Exponential\Cronjob\Extension\Syndication\ImportFeed` | class extends CronjobPart | `extension/syndication/classes/runnable/cronjobs/php_import_feed.php:15` | Cronjob-Teil import_feed: ruft die Eintragsliste jedes aktiven Imports ab und importiert die wartenden Einträge. | – |

### extension/xrowextract (10)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Xrowextract\Archive` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_archive.php:30` | Der Code von extension/xrowextract/bin/php/archive.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\Xrowextract\Csv` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_csv.php:28` | Der Code von extension/xrowextract/bin/php/csv.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\Xrowextract\Destination` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_destination.php:31` | Der Code von extension/xrowextract/bin/php/destination.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\Xrowextract\History` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_history.php:23` | Der Code von extension/xrowextract/bin/php/history.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\Xrowextract\Import` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_import.php:30` | Der Code von extension/xrowextract/bin/php/import.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\Xrowextract\Job` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_job.php:27` | Der Code von extension/xrowextract/bin/php/job.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\Xrowextract\Package` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_package.php:46` | Der Code von extension/xrowextract/bin/php/package.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\Xrowextract\Requirements` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_requirements.php:29` | Der Code von extension/xrowextract/bin/php/requirements.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Extension\Xrowextract\Schedule` | class extends Command | `extension/xrowextract/classes/runnable/commands/php_schedule.php:29` | Der Code von extension/xrowextract/bin/php/schedule.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Extension\Xrowextract\Xrowextract` | class extends CronjobPart | `extension/xrowextract/classes/runnable/cronjobs/xrowextract.php:25` | Der Code von extension/xrowextract/cronjobs/xrowextract.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### extension/xrowmetadata (4)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Cronjob\Extension\Xrowmetadata\Archivesitemap` | class extends CronjobPart | `extension/xrowmetadata/classes/runnable/cronjobs/archivesitemap.php:11` | Der Code von extension/xrowmetadata/cronjobs/archivesitemap.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Extension\Xrowmetadata\Mobilesitemap` | class extends CronjobPart | `extension/xrowmetadata/classes/runnable/cronjobs/mobilesitemap.php:11` | Der Code von extension/xrowmetadata/cronjobs/mobilesitemap.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Extension\Xrowmetadata\Newssitemap` | class extends CronjobPart | `extension/xrowmetadata/classes/runnable/cronjobs/newssitemap.php:11` | Der Code von extension/xrowmetadata/cronjobs/newssitemap.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Extension\Xrowmetadata\Sitemap` | class extends CronjobPart | `extension/xrowmetadata/classes/runnable/cronjobs/sitemap.php:11` | Der Code von extension/xrowmetadata/cronjobs/sitemap.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### kernel/classes/ini (1)

[Alle Muster von kernel/classes/ini](bereich-kernel-classes-ini.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expIniCommand` | class | `kernel/classes/ini/actions/expinicommand.php:15` | Der Befehl exp:ini ohne sein Skript: Zerlegen der Befehlszeile, die Hilfetexte, Finden der Aktion in der Registry, Ausführen mit einem expIniComm… | – |

### kernel/private/classes/commands (95)

[Alle Muster von kernel/private/classes/commands](bereich-kernel-private-classes-commands.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Kernel\Accesscheck` | class extends Command | `kernel/private/classes/commands/accesscheck.php:16` | – | – |
| `Exponential\Command\Kernel\Adddefaultstates` | class extends Command | `kernel/private/classes/commands/adddefaultstates.php:23` | Der Code von bin/php/adddefaultstates.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Addexphelpreadpolicy` | class extends Command | `kernel/private/classes/commands/addexphelpreadpolicy.php:16` | Der Code von bin/php/addexphelpreadpolicy.php. | – |
| `Exponential\Command\Kernel\Addoauthauthorizepolicy` | class extends Command | `kernel/private/classes/commands/addoauthauthorizepolicy.php:15` | Der Code von bin/php/addoauthauthorizepolicy.php. | – |
| `Exponential\Command\Kernel\Audit` | class extends Command | `kernel/private/classes/commands/audit.php:26` | Der Audit-Befehl exp:audit: die Datensätze der Audit-Kanäle und ihre Hash-Ketten. | – |
| `Exponential\Command\Kernel\Benchmark` | class extends Command | `kernel/private/classes/commands/benchmark.php:15` | Der Code von bin/php/benchmark.php (exp:benchmark). | – |
| `Exponential\Command\Kernel\Cache` | class extends Command | `kernel/private/classes/commands/cache.php:23` | Der Code von bin/php/cache.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Changelog2xmltext` | class extends Command | `kernel/private/classes/commands/changelog2xmltext.php:174` | Der Code von bin/php/changelog2xmltext.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Checkclasses` | class extends Command | `kernel/private/classes/commands/checkclasses.php:11` | Der Code von bin/php/checkclasses.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Checkdbfiles` | class extends Command | `kernel/private/classes/commands/checkdbfiles.php:149` | Der Code von bin/php/checkdbfiles.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Cleanuppolicies` | class extends Command | `kernel/private/classes/commands/cleanuppolicies.php:26` | Der Code von bin/php/cleanuppolicies.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Cleanuprss` | class extends Command | `kernel/private/classes/commands/cleanuprss.php:28` | Der Code von bin/php/cleanuprss.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Cleanupversions` | class extends Command | `kernel/private/classes/commands/cleanupversions.php:25` | Der Code von bin/php/cleanupversions.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Clusterize` | class extends Command | `kernel/private/classes/commands/clusterize.php:114` | Der Code von bin/php/clusterize.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Clusterpurge` | class extends Command | `kernel/private/classes/commands/clusterpurge.php:24` | Der Code von bin/php/clusterpurge.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Collaborationsampledata` | class extends Command | `kernel/private/classes/commands/collaborationsampledata.php:16` | – | – |
| `Exponential\Command\Kernel\Contentcheck` | class extends Command | `kernel/private/classes/commands/contentcheck.php:16` | – | – |
| `Exponential\Command\Kernel\Convertezenumtoezselection` | class extends Command | `kernel/private/classes/commands/convertezenumtoezselection.php:23` | Der Code von bin/php/convertezenumtoezselection.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Convertprice2multiprice` | class extends Command | `kernel/private/classes/commands/convertprice2multiprice.php:84` | Der Code von bin/php/convertprice2multiprice.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Dfscleanup` | class extends Command | `kernel/private/classes/commands/dfscleanup.php:35` | Der Code von bin/php/dfscleanup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Expcontentjob` | class extends Command | `kernel/private/classes/commands/expcontentjob.php:30` | Der Befehl bin/php/expcontentjob.php (./console exp:expcontentjob): Inhaltsjobs, die großen Teilbaum-Entfernungen und -Kopien, die stapelweise im Hin… | – |
| `Exponential\Command\Kernel\Exphelp` | class extends Command | `kernel/private/classes/commands/exphelp.php:16` | – | – |
| `Exponential\Command\Kernel\Exphelpcontent` | class extends Command | `kernel/private/classes/commands/exphelpcontent.php:16` | – | – |
| `Exponential\Command\Kernel\Exphelpexamples` | class extends Command | `kernel/private/classes/commands/exphelpexamples.php:16` | – | – |
| `Exponential\Command\Kernel\Explogs` | class extends Command | `kernel/private/classes/commands/explogs.php:21` | – | – |
| `Exponential\Command\Kernel\Exploremipsumfill` | class extends Command | `kernel/private/classes/commands/exploremipsumfill.php:20` | – | – |
| `Exponential\Command\Kernel\Explovestack` | class extends Command | `kernel/private/classes/commands/explovestack.php:16` | – | – |
| `Exponential\Command\Kernel\Ezapplytemplate` | class extends Command | `kernel/private/classes/commands/ezapplytemplate.php:24` | Der Code von bin/php/ezapplytemplate.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezasynchronouspublisher` | class extends Command | `kernel/private/classes/commands/ezasynchronouspublisher.php:43` | Signal-Handler | – |
| `Exponential\Command\Kernel\Ezcache` | class extends Command | `kernel/private/classes/commands/ezcache.php:24` | Der Code von bin/php/ezcache.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezcheckphptag` | class extends Command | `kernel/private/classes/commands/ezcheckphptag.php:24` | Der Code von bin/php/ezcheckphptag.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezchecktranslation` | class extends Command | `kernel/private/classes/commands/ezchecktranslation.php:132` | Der Code von bin/php/ezchecktranslation.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezcontentcache` | class extends Command | `kernel/private/classes/commands/ezcontentcache.php:24` | Der Code von bin/php/ezcontentcache.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezconvert2isbn13` | class extends Command | `kernel/private/classes/commands/ezconvert2isbn13.php:24` | Der Code von bin/php/ezconvert2isbn13.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezconvertdbcharset` | class extends Command | `kernel/private/classes/commands/ezconvertdbcharset.php:1468` | HINWEIS: Was, wenn noch andere Daten in der Datenbank stehen? | – |
| `Exponential\Command\Kernel\Ezconvertmysqltabletype` | class extends Command | `kernel/private/classes/commands/ezconvertmysqltabletype.php:231` | Mit der Datenbank verbinden | – |
| `Exponential\Command\Kernel\Ezcreatedeactivatedusergroup` | class extends Command | `kernel/private/classes/commands/ezcreatedeactivatedusergroup.php:22` | Legt die Benutzergruppe für deaktivierte Konten von „Deaktivieren und verschieben“ (expUserDeactivation) vor der ersten Verwendung an, für den mit -s angegebenen Siteaccess (… | – |
| `Exponential\Command\Kernel\Ezcsvexport` | class extends Command | `kernel/private/classes/commands/ezcsvexport.php:24` | Der Code von bin/php/ezcsvexport.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezcsvimport` | class extends Command | `kernel/private/classes/commands/ezcsvimport.php:24` | Der Code von bin/php/ezcsvimport.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezexec` | class extends Command | `kernel/private/classes/commands/ezexec.php:24` | Der Code von bin/php/ezexec.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezflowupgrade` | class extends Command | `kernel/private/classes/commands/ezflowupgrade.php:199` | Der Code von bin/php/ezflowupgrade.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezgeneratetranslationcache` | class extends Command | `kernel/private/classes/commands/ezgeneratetranslationcache.php:25` | Der Code von bin/php/ezgeneratetranslationcache.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezimportdbafile` | class extends Command | `kernel/private/classes/commands/ezimportdbafile.php:24` | Der Code von bin/php/ezimportdbafile.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezpgenerateautoloads` | class extends Command | `kernel/private/classes/commands/ezpgenerateautoloads.php:25` | Der Code von bin/php/ezpgenerateautoloads.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezpm` | class extends Command | `kernel/private/classes/commands/ezpm.php:313` | Der Code von ezpm.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezpublishingbenchmark` | class extends Command | `kernel/private/classes/commands/ezpublishingbenchmark.php:24` | Der Code von bin/php/ezpublishingbenchmark.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezrequestrules` | class extends Command | `kernel/private/classes/commands/ezrequestrules.php:26` | Der Code von bin/php/ezrequestrules.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezsessiongc` | class extends Command | `kernel/private/classes/commands/ezsessiongc.php:24` | Der Code von bin/php/ezsessiongc.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezsqldiff` | class extends Command | `kernel/private/classes/commands/ezsqldiff.php:99` | Der Code von bin/php/ezsqldiff.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezsqldumpisbndata` | class extends Command | `kernel/private/classes/commands/ezsqldumpisbndata.php:24` | Der Code von bin/php/ezsqldumpisbndata.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezsqldumpschema` | class extends Command | `kernel/private/classes/commands/ezsqldumpschema.php:53` | Der Code von bin/php/ezsqldumpschema.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezsqlinsertschema` | class extends Command | `kernel/private/classes/commands/ezsqlinsertschema.php:53` | Der Code von bin/php/ezsqlinsertschema.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezsubtreecopy` | class extends Command | `kernel/private/classes/commands/ezsubtreecopy.php:271` | Der Code von bin/php/ezsubtreecopy.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezsubtreeremove` | class extends Command | `kernel/private/classes/commands/ezsubtreeremove.php:25` | Der Code von bin/php/ezsubtreeremove.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Eztc` | class extends Command | `kernel/private/classes/commands/eztc.php:24` | Der Code von bin/php/eztc.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Eztemplatecheck` | class extends Command | `kernel/private/classes/commands/eztemplatecheck.php:60` | Der Code von bin/php/eztemplatecheck.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezwebincommon` | class extends Command | `kernel/private/classes/commands/ezwebincommon.php:596` | Der Code von bin/php/ezwebincommon.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezwebininstall` | class extends Command | `kernel/private/classes/commands/ezwebininstall.php:25` | Der Code von bin/php/ezwebininstall.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Ezwebinupgrade` | class extends Command | `kernel/private/classes/commands/ezwebinupgrade.php:357` | Der Code von bin/php/ezwebinupgrade.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Flatten` | class extends Command | `kernel/private/classes/commands/flatten.php:42` | Der Code von bin/php/flatten.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Frankenphp` | class extends Command | `kernel/private/classes/commands/frankenphp.php:24` | Der Code von bin/php/frankenphp.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\I18n` | class extends Command | `kernel/private/classes/commands/i18n.php:18` | – | – |
| `Exponential\Command\Kernel\Ini` | class extends Command | `kernel/private/classes/commands/ini.php:18` | – | – |
| `Exponential\Command\Kernel\Install` | class extends Command | `kernel/private/classes/commands/install.php:29` | Der Code von bin/php/install.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Kickstarter` | class extends Command | `kernel/private/classes/commands/kickstarter.php:153` | Führt dieses Skript erneut als Kindprozess mit denselben Argumenten und PHP-Einstellungen aus, zeigt seine Ausgabe laufend an und kopiert sie nach var/log/kickstart.log, nach… | – |
| `Exponential\Command\Kernel\Mailbounces` | class extends Command | `kernel/private/classes/commands/mailbounces.php:16` | – | – |
| `Exponential\Command\Kernel\Mailconsent` | class extends Command | `kernel/private/classes/commands/mailconsent.php:16` | – | – |
| `Exponential\Command\Kernel\Mailgate` | class extends Command | `kernel/private/classes/commands/mailgate.php:16` | – | – |
| `Exponential\Command\Kernel\Mailpreferences` | class extends Command | `kernel/private/classes/commands/mailpreferences.php:16` | – | – |
| `Exponential\Command\Kernel\Mailstatus` | class extends Command | `kernel/private/classes/commands/mailstatus.php:16` | – | – |
| `Exponential\Command\Kernel\Mailsuppression` | class extends Command | `kernel/private/classes/commands/mailsuppression.php:16` | – | – |
| `Exponential\Command\Kernel\Maintenance` | class extends Command | `kernel/private/classes/commands/maintenance.php:34` | Der Code von bin/php/maintenance.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Makestaticcache` | class extends Command | `kernel/private/classes/commands/makestaticcache.php:24` | Der Code von bin/php/makestaticcache.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Notificationevents` | class extends Command | `kernel/private/classes/commands/notificationevents.php:17` | – | – |
| `Exponential\Command\Kernel\Notificationrun` | class extends Command | `kernel/private/classes/commands/notificationrun.php:17` | – | – |
| `Exponential\Command\Kernel\Notificationstatus` | class extends Command | `kernel/private/classes/commands/notificationstatus.php:17` | – | – |
| `Exponential\Command\Kernel\Notificationsubscriptions` | class extends Command | `kernel/private/classes/commands/notificationsubscriptions.php:17` | – | – |
| `Exponential\Command\Kernel\Phar` | class extends Command | `kernel/private/classes/commands/phar.php:39` | Der Code von bin/php/phar.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Preload` | class extends Command | `kernel/private/classes/commands/preload.php:35` | Der Preload aus der Shell und per Cron. | – |
| `Exponential\Command\Kernel\Preloadjob` | class extends Command | `kernel/private/classes/commands/preloadjob.php:33` | Der Code von bin/php/preloadjob.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\PublishContent` | class extends Command | `kernel/private/classes/commands/publish_content.php:25` | Der Code von bin/php/publish_content.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Resetuserpassword` | class extends Command | `kernel/private/classes/commands/resetuserpassword.php:27` | Der Code von bin/php/resetuserpassword.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Runcronjobs` | class extends Command | `kernel/private/classes/commands/runcronjobs.php:91` | Der Code von runcronjobs.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Solr` | class extends Command | `kernel/private/classes/commands/solr.php:190` | Ein Ergebnis für einen Menschen oder als JSON ausgeben und mit $exit beenden. | – |
| `Exponential\Command\Kernel\Systeminfo` | class extends Command | `kernel/private/classes/commands/systeminfo.php:16` | – | – |
| `Exponential\Command\Kernel\Trashpurge` | class extends Command | `kernel/private/classes/commands/trashpurge.php:24` | Der Code von bin/php/trashpurge.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Updateisbn13` | class extends Command | `kernel/private/classes/commands/updateisbn13.php:24` | Der Code von bin/php/updateisbn13.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Updateniceurls` | class extends Command | `kernel/private/classes/commands/updateniceurls.php:338` | Der Code von bin/php/updateniceurls.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Updatesearchindex` | class extends Command | `kernel/private/classes/commands/updatesearchindex.php:42` | Der Code von bin/php/updatesearchindex.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Userlogincheck` | class extends Command | `kernel/private/classes/commands/userlogincheck.php:18` | – | – |
| `Exponential\Command\Kernel\Velocity` | class extends Command | `kernel/private/classes/commands/velocity.php:262` | Eine Einstellungsliste für einen Menschen zum Lesen ausgeben. | – |
| `Exponential\Command\Kernel\VelocityWarmup` | class extends Command | `kernel/private/classes/commands/velocity-warmup.php:40` | Der Code von bin/php/velocity-warmup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\VerifyAliases` | class extends Command | `kernel/private/classes/commands/verify_aliases.php:43` | Der Code von bin/php/verify_aliases.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Warm` | class extends Command | `kernel/private/classes/commands/warm.php:34` | Der Code von bin/php/warm.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Command\Kernel\Webserver` | class extends Command | `kernel/private/classes/commands/webserver.php:170` | Ein Konfigurationsergebnis für einen Menschen oder als JSON ausgeben. | – |

### kernel/private/classes/cronjobs (27)

[Alle Muster von kernel/private/classes/cronjobs](bereich-kernel-private-classes-cronjobs.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Cronjob\Kernel\Audit` | class extends CronjobPart | `kernel/private/classes/cronjobs/audit.php:17` | Der Cronjob-Teil audit (cronjobs/audit.php; [CronjobPart-audit] und die Gruppe frequent in cronjob.ini): die geplanten Arbeiten des Audits, expAuditMaintenan… | – |
| `Exponential\Cronjob\Kernel\BasketCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/basket_cleanup.php:25` | Der Code von cronjobs/basket_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Cachecleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/cachecleanup.php:59` | Der Code von cronjobs/cachecleanup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Cleanuprss` | class extends CronjobPart | `kernel/private/classes/cronjobs/cleanuprss.php:29` | Der Code von cronjobs/cleanuprss.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Clusterpurge` | class extends CronjobPart | `kernel/private/classes/cronjobs/clusterpurge.php:25` | Der Code von cronjobs/clusterpurge.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Contentcheck` | class extends CronjobPart | `kernel/private/classes/cronjobs/contentcheck.php:18` | Der Code von cronjobs/contentcheck.php: baut den Referenzindex der Inhaltsprüfungen auf und hält ihn im Cache (expContentCheckReferences). | – |
| `Exponential\Cronjob\Kernel\Contentjobs` | class extends CronjobPart | `kernel/private/classes/cronjobs/contentjobs.php:20` | Der Cronjob-Teil contentjobs (cronjobs/contentjobs.php, Gruppe frequent): startet die Inhaltsjobs, die einen Worker brauchen und keinen haben — ein laufender Job, des… | – |
| `Exponential\Cronjob\Kernel\Explogrotate` | class extends CronjobPart | `kernel/private/classes/cronjobs/explogrotate.php:19` | Der Code von cronjobs/explogrotate.php: der Cronjob-Teil für die Log-Rotation ([CronjobPart-logs], in keinem Standardteil, er läuft also nur, wenn eine Site ihn hinzufügt zu… | – |
| `Exponential\Cronjob\Kernel\Hide` | class extends CronjobPart | `kernel/private/classes/cronjobs/hide.php:25` | Der Code von cronjobs/hide.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\HttpcacheCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/httpcache_cleanup.php:23` | Der Code von cronjobs/httpcache_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Indexcontent` | class extends CronjobPart | `kernel/private/classes/cronjobs/indexcontent.php:25` | Der Code von cronjobs/indexcontent.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\InternalDraftsCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/internal_drafts_cleanup.php:25` | Der Code von cronjobs/internal_drafts_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Linkcheck` | class extends CronjobPart | `kernel/private/classes/cronjobs/linkcheck.php:26` | Der Code von cronjobs/linkcheck.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Mailbounces` | class extends CronjobPart | `kernel/private/classes/cronjobs/mailbounces.php:15` | – | – |
| `Exponential\Cronjob\Kernel\Mailpreferences` | class extends CronjobPart | `kernel/private/classes/cronjobs/mailpreferences.php:15` | – | – |
| `Exponential\Cronjob\Kernel\Notification` | class extends CronjobPart | `kernel/private/classes/cronjobs/notification.php:25` | Der Code von cronjobs/notification.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\OldDraftsCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/old_drafts_cleanup.php:25` | Der Code von cronjobs/old_drafts_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\QuerycacheCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/querycache_cleanup.php:18` | Der Code von cronjobs/querycache_cleanup.php. | – |
| `Exponential\Cronjob\Kernel\Rssimport` | class extends CronjobPart | `kernel/private/classes/cronjobs/rssimport.php:407` | Der Code von cronjobs/rssimport.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\SessionGc` | class extends CronjobPart | `kernel/private/classes/cronjobs/session_gc.php:25` | Der Code von cronjobs/session_gc.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\StaticcacheCleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/staticcache_cleanup.php:25` | Der Code von cronjobs/staticcache_cleanup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Subtreeexpirycleanup` | class extends CronjobPart | `kernel/private/classes/cronjobs/subtreeexpirycleanup.php:25` | Der Code von cronjobs/subtreeexpirycleanup.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Trashpurge` | class extends CronjobPart | `kernel/private/classes/cronjobs/trashpurge.php:25` | Der Code von cronjobs/trashpurge.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Unlock` | class extends CronjobPart | `kernel/private/classes/cronjobs/unlock.php:93` | Der Code von cronjobs/unlock.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Unpublish` | class extends CronjobPart | `kernel/private/classes/cronjobs/unpublish.php:25` | Der Code von cronjobs/unpublish.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Updateviewcount` | class extends CronjobPart | `kernel/private/classes/cronjobs/updateviewcount.php:25` | Der Code von cronjobs/updateviewcount.php, in eine Klasse verschoben (#207 Stufe 1). | – |
| `Exponential\Cronjob\Kernel\Workflow` | class extends CronjobPart | `kernel/private/classes/cronjobs/workflow.php:25` | Der Code von cronjobs/workflow.php, in eine Klasse verschoben (#207 Stufe 1). | – |

### kernel/private/classes/runnable (3)

[Alle Muster von kernel/private/classes/runnable](bereich-kernel-private-classes-runnable.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Runnable\Command` | abstract class extends Runnable | `kernel/private/classes/runnable/command.php:36` | Ein CLI-Befehl. | – |
| `Exponential\Runnable\CronjobPart` | abstract class extends Runnable | `kernel/private/classes/runnable/cronjobpart.php:21` | Ein Cronjob-Teil. | – |
| `Exponential\Runnable\ModuleView` | abstract class extends Runnable | `kernel/private/classes/runnable/moduleview.php:23` | Eine Modulansicht. | – |

## Cronjob-Teile (36)

### extension/bccie (4)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `exportcsv` | `extension/bccie/classes/runnable/cronjobs/exportcsv.php` | – |
| `exportsylk` | `extension/bccie/classes/runnable/cronjobs/exportsylk.php` | – |
| `exportcsv` | `extension/bccie/cronjobs/exportcsv.php` | – |
| `exportsylk` | `extension/bccie/cronjobs/exportsylk.php` | – |

### extension/bcgooglesitemaps (4)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `generate` | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generate.php` | Der Code von extension/bcgooglesitemaps/cronjobs/generate.php, in eine Klasse verschoben (#207 Stufe 1). |
| `generatemultilingual` | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generatemultilingual.php` | Der Code von extension/bcgooglesitemaps/cronjobs/generatemultilingual.php, in eine Klasse verschoben (#207 Stufe 1). |
| `generate` | `extension/bcgooglesitemaps/cronjobs/generate.php` | – |
| `generatemultilingual` | `extension/bcgooglesitemaps/cronjobs/generatemultilingual.php` | – |

### extension/cjw_newsletter (6)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `cjw_newsletter_mailbox` | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailbox.php` | Der Code von extension/cjw_newsletter/cronjobs/cjw_newsletter_mailbox.php, als Klasse (#207). |
| `cjw_newsletter_mailqueue_create` | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailqueue_create.php` | Der Code von extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_create.php, in eine Klasse verschoben (#207 Stufe 1). |
| `cjw_newsletter_mailqueue_process` | `extension/cjw_newsletter/classes/runnable/cronjobs/cjw_newsletter_mailqueue_process.php` | Der Code von extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_process.php, in eine Klasse verschoben (#207 Stufe 1). |
| `cjw_newsletter_mailbox` | `extension/cjw_newsletter/cronjobs/cjw_newsletter_mailbox.php` | Cronjob cjw_newsletter_mailbox.php |
| `cjw_newsletter_mailqueue_create` | `extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_create.php` | Cronjob cjw_newsletter_mailqueue_create.php |
| `cjw_newsletter_mailqueue_process` | `extension/cjw_newsletter/cronjobs/cjw_newsletter_mailqueue_process.php` | Cronjob cjw_newsletter_create_mailqueue.php |

### extension/cjw_tmv (1)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `cjw_tmv_refresh` | `extension/cjw_tmv/cronjobs/cjw_tmv_refresh.php` | Cronjob-Teil cjw_tmv: importiert und aktualisiert die TMV-Veranstaltungen jedes tmv_container als Inhaltsobjekte (cjwTmvImporter) und entfernt die vergangenen. |

### extension/ezfind (2)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `ezfindexsubtree` | `extension/ezfind/cronjobs/ezfindexsubtree.php` | – |
| `ezfoptimizeindex` | `extension/ezfind/cronjobs/ezfoptimizeindex.php` | – |

### extension/ezflow (2)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `ezflowcleanup` | `extension/ezflow/cronjobs/ezflowcleanup.php` | – |
| `ezflowupdate` | `extension/ezflow/cronjobs/ezflowupdate.php` | – |

### extension/ezmbpaex (2)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `ezmbpaex_sendexpirynotifications` | `extension/ezmbpaex/cronjobs/ezmbpaex_sendexpirynotifications.php` | – |
| `ezmbpaex_updatechildren` | `extension/ezmbpaex/cronjobs/ezmbpaex_updatechildren.php` | – |

### extension/sevenx_authentication_2fa (1)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `sevenx2facleanup` | `extension/sevenx_authentication_2fa/cronjobs/sevenx2facleanup.php` | – |

### extension/syndication (4)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `php_export_feed` | `extension/syndication/classes/runnable/cronjobs/php_export_feed.php` | – |
| `php_import_feed` | `extension/syndication/classes/runnable/cronjobs/php_import_feed.php` | – |
| `syndication_export` | `extension/syndication/cronjobs/syndication_export.php` | Cronjob-Teil export_feed der Extension syndication. |
| `syndication_import` | `extension/syndication/cronjobs/syndication_import.php` | Cronjob-Teil import_feed der Extension syndication. |

### extension/xrowextract (2)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `xrowextract` | `extension/xrowextract/classes/runnable/cronjobs/xrowextract.php` | Der Code von extension/xrowextract/cronjobs/xrowextract.php, in eine Klasse verschoben (#207 Stufe 1). |
| `xrowextract` | `extension/xrowextract/cronjobs/xrowextract.php` | Der Cronjob-Teil xrowextract (runcronjobs.php xrowextract): startet jeden fälligen Zeitplan als Hintergrundjob über denselben Runner, den "Run in the backgro… |

### extension/xrowmetadata (8)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `archivesitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/archivesitemap.php` | Der Code von extension/xrowmetadata/cronjobs/archivesitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `mobilesitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/mobilesitemap.php` | Der Code von extension/xrowmetadata/cronjobs/mobilesitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `newssitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/newssitemap.php` | Der Code von extension/xrowmetadata/cronjobs/newssitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `sitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/sitemap.php` | Der Code von extension/xrowmetadata/cronjobs/sitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `archivesitemap` | `extension/xrowmetadata/cronjobs/archivesitemap.php` | – |
| `mobilesitemap` | `extension/xrowmetadata/cronjobs/mobilesitemap.php` | – |
| `newssitemap` | `extension/xrowmetadata/cronjobs/newssitemap.php` | – |
| `sitemap` | `extension/xrowmetadata/cronjobs/sitemap.php` | – |
