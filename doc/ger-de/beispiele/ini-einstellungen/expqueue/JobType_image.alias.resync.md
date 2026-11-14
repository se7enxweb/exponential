---
title: expqueue.ini [JobType_image.alias.resync]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [JobType_image.alias.resync] in expqueue.ini: Handler, Class, Timeout, Description"
keywords: Einstellung, expqueue.ini, JobType_image.alias.resync, INI, setting, Handler, Class, Timeout, Description
type: generated
generated: exp:help:examples
---
# expqueue.ini [JobType_image.alias.resync]

Der Block `[JobType_image.alias.resync]` von `settings/expqueue.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/expqueue.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/expqueue.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/expqueue.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/expqueue.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handler

Standard: `expImageAliasQueueHandler`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expqueue.ini.append.php
[JobType_image.alias.resync]
Handler=expImageAliasQueueHandler
*/ ?>
```

## Class

Standard: `bulk`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expqueue.ini.append.php
[JobType_image.alias.resync]
Class=bulk
*/ ?>
```

## Timeout

Standard: `86400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expqueue.ini.append.php
[JobType_image.alias.resync]
Timeout=86400
*/ ?>
```

## Description

Standard: `Fetches the images of a staging site from its source installation`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expqueue.ini.append.php
[JobType_image.alias.resync]
Description=Fetches the images of a staging site from its source installation
*/ ?>
```

## Siehe auch

- [Alle Blöcke von expqueue.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/expqueue.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
