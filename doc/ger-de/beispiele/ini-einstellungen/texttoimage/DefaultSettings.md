---
title: texttoimage.ini [DefaultSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DefaultSettings] in texttoimage.ini: Family, PointSize, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment, BackgroundColor ..."
keywords: Einstellung, texttoimage.ini, DefaultSettings, INI, setting, Family, PointSize, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment, BackgroundColor, TextColor
type: generated
generated: exp:help:examples
---
# texttoimage.ini [DefaultSettings]

Der Block `[DefaultSettings]` von `settings/texttoimage.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/texttoimage.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/texttoimage.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/texttoimage.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/texttoimage.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Family

Standard: `1942`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[DefaultSettings]
Family=1942
*/ ?>
```

## PointSize

Standard: `40`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[DefaultSettings]
PointSize=40
*/ ?>
```

## Angle

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[DefaultSettings]
Angle=0
*/ ?>
```

## XAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[DefaultSettings]
XAdjustment=0
*/ ?>
```

## YAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[DefaultSettings]
YAdjustment=0
*/ ?>
```

## WidthAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[DefaultSettings]
WidthAdjustment=0
*/ ?>
```

## HeightAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[DefaultSettings]
HeightAdjustment=0
*/ ?>
```

## BackgroundColor

Standard: `#ffffff`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[DefaultSettings]
BackgroundColor=#ffffff
*/ ?>
```

## TextColor

Standard: `#000000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[DefaultSettings]
TextColor=#000000
*/ ?>
```

## Siehe auch

- [Alle Blöcke von texttoimage.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/texttoimage.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
