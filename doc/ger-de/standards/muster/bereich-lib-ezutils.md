---
title: Muster in lib/ezutils
audience: developer
category: standards
order: 540
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezutils mit den Entwurfsmustern.
keywords: lib/ezutils, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezutils

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezutils` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (47)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expDebugBarReport` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/expdebugbarreport.php:30` | Der HTML-Debug-Bericht als "Exp Debug"-Leiste: eine am unteren Fensterrand fixierte Leiste mit einer Live-Zusammenfassung der Anfrage und darüber ein Panel mit Reitern… |
| `expInfo` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/expinfo.php:21` | – |
| `eZCacheHelper` | class | – | `lib/ezutils/classes/ezcachehelper.php:12` | – |
| `eZCLI` | class | [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezcli.php:31` | – |
| `eZDateTimeValidator` | class extends eZInputValidator | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/ezdatetimevalidator.php:18` | – |
| `eZDebug` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezdebug.php:59` | – |
| `eZDebugSetting` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/ezdebugsetting.php:39` | Stellt bedingte Debug-Ausgabe bereit |
| `eZExecution` | class | [Registry](registry.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/ezexecution.php:34` | – |
| `eZExpiryHandler` | class | [Registry](registry.md), [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `lib/ezutils/classes/ezexpiryhandler.php:16` | Verwaltet Ablaufschlüssel und ihre Zeitstempel |
| `eZExtension` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/ezextension.php:18` | – |
| `eZFatalUserError` | class extends Error | [Ausnahmehierarchie](ausnahme.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `lib/ezutils/classes/ezfatalusererror.php:35` | Ein schwerer Fehler, den der Kernel selbst auslöst, anstelle eines Benutzerfehlers der Stufe E_USER_ERROR, den PHP 8.4 für trigger_error() als veraltet markiert („throw a… |
| `eZFileExtensionBlackListValidator` | class extends eZInputValidator | – | `lib/ezutils/classes/ezfileextensionblacklistvalidator.php:17` | – |
| `eZFileTransport` | class extends eZMailTransport | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `lib/ezutils/classes/ezfiletransport.php:18` | – |
| `eZFloatValidator` | class extends eZRegExpValidator | – | `lib/ezutils/classes/ezfloatvalidator.php:18` | – |
| `eZFunctionHandler` | class | [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/ezfunctionhandler.php:18` | – |
| `eZHTTPFile` | class | – | `lib/ezutils/classes/ezhttpfile.php:23` | – |
| `eZHTTPPersistence` | class | – | `lib/ezutils/classes/ezhttppersistence.php:21` | Objektpersistenz über HTTP-POST-Variablen |
| `eZHTTPTool` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezhttptool.php:25` | – |
| `eZINI` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezini.php:53` | ### EXP-MULTI-SITE-OVERRIDE-SETTINGS ### - lib/ezutils/classes/ezini.php - neue INI-Einstellung ext-siteaccess-override - - neue INI-Einstellung ext-siteaccess… |
| `eZInputValidator` | class | – | `lib/ezutils/classes/ezinputvalidator.php:78` | – |
| `eZIntegerValidator` | class extends eZRegExpValidator | – | `lib/ezutils/classes/ezintegervalidator.php:18` | – |
| `eZMail` | class | – | `lib/ezutils/classes/ezmail.php:60` | – |
| `eZMailTransport` | class | [Strategie / Handler](strategie-handler.md) | `lib/ezutils/classes/ezmailtransport.php:18` | – |
| `eZMath` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/ezmath.php:21` | – |
| `eZMimeType` | class | [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezmimetype.php:34` | – |
| `eZModule` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Front Controller](front-controller.md) | `lib/ezutils/classes/ezmodule.php:36` | Die Klasse eZModule dient zum Instanziieren und Verwenden von Modulen und Ansichten. |
| `eZModuleFunctionInfo` | class | – | `lib/ezutils/classes/ezmodulefunctioninfo.php:18` | – |
| `eZModuleOperationInfo` | class | – | `lib/ezutils/classes/ezmoduleoperationinfo.php:18` | – |
| `eZMutex` | class | – | `lib/ezutils/classes/ezmutex.php:18` | – |
| `eZOperationHandler` | class | [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/ezoperationhandler.php:18` | – |
| `eZOperationMemento` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `lib/ezutils/classes/ezoperationmemento.php:18` | – |
| `eZPHPCreator` | class | – | `lib/ezutils/classes/ezphpcreator.php:53` | – |
| `ezpRepairQueue` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/ezprepairqueue.php:37` | Repariert eine Installation, deren Composer-Bibliotheken fehlen, von der Fehlerseite aus, die das meldet (eZExecution::renderErrorCasePage('dependencies')), d… |
| `eZProcess` | class | [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezprocess.php:18` | – |
| `eZRegExpValidator` | class extends eZInputValidator | – | `lib/ezutils/classes/ezregexpvalidator.php:18` | – |
| `eZRemoteIdUtility` | class | – | `lib/ezutils/classes/ezremoteidutility.php:16` | Diese Klasse bietet Werkzeuge rund um Remote-IDs |
| `eZSendmailTransport` | class extends eZMailTransport | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `lib/ezutils/classes/ezsendmailtransport.php:20` | – |
| `eZSMTPTransport` | class extends eZMailTransport | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `lib/ezutils/classes/ezsmtptransport.php:18` | – |
| `eZStringUtils` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/ezstringutils.php:18` | – |
| `eZSys` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezsys.php:33` | Einfacher Zugriff auf verschiedene Systemeinstellungen |
| `eZSysInfo` | class | – | `lib/ezutils/classes/ezsysinfo.php:32` | – |
| `eZTextTool` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/eztexttool.php:19` | – |
| `eZTimestamp` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/eztimestamp.php:12` | – |
| `eZTrustedProxy` | class | [Adapter / Bridge / Wrapper](adapter.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezutils/classes/eztrustedproxy.php:30` | Entscheidet, welchen weitergeleiteten Anfrage-Headern man glauben kann. |
| `eZURI` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Front Controller](front-controller.md), [Singleton / instance()](singleton.md) | `lib/ezutils/classes/ezuri.php:32` | Bietet Zugriff auf die HTTP-URI |
| `eZWizardBase` | class | – | `lib/ezutils/classes/ezwizardbase.php:18` | – |
| `eZWizardBaseClassLoader` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `lib/ezutils/classes/ezwizardbaseclassloader.php:18` | – |

## Ereignis-Hooks (2)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `response/output` | filter | `lib/ezutils/classes/ezdebug.php:1562` |
| `module/functionlist` | filter | `lib/ezutils/classes/ezmodule.php:314` |
