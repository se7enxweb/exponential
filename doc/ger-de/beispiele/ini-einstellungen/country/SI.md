---
title: country.ini [SI]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SI] in country.ini: Name, Alpha2, Alpha3, IDC"
keywords: Einstellung, country.ini, SI, INI, setting, Name, Alpha2, Alpha3, IDC
type: generated
generated: exp:help:examples
---
# country.ini [SI]

Der Block `[SI]` von `settings/country.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/country.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/country.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/country.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/country.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Slovenia`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[SI]
Name=Slovenia
*/ ?>
```

## Alpha2

Standard: `SI`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[SI]
Alpha2=SI
*/ ?>
```

## Alpha3

Standard: `SVN`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[SI]
Alpha3=SVN
*/ ?>
```

## IDC

Standard: `386`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[SI]
IDC=386
*/ ?>
```

## Siehe auch

- [Alle Blöcke von country.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/country.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
