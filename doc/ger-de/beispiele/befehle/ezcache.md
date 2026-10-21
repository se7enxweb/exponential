---
title: exp:ezcache
audience: developer, admin
category: examples
summary: Leert die Caches von Exponential nach Tag oder ID (--clear-tag, --clear-id, --clear-all, --list-tags).
keywords: Konsole, Befehl, exp:ezcache, ezcache, console, command, CLI, ezcache.php
type: generated
generated: exp:help:examples
---
# exp:ezcache

Leert die Caches von Exponential nach Tag oder ID (--clear-tag, --clear-id, --clear-all, --list-tags).

Skript `bin/php/ezcache.php`, Code in `kernel/private/classes/commands/ezcache.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezcache --help
php bin/php/ezcache.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Exponential Cache Handler
Allows for easy clearing of Cache files

./bin/php/ezcache.php --clear-tag=content
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--clear-tag` | erforderlich | Leert alle Caches zu einem Tag; mehrere Tags durch Kommas trennen |
| `--clear-id` | erforderlich | Leert alle Caches zu einer ID; mehrere IDs durch Kommas trennen |
| `--clear-all` |  | Leert alle Caches |
| `--iteration-sleep` | erforderlich | Sekunden Pause zwischen den Iterationen beim Löschen, auch als Dezimalzahl. |
| `--iteration-max` | erforderlich | Anzahl der Einträge, die beim Löschen in jeder Iteration entfernt werden. |
| `--expiry` | erforderlich | Datum oder relative Zeit, ab der Cache-Einträge als abgelaufen gelten, z. B. 'now', '-2 days' oder 'last monday'. Nur zusammen mit --purge verwendbar. |
| `--list-tags` |  | Listet alle verfügbaren Tags auf |
| `--list-ids` |  | Listet alle verfügbaren IDs auf |
| `--purge` |  | Erzwingt das Löschen der Cache-Einträge, sodass die angegebenen Einträge physisch entfernt werden (nützlich, um Speicherplatz zu sparen). Zusammen mit den Optionen clear-* verwendet. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezcache.php`, `kernel/private/classes/commands/ezcache.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
