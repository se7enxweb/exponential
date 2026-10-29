---
title: Velocity-Worker und Pools
audience: hosting, admin
category: hosting
order: 90
summary: Wie Sie den Worker-Pool von Velocity auf einem Hosting-Server bemessen und betreiben - feste und dynamische Pools, Instanzen auf einem Port, die Zygote, das Ersetzen von Workern, persistente Worker, Grenzen für Streams, Speicher als PSS gemessen und wie Sie die richtige Zahl durch Messen finden.
keywords: Velocity, Worker, Worker-Pool, Workers, SpareWorkers, IdleWorkerTimeout, Instances, SO_REUSEPORT, Zygote, ForkPerRequest, KeepGlobals, StatTtl, maxRequests, requestTimeout, workerMemoryCeiling, maxConnections, PSS, RSS, Bemessung, MaxStreams, workers, worker pool, zygote, sizing
type: help
---
# Velocity-Worker und Pools

Diese Seite ist für Systemadministratoren bei Hosting-Unternehmen, die Velocity für Kunden-Websites bemessen und wissen müssen, was ein
Worker kostet. Sie beantwortet: welche Einstellungen den Pool formen, wie Speicher ehrlich gemessen wird, wann Worker ersetzt werden, wo
die Grenzen liegen und wie Sie die richtige Zahl für einen Tarif ohne Raten finden. Die Quellen sind
[Kapitel 8, Abschnitt 8.3.6](../../../installation/08-die-website-ausliefern.md#836-worker-pool-größe-der-dynamische-pool-und-die-zygote) und die
[Spezifikation des Worker-Pools](../../../spezifikationen/6.0/velocity-worker-pool.md).

## Inhalt

1. [Das Modell in Kürze](#das-modell-in-kürze)
2. [Die Einstellungen](#die-einstellungen)
3. [Was ein Worker kostet](#was-ein-worker-kostet)
4. [Fester oder dynamischer Pool](#fester-oder-dynamischer-pool)
5. [Instanzen: mehrere Server auf einem Port](#instanzen-mehrere-server-auf-einem-port)
6. [Wann ein Worker ersetzt wird](#wann-ein-worker-ersetzt-wird)
7. [Persistente Worker](#persistente-worker)
8. [Streams halten Worker](#streams-halten-worker)
9. [Die Zahl für einen Tarif finden](#die-zahl-für-einen-tarif-finden)
10. [Checkliste](#checkliste)

## Das Modell in Kürze

- Der Elternprozess lädt die Anwendung einmal und forkt Worker; jeder Worker bedient viele Anfragen und wird nach jeder zurückgesetzt.
- Statische Dateien, Treffer im Antwort-Cache und die eigenen Seiten des Servers beantwortet der Elternprozess, kein Worker.
- Wenn jeder Worker beschäftigt ist, wartet eine Anfrage im Elternprozess. Sie wird erst abgewiesen, wenn `maxConnections` (1024) erreicht
  ist; dann antwortet der Server mit `503`.
- Worker laufen nie als root, es sei denn, Sie erlauben es.

## Die Einstellungen

In `settings/velocity.ini`, `[ServerSettings]` (überschreiben in `settings/override/velocity.ini.append.php`, dann
`exp:velocity restart`):

| Einstellung | Ausgeliefert | Bedeutung |
|---|---|---|
| `Workers` | `4` | die Größe des Pools; mit gesetztem `SpareWorkers` das Maximum, bis zu dem er wächst. Die Datei sagt dazu: immer ausdrücklich setzen |
| `SpareWorkers` | `0` | `0` ist ein fester Pool; über 0 ein dynamischer Pool, der so viele untätige Worker vorhält |
| `IdleWorkerTimeout` | `60` | Sekunden, die ein zusätzlicher Worker untätig bleiben darf, bevor er stillgelegt wird |
| `Instances` | `1` | Server, die sich mit `SO_REUSEPORT` dieselben Ports teilen (Linux und die BSDs); `Workers` und `SpareWorkers` gelten pro Instanz |
| `Zygote` | leer (Standard der Engine: an) | neue Worker aus einem Prozess forken, der nie eine Client-Verbindung hielt |
| `StatTtl` | `0` | Sekunden (höchstens 10), die ein Worker Dateiinformationen über Anfragen hinweg behalten darf |
| `ForkPerRequest` | `disabled` | persistente Worker; `enabled` gibt eine Anfrage pro Worker |
| `Nice` | `5` | die Priorität des Servers und seiner Worker |

Engine-Einstellungen unter `Q.webserver` (über ein Snippet im Konfigurationsbaum gesetzt, siehe
[Velocity für Hoster](velocity-fuer-hoster.md#einstellungen-und-wie-sie-sie-ändern)) umfassen `maxRequests` (1000), `requestTimeout` (30 s),
`workerMemoryCeiling` (256 MB oder drei Viertel von `memory_limit`, wenn das kleiner ist) und `maxConnections` (1024).

## Was ein Worker kostet

Gemessen am 26.09.2026 als PSS aus `/proc/<pid>/smaps_rollup`, Linux mit 12 Kernen, PHP 8.5
([Spezifikation des Worker-Pools](../../../spezifikationen/6.0/velocity-worker-pool.md#was-ein-worker-kostet)):

| | Privat pro Worker | PSS pro Worker | Worker pro GB |
|---|---|---|---|
| Ein vollständiges Exponential (etwa 600 Klassen) | etwa 10 MB | etwa 11 MB | etwa 90 |
| Der Server allein, eine PHP-Seite mit einer Zeile | 1,3 bis 1,9 MB | 1,4 bis 1,9 MB | etwa 530 |

Der geteilte Teil (etwa 47 MB für Exponential) wird einmal bezahlt. Jeder Worker hält seinen eigenen Speicher ab dem Moment seines Forks,
untätig oder nicht (`settings/velocity.ini`).

> **Warnung**
> Beurteilen Sie Speicher nach PSS oder privatem Speicher, nie nach RSS. RSS zählt jede geteilte Copy-on-Write-Seite einmal pro Worker und
> überschätzt einen Pool um ein Vielfaches. Die Karte **Worker Memory** des Kontrollzentrums zeigt PSS
> ([Kontrollzentrum von Velocity](../../../funktionen/6.0/velocity-control-panel.md#das-dashboard)). Ein Überwachungssystem, das RSS
> aufsummiert, meldet Speicher, den die Maschine nicht nutzt.

Der Wert pro Worker hängt von der Website ab. Eine andere Messung in den Quellen, auf einer Testinstallation ohne Aufwärmen, fand 167,6 MB
PSS pro Worker ([HTTP/2 und Vorwärmen von Caches](../../../bc/6.0/http2-und-cache-vorwaermen.md#was-es-auf-der-testinstallation-fand)).
Messen Sie Ihre eigenen Websites, bevor Sie die Grenzen eines Tarifs festlegen.

## Fester oder dynamischer Pool

| | Fest (`SpareWorkers=0`) | Dynamisch (`SpareWorkers` über 0) |
|---|---|---|
| Beim Start | jeder Worker geforkt | nur die Reserve-Worker |
| Unter Last | wartet auf einen freien Worker | forkt weitere, bis zu `Workers` |
| Wenn ruhig | alle Worker bleiben | zusätzliche Worker nach `IdleWorkerTimeout` stillgelegt |
| Speicher | bezahlt für Worker, die vielleicht nie bedienen | bezahlt für das, was genutzt wird |

Gemessen auf einer Installation: Ein fester Pool von 590 hielt 7.094 MB, der dynamische Pool 626 MB
([Spezifikation des Worker-Pools](../../../spezifikationen/6.0/velocity-worker-pool.md#was-ein-worker-kostet)). Kapitel 8 schlägt einen
dynamischen Pool als Ausgangspunkt für die Produktion vor:

```ini
# settings/override/velocity.ini.append.php
[ServerSettings]
Workers=64
SpareWorkers=8
IdleWorkerTimeout=60
```

Für einen Tarif ist das die nützliche Form: `Workers` ist die Obergrenze, die Sie verkaufen, `SpareWorkers` die Untergrenze, die der Kunde
immer hat.

## Instanzen: mehrere Server auf einem Port

Ein Server beantwortet zwischengespeicherte Seiten in seinem eigenen Prozess und ist durch einen Kern begrenzt. `Instances=N` betreibt N
Server auf denselben Ports; der Kernel verteilt neue Verbindungen auf sie. Gemessen auf einer Testinstallation mit 12 Kernen,
zwischengespeicherte Startseite über TLS: etwa 2.950 Seiten pro Sekunde mit einer Instanz, 11.985 mit vier
([Velocity-Engines, Instanzen](../../../bc/6.0/velocity-engines.md#mehrere-qbix-server-auf-einem-port-instances)).

- Jede Instanz hat einen eigenen Pool: Teilen Sie `Workers` und `SpareWorkers` durch `Instances`, um denselben Speicher zu behalten.
- Jede hat eine eigene PID-Datei und ein eigenes Log (`server.pid`, `server.1.pid`, ...); `graceful` lädt sie nacheinander neu.
- Gerenderte Seiten werden mit mehr Instanzen nicht schneller; sie sind durch die Worker begrenzt.
- Nur Engine qbix; Linux und die BSDs.

## Wann ein Worker ersetzt wird

Ein Worker wird durch einen frischen Fork ersetzt nach `maxRequests` Anfragen, wenn sein Heap `workerMemoryCeiling` überschreitet, wenn
seine Ausgabepuffer unausgeglichen hinterlassen wurden oder wenn die Anwendung es verlangt. Eine Anfrage, die nach `requestTimeout`
Sekunden noch läuft, wird abgebrochen, und der Client bekommt `504`. Der Grund wird protokolliert. Ein `GET`, `HEAD` oder `OPTIONS`, auf das
ein sterbender Worker noch nicht zu antworten begonnen hatte, läuft noch einmal auf einem anderen Worker; ein `POST` läuft nie zweimal und
bekommt `502`.

**Die Zygote** ist ein Prozess, der vor der ersten angenommenen Verbindung geforkt wird; spätere Worker werden von ihr geforkt und erben
keine Verbindungen von Besuchern. Ohne sie hielten Worker, die unter Last geforkt wurden, Verbindungen offen (gemessen bis zu 50 in
`CLOSE-WAIT` nach einer Lastspitze von 20 Sekunden). Sie braucht die Erweiterung `sockets`; scheitert sie, werden Worker vom Server
geforkt, und das Log sagt es.

## Persistente Worker

`ForkPerRequest=disabled` (ausgeliefert) hält Worker über Anfragen hinweg am Leben, mit Datenbankverbindungen und Stat-Caches. Gemessen auf
einer angemeldeten Seite der Administration: 256 bis 284 ms mit einem frischen Worker pro Anfrage, 73 bis 80 ms mit persistenten Workern
([Kapitel 8, Abschnitt 8.3.7](../../../installation/08-die-website-ausliefern.md#837-persistente-worker-und-keepglobals)). Zwischen Anfragen stellt
der Worker statische Eigenschaften, Superglobals, Globals, Puffer und Handler wieder her. Vier Registries des Kernels und der Worker-Bereich des
Kernel-Service-Containers (`expKernelServicesWorker`: die Einstellungsdateien jedes Siteaccess, einmal pro Worker
aufgebaut, siehe [den kompilierten Kernel-Container](../leistung/kompilierter-kernel-container.md))
werden absichtlich behalten (`expVelocity::DEFAULT_KEEP_GLOBALS`); `[ApplicationSettings] KeepGlobals[]` fügt Namen nur für eine globale Variable hinzu, die
einmal durch `include_once` gefüllt wird. Behalten Sie nichts anderes: Eine globale Variable mit Anfragebezug, die zwischen Anfragen
behalten wird, lässt die Anfrage eines Besuchers in die nächste durchsickern.

`StatTtl=1` brachte gemessen 13 Prozent weniger CPU pro gerenderter Seite; eine Änderung durch einen anderen Prozess (einen anderen
Worker, Apache auf denselben Dateien) kann dann bis zu so lange brauchen, bis sie sichtbar wird.

## Streams halten Worker

Ab Engine 0.0.4.49 streamt ein Worker Server-Sent Events und ungepufferte Antworten. Jeder offene Stream hält einen Worker für seine ganze
Lebensdauer. `[ServerSettings] StreamTimeout` (Standard der Engine 300 s), `StreamShare` (0,25 von `Workers`) und `MaxStreams` begrenzen
sie; jenseits der Grenze bekommt ein Event-Stream `503` mit `Retry-After`, sodass Seiten den Rest des Pools behalten
([Velocity-Engines, Streaming-Antworten](../../../bc/6.0/velocity-engines.md#gestreamte-antworten)). Halten Sie die Grenze deutlich unter
`Workers`.

## Die Zahl für einen Tarif finden

Die Quellen warnen davor, Zahlen zu übernehmen: Die Werte beschreiben die Templates dieser Website auf dieser Maschine und sind keine
Eigenschaft des Servers ([HTTP/2 und Vorwärmen von Caches](../../../bc/6.0/http2-und-cache-vorwaermen.md#zwei-vorbehalte-zu-allem)). Messen Sie
stattdessen:

1. Messen Sie eine repräsentative Kunden-Website gerendert (`--cold`) bei der erwarteten Parallelität, mit `exp:benchmark`
   ([Eine gehostete Website messen](../leistung/benchmark-einer-gehosteten-website.md)).
2. Erhöhen Sie `Workers` schrittweise und wiederholen Sie. Auf der Testinstallation jener Seite erreichte der Durchsatz bei 16 Workern
   seinen Höhepunkt (etwa das 1,5-Fache der Kerne); 32 brachten nichts, 64 waren schlechter, und p90 sprang jenseits des Höhepunkts auf das
   Zehnfache.
3. Stimmen Sie auf diese **Durchsatzklippe** ab, nicht auf den Speicher. Die anderen Grenzen kommen später: die Obergrenze der Event-Loop
   (einige hundert Worker; knapp unter 1.000 mit der Standard-Event-Loop) und die Obergrenze des Speichers.
4. Nutzen Sie einen dynamischen Pool, damit untätige Kunden keinen Speicher halten.

Der eigene Standard der Engine ohne `Workers` ist, was nach Abzug von 1 GB in den Speicher passt, höchstens 8 pro Kern und insgesamt 64,
nie weniger als 4. Die mitgelieferte Datei setzt ausdrücklich 4, weil der Server sich selbst überlassen eine so große Zahl wählen kann, dass
er Verbindungen annimmt und keine beantwortet.

## Checkliste

- [ ] `Workers` pro Website ausdrücklich gesetzt; `SpareWorkers` über 0 auf geteilten Maschinen.
- [ ] Speicher als PSS überwacht, nicht als RSS.
- [ ] `Instances` über 1 nur, wo der Durchsatz zwischengespeicherter Seiten es braucht, mit entsprechend geteiltem `Workers`.
- [ ] `MaxStreams` gesetzt für Websites, die Event-Streams nutzen.
- [ ] Anzahl der Worker aus einer Messung der Website gewählt, in den Tarif geschrieben.

## Verwandte Seiten

- [Velocity für Hoster](velocity-fuer-hoster.md)
- [Speicher, CPU und Festplatte bemessen](../grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)
- [Eine gehostete Website messen](../leistung/benchmark-einer-gehosteten-website.md)
- [Spezifikation des Worker-Pools von Velocity](../../../spezifikationen/6.0/velocity-worker-pool.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
