---
title: exp:ezsqldumpisbndata
audience: developer, admin
category: examples
summary: Dump the ISBN group and range tables as SQL to a file or standard output.
keywords: Konsole, Befehl, exp:ezsqldumpisbndata, ezsqldumpisbndata, console, command, CLI, ezsqldumpisbndata.php
type: generated
generated: exp:help:examples
---
# exp:ezsqldumpisbndata

Dump the ISBN group and range tables as SQL to a file or standard output.

Skript `bin/php/ezsqldumpisbndata.php`, Code in `kernel/private/classes/commands/ezsqldumpisbndata.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezsqldumpisbndata --help
php bin/php/ezsqldumpisbndata.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--stdout-sql` |  | Result of sql output will be printed to standard output instead of to file. |
| `--stdout-dba` |  | Result of dba output will be printed to standard output instead of to file. |
| `--filename-sql` | erforderlich | Custom name for the sql file. Will be stored in the directory:  kernel/classes/datatypes/ezisbn/sql/<database>/ |
| `--filename-dba` | erforderlich | Custom name for the dba file. Will be stored in the directory:  kernel/classes/datatypes/ezisbn/share/ |
| `--db-host` | erforderlich | Database host. |
| `--db-user` | erforderlich | Database user. |
| `--db-password` | erforderlich | Database password. |
| `--db-database` | erforderlich | Database name. |
| `--db-driver` | erforderlich | Database driver. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezsqldumpisbndata.php`, `kernel/private/classes/commands/ezsqldumpisbndata.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
