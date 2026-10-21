---
title: Profiling und Leistung
audience: app-creator, developer
category: app-creation
order: 630
summary: Herausfinden, wo eine Anwendung ihre Zeit verbringt - exp:benchmark im HTTP-, Kernel- und Mikromodus, der Vergleich mit einer Baseline, Zeiten und Abfragen in der Debug-Leiste, OPcache und Velocity sowie die üblichen Ursachen langsamer Seiten.
keywords: Profiling, Leistung, Performance, Kernel-Modus, Mikromodus, Baseline, Debug-Leiste, SQL-Abfragen, langsame Seiten, Anwendungsentwickler, profiling, performance, exp:benchmark, kernel mode, micro mode, baseline, debug bar, SQL queries, OPcache, Velocity, slow pages, app creator
type: help
---
# Profiling und Leistung

Diese Seite richtet sich an Anwendungsentwickler, die Code schneller machen. Messen, eine Sache ändern, erneut messen
([Benchmarking](../../../anleitungen/benchmarking.md), [Benchmark-Referenz](../../../../eng-us/features/6.0/benchmark.md)).

## Inhalt

1. [Seiten messen](#seiten-messen)
2. [Innerhalb einer Anfrage](#innerhalb-einer-anfrage)
3. [Fair vergleichen](#fair-vergleichen)
4. [Die üblichen Ursachen](#die-üblichen-ursachen)

## Seiten messen

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
# change something
php bin/php/console exp:benchmark --baseline=var/benchmark/base.json --threshold=15 --allow-root-user
php bin/php/console exp:benchmark --cold --requests=50 --allow-root-user   # past the response caches
```

Der Befehl ruft die Startseite, Menüseiten, die Suche und die Anmeldung der Administration nach dem Aufwärmen jeweils
100-mal ab und gibt Median, p90, p99 und weitere Werte aus.

## Innerhalb einer Anfrage

- `exp:benchmark kernel --repeat=30` misst im selben Prozess den Start, das Rendern (kalt und warm), das Laden der INI,
  einen Inhaltsabruf, einen Datenbank-Roundtrip sowie Schreib- und Lesevorgänge im Cache.
- `exp:benchmark micro` misst reine PHP-Hotpaths ohne HTTP und ohne Datenbank.
- Die Debug-Leiste zeigt für jede Anfrage die SQL-Abfragen, Templates und Zeiten ([Exp-Debug-Leiste](../../../../eng-us/features/6.0/exp-debug-bar.md)).
- Unter Velocity zeigt das Profil des Opcode-Caches, warum eine Seite so viel kostet, wie sie kostet
  ([Velocity und der Opcode-Cache](../../../../eng-us/features/6.0/velocity-opcode-cache-and-profile.md)).

## Fair vergleichen

Dieselbe Maschine, nichts anderes läuft, OPcache wie in der Produktion, Xdebug aus, und beurteilen Sie zwei Server nur
anhand von Läufen, die direkt nacheinander gemacht wurden. Jeder Lauf zeichnet seine Umgebung auf, und ein Vergleich mit
einer Baseline aus einer anderen Umgebung weist darauf hin.

## Die üblichen Ursachen

| Symptom | Ursache | Abhilfe |
|---|---|---|
| Viele Abfragen pro Seite | Abrufe in Schleifen, `load_data_map` bei Listen | einmal abrufen, begrenzen, Cache-Block |
| Langsamer erster Aufruf nach dem Deployment | Caches geleert | aufwärmen (`php bin/php/warm.php`) |
| Langsam unter Velocity nach einer Änderung | Worker nicht neu gestartet oder OPcache prüft nicht neu | `exp:velocity deploy` |
| Langsames Veröffentlichen | aufwendige Arbeit in Workflows oder Listenern | in eine Warteschlange verlagern |
| Langsame Listen in der Administration | unbegrenzte Zählungen | seitenweise Abfragen |

## Verwandte Seiten

- [Anwendungen skalieren](anwendungen-skalieren.md)
- [Caching in Anwendungen](caching-in-anwendungen.md)
- [Leistung und Caching](../../../../eng-us/help/creators/website-creators/performance-and-caching.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
