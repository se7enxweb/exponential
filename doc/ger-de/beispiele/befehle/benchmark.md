---
title: exp:benchmark
audience: developer, admin
category: examples
summary: "Misst diese Installation: Seitenzeiten über HTTP, A/B zwischen Servern, Kernel-Messungen, Regressionsprüfung."
keywords: Konsole, Befehl, exp:benchmark, benchmark, console, command, CLI, benchmark.php, exp:bench
type: generated
generated: exp:help:examples
---
# exp:benchmark

Misst diese Installation: Seitenzeiten über HTTP, A/B zwischen Servern, Kernel-Messungen, Regressionsprüfung.

Skript `bin/php/benchmark.php`, Code in `kernel/private/classes/commands/benchmark.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:benchmark --help
./console exp:bench --help
php bin/php/benchmark.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--base` | erforderlich | Der zu messende Server (Standard: https:// und die SiteURL des Siteaccess) |
| `--compare` | erforderlich | Zwei oder mehr Server, durch Kommas getrennt, direkt nacheinander gemessen (A/B) |
| `--url` | erforderlich | Eine zu messende URL oder ein Pfad; für weitere wiederholen |
| `--urls-file` | erforderlich | Eine Datei mit einer URL oder einem Pfad pro Zeile (# beginnt einen Kommentar) |
| `--pages` | erforderlich | Wie viele Seiten des Menüs die Standardliste nimmt (Standard 3) |
| `--no-admin` |  | Die Anmeldeseite der Administration aus der Standardliste weglassen |
| `--no-search` |  | Die Suchseite aus der Standardliste weglassen |
| `--search` | erforderlich | Die Wörter, nach denen die Standard-Suchseite sucht (Standard: das erste Wort der ersten Menüseite) |
| `--requests` | erforderlich | Anfragen pro URL und Server (Standard 100) |
| `--runs` | erforderlich | Dasselbe wie --requests: gemessene Läufe pro URL und Server |
| `--concurrency` | erforderlich | Gleichzeitige Anfragen (Standard 4) |
| `--warmup` | erforderlich | Zuerst ungemessene Läufe: Anfragen pro URL und Server (Standard 5, mindestens eine pro Verbindung), micro: Iterationen pro Messung (Standard 3) |
| `--rounds` | erforderlich | Die Anfragen in Runden aufteilen, mit wechselnder Reihenfolge der Server (Standard 2) |
| `--micro` |  | Dasselbe wie der Modus micro |
| `--format` | erforderlich | Ausgabe: table (Standard), json oder csv |
| `--cold` |  | Bei jeder Anfrage eine eindeutige Query-Zeichenkette, damit kein Antwort-Cache antwortet |
| `--cold-clear` |  | Vor dem Lauf einmal den View-Cache der Inhalte leeren (nur diese Installation) |
| `--insecure` |  | TLS-Zertifikate nicht prüfen (selbstsignierte Server) |
| `--auth` | erforderlich | HTTP-Basic-Auth, benutzer:passwort (oder die Variable EXP_BENCHMARK_AUTH) |
| `--header` | erforderlich | Ein zusätzlicher Request-Header, "Name: Wert"; für weitere wiederholen |
| `--cookie` | erforderlich | Ein zu sendender Cookie-Header, z. B. um eine Seite im angemeldeten Zustand zu messen |
| `--resolve` | erforderlich | host:port:adresse, wie curl --resolve, um einen Host unter einer anderen Adresse zu erreichen |
| `--http` | erforderlich | HTTP-Version: 1.1 oder 2 (Standard: was curl aushandelt) |
| `--encoding` | erforderlich | Das zu sendende Accept-Encoding (Standard gzip; "none" für keines) |
| `--timeout` | erforderlich | Sekunden pro Anfrage (Standard 30) |
| `--no-keepalive` |  | Für jede Anfrage eine neue Verbindung |
| `--tool` | erforderlich | curl (Standard, eingebaut), ab, wrk oder oha, wenn installiert |
| `--duration` | erforderlich | Sekunden pro URL für wrk (Standard 10) |
| `--repeat` | erforderlich | kernel: Läufe jeder Messung (Standard 20); micro: gemessene Iterationen jeder Messung (Standard 30) |
| `--boot-repeat` | erforderlich | kernel: neue Prozesse für den Start und das kalte Rendern (Standard 5) |
| `--node` | erforderlich | kernel: der zu rendernde und abzurufende Knoten (Standard: die Startseite) |
| `--probe` | erforderlich | kernel, micro: nur diese Messungen; für weitere wiederholen |
| `--json` |  | Den Lauf als JSON ausgeben |
| `--csv` |  | Die Zeilen als CSV ausgeben |
| `--save` | erforderlich | Den Lauf als JSON in diese Datei schreiben |
| `--baseline` | erforderlich | Mit diesem gespeicherten Lauf vergleichen; Exit-Code 1, wenn sich ein Messwert verschlechtert hat |
| `--threshold` | erforderlich | Prozent, um die sich ein Messwert verschlechtern darf, bevor es als Regression zählt (Standard 15; micro: 40, nach Normalisierung) |
| `--min-delta` | erforderlich | Millisekunden, um die eine Latenz außerdem wachsen muss, damit es zählt (Standard 1) |
| `--all-checks` |  | Jeden Vergleich anzeigen, nicht nur die Regressionen |
| `--force` |  | Mehr als die rücksichtsvollen Grenzen gegen einen Host erlauben, der nicht Loopback ist |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[MODE*]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/benchmark.php`, `kernel/private/classes/commands/benchmark.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
