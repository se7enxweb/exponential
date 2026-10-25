---
title: exp:exphelpcontent
audience: developer, admin
category: examples
summary: "Hilfe als Inhalt gepflegt: die Hilfeklassen, Sektionen, Zustände, den Baum und die Rollen anlegen (idempotent), die Themen gegen den Styleguide prüfen, das Glossar und die Themen befüllen, berichten und deinstallieren."
keywords: Konsole, Befehl, exp:exphelpcontent, exphelpcontent, console, command, CLI, exphelpcontent.php, exp:help:content
type: generated
generated: exp:help:examples
---
# exp:exphelpcontent

Hilfe als Inhalt gepflegt: die Hilfeklassen, Sektionen, Zustände, den Baum und die Rollen anlegen (idempotent), die Themen gegen den Styleguide prüfen, das Glossar und die Themen befüllen, berichten und deinstallieren.

Skript `bin/php/exphelpcontent.php`, Code in `kernel/private/classes/commands/exphelpcontent.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exphelpcontent --help
./console exp:help:content --help
php bin/php/exphelpcontent.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Help maintained as content (topics, manuals, glossary in the content tree)

./console exp:help:content install -s <admin siteaccess> [--dry-run]   classes, tree and roles (all three steps)
./console exp:help:content classes -s ...    the eight help_* classes in the class group Help (create_classes.php)
./console exp:help:content tree -s ...       sections, the state group help_status, the folder tree (create_tree.php)
./console exp:help:content roles -s ...      the roles Help editor, Help release and the Anonymous policy (create_roles.php)
./console exp:help:content check -s ... [--fix]   the validator (check_help.php); --fix sets the sections from the audience
./console exp:help:content seed-glossary -s ... [--file=] [--update]   the glossary (seed_glossary.php)
./console exp:help:content seed-topics -s ... [--file=] [--update] [--release]   topics and manuals (seed_topics.php)
./console exp:help:content build-manuals -s ... [--manual=<key>] [--language=ger-DE,...] [--release] [--dry-run] [--queue]
                                              the manuals of the Markdown help (exphelp.ini [ManualSettings]):
                                              made, updated and removed so they match the pages (help-gen-...)
./console exp:help:content report -s ...     what is installed, the roles' assignments, the content by state
./console exp:help:content export -s ... --manual=help-manual-editors --format=epub|pdf [--output=file] [--language=]
                                              an e-book of the Released topics of a manual (export_manual.php)
./console exp:help:content dita -s ... [--manual=<remote_id>] [--output=dir] [--dry-run]
                                              the DITA 1.3 export (bookmaps, DITAVAL, topics, glossary) into a
                                              directory, checked after writing (also: export --format=dita)
./console exp:help:content ebooks -s ... [--manual=<remote_id>] [--language=ger-DE] [--dry-run]   build the e-books of the
                                              Released manuals and store them as file objects under E-books
./console exp:help:content review -s ... [--json]   the topics whose review is overdue
./console exp:help:content cache -s ... [--json]   fill the cache of the help rows and search words of the
                                              siteaccess (after a deploy or ezcache --clear-id=exphelpcontent)
./console exp:help:content package -s ... [--output=dir] [--version=6.0.15]   the eight classes as the content
                                              class package exp_help_classes (a directory and an .ezpkg)
./console exp:help:content verify-package -s ... --file=<.ezpkg or directory>   compare a package with the
                                              classes of this release (no database)
./console exp:help:content uninstall -s ... [--with-content] [--dry-run]   take the roles away (and the tree, classes,
                                              state group and sections with --with-content)

-s (--siteaccess) is required: one tree may serve several installations with different databases. Every
step is idempotent: a second run prints "No change.". --dry-run shows what would change and changes nothing.
Exit code: 0 fine, 1 check found an ERROR (or a step could not be done), 2 error.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | Zeigen, was sich ändern würde, nichts ändern |
| `--fix` |  | check: die Sektion jedes Themas, jeder Abbildung, jedes Kapitels und Handbuchs aus der Zielgruppe setzen |
| `--file` | erforderlich | seed-glossary, seed-topics: die Datendatei (Standard share/help/seed-glossary.json, share/help/seed-topics.json) |
| `--update` |  | seed-*: auch vorhandene Objekte aktualisieren, die von der Datei abweichen |
| `--release` |  | seed-topics: set new topics, chapters and manuals to Released; build-manuals: set every generated one to Released |
| `--with-content` |  | uninstall: auch den Hilfebaum (in den Papierkorb), die Klassen, die Zustandsgruppe und die Sektionen |
| `--login` | erforderlich | Der Benutzer, der die Klassen und Objekte anlegt (Standard admin) |
| `--json` |  | Ein JSON-Dokument statt des Textes ausgeben |
| `--manual` | erforderlich | export, dita, ebooks: the remote_id of the manual (help-manual-...); dita and ebooks without it: every manual; build-manuals: the key of one manual of [ManualSettings] Manuals[] |
| `--format` | erforderlich | export: epub, pdf oder dita |
| `--output` | erforderlich | export: die zu schreibende Datei (Standard <cache>/exphelp/ebooks/<remote_id>.<format>); dita: das Verzeichnis (Standard <cache>/exphelp/dita/all oder /<remote_id>); package: das Verzeichnis (Standard <cache>/exphelp/packages) |
| `--version` | erforderlich | package: das Release, MAJOR.MINOR.PATCH (Standard: die Version dieser Installation) |
| `--language` | erforderlich | build-manuals: the translations to write (default [ManualSettings] Languages[]; "none" for none); ebooks, export: the language of the edition (default: the default language) |
| `--queue` |  | build-manuals: queue the build as a background content job (helpmanuals) and return at once; nothing is queued while one runs |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exphelpcontent.php`, `kernel/private/classes/commands/exphelpcontent.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../../../../doc/ger-de/beispiele/README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../../../doc/ger-de/hilfe/freie-dokumentation.md).
