---
title: bookmark.ini [ViewBookmarkSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ViewBookmarkSettings] in bookmark.ini: NonBookmarkableViews, QueryParameters, DefaultIcon, ModuleIcons"
keywords: Einstellung, bookmark.ini, ViewBookmarkSettings, INI, setting, NonBookmarkableViews, QueryParameters, DefaultIcon, ModuleIcons
type: generated
generated: exp:help:examples
---
# bookmark.ini [ViewBookmarkSettings]

Der Block `[ViewBookmarkSettings]` von `settings/bookmark.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/bookmark.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/bookmark.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/bookmark.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/bookmark.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## NonBookmarkableViews

> Modulansichten, die nie gemerkt werden können, als Modul/Ansicht, oder Modul/\* für jede Ansicht eines Moduls.  
> Das sind die Ansichten, die nur ein abgeschicktes Formular verarbeiten (ein Lesezeichen darauf würde eine leere oder  
> fehlerhafte Seite öffnen oder eine Aktion wiederholen), die Ansichten eines Moments (anmelden, abmelden, eine Bestätigung, ein Link, der  
> einmal benutzt wird) und die Bearbeitungsansichten (ein Entwurf ist keine Seite, zu der man zurückkehrt).  
> Eine Moduldefinition kann dasselbe von einer Ansicht sagen, mit 'bookmarkable' => false in ihrem Eintrag in $ViewList.  

Standard: eine Liste mit 40: `content/action`, `content/edit`, `content/removeobject`, `content/removeassignment`, `content/removeeditversion`, `content/copy`, `content/copysubtree`, `content/collectedinfo`, `content/browse`, `content/upload`, `content/download`, `content/restore` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/bookmark.ini.append.php
[ViewBookmarkSettings]
# ein leeres "NonBookmarkableViews[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
NonBookmarkableViews[]
NonBookmarkableViews[]=content/action
NonBookmarkableViews[]=content/edit
NonBookmarkableViews[]=content/removeobject
NonBookmarkableViews[]=content/removeassignment
NonBookmarkableViews[]=content/removeeditversion
NonBookmarkableViews[]=content/copy
NonBookmarkableViews[]=content/copysubtree
NonBookmarkableViews[]=content/collectedinfo
NonBookmarkableViews[]=content/browse
NonBookmarkableViews[]=content/upload
NonBookmarkableViews[]=content/download
NonBookmarkableViews[]=content/restore
NonBookmarkableViews[]=content/pdf
NonBookmarkableViews[]=content/keyword
NonBookmarkableViews[]=user/login
NonBookmarkableViews[]=user/logout
NonBookmarkableViews[]=user/activate
NonBookmarkableViews[]=user/forgotpassword
NonBookmarkableViews[]=user/register
NonBookmarkableViews[]=user/success
# ... 20 weitere in der ausgelieferten Datei
*/ ?>
```

## QueryParameters

> Die Schlüssel des Query-Strings, die eine Ansicht in ihrem Lesezeichen behält, als Modul/Ansicht=Schlüssel;Schlüssel. Jeder andere Schlüssel und der Query-String  
> jeder anderen Ansicht werden verworfen: Eine Modulansicht nimmt ihre Parameter in ihrer Adresse  
> (/(offset)/20, /(folder)/top), und ein Query-String trägt meist den Zustand eines Moments.  
> Ein Schlüssel behält einen einzelnen Wert von höchstens 255 Zeichen; die Schlüssel werden sortiert gespeichert, so ist dieselbe Suche  
> ein Lesezeichen, wie auch immer ihr Formular sie angeordnet hat.  

Standard: ein Hash mit 1: `content/search=SearchText;SectionID;SubTreeArray`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/bookmark.ini.append.php
[ViewBookmarkSettings]
# ein leeres "QueryParameters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
QueryParameters[]
QueryParameters[content/search]=SearchText;SectionID;SubTreeArray
*/ ?>
```

## DefaultIcon

> Das Symbol eines Lesezeichens einer Systemseite nach ihrem Modul: der Name eines Symbols der Administrations-Designs  
> (a4-bi-<name>). Ein Modul, das nicht aufgeführt ist, bekommt DefaultIcon.  

Standard: `gear`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/bookmark.ini.append.php
[ViewBookmarkSettings]
DefaultIcon=gear
*/ ?>
```

## ModuleIcons

Standard: ein Hash mit 25: `setup=tools`, `content=files`, `user=person`, `role=shield-lock`, `section=collection`, `class=diagram-3`, `state=toggles`, `workflow=diagram-2`, `trigger=lightning`, `url=link-45deg`, `shop=piggy-bank`, `package=box-seam` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/bookmark.ini.append.php
[ViewBookmarkSettings]
# ein leeres "ModuleIcons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ModuleIcons[]
ModuleIcons[setup]=tools
ModuleIcons[content]=files
ModuleIcons[user]=person
ModuleIcons[role]=shield-lock
ModuleIcons[section]=collection
ModuleIcons[class]=diagram-3
ModuleIcons[state]=toggles
ModuleIcons[workflow]=diagram-2
ModuleIcons[trigger]=lightning
ModuleIcons[url]=link-45deg
ModuleIcons[shop]=piggy-bank
ModuleIcons[package]=box-seam
ModuleIcons[settings]=sliders
ModuleIcons[notification]=bell
ModuleIcons[rss]=rss
ModuleIcons[audit]=journal-check
ModuleIcons[exphelp]=question-circle
ModuleIcons[git_manager]=git
ModuleIcons[visual]=palette
ModuleIcons[collaboration]=chat-left-dots
# ... 5 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von bookmark.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/bookmark.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
