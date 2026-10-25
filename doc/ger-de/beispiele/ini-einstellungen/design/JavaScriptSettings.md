---
title: design.ini [JavaScriptSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [JavaScriptSettings] in design.ini: JavaScriptList, FrontendJavaScriptList, BackendJavaScriptList, ClassicBackendJavaScriptList"
keywords: Einstellung, design.ini, JavaScriptSettings, INI, setting, JavaScriptList, FrontendJavaScriptList, BackendJavaScriptList, ClassicBackendJavaScriptList
type: generated
generated: exp:help:examples
---
# design.ini [JavaScriptSettings]

Der Block `[JavaScriptSettings]` von `settings/design.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/design.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/design.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/design.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/design.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## JavaScriptList

> Liste der JavaScript-Dateien, die in das Pagelayout eingebunden werden  
> VERALTET zugunsten von \[Frontend|Backend\]JavaScriptList\[\]  

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
> Wie oben, für Frontend-Designs  

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

> Same as above for backend (admin) designs. These scripts are on every admin page and run without jQuery: admin4  
> and admin4l load no jQuery on every page. A template whose scripts need jQuery or Exponential UI (exp::\*) requests  
> them itself with ezscript\_require, outside any cache-block (doc/eng-us/features/6.0/jquery4-and-yui-removal.md).  
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

## ClassicBackendJavaScriptList

> Scripts the older admin designs (admin and admin3, chosen with the UI switch) still expect on every page: their  
> page\_head\_script.tpl loads them after jQuery and BackendJavaScriptList (adminui once its package reads it too);  
> admin4 and admin4l do not read this list. An extension that used to put a jQuery script or an exp:: module into BackendJavaScriptList for those  
> designs adds it here.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[JavaScriptSettings]
# ein leeres "ClassicBackendJavaScriptList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ClassicBackendJavaScriptList[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von design.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/design.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
