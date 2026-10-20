---
title: module.ini [ModuleOverrides]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ModuleOverrides] in module.ini: NavigationPart"
keywords: Einstellung, module.ini, ModuleOverrides, INI, setting, NavigationPart
type: generated
generated: exp:help:examples
---
# module.ini [ModuleOverrides]

Der Block `[ModuleOverrides]` von `settings/module.ini`, mit 1 Einstellung(en).

> This controls overriding of navigations parts defined  
> module.php or by dynamic code.  
>
> You can use the module name alone or with a view  

Bearbeiten Sie `settings/module.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/module.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/module.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/module.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## NavigationPart

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/module.ini.append.php
[ModuleOverrides]
# ein leeres "NavigationPart[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
NavigationPart[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von module.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/module.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
