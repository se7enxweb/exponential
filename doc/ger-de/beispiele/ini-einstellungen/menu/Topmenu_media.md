---
title: menu.ini [Topmenu_media]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Topmenu_media] in menu.ini: Icon, NavigationPartIdentifier, Name, URL, Enabled, Shown, PolicyList"
keywords: Einstellung, menu.ini, Topmenu_media, INI, setting, Icon, NavigationPartIdentifier, Name, URL, Enabled, Shown, PolicyList
type: generated
generated: exp:help:examples
---
# menu.ini [Topmenu_media]

Der Block `[Topmenu_media]` von `settings/menu.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Icon

Standard: `images`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_media]
Icon=images
*/ ?>
```

## NavigationPartIdentifier

Standard: `ezmedianavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_media]
NavigationPartIdentifier=ezmedianavigationpart
*/ ?>
```

## Name

Standard: `Media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_media]
Name=Media
*/ ?>
```

## URL

> Tooltip=Manage images, files, documents, etc.  

Standard: ein Hash mit 2: `default=content/view/full/43`, `browse=content/browse/43`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_media]
# ein leeres "URL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
URL[]
URL[default]=content/view/full/43
URL[browse]=content/browse/43
*/ ?>
```

## Enabled

Standard: ein Hash mit 3: `default=true`, `browse=true`, `edit=false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_media]
# ein leeres "Enabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Enabled[]
Enabled[default]=true
Enabled[browse]=true
Enabled[edit]=false
*/ ?>
```

## Shown

Standard: ein Hash mit 3: `default=true`, `navigation=true`, `browse=true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_media]
# ein leeres "Shown[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Shown[]
Shown[default]=true
Shown[navigation]=true
Shown[browse]=true
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `43`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_media]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=43
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
