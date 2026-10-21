---
title: Singleton / instance()
audience: developer
category: standards
order: 320
summary: Eine Klasse, die pro Schlüssel eine gemeinsame Instanz ausgibt, meist über eine statische Methode instance() (oft in $GLOBALS zwischengespeichert). 69 Instanzen in Exponential.
keywords: Entwurfsmuster, Singleton, Erzeugung, Musterkatalog, design pattern, singleton, pattern catalogue
type: catalogue
---
# Singleton / instance()

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Eine Klasse, die pro Schlüssel eine gemeinsame Instanz ausgibt, meist über eine statische Methode instance() (oft in $GLOBALS zwischengespeichert).

|  |  |
| --- | --- |
| Gruppe | Anlegen |
| Instanzen | 69 |
| Erkannt an | Hat eine statische Methode instance() oder getInstance(). |
| Erweiterungspunkt | Rufen Sie Class::instance() auf; viele davon nehmen die Einstellungsdatei oder die Datenbank als Schlüssel. |

## Klassen nach Bereich

### extension/bccie (1)

[Alle Muster von extension/bccie](bereich-extension-bccie.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `bccieExportFormatOutputHandler` | class | `extension/bccie/classes/bccieExportFormatOutputHandler.php:11` | – | – |

### extension/cjw_newsletter (1)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterLog` | class extends ezcLog | `extension/cjw_newsletter/classes/cjwnewsletterlog.php:28` | Log-Verarbeitung des Cjw Newsletter mit ezcLog, logmessage time [severity] [source] [category] ( element1: value1, .... | – |

### extension/explayouts (1)

[Alle Muster von extension/explayouts](bereich-extension-explayouts.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsSite` | class | `extension/explayouts/classes/explayoutssite.php:9` | Site-Registry für Exponential 4, kompatibel mit der Site-Schnittstelle der Exp Site API. | – |

### extension/ezfind (2)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFindExtendedAttributeFilterFactory` | class | `extension/ezfind/classes/ezfindextendedattributefilterfactory.php:10` | Fabrik für erweiterte Attributfilter | – |
| `ezfSolrDocumentFieldBase` | class | `extension/ezfind/classes/ezfsolrdocumentfieldbase.php:21` | Klasse ezfSolrDocumentFieldBase. | – |

### extension/ezflow (1)

[Alle Muster von extension/ezflow](bereich-extension-ezflow.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFlowAjaxContent` | class | `extension/ezflow/classes/ezflowajaxcontent.php:35` | – | – |

### extension/ezjscore (1)

[Alle Muster von extension/ezjscore](bereich-extension-ezjscore.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezjscServerRouter` | class | `extension/ezjscore/classes/ezjscserverrouter.php:39` | – | – |

### extension/ezupdate (1)

[Alle Muster von extension/ezupdate](bereich-extension-ezupdate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZUpdateManager` | class | `extension/ezupdate/classes/ezupdatemanager.php:19` | Führt Composer für die Installation aus, zu der diese Extension gehört. | – |

### extension/git_manager (1)

[Alle Muster von extension/git_manager](bereich-extension-git-manager.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `GitManager` | class | `extension/git_manager/classes/git_manager.php:9` | – | – |

### extension/sevenx_authentication_2fa (1)

[Alle Muster von extension/sevenx_authentication_2fa](bereich-extension-sevenx-authentication-2fa.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenxAuthentication2faHelper` | class | `extension/sevenx_authentication_2fa/classes/sevenxauthentication2fahelper.php:23` | – | – |

### extension/syndication (1)

[Alle Muster von extension/syndication](bereich-extension-syndication.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZImportEditWizard` | class extends eZWizardBase | `extension/syndication/modules/syndication/import_edit/ezimporteditwizard.php:47` | – | – |

### kernel/classes (12)

[Alle Muster von kernel/classes](bereich-kernel-classes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expPasswordPolicy` | class | `kernel/classes/exppasswordpolicy.php:24` | Die Regeln, die ein neues Passwort erfüllen muss, und was geschieht, nachdem ein Passwort geändert wurde. | – |
| `expScriptStatus` | class | `kernel/classes/expscriptstatus.php:18` | expScriptStatus - schlanker Fortschritts-/Statusmelder für die CLI. | – |
| `expWeakPasswordList` | class | `kernel/classes/expweakpasswordlist.php:35` | Die eine Liste bekannter und häufiger Passwörter und die Prüfung, die jede Stelle verwendet, die ein neues Passwort setzt: der Setup-Assistent, der Kickstarter und exp:i… | – |
| `eZBinaryFileHandler` | class | `kernel/classes/ezbinaryfilehandler.php:23` | – | – |
| `eZClusterFileHandler` | class | `kernel/classes/ezclusterfilehandler.php:11` | – | – |
| `eZCollaborationProfile` | class extends eZPersistentObject | `kernel/classes/ezcollaborationprofile.php:18` | – | – |
| `eZCollaborationViewHandler` | class | `kernel/classes/ezcollaborationviewhandler.php:18` | – | – |
| `eZConfirmOrderHandler` | class | `kernel/classes/ezconfirmorderhandler.php:18` | – | – |
| `eZPackageCreationHandler` | class | `kernel/classes/ezpackagecreationhandler.php:21` | – | – |
| `eZPackageInstallationHandler` | class | `kernel/classes/ezpackageinstallationhandler.php:19` | – | – |
| `eZScript` | class | `kernel/classes/ezscript.php:49` | – | – |
| `eZShopAccountHandler` | class | `kernel/classes/ezshopaccounthandler.php:12` | – | – |

### kernel/classes/datatypes (3)

[Alle Muster von kernel/classes/datatypes](bereich-kernel-classes-datatypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZUser` | class extends eZPersistentObject | `kernel/classes/datatypes/ezuser/ezuser.php:21` | – | extension/AdminAid/settings/aid.ini [SearchDatabase] ClassList[] |
| `eZUserLoginHandler` | class | `kernel/classes/datatypes/ezuser/ezuserloginhandler.php:19` | – | – |
| `eZXMLSchema` | class | `kernel/classes/datatypes/ezxmltext/ezxmlschema.php:12` | – | – |

### kernel/classes/help (4)

[Alle Muster von kernel/classes/help](bereich-kernel-classes-help.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expHelpContentProvider` | class | `kernel/classes/help/content/exphelpcontentprovider.php:19` | Als Inhalt gepflegte Hilfe, ausgeliefert in das Hilfesystem (exphelp): die Themen, die der aktuelle Benutzer sehen darf, gerendert mit dem Markdown-Renderer der Hilfe… | settings/exphelp.ini [ContextHelp] Providers[] |
| `expHelpContentRepository` | class | `kernel/classes/help/content/exphelpcontentrepository.php:15` | Liest als Inhalt gepflegte Hilfe aus der Datenbank in einfache Arrays (die Formen, die expHelpContentValidator beschreibt), ohne jeden Berechtigungsfilter:… | – |
| `expHelpContextRegistry` | class | `kernel/classes/help/exphelpcontextregistry.php:44` | Kontexthilfe: welche Hilfeseite „Hilfe zu dieser Seite“ beantwortet, für ein Modul, eine Ansicht und ein Design. | – |
| `expHelpDocumentIndex` | class | `kernel/classes/help/exphelpdocumentindex.php:34` | Der Index der Hilfeseiten (doc/eng-us/features/6.0/help-system.md, "The index"): ein Eintrag je Dokument der Allowlist (expHelpAllowlist: von Git verfolgte Da… | – |

### kernel/classes/logviewer (2)

[Alle Muster von kernel/classes/logviewer](bereich-kernel-classes-logviewer.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLogManager` | class | `kernel/classes/logviewer/explogmanager.php:27` | Log-Verwaltung (doc/eng-us/features/6.0/log-rotation.md): die Logs der Installation mit ihren Archiven, Größen, Wachstum und Grenzen; Rotation, Bereinigung, Kom… | – |
| `expLogViewerConfig` | class | `kernel/classes/logviewer/explogviewerconfig.php:11` | Die Einstellungen des Log-Viewers (settings/logviewer.ini, doc/eng-us/features/6.0/log-viewer.md, „Settings“), einmal pro Anfrage mit sicheren Grenzen gelesen. | – |

### kernel/classes/mailpreferences (1)

[Alle Muster von kernel/classes/mailpreferences](bereich-kernel-classes-mailpreferences.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expMailCategoryRegistry` | class | `kernel/classes/mailpreferences/expmailcategoryregistry.php:26` | Alle E-Mail-Kategorien, in der Reihenfolge, in der die Präferenzseite sie zeigt: | – |

### kernel/classes/notification (1)

[Alle Muster von kernel/classes/notification](bereich-kernel-classes-notification.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZNotificationTransport` | class | `kernel/classes/notification/eznotificationtransport.php:17` | – | – |

### kernel/classes/requestclassifier (1)

[Alle Muster von kernel/classes/requestclassifier](bereich-kernel-classes-requestclassifier.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expRequestClassifier` | class | `kernel/classes/requestclassifier/exprequestclassifier.php:29` | Der Anfrageklassifizierer: unterscheidet eine Sondierung, eine Traversierung, eine Injektion, einen Scanner oder eine Häufung von Not-found-Antworten von einer gewöhnlichen Anfrage und schreibt eine… | – |

### kernel/classes/subitems (1)

[Alle Muster von kernel/classes/subitems](bereich-kernel-classes-subitems.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expSubitemsColumnRegistry` | class | `kernel/classes/subitems/expsubitemscolumnregistry.php:23` | Die Registry der Spalten der Unterelementliste der Administrationsoberfläche („Tabellenoptionen“). | – |

### kernel/common (1)

[Alle Muster von kernel/common](bereich-kernel-common.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTemplateDesignResource` | class extends eZTemplateFileResource | `kernel/common/eztemplatedesignresource.php:18` | – | – |

### kernel/modules (1)

[Alle Muster von kernel/modules](bereich-kernel-modules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCurrencyConverter` | class | `kernel/shop/classes/ezcurrencyconverter.php:20` | – | – |

### kernel/private (5)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpContentPublishingQueueProcessor` | class | `kernel/private/classes/ezpcontentpublishingqueueprocessor.php:21` | Diese Klasse verwaltet die Veröffentlichungswarteschlange über persistente Objekte ezpContentPublishingProcess | – |
| `ezpEvent` | class | `kernel/private/classes/ezpevent.php:20` | Diese Klasse verarbeitet interne Kernel-Ereignisse in Exponential, auch Hooks genannt. | – |
| `ezpExtension` | class | `kernel/private/classes/ezpextension.php:15` | Objekt, das eine Exponential-Extension darstellt | – |
| `ezpKernel` | class | `kernel/private/classes/ezpkernel.php:16` | Basisklasse des Exponential-Kernels. | – |
| `ezpRestDebug` | class | `kernel/private/rest/classes/debug/debug.php:11` | – | – |

### kernel/private/classes/requestrules (1)

[Alle Muster von kernel/private/classes/requestrules](bereich-kernel-private-classes-requestrules.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpRequestRuleEngine` | class | `kernel/private/classes/requestrules/ezprequestruleengine.php:32` | Die Anfrageregeln: eine geordnete Liste von Regeln, jede eine Menge von Bedingungen und eine Aktion, befragt zu jeder Modulansicht, die der Kernel gleich ausführt. | – |

### lib/ezdb (1)

[Alle Muster von lib/ezdb](bereich-lib-ezdb.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDB` | class | `lib/ezdb/classes/ezdb.php:80` | – | – |

### lib/ezdbschema (1)

[Alle Muster von lib/ezdbschema](bereich-lib-ezdbschema.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZDbSchema` | class | `lib/ezdbschema/classes/ezdbschema.php:19` | – | – |

### lib/ezfile (1)

[Alle Muster von lib/ezfile](bereich-lib-ezfile.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZFileHandler` | class | `lib/ezfile/classes/ezfilehandler.php:56` | – | – |

### lib/ezi18n (8)

[Alle Muster von lib/ezi18n](bereich-lib-ezi18n.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expI18nFallbackResolver` | class | `lib/ezi18n/classes/expi18nfallbackresolver.php:32` | Auf welche Übersetzungen eine Locale in welcher Reihenfolge zurückfällt: die eine verbindliche Quelle für den Übersetzer (eZTSTranslator), die Wahl der Oberflächensprache (expU… | – |
| `eZCharTransform` | class | `lib/ezi18n/classes/ezchartransform.php:21` | – | – |
| `eZCodePage` | class | `lib/ezi18n/classes/ezcodepage.php:19` | – | – |
| `eZCodePageMapper` | class | `lib/ezi18n/classes/ezcodepagemapper.php:18` | – | – |
| `eZMBStringMapper` | class | `lib/ezi18n/classes/ezmbstringmapper.php:25` | – | – |
| `eZTextCodec` | class | `lib/ezi18n/classes/eztextcodec.php:23` | – | – |
| `eZTranslatorManager` | class | `lib/ezi18n/classes/eztranslatormanager.php:31` | – | – |
| `eZUTF8Codec` | class | `lib/ezi18n/classes/ezutf8codec.php:22` | – | – |

### lib/ezimage (1)

[Alle Muster von lib/ezimage](bereich-lib-ezimage.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZImageManager` | class | `lib/ezimage/classes/ezimagemanager.php:85` | – | – |

### lib/ezlocale (1)

[Alle Muster von lib/ezlocale](bereich-lib-ezlocale.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZLocale` | class | `lib/ezlocale/classes/ezlocale.php:111` | – | – |

### lib/eztemplate (3)

[Alle Muster von lib/eztemplate](bereich-lib-eztemplate.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTemplate` | class | `lib/eztemplate/classes/eztemplate.php:200` | – | – |
| `eZTemplateElementParser` | class | `lib/eztemplate/classes/eztemplateelementparser.php:18` | – | – |
| `eZTemplateMultiPassParser` | class extends eZTemplateParser | `lib/eztemplate/classes/eztemplatemultipassparser.php:18` | – | – |

### lib/ezutils (9)

[Alle Muster von lib/ezutils](bereich-lib-ezutils.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZCLI` | class | `lib/ezutils/classes/ezcli.php:31` | – | – |
| `eZDebug` | class | `lib/ezutils/classes/ezdebug.php:59` | – | – |
| `eZExpiryHandler` | class | `lib/ezutils/classes/ezexpiryhandler.php:16` | Verwaltet Ablaufschlüssel und ihre Zeitstempel | – |
| `eZHTTPTool` | class | `lib/ezutils/classes/ezhttptool.php:25` | – | – |
| `eZINI` | class | `lib/ezutils/classes/ezini.php:53` | ### EXP-MULTI-SITE-OVERRIDE-SETTINGS ### - lib/ezutils/classes/ezini.php - neue INI-Einstellung ext-siteaccess-override - - neue INI-Einstellung ext-siteaccess… | – |
| `eZMimeType` | class | `lib/ezutils/classes/ezmimetype.php:34` | – | – |
| `eZProcess` | class | `lib/ezutils/classes/ezprocess.php:18` | – | – |
| `eZSys` | class | `lib/ezutils/classes/ezsys.php:33` | Einfacher Zugriff auf verschiedene Systemeinstellungen | – |
| `eZURI` | class | `lib/ezutils/classes/ezuri.php:32` | Bietet Zugriff auf die HTTP-URI | – |
