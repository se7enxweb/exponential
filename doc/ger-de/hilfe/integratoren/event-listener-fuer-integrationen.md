---
title: Event-Listener für Integrationen
audience: integrator, developer
category: integration
order: 180
summary: Mit ezpEvent-Listenern auf Kernel-Ereignisse reagieren - welche Ereignisse für eine Integration nützlich sind (geleerte Caches, Objektzustände, Benachrichtigungen, Sitzungen), einen Listener in den Einstellungen anhängen, ein Beispiel für einen CDN-Purge, der Unterschied zwischen Webanfragen und Läufen auf der Kommandozeile und die Regeln.
keywords: ezpEvent, Event-Listener, notify, filter, site.ini Event Listeners, content/cache, content/cache/all, content/state/assign, CDN-Purge, Cache-Invalidierung, RunnableSettings, Hook, Integration, event listener, CDN purge, cache invalidation, hook
type: help
---
# Event-Listener für Integrationen

Diese Seite ist für Integrationsentwickler, die auf etwas reagieren möchten, das der Kernel tut, ohne einen Workflow
zu konfigurieren. Der Kernel meldet Ereignisse über `ezpEvent` (`kernel/private/classes/ezpevent.php`); ein Listener
ist eine Funktion oder statische Methode, die in den Einstellungen genannt wird. Listener sind der leichteste Hook,
den es gibt, und der richtige für Reaktionen wie „diese Seiten beim CDN leeren" oder „dem Archiv mitteilen, dass
dieses Objekt seinen Zustand geändert hat". Das vollständige Kapitel ist
[Ereignisse, Listener und Filter](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ereignisse-und-filter.md).

## Inhalt

1. [Für eine Integration nützliche Ereignisse](#für-eine-integration-nützliche-ereignisse)
2. [Einen Listener anhängen](#einen-listener-anhängen)
3. [Beispiel: ein CDN leeren, wenn Caches geleert werden](#beispiel-ein-cdn-leeren-wenn-caches-geleert-werden)
4. [Webanfragen und Läufe auf der Kommandozeile](#webanfragen-und-läufe-auf-der-kommandozeile)
5. [Listener oder Workflows](#listener-oder-workflows)
6. [Regeln](#regeln)

## Für eine Integration nützliche Ereignisse

| Ereignis | Art | Parameter | Verwendung in einer Integration |
|---|---|---|---|
| `content/cache` | notify | `( $nodeList, $contentObjectList )` | Die View-Caches dieser Knoten wurden geleert: dieselben Seiten bei einem CDN oder Reverse Proxy leeren |
| `content/cache/all` | notify | keine | Jeder View-Cache wurde geleert: alles nachgelagert leeren |
| `content/state/assign` | notify | `( $objectID, $selectedStateIDList )` | Ein Objekt hat seinen Zustand geändert (zum Beispiel „freigegeben", „archiviert"): dem System Bescheid geben, das Zuständen folgt |
| `content/notification/create` | filter | ob eine Benachrichtigung erzeugt wird | Benachrichtigungen unterdrücken oder zulassen, zum Beispiel während eines Imports |
| `session/destroy`, `session/regenerate` | notify | Lebenszyklus der Sitzung | Den Benutzer auch bei einer angebundenen Anwendung abmelden |
| `response/output` | filter | die Seite | Jeder Seite ein Tag hinzufügen (dafür besser Templates verwenden) |

Ein Ereignis `content/publish` gibt es in dieser Liste nicht: Der Hook für das Veröffentlichen ist der Trigger
`content / publish / after` ([Workflows und Trigger](workflows-und-trigger-als-hooks.md)), und sein sichtbares Ergebnis
für Caches ist `content/cache`.

## Einen Listener anhängen

In der `settings/site.ini.append.php` Ihrer Erweiterung:

```php
<?php /* #?ini charset="utf-8"?

[Event]
Listeners[]=content/cache@myIntegrationListeners::cacheCleared
Listeners[]=content/cache/all@myIntegrationListeners::allCacheCleared

*/ ?>
```

Die Form ist `<event>@<callback>`. Ein fehlerhafter Eintrag, ein unbekannter Callback oder ein Duplikat wird
übersprungen oder einmal angehängt, mit einem Hinweis im Protokoll.

## Beispiel: ein CDN leeren, wenn Caches geleert werden

Der Listener notiert nur die Knoten-IDs; ein häufig laufender Cronjob-Teil schickt die Purge-Anfragen in Stapeln,
damit eine langsame CDN-API das Veröffentlichen nie verlangsamt.

```php
<?php
/**
 * Event listeners of myintegration, attached in settings/site.ini.append.php [Event] Listeners[].
 */
class myIntegrationListeners
{
    /** notify: the view caches of these nodes were cleared */
    public static function cacheCleared( $nodeList, $contentObjectList )
    {
        $db = eZDB::instance();
        foreach ( (array)$nodeList as $nodeID )
        {
            $db->query( 'INSERT INTO myintegration_purge ( node_id, created ) VALUES ( '
                      . (int)$nodeID . ', ' . time() . ' )' );
        }
    }

    /** notify: every view cache was cleared */
    public static function allCacheCleared()
    {
        eZDB::instance()->query( 'INSERT INTO myintegration_purge ( node_id, created ) VALUES ( 0, ' . time() . ' )' );
    }
}
```

Die Knoten-ID `0` steht in der eigenen Tabelle dieses Beispiels für „alles leeren". Der Cronjob-Teil macht aus
Knoten-IDs URLs (`eZContentObjectTreeNode::fetch( $id )->urlAlias()`) und ruft das CDN auf.

## Webanfragen und Läufe auf der Kommandozeile

`[Event] Listeners[]` wird für **Webanfragen** angehängt (`ezpKernelWeb`). Ein Kommandozeilenskript, ein Cronjob-Teil
oder `ezexec.php` hängt sie nur an, wenn es darum bittet:

```php
<?php
ezpEvent::getInstance()->registerEventListeners();
```

Rufen Sie das nach `$script->initialize()` in einem Importskript auf, dessen Leeren von Caches Ihre Listener sehen
müssen. Listener für Befehle, Cronjob-Teile und Modulansichten (`runnable/<kind>/before` und `after`,
`cronjob/part/run`) werden in `site.ini [RunnableSettings] Listeners[]` registriert und überall angehängt.

## Listener oder Workflows

| Listener | Workflow-Ereignis |
|---|---|
| Keine Konfiguration in der Administration | Website-Ersteller sehen und schalten es unter Setup > Trigger |
| Reagiert nur; kann den Vorgang nicht stoppen | Kann den Vorgang annehmen, ablehnen oder warten lassen |
| Funktioniert für jedes gemeldete Ereignis | Funktioniert für Vorgänge, die in `AvailableOperationList[]` angeboten werden |
| Für Webanfragen angehängt (CLI auf Wunsch) | Läuft überall, wo der Vorgang läuft, Web oder CLI |

## Regeln

- Halten Sie Listener schnell; notieren Sie langsame Arbeit und geben Sie sie an einen Cronjob ab.
- Ein **filter**-Listener muss den Wert zurückgeben, auf jedem Weg; wer das vergisst, zerstört ihn.
- Werfen Sie aus einem Listener keine Ausnahme; protokollieren Sie mit `eZDebug::writeError()` und machen Sie weiter.
- Unter Velocity hängen Sie Listener nie in Code an, der pro Anfrage läuft: Sie häufen sich an. Listener aus den
  Einstellungen werden einmal angehängt.

## Verwandte Seiten

- [Ereignisse, Listener und Filter](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ereignisse-und-filter.md)
- [Filter für Zugriff und View-Cache](../../funktionen/6.0/zugriffs-und-cache-filter.md)
- [Caching nach Importen](caching-nach-importen.md)
- [Workflows und Trigger als Integrations-Hooks](workflows-und-trigger-als-hooks.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
