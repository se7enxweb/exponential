---
title: Registry
audience: developer
category: standards
order: 323
summary: Ein zentraler Ort, an dem Objekte oder Callbacks unter einem Namen registriert und später nachgeschlagen werden. 39 Instanzen in Exponential.
keywords: Entwurfsmuster, Registry, Struktur, Musterkatalog, design pattern, registry, pattern catalogue
type: catalogue
---
# Registry

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Ein zentraler Ort, an dem Objekte oder Callbacks unter einem Namen registriert und später nachgeschlagen werden.

| | |
| --- | --- |
| Gruppe | Struktur |
| Instanzen | 39 |
| Erkannt an | Der Klassenname enthält Registry, oder die Klasse hat statische Methoden register*(). |

## Klassen nach Bereich

### extension/expai (4)

[Alle Muster von extension/expai](area-extension-expai.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAiFieldActionRegistry` | class | `extension/expai/classes/fieldactions/expaifieldactionregistry.php:13` | Die Feldaktionen von expai.ini [EditViewSettings] FieldActions[] und die Beschreibung des Bearbeitungsformulars dazu. | – |
| `expAiProviderRegistry` | class | `extension/expai/classes/expaiproviderregistry.php:24` | Die Anbieter und Modelle von expai.ini: | – |
| `expAiSegmenterRegistry` | class | `extension/expai/classes/diff/expaisegmenterregistry.php:15` | Der Segmentierer eines Datentyps: expai.ini [DiffSettings] Segmenters[]=, sonst FallbackSegmenter (expAiSegmenterGeneric). | – |
| `expAiToolRegistry` | class | `extension/expai/classes/expaitoolregistry.php:16` | Die Werkzeuge aus expai.ini [ToolSettings] Tools[], jeweils [Tool_] Class=, plus die MCP-Werkzeuge, die der aktuelle Benutzer verwenden darf (expAiMcp::tools(): die In-Process-Br… | – |

### extension/expservices (1)

[Alle Muster von extension/expservices](bereich-extension-expservices.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAccountServices` | class extends expUsersBase | `extension/expservices/classes/users/expaccountservices.php:13` | expaccount: die Kontoabläufe einer Person: Registrierung und Aktivierung, vergessenes Passwort und Zurücksetzen, Ändern des eigenen Passworts und der E-Mail-Adresse, und die… | extension/expservices/settings/ezjscore.ini [ezjscServer_expaccount] Class |

### extension/ezoe (1)

[Alle Muster von extension/ezoe](bereich-extension-ezoe.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOEEditor` | class | `extension/ezoe/classes/expoeeditor.php:18` | Die Registry der Editor-Engines und die Helfer, die jeder Aufrufer teilt: die Templates, das Modul ezoe, die ezjscore-Funktionen und die expservices-Klasse alle… | – |

### extension/nxc_powercontent (1)

[Alle Muster von extension/nxc_powercontent](bereich-extension-nxc-powercontent.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZContentOperationCollection` | class | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php:20` | – | – |

### extension/sqliimport (3)

[Alle Muster von extension/sqliimport](area-extension-sqliimport.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expImportRegistry` | class | `extension/sqliimport/classes/exp/engine/expimportregistry.php:23` | Der Katalog einer Site: ihre Quellen, Handler und Transformationen. | extension/sqliimport/settings/kernelservices.ini [Service_sqliimport.registry] Class |
| `expImportTransforms` | class | `extension/sqliimport/classes/exp/expimporttransforms.php:18` | Die Transformationen eines Mappings: die eingebauten und die der Kernel-Services mit dem Tag sqliimport.transform. | – |
| `SQLIImportToken` | class extends eZPersistentObject | `extension/sqliimport/classes/sqliimporttoken.php:12` | Token-Klasse für SQLI Import, behandelt den Import-Status | – |

### kernel/classes (5)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCacheTrash` | class | `kernel/classes/ezcachetrash.php:38` | Entfernt ein Cache-Verzeichnis, indem es zuerst aus dem Weg umbenannt wird. | – |
| `eZDataType` | class | `kernel/classes/ezdatatype.php:46` | – | – |
| `eZWorkflowEventType` | class extends eZWorkflowType | `kernel/classes/ezworkfloweventtype.php:18` | – | – |
| `eZWorkflowGroupType` | class extends eZWorkflowType | `kernel/classes/ezworkflowgrouptype.php:18` | – | – |
| `eZWorkflowType` | class | `kernel/classes/ezworkflowtype.php:18` | – | – |

### kernel/classes/audit (4)

[Alle Muster von kernel/classes/audit](bereich-kernel-classes-audit.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAudit` | class | `kernel/classes/audit/expaudit.php:26` | Das Audit: was in Exponential geschieht, aufgezeichnet als hash-verkettete JSON-Zeilen pro Kanal (doc/eng-us/bc/6.0/audit.md, „The developer API (Z3)“). | – |
| `expAuditFormatRegistry` | class | `kernel/classes/audit/format/expauditformatregistry.php:16` | Die Archivformat-Registry: [AuditArchiveSettings] FormatHandlers[]= (RAD-Erhebungs-Registry „auditformats“). | – |
| `expAuditSinkRegistry` | class | `kernel/classes/audit/sinks/expauditsinkregistry.php:23` | Die Sink-Registry und die Zustellung (doc/eng-us/bc/6.0/audit.md, „Sinks (Q7)“, „The sink registry“). | – |
| `expAuditSourceRegistry` | class | `kernel/classes/audit/console/expauditsourceregistry.php:16` | Die Registry der Quellen der Audit-Konsole (expAuditSource), aus audit.ini [AuditSourceSettings] Sources[]: welche registriert sind, welche hier funktionieren,… | – |

### kernel/classes/datatypes (1)

[Alle Muster von kernel/classes/datatypes](bereich-kernel-classes-datatypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZURL` | class extends eZPersistentObject | `kernel/classes/datatypes/ezurl/ezurl.php:23` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |

### kernel/classes/debugbar (1)

[Alle Muster von kernel/classes/debugbar](bereich-kernel-classes-debugbar.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expDebugBarRegistry` | class | `kernel/classes/debugbar/expdebugbarregistry.php:20` | Liest settings/debugbar.ini (und die debugbar.ini.append.php jeder Extension, zusammengeführt von eZINI): | – |

### kernel/classes/help (2)

[Alle Muster von kernel/classes/help](bereich-kernel-classes-help.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expHelpContextRegistry` | class | `kernel/classes/help/exphelpcontextregistry.php:45` | Kontexthilfe: welche Hilfeseite „Hilfe zu dieser Seite“ beantwortet, für ein Modul, eine Ansicht und ein Design. | – |
| `expHelpHighlighter` | class | `kernel/classes/help/exphelphighlighter.php:29` | Serverseitige Syntaxhervorhebung der Codeblöcke der Hilfeseiten (exphelp, doc/eng-us/features/6.0/help-system.md), ohne JavaScript oder eine externe… | – |

### kernel/classes/ini (2)

[Alle Muster von kernel/classes/ini](bereich-kernel-classes-ini.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expIniActionRegistry` | class | `kernel/classes/ini/actions/expiniactionregistry.php:22` | Die Aktionen des Befehls exp:ini und die Bereichsanbieter des Einstellungseditors, gelesen aus ini.ini [IniCommandSettings]: | – |
| `expIniEditor` | class | `kernel/classes/ini/expinieditor.php:27` | Der INI-Editor. | – |

### kernel/classes/mailpreferences (1)

[Alle Muster von kernel/classes/mailpreferences](bereich-kernel-classes-mailpreferences.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMailCategoryRegistry` | class | `kernel/classes/mailpreferences/expmailcategoryregistry.php:26` | Alle E-Mail-Kategorien, in der Reihenfolge, in der die Präferenzseite sie zeigt: | – |

### kernel/classes/notification (1)

[Alle Muster von kernel/classes/notification](bereich-kernel-classes-notification.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZNotificationEventType` | class | `kernel/classes/notification/eznotificationeventtype.php:18` | – | – |

### kernel/classes/subitems (1)

[Alle Muster von kernel/classes/subitems](bereich-kernel-classes-subitems.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSubitemsColumnRegistry` | class | `kernel/classes/subitems/expsubitemscolumnregistry.php:23` | Die Registry der Spalten der Unterelementliste der Administrationsoberfläche („Tabellenoptionen“). | – |

### kernel/classes/syscard (1)

[Alle Muster von kernel/classes/syscard](area-kernel-classes-syscard.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSysCardRegistry` | class | `kernel/classes/syscard/expsyscardregistry.php:30` | Die Anbieter der Systemkarte und wie sie laufen (settings/syscard.ini [Providers] und [Runtime]): | – |

### kernel/classes/transfer (1)

[Alle Muster von kernel/classes/transfer](area-kernel-classes-transfer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expKernelTransferRegistry` | class | `kernel/classes/transfer/expkerneltransferregistry.php:30` | Die Transfer-Registry: jedes Import- und Export-Werkzeug der Installation, wie es sich in kernelservices.ini selbst deklariert (ein [Service_transfer.]-Block t… | settings/kernelservices.ini [Service_transfer.registry] Class |

### kernel/classes/workflowtypes (1)

[Alle Muster von kernel/classes/workflowtypes](bereich-kernel-classes-workflowtypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZPaymentGatewayType` | class extends eZWorkflowEventType | `kernel/classes/workflowtypes/event/ezpaymentgateway/ezpaymentgatewaytype.php:20` | – | – |

### kernel/private (1)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpRestDbConfig` | class | `kernel/private/rest/classes/lazy.php:12` | – | – |

### kernel/private/classes/httpcache (1)

[Alle Muster von kernel/private/classes/httpcache](bereich-kernel-private-classes-httpcache.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpHttpCacheListener` | class | `kernel/private/classes/httpcache/ezphttpcachelistener.php:27` | Die Kernel-Seite des rollenbewussten HTTP-Caches: speichert gerenderte Inhaltsansichten, damit der frühe Ausstieg und der Elternprozess von Velocity sie ausliefern, und macht aus den… | – |

### lib/ezdb (1)

[Alle Muster von lib/ezdb](bereich-lib-ezdb.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDBQueryCache` | class | `lib/ezdb/classes/ezdbquerycache.php:37` | Ein Ergebnis-Cache für die SQL-Treiber (MySQL/MariaDB, PostgreSQL, SQLite): die Zeilen, die ein SELECT geliefert hat, werden erneut geliefert, bis ein Schreibvorgang in eine der Tabellen… | – |

### lib/ezi18n (1)

[Alle Muster von lib/ezi18n](bereich-lib-ezi18n.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTranslatorManager` | class | `lib/ezi18n/classes/eztranslatormanager.php:31` | – | – |

### lib/ezimage (1)

[Alle Muster von lib/ezimage](bereich-lib-ezimage.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZImageInterface` | class | `lib/ezimage/classes/ezimageinterface.php:20` | – | – |

### lib/ezsession (1)

[Alle Muster von lib/ezsession](bereich-lib-ezsession.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZSession` | class | `lib/ezsession/classes/ezsession.php:67` | Sitzungsschnittstellenklasse von Exponential | – |

### lib/ezutils (2)

[Alle Muster von lib/ezutils](bereich-lib-ezutils.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZExecution` | class | `lib/ezutils/classes/ezexecution.php:34` | – | – |
| `eZExpiryHandler` | class | `lib/ezutils/classes/ezexpiryhandler.php:16` | Verwaltet Ablaufschlüssel und ihre Zeitstempel | – |
