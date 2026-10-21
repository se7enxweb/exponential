---
title: Muster in lib/ezi18n
audience: developer
category: standards
order: 532
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezi18n mit den Entwurfsmustern.
keywords: lib/ezi18n, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezi18n

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezi18n` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (22)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expI18nFallbackConfiguredRule` | class implements expI18nFallbackRuleInterface | – | `lib/ezi18n/classes/expi18nfallbackconfiguredrule.php:19` | Die konfigurierten Fallbacks, i18n.ini [TranslationSettings] FallbackLanguages[]: immer die erste Regel, die der Resolver fragt, sodass das Konfigurierte Vorrang hat vor… |
| `expI18nFallbackResolver` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `lib/ezi18n/classes/expi18nfallbackresolver.php:32` | Auf welche Übersetzungen eine Locale in welcher Reihenfolge zurückfällt: die eine verbindliche Quelle für den Übersetzer (eZTSTranslator), die Wahl der Oberflächensprache (expU… |
| `expI18nFallbackRuleInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `lib/ezi18n/classes/expi18nfallbackruleinterface.php:27` | Eine Übersetzungs-Fallback-Regel: gibt an, auf welche Übersetzung eine Locale zurückfällt, wenn eine Übersetzungswurzel (share/translations des Kernels oder die einer Extension… |
| `expI18nFallbackSameLanguageRule` | class implements expI18nFallbackRuleInterface | – | `lib/ezi18n/classes/expi18nfallbacksamelanguagerule.php:26` | Der automatische Fallback (i18n.ini [TranslationSettings] AutomaticLanguageFallback): eine Locale ohne eigene Übersetzung in der Übersetzung des Kernels… |
| `eZ1337Translator` | class extends eZTranslatorHandler | – | `lib/ezi18n/classes/ez1337translator.php:30` | – |
| `eZBorkTranslator` | class extends eZTranslatorHandler | – | `lib/ezi18n/classes/ezborktranslator.php:46` | – |
| `eZCharsetInfo` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md), [Wertobjekt / Struktur](wertobjekt.md) | `lib/ezi18n/classes/ezcharsetinfo.php:25` | – |
| `eZCharTransform` | class | [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/ezchartransform.php:21` | – |
| `eZCodeMapper` | class | – | `lib/ezi18n/classes/ezcodemapper.php:19` | – |
| `eZCodePage` | class | [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/ezcodepage.php:19` | – |
| `eZCodePageCodec` | class extends eZTextCodec | – | `lib/ezi18n/classes/ezcodepagecodec.php:19` | – |
| `eZCodePageMapper` | class | [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/ezcodepagemapper.php:18` | – |
| `eZMBStringMapper` | class | [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/ezmbstringmapper.php:25` | – |
| `eZRandomTranslator` | class extends eZTranslatorGroup | – | `lib/ezi18n/classes/ezrandomtranslator.php:19` | – |
| `eZShuffleTranslator` | class extends eZTranslatorHandler | – | `lib/ezi18n/classes/ezshuffletranslator.php:19` | – |
| `eZTextCodec` | class | [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/eztextcodec.php:23` | – |
| `eZTranslationCache` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezi18n/classes/eztranslationcache.php:18` | – |
| `eZTranslatorGroup` | class extends eZTranslatorHandler | – | `lib/ezi18n/classes/eztranslatorgroup.php:19` | – |
| `eZTranslatorHandler` | class | [Strategie / Handler](strategie-handler.md) | `lib/ezi18n/classes/eztranslatorhandler.php:19` | – |
| `eZTranslatorManager` | class | [Fassade / Dienst](fassade.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Registry](registry.md), [Singleton / instance()](singleton.md) | `lib/ezi18n/classes/eztranslatormanager.php:31` | – |
| `eZTSTranslator` | class extends eZTranslatorHandler | – | `lib/ezi18n/classes/eztstranslator.php:17` | Bietet Internationalisierung über XML-Dateien (.ts) |
| `eZUTF8Codec` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Singleton / instance()](singleton.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `lib/ezi18n/classes/ezutf8codec.php:22` | – |
