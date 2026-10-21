---
title: exp:ezpgenerateautoloads
audience: developer, admin
category: examples
summary: "Erzeugt die Autoload-Arrays der Klassen: Kernel (-k), Kernel-Overrides (-o), Erweiterungen (-e)."
keywords: Konsole, Befehl, exp:ezpgenerateautoloads, ezpgenerateautoloads, console, command, CLI, ezpgenerateautoloads.php
type: generated
generated: exp:help:examples
---
# exp:ezpgenerateautoloads

Erzeugt die Autoload-Arrays der Klassen: Kernel (-k), Kernel-Overrides (-o), Erweiterungen (-e).

Skript `bin/php/ezpgenerateautoloads.php`, Code in `kernel/private/classes/commands/ezpgenerateautoloads.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezpgenerateautoloads --help
php bin/php/ezpgenerateautoloads.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--help` |  | Hilfe anzeigen (short: -h) |
| `--quiet` |  | keine Ausgabe außer bei Fehlern (short: -q) |
| `--target` | erforderlich | Das Verzeichnis, in das die erzeugte Autoload-Datei geschrieben wird. (short: -t) |
| `--verbose` |  | Ob mehr Informationen angezeigt werden sollen. (short: -v) |
| `--dry-run` |  | Ob eine neue Autoload-Datei geschrieben werden soll. (short: -n) |
| `--kernel` |  | Ob ein Autoload-Array für die Kernel-Dateien erzeugt werden soll. (short: -k) |
| `--kernel-override` |  | Ob ein Autoload-Array für die Kernel-Override-Dateien erzeugt werden soll. (short: -o) |
| `--extension` |  | Ob ein Autoload-Array für die Erweiterungen erzeugt werden soll. (short: -e) |
| `--tests` |  | Ob ein Autoload-Array für die Tests erzeugt werden soll. (short: -s) |
| `--exclude` | erforderlich | Verzeichnisse, die bei der Klassensuche ausgelassen werden. |
| `--progress` |  | Ob der Fortschritt auf der Kommandozeile angezeigt werden soll. (short: -p) |
| `--allow-root-user` |  | Zur Kompatibilität mit den anderen Skripten akzeptiert; hat keine Wirkung. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezpgenerateautoloads.php`, `kernel/private/classes/commands/ezpgenerateautoloads.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
