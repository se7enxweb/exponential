---
title: "Modul 3: Datenbanken"
audience: trainer, hosting, admin
category: training
order: 30
summary: Das dritte Modul von Hosting und Betrieb, eine Datenbank in der offiziellen Reihenfolge wählen, SQLite im Produktivbetrieb, Datenbankserver für eine Website, Datenbanksicherungen und der Wechsel zwischen Datenbanksystemen. Vier Lektionen, 2 Stunden 55 Minuten.
keywords: hosting und betrieb, modul 3, datenbanken, SQLite, PostgreSQL, MariaDB, MySQL, MongoDB, Oracle, sicherung, wiederherstellung, datenbanksystem wechseln, hosting and operations, module 3, databases, backup, restore, switching engines
type: help
---
# Modul 3: Datenbanken

Exponential 6.x unterstützt sechs Datenbanken, und ein Hoster muss diejenige betreiben, prüfen und sichern, die ein
Tarif enthält. Dieses Modul beginnt mit der offiziellen Reihenfolge des Projekts und der Wahl, widmet SQLite (dem
Standard und einer Produktivdatenbank für einen Rechner) eine Lektion, behandelt dann Datenbankserver und endet mit
Sicherungen und dem Umzug einer Website zwischen Datenbanksystemen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Dauer | 2 Stunden 55 Minuten (vier Lektionen) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |
| Form | Praxisorientiert; eine virtuelle Linux-Maschine mit root pro Person |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [3.1 Eine Datenbank wählen](eine-datenbank-auswaehlen.md) | 40 Min. | Die offizielle Reihenfolge lernen, Datenbanksysteme vergleichen, die Datenbank einer Website finden |
| [3.2 SQLite im Produktivbetrieb](sqlite-im-produktivbetrieb.md) | 45 Min. | Die Überwachungsprüfungen, ANALYZE und einen abschneidenden Checkpoint ausführen |
| [3.3 Datenbankserver für eine Website](datenbankserver.md) | 45 Min. | Eine PostgreSQL- oder MariaDB-Datenbank anlegen und darauf installieren |
| [3.4 Datenbanksicherungen und der Wechsel zwischen Datenbanksystemen](datenbanksicherungen-und-umzuege.md) | 45 Min. | Eine SQLite-Datenbank sichern, prüfen und wiederherstellen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation auf SQLite pro Person und das Kommandozeilenprogramm `sqlite3`.
- Für Lektion 3.3: PostgreSQL oder MariaDB auf der virtuellen Maschine jeder Person installiert, mit der passenden
  PHP-Erweiterung (`pgsql` oder `mysqli`) und einer zweiten Kopie des Codes, in die installiert wird. Entscheiden Sie
  vorab, welches Datenbanksystem der Kurs verwendet.
- Nur Wegwerf-Passwörter für die Schulung; zeigen Sie nie ein echtes Passwort am Beamer.
- Proben Sie die Wiederherstellung aus Lektion 3.4 einmal auf Ihrem eigenen Rechner; es ist der Schritt, an den sich die
  Teilnehmenden erinnern.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 | Lektion 3.1 (40 Min.) |
| 0:40 | Lektion 3.2 (45 Min.) |
| 1:25 | Pause (15 Min.) |
| 1:40 | Lektion 3.3 (45 Min.) |
| 2:25 | Lektion 3.4 (45 Min.) |
| 3:10 | Ende (2 Std. 55 Min. Lektionen plus eine Pause) |

## Verwandte Seiten

- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Datenbankunterstützung](../../../datenbankunterstuetzung.md)
- [Datenbanken in der offiziellen Reihenfolge](../../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
