---
title: exp:ezsqldumpschema
audience: developer, admin
category: examples
summary: Dump the database schema to a file or standard output (--type, --user).
keywords: Konsole, Befehl, exp:ezsqldumpschema, ezsqldumpschema, console, command, CLI, ezsqldumpschema.php
type: generated
generated: exp:help:examples
---
# exp:ezsqldumpschema

Dump the database schema to a file or standard output (--type, --user).

Skript `bin/php/ezsqldumpschema.php`, Code in `kernel/private/classes/commands/ezsqldumpschema.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezsqldumpschema --help
php bin/php/ezsqldumpschema.php --help
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
| `--output-array` |  |  |
| `--output-serialized` |  |  |
| `--output-sql` |  |  |
| `--diff-friendly` |  |  |
| `--meta-data` |  |  |
| `--table-type` | erforderlich |  |
| `--table-charset` | erforderlich |  |
| `--compatible-sql` |  |  |
| `--no-sort` |  |  |
| `--format` | erforderlich |  |
| `--output-types` | erforderlich |  |
| `--allow-multi-insert` |  |  |
| `--schema-file` | erforderlich |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[database][filename]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezsqldumpschema.php`, `kernel/private/classes/commands/ezsqldumpschema.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
