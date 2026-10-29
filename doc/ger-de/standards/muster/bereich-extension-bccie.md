---
title: Muster in extension/bccie
audience: developer
category: standards
order: 402
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/bccie mit den Entwurfsmustern.
keywords: extension/bccie, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/bccie

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/bccie` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (48)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `BaseHandler` | class | [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/basehandler.php:11` | – |
| `bccieDashboard` | class | – | `extension/bccie/classes/bccieDashboard.php:14` | Was die Startseite des Moduls zeigt: die Formulare mit gesammelten Informationen und ihre Anzahl, die letzten Exporte, die Einstellungen des geplanten Expor… |
| `bccieExporter` | class | – | `extension/bccie/classes/bccieExporter.php:17` | Schreibt die Zeilen eines Exports Zeile für Zeile als CSV oder SYLK, damit ein großer Export nie die ganze Datei im Speicher braucht. |
| `bccieExportFormatOutputHandler` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/bccieExportFormatOutputHandler.php:11` | – |
| `bccieExportFormatOutputHandlerCP1252` | class extends bccieExportFormatOutputHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerCP1252.php:11` | – |
| `bccieExportFormatOutputHandlerUtf8` | class extends bccieExportFormatOutputHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8.php:11` | – |
| `bccieExportFormatOutputHandlerUtf8Bom` | class extends bccieExportFormatOutputHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8Bom.php:11` | – |
| `bccieExportFormatOutputHandlerUtf16Le` | class extends bccieExportFormatOutputHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf16Le.php:11` | – |
| `bccieExportPage` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/bccie/classes/bccieExportPage.php:15` | Die Exportseite eines Formulars: das Optionsformular, der Download, der Start eines Exports im Hintergrund und das Entfernen der gesammelten Informationen. |
| `bccieExportUtils` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/bccie/classes/bccieExportUtils.php:11` | – |
| `bccieInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/bccie/ezinfo.php:10` | – |
| `bccieJob` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/bccie/classes/bccieJob.php:15` | Ein Export im Hintergrund, gestartet aus der Administrationsoberfläche: derselbe Konsolenbefehl, den die Shell verwendet (ext:bccie:export), gestartet mit expProcessTools wie d… |
| `bccieJobOutput` | class | – | `extension/bccie/classes/bccieJobOutput.php:13` | Ausgabesenke mit den zwei Methoden von eZCLI, die der Runner verwendet; eine Zeile geht an das Terminal und in das Log eines Jobs. |
| `bccieRunner` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/bccie/classes/bccieRunner.php:15` | Die Arbeit der Exporte als Methoden, damit die Admin, die Konsolenbefehle und die Cronjob-Teile dasselbe tun: die Optionen validieren, ein e… |
| `bccieTransfer` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/bccie/classes/bccieTransfer.php:26` | CIE und die Transferläufe des Kernels (Exponential 6.0.15 und neuer, doc/eng-us/features/6.0/transfer-runs.md des Kernels): eine Engine und ein Lauf-Ver… |
| `bccieTransferTask` | class implements expKernelTransferTask | – | `extension/bccie/classes/bccieTransferTask.php:25` | Ein Hintergrundexport von CIE als Transferaufgabe (bccieTransfer): was die Job-Warteschlange (Job-Typ bccie.export) und die Inhalts-Jobs des Kernels für ein… |
| `bccieUI` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/bccie/classes/bccieUI.php:13` | Gemeinsame Helfer der Modulansichten: Flash-Hinweise und die Parameter einer Listenansicht (Paginierung, Sortierung, Filter). |
| `Exponential\Command\Extension\Bccie\Export` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/bccie/classes/runnable/commands/php_export.php:16` | ext:bccie:export - die gesammelten Informationen eines Formulars in eine CSV- oder SYLK-Datei exportieren oder den geplanten Export ausführen (die Cronjob-Teile exportcsv und exp… |
| `Exponential\Command\Extension\Bccie\Purge` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/bccie/classes/runnable/commands/php_purge.php:14` | ext:bccie:purge - die gesammelten Informationen eines Formulars entfernen, alle oder die, die älter als ein Datum sind. |
| `Exponential\Command\Extension\Bccie\Status` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/bccie/classes/runnable/commands/php_status.php:15` | ext:bccie:status - das Dashboard der Admin als Text: die Formulare mit gesammelten Informationen, die letzten Exporte und die gefundenen Probleme. |
| `Exponential\Cronjob\Extension\Bccie\Exportcsv` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/bccie/classes/runnable/cronjobs/exportcsv.php:16` | Cronjob-Teil exportcsv: schreibt die gesammelten Informationen der Objekte in [CieSettings] Collection[] als CSV-Dateien in [CieSettings] Directory. |
| `Exponential\Cronjob\Extension\Bccie\Exportsylk` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/bccie/classes/runnable/cronjobs/exportsylk.php:16` | Cronjob-Teil exportsylk: schreibt die gesammelten Informationen der Objekte in [CieSettings] Collection[] als SYLK-Dateien in [CieSettings] Directory. |
| `Exponential\View\Extension\Bccie\Bccie\Doexport` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/bccie/classes/runnable/views/bccie/doexport.php:22` | Der Code von extension/bccie/modules/bccie/doexport.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Bccie\Bccie\Export` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/bccie/classes/runnable/views/bccie/export.php:22` | Der Code von extension/bccie/modules/bccie/export.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Bccie\Bccie\Overview` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/bccie/classes/runnable/views/bccie/overview.php:22` | Der Code von extension/bccie/modules/bccie/overview.php, in eine Klasse verschoben (#207 Stufe 1), in 1.1.12 als Dashboard mit einer Liste neu aufgebaut. |
| `eZBirthdayExportHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezbirthdayexporthandler.php:13` | – |
| `eZBirthdayHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezbirthdayhandler.php:11` | – |
| `eZBooleanHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezbooleanhandler.php:11` | – |
| `eZCountryHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezcountrtyhandler.php:11` | – |
| `eZDateHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezdatehandler.php:11` | – |
| `eZDateTimeHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezdatetimehandler.php:11` | – |
| `eZEmailHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezemailhandler.php:11` | – |
| `ezenhancedobjectrelationHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezenhancedobjectrelationhandler.php:11` | – |
| `eZExampleCustomHandler` | class extends BaseHandler | [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezexamplecustomhandler.php:11` | – |
| `eZFloatHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezfloathandler.php:11` | – |
| `eZIdentifierHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezidentifierhandler.php:11` | – |
| `eZIntegerHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezintegerhandler.php:11` | – |
| `eZObjectRelationHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezobjectrelationhandler.php:11` | – |
| `eZOptionHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezoptionhandler.php:11` | – |
| `eZSelectionHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezselectionhandler.php:11` | – |
| `eZStringHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezstringhandler.php:11` | – |
| `eZTextHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/eztexthandler.php:11` | – |
| `eZTimeHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/eztimehandler.php:11` | – |
| `eZURLHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezurlhandler.php:11` | – |
| `eZXMLTextHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/ezxmltexthandler.php:11` | – |
| `OWEnhancedSelectionHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/owenhancedselectionhandler.php:11` | – |
| `Parser` | class | – | `extension/bccie/classes/parser.php:15` | Wandelt die gesammelten Informationen eines Objekts in Zeilen um und schreibt sie als CSV oder SYLK. |
| `smileobjectrelationlistHandler` | class extends BaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/bccie/classes/handlers/smileobjectrelationlisthandler.php:11` | – |

## Modulansichten (5)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/bccie/overview` | `overview.php` | read | `extension/bccie/modules/bccie/module.php:27` |
| `/bccie/export` | `export.php` | read | `extension/bccie/modules/bccie/module.php:40` |
| `/bccie/doexport` | `doexport.php` | read | `extension/bccie/modules/bccie/module.php:49` |
| `/bccie/download` | `download.php` | read | `extension/bccie/modules/bccie/module.php:59` |
| `/bccie/job` | `job.php` | read | `extension/bccie/modules/bccie/module.php:69` |

## Richtlinienfunktionen (2)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `bccie/read` | – | `extension/bccie/modules/bccie/module.php:79` |
| `bccie/remove` | – | `extension/bccie/modules/bccie/module.php:80` |

## Cronjob-Teile (4)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `exportcsv` | `extension/bccie/classes/runnable/cronjobs/exportcsv.php` | – |
| `exportsylk` | `extension/bccie/classes/runnable/cronjobs/exportsylk.php` | – |
| `exportcsv` | `extension/bccie/cronjobs/exportcsv.php` | – |
| `exportsylk` | `extension/bccie/cronjobs/exportsylk.php` | – |

## INI-Plugin-Punkte (44)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
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
| `[JobType_bccie.export] Handler` | `expKernelTransferQueueHandler` | `extension/bccie/settings/expqueue.ini.append.php:9` |
| `[Service_transfer.bccie] Class` | `expKernelTransferTool` | `extension/bccie/settings/kernelservices.ini.append.php:9` |
