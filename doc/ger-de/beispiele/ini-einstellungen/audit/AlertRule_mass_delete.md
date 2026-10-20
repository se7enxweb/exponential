---
title: audit.ini [AlertRule_mass_delete]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AlertRule_mass_delete] in audit.ini: Class, Event, Threshold, Window, GroupBy, CountChildren, Severity, Sinks"
keywords: Einstellung, audit.ini, AlertRule_mass_delete, INI, setting, Class, Event, Threshold, Window, GroupBy, CountChildren, Severity, Sinks
type: generated
generated: exp:help:examples
---
# audit.ini [AlertRule_mass_delete]

Der Block `[AlertRule_mass_delete]` von `settings/audit.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Viele Knoten in kurzer Zeit von einem Benutzer entfernt (Unterknoten werden mitgezählt).  

Standard: `threshold`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_mass_delete]
Class=threshold
*/ ?>
```

## Event

Standard: `content.node.remove.*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_mass_delete]
Event=content.node.remove.*
*/ ?>
```

## Threshold

Standard: `500`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_mass_delete]
Threshold=500
*/ ?>
```

## Window

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_mass_delete]
Window=600
*/ ?>
```

## GroupBy

Standard: `actor.user_id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_mass_delete]
GroupBy=actor.user_id
*/ ?>
```

## CountChildren

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_mass_delete]
CountChildren=enabled
*/ ?>
```

## Severity

Standard: `critical`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_mass_delete]
Severity=critical
*/ ?>
```

## Sinks

Standard: eine Liste mit 2: `syslog`, `mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_mass_delete]
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
