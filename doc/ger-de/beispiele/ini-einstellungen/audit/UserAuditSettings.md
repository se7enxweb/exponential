---
title: audit.ini [UserAuditSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UserAuditSettings] in audit.ini: UserAudit, Events, RecordIP, RetentionDays, MaxValueLength"
keywords: Einstellung, audit.ini, UserAuditSettings, INI, setting, UserAudit, Events, RecordIP, RetentionDays, MaxValueLength
type: generated
generated: exp:help:examples
---
# audit.ini [UserAuditSettings]

Der Block `[UserAuditSettings]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## UserAudit

> The user audit log (doc/eng-us/features/6.0/user-audit.md): one row per security event about a  
> user account or a user group in the table expuser\_audit, a copy of the access channel's records  
> made by the sink useraudit (\[AuditChannel\_access\] Sinks\[\]). Read on Users > User audit log  
> (user/audit, policy user/audit), filtered and exported as CSV.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[UserAuditSettings]
UserAudit=enabled
*/ ?>
```

## Events

> Which events: patterns as in \[AuditEventSettings\]; only records naming a user account (the  
> account changed, signed in or refused, the user a role was assigned to) or a user group are kept.  
> An event the audit does not record (\[AuditEventSettings\]) never reaches the table either.  

Standard: eine Liste mit 7: `access.user.*`, `access.role.assign`, `access.role.unassign`, `access.session.*`, `access.passkey.*`, `access.registration.*`, `access.permission.refused`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[UserAuditSettings]
# ein leeres "Events[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Events[]
Events[]=access.user.*
Events[]=access.role.assign
Events[]=access.role.unassign
Events[]=access.session.*
Events[]=access.passkey.*
Events[]=access.registration.*
Events[]=access.permission.refused
*/ ?>
```

## RecordIP

> The address of the request, as \[AuditPrivacySettings\] Field\[actor.ip\] leaves it (by default  
> shortened to /24 or /48); disabled: never stored in the table.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[UserAuditSettings]
RecordIP=enabled
*/ ?>
```

## RetentionDays

> Rows older than this are removed by the cronjob part useraudit (cronjobs/useraudit.php, in the  
> infrequent group). 0 keeps them all.  

Standard: `730`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[UserAuditSettings]
RetentionDays=730
*/ ?>
```

## MaxValueLength

> The longest before or after value stored, in bytes of JSON; a longer one is cut.  

Standard: `2000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[UserAuditSettings]
MaxValueLength=2000
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
