---
title: exp:ezwebininstall
audience: developer, admin
category: examples
summary: Installiert das Seitenpaket ezwebin und konfiguriert die Website.
keywords: Konsole, Befehl, exp:ezwebininstall, ezwebininstall, console, command, CLI, ezwebininstall.php
type: generated
generated: exp:help:examples
---
# exp:ezwebininstall

Installiert das Seitenpaket ezwebin und konfiguriert die Website.

Skript `bin/php/ezwebininstall.php`, Code in `kernel/private/classes/commands/ezwebininstall.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezwebininstall --help
php bin/php/ezwebininstall.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--repository` | erforderlich | Pfad zum Repository, in dem die entpackten Pakete  liegen. Er ist relativ zu 'var/[site.ini].[FileSettings].[StorageDir]/[package.ini].[RepositorySettings].[RepositoryDirectory]'  (Standard ist 'var/storage/packages/ez_systems') |
| `--package` | erforderlich |  |
| `--package-dir` | erforderlich | Pfad zum Verzeichnis mit gepackten (ezpkg) Paketen (Standard ist '/tmp/ezwebin')  |
| `--url` | erforderlich | URL zum Herunterladen der Pakete, z. B. 'http://packages.ez.no/Exponential/3.9'. Mit 'package-dir' können die heruntergeladenen Pakete auf dem lokalen Rechner gespeichert werden. Ohne 'package-dir' wird das Standardverzeichnis ('/tmp/ezwebin') verwendet. |
| `--admin-siteaccess` | erforderlich | Wird als Basis für den Administrations-Siteaccess von eZWebin verwendet |
| `--user-siteaccess` | erforderlich | Wird als Basis für die Benutzer-Siteaccesses von eZWebin verwendet |
| `--auto-mode` | erforderlich | [on/off]. Bei Konflikten nicht nachfragen. Standard ist 'on' |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezwebininstall.php`, `kernel/private/classes/commands/ezwebininstall.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
