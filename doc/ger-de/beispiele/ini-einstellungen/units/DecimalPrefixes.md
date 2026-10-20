---
title: units.ini [DecimalPrefixes]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DecimalPrefixes] in units.ini: yotta, zetta, exa, peta, tera, giga, mega, kilo ..."
keywords: Einstellung, units.ini, DecimalPrefixes, INI, setting, yotta, zetta, exa, peta, tera, giga, mega, kilo, hecto, deca, deci, centi, milli, micro, nano, pico, femto, atto, zepto, yocto
type: generated
generated: exp:help:examples
---
# units.ini [DecimalPrefixes]

Der Block `[DecimalPrefixes]` von `settings/units.ini`, mit 20 Einstellung(en).

> Each entry is an array with the factor value and the symbol  
> The factor used for calculating values in the power of 10  

Bearbeiten Sie `settings/units.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/units.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/units.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/units.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## yotta

Standard: `24;Y`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
yotta=24;Y
*/ ?>
```

## zetta

Standard: `21;Z`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
zetta=21;Z
*/ ?>
```

## exa

Standard: `18;E`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
exa=18;E
*/ ?>
```

## peta

Standard: `15;P`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
peta=15;P
*/ ?>
```

## tera

Standard: `12;T`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
tera=12;T
*/ ?>
```

## giga

Standard: `9;G`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
giga=9;G
*/ ?>
```

## mega

Standard: `6;M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
mega=6;M
*/ ?>
```

## kilo

Standard: `3;k`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
kilo=3;k
*/ ?>
```

## hecto

Standard: `2;h`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
hecto=2;h
*/ ?>
```

## deca

Standard: `1;da`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
deca=1;da
*/ ?>
```

## deci

Standard: `-1;d`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
deci=-1;d
*/ ?>
```

## centi

Standard: `-2;c`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
centi=-2;c
*/ ?>
```

## milli

Standard: `-3;m`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
milli=-3;m
*/ ?>
```

## micro

Standard: `-6;µ`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
micro=-6;µ
*/ ?>
```

## nano

Standard: `-9;n`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
nano=-9;n
*/ ?>
```

## pico

Standard: `-12;p`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
pico=-12;p
*/ ?>
```

## femto

Standard: `-15;f`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
femto=-15;f
*/ ?>
```

## atto

Standard: `-18;a`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
atto=-18;a
*/ ?>
```

## zepto

Standard: `-21;z`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
zepto=-21;z
*/ ?>
```

## yocto

Standard: `-24;y`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[DecimalPrefixes]
yocto=-24;y
*/ ?>
```

## Siehe auch

- [Alle Blöcke von units.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/units.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
