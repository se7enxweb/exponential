---
title: "Modul 9: Die Konsole und persistente Objekte"
audience: trainer, developer
category: training
order: 90
summary: Das neunte Modul des Kurses Entwickler. Die Teilnehmenden finden und starten Befehle mit der Konsole, schreiben einen eigenen Befehl, speichern die Daten ihrer Erweiterung über eZPersistentObject in einer Tabelle schreiben das verbleibende SQL sicher mit eZDB und liefern eine Tabelle als Migration aus. 4 Stunden 20 Minuten.
keywords: entwickler, modul 9, konsole, bin/php/console, ext:-befehle, eZScript, eZPersistentObject, persistente objekte, definition, fetchObjectList, eZDB, escapeString, transaktionen, migrationen, developers course, module 9, console, ext: commands, persistent objects, transactions, migrations, exp:migration:migrate
type: help
---
# Modul 9: Die Konsole und persistente Objekte

Echte Erweiterungen führen Aufgaben auf der Kommandozeile aus und speichern eigene Daten. Dieses Modul vermittelt die
Konsole, die jeden Befehl startet, einen eigenen Befehl der Teilnehmenden mit Optionen und Exit-Codes, eine Tabelle,
die mit `eZPersistentObject` einer Klasse zugeordnet wird, die Regeln für das SQL, das sich mit persistenten
Objekten nicht ausdrücken lässt, und eine Migration, die eine Tabelle auf jede Installation einer Site bringt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 4 Stunden 20 Minuten (mit zwei Pausen von 10 Minuten) |
| Lektionen | 5 |
| Niveau | Einsteiger bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 9.1: Die Konsole](die-konsole.md) | 35 Minuten | `list`, Hilfe, Namensräume, Cronjob-Teile |
| [Lektion 9.2: Ein eigener Befehl](ein-eigener-befehl.md) | 50 Minuten | `ext:mysite_hello:latest` mit Optionen, Hilfe und Exit-Codes |
| [Lektion 9.3: Persistente Objekte](persistente-objekte.md) | 60 Minuten | Eine Tabelle und die Klasse `mysiteGreeting`: speichern, holen, zählen, entfernen |
| [Lektion 9.4: Sicheres SQL mit eZDB](sicheres-sql-mit-ezdb.md) | 45 Minuten | Ein Bericht, Escaping, eine Transaktion mit Rollback, SQL-Dialekte |
| [Lektion 9.5: Migrationen](migrationen.md) | 50 Minuten | Eine Tabelle als Migration ausliefern: erzeugen, prüfen, Probelauf, simulieren, ausführen, zurücknehmen |

## Dieses Modul vorbereiten

- Die Lektionen 9.3 und 9.4 verwenden für die Tabelle die SQLite-Syntax; Entwicklungsinstallationen aus
  `exp:install` laufen auf SQLite. Bereiten Sie für einen Kurs auf MySQL oder PostgreSQL das `CREATE TABLE` für
  diese Datenbank vorab vor.
- Die Teilnehmenden führen Befehle als eigener Benutzer aus, nicht als root.
- Halten Sie fertige Kopien von `latest.php`, `mysitegreeting.php` und den Skripten für Teilnehmende bereit, die
  zurückfallen.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 9.1 | 35 |
| Lektion 9.2 | 50 |
| Pause | 10 |
| Lektion 9.3 | 60 |
| Lektion 9.4 | 45 |
| Pause | 10 |
| Lektion 9.5 | 50 |
| **Gesamt** | **260** |

## Verwandte Seiten

- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
