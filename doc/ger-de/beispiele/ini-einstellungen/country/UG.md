---
title: country.ini [UG]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UG] in country.ini: Name, Alpha2, Alpha3, IDC"
keywords: Einstellung, country.ini, UG, INI, setting, Name, Alpha2, Alpha3, IDC
type: generated
generated: exp:help:examples
---
# country.ini [UG]

Der Block `[UG]` von `settings/country.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/country.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/country.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/country.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/country.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Uganda`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[UG]
Name=Uganda
*/ ?>
```

## Alpha2

Standard: `UG`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[UG]
Alpha2=UG
*/ ?>
```

## Alpha3

Standard: `UGA`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[UG]
Alpha3=UGA
*/ ?>
```

## IDC

Standard: `256`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[UG]
IDC=256
*/ ?>
```

## Siehe auch

- [Alle Blöcke von country.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/country.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
