---
title: exp:expurlexport
audience: developer, admin
category: examples
summary: Den URL-Übersetzer, die Platzhalter und die Weiterleitungen als URL-Zuordnung exportieren (CSV, JSON, .xlsx, .ods, Apache- oder nginx-Regeln).
keywords: Konsole, Befehl, exp:expurlexport, expurlexport, console, command, CLI, expurlexport.php, exp:url:export
type: generated
generated: exp:help:examples
---
# exp:expurlexport

Den URL-Übersetzer, die Platzhalter und die Weiterleitungen als URL-Zuordnung exportieren (CSV, JSON, .xlsx, .ods, Apache- oder nginx-Regeln).

Skript `bin/php/expurlexport.php`, Code in `kernel/private/classes/commands/expurlexport.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expurlexport --help
./console exp:url:export --help
php bin/php/expurlexport.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Export the URL translator, the wildcards and the redirects as a URL map
Writes the global URL aliases (with --include-node-aliases also the custom aliases of nodes), the URL
wildcards and the redirects of exp_url_redirect in any format exp:url:import reads (and .ods); a
redirect's -s filter is the siteaccess it belongs to. Importing the file again gives the same entries.

./console exp:url:export --output=var/tmp/url-map.xlsx
./console exp:url:export --kind=wildcard,redirect --format=nginx
./console exp:url:export --kind=redirect -s site --language=ger-DE --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | import: das Format der Datei (csv, json, xlsx, ods, apache, nginx; Standard nach ihrem Namen oder Inhalt); export: das zu schreibende Format (Standard nach --output, sonst csv); batches, undo: text (Standard) oder json (die Maschinenausgabe exp-url/1) |
| `--kind` | erforderlich | Nur diese Arten: alias (der URL-Übersetzer), wildcard, redirect (durch Kommas getrennt oder wiederholt; Standard alle) |
| `--language` | erforderlich | import: die Sprache von Zeilen ohne eigene (Standard die des Siteaccess); export: nur diese Sprache |
| `--output` | erforderlich | Die zu schreibende Datei (Standard die Standardausgabe; nötig für xlsx und ods) |
| `--include-node-aliases` |  | Auch die eigenen URL-Aliase von Knoten (Ziel node:<id>), nicht nur die des URL-Übersetzers |
| `--output-format` | erforderlich | import, export: was der Befehl ausgibt, text (Standard) oder json (die Maschinenausgabe exp-url/1); --format ist dort das Format der Datei |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expurlexport.php`, `kernel/private/classes/commands/expurlexport.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
