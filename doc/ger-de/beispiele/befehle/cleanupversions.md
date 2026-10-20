---
title: exp:cleanupversions
audience: developer, admin
category: examples
summary: "Remove archived content versions beyond the VersionManagement limits (-n: do not wait)."
keywords: Konsole, Befehl, exp:cleanupversions, cleanupversions, console, command, CLI, cleanupversions.php
type: generated
generated: exp:help:examples
---
# exp:cleanupversions

Remove archived content versions beyond the VersionManagement limits (-n: do not wait).

Skript `bin/php/cleanupversions.php`, Code in `kernel/private/classes/commands/cleanupversions.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:cleanupversions --help
php bin/php/cleanupversions.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Remove archived content object versions according to [VersionManagement]/DefaultVersionHistoryLimit and [VersionManagement]/VersionHistoryClass settings

Example: ./bin/php/cleanupversions.php -n
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `-n` |  | Do not wait |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/cleanupversions.php`, `kernel/private/classes/commands/cleanupversions.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
