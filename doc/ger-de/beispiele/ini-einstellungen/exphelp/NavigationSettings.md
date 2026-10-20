---
title: exphelp.ini [NavigationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [NavigationSettings] in exphelp.ini: FoldedFolders"
keywords: Einstellung, exphelp.ini, NavigationSettings, INI, setting, FoldedFolders
type: generated
generated: exp:help:examples
---
# exphelp.ini [NavigationSettings]

Der Block `[NavigationSettings]` von `settings/exphelp.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## FoldedFolders

> Folder names in the navigation tree, <page id of the folder, its path below doc/eng-us/> => <name> (else the title of the folder's README)  
> FolderNames\[examples/cookbook\]=Cookbook  
> Folded folders: the tree shows their overview link and page count, and lists their pages (and those of the  
> folders below them) only while a page inside is open. For the generated reference examples, which would otherwise  
> add thousands of links to every page of the help. Paths below doc/eng-us/; a folder without a README is never folded.  
> The government task pages: six groups of step-by-step tasks below doc/eng-us/help/government/tasks/  

Standard: eine Liste mit 12: `examples/template-operators`, `examples/template-functions`, `examples/fetch-functions`, `examples/module-views`, `examples/policies`, `examples/ini-settings`, `examples/datatypes`, `examples/workflow-events`, `examples/cronjobs`, `examples/commands`, `examples/extension-points`, `help/government/tasks`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[NavigationSettings]
# ein leeres "FoldedFolders[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FoldedFolders[]
FoldedFolders[]=examples/template-operators
FoldedFolders[]=examples/template-functions
FoldedFolders[]=examples/fetch-functions
FoldedFolders[]=examples/module-views
FoldedFolders[]=examples/policies
FoldedFolders[]=examples/ini-settings
FoldedFolders[]=examples/datatypes
FoldedFolders[]=examples/workflow-events
FoldedFolders[]=examples/cronjobs
FoldedFolders[]=examples/commands
FoldedFolders[]=examples/extension-points
FoldedFolders[]=help/government/tasks
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
