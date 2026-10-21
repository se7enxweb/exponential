---
title: menu.ini [Topmenu_shop]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Topmenu_shop] in menu.ini: Icon, NavigationPartIdentifier, Name, URL, Enabled, Shown, PolicyList"
keywords: Einstellung, menu.ini, Topmenu_shop, INI, setting, Icon, NavigationPartIdentifier, Name, URL, Enabled, Shown, PolicyList
type: generated
generated: exp:help:examples
---
# menu.ini [Topmenu_shop]

Der Block `[Topmenu_shop]` von `settings/menu.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Icon

Standard: `box-seam`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_shop]
Icon=box-seam
*/ ?>
```

## NavigationPartIdentifier

Standard: `ezshopnavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_shop]
NavigationPartIdentifier=ezshopnavigationpart
*/ ?>
```

## Name

Standard: `Store`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_shop]
Name=Store
*/ ?>
```

## URL

> Tooltip=Manage customers, orders, discounts and VAT types; view sales statistics.  

Standard: ein Hash mit 1: `default=shop/dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_shop]
# ein leeres "URL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
URL[]
URL[default]=shop/dashboard
*/ ?>
```

## Enabled

Standard: ein Hash mit 3: `default=true`, `browse=false`, `edit=false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_shop]
# ein leeres "Enabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Enabled[]
Enabled[default]=true
Enabled[browse]=false
Enabled[edit]=false
*/ ?>
```

## Shown

Standard: ein Hash mit 3: `navigation=true`, `default=false`, `browse=true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_shop]
# ein leeres "Shown[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Shown[]
Shown[navigation]=true
Shown[default]=false
Shown[browse]=true
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `shop/administrate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Topmenu_shop]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=shop/administrate
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
