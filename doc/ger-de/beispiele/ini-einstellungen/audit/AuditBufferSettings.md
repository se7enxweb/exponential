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

> F3: Ereignisse werden gesammelt und am Ende der Anfrage pro Kanal in einem Schreibvorgang angehängt  
> (auch bei einem fatalen Fehler, über den Shutdown-Handler).  enabled | disabled  
> (disabled schreibt jedes Ereignis sofort: nur zur Fehlersuche).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditBufferSettings]
Buffering=enabled
*/ ?>
```

## ImmediateEvents

> Ereignisse, die sofort und ungepuffert geschrieben werden (F3: Sicherheitsereignisse): Muster.  

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

> Früher schreiben, wenn der Puffer so viele Ereignisse oder so viele Bytes enthält (lange Anfragen,  
> Befehle). Suffixe K, M.  

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

> Befehle, Cronjob-Teile und Worker der Inhaltsaufträge schreiben außerdem alle so viele Sekunden.  

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
