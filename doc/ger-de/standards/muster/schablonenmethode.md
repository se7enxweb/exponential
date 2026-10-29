---
title: Schablonenmethode / abstrakte Basisklasse
audience: developer
category: standards
order: 325
summary: Eine abstrakte Klasse, die die Schritte eines Algorithmus festlegt und einige davon ihren Unterklassen überlässt. 69 Instanzen in Exponential.
keywords: Entwurfsmuster, Schablonenmethode, Verhalten, Musterkatalog, design pattern, template-method, pattern catalogue
type: catalogue
---
# Schablonenmethode / abstrakte Basisklasse

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Eine abstrakte Klasse, die die Schritte eines Algorithmus festlegt und einige davon ihren Unterklassen überlässt.

| | |
| --- | --- |
| Gruppe | Verhalten |
| Instanzen | 69 |
| Erkannt an | Eine abstrakte Klasse mit abstrakten und konkreten Methoden. |
| Erweiterungspunkt | Erweitern Sie die Basisklasse und implementieren Sie ihre abstrakten Methoden. |

## Klassen nach Bereich

### extension/cjw_newsletter (1)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterSmsTransportBase` | abstract class | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransportbase.php:29` | Was die mitgelieferten SMS-Transporte gemeinsam haben: die Einstellungen mit ihrer Voreinstellung, die generische Prüfung einer eingehenden Anfrage (ein HMAC des Inhalts mit einem gemeinsamen Gehe… | – |

### extension/expai (6)

[Alle Muster von extension/expai](area-extension-expai.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAiBatchApi` | abstract class | `extension/expai/classes/lanes/expaibatchapi.php:27` | Die Batch-API eines Anbieters: viele Anfragen auf einmal einreichen, den Batch abfragen, die Ergebnisse abholen, abbrechen, löschen. | extension/expai/settings/rad.ini [Registry_expai_batch_apis] Implements |
| `expAiProviderBase` | abstract class | `extension/expai/classes/expaiproviderbase.php:16` | Was jeder eingebaute Anbieter gemeinsam hat: seine Einstellungen (die [Provider_]-Werte, mit den [Model_]-Werten zusammengeführt, siehe expAiProviderRegistry::settingsFor()),… | – |
| `expAiSegmenterBase` | abstract class | `extension/expai/classes/diff/expaisegmenterbase.php:14` | Der gemeinsame Teil der Nur-Text-Segmentierer: Blöcke sind Textstücke, durch ein Trennzeichen verbunden; merge() und compose() bauen die Liste der Stücke und fügen sie zusammen. | – |
| `expAiTool` | abstract class | `extension/expai/classes/expaitool.php:16` | Ein Tool, das der Assistent aufrufen kann. | extension/expai/settings/rad.ini [Registry_expai_tools] Entries[Class] |
| `expAiWebSearchProvider` | abstract class | `extension/expai/classes/web/expaiwebsearchprovider.php:16` | Ein Websuchdienst für web_search. | extension/expai/settings/rad.ini [Registry_expai_search_providers] Entries[Class] |
| `expAiWebTool` | abstract class extends expAiTool | `extension/expai/classes/web/expaiwebtool.php:19` | Die Basis der Tools, die außerhalb des Inhaltsbaums lesen (web_fetch, web_search, project_history): - available(): nur einem Benutzer mit der Richtlinie angeboten… | – |

### extension/explayouts (1)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsAbstractContentBlockHandler` | abstract class | `extension/explayouts/classes/explayoutsabstractcontentblockhandler.php:3` | – | – |

### extension/expquery_translator (2)

[Alle Muster von extension/expquery_translator](bereich-extension-expquery-translator.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `QueryTranslator\Languages\Galach\Generators\Lucene\Common\WordBase` | abstract class extends Visitor | `extension/expquery_translator/lib/Languages/Galach/Generators/Lucene/Common/WordBase.php:14` | Basisimplementierung des Word-Node-Visitors. | – |
| `QueryTranslator\Languages\Galach\TokenExtractor` | abstract class | `extension/expquery_translator/lib/Languages/Galach/TokenExtractor.php:14` | Der Token-Extraktor wird vom Tokenizer verwendet, um Tokens aus der Eingabezeichenkette zu extrahieren. | – |

### extension/expqueue (1)

[Alle Muster von extension/expqueue](area-extension-expqueue.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expQueueHandlerBase` | abstract class | `extension/expqueue/classes/expqueuehandlerbase.php:12` | Eine Basis für Handler mit den optionalen Teilen: validate() wird aufgerufen, wenn ein Job in die Warteschlange gestellt wird (eine falsche Nutzlast wird dort sofort abgelehnt, nicht nachts um 3 Uhr. | – |

### extension/expservices (5)

[Alle Muster von extension/expservices](bereich-extension-expservices.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAttrServiceBase` | abstract class extends expServiceBase | `extension/expservices/classes/media/expattrservicebase.php:11` | Gemeinsame Hilfsfunktionen der Mediendienste (Bilder, Dateien, Medien): das Attribut eines Knotens mit einem bestimmten Datentyp finden, absolute URLs und die seitenweise Auflistung de… | – |
| `expCommunityBase` | abstract class extends expServiceBase | `extension/expservices/classes/community/expcommunitybase.php:12` | Die gemeinsame Arbeit der Dienste für Forum, Thema, Antwort und Kommentar: es sind Inhaltsdienste über konfigurierbare Inhaltsklassen ([Community] in expservi… | – |
| `expContentServiceBase` | abstract class extends expServiceBase | `extension/expservices/classes/content/expcontentbase.php:12` | Der gemeinsame Teil der Inhaltsdomänen von expservices: Fetch-Helfer mit den Zugriffsprüfungen, die Exporter, die Inhaltsobjekte in einfache Array… | – |
| `expServiceBase` | abstract class extends ezjscServerFunctions | `extension/expservices/classes/expservicebase.php:38` | – | – |
| `expUsersBase` | abstract class extends expServiceBase | `extension/expservices/classes/users/expusersbase.php:21` | Der gemeinsame Teil der Benutzer- und Zugriffsdomänen von expservices: das kompakte Deklarationsformat und die Helfer, die Benutzer und Gruppen ohne e… | – |

### extension/ezoe (1)

[Alle Muster von extension/ezoe](bereich-extension-ezoe.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOEEditorEngineBase` | abstract class | `extension/ezoe/classes/expoeeditorenginebase.php:14` | Bequeme Basis für Editor-Engines: liest den eigenen ezoe.ini-Block der Engine [Engine_] (ToolbarMap[]=, ExternalPlugins[]=, Scripts[], Styles[], Version,… | – |

### extension/sevenx_authentication_2fa (1)

[Alle Muster von extension/sevenx_authentication_2fa](bereich-extension-sevenx-authentication-2fa.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZOAuthUser` | abstract class extends eZUser | `extension/sevenx_authentication_2fa/classes/ezoauthuser.php:26` | – | – |

### extension/sevenx_dse (8)

[Alle Muster von extension/sevenx_dse](bereich-extension-sevenx-dse.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `AdminNeo\Connection` | abstract class | `extension/sevenx_dse/adminneo/core/Connection.php:5` | – | – |
| `AdminNeo\Driver` | abstract class | `extension/sevenx_dse/adminneo/core/Driver.php:5` | – | – |
| `AdminNeo\MsSqlPdoConnection` | abstract class extends PdoConnection | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:236` | – | – |
| `AdminNeo\Origin` | abstract class extends Plugin | `extension/sevenx_dse/adminneo/core/Origin.php:7` | – | – |
| `AdminNeo\PdoConnection` | abstract class extends Connection | `extension/sevenx_dse/adminneo/include/pdo.inc.php:11` | – | – |
| `AdminNeo\Plugin` | abstract class | `extension/sevenx_dse/adminneo/core/Plugin.php:13` | Plugin-Elternklasse. | – |
| `AdminNeo\Result` | abstract class | `extension/sevenx_dse/adminneo/core/Result.php:7` | – | – |
| `AdminNeo\SqLiteConnectionBase` | abstract class extends Connection | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:18` | – | – |

### extension/sqliimport (8)

[Alle Muster von extension/sqliimport](area-extension-sqliimport.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expImportAbstractSource` | abstract class | `extension/sqliimport/classes/exp/expimportabstractsource.php:14` | Der gemeinsame Teil der Dateiquellen: die Konfiguration, der Datensatzbezug aus einem Schlüsselfeld, das Beispiel und die Textkodierung. | – |
| `expImportContentLineSource` | abstract class extends expImportAbstractSource | `extension/sqliimport/classes/exp/sources/expimportcontentlinesource.php:21` | Der gemeinsame Teil der Textformate aus „Inhaltszeilen“ (iCalendar, RFC 5545, und vCard, RFC 6350 und 2426): | – |
| `expImportCrawlSource` | abstract class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimportcrawlsource.php:16` | Der gemeinsame Teil der Formate, die öffentliche Webseiten lesen (sitemap, webpage). | – |
| `expImportDataTableFieldSourceBase` | abstract class | `extension/sqliimport/classes/exp/expimportdatatablefieldsourcebase.php:17` | Die Felder der Quelle eines Imports für den „Module table“-Assistenten des Kernels (Exponential 6.0.15, die Datentabellen-Schicht): die Karte „Create a new table from… | – |
| `expImportHttpQuerySource` | abstract class extends expImportJsonSource | `extension/sqliimport/classes/exp/sources/expimporthttpquerysource.php:19` | Was die Quellen gemeinsam haben, die eine Abfrage in der eigenen Sprache einer Datenbank an deren HTTP-API senden (Neo4j, ArangoDB, ClickHouse, Snowflake, InfluxDB): die… | – |
| `expImportShopCsvSource` | abstract class extends expImportCsvSource | `extension/sqliimport/classes/exp/sources/expimportshopcsvsource.php:18` | Die Produktexporte von Shop-Systemen als CSV, bei denen ein Produkt mehrere Zeilen umfassen kann (seine Varianten, seine Bilder): Die Datei wird als CSV-Quelle gele… | – |
| `SQLIImportAbstractHandler` | abstract class | `extension/sqliimport/classes/sourcehandlers/sqliimportabstracthandler.php:13` | Abstrakte Klasse für alle Import-Handler. Alle Import-Handler müssen von dieser Klasse erben | – |
| `SQLIImportOptions` | abstract class extends ezcBaseOptions | `extension/sqliimport/classes/options/sqliimportoptions.php:18` | Basis aller Optionsklassen für SQLIImport. Einfache Überschreibung von ezcBaseOptions aus eZComponents, die das Interface Iterator implementiert | – |

### extension/xrowextract (1)

[Alle Muster von extension/xrowextract](bereich-extension-xrowextract.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `XrowExtractTransport` | abstract class | `extension/xrowextract/classes/transports/xrowextracttransport.php:10` | Ein Weg, eine Datei an ein Ziel zu liefern (oder, bei einem geplanten Import, von dort abzuholen). | – |

### extension/xrowmetadata (1)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `xrowSitemapItem` | abstract class extends ezcBaseStruct | `extension/xrowmetadata/classes/structs/xrowsitemapitem.php:3` | – | – |

### kernel/classes (1)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZURLAliasFilter` | abstract class | `kernel/classes/ezurlaliasfilter.php:22` | Die Klasse eZURLAliasFilter ist die Schnittstelle für alle URL-Alias-Filter; die Filterimplementierung muss die Methode process implementieren. | – |

### kernel/classes/audit (4)

[Alle Muster von kernel/classes/audit](bereich-kernel-classes-audit.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAuditAlertRuleBase` | abstract class | `kernel/classes/audit/alerts/expauditalertrulebase.php:13` | Gemeinsamer Code der eingebauten Alarmregel-Klassen: die Datensatzfelder, nach denen eine Regel gruppiert, die Zeit eines Datensatzes und die Deduplizierung (doc/eng-us/bc/6.0/a… | – |
| `expAuditFormatBase` | abstract class | `kernel/classes/audit/format/expauditformatbase.php:14` | Gemeinsamer Code der Archivformat-Handler (doc/eng-us/bc/6.0/audit.md, „Rotation, archives and retention“): Streaming-Kompression über einen PHP-Stre… | – |
| `expAuditSinkBase` | abstract class | `kernel/classes/audit/sinks/expauditsinkbase.php:14` | Gemeinsamer Code der Audit-Senken: der Einstellungsblock der Senke ([AuditSink_]), welche Datensätze sie haben will (Events[]-Muster und MinSeverity) und ob sie d… | – |
| `expAuditSourceBase` | abstract class | `kernel/classes/audit/console/expauditsourcebase.php:11` | Die Basis einer Audit-Konsolenquelle (expAuditSource): jede Methode antwortet "nichts", sodass eine Quelle nur implementiert, was sie anzeigt. | – |

### kernel/classes/contentjob (1)

[Alle Muster von kernel/classes/contentjob](bereich-kernel-classes-contentjob.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentJobBatchType` | abstract class | `kernel/classes/contentjob/expcontentjobbatchtype.php:28` | Die gemeinsame Grundlage der Inhaltsjob-Typen, die auf einer Liste von Knoten arbeiten: move, hide, reveal, section, state, addlocation und removelocation. | – |

### kernel/classes/ini (1)

[Alle Muster von kernel/classes/ini](bereich-kernel-classes-ini.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expIniActionBase` | abstract class | `kernel/classes/ini/actions/expiniactionbase.php:15` | Was die eingebauten exp:ini-Aktionen gemeinsam haben: die drei beschreibenden Methoden aus Klassenkonstanten, die Ausgabe eines Werts als INI-Zeilen und die kleine Prüf… | – |

### kernel/classes/requestclassifier (1)

[Alle Muster von kernel/classes/requestclassifier](bereich-kernel-classes-requestclassifier.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expRequestPatternRuleSet` | abstract class | `kernel/classes/requestclassifier/exprequestpatternruleset.php:29` | Ein Regelsatz aus regulären Ausdrücken: einer pro Regel, alle zu einem kombinierten Ausdruck kompiliert, sodass eine Anfrage einen preg_match() pro Subj… | – |

### kernel/classes/subitems (2)

[Alle Muster von kernel/classes/subitems](bereich-kernel-classes-subitems.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSubitemsColumn` | abstract class | `kernel/classes/subitems/expsubitemscolumn.php:19` | Die Basisklasse einer Spalte der Admin-Unterelementliste ("Table options"). | – |
| `expSubitemsFieldColumn` | abstract class extends expSubitemsColumn | `kernel/classes/subitems/columns/expsubitemsfieldcolumn.php:22` | Die gemeinsame Basis der mitgelieferten Spaltenfamilien der Admin-Unterelementliste. | – |

### kernel/classes/syscard (1)

[Alle Muster von kernel/classes/syscard](area-kernel-classes-syscard.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSysCardBaseProvider` | abstract class | `kernel/classes/syscard/expsyscardbaseprovider.php:23` | Der übliche Anbieter: die Eigenschaften setzen, collect() schreiben und fact() sowie die Helfer verwenden. | – |

### kernel/classes/uiswitch (1)

[Alle Muster von kernel/classes/uiswitch](bereich-kernel-classes-uiswitch.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expUISwitchProvider` | abstract class | `kernel/classes/uiswitch/expuiswitchprovider.php:18` | Der gemeinsame Teil eines UI-Umschalt-Anbieters: seine Einstellungsgruppe uiswitch.ini [UISwitch_], die Erlaubnisliste aus AllowedValues[] (jeder Wert geprüft durch die… | – |

### kernel/modules (2)

[Alle Muster von kernel/modules](bereich-kernel-modules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expExtensionWizard` | abstract class | `kernel/setup/expextensionwizard.php:20` | Was jeder Extension-Assistent braucht, gleich welche Art von Extension er erstellt. | – |
| `ezpClusterGateway` | abstract class | `kernel/clustering/gateway.php:15` | Basis für ein Cluster-Gateway. | – |

### kernel/private (12)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpAsynchronousPublishingFilter` | abstract class | `kernel/private/classes/ezpasynchronouspublishingfilter.php:24` | Diese Schnittstelle dient zum Erstellen von Annahme-/Ablehnungsfiltern für die Funktion Asynchronous Publishing; jede Filterklasse muss die Methode accept() imp… | – |
| `ezpContentException` | abstract class extends ezcBaseException | `kernel/private/api/content/exceptions/content_exception.php:17` | Dies ist die Basisausnahme der Inhalts-API von Exponential | – |
| `ezpOauthBadRequestException` | abstract class extends ezpOauthException | `kernel/private/rest/classes/exceptions/bad_request.php:17` | Dies ist die Basisausnahme zum Auslösen einer BAD-REQUEST-Antwort. | – |
| `ezpOauthException` | abstract class extends ezcBaseException | `kernel/private/rest/classes/exceptions/oauth.php:17` | Dies ist die Basisausnahme der OAuth-Bibliothek von Exponential. | – |
| `ezpOauthRequiredException` | abstract class extends ezpOauthException | `kernel/private/rest/classes/exceptions/auth.php:17` | Dies ist die Basisausnahme für Antworten, die eine Authentifizierung erfordern. | – |
| `ezpRestAuthenticationStyle` | abstract class | `kernel/private/rest/classes/auth/styles/abstract_auth_style.php:11` | – | – |
| `ezpRestCacheStorageCluster` | abstract class extends ezpRestCacheStorageFile | `kernel/private/rest/classes/cache/cluster.php:11` | – | – |
| `ezpRestException` | abstract class extends ezcBaseException | `kernel/private/rest/classes/exceptions/rest_exception.php:17` | Dies ist die Basisausnahme der REST-Schicht von Exponential | – |
| `ezpRestMvcController` | abstract class extends ezcMvcController | `kernel/private/rest/classes/controllers/rest_controller.php:15` | Abstrakte Klasse, die jeder REST-Controller erweitern muss | – |
| `ezpRestPrefixFilterInterface` | abstract class | `kernel/private/rest/classes/prefix_filter.php:12` | – | – |
| `ezpRestRouteFilterInterface` | abstract class | `kernel/private/rest/classes/interfaces/route_filter.php:11` | – | – |
| `ezpUpdatedContent` | abstract class | `kernel/private/rest/classes/sync/updates.php:23` | Diese Klasse stellt eine API bereit, über die man nach aktualisierten Inhaltselementen abfragen kann. | – |

### kernel/private/classes/runnable (4)

[Alle Muster von kernel/private/classes/runnable](bereich-kernel-private-classes-runnable.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\Runnable\Command` | abstract class extends Runnable | `kernel/private/classes/runnable/command.php:36` | Ein CLI-Befehl. | – |
| `Exponential\Runnable\CronjobPart` | abstract class extends Runnable | `kernel/private/classes/runnable/cronjobpart.php:21` | Ein Cronjob-Teil. | – |
| `Exponential\Runnable\ModuleView` | abstract class extends Runnable | `kernel/private/classes/runnable/moduleview.php:23` | Eine Modulansicht. | – |
| `Exponential\Runnable\Runnable` | abstract class | `kernel/private/classes/runnable/runnable.php:30` | Die Basis von allem, was Exponential aus einer Datei ausführt: CLI-Befehle (bin/), Cronjob-Teile (cronjobs/) und Modulansichten (kernel//.php, extension//module… | – |

### kernel/private/classes/views (1)

[Alle Muster von kernel/private/classes/views](bereich-kernel-private-classes-views.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `Exponential\View\Kernel\Mailpreferences\Page` | abstract class extends ModuleView | `kernel/private/classes/views/mailpreferences/page.php:19` | Was die Ansichten des Moduls mailpreferences gemeinsam haben: die Variablen der einbindenden Funktion, das Rendern einer Seite von design:mailpreferences/, die Antwort… | – |

### lib/ezdb (1)

[Alle Muster von lib/ezdb](bereich-lib-ezdb.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZMySQLCharset` | abstract class | `lib/ezdb/classes/ezmysqlcharset.php:13` | – | – |

### lib/ezsession (1)

[Alle Muster von lib/ezsession](bereich-lib-ezsession.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpSessionHandler` | abstract class | `lib/ezsession/classes/ezpsessionhandler.php:27` | Abstrakte Session-Handler-Klasse zum Erweitern | – |
