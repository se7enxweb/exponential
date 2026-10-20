---
title: content.ini [RSSImportCleanupSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RSSImportCleanupSettings] in content.ini: Enabled, KeepPerFeed, ClassIdentifiers, CleanupUser, LogFile, MoveToTrash"
keywords: Einstellung, content.ini, RSSImportCleanupSettings, INI, setting, Enabled, KeepPerFeed, ClassIdentifiers, CleanupUser, LogFile, MoveToTrash
type: generated
generated: exp:help:examples
---
# content.ini [RSSImportCleanupSettings]

Der Block `[RSSImportCleanupSettings]` von `settings/content.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Trims what the RSS import has created, keeping the newest items of each feed.  
>
> An import run adds an object for every item in the feed and never takes one  
> away, so a site importing a busy feed grows without limit. Run it from the  
> cronjob part 'cleanuprss', or by hand:  
>
>   php bin/php/cleanuprss.php --dry-run  
>
> Nothing happens until Enabled is true AND ClassIdentifiers names at least one  
> class: this removes content, and on an installation that has not been set up  
> for it there is no safe guess about what may go.  
> Off until the site says otherwise.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
Enabled=false
*/ ?>
```

## KeepPerFeed

> How many items to keep per feed, newest first. Must be at least 1.  

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
KeepPerFeed=100
*/ ?>
```

## ClassIdentifiers

> The classes that may be removed. Class identifiers, and nothing outside this  
> list is touched however old it is - so a folder or a page that happens to sit  
> in the destination is safe. Empty means nothing is removed.  
> Example:  
> ClassIdentifiers\[\]=article  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
# ein leeres "ClassIdentifiers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ClassIdentifiers[]
*/ ?>
```

## CleanupUser

> Removing is permission checked, so it runs as somebody who may remove.  

Standard: `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
CleanupUser=admin
*/ ?>
```

## LogFile

> Where the run is recorded. Empty writes no file.  

Standard: `rsscleanup.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
LogFile=rsscleanup.log
*/ ?>
```

## MoveToTrash

> Whether removed items go to the trash, where they can be recovered, or are  
> deleted outright. Trash on a busy feed is itself something to empty.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[RSSImportCleanupSettings]
MoveToTrash=true
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
