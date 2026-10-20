---
title: querycache.ini [QueryCacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [QueryCacheSettings] in querycache.ini: Mode, Storage, ApcuReserve, MaxAge, MaxRows, ExcludeTables"
keywords: Einstellung, querycache.ini, QueryCacheSettings, INI, setting, Mode, Storage, ApcuReserve, MaxAge, MaxRows, ExcludeTables
type: generated
generated: exp:help:examples
---
# querycache.ini [QueryCacheSettings]

Der Block `[QueryCacheSettings]` von `settings/querycache.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/querycache.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/querycache.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/querycache.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/querycache.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Mode

> off      nothing is kept (the default)  
> request  a memo for the length of one request: an identical SELECT is not  
>          run twice in the same request  
> shared   the memo, and results kept across requests (see Storage); any  
>          other process (scripts, the admin host, Velocity) invalidates  
>          through the shared state file in var/<site>/cache/querycache/  

Standard: `off`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
Mode=off
*/ ?>
```

## Storage

> Where shared mode keeps results across requests:  
> auto  APCu where this PHP process has it, else files (the default); on the  
>       command line (cronjobs, scripts) without APCu only the request's memo  
> apcu  APCu only; without APCu shared works as request  
> file  files in var/<site>/cache/querycache/entries/, one per result, even  
>       with APCu: every web server that mounts var/ (a cluster on NFS or a  
>       SAN) shares them. The cronjob part cache\_cleanup  
>       (cronjobs/querycache\_cleanup.php) removes the files older than MaxAge;  
>       clearing the cache removes them too.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
Storage=auto
*/ ?>
```

## ApcuReserve

> APCu is one memory for every site of the PHP-FPM pool. Full with valid  
> entries, it empties itself completely: every other cache and counter in it  
> is lost. With less than this share of APCu free (percent), shared mode stops  
> adding results to APCu and keeps them for the request only, until entries  
> expire (below the reserve the cache deletes its expired entries from APCu,  
> once a minute for the pool). 0 never stops. Only with the APCu storage.  

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
ApcuReserve=25
*/ ?>
```

## MaxAge

> Longest life of a result even when no write touched its tables, in seconds:  
> the bound for a write made outside Exponential (another program, a hand-run  
> statement), which the cache cannot see.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
MaxAge=300
*/ ?>
```

## MaxRows

> Results with more rows are not kept.  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
MaxRows=5000
*/ ?>
```

## ExcludeTables

> Tables whose SELECTs are never cached.  

Standard: eine Liste mit 3: `ezsession`, `ezpending_actions`, `ezcollab_notification_rule`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/querycache.ini.append.php
[QueryCacheSettings]
# ein leeres "ExcludeTables[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExcludeTables[]
ExcludeTables[]=ezsession
ExcludeTables[]=ezpending_actions
ExcludeTables[]=ezcollab_notification_rule
*/ ?>
```

## Siehe auch

- [Alle Blöcke von querycache.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/querycache.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
