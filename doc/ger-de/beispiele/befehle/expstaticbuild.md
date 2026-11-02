---
title: exp:expstaticbuild
audience: developer, admin
category: examples
summary: "Baut eine statische Kopie des Siteaccess eines Ziels in seinen Ordner (statisches Veröffentlichen): als anonymer Benutzer durchlaufen, Links umgeschrieben, Dateien gesammelt, Zusatzdateien und Manifest geschrieben."
keywords: Konsole, Befehl, exp:expstaticbuild, expstaticbuild, console, command, CLI, expstaticbuild.php, exp:static:build
type: generated
generated: exp:help:examples
---
# exp:expstaticbuild

Baut eine statische Kopie des Siteaccess eines Ziels in seinen Ordner (statisches Veröffentlichen): als anonymer Benutzer durchlaufen, Links umgeschrieben, Dateien gesammelt, Zusatzdateien und Manifest geschrieben.

Skript `bin/php/expstaticbuild.php`, Code in `kernel/private/classes/commands/expstaticbuild.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expstaticbuild --help
./console exp:static:build --help
php bin/php/expstaticbuild.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Build a static copy of a site
Crawls the target's siteaccesses as the anonymous user, strips debug output, form tokens and
session ids, refuses pages that still carry one, rewrites the links (relative, or below the base
path), collects the files the pages name (and the storage's images with Assets=referenced-and-storage),
writes .nojekyll, 404.html, robots.txt, sitemap.xml and the manifest, checks the links and replaces the
content of var/<site>/static-publish/<target>/. Targets are staticcache.ini [Target_<id>] blocks.

./console exp:static:build github-public
./console exp:static:build github-public --dry-run          report what would change
./console exp:static:build github-public --paths=/about-us  only this page
./console exp:static:build github-public --only-siteaccess=ger    only the siteaccess ger of a target of several
./console exp:static:build github-public --format=json --report=var/log/static.json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--full` |  | Die ganze Website durchlaufen (der Standard ohne --paths oder --nodes); nicht mit --paths oder --nodes |
| `--paths` | erforderlich | Nur diese Seiten bauen, nach ihrem Pfad in der Kopie (/about-us, /ger/about-us für einen Siteaccess in einem Ordner; eine Kommaliste oder wiederholt); der Rest des letzten Builds bleibt |
| `--nodes` | erforderlich | Nur die Seiten dieser Knoten bauen (Knoten-IDs; eine Kommaliste oder wiederholt), in jedem Siteaccess des Ziels; der Rest des letzten Builds bleibt |
| `--dry-run` |  | In den Staging-Ordner bauen, melden, was sich ändern würde, und den Ordner des Ziels lassen, wie er ist |
| `--report` | erforderlich | Den Bericht (exp-static/1, JSON) zusätzlich in diese Datei schreiben |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-static/1) |
| `--crawl-url` | erforderlich | Die Seiten von dieser Adresse abrufen statt von der CrawlURL des Ziels oder der SiteURL des Siteaccess (http://127.0.0.1:8080) |
| `--max-pages` | erforderlich | Das Durchlaufen nach so vielen Seiten beenden (Standard MaxPages des Ziels) |
| `--keep-staging` |  | Den Staging-Ordner des Builds behalten (sein Pfad steht im Bericht), statt ihn zu entfernen |
| `--login` | erforderlich | Run as this user (the administration starts its jobs so); recorded as who ran it |
| `--run-id` | erforderlich | The id of the run (date-time-6 hex digits) instead of a new one (the administration passes its job's id) |
| `--progress` | erforderlich | Also append the progress, one JSON object per line, to this file (the administration follows it) |
| `--only-siteaccess` | erforderlich | Nur diesen der Siteaccesses des Ziels bauen (ein Ziel mehrerer, SiteAccessList[]); die Seiten der anderen aus dem letzten Build bleiben |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[target]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expstaticbuild.php`, `kernel/private/classes/commands/expstaticbuild.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
