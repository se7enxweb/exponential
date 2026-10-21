---
title: country.ini [SA]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SA] in country.ini: Name, Alpha2, Alpha3, IDC"
keywords: Einstellung, country.ini, SA, INI, setting, Name, Alpha2, Alpha3, IDC
type: generated
generated: exp:help:examples
---
# country.ini [SA]

Der Block `[SA]` von `settings/country.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/country.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/country.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/country.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/country.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Saudi Arabia`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[SA]
Name=Saudi Arabia
*/ ?>
```

## Alpha2

Standard: `SA`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[SA]
Alpha2=SA
*/ ?>
```

## Alpha3

Standard: `SAU`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[SA]
Alpha3=SAU
*/ ?>
```

## IDC

Standard: `966`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[SA]
IDC=966
*/ ?>
```

## Siehe auch

- [Alle Blöcke von country.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/country.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
