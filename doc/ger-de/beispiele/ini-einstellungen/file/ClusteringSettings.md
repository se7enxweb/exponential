---
title: file.ini [ClusteringSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ClusteringSettings] in file.ini: FileHandler, NonExistantStaleCacheHandling"
keywords: Einstellung, file.ini, ClusteringSettings, INI, setting, FileHandler, NonExistantStaleCacheHandling
type: generated
generated: exp:help:examples
---
# file.ini [ClusteringSettings]

Der Block `[ClusteringSettings]` von `settings/file.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/file.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/file.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/file.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/file.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## FileHandler

> Cluster file handler.  
> Since 4.1 name of the filehandlers have changed  
> you may choose between :  
> - eZFSFileHandler  
>   standard file handler  
> - eZDFSFileHandler  
>   distributed filesystem handler with a DB overlay. Required for NFS based  
>   architectures  
> and it is case sensitive  

Standard: `eZFSFileHandler`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[ClusteringSettings]
FileHandler=eZFSFileHandler
*/ ?>
```

## NonExistantStaleCacheHandling

> DEPRECATED  
> If you want to use your own cluster file handler located in an extension  
> then you should add the extension name to the list below.  
> In that case the handler will be searched for in the following directory:  
> extension/<your\_extension>/clusterfilehandlers  
> ExtensionDirectories\[\]  
> Defines what happens when a requested cache file is already being generated  
> and no expired cache file exists (for instance if the content is new)  
> Two possible values:  
>   - wait: places the process in a wait loop for a limited time until the file  
>           is done generating. This is the default value  
>   - generate: let the requesting process generate its own data without storing  
>               the result  
> The key of this array defined the type of cache impacted by the setting. Three  
> cache types are allowed here: viewcache, cacheblock and misc (any cache that is  
> not viewcache nor cacheblock).  

Standard: ein Hash mit 3: `viewcache=wait`, `cacheblock=wait`, `misc=wait`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[ClusteringSettings]
# ein leeres "NonExistantStaleCacheHandling[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
NonExistantStaleCacheHandling[]
NonExistantStaleCacheHandling[viewcache]=wait
NonExistantStaleCacheHandling[cacheblock]=wait
NonExistantStaleCacheHandling[misc]=wait
*/ ?>
```

## Siehe auch

- [Alle Blöcke von file.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/file.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
