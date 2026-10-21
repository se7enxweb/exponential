---
title: audit.ini [AlertRule_audit_disabled]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AlertRule_audit_disabled] in audit.ini: Class, Event, Severity, Sinks"
keywords: Einstellung, audit.ini, AlertRule_audit_disabled, INI, setting, Class, Event, Severity, Sinks
type: generated
generated: exp:help:examples
---
# audit.ini [AlertRule_audit_disabled]

Der Block `[AlertRule_audit_disabled]` von `settings/audit.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `match`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_audit_disabled]
Class=match
*/ ?>
```

## Event

Standard: `system.audit.disable`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_audit_disabled]
Event=system.audit.disable
*/ ?>
```

## Severity

Standard: `emergency`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_audit_disabled]
Severity=emergency
*/ ?>
```

## Sinks

Standard: eine Liste mit 2: `syslog`, `mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AlertRule_audit_disabled]
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
