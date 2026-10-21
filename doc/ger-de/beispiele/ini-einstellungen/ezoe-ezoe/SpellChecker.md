---
title: ezoe.ini [SpellChecker]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SpellChecker] in ezoe.ini (ezoe): config"
keywords: Einstellung, ezoe.ini, SpellChecker, INI, setting, config
type: generated
generated: exp:help:examples
---
# ezoe.ini [SpellChecker]

Der Block `[SpellChecker]` von `extension/ezoe/settings/ezoe.ini` (Erweiterung ezoe), mit 1 Einstellung(en).

Bearbeiten Sie `extension/ezoe/settings/ezoe.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezoe.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezoe.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezoe.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## config

> Einstellungen der Rechtschreibprüfung von TinyMCE  
> Damit diese Einstellungen wirken, müssen Sie das Plugin spellchecker aktivieren  
> Wiki: http://www.tinymce.com/wiki.php/Plugin:spellchecker  

Standard: ein Hash mit 1: `general.engine=GoogleSpell`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[SpellChecker]
# ein leeres "config[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
config[]
config[general.engine]=GoogleSpell
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezoe.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/settings/ezoe.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
