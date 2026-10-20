---
title: ezoe.ini [Engine_tinymce8]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Engine_tinymce8] in ezoe.ini (ezoe): TableDialog, ToolbarMap, ExternalPlugins"
keywords: Einstellung, ezoe.ini, Engine_tinymce8, INI, setting, TableDialog, ToolbarMap, ExternalPlugins
type: generated
generated: exp:help:examples
---
# ezoe.ini [Engine_tinymce8]

Der Block `[Engine_tinymce8]` von `extension/ezoe/settings/ezoe.ini` (Erweiterung ezoe), mit 3 Einstellung(en).

> TinyMCE 8 engine: \[EditorLayout\] button name => TinyMCE 8 toolbar item. Buttons with no counterpart are dropped.  

Bearbeiten Sie `extension/ezoe/settings/ezoe.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezoe.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezoe.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezoe.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TableDialog

> Dialog of Insert table / Table properties in TinyMCE 8:  
> classic = the design of the TinyMCE 3 dialog (Properties tab, size grid, width and border with unit, class, custom attributes)  
> modern  = the TinyMCE 8 dialog (plain form fields)  

Standard: `classic`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[Engine_tinymce8]
TableDialog=classic
*/ ?>
```

## ToolbarMap

Standard: ein Hash mit 36: `formatselect=blocks`, `bold=bold`, `italic=italic`, `underline=underline`, `sub=subscript`, `sup=superscript`, `justifyleft=alignleft`, `justifycenter=aligncenter`, `justifyright=alignright`, `justifyfull=alignjustify`, `bullist=bullist`, `numlist=numlist` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[Engine_tinymce8]
# ein leeres "ToolbarMap[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ToolbarMap[]
ToolbarMap[formatselect]=blocks
ToolbarMap[bold]=bold
ToolbarMap[italic]=italic
ToolbarMap[underline]=underline
ToolbarMap[sub]=subscript
ToolbarMap[sup]=superscript
ToolbarMap[justifyleft]=alignleft
ToolbarMap[justifycenter]=aligncenter
ToolbarMap[justifyright]=alignright
ToolbarMap[justifyfull]=alignjustify
ToolbarMap[bullist]=bullist
ToolbarMap[numlist]=numlist
ToolbarMap[outdent]=outdent
ToolbarMap[indent]=indent
ToolbarMap[undo]=undo
ToolbarMap[redo]=redo
ToolbarMap[link]=ezlink
ToolbarMap[unlink]=unlink
ToolbarMap[anchor]=anchor
ToolbarMap[image]=ezembed
# ... 16 weitere in der ausgelieferten Datei
*/ ?>
```

## ExternalPlugins

> ezoe plugins loaded by URL: name => design path (an extension adds its own the same way)  

Standard: ein Hash mit 7: `ezembed=javascript/tinymce8_ez/plugins/ezembed/plugin.js`, `ezcustomtag=javascript/tinymce8_ez/plugins/ezcustomtag/plugin.js`, `ezlink=javascript/tinymce8_ez/plugins/ezlink/plugin.js`, `ezpath=javascript/tinymce8_ez/plugins/ezpath/plugin.js`, `ezliteral=javascript/tinymce8_ez/plugins/ezliteral/plugin.js`, `eztable=javascript/tinymce8_ez/plugins/eztable/plugin.js`, `ezgeneral=javascript/tinymce8_ez/plugins/ezgeneral/plugin.js`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[Engine_tinymce8]
# ein leeres "ExternalPlugins[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExternalPlugins[]
ExternalPlugins[ezembed]=javascript/tinymce8_ez/plugins/ezembed/plugin.js
ExternalPlugins[ezcustomtag]=javascript/tinymce8_ez/plugins/ezcustomtag/plugin.js
ExternalPlugins[ezlink]=javascript/tinymce8_ez/plugins/ezlink/plugin.js
ExternalPlugins[ezpath]=javascript/tinymce8_ez/plugins/ezpath/plugin.js
ExternalPlugins[ezliteral]=javascript/tinymce8_ez/plugins/ezliteral/plugin.js
ExternalPlugins[eztable]=javascript/tinymce8_ez/plugins/eztable/plugin.js
ExternalPlugins[ezgeneral]=javascript/tinymce8_ez/plugins/ezgeneral/plugin.js
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezoe.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/settings/ezoe.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
