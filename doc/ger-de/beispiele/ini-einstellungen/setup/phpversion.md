---
title: setup.ini [phpversion]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [phpversion] in setup.ini: MinimumVersion, SupportedVersion, UnstableVersions"
keywords: Einstellung, setup.ini, phpversion, INI, setting, MinimumVersion, SupportedVersion, UnstableVersions
type: generated
generated: exp:help:examples
---
# setup.ini [phpversion]

Der Block `[phpversion]` von `settings/setup.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/setup.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/setup.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/setup.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/setup.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MinimumVersion

> Unterstützt: PHP 8.0 bis 8.5. Ohne Unterstützung nutzbar: PHP 7.4 (Exponential 6.0.15 und später).  
> MinimumVersion: Älteres PHP wird abgelehnt. SupportedVersion: darunter besteht die Prüfung mit einem  
> Hinweis „nicht unterstützt“ (keine Zusage für Fehler- oder Sicherheitskorrekturen; die Funktionen, die PHP 8 brauchen, fehlen).  
> doc/eng-us/help/why-exponential-supports-php-7-4.md  

Standard: `7.4.0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[phpversion]
MinimumVersion=7.4.0
*/ ?>
```

## SupportedVersion

Standard: `8.0.0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[phpversion]
SupportedVersion=8.0.0
*/ ?>
```

## UnstableVersions

Standard: `6;`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[phpversion]
UnstableVersions=6;
*/ ?>
```

## Siehe auch

- [Alle Blöcke von setup.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
