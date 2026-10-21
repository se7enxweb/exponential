---
title: "Modul 10: Upgrades, Erweiterungen und Pakete"
audience: trainer, admin
category: training
order: 100
summary: Das zehnte Modul von Website-Administration, mit dem Planen und Proben eines Upgrades, der Upgrade-Prüfung, Aktualisierungen aus der Administration mit ezupdate und git_manager, der Seite der Erweiterungen und Paketen. Fünf Lektionen, 3 Stunden 20 Minuten.
keywords: website-administration, modul 10, upgrade, upgrade-prüfung, ezupdate, git_manager, erweiterungen, ActiveExtensions, pakete, ezpkg, site administration, module 10, upgrade check, extensions, packages
type: help
---
# Modul 10: Upgrades, Erweiterungen und Pakete

Das letzte Modul des Kurses hält eine Website auf dem aktuellen Stand. Die Teilnehmenden planen und proben ein Upgrade
innerhalb der Linie 6.0, prüfen vorher Dateien und Datenbank auf Abweichungen, verwenden die Aktualisierungswerkzeuge
der Administration mit ihren Probeläufen, verwalten die Liste der aktiven Erweiterungen und gehen ohne Überraschungen
mit Paketen um.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Dauer | 3 Stunden 20 Minuten (fünf Lektionen) |
| Lektionen | 5 |
| Niveau | Fortgeschrittene bis Experten |
| Format | Praktisch; eine Schulungsinstallation mit Zugang zur Administration und zur Shell pro Person |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [10.1 Ein Upgrade planen und durchführen](ein-upgrade-planen-und-durchfuehren.md) | 50 Minuten | Die Version finden, den passenden Teil der Anleitung wählen, die Schritte proben und prüfen |
| [10.2 Die Upgrade-Prüfung](die-upgrade-pruefung.md) | 35 Minuten | Beide Prüfungen ausführen, eine geänderte Datei gemeldet sehen, checkmanifest.php verwenden |
| [10.3 Aktualisierungen mit ezupdate und git_manager](updates-mit-ezupdate-und-git-manager.md) | 45 Minuten | Nach Aktualisierungen suchen und einen Probelauf machen; die Upstream-Karte lesen |
| [10.4 Die Seite der Erweiterungen](die-erweiterungsseite.md) | 35 Minuten | Karten lesen, eine Änderung planen und überprüfen, sie verwerfen |
| [10.5 Pakete](pakete.md) | 35 Minuten | Ein Paket finden, lesen, validieren und exportieren |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person mit Zugang zur Administration und zur Shell; eine Sicherung aus Modul 9 als
  Rückfallebene.
- Das Werkzeug `sqlite3` für Lektion 10.1 auf Installationen mit SQLite (oder den Client Ihres Datenbanksystems, nur
  lesend).
- Für Lektion 10.3: eine Installation für den Projektor mit aktivem `ezupdate` (und `AllowUpdate` weiterhin
  ausgeschaltet) und, wenn möglich, eine, die ein Git-Checkout mit `git_manager` ist. Prüfen Sie vor dem Kurs, welche
  Werkzeuge die Installationen der Teilnehmenden haben.
- Lesen Sie das aktuelle Changelog und die Dokumente zu Verhaltensänderungen, die die Upgrade-Anleitung aufführt,
  damit Sie Fragen der Art „was hat sich geändert“ beantworten können.

## Zeitplan

| Zeit | Teil |
|---|---|
| 0:00 | Lektionen 10.1 und 10.2 (85 Minuten) |
| 1:25 | Pause (15 Minuten) |
| 1:40 | Lektion 10.3 (45 Minuten) |
| 2:25 | Pause (10 Minuten) |
| 2:35 | Lektionen 10.4 und 10.5 (70 Minuten) |
| 3:45 | Ende (3 Stunden 20 Minuten Unterricht plus Pausen) |

## Verwandte Seiten

- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
