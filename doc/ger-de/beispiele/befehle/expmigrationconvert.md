---
title: exp:expmigrationconvert
audience: developer, admin
category: examples
summary: "Wandelt YAML-Migrationen des Migrations-Bundles der Symfony-basierten Plattform in YAML-Migrationen von Exponential um: eine Datei oder jede .yml-Datei eines Ordners, Schritt für Schritt; was sich nicht umwandeln lässt, steht oben in der neuen Datei; ohne --apply ein Probelauf."
keywords: Konsole, Befehl, exp:expmigrationconvert, expmigrationconvert, console, command, CLI, expmigrationconvert.php, exp:migration:convert
type: generated
generated: exp:help:examples
---
# exp:expmigrationconvert

Wandelt YAML-Migrationen des Migrations-Bundles der Symfony-basierten Plattform in YAML-Migrationen von Exponential um: eine Datei oder jede .yml-Datei eines Ordners, Schritt für Schritt; was sich nicht umwandeln lässt, steht oben in der neuen Datei; ohne --apply ein Probelauf.

Skript `bin/php/expmigrationconvert.php`, Code in `kernel/private/classes/commands/expmigrationconvert.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationconvert --help
./console exp:migration:convert --help
php bin/php/expmigrationconvert.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Convert migrations of the migration bundle of the Symfony based platform
Reads a YAML migration of the bundle (a list of steps with type, mode, match, attributes and
references), or every .yml and .yaml file of a folder, and writes a YAML migration of Exponential
for each: content, content_type, content_type_group, location, role, section, user, user_group,
language, tag, sql, reference and trash steps convert; "reference:<name>" becomes ${ref.<name>}.
What does not convert (PHP and service calls, HTTP, mail, processes, loops, files, keys without a
counterpart) is never guessed: it is listed at the top of the new file, to be done by hand. Every
converted step is checked by its step type. The new file keeps the date and time of the old name and
goes into the site's migrations directory or, with --source=extension:<name>, the extension's. Without
--apply it shows the files and writes nothing; an existing file is never replaced.

./console exp:migration:convert var/import/20240115093000_create_news_folder.yml
./console exp:migration:convert var/import/MigrationVersions --apply
./console exp:migration:convert var/import/MigrationVersions --source=extension:expnews --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--source` | erforderlich | Die Quelle: site oder extension:<Name> (generate: wohin die Datei kommt; rollback: welche Migration, wenn zwei Quellen ihre ID haben) |
| `--author` | erforderlich | Der Autor in den Kopfzeilen der neuen Migration |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[file]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationconvert.php`, `kernel/private/classes/commands/expmigrationconvert.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
