---
title: exp:ezchecktranslation
audience: developer, admin
category: examples
summary: Show statistics for one translation, e.g. ezchecktranslation.php ita-IT.
keywords: Konsole, Befehl, exp:ezchecktranslation, ezchecktranslation, console, command, CLI, ezchecktranslation.php
type: generated
generated: exp:help:examples
---
# exp:ezchecktranslation

Show statistics for one translation, e.g. ezchecktranslation.php ita-IT.

Skript `bin/php/ezchecktranslation.php`, Code in `kernel/private/classes/commands/ezchecktranslation.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezchecktranslation --help
php bin/php/ezchecktranslation.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--ignore-tr-setup` |  | Tells the analyzer to skip all translations regarding the setup |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[translation]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezchecktranslation.php`, `kernel/private/classes/commands/ezchecktranslation.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
