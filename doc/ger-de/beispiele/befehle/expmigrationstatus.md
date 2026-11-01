---
title: exp:expmigrationstatus
audience: developer, admin
category: examples
summary: "Migrationsstatus: jede Migration mit ihrem Zustand (done, pending, not-needed, failed, unknown), die letzten Läufe und was wartet."
keywords: Konsole, Befehl, exp:expmigrationstatus, expmigrationstatus, console, command, CLI, expmigrationstatus.php, exp:migration:status
type: generated
generated: exp:help:examples
---
# exp:expmigrationstatus

Migrationsstatus: jede Migration mit ihrem Zustand (done, pending, not-needed, failed, unknown), die letzten Läufe und was wartet.

Skript `bin/php/expmigrationstatus.php`, Code in `kernel/private/classes/commands/expmigrationstatus.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationstatus --help
./console exp:migration:status --help
php bin/php/expmigrationstatus.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Migration status
Every migration of the site, the active extensions and the installed packages for the environment,
in the order they run, with its state (done, pending, not-needed, failed, unknown) and its record;
the records whose file is gone, the latest runs, the runs whose push waits and who holds the lock.
Changes nothing, takes no lock.

./console exp:migration:status
./console exp:migration:status --environment=production --format=json
./console exp:migration:status --fail-on-pending     exit 1 when something is pending, for CI
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--only` | erforderlich | Nur diese Migrationen (Migrations-IDs, als Komma-Liste oder wiederholt) |
| `--environment` | erforderlich | Die Umgebung (Standard die der Installation: [AdminEnvironment] Level, oder EXP_ENV bei Level=auto; check: jede Umgebung); nur die Migrationen für sie zählen |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--fail-on-pending` |  | Exit-Code 1, wenn Migrationen offen oder fehlgeschlagen sind (für CI) |
| `--runs` | erforderlich | Wie viele der letzten Läufe aufgelistet werden (Standard 10, 0 für keine) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationstatus.php`, `kernel/private/classes/commands/expmigrationstatus.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
