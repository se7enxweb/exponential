---
title: exp:webserver
audience: developer, admin
category: examples
summary: Steuert den Exponential-Webserver (Engines php, frankenphp oder qbix).
keywords: Konsole, Befehl, exp:webserver, webserver, console, command, CLI, webserver.php
type: generated
generated: exp:help:examples
---
# exp:webserver

Steuert den Exponential-Webserver (Engines php, frankenphp oder qbix).

Skript `bin/php/webserver.php`, Code in `kernel/private/classes/commands/webserver.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:webserver --help
php bin/php/webserver.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  | Als JSON berichten, für einen Aufrufer, der kein Mensch ist |
| `--engine` | erforderlich | php, frankenphp oder qbix nur für diesen Befehl (oder mehrere, durch Kommas getrennt, oder --all), statt des konfigurierten Standards |
| `--port` | erforderlich | configure/reconfigure: der Port, der für die Engine gespeichert wird |
| `--docroot` | erforderlich | configure/reconfigure: das Dokumentenverzeichnis, das für die Engine gespeichert wird |
| `--https-port` | erforderlich | configure/reconfigure: der HTTPS-Port, der gespeichert wird (frankenphp oder qbix; php hat kein TLS) |
| `--reconfigure` |  | start: Engine, Port, Dokumentenverzeichnis und HTTPS-Port vor dem Start erneut ermitteln und speichern |
| `--dry-run` |  | configure/reconfigure: zeigen, was geschrieben würde, ohne es zu schreiben |
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

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/webserver.php`, `kernel/private/classes/commands/webserver.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
