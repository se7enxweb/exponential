---
title: audit.ini [AlertRule_brute_force_user]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AlertRule_brute_force_user] in audit.ini: Class, Event, Threshold, Window, GroupBy, Severity, Sinks"
keywords: Einstellung, audit.ini, AlertRule_brute_force_user, INI, setting, Class, Event, Threshold, Window, GroupBy, Severity, Sinks
type: generated
generated: exp:help:examples
---
# audit.ini [AlertRule_brute_force_user]

Der Block `[AlertRule_brute_force_user]` von `settings/audit.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Many failed logins for one account, from anywhere.  

Standard: `threshold`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force_user]
Class=threshold
*/ ?>
```

## Event

Standard: `access.session.login.failed`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force_user]
Event=access.session.login.failed
*/ ?>
```

## Threshold

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force_user]
Threshold=10
*/ ?>
```

## Window

Standard: `900`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force_user]
Window=900
*/ ?>
```

## GroupBy

Standard: `object.id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force_user]
GroupBy=object.id
*/ ?>
```

## Severity

Standard: `alert`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force_user]
Severity=alert
*/ ?>
```

## Sinks

Standard: eine Liste mit 2: `syslog`, `mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_brute_force_user]
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
