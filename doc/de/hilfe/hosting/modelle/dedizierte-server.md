---
title: Dedizierte Server
audience: hosting, admin
category: hosting
order: 50
summary: Wie Sie einen dedizierten Server für Exponential nutzen - eine große Website oder viele abgeschottete, die Größe des Worker-Pools und der Instanzen von Velocity aus den gemessenen Zahlen der Quellen, die Datenbank auf derselben Maschine oder daneben und was Sie messen, bevor Sie sich auf Hardware festlegen.
keywords: dedizierter Server, Bare Metal, Dimensionierung, Velocity, Worker, Instanzen, SO_REUSEPORT, dynamischer Pool, PSS, Arbeitsspeicher, PostgreSQL, SQLite, mehrere Websites, Abschottung, Benchmark, dedicated server, bare metal, sizing, workers, instances, dynamic pool, memory, several sites, isolation, benchmark
type: help
---
# Dedizierte Server

Diese Seite ist für Hosting-Anbieter, die Exponential auf einen dedizierten (physischen) Server stellen: entweder eine große
Kunden-Website auf einer eigenen Maschine oder viele Kunden-Websites auf einer großen Maschine. Sie beantwortet, wie die
Quellen des Projekts die Ressourcen einer solchen Maschine zu nutzen empfehlen, welche Zahlen sie nennen und was Sie selbst
messen müssen.

## Inhalt

1. [Zwei Arten, einen dedizierten Server zu nutzen](#zwei-arten-einen-dedizierten-server-zu-nutzen)
2. [Die Kerne nutzen: Worker und Instanzen](#die-kerne-nutzen-worker-und-instanzen)
3. [Arbeitsspeicher: PSS lesen, nicht RSS](#arbeitsspeicher-pss-lesen-nicht-rss)
4. [Die Datenbank auf einem dedizierten Server](#die-datenbank-auf-einem-dedizierten-server)
5. [Viele Websites auf einer Maschine](#viele-websites-auf-einer-maschine)
6. [Messen, bevor Sie kaufen](#messen-bevor-sie-kaufen)
7. [Checkliste](#checkliste)

## Zwei Arten, einen dedizierten Server zu nutzen

| Nutzung | Form | Hauptanliegen |
|---|---|---|
| Eine große Website | Velocity zu den Besuchern hin, ein großer Worker-Pool, vielleicht mehrere Instanzen; die Datenbank auf derselben Maschine oder einer zweiten | alle Kerne nutzen; Schreibvorgänge in die Datenbank |
| Viele Kunden-Websites | ein PHP-FPM-Pool oder eine Velocity-Instanz pro Website, jeweils als eigener Benutzer | Abschottung; faire Aufteilung von Speicher und CPU |

Ein dedizierter Server ist Stufe 1, 2 oder 3 nach der Distribution, die Sie installieren, genau wie eine virtuelle Maschine
([Betriebssystemunterstützung](../../../../en/us/help/os-support.md#the-four-tiers)). Der eigene Entwicklungsserver des Projekts ist eine
Maschine mit AlmaLinux 9 und Plesk, die Apache mit PHP-FPM 8.5 und Exponential Velocity auf derselben Installation betreibt
([Stufe 1](../../../../en/us/help/os-support.md#tier-1-tested-here)).

## Die Kerne nutzen: Worker und Instanzen

Die Kapazität von Velocity wird in `settings/override/velocity.ini.append.php`, `[ServerSettings]` gesetzt
([8.3.6](../../../../en/us/install/08-serving-the-site.md#836-workers-pool-size-the-dynamic-pool-and-the-zygote)):

| Einstellung | Mitgeliefert | Bedeutung |
|---|---|---|
| `Workers` | `4` | die Größe des Pools; mit `SpareWorkers` das Maximum, bis zu dem er wächst |
| `SpareWorkers` | `0` | über 0: ein dynamischer Pool, der so viele untätige Worker vorhält |
| `IdleWorkerTimeout` | `60` | Sekunden, die ein zusätzlicher Worker untätig bleiben darf, bevor er beendet wird |
| `Instances` | `1` | Server, die sich mit `SO_REUSEPORT` dieselben Ports teilen (Linux und die BSDs); `Workers` und `SpareWorkers` gelten pro Instanz |

Kapitel 8 schlägt als guten Anfang für eine Produktions-Website einen dynamischen Pool vor:

```ini
# settings/override/velocity.ini.append.php
[ServerSettings]
Workers=64
SpareWorkers=8
IdleWorkerTimeout=60
```

Was die Quellen gemessen haben: Ein Server beantwortet zwischengespeicherte Seiten in seinem eigenen Prozess und ist durch
einen Kern begrenzt (gemessen: etwa 2.950 zwischengespeicherte Seiten pro Sekunde über TLS; vier Instanzen etwa 12.000).
Gerenderte Seiten sind durch die Worker begrenzt, nicht durch die Anzahl der Instanzen. Auf einer Maschine mit vielen Kernen
verteilt `Instances` über 1 den zwischengespeicherten Verkehr; die Anzahl der Worker entscheidet über den gerenderten Verkehr.

> **Hinweis**
> Diese Zahlen sind die eigene Messung des Projekts auf seiner eigenen Maschine, zitiert, damit Sie die Form der
> Skalierung kennen. Sie sind kein Versprechen für Ihre Hardware. Messen Sie Ihre eigenen (siehe
> [Messen, bevor Sie kaufen](#messen-bevor-sie-kaufen)).

Mit Apache oder nginx und PHP-FPM übernehmen die Prozesseinstellungen des PHP-FPM-Pools die Rolle von `Workers`; siehe
[Abstimmung von PHP-FPM](../leistung/abstimmung-von-php-fpm.md).

## Arbeitsspeicher: PSS lesen, nicht RSS

Ein Worker eines vollständigen Exponential kostet etwa 10 bis 11 MB eigenen Speicher (gemessen als PSS auf 12 Kernen und
PHP 8.5); der gemeinsame Teil, etwa 47 MB, wird einmal bezahlt. Beurteilen Sie den Speicher nach PSS oder privatem Speicher,
nie nach RSS, das jede gemeinsame Seite einmal pro Worker zählt und einen Pool um ein Vielfaches überschätzt
([8.3.6](../../../../en/us/install/08-serving-the-site.md#836-workers-pool-size-the-dynamic-pool-and-the-zygote)).

Für einen dedizierten Server heißt das: Ein großer Pool ist im Speicher günstig; die Grenzen kommen von CPU, Datenbank und
Caches. Das Kontrollzentrum von Velocity zeigt den Speicher als PSS
([Kontrollzentrum von Velocity](../../../../en/us/features/6.0/velocity-control-panel.md)).

Worker werden außerdem von selbst ersetzt nach `maxRequests` (1000), wenn der Heap `workerMemoryCeiling` überschreitet,
oder wenn eine Anfrage länger als `requestTimeout` läuft (30 Sekunden, der Client erhält 504). Das sind Einstellungen der
Engine ([Worker-Pool von Velocity](../../../../en/us/specifications/6.0/velocity-worker-pool.md)).

## Die Datenbank auf einem dedizierten Server

In der offiziellen Reihenfolge: 1. SQLite 3 (der Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL.
Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich.

- **1. SQLite 3** auf derselben Maschine skaliert Lesezugriffe mit der Anzahl der Prozesse; Schreibvorgänge stehen in
  einer Warteschlange. Es ist eine vollwertige Produktionsdatenbank für eine Maschine. Rechnen Sie mit bis zu 64 MB
  Seitencache pro Prozess, der die Datenbank geöffnet hat, wenn Sie den Speicher für einen großen Worker-Pool bemessen
  (`SQLitePragmas[]=cache_size=-16384` senkt ihn auf 16 MB)
  ([Leistung und Skalierung](../../../../en/us/help/database-support.md#performance-and-scaling)).
- **2. PostgreSQL** (und 4. MariaDB, 6. MySQL) skalieren Schreibvorgänge mit Sperren auf Zeilenebene; unter Velocity hält
  jeder Worker seine Verbindung, sodass der Datenbankserver so viele Verbindungen annehmen muss, wie es Worker über alle
  Instanzen gibt.
- Eine zweite Maschine für die Datenbank ist der Schritt zu [mehreren Web-Knoten](../betrieb/mehrere-web-knoten.md).

Die meisten Anfragen erreichen die Datenbank nie: Der Antwort-Cache von Velocity, der View-Cache und der Template-Block-Cache
antworten zuerst ([Cache-Schichten](../leistung/cache-schichten.md)).

## Viele Websites auf einer Maschine

Für viele Kunden auf einem dedizierten Server folgen Sie der Regel zur Abschottung aus der `README.md` im Wurzelverzeichnis:
Betreiben Sie eine Velocity-Instanz pro Website hinter einem Proxy, jede als eigener Benutzer des Betriebssystems, oder
nutzen Sie einen klassischen Server mit einem PHP-FPM-Pool pro Website
([8.1](../../../../en/us/install/08-serving-the-site.md#81-the-choice-in-one-table)). Eine Exponential-Installation mit mehreren
Siteaccesses ist keine Grenze zwischen Kunden ([Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md)).

Jede Website hat dann ihr eigenes `var/`, ihre eigenen Cron-Zeilen in der Crontab ihres eigenen Benutzers und ihre eigenen
Backups.

## Messen, bevor Sie kaufen

Das Installationsbuch nennt wenige harte Zahlen und erfindet keine weiteren
([2.10 Arbeitsspeicher, CPU und Festplatte](../../../../en/us/install/02-requirements.md#210-memory-cpu-and-disk)). Messen Sie eine
repräsentative Website auf der Hardware, die in Frage kommt:

```bash
php bin/php/console exp:benchmark
php bin/php/console exp:benchmark --compare=https://example.com,https://example.com:8080
```

Die zweite Form vergleicht zwei Adressen, zum Beispiel Apache und Velocity auf derselben Installation
([Benchmark](../../../../en/us/features/6.0/benchmark.md), [Benchmark einer gehosteten Website](../leistung/benchmark-einer-gehosteten-website.md)).
Halten Sie zu jedem Ergebnis Hardware, PHP-Version, Engine, Worker-Einstellungen und Zustand des Caches fest.

## Checkliste

- [ ] Eine große Website oder viele Websites entschieden; Abschottung pro Website bei vielen Kunden.
- [ ] `Workers`, `SpareWorkers` und `Instances` ausdrücklich gesetzt und gemessen.
- [ ] Speicher nach PSS beurteilt; die Verbindungsgrenzen der Datenbank passen zur Anzahl der Worker.
- [ ] Datenbank in der offiziellen Reihenfolge, auf dieser Maschine oder einer zweiten.
- [ ] Benchmarks für den Maschinentyp festgehalten, bevor er als Tarif verkauft wird.
- [ ] Startdienst für Velocity, Überwachung und Backups eingerichtet.

## Verwandte Seiten

- [Ein Hosting-Modell wählen](ein-hosting-modell-waehlen.md), [VPS-Hosting](vps-hosting.md)
- [Speicher, CPU und Festplatte dimensionieren](../grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)
- [Velocity-Worker und Pools](../server/velocity-worker-und-pools.md)
- [Velocity-Webserver](../../../../en/us/features/6.0/velocity-web-server.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
