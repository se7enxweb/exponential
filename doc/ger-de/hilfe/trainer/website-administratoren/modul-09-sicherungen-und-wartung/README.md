---
title: "Modul 9: Sicherungen und Wartung"
audience: trainer, admin
category: training
order: 90
summary: Das neunte Modul von Website-Administration, damit, was eine Sicherung enthalten muss, dem Wartungsmodus, einem echten Test von Sicherung und Wiederherstellung und den Prüfungen auf fehlende Dateien, verwaiste Dateien und geänderte Installationsdateien. Vier Lektionen, 2 Stunden 55 Minuten.
keywords: website-administration, modul 9, sicherung, wiederherstellung, wiederherstellungstest, wartungsmodus, fehlende dateien, speicherdateien, quarantäne, dateikonsistenz, site administration, module 9, backup, restore, restore test, maintenance mode, missing files, storage files, quarantine, file consistency
type: help
---
# Modul 9: Sicherungen und Wartung

Jede Website braucht eines Tages ihre Sicherung. Dieses Modul zeigt Administratoren, was eine vollständige Sicherung
von Exponential enthält (einschließlich der Schlüsseldateien, die nichts anderes ersetzen kann), wie sie eine Website
sauber offline nehmen, wie sie wiederherstellen und nachweisen, dass die Wiederherstellung funktioniert hat, und wie
sie danach die Dateien und den Speicher prüfen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Dauer | 2 Stunden 55 Minuten (vier Lektionen) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |
| Format | Praktisch; eine Schulungsinstallation mit SQLite und Zugang zur Administration und zur Shell pro Person |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [9.1 Was gesichert werden muss](was-gesichert-werden-muss.md) | 40 Minuten | Die Datenbank, den Speicher und die Schlüsseldateien finden; einen Sicherungsplan schreiben |
| [9.2 Wartungsmodus](wartungsmodus.md) | 40 Minuten | Die Website offline und wieder online nehmen, über die Seite und die Shell |
| [9.3 Eine Sicherung und ein Wiederherstellungstest](sicherung-und-wiederherstellungstest.md) | 55 Minuten | Eine Schulungs-Website sichern, ändern, wiederherstellen und prüfen |
| [9.4 Dateien und Speicher prüfen](dateien-und-speicher-pruefen.md) | 40 Minuten | Fehlende Dateien finden, eine verwaiste Datei in Quarantäne verschieben, die Dateien des Release prüfen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person mit SQLite (die Voreinstellung von `exp:install`), mit installiertem
  Kommandozeilenwerkzeug `sqlite3`, Zugang zur Administration und zur Shell und einigen Bildern im Inhalt.
- Speicherplatz für eine Sicherung jeder Installation außerhalb ihres Verzeichnisses.
- Die Befehle zum Stoppen und Starten des Webservers, auf dem die Schulungs-Websites laufen (Velocity:
  `exp:velocity stop` und `exp:velocity start`).
- Für eine Vorführung von **Setup > Sicherung**: eine Installation mit der Erweiterung `git_manager` auf MySQL oder
  MariaDB (sie braucht `mysqldump`); andernfalls zeigen Sie nur ihre Dokumentation.
- Proben Sie Lektion 9.3 vollständig auf Ihrem eigenen Rechner und messen Sie die Zeit.

## Zeitplan

| Zeit | Teil |
|---|---|
| 0:00 | Lektionen 9.1 und 9.2 (80 Minuten) |
| 1:20 | Pause (15 Minuten) |
| 1:35 | Lektion 9.3 (55 Minuten) |
| 2:30 | Pause (10 Minuten) |
| 2:40 | Lektion 9.4 (40 Minuten) |
| 3:20 | Ende (2 Stunden 55 Minuten Unterricht plus Pausen) |

## Verwandte Seiten

- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
