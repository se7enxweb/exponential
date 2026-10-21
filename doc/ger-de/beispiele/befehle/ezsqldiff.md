---
title: exp:ezsqldiff
audience: developer, admin
category: examples
summary: Zeigt die Unterschiede zwischen zwei Datenbankschemata; der Exit-Code sagt, ob sie sich unterscheiden.
keywords: Konsole, Befehl, exp:ezsqldiff, ezsqldiff, console, command, CLI, ezsqldiff.php
type: generated
generated: exp:help:examples
---
# exp:ezsqldiff

Zeigt die Unterschiede zwischen zwei Datenbankschemata; der Exit-Code sagt, ob sie sich unterscheiden.

Skript `bin/php/ezsqldiff.php`, Code in `kernel/private/classes/commands/ezsqldiff.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezsqldiff --help
php bin/php/ezsqldiff.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--source-type` | erforderlich |  |
| `--source-host` | erforderlich |  |
| `--source-user` | erforderlich |  |
| `--source-password` | optional |  |
| `--source-socket` | erforderlich |  |
| `--match-type` | erforderlich |  |
| `--match-host` | erforderlich |  |
| `--match-user` | erforderlich |  |
| `--match-password` | optional |  |
| `--match-socket` | erforderlich |  |
| `-t` | erforderlich |  |
| `--type` | erforderlich |  |
| `--host` | erforderlich |  |
| `-u` | erforderlich |  |
| `--user` | erforderlich |  |
| `-p` | erforderlich |  |
| `--password` | optional |  |
| `--socket` | erforderlich |  |
| `--lint-check` |  |  |
| `--reverse` |  |  |
| `--check-only` |  |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[source][match]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezsqldiff.php`, `kernel/private/classes/commands/ezsqldiff.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
