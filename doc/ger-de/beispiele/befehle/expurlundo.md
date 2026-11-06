---
title: exp:expurlundo
audience: developer, admin
category: examples
summary: "Einen Import einer URL-Zuordnung zurücknehmen: jeden Alias, Platzhalter und jede Weiterleitung, die er angelegt oder geändert hat, wieder so, wie sie waren."
keywords: Konsole, Befehl, exp:expurlundo, expurlundo, console, command, CLI, expurlundo.php, exp:url:undo
type: generated
generated: exp:help:examples
---
# exp:expurlundo

Einen Import einer URL-Zuordnung zurücknehmen: jeden Alias, Platzhalter und jede Weiterleitung, die er angelegt oder geändert hat, wieder so, wie sie waren.

Skript `bin/php/expurlundo.php`, Code in `kernel/private/classes/commands/expurlundo.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expurlundo --help
./console exp:url:undo --help
php bin/php/expurlundo.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Undo a URL map import
Puts every URL alias, wildcard and redirect the import created or changed back as it was before, the
last first, in one transaction. An entry changed after the import is kept (the batch is then
partly-undone), unless --force is given. exp:url:batches lists the imports.

./console exp:url:undo 12
./console exp:url:undo 12 --force --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--force` |  | Auch die Einträge zurücknehmen, die sich nach dem Import geändert haben (Standard: sie bleiben) |
| `--format` | erforderlich | import: das Format der Datei (csv, json, xlsx, ods, apache, nginx; Standard nach ihrem Namen oder Inhalt); export: das zu schreibende Format (Standard nach --output, sonst csv); batches, undo: text (Standard) oder json (die Maschinenausgabe exp-url/1) |
| `--login` | erforderlich | Als dieser Benutzer ausführen (mit dem Import und im Audit aufgezeichnet) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[batch]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expurlundo.php`, `kernel/private/classes/commands/expurlundo.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
