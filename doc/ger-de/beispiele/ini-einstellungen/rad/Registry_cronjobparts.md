---
title: rad.ini [Registry_cronjobparts]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Registry_cronjobparts] in rad.ini: Title, Description, Doc, Ini, Section, Entries, InTotal"
keywords: Einstellung, rad.ini, Registry_cronjobparts, INI, setting, Title, Description, Doc, Ini, Section, Entries, InTotal
type: generated
generated: exp:help:examples
---
# rad.ini [Registry_cronjobparts]

Der Block `[Registry_cronjobparts]` von `settings/rad.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/rad.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rad.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rad.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rad.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Title

Standard: `Cronjob part scripts`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_cronjobparts]
Title=Cronjob part scripts
*/ ?>
```

## Description

Standard: `The scripts of every cronjob part (cronjob.ini [CronjobPart-<part>] Scripts[]). Most of them are commands counted as runnables already, so they are listed and not added to the total.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_cronjobparts]
Description=The scripts of every cronjob part (cronjob.ini [CronjobPart-<part>] Scripts[]). Most of them are commands counted as runnables already, so they are listed and not added to the total.
*/ ?>
```

## Doc

Standard: `doc/eng-us/bc/6.0/rad-extension-points.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_cronjobparts]
Doc=doc/eng-us/bc/6.0/rad-extension-points.md
*/ ?>
```

## Ini

Standard: `cronjob.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_cronjobparts]
Ini=cronjob.ini
*/ ?>
```

## Section

Standard: `CronjobPart-*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_cronjobparts]
Section=CronjobPart-*
*/ ?>
```

## Entries

Standard: ein Hash mit 1: `Scripts=name`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_cronjobparts]
# ein leeres "Entries[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Entries[]
Entries[Scripts]=name
*/ ?>
```

## InTotal

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_cronjobparts]
InTotal=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rad.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rad.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
