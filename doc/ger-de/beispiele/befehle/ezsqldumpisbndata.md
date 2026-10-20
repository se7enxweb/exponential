---
title: exp:ezsqldumpisbndata
audience: developer, admin
category: examples
summary: Schreibt die ISBN-Gruppen- und -Bereichstabellen als SQL in eine Datei oder auf die Standardausgabe.
keywords: Konsole, Befehl, exp:ezsqldumpisbndata, ezsqldumpisbndata, console, command, CLI, ezsqldumpisbndata.php
type: generated
generated: exp:help:examples
---
# exp:ezsqldumpisbndata

Schreibt die ISBN-Gruppen- und -Bereichstabellen als SQL in eine Datei oder auf die Standardausgabe.

Skript `bin/php/ezsqldumpisbndata.php`, Code in `kernel/private/classes/commands/ezsqldumpisbndata.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezsqldumpisbndata --help
php bin/php/ezsqldumpisbndata.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--stdout-sql` |  | Die sql-Ausgabe wird auf der Standardausgabe statt in eine Datei geschrieben. |
| `--stdout-dba` |  | Die dba-Ausgabe wird auf der Standardausgabe statt in eine Datei geschrieben. |
| `--filename-sql` | erforderlich | Eigener Name für die sql-Datei. Sie wird im Verzeichnis gespeichert:  kernel/classes/datatypes/ezisbn/sql/<database>/ |
| `--filename-dba` | erforderlich | Eigener Name für die dba-Datei. Sie wird im Verzeichnis gespeichert:  kernel/classes/datatypes/ezisbn/share/ |
| `--db-host` | erforderlich | Datenbankhost. |
| `--db-user` | erforderlich | Datenbankbenutzer. |
| `--db-password` | erforderlich | Datenbankpasswort. |
| `--db-database` | erforderlich | Datenbankname. |
| `--db-driver` | erforderlich | Datenbanktreiber. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezsqldumpisbndata.php`, `kernel/private/classes/commands/ezsqldumpisbndata.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
