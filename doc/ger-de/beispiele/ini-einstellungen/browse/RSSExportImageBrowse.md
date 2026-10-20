---
title: browse.ini [RSSExportImageBrowse]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RSSExportImageBrowse] in browse.ini: StartNode, SelectionType, ReturnType, Class, TopLevelNodes"
keywords: Einstellung, browse.ini, RSSExportImageBrowse, INI, setting, StartNode, SelectionType, ReturnType, Class, TopLevelNodes
type: generated
generated: exp:help:examples
---
# browse.ini [RSSExportImageBrowse]

Der Block `[RSSExportImageBrowse]` von `settings/browse.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/browse.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/browse.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/browse.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/browse.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StartNode

Standard: `media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[RSSExportImageBrowse]
StartNode=media
*/ ?>
```

## SelectionType

Standard: `single`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[RSSExportImageBrowse]
SelectionType=single
*/ ?>
```

## ReturnType

Standard: `NodeID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[RSSExportImageBrowse]
ReturnType=NodeID
*/ ?>
```

## Class

Standard: eine Liste mit 2: ``, `image`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[RSSExportImageBrowse]
# ein leeres "Class[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Class[]
Class[]=
Class[]=image
*/ ?>
```

## TopLevelNodes

Standard: eine Liste mit 2: `content`, `media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[RSSExportImageBrowse]
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
