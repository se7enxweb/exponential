---
title: exp:updateniceurls
audience: developer, admin
category: examples
summary: Import old URL aliases and update the URL aliases of all nodes (--update-nodes).
keywords: Konsole, Befehl, exp:updateniceurls, updateniceurls, console, command, CLI, updateniceurls.php
type: generated
generated: exp:help:examples
---
# exp:updateniceurls

Import old URL aliases and update the URL aliases of all nodes (--update-nodes).

Skript `bin/php/updateniceurls.php`, Code in `kernel/private/classes/commands/updateniceurls.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:updateniceurls --help
php bin/php/updateniceurls.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--db-host` | erforderlich | Datenbankhost |
| `--db-user` | erforderlich | Datenbankbenutzer |
| `--db-password` | erforderlich | Datenbankpasswort |
| `--db-database` | erforderlich | Datenbankname |
| `--db-type` | erforderlich | Datenbanktreiber, Alias für --db-driver |
| `--db-driver` | erforderlich | Datenbanktreiber |
| `--sql` |  | SQL-Abfragen anzeigen |
| `--no-import` |  | Disables all import routines. To enable specific ones use the --import-* options. |
| `--import` |  | Enables all import routines. |
| `--import-nodes` |  | Enables importing of urls from the old node data. |
| `--import-aliases` |  | Enables importing of old urls (system and customized). |
| `--import-redirections` |  | Enables importing of urls which redirects to the correct url (ie. history). |
| `--import-wildcards` |  | Enables importing of urls which redirects to the correct url using wildcards (ie. history). |
| `--no-update-nodes` |  | Disable updating of the urls of content object nodes. |
| `--update-nodes` |  | Enable updating of the urls of content object nodes. |
| `--verify-data` |  | Verify the database after new data has been inserted, this should only be used for debugging. |
| `--interactive` |  | Enables interactive mode for --verify-data, this will halt execution when database errors occurs and allow for manual inspection. |
| `--backup-tables` | erforderlich | Performs a backup of the ezurlalias and ezurlalias_ml tables after each stage is done (import or update), the backup tables will use the original name but with the suffix supplied to this option. Note: Use only for debugging and only on MySQL. |
| `--column-width` | erforderlich | The approximate width of the output block, defaults to 72. |
| `--fetch-limit` | erforderlich | The number of items to fetch in one go, increasing it may reduce total time but will also increase memory usage, defaults to 200. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/updateniceurls.php`, `kernel/private/classes/commands/updateniceurls.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
