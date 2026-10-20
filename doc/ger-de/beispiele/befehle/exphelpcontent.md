---
title: exp:exphelpcontent
audience: developer, admin
category: examples
summary: "Help maintained as content: create the help classes, sections, states, tree and roles (idempotent), check the topics against the style guide, seed the glossary and topics, report and uninstall."
keywords: Konsole, Befehl, exp:exphelpcontent, exphelpcontent, console, command, CLI, exphelpcontent.php, exp:help:content
type: generated
generated: exp:help:examples
---
# exp:exphelpcontent

Help maintained as content: create the help classes, sections, states, tree and roles (idempotent), check the topics against the style guide, seed the glossary and topics, report and uninstall.

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
./console exp:help:content report -s ...     what is installed, the roles' assignments, the content by state
./console exp:help:content export -s ... --manual=help-manual-editors --format=epub|pdf [--output=file]
                                              an e-book of the Released topics of a manual (export_manual.php)
./console exp:help:content dita -s ... [--manual=<remote_id>] [--output=dir] [--dry-run]
                                              the DITA 1.3 export (bookmaps, DITAVAL, topics, glossary) into a
                                              directory, checked after writing (also: export --format=dita)
./console exp:help:content ebooks -s ... [--manual=<remote_id>] [--dry-run]   build the e-books of the
                                              Released manuals and store them as file objects under E-books
./console exp:help:content review -s ... [--json]   the topics whose review is overdue
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
| `--dry-run` |  | Show what would change, change nothing |
| `--fix` |  | check: set the section of each topic, figure, chapter and manual from the audience |
| `--file` | erforderlich | seed-glossary, seed-topics: the data file (default share/help/seed-glossary.json, share/help/seed-topics.json) |
| `--update` |  | seed-*: also update existing objects that differ from the file |
| `--release` |  | seed-topics: set new topics, chapters and manuals to Released |
| `--with-content` |  | uninstall: also the help tree (into the trash), the classes, the state group and the sections |
| `--login` | erforderlich | The user who creates the classes and objects (default admin) |
| `--json` |  | Print one JSON document instead of the text |
| `--manual` | erforderlich | export, dita, ebooks: the remote_id of the manual (help-manual-...); dita and ebooks without it: every manual |
| `--format` | erforderlich | export: epub, pdf or dita |
| `--output` | erforderlich | export: the file to write (default <cache>/exphelp/ebooks/<remote_id>.<format>); dita: the directory (default <cache>/exphelp/dita/all or /<remote_id>); package: the directory (default <cache>/exphelp/packages) |
| `--version` | erforderlich | package: the release, MAJOR.MINOR.PATCH (default: the version of this installation) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exphelpcontent.php`, `kernel/private/classes/commands/exphelpcontent.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
