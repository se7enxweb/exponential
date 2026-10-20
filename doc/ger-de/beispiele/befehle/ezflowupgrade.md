---
title: exp:ezflowupgrade
audience: developer, admin
category: examples
summary: Upgrade the installed ezflow packages (--to-version, --package, --url).
keywords: Konsole, Befehl, exp:ezflowupgrade, ezflowupgrade, console, command, CLI, ezflowupgrade.php
type: generated
generated: exp:help:examples
---
# exp:ezflowupgrade

Upgrade the installed ezflow packages (--to-version, --package, --url).

Skript `bin/php/ezflowupgrade.php`, Code in `kernel/private/classes/commands/ezflowupgrade.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezflowupgrade --help
php bin/php/ezflowupgrade.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--to-version` | erforderlich | Specify what upgrade path to use.   available options: '1.1-0' - upgrade 1.0-0 to 1.1-0 |
| `--repository` | erforderlich | Path to repository where unpacked(unarchived) packages are  placed. it's relative to 'var/[site.ini].[FileSettings].[StorageDir]/[package.ini].[RepositorySettings].[RepositoryDirectory]'  (default is 'var/storage/packages/eZ-systems') |
| `--package` | erforderlich | Package(s) to install, f.e. 'ezflow_classes' |
| `--package-dir` | erforderlich | Path to directory with packed(ezpkg) packages(default is '/tmp/ezflow')  |
| `--url` | erforderlich | URL to download packages, f.e. 'http://packages.ez.no/Exponential/3.9'. 'package-dir' can be specified to store uploaded packages on local computer. if 'package-dir' is not specified then default dir('/tmp/ezflow') will be used. |
| `--auto-mode` | erforderlich | [on/off]. Do not ask what to do in case of confilicts. By default is 'on' |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezflowupgrade.php`, `kernel/private/classes/commands/ezflowupgrade.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
