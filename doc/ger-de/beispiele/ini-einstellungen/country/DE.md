---
title: country.ini [DE]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DE] in country.ini: Name, Alpha2, Alpha3, IDC"
keywords: Einstellung, country.ini, DE, INI, setting, Name, Alpha2, Alpha3, IDC
type: generated
generated: exp:help:examples
---
# country.ini [DE]

Der Block `[DE]` von `settings/country.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/country.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/country.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/country.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/country.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Germany`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[DE]
Name=Germany
*/ ?>
```

## Alpha2

Standard: `DE`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[DE]
Alpha2=DE
*/ ?>
```

## Alpha3

Standard: `DEU`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[DE]
Alpha3=DEU
*/ ?>
```

## IDC

Standard: `49`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[DE]
IDC=49
*/ ?>
```

## Siehe auch

- [Alle Blöcke von country.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/country.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
