---
title: migration.ini [YamlSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [YamlSettings] in migration.ini: LibraryPath"
keywords: Einstellung, migration.ini, YamlSettings, INI, setting, LibraryPath
type: generated
generated: exp:help:examples
---
# migration.ini [YamlSettings]

Der Block `[YamlSettings]` von `settings/migration.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LibraryPath

> YAML-Migrationen (YYYYMMDDHHMMSS\_name.yml) werden mit symfony/yaml gelesen, das composer.json verlangt. Eine  
> Installation, die es außerhalb ihres Composer-vendor/ hält, nennt hier den Ordner: ein Composer-Verzeichnis (mit  
> vendor/autoload.php) oder das Paket selbst (den Ordner mit Yaml.php), relativ zur Wurzel oder absolut. Die  
> Umgebungsvariable MIGRATION\_YAML\_LIBRARY\_PATH wird genauso gelesen, wenn dies leer ist.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[YamlSettings]
LibraryPath=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
