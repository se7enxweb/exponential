---
title: exp:adddefaultstates
audience: developer, admin
category: examples
summary: Gibt jedem Inhaltsobjekt den Standardzustand jeder Zustandsgruppe, aus der es keinen Zustand hat (Probelauf, außer mit --apply).
keywords: Konsole, Befehl, exp:adddefaultstates, adddefaultstates, console, command, CLI, adddefaultstates.php
type: generated
generated: exp:help:examples
---
# exp:adddefaultstates

Gibt jedem Inhaltsobjekt den Standardzustand jeder Zustandsgruppe, aus der es keinen Zustand hat (Probelauf, außer mit --apply).

Skript `bin/php/adddefaultstates.php`, Code in `kernel/private/classes/commands/adddefaultstates.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:adddefaultstates --help
php bin/php/adddefaultstates.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Adds default states to content objects: every object that has no state of a state group gets
the group's default state (the state with priority 0). Objects with a state of the group are left
alone, so the script can run again. Prints PASS or FAIL at the end.
Without --apply nothing is written: the script lists per state group how many objects would get
its default state. --dry-run means the same as no flag; --apply together with --dry-run is a usage
error (exit 2).

Example: php bin/php/adddefaultstates.php
         php bin/php/adddefaultstates.php --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Zustände hinzufügen (Standard ist ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard: auflisten, wie viele Objekte einen Standardzustand erhielten, nichts ändern |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/adddefaultstates.php`, `kernel/private/classes/commands/adddefaultstates.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
