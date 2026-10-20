---
title: Tägliche Checkliste
audience: customer, editor, admin
category: customer-success
order: 10
summary: Zehn Minuten am Tag für die Person, die sich um eine Exponential-Website kümmert - ist die Website erreichbar, was wartet auf Freigabe, welche Formulare sind eingegangen, die Bestellungen des Shops, Alarme im Postfach - und was zu tun ist, wenn etwas nicht stimmt.
keywords: tägliche Checkliste, tägliche Routine, Website erreichbar, ausstehende Elemente, Freigaben, Formulareinsendungen, Bestellungen, Alarme des Prüfprotokolls, Postfach, Website-Betreiber, daily checklist, daily routine, site up, pending items, approvals, form submissions, orders, audit alerts, mailbox, site owner
type: help
---
# Tägliche Checkliste

Diese Seite ist die Zehn-Minuten-Prüfung für die Person, die sich an einem bestimmten Tag um die Website kümmert: oft
die Website-Leitung oder eine erfahrene Redakteurin, im Wechsel. Sie fängt die Probleme ab, die Besucher sonst zuerst
melden würden. Kopieren Sie die Liste und bewahren Sie sie dort auf, wo das Team arbeitet.

## Die Checkliste

```text
DAILY CHECKLIST - date: ________     Done by: ________

SITE
[ ] The front page and one inner page open normally (also on a phone)
[ ] Nothing on the front page is out of date (events past, offers ended)

WORK WAITING
[ ] My pending items / items waiting for approval looked at
[ ] Drafts due today are on their way (scheduled items, campaign pages)

WHAT CAME IN
[ ] New form submissions read and handed on (Setup > Collected information)
[ ] New registrations look genuine (the "New user registered" mails)
[ ] The shared mailbox: alert mails, order copies, replies from visitors

STORE (if you sell)
[ ] Store daily routine done (dashboard, orders waiting, failed payments)

IF SOMETHING LOOKS WRONG
[ ] Error page with a reference (ERR-...) noted and sent to the administrator
[ ] Host or partner called if the site is down or very slow
```

## Wo jeder Punkt erledigt wird

| Punkt | Wo | Lesen Sie |
|---|---|---|
| Elemente, die auf Freigabe warten | **Meine Warteliste** in der Administration | [Veröffentlichen und Versionen](../../../veroeffentlichen-und-versionen.md) |
| Geplante Elemente | Das Workflow-Ereignis *Bis Datum warten* läuft mit dem Cronjob | [Workflows](../../../../anleitungen/workflows.md) |
| Formulareinsendungen | **Setup > Gesammelte Informationen**, Richtlinie `infocollector/read` | [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md) |
| Registrierungs-E-Mails | Gesendet an `RegistrationEmail`, sonst `AdminEmail`, mit signierten Links zum Genehmigen oder Ablehnen | [Signierte Links in der Registrierungs-E-Mail](../../../../../eng-us/features/6.0/registration-admin-links.md) |
| Alarm-E-Mails | Die Alarmregeln des Prüfprotokolls schicken standardmäßig E-Mails an die `AdminEmail` der Website | [Das Prüfprotokoll](../../../../../eng-us/features/6.0/audit-trail.md) |
| Shop | Dashboard **Shop** | [Tägliche Shop-Routine](../../handel/taegliche-shop-routine.md) |
| Fehlerreferenzen | Eine Fehlerseite zeigt eine Referenz wie `ERR-0123456789` | [Einen Fehler anhand seiner Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md) |

## Wenn etwas nicht stimmt

| Sie sehen | Erster Schritt |
|---|---|
| Eine Seite mit einer Fehlerreferenz | Schicken Sie die Referenz, die Adresse und die Uhrzeit an Ihren Administrator; er findet die Einzelheiten unter **Setup > Logdateien** |
| „This form has expired“ | Meist eine zu lange offen gelassene Seite; neu laden und erneut senden ([Seite für abgelaufene Formulare](../../../../../eng-us/features/6.0/form-expired-page.md)) |
| Die ganze Website ist nicht erreichbar, oder eine unerwartete Wartungsseite | Rufen Sie den Hoster an ([Website nicht erreichbar](../../../hosting/runbooks/website-nicht-erreichbar.md)) |
| Eine Seite, die Sie nicht geändert haben, sieht verändert aus | Prüfen Sie ihre Versionen; das Prüfprotokoll zeigt, wer was getan hat ([Das Prüfprotokoll](../../../../../eng-us/features/6.0/audit-trail.md)) |

Weitere erste Schritte stehen unter [Wenn etwas schiefgeht](../../../wenn-etwas-schiefgeht.md).

> **Tipp**
> Zehn Minuten genügen, wenn die Liste jeden Tag erledigt wird. Dauert es länger, wartet etwas darauf zu lange: Finden
> Sie heraus, was, und beheben Sie die Ursache.

## Verwandte Seiten

- [Wöchentliche Checkliste](woechentlich.md)
- [Tägliche Shop-Routine](../../handel/taegliche-shop-routine.md)
- [Wenn etwas schiefgeht](../../../wenn-etwas-schiefgeht.md)
- [Checklisten für Betreiber](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
