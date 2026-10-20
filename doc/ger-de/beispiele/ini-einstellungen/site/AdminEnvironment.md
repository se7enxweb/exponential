---
title: site.ini [AdminEnvironment]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdminEnvironment] in site.ini: Level, Name, ShortName, Release, ShowVersion, ShowInTitle, BadgeDisplay"
keywords: Einstellung, site.ini, AdminEnvironment, INI, setting, Level, Name, ShortName, Release, ShowVersion, ShowInTitle, BadgeDisplay
type: generated
generated: exp:help:examples
---
# site.ini [AdminEnvironment]

Der Block `[AdminEnvironment]` von `settings/site.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Level

> The environment badge of the administration (designs admin4 and admin4l): the  
> name of this installation's environment in the header, on the sign-in page and  
> before the page title, so a live site is not taken for a test copy.  
> Set it per environment, for example in the variant files of EXP\_ENV  
> (settings/override/site.ini.prod.append.php, see config.env.php.example).  
> The header's cache-blocks are keyed by the badge: a changed setting shows on  
> the next request (clear the INI cache where EZP\_INI\_FILEMTIME\_CHECK is false).  
>
> development, test, staging or production: the color and the default name.  
> auto takes the level from EXP\_ENV: dev and development, test, stage and  
> staging, prod, production and live. Empty (the default): no badge.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
Level=
*/ ?>
```

## Name

> The badge's text; empty takes the level's name (Development, Test, Staging,  
> Production, translated)  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
Name=
*/ ?>
```

## ShortName

> The text where the header is narrow (up to 767px, phones too) with  
> BadgeDisplay=always or on touch screens; empty takes the level's code (DEV, TEST, STAGE, PROD).  
> The name is read out.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
ShortName=
*/ ?>
```

## Release

> Free text after the name, for example a release or build; shown to signed-in  
> users only, not on the sign-in page  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
Release=
*/ ?>
```

## ShowVersion

> enabled adds the Exponential version after the release (signed-in users only)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
ShowVersion=disabled
*/ ?>
```

## ShowInTitle

> The name before the page title: "\[PRODUCTION\] Dashboard - Site"; disabled  
> leaves the title as it is  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
ShowInTitle=enabled
*/ ?>
```

## BadgeDisplay

> hover (the default): the badge shows only while a mouse or trackpad pointer  
> rests on the logo, or the logo has the keyboard focus (Escape hides it); on  
> touch screens, which cannot hover, beside the logo as with always. Screen  
> readers read it with the logo link. always: beside the logo  
> at all times  

Standard: `hover`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminEnvironment]
BadgeDisplay=hover
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
