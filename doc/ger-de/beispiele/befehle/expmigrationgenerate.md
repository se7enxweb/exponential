---
title: exp:expmigrationgenerate
audience: developer, admin
category: examples
summary: "Schreibt eine neue Migrationsdatei: --skeleton schreibt eine leere Migration (PHP, oder mit --type=yml, sql, ini oder graphql diese Art); --from-recording macht aus einer Aufzeichnung von Änderungen eine YAML-Migration; --from-content, --from-class, --from-role, --from-section ..."
keywords: Konsole, Befehl, exp:expmigrationgenerate, expmigrationgenerate, console, command, CLI, expmigrationgenerate.php, exp:migration:generate
type: generated
generated: exp:help:examples
---
# exp:expmigrationgenerate

Schreibt eine neue Migrationsdatei: --skeleton schreibt eine leere Migration (PHP, oder mit --type=yml, sql, ini oder graphql diese Art); --from-recording macht aus einer Aufzeichnung von Änderungen eine YAML-Migration; --from-content, --from-class, --from-role, --from-section und --from-settings machen eine aus dem, was die Installation hat; --from-diff macht eine aus dem, was sich seit einem Sicherungspunkt, gegenüber einer anderen Installation oder einem Datenbank-Dump geändert hat; ein Probelauf ohne --apply.

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
Extension\<Name>\Migrations. --type=yml writes YYYYMMDDHHMMSS_<name>.yml instead (the header and an
empty list of steps), --type=graphql the same with an example of the step graphql, --type=ini an INI
migration and --type=sql an SQL migration (with --engine=mysql, pgsql, sqlite or oracle the file of one
engine, with --engine=mongodb the .mongodb.json file). Without --apply it shows the file and writes
nothing; an existing file is never replaced.
--from-recording=<id> turns a recording of changes (exp:migration:record:start) into a YAML
migration: its events folded into one step per thing changed, checked as exp:migration:check checks
a file. What no step type repeats is listed, and named in the file, to be done by hand.
--from-content=<node>[,<node>...] (with --depth=<levels below> and --max-objects=<n>), --from-class,
--from-role, --from-section (identifiers or names, comma-separated) and --from-settings=<file>[,...]
(of settings/override, or with --siteaccess=<name> of that siteaccess) write a YAML migration that
makes those things as they are now (mode ensure). Personal data stays out: a user is named by
secrets, and secret settings are left to be set by hand.
--from-diff=<caption|address|dump> compares this installation with a saved state: a caption
(Sicherungspunkt) by id, another installation by its address (https://..., read once through its REST
route /api/migration/v1/snapshot with an API key of the scope migration:read, --token; never written
to), or a database dump (a SQLite file, or an SQL dump of this installation's engine, loaded into a
throwaway database). --compare names what (classes, roles, sections, states, languages, content,
settings; content with --subtree=<node>). What was added or changed becomes a step that makes it as it
is now (mode ensure), what was removed a delete step; run on the saved state, the migration makes it
like this installation. What no step type repeats is listed to be done by hand.

./console exp:migration:generate --skeleton add_news_class
./console exp:migration:generate --skeleton news_folder --type=yml --apply
./console exp:migration:generate --skeleton news_index --type=sql --engine=pgsql --apply
./console exp:migration:generate --skeleton news_settings --type=ini --apply
./console exp:migration:generate --skeleton add_news_class --description="The news class" --apply
./console exp:migration:generate --skeleton news_folder --source=extension:expnews --apply
./console exp:migration:generate --from-recording=20261101-120000-a1b2c3 news_section --apply
./console exp:migration:generate --from-class=news_article --from-role="News editor" news_setup
./console exp:migration:generate --from-content=path:news --depth=1 news_tree --apply
./console exp:migration:generate --from-diff=20261102-101500 news_changes
./console exp:migration:generate --from-diff=var/backup/before.sqlite --compare=classes,roles --apply
./console exp:migration:generate --from-diff=https://staging.example.com --token=staging_api_key --subtree=path:news
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--skeleton` |  | Eine leere Migration mit Kopfzeilen: PHP mit plan(), up() und down(), oder die Art, die --type nennt |
| `--source` | erforderlich | Die Quelle: site oder extension:<Name> (generate: wohin die Datei kommt; rollback: welche Migration, wenn zwei Quellen ihre ID haben) |
| `--description` | erforderlich | Die Beschreibung in den Kopfzeilen der neuen Migration |
| `--author` | erforderlich | Der Autor in den Kopfzeilen der neuen Migration |
| `--type` | erforderlich | Der Typ der neuen Migration: php (der Standard), yml, sql, ini oder graphql (eine YAML-Migration mit einem graphql-Schritt zum Ausfüllen) |
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--from-recording` | erforderlich | Erzeugt eine YAML-Migration aus einer Aufzeichnung von Änderungen (ihre ID) |
| `--engine` | erforderlich | Mit --type=sql: die Datei einer Engine (mysql, pgsql, sqlite, oracle, mongodb) statt der portablen |
| `--from-content` | erforderlich | Erzeugt eine YAML-Migration von Inhalten: die Knoten (ID, remote:<Remote-ID>, path:<URL-Pfad>), durch Kommas getrennt |
| `--depth` | erforderlich | Mit --from-content: die Ebenen unter jedem Knoten, die mit hineinkommen (Standard 0: der Knoten allein) |
| `--max-objects` | erforderlich | Mit --from-content oder mit --from-diff und --subtree: höchstens so viele Objekte (Standard 500) |
| `--from-class` | erforderlich | Erzeugt eine YAML-Migration von Inhaltsklassen (Bezeichner, durch Kommas getrennt) |
| `--from-role` | erforderlich | Erzeugt eine YAML-Migration von Rollen mit ihren Richtlinien (Namen, durch Kommas getrennt) |
| `--from-section` | erforderlich | Erzeugt eine YAML-Migration von Sektionen (Bezeichner, durch Kommas getrennt) |
| `--from-settings` | erforderlich | Erzeugt eine YAML-Migration von Einstellungsdateien aus settings/override (site.ini ..., durch Kommas getrennt) |
| `--siteaccess` | erforderlich | Mit --from-settings: die Dateien dieses Siteaccess statt settings/override |
| `--from-diff` | erforderlich | Erzeugt eine YAML-Migration von dem, was sich seit einem gespeicherten Stand geändert hat: eine Sicherungspunkt-ID, die Adresse einer anderen Installation (https://...) oder ein Datenbank-Dump |
| `--compare` | erforderlich | Mit --from-diff: was verglichen wird (classes, roles, sections, states, languages, content, settings; durch Kommas getrennt, Standard alles außer content) |
| `--subtree` | erforderlich | Mit --from-diff: vergleicht die Inhalte des Teilbaums dieses Knotens (ID, remote:<Remote-ID>, path:<URL-Pfad>) |
| `--token` | erforderlich | Mit --from-diff=<Adresse>: der API-Schlüssel der anderen Installation mit der Berechtigung migration:read, als Name eines Geheimnisses oder env:VARIABLE |
| `--backup-dir` | erforderlich | Wo die ohne git_manager angelegten Sicherungspunkte liegen (Standard var/backup/upgrade) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[name]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationgenerate.php`, `kernel/private/classes/commands/expmigrationgenerate.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
