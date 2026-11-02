---
title: exp:expmigrationrecordlist
audience: developer, admin
category: examples
summary: Listet die Aufzeichnungen von Änderungen, laufende und beendete, die neueste zuerst, mit Art, Zustand und Zahl der Ereignisse.
keywords: Konsole, Befehl, exp:expmigrationrecordlist, expmigrationrecordlist, console, command, CLI, expmigrationrecordlist.php, exp:migration:record:list
type: generated
generated: exp:help:examples
---
# exp:expmigrationrecordlist

Listet die Aufzeichnungen von Änderungen, laufende und beendete, die neueste zuerst, mit Art, Zustand und Zahl der Ereignisse.

Skript `bin/php/expmigrationrecordlist.php`, Code in `kernel/private/classes/commands/expmigrationrecordlist.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationrecordlist --help
./console exp:migration:record:list --help
php bin/php/expmigrationrecordlist.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
List the recordings of changes
Every recording, the newest first: its id, state (recording or stopped), kind (session or site), how
many events it has, when it started, whose changes a session recording keeps (the user id) and its
label. Changes nothing.

./console exp:migration:record:list
./console exp:migration:record:list --count=10 --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--count` | erforderlich | Wie viele Aufzeichnungen gelistet werden, die neueste zuerst (Standard 50) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationrecordlist.php`, `kernel/private/classes/commands/expmigrationrecordlist.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
