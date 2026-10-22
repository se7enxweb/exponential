---
title: "Caching: die Ebenen und wie man sie invalidiert"
audience: web-framework-developer, developer
category: web-frameworks
order: 280
summary: Die Caches von Exponential für Entwickler, die eine Cache-Komponente mit Pools und Tags kennen - kompilierte Templates, INI- und Override-Caches, der Content-View-Cache pro Rolle, Template-Cache-Blöcke, Daten-Caches über den Cluster-Datei-Handler, der rollenbewusste HTTP-Cache und der Velocity-Response-Cache - was jeder enthält, was ihn leert und die zugehörigen Befehle.
keywords: Cache, Caching, View-Cache, Cache leeren, Invalidierung, Template-Cache, cache, caching, view cache, ViewCaching, cache-block, template-block, eZClusterFileHandler, processCache, eZCache, ezcache.php, clear-tag, clear-id, exp:cache, httpcache, Velocity response cache, invalidation, Symfony Cache, Laravel Cache, Django cache, Web-Framework-Entwickler, web framework developer
type: help
---
# Caching: die Ebenen und wie man sie invalidiert

Diese Seite richtet sich an Entwickler, die eine Cache-Komponente verwenden (Symfony Cache, `Cache` von Laravel, das
Cache-Framework von Django, `Rails.cache` von Rails). Exponential hat mehrere Caches, jeder mit einer ID und Tags,
verwaltet von `eZCache` (`kernel/classes/ezcache.php`), und die meisten davon invalidiert der Kernel, wenn Inhalte
veröffentlicht werden. Der übliche Grund für „meine Änderung erscheint nicht“ ist eine dieser Ebenen
([Caches](../../standards/architektur/kernel-architektur.md#caches)).

## Inhalt

1. [Die Ebenen](#die-ebenen)
2. [Leeren](#leeren)
3. [Fragment-Caching in Templates](#fragment-caching-in-templates)
4. [Daten in PHP cachen](#daten-in-php-cachen)
5. [Unter persistenten Workern](#unter-persistenten-workern)
6. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Die Ebenen

| Ebene | Enthält | Geleert durch |
|---|---|---|
| Kompilierte Templates (`template`) | zu PHP kompilierte `.tpl` | Template-Änderungen, `--clear-tag=template` |
| Template-Override-Cache (`template-override`) | auf welche Datei eine Override-Regel auflöst | eine neue Template-Datei erfordert das Leeren |
| INI-Caches (`ini`, `global_ini`) | geparste Einstellungen | `--clear-tag=ini` |
| Content-View-Cache (`content`) | gerenderte Inhalts-Views, pro Berechtigungskontext | Veröffentlichen des Knotens und verwandter Inhalte (`eZContentCacheManager`) |
| Template-Block-Cache (`template-block`) | `{cache-block}`-Fragmente | Veröffentlichen (standardmäßig), Ablauf |
| Rollenbewusster HTTP-Cache (`exphttpcache`) | ganze Seiten pro Berechtigungskontext; aus, bis `httpcache.ini Enabled=enabled` | Inhaltsänderungen ([Der rollenbewusste HTTP-Cache](../../bc/6.0/httpcache.md)) |
| Velocity-Response-Cache | öffentliche Seiten im Serverprozess; aus, bis er aktiviert wird | eigene Regeln, `./console exp:velocity cache clear` ([Velocity-Response-Cache](../../funktionen/6.0/velocity-antwort-cache.md)) |
| Weitere | `urlalias`, `imagealias`, `translation`, `design_base`, `user_info_cache`, `querycache` ... | ihre IDs und Tags |

Das Caching von Inhalts-Views steuert `site.ini [ContentSettings] ViewCaching` (mitgeliefert: `enabled`). Die
vollständige Liste mit IDs und Tags: [Anleitung zu Caches](../../anleitungen/caches.md).

## Leeren

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=template-override --allow-root-user
./console exp:cache status
./console exp:velocity deploy          # the usual way after a code change: everything in the right order
```

`exp:velocity deploy` leert die Caches in einer Reihenfolge, die so gewählt ist, dass eine von altem Code gerenderte
Seite nie erneut gecacht wird ([Server und Deployment](server-und-deployment.md)). Eine Erweiterung kann ihren
eigenen Cache mit `site.ini [Cache] CacheItems[]` und einem `[Cache_<id>]`-Block in die Liste aufnehmen, sodass
`ezcache.php` und **Setup > Caches** ihn leeren können.

## Fragment-Caching in Templates

```text
{cache-block keys=array( 'expdemo-stats', $current_user.contentobject_id ) expiry=300}
  {def $stats = fetch( 'expdemo', 'stats', hash() )} ... {undef $stats}
{/cache-block}
```

Daten aus Ihren eigenen Tabellen lösen das Leeren beim Veröffentlichen nicht aus: Wählen Sie ein `expiry`, oder
verwenden Sie `ignore_content_expiry` und leeren Sie den `template-block`-Cache selbst, wenn sich Ihre Daten ändern.
Bilden Sie den Schlüssel aus allem, wovon die Ausgabe abhängt, einschließlich des Benutzers, wenn das Fragment
persönlich ist ([Template-Caches](../template-sprache/template-caches.md)).

## Daten in PHP cachen

Im Kernel gibt es keinen Cache-Pool nach PSR-6 oder PSR-16. Aufwendige Ergebnisse werden über den
Cluster-Datei-Handler gespeichert, sodass sie auf einem einzelnen Server und in einem Cluster funktionieren:

```php
$file = eZClusterFileHandler::instance( eZSys::cacheDirectory() . '/expdemo/stats.cache' );
$stats = $file->processCache(
    function ( $path, $mtime ) { return json_decode( (string)file_get_contents( $path ), true ); },
    function ( $path ) {
        $stats = expDemoStats::compute();
        return array( 'content' => $stats, 'binarydata' => json_encode( $stats ), 'scope' => 'expdemo-stats' );
    },
    300 );
```

Während ein Prozess die Daten erzeugt, warten die anderen, statt dieselben Daten ebenfalls zu erzeugen
([Caching in Anwendungen](../ersteller/anwendungsentwickler/caching-in-anwendungen.md)). Cache-Dateien müssen über
`eZClusterFileHandler::instance()` geschrieben werden, nicht mit direkten Dateiaufrufen. Mit der Erweiterung
`sevenx_valkey_cache` hält eine Installation den Content-View-Cache und den kompilierten INI-Cache in Valkey oder
Redis ([sevenx_valkey_cache](../../funktionen/6.0/erweiterungen/sevenx_valkey_cache.md)).

## Unter persistenten Workern

Velocity hält PHP-Prozesse zwischen Requests am Leben: Eine statische Variable überlebt bis in den nächsten Request.
Halten Sie niemals benutzerspezifische Daten in statischen Variablen; der Prozessspeicher wird zwischen Workern nicht
geteilt, er ist also auch kein gemeinsamer Cache.

## Im Vergleich mit anderen Frameworks

| Framework | Konzept | Exponential |
|---|---|---|
| Symfony | Cache-Pools, Tags, HTTP-Cache mit ESI | Cache-IDs und Tags in `eZCache`, der View-Cache, die HTTP-Caches |
| Laravel | `Cache::remember()` | `processCache()` mit einem Erzeugungs-Callback |
| Django | Cache pro View, Template-Fragment-Cache | Content-View-Cache, `{cache-block}` |
| Rails | Fragment- und Russian-Doll-Caching | `{cache-block}` mit Schlüsseln |

Der entscheidende Unterschied: Inhalts-Caches werden durch das Veröffentlichen invalidiert, automatisch und pro
Knoten; Ihre eigenen Daten brauchen einen expliziten Ablauf oder ein explizites Leeren.

## Verwandte Seiten

- [HTTP-Caching und Reverse-Proxies](http-caching-und-reverse-proxies.md)
- [View-Cache und Cache-Blöcke](../ersteller/website-ersteller/view-cache-und-cache-bloecke.md)
- [Cache-Konsole](../../bc/6.0/cache-konsole.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
