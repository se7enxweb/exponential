---
title: exp:eztc
audience: developer, admin
category: examples
summary: Kompiliert die Templates eines Siteaccess in den Template-Cache (--force kompiliert alle neu).
keywords: Konsole, Befehl, exp:eztc, eztc, console, command, CLI, eztc.php
type: generated
generated: exp:help:examples
---
# exp:eztc

Kompiliert die Templates eines Siteaccess in den Template-Cache (--force kompiliert alle neu).

Skript `bin/php/eztc.php`, Code in `kernel/private/classes/commands/eztc.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:eztc --help
php bin/php/eztc.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--compile-directory` | erforderlich | Wohin die kompilierten Dateien gelegt werden, Standard ist template/compiled im aktuellen Cache-Verzeichnis |
| `--www-dir` | erforderlich | Der Teil vor der index.php in Ihrer URL; geben Sie ihn an, wenn Sie im Modus ohne virtuellen Host arbeiten |
| `--index-file` | erforderlich | Der Name Ihrer index.php, wenn Sie im Modus ohne virtuellen Host arbeiten |
| `--access-path` | erforderlich | Zusätzlicher Zugriffspfad |
| `--force` |  | Templates kompilieren, ob sie sich geändert haben oder nicht |
| `--full-url` |  | Sorgt dafür, dass erzeugte URLs http:// enthalten (also global sind), vor allem für Websites, die das HTML von Exponential einbinden (z. B. Zahlungs-Gateways) |
| `--no-full-url` |  | Sorgt dafür, dass erzeugte URLs relativ zur Website sind. (Standard) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/eztc.php`, `kernel/private/classes/commands/eztc.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
