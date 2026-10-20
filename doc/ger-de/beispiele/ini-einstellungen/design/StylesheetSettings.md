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

> Eine Liste von CSS-Dateien, die immer in das Pagelayout eingebunden werden  
> Erweiterungen können sie befüllen, um Styles bereitzustellen,  
> die nicht in den Standard-Themes sind.  
> VERALTET zugunsten von \[Frontend|Backend\]CSSFileList\[\]  

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

> Wie oben, für Frontend-Designs  

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

> Wie oben, für Backend-Designs (Administration). Die Tabellen, Uploads, Dialoge, Datumsfelder, ausklappbaren Menüs und  
> festen Werkzeugleisten laufen auf Exponential UI (die Erweiterung expui fügt dieser Liste exp::\* hinzu). admin3 lädt seine responsive.css  
> selbst (page\_head\_style.tpl); früher nannte die Liste sie für jedes Administrations-Design.  

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

> Die gemeinsamen Komponentenklassen der Administration (.a4-\*) und ihre Modul-Stylesheets, von den Designs admin, admin3  
> und admin4 nach ihren eigenen Stylesheets und vor BackendCSSFileList geladen, sodass der Packer sie  
> mit dem Rest bündelt und cacht und eine Erweiterung trotzdem das letzte Wort hat. Jedes Modul-Stylesheet steht hier mit Namen:  
> ein Bündel für jede Administrationsseite, einmal gecacht. Die Templates der Administration tragen kein eigenes CSS (kein <style>, kein  
> style=""); siehe doc/eng-us/bc/6.0/admin4-styling.md. Eine Erweiterung fügt ihre Datei BackendCSSFileList hinzu, nicht hier.  

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
