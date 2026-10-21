---
title: ezjscore.ini [ezjscServer_ezjsctemplate]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ezjscServer_ezjsctemplate] in ezjscore.ini (ezjscore): TemplateFunction, Functions"
keywords: Einstellung, ezjscore.ini, ezjscServer_ezjsctemplate, INI, setting, TemplateFunction, Functions
type: generated
generated: exp:help:examples
---
# ezjscore.ini [ezjscServer_ezjsctemplate]

Der Block `[ezjscServer_ezjsctemplate]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 2 Einstellung(en).

> File=kernel/classes/debugbar/expdebugbarserverfunctions.php  

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TemplateFunction

> URL zum Testen dieser Serverfunktion (gibt eine Meldung aus):  
> <root>/ezjscore/call/ezjsctemplate::alert  
> führt templates/ezjsctemplate/alert.tpl aus  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjsctemplate]
TemplateFunction=true
*/ ?>
```

## Functions

Standard: eine Liste mit 1: `ezjsctemplate`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjsctemplate]
# ein leeres "Functions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Functions[]
Functions[]=ezjsctemplate
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
