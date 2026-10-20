---
title: exphelp.ini [License]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [License] in exphelp.ini: Enabled, Name, ShortName, Version, VersionDate, OrLater, URL, TextURL ..."
keywords: Einstellung, exphelp.ini, License, INI, setting, Enabled, Name, ShortName, Version, VersionDate, OrLater, URL, TextURL, TextPage, NoticePage, ExplainerPage, CopyrightYears, CopyrightHolder, OtherLicensePathRules, OtherCopyright, OtherLicenseName, OtherLicenseURL
type: generated
generated: exp:help:examples
---
# exphelp.ini [License]

Der Block `[License]` von `settings/exphelp.ini`, mit 17 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Die Lizenz der Dokumentation, die die Hilfe zeigt (doc/eng-us/README-LICENSE.md, doc/eng-us/help/free-documentation.md).  
> Jede Hilfeseite, das Dashboard, die Druckansicht und das PDF tragen ihren Hinweis; der Name bleibt unübersetzt.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
Enabled=true
*/ ?>
```

## Name

Standard: `GNU Free Documentation License`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
Name=GNU Free Documentation License
*/ ?>
```

## ShortName

Standard: `GFDL`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
ShortName=GFDL
*/ ?>
```

## Version

Standard: `1.3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
Version=1.3
*/ ?>
```

## VersionDate

Standard: `3 November 2008`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
VersionDate=3 November 2008
*/ ?>
```

## OrLater

> "or any later version published by the Free Software Foundation"  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
OrLater=true
*/ ?>
```

## URL

Standard: `https://www.gnu.org/licenses/fdl-1.3.html`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
URL=https://www.gnu.org/licenses/fdl-1.3.html
*/ ?>
```

## TextURL

Standard: `https://www.gnu.org/licenses/fdl-1.3.txt`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
TextURL=https://www.gnu.org/licenses/fdl-1.3.txt
*/ ?>
```

## TextPage

> Seiten-IDs der Hilfe (Pfade unter doc/eng-us/): der unveränderte Lizenztext, der Urheberrechts- und Erlaubnishinweis  
> und die einfache Erklärung für Leser  

Standard: `LICENSE-GFDL-1.3.txt`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
TextPage=LICENSE-GFDL-1.3.txt
*/ ?>
```

## NoticePage

Standard: `README-LICENSE.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
NoticePage=README-LICENSE.md
*/ ?>
```

## ExplainerPage

Standard: `help/free-documentation.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
ExplainerPage=help/free-documentation.md
*/ ?>
```

## CopyrightYears

Standard: `1998 - 2026`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
CopyrightYears=1998 - 2026
*/ ?>
```

## CopyrightHolder

Standard: `7x & Exponential Foundation and the Exponential contributors`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
CopyrightHolder=7x & Exponential Foundation and the Exponential contributors
*/ ?>
```

## OtherLicensePathRules

> Seiten, deren Urheberrecht das Projekt nicht hält und die es nicht neu lizenziert: die von der  
> ursprünglichen Software geerbte Dokumentation (eZ Systems AS, 1999 - 2014). Sie behalten die Bedingungen der  
> LICENSE des Repositorys und zeigen stattdessen den Hinweis unten. <pattern> auf den Pfad in doc/, "\*" passt auch auf "/".  

Standard: eine Liste mit 9: `doc/eng-us/features/[2-5].*`, `doc/eng-us/bc/[2-5].*`, `doc/eng-us/specifications/[2-5].*`, `doc/eng-us/specifications/trunk/*`, `doc/changelogs/[2-5].*`, `doc/changelogs/README.md`, `doc/eng-us/shop/*`, `doc/eng-us/release-overview`, `doc/eng-us/standards/header.txt`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
# ein leeres "OtherLicensePathRules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
OtherLicensePathRules[]
OtherLicensePathRules[]=doc/eng-us/features/[2-5].*
OtherLicensePathRules[]=doc/eng-us/bc/[2-5].*
OtherLicensePathRules[]=doc/eng-us/specifications/[2-5].*
OtherLicensePathRules[]=doc/eng-us/specifications/trunk/*
OtherLicensePathRules[]=doc/changelogs/[2-5].*
OtherLicensePathRules[]=doc/changelogs/README.md
OtherLicensePathRules[]=doc/eng-us/shop/*
OtherLicensePathRules[]=doc/eng-us/release-overview
OtherLicensePathRules[]=doc/eng-us/standards/header.txt
*/ ?>
```

## OtherCopyright

Standard: `Copyright (C) 1999 - 2014 eZ Systems AS and contributors`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
OtherCopyright=Copyright (C) 1999 - 2014 eZ Systems AS and contributors
*/ ?>
```

## OtherLicenseName

Standard: `GNU General Public License, version 2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
OtherLicenseName=GNU General Public License, version 2
*/ ?>
```

## OtherLicenseURL

Standard: `https://www.gnu.org/licenses/old-licenses/gpl-2.0.html`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[License]
OtherLicenseURL=https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
