---
title: exp:checkdbfiles
audience: developer, admin
category: examples
summary: Prüft die Datenbank-Update-Dateien gegen den Upgrade-Pfad; meldet fehlende oder überzählige Dateien.
keywords: Konsole, Befehl, exp:checkdbfiles, checkdbfiles, console, command, CLI, checkdbfiles.php
type: generated
generated: exp:help:examples
---
# exp:checkdbfiles

Prüft die Datenbank-Update-Dateien gegen den Upgrade-Pfad; meldet fehlende oder überzählige Dateien.

Skript `bin/php/checkdbfiles.php`, Code in `kernel/private/classes/commands/checkdbfiles.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:checkdbfiles --help
php bin/php/checkdbfiles.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--no-verify-branches` |  | Den Inhalt der Dateien nicht mit früheren Branches vergleichen (um SVN zu vermeiden) |
| `--export-path` | erforderlich | Verzeichnis, in dem ein neues Verzeichnis für die SVN-Exporte angelegt und wieder entfernt wird (Standard var/tmp) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/checkdbfiles.php`, `kernel/private/classes/commands/checkdbfiles.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
