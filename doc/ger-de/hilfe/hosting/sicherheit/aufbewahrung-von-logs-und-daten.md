---
title: Aufbewahrung von Logs und Daten
audience: hosting, admin
category: hosting
order: 140
summary: Wie lange eine Exponential-Website jedes Log und jede Art von Aufzeichnung standardmäßig behält, welche Einstellung das ändert und welcher Cronjob oder Befehl es durchsetzt - Logs des Kernels, Anfrage- und CSP-Logs, die Logs von Velocity, das Prüfprotokoll und sein Index, das Einwilligungsprotokoll und die Unterdrückungsliste für Mails, Papierkorb, Inhaltsjobs und Sitzungen. Mit den Teilen, für die ein Hoster zuständig ist - Backups und Server-Logs.
keywords: Aufbewahrung, Aufbewahrung von Logs, Aufbewahrung von Daten, Rotation, Bereinigung, LogFileRotateCount, LogFileMaxAge, RotationSettings, MaxAgeDays, Audit LiveDays, ArchiveDays, KeepDays, PseudonymiseAfterDays, RetentionDays, Einwilligungsprotokoll, Unterdrückungsliste, Papierkorb KeepItemsForDays, Inhaltsjobs, Sitzungen, Backups, DSGVO, Speicherbegrenzung, retention, log retention, data retention, rotation, prune, consent log, suppression list, content jobs, sessions, backups, GDPR, storage limitation
type: help
---
# Aufbewahrung von Logs und Daten

Diese Seite ist für die Systemadministratoren und Compliance-Verantwortlichen eines Hosters. Sie nennt für jedes Log und jede
Aufzeichnung, die eine Exponential-Website führt, die Standardaufbewahrung, die Einstellung, die sie ändert, und was sie durchsetzt.
Nutzen Sie sie, um den Abschnitt zur Aufbewahrung einer Tarifbeschreibung zu schreiben oder einem Kunden zu antworten, dessen
Datenschutzerklärung einen Zeitraum nennt. Einstellungen werden in Überschreibungen geändert (`settings/override/<file>.ini.append.php`);
leeren Sie danach den INI-Cache.

> **Hinweis**
> Welche Zeiträume richtig sind, ist eine rechtliche und geschäftliche Entscheidung von Hoster und Kunde, nicht der Software. Diese Seite
> ist keine Rechtsberatung ([Compliance und DSGVO](compliance-und-dsgvo.md)).

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [Logs des Kernels](#logs-des-kernels)
3. [Anfrage-, Sicherheits- und CSP-Logs](#anfrage--sicherheits--und-csp-logs)
4. [Die Logs von Velocity](#die-logs-von-velocity)
5. [Das Prüfprotokoll](#das-prüfprotokoll)
6. [Einwilligung und Unterdrückung bei Mails](#einwilligung-und-unterdrückung-bei-mails)
7. [Inhalte und Sitzungen](#inhalte-und-sitzungen)
8. [Wofür der Hoster zuständig ist](#wofür-der-hoster-zuständig-ist)
9. [Checkliste](#checkliste)

## Auf einen Blick

| Daten | Standardaufbewahrung | Einstellung | Durchgesetzt von |
|---|---|---|---|
| `error.log`, `warning.log`, `notice.log`, `debug.log`, `storage.log` ... | 3 Archive, bei 2 MB rotiert; keine Altersgrenze | `site.ini [FileSettings] LogFileRotateCount`, `LogFileMaxSize`, `LogFileMaxAge` (`0`) | dem Schreiber; `exp:logs --prune`; Cronjob-Teil `logs` |
| `404.log` | 5 Archive, 10 MB, nach 30 Tagen bereinigt | `logviewer.ini [RotationSettings]` Schlüssel `404` | Cronjob-Teil `logs`, `exp:logs` |
| `security.log` | 5 Archive, 10 MB, nach 90 Tagen bereinigt | Schlüssel `security` | dasselbe |
| `csp.log` | 5 Archive, 10 MB, nach 30 Tagen bereinigt | Schlüssel `csp` | dasselbe |
| Zugriffs- und Fehlerlogs von Velocity | nach Datum rotiert | `velocity.ini [LogSettings]` | der Engine |
| Audit, Kanäle `content`, `access`, `system`, `commerce` | 90 Tage aktuell, 730 Tage archiviert | `audit.ini [AuditChannel_<channel>] LiveDays`, `ArchiveDays` | Cronjob-Teil `audit` (täglich) |
| Audit, Kanal `read` (standardmäßig aus) | 30 Tage aktuell, 90 archiviert | `[AuditChannel_read]` | dasselbe |
| Audit-Index | 730 Tage; nach 90 pseudonymisiert | `[AuditIndexSettings] KeepDays`; `[AuditPrivacySettings] PseudonymiseAfterDays` | dasselbe |
| Einwilligungsprotokoll für Mails von Personen, die nicht mehr da sind | 1095 Tage | `mailpreferences.ini [ConsentSettings] RetentionDays` | Cronjob-Teil `mailpreferences` |
| Ausstehende Bestätigungen von Mails | 7 Tage | `[ConsentSettings] PendingDays` | dasselbe |
| Unterdrückungsliste | behalten (`0`) | `[SuppressionSettings] RetentionDays` | dasselbe, wenn über 0 |
| Papierkorb | vollständig geleert, wenn der Cronjob läuft (leerer Wert) | `content.ini [TrashSettings] KeepItemsForDays` | `cronjobs/trashpurge.php`, wenn geplant |
| Abgeschlossene Inhaltsjobs | 30 Tage | `content.ini [ContentJobSettings] KeepDays` | Cronjob-Teil `contentjobs` |
| Sitzungen | `SessionTimeout` 259200 s (3 Tage) | `site.ini [Session]` | die Sitzungsbereinigung von PHP; `bin/php/ezsessiongc.php` auf Anforderung |

## Logs des Kernels

Wenn ein Log `LogFileMaxSize` (2 MB) überschreitet, rotiert der nächste Schreibvorgang es, und die Archive über `LogFileRotateCount` (3)
hinaus werden entfernt. `LogFileMaxAge` (`0`, keine Grenze) fügt eine Altersgrenze hinzu, die eine Bereinigung anwendet;
`LogFileCompress=enabled` komprimiert Archive ab `.2` ([Log-Rotation](../../../../eng-us/features/6.0/log-rotation.md)). Pro Log, nach seinem
Schlüssel:

```ini
# settings/override/logviewer.ini.append.php
[RotationSettings]
Keep[error]=10
MaxAgeDays[error]=90
Compress[error]=enabled
```

Auf Anforderung oder nächtlich als Benutzer der Website:

```bash
./console exp:logs --prune --dry-run
./console exp:logs --prune
# crontab: 15 3 * * * cd /var/www/vhosts/<domain>/<installation> && php runcronjobs.php -q logs
```

Der Cronjob-Teil `logs` ist in keiner Standardgruppe, er läuft also nur, wenn er geplant ist. Wenn stattdessen `logrotate` des Systems
`var/log` verwaltet, setzen Sie `LogFileRotateCount=0`, damit der Kernel nicht zusätzlich rotiert. Jede Änderung durch den Teil wird im
Prüfprotokoll festgehalten.

## Anfrage-, Sicherheits- und CSP-Logs

Diese werden mit eigenen Grenzen in `logviewer.ini [RotationSettings]` ausgeliefert (Schlüssel `404`, `security`, `csp`: `MaxSize`
10485760, `Keep` 5, `Compress` enabled, `MaxAgeDays` 30, 90 und 30). Um `security.log` länger zu behalten
([Anfrage-Logs](../../../../eng-us/features/6.0/request-logs.md#the-log-viewer-rotation-and-the-error-digest)):

```ini
# settings/override/logviewer.ini.append.php
[RotationSettings]
Keep[security]=10
MaxAgeDays[security]=365
```

## Die Logs von Velocity

Velocity schreibt `<siteaccess>-access.log` und `<siteaccess>-error.log` unter `var/vc/qbix/log/` (`[LogSettings] Dir`), im Format
combined von Apache, mit den Modi `0640` und `0750`. Die Rotation setzt das Datum vor die Endung (`site-access.2026-09-24.log`)
([8.3.12 Logs](../../../installation/08-die-website-ausliefern.md#8312-logs)). Das Repository dokumentiert für diese datierten Dateien keine
Altersgrenze; entfernen Sie alte mit Ihrem eigenen geplanten Job, nach dem Zeitraum, den Ihr Tarif nennt.

## Das Prüfprotokoll

Pro Kanal in `[AuditChannel_<channel>]`: `LiveDays` (unkomprimiert) und `ArchiveDays` (als Archive behalten). Die mitgelieferten Blöcke
der Kanäle setzen beide Werte; setzen Sie sie also pro Kanal, nicht in `[AuditRotationSettings]`
([4.3 Aufbewahrung](../../../../eng-us/bc/6.0/audit.md#43-retention)):

```ini
# settings/override/audit.ini.append.php: one year of archives instead of two
[AuditChannel_content]
ArchiveDays=365

[AuditChannel_access]
ArchiveDays=365

[AuditChannel_system]
ArchiveDays=365

[AuditChannel_commerce]
ArchiveDays=365
```

- Das Entfernen geschieht automatisch, täglich, und jedes Entfernen wird selbst festgehalten.
- Die Aufbewahrung entfernt nie einen Signaturschlüssel: `exp:audit purge` nennt die Schlüssel, die aufbewahrte Archive noch brauchen.
  Behalten Sie jeden `SigningKey[]` so lange wie die Archive, die er signiert hat.
- Lange Aufbewahrung (zum Beispiel zehn Jahre auf einmal beschreibbarem Speicher) ist die
  [Konfiguration für Compliance](../../../../eng-us/bc/6.0/audit.md#66-compliance-long-retention); minimale Aufbewahrung mit gehashten Feldern ist
  die [Konfiguration für strengen Datenschutz](../../../../eng-us/bc/6.0/audit.md#64-strict-privacy).
- Einträge, die eine Senke weiterleitet, behält das empfangende System nach eigenen Regeln.

## Einwilligung und Unterdrückung bei Mails

Der Cronjob-Teil `mailpreferences` (Gruppe `infrequent`) entfernt die Zeilen des Einwilligungsprotokolls von Personen, die nicht mehr da
sind, sobald sie älter sind als `RetentionDays` (1095 Tage: die Lebensdauer des Kontos plus drei Jahre), entfernt Bestätigungen, die länger
als `PendingDays` (7) gewartet haben, und, wenn `[SuppressionSettings] RetentionDays` über 0 liegt, alte Einträge der Unterdrückungsliste
([9. Aufbewahrung und der Cronjob](../../../anleitungen/mail-einstellungen-administrator.md#9-aufbewahrung-und-der-cronjob)).

```bash
./console exp:mail:consent cleanup --dry-run
php runcronjobs.php mailpreferences
```

Denken Sie daran, dass das Entfernen eines Eintrags der Unterdrückungsliste optionale Mail wieder an diese Adresse lässt, wenn die
Person sie vorher eingeschaltet hatte.

## Inhalte und Sitzungen

- **Papierkorb**: `[TrashSettings] KeepItemsForDays` leer oder `0` bedeutet, dass der Cronjob `trashpurge` (`cronjobs/trashpurge.php`) wie
  bisher den ganzen Papierkorb leert; eine Zahl behält Elemente so viele Tage (bis zu 36500). `bin/php/trashpurge.php` nimmt
  `--trashed-days=<days>`.
- **Inhaltsjobs**: Abgeschlossene Jobs werden nach `[ContentJobSettings] KeepDays=30` entfernt (`0` behält sie).
- **Sitzungen**: `SessionTimeout` (259200 Sekunden) setzt `session.gc_maxlifetime` beim eigenen Sitzungs-Handler von PHP;
  `bin/php/ezsessiongc.php` entfernt auf Anforderung abgelaufene Sitzungen nach `SessionTimeout`.
- **Versionen und Entwürfe** von Inhalten folgen den Inhaltseinstellungen der Website; sie sind Inhalte, und ihre Aufbewahrung ist die
  redaktionelle Entscheidung des Kunden.

## Wofür der Hoster zuständig ist

- **Backups** enthalten alles oben, einschließlich Logs und personenbezogener Daten, so lange das Backup aufbewahrt wird. Entscheiden Sie
  die Aufbewahrung der Backups zusammen mit diesen Zeiträumen ([Backup-Zeitpläne und Aufbewahrung](../betrieb/backup-zeitplaene-und-aufbewahrung.md)).
- **Logs von Webserver, PHP-FPM, Panel und Mailserver** liegen außerhalb von Exponential; ihre Aufbewahrung ist die Konfiguration des
  Hosters.
- **Zentrale Log-Plattformen und SIEMs**, die Senken des Audits oder Kopien von Logs empfangen, behalten sie nach eigenen Regeln.
- **Exportierte Dateien** (Audit-Exporte, CSVs der Einwilligungen, Exporte für betroffene Personen in `var/tmp/`) sollten gelöscht werden,
  wenn die Anfrage, die sie beantworten, erledigt ist.

## Checkliste

- [ ] Die Aufbewahrung jeder Zeile der Tabelle oben entschieden und in die Tarifbeschreibung geschrieben.
- [ ] Die Cronjob-Gruppen `frequent` und `infrequent` laufen; der Teil `logs` geplant, wenn Altersgrenzen gewünscht sind.
- [ ] `LogFileMaxAge` oder `MaxAgeDays` pro Log gesetzt, wo der Tarif einen Zeitraum für Logs des Kernels nennt.
- [ ] Die datierten Logdateien von Velocity werden durch einen geplanten Job nach dem genannten Zeitraum entfernt.
- [ ] `ArchiveDays` des Audits pro Kanal mit dem Kunden vereinbart; Signaturschlüssel so lange wie die Archive behalten.
- [ ] Aufbewahrung der Backups mit den Zeiträumen für Logs und Daten abgestimmt.
- [ ] Exportierte personenbezogene Daten nach der Nutzung gelöscht.

## Verwandte Seiten

- [Sicherheit für Hoster: Überblick](README.md)
- [Compliance und DSGVO](compliance-und-dsgvo.md)
- [Log-Viewer und Log-Rotation](../betrieb/log-viewer-und-log-rotation.md)
- [Prüfprotokoll](pruefprotokoll.md)
- [Log-Rotation und Bereinigung](../../../../eng-us/features/6.0/log-rotation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
