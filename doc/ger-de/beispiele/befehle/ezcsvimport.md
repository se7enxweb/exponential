---
title: exp:ezcsvimport
audience: developer, admin
category: examples
summary: Import content objects of a class below a node from a CSV file (--class, --creator).
keywords: Konsole, Befehl, exp:ezcsvimport, ezcsvimport, console, command, CLI, ezcsvimport.php
type: generated
generated: exp:help:examples
---
# exp:ezcsvimport

Import content objects of a class below a node from a CSV file (--class, --creator).

Skript `bin/php/ezcsvimport.php`, Code in `kernel/private/classes/commands/ezcsvimport.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezcsvimport --help
php bin/php/ezcsvimport.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--class` | erforderlich | class identifier to create objects |
| `--creator` | erforderlich | user id of imported objects creator |
| `--storage-dir` | erforderlich | path to directory which will be added to the path of CSV elements |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[node][file]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezcsvimport.php`, `kernel/private/classes/commands/ezcsvimport.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
