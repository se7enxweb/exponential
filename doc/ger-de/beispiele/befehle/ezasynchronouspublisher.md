---
title: exp:ezasynchronouspublisher
audience: developer, admin
category: examples
summary: Der Befehl exp:ezasynchronouspublisher (bin/php/ezasynchronouspublisher.php).
keywords: Konsole, Befehl, exp:ezasynchronouspublisher, ezasynchronouspublisher, console, command, CLI, ezasynchronouspublisher.php
type: generated
generated: exp:help:examples
---
# exp:ezasynchronouspublisher


Skript `bin/php/ezasynchronouspublisher.php`, Code in `kernel/private/classes/commands/ezasynchronouspublisher.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezasynchronouspublisher --help
php bin/php/ezasynchronouspublisher.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Processes the Exponential publishing queue
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `-n` |  |  |
| `--daemon` |  | Im Hintergrund laufen |
| `-p` | erforderlich |  |
| `--pid-file` | erforderlich | PID-Datei |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezasynchronouspublisher.php`, `kernel/private/classes/commands/ezasynchronouspublisher.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
