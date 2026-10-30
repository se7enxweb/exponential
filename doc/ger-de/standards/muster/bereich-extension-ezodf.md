---
title: Muster in extension/ezodf
audience: developer
category: standards
order: 450
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezodf mit den Entwurfsmustern.
keywords: extension/ezodf, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezodf

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezodf` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (39)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expOdfAiAssist` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfaiassist.php:20` | KI-Unterstützung des Imports (expai, optional): für ein importiertes Dokument eine Zusammenfassung für das Intro und Schlüsselwörter, als Vorschläge, die eine Person im Batch… |
| `expOdfBatch` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezodf/classes/converter/expodfbatch.php:25` | Ein Import von Dokumenten: Dateien, ein Ordner (sein Baum), ZIP-Archive und URLs, hochgeladen auf der Seite Dokumente importieren. |
| `expOdfCleanup` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfcleanup.php:15` | Entfernt, was ezodf zurücklässt: Arbeits- und Spool-Verzeichnisse von Konvertierungen (älter als eine Stunde: eine Konvertierung dauert nie so lange), hochgeladene Batches… |
| `expOdfConverter` | class | [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfconverter.php:29` | Konvertiert Dokumente. |
| `expOdfConverterAdmin` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfconverteradmin.php:14` | Die Daten und die Aktionen der Seite Document converter (ezodf/converter) und ihr Live-Zustand (ezodf/converterstatus). |
| `expOdfConverterException` | class extends RuntimeException | [Ausnahmehierarchie](ausnahme.md) | `extension/ezodf/classes/converter/expodfconverterexception.php:14` | Eine fehlgeschlagene Konvertierung. |
| `expOdfConverterTest` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfconvertertest.php:15` | Die Testkonvertierungen der Seite Dokumentkonverter: Jedes mitgelieferte Beispiel (share/samples) wird mit dem Konverter konvertiert, wie er eingerichtet ist (sein Backend… |
| `expOdfDocumentsAdmin` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfdocumentsadmin.php:13` | Die Daten der Seiten Import documents, batch und runs (ezodf/import, ezodf/batch, ezodf/runs). |
| `expOdfExporter` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfexporter.php:18` | Items als Dokumente aus. |
| `expOdfFilterCatalogue` | class | [Dekorierer / Filter](dekorierer.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodffiltercatalogue.php:16` | Die Import- und Exportfilter einer LibreOffice-Installation, gelesen aus ihren Dateien share/registry/*.xcd (die Filters und Types der Typerkennung), damit… |
| `expOdfHtmlToOdt` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezodf/classes/readers/expodfhtmltoodt.php:47` | Schreibt das HTML von expOdfReaders als ODT-Text, den der ODT-Parser von ezodf (eZOOImport) importiert: |
| `expOdfImportDocumentFeedSource` | class extends expOdfImportDocumentUrlsSource | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/sqliimport/expodfimportdocumentfeedsource.php:18` | „Dokumente eines Feeds“ von 7x SQLI Import: ein RSS- oder Atom-Feed, dessen Einträge auf Dokumente verweisen (Enclosures oder Links, die auf eine Dokumenterweiterung enden: ein pu… |
| `expOdfImportDocumentSource` | class extends expImportJsonSource | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezodf/classes/sqliimport/expodfimportdocumentsource.php:30` | Die Dokumentformate von 7x SQLI Import (sqliimport 2.x), von ezodf in seiner kernelservices.ini mit dem Tag sqliimport.source deklariert; nur von s… |
| `expOdfImportDocumentUrlsSource` | class extends expOdfImportDocumentSource | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/sqliimport/expodfimportdocumenturlssource.php:16` | „Dokumente von URLs“ von 7x SQLI Import: eine Liste von Adressen von Dokumenten (eine pro Zeile). |
| `expOdfImportGoogleDriveSource` | class extends expOdfImportDocumentSource | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/sqliimport/expodfimportgoogledrivesource.php:33` | „Dokumente in einem Google-Drive-Ordner“ von 7x SQLI Import: die Dokumente eines Drive-Ordners (in Meine Ablage oder einem geteilten Laufwerk) und seiner Unterordner bis zu… |
| `expOdfImportHttp` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/sqliimport/expodfimporthttp.php:27` | Die HTTP-Anfragen der Remote-Dokumentquellen von 7x SQLI Import (WebDAV und Nextcloud, Google Drive, OneDrive und SharePoint), mit der Adresse po… |
| `expOdfImportMailboxDocumentsSource` | class extends expOdfImportDocumentSource | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/sqliimport/expodfimportmailboxdocumentssource.php:31` | „Dokumente als E-Mail-Anhänge (IMAP)“ von 7x SQLI Import: die Dokumentanhänge der Nachrichten eines IMAP-Postfachs (eine Einreichungsadresse, eine Pres… |
| `expOdfImportOneDriveSource` | class extends expOdfImportDocumentSource | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/sqliimport/expodfimportonedrivesource.php:36` | „Dokumente in einem OneDrive- oder SharePoint-Ordner“ von 7x SQLI Import: die Dokumente eines Ordners eines OneDrive-for-Business-Laufwerks oder einer SharePoint-Dokument… |
| `expOdfImportWebdavSource` | class extends expOdfImportDocumentSource | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/sqliimport/expodfimportwebdavsource.php:33` | „Dokumente in einem WebDAV- oder Nextcloud-Ordner“ von 7x SQLI Import: die Dokumente eines Ordners auf einem WebDAV-Server (Nextcloud, ownCloud, ein SharePoint-WebDAV… |
| `expOdfInstallation` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezodf/classes/converter/expodfinstallation.php:22` | Eine LibreOffice-Installation, die ezodf verwenden kann: |
| `expOdfInstaller` | class | – | `extension/ezodf/classes/converter/expodfinstaller.php:30` | Die Ein-Klick-Installation von LibreOffice, ohne root: der offizielle Build der Document Foundation wird nach var//ezodf/libreoffice/ heruntergeladen, ve… |
| `expOdfJobs` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfjobs.php:24` | Hintergrundarbeit von ezodf, aus der Administration gestartet und nie innerhalb einer Webanfrage ausgeführt: |
| `expOdfPaths` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfpaths.php:24` | Wo ezodf 7 seine Dateien ablegt, alle unterhalb des var-Verzeichnisses der Site (var//ezodf/): |
| `expOdfPhpConverter` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfphpconverter.php:25` | Der letzte Fallback des Konverters: nur PHP, kein LibreOffice. |
| `expOdfQueueHandler` | class extends expQueueHandlerBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/ezodf/classes/converter/expodfqueuehandler.php:14` | Der expqueue-Handler der Jobs von ezodf (expqueue.ini [JobType_ezodf.*]); die eigentliche Arbeit steht in expOdfJobs::run(). |
| `expOdfReaders` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezodf/classes/readers/expodfreaders.php:39` | Liest Dokumente ohne LibreOffice und verwandelt sie in ein sicheres HTML-Fragment plus die darin enthaltenen Bilder: |
| `expOdfRemoteFetcher` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfremotefetcher.php:24` | Lädt ein Dokument von einer URL herunter, die ein Redakteur angegeben hat, ohne dass die URL das eigene Netzwerk des Servers erreichen kann (Server-Side Request Forgery): |
| `expOdfRemoteServer` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfremoteserver.php:22` | Ein Konvertierungsdienst an anderer Stelle, eingestellt auf der Seite Dokumentkonverter (converter.remote_url, remote_kind, optional ein Benutzer und Passwort für HTTP-Basic-A… |
| `expOdfSandbox` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfsandbox.php:23` | Führt LibreOffice (und das Worker-Skript von ezodf) in einer Sandbox aus: |
| `expOdfServer` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfserver.php:23` | Der Konvertierungsserver: ein oder mehrere langlebige Headless-LibreOffice-Prozesse, jeder vom Worker-Skript von ezodf (bin/ezodfworker.py) überwacht, im… |
| `expOdfSettings` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezodf/classes/converter/expodfsettings.php:22` | Die Einstellungen von ezodf 7, wie die Administration sie ändert: Die Einstellungsseiten schreiben var//ezodf/settings.json (der eigene Speicher der Extension, nie setti… |
| `Exponential\View\Extension\Ezodf\Ezodf\Export` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezodf/classes/runnable/views/ezodf/export.php:71` | Der Code von extension/ezodf/modules/ezodf/export.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezodf\Ezodf\Import` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezodf/classes/runnable/views/ezodf/import.php:44` | Der Code von extension/ezodf/modules/ezodf/import.php, in eine Klasse verschoben (#207 Stufe 1). |
| `ezodfInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezodf/ezinfo.php:12` | – |
| `eZOOConverter` | class | [Strategie / Handler](strategie-handler.md) | `extension/ezodf/classes/ezooconverter.php:41` | – |
| `eZOOGenerator` | class | [Builder](builder.md) | `extension/ezodf/classes/ezoogenerator.php:41` | – |
| `eZOOImport` | class | – | `extension/ezodf/classes/ezooimport.php:41` | – |
| `eZOpenofficeUploadHandler` | class extends eZContentUploadHandler | [Strategie / Handler](strategie-handler.md) | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php:41` | – |
| `eZRESTODFHandler` | class extends eZRESTBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/ezodf/classes/ezrestodfhandler.php:46` | – |

## Modulansichten (11)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezodf/import` | `import.php` | import | `extension/ezodf/modules/ezodf/module.php:29` |
| `/ezodf/export` | `export.php` | export | `extension/ezodf/modules/ezodf/module.php:37` |
| `/ezodf/upload` | `upload.php` | import | `extension/ezodf/modules/ezodf/module.php:45` |
| `/ezodf/batch` | `batch.php` | import | `extension/ezodf/modules/ezodf/module.php:50` |
| `/ezodf/batchstatus` | `batchstatus.php` | import | `extension/ezodf/modules/ezodf/module.php:56` |
| `/ezodf/runs` | `runs.php` | import | `extension/ezodf/modules/ezodf/module.php:61` |
| `/ezodf/exportfile` | `exportfile.php` | export | `extension/ezodf/modules/ezodf/module.php:67` |
| `/ezodf/exportstatus` | `exportstatus.php` | export | `extension/ezodf/modules/ezodf/module.php:72` |
| `/ezodf/converter` | `converter.php` | converter | `extension/ezodf/modules/ezodf/module.php:77` |
| `/ezodf/converterstatus` | `converterstatus.php` | converter | `extension/ezodf/modules/ezodf/module.php:83` |
| `/ezodf/converterfile` | `converterfile.php` | converter | `extension/ezodf/modules/ezodf/module.php:88` |

## Richtlinienfunktionen (3)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `ezodf/import` | – | `extension/ezodf/modules/ezodf/module.php:94` |
| `ezodf/export` | – | `extension/ezodf/modules/ezodf/module.php:95` |
| `ezodf/converter` | – | `extension/ezodf/modules/ezodf/module.php:97` |

## Cronjob-Teile (1)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `ezodf_keepalive` | `extension/ezodf/cronjobs/ezodf_keepalive.php` | Cron-Teil von ezodf: hält den Konvertierungsserver am Laufen, wenn die Administration ihn eingeschaltet hat (nach einem Neustart oder Absturz), und entfernt Arbeitsdateien, Upl… |

## INI-Plugin-Punkte (14)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[JobType_ezodf.install] Handler` | `expOdfQueueHandler` | `extension/ezodf/settings/expqueue.ini.append.php:5` |
| `[JobType_ezodf.server] Handler` | `expOdfQueueHandler` | `extension/ezodf/settings/expqueue.ini.append.php:12` |
| `[JobType_ezodf.test] Handler` | `expOdfQueueHandler` | `extension/ezodf/settings/expqueue.ini.append.php:19` |
| `[JobType_ezodf.import] Handler` | `expOdfQueueHandler` | `extension/ezodf/settings/expqueue.ini.append.php:26` |
| `[JobType_ezodf.export] Handler` | `expOdfQueueHandler` | `extension/ezodf/settings/expqueue.ini.append.php:33` |
| `[RESTSettings] HandlerList[]` | `eZRESTODFHandler` | `extension/ezodf/settings/ezrest.ini.append.php:4` |
| `[Service_transfer.ezodf] Class` | `expKernelTransferTool` | `extension/ezodf/settings/kernelservices.ini.append.php:9` |
| `[Source_documents] Class` | `expOdfImportDocumentSource` | `extension/ezodf/settings/sqliimport.ini.append.php:16` |
| `[Source_document_urls] Class` | `expOdfImportDocumentUrlsSource` | `extension/ezodf/settings/sqliimport.ini.append.php:19` |
| `[Source_document_feed] Class` | `expOdfImportDocumentFeedSource` | `extension/ezodf/settings/sqliimport.ini.append.php:22` |
| `[Source_webdav] Class` | `expOdfImportWebdavSource` | `extension/ezodf/settings/sqliimport.ini.append.php:25` |
| `[Source_google_drive] Class` | `expOdfImportGoogleDriveSource` | `extension/ezodf/settings/sqliimport.ini.append.php:28` |
| `[Source_onedrive] Class` | `expOdfImportOneDriveSource` | `extension/ezodf/settings/sqliimport.ini.append.php:31` |
| `[Source_mailbox_documents] Class` | `expOdfImportMailboxDocumentsSource` | `extension/ezodf/settings/sqliimport.ini.append.php:34` |
