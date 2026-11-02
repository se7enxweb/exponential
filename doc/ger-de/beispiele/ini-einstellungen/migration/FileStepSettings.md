---
title: migration.ini [FileStepSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FileStepSettings] in migration.ini: AllowedRootList"
keywords: Einstellung, migration.ini, FileStepSettings, INI, setting, AllowedRootList
type: generated
generated: exp:help:examples
---
# migration.ini [FileStepSettings]

Der Block `[FileStepSettings]` von `settings/migration.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AllowedRootList

> The folders, relative to the installation, below which the step type code\_file writes, replaces or removes code and  
> design files (templates, stylesheets, scripts, an extension's files). Nothing outside them is written, nor a .git  
> folder, settings/override, var or vendor below them, nor a link that leads out of them. The file as it was is kept  
> in var/backup/migration/files/<migration id>/ first, with its SHA-256 before and after; a failed migration and a  
> rollback put it back. Empty (AllowedRootList\[\] alone): no file can be written, the step type is off.  

Standard: eine Liste mit 2: `design`, `extension`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[FileStepSettings]
# ein leeres "AllowedRootList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedRootList[]
AllowedRootList[]=design
AllowedRootList[]=extension
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
