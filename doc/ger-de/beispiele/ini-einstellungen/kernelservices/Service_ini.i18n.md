---
title: kernelservices.ini [Service_ini.i18n]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_ini.i18n] in kernelservices.ini: Class, Factory, Arguments, Scope, Tags, Description"
keywords: Einstellung, kernelservices.ini, Service_ini.i18n, INI, setting, Class, Factory, Arguments, Scope, Tags, Description
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_ini.i18n]

Der Block `[Service_ini.i18n]` von `settings/kernelservices.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `eZINI`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.i18n]
Class=eZINI
*/ ?>
```

## Factory

Standard: `eZINI::instance`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.i18n]
Factory=eZINI::instance
*/ ?>
```

## Arguments

Standard: eine Liste mit 1: `i18n.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.i18n]
# ein leeres "Arguments[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Arguments[]
Arguments[]=i18n.ini
*/ ?>
```

## Scope

Standard: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.i18n]
Scope=worker
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `kernel.settings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.i18n]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=kernel.settings
*/ ?>
```

## Description

Standard: `i18n.ini of the current siteaccess.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_ini.i18n]
Description=i18n.ini of the current siteaccess.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
