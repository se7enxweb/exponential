---
title: texttoimage.ini [a_d_mono]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [a_d_mono] in texttoimage.ini: Family, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment"
keywords: Einstellung, texttoimage.ini, a_d_mono, INI, setting, Family, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment
type: generated
generated: exp:help:examples
---
# texttoimage.ini [a_d_mono]

Der Block `[a_d_mono]` von `settings/texttoimage.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/texttoimage.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/texttoimage.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/texttoimage.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/texttoimage.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Family

Standard: `a_d_mono`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[a_d_mono]
Family=a_d_mono
*/ ?>
```

## Angle

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[a_d_mono]
Angle=0
*/ ?>
```

## XAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[a_d_mono]
XAdjustment=0
*/ ?>
```

## YAdjustment

Standard: `-1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[a_d_mono]
YAdjustment=-1
*/ ?>
```

## WidthAdjustment

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[a_d_mono]
WidthAdjustment=1
*/ ?>
```

## HeightAdjustment

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[a_d_mono]
HeightAdjustment=1
*/ ?>
```

## Siehe auch

- [Alle Blöcke von texttoimage.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/texttoimage.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
