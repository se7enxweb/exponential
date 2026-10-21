---
title: audit.ini [AlertRule_settings_out_of_hours]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AlertRule_settings_out_of_hours] in audit.ini: Class, Event, OutOfHours, Severity, Sinks"
keywords: Einstellung, audit.ini, AlertRule_settings_out_of_hours, INI, setting, Class, Event, OutOfHours, Severity, Sinks
type: generated
generated: exp:help:examples
---
# audit.ini [AlertRule_settings_out_of_hours]

Der Block `[AlertRule_settings_out_of_hours]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `schedule`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_settings_out_of_hours]
Class=schedule
*/ ?>
```

## Event

Standard: `system.setting.write`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_settings_out_of_hours]
Event=system.setting.write
*/ ?>
```

## OutOfHours

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_settings_out_of_hours]
OutOfHours=enabled
*/ ?>
```

## Severity

Standard: `warning`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_settings_out_of_hours]
Severity=warning
*/ ?>
```

## Sinks

Standard: eine Liste mit 1: `syslog`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_settings_out_of_hours]
# ein leeres "Sinks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Sinks[]
Sinks[]=syslog
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
