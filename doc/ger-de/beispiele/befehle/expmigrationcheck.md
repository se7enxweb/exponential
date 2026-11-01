---
title: exp:expmigrationcheck
audience: developer, admin
category: examples
summary: "Prüft Migrationsdateien, ohne sie auszuführen: Namen, Kopfzeilen, Reihenfolge, PHP-Syntax und Klassen (die Prüfung für CI)."
keywords: Konsole, Befehl, exp:expmigrationcheck, expmigrationcheck, console, command, CLI, expmigrationcheck.php, exp:migration:check
type: generated
generated: exp:help:examples
---
# exp:expmigrationcheck

Prüft Migrationsdateien, ohne sie auszuführen: Namen, Kopfzeilen, Reihenfolge, PHP-Syntax und Klassen (die Prüfung für CI).

Skript `bin/php/expmigrationcheck.php`, Code in `kernel/private/classes/commands/expmigrationcheck.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationcheck --help
./console exp:migration:check --help
php bin/php/expmigrationcheck.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Check migration files
The lint for CI, without a database: every migration of the migration directories (or of --path)
is checked for a valid file name, a header of known keys, requirements that exist and no cycle,
active extensions it requires, and, for a PHP migration, its syntax and exactly one class that
extends expMigrationPhp and loads; for a YAML migration, its YAML and every step against the
schema of its step type, with its conditions, loops and references. Nothing of a migration is run.
Exit 1 when something is wrong.

./console exp:migration:check
./console exp:migration:check --path=extension/expnews/migrations --format=json
./console exp:migration:check --environment=production
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--path` | erforderlich | Migrationsdateien oder -ordner, relativ zur Wurzel oder absolut (als Komma-Liste oder wiederholt): migrate und dry-run nehmen nur die Migrationen darin, check prüft sie statt der Migrationsverzeichnisse |
| `--environment` | erforderlich | Die Umgebung (Standard die der Installation: [AdminEnvironment] Level, oder EXP_ENV bei Level=auto; check: jede Umgebung); nur die Migrationen für sie zählen |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationcheck.php`, `kernel/private/classes/commands/expmigrationcheck.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
