---
title: exp:exphelpexamples
audience: developer, admin
category: examples
summary: "Generate the reference examples of the help below doc/eng-us/examples/ from the code: template operators and functions, fetch functions, module views, policies, INI settings, datatypes, workflow events, cronjobs, console commands and extension points."
keywords: Konsole, Befehl, exp:exphelpexamples, exphelpexamples, console, command, CLI, exphelpexamples.php, exp:help:examples
type: generated
generated: exp:help:examples
---
# exp:exphelpexamples

Generate the reference examples of the help below doc/eng-us/examples/ from the code: template operators and functions, fetch functions, module views, policies, INI settings, datatypes, workflow events, cronjobs, console commands and extension points.

Skript `bin/php/exphelpexamples.php`, Code in `kernel/private/classes/commands/exphelpexamples.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exphelpexamples --help
./console exp:help:examples --help
php bin/php/exphelpexamples.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The generated reference examples of the help (doc/eng-us/examples/)

./console exp:help:examples                      write the pages of every area (only changed files are written)
./console exp:help:examples --area=datatypes     only this area (repeat with a comma: --area=datatypes,cronjobs)
./console exp:help:examples --check              compare only: exit 1 when a page is missing, different or stale
./console exp:help:examples --list               the areas and how many pages each has
./console exp:help:examples --language=ger-de    the same pages in a translation (doc/ger-de/beispiele/), with
                                                 their entries in doc/ger-de/.paths.json
./console exp:help:examples --language=ger-de --worklist=var/tmp/todo.json
                                                 also write the source texts that have no translation yet

A translation reads its text from doc/<language>/.examples/: strings.json (the fixed text of the pages) and
descriptions/*.json (the comments and descriptions quoted from the sources, by item). A text without a
translation is written in English and counted as untranslated.

The pages are read from the git-tracked sources of this installation (the template autoload files,
function_definition.php, module.php, operation_definition.php, the shipped INI files, the datatype and
workflow event classes, cronjob.ini and the command classes). The same sources always give the same pages.
Only pages marked "generated: exp:help:examples" are replaced or removed; nothing else below doc/ is
touched. Run "./console exp:help index" afterwards to rebuild the help index.
Exit code: 0 fine, 1 --check found differences, 2 error.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--area` | erforderlich | Only these areas, comma separated (template-operators, template-functions, fetch-functions, module-views, policies, ini-settings, datatypes, workflow-events, cronjobs, commands, extension-points) |
| `--check` |  | Compare the generated pages with the files and change nothing; exit 1 on a difference |
| `--list` |  | List the areas with their page counts |
| `--out` | erforderlich | The output directory relative to the installation root (default doc/eng-us/examples, or the translation's directory) |
| `--language` | erforderlich | Write the pages in a translation: a language directory below doc/ (ger-de); default the English original |
| `--worklist` | erforderlich | With --language: write the source texts without a translation to this file, as a description catalogue to fill in |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exphelpexamples.php`, `kernel/private/classes/commands/exphelpexamples.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
