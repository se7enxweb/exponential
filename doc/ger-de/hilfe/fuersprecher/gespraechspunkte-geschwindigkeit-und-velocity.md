---
title: "Gesprächspunkte: Geschwindigkeit und Velocity"
audience: advocate
category: advocacy
order: 19
summary: Wie Sie über die Geschwindigkeit von Exponential und Exponential Velocity sprechen, ohne erfundene Benchmarks, mit belegten Punkten zur Engine mit dauerhaften Workern, zu Caches, HTTPS und HTTP/2, den Engines im Vergleich und wie Sie Ihre eigenen Zahlen ehrlich messen und berichten.
keywords: Gesprächspunkte, Geschwindigkeit, Leistung, Velocity, dauerhafte Worker, FrankenPHP, PHP-FPM, HTTP-Cache, Antwort-Cache, Abfrage-Cache, HTTP/2, HTTPS, Benchmark, Messung, talking points, speed, performance, persistent workers, HTTP cache, response cache, query cache, benchmark, measurement
type: help
---
# Gesprächspunkte: Geschwindigkeit und Velocity

Diese Seite ist für Fürsprecher, die gefragt werden: „Ist es schnell?“ Bei der Geschwindigkeit richten erfundene Zahlen
den größten Schaden an. Beschreiben Sie, **wie** Exponential schnell gemacht wird, und wenn Sie eine Zahl nennen, sagen
Sie genau, wie Sie gemessen haben.

## Inhalt

1. [Was Velocity ist](#was-velocity-ist)
2. [Die Punkte](#die-punkte)
3. [Zahlen: messen, nicht zitieren](#zahlen-messen-nicht-zitieren)
4. [Sagen Sie nicht](#sagen-sie-nicht)
5. [Fragen, die Sie hören werden](#fragen-die-sie-hören-werden)

## Was Velocity ist

„Ein reiner PHP-Webserver. Kein nginx, kein Apache, kein php-fpm. Ein Prozess liefert statische Dateien,
PHP-Skripte, WebSocket-Verbindungen und ein Live-Dashboard aus“ (README von `se7enxweb/exponential-velocity`, zitiert
in [Exponential Velocity](../wer-ist-die-exponential-foundation.md#exponential-velocity)). Er hält die Anwendung zwischen
Anfragen geladen und „begann als Fork der Webserver-Engine Qbix“. Er steht unter der MIT-Lizenz.

## Die Punkte

| Sagen Sie | Quelle |
|---|---|
| „Velocity ist die empfohlene Engine für jede Phase, von der Entwicklung bis zum Produktivbetrieb. FrankenPHP ist die produktionsreife Alternative.“ | [Velocity, Warum Sie ihn nutzen würden](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md#wozu-sie-es-verwenden) |
| „Es hält die Anwendung zwischen Anfragen geladen, sodass nicht jede Anfrage bei null beginnt.“ | [Exponential Velocity](../wer-ist-die-exponential-foundation.md#exponential-velocity) |
| „HTTPS vom ersten Start an, mit einem für Sie erzeugten selbstsignierten Zertifikat; HTTP/2 ist eingebaut.“ | [Velocity, Warum Sie ihn nutzen würden](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md#wozu-sie-es-verwenden) |
| „Ganze Seiten können beantwortet werden, bevor der Kernel startet, aus dem rollenbewussten HTTP-Cache und dem Antwort-Cache des Servers.“ | ebenda |
| „Ein SQL-Abfrage-Cache verringert die Datenbankabfragen pro Seite.“ | ebenda |
| „Ein Befehl rollt eine PHP-Änderung aus.“ | [Velocity, Eine PHP-Änderung mit einem Befehl ausrollen](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md#eine-php-änderung-mit-einem-befehl-ausrollen) |
| „Es gibt einen Benchmark-Befehl, sodass Sie Ihre eigene Website messen können.“ | [Benchmarks](../../anleitungen/benchmarking.md) |
| „Velocity ist jung: erste Commits im Juli 2026, erstes Release im September 2026.“ | [Exponential Velocity](../wer-ist-die-exponential-foundation.md#exponential-velocity) |

Den letzten Punkt sollten Sie selbst sagen: Er zeigt, dass Sie ehrlich sind, und er lässt die Menschen entscheiden, wie
sie es einführen.

## Zahlen: messen, nicht zitieren

Wenn Sie über Zahlen sprechen möchten:

1. Messen Sie auf einem Rechner, den Sie kontrollieren, mit der [Anleitung zu Benchmarks](../../anleitungen/benchmarking.md).
2. Schreiben Sie auf: die Hardware, die PHP-Version, die Engine, die Datenbank, welche Caches an oder aus sind, die
   gemessene Seite, das Werkzeug und die Zahl der Anfragen.
3. Veröffentlichen Sie die Methode mit dem Ergebnis und sagen Sie „auf meinem Rechner“.
4. Vergleichen Sie nie mit Zahlen eines anderen Produkts, die anders gemessen wurden
   ([Andere Projekte und Unternehmen respektieren](andere-projekte-respektieren.md)).

```text
MEASUREMENT NOTE                                   Date: <date>
Machine        <CPU, cores, memory>      OS: <distribution>
PHP            <version>                 Engine: <Velocity | FrankenPHP | PHP-FPM + web server>
Database       <engine, version>         Caches: <which on, which off>
Page           <path, logged in or not>
Tool           <name, options>           Requests: <n>, concurrency <n>
Result         <median, 95th percentile>
Caveats        <anything unusual>
```

## Sagen Sie nicht

- „N-mal schneller als X.“ Es sei denn, Sie haben beide selbst auf dieselbe Weise gemessen und veröffentlichen die Methode.
- „Velocity hat sich im großen Maßstab im Produktivbetrieb bewährt.“ Das sagen die Quellen nicht; es ist jung.
- „Sie brauchen keinen Cache.“ Caches gehören dazu, wie es schnell wird.
- „Es läuft auf jedem Hosting.“ Velocity kann auf Shared Hosting meist nicht laufen
  ([Container, Cloud und Hosting-Panels](../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels)).

## Fragen, die Sie hören werden

| Frage | Antwort |
|---|---|
| „Muss ich Velocity nutzen?“ | Nein. FrankenPHP, Apache oder nginx mit PHP-FPM liefern Exponential ebenfalls aus ([Engines im Vergleich](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md#engines-nebeneinander)). |
| „Was ist die Standard-Engine?“ | Der mitgelieferte Standard in `settings/velocity.ini` ist der eingebaute Server von PHP, weil er keine Installation braucht; ändern Sie ihn für jede echte Website ([Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md#wozu-sie-es-verwenden)). |
| „Wo melde ich einen Fehler in Velocity?“ | Im Tracker von Velocity ([Welcher Tracker für welche Meldung](welcher-tracker-fuer-welche-meldung.md)). |
| „Gibt es Grenzen?“ | Ja, dokumentiert in [Grenzen und Vorsichtsmaßnahmen](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md#grenzen-und-vorsichtsmaßnahmen). |

## Verwandte Seiten

- [Gesprächspunkte für Administratoren](gespraechspunkte-fuer-administratoren.md)
- [Aussagen, die Sie nicht machen dürfen](aussagen-die-sie-nicht-machen-duerfen.md)
- [Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
