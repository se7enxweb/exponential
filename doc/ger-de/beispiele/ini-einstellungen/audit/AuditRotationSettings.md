---
title: audit.ini [AuditRotationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditRotationSettings] in audit.ini: LiveDays, ArchiveDays, MaxFileSize, ArchiveFormat, VerifyBeforeArchive, VerifyAfterArchive, RotateAfter"
keywords: Einstellung, audit.ini, AuditRotationSettings, INI, setting, LiveDays, ArchiveDays, MaxFileSize, ArchiveFormat, VerifyBeforeArchive, VerifyAfterArchive, RotateAfter
type: generated
generated: exp:help:examples
---
# audit.ini [AuditRotationSettings]

Der Block `[AuditRotationSettings]` von `settings/audit.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LiveDays

> Q8: nach Tag und nach Größe, komprimierte Archive, Aufbewahrung pro Kanal; ausgeführt vom Cronjob-Teil  
> audit (\[CronjobPart-audit\] in cronjob.ini), aus der Konsole und von exp:audit.  
> Standards für die Blöcke \[AuditChannel\_\*\]:  

Standard: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRotationSettings]
LiveDays=90
*/ ?>
```

## ArchiveDays

Standard: `730`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRotationSettings]
ArchiveDays=730
*/ ?>
```

## MaxFileSize

Standard: `64M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRotationSettings]
MaxFileSize=64M
*/ ?>
```

## ArchiveFormat

Standard: `gzip`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRotationSettings]
ArchiveFormat=gzip
*/ ?>
```

## VerifyBeforeArchive

> Die Kette einer Datei prüfen, bevor sie archiviert wird; eine defekte Datei wird trotzdem archiviert  
> (Beweise zu entfernen ist schlimmer), aber ihr Manifest sagt broken, und system.audit.chain.broken  
> wird geschrieben.  enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRotationSettings]
VerifyBeforeArchive=enabled
*/ ?>
```

## VerifyAfterArchive

> Jedes Archiv zurücklesen und seinen sha256 vergleichen, bevor die Live-Datei entfernt wird.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRotationSettings]
VerifyAfterArchive=enabled
*/ ?>
```

## RotateAfter

> Die täglichen Aufgaben (Rotation des Vortags, Archivierung, Aufbewahrung, Prüfpunkt) laufen beim  
> ersten Cronjob-Lauf nach dieser Tageszeit (HH:MM, die Zeitzone der Website).  

Standard: `00:15`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditRotationSettings]
RotateAfter=00:15
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
