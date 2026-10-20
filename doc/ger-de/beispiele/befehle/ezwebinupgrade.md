---
title: exp:ezwebinupgrade
audience: developer, admin
category: examples
summary: Upgrade an existing ezwebin installation to the current package version (--to-version).
keywords: Konsole, Befehl, exp:ezwebinupgrade, ezwebinupgrade, console, command, CLI, ezwebinupgrade.php
type: generated
generated: exp:help:examples
---
# exp:ezwebinupgrade

Upgrade an existing ezwebin installation to the current package version (--to-version).

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
| `--to-version` | erforderlich | Specify what upgrade path to use.   available options: '1.2-0' - upgrade 1.1-1 to 1.2-0                     '1.3-0' - upgrade 1.2-0 to 1.3-0                     '1.4-0' - upgrade 1.3-0 to 1.4-0 |
| `--repository` | erforderlich | Path to repository where unpacked(unarchived) packages are  placed. it's relative to 'var/[site.ini].[FileSettings].[StorageDir]/[package.ini].[RepositorySettings].[RepositoryDirectory]'  (default is 'var/storage/packages/ez_systems') |
| `--package` | erforderlich | Package(s) to install, f.e. 'ezwebin_classes' |
| `--package-dir` | erforderlich | Path to directory with packed(ezpkg) packages(default is '/tmp/ezwebin')  |
| `--url` | erforderlich | URL to download packages, f.e. 'http://packages.ez.no/Exponential/3.9'. 'package-dir' can be specified to store uploaded packages on local computer. if 'package-dir' is not specified then default dir('/tmp/ezwebin') will be used. |
| `--auto-mode` | erforderlich | [on/off]. Do not ask what to do in case of confilicts. By default is 'on' |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezwebinupgrade.php`, `kernel/private/classes/commands/ezwebinupgrade.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
