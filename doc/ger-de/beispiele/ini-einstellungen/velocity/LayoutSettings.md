---
title: velocity.ini [LayoutSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LayoutSettings] in velocity.ini: ConfDir, StateDir, SiteName"
keywords: Einstellung, velocity.ini, LayoutSettings, INI, setting, ConfDir, StateDir, SiteName
type: generated
generated: exp:help:examples
---
# velocity.ini [LayoutSettings]

Der Block `[LayoutSettings]` von `settings/velocity.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ConfDir

> Where the server's configuration lives, laid out like Debian's /etc/apache2  
> (see doc/eng-us/bc/6.0/velocity-ondisk-layout.md):  
>   vc.conf, ports.conf, envvars  
>   mods-available/  -> mods-enabled/   engine modules  
>   conf-available/  -> conf-enabled/   shared snippets  
>   sites-available/ -> sites-enabled/  one file per installation  
> Written from these settings on every start and used when, merged the way  
> the server merges, it is exactly the configuration generated -- otherwise  
> the single file var/tmp/velocity-server.json is used, as before.  
>
> auto      /etc/vc if it exists or can be created, else an existing  
>           /etc/qbix, else var/vc/qbix/etc in this installation (default)  
> disabled  the single file only  
> <path>    that directory  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LayoutSettings]
ConfDir=auto
*/ ?>
```

## StateDir

> Extended information about each site (root, paths, when it was migrated):  
> /var/lib/vc when the configuration is in /etc/vc, else var/vc/qbix/lib.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LayoutSettings]
StateDir=auto
*/ ?>
```

## SiteName

> This installation's name in sites-available; default: the host of  
> site.ini \[SiteSettings\] SiteURL.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[LayoutSettings]
SiteName=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
