---
title: design.ini [AdminHeaderSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdminHeaderSettings] in design.ini: HeaderItems"
keywords: Einstellung, design.ini, AdminHeaderSettings, INI, setting, HeaderItems
type: generated
generated: exp:help:examples
---
# design.ini [AdminHeaderSettings]

Der Block `[AdminHeaderSettings]` von `settings/design.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/design.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/design.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/design.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/design.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HeaderItems

> admin4: die Header-Elemente der Erweiterungen, gezeichnet unter den Werkzeugen des Headers nach Hilfe und vor dem Benutzermenü  
> (page\_topmenu.tpl). Jedes ist ein Template, z. B. HeaderItems\[\]=sqliimport/header\_bell.tpl in der design.ini.append.php  
> einer Erweiterung; parts/a4\_header\_item.tpl beschreibt sein Markup: ein runder 36-px-Button mit einer Zahl, ein Panel  
> und, für Live-Daten, ein JSON-Feed (data-a4-header-feed), den admin4.js lädt, da der Header pro Benutzer gecacht wird.  
> doc/eng-us/features/6.0/admin4-design.md, "Header items".  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[AdminHeaderSettings]
# ein leeres "HeaderItems[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HeaderItems[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von design.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/design.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
