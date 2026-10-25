---
title: kernelservices.ini [Service_transfer.registry]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_transfer.registry] in kernelservices.ini: Class, Factory, Scope, Tags, Description"
keywords: Einstellung, kernelservices.ini, Service_transfer.registry, INI, setting, Class, Factory, Scope, Tags, Description
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_transfer.registry]

Der Block `[Service_transfer.registry]` von `settings/kernelservices.ini`, mit 5 Einstellung(en).

> The transfer registry (doc/eng-us/features/6.0/transfer-registry.md): every import and export tool declares itself  
> in a block tagged transfer.tool, the kernel's below, an extension's in its own  
> settings/kernelservices.ini.append.php. Every link to a tool reads the registry: the "Looking for…?" boxes, the  
> entries of the content view's Import/Export menu and of the class pages, the "Import and export" list of the Setup  
> menu and of the tools' left menus, the admin search's tool suggestions, and the context help. A tool whose  
> extension is not active, whose module does not exist or that the user may not use is never shown.  
>
> Class=expKernelTransferTool, Factory=expKernelTransferTool::fromSettings, Arguments\[\]=<the block's id>,  
> Scope=worker, Tags\[\]=transfer.tool, and:  
> Description  one sentence: what it is for  
> Name         its name in lists  
> Direction    import, export or both  
> Scopes\[\]     site, subtree, object, class, data (records that are not content: submissions, subscribers, logs)  
> Formats\[\]    the file formats it reads or writes (CSV, XLSX, ezpkg ...)  
> EntryURL     its main page; its first part is the tool's module  
> Permission   module/function needed to use it; alternatives separated by "|"  
> Modules\[\]    further modules whose pages belong to the tool  
> HelpPage     its help page (a page id below doc/eng-us/, ext/<extension>/<path> for an extension's, #anchor allowed)  
> RoundTrip\[\]  the ids of tools that read what it writes or write what it reads  
> Keywords\[\]   task words the admin search finds it by (English and German)  
> Actions\[\]    context|import or export|label|URL\[|module/function\]: an entry of a context. Contexts: node (any  
>              published item), container (an item below which the user may create), class (a class page),  
>              setup (the Setup pages); %nodeID%, %objectID%, %classID%, %classIdentifier% in the URL  
> Weight       the order in lists (lower first, then by name; default 100)  

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `expKernelTransferRegistry`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.registry]
Class=expKernelTransferRegistry
*/ ?>
```

## Factory

Standard: `expKernelTransferRegistry::fromContainer`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.registry]
Factory=expKernelTransferRegistry::fromContainer
*/ ?>
```

## Scope

Standard: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.registry]
Scope=worker
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `kernel.core`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.registry]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=kernel.core
*/ ?>
```

## Description

Standard: `The transfer registry: the import and export tools of the installation (tag transfer.tool).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_transfer.registry]
Description=The transfer registry: the import and export tools of the installation (tag transfer.tool).
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
