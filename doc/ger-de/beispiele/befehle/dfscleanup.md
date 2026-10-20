---
title: exp:dfscleanup
audience: developer, admin
category: examples
summary: Gleicht Datenbank und DFS-Dateien miteinander ab (-S, -B) und löscht nicht vorhandene (-D).
keywords: Konsole, Befehl, exp:dfscleanup, dfscleanup, console, command, CLI, dfscleanup.php
type: generated
generated: exp:help:examples
---
# exp:dfscleanup

Gleicht Datenbank und DFS-Dateien miteinander ab (-S, -B) und löscht nicht vorhandene (-D).

Skript `bin/php/dfscleanup.php`, Code in `kernel/private/classes/commands/dfscleanup.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:dfscleanup --help
php bin/php/dfscleanup.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Script for checking database and DFS file consistency
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `-S` |  | Prüft die Dateien auf der DFS-Freigabe gegen die Dateien in der Datenbank |
| `-B` |  | Prüft die Dateien in der Datenbank gegen die Dateien auf der DFS-Freigabe |
| `-D` |  | Nicht vorhandene Dateien löschen |
| `--path` | erforderlich | Pfad, auf den die Prüfungen beschränkt werden (z. B. var/storage/content; Standard: var/) |
| `--iteration-limit` | erforderlich | Anzahl der Einträge, die beim Löschen in jeder Iteration entfernt werden. Standard ist alles in einer Iteration. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/dfscleanup.php`, `kernel/private/classes/commands/dfscleanup.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
