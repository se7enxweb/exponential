---
title: Muster in extension/ezurlmultisite
audience: developer
category: standards
order: 458
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezurlmultisite mit den Entwurfsmustern.
keywords: extension/ezurlmultisite, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezurlmultisite

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezurlmultisite` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (8)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZURLMultisite` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/ezurlmultisite/classes/ezurlmultisite.php:27` | Siteaccess-übergreifende Linkauflösung für den Template-Operator ezurl. |
| `eZURLMultisiteBinaryFileHandler` | class implements eZURLMultisiteDataTypeHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/ezurlmultisite/classes/ezurlmultisitebinaryfilehandler.php:3` | – |
| `eZURLMultisiteDataTypeHandler` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `extension/ezurlmultisite/classes/ezurlmultisitedatatypehandler.php:3` | – |
| `ezurlmultisiteInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezurlmultisite/ezinfo.php:10` | – |
| `eZURLMultisiteObjectRelationHandler` | class implements eZURLMultisiteDataTypeHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/ezurlmultisite/classes/ezurlmultisiteobjectrelationhandler.php:3` | – |
| `eZURLMultisiteOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezurlmultisite/classes/ezurlmultisiteoperator.php:3` | – |
| `eZURLMultisiteURLHandler` | class implements eZURLMultisiteDataTypeHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/ezurlmultisite/classes/ezurlmultisiteurlhandler.php:3` | – |
| `eZURLMultisiteURLOperator` | class extends eZURLOperator | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezurlmultisite/classes/ezurlmultisiteurloperator.php:24` | Der eZURLOperator des Kernels mit einem multisite-fähigen ezurl. |

## Template-Operatoren (2)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable | `eZURLMultisiteURLOperator` | `extension/ezurlmultisite/autoloads/eztemplateautoload.php:14` |
| ezurl_path, ezurl_siteaccess, ezurl_roots | `eZURLMultisiteOperator` | `extension/ezurlmultisite/autoloads/eztemplateautoload.php:21` |

## INI-Plugin-Punkte (4)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[DataTypeSettings] HandlerList[ezurl]` | `eZURLMultisiteURLHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:79` |
| `[DataTypeSettings] HandlerList[ezstring]` | `eZURLMultisiteURLHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:80` |
| `[DataTypeSettings] HandlerList[ezbinaryfile]` | `eZURLMultisiteBinaryFileHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:81` |
| `[DataTypeSettings] HandlerList[ezobjectrelation]` | `eZURLMultisiteObjectRelationHandler` | `extension/ezurlmultisite/settings/ezurlmultisite.ini:82` |
