---
title: exp:phar
audience: developer, admin
category: examples
summary: Build and inspect the engine archive dist/engine.phar (verbs build, check, clean, info).
keywords: Konsole, Befehl, exp:phar, phar, console, command, CLI, phar.php
type: generated
generated: exp:help:examples
---
# exp:phar

Build and inspect the engine archive dist/engine.phar (verbs build, check, clean, info).

Skript `bin/php/phar.php`, Code in `kernel/private/classes/commands/phar.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:phar --help
php bin/php/phar.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Exponential engine phar

Build and inspect the engine archive.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  | Print the result as JSON. |
| `--output` | erforderlich | Write the archive somewhere other than dist/engine.phar. |
| `--force` |  | build: rebuild even when the archive already carries exactly the files on disk. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[verb]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/phar.php`, `kernel/private/classes/commands/phar.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
