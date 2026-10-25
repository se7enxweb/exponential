---
title: kernelservices.ini [Service_transfer.rss_import]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_transfer.rss_import] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description, Name, Direction ..."
keywords: Einstellung, kernelservices.ini, Service_transfer.rss_import, INI, setting, Class, Factory, Arguments, Scope, Tags, Description, Name, Direction, Scopes, Formats, EntryURL, Permission, HelpPage, RoundTrip, Keywords, Weight
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.rss_import]

Der Block `[Service_transfer.rss_import]` von `settings/kernelservices.ini`, mit 16 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `expKernelTransferTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Class=expKernelTransferTool
*/ ?>
```

## Factory

Standard: `expKernelTransferTool::fromSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Factory=expKernelTransferTool::fromSettings
*/ ?>
```

## Arguments

Standard: eine Liste mit 1: `transfer.rss_import`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# ein leeres "Arguments[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Arguments[]
Arguments[]=transfer.rss_import
*/ ?>
```

## Scope

Standard: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Scope=worker
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=transfer.tool
*/ ?>
```

## Description

Standard: `Create content from an RSS or Atom feed of another website, again and again by the rssimport cronjob.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Description=Create content from an RSS or Atom feed of another website, again and again by the rssimport cronjob.
*/ ?>
```

## Name

Standard: `RSS import`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Name=RSS import
*/ ?>
```

## Direction

Standard: `import`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Direction=import
*/ ?>
```

## Scopes

Standard: eine Liste mit 1: `subtree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# ein leeres "Scopes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scopes[]
Scopes[]=subtree
*/ ?>
```

## Formats

Standard: eine Liste mit 2: `RSS`, `Atom`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# ein leeres "Formats[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Formats[]
Formats[]=RSS
Formats[]=Atom
*/ ?>
```

## EntryURL

Standard: `rss/list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
EntryURL=rss/list
*/ ?>
```

## Permission

Standard: `rss/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Permission=rss/edit
*/ ?>
```

## HelpPage

Standard: `guides/rss-feeds.md#4-an-import-card-and-the-cronjob`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
HelpPage=guides/rss-feeds.md#4-an-import-card-and-the-cronjob
*/ ?>
```

## RoundTrip

Standard: eine Liste mit 1: `transfer.rss_export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# ein leeres "RoundTrip[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RoundTrip[]
RoundTrip[]=transfer.rss_export
*/ ?>
```

## Keywords

Standard: eine Liste mit 3: `feed`, `news from another website`, `Nachrichten einlesen`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
# ein leeres "Keywords[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Keywords[]
Keywords[]=feed
Keywords[]=news from another website
Keywords[]=Nachrichten einlesen
*/ ?>
```

## Weight

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.rss_import]
Weight=60
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
