---
title: exp:exphelp
audience: developer, admin
category: examples
summary: "The help system on the command line: build and describe the index of doc/, search it, show a page's metadata, check its links and images."
keywords: Konsole, Befehl, exp:exphelp, exphelp, console, command, CLI, exphelp.php, exp:help
type: generated
generated: exp:help:examples
---
# exp:exphelp

The help system on the command line: build and describe the index of doc/, search it, show a page's metadata, check its links and images.

Skript `bin/php/exphelp.php`, Code in `kernel/private/classes/commands/exphelp.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exphelp --help
./console exp:help --help
php bin/php/exphelp.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The help system: the index of the documentation, its search and its link check

./console exp:help index                     build the index (if it is not current) and describe it
./console exp:help index --rebuild           build it again, whatever is cached
./console exp:help index --list              one line per page: id, type, category, audiences, title
./console exp:help search "clear cache"      search the pages (--audience, --category, --type, --limit)
./console exp:help show guides/caches.md     the metadata and headings of one page
./console exp:help check                     check the links and images of every page (--below=guides/)
./console exp:help pdf help/searching.md      the page as a PDF file (--out=file.pdf, --base-url=https://host)
./console exp:help maps --language=ger-de    check doc/ger-de/.paths.json and .dirnames.json; --fix writes the
                                             union of both sides of a git conflict (and of --paths=, --dirnames=)

Only git-tracked files below doc/ (and the doc/ of the extensions in exphelp.ini ExtensionDocDirs[]) are
pages; nothing is changed except the cache in var/<site>/cache/exphelp. --json prints one JSON document.
index builds the index of every language of exphelp.ini [DocLanguages] (doc/eng-us/, doc/ger-de/ ...);
--language=ger-de limits it to one, and makes search, show, check and pdf read that language.
Exit code: 0 fine, 1 check found errors (with --strict also warnings) or nothing was found, 2 error.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--rebuild` |  | index: build the index again even when the cached one is current |
| `--list` |  | index: list every page |
| `--json` |  | Print one JSON document instead of the text |
| `--audience` | erforderlich | search, index --list: only pages for this audience (editor, admin, developer, user) |
| `--category` | erforderlich | search, index --list: only pages of this category |
| `--type` | erforderlich | search, index --list: only pages of this type (guide, feature, bc, changelog ...) |
| `--limit` | erforderlich | search: the number of results (default 10) |
| `--below` | erforderlich | check, index --list: only the pages whose id starts with this (e.g. guides/) |
| `--all` |  | check: also list the notices (links to files that are not help pages) |
| `--strict` |  | check: exit 1 on warnings too |
| `--out` | erforderlich | pdf: the file to write (default: the page title as file name, in the current directory) |
| `--base-url` | erforderlich | pdf: scheme and host for the links and the page address (https://www.example.com) |
| `--language` | erforderlich | the documentation language: a directory of exphelp.ini [DocLanguages] Directories[] (eng-us, ger-de; the old en/us and de are accepted) |
| `--fix` |  | maps: write .paths.json and .dirnames.json as the union of their entries, sorted |
| `--paths` | erforderlich | maps --fix: also merge the entries of this JSON file into .paths.json |
| `--dirnames` | erforderlich | maps --fix: also merge the entries of this JSON file into .dirnames.json |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exphelp.php`, `kernel/private/classes/commands/exphelp.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
