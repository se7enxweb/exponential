---
title: exp:expmigrationgenerate
audience: developer, admin
category: examples
summary: "Schreibt eine neue Migrationsdatei: --skeleton schreibt eine leere PHP-Migration mit Kopfzeilen, plan(), up() und down(); ein Probelauf ohne --apply."
keywords: Konsole, Befehl, exp:expmigrationgenerate, expmigrationgenerate, console, command, CLI, expmigrationgenerate.php, exp:migration:generate
type: generated
generated: exp:help:examples
---
# exp:expmigrationgenerate

Schreibt eine neue Migrationsdatei: --skeleton schreibt eine leere PHP-Migration mit Kopfzeilen, plan(), up() und down(); ein Probelauf ohne --apply.

Skript `bin/php/expmigrationgenerate.php`, Code in `kernel/private/classes/commands/expmigrationgenerate.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationgenerate --help
./console exp:migration:generate --help
php bin/php/expmigrationgenerate.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Write a new migration
--skeleton writes an empty PHP migration named YYYYMMDDHHMMSS_<name>.php after the time of now,
with the header (description, author), plan(), up() and down(), in the site's migrations directory
(the first [MigrationSettings] DirectoryList entry) or, with --source=extension:<name>, in the
extension's. The class is named after the name and the time, in the namespace Site\Migrations or
Extension\<Name>\Migrations. Without --apply it shows the file and writes nothing; an existing file is
never replaced.

./console exp:migration:generate --skeleton add_news_class
./console exp:migration:generate --skeleton add_news_class --description="The news class" --apply
./console exp:migration:generate --skeleton news_folder --source=extension:expnews --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--skeleton` |  | Eine leere PHP-Migration mit Kopfzeilen, plan(), up() und down() |
| `--source` | erforderlich | Die Quelle: site oder extension:<Name> (generate: wohin die Datei kommt; rollback: welche Migration, wenn zwei Quellen ihre ID haben) |
| `--description` | erforderlich | Die Beschreibung in den Kopfzeilen der neuen Migration |
| `--author` | erforderlich | Der Autor in den Kopfzeilen der neuen Migration |
| `--type` | erforderlich | Der Typ der neuen Migration: php (der Standard und vorerst der einzige) |
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[name]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationgenerate.php`, `kernel/private/classes/commands/expmigrationgenerate.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
