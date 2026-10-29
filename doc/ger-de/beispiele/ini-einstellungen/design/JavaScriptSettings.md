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

> Wie oben für Backend-Designs (Admin). Diese Skripte sind auf jeder Admin-Seite und laufen ohne jQuery: admin4  
> und admin4l laden auf keiner Seite jQuery. Ein Template, dessen Skripte jQuery oder Exponential UI (exp::\*) brauchen, fordert  
> sie selbst mit ezscript\_require an, außerhalb jedes cache-block (doc/eng-us/features/6.0/jquery4-and-yui-removal.md).  
> leftmenu\_widthcontrol.js ist der Spaltengriff von design/admin, dessen page\_leftmenu.tpl es selbst lädt; admin4 hat  
> seinen eigenen in ezadmin\_menubar.js und liefert die Datei nicht mit. Hier nicht hinzufügen.  

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

> Skripte, die die älteren Admin-Designs (admin und admin3, gewählt mit dem UI-Schalter) weiterhin auf jeder Seite erwarten: ihre  
> page\_head\_script.tpl lädt sie nach jQuery und BackendJavaScriptList (adminui, sobald es diese Liste auch liest);  
> admin4 und admin4l lesen diese Liste nicht. Eine Erweiterung, die für diese Designs bisher ein jQuery-Skript oder ein exp::-Modul in BackendJavaScriptList eingetragen hat,  
> trägt es hier ein.  

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
