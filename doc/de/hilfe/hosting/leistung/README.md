---
title: Leistung
audience: hosting
category: hosting
order: 10
summary: Überblick über den Bereich Leistung für Hosting-Anbieter - die Hebel in der Reihenfolge ihrer Wirkung, die Cache-Schichten, Messen vor dem Abstimmen und ein einzeiliger Wegweiser zu jeder Seite des Bereichs.
keywords: Leistung, Geschwindigkeit, Abstimmung, Cache, Caching, OPcache, PHP-FPM, Velocity, Antwort-Cache, HTTP-Cache, SQL-Abfrage-Cache, Valkey, Redis, statischer Cache, CDN, Varnish, Benchmark, Überblick, performance, speed, tuning, response cache, HTTP cache, SQL query cache, static cache, overview
type: help
---
# Leistung

Dieser Bereich ist für Systemadministratoren und Produktmanager bei Hosting-Anbietern, die möchten, dass Exponential-Websites auf
ihren Maschinen schnell sind, und die Kunden erklären müssen, was in einem Tarif enthalten ist und was nicht. Er beantwortet: welche
Hebel am meisten zählen, welche Caches es gibt und wer sie leert, und wie Sie vor und nach einer Änderung messen.

Zurück zu [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md).

## Inhalt

1. [Die Hebel, in der Reihenfolge ihrer Wirkung](#die-hebel-in-der-reihenfolge-ihrer-wirkung)
2. [Zuerst messen](#zuerst-messen)
3. [Seiten in diesem Bereich](#seiten-in-diesem-bereich)
4. [Was Hoster am häufigsten falsch machen](#was-hoster-am-häufigsten-falsch-machen)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Hebel, in der Reihenfolge ihrer Wirkung

Das Installationsbuch führt sie in dieser Reihenfolge auf
([Kapitel 10, Abschnitt 10.12](../../../../en/us/install/10-after-installing.md#1012-performance-tuning)):

1. **Mit Velocity ausliefern** und seinem Antwort-Cache. Eine zwischengespeicherte anonyme Seite wird ohne PHP beantwortet;
   persistente Worker halten die Anwendung, die Datenbankverbindung und die kompilierten Dateien warm.
2. **Den Content-View-Cache und den Template-Block-Cache anlassen** (`site.ini [ContentSettings] ViewCaching=enabled`,
   `[TemplateSettings] TemplateCache=enabled`, beide mitgeliefert), und die Website nach einem Leeren vorwärmen.
3. **OPcache.**
4. **APCu** für die Speicherstufe des Antwort-Caches von Velocity und den SQL-Abfrage-Cache im Modus `shared`.
5. **Der SQL-Abfrage-Cache** (`querycache.ini [QueryCacheSettings] Mode=request` oder `shared`), auf Ihrer Website gemessen.
6. **Die Datenbank** ([Kapitel 9](../../../../en/us/install/09-databases.md)).

Das README fügt einen Punkt hinzu, der für Hoster zählt, die Apache oder nginx behalten: Der größte Teil der Geschwindigkeit kam von
Korrekturen an Exponential und am Cache, nicht vom Prozessmodell des Servers; Exponential 6.0.15+ ist auch unter Apache oder Nginx
unter Last schnell. Velocity hebt die Decke weiter an; es ist nicht das, was die Anwendung schnell macht.

## Zuerst messen

`exp:benchmark` misst Seiten über HTTP, zwischengespeichert und gerendert, und vergleicht zwei Server direkt nacheinander:

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
# ... make one change ...
php bin/php/console exp:benchmark --baseline=var/benchmark/base.json --allow-root-user
```

Jede Zahl in diesem Bereich, die aus den Quellen stammt, ist eine Messung auf einer bestimmten Maschine und Website. Die Quellen sagen
das selbst; betrachten Sie sie als Beispiele dafür, was zu messen ist, nicht als Versprechen an Kunden. Siehe
[Benchmark einer gehosteten Website](benchmark-einer-gehosteten-website.md).

## Seiten in diesem Bereich

| Seite | Was sie beantwortet |
|---|---|
| [Abstimmung von PHP-FPM](abstimmung-von-php-fpm.md) | Pool-Einstellungen, die für Exponential zählen, Neuladen, Zeitlimits, APCu |
| [OPcache](opcache.md) | Die richtigen OPcache-Einstellungen unter PHP-FPM, Velocity und FrankenPHP, und warum sie sich unterscheiden |
| [Cache-Schichten](cache-schichten.md) | Jeder Cache vom Browser bis zur Datenbank, wer ihn füllt und wer ihn leert |
| [Antwort-Cache von Velocity](antwort-cache-von-velocity.md) | Der Cache im Elternprozess von Velocity: Einstellungen, Prüfungen, Leeren |
| [HTTP-Cache-Header](http-cache-header.md) | Was Browser, Proxys und CDNs behalten dürfen, und der rollenbewusste HTTP-Cache |
| [Varnish und Reverse Proxys](varnish-und-reverse-proxys.md) | Zwischenspeichernde Proxys davor: Vertrauen, Leeren, was nicht zwischengespeichert werden darf |
| [CDN](cdn.md) | Ein Content Delivery Network vor Kunden-Websites stellen |
| [Valkey und Redis](valkey-und-redis.md) | Die Erweiterung `sevenx_valkey_cache`: Caches in Redis oder Valkey |
| [Statischer Cache](statischer-cache.md) | Als Dateien erzeugte Seiten; warum er ausgeschaltet ausgeliefert wird und wann Sie ihn nutzen |
| [Caches leeren und vorwärmen](caches-leeren-und-vorwaermen.md) | Welcher Cache für welche Änderung geleert wird, in welcher Reihenfolge, und wie Sie vorwärmen |
| [Benchmark einer gehosteten Website](benchmark-einer-gehosteten-website.md) | `exp:benchmark` für Hoster: faire Vergleiche, Prüfungen nach dem Ausrollen, Grenzen von Tarifen |

## Was Hoster am häufigsten falsch machen

| Fehler | Wirkung | Seite |
|---|---|---|
| `opcache.validate_timestamps=0` auf einer Exponential-Website | eine nach dem Leeren neu geschriebene Cache-Datei wird bis zum Neustart von PHP in ihrer alten Form ausgeliefert | [OPcache](opcache.md) |
| Caches mit gerenderter Ausgabe leeren, bevor der neue Code läuft | die alte Ausgabe wird erneut zwischengespeichert, und die Änderung „hat nicht gewirkt“ | [Caches leeren und vorwärmen](caches-leeren-und-vorwaermen.md) |
| den falschen PHP-FPM-Master neu laden | die Website führt weiter den alten Code aus | [Abstimmung von PHP-FPM](abstimmung-von-php-fpm.md) |
| den Speicher von Velocity nach RSS beurteilen | der Pool sieht um ein Vielfaches größer aus, als er ist | [Velocity-Worker und Pools](../server/velocity-worker-und-pools.md) |
| ein zwischenspeichernder Proxy, der Seiten mit einem Sitzungscookie speichert | Seiten angemeldeter Benutzer werden anderen Besuchern ausgeliefert | [Varnish und Reverse Proxys](varnish-und-reverse-proxys.md) |
| den statischen Cache der Geschwindigkeit wegen einschalten | jede Veröffentlichung erzeugt Seiten innerhalb der Anfrage des Redakteurs neu | [Statischer Cache](statischer-cache.md) |

## Verwandte Seiten

- [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md)
- [Webserver und Engines](../server/README.md)
- [Langsame Website](../runbooks/langsame-website.md)
- [Caches](../../../../en/us/guides/caches.md)
- [Benchmark von Exponential](../../../../en/us/guides/benchmarking.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
