---
title: bookmark.ini [BookmarkTypeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [BookmarkTypeSettings] in bookmark.ini: TypeList, TypeClass"
keywords: Einstellung, bookmark.ini, BookmarkTypeSettings, INI, setting, TypeList, TypeClass
type: generated
generated: exp:help:examples
---
# bookmark.ini [BookmarkTypeSettings]

Der Block `[BookmarkTypeSettings]` von `settings/bookmark.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/bookmark.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/bookmark.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/bookmark.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/bookmark.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TypeList

> Die Arten von Lesezeichen für Systemseiten. Jede ist eine Klasse, die expBookmarkType erweitert: Sie sagt, welche  
> Modulansichten sie nimmt (accepts()), welche Schlüssel des Query-Strings ihre Lesezeichen behalten, das Symbol und die Bezeichnung ihrer Lesezeichen.  
> Sie werden in dieser Reihenfolge gefragt; die erste, die eine Ansicht nimmt, entscheidet, und eine Ansicht, die keine Art nimmt, ist eine  
> „Systemseite“ (expBookmarkTypeSystemPage). Eine Erweiterung fügt eine Art mit ihrer eigenen  
> settings/bookmark.ini.append.php hinzu:  
>   \[BookmarkTypeSettings\]  
>   TypeList\[\]=layout  
>   TypeClass\[layout\]=myLayoutBookmarkType  

Standard: eine Liste mit 1: `search`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/bookmark.ini.append.php
[BookmarkTypeSettings]
# ein leeres "TypeList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TypeList[]
TypeList[]=search
*/ ?>
```

## TypeClass

Standard: ein Hash mit 1: `search=expBookmarkTypeSearch`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/bookmark.ini.append.php
[BookmarkTypeSettings]
# ein leeres "TypeClass[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TypeClass[]
TypeClass[search]=expBookmarkTypeSearch
*/ ?>
```

## Siehe auch

- [Alle Blöcke von bookmark.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/bookmark.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
