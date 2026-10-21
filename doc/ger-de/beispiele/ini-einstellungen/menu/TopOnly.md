---
title: menu.ini [TopOnly]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TopOnly] in menu.ini: TitleText, MenuThumbnail, TopMenu, LeftMenu"
keywords: Einstellung, menu.ini, TopOnly, INI, setting, TitleText, MenuThumbnail, TopMenu, LeftMenu
type: generated
generated: exp:help:examples
---
# menu.ini [TopOnly]

Der Block `[TopOnly]` von `settings/menu.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TitleText

Standard: `Only top menu`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopOnly]
TitleText=Only top menu
*/ ?>
```

## MenuThumbnail

Standard: `menu/top_only.jpg`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopOnly]
MenuThumbnail=menu/top_only.jpg
*/ ?>
```

## TopMenu

Standard: `flat_top`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopOnly]
TopMenu=flat_top
*/ ?>
```

## LeftMenu

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopOnly]
LeftMenu=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
