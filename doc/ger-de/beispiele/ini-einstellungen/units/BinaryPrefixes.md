---
title: units.ini [BinaryPrefixes]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [BinaryPrefixes] in units.ini: kibi, mebi, gibi, tebi, pebi, exbi"
keywords: Einstellung, units.ini, BinaryPrefixes, INI, setting, kibi, mebi, gibi, tebi, pebi, exbi
type: generated
generated: exp:help:examples
---
# units.ini [BinaryPrefixes]

Der Block `[BinaryPrefixes]` von `settings/units.ini`, mit 6 Einstellung(en).

> Jeder Eintrag ist ein Array mit dem Faktor und dem Symbol  
> Der Faktor für die Berechnung von Werten in Zweierpotenzen  

Bearbeiten Sie `settings/units.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/units.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/units.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/units.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## kibi

Standard: `10;Ki`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[BinaryPrefixes]
kibi=10;Ki
*/ ?>
```

## mebi

Standard: `20;Mi`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[BinaryPrefixes]
mebi=20;Mi
*/ ?>
```

## gibi

Standard: `30;Gi`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[BinaryPrefixes]
gibi=30;Gi
*/ ?>
```

## tebi

Standard: `40;Ti`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[BinaryPrefixes]
tebi=40;Ti
*/ ?>
```

## pebi

Standard: `50;Pi`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[BinaryPrefixes]
pebi=50;Pi
*/ ?>
```

## exbi

Standard: `60;Ei`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/units.ini.append.php
[BinaryPrefixes]
exbi=60;Ei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von units.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/units.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
