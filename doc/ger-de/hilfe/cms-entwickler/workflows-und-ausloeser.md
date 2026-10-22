---
title: Workflows und Auslöser
audience: cms-developer
category: cms-development
order: 33
summary: Für CMS-Entwickler, wie Sie Logik ausführen, wenn Inhalte veröffentlicht werden, ein Benutzer sich registriert oder eine Bestellung aufgegeben wird. Die Seite behandelt Workflows, Events und Auslöser, die mitgelieferten Event-Typen, die Freigabe vor dem Veröffentlichen, den Workflow-Cronjob, Event-Listener als leichtere Alternative und eigene Workflow-Event-Typen.
keywords: Workflows, Auslöser, Trigger, Workflow-Events, Freigeben, Freigabe, Bis Datum warten, Multiplexer, Zahlungsgateway, Registrierung abschließen, content/publish, pre_publish, post_publish, Workflow-Cronjob, Workflow-Prozess, Event-Listener, ezpEvent, eigener Workflow-Event-Typ, Hooks, workflows, triggers, workflow events, approve, approval, wait until date, payment gateway, workflow cronjob, workflow process, event listener, custom workflow event type
type: help
---
# Workflows und Auslöser

Diese Seite richtet sich an Entwickler, die "wenn X geschieht, tue Y" brauchen: eine Freigabe vor dem Veröffentlichen,
eine E-Mail nach einer Registrierung, einen Abgleich mit einem anderen System nach einer Aktualisierung. Exponential hat
dafür zwei Mechanismen: **Workflows**, die Redakteure und Administratoren in der Administration konfigurieren und die
eine Operation zurückhalten können, und **Event-Listener**, die Ihr Code in Einstellungen registriert.

## Inhalt

1. [Workflows, Events und Auslöser](#workflows-events-und-auslöser)
2. [Die mitgelieferten Event-Typen](#die-mitgelieferten-event-typen)
3. [Freigabe vor dem Veröffentlichen](#freigabe-vor-dem-veröffentlichen)
4. [Prozesse und der Workflow-Cronjob](#prozesse-und-der-workflow-cronjob)
5. [Event-Listener: der leichtere Weg](#event-listener-der-leichtere-weg)
6. [Ein eigener Workflow-Event-Typ](#ein-eigener-workflow-event-typ)
7. [Die Wahl](#die-wahl)
8. [Stolperfallen](#stolperfallen)

## Workflows, Events und Auslöser

| Begriff | Was es ist | Wo |
|---|---|---|
| Workflow | eine benannte, geordnete Liste von Events in einer Workflow-Gruppe | **Setup** > **Workflows** (`/workflow/grouplist`) |
| Event | ein Schritt eines Workflows, von einem Event-Typ | die Bearbeitungsseite des Workflows |
| Auslöser | die Verbindung zwischen einem Workflow und einer Operation, davor oder danach (`pre_publish`, `post_publish` ...) | **Setup** > **Auslöser** (`/trigger/list`) |
| Prozess | ein nicht abgeschlossener Lauf eines Workflows für ein Objekt und eine Version | **Setup** > **Workflow-Prozesse** (`/workflow/processlist`) |

Die für Auslöser angebotenen Operationen stehen in `workflow.ini [OperationSettings] AvailableOperationList[]`:
mitgeliefert sind `content_publish` und die Shop-Operationen (`before_shop_confirmorder`, `shop_checkout`,
`shop_addtobasket`, `shop_updatebasket`). Weitere (Verschieben, Verbergen, Entfernen, Lesen von Inhalten,
Benutzeraktivierung und andere) sind dort als Kommentare aufgeführt und lassen sich in Ihren Einstellungen
einschalten, mit dem Hinweis, dass sich ihr Code ändern kann
([Workflows, Abschnitt 1](../../anleitungen/workflows.md#1-workflows-ereignisse-trigger-und-prozesse)).

## Die mitgelieferten Event-Typen

`settings/workflow.ini [EventSettings] AvailableEventTypes[]` (`kernel/classes/workflowtypes/event/`):

| Event-Typ | Tut |
|---|---|
| Freigeben (`ezapprove`) | hält das Veröffentlichen zurück, bis ein Freigebender im Posteingang der Zusammenarbeit entscheidet |
| Bis Datum warten (`ezwaituntildate`) | hält das Veröffentlichen zurück, bis der Wert eines Datumsattributs erreicht ist |
| Multiplexer (`ezmultiplexer`) | führt einen anderen Workflow nur für bestimmte Klassen, Sektionen oder Benutzer aus |
| Zahlungsgateway (`ezpaymentgateway`) | übergibt eine Bestellung an ein Zahlungsgateway |
| Einfacher Versand (`ezsimpleshipping`) | fügt einer Bestellung Versandkosten hinzu |
| Registrierung abschließen (`ezfinishuserregister`) | schließt eine Registrierung ab, die ein Workflow zurückgehalten hat |

Erweiterungen fügen weitere hinzu.

## Freigabe vor dem Veröffentlichen

1. Legen Sie einen Workflow mit einem Event **Freigeben** an: die Freigebenden (Benutzer oder eine Gruppe), die
   Sektionen und Sprachen, für die er gilt, und für wen er nicht gilt (zum Beispiel die Freigebenden selbst).
2. Packen Sie ihn in einen **Multiplexer**, wenn nur einige Klassen eine Freigabe brauchen.
3. Verbinden Sie ihn mit dem Auslöser `content` / `publish` / *before*.
4. Stellen Sie sicher, dass der Workflow-Cronjob läuft.

Die Version des Redakteurs wird *ausstehend*; ein Freigebender gibt im Posteingang der Zusammenarbeit frei oder lehnt
ab; der nächste Lauf des Workflow-Cronjobs veröffentlicht oder gibt den Entwurf zurück. Das durchgerechnete Beispiel mit
jedem Bildschirm: [Freigabe vor dem Veröffentlichen](../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen).

## Prozesse und der Workflow-Cronjob

Eine zurückgehaltene Operation wird als Prozess mit einem Memento der Operation gespeichert; `workflow.php` in den
Cronjob-Teilen setzt wartende Prozesse fort. **Ohne Cron werden freigegebene Inhalte nie veröffentlicht**
([Cronjobs](cronjobs.md), [Der Workflow-Cronjob](../../anleitungen/workflows.md#6-der-workflow-cronjob-wie-prozesse-fortgesetzt-werden)).
Prozesse, die auf einen entfernten Freigebenden warten, müssen bereinigt werden
([Prozesse abbrechen und bereinigen](../../anleitungen/workflows.md#7-prozesse-sicher-abbrechen-und-aufräumen)).

## Event-Listener: der leichtere Weg

Wenn Ihr Code nur reagieren muss (protokollieren, benachrichtigen, abgleichen) und die Operation nie zurückhalten muss,
ist ein Event-Listener einfacher: ein Callback, registriert in `site.ini [Event] Listeners[]` für ein Event, das der
Kernel ankündigt, etwa `content/state/assign` (Zustände geändert) oder `content/cache` (die Caches von Knoten wurden
geleert, was das Veröffentlichen tut). Es gibt kein eigenes Event "nach dem Veröffentlichen"; dafür verwenden Sie ein
Workflow-Event an `post_publish`. Filter-Events lassen einen Listener einen Wert ändern und müssen ihn zurückgeben
([Events, Listener und Filter](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ereignisse-und-filter.md)).

## Ein eigener Workflow-Event-Typ

Wenn Redakteure Ihren Schritt ihren Workflows hinzufügen können müssen oder der Schritt die Operation zurückhalten muss,
schreiben Sie einen Workflow-Event-Typ: eine Klasse, die `eZWorkflowEventType` erweitert, mit einer Methode `execute()`,
die angenommen, abgelehnt oder "später erneut ausführen" antwortet, registriert in `workflow.ini.append.php` Ihrer
Erweiterung ([Workflow-Event-Typen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/workflow-ereignistypen.md),
[Ein Workflow-Event von Hand](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tutorials/von-hand-workflow-ereignis.md),
[Die eZWorkflowType-API](../erweiterungsentwicklung/teil-8-referenz/ezworkflowtype-api.md)).

## Die Wahl

| Bedarf | Verwenden Sie |
|---|---|
| Eine redaktionelle Freigabe | das Event Freigeben, von einem Administrator konfiguriert |
| Veröffentlichen zu einem Datum | Bis Datum warten oder die Cronjobs `hide.php` und `unpublish.php` zum Verbergen und Entfernen ([Versionen, Entwürfe und Veröffentlichen](versionen-entwuerfe-und-veroeffentlichen.md#veröffentlichen-und-verbergen-zu-einem-datum)) |
| Ein anderes System nach dem Veröffentlichen benachrichtigen | ein Workflow-Event an `post_publish` |
| Ein Veröffentlichen stoppen, das eine Ihrer Regeln verletzt | einen Workflow-Event-Typ an `pre_publish`, der ablehnt |
| Stufen mit eigenen Berechtigungen | Objektzustände, von Redakteuren oder einem Workflow-Event geändert ([Objektzustände](../../anleitungen/objektzustaende.md)) |

## Stolperfallen

- **Langsame Events verlangsamen das Veröffentlichen.** Ein Aufruf eines anderen Systems in `post_publish` lässt den
  Redakteur warten; reihen Sie ihn ein und lassen Sie einen Cronjob oder einen Inhaltsjob die Arbeit erledigen.
- **Schleifen.** Ein Event, das Inhalte veröffentlicht, löst die Veröffentlichungs-Workflows erneut aus; schützen Sie
  sich dagegen.
- **Testen als Administrator.** Viele Freigabe-Einrichtungen nehmen Administratoren aus; testen Sie mit einem
  Redakteurskonto unter `example.invalid`.
- **Cron auf einem neuen Server vergessen.** Prüfen Sie vor dem Live-Gang, dass der Workflow-Cronjob eingeplant ist.

## Verwandte Seiten

- [Workflows](../../anleitungen/workflows.md): die vollständige Anleitung
- [Workflows für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/workflows-fuer-builder.md)
- [Auslöser und Workflows für Entwickler](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/trigger-und-workflows-fuer-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
