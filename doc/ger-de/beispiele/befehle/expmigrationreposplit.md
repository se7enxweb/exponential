---
title: exp:expmigrationreposplit
audience: developer, admin
category: examples
summary: Trennt den Migrationsordner des Git-Checkouts der Installation mit seiner Historie in ein eigenes Repository ab (jeder Commit, der ihn geändert hat, mit denselben Autoren, Daten und Nachrichten); ohne --apply ein Probelauf.
keywords: Konsole, Befehl, exp:expmigrationreposplit, expmigrationreposplit, console, command, CLI, expmigrationreposplit.php, exp:migration:repo:split
type: generated
generated: exp:help:examples
---
# exp:expmigrationreposplit

Trennt den Migrationsordner des Git-Checkouts der Installation mit seiner Historie in ein eigenes Repository ab (jeder Commit, der ihn geändert hat, mit denselben Autoren, Daten und Nachrichten); ohne --apply ein Probelauf.

Skript `bin/php/expmigrationreposplit.php`, Code in `kernel/private/classes/commands/expmigrationreposplit.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationreposplit --help
./console exp:migration:repo:split --help
php bin/php/expmigrationreposplit.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Split the migrations folder into a repository of its own, with its history
Every commit of the installation's checkout that changed the folder becomes a commit of the folder
alone (same author, committer, dates and message), on the branch --branch of the checkout (default
migrations-split; a branch that exists is never moved). With --to a new repository is made in that
new or empty folder with the history as its branch main and, with --remote, the remote origin; it is
never pushed. Neither the settings nor the files of the site are changed: the report says what to set
in migration.ini ([RepositorySettings] Mode=separate, Path) and how to take the folder out of the
site's checkout. Needs only git (no git subtree). Without --apply it says what it would do.

./console exp:migration:repo:split
./console exp:migration:repo:split --to=../site-migrations --apply
./console exp:migration:repo:split --to=../site-migrations --remote=git@github.com:example/site-migrations.git --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--directory` | erforderlich | Der abzutrennende Migrationsordner (Vorgabe der erste Eintrag von [MigrationSettings] DirectoryList) |
| `--branch` | erforderlich | Der Branch des Checkouts, in den die abgetrennte Historie kommt (Vorgabe migrations-split; nie ein vorhandener) |
| `--to` | erforderlich | Ein neuer oder leerer Ordner für das neue Repository (sein Branch main); ohne ihn wird nur der Branch angelegt |
| `--remote` | erforderlich | Das Remote origin des neuen Repositorys (eine URL oder ein Pfad; es wird nicht gepusht) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationreposplit.php`, `kernel/private/classes/commands/expmigrationreposplit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
