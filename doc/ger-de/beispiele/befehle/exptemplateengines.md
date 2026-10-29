---
title: exp:exptemplateengines
audience: developer, admin
category: examples
summary: "Listet die Template-Engines: Bibliothek gefunden oder nicht, Version, Dateiendungen, Einstellungen, Sicherheitsrichtlinie, Cache der kompilierten Templates."
keywords: Konsole, Befehl, exp:exptemplateengines, exptemplateengines, console, command, CLI, exptemplateengines.php, exp:template:engines
type: generated
generated: exp:help:examples
---
# exp:exptemplateengines

Listet die Template-Engines: Bibliothek gefunden oder nicht, Version, Dateiendungen, Einstellungen, Sicherheitsrichtlinie, Cache der kompilierten Templates.

Skript `bin/php/exptemplateengines.php`, Code in `kernel/private/classes/commands/exptemplateengines.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exptemplateengines --help
./console exp:template:engines --help
php bin/php/exptemplateengines.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
List the template engines of the installation
For each engine: whether its library is available, its version, the file endings it renders, its
settings, whether its security policy is on and where its compiled templates are kept.

./console exp:template:engines
./console exp:template:engines --format=json

Exit code: 0 every engine is available; 3 an engine's library is missing; 2 wrong options.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | text (Standard) oder json |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exptemplateengines.php`, `kernel/private/classes/commands/exptemplateengines.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
