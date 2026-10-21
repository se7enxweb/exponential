---
title: Audit-Alarme und ihre Empfänger
audience: government, admin
category: public-sector
order: 80
summary: Für Administratorinnen und Administratoren öffentlicher Websites - die sieben Alarmregeln, die Exponential mitliefert (Brute-Force, Vergabe einer Administratorrolle, Einstellungen außerhalb der Dienstzeit geändert, Massenlöschung, Audit abgeschaltet, unterbrochene Kette), wer die E-Mail erhält, wie Sie Alarme an ein Sicherheitsteam leiten und wie Sie eine Regel testen.
keywords: Audit-Alarme, Alarmregeln, Empfänger, E-Mail, Sicherheitsteam, Brute-Force, Massenlöschung, audit alerts, alert rules, brute_force, brute_force_user, admin_role_granted, settings_out_of_hours, mass_delete, audit_disabled, chain_broken, recipients, mail, syslog, security team, exp:audit alerts
type: help
---
# Audit-Alarme und ihre Empfänger

Diese Seite richtet sich an Administratorinnen und Administratoren, die dafür sorgen, dass ein ernstes Ereignis auf
einer öffentlichen Website einen Menschen erreicht. Das Audit-Protokoll wertet Alarmregeln über seine Einträge aus;
jede Auslösung wird als `system.audit.alert` aufgezeichnet und an Syslog und per E-Mail gesendet
([4.5 Alarme und ihre Empfänger](../../../bc/6.0/audit.md#45-alarme-und-ihre-empfänger)).

## Inhalt

1. [Die mitgelieferten Regeln](#die-mitgelieferten-regeln)
2. [Wer die E-Mail erhält](#wer-die-e-mail-erhält)
3. [Alarme an Ihr Sicherheitsteam leiten](#alarme-an-ihr-sicherheitsteam-leiten)
4. [Testen und feinabstimmen](#testen-und-feinabstimmen)
5. [Verwandte Seiten](#verwandte-seiten)

## Die mitgelieferten Regeln

Aus `settings/audit.ini [AuditAlertSettings] Rules[]`:

| Regel | Löst aus, wenn | Schweregrad | Gesendet an |
|---|---|---|---|
| `brute_force` | 20 fehlgeschlagene Anmeldungen aus einem Netz innerhalb von 5 Minuten | critical | Syslog, E-Mail |
| `brute_force_user` | 10 fehlgeschlagene Anmeldungen für ein Konto innerhalb von 15 Minuten | alert | Syslog, E-Mail |
| `admin_role_granted` | eine Rolle mit `*/*`, `setup/*`, `role/*` oder `audit/manage` zugewiesen wird | critical | Syslog, E-Mail |
| `settings_out_of_hours` | eine Einstellung außerhalb der Dienstzeit geschrieben wird | warning | Syslog |
| `mass_delete` | ein Benutzer innerhalb von 10 Minuten 500 Knoten entfernt (Unterknoten mitgezählt) | critical | Syslog, E-Mail |
| `audit_disabled` | das Audit abgeschaltet wird | emergency | Syslog, E-Mail |
| `chain_broken` | die Prüfung eine unterbrochene Kette findet | alert | Syslog, E-Mail |

Alarme wertet der Cronjob-Teil aus (die Gruppe `frequent`): Läuft er nicht, löst kein Alarm aus.

## Wer die E-Mail erhält

Die erste dieser Angaben, die gesetzt ist:

1. das eigene `[AlertRule_<rule>] Recipients[]` der Regel;
2. `[AuditAlertSettings] Recipients[]`;
3. `[AuditSink_mail] Receivers[]` (älterer Name);
4. `admin`, also `site.ini [MailSettings] AdminEmail`.

Empfänger können `admin` sein, eine Adresse, `group:<name>` (eine Liste in `[AlertRecipients_<name>]`), `user:<id>`,
`login:<login>`, `usergroup:<id>` oder `role:<name or id>`. Deaktivierte Benutzer und ungültige Adressen werden
ausgelassen. E-Mails verschickt nur der Cronjob-Teil, eine je Empfänger, höchstens eine je Regel und Empfänger
innerhalb von 900 Sekunden.

## Alarme an Ihr Sicherheitsteam leiten

In `settings/override/audit.ini.append.php`:

```ini
[AuditAlertSettings]
Recipients[]
Recipients[]=group:security

[AlertRecipients_security]
Addresses[]
Addresses[]=security-team@example.gov
Recipients[]
Recipients[]=role:Administrator
```

Verwenden Sie ein Teampostfach statt der Adresse einer einzelnen Person, damit Alarme auch bei Abwesenheiten gesehen
werden. Prüfen Sie anschließend:

```bash
./console exp:audit alerts recipients --allow-root-user
./console exp:audit alerts recipients --rule=admin_role_granted --allow-root-user
```

Der Befehl listet jede Adresse mit den Einträgen auf, aus denen sie hervorgegangen ist, und jedes Problem (eine
unbekannte Gruppe, ein deaktivierter Benutzer).

## Testen und feinabstimmen

- Spielen Sie eine Regel vor einer Änderung gegen einen vergangenen Tag ab:
  `./console exp:audit alerts test mass_delete --replay=<YYYY-MM-DD> --allow-root-user`.
- Die Dienstzeiten für `settings_out_of_hours` sind ab Werk `[AuditAlertSettings] BusinessDays=1-5` und
  `BusinessHours=7-19` (in der Zeitzone der Website); passen Sie sie an Ihre Dienststelle an.
- Ausgelöste Alarme sehen Sie im Reiter Audit unter **Alarme** (`/audit/alerts`).
- Halten Sie für jede Regel fest, wer handelt und wie (siehe „Reaktion auf Sicherheitsvorfälle“ in diesem Bereich).

## Verwandte Seiten

- [Audit-Leitfaden, 4.5](../../../bc/6.0/audit.md#45-alarme-und-ihre-empfänger)
- [Das Audit-Protokoll für öffentliche Stellen](audit-protokoll-fuer-oeffentliche-stellen.md)
- [13.14 Das Audit-Protokoll](../../../installation/13-sicherheitshaertung.md#1314-das-audit-protokoll)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
