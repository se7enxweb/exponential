---
title: package.ini [MaintainerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MaintainerSettings] in package.ini: RoleList"
keywords: Einstellung, package.ini, MaintainerSettings, INI, setting, RoleList
type: generated
generated: exp:help:examples
---
# package.ini [MaintainerSettings]

Der Block `[MaintainerSettings]` von `settings/package.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RoleList

Standard: eine Liste mit 5: `lead`, `developer`, `designer`, `contributor`, `tester`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[MaintainerSettings]
# ein leeres "RoleList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RoleList[]
RoleList[]=lead
RoleList[]=developer
RoleList[]=designer
RoleList[]=contributor
RoleList[]=tester
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
