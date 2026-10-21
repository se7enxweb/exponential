---
title: "Modul 5: Feinabstimmung und Fehlersuche"
audience: trainer, admin
category: training
order: 50
summary: Das fünfte Modul des Kurses Suche, die Sucheinstellungen abstimmen, Suche und Sprachen, eine Checkliste für fehlende Ergebnisse und die Suchleistung messen. Vier Lektionen, 2 Stunden.
keywords: suchkurs, modul 5, feinabstimmung, fehlersuche, platzhalter, stoppwörter, sprachen, fehlende ergebnisse, checkliste, exp:benchmark, leistung, search course, module 5, tuning, troubleshooting, wildcards, stop words, languages, missing results, checklist, performance
type: help
---
# Modul 5: Feinabstimmung und Fehlersuche

Das letzte Modul macht die Teilnehmenden zu den Personen, die andere anrufen, wenn die Suche sich falsch verhält. Sie
stimmen die Einstellungen pro Siteaccess ab, verstehen, wie Übersetzungen durchsucht werden, folgen einer Checkliste
für fehlende Ergebnisse und messen die Suchseite, statt zu raten.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Dauer | 2 Stunden (vier Lektionen) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene bis Experten |
| Form | Praxis auf der eigenen Schulungsinstallation; eine vom Trainer vorbereitete Fallübung |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [5.1 Die Sucheinstellungen abstimmen](die-sucheinstellungen-abstimmen.md) | 30 Min. | Platzhalter pro Siteaccess ein- und ausschalten, die Stoppwortregel und die Grenzen lesen |
| [5.2 Suche und Sprachen](suche-und-sprachen.md) | 25 Min. | Übersetzte Inhalte in zwei Siteaccesses durchsuchen |
| [5.3 Wenn Ergebnisse fehlen](wenn-ergebnisse-fehlen.md) | 35 Min. | Vier vorbereitete Fälle „nicht gefunden“ mit der Checkliste lösen |
| [5.4 Suchleistung und Kapazität](suchleistung-und-kapazitaet.md) | 30 Min. | Die Suchseite messen und mit einer Ausgangsmessung vergleichen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person mit Beispielinhalten, ausgeliefert (zum Beispiel mit `exp:velocity start`),
  damit `exp:benchmark` sie erreichen kann.
- Für Lektion 5.2: eine zweite Inhaltssprache mit eigenem öffentlichen Siteaccess auf jeder Installation, oder planen
  Sie die Lektion als Vorführung.
- Für Lektion 5.3 auf jeder Installation vier Artikel mit eindeutigen Wörtern im Titel, jeder mit einer Ursache: einer
  als Entwurf belassen, einer in einer Sektion, die die anonyme Rolle nicht lesen darf, einer, dessen Wort nur in
  einem Feld steht, das Sie vor dem Neuaufbau des Index nicht durchsuchbar gemacht haben, und einer, der veröffentlicht
  wurde, während `DelayedIndexing` eingeschaltet war und der Cronjob nicht lief. Behalten Sie die Liste der Wörter und
  Ursachen für sich.
- Für Lektion 5.4: Bitten Sie die Teilnehmenden, auf einem gemeinsamen Rechner Messungen reihenweise nacheinander
  auszuführen.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 | Lektion 5.1 (30 Min.) |
| 0:30 | Lektion 5.2 (25 Min.) |
| 0:55 | Lektion 5.3 (35 Min.) |
| 1:30 | Lektion 5.4 (30 Min.) |
| 2:00 | Ende des Moduls |

## Verwandte Seiten

- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
