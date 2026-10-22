---
title: "Erweitern: warum, wo und wie"
audience: developer, builder
category: books
order: 520
summary: Warum Sie eine Erweiterung schreiben, wie Sie eine benennen und aktivieren und wohin ihre Ordner gehören, mit der Tabelle der Unterordner aus dem Buch, dem Aufbau einer Erweiterung in Exponential 6 zugeordnet.
keywords: Erweiterung, warum erweitern, Benennung, Erweiterungsverzeichnis, Module, Datentypen, Ereignistypen, Aktionen, Einstellungen, Buch Learning, extension, why extend, naming, ActiveExtensions, ActiveAccessExtensions, extension directory, modules, datatypes, eventtypes, actions, settings, Learning book
type: help
---
# Erweitern: warum, wo und wie

Kapitel 5 geht davon aus, dass fast jeder Bereich des Systems für Erweiterungen offen ist: Module, Datentypen,
Template-Operatoren, Workflow-Ereignisse, Aktionen und Benachrichtigungen. Eine **Erweiterung** ist ein Ordner unter
`extension/`, der eines oder mehrere davon hinzufügt, ohne den Kernel anzufassen.

## Warum Sie eine schreiben

Das Buch nennt Gründe; sie gelten weiterhin:

1. Die Funktion, die Sie brauchen, gibt es nicht.
2. Eine ähnliche gibt es, aber nicht genau so: Fragen Sie danach oder [senden Sie einen Pull Request](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md).
3. Eine Funktion hat einen Fehler, und die Behebung kann nicht warten: Eine kleine Erweiterung (oder ein
   Kernel-Override) ist eine vorübergehende Lösung.
4. Die Funktion gehört allein Ihrem Unternehmen.
5. Sie bewegt Daten in ein anderes System hinein oder aus ihm heraus.

Bevor Sie beginnen, sehen Sie sich an, was es gibt. Das Buch schickt Sie in die Foren und auf einen Subversion-Server
seiner Zeit; heute sind [der Erweiterungskatalog von se7enxweb](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/der-se7enxweb-erweiterungskatalog.md),
[Packagist](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/ideen-auf-packagist-finden.md) und
[GitHub](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/ideen-auf-github-finden.md) die richtigen Orte.

## Benennung

Wählen Sie einen eindeutigen Namen, damit er später nicht kollidiert. Verwenden Sie Buchstaben, Ziffern und einen
Unterstrich; **keinen Bindestrich** (das Buch sagt das, und es stimmt immer noch: Der Name wird Teil von Klassennamen und
Einstellungswerten). Das Beispiel des Buchs: eZ Systems stellte seinen eigenen ein `ez` voran. Neue Namen in Exponential
verwenden `exp` ([Die Erweiterung benennen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/verzeichnisaufbau-einer-erweiterung.md#die-erweiterung-benennen)).

## Sie aktivieren

Legen Sie einen Ordner `extension/<name>` an und teilen Sie es dem System in einer Einstellungsdatei mit. Die
Einstellung des Buchs ist die, die heute verwendet wird, und sie hat eine zweite Form für einen einzelnen Siteaccess:

```ini
# settings/override/site.ini.append.php: for every siteaccess
[ExtensionSettings]
ActiveExtensions[]=newextension

# settings/siteaccess/<site>/site.ini.append.php: for one siteaccess only
[ExtensionSettings]
ActiveAccessExtensions[]=newextension
```

Die Reihenfolge der Liste ist wichtig, wenn zwei Erweiterungen dieselbe Einstellung oder dasselbe Template bereitstellen
([Ladereihenfolge der Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ladereihenfolge-der-erweiterungen.md)).
Die Administration zeigt sie ebenfalls an und schaltet sie um ([Die Seite Erweiterungen](../../anleitungen/extensions-seite.md),
[Eine Erweiterung aktivieren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/eine-erweiterung-aktivieren.md)).

## Die Ordner

| Buch | Exponential 6 | Was er enthält |
|---|---|---|
| `actions/` | (selten verwendet) | Neue Aktionen für Formulare |
| `datatypes/` | `datatypes/<type>/<type>type.php` | Neue Datentypen ([Datentypen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datentypen.md)) |
| `eventtypes/` | `eventtypes/event/<type>/<type>type.php` | Neue Workflow-Ereignistypen ([Ereignistypen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/workflow-ereignistypen.md)) |
| `modules/` | `modules/<module>/module.php` und View-Skripte | Neue Module und Views ([Module und Views](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/module-und-views.md)) |
| `settings/` | `settings/` | Einstellungen der Erweiterung ([Einstellungen in einer Erweiterung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/einstellungen-in-einer-erweiterung.md)) |
| `translations/` | `translations/<locale>/translation.ts` | Übersetzungen |
| (keiner) | `design/<design>/` | Templates, Stile, Bilder eines Designs ([Ein Design in einer Erweiterung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/design-in-einer-erweiterung.md)) |
| (keiner) | `classes/` | Ihre PHP-Klassen, die über die Autoloads gefunden werden |
| (keiner) | `autoloads/eztemplateautoload.php` | Template-Operatoren und -Funktionen |
| (keiner) | `cronjobs/`, `bin/php/` | Cronjob-Skripte und Kommandozeilenskripte |
| (keiner) | `ezinfo.php`, `extension.xml` | Name, Version und Lizenz ([ezinfo.php](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ezinfo-php.md)) |

Den vollständigen Baum, mit dem, was der Kernel mit jedem Ordner tut, finden Sie unter [Der Verzeichnisaufbau einer Erweiterung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/verzeichnisaufbau-einer-erweiterung.md).
Führen Sie nach dem Hinzufügen einer Klasse `php bin/php/ezpgenerateautoloads.php -e` aus ([Autoloads und Klassenbenennung](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/autoloads-und-klassennamen.md)).

Weiter: [Die Bausteine einer Erweiterung](die-bausteine-einer-erweiterung.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 5, „Extending eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: als Wegweiser zu den aktuellen Entwicklerseiten umgestellt; Ordnernamen, Einstellungen und Konstanten gegen Exponential 6 geprüft; SOAP durch die REST-API ersetzt.
