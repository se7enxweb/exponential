---
title: exp:expurlimport
audience: developer, admin
category: examples
summary: Eine URL-Zuordnung (CSV, JSON, .xlsx, .ods, Apache- oder nginx-Regeln) in den URL-Übersetzer, die Platzhalter und die Weiterleitungen importieren, als ein Stapel, der sich zurücknehmen lässt.
keywords: Konsole, Befehl, exp:expurlimport, expurlimport, console, command, CLI, expurlimport.php, exp:url:import
type: generated
generated: exp:help:examples
---
# exp:expurlimport

Eine URL-Zuordnung (CSV, JSON, .xlsx, .ods, Apache- oder nginx-Regeln) in den URL-Übersetzer, die Platzhalter und die Weiterleitungen importieren, als ein Stapel, der sich zurücknehmen lässt.

Skript `bin/php/expurlimport.php`, Code in `kernel/private/classes/commands/expurlimport.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expurlimport --help
./console exp:url:import --help
php bin/php/expurlimport.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Import a URL map into the URL translator, the wildcards and the redirects
Reads the file (CSV, JSON, .xlsx, .ods, Apache or nginx rules; the format from its name or content,
or --format), plans every row (new, changed, unchanged, duplicate, conflict, broken target, unknown
host, not importable) and imports the new ones, and the changed ones with --on-conflict=overwrite, as
one batch: exp:url:undo <batch> takes it back. Bare paths belong to the siteaccess of -s (default the
default siteaccess); a full address's host picks the siteaccess, or --host maps an old host to one.

./console exp:url:import old-site.csv --dry-run --report=var/tmp/plan.csv
./console exp:url:import old-site.htaccess -s site --on-conflict=overwrite
./console exp:url:import map.xlsx --host=www.old-site.com=site --output-format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | import: das Format der Datei (csv, json, xlsx, ods, apache, nginx; Standard nach ihrem Namen oder Inhalt); export: das zu schreibende Format (Standard nach --output, sonst csv); batches, undo, from-sitemap, from-404, suggest, test: text (Standard) oder json (die Maschinenausgabe exp-url/1) |
| `--language` | erforderlich | import: die Sprache von Zeilen ohne eigene (Standard die des Siteaccess); export: nur diese Sprache |
| `--on-conflict` | erforderlich | Was mit einer alten Adresse geschieht, die schon einen anderen Eintrag hat: skip (überspringen, Standard) oder overwrite (überschreiben) |
| `--dry-run` |  | Nur planen und prüfen: auflisten, was importiert würde, nichts schreiben |
| `--report` | erforderlich | import: den Plan zusätzlich Zeile für Zeile in diese Datei schreiben (.json oder .csv); suggest, test: jede Zeile mit ihren Vorschlägen oder ihrem Ergebnis zusätzlich in diese Datei schreiben (.json oder .csv) |
| `--host` | erforderlich | Ein alter Host und der Siteaccess, für den er steht, alte-website.de=site (durch Kommas getrennt oder wiederholt): für vollständige Adressen eines Hosts, der nicht zu dieser Installation gehört |
| `--login` | erforderlich | Als dieser Benutzer ausführen (mit dem Import und im Audit aufgezeichnet) |
| `--output-format` | erforderlich | import, export: was der Befehl ausgibt, text (Standard) oder json (die Maschinenausgabe exp-url/1); --format ist dort das Format der Datei; from-sitemap, from-404, suggest: das Format der geschriebenen Datei (csv, json, xlsx, ods, apache, nginx; Standard nach --output, sonst csv) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[file]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expurlimport.php`, `kernel/private/classes/commands/expurlimport.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
