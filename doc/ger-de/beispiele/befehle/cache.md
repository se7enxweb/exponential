---
title: exp:cache
audience: developer, admin
category: examples
summary: "Clear and inspect caches by tag, id or all: content, static, HTTP, Velocity, OPcache, APCu."
keywords: Konsole, Befehl, exp:cache, cache, console, command, CLI, cache.php
type: generated
generated: exp:help:examples
---
# exp:cache

Clear and inspect caches by tag, id or all: content, static, HTTP, Velocity, OPcache, APCu.

Skript `bin/php/cache.php`, Code in `kernel/private/classes/commands/cache.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:cache --help
php bin/php/cache.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | List what would be cleared and change nothing |
| `--json` |  | Report as one JSON object |
| `--all` |  | clear: every cache |
| `--tag` | erforderlich | clear: caches with these tags (comma separated); httpcache purge: these HTTP cache tags |
| `--id` | erforderlich | clear: caches with these ids (comma separated) |
| `--purge` |  | clear, imagealias: remove the entries for real |
| `--expiry` | erforderlich | with --purge: only entries older than this, e.g. "-2 days" |
| `--iteration-sleep` | erforderlich | with --purge: seconds to sleep between iterations |
| `--iteration-max` | erforderlich | with --purge: entries per iteration |
| `--sizes` |  | list: count the files and bytes of each cache |
| `--site` | erforderlich | static: the siteaccess to generate or clear (default every cacheable one) |
| `--path` | erforderlich | static: pages relative to the site, comma separated |
| `--node` | erforderlich | static, httpcache purge: node ids, comma separated |
| `--url` | erforderlich | httpcache purge: urls, comma separated |
| `--max-pages` | erforderlich | static regenerate: stop after this many pages per site (default 2500) |
| `--max-depth` | erforderlich | static regenerate: follow links this many steps (default 12) |
| `--keep` |  | static regenerate: add to what is stored instead of replacing it |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[group][action]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/cache.php`, `kernel/private/classes/commands/cache.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
