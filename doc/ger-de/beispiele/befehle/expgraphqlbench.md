---
title: exp:expgraphqlbench
audience: developer, admin
category: examples
summary: "Misst die Geschwindigkeit der GraphQL-API an Entscheidung 18: zwischengespeichertes GET, 304, zwischengespeichertes POST, eine zwischengespeicherte Antwort über den Kernel und eine nicht zwischengespeicherte Abfrage, je N Anfragen, mit Median, p95 und Maximum."
keywords: Konsole, Befehl, exp:expgraphqlbench, expgraphqlbench, console, command, CLI, expgraphqlbench.php, exp:graphql:bench
type: generated
generated: exp:help:examples
---
# exp:expgraphqlbench

Misst die Geschwindigkeit der GraphQL-API an Entscheidung 18: zwischengespeichertes GET, 304, zwischengespeichertes POST, eine zwischengespeicherte Antwort über den Kernel und eine nicht zwischengespeicherte Abfrage, je N Anfragen, mit Median, p95 und Maximum.

Skript `bin/php/expgraphqlbench.php`, Code in `kernel/private/classes/commands/expgraphqlbench.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expgraphqlbench --help
./console exp:graphql:bench --help
php bin/php/expgraphqlbench.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Measure the speed of the GraphQL API against decision 18
Sends the requests of each row one after the other to an end point, --requests times each
(after two that warm it up), --runs times, and prints the median, p95 and max of every row:

  get       a cached persisted query, GET ?id=          (answered before the kernel starts)
  304       the same with If-None-Match
  post      a cached persisted query, POST JSON {id}    (answered before the kernel starts)
  kernel    a cached persisted query as a form POST, which starts the kernel (what the first
            request of new credentials costs)
  uncached  the text of --query-file as an ad-hoc POST (never cached)

Every cached answer must come within 33 ms (the hard limit, on every request); the targets are
20 ms, then 13 ms, for the median and the p95. Uncached answers are measured, without a limit.
A time runs from the request being sent to the last byte of the answer (one connection for all,
so the TLS handshake is not in it). An OAuth token or a personal API key is read from the
environment variable
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--url` | erforderlich | der Endpunkt, z. B. https://example.com/api/graphql |
| `--id` | erforderlich | der Name oder SHA-256 der gespeicherten Abfrage, die gemessen wird |
| `--variables` | erforderlich | die Variablen der Abfrage, als JSON |
| `--query-file` | erforderlich | eine .graphql-Datei mit dem Text der Abfrage, für die Zeile ohne Zwischenspeicher |
| `--requests` | erforderlich | Anfragen je Zeile und Durchlauf (Standard 200) |
| `--runs` | erforderlich | wie oft jede Zeile läuft (Standard 3) |
| `--rows` | erforderlich | die Zeilen, die laufen, durch Kommas getrennt: get,304,post,kernel,uncached (Standard alle) |
| `--siteaccess-header` | erforderlich | X-Siteaccess mit diesem Siteaccess senden |
| `--insecure` |  | das TLS-Zertifikat nicht prüfen (ein eigener Testserver) |
| `--format` | erforderlich | text (Standard) oder json |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expgraphqlbench.php`, `kernel/private/classes/commands/expgraphqlbench.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
