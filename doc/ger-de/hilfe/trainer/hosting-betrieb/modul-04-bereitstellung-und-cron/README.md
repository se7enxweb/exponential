---
title: "Modul 4: Bereitstellung und Cron"
audience: trainer, hosting, admin
category: training
order: 40
summary: Das vierte Modul von Hosting und Betrieb, eine Änderung mit exp:velocity deploy bereitstellen, Cron auf dem Server, die Cronjob-Konsole und das Aktualisieren von Kunden-Websites. Vier Lektionen, 2 Stunden 55 Minuten.
keywords: hosting und betrieb, modul 4, bereitstellung, exp:velocity deploy, cron, crontab, cronjob-konsole, anhalten, upgrade, kunden-websites aktualisieren, hosting and operations, module 4, deploy, cronjob console, upgrading customer sites
type: help
---
# Modul 4: Bereitstellung und Cron

Die meisten Störungen nach einer Änderung entstehen durch Schritte in der falschen Reihenfolge oder durch ausgelassene
Schritte. Dieses Modul vermittelt die Routinen, die eine Website aktuell und ihre geplanten Aufgaben am Laufen halten:
einen Befehl für eine PHP-Änderung, eine korrekte Crontab, die Cronjob-Konsole für Support-Mitarbeitende ohne Shell und
die Upgrade-Routine für viele Kunden-Websites.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Dauer | 2 Stunden 55 Minuten (vier Lektionen) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |
| Form | Praxisorientiert; eine Schulungsinstallation und eine Shell pro Person |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [4.1 Eine Änderung bereitstellen](eine-aenderung-bereitstellen.md) | 45 Min. | Einen Probelauf lesen, eine INI-Änderung bereitstellen, für eine Template-Änderung leeren |
| [4.2 Cron auf dem Server](cron-auf-dem-server.md) | 45 Min. | Teile auflisten, einen von Hand ausführen, eine Crontab als Benutzer der Website installieren |
| [4.3 Die Cronjob-Konsole](die-cronjob-konsole.md) | 40 Min. | Teile in der Administration ausführen, verfolgen, anhalten und fortsetzen |
| [4.4 Kunden-Websites aktualisieren](kunden-websites-aktualisieren.md) | 45 Min. | Eine Bestandsaufnahme machen, die Prüfungen ausführen, einen Upgrade-Plan schreiben |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person, ausgeliefert von Velocity, mit einer Shell als der Benutzer, dem ihre Dateien
  gehören.
- Ein Administratorkonto auf jeder Installation für die Lektionen 4.3 und 4.4.
- Eine zweite Kopie der Installation pro Person für Lektion 4.4 (ein frisches `exp:install` genügt).
- Prüfen Sie, dass die Teilnehmenden auf den Schulungsrechnern ihre eigene Crontab bearbeiten dürfen.
- Proben Sie die Bereitstellung aus Lektion 4.1 auf demselben Release, das die Teilnehmenden verwenden; die Anzahl der
  Schritte, die sie ausgibt, hängt vom Release ab.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 | Lektion 4.1 (45 Min.) |
| 0:45 | Lektion 4.2 (45 Min.) |
| 1:30 | Pause (15 Min.) |
| 1:45 | Lektion 4.3 (40 Min.) |
| 2:25 | Lektion 4.4 (45 Min.) |
| 3:10 | Ende (2 Std. 55 Min. Lektionen plus eine Pause) |

## Verwandte Seiten

- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Betrieb](../../../hosting/betrieb/README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
