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

> Wo nach Symbolen gesucht wird, relativ zu Exponential  

Standard: `share/icons`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[IconSettings]
Repository=share/icons
*/ ?>
```

## Theme

> Welches Theme für alle Symboltypen als aktuelles verwendet wird  
> Wenn Sie pro Typ ein anderes Theme wollen,  
> können Sie diese Variable in der INI-Gruppe des Typs setzen.  

Standard: `crystal`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[IconSettings]
Theme=crystal
*/ ?>
```

## StandardTheme

> Das Standard-Theme. Es dient als Ausweich-Theme, wenn ein Symbol im aktuellen Theme nicht verfügbar ist.  

Standard: `crystal`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[IconSettings]
StandardTheme=crystal
*/ ?>
```

## DesignTheme

> Zusätzliche Themes, die durchsucht werden, bevor auf das Standard-Theme zurückgefallen wird.  
> AdditionalThemeList\[\]  
> Das Symbol-Theme eines Designs. Solange das Design das SiteDesign des Siteaccess ist (site.ini \[DesignSettings\]), ersetzt sein Theme  
> Theme, und Theme wird direkt danach durchsucht, sodass ein Schlüssel, den das Theme des Designs nicht zuordnet, das Symbol behält, das er  
> hatte. Das eigene Theme eines Typs (\[ClassIcons\] Theme usw.) gewinnt weiterhin. Ein leerer Wert schaltet es für dieses Design ab, ein leeres  
> DesignTheme\[\] für jedes Design. Ein eigenes, auf admin4 aufbauendes Design braucht einen eigenen Eintrag.  

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

> Gewünschte Größe der Symbole; die Größe wird nicht geprüft und soll  
> einfach eine Zeichenkette sein, die einem Verzeichnis im Theme entspricht  

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
