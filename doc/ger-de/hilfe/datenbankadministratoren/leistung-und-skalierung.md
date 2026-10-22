---
title: Leistung und Skalierung
audience: dba
category: database-administration
order: 40
summary: Wie die Last einer Exponential-Website ihre Datenbank erreicht und wie Sie sie skalieren - die Cache-Ebenen, die die meisten Anfragen fernhalten, was die Datenbank dennoch erreicht, persistente Verbindungen und die persistenten Worker von Velocity, Speicher je Worker auf SQLite, Verbindungszahlen auf Server-Engines, Replikate, MongoDB-Indizes, Oracle-Pooling - und die Reihenfolge, in der Sie die Hebel ansetzen.
keywords: Leistung, Skalierung, Cache-Ebenen, Antwort-Cache von Velocity, View-Cache, Template-Block-Cache, Abfrage-Cache, statischer Cache, persistente Verbindung, UsePersistentConnection, Velocity-Worker, cache_size, max_connections, Replikate, UseSlaveServer, DRCP, MongoDB-Indizes, ANALYZE, Statistiken, performance, scaling, caching layers, persistent connection, replicas, statistics
type: help
---
# Leistung und Skalierung

Diese Seite richtet sich an Datenbankadministratoren, die die Datenbank einer Exponential-Website bemessen oder den Grund
suchen, warum sie langsam ist. Sie beantwortet: wie viel des Verkehrs die Datenbank überhaupt erreicht, was sie erreicht und
welche Hebel bei jeder Engine helfen. Quellen:
[Datenbankunterstützung, Leistung und Skalierung](../datenbankunterstuetzung.md#performance-und-skalierung),
[Was die Leselast trägt](../../installation/09-datenbanken.md#was-die-leselast-trägt-die-cache-schichten) und
[10.12 Leistungsoptimierung](../../installation/10-nach-der-installation.md#1012-performance-tuning).

## Inhalt

1. [Die meisten Anfragen erreichen die Datenbank nie](#die-meisten-anfragen-erreichen-die-datenbank-nie)
2. [Was sie erreicht](#was-sie-erreicht)
3. [Verbindungen und Worker](#verbindungen-und-worker)
4. [Je Engine](#je-engine)
5. [Die Hebel, der Reihe nach](#die-hebel-der-reihe-nach)
6. [Checkliste](#checkliste)

## Die meisten Anfragen erreichen die Datenbank nie

| Ebene | Einstellung | Was ein Treffer kostet |
|---|---|---|
| Antwort-Cache von Velocity | `settings/velocity.ini [CacheSettings] Enabled=enabled`, `DefaultTtl=30` | vom Elternprozess von Velocity beantwortet: kein PHP-Worker, keine Datenbank; nur anonyme Besucher |
| Cache der Inhaltsansichten | `site.ini [ContentSettings] ViewCaching=enabled` | der Hauptbereich einer Seite aus einer Datei |
| Template-Block-Cache | `site.ini [TemplateSettings] TemplateCache=enabled` | Menüs und Fußzeilen aus Dateien |
| SQL-Abfrage-Cache | standardmäßig `querycache.ini Mode=off` | ein wiederholtes `SELECT` aus APCu oder einer Datei; nicht auf MongoDB ([Der SQL-Abfrage-Cache](sql-abfrage-cache.md)) |
| Dateicache des Betriebssystems | | Datenbankseiten nach dem ersten Lesen aus dem Speicher |

Der statische Cache (`[ContentSettings] StaticCache`) ist im mitgelieferten Installer ausgeschaltet und sollte es bleiben: Mit
ihm erzeugte jede Veröffentlichung die betroffenen Seiten innerhalb der Anfrage des Redakteurs über HTTP neu.

## Was sie erreicht

- die ersten Aufrufe nach dem Leeren eines Cache oder einer Veröffentlichung;
- angemeldete Redakteure und die Administration;
- die Suche (die eingebaute Maschine liest `ezsearch_*`);
- Veröffentlichen, Workflows, Cronjobs, Importe.

Bemessen Sie die Datenbank dafür, nicht für die Gesamtzahl der Seitenaufrufe. Rechnen Sie nach einem Deploy, der Caches leert,
mit einem kurzen Schub (dem Aufwärmen); `exp:preload` wärmt die Seiten einer Website gezielt vor
([Caches vorladen](../../anleitungen/caches-vorwaermen.md)).

## Verbindungen und Worker

- Unter PHP-FPM öffnet jeder Worker seine eigene Verbindung, wenn eine Anfrage eine braucht. `UsePersistentConnection=enabled`
  behält sie für die Lebensdauer des Workers (MySQL, PostgreSQL, Oracle).
- Unter Velocity mit persistenten Workern (`settings/velocity.ini [ServerSettings] ForkPerRequest=disabled`, der mitgelieferte
  Wert) hält jeder Worker die Anwendung, seine Verbindung und seine Caches warm. Die Poolgröße ist `[ServerSettings] Workers`
  (mitgeliefert `4`), optional dynamisch mit `SpareWorkers` und `IdleWorkerTimeout`
  ([Velocity-Worker und Pools](../hosting/server/velocity-worker-und-pools.md)).
- Zählen Sie die Verbindungen: PHP-FPM-Worker plus Velocity-Worker plus Cron- und Konsolenprozesse, je Website, gegen das Limit
  des Servers (`max_connections` auf MySQL und PostgreSQL, Sessions oder ein DRCP-Pool auf Oracle).

## Je Engine

| Engine | Hebel |
|---|---|
| SQLite | Lesezugriffe skalieren mit der Zahl der Prozesse; Schreibzugriffe reihen sich ein. Rechnen Sie mit bis zu 64 MB Seiten-Cache je Prozess, der die Datenbank geöffnet hat (`SQLitePragmas[]=cache_size=-16384` senkt ihn auf 16 MB). `ANALYZE` nach Importen ([SQLite-Wartung](sqlite-wartung.md)) |
| PostgreSQL | Sperren auf Zeilenebene; persistente Verbindungen; Ihr übliches Tuning von `shared_buffers` und Autovacuum; Replikation ist extern |
| MariaDB, MySQL | InnoDB-Zeilensperren; persistente Verbindungen; `innodb_buffer_pool_size` für den Arbeitsbestand bemessen; Lesereplikate mit `UseSlaveServer` ([Lesereplikate](mysql-lesereplikate.md)) |
| MongoDB | die Leistung folgt der Zahl der Anweisungen je Seite; legen Sie die Indizes aus `bin/mongodb/create_indexes.js` an, ohne die Teilbaumabfragen und URL-Alias-Suchen ganze Collections durchsuchen |
| Oracle | eine neue dedizierte Session kostet Dutzende Millisekunden: `Persistent=enabled` und DRCP; die Einstellungen zu `Prefetch`; Statistiken mit `gather-stats` ([Oracle-Tuning](oracle-verbindungen-und-tuning.md)) |

Serverparameter wie Buffer-Pools gehören Ihrer Engine; Exponential setzt oder verlangt keine bestimmten Werte.

## Die Hebel, der Reihe nach

1. **Caches an**: View-Cache, Template-Block-Cache, Antwort-Cache von Velocity; prüfen Sie sie unter
   **Setup > Systeminformationen**, Karte Caches.
2. **Statistiken aktuell**: `ANALYZE` (SQLite, PostgreSQL), `gather-stats` (Oracle); MongoDB-Indizes angelegt.
3. **Die Anweisungen finden**: [Langsame Abfragen finden](langsame-abfragen-finden.md); korrigieren Sie wiederholte Abfragen an
   ihrer Quelle.
4. **Abfrage-Cache** `request`, dann `shared`.
5. **Persistente Verbindungen** oder die persistenten Worker von Velocity.
6. **Replikate** für MySQL und MariaDB; ein größerer Server oder eine andere Engine, wenn sich Schreibzugriffe stauen
   ([Eine Engine wählen](eine-engine-waehlen.md)).

Messen Sie jeden Schritt mit `exp:benchmark --save` und `--baseline` ([Benchmark](../../funktionen/6.0/benchmark.md)).

## Checkliste

- [ ] Caches eingeschaltet; statischer Cache aus.
- [ ] Verbindungszahl aller Worker unter dem Limit des Servers.
- [ ] SQLite-Speicher für `cache_size` je Worker bemessen.
- [ ] Statistiken und Indizes aktuell.
- [ ] Jede Änderung vorher und nachher gemessen.

## Verwandte Seiten

- [Caching-Ebenen](../hosting/leistung/cache-schichten.md), [PHP-FPM-Tuning](../hosting/leistung/abstimmung-von-php-fpm.md)
- [Speicher, CPU und Festplatte bemessen](../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)
- [Die Datenbank überwachen](die-datenbank-ueberwachen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
