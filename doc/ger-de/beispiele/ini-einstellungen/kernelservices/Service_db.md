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

> Ein Block pro Service, \[Service\_<id>\]. Die id ist der Name, den der Code anfordert:  
>   expKernelServices::get( '<id>' )  
>
> Class        die Klasse (oder Schnittstelle) des Services  
> Factory      Class::method zum Aufrufen; ohne sie "new Class( Arguments )"  
> Arguments\[\]  @<id> ein anderer Service, %ini:<file>/<Section>/<Variable>% eine  
>              Einstellung, alles andere wie geschrieben (@@ und %% für ein führendes @  
>              oder % in einem Literal)  
> Scope        worker: einer pro Worker und Siteaccess (über Anfragen hinweg behalten bei  
>                      Velocity; er darf nichts aus einer Anfrage halten)  
>              request: einer pro Anfrage (der Standard)  
>              prototype: jedes Mal ein neuer  
> Lazy         true (der Standard): beim ersten Anfordern erzeugt  
> Tags\[\]       Namen, die Services für einen Verbraucher sammeln  
>              (expKernelServices::tagged( '<tag>' ))  
> Description  eine Zeile für exp:services:list und exp:services:show  
> Enabled      false schaltet einen Service ab (eine Erweiterung kann einen  
>              des Kernels abschalten)  
>
> Die ids ini.<name> brauchen keinen Block: expKernelServices::get( 'ini.site' ) ist  
> eZINI::instance( 'site.ini' ). Die Blöcke für ini.site und ini.i18n unten  
> dokumentieren sie nur.  

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

> das Handle in $GLOBALS\['eZDBGlobalInstance'\] gilt pro Anfrage; die Verbindung  
> darunter ist auf Treiberebene persistent, wo der Treiber eine hält  

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
