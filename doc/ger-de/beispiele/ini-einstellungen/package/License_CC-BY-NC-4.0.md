---
title: package.ini [License_CC-BY-NC-4.0]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [License_CC-BY-NC-4.0] in package.ini: Name, URL, Group"
keywords: Einstellung, package.ini, License_CC-BY-NC-4.0, INI, setting, Name, URL, Group
type: generated
generated: exp:help:examples
---
# package.ini [License_CC-BY-NC-4.0]

Der Block `[License_CC-BY-NC-4.0]` von `settings/package.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Creative Commons Attribution-NonCommercial 4.0 International`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_CC-BY-NC-4.0]
Name=Creative Commons Attribution-NonCommercial 4.0 International
*/ ?>
```

## URL

Standard: `https://creativecommons.org/licenses/by-nc/4.0/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_CC-BY-NC-4.0]
URL=https://creativecommons.org/licenses/by-nc/4.0/
*/ ?>
```

## Group

Standard: `cc-4.0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_CC-BY-NC-4.0]
Group=cc-4.0
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
