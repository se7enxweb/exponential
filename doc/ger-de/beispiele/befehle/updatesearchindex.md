---
title: exp:updatesearchindex
audience: developer, admin
category: examples
summary: Indiziert alle Inhaltsobjekte in der Suchmaschine neu.
keywords: Konsole, Befehl, exp:updatesearchindex, updatesearchindex, console, command, CLI, updatesearchindex.php
type: generated
generated: exp:help:examples
---
# exp:updatesearchindex

Indiziert alle Inhaltsobjekte in der Suchmaschine neu.

Skript `bin/php/updatesearchindex.php`, Code in `kernel/private/classes/commands/updatesearchindex.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:updatesearchindex --help
php bin/php/updatesearchindex.php --help
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
| `--clean` |  | Vor dem Indizieren alle Suchdaten entfernen |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/updatesearchindex.php`, `kernel/private/classes/commands/updatesearchindex.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
