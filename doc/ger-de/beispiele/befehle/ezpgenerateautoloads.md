---
title: exp:ezpgenerateautoloads
audience: developer, admin
category: examples
summary: "Generate the class autoload arrays: kernel (-k), kernel overrides (-o), extensions (-e)."
keywords: Konsole, Befehl, exp:ezpgenerateautoloads, ezpgenerateautoloads, console, command, CLI, ezpgenerateautoloads.php
type: generated
generated: exp:help:examples
---
# exp:ezpgenerateautoloads

Generate the class autoload arrays: kernel (-k), kernel overrides (-o), extensions (-e).

Skript `bin/php/ezpgenerateautoloads.php`, Code in `kernel/private/classes/commands/ezpgenerateautoloads.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezpgenerateautoloads --help
php bin/php/ezpgenerateautoloads.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--help` |  | Show help information (short: -h) |
| `--quiet` |  | do not give any output except when errors occur (short: -q) |
| `--target` | erforderlich | The directory to where the generated autoload file should be written. (short: -t) |
| `--verbose` |  | Whether or not to display more information. (short: -v) |
| `--dry-run` |  | Whether a new file autoload file should be written. (short: -n) |
| `--kernel` |  | If an autoload array for the kernel files should be generated. (short: -k) |
| `--kernel-override` |  | If an autoload array for the kernel override files should be generated. (short: -o) |
| `--extension` |  | If an autoload array for the extensions should be generated. (short: -e) |
| `--tests` |  | If an autoload array for the tests should be generated. (short: -s) |
| `--exclude` | erforderlich | Folders to exclude from the class search. |
| `--progress` |  | If progress output should be shown on the command-line. (short: -p) |
| `--allow-root-user` |  | Accepted for compatibility with the other scripts; has no effect. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezpgenerateautoloads.php`, `kernel/private/classes/commands/ezpgenerateautoloads.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
