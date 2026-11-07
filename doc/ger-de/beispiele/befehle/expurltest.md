---
title: exp:expurltest
audience: developer, admin
category: examples
summary: "Eine URL-Zuordnung an einer laufenden Website prüfen: jede alte Adresse aufrufen, den Weiterleitungen folgen und vergleichen, wo sie landet, mit new_url."
keywords: Konsole, Befehl, exp:expurltest, expurltest, console, command, CLI, expurltest.php, exp:url:test
type: generated
generated: exp:help:examples
---
# exp:expurltest

Eine URL-Zuordnung an einer laufenden Website prüfen: jede alte Adresse aufrufen, den Weiterleitungen folgen und vergleichen, wo sie landet, mit new_url.

Skript `bin/php/expurltest.php`, Code in `kernel/private/classes/commands/expurltest.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expurltest --help
./console exp:url:test --help
php bin/php/expurltest.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Test a URL map against a running site
Requests every old address of the map on --base, several at a time, follows the redirects and
compares where it lands with the row's new_url: landed, wrong-status (permanent where a temporary
redirect was expected, or the other way), wrong-target, not-redirected, error; rows without a
new_url are skipped. A wildcard row is tested with a sample for its *. Ends with exit 1 when a row did
not land. Node targets (node:<id>) are compared with the node's address on the siteaccess of -s.

./console exp:url:test var/tmp/map.csv --base=https://www.example.com
./console exp:url:test var/tmp/map.xlsx --base=https://staging.example.com --insecure --report=var/tmp/test.csv
./console exp:url:test var/tmp/map.csv --base=https://www.example.com --concurrency=16 --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--base` | erforderlich | test: die zu prüfende Website, https://www.example.com (erforderlich) |
| `--format` | erforderlich | import: das Format der Datei (csv, json, xlsx, ods, apache, nginx; Standard nach ihrem Namen oder Inhalt); export: das zu schreibende Format (Standard nach --output, sonst csv); batches, undo, from-sitemap, from-404, suggest, test: text (Standard) oder json (die Maschinenausgabe exp-url/1) |
| `--report` | erforderlich | import: den Plan zusätzlich Zeile für Zeile in diese Datei schreiben (.json oder .csv); suggest, test: jede Zeile mit ihren Vorschlägen oder ihrem Ergebnis zusätzlich in diese Datei schreiben (.json oder .csv) |
| `--limit` | erforderlich | batches: wie viele der letzten Importe aufgelistet werden (Standard 20); from-sitemap, from-404, test: die meisten Zeilen (Standard alle) |
| `--concurrency` | erforderlich | test: die gleichzeitigen Anfragen (Standard 8) |
| `--timeout` | erforderlich | from-sitemap, test: die Sekunden, die eine Anfrage dauern darf (Standard 15) |
| `--max-redirects` | erforderlich | test: die Weiterleitungen, denen gefolgt wird, bevor eine Zeile als Fehler zählt (Standard 5) |
| `--insecure` |  | from-sitemap, test: TLS-Zertifikate nicht prüfen (für Testhosts mit einem selbst signierten) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[file]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expurltest.php`, `kernel/private/classes/commands/expurltest.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
