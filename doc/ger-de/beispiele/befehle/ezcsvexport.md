---
title: exp:ezcsvexport
audience: developer, admin
category: examples
summary: Exportiert den Teilbaum unter einem Knoten in eine CSV-Datei pro Inhaltsklasse (--storage-dir).
keywords: Konsole, Befehl, exp:ezcsvexport, ezcsvexport, console, command, CLI, ezcsvexport.php
type: generated
generated: exp:help:examples
---
# exp:ezcsvexport

Exportiert den Teilbaum unter einem Knoten in eine CSV-Datei pro Inhaltsklasse (--storage-dir).

Skript `bin/php/ezcsvexport.php`, Code in `kernel/private/classes/commands/ezcsvexport.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezcsvexport --help
php bin/php/ezcsvexport.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--storage-dir` | erforderlich | Verzeichnis, in das die exportierten Dateien gelegt werden |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[node]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezcsvexport.php`, `kernel/private/classes/commands/ezcsvexport.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
