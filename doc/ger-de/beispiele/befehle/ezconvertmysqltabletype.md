---
title: exp:ezconvertmysqltabletype
audience: developer, admin
category: examples
summary: Convert the tables of a MySQL database to another table type (--list, --newtype=TYPE).
keywords: Konsole, Befehl, exp:ezconvertmysqltabletype, ezconvertmysqltabletype, console, command, CLI, ezconvertmysqltabletype.php
type: generated
generated: exp:help:examples
---
# exp:ezconvertmysqltabletype

Convert the tables of a MySQL database to another table type (--list, --newtype=TYPE).

Skript `bin/php/ezconvertmysqltabletype.php`, Code in `kernel/private/classes/commands/ezconvertmysqltabletype.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezconvertmysqltabletype --help
php bin/php/ezconvertmysqltabletype.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--host` | erforderlich | Connect to host database |
| `--user` | erforderlich | User for login to the database |
| `--password` | erforderlich | Password to use when connecting to the database |
| `--database` | erforderlich |  |
| `--list` |  | List the table types |
| `--newtype` | erforderlich | Convert the database to the given type. Type can either be: myisam or innodb Make sure that you have made a BACKUP UP of YOUR DATABASE! |
| `--usecopy` |  | To convert the table we rename the original table and copy the data to the new table structure. This conversion method is much slower and has a higher risk to corrupt the data in the database. However this option may circumvent the MySQL crash on the ALTER query. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezconvertmysqltabletype.php`, `kernel/private/classes/commands/ezconvertmysqltabletype.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
