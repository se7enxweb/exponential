---
title: country.ini [LC]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LC] in country.ini: Name, Alpha2, Alpha3, IDC"
keywords: Einstellung, country.ini, LC, INI, setting, Name, Alpha2, Alpha3, IDC
type: generated
generated: exp:help:examples
---
# country.ini [LC]

Der Block `[LC]` von `settings/country.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/country.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/country.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/country.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/country.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Saint Lucia`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[LC]
Name=Saint Lucia
*/ ?>
```

## Alpha2

Standard: `LC`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[LC]
Alpha2=LC
*/ ?>
```

## Alpha3

Standard: `LCA`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[LC]
Alpha3=LCA
*/ ?>
```

## IDC

Standard: `1758`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/country.ini.append.php
[LC]
IDC=1758
*/ ?>
```

## Siehe auch

- [Alle Blöcke von country.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/country.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
