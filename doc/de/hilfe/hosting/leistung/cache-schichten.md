---
title: Cache-Schichten
audience: hosting, admin
category: hosting
order: 40
summary: Jeder Cache zwischen einem Besucher und der Datenbank einer Exponential-Website, in Reihenfolge - was jeder enthält, ob er ein- oder ausgeschaltet ausgeliefert wird, wo er liegt, welcher Server ihn nutzt und wie er geleert wird, damit ein Hoster weiß, was ein Tarif enthält, und der Support weiß, woher eine veraltete Seite kommt.
keywords: Cache, Cache-Schichten, Browser-Cache, StaticMaxAge, CDN, Reverse Proxy, Antwort-Cache von Velocity, HTTP-Cache, httpcache.ini, statischer Cache, View-Cache, ViewCaching, template-block, TemplateCache, kompilierte Templates, INI-Cache, SQL-Abfrage-Cache, querycache.ini, Valkey, Redis, OPcache, APCu, X-Cache, X-Exp-Cache, caching layers, browser cache, reverse proxy, response cache, static cache, view cache, compiled templates, SQL query cache
featured: true
type: help
---
# Cache-Schichten

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei Hosting-Anbietern. Sie beantwortet: welche Caches eine
Exponential-Website hat, vom Browser des Besuchers bis hinunter zur Datenbank, welche davon eingeschaltet ausgeliefert werden, wo jeder
liegt, welcher Server ihn nutzt und wie jeder geleert wird. Damit kann ein Hoster entscheiden, was ein Tarif einschaltet, und der
Support erkennen, woher eine veraltete oder langsame Seite kommt.

## Inhalt

1. [Die Schichten auf einen Blick](#die-schichten-auf-einen-blick)
2. [Vor dem Server](#vor-dem-server)
3. [Im Server, vor dem Kernel](#im-server-vor-dem-kernel)
4. [Im Kernel](#im-kernel)
5. [Unter dem Kernel](#unter-dem-kernel)
6. [Welche Schichten zwischen Servern geteilt werden](#welche-schichten-zwischen-servern-geteilt-werden)
7. [Die Header lesen](#die-header-lesen)
8. [Was ein Tarif einschalten kann](#was-ein-tarif-einschalten-kann)
9. [Checkliste](#checkliste)

## Die Schichten auf einen Blick

| # | Schicht | Ausgeliefert | Bedient | Geleert durch |
|---|---|---|---|---|
| 1 | Browser-Cache statischer Dateien | Velocity: `StaticMaxAge=31536000`; Apache: Ihr `mod_expires` | alle | Dateinamen, oder das Abwarten der Lebensdauer |
| 2 | Browser-Cache von Seiten | aus (`site.ini [HTTPHeaderSettings] CustomHeader=disabled`) | anonyme Besucher, wenn eingeschaltet | Lebensdauer |
| 3 | CDN oder zwischenspeichernder Proxy | nicht Teil von Exponential | wie Sie es konfigurieren | Ihren Proxy; Exponential sendet kein Leeren |
| 4 | Antwort-Cache von Velocity | an unter Velocity (`velocity.ini [CacheSettings] Enabled=enabled`) | anonyme Besucher | `exp:velocity cache clear` |
| 5 | Rollenbewusster HTTP-Cache | aus (`httpcache.ini [HttpCacheSettings] Enabled=disabled`) | anonyme und angemeldete Besucher, je Berechtigungskontext | bei Änderungen an Inhalten geleert; `--clear-id=exphttpcache` |
| 6 | Statischer Cache | aus (`site.ini [ContentSettings] StaticCache=disabled`) | anonyme Besucher, als Dateien | beim Veröffentlichen neu erzeugt |
| 7 | Content-View-Cache | an (`[ContentSettings] ViewCaching=enabled`) | jeden Besucher | Änderung an Inhalten; `--clear-tag=content` |
| 8 | Template-Block-Cache | an (`[TemplateSettings] TemplateCache=enabled`) | jeden Besucher | Ablauf; `--clear-id=template-block` |
| 9 | Kompilierte Templates, INI- und Override-Caches | an | jede Anfrage | `--clear-tag=template`, `--clear-tag=ini` |
| 10 | Redis oder Valkey für Views, Blöcke, INI | nur mit der Erweiterung `sevenx_valkey_cache` | jede Anfrage | das Leeren der Erweiterung; ihr Skript zum Leeren |
| 11 | SQL-Abfrage-Cache | aus (`querycache.ini [QueryCacheSettings] Mode=off`) | jede Anfrage, die er beantworten kann | Schreibvorgänge in die gelesenen Tabellen; `--clear-id=querycache` |
| 12 | OPcache, APCu | pro PHP-Prozess | jede Anfrage | Neuladen oder Neustart; Schaltflächen in Setup > Caches |

Quellen: [Caches](../../../../en/us/guides/caches.md), [Kapitel 10, Abschnitt 10.6](../../../../en/us/install/10-after-installing.md#106-caches) und
die unten verlinkten Seiten.

## Vor dem Server

**Browser.** Velocity sendet `Cache-Control` mit einer Lebensdauer von einem Jahr für Stylesheets, Skripte und Bilder
(`[ServerSettings] StaticMaxAge`); eine unter demselben Namen ersetzte Datei wird erst nach Ablauf erneut geholt. Seiten werden mit
`no-cache` gesendet, es sei denn, `[HTTPHeaderSettings]` ist nur für anonyme Besucher eingeschaltet. Siehe
[HTTP-Cache-Header](http-cache-header.md). Unter Velocity kann ein optionaler Navigations-Cache (ein Service Worker, `/index.js` und
`sw.js`) wiederholte Besuche im eigenen Browser des Besuchers beantworten
([Der Antwort-Cache, und dem Netzwerk aus dem Weg gehen](../../../../en/us/bc/6.0/response-cache-and-navigation.md)).

**CDN oder Proxy.** Exponential sendet keine Anfragen zum Leeren oder Sperren an einen Proxy; einem Proxy, der Seiten speichert, muss
gesagt werden, was er speichern soll und wie lange. Siehe [Varnish und Reverse Proxys](varnish-und-reverse-proxys.md) und [CDN](cdn.md).

## Im Server, vor dem Kernel

**Antwort-Cache von Velocity.** Hält gerenderte anonyme Seiten im Elternprozess und beantwortet wiederholte Anfragen, bevor ein Worker
gewählt wird: kein Fork, kein PHP, keine Datenbank. Standard-Lebensdauer 30 Sekunden, stale-while-revalidate 60 Sekunden; angemeldete
Besucher werden nie daraus bedient. Siehe [Antwort-Cache von Velocity](antwort-cache-von-velocity.md).

**Rollenbewusster HTTP-Cache.** Standardmäßig aus. Wenn an, hält er ganze Inhaltsansichten pro Berechtigungskontext (anonym, ein
Rollenkontext, den Benutzer mit denselben Rollen teilen, oder ein privater Kontext) und liefert sie aus, bevor der Kernel startet:
unter Apache oder PHP-FPM über einen frühen Ausstieg, der aus `config.php` eingebunden wird, unter Velocity aus dem Serverprozess. Er
leert bei Änderungen an Inhalten (standardmäßig `ContentChangePurges=all`). Gemessen auf einer Referenzinstallation, eine Seite eines
angemeldeten Benutzers unter Apache: 484 Anfragen pro Sekunde aus dem Cache gegenüber 4,3 gerenderten
([HTTP-Cache](../../../../en/us/bc/6.0/httpcache.md)).

**Statischer Cache.** Standardmäßig aus, und das Installationsbuch sagt, ihn auszulassen: Wenn er an war, erzeugte jede Veröffentlichung
jede betroffene Seite über HTTP innerhalb der Anfrage des Redakteurs neu. Siehe [Statischer Cache](statischer-cache.md).

## Im Kernel

| Cache | Was er enthält | Einstellung |
|---|---|---|
| Content-View-Cache | den gerenderten Inhalt jeder Ansicht eines Knotens | `site.ini [ContentSettings] ViewCaching` |
| Template-Block-Cache | `cache-block`-Fragmente von Templates | `[TemplateSettings] TemplateCache` |
| Kompilierte Templates | zu PHP kompilierte Templates | `[TemplateSettings] TemplateCompile` |
| INI-, Override-, Übersetzungs-, Design-Caches | zusammengeführte Einstellungen und Nachschlagewerte | immer an |
| Bild-Aliase | in der Größe geänderte Bilder, bei der ersten Ansicht erzeugt | `image.ini` |

Wenn eine Änderung die View-Caches von `[ContentSettings] CacheThreshold` Knoten oder mehr (250 mitgeliefert) leeren würde, wird
stattdessen jeder View-Cache der Website als abgelaufen markiert, und das Log meldet `Expiring all view cache`
([Caches, häufige Fragen](../../../../en/us/guides/caches.md#6-usual-questions)).

Mit der Erweiterung `sevenx_valkey_cache` können der Content-View-Cache, Template-Blöcke und der kompilierte INI-Cache statt in Dateien
in Redis oder Valkey liegen. Siehe [Valkey und Redis](valkey-und-redis.md).

## Unter dem Kernel

**SQL-Abfrage-Cache.** Standardmäßig aus. `Mode=request` entfernt wiederholte identische `SELECT`s innerhalb einer Anfrage;
`Mode=shared` hält Ergebnisse über Anfragen hinweg in APCu oder Dateien. Gemessen auf einer Testinstallation unter Apache mit PHP-FPM
ging eine gerenderte Startseite von etwa 0,6 s auf etwa 0,17 s, ohne dass eine Anweisung an die Datenbank ging
([SQL-Abfrage-Cache](../../../../en/us/bc/6.0/sql-query-cache.md)). Er funktioniert mit den SQL-Treibern (1. SQLite 3, 2. PostgreSQL,
4. MariaDB, 5. Oracle über `ezoracle`, 6. MySQL); der Treiber für 3. MongoDB nutzt ihn nicht. Schreibvorgänge außerhalb von Exponential
(eine Wiederherstellung, ein SQL-Client) sind für ihn unsichtbar: Leeren Sie ihn nach jeder Wiederherstellung.

**OPcache und APCu** gehören zu jedem PHP-Prozessbaum. Siehe [OPcache](opcache.md) und [Abstimmung von PHP-FPM](abstimmung-von-php-fpm.md#apcu).

## Welche Schichten zwischen Servern geteilt werden

Die Cache-Dateien liegen in `var/`, das sich Apache mit PHP-FPM und Velocity teilen: Das Leeren eines Caches in Setup > Caches erreicht
beide. Zwei Dinge nicht ([Caches](../../../../en/us/guides/caches.md#in-short)):

- Velocity hält Einstellungen im Speicher: Starten Sie es nach dem Leeren der INI-Caches neu.
- Der Antwort-Cache von Velocity beantwortet Seiten aus seinem eigenen Speicher: Leeren Sie ihn mit `exp:velocity cache clear`.

APCu und OPcache gelten pro Prozessbaum. Auf mehreren Web-Knoten werden Dateien in einem geteilten `var/` geteilt; APCu gilt pro Server
(dafür gibt es `Storage=file` des Abfrage-Caches). Siehe [Mehrere Web-Knoten](../betrieb/mehrere-web-knoten.md).

## Die Header lesen

| Header | Gesetzt von | Bedeutung |
|---|---|---|
| `X-Cache: HIT`, `STALE` | dem Antwort-Cache von Velocity | aus dem Cache beantwortet, oder aus einer abgelaufenen Kopie, während eine neue gerendert wird |
| `Age` | dem Antwort-Cache von Velocity | Sekunden, seit der Eintrag gespeichert wurde |
| `X-Exp-Cache: HIT`, `STALE`, `MISS (reason)`, `BYPASS (reason)` | dem rollenbewussten HTTP-Cache | was er getan hat und warum |

```bash
curl -sI https://example.com/ | grep -iE 'x-cache|x-exp-cache|age|cache-control'
```

Eine Seite, die der Antwort-Cache von Velocity beantwortet, trägt auch das mit ihr gespeicherte `X-Exp-Cache`, oft `MISS` aus dem
Rendering, das beide Caches gefüllt hat ([HTTP-Cache, sehen, was er tut](../../../../en/us/bc/6.0/httpcache.md#seeing-what-it-does)).

## Was ein Tarif einschalten kann

| Schicht | Option im Tarif | Achten Sie auf |
|---|---|---|
| Antwort-Cache von Velocity | an bei Tarifen mit Velocity | `DefaultTtl` ist, wie veraltet eine anonyme Seite sein darf |
| Rollenbewusster HTTP-Cache | eine Option für Websites mit vielen angemeldeten Besuchern | Sitzungen müssen in Dateien liegen, die jeder Server lesen kann |
| SQL-Abfrage-Cache | `request` überall; `shared` mit bemessenem APCu | nach Wiederherstellungen leeren |
| Redis oder Valkey | eine Option, wo Sie den Server betreiben | dimensionieren, eine Verdrängungsrichtlinie setzen |
| Seiten-Header für Browser und CDNs | eine Option für Websites mit überwiegend anonymen Besuchern | immer `OnlyForAnonymous=enabled` |
| Statischer Cache | nicht empfohlen | Kosten beim Veröffentlichen |

## Checkliste

- [ ] Der Tarif sagt, welche Schichten an sind und wer sie leert.
- [ ] Der Support kennt die Header oben und die Reihenfolge beim Leeren ([Caches leeren und vorwärmen](caches-leeren-und-vorwaermen.md)).
- [ ] Caches mit gerenderter Ausgabe werden erst geleert, wenn neuer Code läuft.
- [ ] Der Abfrage-Cache wird nach jeder Wiederherstellung der Datenbank geleert.
- [ ] Kein Proxy und kein CDN speichert Seiten, die von einem Sitzungscookie abhängen.

## Verwandte Seiten

- [Antwort-Cache von Velocity](antwort-cache-von-velocity.md)
- [HTTP-Cache-Header](http-cache-header.md)
- [Caches leeren und vorwärmen](caches-leeren-und-vorwaermen.md)
- [Caches](../../../../en/us/guides/caches.md)
- [Cache-Steuerung aus der Konsole](../../../../en/us/bc/6.0/cache-console.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
