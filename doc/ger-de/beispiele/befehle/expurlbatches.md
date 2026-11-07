---
title: exp:expurlbatches
audience: developer, admin
category: examples
summary: "Die Importe von URL-Zuordnungen auflisten: wer wann welche Datei importiert hat, die Anzahlen und ob er zurückgenommen wurde."
keywords: Konsole, Befehl, exp:expurlbatches, expurlbatches, console, command, CLI, expurlbatches.php, exp:url:batches
type: generated
generated: exp:help:examples
---
# exp:expurlbatches

Die Importe von URL-Zuordnungen auflisten: wer wann welche Datei importiert hat, die Anzahlen und ob er zurückgenommen wurde.

Skript `bin/php/expurlbatches.php`, Code in `kernel/private/classes/commands/expurlbatches.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expurlbatches --help
./console exp:url:batches --help
php bin/php/expurlbatches.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
List the URL map imports
The batches of exp:url:import (and the administration's import), newest first: the file, who and when,
the counts, and whether it was undone (exp:url:undo).

./console exp:url:batches
./console exp:url:batches --limit=100 --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--limit` | erforderlich | batches: wie viele der letzten Importe aufgelistet werden (Standard 20); from-sitemap, from-404, test: die meisten Zeilen (Standard alle) |
| `--format` | erforderlich | import: das Format der Datei (csv, json, xlsx, ods, apache, nginx; Standard nach ihrem Namen oder Inhalt); export: das zu schreibende Format (Standard nach --output, sonst csv); batches, undo, from-sitemap, from-404, suggest, test: text (Standard) oder json (die Maschinenausgabe exp-url/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expurlbatches.php`, `kernel/private/classes/commands/expurlbatches.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
