---
title: Patterns in extension/ezodf
audience: developer
category: standards
order: 449
summary: Every class, view, fetch function, operator and plugin point of extension/ezodf with its design patterns.
keywords: extension/ezodf, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezodf

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezodf` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (39)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expOdfAiAssist` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfaiassist.php:20` | AI assist of the import (expai, opt-in): for an imported document, a summary for the intro and keywords, as suggestions a person applies on the batch… |
| `expOdfBatch` | class | [Factory and static factory methods](factory.md) | `extension/ezodf/classes/converter/expodfbatch.php:25` | One import of documents: files, a folder (its tree), ZIP archives and URLs, uploaded on the Import documents page. |
| `expOdfCleanup` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfcleanup.php:15` | Removes what ezodf leaves behind: work and spool directories of conversions (older than an hour: a conversion never takes that long), uploaded batche… |
| `expOdfConverter` | class | [Strategy / handler](strategy-handler.md), [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfconverter.php:29` | Converts documents. |
| `expOdfConverterAdmin` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfconverteradmin.php:14` | The data and the actions of the Document converter page (ezodf/converter) and its live state (ezodf/converterstatus). |
| `expOdfConverterException` | class extends RuntimeException | [Exception hierarchy](exception.md) | `extension/ezodf/classes/converter/expodfconverterexception.php:14` | A conversion that failed. |
| `expOdfConverterTest` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfconvertertest.php:15` | The test conversions of the Document converter page: each bundled sample (share/samples) is converted with the converter as it is set up (its backend… |
| `expOdfDocumentsAdmin` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfdocumentsadmin.php:13` | The data of the Import documents, batch and runs pages (ezodf/import, ezodf/batch, ezodf/runs). |
| `expOdfExporter` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfexporter.php:18` | Items out as documents. |
| `expOdfFilterCatalogue` | class | [Decorator / filter](decorator.md), [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodffiltercatalogue.php:16` | The import and export filters of a LibreOffice installation, read from its share/registry/*.xcd files (the type detection's Filters and Types), so th… |
| `expOdfHtmlToOdt` | class | [Factory and static factory methods](factory.md) | `extension/ezodf/classes/readers/expodfhtmltoodt.php:47` | Writes the HTML of expOdfReaders as an ODT text that ezodf's ODT parser (eZOOImport) imports: |
| `expOdfImportDocumentFeedSource` | class extends expOdfImportDocumentUrlsSource | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezodf/classes/sqliimport/expodfimportdocumentfeedsource.php:18` | "Documents of a feed" of 7x SQLI Import: an RSS or Atom feed whose items link to documents (enclosures, or links ending in a document extension: a pu… |
| `expOdfImportDocumentSource` | class extends expImportJsonSource | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezodf/classes/sqliimport/expodfimportdocumentsource.php:30` | The document formats of 7x SQLI Import (sqliimport 2.x), declared by ezodf in its kernelservices.ini with the tag sqliimport.source; loaded only by s… |
| `expOdfImportDocumentUrlsSource` | class extends expOdfImportDocumentSource | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezodf/classes/sqliimport/expodfimportdocumenturlssource.php:16` | "Documents from URLs" of 7x SQLI Import: a list of addresses of documents (one per line). |
| `expOdfImportGoogleDriveSource` | class extends expOdfImportDocumentSource | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezodf/classes/sqliimport/expodfimportgoogledrivesource.php:33` | "Documents in a Google Drive folder" of 7x SQLI Import: the documents of a Drive folder (in My Drive or a shared drive) and of its sub folders down t… |
| `expOdfImportHttp` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/sqliimport/expodfimporthttp.php:27` | The HTTP requests of the remote document sources of 7x SQLI Import (WebDAV and Nextcloud, Google Drive, OneDrive and SharePoint), with the address po… |
| `expOdfImportMailboxDocumentsSource` | class extends expOdfImportDocumentSource | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezodf/classes/sqliimport/expodfimportmailboxdocumentssource.php:31` | "Documents attached to e-mails (IMAP)" of 7x SQLI Import: the document attachments of the messages of an IMAP mailbox (a submissions address, a press… |
| `expOdfImportOneDriveSource` | class extends expOdfImportDocumentSource | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezodf/classes/sqliimport/expodfimportonedrivesource.php:36` | "Documents in a OneDrive or SharePoint folder" of 7x SQLI Import: the documents of a folder of a OneDrive for Business drive or a SharePoint document… |
| `expOdfImportWebdavSource` | class extends expOdfImportDocumentSource | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezodf/classes/sqliimport/expodfimportwebdavsource.php:33` | "Documents in a WebDAV or Nextcloud folder" of 7x SQLI Import: the documents of a folder on a WebDAV server (Nextcloud, ownCloud, a SharePoint WebDAV… |
| `expOdfInstallation` | class | [Factory and static factory methods](factory.md) | `extension/ezodf/classes/converter/expodfinstallation.php:22` | A LibreOffice installation ezodf can use: |
| `expOdfInstaller` | class | – | `extension/ezodf/classes/converter/expodfinstaller.php:30` | The one-click installation of LibreOffice, without root: the official build of The Document Foundation is downloaded into var//ezodf/libreoffice/, ve… |
| `expOdfJobs` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfjobs.php:24` | ezodf's background work, started from the administration and never run inside a web request: |
| `expOdfPaths` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfpaths.php:24` | Where ezodf 7 keeps its files, all below the site's var directory (var//ezodf/): |
| `expOdfPhpConverter` | class | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md), [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfphpconverter.php:25` | The last fallback of the converter: PHP only, no LibreOffice. |
| `expOdfQueueHandler` | class extends expQueueHandlerBase | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/ezodf/classes/converter/expodfqueuehandler.php:14` | The expqueue handler of ezodf's jobs (expqueue.ini [JobType_ezodf.*]); the work itself is in expOdfJobs::run(). |
| `expOdfReaders` | class | [Factory and static factory methods](factory.md) | `extension/ezodf/classes/readers/expodfreaders.php:39` | Reads documents without LibreOffice and turns them into a safe HTML fragment plus the images it holds: |
| `expOdfRemoteFetcher` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfremotefetcher.php:24` | Downloads a document from a URL an editor gave, without letting the URL reach the server's own network (server-side request forgery): |
| `expOdfRemoteServer` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfremoteserver.php:22` | A conversion service elsewhere, set on the Document converter page (converter.remote_url, remote_kind, an optional user and password for HTTP basic a… |
| `expOdfSandbox` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfsandbox.php:23` | Runs LibreOffice (and ezodf's worker script) in a sandbox: |
| `expOdfServer` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfserver.php:23` | The conversion server: one or more long-lived headless LibreOffice processes, each supervised by ezodf's worker script (bin/ezodfworker.py) inside th… |
| `expOdfSettings` | class | [Utility (static helper)](utility.md) | `extension/ezodf/classes/converter/expodfsettings.php:22` | The settings of ezodf 7 as the administration changes them: the settings pages write var//ezodf/settings.json (the extension's own store, never setti… |
| `Exponential\View\Extension\Ezodf\Ezodf\Export` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezodf/classes/runnable/views/ezodf/export.php:71` | The code of extension/ezodf/modules/ezodf/export.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezodf\Ezodf\Import` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezodf/classes/runnable/views/ezodf/import.php:44` | The code of extension/ezodf/modules/ezodf/import.php, moved into a class (#207 stage 1). |
| `ezodfInfo` | class | [Value object / struct](value-object.md) | `extension/ezodf/ezinfo.php:12` | – |
| `eZOOConverter` | class | [Strategy / handler](strategy-handler.md) | `extension/ezodf/classes/ezooconverter.php:41` | – |
| `eZOOGenerator` | class | [Builder](builder.md) | `extension/ezodf/classes/ezoogenerator.php:41` | – |
| `eZOOImport` | class | – | `extension/ezodf/classes/ezooimport.php:41` | – |
| `eZOpenofficeUploadHandler` | class extends eZContentUploadHandler | [Strategy / handler](strategy-handler.md) | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php:41` | – |
| `eZRESTODFHandler` | class extends eZRESTBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/ezodf/classes/ezrestodfhandler.php:46` | – |

## Module views (11)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
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

## Policy functions (3)

| Function | Limitations | Defined in |
|---|---|---|
| `ezodf/import` | – | `extension/ezodf/modules/ezodf/module.php:94` |
| `ezodf/export` | – | `extension/ezodf/modules/ezodf/module.php:95` |
| `ezodf/converter` | – | `extension/ezodf/modules/ezodf/module.php:97` |

## Cronjob parts (1)

| Cronjob | File | Description |
|---|---|---|
| `ezodf_keepalive` | `extension/ezodf/cronjobs/ezodf_keepalive.php` | Cron part of ezodf: keeps the conversion server running when the administration turned it on (after a reboot or a crash), and removes work files, upl… |

## INI plugin points (14)

| Setting | Class | Set in |
|---|---|---|
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
