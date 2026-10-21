---
title: "Modul 7: Leistung und Skalierung"
audience: trainer, hosting, admin
category: training
order: 70
summary: Das siebte Modul von Hosting und Betrieb, eine Website messen, Cache-Schichten mit HTTP-Headern, Proxys und CDN, ein Valkey- oder Redis-Cache und mehrere Webknoten mit Clustering. Vier Lektionen, 2 Stunden 55 Minuten.
keywords: hosting und betrieb, modul 7, leistung, skalierung, benchmark, exp:benchmark, cache-schichten, HTTP-cache-header, CDN, Varnish, Valkey, Redis, cluster, mehrere webknoten, hosting and operations, module 7, performance, scaling, caching layers, several web nodes
type: help
---
# Modul 7: Leistung und Skalierung

Leistungsarbeit beginnt mit einer Zahl und endet mit einer Entscheidung. Dieses Modul bringt den Teilnehmenden bei,
eine Website mit `exp:benchmark` zu messen, jede Cache-Schicht zu kennen und gemeinsam genutzte Caches sicher zu
halten, einen Valkey- oder Redis-Cache hinzuzufügen und eine Website für mehrere Webknoten zu entwerfen, wenn ein
Rechner nicht mehr genügt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Dauer | 2 Stunden 55 Minuten (vier Lektionen) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene bis Experten |
| Form | Praxisorientiert (7.1 bis 7.3) und eine Entwurfsübung (7.4) |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [7.1 Eine Website messen](benchmarking.md) | 45 Min. | Warme und kalte Benchmarks ausführen, eine Baseline speichern, den Rückgabewert lesen |
| [7.2 Cache-Schichten, HTTP-Header, Proxys und CDN](cache-schichten-proxys-und-cdn.md) | 45 Min. | Cache-Header lesen, cachebare Header für anonyme Besucher einschalten |
| [7.3 Ein Valkey- oder Redis-Cache](valkey-cache.md) | 40 Min. | Die Erweiterung installieren, Schlüssel mit einem eindeutigen Präfix prüfen |
| [7.4 Mehrere Webknoten und Clustering](mehrere-webknoten-und-clustering.md) | 45 Min. | Eine Website mit zwei Knoten und ihren Betrieb entwerfen |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person, ausgeliefert von Velocity, auf einem Rechner, der ruhig genug für Benchmarks
  ist; vereinbaren Sie einen kleinen Wert für `--requests`, wenn Rechner gemeinsam genutzt werden.
- Für Lektion 7.3: ein Valkey- oder Redis-Server und die PHP-Erweiterung `redis` auf der virtuellen Maschine jeder
  Person, Zugang zu Composer und ein Schlüsselpräfix pro Person. Ohne diese führen Sie es auf Ihrem eigenen Rechner vor.
- Papier oder ein gemeinsames Dokument für die Entwurfsübung aus Lektion 7.4.
- Proben Sie die Header-Änderung aus Lektion 7.2 und ihre Entfernung; hinterlassen Sie kein Override.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 | Lektion 7.1 (45 Min.) |
| 0:45 | Lektion 7.2 (45 Min.) |
| 1:30 | Pause (15 Min.) |
| 1:45 | Lektion 7.3 (40 Min.) |
| 2:25 | Lektion 7.4 (45 Min.) |
| 3:10 | Ende (2 Std. 55 Min. Lektionen plus eine Pause) |

## Verwandte Seiten

- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Leistung](../../../hosting/leistung/README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
