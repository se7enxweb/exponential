---
title: exp:notificationevents
audience: developer, admin
category: examples
summary: List the notification events, or remove the old ones.
keywords: Konsole, Befehl, exp:notificationevents, notificationevents, console, command, CLI, notificationevents.php, exp:notification:events, exp:notify:events
type: generated
generated: exp:help:examples
---
# exp:notificationevents

List the notification events, or remove the old ones.

Skript `bin/php/notificationevents.php`, Code in `kernel/private/classes/commands/notificationevents.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:notificationevents --help
./console exp:notification:events --help
./console exp:notify:events --help
php bin/php/notificationevents.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Notification events
  list                      the newest events: id, status, type, age, messages waiting
  cleanup                   remove the handled events nothing waits for, and with --older-than the
                            events (and their messages) older than that

An event has no date of its own; its age is read from what it is about (the published version, the
collaboration item, the time of a time event). Events whose age cannot be told, because the content
is gone, are listed as unknown and removed only with --include-unknown.

./console exp:notification:events list --status=pending --limit=20
./console exp:notification:events cleanup --older-than=30d --dry-run
./console exp:notification:events cleanup --older-than=30d
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--status` | erforderlich | list, cleanup --older-than: pending or handled (default both) |
| `--limit` | erforderlich | list: how many (default 50) |
| `--older-than` | erforderlich | cleanup: 90s, 15m, 12h, 30d, 2w or a date (2026-09-01) |
| `--include-unknown` |  | cleanup: also remove events whose age cannot be told |
| `--dry-run` |  | cleanup: count, remove nothing |
| `--json` |  | list: JSON |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/notificationevents.php`, `kernel/private/classes/commands/notificationevents.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
