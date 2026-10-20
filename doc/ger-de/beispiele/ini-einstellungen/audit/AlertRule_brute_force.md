---
title: audit.ini [AlertRule_brute_force]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AlertRule_brute_force] in audit.ini: Class, Event, Threshold, Window, GroupBy, Severity, Sinks"
keywords: Einstellung, audit.ini, AlertRule_brute_force, INI, setting, Class, Event, Threshold, Window, GroupBy, Severity, Sinks
type: generated
generated: exp:help:examples
---
# audit.ini [AlertRule_brute_force]

Der Block `[AlertRule_brute_force]` von `settings/audit.ini`, mit 7 Einstellung(en).

> Recipients\[\]=admin  
> Recipients\[\]=group:security  
> Recipients\[\]=role:Administrator  
> Named recipient groups: \[AlertRecipients\_<name>\], referenced as group:<name> from  
> Recipients\[\] here or in a rule. Addresses\[\] lists e-mail addresses; Recipients\[\] may  
> name any of the kinds above (other groups too; loops are cut).  
> \[AlertRecipients\_security\]  
> Addresses\[\]  
> Addresses\[\]=security@example.com  
> Addresses\[\]=oncall@example.com  
> Recipients\[\]  
> Recipients\[\]=usergroup:12  
> Recipients\[\]=login:editor1  
> An INI rule: Event (pattern), Threshold (events), Window (seconds), GroupBy (record  
> field), Severity, Sinks\[\], Class (default threshold), Recipients\[\] (who is mailed when  
> Sinks\[\] has mail; empty = \[AuditAlertSettings\] Recipients\[\]). Each fires  
> system.audit.alert. Example:  
>   \[AlertRule\_brute\_force\]  
>   Recipients\[\]  
>   Recipients\[\]=group:security  
>   Recipients\[\]=address:soc@example.com  

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Many failed logins from one network.  

Standard: `threshold`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force]
Class=threshold
*/ ?>
```

## Event

Standard: `access.session.login.failed`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force]
Event=access.session.login.failed
*/ ?>
```

## Threshold

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force]
Threshold=20
*/ ?>
```

## Window

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force]
Window=300
*/ ?>
```

## GroupBy

Standard: `actor.ip`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force]
GroupBy=actor.ip
*/ ?>
```

## Severity

Standard: `critical`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force]
Severity=critical
*/ ?>
```

## Sinks

Standard: eine Liste mit 2: `syslog`, `mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force]
# ein leeres "Sinks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Sinks[]
Sinks[]=syslog
Sinks[]=mail
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
