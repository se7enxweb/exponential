---
title: setup.ini [ezcversion]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ezcversion] in setup.ini: TestClass, TestFunction, MinimumVersion"
keywords: Einstellung, setup.ini, ezcversion, INI, setting, TestClass, TestFunction, MinimumVersion
type: generated
generated: exp:help:examples
---
# setup.ini [ezcversion]

Der Block `[ezcversion]` von `settings/setup.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/setup.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/setup.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/setup.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/setup.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TestClass

> Die richtige Version der eZ Components anhand vorhandener  
> Klasse und Funktion prüfen, da es derzeit keine Möglichkeit gibt, Versionsinformationen zu erhalten  

Standard: `ezcBaseFile`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[ezcversion]
TestClass=ezcBaseFile
*/ ?>
```

## TestFunction

Standard: `walkrecursive`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[ezcversion]
TestFunction=walkrecursive
*/ ?>
```

## MinimumVersion

Standard: `2008.2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[ezcversion]
MinimumVersion=2008.2
*/ ?>
```

## Siehe auch

- [Alle Blöcke von setup.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
