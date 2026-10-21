---
title: country.ini [GS]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GS] in country.ini: Name, Alpha2, Alpha3, IDC"
keywords: Einstellung, country.ini, GS, INI, setting, Name, Alpha2, Alpha3, IDC
type: generated
generated: exp:help:examples
---
# country.ini [GS]

Der Block `[GS]` von `settings/country.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/country.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/country.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/country.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/country.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `South Georgia and The South Sandwich Islands`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[GS]
Name=South Georgia and The South Sandwich Islands
*/ ?>
```

## Alpha2

Standard: `GS`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[GS]
Alpha2=GS
*/ ?>
```

## Alpha3

Standard: `SGS`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[GS]
Alpha3=SGS
*/ ?>
```

## IDC

Standard: `500`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[GS]
IDC=500
*/ ?>
```

## Siehe auch

- [Alle Blöcke von country.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/country.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
