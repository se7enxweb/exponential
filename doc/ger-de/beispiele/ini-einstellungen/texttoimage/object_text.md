---
title: texttoimage.ini [object_text]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [object_text] in texttoimage.ini: Family, PointSize, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment, BackgroundColor ..."
keywords: Einstellung, texttoimage.ini, object_text, INI, setting, Family, PointSize, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment, BackgroundColor, TextColor
type: generated
generated: exp:help:examples
---
# texttoimage.ini [object_text]

Der Block `[object_text]` von `settings/texttoimage.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/texttoimage.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/texttoimage.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/texttoimage.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/texttoimage.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Family

Standard: `bluehigc`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[object_text]
Family=bluehigc
*/ ?>
```

## PointSize

Standard: `15`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[object_text]
PointSize=15
*/ ?>
```

## Angle

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[object_text]
Angle=1
*/ ?>
```

## XAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[object_text]
XAdjustment=0
*/ ?>
```

## YAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[object_text]
YAdjustment=0
*/ ?>
```

## WidthAdjustment

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[object_text]
WidthAdjustment=5
*/ ?>
```

## HeightAdjustment

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[object_text]
HeightAdjustment=5
*/ ?>
```

## BackgroundColor

Standard: `#ffffff`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[object_text]
BackgroundColor=#ffffff
*/ ?>
```

## TextColor

Standard: `#ff0000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[object_text]
TextColor=#ff0000
*/ ?>
```

## Siehe auch

- [Alle Blöcke von texttoimage.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/texttoimage.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
