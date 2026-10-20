---
title: exp:frankenphp
audience: developer, admin
category: examples
summary: Control the FrankenPHP engine of the Exponential web server.
keywords: Konsole, Befehl, exp:frankenphp, frankenphp, console, command, CLI, frankenphp.php, exp:fp
type: generated
generated: exp:help:examples
---
# exp:frankenphp

Control the FrankenPHP engine of the Exponential web server.

Skript `bin/php/frankenphp.php`, Code in `kernel/private/classes/commands/frankenphp.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:frankenphp --help
./console exp:fp --help
php bin/php/frankenphp.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  | Report as JSON, for a caller that is not a person |
| `--engine` | erforderlich | override the frankenphp default for this command only (php, qbix, or several) |
| `--all` |  | start, stop, restart, graceful, kill or status every engine, one after the other |
| `--force` |  | install: download again even if the binary is already there |
| `--check` |  | install: re-hash the installed binary against its SHA-256 |
| `--trust-github-digest` |  | install: accept the digest GitHub publishes when no Sha256 is pinned |
| `--from` | erforderlich | install: take the binary from this file instead of downloading it |
| `--keep-global` | erforderlich |  |
| `--https` |  |  |
| `--no-https` |  |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[command]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/frankenphp.php`, `kernel/private/classes/commands/frankenphp.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
