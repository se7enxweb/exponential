---
title: texttoimage.ini [arial]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [arial] in texttoimage.ini: Family, PointSize, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment, BackgroundColor, TextColor"
keywords: Einstellung, texttoimage.ini, arial, INI, setting, Family, PointSize, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment, BackgroundColor, TextColor
type: generated
generated: exp:help:examples
---
# texttoimage.ini [arial]

Der Block `[arial]` von `settings/texttoimage.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/texttoimage.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/texttoimage.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/texttoimage.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/texttoimage.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Family

Standard: `arial`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[arial]
Family=arial
*/ ?>
```

## PointSize

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[arial]
PointSize=30
*/ ?>
```

## XAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[arial]
XAdjustment=0
*/ ?>
```

## YAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[arial]
YAdjustment=0
*/ ?>
```

## WidthAdjustment

Standard: `8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[arial]
WidthAdjustment=8
*/ ?>
```

## HeightAdjustment

Standard: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[arial]
HeightAdjustment=6
*/ ?>
```

## BackgroundColor

Standard: `#ffffff`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[arial]
BackgroundColor=#ffffff
*/ ?>
```

## TextColor

Standard: `#000000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[arial]
TextColor=#000000
*/ ?>
```

## Siehe auch

- [Alle Blöcke von texttoimage.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/texttoimage.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
