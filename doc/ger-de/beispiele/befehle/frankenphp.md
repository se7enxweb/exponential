---
title: exp:frankenphp
audience: developer, admin
category: examples
summary: Steuert die FrankenPHP-Engine des Exponential-Webservers.
keywords: Konsole, Befehl, exp:frankenphp, frankenphp, console, command, CLI, frankenphp.php, exp:fp
type: generated
generated: exp:help:examples
---
# exp:frankenphp

Steuert die FrankenPHP-Engine des Exponential-Webservers.

Skript `bin/php/frankenphp.php`, Code in `kernel/private/classes/commands/frankenphp.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:frankenphp --help
./console exp:fp --help
php bin/php/frankenphp.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  | Als JSON berichten, für einen Aufrufer, der kein Mensch ist |
| `--engine` | erforderlich | den frankenphp-Standard nur für diesen Befehl überschreiben (php, qbix oder mehrere) |
| `--all` |  | start, stop, restart, graceful, kill oder status für jede Engine, eine nach der anderen |
| `--force` |  | install: erneut herunterladen, auch wenn die Binärdatei schon da ist |
| `--check` |  | install: den Hash der installierten Binärdatei erneut gegen ihre SHA-256 prüfen |
| `--trust-github-digest` |  | install: den von GitHub veröffentlichten Digest akzeptieren, wenn keine Sha256 festgelegt ist |
| `--from` | erforderlich | install: die Binärdatei aus dieser Datei nehmen, statt sie herunterzuladen |
| `--keep-global` | erforderlich |  |
| `--https` |  |  |
| `--no-https` |  |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[command]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/frankenphp.php`, `kernel/private/classes/commands/frankenphp.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
