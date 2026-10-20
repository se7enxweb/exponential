---
title: audit.ini [AuditChannel_content]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditChannel_content] in audit.ini: LiveDays, ArchiveDays, MaxFileSize, ArchiveFormat, Sinks"
keywords: Einstellung, audit.ini, AuditChannel_content, INI, setting, LiveDays, ArchiveDays, MaxFileSize, ArchiveFormat, Sinks
type: generated
generated: exp:help:examples
---
# audit.ini [AuditChannel_content]

Der Block `[AuditChannel_content]` von `settings/audit.ini`, mit 5 Einstellung(en).

> One block per channel. Every variable may be left out: the \[AuditRotationSettings\]  
> defaults apply.  

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LiveDays

> Days a file stays live (uncompressed, in LogDir) before it is archived (F6: 90).  

Standard: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_content]
LiveDays=90
*/ ?>
```

## ArchiveDays

> Days an archive is kept before retention removes it (F6: 2 years).  

Standard: `730`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_content]
ArchiveDays=730
*/ ?>
```

## MaxFileSize

> Size at which a day's file is closed and a new part started; K, M, G suffixes.  

Standard: `64M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_content]
MaxFileSize=64M
*/ ?>
```

## ArchiveFormat

> The archive format handler (see \[AuditArchiveSettings\] FormatHandlers\[\]).  

Standard: `gzip`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_content]
ArchiveFormat=gzip
*/ ?>
```

## Sinks

> Sinks this channel's events are sent to, besides the file (see \[AuditSinkSettings\]).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_content]
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
