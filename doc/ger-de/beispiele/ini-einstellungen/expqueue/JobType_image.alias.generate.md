---
title: expqueue.ini [JobType_image.alias.generate]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [JobType_image.alias.generate] in expqueue.ini: Handler, Class, Timeout, MaxAttempts, RetryBase, MaxBackoff, Description"
keywords: Einstellung, expqueue.ini, JobType_image.alias.generate, INI, setting, Handler, Class, Timeout, MaxAttempts, RetryBase, MaxBackoff, Description
type: generated
generated: exp:help:examples
---
# expqueue.ini [JobType_image.alias.generate]

Der Block `[JobType_image.alias.generate]` von `settings/expqueue.ini`, mit 7 Einstellung(en).

> Bild-Aliase (image.ini \[AliasSettings\], expImageAliasJob). Der Erzeuger setzt Klasse und Priorität: ein Fehltreffer eines Besuchers  
> (der Alias wurde nicht rechtzeitig erzeugt, die Antwort war der Platzhalter) interactive 80, Vorab-Erzeugung nach dem Veröffentlichen  
> editorial 60, die Administration interactive 60, Massenläufe bulk 40.  

Bearbeiten Sie `settings/expqueue.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/expqueue.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/expqueue.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/expqueue.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handler

Standard: `expImageAliasQueueHandler`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expqueue.ini.append.php
[JobType_image.alias.generate]
Handler=expImageAliasQueueHandler
*/ ?>
```

## Class

Standard: `editorial`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expqueue.ini.append.php
[JobType_image.alias.generate]
Class=editorial
*/ ?>
```

## Timeout

Standard: `1800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expqueue.ini.append.php
[JobType_image.alias.generate]
Timeout=1800
*/ ?>
```

## MaxAttempts

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expqueue.ini.append.php
[JobType_image.alias.generate]
MaxAttempts=5
*/ ?>
```

## RetryBase

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expqueue.ini.append.php
[JobType_image.alias.generate]
RetryBase=30
*/ ?>
```

## MaxBackoff

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expqueue.ini.append.php
[JobType_image.alias.generate]
MaxBackoff=600
*/ ?>
```

## Description

Standard: `Makes the image aliases (and WebP/AVIF variants) of images: a visitor's miss, or after publishing`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expqueue.ini.append.php
[JobType_image.alias.generate]
Description=Makes the image aliases (and WebP/AVIF variants) of images: a visitor's miss, or after publishing
*/ ?>
```

## Siehe auch

- [Alle Blöcke von expqueue.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/expqueue.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
