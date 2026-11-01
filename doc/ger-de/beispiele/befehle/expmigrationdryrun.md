---
title: exp:expmigrationdryrun
audience: developer, admin
category: examples
summary: "Probelauf der offenen Migrationen: der Plan, und mit --simulate ein Lauf in einer Transaktion, die zurückgerollt wird (--diff zeigt, was sich geändert hat)."
keywords: Konsole, Befehl, exp:expmigrationdryrun, expmigrationdryrun, console, command, CLI, expmigrationdryrun.php, exp:migration:dry-run
type: generated
generated: exp:help:examples
---
# exp:expmigrationdryrun

Probelauf der offenen Migrationen: der Plan, und mit --simulate ein Lauf in einer Transaktion, die zurückgerollt wird (--diff zeigt, was sich geändert hat).

Skript `bin/php/expmigrationdryrun.php`, Code in `kernel/private/classes/commands/expmigrationdryrun.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationdryrun --help
./console exp:migration:dry-run --help
php bin/php/expmigrationdryrun.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Dry run of the pending migrations
exp:migration:migrate without --apply: every pending migration with what it would change, the
checksum guard and the check of the code trees, nothing written. --simulate runs the migrations with
up() in one transaction that is rolled back at the end, under the installation lock, where the
database undoes schema changes too (PostgreSQL, SQLite; elsewhere the plan is listed with a warning).
--diff adds what each migration changed: tables and columns added or dropped, rows per table. Files,
caches and the search index a migration writes are not undone by the rollback.

./console exp:migration:dry-run
./console exp:migration:dry-run --simulate --diff
./console exp:migration:dry-run --only=20261031120000_add_news_class --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--only` | erforderlich | Nur diese Migrationen (Migrations-IDs, als Komma-Liste oder wiederholt) |
| `--path` | erforderlich | Migrationsdateien oder -ordner, relativ zur Wurzel oder absolut (als Komma-Liste oder wiederholt): migrate und dry-run nehmen nur die Migrationen darin, check prüft sie statt der Migrationsverzeichnisse |
| `--environment` | erforderlich | Die Umgebung (Standard die der Installation: [AdminEnvironment] Level, oder EXP_ENV bei Level=auto; check: jede Umgebung); nur die Migrationen für sie zählen |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--login` | erforderlich | Als dieser Benutzer ausführen (Standard: [RunSettings] RunAsUser in migration.ini) |
| `--diff` |  | Mit --simulate: was jede Migration in der Datenbank geändert hat (Tabellen, Spalten, Zeilen) |
| `--simulate` |  | Die Migrationen in einer Transaktion ausführen, die zurückgerollt wird (PostgreSQL und SQLite; anderswo wird der Plan aufgelistet) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationdryrun.php`, `kernel/private/classes/commands/expmigrationdryrun.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
