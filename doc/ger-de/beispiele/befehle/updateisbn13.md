---
title: exp:updateisbn13
audience: developer, admin
category: examples
summary: Update the ISBN range data in the database from an XML file (--url is required).
keywords: Konsole, Befehl, exp:updateisbn13, updateisbn13, console, command, CLI, updateisbn13.php
type: generated
generated: exp:help:examples
---
# exp:updateisbn13

Update the ISBN range data in the database from an XML file (--url is required).

Skript `bin/php/updateisbn13.php`, Code in `kernel/private/classes/commands/updateisbn13.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:updateisbn13 --help
php bin/php/updateisbn13.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Exponential ISBN-13 update

Update the database with new updated ISBN data to the database.

Example: ./bin/php/updateisbn13.php --url=<url of the xml file with the ranges>
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--url` | erforderlich | URL containing the xml file for the different ranges |
| `--db-host` | erforderlich | Database host. |
| `--db-user` | erforderlich | Database user. |
| `--db-password` | erforderlich | Database password. |
| `--db-database` | erforderlich | Database name. |
| `--db-driver` | erforderlich | Database driver. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/updateisbn13.php`, `kernel/private/classes/commands/updateisbn13.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
