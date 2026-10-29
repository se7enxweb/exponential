---
title: exp:exphelpcontent
audience: developer, admin
category: examples
summary: "Hilfe als Inhalt: die Hilfeklassen, Abschnitte, Zustände, den Baum und die Rollen anlegen (idempotent), die Themen gegen den Stilleitfaden prüfen, das Glossar und Themen anlegen, berichten und deinstallieren (die ändernden Aktionen sind ein Probelauf, außer mit --apply)."
keywords: Konsole, Befehl, exp:exphelpcontent, exphelpcontent, console, command, CLI, exphelpcontent.php, exp:help:content
type: generated
generated: exp:help:examples
---
# exp:exphelpcontent

Hilfe als Inhalt: die Hilfeklassen, Abschnitte, Zustände, den Baum und die Rollen anlegen (idempotent), die Themen gegen den Stilleitfaden prüfen, das Glossar und Themen anlegen, berichten und deinstallieren (die ändernden Aktionen sind ein Probelauf, außer mit --apply).

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

./console exp:help:content install -s <admin siteaccess> [--apply]   tables, classes, tree and roles (all four steps)
./console exp:help:content tables -s ...     the tables exphelp_help_id (released help_ids) and exphelp_feedback
                                              ("Was this helpful?"), when an older installation lacks them
./console exp:help:content classes -s ...    the eight help_* classes in the class group Help (create_classes.php)
./console exp:help:content tree -s ...       sections, the state group help_status, the folder tree (create_tree.php)
./console exp:help:content roles -s ...      the roles Help editor, Help release and the Anonymous policy (create_roles.php)
./console exp:help:content check -s ... [--fix --apply]   the validator (check_help.php); --fix sets the sections from the audience
./console exp:help:content seed-glossary -s ... [--file=] [--update]   the glossary (seed_glossary.php)
./console exp:help:content seed-topics -s ... [--file=] [--update] [--release]   topics and manuals (seed_topics.php)
./console exp:help:content build-manuals -s ... [--manual=<key>] [--language=ger-DE,...] [--release] [--queue] [--apply]
                                              the manuals of the Markdown help (exphelp.ini [ManualSettings]):
                                              made, updated and removed so they match the pages (help-gen-...)
./console exp:help:content report -s ...     what is installed, the roles' assignments, the content by state
./console exp:help:content export -s ... --manual=help-manual-editors --format=epub|pdf [--output=file] [--language=] [--check]
                                              an e-book of the Released topics of a manual (export_manual.php);
                                              --check runs epubcheck and Ace by DAISY where the site has them
./console exp:help:content dita -s ... [--manual=<remote_id>] [--output=dir] [--dry-run] [--commit]
                                              the DITA 1.3 export (bookmaps, DITAVAL, topics, glossary) into a
                                              directory, checked after writing (also: export --format=dita);
                                              --commit records it in git (the cronjob part exphelp does it regularly)
./console exp:help:content feedback -s ... [--prune --apply] [--json]   the answers to "Was this helpful?" per topic
./console exp:help:content ebooks -s ... [--manual=<remote_id>] [--language=ger-DE] [--apply]   build the e-books of the
                                              Released manuals and store them as file objects under E-books
./console exp:help:content review -s ... [--json]   the topics whose review is overdue
./console exp:help:content cache -s ... [--json]   fill the cache of the help rows and search words of the
                                              siteaccess (after a deploy or ezcache --clear-id=exphelpcontent)
./console exp:help:content package -s ... [--output=dir] [--version=6.0.15]   the eight classes as the content
                                              class package exp_help_classes (a directory and an .ezpkg)
./console exp:help:content verify-package -s ... --file=<.ezpkg or directory>   compare a package with the
                                              classes of this release (no database)
./console exp:help:content uninstall -s ... [--with-content] [--apply]   take the roles away (and the tree, classes,
                                              state group and sections with --with-content)

-s (--siteaccess) is required: one tree may serve several installations with different databases. Every
step is idempotent: a second run prints "No change.". The actions that change the installation (install,
tables, classes, tree, roles, check --fix, seed-glossary, seed-topics, build-manuals, ebooks, feedback --prune,
uninstall) only show what would change unless --apply is given; --dry-run means the same as no --apply, both
together are a usage error (exit 2). export, dita, package and cache write their files as before (--dry-run
still shows the plan of export and dita); report, review and verify-package only read.
Exit code: 0 fine, 1 check found an ERROR (or a step could not be done), 2 error.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | install, tables, classes, tree, roles, check --fix, seed-*, build-manuals, ebooks, feedback --prune, uninstall: die Änderungen vornehmen (Standard ist ein Probelauf) |
| `--dry-run` |  | Zeigen, was sich ändern würde, nichts ändern (der Standard der ändernden Aktionen; export und dita: ihr Plan) |
| `--check` |  | export: das EPUB mit epubcheck und Ace by DAISY prüfen ([ContentSettings] EpubCheckCommand, AceCommand); Exit-Code 1, wenn eines scheitert |
| `--prune` |  | feedback: die Antworten entfernen, die älter sind als [ContentSettings] FeedbackKeepDays |
| `--commit` |  | dita: die geschriebenen Dateien committen, wenn das Verzeichnis ein Git-Repository ist (die Geschichte des Auslieferungswegs 4) |
| `--fix` |  | check: den Abschnitt jedes Themas, jeder Abbildung, jedes Kapitels und jedes Handbuchs aus der Zielgruppe setzen und die help_ids der freigegebenen Themen speichern |
| `--file` | erforderlich | seed-glossary, seed-topics: die Datendatei (Standard share/help/seed-glossary.json, share/help/seed-topics.json) |
| `--update` |  | seed-*: auch vorhandene Objekte aktualisieren, die von der Datei abweichen |
| `--release` |  | seed-topics: neue Themen, Kapitel und Handbücher auf Released setzen; build-manuals: jedes erzeugte auf Released setzen |
| `--with-content` |  | uninstall: auch den Hilfebaum (in den Papierkorb), die Klassen, die Zustandsgruppe und die Sektionen |
| `--login` | erforderlich | Der Benutzer, der die Klassen und Objekte anlegt (Standard admin) |
| `--json` |  | Ein JSON-Dokument statt des Textes ausgeben |
| `--manual` | erforderlich | export, dita, ebooks: die remote_id des Handbuchs (help-manual-...); dita und ebooks ohne sie: jedes Handbuch; build-manuals: der Schlüssel eines Handbuchs aus [ManualSettings] Manuals[] |
| `--format` | erforderlich | export: epub, pdf oder dita |
| `--output` | erforderlich | export: die zu schreibende Datei (Standard <cache>/exphelp/ebooks/<remote_id>.<format>); dita: das Verzeichnis (Standard <cache>/exphelp/dita/all oder /<remote_id>); package: das Verzeichnis (Standard <cache>/exphelp/packages) |
| `--version` | erforderlich | package: das Release, MAJOR.MINOR.PATCH (Standard: die Version dieser Installation) |
| `--language` | erforderlich | build-manuals: die zu schreibenden Übersetzungen (Standard [ManualSettings] Languages[]; "none" für keine); ebooks, export: die Sprache der Ausgabe (Standard: die Standardsprache) |
| `--queue` |  | build-manuals: den Aufbau als Inhaltsjob im Hintergrund einreihen (helpmanuals) und sofort zurückkehren; solange einer läuft, wird nichts eingereiht |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exphelpcontent.php`, `kernel/private/classes/commands/exphelpcontent.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
