---
title: exp:benchmark
audience: developer, admin
category: examples
summary: "Measure this installation: page timings over HTTP, A/B between servers, kernel probes, regression check."
keywords: Konsole, Befehl, exp:benchmark, benchmark, console, command, CLI, benchmark.php, exp:bench
type: generated
generated: exp:help:examples
---
# exp:benchmark

Measure this installation: page timings over HTTP, A/B between servers, kernel probes, regression check.

Skript `bin/php/benchmark.php`, Code in `kernel/private/classes/commands/benchmark.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:benchmark --help
./console exp:bench --help
php bin/php/benchmark.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--base` | erforderlich | Server to measure (default: https:// and the SiteURL of the siteaccess) |
| `--compare` | erforderlich | Two or more servers, comma separated, measured back to back (A/B) |
| `--url:*` |  |  |
| `--urls-file` | erforderlich | A file with one URL or path per line (# starts a comment) |
| `--pages` | erforderlich | How many pages of the menu the default list takes (default 3) |
| `--no-admin` |  | Leave the admin login page out of the default list |
| `--no-search` |  | Leave the search page out of the default list |
| `--search` | erforderlich | The words the default search page searches for (default: the first word of the first menu page) |
| `--requests` | erforderlich | Requests per URL and server (default 100) |
| `--runs` | erforderlich | The same as --requests: measured runs per URL and server |
| `--concurrency` | erforderlich | Requests at the same time (default 4) |
| `--warmup` | erforderlich | Unmeasured runs first: requests per URL and server (default 5, at least one per connection), micro: iterations per probe (default 3) |
| `--rounds` | erforderlich | Split the requests into rounds, alternating the server order (default 2) |
| `--micro` |  | The same as the micro mode |
| `--format` | erforderlich | Output: table (default), json or csv |
| `--cold` |  | A unique query string on every request, so no response cache answers |
| `--cold-clear` |  | Clear the content view cache once before the run (this installation only) |
| `--insecure` |  | Do not verify TLS certificates (self-signed servers) |
| `--auth` | erforderlich | HTTP basic auth, user:password (or the EXP_BENCHMARK_AUTH variable) |
| `--header:*` |  |  |
| `--cookie` | erforderlich | A Cookie header to send, e.g. to measure a signed-in page |
| `--resolve:*` |  |  |
| `--http` | erforderlich | HTTP version: 1.1 or 2 (default: what curl negotiates) |
| `--encoding` | erforderlich | The Accept-Encoding to send (default gzip; "none" for none) |
| `--timeout` | erforderlich | Seconds per request (default 30) |
| `--no-keepalive` |  | A new connection for every request |
| `--tool` | erforderlich | curl (default, built in), ab, wrk or oha when installed |
| `--duration` | erforderlich | Seconds per URL for wrk (default 10) |
| `--repeat` | erforderlich | kernel: runs of every probe (default 20); micro: measured iterations of every probe (default 30) |
| `--boot-repeat` | erforderlich | kernel: new processes for boot and the cold render (default 5) |
| `--node` | erforderlich | kernel: the node to render and fetch (default: the front page) |
| `--probe:*` |  |  |
| `--json` |  | Print the run as JSON |
| `--csv` |  | Print the rows as CSV |
| `--save` | erforderlich | Write the run as JSON to this file |
| `--baseline` | erforderlich | Compare with this saved run; exit 1 when a metric regressed |
| `--threshold` | erforderlich | Percent a metric may get worse before it counts as a regression (default 15; micro: 40, after normalisation) |
| `--min-delta` | erforderlich | Milliseconds a latency must grow by, too, to count (default 1) |
| `--all-checks` |  | Show every comparison, not only the regressions |
| `--force` |  | Allow more than the polite limits against a host that is not loopback |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[MODE*]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/benchmark.php`, `kernel/private/classes/commands/benchmark.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
