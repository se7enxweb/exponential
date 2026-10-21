---
title: Schnittstelle (Vertrag)
audience: developer
category: standards
order: 326
summary: Ein Vertrag, dem Implementierungen folgen; der Code hängt von der Schnittstelle ab, nicht von einer Klasse. 72 Instanzen in Exponential.
keywords: Entwurfsmuster, Schnittstelle, Struktur, Musterkatalog, design pattern, interface, pattern catalogue
type: catalogue
---
# Schnittstelle (Vertrag)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Ein Vertrag, dem Implementierungen folgen; der Code hängt von der Schnittstelle ab, nicht von einer Klasse.

|  |  |
| --- | --- |
| Gruppe | Struktur |
| Instanzen | 72 |
| Erkannt an | Jede Schnittstelle. |
| Erweiterungspunkt | Implementieren Sie die Schnittstelle und registrieren Sie die Klasse dort, wo die Schnittstelle verwendet wird. |

## Klassen nach Bereich

### extension/cjw_newsletter (2)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterScheduleConditionInterface` | interface | `extension/cjw_newsletter/classes/editorial/cjwnewsletterscheduleconditioninterface.php:19` | Eine Bedingung, die ein wiederkehrender Versand vor jedem Lauf prüft (cjwnl_schedule.condition_handler). | – |
| `CjwNewsletterSmsTransport` | interface | `extension/cjw_newsletter/classes/sms/cjwnewslettersmstransport.php:25` | Ein Weg, SMS zu senden und die SMS zu lesen, die ein Anbieter zurückgibt (der anbieterneutrale Teil des SMS-Kanals). | – |

### extension/explayouts (2)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsBlockHandlerInterface` | interface | `extension/explayouts/classes/explayoutsblockhandlerinterface.php:2` | – | – |
| `expLayoutsQueryHandlerInterface` | interface | `extension/explayouts/classes/explayoutsqueryhandlerinterface.php:2` | – | – |

### extension/expquery_translator (2)

[Alle Muster von extension/expquery_translator](bereich-extension-expquery-translator.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `QueryTranslator\Parsing` | interface | `extension/expquery_translator/lib/Parsing.php:10` | Schnittstelle zum Parsen einer Folge von Tokens in einen Syntaxbaum. | – |
| `QueryTranslator\Tokenizing` | interface | `extension/expquery_translator/lib/Tokenizing.php:8` | Schnittstelle zum Zerlegen einer Zeichenkette in eine Folge von Tokens. | – |

### extension/exp_oe_tiptap (1)

[Alle Muster von extension/exp_oe_tiptap](bereich-extension-exp-oe-tiptap.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOETiptapAIProvider` | interface | `extension/exp_oe_tiptap/classes/ai/expoetiptapaiprovider.php:16` | Ein Textgenerierungs-Backend der KI-Hooks. | – |

### extension/ezfind (2)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezfIndexPlugin` | interface | `extension/ezfind/classes/indexplugins/ezfindexplugin.php:18` | Beschreibung von ezfIndexPlugin. | – |
| `eZFindExtendedAttributeFilterInterface` | interface | `extension/ezfind/classes/ezfindextendedattributefilterinterface.php:10` | Schnittstelle für erweiterte Attributfilter | – |

### extension/ezflow (2)

[Alle Muster von extension/ezflow](bereich-extension-ezflow.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFlowFetchInterface` | interface | `extension/ezflow/classes/ezflowfetchinterface.php:28` | – | – |
| `eZHTTPCacheManagerInterface` | interface | `extension/ezflow/classes/ezhttpcachemanagerinterface.php:32` | Schnittstellendeklaration | – |

### extension/ezie (1)

[Alle Muster von extension/ezie](bereich-extension-ezie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZIEEzcConversions` | interface | `extension/ezie/classes/interfaces/conversions.php:11` | – | – |

### extension/ezjscore (1)

[Alle Muster von extension/ezjscore](bereich-extension-ezjscore.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpAjaxUploaderHandlerInterface` | interface | `extension/ezjscore/classes/ajaxuploader/ezpajaxuploaderhandlerinterface.php:19` | Schnittstelle des AJAX-Uploader-Handlers | – |

### extension/ezmultiupload (1)

[Alle Muster von extension/ezmultiupload](bereich-extension-ezmultiupload.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZMultiuploadHandlerInterface` | interface | `extension/ezmultiupload/classes/ezmultiuploadhandlerinterface.php:12` | – | – |

### extension/ezoe (1)

[Alle Muster von extension/ezoe](bereich-extension-ezoe.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expOEEditorEngine` | interface | `extension/ezoe/classes/expoeeditorengine.php:21` | Eine Editor-Engine des Online-Editors: das, was ein ezxmltext-Attribut im Browser zu einem Editor macht. | extension/ezoe/settings/rad.ini [Registry_ezoeengines] Entries[Engines] |

### extension/ezurlmultisite (1)

[Alle Muster von extension/ezurlmultisite](bereich-extension-ezurlmultisite.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZURLMultisiteDataTypeHandler` | interface | `extension/ezurlmultisite/classes/ezurlmultisitedatatypehandler.php:3` | – | – |

### extension/xrowmetadata (3)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `xrowSitemapImageConverterInterface` | interface | `extension/xrowmetadata/classes/interfaces/xrowsitemapimageconverterinterface.php:3` | – | – |
| `xrowSitemapNewsConverterInterface` | interface | `extension/xrowmetadata/classes/interfaces/xrowsitemapnewsconverterinterface.php:3` | – | – |
| `xrowSitemapVideoConverterInterface` | interface | `extension/xrowmetadata/classes/interfaces/xrowsitemapvideoconverterinterface.php:3` | – | – |

### kernel/classes (1)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expNamePatternResolverInterface` | interface | `kernel/classes/expnamepatternresolverinterface.php:27` | Eine PHP-Klasse, die den Namen eines Inhaltsobjekts oder den Namen, aus dem sein URL-Alias gebildet wird, erzeugt, statt der Attribut-Tokens des Namensmusters der Klasse… | – |

### kernel/classes/audit (5)

[Alle Muster von kernel/classes/audit](bereich-kernel-classes-audit.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expAuditAlertRule` | interface | `kernel/classes/audit/expauditalertrule.php:14` | Eine Alarmregelklasse, registriert in audit.ini: [AuditAlertSettings] RuleClasses[]= Gezählt von der RAD-Erhebung (Registry „auditalertrules“). | – |
| `expAuditFormatHandler` | interface | `kernel/classes/audit/expauditformathandler.php:14` | Ein Archivformat (gzip, bzip2, xz, zstd, zip), registriert in audit.ini: [AuditArchiveSettings] FormatHandlers[]= Gezählt von der RAD-Erhebung (Registry… | – |
| `expAuditSink` | interface | `kernel/classes/audit/expauditsink.php:15` | Eine Kopie der Audit-Datensätze außerhalb der Datei (Syslog, Webhook, Mail), registriert in audit.ini: [AuditSinkSettings] SinkClasses[]= Gezählt von der RAD… | – |
| `expAuditSource` | interface | `kernel/classes/audit/console/expauditsource.php:23` | Eine optionale Quelle der Audit-Konsole: eine Extension, die eigene Ereignisse schreibt (meist in einen eigenen Kanal) und den Ansichten der Konsole mitteilt, wi… | settings/rad.ini [Registry_auditsources] Entries[Sources] |
| `expAuditTaxonomyBranch` | interface | `kernel/classes/audit/expaudittaxonomybranch.php:15` | Der Zweig einer Extension in der Audit-Taxonomie, registriert in audit.ini: [AuditEventSettings] Branches[myext]=myExtAuditBranch Gezählt von der RAD-Erhebung… | – |

### kernel/classes/contentcheck (1)

[Alle Muster von kernel/classes/contentcheck](bereich-kernel-classes-contentcheck.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentCheckFileHandler` | interface | `kernel/classes/contentcheck/expcontentcheckfilehandler.php:15` | Gibt an, für welche Speicherdateien ein dateibasiertes Feld steht (doc/eng-us/features/6.0/content-checks.md, „Missing files“). | – |

### kernel/classes/contentjob (1)

[Alle Muster von kernel/classes/contentjob](bereich-kernel-classes-contentjob.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expContentJobType` | interface | `kernel/classes/contentjob/expcontentjobtype.php:22` | Eine Art von Inhaltsjob, registriert in content.ini [ContentJobSettings] JobTypes[]=. | – |

### kernel/classes/debugbar (1)

[Alle Muster von kernel/classes/debugbar](bereich-kernel-classes-debugbar.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expDebugBarPanelInterface` | interface | `kernel/classes/debugbar/expdebugbarpanelinterface.php:26` | Ein Panel der Exp-Debug-Leiste aus einer Extension: ein eigener Reiter nach Memory, optional ein Chip in der Zusammenfassung und Daten im JSON der Seite. | – |

### kernel/classes/help (2)

[Alle Muster von kernel/classes/help](bereich-kernel-classes-help.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expHelpContextPageInterface` | interface | `kernel/classes/help/exphelpcontextpageinterface.php:16` | Ein Kontexthilfe-Anbieter (expHelpContextProviderInterface), der auch Seiten ausliefert, die keine Dateien unterhalb von doc/ sind: als Inhalt gepflegte Hilfethemen, w… | – |
| `expHelpContextProviderInterface` | interface | `kernel/classes/help/exphelpcontextproviderinterface.php:15` | Eine Klasse, die „Hilfe zu dieser Seite“ im Code beantwortet, für das, was ein Map[]-Eintrag pro Ansicht nicht ausdrücken kann (eine Ansicht, deren Hilfe von ihren Parametern abhängt, eine Exte… | – |

### kernel/classes/ini (2)

[Alle Muster von kernel/classes/ini](bereich-kernel-classes-ini.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expIniAction` | interface | `kernel/classes/ini/actions/expiniaction.php:15` | Eine Aktion des Befehls exp:ini (get, set, add, rem, ...). | – |
| `expIniScopeProvider` | interface | `kernel/classes/ini/expiniscopeprovider.php:20` | Eine Quelle von INI-Bereichen (Orte, an die eine INI-Datei geschrieben werden kann). | – |

### kernel/classes/logviewer (1)

[Alle Muster von kernel/classes/logviewer](bereich-kernel-classes-logviewer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLogManagerInterface` | interface | `kernel/classes/logviewer/explogmanagerinterface.php:19` | Log-Verwaltung (doc/eng-us/features/6.0/log-rotation.md, „For developers“): was die Befehlszeile, der Cronjob-Teil „logs“ und die Aktionen des Log-Viewers verwenden. | – |

### kernel/classes/mailpreferences (1)

[Alle Muster von kernel/classes/mailpreferences](bereich-kernel-classes-mailpreferences.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMailCategoryHandler` | interface | `kernel/classes/mailpreferences/expmailcategoryhandler.php:26` | Die optionale PHP-Klasse einer Kategorie ([Category_] HandlerClass): sie liest Daten, die vor dem Präferenzsystem existierten (Teilbaum-Benachrichtigungen, Dige… | – |

### kernel/classes/requestclassifier (2)

[Alle Muster von kernel/classes/requestclassifier](bereich-kernel-classes-requestclassifier.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expRequestRuleSetInterface` | interface | `kernel/classes/requestclassifier/exprequestrulesetinterface.php:24` | Ein Regelsatz des Anfrageklassifizierers (settings/requestclassifier.ini, RuleSets[] und [RuleSet-] Class=). | – |
| `expRequestVerdictListenerInterface` | interface | `kernel/classes/requestclassifier/exprequestverdictlistenerinterface.php:18` | Wird über jedes Urteil des Anfrageklassifizierers informiert, nachdem dessen security.log-Zeile geschrieben wurde (settings/requestclassifier.ini [ClassifierSettings] Listen… | – |

### kernel/classes/uiswitch (1)

[Alle Muster von kernel/classes/uiswitch](bereich-kernel-classes-uiswitch.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expUISwitchProviderInterface` | interface | `kernel/classes/uiswitch/expuiswitchproviderinterface.php:19` | Eine umschaltbare Einstellung der Administrationsoberfläche (das "UI-Chrome"): die Anzeigesprache, das Design oder was eine Extension hinzufügt. | – |

### kernel/private (24)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZClusterFileHandlerInterface` | interface | `kernel/private/interfaces/ezclusterfilehandlerinterface.php:15` | Schnittstelle für Cluster-Datei-Handler | – |
| `ezpAsynchronousPublisherOutput` | interface | `kernel/private/interfaces/asynchronouspublisheroutput.php:16` | Diese Schnittstelle dient als Basis für den Daemon ezpasynchronouspublisher.php | – |
| `ezpAsynchronousPublishingFilterInterface` | interface | `kernel/private/interfaces/asynchronouspublishingfilter.php:17` | Basisschnittstelle für Filterklassen von eZ Asynchronous Publishing | – |
| `ezpAttributeOperatorFormatterInterface` | interface | `kernel/private/eztemplate/ezpattributeoperatorformatterinterface.php:12` | – | – |
| `ezpAutoloadOutput` | interface | `kernel/private/interfaces/ezpautoloadoutput.php:17` | Schnittstelle für Klassen, die Ausgaben für die Autoload-Erzeugung bereitstellen. | – |
| `ezpContentCriteriaInterface` | interface | `kernel/private/api/interfaces/content_criteria.php:16` | Diese Schnittstelle ist die Basis für die Implementierung von Kriterien für Inhaltsabfragen | – |
| `ezpContentLimitationHandler` | interface | `kernel/private/interfaces/ezpcontentlimitationhandler.php:29` | Wertet eine Richtlinieneinschränkung des Moduls content aus, die der Kernel selbst nicht kennt. | – |
| `ezpContentLimitationSolrHandler` | interface extends ezpContentLimitationHandler | `kernel/private/interfaces/ezpcontentlimitationsolrhandler.php:23` | Ein Handler für Inhaltseinschränkungen, der auch eine Suche in einer Suchmaschine einschränken kann, die selbst nach den Richtlinien des Benutzers filtert, etwa eZ Find mit… | – |
| `ezpContentPublishingQueueReaderInterface` | interface | `kernel/private/interfaces/ezpcontentpublishingqueuereader.php:18` | Diese Schnittstelle beschreibt einen Leser der asynchronen Veröffentlichungswarteschlange. Queue-Leser sollen dem asynchronen Veröffentlichungssystem das nächste… | – |
| `ezpDatabaseBasedClusterFileHandler` | interface | `kernel/private/interfaces/ezpdatabasebasedclusterfilehandler.php:20` | Diese Schnittstelle beschreibt einen datenbankbasierten Cluster-Datei-Handler | – |
| `ezpKernelHandler` | interface | `kernel/private/interfaces/ezpkernelhandler.php:14` | Schnittstelle für Kernel-Handler | – |
| `ezpLanguageSwitcherCapable` | interface | `kernel/private/interfaces/ezplanguageswitchercapable.php:17` | Schnittstelle für Klassen, die Funktionen zur Sprachumschaltung bereitstellen. | – |
| `ezpMobileDeviceDetectFilterInterface` | interface | `kernel/private/classes/ezpmobiledevicedetectfilterinterface.php:12` | – | – |
| `ezpMultivariateTestHandlerInterface` | interface | `kernel/private/classes/ezpmultivariatetesthandlerinterface.php:12` | – | – |
| `ezpRestAuthenticationStyleInterface` | interface | `kernel/private/rest/classes/auth/auth_style.php:12` | – | – |
| `ezpRestPreRoutingFilterInterface` | interface | `kernel/private/rest/classes/interfaces/prerouting_filter.php:12` | – | – |
| `ezpRestProviderInterface` | interface | `kernel/private/rest/classes/interfaces/rest_provider.php:12` | – | – |
| `ezpRestRequestFilterInterface` | interface | `kernel/private/rest/classes/interfaces/request_filter.php:12` | – | – |
| `ezpRestResponseFilterInterface` | interface | `kernel/private/rest/classes/interfaces/response_filter.php:12` | – | – |
| `ezpRestResultFilterInterface` | interface | `kernel/private/rest/classes/interfaces/result_filter.php:12` | – | – |
| `ezpRestViewControllerInterface` | interface | `kernel/private/rest/classes/interfaces/view_controller.php:12` | – | – |
| `ezpSearchEngine` | interface | `kernel/private/interfaces/ezpsearchengine.php:18` | Diese Schnittstelle dient als Grundlage für die verschiedenen Suchmaschinen-Implementierungen | – |
| `ezpStaticCache` | interface | `kernel/private/interfaces/ezpstaticcache.php:16` | Diese Schnittstelle dient als Basis für die verschiedenen Implementierungen der StaticCache-Engines | – |
| `ezpWebBasedKernelHandler` | interface extends ezpKernelHandler | `kernel/private/interfaces/ezpwebbasedkernelhandler.php:14` | Schnittstelle für webbasierte Kernel-Handler | – |

### kernel/private/classes/clusterfilehandlers (5)

[Alle Muster von kernel/private/classes/clusterfilehandlers](bereich-kernel-private-classes-clusterfilehandlers.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZClusterEventListener` | interface | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventlistener.php:14` | Schnittstelle, die jeder Cluster-Ereignis-Listener implementieren muss | – |
| `eZClusterEventLogger` | interface | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventlogger.php:20` | Schnittstelle für eZClusterEvent-Logger. | – |
| `eZClusterEventNotifier` | interface | `kernel/private/classes/clusterfilehandlers/interfaces/ezclustereventnotifier.php:15` | Schnittstelle, die Cluster-Backends implementieren müssen, die Ereignisbenachrichtigungen unterstützen. | – |
| `eZDFSFileHandlerDFSBackendFactoryInterface` | interface | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendfactoryinterface.php:14` | Macht einen DFSBackend-Handler über die statische Methode build instanziierbar. | – |
| `eZDFSFileHandlerDFSBackendInterface` | interface | `kernel/private/classes/clusterfilehandlers/dfsbackends/ezdfsfilehandlerdfsbackendinterface.php:17` | Schnittstelle für einen DFS-FS-Handler, der CRUD-Unterstützung für Binärdateien bietet. | – |

### kernel/private/classes/requestrules (5)

[Alle Muster von kernel/private/classes/requestrules](bereich-kernel-private-classes-requestrules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpRequestFactProvider` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:90` | Fügt dem Kontext jeder Anfrage Fakten hinzu, bevor die Regeln laufen. | – |
| `ezpRequestRuleAction` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:57` | Eine Aktion: was geschieht, wenn alle Bedingungen einer Regel zutreffen. | – |
| `ezpRequestRuleCondition` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:23` | Eine Bedingung: ein Test auf die Fakten einer Anfrage. | – |
| `ezpRequestRuleProvider` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:76` | Fügt Regeln aus PHP hinzu: eine Extension, deren Regeln berechnet werden (aus einer Datenbanktabelle, aus einer anderen INI-Datei), statt in requestrules.ini geschrieben zu sein. | – |
| `ezpRequestRuleStableCondition` | interface | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:46` | Kennzeichnet eine Bedingung, deren Antwort für jede Anfrage gleich ist, die die Seiten-Caches mit einer gespeicherten Seite beantworten würden: sie liest nur, was ihre Schlüssel enth… | – |

### lib/ezi18n (1)

[Alle Muster von lib/ezi18n](bereich-lib-ezi18n.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expI18nFallbackRuleInterface` | interface | `lib/ezi18n/classes/expi18nfallbackruleinterface.php:27` | Eine Übersetzungs-Fallback-Regel: gibt an, auf welche Übersetzung eine Locale zurückfällt, wenn eine Übersetzungswurzel (share/translations des Kernels oder die einer Extension… | – |
