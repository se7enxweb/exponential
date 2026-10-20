---
title: audit.ini [AlertRule_admin_role_granted]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AlertRule_admin_role_granted] in audit.ini: Class, Event, Policies, Severity, Sinks"
keywords: Einstellung, audit.ini, AlertRule_admin_role_granted, INI, setting, Class, Event, Policies, Severity, Sinks
type: generated
generated: exp:help:examples
---
# audit.ini [AlertRule_admin_role_granted]

Der Block `[AlertRule_admin_role_granted]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> A role holding these policies is assigned to anyone.  

Standard: `match`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_admin_role_granted]
Class=match
*/ ?>
```

## Event

Standard: `access.role.assign`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_admin_role_granted]
Event=access.role.assign
*/ ?>
```

## Policies

> Match when the role grants any of these module/function pairs.  

Standard: eine Liste mit 4: `*/*`, `setup/*`, `role/*`, `audit/manage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_admin_role_granted]
# ein leeres "Policies[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Policies[]
Policies[]=*/*
Policies[]=setup/*
Policies[]=role/*
Policies[]=audit/manage
*/ ?>
```

## Severity

Standard: `critical`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_admin_role_granted]
Severity=critical
*/ ?>
```

## Sinks

Standard: eine Liste mit 2: `syslog`, `mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_admin_role_granted]
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
