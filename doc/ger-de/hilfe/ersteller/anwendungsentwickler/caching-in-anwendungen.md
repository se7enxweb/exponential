---
title: Caching in Anwendungen
audience: app-creator, developer
category: app-creation
order: 610
summary: Die Arbeit einer Anwendung richtig cachen - welche Caches der Kernel hat, Cache-Blöcke für Template-Ausgaben, berechnete Daten in PHP mit eZClusterFileHandler und Ablaufzeit cachen, bei Änderungen leeren, Caches unter den persistenten Workern von Velocity und ein eigener Cache in der Cache-Liste.
keywords: Caching, Cache-Block, View-Cache, Ablaufzeit, Cache leeren, Cache-IDs, Tags, persistente Worker, statische Variablen, Anwendungsentwickler, caching, cache-block, view cache, eZClusterFileHandler, expiry, cache clear, ezcache, cache ids, tags, persistent workers, static variables, app creator
type: help
---
# Caching in Anwendungen

Diese Seite ist für Anwendungsentwickler, die berechnete Daten und gerenderte Ausgaben cachen. Caching macht Seiten
schnell; ein falscher Schlüssel oder ein fehlendes Leeren zeigt einem Benutzer die Daten eines anderen oder hält
veraltete Daten für immer fest.

## Inhalt

1. [Die Caches des Kernels](#die-caches-des-kernels)
2. [Template-Ausgabe: Cache-Blöcke](#template-ausgabe-cache-blöcke)
3. [Berechnete Daten in PHP](#berechnete-daten-in-php)
4. [Leeren](#leeren)
5. [Unter persistenten Workern](#unter-persistenten-workern)

## Die Caches des Kernels

| Cache | Wird geleert bei | Ihre Aufgabe |
|---|---|---|
| View-Cache für Inhalte | Veröffentlichen des Knotens und verwandter Inhalte | Ansichten von Inhalten werden je nach Rollen des Benutzers gecacht; die Ausgabe Ihres eigenen Ansichtsmoduls nicht |
| Template-Block-Cache | Veröffentlichen (standardmäßig), Ablaufzeit | Schlüssel und Ablaufzeit Ihrer Blöcke |
| Kompilierte Templates, Overrides, INI | Änderungen an Templates und Einstellungen | nach Deployments leeren |
| Antwort-Cache von Velocity | eigene Regeln und ausdrückliches Leeren | nur anonyme Seiten |

Die vollständige Liste mit IDs und Tags: [10.6 Caches](../../../installation/10-nach-der-installation.md#106-caches).

## Template-Ausgabe: Cache-Blöcke

```text
{cache-block keys=array( 'expdemo-stats', $current_user.contentobject_id ) expiry=300}
  {def $stats = fetch( 'expdemo', 'stats', hash() )} ... {undef $stats}
{/cache-block}
```

Daten aus Ihren eigenen Tabellen lösen das Leeren beim Veröffentlichen nicht aus: Wählen Sie eine `expiry`, die zu der
erlaubten Veraltung passt, oder fügen Sie `ignore_content_expiry` hinzu und leeren Sie den Cache `template-block`
selbst, wenn sich Ihre Daten ändern
([Template-Funktionen, Includes und Cache-Blöcke](template-funktionen-und-includes.md#cache-blöcke)).

## Berechnete Daten in PHP

Speichern Sie aufwendige Ergebnisse über den Cluster-Datei-Handler des Kernels, damit sie auf einem einzelnen Server
und in einem Cluster funktionieren:

```php
$file = eZClusterFileHandler::instance( eZSys::cacheDirectory() . '/expdemo/stats.cache' );
$stats = $file->processCache(
    function ( $path, $mtime ) {                                          // read a valid cache file
        return json_decode( (string)file_get_contents( $path ), true );
    },
    function ( $path ) {                                                  // generate when expired or missing
        $stats = expDemoStats::compute();
        return array( 'content' => $stats, 'binarydata' => json_encode( $stats ), 'scope' => 'expdemo-stats' );
    },
    300 );                                                                // $ttl: seconds from storing
```

`processCache( $retrieveCallback, $generateCallback = null, $ttl = null, $expiry = null, $extraData = null )` ist in
`kernel/classes/clusterfilehandlers/ezfsfilehandler.php` dokumentiert: Der Lese-Callback erhält den Pfad und die mtime,
der Erzeugungs-Callback den Pfad und gibt `content` (das Sie zurückbekommen) und `binarydata` (das in die Datei
geschrieben wird) zurück; während ein Prozess erzeugt, warten die anderen, statt dieselben Daten zu erzeugen.

## Leeren

- Leeren Sie, was Sie gecacht haben, wenn sich seine Quelle ändert: in dem Code, der sie ändert, oder in einem
  Workflow-Ereignis oder Listener.
- Geben Sie Ihrem Cache ein eigenes Verzeichnis, damit er sich allein entfernen lässt.
- Eine Erweiterung kann ihren Cache in die Cache-Liste aufnehmen (`site.ini [Cache] CacheItems[]` und ein Block
  `[Cache_<id>]`, wie es der mitgelieferte Block `[Cache_rest]` tut), sodass `ezcache.php --clear-id=<id>` und
  **Setup > Caches** ihn leeren können, so wie es die Blöcke `[Cache_*]` des Kernels ermöglichen.

## Unter persistenten Workern

Velocity hält PHP-Prozesse zwischen Anfragen am Leben: Eine statische Variable oder ein Singleton überlebt bis in die
nächste Anfrage. Halten Sie niemals benutzerspezifische Daten in statischen Variablen; setzen Sie den Zustand pro
Anfrage zurück oder legen Sie ihn unter einem Schlüssel der Anfrage ab. Der Prozessspeicher wird nicht zwischen Workern geteilt, er ist
also auch kein gemeinsamer Cache
([Persistenter Worker-Server Velocity](../../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)).

## Verwandte Seiten

- [View-Cache und Cache-Blöcke](../website-ersteller/view-cache-und-cache-bloecke.md)
- [Caches](../../../anleitungen/caches.md)
- Caching-Kochbuch (`doc/eng-us/examples/cookbook/caching.md`)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
