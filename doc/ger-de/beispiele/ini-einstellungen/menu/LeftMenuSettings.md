---
title: menu.ini [LeftMenuSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LeftMenuSettings] in menu.ini: MenuWidth"
keywords: Einstellung, menu.ini, LeftMenuSettings, INI, setting, MenuWidth
type: generated
generated: exp:help:examples
---
# menu.ini [LeftMenuSettings]

Der Block `[LeftMenuSettings]` von `settings/menu.ini`, mit 1 Einstellung(en).

> Steuert die Anzeige des linken Menüs der Administrationsoberfläche.  
> Mit der Einstellung Width lassen sich die Breiten (in em) für die  
> verschiedenen Menügrößen (small/medium/large) festlegen.  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MenuWidth

Standard: ein Hash mit 3: `small=13`, `medium=19`, `large=25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[LeftMenuSettings]
# ein leeres "MenuWidth[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MenuWidth[]
MenuWidth[small]=13
MenuWidth[medium]=19
MenuWidth[large]=25
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
