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

> Die Ordner, relativ zur Installation, unterhalb derer der Schritttyp code\_file Code- und Designdateien schreibt, ersetzt oder entfernt  
> (Templates, Stylesheets, Skripte, die Dateien einer Erweiterung). Nichts außerhalb davon wird geschrieben, auch kein .git-Ordner,  
> settings/override, var oder vendor darunter, und kein Link, der aus ihnen hinausführt. Die Datei, wie sie war, wird zuerst  
> in var/backup/migration/files/<migration id>/ aufbewahrt, mit ihrem SHA-256 vorher und nachher; eine gescheiterte Migration und eine  
> Rücknahme stellen sie wieder her. Leer (AllowedRootList\[\] allein): Keine Datei kann geschrieben werden, der Schritttyp ist aus.  

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
