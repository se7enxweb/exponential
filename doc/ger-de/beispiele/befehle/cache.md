---
title: exp:cache
audience: developer, admin
category: examples
summary: "Caches nach Tag, ID oder alle leeren und untersuchen: Inhalte, statisch, HTTP, Velocity, OPcache, APCu."
keywords: Konsole, Befehl, exp:cache, cache, console, command, CLI, cache.php
type: generated
generated: exp:help:examples
---
# exp:cache

Caches nach Tag, ID oder alle leeren und untersuchen: Inhalte, statisch, HTTP, Velocity, OPcache, APCu.

Skript `bin/php/cache.php`, Code in `kernel/private/classes/commands/cache.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:cache --help
php bin/php/cache.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | Auflisten, was geleert würde, und nichts ändern |
| `--json` |  | Als ein JSON-Objekt berichten |
| `--all` |  | clear: jeder Cache |
| `--tag` | erforderlich | clear: Caches mit diesen Tags (durch Kommas getrennt); httpcache purge: diese HTTP-Cache-Tags |
| `--id` | erforderlich | clear: Caches mit diesen IDs (durch Kommas getrennt) |
| `--purge` |  | clear, imagealias: die Einträge wirklich entfernen |
| `--expiry` | erforderlich | mit --purge: nur Einträge, die älter sind, z. B. "-2 days" |
| `--iteration-sleep` | erforderlich | mit --purge: Sekunden Pause zwischen den Iterationen |
| `--iteration-max` | erforderlich | mit --purge: Einträge pro Iteration |
| `--sizes` |  | list: Dateien und Bytes jedes Caches zählen |
| `--site` | erforderlich | static: der Siteaccess, der erzeugt oder geleert wird (Standard: jeder cachebare) |
| `--path` | erforderlich | static: Seiten relativ zur Website, durch Kommas getrennt |
| `--node` | erforderlich | static, httpcache purge: Knoten-IDs, durch Kommas getrennt |
| `--url` | erforderlich | httpcache purge: URLs, durch Kommas getrennt |
| `--max-pages` | erforderlich | static regenerate: nach so vielen Seiten pro Website aufhören (Standard 2500) |
| `--max-depth` | erforderlich | static regenerate: Links so viele Schritte weit folgen (Standard 12) |
| `--keep` |  | static regenerate: zum Gespeicherten hinzufügen, statt es zu ersetzen |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[group][action]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/cache.php`, `kernel/private/classes/commands/cache.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
