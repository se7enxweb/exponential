---
title: exp:ezimportdbafile
audience: developer, admin
category: examples
summary: Importiert die dba-Daten eines Datentyps in die Datenbank (--datatype=ezisbn).
keywords: Konsole, Befehl, exp:ezimportdbafile, ezimportdbafile, console, command, CLI, ezimportdbafile.php
type: generated
generated: exp:help:examples
---
# exp:ezimportdbafile

Importiert die dba-Daten eines Datentyps in die Datenbank (--datatype=ezisbn).

Skript `bin/php/ezimportdbafile.php`, Code in `kernel/private/classes/commands/ezimportdbafile.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezimportdbafile --help
php bin/php/ezimportdbafile.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--datatype` | erforderlich | Der Name des Datentyps, für den die Datenbank aktualisiert werden soll. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezimportdbafile.php`, `kernel/private/classes/commands/ezimportdbafile.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
