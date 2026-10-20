---
title: audit.ini [AuditArchiveSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditArchiveSettings] in audit.ini: ArchiveDir, FormatHandlers, Level, FileMode, DirMode"
keywords: Einstellung, audit.ini, AuditArchiveSettings, INI, setting, ArchiveDir, FormatHandlers, Level, FileMode, DirMode
type: generated
generated: exp:help:examples
---
# audit.ini [AuditArchiveSettings]

Der Block `[AuditArchiveSettings]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ArchiveDir

> Wohin Archive kommen: absolut oder relativ zum var-Verzeichnis der Website. Am besten auf einem anderen  
> Dateisystem oder einem Mount, den der Webserver nicht beschreiben kann (siehe die Sicherheitshinweise).  

Standard: `log/audit/archive`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditArchiveSettings]
ArchiveDir=log/audit/archive
*/ ?>
```

## FormatHandlers

> Format-Handler: Name => Klasse, die expAuditFormatHandler implementiert (RAD-Survey-Registry  
> "auditformats"). Ein Handler, dessen PHP-Erweiterung oder Binärdatei fehlt, meldet sich als  
> nicht verfügbar, und der Archivlauf weicht auf gzip aus.  
> Eingebaut.  

Standard: ein Hash mit 5: `gzip=expAuditGzipFormat`, `bzip2=expAuditBzip2Format`, `xz=expAuditXzFormat`, `zstd=expAuditZstdFormat`, `zip=expAuditZipFormat`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditArchiveSettings]
# ein leeres "FormatHandlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FormatHandlers[]
FormatHandlers[gzip]=expAuditGzipFormat
FormatHandlers[bzip2]=expAuditBzip2Format
FormatHandlers[xz]=expAuditXzFormat
FormatHandlers[zstd]=expAuditZstdFormat
FormatHandlers[zip]=expAuditZipFormat
*/ ?>
```

## Level

> Kompressionsstufe pro Handler (im eigenen Bereich jedes Handlers).  

Standard: ein Hash mit 5: `gzip=9`, `bzip2=9`, `xz=6`, `zstd=19`, `zip=9`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditArchiveSettings]
# ein leeres "Level[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Level[]
Level[gzip]=9
Level[bzip2]=9
Level[xz]=6
Level[zstd]=19
Level[zip]=9
*/ ?>
```

## FileMode

> Modus der Archivdateien und -verzeichnisse.  

Standard: `0440`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditArchiveSettings]
FileMode=0440
*/ ?>
```

## DirMode

Standard: `0750`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditArchiveSettings]
DirMode=0750
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
