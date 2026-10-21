---
title: package.ini [License_LGPL-2.1-only]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [License_LGPL-2.1-only] in package.ini: Name, URL, Group"
keywords: Einstellung, package.ini, License_LGPL-2.1-only, INI, setting, Name, URL, Group
type: generated
generated: exp:help:examples
---
# package.ini [License_LGPL-2.1-only]

Der Block `[License_LGPL-2.1-only]` von `settings/package.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `GNU Lesser General Public License v2.1 only`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_LGPL-2.1-only]
Name=GNU Lesser General Public License v2.1 only
*/ ?>
```

## URL

Standard: `https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_LGPL-2.1-only]
URL=https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html
*/ ?>
```

## Group

Standard: `software`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_LGPL-2.1-only]
Group=software
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
