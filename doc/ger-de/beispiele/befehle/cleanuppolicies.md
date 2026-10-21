---
title: exp:cleanuppolicies
audience: developer, admin
category: examples
summary: Entfernt Richtlinien von Modulen, die es nicht mehr gibt (--dry-run listet sie zuerst auf).
keywords: Konsole, Befehl, exp:cleanuppolicies, cleanuppolicies, console, command, CLI, cleanuppolicies.php
type: generated
generated: exp:help:examples
---
# exp:cleanuppolicies

Entfernt Richtlinien von Modulen, die es nicht mehr gibt (--dry-run listet sie zuerst auf).

Skript `bin/php/cleanuppolicies.php`, Code in `kernel/private/classes/commands/cleanuppolicies.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:cleanuppolicies --help
php bin/php/cleanuppolicies.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Remove from database policies defined on module which do not exist in a modules folder
according to settings from module.ini/[ModuleSettings]/ExtensionRepositories

Example: ./bin/php/cleanuppolicies.php --dry-run
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | Testmodus: die Liste der betroffenen Richtlinien ausgeben, ohne sie zu entfernen |
| `-n` |  | Nicht warten |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/cleanuppolicies.php`, `kernel/private/classes/commands/cleanuppolicies.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
