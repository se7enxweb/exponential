---
title: exp:ezwebinupgrade
audience: developer, admin
category: examples
summary: Aktualisiert eine vorhandene ezwebin-Installation auf die aktuelle Paketversion (--to-version).
keywords: Konsole, Befehl, exp:ezwebinupgrade, ezwebinupgrade, console, command, CLI, ezwebinupgrade.php
type: generated
generated: exp:help:examples
---
# exp:ezwebinupgrade

Aktualisiert eine vorhandene ezwebin-Installation auf die aktuelle Paketversion (--to-version).

Skript `bin/php/ezwebinupgrade.php`, Code in `kernel/private/classes/commands/ezwebinupgrade.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezwebinupgrade --help
php bin/php/ezwebinupgrade.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
intro
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--to-version` | erforderlich | Den zu verwendenden Upgrade-Pfad angeben.   Verfügbare Optionen: '1.2-0' - Upgrade von 1.1-1 auf 1.2-0                     '1.3-0' - Upgrade von 1.2-0 auf 1.3-0                     '1.4-0' - Upgrade von 1.3-0 auf 1.4-0 |
| `--repository` | erforderlich | Pfad zum Repository, in dem die entpackten Pakete  liegen. Er ist relativ zu 'var/[site.ini].[FileSettings].[StorageDir]/[package.ini].[RepositorySettings].[RepositoryDirectory]'  (Standard ist 'var/storage/packages/ez_systems') |
| `--package` | erforderlich | Zu installierende(s) Paket(e), z. B. 'ezwebin_classes' |
| `--package-dir` | erforderlich | Pfad zum Verzeichnis mit gepackten (ezpkg) Paketen (Standard ist '/tmp/ezwebin')  |
| `--url` | erforderlich | URL zum Herunterladen der Pakete, z. B. 'http://packages.ez.no/Exponential/3.9'. Mit 'package-dir' können die heruntergeladenen Pakete auf dem lokalen Rechner gespeichert werden. Ohne 'package-dir' wird das Standardverzeichnis ('/tmp/ezwebin') verwendet. |
| `--auto-mode` | erforderlich | [on/off]. Bei Konflikten nicht nachfragen. Standard ist 'on' |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezwebinupgrade.php`, `kernel/private/classes/commands/ezwebinupgrade.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
