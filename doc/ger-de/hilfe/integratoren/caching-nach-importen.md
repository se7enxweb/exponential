---
title: Caching nach Importen
audience: integrator, developer, admin
category: integration
order: 360
summary: Welche Caches ein Import oder eine Synchronisation berührt und welche nicht - was der Veröffentlichungsvorgang selbst leert, wann mehr zu leeren ist, die Cache-IDs von ezcache.php, der HTTP-Cache und der Antwort-Cache von Velocity, Caches nach Änderungen an Einstellungen und Code und CDNs vor der Website.
keywords: Cache, Caching, ezcache.php, clear-id, clear-tag, Inhalts-Cache, View-Cache, template-block, exphttpcache, querycache, rss_cache, urlalias, Antwort-Cache von Velocity, exp:velocity cache clear, eZContentCacheManager, clearContentCacheIfNeeded, CDN-Purge, Import, cache, content cache, view cache, Velocity response cache, CDN purge, import
type: help
---
# Caching nach Importen

Diese Seite ist für Integratoren und Betreiber, die nach einem Import alte Inhalte sehen oder genau so viel leeren
möchten wie nötig und nicht mehr. Exponential cacht auf mehreren Ebenen. Inhalte, die über die unterstützten
Funktionen geändert werden, leeren ihre eigenen Caches; auf anderem Weg geänderte Inhalte, Einstellungen, Code und
äußere Caches brauchen Nachhilfe. Die Übersicht über jeden Cache ist [Caches](../../anleitungen/caches.md).

## Inhalt

1. [Was das Veröffentlichen selbst leert](#was-das-veröffentlichen-selbst-leert)
2. [Wann mehr zu leeren ist](#wann-mehr-zu-leeren-ist)
3. [Die Cache-IDs](#die-cache-ids)
4. [Äußere Caches](#äußere-caches)
5. [Nach Änderungen an Einstellungen und Code](#nach-änderungen-an-einstellungen-und-code)
6. [Rezepte](#rezepte)

## Was das Veröffentlichen selbst leert

`eZContentFunctions::createAndPublishObject()`, `updateAndPublishObject()`, die REST-Route zum Anlegen und die
Schreibvorgänge von `expservices` durchlaufen alle den Veröffentlichungsvorgang, der über `eZContentCacheManager` die
View-Caches der Knoten des Objekts und der davon abhängigen Knoten leert (übergeordnete, verwandte Objekte, laut
`viewcache.ini`) und für Listener das Ereignis `content/cache` meldet
([Event-Listener](event-listener-fuer-integrationen.md)).

> **Hinweis**
> Wenn eine Änderung mehr Knoten leeren würde, als die Aufräumschwelle erlaubt, leert der Kernel stattdessen **jeden**
> View-Cache, schreibt eine Warnung ins Protokoll und meldet `content/cache/all`. Ein großer Import kann daher die
> nächsten Aufrufe überall langsamer machen; führen Sie große Importe zu ruhigen Zeiten aus.

## Wann mehr zu leeren ist

| Situation | Was zu leeren ist |
|---|---|
| Inhalte wurden außerhalb des Veröffentlichungsvorgangs geändert (direktes SQL, eine Reparatur) | Die View-Caches der Objekte: `eZContentCacheManager::clearContentCacheIfNeeded( $objectID )` in PHP oder `expobject::expireCache` über `expservices`; besser ändern Sie Inhalte gar nicht auf diese Weise |
| Template-Blöcke (`cache-block`) zeigen Listen importierter Inhalte | `template-block`, oder auf ihr Ablaufen vertrauen |
| Menüs zeigen importierte Ordner | `content_tree_menu` (Baum der Administration), `template-block` |
| RSS-Exporte müssen importierte Einträge sofort zeigen | `rss_cache` |
| Importierte Objekte haben neue URL-Aliase bekommen | Meist nichts; `urlalias` nur nach einer Reparatur |
| Klassen oder Klassenattribute wurden geändert (ein neues Klassenpaket) | `classid`, `sortkey` und die Inhalts-View-Caches |
| Suchergebnisse sind veraltet | Kein Cache: den Index neu aufbauen ([Anbindung einer Suchmaschine](anbindung-einer-suchmaschine.md#indizieren-nach-importen)) |

## Die Cache-IDs

`php bin/php/ezcache.php --list-ids --allow-root-user` listet sie auf. Die, denen eine Integration am häufigsten
begegnet:

| ID | Enthält |
|---|---|
| `content` | View-Caches der Inhalte |
| `template-block` | Ausgabe von Template-`cache-block`s |
| `exphttpcache` | Den HTTP-Cache ganzer Seiten |
| `querycache` | Den SQL-Abfrage-Cache |
| `rss_cache` | Geschriebene RSS-Exporte |
| `urlalias` | Nachschlagen von URL-Aliasen |
| `classid`, `sortkey` | Nachschlagen von Klassen |
| `content_tree_menu` | Das Baummenü der Administration |
| `ini`, `global_ini` | Einstellungen (leeren mit `--clear-tag=ini`) |
| `template`, `template-override` | Kompilierte Templates und den Override-Index |

```bash
php bin/php/ezcache.php --clear-id=content,template-block --allow-root-user
php bin/php/ezcache.php --clear-tag=content --allow-root-user
php bin/php/console exp:cache clear --id=content --dry-run --allow-root-user   # what would be cleared, with sizes
```

Vermeiden Sie `--clear-all` auf einer stark besuchten Produktionswebsite nach einem Import: Es leert jeden Cache auf
einmal, und jeder Besucher bezahlt für den Neuaufbau.

## Äußere Caches

| Ebene | Leeren |
|---|---|
| Der Antwort-Cache von Velocity (wenn Velocity die Website ausliefert) | `php bin/php/console exp:velocity cache clear --apply` |
| Der HTTP-Cache | Cache-ID `exphttpcache` ([HTTP-Cache](../../bc/6.0/httpcache.md)) |
| Ein CDN oder Reverse Proxy, den Sie betreiben | Seine eigene Purge-API, angestoßen durch `content/cache`-Ereignisse ([Beispiel](event-listener-fuer-integrationen.md#beispiel-ein-cdn-leeren-wenn-caches-geleert-werden)) |
| Browser | Cache-Header Ihrer Seiten; nichts zu leeren |

Der statische Cache ist in den mitgelieferten Einstellungen ausgeschaltet; hat eine Website ihn eingeschaltet, müssen
auch seine Dateien neu erzeugt werden ([Generator für den statischen Cache](../../funktionen/6.0/generator-fuer-den-statischen-cache.md)).

## Nach Änderungen an Einstellungen und Code

Eine neue Version Ihrer Integrationserweiterung zu deployen ist kein Import und braucht mehr:

1. `php bin/php/ezpgenerateautoloads.php -e`, wenn Klassen hinzugefügt oder umbenannt wurden.
2. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` nach Änderungen an Einstellungen (nicht
   `--clear-id=ini`, das den globalen INI-Cache stehen lässt).
3. PHP-FPM neu laden und Velocity neu starten: Die Worker von Velocity behalten die Klassen, die sie beim Start
   geladen haben.
4. Dann den Inhalts-View-Cache und den Antwort-Cache von Velocity leeren, **nach** dem Neuladen, damit keine vom alten
   Code erzeugte Seite erneut gecacht wird.

`php bin/php/console exp:velocity deploy` erledigt diese Schritte in der richtigen Reihenfolge
([Deployen](../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern)).

## Rezepte

```bash
# after a nightly product import that publishes through eZContentFunctions: usually nothing; lists in cache-blocks:
php bin/php/ezcache.php --clear-id=template-block --allow-root-user

# after installing a package with classes and content (indexing was delayed)
php bin/php/updatesearchindex.php --allow-root-user
php bin/php/ezcache.php --clear-id=content,template-block,classid,sortkey --allow-root-user
php bin/php/console exp:velocity cache clear --apply

# after a repair that changed content tables directly
php bin/php/ezcache.php --clear-id=content,template-block,urlalias --allow-root-user
```

## Verwandte Seiten

- [Caches](../../anleitungen/caches.md)
- [Cache-Tags und Leeren](../erweiterungsentwicklung/teil-8-referenz/cache-tags-und-leeren.md)
- [Caching in Anwendungen](../ersteller/anwendungsentwickler/caching-in-anwendungen.md)
- [Velocity und Performance für Integrationen](velocity-und-leistung-fuer-integrationen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
