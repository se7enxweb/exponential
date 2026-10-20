---
title: content.ini [VersionView]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [VersionView] in content.ini: AvailableSiteDesignList, ViewModes"
keywords: Einstellung, content.ini, VersionView, INI, setting, AvailableSiteDesignList, ViewModes
type: generated
generated: exp:help:examples
---
# content.ini [VersionView]

Der Block `[VersionView]` von `settings/content.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AvailableSiteDesignList

> OBSOLETE: Use AvailableSiteDesignList instead  
> Specify all available site designs  
> AvailableSiteDesigns=standard;user;admin;demo  
> OBSOLETE: A list of designs the site can use  

Standard: eine Liste mit 1: `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[VersionView]
# ein leeres "AvailableSiteDesignList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableSiteDesignList[]
AvailableSiteDesignList[]=admin
*/ ?>
```

## ViewModes

> AvailableSiteDesignList\[\]=base  
> The view modes a version can be shown in, content/versionview/<object>/<version>/(view\_mode)/<mode>  
> (node/view/<mode>.tpl); another one is not available. Without the parameter it is full.  
> With more than one listed, the View control box of the preview in the admin offers them.  
> design/standard has node/view/print.tpl as an example of a print layout.  

Standard: eine Liste mit 1: `full`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[VersionView]
# ein leeres "ViewModes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewModes[]
ViewModes[]=full
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
