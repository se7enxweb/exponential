---
title: exp:upgradesteps
audience: developer, admin
category: examples
summary: "Upgrade-Schritte: die registrierten Schritte mit ID, Stufe, Version, Engines und Beschreibung."
keywords: Konsole, Befehl, exp:upgradesteps, upgradesteps, console, command, CLI, upgradesteps.php, exp:upgrade:steps
type: generated
generated: exp:help:examples
---
# exp:upgradesteps

Upgrade-Schritte: die registrierten Schritte mit ID, Stufe, Version, Engines und Beschreibung.

Skript `bin/php/upgradesteps.php`, Code in `kernel/private/classes/commands/upgradesteps.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:upgradesteps --help
./console exp:upgrade:steps --help
php bin/php/upgradesteps.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Upgrade steps
The registered upgrade steps in the order they run: id, stage, version, engines, owner (kernel
or the extension that registers it), whether it runs only when named (optional) and what it
does. Reads the settings only, never the database.

./console exp:upgrade:steps
./console exp:upgrade:steps --stage=database --engine=postgresql --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--stage` | erforderlich | Nur die Schritte dieser Stufe |
| `--engine` | erforderlich | Nur die Schritte für diese Engine: mysql, postgresql, sqlite, oracle, mongodb |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-upgrade/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/upgradesteps.php`, `kernel/private/classes/commands/upgradesteps.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
