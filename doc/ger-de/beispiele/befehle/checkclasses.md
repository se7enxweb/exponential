---
title: exp:checkclasses
audience: developer, admin
category: examples
summary: Load every declared class and report the ones PHP refuses to load.
keywords: Konsole, Befehl, exp:checkclasses, checkclasses, console, command, CLI, checkclasses.php
type: generated
generated: exp:help:examples
---
# exp:checkclasses

Load every declared class and report the ones PHP refuses to load.

Skript `bin/php/checkclasses.php`, Code in `kernel/private/classes/commands/checkclasses.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:checkclasses --help
php bin/php/checkclasses.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Exponential class loader check
Loads every class this installation declares and reports the ones php refuses.

./bin/php/checkclasses.php
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--kernel` |  | Check the kernel classes as well as the extension ones. Slower, and they are the ones least likely to be wrong. |
| `--tests` |  | Include classes that live under a tests directory. Left out by default: they are usually written against whichever version of phpunit was current, and a test class that will not load breaks nothing anybody is looking at. |
| `--quiet-ok` |  | Print nothing when everything loads. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/checkclasses.php`, `kernel/private/classes/commands/checkclasses.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
