---
title: menu.ini [MenuSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MenuSettings] in menu.ini: AvailableMenuArray"
keywords: Einstellung, menu.ini, MenuSettings, INI, setting, AvailableMenuArray
type: generated
generated: exp:help:examples
---
# menu.ini [MenuSettings]

Der Block `[MenuSettings]` von `settings/menu.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AvailableMenuArray

Standard: eine Liste mit 4: `TopOnly`, `LeftOnly`, `DoubleTop`, `LeftTop`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[MenuSettings]
# ein leeres "AvailableMenuArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableMenuArray[]
AvailableMenuArray[]=TopOnly
AvailableMenuArray[]=LeftOnly
AvailableMenuArray[]=DoubleTop
AvailableMenuArray[]=LeftTop
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
