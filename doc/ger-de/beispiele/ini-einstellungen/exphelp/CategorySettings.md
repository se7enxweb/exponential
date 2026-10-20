---
title: exphelp.ini [CategorySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CategorySettings] in exphelp.ini: Categories, CategoryNames, CategoryIcons, PathRules, TypeDefaults"
keywords: Einstellung, exphelp.ini, CategorySettings, INI, setting, Categories, CategoryNames, CategoryIcons, PathRules, TypeDefaults
type: generated
generated: exp:help:examples
---
# exphelp.ini [CategorySettings]

Der Block `[CategorySettings]` von `settings/exphelp.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Categories

> Die Bereiche der Hilfe, in der Reihenfolge des Dashboards. Die Kategorie einer Seite: ihr Front Matter (category: editing), sonst die  
> erste passende Regel aus PathRules\[\], sonst TypeDefaults\[<type>\]  

Standard: eine Liste mit 45: `getting-started`, `manual`, `editing`, `media`, `publishing`, `search`, `languages`, `account`, `layouts`, `build-two-ways`, `shortcuts`, `support` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CategorySettings]
# ein leeres "Categories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Categories[]
Categories[]=getting-started
Categories[]=manual
Categories[]=editing
Categories[]=media
Categories[]=publishing
Categories[]=search
Categories[]=languages
Categories[]=account
Categories[]=layouts
Categories[]=build-two-ways
Categories[]=shortcuts
Categories[]=support
Categories[]=leadership
Categories[]=business
Categories[]=partnership
Categories[]=advocacy
Categories[]=theme-design
Categories[]=templates
Categories[]=extension-development
Categories[]=theme-extensions
# ... 25 weitere in der ausgelieferten Datei
*/ ?>
```

## CategoryNames

Standard: ein Hash mit 45: `getting-started=Getting started`, `manual=User manual`, `editing=Editing content`, `media=Images and media`, `publishing=Publishing`, `search=Search`, `languages=Languages`, `account=Your account`, `layouts=Layouts`, `build-two-ways=Two ways to build`, `shortcuts=Keyboard shortcuts`, `support=Help and support` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CategorySettings]
# ein leeres "CategoryNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CategoryNames[]
CategoryNames[getting-started]=Getting started
CategoryNames[manual]=User manual
CategoryNames[editing]=Editing content
CategoryNames[media]=Images and media
CategoryNames[publishing]=Publishing
CategoryNames[search]=Search
CategoryNames[languages]=Languages
CategoryNames[account]=Your account
CategoryNames[layouts]=Layouts
CategoryNames[build-two-ways]=Two ways to build
CategoryNames[shortcuts]=Keyboard shortcuts
CategoryNames[support]=Help and support
CategoryNames[leadership]=Project leadership
CategoryNames[business]=Sales and marketing
CategoryNames[partnership]=Partnership
CategoryNames[advocacy]=Advocacy and community
CategoryNames[theme-design]=Theme design
CategoryNames[templates]=Templates
CategoryNames[extension-development]=Extension development
CategoryNames[theme-extensions]=Theme extensions
# ... 25 weitere in der ausgelieferten Datei
*/ ?>
```

## CategoryIcons

> Symbolnamen; jeder muss eine Klasse .a4-bi-<name> in stylesheets/components.css haben  

Standard: ein Hash mit 45: `getting-started=signpost-split`, `manual=question-circle`, `editing=pencil-square`, `media=images`, `publishing=journal-check`, `search=search`, `languages=translate`, `account=person-lock`, `layouts=layout-sidebar-inset`, `build-two-ways=signpost-split`, `shortcuts=input-cursor-text`, `support=info-circle` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CategorySettings]
# ein leeres "CategoryIcons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CategoryIcons[]
CategoryIcons[getting-started]=signpost-split
CategoryIcons[manual]=question-circle
CategoryIcons[editing]=pencil-square
CategoryIcons[media]=images
CategoryIcons[publishing]=journal-check
CategoryIcons[search]=search
CategoryIcons[languages]=translate
CategoryIcons[account]=person-lock
CategoryIcons[layouts]=layout-sidebar-inset
CategoryIcons[build-two-ways]=signpost-split
CategoryIcons[shortcuts]=input-cursor-text
CategoryIcons[support]=info-circle
CategoryIcons[leadership]=diagram-3
CategoryIcons[business]=graph-up-arrow
CategoryIcons[partnership]=building
CategoryIcons[advocacy]=people
CategoryIcons[theme-design]=palette
CategoryIcons[templates]=file-earmark-code
CategoryIcons[extension-development]=puzzle
CategoryIcons[theme-extensions]=puzzle
# ... 25 weitere in der ausgelieferten Datei
*/ ?>
```

## PathRules

> <pattern>;<category>, der erste Treffer gewinnt  

Standard: eine Liste mit 80: `doc/eng-us/help/manual/*;manual`, `doc/eng-us/help/leadership/*;leadership`, `doc/eng-us/help/business/*;business`, `doc/eng-us/help/partners/*;partnership`, `doc/eng-us/help/advocates/*;advocacy`, `doc/eng-us/help/designers/templates/*;templates`, `doc/eng-us/help/designers/*;theme-design`, `doc/eng-us/help/hosting/*;hosting`, `doc/eng-us/help/creators/app-creators/*;app-creation`, `doc/eng-us/help/creators/website-creators/*;website-creation`, `doc/eng-us/help/creators/*;website-creation`, `doc/eng-us/help/supporters/*;supporting` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CategorySettings]
# ein leeres "PathRules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PathRules[]
PathRules[]=doc/eng-us/help/manual/*;manual
PathRules[]=doc/eng-us/help/leadership/*;leadership
PathRules[]=doc/eng-us/help/business/*;business
PathRules[]=doc/eng-us/help/partners/*;partnership
PathRules[]=doc/eng-us/help/advocates/*;advocacy
PathRules[]=doc/eng-us/help/designers/templates/*;templates
PathRules[]=doc/eng-us/help/designers/*;theme-design
PathRules[]=doc/eng-us/help/hosting/*;hosting
PathRules[]=doc/eng-us/help/creators/app-creators/*;app-creation
PathRules[]=doc/eng-us/help/creators/website-creators/*;website-creation
PathRules[]=doc/eng-us/help/creators/*;website-creation
PathRules[]=doc/eng-us/help/supporters/*;supporting
PathRules[]=doc/eng-us/help/sponsors/*;sponsorship
PathRules[]=doc/eng-us/help/customers/*;customer-success
PathRules[]=doc/eng-us/help/government/*;public-sector
PathRules[]=doc/eng-us/help/businesses/*;business-solutions
PathRules[]=doc/eng-us/help/press/*;press-media
PathRules[]=doc/eng-us/help/trainers/*;training
PathRules[]=doc/eng-us/help/project/*;project
PathRules[]=doc/eng-us/help/build-two-ways/*;build-two-ways
# ... 60 weitere in der ausgelieferten Datei
*/ ?>
```

## TypeDefaults

> <type> => <category>, wenn sonst nichts etwas sagt  

Standard: ein Hash mit 14: `help=getting-started`, `guide=administration`, `feature=features`, `bc=install-upgrade`, `install=install-upgrade`, `changelog=release-notes`, `specification=development`, `history=history`, `example=examples`, `generated=examples`, `reference=reference`, `project=project-files` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[CategorySettings]
# ein leeres "TypeDefaults[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TypeDefaults[]
TypeDefaults[help]=getting-started
TypeDefaults[guide]=administration
TypeDefaults[feature]=features
TypeDefaults[bc]=install-upgrade
TypeDefaults[install]=install-upgrade
TypeDefaults[changelog]=release-notes
TypeDefaults[specification]=development
TypeDefaults[history]=history
TypeDefaults[example]=examples
TypeDefaults[generated]=examples
TypeDefaults[reference]=reference
TypeDefaults[project]=project-files
TypeDefaults[catalogue]=standards
TypeDefaults[extension]=extensions
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
