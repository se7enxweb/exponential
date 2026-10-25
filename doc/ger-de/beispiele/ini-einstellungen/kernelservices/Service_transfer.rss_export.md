---
title: kernelservices.ini [Service_transfer.rss_export]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_transfer.rss_export] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description, Name, Direction ..."
keywords: Einstellung, kernelservices.ini, Service_transfer.rss_export, INI, setting, Class, Factory, Arguments, Scope, Tags, Description, Name, Direction, Scopes, Formats, EntryURL, Permission, HelpPage, RoundTrip, Keywords, Weight
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.rss_export]

Der Block `[Service_transfer.rss_export]` von `settings/kernelservices.ini`, mit 16 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `expKernelTransferTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Class=expKernelTransferTool
*/ ?>
```

## Factory

Standard: `expKernelTransferTool::fromSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Factory=expKernelTransferTool::fromSettings
*/ ?>
```

## Arguments

Standard: eine Liste mit 1: `transfer.rss_export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# ein leeres "Arguments[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Arguments[]
Arguments[]=transfer.rss_export
*/ ?>
```

## Scope

Standard: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Scope=worker
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=transfer.tool
*/ ?>
```

## Description

Standard: `Publish the newest items of a folder as an RSS or Atom feed, or a list of feeds as OPML.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Description=Publish the newest items of a folder as an RSS or Atom feed, or a list of feeds as OPML.
*/ ?>
```

## Name

Standard: `RSS export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Name=RSS export
*/ ?>
```

## Direction

Standard: `export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Direction=export
*/ ?>
```

## Scopes

Standard: eine Liste mit 1: `subtree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# ein leeres "Scopes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scopes[]
Scopes[]=subtree
*/ ?>
```

## Formats

Standard: eine Liste mit 3: `RSS`, `Atom`, `OPML`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# ein leeres "Formats[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Formats[]
Formats[]=RSS
Formats[]=Atom
Formats[]=OPML
*/ ?>
```

## EntryURL

Standard: `rss/list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
EntryURL=rss/list
*/ ?>
```

## Permission

Standard: `rss/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Permission=rss/edit
*/ ?>
```

## HelpPage

Standard: `guides/rss-feeds.md#3-an-export-card`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
HelpPage=guides/rss-feeds.md#3-an-export-card
*/ ?>
```

## RoundTrip

Standard: eine Liste mit 1: `transfer.rss_import`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# ein leeres "RoundTrip[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RoundTrip[]
RoundTrip[]=transfer.rss_import
*/ ?>
```

## Keywords

Standard: eine Liste mit 4: `feed`, `newsletter feed`, `subscribe`, `abonnieren`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
# ein leeres "Keywords[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Keywords[]
Keywords[]=feed
Keywords[]=newsletter feed
Keywords[]=subscribe
Keywords[]=abonnieren
*/ ?>
```

## Weight

Standard: `61`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_export]
Weight=61
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
