---
title: browse.ini [BrowseSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [BrowseSettings] in browse.ini: DefaultSelectionType, DefaultReturnType, DefaultActionName, AliasList, MaximumRecentItems, DefaultTopLevelNodes"
keywords: Einstellung, browse.ini, BrowseSettings, INI, setting, DefaultSelectionType, DefaultReturnType, DefaultActionName, AliasList, MaximumRecentItems, DefaultTopLevelNodes
type: generated
generated: exp:help:examples
---
# browse.ini [BrowseSettings]

Der Block `[BrowseSettings]` von `settings/browse.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/browse.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/browse.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/browse.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/browse.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultSelectionType

Standard: `single`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[BrowseSettings]
DefaultSelectionType=single
*/ ?>
```

## DefaultReturnType

Standard: `NodeID`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[BrowseSettings]
DefaultReturnType=NodeID
*/ ?>
```

## DefaultActionName

Standard: `content`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[BrowseSettings]
DefaultActionName=content
*/ ?>
```

## AliasList

> Eine Zuordnung von Knoten-Aliasen zu Knoten-IDs; hier lassen sich eigene Alias-Namen definieren  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[BrowseSettings]
# ein leeres "AliasList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AliasList[]
*/ ?>
```

## MaximumRecentItems

> AliasList\[images\]=2  
> Der Standardwert für die höchste Zahl angezeigter zuletzt verwendeter Einträge  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[BrowseSettings]
MaximumRecentItems=10
*/ ?>
```

## DefaultTopLevelNodes

> Standardliste der Knoten der obersten Ebene auf der Seite zum Durchsuchen  
> Dies zeigt den Inhalts- und den Benutzerknoten.  

Standard: eine Liste mit 4: `content`, `users`, `media`, `setup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/browse.ini.append.php
[BrowseSettings]
# ein leeres "DefaultTopLevelNodes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DefaultTopLevelNodes[]
DefaultTopLevelNodes[]=content
DefaultTopLevelNodes[]=users
DefaultTopLevelNodes[]=media
DefaultTopLevelNodes[]=setup
*/ ?>
```

## Siehe auch

- [Alle Blöcke von browse.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/browse.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
