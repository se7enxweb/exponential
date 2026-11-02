---
title: exp:expmigrationresume
audience: developer, admin
category: examples
summary: "Setzt einen fehlgeschlagenen oder abgebrochenen Migrationslauf fort: die noch offenen Migrationen laufen erneut; ein Probelauf ohne --apply."
keywords: Konsole, Befehl, exp:expmigrationresume, expmigrationresume, console, command, CLI, expmigrationresume.php, exp:migration:resume
type: generated
generated: exp:help:examples
---
# exp:expmigrationresume

Setzt einen fehlgeschlagenen oder abgebrochenen Migrationslauf fort: die noch offenen Migrationen laufen erneut; ein Probelauf ohne --apply.

Skript `bin/php/expmigrationresume.php`, Code in `kernel/private/classes/commands/expmigrationresume.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationresume --help
./console exp:migration:resume --help
php bin/php/expmigrationresume.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Resume a migration run
The run named, or the newest run that failed or was interrupted (its record says running, but no
process holds the installation lock any more). A run that is still running, that finished or that
was rolled back is not resumed. Resuming is a new run, in the environment of the old one, of the
migrations still pending: those the old run did not reach, the one that failed, and those the
restore after the failure took back. With --apply the record of an interrupted run is closed as
failed first. The options of the run are those of exp:migration:migrate.

./console exp:migration:resume
./console exp:migration:resume 20261031-120000-a1b2c3 --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--environment` | erforderlich | Die Umgebung (Standard die der Installation: [AdminEnvironment] Level, oder EXP_ENV bei Level=auto; check: jede Umgebung); nur die Migrationen für sie zählen |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--login` | erforderlich | Als dieser Benutzer ausführen (Standard: [RunSettings] RunAsUser in migration.ini) |
| `--no-caption` |  | Kein Sicherungspunkt vor und nach dem Lauf: ein Fehler kann nicht automatisch wiederhergestellt werden |
| `--no-restore` |  | Nach einem Fehler die Installation so lassen, wie der Fehler sie hinterließ, statt den vor dem Lauf angelegten Sicherungspunkt wiederherzustellen |
| `--include-uncommitted` |  | Ausführen, obwohl settings/, extension/ oder design/ nicht committete Änderungen haben, und diese im Sicherungspunkt aufbewahren |
| `--no-commit` |  | Die Statusdatei und das Laufprotokoll nicht in das Migrations-Repository committen (auch kein Tag und kein Push) |
| `--no-push` |  | Committen und taggen, aber nicht pushen |
| `--no-tag` |  | Committen und pushen, aber den Lauf nicht taggen |
| `--force` |  | Mit --reason: Migrationen annehmen, deren Datei sich nach dem Anwenden geändert hat (sie laufen nicht erneut) |
| `--reason` | erforderlich | Warum eine geänderte Migration angenommen wird (nötig mit --force; im Protokoll der Migrationen und im Audit-Log festgehalten) |
| `--run-id` | erforderlich | Die Kennung des zu startenden Laufs (Standard eine neue: <Datum>-<Uhrzeit>-<6 Hex-Ziffern>); die Administration vergibt sie, bevor sie den Lauf startet |
| `--progress` | erforderlich | Den Fortschritt des Laufs an diese Datei anhängen, ein JSON-Objekt pro Zeile (was die Administration zeigt, während der Lauf vor sich geht) |
| `--max-migrations` | erforderlich | Apply at most this many migrations; the rest stay pending for the next run (default [RunSettings] MaxMigrations, empty: all) |
| `--max-time` | erforderlich | Start no further migration after this many seconds; the rest stay pending for the next run (default [RunSettings] MaxTime) |
| `--throttle` | erforderlich | Pause this many seconds between two migrations and between the batches of a long migration (default [RunSettings] Throttle) |
| `--nice` | erforderlich | Run at this lower priority, 1 to 19 as nice(1) (default [RunSettings] Nice) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[run]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationresume.php`, `kernel/private/classes/commands/expmigrationresume.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
