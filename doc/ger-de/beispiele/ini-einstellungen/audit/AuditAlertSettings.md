---
title: audit.ini [AuditAlertSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditAlertSettings] in audit.ini: Alerts, EvaluateIn, Rules, RuleClasses, BusinessDays, BusinessHours, Recipients"
keywords: Einstellung, audit.ini, AuditAlertSettings, INI, setting, Alerts, EvaluateIn, Rules, RuleClasses, BusinessDays, BusinessHours, Recipients
type: generated
generated: exp:help:examples
---
# audit.ini [AuditAlertSettings]

Der Block `[AuditAlertSettings]` von `settings/audit.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Alerts

> F5: eingebaute Regeln, INI-Regeln (\[AlertRule\_\*\]) und Regelklassen.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
Alerts=enabled
*/ ?>
```

## EvaluateIn

> Wo Regeln ausgewertet werden: flush (am Ende der Anfrage, die das Ereignis geschrieben hat) und  
> cronjob (der Cronjob-Teil audit, für Zeitfenster und Regeln über viele Anfragen).  

Standard: eine Liste mit 2: `flush`, `cronjob`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
# ein leeres "EvaluateIn[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
EvaluateIn[]
EvaluateIn[]=flush
EvaluateIn[]=cronjob
*/ ?>
```

## Rules

> Die verwendeten Regeln: Blöcke \[AlertRule\_<name>\].  

Standard: eine Liste mit 7: `brute_force`, `brute_force_user`, `admin_role_granted`, `settings_out_of_hours`, `mass_delete`, `audit_disabled`, `chain_broken`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
# ein leeres "Rules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Rules[]
Rules[]=brute_force
Rules[]=brute_force_user
Rules[]=admin_role_granted
Rules[]=settings_out_of_hours
Rules[]=mass_delete
Rules[]=audit_disabled
Rules[]=chain_broken
*/ ?>
```

## RuleClasses

> Regelklassen: Name => Klasse, die expAuditAlertRule implementiert (RAD-Survey-Registry  
> "auditalertrules").  
> Eingebaut.  

Standard: ein Hash mit 3: `threshold=expAuditThresholdRule`, `match=expAuditMatchRule`, `schedule=expAuditScheduleRule`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
# ein leeres "RuleClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RuleClasses[]
RuleClasses[threshold]=expAuditThresholdRule
RuleClasses[match]=expAuditMatchRule
RuleClasses[schedule]=expAuditScheduleRule
*/ ?>
```

## BusinessDays

> Geschäftszeiten für Regeln mit OutOfHours=enabled: Tage 1 (Mo) … 7 (So), Stunden 0-24,  
> in der Zeitzone der Website.  

Standard: `1-5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
BusinessDays=1-5
*/ ?>
```

## BusinessHours

Standard: `7-19`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
BusinessHours=7-19
*/ ?>
```

## Recipients

> Wer Alarm-Mails erhält (Regeln mit Sinks\[\]=mail und jeder andere per Mail versandte Eintrag): die  
> Standardliste. Die eigene Recipients\[\] einer Regel in \[AlertRule\_<rule>\] ersetzt sie für diese Regel.  
> Leer (und \[AuditSink\_mail\] Receivers\[\] ebenfalls leer) = die AdminEmail der Website.  
> Ein Empfänger pro Zeile, kombinierbar; Adressen werden entdoppelt (ohne Beachtung der Groß-/Kleinschreibung),  
> Benutzer werden beim Versand mit ihrer aktuellen E-Mail-Adresse aufgelöst, deaktivierte  
> Benutzer werden ausgelassen, und nichts aus dem Ereignis selbst wird je zum Empfänger:  
>   admin                    site.ini \[MailSettings\] AdminEmail  
>   address:ops@example.com  eine E-Mail-Adresse (eine bloße Adresse mit @ funktioniert auch)  
>   group:security           die benannte Liste \[AlertRecipients\_security\] unten  
>   user:14                  der Benutzer mit der Inhaltsobjekt-ID 14  
>   login:editor1            der Benutzer mit diesem Login  
>   usergroup:12             jeder Benutzer unter der Benutzergruppe mit der Knoten-ID 12,  
>                            Untergruppen eingeschlossen (usergroup:<remote id> funktioniert auch,  
>                            die Remote-ID des Knotens oder des Objekts)  
>   role:Administrator       jeder Benutzer, dem die Rolle zugewiesen ist, direkt oder über  
>                            eine Benutzergruppe (role:<role id> funktioniert auch)  
> Mails werden pro Regel (oder Ereignisname) und Empfänger gedrosselt (\[AuditSink\_mail\] Throttle).  
> Zeigen, wer was erhalten würde: ./console exp:audit alerts recipients \[--rule=<rule>\]  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
# ein leeres "Recipients[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Recipients[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
