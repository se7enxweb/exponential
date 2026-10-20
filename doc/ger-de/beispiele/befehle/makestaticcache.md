---
title: exp:makestaticcache
audience: developer, admin
category: examples
summary: Crawl the public siteaccesses and generate static pages (--site, --max-pages, --max-depth).
keywords: Konsole, Befehl, exp:makestaticcache, makestaticcache, console, command, CLI, makestaticcache.php
type: generated
generated: exp:help:examples
---
# exp:makestaticcache

Crawl the public siteaccesses and generate static pages (--site, --max-pages, --max-depth).

Skript `bin/php/makestaticcache.php`, Code in `kernel/private/classes/commands/makestaticcache.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:makestaticcache --help
php bin/php/makestaticcache.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `-f` |  |  |
| `--force` |  | Accepted for compatibility; a run always replaces what it generates. |
| `--site` | erforderlich | Generate only this siteaccess. Repeat for several; omit for every public one. |
| `--max-pages` | erforderlich | Stop after this many pages per site. Default 2500. |
| `--max-depth` | erforderlich | Follow links this many steps from the site root. Default 12. |
| `--keep` |  | Add to what is already stored instead of replacing it. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/makestaticcache.php`, `kernel/private/classes/commands/makestaticcache.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
