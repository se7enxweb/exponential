---
title: exp:expmigrationrecorddiscard
audience: developer, admin
category: examples
summary: "Verwirft eine Aufzeichnung von Änderungen: die Aufzeichnung und jedes erfasste Ereignis werden entfernt (eine laufende hört auf aufzuzeichnen); ein Probelauf ohne --apply."
keywords: Konsole, Befehl, exp:expmigrationrecorddiscard, expmigrationrecorddiscard, console, command, CLI, expmigrationrecorddiscard.php, exp:migration:record:discard
type: generated
generated: exp:help:examples
---
# exp:expmigrationrecorddiscard

Verwirft eine Aufzeichnung von Änderungen: die Aufzeichnung und jedes erfasste Ereignis werden entfernt (eine laufende hört auf aufzuzeichnen); ein Probelauf ohne --apply.

Skript `bin/php/expmigrationrecorddiscard.php`, Code in `kernel/private/classes/commands/expmigrationrecorddiscard.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationrecorddiscard --help
./console exp:migration:record:discard --help
php bin/php/expmigrationrecorddiscard.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Discard a recording of changes
Removes the recording and every event it captured; a recording that runs stops. What was changed
while it recorded stays changed: only the record of it goes. Without --apply it says what it would
remove.

./console exp:migration:record:discard 20261101-120000-a1b2c3
./console exp:migration:record:discard 20261101-120000-a1b2c3 --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[recording]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationrecorddiscard.php`, `kernel/private/classes/commands/expmigrationrecorddiscard.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
