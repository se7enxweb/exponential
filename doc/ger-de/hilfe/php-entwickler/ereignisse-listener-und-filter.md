---
title: Ereignisse, Listener und Filter
audience: php-developer, developer
category: php-development
order: 300
summary: Der Ereignis-Hub ezpEvent des Kernels für PHP-Entwickler - notify- und filter-Ereignisse, das Registrieren von Listenern in site.ini [Event] und [RunnableSettings] oder mit attach(), die Ereignisse, die der Kernel sendet, das Schreiben eines Filters, der seinen Wert zurückgibt, und die Regeln für Listener unter persistenten Workern.
keywords: Ereignisse, Listener, Filter, Beobachter, ezpEvent, events, listeners, filters, notify, filter, attach, detach, hasListeners, site.ini Event Listeners, RunnableSettings Listeners, request/input, response/output, content/cache, session/regenerate, httpcache/store/headers, observer
type: help
---
# Ereignisse, Listener und Filter

Diese Seite richtet sich an PHP-Entwickler, deren Code auf etwas reagieren muss, das der Kernel tut, oder einen Wert
unterwegs ändern muss, ohne den Kernel zu bearbeiten. Der Kernel hat einen Ereignis-Hub, `ezpEvent`
(`kernel/private/classes/ezpevent.php`). Der Kernel sendet Ereignisse; Ihr Listener empfängt sie.

## Inhalt

1. [Zwei Arten von Ereignissen](#zwei-arten-von-ereignissen)
2. [Einen Listener registrieren](#einen-listener-registrieren)
3. [Ein notify-Listener](#ein-notify-listener)
4. [Ein filter-Listener](#ein-filter-listener)
5. [Ereignisse, die der Kernel sendet](#ereignisse-die-der-kernel-sendet)
6. [Eigene Ereignisse senden](#eigene-ereignisse-senden)
7. [Regeln](#regeln)

## Zwei Arten von Ereignissen

| Art | Kernel-Aufruf | Ihr Listener |
|---|---|---|
| notify | `ezpEvent::getInstance()->notify( $name, array( ... ) )` | erhält die Parameter; sein Rückgabewert wird ignoriert |
| filter | `ezpEvent::getInstance()->filter( $name, $value, ... )` | erhält den Wert (und zusätzliche Argumente) und **muss** den Wert **zurückgeben**, geändert oder nicht |

Ein filter-Listener, der das Zurückgeben vergisst, zerstört den Wert: Die Seite wird leer, die Header-Liste ist leer.

## Einen Listener registrieren

In den Einstellungen, für Webanfragen:

```ini
# extension/acmereport/settings/site.ini.append.php
<?php /* #?ini charset="utf-8"?
[Event]
Listeners[]=content/cache@acmeReportListener::contentCacheCleared
Listeners[]=response/output@acmeReportListener::output
*/ ?>
```

Die Form ist `<event>@<callback>`, und der Callback ist ein Funktionsname oder `Class::method`. Ein falscher Callback
wird mit einer Zeile im Log übersprungen. `[Event] Listeners[]` werden **nur an Webanfragen** angehängt. Für Befehle,
Cronjob-Teile und Views hängt `[RunnableSettings] Listeners[]` Listener der Ereignisse `runnable/<kind>/before` und
`after` in jedem Kontext an ([Modul-Views als Klassen](modul-views-als-klassen.md#ereignisse-rund-um-run)).

Im Code, zur Laufzeit:

```php
$id = ezpEvent::getInstance()->attach( 'content/cache', array( 'acmeReportListener', 'contentCacheCleared' ) );
ezpEvent::getInstance()->detach( 'content/cache', $id );
```

`ezformtoken` registriert seine drei Listener auf diese Weise in seiner `site.ini.append.php`:
`request/input@ezxFormToken::input`, `response/output@ezxFormToken::output`, `session/regenerate@ezxFormToken::reset`.

## Ein notify-Listener

```php
class acmeReportListener
{
    /**
     * content/cache: the view cache of these nodes and objects was cleared
     *
     * @param array $nodeList node ids
     * @param array $objectList content object ids
     */
    public static function contentCacheCleared( $nodeList, $objectList = array() )
    {
        acmeReportCache::forgetNodes( is_array( $nodeList ) ? $nodeList : array() );
    }
}
```

## Ein filter-Listener

```php
class acmeReportListener
{
    /**
     * response/output: the page before it is sent
     *
     * @param string $html
     * @return string
     */
    public static function output( $html )
    {
        if ( !is_string( $html ) )
            return $html;
        // change $html only when you must; this runs on every page
        return $html;
    }
}
```

## Ereignisse, die der Kernel sendet

Eine Auswahl, gefunden mit `grep -rn "ezpEvent::getInstance()->" kernel lib`:

| Ereignis | Art | Wann |
|---|---|---|
| `request/preinput`, `request/input` | notify | die Anfrage kommt an; Eingabeprüfungen |
| `response/preoutput`, `response/output` | filter | die gerenderte Seite, vor und nach der Ausgabe des Pagelayouts |
| `module/functionlist` | filter | die Liste der Views und Funktionen eines Moduls |
| `content/view` | filter | die Knoten-ID einer Inhaltsansicht |
| `content/cache`, `content/cache/all`, `content/cache/version` | notify | View-Caches wurden geleert |
| `content/class/cache`, `content/section/cache`, `content/state/cache` ... | notify | andere Caches wurden geleert |
| `content/edit/access`, `content/download/access` | filter | Zugriffsentscheidungen, die Sie verschärfen dürfen |
| `session/regenerate`, `session/destroy`, `session/gc`, `session/cleanup` | notify | der Lebenszyklus der Sitzung |
| `httpcache/store/headers` | filter | die Header, die der HTTP-Cache mit einer Seite aufbewahrt |
| `image/alias` | notify | ein Bild-Alias wurde erzeugt |
| `runnable/<kind>/before`, `runnable/<kind>/after` | notify, filter | rund um jeden Befehl, jeden Cronjob-Teil und jede View |

**Setup > RAD**, Übersicht „events“ (`/setup/radsurvey/(show)/events`), listet die Ereignisse und ihre Listener auf
Ihrer Installation auf ([Erweiterungspunkte](../../bc/6.0/rad-erweiterungspunkte.md#listener-für-filter-ereignisse)).

## Eigene Ereignisse senden

Eine Erweiterung kann anderen auf dieselbe Weise Ereignisse anbieten:

```php
ezpEvent::getInstance()->notify( 'acmereport/item/stored', array( $item->attribute( 'id' ) ) );
$rows = ezpEvent::getInstance()->filter( 'acmereport/export/rows', $rows, $month );
```

Benennen Sie sie unter dem Präfix Ihrer Erweiterung, dokumentieren Sie die Parameter, und prüfen Sie zuerst
`hasListeners( $name )`, wenn das Zusammenstellen der Parameter teuer ist.

## Regeln

- **Schnell und still.** Listener laufen innerhalb von Anfragen, manche auf jeder Seite. Keine langsamen Aufrufe, keine Ausgabe.
- **Geben Sie in einem Filter den Wert zurück**, immer, auch auf den Pfaden, die nichts ändern.
- **Werfen Sie niemals** aus einem Listener heraus; fangen Sie ab, protokollieren Sie und geben Sie zurück.
- **Persistente Worker.** Unter Velocity bedient ein Worker viele Anfragen. Registrieren Sie Listener in den
  Einstellungen, oder hängen Sie sie im Code einmal pro Anfrage an (abgesichert mit `eZExecution::requestKey()`),
  niemals mit einem `true`-Flag, das ein Worker aus seinem Warm-up behält, sonst läuft der Listener zweimal oder gar
  nicht ([Fallen durch statischen Zustand](fallen-durch-statischen-zustand.md)).

## Verwandte Seiten

- [Ereignisse und Filter (Erweiterungsentwickler)](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ereignisse-und-filter.md)
- [Das Muster Event-Hook](../../standards/muster/ereignis-hook.md), [Das Muster Beobachter](../../standards/muster/beobachter.md)
- [Erweiterungspunkte aus PHP](erweiterungspunkte-aus-php.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
