---
title: exp:trashpurge
audience: developer, admin
category: examples
summary: Permanently delete all objects in the trash.
keywords: Konsole, Befehl, exp:trashpurge, trashpurge, console, command, CLI, trashpurge.php
type: generated
generated: exp:help:examples
---
# exp:trashpurge

Permanently delete all objects in the trash.

Skript `bin/php/trashpurge.php`, Code in `kernel/private/classes/commands/trashpurge.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:trashpurge --help
php bin/php/trashpurge.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Empty Exponential trash.
Permanently deletes all objects in the trash.

./bin/php/trashpurge.php
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--iteration-sleep` | erforderlich | Sekunden Pause zwischen den Iterationen beim Löschen, auch als Dezimalzahl. Standard ist eine Sekunde. |
| `--iteration-limit` | erforderlich | Anzahl der Einträge, die beim Löschen in jeder Iteration entfernt werden. Standard ist 100. |
| `--memory-monitoring` |  | If set, memory usage will be logged in var/log/trashpurge.log. |
| `--trashed-days` | erforderlich | If set, only objects that has been trashed for at least the specified amount of days will be purged. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/trashpurge.php`, `kernel/private/classes/commands/trashpurge.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
