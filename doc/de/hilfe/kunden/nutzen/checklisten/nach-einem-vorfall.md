---
title: Nach einem Vorfall
audience: customer, admin, hosting
category: customer-success
order: 70
summary: Was der Betreiber einer Exponential-Website in den Tagen nach einem Ausfall, einem Datenfehler, einem fehlgeschlagenen Update oder einem Sicherheitsproblem tut - bestätigen, dass es vorbei ist, mit den Logs und dem Prüfprotokoll herausfinden, was geschah, die Betroffenen informieren, die Ursache beheben und Notizen führen.
keywords: Vorfall, nach einem Vorfall, Nachbesprechung, Ausfall, Fehlerreferenz, Log-Viewer, Prüfprotokoll, Sicherheitsmeldung, Lehren, Vorfallnotizen, incident, after an incident, post-incident review, outage, error reference, log viewer, audit trail, security report, lessons learned, incident notes
type: help
---
# Nach einem Vorfall

Diese Seite ist die Checkliste für den Betreiber, sobald das unmittelbare Problem vorbei ist: Die Website war nicht
erreichbar, Inhalte gingen verloren oder wurden geändert, ein Update ging schief, oder jemand ist möglicherweise
eingedrungen. Die Arbeit während des Vorfalls gehört Ihrem Hoster oder Partner und ihren Runbooks; diese Seite handelt
von den Tagen danach, damit dasselbe nicht wieder geschieht. Das allgemeine Vorgehen für kleine Teams steht unter
[Vorfallreaktion für kleine Teams](../../../../../en/us/help/businesses/reliability/incident-response-for-small-teams.md).

## Die Checkliste

```text
AFTER AN INCIDENT - [short name]     Started: ________   Ended: ________   Owner: ________

IS IT OVER?
[ ] The site works for visitors and staff (front page, a form, the store if any)
[ ] Maintenance mode is off, if it was used
[ ] Scheduled jobs run again (cron jobs not left paused)

WHAT HAPPENED (with the administrator or host)
[ ] Error references collected (ERR-...) and looked up in the logs
[ ] The audit trail of the time read: who did what
[ ] Cause written in one or two sentences
[ ] Whether personal data or payments were involved: yes / no / unknown

WHO NEEDS TO KNOW
[ ] Team informed of what happened and what changes
[ ] Customers or members affected informed, if needed
[ ] Payment provider informed, if payments may be involved
[ ] Your adviser asked whether a notification duty applies (not legal advice)
[ ] A vulnerability in Exponential reported privately to security@se7enx.com
[ ] A bug in Exponential reported in the issue tracker

FIX THE CAUSE
[ ] What will prevent it next time, with an owner and a date
[ ] Checklists updated, if a check would have caught it
[ ] Backups and a restore test confirmed after any restore

NOTES
[ ] Incident notes filed with the timeline, cause, effect, actions
```

## Wo jeder Punkt erledigt wird

| Punkt | Wo | Lesen Sie |
|---|---|---|
| Wartungsmodus | **Setup > Wartung** | [Wartungsmodus](../../../../../en/us/guides/maintenance-mode.md) |
| Pausierte Cron-Teile | **Setup > Cronjobs** | [Cronjob-Teile pausieren](../../../../../en/us/features/6.0/cronjob-pause.md) |
| Fehlerreferenzen | **Setup > Logdateien**, Feld **Fehlerreferenz** | [Einen Fehler anhand seiner Referenz finden](../../../../../en/us/guides/finding-an-error-by-its-reference.md) |
| Die Fehler des letzten Tages | **Setup > Logdateien > Fehlerübersicht** | [Der Log-Viewer](../../../../../en/us/features/6.0/log-viewer.md) |
| Wer was getan hat | Das Prüfprotokoll; es hält Anmeldungen, Inhaltsänderungen, Schreibvorgänge in Einstellungen, das Leeren von Caches, Deployments, Bestellungen und Änderungen der Mehrwertsteuer fest | [Das Prüfprotokoll](../../../../../en/us/features/6.0/audit-trail.md) |
| Inhalte verloren oder geändert | Versionen und der Papierkorb | [Veröffentlichen und Versionen](../../../../../en/us/help/publishing-and-versions.md) |
| Eine Wiederherstellung | Hoster | [Aus einem Backup wiederherstellen](../../../hosting/runbooks/aus-einem-backup-wiederherstellen.md) |
| Eine kompromittierte Website | Hoster | [Kompromittierte Website](../../../hosting/runbooks/kompromittierte-website.md) |

## An das Projekt melden

- **Ein Sicherheitsproblem** in Exponential wird vertraulich an security@se7enx.com gemeldet, nie in einem öffentlichen
  Tracker (`SECURITY.md`, [Wenn etwas schiefgeht](../../../../../en/us/help/when-something-goes-wrong.md)). Lassen Sie Passwörter,
  Geheimnisse und personenbezogene Daten weg.
- **Ein Fehler** in Exponential geht an <https://github.com/se7enxweb/exponential/issues>. Die Meldeseite der Hilfe in der
  Administration, `/exphelp/report`, füllt das Issue vor und listet die zu kopierenden Diagnosedaten, nie Passwörter,
  Schlüssel, Datenbankhosts oder personenbezogene Daten ([Das Hilfesystem](../../../../../en/us/features/6.0/help-system.md)).
- **Ein Problem mit dem Code oder der Einrichtung Ihrer eigenen Website** geht an Ihren Partner.

## Vorfallnotizen

Führen Sie zu jedem Vorfall eine kurze Notiz, auch zu einem kleinen:

```text
INCIDENT NOTE - [short name]
When it started / was noticed / ended: ________ / ________ / ________
What visitors or staff saw: ______________________________________________
Cause: ___________________________________________________________________
Effect (pages, orders, data, hours): _____________________________________
What we did during it: ___________________________________________________
What we change so it does not happen again (owner, date): ________________
Reported to the project: no / bug # ______ / security report sent ________
```

> **Hinweis**
> Keine Rechtsberatung. Wenn personenbezogene Daten oder Zahlungen betroffen sein könnten, können Meldepflichten kurze
> Fristen haben. Fragen Sie sofort Ihren Anwalt oder Datenschutzbeauftragten, und Ihren Zahlungsanbieter, wenn Zahlungen
> betroffen sind.

## Verwandte Seiten

- [Vorfallreaktion für kleine Teams](../../../../../en/us/help/businesses/reliability/incident-response-for-small-teams.md)
- [Wenn etwas schiefgeht](../../../../../en/us/help/when-something-goes-wrong.md)
- [Tägliche Checkliste](taeglich.md)
- [Checklisten für Betreiber](README.md)
- [Freie Dokumentation](../../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
