---
title: kernelservices.ini [Service_http]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_http] in kernelservices.ini: Class, Factory, Scope, Tags, Description"
keywords: Einstellung, kernelservices.ini, Service_http, INI, setting, Class, Factory, Scope, Tags, Description
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_http]

Der Block `[Service_http]` von `settings/kernelservices.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

Standard: `eZHTTPTool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_http]
Class=eZHTTPTool
*/ ?>
```

## Factory

Standard: `eZHTTPTool::instance`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_http]
Factory=eZHTTPTool::instance
*/ ?>
```

## Scope

Standard: `request`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_http]
Scope=request
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `kernel.core`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_http]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=kernel.core
*/ ?>
```

## Description

Standard: `The HTTP request helper (eZHTTPTool::instance()).`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_http]
Description=The HTTP request helper (eZHTTPTool::instance()).
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
