---
title: Caching-Ebenen für Ingenieure
audience: software-engineer, admin
category: software-engineering
order: 300
summary: Jeder Cache zwischen einem Besucher und der Datenbank einer Exponential-Website aus Sicht eines Ingenieurs - was jede Ebene speichert, wonach sie geschlüsselt ist, ob sie eingeschaltet ausgeliefert wird, wen sie bedient, welche Server sie teilen und welche Risiken für die Korrektheit jede mit sich bringt - vom Browser und dem Antwort-Cache von Velocity bis zu View-Cache, Template-Blöcken, INI-Cache, SQL-Abfrage-Cache und der Option Redis oder Valkey.
keywords: Caching, Cache-Ebenen, Antwort-Cache, HTTP-Cache, View-Cache, Template-Block, INI-Cache, Abfrage-Cache, Valkey, Redis, OPcache, statischer Cache, Cache-Schlüssel, veraltete Inhalte, Softwareingenieur, caching, cache layers, response cache, view cache, template block, query cache, static cache, cache keys, stale content
type: help
---
# Caching-Ebenen für Ingenieure

Diese Seite richtet sich an Ingenieure, die Leistung und Korrektheit zusammen betrachten. Exponential hat viele Caches;
die meiste Leistungsarbeit besteht darin zu wählen, welche eingeschaltet werden, und die meisten Fehler mit "veralteter
Seite" sind einer davon, der antwortet, wenn er es nicht sollte. Die Sicht der Betreiber auf dieselben Ebenen ist
[Caching-Ebenen](../hosting/leistung/cache-schichten.md), und die Administrationsseite ist unter
[Caches](../../anleitungen/caches.md) beschrieben.

## Inhalt

1. [Die Ebenen der Reihe nach](#die-ebenen-der-reihe-nach)
2. [Wonach jede Ebene geschlüsselt ist](#wonach-jede-ebene-geschlüsselt-ist)
3. [Geteilt oder je Knoten](#geteilt-oder-je-knoten)
4. [Risiken für die Korrektheit](#risiken-für-die-korrektheit)
5. [Wählen, was eingeschaltet wird](#wählen-was-eingeschaltet-wird)

## Die Ebenen der Reihe nach

Vom Besucher hinunter zur Datenbank:

| # | Ebene | Ausgeliefert | Bedient | Quelle |
|---|---|---|---|---|
| 1 | Browser-Cache für statische Dateien | Velocity: `StaticMaxAge` ein Jahr | alle | [HTTP-Caching](../../bc/6.0/http-caching.md) |
| 2 | Browser-Cache für Seiten | aus (`[HTTPHeaderSettings]`) | anonyme Besucher, wenn eingeschaltet | [HTTP-Caching](../../bc/6.0/http-caching.md) |
| 3 | CDN oder Caching-Proxy | nicht Teil von Exponential | wie konfiguriert | Exponential sendet keine Purges |
| 4 | Antwort-Cache von Velocity | unter Velocity an (`velocity.ini [CacheSettings] Enabled=enabled`) | anonyme Besucher | [Antwort-Cache von Velocity](../../funktionen/6.0/velocity-antwort-cache.md) |
| 5 | Rollenbewusster HTTP-Cache | aus (`httpcache.ini`) | anonyme und angemeldete Besucher, je Berechtigungskontext | [HTTP-Cache](../../bc/6.0/httpcache.md) |
| 6 | Statischer Cache | aus | anonyme Besucher, als Dateien | [Standards des statischen Cache](../../bc/6.0/standardwerte-des-statischen-caches.md) |
| 7 | View-Cache für Inhalte | an (`[ContentSettings] ViewCaching=enabled`) | jeden Besucher | [Caches](../../anleitungen/caches.md) |
| 8 | Cache für Template-Blöcke | an (`[TemplateSettings] TemplateCache=enabled`) | jeden Besucher | [Die Template-Engine und die Design-Kette](template-engine-und-design-kette.md) |
| 9 | Kompilierte Templates, INI- und Override-Caches | an | jede Anfrage | [Caches](../../anleitungen/caches.md) |
| 10 | Redis oder Valkey für Views, Blöcke, INI | nur mit `sevenx_valkey_cache` | jede Anfrage | [sevenx_valkey_cache](../../funktionen/6.0/erweiterungen/sevenx_valkey_cache.md) |
| 11 | SQL-Abfrage-Cache | aus (`querycache.ini Mode=off`) | jede Anfrage, die er beantworten kann | [Der SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md) |
| 12 | OPcache, APCu | je PHP-Prozess | jede Anfrage | Ihre PHP-Konfiguration |

Den Antwort-Cache von Velocity gibt es nur unter der Engine `qbix`: FrankenPHP hat noch keinen, und PHP-FPM hinter Apache
oder nginx hat keinen eigenen ([Anwendungsserver](anwendungsserver.md)).

## Wonach jede Ebene geschlüsselt ist

| Ebene | Schlüssel (kurz) | Invalidiert durch |
|---|---|---|
| Antwort-Cache von Velocity | die Anfrage eines anonymen Besuchers; Lebensdauer `[CacheSettings] DefaultTtl` (mitgeliefert 30 Sekunden), sofern `Cache-Control` der Seite nichts anderes sagt | Lebensdauer, `./console exp:velocity cache clear` |
| Rollenbewusster HTTP-Cache | die Inhaltsansicht und der Berechtigungskontext (anonym, ein gemeinsamer Rollenkontext oder privat) | Purges bei Inhaltsänderungen (standardmäßig `ContentChangePurges=all`), `--clear-id=exphttpcache` |
| View-Cache für Inhalte | der Knoten, die Ansicht und ihre Parameter und der Zugriffskontext des Benutzers | `eZContentCacheManager` beim Veröffentlichen, `--clear-tag=content` |
| Cache für Template-Blöcke | die in `{cache-block}` geschriebenen Schlüssel und sein Ablauf | Ablauf, `--clear-id=template-block` |
| INI-Cache | die Dateien der Kaskade für einen Siteaccess (und eine Umgebung) | `--clear-tag=ini` |
| SQL-Abfrage-Cache | die `SELECT`-Anweisung | ein Schreibvorgang in eine der gelesenen Tabellen |

## Geteilt oder je Knoten

Die Caches in `var/` sind Dateien, die über den Cluster-Datei-Handler geschrieben werden, sodass derselbe Code sie auf
Cluster-Speicher teilen kann. Der Antwort-Cache von Velocity und der Speicherzustand seiner Worker gelten je
Serverprozess; OPcache und APCu je PHP-Prozess. Entscheiden Sie bei mehreren Webknoten für jede Ebene, ob sie geteilt,
auf jedem Knoten geleert oder ablaufen gelassen wird ([Skalierung und Clustering](skalierung-und-clustering.md)).

## Risiken für die Korrektheit

- **Leeren vor dem Neuladen.** Eine Seite, die der alte Code zwischen dem Leeren eines Cache und dem Neuladen von PHP-FPM
  oder Velocity rendert, wird erneut zwischengespeichert; leeren Sie Seiten-Caches zuletzt
  ([Eine Änderung bereitstellen](eine-aenderung-bereitstellen.md)).
- **Schreibvorgänge von außen.** Eine Wiederherstellung oder ein Schreibvorgang über einen SQL-Client umgeht die
  Invalidierung des SQL-Abfrage-Cache; leeren Sie ihn danach.
- **Cache-Blöcke ohne die richtigen Schlüssel.** Ein `{cache-block}`, der nicht nach dem schlüsselt, was die Ausgabe
  ändert, liefert das Fragment eines Benutzers oder einer Seite an einen anderen aus.
- **Velocity hält Einstellungen im Speicher.** Nach einer INI-Änderung genügt unter Velocity das Leeren des INI-Cache
  nicht; starten Sie es neu.
- **Statischer Cache.** War er an, erzeugte jedes Veröffentlichen jede betroffene Seite über HTTP innerhalb der Anfrage
  des Redakteurs neu; das Projekt liefert ihn ausgeschaltet aus und empfiehlt, ihn ausgeschaltet zu lassen.

## Wählen, was eingeschaltet wird

1. Behalten Sie die mitgelieferten Standards: View-Cache, Template-Blöcke, kompilierte Templates, INI-Cache.
2. Für anonymen Verkehr bringt unter Velocity der Antwort-Cache den größten Gewinn je Änderung.
3. Für angemeldeten Verkehr hält der rollenbewusste HTTP-Cache ganze Ansichten je Berechtigungskontext.
4. Für Seiten, die sich nicht als Ganzes speichern lassen (die Administration, private Kontexte, Skripte), der
   SQL-Abfrage-Cache.
5. Bei mehreren Webknoten erwägen Sie Redis oder Valkey für Views, Blöcke und INI.

Messen Sie jeden Schritt ([Leistungsmessung und Benchmarks](leistungsmessung-und-benchmarks.md)).

## Verwandte Seiten

- [Cache-Invalidierung und Leeren](cache-invalidierung-und-leeren.md)
- [Caching in Anwendungen](../ersteller/anwendungsentwickler/caching-in-anwendungen.md)
- [Der Antwort-Cache und dem Netz aus dem Weg gehen](../../bc/6.0/antwort-cache-und-navigation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
