---
title: country.ini [GF]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GF] in country.ini: Name, Alpha2, Alpha3, IDC"
keywords: Einstellung, country.ini, GF, INI, setting, Name, Alpha2, Alpha3, IDC
type: generated
generated: exp:help:examples
---
# country.ini [GF]

Der Block `[GF]` von `settings/country.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/country.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/country.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/country.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/country.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `French Guiana`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[GF]
Name=French Guiana
*/ ?>
```

## Alpha2

Standard: `GF`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[GF]
Alpha2=GF
*/ ?>
```

## Alpha3

Standard: `GUF`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[GF]
Alpha3=GUF
*/ ?>
```

## IDC

Standard: `594`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[GF]
IDC=594
*/ ?>
```

## Siehe auch

- [Alle Blöcke von country.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/country.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
