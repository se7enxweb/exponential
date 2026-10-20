---
title: icon.ini [FlagIcons]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FlagIcons] in icon.ini: Repository, Theme, IconFormat"
keywords: Einstellung, icon.ini, FlagIcons, INI, setting, Repository, Theme, IconFormat
type: generated
generated: exp:help:examples
---
# icon.ini [FlagIcons]

Der Block `[FlagIcons]` von `settings/icon.ini`, mit 3 Einstellung(en).

> IconExtensions\[\]=myiconsextension  

Bearbeiten Sie `settings/icon.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/icon.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/icon.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/icon.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Repository

> Wo nach Flaggensymbolen gesucht wird, relativ zu Exponential  

Standard: `share/icons`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[FlagIcons]
Repository=share/icons
*/ ?>
```

## Theme

> Welches Theme für Flaggen verwendet wird  

Standard: `flags`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[FlagIcons]
Theme=flags
*/ ?>
```

## IconFormat

> Die Endung aller Symboldateien, z. B. eng-GB.gif  

Standard: `gif`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[FlagIcons]
IconFormat=gif
*/ ?>
```

## Siehe auch

- [Alle Blöcke von icon.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/icon.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
