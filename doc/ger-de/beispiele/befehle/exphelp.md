---
title: exp:exphelp
audience: developer, admin
category: examples
summary: "Das Hilfesystem auf der Kommandozeile: den Index von doc/ aufbauen und beschreiben, darin suchen, die Metadaten einer Seite zeigen, ihre Links und Bilder prüfen."
keywords: Konsole, Befehl, exp:exphelp, exphelp, console, command, CLI, exphelp.php, exp:help
type: generated
generated: exp:help:examples
---
# exp:exphelp

Das Hilfesystem auf der Kommandozeile: den Index von doc/ aufbauen und beschreiben, darin suchen, die Metadaten einer Seite zeigen, ihre Links und Bilder prüfen.

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
| `--rebuild` |  | index: den Index neu aufbauen, auch wenn der im Cache aktuell ist |
| `--list` |  | index: jede Seite auflisten |
| `--json` |  | Ein JSON-Dokument statt des Textes ausgeben |
| `--audience` | erforderlich | search, index --list: nur Seiten für diese Zielgruppe (editor, admin, developer, user) |
| `--category` | erforderlich | search, index --list: nur Seiten dieser Kategorie |
| `--type` | erforderlich | search, index --list: nur Seiten dieses Typs (guide, feature, bc, changelog ...) |
| `--limit` | erforderlich | search: die Anzahl der Ergebnisse (Standard 10) |
| `--below` | erforderlich | check, index --list: nur die Seiten, deren ID damit beginnt (z. B. guides/) |
| `--all` |  | check: auch die Hinweise auflisten (Links auf Dateien, die keine Hilfeseiten sind) |
| `--strict` |  | check: auch bei Warnungen Exit-Code 1 |
| `--out` | erforderlich | pdf: die zu schreibende Datei (Standard: der Seitentitel als Dateiname, im aktuellen Verzeichnis) |
| `--base-url` | erforderlich | pdf: Schema und Host für die Links und die Adresse der Seite (https://www.example.com) |
| `--language` | erforderlich | die Sprache der Dokumentation: ein Verzeichnis aus exphelp.ini [DocLanguages] Directories[] (eng-us, ger-de; die alten en/us und de werden akzeptiert) |
| `--fix` |  | maps: .paths.json und .dirnames.json als Vereinigung ihrer Einträge schreiben, sortiert |
| `--paths` | erforderlich | maps --fix: außerdem die Einträge dieser JSON-Datei in .paths.json übernehmen |
| `--dirnames` | erforderlich | maps --fix: außerdem die Einträge dieser JSON-Datei in .dirnames.json übernehmen |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exphelp.php`, `kernel/private/classes/commands/exphelp.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
