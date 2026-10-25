---
title: kernelservices.ini [Service_db]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_db] in kernelservices.ini: Class, Factory, Scope, Tags, Description"
keywords: Einstellung, kernelservices.ini, Service_db, INI, setting, Class, Factory, Scope, Tags, Description
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_db]

Der Block `[Service_db]` von `settings/kernelservices.ini`, mit 5 Einstellung(en).

> One block per service, \[Service\_<id>\]. The id is the name code asks for:  
>   expKernelServices::get( '<id>' )  
>
> Class        the class (or interface) of the service  
> Factory      Class::method to call; without it, "new Class( Arguments )"  
> Arguments\[\]  @<id> another service, %ini:<file>/<Section>/<Variable>% a  
>              setting, anything else as written (@@ and %% for a leading @  
>              or % in a literal)  
> Scope        worker: one per worker and siteaccess (kept across requests on  
>                      Velocity; it must hold nothing of a request)  
>              request: one per request (the default)  
>              prototype: a new one each time  
> Lazy         true (the default): made when first asked for  
> Tags\[\]       names that collect services for a consumer  
>              (expKernelServices::tagged( '<tag>' ))  
> Description  one line for exp:services:list and exp:services:show  
> Enabled      false switches a service off (an extension can switch off one  
>              of the kernel's)  
>
> The ids ini.<name> need no block: expKernelServices::get( 'ini.site' ) is  
> eZINI::instance( 'site.ini' ). The blocks for ini.site and ini.i18n below  
> only document them.  

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `eZDBInterface`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_db]
Class=eZDBInterface
*/ ?>
```

## Factory

Standard: `eZDB::instance`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_db]
Factory=eZDB::instance
*/ ?>
```

## Scope

> the handle in $GLOBALS\['eZDBGlobalInstance'\] is per request; the connection  
> underneath is persistent at driver level where the driver keeps one  

Standard: `request`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_db]
Scope=request
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `kernel.core`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_db]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=kernel.core
*/ ?>
```

## Description

Standard: `The database handler of the request (eZDB::instance()).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_db]
Description=The database handler of the request (eZDB::instance()).
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
