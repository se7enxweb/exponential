---
title: setup.ini [imagemagick_program]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [imagemagick_program] in setup.ini: unix_Executable, unix_SearchPaths, unix_ExcludePaths, win32_Executable, win32_SearchPaths, win32_ExcludePaths"
keywords: Einstellung, setup.ini, imagemagick_program, INI, setting, unix_Executable, unix_SearchPaths, unix_ExcludePaths, win32_Executable, win32_SearchPaths, win32_ExcludePaths
type: generated
generated: exp:help:examples
---
# setup.ini [imagemagick_program]

Der Block `[imagemagick_program]` von `settings/setup.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/setup.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/setup.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/setup.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/setup.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## unix_Executable

Standard: `convert`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[imagemagick_program]
unix_Executable=convert
*/ ?>
```

## unix_SearchPaths

Standard: `/bin;/sbin;/usr/bin;/usr/sbin;/usr/local/bin;/usr/local/sbin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[imagemagick_program]
unix_SearchPaths=/bin;/sbin;/usr/bin;/usr/sbin;/usr/local/bin;/usr/local/sbin
*/ ?>
```

## unix_ExcludePaths

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[imagemagick_program]
unix_ExcludePaths=
*/ ?>
```

## win32_Executable

Standard: `convertim.exe;convert.exe`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[imagemagick_program]
win32_Executable=convertim.exe;convert.exe
*/ ?>
```

## win32_SearchPaths

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[imagemagick_program]
win32_SearchPaths=
*/ ?>
```

## win32_ExcludePaths

Standard: `convert.exe@Windows/system32`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[imagemagick_program]
win32_ExcludePaths=convert.exe@Windows/system32
*/ ?>
```

## Siehe auch

- [Alle Blöcke von setup.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
