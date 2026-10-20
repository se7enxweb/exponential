---
title: ezoe.ini [EditorLayout]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [EditorLayout] in ezoe.ini (ezoe): Buttons, ToolbarLocation, PathLocation"
keywords: Einstellung, ezoe.ini, EditorLayout, INI, setting, Buttons, ToolbarLocation, PathLocation
type: generated
generated: exp:help:examples
---
# ezoe.ini [EditorLayout]

Der Block `[EditorLayout]` von `extension/ezoe/settings/ezoe.ini` (Erweiterung ezoe), mit 3 Einstellung(en).

Bearbeiten Sie `extension/ezoe/settings/ezoe.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezoe.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezoe.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezoe.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Buttons

> Global editor layout (buttons and path / toolbar placment)  
> Default buttons you want to use in the editor  
> buttons not mentioned here aren't tested and therefore not supported together with Online Editor  
> NOTE: underline and pagebreak will not appear if these custom tags are not enabled in content.ini!  
> To controll alignment on p|header|custom|table|td|th tags  
> Only supported on eZ Publish 4.1 and higher (buttons will be hidden in 4.0.x)  
> These two needs the directionality to be enabled to be used  
> Buttons\[\]=|  
> Buttons\[direction\]=ltr,rtl  
> These two needs the searchreplace plugin in order to work  
> Buttons\[\]=|  
> Buttons\[searchreplace\]=search,replace  
> Additonal embed button for specific file UI  
> Buttons\[\]=file  
> Needed to be able to past content from word, to wash the syntax (should not be needed on 5.0.2 and up)  
> Buttons\[\]=pasteword  
> Needs the nonbreaking plugin enabled and probably also AllowMultipleSpaces set to true in ezxml.ini  
> Buttons\[\]=nonbreaking  
> emotions button needs ezemotions plugin to be activated  
> Buttons\[\]=emotions  
> SplitButton that puts all table related buttons inside a sub menu.  
> Buttons\[table\]=tablemenu  
> But by default we use buttons directly in toolbar for quick access  
> Additional table buttons  
> Buttons\[\]=col\_before  
> Buttons\[\]=row\_before  
> Lets you see a popup with html markup  
> needs rewrite rules to allow html files in design folders if in virtualhost mode (like shown in INSTALL doc)  
> Buttons\[\]=code  
> needs the visualchars plugin in order to work  
> Buttons\[\]=visualchars  
> Buttons for publish / store / discard actions on whole object  
> Buttons\[\]=|  
> Buttons\[\]=publish  
> Buttons\[\]=store  
> Buttons\[\]=discard  
> Buttons\[\]=|  
> Button for TinyMCE spellchecker, needs spellchecker plugin  
> Buttons\[\]=spellchecker  
> Button to disable editor  

Standard: ein Hash mit 20: `style=formatselect,bold,italic,underline`, `|`, `math=sub,sup`, `|`, `align=justifyleft,justifycenter,justifyright,justifyfull`, `|`, `lists=bullist,numlist,outdent,indent`, `|`, `undo=undo,redo`, `|`, `link=link,unlink,anchor`, `|` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorLayout]
# ein leeres "Buttons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Buttons[]
Buttons[style]=formatselect,bold,italic,underline
Buttons[]=|
Buttons[math]=sub,sup
Buttons[]=|
Buttons[align]=justifyleft,justifycenter,justifyright,justifyfull
Buttons[]=|
Buttons[lists]=bullist,numlist,outdent,indent
Buttons[]=|
Buttons[undo]=undo,redo
Buttons[]=|
Buttons[link]=link,unlink,anchor
Buttons[]=|
Buttons[embed]=image,object
Buttons[custom]=custom,literal,charmap,pagebreak
Buttons[]=|
Buttons[table]=table,delete_table,delete_col,col_after,delete_row,row_after,split_cells,merge_cells
Buttons[]=|
Buttons[extra]=fullscreen,help
Buttons[]=|
Buttons[]=disable
*/ ?>
```

## ToolbarLocation

> Toolbar location (theme\_advanced\_toolbar\_location), possible values: top / bottom / none  

Standard: `top`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorLayout]
ToolbarLocation=top
*/ ?>
```

## PathLocation

> Path location (theme\_advanced\_statusbar\_location), possible values: top / bottom / none  

Standard: `bottom`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorLayout]
PathLocation=bottom
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezoe.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/settings/ezoe.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
