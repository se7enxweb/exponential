---
title: exp:expmigrationpush
audience: developer, admin
category: examples
summary: Überträgt die Migrationsläufe, deren Push fehlschlug (Push-Zustand pending), an das Remote des Migrations-Repositorys; ein Probelauf ohne --apply.
keywords: Konsole, Befehl, exp:expmigrationpush, expmigrationpush, console, command, CLI, expmigrationpush.php, exp:migration:push
type: generated
generated: exp:help:examples
---
# exp:expmigrationpush

Überträgt die Migrationsläufe, deren Push fehlschlug (Push-Zustand pending), an das Remote des Migrations-Repositorys; ein Probelauf ohne --apply.

Skript `bin/php/expmigrationpush.php`, Code in `kernel/private/classes/commands/expmigrationpush.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationpush --help
./console exp:migration:push --help
php bin/php/expmigrationpush.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Push the migration runs that wait
A run whose push failed keeps its commit and tag in the local migrations repository and the push
state pending; the cronjob part migrationpush pushes them again, and so does this command. Without
--apply it lists the runs that wait. The branch and the tags go to [RepositorySettings] Remote, oldest
run first; each run's push state is recorded.

./console exp:migration:push
./console exp:migration:push --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--limit` | erforderlich | Höchstens so viele Läufe pushen (Standard 20) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationpush.php`, `kernel/private/classes/commands/expmigrationpush.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
