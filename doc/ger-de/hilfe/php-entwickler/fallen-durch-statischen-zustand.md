---
title: Fallen durch statischen Zustand
audience: php-developer, developer
category: php-development
order: 440
summary: Eine Erklärung der Fehler, die aus Zustand entstehen, der zwischen Anfragen oder Tests erhalten bleibt - statische Variablen in Funktionen, Flags für einmaliges Registrieren, außerhalb des Klassenrumpfs zugewiesene statische Eigenschaften, nach Zeit geschlüsselte Caches pro Anfrage, zu lange gehaltene Einstellungen und Singletons - mit den eigenen Beispielen des Kernels und den Lösungen, für Velocity-Worker, lange CLI-Läufe und PHPUnit gleichermaßen.
keywords: statischer Zustand, statische Eigenschaften, Singleton, einmal registrieren, persistenter Worker, Speicherleck, lange CLI-Läufe, static state, statics, function static, singleton, register once, requestKey, REQUEST_TIME, class static reset, declared default, persistent worker, Velocity, PHPUnit isolation, memory leak, CLI long runs, clearCache, eZINI instance
type: help
---
# Fallen durch statischen Zustand

Diese Seite richtet sich an PHP-Entwickler, die irgendetwas in statischen Eigenschaften, statischen Variablen von
Funktionen oder globalen Variablen halten. Der Exponential-Kernel verwendet statischen Zustand häufig: Singletons
(`eZINI::instance()`, `eZDB::instance()`, `ezpEvent::getInstance()`) und In-Memory-Caches. Unter PHP-FPM verschwindet
dieser Zustand mit der Anfrage. Drei Situationen halten ihn am Leben: ein Velocity-Worker, der viele Anfragen bedient,
ein langer Kommandozeilenlauf und ein PHPUnit-Lauf mit vielen Tests in einem Prozess. Jede der folgenden Fallen hat den
Kernel selbst getroffen.

## Inhalt

1. [Wo Zustand überlebt](#wo-zustand-überlebt)
2. [Falle 1: statische Variablen in Funktionen](#falle-1-statische-variablen-in-funktionen)
3. [Falle 2: Flags für einmaliges Registrieren](#falle-2-flags-für-einmaliges-registrieren)
4. [Falle 3: außerhalb der Klasse zugewiesene statische Eigenschaften](#falle-3-außerhalb-der-klasse-zugewiesene-statische-eigenschaften)
5. [Falle 4: zu knapp geschlüsselte Caches](#falle-4-zu-knapp-geschlüsselte-caches)
6. [Falle 5: beim Warm-up erfasste Werte](#falle-5-beim-warm-up-erfasste-werte)
7. [Falle 6: Caches, die nur wachsen](#falle-6-caches-die-nur-wachsen)
8. [Wie Sie sie finden](#wie-sie-sie-finden)

## Wo Zustand überlebt

| Situation | Erhalten zwischen | Zurückgesetzt |
|---|---|---|
| Velocity-Worker | Anfragen verschiedener Benutzer, Siteaccesses, Sprachen | statische Klasseneigenschaften auf ihre Werte nach dem Warm-up; Shutdown-Funktionen und Handler pro Anfrage ([Persistente Velocity-Worker für PHP-Entwickler](persistente-velocity-worker-fuer-php-entwickler.md)) |
| ein Befehl, der über viele Elemente läuft | Durchläufen | nichts, sofern Sie nicht leeren |
| ein Cronjob-Lauf | den Skripten eines Teils | nichts |
| PHPUnit | Tests | nichts, sofern der Test nicht zurücksetzt |

## Falle 1: statische Variablen in Funktionen

```php
// wrong: survives in a worker, and nothing resets function statics the way class statics are reset
function acmeAssetPaths()
{
    static $paths = null;
    if ( $paths === null )
        $paths = acmeComputePathsForCurrentSiteaccess();
    return $paths;
}
```

Der Kernel hatte genau das: Statische Variablen in Funktionen trugen die Asset-Pfade eines Siteaccess in die nächste
Anfrage. Die Lösung war, sie in statische Klasseneigenschaften zu verlagern, die der Worker zurücksetzt, und den Cache
nach dem zu schlüsseln, wovon er abhängt (Falle 4).

## Falle 2: Flags für einmaliges Registrieren

```php
// wrong: the flag is set during the warm-up and stays true; later requests register nothing
if ( !self::$registered )
{
    self::$registered = true;
    register_shutdown_function( array( __CLASS__, 'flush' ) );
}

// right: once per request
if ( self::$registered !== eZExecution::requestKey() )
{
    self::$registered = eZExecution::requestKey();
    register_shutdown_function( array( __CLASS__, 'flush' ) );
}
```

Den umgekehrten Fehler gibt es auch: Ein Listener, der bei jeder Anfrage ohne Absicherung angehängt wurde, lief zweimal.
Machen Sie Registrierungen idempotent, oder legen Sie Listener in die Einstellungen.

## Falle 3: außerhalb der Klasse zugewiesene statische Eigenschaften

```php
// wrong
class acmeFooServices extends expServiceBase
{
    public static $services = array();
}
acmeFooServices::$services = array( 'bar' => array( ... ) );   // runs only when the file is first included

// right
class acmeFooServices extends expServiceBase
{
    public static $services = array( 'bar' => array( ... ) );
}
```

Ein Worker setzt statische Eigenschaften zwischen Anfragen auf ihren **deklarierten** Wert zurück, und `include_once`
führt die Zuweisung auf Dateiebene nicht erneut aus. In `expservices` des Kernels haben zehn Domänen ihre Dienstlisten
bis zum 12.10.2026 auf diese Weise deklariert: Ab der zweiten Anfrage antwortete jeder Dienst dieser Klassen auf diesem
Worker mit „not declared“ (404) ([Backend-Dienste über ezjscore](../../bc/6.0/backend-dienste-ueber-ezjscore.md)).

## Falle 4: zu knapp geschlüsselte Caches

Ein statischer Cache muss nach allem geschlüsselt sein, wovon der Wert abhängt: dem Siteaccess, der Sprache, dem
Benutzer oder der Rolle, der Anfrage. Werte, die unter einem Schlüssel zwischengespeichert werden, der eines davon
ignoriert, sickern zwischen Anfragen durch:

```php
$key = $GLOBALS['eZCurrentAccess']['name'] . '|' . $languageCode . '|' . $nodeID;
```

Kernel-Caches pro Anfrage verwenden den Schlüssel der Anfrage, der vom `REQUEST_TIME` pro Anfrage abhängt; deshalb
brauchte der Kernel das Engine-Release 0.0.4.42 von Velocity, das `REQUEST_TIME` und `REQUEST_TIME_FLOAT` pro Anfrage
gemacht hat ([Hinweise zum Upgrade der Velocity-Engine](../../bc/6.0/velocity-engine-upgrade-hinweise.md)).

## Falle 5: beim Warm-up erfasste Werte

Alles, was das Warm-up berechnet, erbt jeder Worker: Das Warm-up hinterließ einmal die Sprache der öffentlichen Site,
ihre Einstellung für versteckte Knoten und ihre Template-Pfade in jedem Worker, bis der Kernel sie zurücksetzte.
Berechnen Sie anfrageabhängige Werte nicht in Code, der beim Laden einer Klasse oder im Warm-up läuft; berechnen Sie sie,
wenn eine Anfrage sie braucht.

## Falle 6: Caches, die nur wachsen

In einem langen Befehl wachsen die In-Memory-Caches des Kernels (Inhaltsobjekte, Knoten, Attribute) mit jedem Element:

```php
foreach ( $batches as $batch )
{
    acmeReportService::process( $batch );
    eZContentObject::clearCache();      // drop the in-memory object cache between batches
}
```

Ihre eigenen statischen Caches brauchen eine Grenze oder eine Methode zum Zurücksetzen, die die Schleife aufruft.

## Wie Sie sie finden

1. Fragen Sie unter Velocity dieselbe Seite zweimal hintereinander ab, als zwei verschiedene Benutzer, in zwei
   Siteaccesses, in zwei Sprachen.
2. Vergleichen Sie mit dem eingebauten Server von PHP (`--engine=php`), der jede Anfrage frisch beginnt: Ein Unterschied
   ist erhaltener Zustand.
3. Führen Sie in PHPUnit die verdächtige Testdatei allein und zusammen mit anderen aus; ein Paar, das nur gemeinsam
   fehlschlägt, teilt sich Zustand.
4. Durchsuchen Sie Ihren Code: `grep -rn "static \$" extension/acmereport/` und prüfen Sie jede Stelle anhand dieser Seite.

## Verwandte Seiten

- [Persistente Velocity-Worker für PHP-Entwickler](persistente-velocity-worker-fuer-php-entwickler.md)
- [Das Muster Singleton](../../standards/muster/singleton.md)
- [Das Muster Registry](../../standards/muster/registry.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
