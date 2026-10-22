---
title: PHP-Code profilieren
audience: php-developer, developer
category: php-development
order: 400
summary: PHP-Code in Exponential messen und beschleunigen - exp:benchmark für Seiten, Kernel-Messpunkte und Mikro-Benchmarks, eZDebug-Akkumulatoren um eigenen Code, die SQL- und Speicherwerte der Debug-Leiste, das OPcache-Profil, der SQL-Abfrage-Cache und die Ursachen langsamen Codes, die immer wiederkehren.
keywords: Profiling, Performance, Mikro-Benchmark, Baseline, Akkumulatoren, langsame Abfragen, Speicher, profiling, performance, exp:benchmark, benchmark.php, kernel probes, micro benchmark, baseline, accumulators, SQL count, debug bar, OPcache profile, opcache_profile.on, SQL query cache, querycache.ini, Xdebug, slow queries, memory
type: help
---
# PHP-Code profilieren

Diese Seite richtet sich an PHP-Entwickler, die Code schneller machen oder nachweisen müssen, dass eine Änderung ihn
nicht langsamer gemacht hat. Die Methode ist immer dieselbe: messen, eine Sache ändern, erneut messen, unter denselben
Bedingungen. Die Anwendungssicht ist [Profiling und Performance](../ersteller/anwendungsentwickler/profiling-und-leistung.md);
der vollständige Leitfaden ist [Exponential benchmarken](../../anleitungen/benchmarking.md).

## Inhalt

1. [Drei Messebenen](#drei-messebenen)
2. [Seiten: exp:benchmark](#seiten-expbenchmark)
3. [Der Kernel und Mikro-Benchmarks](#der-kernel-und-mikro-benchmarks)
4. [Innerhalb einer Anfrage](#innerhalb-einer-anfrage)
5. [OPcache, unter jeder Engine](#opcache-unter-jeder-engine)
6. [Der SQL-Abfrage-Cache](#der-sql-abfrage-cache)
7. [Wiederkehrende Ursachen](#wiederkehrende-ursachen)
8. [Fair messen](#fair-messen)

## Drei Messebenen

| Ebene | Werkzeug | Beantwortet |
|---|---|---|
| ganze Seiten über HTTP | `exp:benchmark` | ist die Site schneller oder langsamer, und um wie viel |
| Kernel-Operationen im Prozess | `exp:benchmark kernel` | welcher Teil einer Anfrage kostet (Boot, Rendern, INI, Fetch, Datenbank, Cache) |
| Ihr Code in einer Anfrage | Akkumulatoren, die Debug-Leiste | wo in Ihrem Code die Zeit verbraucht wird |

## Seiten: exp:benchmark

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json
# change something
php bin/php/console exp:benchmark --baseline=var/benchmark/base.json --threshold=15
php bin/php/console exp:benchmark --cold --requests=50                 # past the response caches
php bin/php/console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080
```

Es fragt eine Reihe von Seiten nach einem Warm-up viele Male ab und gibt Median, p90, p99 und mehr aus. `--baseline`
mit `--threshold` macht daraus eine Regressionsprüfung, die fehlschlägt, wenn eine Seite um mehr als den Schwellenwert
langsamer wurde ([Benchmark](../../funktionen/6.0/benchmark.md)).

## Der Kernel und Mikro-Benchmarks

```bash
php bin/php/console exp:benchmark kernel --repeat=30 --save=var/benchmark/kernel.json
php bin/php/benchmark.php micro --baseline=tests/benchmark/baseline.json --all-checks
```

Die Kernel-Messpunkte messen im Prozess Boot, Rendern (kalt und warm), Laden der INI, einen Inhalts-Fetch, einen
Datenbank-Roundtrip sowie Schreib- und Lesezugriffe auf den Cache. Der Mikro-Benchmark misst reine PHP-Hot-Paths ohne
HTTP oder Datenbank; der Performance-Workflow der CI führt ihn unter PHP 8.5 mit ausgeschaltetem OPcache gegen
`tests/benchmark/baseline.json` aus, als Warnung.

## Innerhalb einer Anfrage

Umschließen Sie den verdächtigen Code mit einem Akkumulator:

```php
eZDebug::accumulatorStart( 'acmereport_totals', 'acmereport_total', 'Monthly totals' );
$totals = acmeReportService::monthlyTotals( $month );
eZDebug::accumulatorStop( 'acmereport_totals' );
```

Mit eingeschalteter Debug-Ausgabe zeigt die Leiste:

- **Zeiten**: Ihre Akkumulatoren mit Gesamtzeit und Anzahl der Aufrufe, neben denen des Kernels;
- **SQL**: die Anzahl der Anweisungen, ihre Zeit und ihren Anteil an der Seite, die langsamsten zuerst (`SQLOutput=enabled`);
- **Speicher**: die Spitze und die Schritte, die am meisten verbraucht haben;
- **Templates**: wie viele Templates verwendet wurden.

Die Zusammenfassung färbt Werte anhand der Schwellenwerte in `debugbar.ini` gelb oder rot, wodurch ein neuer Hot Spot
auf einen Blick sichtbar wird ([Die Debug-Leiste für PHP-Entwickler](die-debug-leiste-fuer-php-entwickler.md)).

## OPcache, unter jeder Engine

Der Kernel kann pro Anfrage eine Zeile schreiben, die angibt, wie die Anfrage OPcache genutzt hat: Treffer und
Fehlschläge, die am häufigsten ausgelieferten Skripte und eingebundene Dateien, die OPcache nicht hält (jede davon wird
bei jedem Einbinden erneut kompiliert). Das ist ausgeschaltet, sofern die Datei `var/tmp/opcache_profile.on` nicht
existiert; die Zeilen gehen nach `var/tmp/opcache_profile.log` (`ezpKernelWeb::writeOPcacheProfile()`). Nutzen Sie es,
um Engines zu vergleichen; entfernen Sie die Schalterdatei danach
([Velocity und der Opcode-Cache](../../funktionen/6.0/velocity-opcode-cache-und-profil.md)).

## Der SQL-Abfrage-Cache

`settings/querycache.ini` (standardmäßig `Mode=off`) speichert die Ergebnisse wiederholter Leseabfragen
zwischen (standardmäßig in APCu, wo der Prozess es hat, sonst in Dateien). Messen Sie mit ein- und ausgeschaltetem
Cache: Eine Seite, die dieselbe Abfrage viele Male stellt, gewinnt, eine Seite mit eindeutigen Abfragen nicht. Er gilt
nicht für MongoDB ([SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md)).

## Wiederkehrende Ursachen

| Symptom | Übliche Ursache | Lösung |
|---|---|---|
| viele Abfragen pro Seite | Fetches in Schleifen, `dataMap()` bei jedem Element einer Liste | einmal abrufen; begrenzen; ein Cache-Block |
| eine Liste wird mit den Daten langsamer | kein Limit oder eine Zählung von allem | seitenweise; begrenzte Zählungen |
| der Speicher wächst in einem Befehl | Objekte bleiben in den In-Memory-Caches des Kernels | `eZContentObject::clearCache()` zwischen den Seiten |
| ein langsames Veröffentlichen | schwere Arbeit in einem Workflow-Ereignis oder Listener | eine Warteschlange oder ein Content-Job |
| langsam nach dem Deployment | Caches geleert | wärmen Sie sie vor ([OPcache und Cache-Vorladen](opcache-und-cache-vorladen.md)) |
| nach einer Änderung nur unter Velocity langsam | Worker nicht neu gestartet oder OPcache validiert nicht neu | `exp:velocity deploy` |

## Fair messen

- Dieselbe Maschine, nichts anderes läuft, OPcache wie in der Produktion, Xdebug aus.
- Vergleichen Sie zwei Server mit abwechselnden Anfragen, nicht indem Sie einen nach dem anderen messen.
- Halten Sie bei jedem Lauf die Umgebung fest; ein Vergleich mit einer Baseline aus einem anderen Aufbau sagt wenig.
- Bewahren Sie die Läufe auf: Committen Sie eine Baseline-Datei für Ihre Erweiterung und vergleichen Sie vor einem
  Release damit.

## Verwandte Seiten

- [Exponential benchmarken](../../anleitungen/benchmarking.md)
- [Performance in Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/leistung-in-erweiterungen.md)
- [Caching in Anwendungen](../ersteller/anwendungsentwickler/caching-in-anwendungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
