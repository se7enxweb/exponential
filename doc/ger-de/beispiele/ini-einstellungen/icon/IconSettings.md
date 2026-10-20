---
title: icon.ini [IconSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [IconSettings] in icon.ini: Repository, Theme, StandardTheme, DesignTheme, Size"
keywords: Einstellung, icon.ini, IconSettings, INI, setting, Repository, Theme, StandardTheme, DesignTheme, Size
type: generated
generated: exp:help:examples
---
# icon.ini [IconSettings]

Der Block `[IconSettings]` von `settings/icon.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/icon.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/icon.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/icon.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/icon.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Repository

> Where to look for icons, relative to Exponential  

Standard: `share/icons`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[IconSettings]
Repository=share/icons
*/ ?>
```

## Theme

> Which theme to use as current for all icon types  
> If you wish to have a different theme per type you  
> can set this variable in the INI group for the type.  

Standard: `crystal`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[IconSettings]
Theme=crystal
*/ ?>
```

## StandardTheme

> The standard theme. It is used as fallback theme if an icon is not available in the current theme.  

Standard: `crystal`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[IconSettings]
StandardTheme=crystal
*/ ?>
```

## DesignTheme

> Additional themes to search before falling back to the standard theme.  
> AdditionalThemeList\[\]  
> The icon theme of a design. While the design is the siteaccess's SiteDesign (site.ini \[DesignSettings\]), its theme  
> replaces Theme, and Theme is searched right after it, so a key the design's theme does not map keeps the icon it  
> had. A type's own Theme (\[ClassIcons\] Theme etc.) still wins. An empty value turns it off for that design, an empty  
> DesignTheme\[\] for every design. A design of your own built on admin4 needs its own entry.  

Standard: ein Hash mit 2: `admin4=bootstrap-icons`, `admin4l=bootstrap-icons`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[IconSettings]
# ein leeres "DesignTheme[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DesignTheme[]
DesignTheme[admin4]=bootstrap-icons
DesignTheme[admin4l]=bootstrap-icons
*/ ?>
```

## Size

> Wanted size of icons, the size is not checked and is expected  
> to be just a string which should be a directory in the theme  

Standard: `normal`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[IconSettings]
Size=normal
*/ ?>
```

## Siehe auch

- [Alle Blöcke von icon.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/icon.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
