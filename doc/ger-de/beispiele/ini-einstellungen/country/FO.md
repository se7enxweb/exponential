---
title: country.ini [FO]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FO] in country.ini: Name, Alpha2, Alpha3, IDC"
keywords: Einstellung, country.ini, FO, INI, setting, Name, Alpha2, Alpha3, IDC
type: generated
generated: exp:help:examples
---
# country.ini [FO]

Der Block `[FO]` von `settings/country.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/country.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/country.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/country.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/country.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Faroe Islands`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[FO]
Name=Faroe Islands
*/ ?>
```

## Alpha2

Standard: `FO`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[FO]
Alpha2=FO
*/ ?>
```

## Alpha3

Standard: `FRO`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[FO]
Alpha3=FRO
*/ ?>
```

## IDC

Standard: `298`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[FO]
IDC=298
*/ ?>
```

## Siehe auch

- [Alle Blöcke von country.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/country.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
