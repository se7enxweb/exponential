---
title: "Modul 4: Von der 4.x-Linie"
audience: trainer, admin, developer
category: training
order: 40
summary: Das Modul zum In-place-Umzug einer alten 4.x-Website des Legacy-Kernels zu Exponential 6 - Bestandsaufnahme, das Datenbank-Runbook, Dateien, Einstellungen, Designs und die Portierung von Erweiterungen auf PHP 8 sowie die Überprüfung des Ergebnisses. Vier Lektionen, 3 Stunden 30 Minuten.
keywords: migration zu exponential, legacy-kernel, ältere versionen, datenbank-update, phase a, phase b, erweiterungen portieren, einstellungen, überprüfen, modul, migrating to exponential, 4.x, legacy kernel, older releases, database update, phase A, phase B, PHP 8, porting extensions, settings, verify, module
type: help
---
# Modul 4: Von der 4.x-Linie

Websites, die noch eine 4.x-Version des alten Legacy-Kernels unter PHP 5 betreiben, ziehen in place zu Exponential 6
um: Es wird nichts exportiert und importiert, die Datenbank wird auf den aktuellen Stand gebracht und der Code wird
portiert. Dieses Modul folgt Kapitel 14 des Installationshandbuchs in der Reihenfolge, in der ein echtes Projekt es
abarbeitet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Dauer | 3 Stunden 30 Minuten |
| Lektionen | 4 |
| Niveau | Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [4.1 Bestandsaufnahme einer alten 4.x-Website](bestandsaufnahme-der-alten-website.md) | 45 Minuten | Versionen, Größen und Einstellungen lesen; die Prüfung der Dateikonsistenz ausführen; einen Bestandsaufnahmebogen schreiben |
| [4.2 Das Datenbank-Runbook: Vorabprüfung, Phase A und Phase B](das-datenbank-runbook.md) | 60 Minuten | Die Update-Dateien prüfen; die Vorabprüfungen ausführen; das Runbook für eine 4.3-Website schreiben |
| [4.3 Dateien, Einstellungen, Designs und die Portierung von Erweiterungen auf PHP 8](dateien-einstellungen-und-code.md) | 60 Minuten | Die Klassen einer Erweiterung auf Syntax prüfen und laden; PHP-5-Code portieren oder alte Einstellungen durchsehen |
| [4.4 Die migrierte Website überprüfen](die-migrierte-website-pruefen.md) | 45 Minuten | Die nur lesenden Prüfungen aus Kapitel 14.12 ausführen; häufigen Problemen ihre Lösungen zuordnen |

## Dieses Modul vorbereiten

- [ ] Eine Schulungsinstallation pro Person mit Shell-Zugang und einem Administratorkonto; `sqlite3` installiert.
- [ ] Jede Person hat die Zählungen aus Lektion 4.1 notiert; sie werden in Lektion 4.4 verglichen.
- [ ] Kapitel 14 des Installationshandbuchs vom Trainer einmal vollständig gelesen; es ist lang, und die
      Teilnehmenden werden nach ihrer eigenen Ausgangsversion fragen.
- [ ] Wenn möglich, eine alte Demo-Datenbank (ohne personenbezogene Daten) auf einem separaten Rechner eingespielt,
      um eine echte Versionszeile von `ezsite_data` einer alten Version zu zeigen; andernfalls unterrichten Sie wie
      beschrieben mit der Schulungsinstallation.
- [ ] Teilen Sie die Gruppe für Lektion 4.3: Entwickler portieren Code, Administratoren sehen Einstellungen durch.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 4.1 | 45 |
| Lektion 4.2 | 60 |
| Pause | 15 |
| Lektion 4.3 | 60 |
| Lektion 4.4 | 45 |
| Gesamt | 225 (3 Stunden 30 Minuten Lektionen, zuzüglich einer Pause von 15 Minuten) |

## Verwandte Seiten

- [Migration zu Exponential: Kursüberblick](../README.md)
- [Kapitel 14: Migration von der 4.x-Linie](../../../../installation/14-migration-von-4x.md): das Kapitel, auf dem dieses
  Modul aufbaut.
- [Ältere Versionen dieses Kernels](../../../../installation/migration/aeltere-versionen.md): welches Kapitel zu welcher
  Version passt.
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
