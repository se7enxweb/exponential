---
title: design.ini [StylesheetSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [StylesheetSettings] in design.ini: SiteCSS, ClassesCSS, CSSFileList, FrontendCSSFileList, BackendCSSFileList, AdminComponentCSSFileList"
keywords: Einstellung, design.ini, StylesheetSettings, INI, setting, SiteCSS, ClassesCSS, CSSFileList, FrontendCSSFileList, BackendCSSFileList, AdminComponentCSSFileList
type: generated
generated: exp:help:examples
---
# design.ini [StylesheetSettings]

Der Block `[StylesheetSettings]` von `settings/design.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/design.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/design.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/design.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/design.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SiteCSS

Standard: `stylesheets/t02/site-colors.css`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[StylesheetSettings]
SiteCSS=stylesheets/t02/site-colors.css
*/ ?>
```

## ClassesCSS

Standard: `stylesheets/t02/classes-colors.css`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[StylesheetSettings]
ClassesCSS=stylesheets/t02/classes-colors.css
*/ ?>
```

## CSSFileList

> A list of CSS file to always include in the pagelayout  
> This can be filled in by extensions to provide styles  
> that are not in the standard themes.  
> DEPRECATED in favor of \[Frontend|Backend\]CSSFileList\[\]  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[StylesheetSettings]
# ein leeres "CSSFileList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CSSFileList[]
*/ ?>
```

## FrontendCSSFileList

> Same as above for frontend designs  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[StylesheetSettings]
# ein leeres "FrontendCSSFileList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FrontendCSSFileList[]
*/ ?>
```

## BackendCSSFileList

> Same as above for backend (admin) designs. The tables, uploads, dialogs, date fields, collapsing menus and  
> sticky toolbars run on Exponential UI (extension expui adds exp::\* to this list). admin3 loads its responsive.css  
> itself (page\_head\_style.tpl); the list used to name it for every admin design.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[StylesheetSettings]
# ein leeres "BackendCSSFileList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
BackendCSSFileList[]
*/ ?>
```

## AdminComponentCSSFileList

> The administration's shared component classes (.a4-\*) and its module stylesheets, loaded by the admin, admin3  
> and admin4 designs after their own stylesheets and before BackendCSSFileList, so the packer bundles and caches  
> them with the rest and an extension still has the last word. Every module stylesheet is listed here by name:  
> one bundle for every admin page, cached once. The admin's templates carry no CSS of their own (no <style>, no  
> style=""); see doc/eng-us/bc/6.0/admin4-styling.md. An extension adds its file to BackendCSSFileList, not here.  

Standard: eine Liste mit 7: `components.css`, `modules/setup.css`, `modules/content.css`, `modules/people.css`, `modules/structure.css`, `modules/workflow.css`, `modules/help.css`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/design.ini.append.php
[StylesheetSettings]
# ein leeres "AdminComponentCSSFileList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdminComponentCSSFileList[]
AdminComponentCSSFileList[]=components.css
AdminComponentCSSFileList[]=modules/setup.css
AdminComponentCSSFileList[]=modules/content.css
AdminComponentCSSFileList[]=modules/people.css
AdminComponentCSSFileList[]=modules/structure.css
AdminComponentCSSFileList[]=modules/workflow.css
AdminComponentCSSFileList[]=modules/help.css
*/ ?>
```

## Siehe auch

- [Alle Blöcke von design.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/design.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
