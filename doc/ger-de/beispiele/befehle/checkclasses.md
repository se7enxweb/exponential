---
title: exp:checkclasses
audience: developer, admin
category: examples
summary: Lädt jede deklarierte Klasse und meldet die, deren Laden PHP verweigert.
keywords: Konsole, Befehl, exp:checkclasses, checkclasses, console, command, CLI, checkclasses.php
type: generated
generated: exp:help:examples
---
# exp:checkclasses

Lädt jede deklarierte Klasse und meldet die, deren Laden PHP verweigert.

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
| `--kernel` |  | Die Kernel-Klassen ebenso prüfen wie die der Erweiterungen. Langsamer, und sie sind am wenigsten wahrscheinlich fehlerhaft. |
| `--tests` |  | Klassen einbeziehen, die unter einem Verzeichnis tests liegen. Standardmäßig weggelassen: Sie sind meist gegen die jeweils aktuelle Version von phpunit geschrieben, und eine Testklasse, die nicht lädt, macht nichts kaputt, worauf jemand schaut. |
| `--quiet-ok` |  | Nichts ausgeben, wenn alles lädt. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/checkclasses.php`, `kernel/private/classes/commands/checkclasses.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
