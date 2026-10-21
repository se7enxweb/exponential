---
title: exp:warm
audience: developer, admin
category: examples
summary: Fordert jede veröffentlichte Seite an, damit kein Besucher für das Rendern bezahlt (--limit, --concurrency).
keywords: Konsole, Befehl, exp:warm, warm, console, command, CLI, warm.php
type: generated
generated: exp:help:examples
---
# exp:warm

Fordert jede veröffentlichte Seite an, damit kein Besucher für das Rendern bezahlt (--limit, --concurrency).

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
| `--json` |  | Das Ergebnis als JSON ausgeben. |
| `--verbose` |  | Jede Seite nennen, die gerendert werden musste. |
| `--limit` | erforderlich | Höchstens so viele Seiten vorwärmen. |
| `--host` | erforderlich | Zu sendender Host-Header (Standard: SiteURL aus site.ini, mit dem HTTPS-Port von Velocity). |
| `--base` | erforderlich | Wohin die Anfragen gehen (Standard: http://127.0.0.1:8088). |
| `--concurrency` | erforderlich | Wie viele gleichzeitig (Standard: 8). |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/warm.php`, `kernel/private/classes/commands/warm.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
