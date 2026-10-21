---
title: exp:ezcheckphptag
audience: developer, admin
category: examples
summary: Prüft Dateien auf Zeichen vor dem PHP-Start-Tag oder nach dem End-Tag.
keywords: Konsole, Befehl, exp:ezcheckphptag, ezcheckphptag, console, command, CLI, ezcheckphptag.php
type: generated
generated: exp:help:examples
---
# exp:ezcheckphptag

Prüft Dateien auf Zeichen vor dem PHP-Start-Tag oder nach dem End-Tag.

Skript `bin/php/ezcheckphptag.php`, Code in `kernel/private/classes/commands/ezcheckphptag.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezcheckphptag --help
php bin/php/ezcheckphptag.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--no-print` |  | Den Pfad fehlerhafter Dateien nicht ausgeben |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[path+]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezcheckphptag.php`, `kernel/private/classes/commands/ezcheckphptag.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
