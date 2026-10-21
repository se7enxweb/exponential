---
title: "Modul 1: Eine Migration planen"
audience: trainer, admin, developer, project-manager
category: training
order: 10
summary: Das Planungsmodul des Migrationskurses - die Exponential-Familie und die Wahl eines Wegs, Bestandsaufnahme und Umfang, die Phasen der gemeinsamen Checkliste sowie Probelauf, Umstellung und Rückweg. Vier Lektionen, 3 Stunden 15 Minuten.
keywords: migration zu exponential, planung, migrationsweg, bestandsaufnahme, umfang, checkliste, probelauf, umstellung, rückweg, modul, migrating to exponential, planning, migration path, inventory, scope, checklist, rehearsal, cut-over, rollback, module
type: help
---
# Modul 1: Eine Migration planen

Bevor jemand ein Importskript schreibt, braucht eine Migration ein Ziel, eine Bestandsaufnahme, einen Plan und einen
Rückweg. Dieses Modul vermittelt den Rahmen, den alle Migrationsanleitungen der Dokumentation gemeinsam haben, damit
die späteren Module zu bestimmten Quellen darauf aufbauen können.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Dauer | 3 Stunden 15 Minuten |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [1.1 Die Exponential-Familie und die Wahl eines Wegs](einen-weg-waehlen.md) | 45 Minuten | Für fünf beschriebene Websites die richtige Migrationsanleitung auswählen |
| [1.2 Bestandsaufnahme und Umfang](bestandsaufnahme-und-umfang.md) | 60 Minuten | Mit nur lesenden Abfragen eine Bestandsaufnahme ihrer Schulungsinstallation machen und eine Umfangstabelle ausfüllen |
| [1.3 Die Phasen einer Migration und die Checkliste](phasen-und-die-checkliste.md) | 45 Minuten | Die gemeinsame Checkliste in einen Plan mit Verantwortlichen und Abnahmekriterien umwandeln |
| [1.4 Probelauf, Umstellung und Rückweg](probelauf-umstellung-und-rueckfall.md) | 45 Minuten | Den Wartungsmodus nutzen und ein Umstellungs-Runbook mit einem Abschnitt zum Rückweg schreiben |

## Dieses Modul vorbereiten

- [ ] Eine Schulungsinstallation pro Person, installiert mit `php bin/php/console exp:install` (SQLite), mit
      Shell-Zugang ([Installation über die Kommandozeile](../../../../installation/07-installation-ueber-die-konsole.md)).
- [ ] Das Kommandozeilenwerkzeug `sqlite3` auf den Schulungsrechnern (Lektion 1.2).
- [ ] Der Migrations-Hub und die Checkliste einmal am Beamer geöffnet und die Produktversionen im Hub mit dem
      heutigen Datum abgeglichen (Lektion 1.1).
- [ ] Die fünf Website-Beschreibungen aus Lektion 1.1 und das WordPress-Szenario aus Lektion 1.3 ausgedruckt oder in
      einem gemeinsamen Dokument.
- [ ] Den Wartungsmodus einmal auf Ihrer eigenen Schulungsinstallation ausprobiert (Lektion 1.4); denken Sie daran,
      ihn wieder auszuschalten.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 1.1 | 45 |
| Lektion 1.2 | 60 |
| Pause | 15 |
| Lektion 1.3 | 45 |
| Lektion 1.4 | 45 |
| Gesamt | 210 (3 Stunden 15 Minuten Lektionen, zuzüglich einer Pause von 15 Minuten) |

## Verwandte Seiten

- [Migration zu Exponential: Kursüberblick](../README.md)
- [Migration zu und von Exponential](../../../../installation/migration/README.md): der Migrations-Hub, auf dem dieses
  Modul aufbaut.
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
