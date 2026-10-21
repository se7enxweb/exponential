---
title: Anwendungen skalieren
audience: app-creator, developer, admin
category: app-creation
order: 600
summary: Eine Exponential-Anwendung von einem Server auf viele erweitern - zuerst messen, Caches und Velocity-Worker, die Datenbank, Hintergrundarbeit, Suche mit Solr, gemeinsamer Speicher und Clustering und was jeder Schritt kostet.
keywords: Skalierung, Leistung, Velocity-Worker, Caching, Datenbank, Leselast, Hintergrund-Jobs, Cluster, Lastverteiler, Anwendungsentwickler, scaling, performance, Velocity workers, caching, database, read load, background jobs, Solr, cluster, DFS, load balancer, Valkey, Redis, app creator
type: help
---
# Anwendungen skalieren

Diese Seite richtet sich an Anwendungsentwickler, deren Anwendung mehr Benutzer oder mehr Daten bedienen muss. Skalieren
Sie in Schritten und messen Sie vor jedem einzelnen: Die meisten Websites brauchen nie mehr als die ersten drei.

## Inhalt

1. [Zuerst messen](#zuerst-messen)
2. [Die Schritte der Reihe nach](#die-schritte-der-reihe-nach)
3. [Mehrere Webserver](#mehrere-webserver)
4. [Ein Anwendungsdesign, das skaliert](#ein-anwendungsdesign-das-skaliert)

## Zuerst messen

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
php bin/php/console exp:benchmark kernel --repeat=30 --allow-root-user
```

`exp:benchmark` liefert Median, p90 und p99 je Seite; der Modus `kernel` misst Start, Rendern, Laden der INI, Abrufe und
Datenbank-Roundtrips ([Benchmarking](../../../anleitungen/benchmarking.md), [Profiling und Leistung](profiling-und-leistung.md)).

## Die Schritte der Reihe nach

| Schritt | Wirkung | Lesen |
|---|---|---|
| 1. Caches an und mit den richtigen Cache-Schlüsseln | die meisten Anfragen werden nie gerendert | [Caching in Anwendungen](caching-in-anwendungen.md) |
| 2. Velocity mit seinem Antwort-Cache | wiederholte anonyme Anfragen werden ohne PHP beantwortet; persistente Worker halten die Anwendung warm | [Antwort-Cache von Velocity](../../../../eng-us/features/6.0/velocity-response-cache.md) |
| 3. Langsame Arbeit aus den Anfragen heraus | Anfragen bleiben unter Last schnell | [Muster für Hintergrundarbeit](muster-fuer-hintergrundarbeit.md) |
| 4. Die Datenbank abgestimmt; Abfrage-Cache | weniger und günstigere Abfragen | [Datenbanken](../../../installation/09-datenbanken.md), [SQL-Abfrage-Cache](../../../bc/6.0/sql-abfrage-cache.md) |
| 5. Suche mit Solr | Suchlast weg von der Datenbank | [Suche mit ezfind und Solr](../website-ersteller/suche-mit-ezfind-und-solr.md) |
| 6. Gemeinsames Cache-Backend | Inhalts- und INI-Caches in Redis oder Valkey | [sevenx_valkey_cache](../../../../eng-us/features/6.0/extensions/sevenx_valkey_cache.md) |
| 7. Mehrere Webserver | horizontale Kapazität | [Clustering und gemeinsamer Speicher](clustering-und-gemeinsamer-speicher.md) |

Der Worker-Pool von Velocity und seine dynamische Größe werden in `velocity.ini` eingestellt
([8.3.6 Worker](../../../installation/08-die-website-ausliefern.md#836-worker-pool-größe-der-dynamische-pool-und-die-zygote)).

## Mehrere Webserver

Mehrere Webserver brauchen: eine Datenbank (oder einen Cluster davon), gemeinsamen Dateispeicher über den
Cluster-Dateihandler, denselben Code und dieselben Einstellungen auf jedem Server, Sitzungen, die jeder Server lesen kann
(der Datenbank-Sitzungshandler), einen Lastverteiler, der in `TrustedProxies[]` eingetragen ist, und Cron- und
Warteschlangen-Worker auf einem dafür bestimmten Server (oder mit Sperre).

## Ein Anwendungsdesign, das skaliert

- Jede Liste seitenweise und begrenzt; Zählungen mit Zählabfragen.
- Keine Arbeit pro Anfrage, die mit der Größe der Website wächst (Abrufe des ganzen Baums im Pagelayout).
- Schreibvorgänge gebündelt, Transaktionen kurz.
- Idempotente Hintergrund-Jobs, damit mehr Worker keine Duplikate bedeuten.
- Cache-Schlüssel aus dem gebildet, was sich ändert, Caches und nach Möglichkeit durch Ereignisse statt nach Zeit geleert.

## Verwandte Seiten

- [Leistung und Caching](../website-ersteller/leistung-und-caching.md)
- [Clustering und gemeinsamer Speicher](clustering-und-gemeinsamer-speicher.md)
- [10.12 Leistungsoptimierung](../../../installation/10-nach-der-installation.md#1012-performance-tuning)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
