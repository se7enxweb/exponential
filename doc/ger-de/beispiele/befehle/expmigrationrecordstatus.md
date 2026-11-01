---
title: exp:expmigrationrecordstatus
audience: developer, admin
category: examples
summary: "Die laufenden Aufzeichnungen von Änderungen: das websiteweite Fenster und die eigene Aufzeichnung des Benutzers, mit dem bisher Aufgezeichneten; repariert die Datei der laufenden Aufzeichnungen."
keywords: Konsole, Befehl, exp:expmigrationrecordstatus, expmigrationrecordstatus, console, command, CLI, expmigrationrecordstatus.php, exp:migration:record:status
type: generated
generated: exp:help:examples
---
# exp:expmigrationrecordstatus

Die laufenden Aufzeichnungen von Änderungen: das websiteweite Fenster und die eigene Aufzeichnung des Benutzers, mit dem bisher Aufgezeichneten; repariert die Datei der laufenden Aufzeichnungen.

Skript `bin/php/expmigrationrecordstatus.php`, Code in `kernel/private/classes/commands/expmigrationrecordstatus.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationrecordstatus --help
./console exp:migration:record:status --help
php bin/php/expmigrationrecordstatus.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The recordings of changes that run
Every recording that runs (the site-wide window and the session recordings of users), how many events
each has, and whether --user (default: [RunSettings] RunAsUser) is recorded now. When the file of the
running recordings in var/ does not match the database (var/ was cleared, a server was added) it is
written again, and a warning says so. Changes nothing else.

./console exp:migration:record:status
./console exp:migration:record:status --user=editor --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--user` | erforderlich | Der Benutzer, nach Login, dessen eigene Änderungen eine Sitzungsaufzeichnung festhält (Standard: [RunSettings] RunAsUser der migration.ini) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationrecordstatus.php`, `kernel/private/classes/commands/expmigrationrecordstatus.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
