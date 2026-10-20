---
title: audit.ini [AuditChannel_read]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditChannel_read] in audit.ini: LiveDays, ArchiveDays, MaxFileSize, ArchiveFormat, Sinks"
keywords: Einstellung, audit.ini, AuditChannel_read, INI, setting, LiveDays, ArchiveDays, MaxFileSize, ArchiveFormat, Sinks
type: generated
generated: exp:help:examples
---
# audit.ini [AuditChannel_read]

Der Block `[AuditChannel_read]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LiveDays

> Reads are many and of little value after a while (Proposed).  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_read]
LiveDays=30
*/ ?>
```

## ArchiveDays

Standard: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_read]
ArchiveDays=90
*/ ?>
```

## MaxFileSize

Standard: `256M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_read]
MaxFileSize=256M
*/ ?>
```

## ArchiveFormat

Standard: `zstd`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_read]
ArchiveFormat=zstd
*/ ?>
```

## Sinks

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_read]
# ein leeres "Sinks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Sinks[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
