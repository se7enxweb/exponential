---
title: exp:expurlsuggest
audience: developer, admin
category: examples
summary: Neue Ziele für die Zeilen einer URL-Zuordnung vorschlagen, die keines haben, danach, wie ähnlich ihre alte Adresse den Adressen und Namen der Seiten dieser Website ist.
keywords: Konsole, Befehl, exp:expurlsuggest, expurlsuggest, console, command, CLI, expurlsuggest.php, exp:url:suggest
type: generated
generated: exp:help:examples
---
# exp:expurlsuggest

Neue Ziele für die Zeilen einer URL-Zuordnung vorschlagen, die keines haben, danach, wie ähnlich ihre alte Adresse den Adressen und Namen der Seiten dieser Website ist.

Skript `bin/php/expurlsuggest.php`, Code in `kernel/private/classes/commands/expurlsuggest.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expurlsuggest --help
./console exp:url:suggest --help
php bin/php/expurlsuggest.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Suggest new targets for a URL map
For every row without a new_url, compares the old address (its last element most, without endings
such as .html, transliterated) with the URL aliases and names of the visible pages of the siteaccess
of -s and scores the best ones from 0 to 100. With --fill-above the best one is written into new_url
when it scores that much; the others are listed in the row's note. The map is written again, with
every row; nothing is imported.

./console exp:url:suggest var/tmp/old-site.csv --output=var/tmp/old-site-suggested.csv
./console exp:url:suggest var/tmp/old-site.csv -s site --fill-above=85 --output=var/tmp/map.xlsx
./console exp:url:suggest var/tmp/old-site.csv --top=5 --report=var/tmp/suggestions.json --output=var/tmp/map.csv
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--output` | erforderlich | Die zu schreibende Datei (Standard die Standardausgabe; nötig für xlsx und ods) |
| `--output-format` | erforderlich | import, export: was der Befehl ausgibt, text (Standard) oder json (die Maschinenausgabe exp-url/1); --format ist dort das Format der Datei; from-sitemap, from-404, suggest: das Format der geschriebenen Datei (csv, json, xlsx, ods, apache, nginx; Standard nach --output, sonst csv) |
| `--format` | erforderlich | import: das Format der Datei (csv, json, xlsx, ods, apache, nginx; Standard nach ihrem Namen oder Inhalt); export: das zu schreibende Format (Standard nach --output, sonst csv); batches, undo, from-sitemap, from-404, suggest, test: text (Standard) oder json (die Maschinenausgabe exp-url/1) |
| `--top` | erforderlich | suggest: die Vorschläge je Zeile (Standard 3) |
| `--fill-above` | erforderlich | suggest: den besten Vorschlag in new_url schreiben, wenn er so hoch oder höher bewertet ist (0 bis 100; nie bei Gleichstand; Standard: nie) |
| `--min-score` | erforderlich | suggest: die niedrigste vorgeschlagene Bewertung (0 bis 100, Standard 30) |
| `--report` | erforderlich | import: den Plan zusätzlich Zeile für Zeile in diese Datei schreiben (.json oder .csv); suggest, test: jede Zeile mit ihren Vorschlägen oder ihrem Ergebnis zusätzlich in diese Datei schreiben (.json oder .csv) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[file]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expurlsuggest.php`, `kernel/private/classes/commands/expurlsuggest.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
