---
title: "Modul 6: Cronjobs, Workflows und Benachrichtigungen"
audience: trainer, admin
category: training
order: 60
summary: Das sechste Modul von Website-Administration, mit Cronjob-Teilen und der Crontab, der Cronjob-Seite, Workflows und Freigaben, Benachrichtigungen und der Sperre über die E-Mail-Einstellungen. Fünf Lektionen, 3 Stunden 30 Minuten.
keywords: website-administration, modul 6, cronjobs, crontab, workflows, freigabe, trigger, benachrichtigungen, e-mail-einstellungen, mail-sperre, site administration, module 6, approval, triggers, notifications, e-mail preferences, mail gate
type: help
---
# Modul 6: Cronjobs, Workflows und Benachrichtigungen

Alles, was eine Website im Hintergrund tut, hängt von Cronjobs ab: Benachrichtigungen, Freigaben, Inhaltsaufträge, das
Audit-Protokoll und das Aufräumen der Einwilligungsdatensätze für E-Mails. In diesem Modul lernen Administratoren zu
sehen, was wann läuft, Teile sicher auszuführen und anzuhalten, einen Freigabe-Workflow einzurichten, der wirklich zu
Ende läuft, und Benachrichtigungen und die Mail-Sperre zu prüfen, ohne im Kurs je eine E-Mail zu versenden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Dauer | 3 Stunden 30 Minuten (fünf Lektionen) |
| Lektionen | 5 |
| Niveau | Fortgeschrittene |
| Format | Praktisch; eine Schulungsinstallation pro Person mit Zugang zur Administration und zur Shell |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [6.1 Cronjob-Teile und die Crontab](cronjob-teile-und-die-crontab.md) | 40 Min. | Teile auflisten, einen Teil und ein Skript ausführen, eine Crontab-Zeile schreiben |
| [6.2 Die Cronjob-Seite](die-cronjobs-seite.md) | 45 Min. | Teile unter Setup > Cronjobs ausführen, verfolgen, anhalten und fortsetzen |
| [6.3 Workflows und Freigaben](workflows-und-freigaben.md) | 50 Min. | Eine Freigabe einrichten, sie erteilen, einen Prozess abbrechen |
| [6.4 Benachrichtigungen ausführen](benachrichtigungen-ausfuehren.md) | 40 Min. | Einen Benachrichtigungslauf in Dateien vorab ansehen und proben |
| [6.5 E-Mail-Einstellungen und die Mail-Sperre](e-mail-einstellungen-und-das-mail-gate.md) | 35 Min. | Die Sperre, die Sperrliste und die Absenderangaben prüfen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person mit Zugang zur Administration und zur Shell (siehe
  [Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md)), ausgeliefert zum Beispiel mit
  `exp:velocity start`.
- Für Lektion 6.3 ein Test-Redakteurskonto pro Person mit einer Adresse unter `example.invalid`.
- Der Mail-Transport jeder Schulungsinstallation steht auf `file` (`site.ini [MailSettings] Transport=file`), damit ein
  Fehler keine E-Mail versenden kann. Prüfen Sie das vor dem Kurs.
- Proben Sie Lektion 6.3 auf Ihrer eigenen Installation: die Namen der Schaltflächen im Bearbeitungsformular des
  Workflows und die Stelle, an der Ihr Seitenpaket einen Benutzer einem Ordner folgen lässt (Lektion 6.4, Schritt 4).
- Optional: `php bin/php/console exp:collaborationsampledata --dry-run`, um Beispiel-Freigaben zu zeigen.

## Zeitplan

| Zeit | Teil |
|---|---|
| 0:00 | Lektionen 6.1 und 6.2 (85 Min.) |
| 1:25 | Pause (15 Min.) |
| 1:40 | Lektion 6.3 (50 Min.) |
| 2:30 | Pause (10 Min.) |
| 2:40 | Lektionen 6.4 und 6.5 (75 Min.) |
| 3:55 | Ende (3 Std. 30 Min. Unterricht plus Pausen) |

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
