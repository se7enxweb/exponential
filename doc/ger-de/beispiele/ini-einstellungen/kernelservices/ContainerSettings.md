---
title: kernelservices.ini [ContainerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContainerSettings] in kernelservices.ini: Mode, CacheDir, AutoRebuild, WorkerScope"
keywords: Einstellung, kernelservices.ini, ContainerSettings, INI, setting, Mode, CacheDir, AutoRebuild, WorkerScope
type: generated
generated: exp:help:examples
---
# kernelservices.ini [ContainerSettings]

Der Block `[ContainerSettings]` von `settings/kernelservices.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Mode

> These four are read before the extensions are active: set them here or in  
> settings/override/kernelservices.ini.append.php, not in an extension.  
>
> compiled  the web kernel applies the compiled container in CacheDir: the  
>           extension activation of each siteaccess, recorded once, instead  
>           of looking up every extension directory on every request (the  
>           default). Without a compiled file, or when a request does not  
>           start from exactly the recorded state, it runs as without a  
>           container.  
> dev       nothing compiled is used; services are read from the settings  
>           as they stand (for development)  
> disabled  the container takes no part in the boot; services can still be  
>           asked for, read from the settings  

Standard: `compiled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[ContainerSettings]
Mode=compiled
*/ ?>
```

## CacheDir

> Where the compiled container lives, relative to the installation root:  
> index.php and one <siteaccess>.php per siteaccess.  

Standard: `var/cache/kernelservices`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[ContainerSettings]
CacheDir=var/cache/kernelservices
*/ ?>
```

## AutoRebuild

> enabled   a web request of a siteaccess without a current compiled file  
>           records its own extension activation and writes it, so the next  
>           request uses it (after an INI cache clear from the  
>           administration, or before the first compile)  
> disabled  only exp:services:compile, the deploy and ezcache.php on the  
>           command line write it  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[ContainerSettings]
AutoRebuild=enabled
*/ ?>
```

## WorkerScope

> The worker scope of the settings files: each settings file of a siteaccess  
> is built once per worker and handed out as a copy afterwards. On Exponential  
> Velocity a worker serves thousands of requests, so this saves loading every  
> settings file on every request; on PHP-FPM a worker is one request long.  
> auto      on only when the INI cache does not check file times either  
>           (EZP\_INI\_FILEMTIME\_CHECK false in config.php), so a kept settings  
>           file is exactly what loading it again would give (the default)  
> enabled   always (an edited settings file then reaches a Velocity worker  
>           with the next compile, INI cache clear or restart)  
> disabled  never  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[ContainerSettings]
WorkerScope=auto
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
