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

> Globales Layout des Editors (Schaltflächen und Platzierung von Pfad / Werkzeugleiste)  
> Standardschaltflächen, die Sie im Editor verwenden wollen  
> Hier nicht genannte Schaltflächen sind nicht getestet und werden daher mit dem Online Editor nicht unterstützt  
> HINWEIS: underline und pagebreak erscheinen nicht, wenn diese eigenen Tags in content.ini nicht aktiviert sind!  
> Zur Steuerung der Ausrichtung bei den Tags p|header|custom|table|td|th  
> Nur ab eZ Publish 4.1 unterstützt (in 4.0.x werden die Schaltflächen ausgeblendet)  
> Diese beiden brauchen das Plugin directionality  
> Buttons\[\]=|  
> Buttons\[direction\]=ltr,rtl  
> Diese beiden brauchen das Plugin searchreplace  
> Buttons\[\]=|  
> Buttons\[searchreplace\]=search,replace  
> Zusätzliche Einbettungs-Schaltfläche für eine eigene Datei-Oberfläche  
> Buttons\[\]=file  
> Nötig, um Inhalte aus Word einzufügen und die Syntax zu bereinigen (ab 5.0.2 nicht mehr nötig)  
> Buttons\[\]=pasteword  
> Braucht das aktivierte Plugin nonbreaking und wahrscheinlich auch AllowMultipleSpaces=true in ezxml.ini  
> Buttons\[\]=nonbreaking  
> Die Schaltfläche emotions braucht das aktivierte Plugin ezemotions  
> Buttons\[\]=emotions  
> SplitButton, der alle Tabellen-Schaltflächen in ein Untermenü legt.  
> Buttons\[table\]=tablemenu  
> Standardmäßig verwenden wir die Schaltflächen aber direkt in der Werkzeugleiste für schnellen Zugriff  
> Zusätzliche Tabellen-Schaltflächen  
> Buttons\[\]=col\_before  
> Buttons\[\]=row\_before  
> Zeigt ein Popup mit dem HTML-Markup  
> braucht Rewrite-Regeln, die HTML-Dateien in Design-Ordnern erlauben, im Modus mit virtuellem Host (wie im INSTALL-Dokument gezeigt)  
> Buttons\[\]=code  
> braucht das Plugin visualchars  
> Buttons\[\]=visualchars  
> Schaltflächen für die Aktionen Veröffentlichen / Speichern / Verwerfen für das ganze Objekt  
> Buttons\[\]=|  
> Buttons\[\]=publish  
> Buttons\[\]=store  
> Buttons\[\]=discard  
> Buttons\[\]=|  
> Schaltfläche für die Rechtschreibprüfung von TinyMCE, braucht das Plugin spellchecker  
> Buttons\[\]=spellchecker  
> Schaltfläche zum Deaktivieren des Editors  

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

> Position der Werkzeugleiste (theme\_advanced\_toolbar\_location), mögliche Werte: top / bottom / none  

Standard: `top`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorLayout]
ToolbarLocation=top
*/ ?>
```

## PathLocation

> Position des Pfades (theme\_advanced\_statusbar\_location), mögliche Werte: top / bottom / none  

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
