---
title: "Modul 10: Workflows, Ereignisse und Cronjobs"
audience: trainer, developer
category: training
order: 100
summary: Das zehnte Modul des Kurses Entwickler. Die Teilnehmenden verfolgen Workflows, Trigger und wartende Prozesse, schreiben einen Workflow-Ereignistyp, der das Veröffentlichen protokolliert, und schreiben einen Cronjob-Teil, der die Tabelle ihrer Erweiterung aufräumt. 2 Stunden 40 Minuten.
keywords: entwickler, modul 10, workflow, trigger, workflow-prozess, ereignistyp, eZWorkflowEventType, workflow.ini, cronjob, cronjob-teil, runcronjobs.php, cron:, developers course, module 10, workflow process, event type, cronjob part
type: help
---
# Modul 10: Workflows, Ereignisse und Cronjobs

Code läuft nicht nur, wenn jemand eine Seite öffnet. Dieses Modul behandelt die beiden anderen Wege: als Schritt
eines Workflows rund um eine Kernel-Operation wie das Veröffentlichen und als Cronjob nach einem Zeitplan. Die
Teilnehmenden verbinden einen Workflow mit einem Trigger, schreiben einen Ereignistyp und schreiben einen Cronjob-Teil
für ihre Erweiterung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 2 Stunden 40 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 3 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 10.1: Workflows und Trigger für Entwickler](workflows-und-trigger.md) | 45 Minuten | Einen Workflow bauen, ihn mit dem Veröffentlichen verbinden, den Workflow-Cronjob ausführen |
| [Lektion 10.2: Ein eigener Workflow-Ereignistyp](ein-workflow-ereignistyp.md) | 60 Minuten | **Log publishing** schreiben, registrieren und nach dem Veröffentlichen verwenden |
| [Lektion 10.3: Ein Cronjob-Teil](ein-cronjob-teil.md) | 45 Minuten | Ein Aufräumskript schreiben, ihm einen Teil geben, es auf drei Arten ausführen |

## Dieses Modul vorbereiten

- Alle Teilnehmenden brauchen ein Administratorkonto auf einer eigenen Installation: Die Lektionen ändern Trigger,
  die jedes Veröffentlichen auf der Installation betreffen.
- Lektion 10.3 braucht die Tabelle `mysite_greeting` aus Lektion 9.3.
- Prüfen Sie, dass `runcronjobs.php` auf den Rechnern der Teilnehmenden unter ihrem eigenen Benutzer läuft.
- Prüfen Sie am Ende des Moduls, dass alle Teilnehmenden die Trigger für das Veröffentlichen zurückgesetzt haben.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 10.1 | 45 |
| Lektion 10.2 | 60 |
| Pause | 10 |
| Lektion 10.3 | 45 |
| **Gesamt** | **160** |

## Verwandte Seiten

- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
