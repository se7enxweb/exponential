---
title: "Ereignisse und Listener: ezpEvent"
audience: web-framework-developer, developer
category: web-frameworks
order: 220
summary: Der prozessinterne Ereignisbus von Exponential im Vergleich mit dem EventDispatcher von Symfony, Laravel-Events, Django-Signalen und Spring-Application-Events – Notify- und Filter-Ereignisse, Listener in site.ini registrieren, die Ereignisse, die der Kernel auslöst, Runnable-Ereignisse, die Regeln für Listener bei persistenten Workern und wann Sie stattdessen Workflows verwenden.
keywords: Ereignisse, events, Listener, listeners, Event Dispatcher, event dispatcher, Signale, signals, Observer, observers, ezpEvent, notify, filter, attach, Listeners, request/input, response/output, content/cache, Runnable-Ereignisse, runnable events, EventDispatcher, Django signals, Spring events, Web-Framework-Entwickler, web framework developer
type: help
---
# Ereignisse und Listener: ezpEvent

Diese Seite richtet sich an Entwickler, die Framework-Ereignisse abonnieren (den `EventDispatcher` von Symfony,
`Event::listen` von Laravel, Django-Signale, `ActiveSupport::Notifications` von Rails, `@EventListener` von Spring).
Exponential hat einen kleinen prozessinternen Ereignisbus, `ezpEvent` (`kernel/private/classes/ezpevent.php`), mit zwei
Arten von Ereignissen: **Notify**-Ereignisse teilen Listenern mit, dass etwas geschehen ist, **Filter**-Ereignisse
reichen einen Wert durch Listener, die ihn ändern dürfen
([Ereignisse (ezpEvent)](../../standards/architektur/kernel-architektur.md#ereignisse-ezpevent)).

## Inhalt

1. [Die API](#die-api)
2. [Listener registrieren](#listener-registrieren)
3. [Ereignisse, die der Kernel auslöst](#ereignisse-die-der-kernel-auslöst)
4. [Regeln für Listener](#regeln-für-listener)
5. [Ereignisse, Workflows oder Benachrichtigungen?](#ereignisse-workflows-oder-benachrichtigungen)
6. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Die API

```php
$events = ezpEvent::getInstance();
$events->attach( 'content/cache', array( 'expDemoListener', 'onContentCache' ) );  // or 'expDemoListener::onContentCache'
$events->notify( 'expdemo/item/stored', array( $itemID ) );                       // tell listeners
$html = $events->filter( 'expdemo/item/html', $html );                             // let listeners change a value
```

Ihre eigene Erweiterung kann mit denselben Aufrufen eigene Ereignisse auslösen, sodass sich andere Erweiterungen daran
anhängen können.

## Listener registrieren

```ini
# extension/expdemo/settings/site.ini.append.php
<?php /* #?ini charset="utf-8"?
[Event]
Listeners[]=content/cache@expDemoListener::onContentCache
Listeners[]=response/output@expDemoListener::filterOutput
*/ ?>
```

- Einträge in `site.ini [Event] Listeners[]` werden für Web-Requests angehängt.
- Einträge in `site.ini [RunnableSettings] Listeners[]` werden für Befehle, Cronjob-Teile und Views in jedem Kontext
  angehängt, die Kommandozeile eingeschlossen, einmal pro Prozess
  ([Ereignisse rund um run()](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md#ereignisse-um-run)).
- `site.ini` kennzeichnet `[Event]` als private API, die sich ändern kann; die eigenen Erweiterungen des Kernels
  (`ezformtoken`) verlassen sich darauf.

## Ereignisse, die der Kernel auslöst

| Ereignis | Art | Wann |
|---|---|---|
| `request/preinput`, `request/input` | Notify | früh in einem Web-Request, mit der URI |
| `response/preoutput`, `response/output` | Filter | die endgültige Ausgabe eines Web-Requests |
| `content/view` | Filter | das Ergebnis einer Inhalts-View |
| `content/view/cachekeys` | Filter | die Schlüssel des Caches für Inhalts-Views |
| `content/cache`, `content/cache/all`, `content/cache/version` | Notify | Caches der Inhalts-Views geleert |
| `content/class/cache`, `content/section/cache`, `content/state/cache` | Notify | Caches von Klassen, Sektionen und Zuständen geleert |
| `content/state/assign` | Notify | ein Objektzustand wurde zugewiesen |
| `content/edit/access`, `content/download/access`, `collaboration/item/access` | Filter | Zugriffsentscheidungen, zu denen der Kernel die Listener befragt |
| `httpcache/store/headers` | Filter | die Header, die der HTTP-Cache mit einer Seite aufbewahrt |
| `image/alias`, `image/removeAliases`, `image/purgeAliases` | Notify | Bildvarianten erzeugt oder entfernt |
| `session/regenerate`, `session/destroy`, `session/gc`, `session/cleanup` | Notify | Lebenszyklus der Sitzung |
| `runnable/<kind>/before`, `runnable/<kind>/after` | Notify, Filter | rund um jeden klassenbasierten Befehl, Cronjob-Teil und jede View |

Die Liste wurde aus den Aufrufen von `ezpEvent::getInstance()->notify()` und `filter()` in `kernel/` und `lib/`
gewonnen; suchen Sie danach, um die Parameter zu sehen, die jeder Aufruf übergibt. Auch die Übersicht unter
**Setup > RAD** listet die Ereignisse auf.

## Regeln für Listener

- **Ein Filter-Listener muss den Wert zurückgeben, den er erhalten hat**, verändert oder nicht; einer, der das vergisst,
  zerstört den Wert.
- **Registrierungen müssen idempotent sein.** Unter Velocity sammeln sich Listener an, die in einem langlebigen Worker
  einmal pro Request angehängt werden. Registrieren Sie sie in den Einstellungen oder hängen Sie sie im Code nur einmal
  pro Prozess an.
- **Halten Sie Listener schnell.** Sie laufen innerhalb des Requests; schicken Sie langsame Arbeit in eine
  Warteschlange ([Warteschlangen](warteschlangen-und-hintergrundjobs.md)).
- **Benennen Sie Listener als Zeichenketten der Form `Class::method`**, damit sie sich konfigurieren lassen.

## Ereignisse, Workflows oder Benachrichtigungen?

| Bedarf | Verwenden Sie |
|---|---|
| Im Code auf ein Kernel-Ereignis reagieren, immer und synchron | einen `ezpEvent`-Listener |
| Schritte vor oder nach dem Veröffentlichen, dem Checkout und anderen Operationen ausführen, unter Umständen mit Warten (Freigabe, Zahlung, Datum), von Administratoren umschaltbar | einen Workflow an einem Trigger ([Workflows und Trigger](workflows-trigger-und-domain-events.md)) |
| Benutzer per Mail über neue Inhalte oder Elemente der Zusammenarbeit informieren | das Benachrichtigungssystem ([Benachrichtigungen](../../anleitungen/benachrichtigungen-entwickler.md)) |
| Echtzeitnachrichten an Browser | WebSockets und Server-Sent Events von Velocity ([Velocity-WebSockets](../../funktionen/6.0/velocity-websockets-und-ereignisse.md)) |

Für Ihre eigenen `eZPersistentObject`-Klassen gibt es kein eingebautes Ereignis „Model gespeichert“; lösen Sie ein
eigenes Ereignis aus Ihrer `store()`-Methode oder Ihrer Dienstmethode aus.

## Im Vergleich mit anderen Frameworks

| Framework | Konzept | Exponential |
|---|---|---|
| Symfony | Event Subscriber mit Prioritäten, abbrechbare Ereignisse | Listener in der Reihenfolge von `Listeners[]`; kein Abbrechen, keine Prioritäten |
| Laravel | Events und Listener, Listener in der Queue | Listener; die Arbeit stellen Sie selbst mit expqueue in die Warteschlange |
| Django | Signale (`post_save`) | Kernel-Ereignisse und Workflows an Inhaltsoperationen |
| Rails | Callbacks, Notifications | dasselbe |
| Spring | Application Events, `@TransactionalEventListener` | Listener; keine Transaktionsphasen |

## Verwandte Seiten

- [Middleware: Request-Regeln, Eingabe- und Ausgabe-Listener](middleware-und-request-regeln.md)
- [Muster: Event-Hook](../../standards/muster/ereignis-hook.md), [Muster: Observer](../../standards/muster/beobachter.md)
- [Erweiterungspunkte: Listener für Kernel-Ereignisse](../../bc/6.0/rad-erweiterungspunkte.md#listener-für-kernel-ereignisse)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
