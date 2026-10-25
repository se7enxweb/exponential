---
title: kernelservices.ini [Service_transfer.pdf]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_transfer.pdf] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description, Name, Direction ..."
keywords: Einstellung, kernelservices.ini, Service_transfer.pdf, INI, setting, Class, Factory, Arguments, Scope, Tags, Description, Name, Direction, Scopes, Formats, EntryURL, Permission, HelpPage, Keywords, Weight
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.pdf]

Der Block `[Service_transfer.pdf]` von `settings/kernelservices.ini`, mit 15 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `expKernelTransferTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
Class=expKernelTransferTool
*/ ?>
```

## Factory

Standard: `expKernelTransferTool::fromSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
Factory=expKernelTransferTool::fromSettings
*/ ?>
```

## Arguments

Standard: eine Liste mit 1: `transfer.pdf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
# ein leeres "Arguments[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Arguments[]
Arguments[]=transfer.pdf
*/ ?>
```

## Scope

Standard: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
Scope=worker
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=transfer.tool
*/ ?>
```

## Description

Standard: `Make a PDF document of a folder and its items, to print or to send.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
Description=Make a PDF document of a folder and its items, to print or to send.
*/ ?>
```

## Name

Standard: `PDF export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
Name=PDF export
*/ ?>
```

## Direction

Standard: `export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
Direction=export
*/ ?>
```

## Scopes

Standard: eine Liste mit 1: `subtree`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
# ein leeres "Scopes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scopes[]
Scopes[]=subtree
*/ ?>
```

## Formats

Standard: eine Liste mit 1: `PDF`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
# ein leeres "Formats[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Formats[]
Formats[]=PDF
*/ ?>
```

## EntryURL

Standard: `pdf/list`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
EntryURL=pdf/list
*/ ?>
```

## Permission

Standard: `pdf/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
Permission=pdf/edit
*/ ?>
```

## HelpPage

Standard: `guides/pdf-exports.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
HelpPage=guides/pdf-exports.md
*/ ?>
```

## Keywords

Standard: eine Liste mit 3: `print`, `document`, `drucken`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
# ein leeres "Keywords[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Keywords[]
Keywords[]=print
Keywords[]=document
Keywords[]=drucken
*/ ?>
```

## Weight

Standard: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.pdf]
Weight=90
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
