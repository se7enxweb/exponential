---
title: Monatliche Checkliste
audience: customer, admin, businesses
category: customer-success
order: 30
summary: Die monatliche Checkliste für den Betreiber einer Exponential-Website - Releases und Updates, Backups, Sicherheitssignale, Konten, Formulardaten, Inhaltsprüfungen, Kosten und der Shop - mit den Fragen an Hoster und Partner und dem Zeitaufwand.
keywords: monatliche Checkliste, monatliche Routine, Updates, Release 6.0.x, Backups, Prüfprotokoll, Konten, Formulareinsendungen, Inhaltsprüfungen, Kosten, Fragen an den Hoster, monthly checklist, monthly routine, updates, backups, audit trail, accounts, form submissions, content checks, costs, host questions
type: help
---
# Monatliche Checkliste

Diese Seite ist die monatliche Prüfung des Betreibers, erledigt in den ersten Tagen jedes Monats mit Antworten vom
Hoster oder Partner. Sie hält die Website aktuell und sicher und die Kosten bekannt. Der Bereich für Unternehmen hat eine
umfassendere Vorlage für den Betrieb für Teams, die den Server selbst betreiben
([Monatliche Checkliste für den Betrieb](../../../../../en/us/help/businesses/templates/monthly-operations-checklist.md)); diese Liste
ist der Anteil des Betreibers daran.

## Die Checkliste

```text
MONTHLY CHECKLIST - month: ________     Owner: ________

RELEASES AND UPDATES (ask the partner)
[ ] Is there a new Exponential 6.0.x release? Planned for: ________
[ ] Extension updates due? Planned for: ________
[ ] After last month's update: was the upgrade check clean?

BACKUPS (ask the host)
[ ] Backups ran every day this month
[ ] Date of the last restore test: ________ (one each quarter)

SECURITY SIGNALS (ask the administrator)
[ ] Audit alerts of the month: any? Answered by: ________
[ ] Unusual failed sign-ins?
[ ] HTTPS certificate renews automatically or is valid for more than 30 days

ACCOUNTS
[ ] Leavers of the month disabled
[ ] New administrator roles intended
[ ] Unused accounts reviewed (Users > Check user accounts)

DATA
[ ] Form submissions processed; old ones removed as your retention schedule says
[ ] Privacy requests of the month closed

CONTENT
[ ] Broken links and content checks reviewed
[ ] Unused images and files tidied (content checks)

COSTS
[ ] Invoices of host, partner and services match the cost register

STORE (if you sell)
[ ] Store monthly checklist done
```

## Wo jeder Punkt erledigt wird

| Punkt | Wo, oder wen Sie fragen | Lesen Sie |
|---|---|---|
| Neue Releases | Partner; Sicherheitskorrekturen werden mit dem nächsten Release 6.0.x veröffentlicht | `SECURITY.md`, [Upgrade](../../../../../en/us/guides/upgrading.md) |
| Upgrade-Prüfung | **Setup > Upgrade-Prüfung**; beide Prüfungen lesen nur | [Upgrade-Prüfung](../../../../../en/us/guides/upgrade-check.md) |
| Backups und Wiederherstellungstests | Hoster | [Backups, die sich wiederherstellen lassen](../../../../../en/us/help/businesses/reliability/backups-that-restore.md) |
| Alarme des Prüfprotokolls | Das Dashboard des Prüfprotokolls, Richtlinie `audit/read` | [Das Prüfprotokoll](../../../../../en/us/features/6.0/audit-trail.md) |
| HTTPS | Hoster | [TLS und HTTPS](../../../../../en/us/help/hosting/security/tls-and-https.md) |
| Konten | **Benutzer > Benutzerkonten prüfen** | [Benutzerkonten prüfen](../../../../../en/us/features/6.0/user-hygiene.md) |
| Formulareinsendungen | **Setup > Gesammelte Informationen** | [Gesammelte Informationen](../../../../../en/us/guides/collected-information.md) |
| Datenschutzanfragen | Ihr Anfragenprotokoll | [DSGVO-Anfragen](../../handel/dsgvo-anfragen.md) |
| Inhaltsprüfungen | `/contentcheck/...` | [Die Inhaltsprüfungen](../../../../../en/us/features/6.0/content-checks.md) |
| Kosten | Ihr Kostenregister | [Betriebskosten für Betreiber](../betriebskosten-fuer-betreiber.md) |
| Shop | **Shop** | [Monatliche Shop-Checkliste](../../handel/monatliche-shop-checkliste.md) |

## Gute Antworten von Hoster und Partner

| Frage | Eine gute Antwort klingt so |
|---|---|
| Gibt es ein neues Release? | „Ja, 6.0.x; wir haben das Changelog gelesen, es auf einer Kopie getestet und schlagen vor, es am [Datum] einzuspielen.“ Oder: „Diesen Monat kein neues Release.“ |
| Sind die Backups gelaufen? | „Ja, jeden Tag; der letzte Wiederherstellungstest war am [Datum] und dauerte [Zeit].“ |
| Gab es Alarme? | „Jeder Alarm des Monats, mit seiner Ursache und dem, was wir getan haben.“ |
| Ist die Upgrade-Prüfung sauber? | „Ja, keine geänderten oder fehlenden Dateien.“ |

Eine Antwort „Ich glaube schon“ auf eine dieser Fragen ist ein Grund, nächste Woche erneut zu fragen.

> **Hinweis**
> Ein veröffentlichtes Release wird nie geändert: Eine Korrektur erreicht Sie als nächstes Release (`SECURITY.md`).
> Aktuell zu bleiben ist daher der einzige Weg, Korrekturen zu erhalten; planen Sie lieber mehrere kleine Updates im
> Jahr als ein großes ([Budget für Upgrades und Wartung](../../../../../en/us/help/businesses/costs/budgeting-for-upgrades-and-maintenance.md)).

## Verwandte Seiten

- [Wöchentliche Checkliste](woechentlich.md)
- [Vierteljährliche Checkliste](vierteljaehrlich.md)
- [Monatliche Checkliste für den Betrieb](../../../../../en/us/help/businesses/templates/monthly-operations-checklist.md)
- [Monatliche Shop-Checkliste](../../handel/monatliche-shop-checkliste.md)
- [Freie Dokumentation](../../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
