---
title: package.ini [LicenseSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LicenseSettings] in package.ini: DefaultLicense, GroupList, LicenseList, AliasList"
keywords: Einstellung, package.ini, LicenseSettings, INI, setting, DefaultLicense, GroupList, LicenseList, AliasList
type: generated
generated: exp:help:examples
---
# package.ini [LicenseSettings]

Der Block `[LicenseSettings]` von `settings/package.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultLicense

> The licenses a package can be given in the package creation wizards  
> (Setup > Packages > Create new package, every creator's "Package information"  
> step). The license is a strict choice from this list: the step shows a  
> drop-down and refuses any value that is not listed here.  
>
> The value stored in a package's package.xml (<licence>) is the license's  
> identifier. Use the SPDX identifier where one exists (https://spdx.org/licenses/),  
> otherwise an SPDX style "LicenseRef-<name>" identifier.  
>
> The identifier chosen when the step opens. Must be one of LicenseList.  

Standard: `GPL-2.0-or-later`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
DefaultLicense=GPL-2.0-or-later
*/ ?>
```

## GroupList

> The drop-down's groups (<optgroup>), in the order shown. Each group has a  
> \[LicenseGroup\_<group>\] block with Name=, the group's heading. A license whose  
> Group= is not listed here is shown under "Other licenses" at the end.  

Standard: eine Liste mit 9: `software`, `documentation`, `cc-4.0`, `cc-3.0`, `cc-2.5`, `cc-2.0`, `cc-1.0`, `public-domain`, `proprietary`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
# ein leeres "GroupList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupList[]
GroupList[]=software
GroupList[]=documentation
GroupList[]=cc-4.0
GroupList[]=cc-3.0
GroupList[]=cc-2.5
GroupList[]=cc-2.0
GroupList[]=cc-1.0
GroupList[]=public-domain
GroupList[]=proprietary
*/ ?>
```

## LicenseList

> The licenses that can be chosen, in the order shown within their group. Each  
> one has a \[License\_<identifier>\] block:  
>   Name=         the license's official name (shown as it is, not translated)  
>   URL=          the official page of the license  
>   Group=        the group it is listed under (see GroupList)  
>   Description=  optional, a short text shown below the drop-down  
>
> To add a license, in settings/override/package.ini.append.php:  
>   \[LicenseSettings\]  
>   LicenseList\[\]=Apache-2.0  
>   \[License\_Apache-2.0\]  
>   Name=Apache License 2.0  
>   URL=https://www.apache.org/licenses/LICENSE-2.0  
>   Group=software  
>
> To replace the whole list, start with an empty LicenseList\[\] line in the  
> override and list only the licenses you want (and set DefaultLicense to one  
> of them).  

Standard: eine Liste mit 46: `GPL-2.0-or-later`, `GPL-2.0-only`, `GPL-3.0-or-later`, `GPL-3.0-only`, `LGPL-2.1-or-later`, `LGPL-2.1-only`, `LGPL-3.0-or-later`, `LGPL-3.0-only`, `AGPL-3.0-or-later`, `AGPL-3.0-only`, `MIT`, `GFDL-1.3-or-later` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
# ein leeres "LicenseList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LicenseList[]
LicenseList[]=GPL-2.0-or-later
LicenseList[]=GPL-2.0-only
LicenseList[]=GPL-3.0-or-later
LicenseList[]=GPL-3.0-only
LicenseList[]=LGPL-2.1-or-later
LicenseList[]=LGPL-2.1-only
LicenseList[]=LGPL-3.0-or-later
LicenseList[]=LGPL-3.0-only
LicenseList[]=AGPL-3.0-or-later
LicenseList[]=AGPL-3.0-only
LicenseList[]=MIT
LicenseList[]=GFDL-1.3-or-later
LicenseList[]=GFDL-1.3-only
LicenseList[]=CC-BY-4.0
LicenseList[]=CC-BY-SA-4.0
LicenseList[]=CC-BY-ND-4.0
LicenseList[]=CC-BY-NC-4.0
LicenseList[]=CC-BY-NC-SA-4.0
LicenseList[]=CC-BY-NC-ND-4.0
LicenseList[]=CC-BY-3.0
# ... 26 weitere in der ausgelieferten Datei
*/ ?>
```

## AliasList

> Other names a stored license is known by, mapped to a LicenseList identifier.  
> Packages made before the license became a choice store "GPL", which always  
> meant the GNU GPL version 2 or (at your option) any later version, the license  
> of Exponential itself. An alias is shown as the license it stands for, and a  
> posted alias is stored as that license's identifier.  

Standard: ein Hash mit 1: `GPL=GPL-2.0-or-later`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
# ein leeres "AliasList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AliasList[]
AliasList[GPL]=GPL-2.0-or-later
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
