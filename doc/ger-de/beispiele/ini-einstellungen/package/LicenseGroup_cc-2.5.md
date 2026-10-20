---
title: package.ini [LicenseGroup_cc-2.5]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LicenseGroup_cc-2.5] in package.ini: Name"
keywords: Einstellung, package.ini, LicenseGroup_cc-2.5, INI, setting, Name
type: generated
generated: exp:help:examples
---
# package.ini [LicenseGroup_cc-2.5]

Der Block `[LicenseGroup_cc-2.5]` von `settings/package.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Creative Commons 2.5 Generic`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseGroup_cc-2.5]
Name=Creative Commons 2.5 Generic
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
