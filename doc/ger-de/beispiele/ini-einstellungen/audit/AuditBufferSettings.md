---
title: audit.ini [AuditBufferSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditBufferSettings] in audit.ini: Buffering, ImmediateEvents, MaxEvents, MaxBytes, FlushInterval"
keywords: Einstellung, audit.ini, AuditBufferSettings, INI, setting, Buffering, ImmediateEvents, MaxEvents, MaxBytes, FlushInterval
type: generated
generated: exp:help:examples
---
# audit.ini [AuditBufferSettings]

Der Block `[AuditBufferSettings]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Buffering

> F3: events are collected and written in one append per channel at the end of the  
> request (also on a fatal error, through the shutdown handler).  enabled | disabled  
> (disabled writes every event at once: for debugging only).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditBufferSettings]
Buffering=enabled
*/ ?>
```

## ImmediateEvents

> Events written at once, not buffered (F3: security events): patterns.  

Standard: eine Liste mit 3: `access.*`, `system.audit.*`, `system.setting.write`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditBufferSettings]
# ein leeres "ImmediateEvents[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ImmediateEvents[]
ImmediateEvents[]=access.*
ImmediateEvents[]=system.audit.*
ImmediateEvents[]=system.setting.write
*/ ?>
```

## MaxEvents

> Flush early when the buffer holds this many events or this many bytes (long requests,  
> commands). K, M suffixes.  

Standard: `500`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditBufferSettings]
MaxEvents=500
*/ ?>
```

## MaxBytes

Standard: `1M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditBufferSettings]
MaxBytes=1M
*/ ?>
```

## FlushInterval

> Commands, cronjob parts and content job workers also flush every this many seconds.  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditBufferSettings]
FlushInterval=5
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
