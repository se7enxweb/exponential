---
title: package.ini [License_LicenseRef-Proprietary]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [License_LicenseRef-Proprietary] in package.ini: Name, Group"
keywords: Einstellung, package.ini, License_LicenseRef-Proprietary, INI, setting, Name, Group
type: generated
generated: exp:help:examples
---
# package.ini [License_LicenseRef-Proprietary]

Der Block `[License_LicenseRef-Proprietary]` von `settings/package.ini`, mit 2 Einstellung(en).

> Alle Rechte vorbehalten: Das Paket darf nur so verwendet werden, wie sein Eigentümer es erlaubt. Dafür gibt es  
> keinen SPDX-Bezeichner; die Bedingungen sind die des Eigentümers, im Paket angegeben.  

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Proprietary (all rights reserved)`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_LicenseRef-Proprietary]
Name=Proprietary (all rights reserved)
*/ ?>
```

## Group

Standard: `proprietary`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[License_LicenseRef-Proprietary]
Group=proprietary
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
