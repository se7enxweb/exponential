---
title: "Modul 7: Logs und das Audit-Protokoll"
audience: trainer, admin
category: training
order: 70
summary: Das siebte Modul von Website-Administration, mit den Logdateien, dem Log-Viewer, der Suche nach einem Fehler über seine Referenz, dem Audit-Protokoll und Ermittlungen damit. Fünf Lektionen, 3 Stunden 15 Minuten.
keywords: website-administration, modul 7, logs, log-viewer, fehlerreferenz, 404.log, security.log, audit-protokoll, audit-konsole, ermittlung, site administration, module 7, log viewer, error reference, audit trail, audit console, investigation
type: help
---
# Modul 7: Logs und das Audit-Protokoll

Logs sagen einem Administrator, was schiefgegangen ist; das Audit-Protokoll sagt, wer was getan hat. Dieses Modul
vermittelt beides: wo die Logs liegen und wie sie rotiert werden, wie man sie unter **Setup > Logdateien** liest und
durchsucht, wie man aus der Fehlerreferenz eines Besuchers die Ursache ermittelt und wie man das manipulationssichere
Audit-Protokoll nach einem Vorfall liest, prüft und exportiert.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Dauer | 3 Stunden 15 Minuten (fünf Lektionen) |
| Lektionen | 5 |
| Niveau | Fortgeschrittene |
| Format | Praktisch; eine Schulungsinstallation pro Person mit Zugang zur Administration und zur Shell |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [7.1 Die Logdateien](die-logdateien.md) | 35 Min. | Die Logs finden, eine 404-Zeile lesen, die Einstellungen zur Rotation prüfen |
| [7.2 Der Log-Viewer](der-log-viewer.md) | 45 Min. | Ein Log verfolgen, durchsuchen und teilen; Fehlerübersicht und Zustand der Logs lesen |
| [7.3 Einen Fehler über seine Referenz finden](einen-fehler-ueber-seine-referenz-finden.md) | 35 Min. | Einer Referenz oder einem Zeitfenster bis zur Anfrage folgen |
| [7.4 Das Audit-Protokoll](das-audit-protokoll.md) | 45 Min. | Die eigene Anmeldung finden, die Konsole filtern, die Kette prüfen |
| [7.5 Mit dem Audit-Protokoll ermitteln](untersuchen-mit-dem-audit-protokoll.md) | 35 Min. | Ein Entfernen finden, Ereignisse exportieren, eine Prüferrolle bauen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person mit Zugang zur Administration und zur Shell, ausgeliefert zum Beispiel mit
  `exp:velocity start`.
- Für Lektion 7.3: eine Fehlerreferenz von Ihrer eigenen Installation am Beamer oder die Prüfung, dass die
  **Fehlerübersicht** der Teilnehmenden eine zeigt; andernfalls unterrichten Sie nur die Suche nach Zeitfenster.
- Der Mail-Transport steht auf jeder Schulungsinstallation auf `file`, damit Audit-Alarme keine E-Mails versenden
  können.
- Für Lektion 7.5 pro Person eine Test-Benutzergruppe und ein Testbenutzer unter `example.invalid`.
- Verwenden Sie im Kurs nie die Logs oder das Audit einer Produktions-Website: Sie enthalten Daten echter Menschen.

## Zeitplan

| Zeit | Teil |
|---|---|
| 0:00 | Lektionen 7.1 und 7.2 (80 Min.) |
| 1:20 | Pause (15 Min.) |
| 1:35 | Lektion 7.3 (35 Min.) |
| 2:10 | Pause (10 Min.) |
| 2:20 | Lektionen 7.4 und 7.5 (80 Min.) |
| 3:40 | Ende (3 Std. 15 Min. Unterricht plus Pausen) |

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
