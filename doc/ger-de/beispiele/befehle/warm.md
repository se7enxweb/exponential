---
title: exp:warm
audience: developer, admin
category: examples
summary: Request every published page so no visitor pays for a render (--limit, --concurrency).
keywords: Konsole, Befehl, exp:warm, warm, console, command, CLI, warm.php
type: generated
generated: exp:help:examples
---
# exp:warm

Request every published page so no visitor pays for a render (--limit, --concurrency).

Skript `bin/php/warm.php`, Code in `kernel/private/classes/commands/warm.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:warm --help
php bin/php/warm.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Exponential cache warmer

Request every published page so no visitor pays for a render.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  | Print the result as JSON. |
| `--verbose` |  | Name every page that had to be rendered. |
| `--limit` | erforderlich | Warm at most this many pages. |
| `--host` | erforderlich | Host header to send (default: SiteURL of site.ini, with Velocity's HTTPS port). |
| `--base` | erforderlich | Where to send them (default: http://127.0.0.1:8088). |
| `--concurrency` | erforderlich | How many at once (default: 8). |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/warm.php`, `kernel/private/classes/commands/warm.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
