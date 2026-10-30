---
title: audit.ini [AuditChannel_access]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditChannel_access] in audit.ini: LiveDays, ArchiveDays, MaxFileSize, ArchiveFormat, Sinks"
keywords: Einstellung, audit.ini, AuditChannel_access, INI, setting, LiveDays, ArchiveDays, MaxFileSize, ArchiveFormat, Sinks
type: generated
generated: exp:help:examples
---
# audit.ini [AuditChannel_access]

Der Block `[AuditChannel_access]` von `settings/audit.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LiveDays

Standard: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_access]
LiveDays=90
*/ ?>
```

## ArchiveDays

Standard: `730`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_access]
ArchiveDays=730
*/ ?>
```

## MaxFileSize

Standard: `64M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_access]
MaxFileSize=64M
*/ ?>
```

## ArchiveFormat

Standard: `gzip`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_access]
ArchiveFormat=gzip
*/ ?>
```

## Sinks

> Das Benutzer-Audit-Log: Einträge über ein Benutzerkonto oder eine Benutzergruppe gehen auch in die Tabelle  
> expuser\_audit (\[UserAuditSettings\], doc/eng-us/features/6.0/user-audit.md)  

Standard: eine Liste mit 2: `syslog`, `useraudit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditChannel_access]
# ein leeres "Sinks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Sinks[]
Sinks[]=syslog
Sinks[]=useraudit
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
