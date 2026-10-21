---
title: country.ini [ER]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ER] in country.ini: Name, Alpha2, Alpha3, IDC"
keywords: Einstellung, country.ini, ER, INI, setting, Name, Alpha2, Alpha3, IDC
type: generated
generated: exp:help:examples
---
# country.ini [ER]

Der Block `[ER]` von `settings/country.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/country.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/country.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/country.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/country.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Eritrea`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[ER]
Name=Eritrea
*/ ?>
```

## Alpha2

Standard: `ER`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[ER]
Alpha2=ER
*/ ?>
```

## Alpha3

Standard: `ERI`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[ER]
Alpha3=ERI
*/ ?>
```

## IDC

Standard: `291`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[ER]
IDC=291
*/ ?>
```

## Siehe auch

- [Alle Blöcke von country.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/country.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
