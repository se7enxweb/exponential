---
title: country.ini [AD]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AD] in country.ini: Name, Alpha2, Alpha3, IDC"
keywords: Einstellung, country.ini, AD, INI, setting, Name, Alpha2, Alpha3, IDC
type: generated
generated: exp:help:examples
---
# country.ini [AD]

Der Block `[AD]` von `settings/country.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/country.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/country.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/country.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/country.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Andorra`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[AD]
Name=Andorra
*/ ?>
```

## Alpha2

Standard: `AD`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[AD]
Alpha2=AD
*/ ?>
```

## Alpha3

Standard: `AND`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[AD]
Alpha3=AND
*/ ?>
```

## IDC

Standard: `376`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[AD]
IDC=376
*/ ?>
```

## Siehe auch

- [Alle Blöcke von country.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/country.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
