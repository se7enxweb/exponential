---
title: exp:systeminfo
audience: developer, admin
category: examples
summary: The facts and health checks of Setup > System information, as text or JSON (--json, --sizes).
keywords: Konsole, Befehl, exp:systeminfo, systeminfo, console, command, CLI, systeminfo.php, exp:system:info
type: generated
generated: exp:help:examples
---
# exp:systeminfo

The facts and health checks of Setup > System information, as text or JSON (--json, --sizes).

Skript `bin/php/systeminfo.php`, Code in `kernel/private/classes/commands/systeminfo.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:systeminfo --help
./console exp:system:info --help
php bin/php/systeminfo.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
System information
The report of Setup > System information for this command-line PHP: Exponential, PHP, OPcache,
the database, storage, caches, cronjobs, mail, locale, the extensions and the health checks.
The web server's own PHP (PHP-FPM, Velocity) can differ: its report is on the page.
Nothing is changed, and no password, key, token or session id is printed.

./console exp:system:info
./console exp:system:info --json
./console exp:system:info --sizes      also measure var/, the cache directories and the database

Exit 0 when no check fails, 1 when one does.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  | One JSON object instead of the text |
| `--sizes` |  | Also measure the size of var/, of each cache directory and of the database |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/systeminfo.php`, `kernel/private/classes/commands/systeminfo.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
