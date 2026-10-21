---
title: exp:flatten
audience: developer, admin
category: examples
summary: "Entfernt ungenutzte Daten aus der Datenbank: contentobject, contentclass, workflow, role oder all."
keywords: Konsole, Befehl, exp:flatten, flatten, console, command, CLI, flatten.php
type: generated
generated: exp:help:examples
---
# exp:flatten

Entfernt ungenutzte Daten aus der Datenbank: contentobject, contentclass, workflow, role oder all.

Skript `bin/php/flatten.php`, Code in `kernel/private/classes/commands/flatten.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:flatten --help
php bin/php/flatten.php --help
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

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[name]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/flatten.php`, `kernel/private/classes/commands/flatten.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
