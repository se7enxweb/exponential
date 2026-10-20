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

> F5: built-in rules, INI rules (\[AlertRule\_\*\]) and rule classes.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditAlertSettings]
Alerts=enabled
*/ ?>
```

## EvaluateIn

> Where rules are evaluated: flush (at the end of the request that wrote the event) and  
> cronjob (the audit cronjob part, for windows and rules over many requests).  

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

> The rules in use: \[AlertRule\_<name>\] blocks.  

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

> Rule classes: name => class implementing expAuditAlertRule (RAD survey registry  
> "auditalertrules").  
> Built in.  

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

> Business hours for rules with OutOfHours=enabled: days 1 (Mon) … 7 (Sun), hours 0-24,  
> in the site's time zone.  

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

> Who gets alert mail (rules with Sinks\[\]=mail, and any other record mailed): the  
> default list. A rule's own \[AlertRule\_<rule>\] Recipients\[\] replaces it for that rule.  
> Empty (and \[AuditSink\_mail\] Receivers\[\] empty too) = the site's AdminEmail.  
> One recipient per line, combinable; addresses are deduplicated (case-insensitive),  
> users are resolved when the mail is sent with their current e-mail address, disabled  
> users are left out, and nothing in the event itself ever becomes a recipient:  
>   admin                    site.ini \[MailSettings\] AdminEmail  
>   address:ops@example.com  an e-mail address (a bare address with @ works too)  
>   group:security           the named list \[AlertRecipients\_security\] below  
>   user:14                  the user with content object id 14  
>   login:editor1            the user with that login  
>   usergroup:12             every user below the user group with node id 12,  
>                            sub-groups included (usergroup:<remote id> works too,  
>                            the node's or the object's remote id)  
>   role:Administrator       every user the role is assigned to, directly or through  
>                            a user group (role:<role id> works too)  
> Mail is throttled per rule (or event name) and recipient (\[AuditSink\_mail\] Throttle).  
> Show who would get what: ./console exp:audit alerts recipients \[--rule=<rule>\]  

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
