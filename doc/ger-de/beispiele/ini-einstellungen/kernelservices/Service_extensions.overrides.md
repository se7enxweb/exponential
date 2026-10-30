---
title: kernelservices.ini [Service_extensions.overrides]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Service_extensions.overrides] in kernelservices.ini: Class, Scope, Tags, Description"
keywords: Einstellung, kernelservices.ini, Service_extensions.overrides, INI, setting, Class, Scope, Tags, Description
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Service_extensions.overrides]

Der Block `[Service_extensions.overrides]` von `settings/kernelservices.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> was die Erweiterungsaktivierung des Web-Kernels für den aktuellen  
> Siteaccess ergibt, wie kompiliert: die Einstellungsverzeichnisse der Reihe nach und die  
> Erweiterungsverzeichnisse  

Standard: `expKernelExtensionOverrideList`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_extensions.overrides]
Class=expKernelExtensionOverrideList
*/ ?>
```

## Scope

Standard: `worker`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_extensions.overrides]
Scope=worker
*/ ?>
```

## Tags

Standard: eine Liste mit 1: `kernel.core`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_extensions.overrides]
# ein leeres "Tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tags[]
Tags[]=kernel.core
*/ ?>
```

## Description

Standard: `The compiled extension activation (settings override directories) of the current siteaccess.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Service_extensions.overrides]
Description=The compiled extension activation (settings override directories) of the current siteaccess.
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
