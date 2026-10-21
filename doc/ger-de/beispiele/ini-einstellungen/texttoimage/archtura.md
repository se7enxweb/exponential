---
title: texttoimage.ini [archtura]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [archtura] in texttoimage.ini: Family, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment, BackgroundColor, TextColor"
keywords: Einstellung, texttoimage.ini, archtura, INI, setting, Family, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment, BackgroundColor, TextColor
type: generated
generated: exp:help:examples
---
# texttoimage.ini [archtura]

Der Block `[archtura]` von `settings/texttoimage.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/texttoimage.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/texttoimage.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/texttoimage.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/texttoimage.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Family

Standard: `archtura`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[archtura]
Family=archtura
*/ ?>
```

## Angle

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[archtura]
Angle=0
*/ ?>
```

## XAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[archtura]
XAdjustment=0
*/ ?>
```

## YAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[archtura]
YAdjustment=0
*/ ?>
```

## WidthAdjustment

Standard: `8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[archtura]
WidthAdjustment=8
*/ ?>
```

## HeightAdjustment

Standard: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[archtura]
HeightAdjustment=6
*/ ?>
```

## BackgroundColor

Standard: `#ffffff`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[archtura]
BackgroundColor=#ffffff
*/ ?>
```

## TextColor

Standard: `#578ec5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[archtura]
TextColor=#578ec5
*/ ?>
```

## Siehe auch

- [Alle Blöcke von texttoimage.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/texttoimage.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
