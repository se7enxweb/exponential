---
title: exp:expurlfrom404
audience: developer, admin
category: examples
summary: "Eine URL-Zuordnung aus dem Protokoll nicht gefundener Seiten beginnen: die Adressen, die Besucher aufgerufen und nicht gefunden haben, die häufigsten zuerst, als auszufüllende Zeilen."
keywords: Konsole, Befehl, exp:expurlfrom404, expurlfrom404, console, command, CLI, expurlfrom404.php, exp:url:from-404
type: generated
generated: exp:help:examples
---
# exp:expurlfrom404

Eine URL-Zuordnung aus dem Protokoll nicht gefundener Seiten beginnen: die Adressen, die Besucher aufgerufen und nicht gefunden haben, die häufigsten zuerst, als auszufüllende Zeilen.

Skript `bin/php/expurlfrom404.php`, Code in `kernel/private/classes/commands/expurlfrom404.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expurlfrom404 --help
./console exp:url:from-404 --help
php bin/php/expurlfrom404.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Start a URL map from the not-found log
Reads the log of pages not found (var/log/404.log and its rotated files, written while site.ini
[RequestLogSettings] NotFoundLog=enabled, the default), or with --log web server access logs, and
writes every address that answered 404 or 410 once, the most asked first, as a row with an empty
new_url and the hits in its note. Assets, bots and methods other than GET and HEAD are left out;
-s keeps the lines of one siteaccess. The log hides query values, so only the path counts there.

./console exp:url:from-404 --min-hits=3 --since=30d --output=var/tmp/not-found.csv
./console exp:url:from-404 -s site --limit=200 --output=var/tmp/not-found.xlsx
./console exp:url:from-404 --log=/var/log/nginx/access.log.1.gz --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--output` | erforderlich | Die zu schreibende Datei (Standard die Standardausgabe; nötig für xlsx und ods) |
| `--output-format` | erforderlich | import, export: was der Befehl ausgibt, text (Standard) oder json (die Maschinenausgabe exp-url/1); --format ist dort das Format der Datei; from-sitemap, from-404, suggest: das Format der geschriebenen Datei (csv, json, xlsx, ods, apache, nginx; Standard nach --output, sonst csv) |
| `--format` | erforderlich | import: das Format der Datei (csv, json, xlsx, ods, apache, nginx; Standard nach ihrem Namen oder Inhalt); export: das zu schreibende Format (Standard nach --output, sonst csv); batches, undo, from-sitemap, from-404, suggest, test: text (Standard) oder json (die Maschinenausgabe exp-url/1) |
| `--min-hits` | erforderlich | from-404: nur Adressen, die so oft oder öfter aufgerufen wurden (Standard 1) |
| `--since` | erforderlich | from-404: nur Anfragen seit diesem Datum (2026-11-01) oder seit dieser Zeitspanne (30d, 12h, 2w) |
| `--limit` | erforderlich | batches: wie viele der letzten Importe aufgelistet werden (Standard 20); from-sitemap, from-404, test: die meisten Zeilen (Standard alle) |
| `--include-assets` |  | from-404: auch Bilder, Skripte, Stylesheets, Schriften und Dateien unter /var/, /design/ und /extension/ |
| `--include-bots` |  | from-404: auch die Anfragen von Bots und Crawlern |
| `--log` | erforderlich | from-404: diese Protokolle statt des Protokolls nicht gefundener Seiten dieser Installation lesen: 404.log-Dateien oder Zugriffsprotokolle des Webservers im Format common oder combined, auch .gz (durch Kommas getrennt oder wiederholt) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expurlfrom404.php`, `kernel/private/classes/commands/expurlfrom404.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
