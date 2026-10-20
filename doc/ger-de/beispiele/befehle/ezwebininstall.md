---
title: exp:ezwebininstall
audience: developer, admin
category: examples
summary: Install the ezwebin site package and configure the site.
keywords: Konsole, Befehl, exp:ezwebininstall, ezwebininstall, console, command, CLI, ezwebininstall.php
type: generated
generated: exp:help:examples
---
# exp:ezwebininstall

Install the ezwebin site package and configure the site.

Skript `bin/php/ezwebininstall.php`, Code in `kernel/private/classes/commands/ezwebininstall.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezwebininstall --help
php bin/php/ezwebininstall.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--repository` | erforderlich | Path to repository where unpacked(unarchived) packages are  placed. it's relative to 'var/[site.ini].[FileSettings].[StorageDir]/[package.ini].[RepositorySettings].[RepositoryDirectory]'  (default is 'var/storage/packages/ez_systems') |
| `--package` | erforderlich |  |
| `--package-dir` | erforderlich | Path to directory with packed(ezpkg) packages(default is '/tmp/ezwebin')  |
| `--url` | erforderlich | URL to download packages, f.e. 'http://packages.ez.no/Exponential/3.9'. 'package-dir' can be specified to store uploaded packages on local computer. if 'package-dir' is not specified then default dir('/tmp/ezwebin') will be used. |
| `--admin-siteaccess` | erforderlich | Will be used as base for eZWebin admin siteaccess |
| `--user-siteaccess` | erforderlich | Will be used as base for eZWebin user siteaccesses |
| `--auto-mode` | erforderlich | [on/off]. Do not ask what to do in case of confilicts. By default is 'on' |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezwebininstall.php`, `kernel/private/classes/commands/ezwebininstall.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
