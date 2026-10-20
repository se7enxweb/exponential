---
title: design.ini [JavaScriptSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [JavaScriptSettings] in design.ini: JavaScriptList, FrontendJavaScriptList, BackendJavaScriptList"
keywords: Einstellung, design.ini, JavaScriptSettings, INI, setting, JavaScriptList, FrontendJavaScriptList, BackendJavaScriptList
type: generated
generated: exp:help:examples
---
# design.ini [JavaScriptSettings]

Der Block `[JavaScriptSettings]` von `settings/design.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/design.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/design.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/design.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/design.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## JavaScriptList

> List of JavaScript files to include in pagelayout  
> DEPRECATED in favor of \[Frontend|Backend\]JavaScriptList\[\]  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[JavaScriptSettings]
# ein leeres "JavaScriptList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
JavaScriptList[]
*/ ?>
```

## FrontendJavaScriptList

> JavaScriptList\[\]=tools/ezjsselection.js  
> Same as above for frontend designs  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[JavaScriptSettings]
# ein leeres "FrontendJavaScriptList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FrontendJavaScriptList[]
*/ ?>
```

## BackendJavaScriptList

> Same as above for backend (admin) designs. The tables, uploads, dialogs, date fields, collapsing menus and  
> sticky toolbars run on Exponential UI (extension expui adds exp::\* to this list).  
> leftmenu\_widthcontrol.js is the column grip of design/admin, whose page\_leftmenu.tpl loads it itself; admin4 has  
> its own in ezadmin\_menubar.js and does not ship the file. Do not add it here.  

Standard: eine Liste mit 5: `tools/ezjsselection.js`, `lib/ezjslibimagepreloader.js`, `popupmenu/ezpopupmenu.js`, `contentstructuremenu_dynamic.js`, `ezadmin_menubar.js`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[JavaScriptSettings]
# ein leeres "BackendJavaScriptList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
BackendJavaScriptList[]
BackendJavaScriptList[]=tools/ezjsselection.js
BackendJavaScriptList[]=lib/ezjslibimagepreloader.js
BackendJavaScriptList[]=popupmenu/ezpopupmenu.js
BackendJavaScriptList[]=contentstructuremenu_dynamic.js
BackendJavaScriptList[]=ezadmin_menubar.js
*/ ?>
```

## Siehe auch

- [Alle Blöcke von design.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/design.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
