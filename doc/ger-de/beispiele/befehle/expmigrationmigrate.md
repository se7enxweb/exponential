---
title: exp:expmigrationmigrate
audience: developer, admin
category: examples
summary: "Führt die offenen Migrationen aus: ein Probelauf ohne --apply; Sicherungspunkte davor und danach, Wiederherstellung bei einem Fehler, Commit, Tag und Push."
keywords: Konsole, Befehl, exp:expmigrationmigrate, expmigrationmigrate, console, command, CLI, expmigrationmigrate.php, exp:migration:migrate
type: generated
generated: exp:help:examples
---
# exp:expmigrationmigrate

Führt die offenen Migrationen aus: ein Probelauf ohne --apply; Sicherungspunkte davor und danach, Wiederherstellung bei einem Fehler, Commit, Tag und Push.

Skript `bin/php/expmigrationmigrate.php`, Code in `kernel/private/classes/commands/expmigrationmigrate.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationmigrate --help
./console exp:migration:migrate --help
php bin/php/expmigrationmigrate.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Run the pending migrations
Without --apply a dry run: every pending migration with what it would change, nothing written.
With --apply, under the installation lock (shared with exp:upgrade) and as [RunSettings] RunAsUser:
the code trees are checked for changes that are not committed, a caption is taken, the migrations
run in order (each in a transaction where the database has one), the run stops at the first failure
and restores the caption, a caption is taken after a run that passed, and the status file and run log
are committed, tagged and pushed in the migrations repository. A migration whose file changed after
it was applied stops the run unless --force --reason=<text> accepts it.

./console exp:migration:migrate
./console exp:migration:migrate --apply
./console exp:migration:migrate --only=20261031120000_add_news_class --apply
./console exp:migration:migrate --path=extension/expnews/migrations --environment=staging --apply --no-push
./console exp:migration:migrate --apply --max-migrations=20 --max-time=600 --throttle=0.5 --nice=10
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--only` | erforderlich | Nur diese Migrationen (Migrations-IDs, als Komma-Liste oder wiederholt) |
| `--path` | erforderlich | Migrationsdateien oder -ordner, relativ zur Wurzel oder absolut (als Komma-Liste oder wiederholt): migrate und dry-run nehmen nur die Migrationen darin, check prüft sie statt der Migrationsverzeichnisse |
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

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationmigrate.php`, `kernel/private/classes/commands/expmigrationmigrate.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
