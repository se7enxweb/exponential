---
title: "Modul 1: Wie die Suche funktioniert"
audience: trainer, admin
category: training
order: 10
summary: Das erste Modul des Kurses Suche, wo gesucht wird, die eingebaute Suchmaschine und ihre Grenzen, Solr über ezfind auf einen Blick und die Wahl eines Backends. Vier Lektionen, 1 Stunde 45 Minuten.
keywords: suchkurs, modul 1, wie die suche funktioniert, eZSearchEngine, Solr, ezfind, such-backend, schnellsuche, inhaltsfinder, search course, module 1, how search works, search backend, quick search, content finder
type: help
---
# Modul 1: Wie die Suche funktioniert

Dieses Modul gibt den Teilnehmenden die Landkarte, die sie für den Rest des Kurses brauchen: die Orte, an denen
gesucht wird, die eingebaute Suchmaschine, die die meisten Suchen beantwortet, den optionalen Solr-Server über die
Erweiterung ezfind und eine einfache Methode, zwischen beiden zu wählen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Dauer | 1 Stunde 45 Minuten (vier Lektionen) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |
| Form | Kurze Präsentationen, Übungen auf der Schulungsinstallation jeder Person und eine Partnerübung auf Papier |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [1.1 Wo in Exponential gesucht wird](wo-gesucht-wird.md) | 25 Min. | Die Schnellsuche, die erweiterte Suche, den Inhaltsfinder, die Website-Suche und die Hilfesuche ausprobieren |
| [1.2 Die eingebaute Suchmaschine](die-eingebaute-suchmaschine.md) | 30 Min. | Die Sucheinstellungen mit `exp:ini` lesen und Wörter, Akzente und Phrasen testen |
| [1.3 Solr und ezfind auf einen Blick](solr-und-ezfind-auf-einen-blick.md) | 25 Min. | `exp:solr status` ausführen und prüfen, welche Suchmaschine die Installation verwendet |
| [1.4 Ein Such-Backend wählen](ein-such-backend-waehlen.md) | 25 Min. | Für drei beschriebene Websites ein Backend empfehlen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person mit Beispielinhalten (zum Beispiel mit `exp:install` installiert, siehe
  [Installation über die Kommandozeile](../../../../installation/07-installation-ueber-die-konsole.md)), ein Administratorkonto und ein
  Terminal im Wurzelverzeichnis der Installation.
- Prüfen Sie, dass die öffentliche Website der Schulungsinstallation ein Suchfeld hat; wenn ihr Design keines hat,
  zeigen Sie in Lektion 1.1 direkt die Seite `content/search`.
- Führen Sie `php bin/php/console exp:solr status` einmal auf der Schulungsinstallation aus, damit Sie seine Meldung
  kennen.
- Drucken Sie die drei Website-Beschreibungen aus Lektion 1.4 aus, eine Kopie pro Paar.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 | Lektion 1.1 (25 Min.) |
| 0:25 | Lektion 1.2 (30 Min.) |
| 0:55 | Lektion 1.3 (25 Min.) |
| 1:20 | Lektion 1.4 (25 Min.) |
| 1:45 | Ende des Moduls |

## Verwandte Seiten

- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
