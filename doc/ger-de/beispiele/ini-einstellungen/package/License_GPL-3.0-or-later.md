---
title: package.ini [License_GPL-3.0-or-later]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [License_GPL-3.0-or-later] in package.ini: Name, URL, Group"
keywords: Einstellung, package.ini, License_GPL-3.0-or-later, INI, setting, Name, URL, Group
type: generated
generated: exp:help:examples
---
# package.ini [License_GPL-3.0-or-later]

Der Block `[License_GPL-3.0-or-later]` von `settings/package.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `GNU General Public License v3.0 or later`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_GPL-3.0-or-later]
Name=GNU General Public License v3.0 or later
*/ ?>
```

## URL

Standard: `https://www.gnu.org/licenses/gpl-3.0.html`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_GPL-3.0-or-later]
URL=https://www.gnu.org/licenses/gpl-3.0.html
*/ ?>
```

## Group

Standard: `software`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_GPL-3.0-or-later]
Group=software
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
