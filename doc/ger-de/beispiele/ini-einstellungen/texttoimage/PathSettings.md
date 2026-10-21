---
title: texttoimage.ini [PathSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PathSettings] in texttoimage.ini: FontDir, CacheDir, HtmlDir"
keywords: Einstellung, texttoimage.ini, PathSettings, INI, setting, FontDir, CacheDir, HtmlDir
type: generated
generated: exp:help:examples
---
# texttoimage.ini [PathSettings]

Der Block `[PathSettings]` von `settings/texttoimage.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/texttoimage.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/texttoimage.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/texttoimage.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/texttoimage.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## FontDir

Standard: eine Liste mit 1: `design/standard/fonts`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[PathSettings]
# ein leeres "FontDir[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FontDir[]
FontDir[]=design/standard/fonts
*/ ?>
```

## CacheDir

Standard: `var/cache/texttoimage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[PathSettings]
CacheDir=var/cache/texttoimage
*/ ?>
```

## HtmlDir

Standard: `/var/cache/texttoimage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/texttoimage.ini.append.php
[PathSettings]
HtmlDir=/var/cache/texttoimage
*/ ?>
```

## Siehe auch

- [Alle Blöcke von texttoimage.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/texttoimage.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
