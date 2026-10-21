---
title: i18n.ini [CharacterSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CharacterSettings] in i18n.ini: Charset, HTTPCharset, MBStringExtension"
keywords: Einstellung, i18n.ini, CharacterSettings, INI, setting, Charset, HTTPCharset, MBStringExtension
type: generated
generated: exp:help:examples
---
# i18n.ini [CharacterSettings]

Der Block `[CharacterSettings]` von `settings/i18n.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/i18n.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/i18n.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/i18n.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/i18n.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Charset

Standard: `utf-8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[CharacterSettings]
Charset=utf-8
*/ ?>
```

## HTTPCharset

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[CharacterSettings]
HTTPCharset=
*/ ?>
```

## MBStringExtension

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/i18n.ini.append.php
[CharacterSettings]
MBStringExtension=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von i18n.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/i18n.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
