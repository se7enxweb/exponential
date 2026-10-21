---
title: "Kurskatalog"
audience: trainer
category: training
order: 2
summary: Alle Trainerkurse für Exponential an einem Ort, mit Zielgruppe, Dauer, Niveau, Voraussetzungen und Modulen, dazu eine empfohlene Reihenfolge und Kombinationen für typische Gruppen.
keywords: kurskatalog, kurse, lehrplan, schulungsprogramm, redakteure, administratoren, entwickler, designer, layouts, hosting, migration, workshops, prüfungen, zeitplan, course catalogue, courses, curriculum, training program
featured: true
type: help
---
# Kurskatalog

Dieser Katalog listet die Trainerkurse der Hilfe auf. Jeder Kurs hat einen Kursüberblick mit seinen Modulen, einem
Lesepfad für den Trainer und vorgeschlagenen Zeitplänen; jedes Modul hat einen Zeitplan; jede Lektion ist eine Seite.
Die Zeitangaben enthalten die praktischen Übungen, aber keine Pausen.

## Die Kurse

| Kurs | Für | Niveau | Umfang | Voraussetzungen |
|---|---|---|---|---|
| [Train the Trainer](train-the-trainer/README.md) | Menschen, die Exponential unterrichten werden | Einsteiger bis Fortgeschrittene | 34 Lektionen, etwa 21 Stunden (3 bis 4 Tage) | Umgang mit Browser und Terminal |
| [Redakteure 101](redakteure-101/README.md) | Neue Redakteure und Autoren | Einsteiger | 27 Lektionen, etwa 19,5 Stunden (etwa 3 Tage) | Ein Browser |
| [Redakteure 201](../../../eng-us/help/trainers/editors-201/README.md) | Redakteure, die jede Woche veröffentlichen | Fortgeschrittene | 23 Lektionen, etwa 16 Stunden (etwa 3 Tage) | Redakteure 101 |
| [Exponential Layouts](../../../eng-us/help/trainers/layouts/README.md) | Redakteure und Website-Bauer, die Seiten aus Blöcken bauen | Einsteiger bis Fortgeschrittene | 23 Lektionen, etwa 17,5 Stunden (etwa 3 Tage) | Redakteure 101 |
| [Exponential Intelligence](../../../eng-us/help/trainers/exponential-intelligence/README.md) | Redakteure und Administratoren, die die KI-Funktionen nutzen | Einsteiger bis Experten | 21 Lektionen, etwa 15,5 Stunden (etwa 2,5 Tage) | Redakteure 101 |
| [Website-Administration](website-administratoren/README.md) | Menschen, die eine Website betreiben | Fortgeschrittene | 48 Lektionen, etwa 30 Stunden (etwa 5 Tage) | Redakteure 101, Grundkenntnisse der Kommandozeile |
| [Hosting und Betrieb](../../../eng-us/help/trainers/hosting-operations/README.md) | Systemadministratoren und Hosting-Mitarbeiter | Fortgeschrittene bis Experten | 28 Lektionen, etwa 20 Stunden (3 bis 4 Tage) | Website-Administration, Linux |
| [Suche](../../../eng-us/help/trainers/search/README.md) | Administratoren und Entwickler | Fortgeschrittene | 20 Lektionen, etwa 10 Stunden (1,5 bis 2 Tage) | Website-Administration |
| [Multisite und Seitenpakete](../../../eng-us/help/trainers/multisite-and-packages/README.md) | Administratoren und Entwickler | Fortgeschrittene bis Experten | 26 Lektionen, etwa 22 Stunden (etwa 4 Tage) | Website-Administration |
| [Entwickler](entwickler/README.md) | PHP-Entwickler, die auf Exponential aufbauen | Fortgeschrittene bis Experten | 52 Lektionen, etwa 44 Stunden (etwa 7 Tage) | PHP, Git |
| [Theme-Design](theme-designer/README.md) | Designer und Frontend-Entwickler | Einsteiger bis Experten | 30 Lektionen, etwa 24 Stunden (etwa 4 Tage) | HTML, CSS |
| [Newsletter](../../../eng-us/help/trainers/newsletter/README.md) | Redakteure und Administratoren, die Newsletter versenden | Einsteiger bis Experten | 20 Lektionen, etwa 16 Stunden (etwa 2,5 Tage; 1,5 Tage für den Pfad der Redakteure) | Redakteure 101 |
| [Migration zu Exponential](../../../eng-us/help/trainers/migration/README.md) | Teams, die eine Website zu Exponential umziehen | Fortgeschrittene bis Experten | 25 Lektionen, etwa 22,5 Stunden (etwa 4 Tage) | Website-Administration oder Entwickler |
| [Workshops und Bootcamps](../../../eng-us/help/trainers/workshops/README.md) | Gruppen, die ein kompaktes Programm brauchen | Alle | Programme für einen halben, 1, 3 und 5 Tage | Hängt vom Ablaufplan ab |
| [Prüfungen und praktische Übungen](pruefungen/README.md) | Trainer, die Teilnehmende prüfen | Alle | Etwa 6 Stunden für den Trainer; Tests und Aufgaben je Kurs | Der geprüfte Kurs |

Jeder Kursüberblick listet kürzere Zeitpläne auf, die aus seinen Modulen zusammengestellt sind.

## Empfohlene Reihenfolge

```text
                         Train the trainer  (for the trainer)

  Editors 101 --> Editors 201 --> Newsletters
       |              |
       |              +--> Exponential Layouts --> Exponential Intelligence
       |
       +--> Site administration --> Hosting and operations
                     |           --> Search
                     |           --> Multisite and site packages
                     |
                     +--> Developers --> Theme design
                                     --> Migrating to Exponential
```

## Kombinationen für typische Gruppen

| Gruppe | Programm |
|---|---|
| Ein neues Redaktionsteam | Redakteure 101 (3 Tage), dann Exponential Layouts (3 Tage), einen Monat später Redakteure 201; oder das [Bootcamp für Redakteure](../../../eng-us/help/trainers/workshops/module-03-three-day-bootcamps/editor-bootcamp.md) |
| Ein Intranet-Administrator | Redakteure 101, nur Module 1 bis 3; dann Website-Administration (5 Tage oder der intensive viertägige Zeitplan) |
| Die Entwickler einer Agentur | Entwickler (7 Tage oder der viertägige Schnelldurchgang für Agenturen), Theme-Design (4 Tage), Exponential Layouts (ausgewählte Module) |
| Das Support-Team eines Hosting-Unternehmens | Website-Administration, die Module zu Caches, Cronjobs, Logs und Upgrades; dann Hosting und Betrieb |
| Ein Migrationsprojektteam | Website-Administration (ausgewählte Module), Migration zu Exponential, dann nach Bedarf Entwickler |
| Neue Trainer | Train the Trainer, dann ein Fachkurs, den sie der Gruppe zurückunterrichten |

## Wie Sie einen Kurs nutzen

1. Lesen Sie den Kursüberblick und seinen Lesepfad.
2. Proben Sie jede Übung mit der Version, die Sie unterrichten werden
   ([Übungen schreiben, die funktionieren](train-the-trainer/modul-03-einen-kurs-gestalten/uebungen-schreiben-die-funktionieren.md)).
3. Bauen Sie die Übungsumgebung auf ([Modul 2 von Train the Trainer](train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/README.md)).
4. Führen Sie die Module nach ihren Zeitplänen durch; prüfen Sie das Verständnis mit den Quizfragen und den
   erwarteten Ergebnissen.
5. Sammeln Sie Feedback und verbessern Sie den Kurs
   ([Feedback sammeln](../../../eng-us/help/trainers/train-the-trainer/module-06-assessment-and-follow-up/collecting-feedback.md)).

## Verwandte Seiten

- [Trainer: hier beginnen](trainer-hier-beginnen.md)
- [Train the Trainer: Kursüberblick](train-the-trainer/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
