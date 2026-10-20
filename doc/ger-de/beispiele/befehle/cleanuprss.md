---
title: exp:cleanuprss
audience: developer, admin
category: examples
summary: Trim RSS-imported content, keeping the newest items of each active import.
keywords: Konsole, Befehl, exp:cleanuprss, cleanuprss, console, command, CLI, cleanuprss.php
type: generated
generated: exp:help:examples
---
# exp:cleanuprss

Trim RSS-imported content, keeping the newest items of each active import.

Skript `bin/php/cleanuprss.php`, Code in `kernel/private/classes/commands/cleanuprss.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:cleanuprss --help
php bin/php/cleanuprss.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Exponential RSS Import Cleanup
Keeps the newest items of each active RSS import and removes the rest.

Configured in content.ini [RSSImportCleanupSettings]; it removes nothing
until that names the classes it is allowed to remove.

./bin/php/cleanuprss.php --dry-run
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | List what would be removed, and remove nothing |
| `--keep` | erforderlich | Keep this many items per feed instead of the configured number |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/cleanuprss.php`, `kernel/private/classes/commands/cleanuprss.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
