---
title: exp:ezsqlinsertschema
audience: developer, admin
category: examples
summary: Insert a database schema and data from a .dba file into a database (--type, --user).
keywords: Konsole, Befehl, exp:ezsqlinsertschema, ezsqlinsertschema, console, command, CLI, ezsqlinsertschema.php
type: generated
generated: exp:help:examples
---
# exp:ezsqlinsertschema

Insert a database schema and data from a .dba file into a database (--type, --user).

Skript `bin/php/ezsqlinsertschema.php`, Code in `kernel/private/classes/commands/ezsqlinsertschema.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezsqlinsertschema --help
php bin/php/ezsqlinsertschema.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--type` | erforderlich |  |
| `--user` | erforderlich |  |
| `--host` | erforderlich |  |
| `--password` | optional |  |
| `--port` | erforderlich |  |
| `--socket` | erforderlich |  |
| `--table-type` | erforderlich |  |
| `--table-charset` | erforderlich |  |
| `--insert-types` | erforderlich |  |
| `--allow-multi-insert` |  |  |
| `--schema-file` | erforderlich |  |
| `--clean-existing` |  |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[filename][database]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezsqlinsertschema.php`, `kernel/private/classes/commands/ezsqlinsertschema.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
