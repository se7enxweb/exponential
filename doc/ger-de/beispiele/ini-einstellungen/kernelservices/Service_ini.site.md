---
title: kernelservices.ini [Service_ini.site]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_ini.site] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description"
keywords: Einstellung, kernelservices.ini, Service_ini.site, INI, setting, Class, Factory, Arguments, Scope, Tags, Description
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_ini.site]

Der Block `[Service_ini.site]` von `settings/kernelservices.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `eZINI`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.site]
Class=eZINI
*/ ?>
```

## Factory

Standard: `eZINI::instance`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.site]
Factory=eZINI::instance
*/ ?>
```

## Arguments

Standard: eine Liste mit 1: `site.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.site]
# ein leeres "Arguments[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Arguments[]
Arguments[]=site.ini
*/ ?>
```

## Scope

> kept per siteaccess for the worker when WorkerScope is on  

Standard: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.site]
Scope=worker
*/ ?>
```

## Tags

Standard: eine Liste mit 2: `kernel.core`, `kernel.settings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.site]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=kernel.core
Tags[]=kernel.settings
*/ ?>
```

## Description

Standard: `site.ini of the current siteaccess (eZINI::instance( 'site.ini' )).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.site]
Description=site.ini of the current siteaccess (eZINI::instance( 'site.ini' )).
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
