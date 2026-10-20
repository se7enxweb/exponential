---
title: browse.ini [RSSOPMLNodeBrowse]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RSSOPMLNodeBrowse] in browse.ini: StartNode, SelectionType, ReturnType, TopLevelNodes"
keywords: Einstellung, browse.ini, RSSOPMLNodeBrowse, INI, setting, StartNode, SelectionType, ReturnType, TopLevelNodes
type: generated
generated: exp:help:examples
---
# browse.ini [RSSOPMLNodeBrowse]

Der Block `[RSSOPMLNodeBrowse]` von `settings/browse.ini`, mit 4 Einstellung(en).

> Action used to point an OPML outline at a content node instead of a feed,  
> from the Browse content button on rss/edit\_export. Without this section  
> eZContentBrowse::browse() has no StartNode to read, so content/browse opens  
> on nothing and answers 404 - the button appears to do nothing at all.  
> Same shape as RSSObjectBrowse: one node, returned as a node id.  

Bearbeiten Sie `settings/browse.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/browse.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/browse.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/browse.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartNode

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[RSSOPMLNodeBrowse]
StartNode=content
*/ ?>
```

## SelectionType

Standard: `single`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[RSSOPMLNodeBrowse]
SelectionType=single
*/ ?>
```

## ReturnType

Standard: `NodeID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[RSSOPMLNodeBrowse]
ReturnType=NodeID
*/ ?>
```

## TopLevelNodes

Standard: eine Liste mit 2: `content`, `media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[RSSOPMLNodeBrowse]
# ein leeres "TopLevelNodes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TopLevelNodes[]
TopLevelNodes[]=content
TopLevelNodes[]=media
*/ ?>
```

## Siehe auch

- [Alle Blöcke von browse.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/browse.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
