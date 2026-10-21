---
title: image.ini [GD]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GD] in image.ini: Name, IsEnabled, Handler"
keywords: Einstellung, image.ini, GD, INI, setting, Name, IsEnabled, Handler
type: generated
generated: exp:help:examples
---
# image.ini [GD]

Der Block `[GD]` von `settings/image.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `GD`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[GD]
Name=GD
*/ ?>
```

## IsEnabled

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[GD]
IsEnabled=true
*/ ?>
```

## Handler

> Welcher PHP-Handler für die Umwandlung verwendet wird;  
> ezimagegd bedeutet die Erweiterung ImageGD in PHP  

Standard: `eZImageGDFactory`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[GD]
Handler=eZImageGDFactory
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
