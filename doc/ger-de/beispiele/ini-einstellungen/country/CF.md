---
title: country.ini [CF]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CF] in country.ini: Name, Alpha2, Alpha3, IDC"
keywords: Einstellung, country.ini, CF, INI, setting, Name, Alpha2, Alpha3, IDC
type: generated
generated: exp:help:examples
---
# country.ini [CF]

Der Block `[CF]` von `settings/country.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/country.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/country.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/country.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/country.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Central African Republic`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[CF]
Name=Central African Republic
*/ ?>
```

## Alpha2

Standard: `CF`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[CF]
Alpha2=CF
*/ ?>
```

## Alpha3

Standard: `CAF`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[CF]
Alpha3=CAF
*/ ?>
```

## IDC

Standard: `236`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[CF]
IDC=236
*/ ?>
```

## Siehe auch

- [Alle Blöcke von country.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/country.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
