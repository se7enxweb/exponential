---
title: exp:trashpurge
audience: developer, admin
category: examples
summary: Löscht alle Objekte im Papierkorb endgültig (Probelauf, außer mit --apply).
keywords: Konsole, Befehl, exp:trashpurge, trashpurge, console, command, CLI, trashpurge.php
type: generated
generated: exp:help:examples
---
# exp:trashpurge

Löscht alle Objekte im Papierkorb endgültig (Probelauf, außer mit --apply).

Skript `bin/php/trashpurge.php`, Code in `kernel/private/classes/commands/trashpurge.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:trashpurge --help
php bin/php/trashpurge.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Empty Exponential trash.
Permanently deletes all objects in the trash.
Without --apply nothing is deleted: the script counts and lists what it would purge. --dry-run
means the same as no flag; --apply together with --dry-run is a usage error (exit 2). The cronjob
part trashpurge purges as before.

./bin/php/trashpurge.php
./bin/php/trashpurge.php --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Objekte löschen (Standard ist ein Probelauf, der sie auflistet und nichts löscht) |
| `--dry-run` |  | Der Standard: zählen und auflisten, was gelöscht würde, nichts ändern |
| `--iteration-sleep` | erforderlich | Sekunden Pause zwischen den Iterationen beim Löschen, auch als Dezimalzahl. Standard ist eine Sekunde. |
| `--iteration-limit` | erforderlich | Anzahl der Einträge, die beim Löschen in jeder Iteration entfernt werden. Standard ist 100. |
| `--memory-monitoring` |  | Wenn gesetzt, wird der Speicherverbrauch in var/log/trashpurge.log protokolliert. |
| `--trashed-days` | erforderlich | Wenn gesetzt, werden nur Objekte endgültig gelöscht, die seit mindestens so vielen Tagen im Papierkorb liegen. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/trashpurge.php`, `kernel/private/classes/commands/trashpurge.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
