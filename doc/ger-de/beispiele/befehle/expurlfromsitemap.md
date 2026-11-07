---
title: exp:expurlfromsitemap
audience: developer, admin
category: examples
summary: "Eine URL-Zuordnung aus der Sitemap einer alten Website (sitemap.xml, ein Sitemap-Index, .gz) oder einer Crawl-Liste beginnen: jede alte Adresse als auszufüllende Zeile."
keywords: Konsole, Befehl, exp:expurlfromsitemap, expurlfromsitemap, console, command, CLI, expurlfromsitemap.php, exp:url:from-sitemap
type: generated
generated: exp:help:examples
---
# exp:expurlfromsitemap

Eine URL-Zuordnung aus der Sitemap einer alten Website (sitemap.xml, ein Sitemap-Index, .gz) oder einer Crawl-Liste beginnen: jede alte Adresse als auszufüllende Zeile.

Skript `bin/php/expurlfromsitemap.php`, Code in `kernel/private/classes/commands/expurlfromsitemap.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expurlfromsitemap --help
./console exp:url:from-sitemap --help
php bin/php/expurlfromsitemap.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Start a URL map from an old site's sitemap or a crawl list
Reads a sitemap (an address or a file): a <urlset>, a sitemap index with the sitemaps it names, either
gzip-compressed, or a crawl list with one address per line. Writes every old address once as a row
with an empty new_url, in any format exp:url:import reads: fill the targets in (exp:url:suggest helps),
then import. Only the addresses of the sitemap's own host are kept unless --only-host or --any-host.

./console exp:url:from-sitemap https://www.old-site.com/sitemap.xml --output=var/tmp/old-site.csv
./console exp:url:from-sitemap var/tmp/crawl.txt --limit=500 --output=var/tmp/old-site.xlsx
./console exp:url:from-sitemap https://www.old-site.com/sitemap_index.xml --any-host --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--output` | erforderlich | Die zu schreibende Datei (Standard die Standardausgabe; nötig für xlsx und ods) |
| `--output-format` | erforderlich | import, export: was der Befehl ausgibt, text (Standard) oder json (die Maschinenausgabe exp-url/1); --format ist dort das Format der Datei; from-sitemap, from-404, suggest: das Format der geschriebenen Datei (csv, json, xlsx, ods, apache, nginx; Standard nach --output, sonst csv) |
| `--format` | erforderlich | import: das Format der Datei (csv, json, xlsx, ods, apache, nginx; Standard nach ihrem Namen oder Inhalt); export: das zu schreibende Format (Standard nach --output, sonst csv); batches, undo, from-sitemap, from-404, suggest, test: text (Standard) oder json (die Maschinenausgabe exp-url/1) |
| `--limit` | erforderlich | batches: wie viele der letzten Importe aufgelistet werden (Standard 20); from-sitemap, from-404, test: die meisten Zeilen (Standard alle) |
| `--only-host` | erforderlich | from-sitemap: nur die Adressen dieses Hosts behalten (durch Kommas getrennt oder wiederholt; Standard der Host der Sitemap-Adresse oder ihrer ersten Adresse) |
| `--any-host` |  | from-sitemap: die Adressen aller Hosts behalten |
| `--keep-duplicates` |  | from-sitemap: eine mehrfach aufgeführte Adresse mehrfach behalten (Standard: einmal) |
| `--full-urls` |  | from-sitemap: die alten Adressen mit Schema und Host schreiben (Standard: Pfad und Abfrage, wie ein Import auf dieser Website sie braucht) |
| `--timeout` | erforderlich | from-sitemap, test: die Sekunden, die eine Anfrage dauern darf (Standard 15) |
| `--insecure` |  | from-sitemap, test: TLS-Zertifikate nicht prüfen (für Testhosts mit einem selbst signierten) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[source]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expurlfromsitemap.php`, `kernel/private/classes/commands/expurlfromsitemap.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
