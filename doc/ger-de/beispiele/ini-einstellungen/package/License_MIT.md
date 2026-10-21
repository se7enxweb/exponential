---
title: package.ini [License_MIT]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [License_MIT] in package.ini: Name, URL, Group"
keywords: Einstellung, package.ini, License_MIT, INI, setting, Name, URL, Group
type: generated
generated: exp:help:examples
---
# package.ini [License_MIT]

Der Block `[License_MIT]` von `settings/package.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `MIT License`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_MIT]
Name=MIT License
*/ ?>
```

## URL

Standard: `https://opensource.org/license/mit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_MIT]
URL=https://opensource.org/license/mit
*/ ?>
```

## Group

Standard: `software`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_MIT]
Group=software
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
