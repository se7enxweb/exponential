---
title: texttoimage.ini [smartie]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [smartie] in texttoimage.ini: Family, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment"
keywords: Einstellung, texttoimage.ini, smartie, INI, setting, Family, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment
type: generated
generated: exp:help:examples
---
# texttoimage.ini [smartie]

Der Block `[smartie]` von `settings/texttoimage.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/texttoimage.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/texttoimage.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/texttoimage.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/texttoimage.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Family

Standard: `smartie`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[smartie]
Family=smartie
*/ ?>
```

## Angle

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[smartie]
Angle=0
*/ ?>
```

## XAdjustment

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[smartie]
XAdjustment=2
*/ ?>
```

## YAdjustment

Standard: `-1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[smartie]
YAdjustment=-1
*/ ?>
```

## WidthAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[smartie]
WidthAdjustment=0
*/ ?>
```

## HeightAdjustment

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[smartie]
HeightAdjustment=3
*/ ?>
```

## Siehe auch

- [Alle Blöcke von texttoimage.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/texttoimage.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
