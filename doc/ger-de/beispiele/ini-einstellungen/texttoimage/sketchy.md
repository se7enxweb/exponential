---
title: texttoimage.ini [sketchy]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [sketchy] in texttoimage.ini: Family, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment"
keywords: Einstellung, texttoimage.ini, sketchy, INI, setting, Family, Angle, XAdjustment, YAdjustment, WidthAdjustment, HeightAdjustment
type: generated
generated: exp:help:examples
---
# texttoimage.ini [sketchy]

Der Block `[sketchy]` von `settings/texttoimage.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/texttoimage.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/texttoimage.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/texttoimage.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/texttoimage.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Family

Standard: `sketchy`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[sketchy]
Family=sketchy
*/ ?>
```

## Angle

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[sketchy]
Angle=0
*/ ?>
```

## XAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[sketchy]
XAdjustment=0
*/ ?>
```

## YAdjustment

Standard: `-1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[sketchy]
YAdjustment=-1
*/ ?>
```

## WidthAdjustment

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[sketchy]
WidthAdjustment=0
*/ ?>
```

## HeightAdjustment

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[sketchy]
HeightAdjustment=1
*/ ?>
```

## Siehe auch

- [Alle Blöcke von texttoimage.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/texttoimage.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
