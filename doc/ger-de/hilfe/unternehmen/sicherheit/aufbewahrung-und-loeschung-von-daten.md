---
title: Aufbewahrung und Löschung von Daten
audience: businesses, admin, project-manager
category: business-solutions
order: 80
summary: Wie lange eine Exponential-Website Logs, Audit-Datensätze, Einwilligungsnachweise, Papierkorb, Sessions und Formulardaten standardmäßig aufbewahrt, welche Einstellungen das ändern, wie Sie Daten löschen, wenn eine Person darum bittet, und was bei Ihnen und Ihrem Hoster bleibt (Backups, Exporte). Keine Rechtsberatung.
keywords: Aufbewahrung, Datenaufbewahrung, Löschung, Speicherbegrenzung, Logs, Audit-Trail, Einwilligungsprotokoll, Sperrliste, Papierkorb, Sessions, Formulareingaben, Bestellungen, Backups, Exporte, DSGVO, Recht auf Löschung, retention, data retention, deletion, erasure, storage limitation, logs, audit trail, consent log, suppression list, trash, sessions, form submissions, orders, backups, exports, GDPR, right to erasure
type: help
---
# Aufbewahrung und Löschung von Daten

Diese Seite richtet sich an Inhaber, Datenschutzbeauftragte und Administratoren, die entscheiden müssen, wie lange die
Website personenbezogene Daten aufbewahrt und wie sie diese löschen, wenn die Zeit abgelaufen ist oder eine Person darum
bittet. Sie fasst die Standardwerte zusammen, mit denen die Software geliefert wird, und die Befehle, die löschen, und
verweist auf die vollständige Tabelle für Administratoren.

> **Warnung**
> Grundlagen, keine Rechtsberatung. Welche Fristen richtig sind, ist eine rechtliche und geschäftliche Entscheidung,
> keine der Software. Buchführungs-, Steuer- und Branchenregeln können verlangen, dass Sie manche Datensätze länger
> aufbewahren, als die Datenschutzregeln nahelegen würden. Fragen Sie einen Anwalt, Ihren Steuerberater oder Ihren
> Datenschutzbeauftragten.

## Inhalt

1. [Die Standardwerte auf einen Blick](#die-standardwerte-auf-einen-blick)
2. [Ihre Fristen festlegen](#ihre-fristen-festlegen)
3. [Auf Anfrage löschen](#auf-anfrage-löschen)
4. [Was sich nicht selbst löscht](#was-sich-nicht-selbst-löscht)
5. [Backups](#backups)
6. [Ein Aufbewahrungsplan zum Ausfüllen](#ein-aufbewahrungsplan-zum-ausfüllen)

## Die Standardwerte auf einen Blick

Aus [Aufbewahrung von Logs und Daten](../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md#auf-einen-blick):

| Daten | Standard | Geändert in |
|---|---|---|
| Kernel-Logs (`error.log` und andere) | 3 Archive, rotiert bei 2 MB, keine Altersgrenze | `site.ini [FileSettings]` |
| `404.log` | nach 30 Tagen bereinigt | `logviewer.ini [RotationSettings]` |
| `security.log` | nach 90 Tagen bereinigt | dieselbe |
| `csp.log` | nach 30 Tagen bereinigt | dieselbe |
| Audit-Trail (content, access, system, commerce) | 90 Tage live, 730 Tage archiviert | `audit.ini [AuditChannel_<channel>]` |
| Audit-Index | 730 Tage; personenbezogene Felder nach 90 pseudonymisiert | `[AuditIndexSettings]`, `[AuditPrivacySettings]` |
| Einwilligungsprotokoll für Mails von Menschen, die nicht mehr da sind | 1095 Tage | `mailpreferences.ini [ConsentSettings] RetentionDays` |
| Wartende Bestätigungen für Mails | 7 Tage | `[ConsentSettings] PendingDays` |
| Sperrliste | wird behalten | `[SuppressionSettings] RetentionDays` |
| Papierkorb | wird vollständig geleert, wenn der Papierkorb-Cronjob läuft | `content.ini [TrashSettings] KeepItemsForDays` |
| Abgeschlossene Inhaltsjobs | 30 Tage | `content.ini [ContentJobSettings] KeepDays` |
| Sessions | 3 Tage | `site.ini [Session] SessionTimeout` |

Die meisten davon werden von Cronjobs durchgesetzt. Laufen die Cronjobs nicht, wird nichts entfernt: Sorgen Sie dafür,
dass Ihr Administrator oder Hoster sie einplant ([Cronjobs und Hintergrundjobs](../zuverlaessigkeit/cronjobs-und-hintergrundjobs.md)).

## Ihre Fristen festlegen

1. Beginnen Sie mit Ihrem [Dateninventar](personenbezogene-daten-in-exponential.md#ihr-eigenes-dateninventar-aufbauen).
2. Fragen Sie Ihren Berater für jede Art von Daten, wie lange Sie sie aufbewahren müssen und wie lange Sie dürfen.
3. Schreiben Sie die Frist in Ihren Datenschutzhinweis und Ihren Aufbewahrungsplan.
4. Bitten Sie den Administrator, die passenden Einstellungen in `settings/override` zu setzen; die vollständige Liste
   mit Beispielen steht in [Aufbewahrung von Logs und Daten](../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md).
5. Stimmen Sie die Aufbewahrung der Backups mit Ihrem Hoster ab, damit sie zu denselben Fristen passt.

## Auf Anfrage löschen

| Daten | Wie löschen | Quelle |
|---|---|---|
| E-Mail-Einstellungen und wartende Bestätigungen | `./console exp:mail:preferences erase --email=<address> --yes`; das Einwilligungsprotokoll wird anonymisiert und der Widerruf als Nachweis behalten | [Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md#anfragen-betroffener-personen) |
| Alle Mails stoppen | `exp:mail:suppression add --email=<address> --reason=legal` oder der eigene Abmeldelink der Person | dieselbe |
| Ein Benutzerkonto | das Benutzerobjekt entfernen; das ist endgültig (kein Papierkorb) und löscht seine E-Mail-Einstellungen | [Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md), dieselbe |
| Formulareingaben | **Setup > Gesammelte Informationen**: die Sammlungen eines Formulars oder einzelne Sammlungen entfernen | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |
| Bestellungen | in der Shop-Administration archivieren oder entfernen; prüfen Sie zuerst die Buchführungspflichten | [Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md) |
| Datensätze von Newsletter-Abonnenten | in der Newsletter-Administration (Benutzerliste von `cjw_newsletter`) | [cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md) |
| Audit-Datensätze | werden nicht einzeln gelöscht; siehe unten | [Audit: Aufgaben zu Datenschutz und DSGVO](../../../../eng-us/bc/6.0/audit.md#412-privacy-and-gdpr-tasks) |

> **Hinweis**
> Der Audit-Trail ist eine Hash-Kette: Einen Datensatz zu entfernen, würde den Nachweis brechen, dass niemand das Log
> verändert hat. Der Audit-Leitfaden beschreibt, Datensätze für ihre Aufbewahrungsfrist zu behalten, personenbezogene
> Felder im Index zu pseudonymisieren und eine Löschanfrage mit dieser Frist zu beantworten. Ob das in Ihrem Fall genügt,
> ist eine Frage für Ihren Berater.

Überlegen Sie vor dem Löschen, ob Sie zuerst eine Auskunftsanfrage beantworten: Die Person möchte vielleicht eine Kopie.
Löschen Sie die Exportdateien, wenn die Anfrage erledigt ist; sie enthalten personenbezogene Daten.

## Was sich nicht selbst löscht

- [ ] Exporte, die Sie heruntergeladen haben (CSV, JSON), auf Computern der Mitarbeiter und in `var/tmp/`.
- [ ] Mails, die Formulare in den Posteingang eines Empfängers geschickt haben.
- [ ] Die datierten Logdateien von Velocity: Das Repository dokumentiert keine Altersgrenze, entfernen Sie sie daher mit
      einem geplanten Job ([Aufbewahrung von Logs und Daten](../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md#die-logs-von-velocity)).
- [ ] Logs von Webserver, Mail-Server und Panel außerhalb von Exponential.
- [ ] Inhaltsversionen und Entwürfe: Ihre Aufbewahrung ist eine redaktionelle Entscheidung.
- [ ] Datensätze, die an andere Systeme (ein SIEM, einen Syslog-Server) weitergeleitet werden, nach deren eigenen Regeln.

## Backups

Backups enthalten alles oben Genannte, solange sie aufbewahrt werden. Entscheiden Sie mit Ihrem Hoster und Berater:

- wie lange Backups aufbewahrt werden und wo;
- ob gelöschte Daten aus Backups entfernt werden müssen oder ob sie mit ihnen ablaufen;
- wie eine Wiederherstellung verhindert, dass gelöschte Daten wieder in Gebrauch kommen.

[Zeitpläne und Aufbewahrung von Backups](../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md) behandelt die
technische Seite.

## Ein Aufbewahrungsplan zum Ausfüllen

```text
RETENTION SCHEDULE - [Site]   Version [x], [date]   Owner: [name]
Data                        | Keep for | Reason / rule          | Setting or method          | Who checks
----------------------------|----------|------------------------|----------------------------|-----------
Request and security logs   |          |                        | logviewer.ini MaxAgeDays   |
Kernel logs                 |          |                        | LogFileMaxAge / MaxAgeDays |
Audit trail archives        |          |                        | audit.ini ArchiveDays      |
Mail consent log            |          |                        | RetentionDays              |
Form submissions            |          |                        | removed by hand, monthly   |
Orders                      |          |                        | archived / removed         |
Newsletter subscribers      |          |                        | newsletter admin           |
User accounts (inactive)    |          |                        | Check user accounts        |
Backups                     |          |                        | host contract              |
Exports                     |          |                        | deleted after the request  |
Approved by: __________   Next review: __________
```

## Verwandte Seiten

- [Aufbewahrung von Logs und Daten für Hoster](../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md)
- [DSGVO-Grundlagen](dsgvo-grundlagen.md)
- [Checkliste für das Offboarding von Kunden](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
