---
title: kernelservices.ini [Service_transfer.package]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_transfer.package] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description, Name, Direction ..."
keywords: Einstellung, kernelservices.ini, Service_transfer.package, INI, setting, Class, Factory, Arguments, Scope, Tags, Description, Name, Direction, Scopes, Formats, EntryURL, Permission, HelpPage, RoundTrip, Keywords, Actions, Weight
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.package]

Der Block `[Service_transfer.package]` von `settings/kernelservices.ini`, mit 17 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `expKernelTransferTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Class=expKernelTransferTool
*/ ?>
```

## Factory

Standard: `expKernelTransferTool::fromSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Factory=expKernelTransferTool::fromSettings
*/ ?>
```

## Arguments

Standard: eine Liste mit 1: `transfer.package`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# ein leeres "Arguments[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Arguments[]
Arguments[]=transfer.package
*/ ?>
```

## Scope

Standard: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Scope=worker
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=transfer.tool
*/ ?>
```

## Description

Standard: `Move content with its classes, or classes, extensions and designs, to another installation as a package file.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Description=Move content with its classes, or classes, extensions and designs, to another installation as a package file.
*/ ?>
```

## Name

Standard: `Packages`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Name=Packages
*/ ?>
```

## Direction

Standard: `both`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Direction=both
*/ ?>
```

## Scopes

Standard: eine Liste mit 4: `site`, `subtree`, `object`, `class`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# ein leeres "Scopes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scopes[]
Scopes[]=site
Scopes[]=subtree
Scopes[]=object
Scopes[]=class
*/ ?>
```

## Formats

Standard: eine Liste mit 1: `ezpkg`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# ein leeres "Formats[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Formats[]
Formats[]=ezpkg
*/ ?>
```

## EntryURL

Standard: `package/list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
EntryURL=package/list
*/ ?>
```

## Permission

Standard: `package/list|package/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Permission=package/list|package/read
*/ ?>
```

## HelpPage

Standard: `guides/packages.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
HelpPage=guides/packages.md
*/ ?>
```

## RoundTrip

Standard: eine Liste mit 2: `transfer.package`, `transfer.xrowextract`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# ein leeres "RoundTrip[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RoundTrip[]
RoundTrip[]=transfer.package
RoundTrip[]=transfer.xrowextract
*/ ?>
```

## Keywords

Standard: eine Liste mit 6: `copy to another site`, `move content between installations`, `class definitions`, `install a package`, `Paket`, `auf eine andere Website kopieren`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# ein leeres "Keywords[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Keywords[]
Keywords[]=copy to another site
Keywords[]=move content between installations
Keywords[]=class definitions
Keywords[]=install a package
Keywords[]=Paket
Keywords[]=auf eine andere Website kopieren
*/ ?>
```

## Actions

Standard: eine Liste mit 2: `class|export|Export classes as a package…|package/create/(creator)/ezcontentclass|package/create`, `setup|import|Upload and install a package…|package/upload|package/import`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
# ein leeres "Actions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Actions[]
Actions[]=class|export|Export classes as a package…|package/create/(creator)/ezcontentclass|package/create
Actions[]=setup|import|Upload and install a package…|package/upload|package/import
*/ ?>
```

## Weight

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.package]
Weight=10
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
