---
title: Muster in extension/syndication
audience: developer
category: standards
order: 475
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/syndication mit den Entwurfsmustern.
keywords: extension/syndication, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/syndication

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/syndication` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (34)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Syndication\Export` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/syndication/classes/runnable/commands/php_export.php:14` | ext:syndication:export - den Export-Cache der aktiven Feeds schreiben (der Cronjob-Teil export_feed, von Hand). |
| `Exponential\Command\Extension\Syndication\Import` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/syndication/classes/runnable/commands/php_import.php:15` | ext:syndication:import - die Elementlisten der Importe abrufen und die wartenden Elemente importieren (der Cronjob-Teil import_feed, von Hand). |
| `Exponential\Command\Extension\Syndication\Install` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/syndication/classes/runnable/commands/php_install.php:14` | ext:syndication:install - die Tabellen der Extension aus share/db_schema.dba anlegen. |
| `Exponential\Command\Extension\Syndication\Status` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/syndication/classes/runnable/commands/php_status.php:14` | ext:syndication:status - was exportiert wird, was importiert wird, die letzten Läufe und die gefundenen Probleme. |
| `Exponential\Cronjob\Extension\Syndication\ExportFeed` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/syndication/classes/runnable/cronjobs/php_export_feed.php:16` | Cronjob-Teil export_feed: schreibt den Export-Cache jedes aktiven Feeds. |
| `Exponential\Cronjob\Extension\Syndication\ImportFeed` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/syndication/classes/runnable/cronjobs/php_import_feed.php:15` | Cronjob-Teil import_feed: ruft die Eintragsliste jedes aktiven Imports ab und importiert die wartenden Einträge. |
| `eZFilterAttribute` | class extends eZSyndicationFilter | [Active Record (eZPersistentObject)](active-record.md), [Dekorierer / Filter](dekorierer.md) | `extension/syndication/classes/filter/ezfilterattribute.php:48` | – |
| `eZFilterSection` | class extends eZSyndicationFilter | [Active Record (eZPersistentObject)](active-record.md), [Dekorierer / Filter](dekorierer.md) | `extension/syndication/classes/filter/ezfiltersection.php:47` | – |
| `eZImportEditFeed` | class extends eZImportEditWizard | – | `extension/syndication/modules/syndication/import_edit/ezimporteditfeed.php:47` | – |
| `eZImportEditFilter` | class extends eZImportEditWizard | [Dekorierer / Filter](dekorierer.md) | `extension/syndication/modules/syndication/import_edit/ezimporteditfilter.php:47` | – |
| `eZImportEditFinal` | class extends eZImportEditWizard | – | `extension/syndication/modules/syndication/import_edit/ezimporteditfinal.php:47` | – |
| `eZImportEditName` | class extends eZImportEditWizard | – | `extension/syndication/modules/syndication/import_edit/ezimporteditname.php:47` | – |
| `eZImportEditOptions` | class extends eZImportEditWizard | – | `extension/syndication/modules/syndication/import_edit/ezimporteditoptions.php:48` | – |
| `eZImportEditWizard` | class extends eZWizardBase | [Singleton / instance()](singleton.md) | `extension/syndication/modules/syndication/import_edit/ezimporteditwizard.php:47` | – |
| `eZSyndicateType` | class extends eZBooleanType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/syndication/datatypes/ezsyndicate/ezsyndicatetype.php:47` | – |
| `eZSyndication` | class | – | `extension/syndication/classes/ezsyndication.php:47` | – |
| `eZSyndicationDashboard` | class | – | `extension/syndication/classes/ezsyndicationdashboard.php:15` | – |
| `eZSyndicationFeed` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/syndication/classes/ezsyndicationfeed.php:47` | – |
| `eZSyndicationFeedCacheManager` | class | [Fassade / Dienst](fassade.md) | `extension/syndication/classes/ezsyndicationfeedcachemanager.php:48` | – |
| `eZSyndicationFeedItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/syndication/classes/ezsyndicationfeeditem.php:48` | – |
| `eZSyndicationFeedItemExport` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/syndication/classes/ezsyndicationfeeditemexport.php:48` | – |
| `eZSyndicationFeedItemStatus` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/syndication/classes/ezsyndicationfeeditemstatus.php:48` | – |
| `eZSyndicationFeedSource` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/syndication/classes/ezsyndicationfeedsource.php:47` | – |
| `eZSyndicationFeedSourceFilter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Dekorierer / Filter](dekorierer.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/syndication/classes/ezsyndicationfeedsourcefilter.php:47` | – |
| `eZSyndicationFilter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Dekorierer / Filter](dekorierer.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/syndication/classes/ezsyndicationfilter.php:48` | – |
| `eZSyndicationImport` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/syndication/classes/ezsyndicationimport.php:47` | – |
| `eZSyndicationImportFilter` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Dekorierer / Filter](dekorierer.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/syndication/classes/ezsyndicationimportfilter.php:47` | – |
| `eZSyndicationInstaller` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/syndication/classes/ezsyndicationinstaller.php:17` | – |
| `eZSyndicationJob` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/syndication/classes/ezsyndicationjob.php:16` | – |
| `eZSyndicationJobOutput` | class | – | `extension/syndication/classes/ezsyndicationjoboutput.php:14` | – |
| `eZSyndicationRunner` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/syndication/classes/ezsyndicationrunner.php:15` | – |
| `eZSyndicationUI` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/syndication/classes/ezsyndicationui.php:15` | – |
| `SyndicationAction` | class | – | `extension/syndication/modules/syndication/syndicationaction.php:50` | – |
| `syndicationInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/syndication/ezinfo.php:42` | – |

## Modulansichten (13)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
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

## Richtlinienfunktionen (11)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
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

## Cronjob-Teile (4)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `php_export_feed` | `extension/syndication/classes/runnable/cronjobs/php_export_feed.php` | – |
| `php_import_feed` | `extension/syndication/classes/runnable/cronjobs/php_import_feed.php` | – |
| `syndication_export` | `extension/syndication/cronjobs/syndication_export.php` | Cronjob-Teil export_feed der Extension syndication. |
| `syndication_import` | `extension/syndication/cronjobs/syndication_import.php` | Cronjob-Teil import_feed der Extension syndication. |
