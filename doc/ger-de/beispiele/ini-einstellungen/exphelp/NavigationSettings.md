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

> Ordnernamen im Navigationsbaum, <Seiten-ID des Ordners, sein Pfad unter doc/eng-us/> => <name> (sonst der Titel der README des Ordners)  
> FolderNames\[examples/cookbook\]=Cookbook  
> Eingeklappte Ordner: Der Baum zeigt ihren Übersichtslink und die Zahl ihrer Seiten und listet ihre Seiten (und die der  
> Ordner darunter) nur auf, solange eine Seite darin geöffnet ist. Für die generierten Referenzbeispiele, die sonst  
> jeder Seite der Hilfe Tausende Links hinzufügen würden. Pfade unter doc/eng-us/; ein Ordner ohne README wird nie eingeklappt.  
> Die Aufgabenseiten für Behörden: sechs Gruppen von Schritt-für-Schritt-Aufgaben unter doc/eng-us/help/government/tasks/  

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
