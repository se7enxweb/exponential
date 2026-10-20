---
title: exphelp.ini [LandingSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LandingSettings] in exphelp.ini: Enabled, Blocks, StartLimit, BoxLimit, KeyLimit, LatestLimit, ExcludeTypes"
keywords: Einstellung, exphelp.ini, LandingSettings, INI, setting, Enabled, Blocks, StartLimit, BoxLimit, KeyLimit, LatestLimit, ExcludeTypes
type: generated
generated: exp:help:examples
---
# exphelp.ini [LandingSettings]

Der Block `[LandingSettings]` von `settings/exphelp.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> exphelp/audience/<audience>: a landing page per audience (expHelpLanding, doc/eng-us/features/6.0/help-system.md "Landing  
> pages"). enabled: the dashboard's audience cards open it; disabled: the view is a 404 and the cards open the search.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[LandingSettings]
Enabled=enabled
*/ ?>
```

## Blocks

> The blocks of every landing page, in order. \[Landing\_<audience>\] Blocks\[\] replaces the list for one audience.  
>   hero: name, summary, a search box and buttons    start: the reading path ("start here")  
>   boxes: info boxes, pages with their summaries    categories: a search button per category  
>   key: the key pages                               latest: recently updated pages for the audience  
>   related: related audiences                       commits: the latest commits of the project (\[CommitFeedSettings\])  
>   support: the Support Exponential block (\[SupportSettings\])  

Standard: eine Liste mit 7: `hero`, `start`, `boxes`, `categories`, `key`, `latest`, `related`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[LandingSettings]
# ein leeres "Blocks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Blocks[]
Blocks[]=hero
Blocks[]=start
Blocks[]=boxes
Blocks[]=categories
Blocks[]=key
Blocks[]=latest
Blocks[]=related
*/ ?>
```

## StartLimit

> How many pages a list shows when the index fills it (a list given in \[Landing\_<audience>\] is shown as it is)  

Standard: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[LandingSettings]
StartLimit=6
*/ ?>
```

## BoxLimit

Standard: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[LandingSettings]
BoxLimit=6
*/ ?>
```

## KeyLimit

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[LandingSettings]
KeyLimit=10
*/ ?>
```

## LatestLimit

Standard: `8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[LandingSettings]
LatestLimit=8
*/ ?>
```

## ExcludeTypes

> Page types the automatic lists never pick (they are reached through the search and the categories)  

Standard: eine Liste mit 5: `changelog`, `history`, `bc`, `specification`, `catalogue`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[LandingSettings]
# ein leeres "ExcludeTypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExcludeTypes[]
ExcludeTypes[]=changelog
ExcludeTypes[]=history
ExcludeTypes[]=bc
ExcludeTypes[]=specification
ExcludeTypes[]=catalogue
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
