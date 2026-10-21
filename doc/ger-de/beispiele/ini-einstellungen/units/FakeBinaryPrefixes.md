---
title: units.ini [FakeBinaryPrefixes]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FakeBinaryPrefixes] in units.ini: kilo, mega, giga, tera, peta, exa"
keywords: Einstellung, units.ini, FakeBinaryPrefixes, INI, setting, kilo, mega, giga, tera, peta, exa
type: generated
generated: exp:help:examples
---
# units.ini [FakeBinaryPrefixes]

Der Block `[FakeBinaryPrefixes]` von `settings/units.ini`, mit 6 Einstellung(en).

> Jeder Eintrag ist ein Array mit dem Faktor und dem Symbol  
> Der Faktor für die Berechnung von Werten in Zweierpotenzen  

Bearbeiten Sie `settings/units.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/units.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/units.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/units.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## kilo

Standard: `10;k`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[FakeBinaryPrefixes]
kilo=10;k
*/ ?>
```

## mega

Standard: `20;M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[FakeBinaryPrefixes]
mega=20;M
*/ ?>
```

## giga

Standard: `30;G`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[FakeBinaryPrefixes]
giga=30;G
*/ ?>
```

## tera

Standard: `40;T`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[FakeBinaryPrefixes]
tera=40;T
*/ ?>
```

## peta

Standard: `50;P`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[FakeBinaryPrefixes]
peta=50;P
*/ ?>
```

## exa

Standard: `60;E`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[FakeBinaryPrefixes]
exa=60;E
*/ ?>
```

## Siehe auch

- [Alle Blöcke von units.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/units.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
