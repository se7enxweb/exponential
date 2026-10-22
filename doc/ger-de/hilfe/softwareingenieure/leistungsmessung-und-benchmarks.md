---
title: Leistungsmessung und Benchmarks
audience: software-engineer, developer, admin
category: software-engineering
order: 360
summary: Wie Sie die Leistung von Exponential vor und nach einer Änderung messen - exp:benchmark in seinen Modi http, kernel, micro und baseline, der Micro-Benchmark, den die CI bei jedem Push ausführt, die Zeit- und SQL-Zahlen der Debug-Leiste, Profiling, faire A/B-Vergleiche und wie Sie die veröffentlichten Zahlen des Projekts lesen.
keywords: Leistung, Benchmark, exp:benchmark, Micro-Benchmark, Ausgangsbasis, Regression, A/B, Profiling, Debug-Leiste, SQL-Anzahl, Zeitmessung, CI-Leistung, Messung, Softwareingenieur, performance, micro benchmark, baseline, regression, profiling, debug bar, SQL count, timing, measurement
type: help
---
# Leistungsmessung und Benchmarks

Diese Seite richtet sich an Ingenieure, die Zahlen brauchen: um Server zu vergleichen, um zu belegen, dass eine Änderung
nichts langsamer gemacht hat, oder um zu finden, wo eine Seite ihre Zeit verbringt. Exponential liefert einen eigenen
Benchmark-Befehl mit; kein externes Lastwerkzeug ist nötig. Die Referenz ist
[Benchmark: exp:benchmark](../../funktionen/6.0/benchmark.md), und die Schritt-für-Schritt-Anleitung ist
[Benchmarking von Exponential](../../anleitungen/benchmarking.md).

## Inhalt

1. [Die vier Modi](#die-vier-modi)
2. [Standardmäßig sicher](#standardmäßig-sicher)
3. [Leistungsprüfungen in der CI](#leistungsprüfungen-in-der-ci)
4. [Finden, wohin die Zeit geht](#finden-wohin-die-zeit-geht)
5. [Fair vergleichen](#fair-vergleichen)
6. [Veröffentlichte Zahlen lesen](#veröffentlichte-zahlen-lesen)

## Die vier Modi

```bash
./console exp:benchmark --allow-root-user          # http: the front page, three menu pages, the admin login
./console exp:benchmark kernel --allow-root-user   # the parts of a page, in-process
./console exp:benchmark micro --allow-root-user    # hot paths, no web server, no database
./console exp:bench --help                          # exp:bench is the short name
```

| Modus | Misst | Verwenden Sie ihn für |
|---|---|---|
| `http` (Standard) | Anfragen und ihre Zeiten, gegen einen Server oder mehrere nacheinander | Vergleich von Engines, Servern oder Konfigurationen |
| `kernel` | Start, Template-Rendern kalt und warm, INI-Laden, Inhalts-Fetch, Datenbank-Rundreise, Cache schreiben und lesen | finden, welcher Teil einer Seite langsamer wurde |
| `micro` | reine PHP-Hot-Paths (Template-Kompilierung und -Rendern, INI-Parsen, Autoload, Übersetzungen), gegen eine Kalibrierschleife normalisiert | Codeänderungen über Rechner hinweg vergleichen |
| `baseline` | speichert einen Lauf als JSON und vergleicht einen späteren Lauf damit, Exit-Status 1 über einer Schwelle | ein CI-Job oder ein Bereitstellungsskript |

`php bin/php/benchmark.php ...` ist derselbe Befehl ohne die Konsole.

## Standardmäßig sicher

Ein Benchmark ist Last. Die Standards sind höflich (100 Anfragen je URL, 4 gleichzeitig, nur `GET`), und alles, was wie
ein Lasttest des Servers eines anderen aussieht (ein anderer Host, hohe Gleichzeitigkeit, viele Anfragen), braucht
`--force`. Ein Host, den die Website nicht bedient, wird abgelehnt, sofern seine URLs nicht ausdrücklich genannt sind.
`--cold-clear` ist die einzige Option, die schreibt: Sie leert zuerst den View-Cache für Inhalte; `--cold` misst an den
Caches vorbei, ohne etwas zu leeren.

## Leistungsprüfungen in der CI

Der Workflow **Performance** führt den Micro-Benchmark (PHP 8.5) bei einem Push oder Pull-Request aus, der PHP, Templates,
`settings/` oder den Benchmark selbst ändert, gegen `tests/benchmark/baseline.json`. Eine Messung über der Schwelle wird
noch einmal gemessen, und nur der zweite Lauf zählt. Es ist eine Warnung, keine blockierende Prüfung. Reproduzieren Sie
sie mit:

```bash
php bin/php/benchmark.php micro --baseline=tests/benchmark/baseline.json --all-checks
```

mit ausgeschaltetem OPcache für die Kommandozeile ([Continuous Integration](../../bc/6.0/ci.md)).

## Finden, wohin die Zeit geht

- **Die Debug-Leiste** zeigt Seitenzeit, SQL-Anzahl und -Zeit, Spitzenspeicher und verwendete Templates, mit Reitern für
  Zeitmessung, SQL, Templates und Speicher ([Die Exp-Debug-Leiste](../../funktionen/6.0/exp-debug-leiste.md)). Schalten Sie
  sie nur für Ihre eigene Adresse ein.
- **Der Modus kernel** des Benchmarks zerlegt eine Seite in ihre Teile.
- **Velocity** hat einen Bericht zum Opcode-Cache und ein Profil
  ([Opcode-Cache und Profil von Velocity](../../funktionen/6.0/velocity-opcode-cache-und-profil.md)).
- Hinweise zum Profiling von Anwendungscode: [Profiling und Leistung](../ersteller/anwendungsentwickler/profiling-und-leistung.md).

## Fair vergleichen

- Vergleichen Sie A und B direkt nacheinander, auf demselben Rechner, mit denselben warmen oder kalten Caches.
- Verwenden Sie Mediane über viele Anfragen, keine einzelnen Anfragen oder Bestwerte.
- Ändern Sie jeweils eine Sache: die Engine, eine Einstellung oder den Code.
- Notieren Sie PHP-Version, Engine, Datenbank und welche Caches an waren.
- Beurteilen Sie den Speicher von Velocity nach PSS, nie nach RSS.

## Veröffentlichte Zahlen lesen

Das README dokumentiert Zahlen für den Velocity-Stack (zum Beispiel einen Median je zwischengespeicherter Seite und
Anfragen je Sekunde auf einem Rechner mit 12 Kernen) und sagt, dass Methodik und Skripte mit dem Projekt ausgeliefert
werden. Behandeln Sie jede veröffentlichte Zahl als Messung auf dieser Hardware, mit dieser Seite und diesen Caches, und
wiederholen Sie sie auf Ihrer, bevor Sie damit planen.

## Verwandte Seiten

- [Caching-Ebenen für Ingenieure](caching-ebenen-fuer-ingenieure.md)
- [Benchmarking einer gehosteten Website](../hosting/leistung/benchmark-einer-gehosteten-website.md)
- [HTTP/2 und Vorwärmen von Caches](../../bc/6.0/http2-und-cache-vorwaermen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
