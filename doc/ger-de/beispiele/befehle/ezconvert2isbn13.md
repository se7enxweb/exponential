---
title: exp:ezconvert2isbn13
audience: developer, admin
category: examples
summary: Convert ISBN-10 numbers to ISBN-13.
keywords: Konsole, Befehl, exp:ezconvert2isbn13, ezconvert2isbn13, console, command, CLI, ezconvert2isbn13.php
type: generated
generated: exp:help:examples
---
# exp:ezconvert2isbn13

Convert ISBN-10 numbers to ISBN-13.

Skript `bin/php/ezconvert2isbn13.php`, Code in `kernel/private/classes/commands/ezconvert2isbn13.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezconvert2isbn13 --help
php bin/php/ezconvert2isbn13.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--class-id` | erforderlich | The class id for the ISBN attribute. |
| `--attribute-id` | erforderlich | The attribute id for the ISBN attribute which should be converted. |
| `--all-classes` |  | Will convert all ISBN attributes in all content classes. |
| `-f` |  | Short alias for force. |
| `--force` |  | Will convert all attributes even if the class is set to ISBN. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezconvert2isbn13.php`, `kernel/private/classes/commands/ezconvert2isbn13.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
