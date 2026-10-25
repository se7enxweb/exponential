---
title: kernelservices.ini [Service_transfer.audit]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_transfer.audit] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description, Name, Direction ..."
keywords: Einstellung, kernelservices.ini, Service_transfer.audit, INI, setting, Class, Factory, Arguments, Scope, Tags, Description, Name, Direction, Scopes, Formats, EntryURL, Permission, HelpPage, Keywords, Weight
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.audit]

Der Block `[Service_transfer.audit]` von `settings/kernelservices.ini`, mit 15 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `expKernelTransferTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
Class=expKernelTransferTool
*/ ?>
```

## Factory

Standard: `expKernelTransferTool::fromSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
Factory=expKernelTransferTool::fromSettings
*/ ?>
```

## Arguments

Standard: eine Liste mit 1: `transfer.audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
# ein leeres "Arguments[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Arguments[]
Arguments[]=transfer.audit
*/ ?>
```

## Scope

Standard: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
Scope=worker
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=transfer.tool
*/ ?>
```

## Description

Standard: `Download the audit trail's records, filtered as in the console, as CSV, JSON lines or JSON.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
Description=Download the audit trail's records, filtered as in the console, as CSV, JSON lines or JSON.
*/ ?>
```

## Name

Standard: `Audit export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
Name=Audit export
*/ ?>
```

## Direction

Standard: `export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
Direction=export
*/ ?>
```

## Scopes

Standard: eine Liste mit 1: `data`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
# ein leeres "Scopes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scopes[]
Scopes[]=data
*/ ?>
```

## Formats

Standard: eine Liste mit 3: `CSV`, `JSONL`, `JSON`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
# ein leeres "Formats[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Formats[]
Formats[]=CSV
Formats[]=JSONL
Formats[]=JSON
*/ ?>
```

## EntryURL

Standard: `audit/export`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
EntryURL=audit/export
*/ ?>
```

## Permission

Standard: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
Permission=audit/read
*/ ?>
```

## HelpPage

Standard: `features/6.0/audit-trail.md#exports-and-the-channels-of-extensions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
HelpPage=features/6.0/audit-trail.md#exports-and-the-channels-of-extensions
*/ ?>
```

## Keywords

Standard: eine Liste mit 4: `log`, `who changed what`, `compliance`, `Protokoll`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
# ein leeres "Keywords[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Keywords[]
Keywords[]=log
Keywords[]=who changed what
Keywords[]=compliance
Keywords[]=Protokoll
*/ ?>
```

## Weight

Standard: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.audit]
Weight=80
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
