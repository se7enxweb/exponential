---
title: ezoe.ini [EditorLayout_mini]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [EditorLayout_mini] in ezoe.ini (ezoe): Buttons, PathLocation"
keywords: Einstellung, ezoe.ini, EditorLayout_mini, INI, setting, Buttons, PathLocation
type: generated
generated: exp:help:examples
---
# ezoe.ini [EditorLayout_mini]

Der Block `[EditorLayout_mini]` von `extension/ezoe/settings/ezoe.ini` (Erweiterung ezoe), mit 2 Einstellung(en).

> Beispiel für das Layout eines Mini-Editors  
> 'mini' stammt aus dem in ezxml.ini definierten TagPreset und  
> wird beim Bearbeiten der Klasse pro Attribut gespeichert.  
> Diese Funktion gibt es erst ab eZ Publish 4.1!  

Bearbeiten Sie `extension/ezoe/settings/ezoe.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezoe.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezoe.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezoe.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Buttons

Standard: eine Liste mit 6: `bold`, `italic`, `underline`, `|`, `undo`, `redo`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorLayout_mini]
# ein leeres "Buttons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Buttons[]
Buttons[]=bold
Buttons[]=italic
Buttons[]=underline
Buttons[]=|
Buttons[]=undo
Buttons[]=redo
*/ ?>
```

## PathLocation

Standard: `none`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorLayout_mini]
PathLocation=none
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezoe.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/settings/ezoe.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
