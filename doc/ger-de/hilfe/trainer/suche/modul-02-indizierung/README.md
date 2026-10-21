---
title: "Modul 2: Indizierung"
audience: trainer, admin
category: training
order: 20
summary: Das zweite Modul des Kurses Suche, Indizierung beim Veröffentlichen, verzögerte Indizierung mit dem Cronjob indexcontent, Neuaufbau des ganzen Index und Auswahl durchsuchbarer Attribute. Vier Lektionen, 2 Stunden.
keywords: suchkurs, modul 2, indizierung, DelayedIndexing, indexcontent, ezpending_actions, updatesearchindex, durchsuchbare attribute, neu indizieren, search course, module 2, indexing, searchable attributes, reindex
type: help
---
# Modul 2: Indizierung

Eine Suche kann nur finden, was in ihrem Index steht. Dieses Modul verfolgt Inhalte bis in den Index: beim
Veröffentlichen, über die Warteschlange der verzögerten Indizierung und ihren Cronjob, bei einem vollständigen
Neuaufbau und über die Klasseneinstellung, die entscheidet, welche Felder überhaupt indiziert werden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Dauer | 2 Stunden (vier Lektionen) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |
| Form | Praxisorientiert; Administration und Terminal auf der eigenen Schulungsinstallation |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [2.1 Indizierung beim Veröffentlichen](indizierung-beim-veroeffentlichen.md) | 25 Min. | Nachweisen, dass Entwürfe nicht gefunden werden, neue Versionen Wörter ersetzen und Verschieben keine Neuindizierung braucht |
| [2.2 Verzögerte Indizierung und der Cronjob indexcontent](verzoegerte-indizierung-und-der-indexcontent-cronjob.md) | 35 Min. | Die verzögerte Indizierung einschalten, die Warteschlange beobachten, den Cronjob ausführen, sie ausschalten |
| [2.3 Den Suchindex neu aufbauen](den-suchindex-neu-aufbauen.md) | 30 Min. | `updatesearchindex.php` mit und ohne `--clean` ausführen und für eine Live-Website planen |
| [2.4 Durchsuchbare Attribute wählen](durchsuchbare-attribute-waehlen.md) | 30 Min. | Die Markierung **Durchsuchbar** einer Klasse lesen und ändern und neu aufbauen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person (die Lektionen 2.2 und 2.4 ändern Einstellungen und eine Klasse; teilen Sie
  für dieses Modul nie eine Installation).
- Ein Terminal im Wurzelverzeichnis der Installation für jede Person, und der Name des öffentlichen Siteaccess an der
  Tafel.
- Prüfen Sie vor dem Kurs, dass `DelayedIndexing` auf `disabled` steht
  (`php bin/php/console exp:ini get site.ini/SearchSettings/DelayedIndexing`).
- Proben Sie Lektion 2.3 einmal, um zu wissen, wie lange ein Neuaufbau mit den Schulungsinhalten dauert.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 | Lektion 2.1 (25 Min.) |
| 0:25 | Lektion 2.2 (35 Min.) |
| 1:00 | Lektion 2.3 (30 Min.) |
| 1:30 | Lektion 2.4 (30 Min.) |
| 2:00 | Ende des Moduls |

## Verwandte Seiten

- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
