---
title: exp:ezcache
audience: developer, admin
category: examples
summary: Clear Exponential caches by tag or id (--clear-tag, --clear-id, --clear-all, --list-tags).
keywords: Konsole, Befehl, exp:ezcache, ezcache, console, command, CLI, ezcache.php
type: generated
generated: exp:help:examples
---
# exp:ezcache

Clear Exponential caches by tag or id (--clear-tag, --clear-id, --clear-all, --list-tags).

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
| `--clear-tag` | erforderlich | Clears all caches related to a given tag, separate multiple tags with a comma |
| `--clear-id` | erforderlich | Clears all caches related to a given id, separate multiple ids with a comma |
| `--clear-all` |  | Clears all caches |
| `--iteration-sleep` | erforderlich | Amount of seconds to sleep between each iteration when performing a purge operation, can be a float. |
| `--iteration-max` | erforderlich | Amount of items to remove in each iteration when performing a purge operation. |
| `--expiry` | erforderlich | Date or relative time which specifies when cache items are to be considered expired, e.g 'now', '-2 days' or 'last monday'. Can only be used together with --purge. |
| `--list-tags` |  | Lists all available tags |
| `--list-ids` |  | Lists all available ids |
| `--purge` |  | Enforces purging of cache items which ensures that specified entries are physically removed (Useful for saving diskspace). Used together with the clear-* options. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezcache.php`, `kernel/private/classes/commands/ezcache.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
