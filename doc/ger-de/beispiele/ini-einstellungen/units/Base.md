---
title: units.ini [Base]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Base] in units.ini: meter, gram, second, ampere, kelvin, mole, candela, byte ..."
keywords: Einstellung, units.ini, Base, INI, setting, meter, gram, second, ampere, kelvin, mole, candela, byte, bit
type: generated
generated: exp:help:examples
---
# units.ini [Base]

Der Block `[Base]` von `settings/units.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/units.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/units.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/units.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/units.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## meter

Standard: `m`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[Base]
meter=m
*/ ?>
```

## gram

Standard: `g`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[Base]
gram=g
*/ ?>
```

## second

Standard: `s`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[Base]
second=s
*/ ?>
```

## ampere

Standard: `A`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[Base]
ampere=A
*/ ?>
```

## kelvin

Standard: `K`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[Base]
kelvin=K
*/ ?>
```

## mole

Standard: `mol`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[Base]
mole=mol
*/ ?>
```

## candela

Standard: `cd`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[Base]
candela=cd
*/ ?>
```

## byte

Standard: `B`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[Base]
byte=B
*/ ?>
```

## bit

Standard: `b`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[Base]
bit=b
*/ ?>
```

## Siehe auch

- [Alle Blöcke von units.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/units.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
