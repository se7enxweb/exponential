---
title: exp:dfscleanup
audience: developer, admin
category: examples
summary: Check database and DFS files against each other (-S, -B) and delete nonexistent ones (-D).
keywords: Konsole, Befehl, exp:dfscleanup, dfscleanup, console, command, CLI, dfscleanup.php
type: generated
generated: exp:help:examples
---
# exp:dfscleanup

Check database and DFS files against each other (-S, -B) and delete nonexistent ones (-D).

Skript `bin/php/dfscleanup.php`, Code in `kernel/private/classes/commands/dfscleanup.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:dfscleanup --help
php bin/php/dfscleanup.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Script for checking database and DFS file consistency
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `-S` |  | Check files on DFS share against files in the database |
| `-B` |  | Checks files in database against files on DFS share |
| `-D` |  | Delete nonexistent files |
| `--path` | erforderlich | Path to limit checks to (e.g.: var/storage/content - Default: var/) |
| `--iteration-limit` | erforderlich | Amount of items to remove in each iteration when performing a purge operation. Default is all in one iteration. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/dfscleanup.php`, `kernel/private/classes/commands/dfscleanup.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
