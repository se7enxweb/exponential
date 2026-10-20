---
title: Ereignisse, Listener und Filter
audience: developer, app-creator
category: extension-development
order: 280
summary: Auf den Kernel reagieren, ohne ihn zu ändern - ezpEvent, [Event] Listeners[], Notify- und Filter-Ereignisse, die Ereignisse, die der Kernel ankündigt, und die Regel, dass ein Filter seinen Wert zurückgibt.
keywords: Ereignis, Listener, Filter, Benachrichtigung, Systemverhalten, event, listener, filter, ezpEvent, notify, attach, site.ini Event Listeners, response/output, content/cache, content/state/assign, module/functionlist, content/view/cachekeys, runnable events, cronjob/part/run
type: help
---
# Ereignisse, Listener und Filter

Teil 3, Kapitel 28. Der leichteste Weg, das Verhalten des Systems zu ändern, ist es, auf ein Ereignis zu **hören**,
das der Kernel ankündigt. Nichts wird ersetzt, nichts überschrieben; Ihre Funktion wird einfach aufgerufen. Dieses
Kapitel erklärt das Ereignissystem `ezpEvent` (`kernel/private/classes/ezpevent.php`) und listet die Ereignisse auf,
die Sie verwenden können.

## Zwei Arten

| Art | Der Kernel ruft auf | Ihr Listener |
|---|---|---|
| **notify** | `ezpEvent::getInstance()->notify( $name, $params )` | wird mit den Parametern aufgerufen; sein Rückgabewert wird ignoriert |
| **filter** | `ezpEvent::getInstance()->filter( $name, $value, ... )` | erhält den Wert (und weitere Argumente) und **muss ihn zurückgeben**, geändert oder nicht |

Ein Filter-Listener, der vergisst, den Wert zurückzugeben, zerstört ihn: die Seite, die Cache-Schlüssel, die Liste der
Funktionen. Das ist die eine Regel, die Sie sich merken müssen.

## Einen Listener anhängen

In der Datei `settings/site.ini.append.php` Ihrer Erweiterung:

```php
<?php /* #?ini charset="utf-8"?

[Event]
Listeners[]=content/state/assign@mysiteHelloListeners::stateAssigned
Listeners[]=response/output@mysiteHelloListeners::output

*/ ?>
```

Die Form ist `<event>@<callback>`, der Callback eine Funktion oder `Class::method`. Ein fehlerhafter Eintrag, ein
unbekannter Callback oder derselbe Eintrag zweimal wird übersprungen bzw. nur einmal angehängt, mit einem Hinweis im
Protokoll (`ezpEvent::registerEventListeners()`). `[Event] Listeners[]` wird für Webanfragen angehängt; die Ereignisse
der Runnables weiter unten werden überall angehängt.

## Die Listener-Klasse

```php
<?php
/**
 * Event listeners of mysite_hello, attached in settings/site.ini.append.php:
 *   [Event]
 *   Listeners[]=content/state/assign@mysiteHelloListeners::stateAssigned
 *   Listeners[]=response/output@mysiteHelloListeners::output
 */
class mysiteHelloListeners
{
    /** notify: called with ( $objectID, $selectedStateIDList ) after object states were assigned */
    public static function stateAssigned( $objectID, $stateIDList )
    {
        eZLog::write( sprintf( 'Object %d got states %s', (int)$objectID, implode( ',', array_map( 'intval', (array)$stateIDList ) ) ),
                      'mysite_hello.log' );
    }

    /** filter: receives the page and MUST return it (changed or not) */
    public static function output( $html )
    {
        if ( !is_string( $html ) )
            return $html;
        return str_replace( '</body>', "<!-- served with mysite_hello -->\n</body>", $html );
    }
}
```

An eine frische Instanz von `ezpEvent` bei geladenem Kernel angehängt, gab
`filter( 'response/output', '<html><body>x</body></html>' )` die Seite mit dem eingefügten Kommentar zurück, und
`notify()` für ein Ereignis, auf das niemand hört, gab `false` zurück.

## Die Ereignisse des Kernels

Im Code des Kernels gefunden mit `grep -rn "getInstance()->notify\|getInstance()->filter" kernel lib`:

| Ereignis | Art | Parameter / Wert |
|---|---|---|
| `request/preinput`, `request/input` | notify | die Anfrage, früh |
| `response/preoutput`, `response/output` | filter | die Ausgabe, bevor sie gesendet wird |
| `content/view` | filter | das Ergebnis einer Inhaltsansicht |
| `content/view/cachekeys` | filter | die Schlüssel des View-Caches (fügen Sie einen Schlüssel hinzu, zum Beispiel pro Benutzergruppe) |
| `content/cache`, `content/cache/version`, `content/cache/all` | notify | Caches von Knoten und Objekten wurden geleert: `( $nodeList, $contentObjectList )` |
| `content/class/cache`, `content/section/cache`, `content/state/cache`, `content/translations/cache`, `user/cache/all` | notify | andere Caches geleert |
| `content/state/assign` | notify | `( $objectID, $selectedStateIDList )` |
| `content/download/access`, `content/edit/access`, `collaboration/item/access` | filter | ein Mitspracherecht bei Zugriffsentscheidungen |
| `content/notification/create` | filter | ob eine Benachrichtigung erzeugt wird |
| `module/functionlist` | filter | die Richtlinienfunktionen eines Moduls (Inhaltseinschränkungen von Erweiterungen verwenden es) |
| `image/alias` | notify | ein Bild-Alias wurde erzeugt |
| `session/regenerate`, `session/destroy`, `session/gc`, `session/cleanup` | notify | Lebenszyklus der Session |
| `httpcache/store/headers` | filter | Header, die mit einem Eintrag des HTTP-Caches gespeichert werden |

Die Zugriffsfilter und die Schlüssel des View-Caches sind in
[Filter für Zugriff und View-Cache](../../../../eng-us/features/6.0/access-and-cache-filters.md) dokumentiert,
Inhaltseinschränkungen in
[Inhaltseinschränkungen von Richtlinien durch Erweiterungen](../../../../eng-us/features/6.0/content-limitation-handlers.md).

## Ereignisse von Befehlen, Cronjobs und Views

Runnables kündigen `runnable/<kind>/before` (notify, mit `$runnable, $class, $scope`) und `runnable/<kind>/after`
(Filter des Ergebnisses) an, wobei `<kind>` `command`, `cronjob` oder `view` ist, und `runcronjobs.php` fragt den
Filter `cronjob/part/run`, bevor ein Teil ausgeführt wird
([Einen Cronjob-Teil auslassen](../../../../eng-us/features/6.0/cronjob-part-filter.md)). Sie werden in
`site.ini [RunnableSettings] Listeners[]` registriert, das auch erlaubt, eine Runnable-Klasse mit
`Implementation[<class>]=<subclass>` zu ersetzen.

## Eigene Ereignisse ankündigen

Ihre Erweiterung kann anderen auf dieselbe Weise erlauben, sie zu erweitern:

```php
<?php
$items = ezpEvent::getInstance()->filter( 'mysite_hello/greetings', $items );
ezpEvent::getInstance()->notify( 'mysite_hello/greeted', array( $name ) );
```

Benennen Sie Ihre Ereignisse nach Ihrer Erweiterung, und dokumentieren Sie ihre Parameter.

## Regeln

- Halten Sie Listener schnell: `response/output` läuft auf jeder Seite.
- Geben Sie in jedem Filter auf jedem Weg den Wert zurück.
- Werfen Sie aus einem Listener keine Exceptions; protokollieren Sie und machen Sie weiter.
- Unter einem persistenten Server (Velocity) dürfen Sie Listener nie ohne Prüfung in Code anhängen, der pro Anfrage
  läuft: Sie würden sich ansammeln. Listener aus Einstellungen werden einmal angehängt.

## Verwandte Seiten

- Zurück: [Datenbankschema, .dba-Dateien und Upgrades](datenbankschema-und-dba-dateien.md)
- Weiter: [Handler, die über Einstellungen benannt werden](handler-ueber-einstellungen-benennen.md)
- [Workflow-Ereignistypen](workflow-ereignistypen.md)
- [Erweiterungspunkte](../../../bc/6.0/rad-erweiterungspunkte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
