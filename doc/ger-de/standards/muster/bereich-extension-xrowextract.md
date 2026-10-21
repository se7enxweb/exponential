---
title: Muster in extension/xrowextract
audience: developer
category: standards
order: 476
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/xrowextract mit den Entwurfsmustern.
keywords: extension/xrowextract, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/xrowextract

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/xrowextract` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (91)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Xrowextract\Archive` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowextract/classes/runnable/commands/php_archive.php:30` | Der Code von extension/xrowextract/bin/php/archive.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\Xrowextract\Csv` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowextract/classes/runnable/commands/php_csv.php:28` | Der Code von extension/xrowextract/bin/php/csv.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\Xrowextract\Destination` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowextract/classes/runnable/commands/php_destination.php:31` | Der Code von extension/xrowextract/bin/php/destination.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\Xrowextract\History` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowextract/classes/runnable/commands/php_history.php:23` | Der Code von extension/xrowextract/bin/php/history.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\Xrowextract\Import` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowextract/classes/runnable/commands/php_import.php:30` | Der Code von extension/xrowextract/bin/php/import.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\Xrowextract\Job` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowextract/classes/runnable/commands/php_job.php:27` | Der Code von extension/xrowextract/bin/php/job.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\Xrowextract\Package` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowextract/classes/runnable/commands/php_package.php:46` | Der Code von extension/xrowextract/bin/php/package.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\Xrowextract\Requirements` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowextract/classes/runnable/commands/php_requirements.php:29` | Der Code von extension/xrowextract/bin/php/requirements.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Command\Extension\Xrowextract\Schedule` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowextract/classes/runnable/commands/php_schedule.php:29` | Der Code von extension/xrowextract/bin/php/schedule.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Extension\Xrowextract\Xrowextract` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowextract/classes/runnable/cronjobs/xrowextract.php:25` | Der Code von extension/xrowextract/cronjobs/xrowextract.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Archive` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/archive.php:19` | Der Code von extension/xrowextract/modules/xrowextract/archive.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Browse` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/browse.php:42` | Der Code von extension/xrowextract/modules/xrowextract/browse.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\BrowseFile` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/browse_file.php:30` | Der Code von extension/xrowextract/modules/xrowextract/browse_file.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Compare` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/compare.php:25` | Der Code von extension/xrowextract/modules/xrowextract/compare.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Csv` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/csv.php:95` | Einen Export so zurücklesen, wie es eine Tabellenkalkulation tut (gleiches Trennzeichen und gleiche Anführungszeichen), für die Vorschautabelle: Kopfzeile, Zeilen mit einer Markierung pro Zelle und was die Vor… |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Destinations` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/destinations.php:19` | Der Code von extension/xrowextract/modules/xrowextract/destinations.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\History` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/history.php:20` | Der Code von extension/xrowextract/modules/xrowextract/history.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Import` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/import.php:21` | Der Code von extension/xrowextract/modules/xrowextract/import.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\JobDownload` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/job_download.php:22` | Der Code von extension/xrowextract/modules/xrowextract/job_download.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Jobs` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/jobs.php:19` | Der Code von extension/xrowextract/modules/xrowextract/jobs.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\JobStatus` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/job_status.php:19` | Der Code von extension/xrowextract/modules/xrowextract/job_status.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Package` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/package.php:24` | Der Code von extension/xrowextract/modules/xrowextract/package.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\Schedules` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/schedules.php:21` | Der Code von extension/xrowextract/modules/xrowextract/schedules.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowextract\Xrowextract\SubtreeClasses` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/subtree_classes.php:13` | Der Code von extension/xrowextract/modules/xrowextract/subtree_classes.php: die Klassen unterhalb eines Knotens für Advanced › Export › "Subtr… |
| `Exponential\View\Extension\Xrowextract\Xrowextract\UploadChunk` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowextract/classes/runnable/views/xrowextract/upload_chunk.php:23` | Der Code von extension/xrowextract/modules/xrowextract/upload_chunk.php, in eine Klasse verschoben (#207 Stufe 1). |
| `ParserInterface` | class | – | `extension/xrowextract/classes/parserinterface.php:8` | – |
| `XrowBaseHandler` | class | [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/xrowbasehandler.php:3` | – |
| `XrowExtractArchive` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractarchive.php:12` | Das Site-Archiv: die Objekte vieler Klassen unterhalb vieler Knoten, eine CSV-Datei pro Klasse, mit einem Manifest, gepackt als zip, tar.gz, tar.bz2, tar.xz, 7z o… |
| `XrowExtractCatalogue` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractcatalogue.php:13` | Der Spaltenkatalog über die Klassenattribute selbst hinaus: |
| `XrowExtractColumns` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractcolumns.php:8` | Die Spalten eines Exports und ihre Werte: die besonderen Spalten (keine Klassenattribute), die Attributspalten einer Klasse und ein Objekt als Zeile von CSV-Zel… |
| `XrowExtractCron` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractcron.php:16` | Wann ein Zeitplan läuft: die einfachen Auswahlmöglichkeiten der Seite Schedules (stündlich zur Minute M, täglich um HH:MM, wöchentlich an einem Tag um HH:MM, monatlich an einem Tag um HH:… |
| `XrowExtractDestination` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/xrowextract/classes/xrowextractdestination.php:9` | Ein benanntes, wiederverwendbares Lieferziel (Tabelle xrowextract_destination). |
| `XrowExtractEntry` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractentry.php:12` | Die Einstiegsparameter von xrowextract/csv: das Menü Advanced › Export des Inhaltsbaums öffnet das CSV-Formular vorausgefüllt für einen Knoten mit xrowextract/csv/(nod… |
| `XrowExtractFetchAlias` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractfetchalias.php:18` | Benannte Fetches aus fetchalias.ini, nutzbar in der Ansicht extract und in der CLI: die Constant-Werte eines Alias "tree" / "list" / "tree_count" / "list_count" le… |
| `XrowExtractFileModes` | trait | [Trait (Mixin)](trait.md) | `extension/xrowextract/classes/xrowextractfilemodes.php:14` | Die Modi, die die Extension ihren Dateien und Verzeichnissen gibt, innerhalb der Grenzen EZP_FILE_MODE_MAX / EZP_DIR_MODE_MAX, die der Kernel aus config.php liest (siehe… |
| `XrowExtractFilters` | class | [Dekorierer / Filter](dekorierer.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/xrowextract/classes/xrowextractfilters.php:16` | Filter eines Exports: ein Datumsbereich (veröffentlicht, geändert oder ein Datumsattribut der Klasse; beliebig, heute, die letzten N Tage, seit, vor, zwischen, zukünf… |
| `XrowExtractFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractfunctioncollection.php:4` | Die Template-Fetch-Funktionen des Moduls xrowextract (modules/xrowextract/function_definition.php). |
| `XrowExtractHasChildrenFilter` | class | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/xrowextract/classes/xrowextracthaschildrenfilter.php:11` | Erweiterter Attributfilter „XrowExtractHasChildren“: nur Objekte, deren Knoten mindestens ein Kind hat (sonst Blattinhalte). |
| `XrowExtractHistory` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/xrowextract/classes/xrowextracthistory.php:12` | Der Index der Exportverläufe (Tabelle xrowextract_history): eine Zeile pro Lauf - ein Hintergrundjob (von Hand, von der Kommandozeile oder durch einen Zeitplan gestartet) oder… |
| `XrowExtractImport` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractimport.php:22` | Die andere Richtung des Exports: liest eine CSV- oder JSON-Datei, die XrowExtractWriter geschrieben hat (beliebiger Spaltensatz, insbesondere „migration“), zurück in Inhal… |
| `xrowextractInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/xrowextract/ezinfo.php:10` | – |
| `XrowExtractJob` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractjob.php:16` | Ein Exportjob im Hintergrund: ein Ordner pro Job unterhalb des var-Verzeichnisses (var//xrowextract-jobs//), mit job.json (Zustand), progress.json (geschrieben vom… |
| `XrowExtractManifest` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractmanifest.php:20` | Das typisierte Spaltenmanifest eines Exports: was jede Spalte einer Datei ist (ihre ID, der Name in der Datei, der Datentyp, das gewählte Format und die Sprache),… |
| `XrowExtractNotifier` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractnotifier.php:13` | Was der Eigentümer eines Zeitplans (und wen er sonst nennt) über einen Lauf erfährt: - E-Mail bei Fehlschlag (ein fehlgeschlagener oder übersprungener Lauf oder eine fehlgeschlagene Zustellung): immer an… |
| `XrowExtractNotifierHttp` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/xrowextractnotifier.php:216` | Der HTTP-Aufruf des Webhooks: der curl-Helfer von XrowExtractTransport mit einem JSON-Body. |
| `XrowExtractPackage` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractpackage.php:18` | Unterstützung für Inhaltspakete (.ezpkg) für xrowextract/package: Prüfen der Inhaltsklassen und Inhaltsobjekte eines Pakets, bevor etwas geschrieben wird (die… |
| `XrowExtractPreset` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractpreset.php:25` | Eine gespeicherte Exportvoreinstellung: eine vollständige, benannte Definition eines CSV- oder Archivexports (Bereich, Knoten, Klasse oder Sets, Spalten, Sprachen, alle Filter, Sortierung, Aus… |
| `XrowExtractRelationFilter` | class | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/xrowextract/classes/xrowextractrelationfilter.php:17` | Erweiterter Attributfilter „XrowExtractRelation“: nur Objekte, die eine Relation zu einem gegebenen Objekt haben (relation_type wird ignoriert — jede Relation zählt, einschließlich o… |
| `XrowExtractRequirements` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractrequirements.php:19` | Was die Extension von PHP und dem Server braucht, an einer Stelle: jede Anforderung (eine PHP-Version, eine PHP-Extension, eine Funktion, die ein Hoster deaktivieren kann, ein… |
| `XrowExtractSchedule` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/xrowextract/classes/xrowextractschedule.php:20` | Ein geplanter Export oder Import (Tabelle xrowextract_schedule, bei Bedarf von XrowExtractSchema angelegt). |
| `XrowExtractScheduler` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractscheduler.php:15` | Führt Zeitpläne aus: findet die fälligen (der Cronjob-Teil, cronjobs/xrowextract.php, oder bin/php/schedule.php --cron), macht aus jedem einen gewöhnlichen Hintergr… |
| `XrowExtractSchema` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractschema.php:11` | Die eigenen Tabellen der Extension (xrowextract_schedule, xrowextract_destination, xrowextract_history), verzögert und idempotent angelegt, sobald eine d… |
| `XrowExtractSecrets` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractsecrets.php:15` | Zugangsdaten von Zielen im Ruhezustand: libsodium secretbox (XSalsa20-Poly1305) mit einem 32-Byte-Schlüssel in einer Schlüsseldatei außerhalb der Datenbank. |
| `XrowExtractTranslationFilter` | class | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/xrowextract/classes/xrowextracttranslationfilter.php:24` | Erweiterter Attributfilter „XrowExtractTranslation“: nur Objekte, die eine Übersetzung in der gegebenen Sprache haben. |
| `XrowExtractTransport` | abstract class | [Strategie / Handler](strategie-handler.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/xrowextract/classes/transports/xrowextracttransport.php:10` | Ein Weg, eine Datei an ein Ziel zu liefern (oder, bei einem geplanten Import, von dort abzuholen). |
| `XrowExtractTransportFtp` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransportftp.php:9` | FTP und FTPS über die curl-Extension: einfaches FTP (unverschlüsselt: Benutzer, Passwort und Datei gehen im Klartext - die Seite Ziele sagt das), expli… |
| `XrowExtractTransportHttp` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransporthttp.php:10` | HTTP POST der Datei (ein Webhook-Upload): multipart/form-data mit der Datei (standardmäßig "file"), ihrem Manifest ("manifest", wenn es eines gibt) und einem J… |
| `XrowExtractTransportLocal` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransportlocal.php:8` | Ein lokaler Ordner oder eine auf diesem Server eingehängte NAS-Freigabe. |
| `XrowExtractTransportS3` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransports3.php:9` | S3-kompatibler Objektspeicher (AWS S3, MinIO, Wasabi, ...) mit AWS Signature Version 4, hier direkt geschrieben (kein SDK, kein Composer): PUT Object mit… |
| `XrowExtractTransportSftp` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransportsftp.php:18` | SFTP. |
| `XrowExtractTransportWebdav` | class extends XrowExtractTransport | – | `extension/xrowextract/classes/transports/xrowextracttransportwebdav.php:7` | WebDAV (Nextcloud, ownCloud, Apache mod_dav, ein NAS): Der Ordner wird mit MKCOL angelegt, wo er fehlt, die Datei wird unter einem temporären Namen per PUT hochgeladen und per MOVE… |
| `XrowExtractUpload` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowextract/classes/xrowextractupload.php:22` | Ein Upload in Teilstücken: ein Ordner pro Upload (var//xrowextract/uploads//), mit meta.json (Eigentümer, ursprünglicher Name, Gesamtgröße, bisher empfangene Bytes, ob… |
| `XrowExtractUserStatusFilter` | class | [Dekorierer / Filter](dekorierer.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/xrowextract/classes/xrowextractuserstatusfilter.php:12` | Erweiterter Attributfilter "XrowExtractUserStatus": nur Benutzerobjekte, die aktiviert sind, oder nur solche, die deaktiviert sind, über das Standard-`ezuser_setti… |
| `XrowExtractWriter` | class | – | `extension/xrowextract/classes/xrowextractwriter.php:13` | Schreibt Exportzeilen als CSV, JSON oder XML. |
| `XroweZBinaryfileExportHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezbinaryfilehandler.php:2` | – |
| `XroweZBooleanHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezbooleanhandler.php:2` | – |
| `XroweZDateHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezdatehandler.php:2` | – |
| `XroweZDateTimeHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezdatetimehandler.php:2` | – |
| `XroweZEmailHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezemailhandler.php:3` | – |
| `XroweZenhancedobjectrelationHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezenhancedobjectrelationhandler.php:3` | – |
| `XroweZEnumHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezenumhandler.php:2` | – |
| `XroweZExampleHandler` | class extends XrowBaseHandler | [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezexamplehandler.php:2` | – |
| `XroweZFloatHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezfloathandler.php:3` | – |
| `XroweZIdentifierHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezidentifierhandler.php:3` | – |
| `XroweZImageExportHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezimagehandler.php:2` | – |
| `XroweZIntegerHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezintegerhandler.php:3` | – |
| `XroweZKeywordHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezkeywordhandler.php:2` | – |
| `XroweZMatrixExportHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezmatrixhandler.php:3` | – |
| `XroweZMediaExportHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezmediahandler.php:2` | – |
| `XroweZObjectRelationHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezobjectrelationhandler.php:3` | – |
| `XroweZObjectRelationListHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezobjectrelationlisthandler.php:3` | – |
| `XroweZPriceHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezpricehandler.php:3` | – |
| `XroweZSelectionHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezselectionhandler.php:2` | – |
| `XroweZStringHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezstringhandler.php:3` | – |
| `XroweZTextHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xroweztexthandler.php:3` | – |
| `XroweZTimeHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xroweztimehandler.php:2` | – |
| `XroweZURLHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezurlhandler.php:3` | – |
| `XroweZUserHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezuserhandler.php:3` | – |
| `XroweZXMLTextHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowezxmltexthandler.php:2` | – |
| `XrowhmregexplineHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowhmregexplinehandler.php:3` | – |
| `XrowxrowmetadataHandler` | class extends XrowBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/xrowextract/classes/parsers/xrowxrowmetadatahandler.php:7` | xrowmetadata (SEO- und Sitemap-Metadaten): standardmäßig der Titel. |

## Modulansichten (9)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/xrowextract/csv` | `csv.php` | csv | `extension/xrowextract/modules/xrowextract/module.php:6` |
| `/xrowextract/upload_chunk` | `upload_chunk.php` | import | `extension/xrowextract/modules/xrowextract/module.php:56` |
| `/xrowextract/package` | `package.php` | import | `extension/xrowextract/modules/xrowextract/module.php:65` |
| `/xrowextract/schedules` | `schedules.php` | schedule | `extension/xrowextract/modules/xrowextract/module.php:74` |
| `/xrowextract/destinations` | `destinations.php` | destinations | `extension/xrowextract/modules/xrowextract/module.php:80` |
| `/xrowextract/history` | `history.php` | history | `extension/xrowextract/modules/xrowextract/module.php:86` |
| `/xrowextract/browse` | `browse.php` | import | `extension/xrowextract/modules/xrowextract/module.php:96` |
| `/xrowextract/compare` | `compare.php` | import | `extension/xrowextract/modules/xrowextract/module.php:105` |
| `/xrowextract/browse_file` | `browse_file.php` | import | `extension/xrowextract/modules/xrowextract/module.php:111` |

## Richtlinienfunktionen (8)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `xrowextract/schedule` | – | `extension/xrowextract/modules/xrowextract/module.php:117` |
| `xrowextract/destinations` | – | `extension/xrowextract/modules/xrowextract/module.php:119` |
| `xrowextract/history` | – | `extension/xrowextract/modules/xrowextract/module.php:121` |
| `xrowextract/csv` | – | `extension/xrowextract/modules/xrowextract/module.php:122` |
| `xrowextract/password_hash` | – | `extension/xrowextract/modules/xrowextract/module.php:124` |
| `xrowextract/jobs` | – | `extension/xrowextract/modules/xrowextract/module.php:126` |
| `xrowextract/all_jobs` | – | `extension/xrowextract/modules/xrowextract/module.php:128` |
| `xrowextract/import` | – | `extension/xrowextract/modules/xrowextract/module.php:130` |

## Fetch-Funktionen (2)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'xrowextract', 'schedule_alerts' )` | `XrowExtractFunctionCollection::fetchScheduleAlerts()` | `extension/xrowextract/modules/xrowextract/function_definition.php:10` |
| `fetch( 'xrowextract', 'requirement_notices' )` | `XrowExtractFunctionCollection::fetchRequirementNotices()` | `extension/xrowextract/modules/xrowextract/function_definition.php:17` |

## Cronjob-Teile (2)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `xrowextract` | `extension/xrowextract/classes/runnable/cronjobs/xrowextract.php` | Der Code von extension/xrowextract/cronjobs/xrowextract.php, in eine Klasse verschoben (#207 Stufe 1). |
| `xrowextract` | `extension/xrowextract/cronjobs/xrowextract.php` | Der Cronjob-Teil xrowextract (runcronjobs.php xrowextract): startet jeden fälligen Zeitplan als Hintergrundjob über denselben Runner, den "Run in the backgro… |

## INI-Plugin-Punkte (33)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
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
