---
title: exp:ezflowupgrade
audience: developer, admin
category: examples
summary: Aktualisiert die installierten ezflow-Pakete (--to-version, --package, --url).
keywords: Konsole, Befehl, exp:ezflowupgrade, ezflowupgrade, console, command, CLI, ezflowupgrade.php
type: generated
generated: exp:help:examples
---
# exp:ezflowupgrade

Aktualisiert die installierten ezflow-Pakete (--to-version, --package, --url).

Skript `bin/php/ezflowupgrade.php`, Code in `kernel/private/classes/commands/ezflowupgrade.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezflowupgrade --help
php bin/php/ezflowupgrade.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--to-version` | erforderlich | Den zu verwendenden Upgrade-Pfad angeben.   Verfügbare Optionen: '1.1-0' - Upgrade von 1.0-0 auf 1.1-0 |
| `--repository` | erforderlich | Pfad zum Repository, in dem die entpackten Pakete  liegen. Er ist relativ zu 'var/[site.ini].[FileSettings].[StorageDir]/[package.ini].[RepositorySettings].[RepositoryDirectory]'  (Standard ist 'var/storage/packages/eZ-systems') |
| `--package` | erforderlich | Zu installierende(s) Paket(e), z. B. 'ezflow_classes' |
| `--package-dir` | erforderlich | Pfad zum Verzeichnis mit gepackten (ezpkg) Paketen (Standard ist '/tmp/ezflow')  |
| `--url` | erforderlich | URL zum Herunterladen der Pakete, z. B. 'http://packages.ez.no/Exponential/3.9'. Mit 'package-dir' können die heruntergeladenen Pakete auf dem lokalen Rechner gespeichert werden. Ohne 'package-dir' wird das Standardverzeichnis ('/tmp/ezflow') verwendet. |
| `--auto-mode` | erforderlich | [on/off]. Bei Konflikten nicht nachfragen. Standard ist 'on' |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezflowupgrade.php`, `kernel/private/classes/commands/ezflowupgrade.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
