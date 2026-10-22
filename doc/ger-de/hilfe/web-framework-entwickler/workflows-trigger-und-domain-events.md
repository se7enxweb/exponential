---
title: "Workflows und Trigger: Einstiegspunkte an Inhaltsoperationen"
audience: web-framework-developer, developer
category: web-frameworks
order: 230
summary: Wo ein Framework-Entwickler Model-Observer, Signale nach dem Speichern, Zustandsautomaten oder Prozessmanager schreibt, bietet Exponential Workflows - Ereignistypen, die vor oder nach Operationen wie dem Veröffentlichen und dem Bestellabschluss laufen, über Trigger angebunden werden und annehmen, ablehnen, auf den Cronjob warten, eine Seite anzeigen oder weiterleiten können - wie sie funktionieren, wie Sie einen Ereignistyp schreiben und wann Sie sie einsetzen.
keywords: Workflow, Trigger, Ereignistyp, Operation, vorher, nachher, an Cronjob übergeben, Freigabe, Zustandsautomat, Observer, Signale, Domain Events, Prozessmanager, event type, eZWorkflowEventType, operation, content_publish, before, after, deferred to cron, approval, state machine, observers, signals, domain events, Symfony Workflow, process manager, Web-Framework-Entwickler, web framework developer
type: help
---
# Workflows und Trigger: Einstiegspunkte an Inhaltsoperationen

Diese Seite richtet sich an Entwickler, die Geschäftslogik in die Speicherereignisse eines ORM einhängen, ein
Django-Signal `post_save` schreiben oder einen Freigabeprozess mit einer Bibliothek für Zustandsautomaten modellieren
würden. In Exponential können **Operationen** wie das Veröffentlichen von Inhalten oder das Bestätigen einer Bestellung
einen **Workflow** **vor** oder **nach** ihrer Ausführung starten. Ein Workflow ist eine Liste von **Ereignistypen**
(PHP-Klassen), die über einen **Trigger** mit einer Operation verbunden ist, und er kann die Operation zurückhalten, bis
etwas geschieht: eine Freigabe, eine Zahlung, ein Datum ([Workflows, Trigger und Ereignisse](../ersteller/anwendungsentwickler/workflows-und-ereignisse.md)).

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Operationen, an denen Sie sich einklinken können](#operationen-an-denen-sie-sich-einklinken-können)
3. [Was ein Ereignis antworten kann](#was-ein-ereignis-antworten-kann)
4. [Einen Ereignistyp schreiben](#einen-ereignistyp-schreiben)
5. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)
6. [Wann was einsetzen](#wann-was-einsetzen)

## Die Bausteine

| Begriff | Was |
|---|---|
| Workflow | eine benannte Liste von Ereignissen in einer Workflow-Gruppe (**Setup > Workflows**) |
| Ereignistyp | eine Klasse, die einen Schritt ausführt: Freigabe, Warten bis zu einem Datum, Multiplexer, Zahlungs-Gateway sowie die Ereignistypen von Erweiterungen |
| Trigger | verbindet einen Workflow mit einer Operation, `before` oder `after` dieser (**Setup > Trigger**) |
| Prozess | ein Durchlauf eines Workflows für ein Objekt und eine Version, der in der Datenbank gehalten wird, solange er wartet |

Workflows werden von Administratoren im Browser konfiguriert; Ihre Erweiterung stellt die Ereignistypen bereit.

## Operationen, an denen Sie sich einklinken können

`settings/workflow.ini [OperationSettings] AvailableOperationList[]` listet die Operationen auf, die Trigger anbieten.
Ausgeliefert und aktiv sind: `content_publish`, `before_shop_confirmorder`, `shop_checkout`, `shop_addtobasket`,
`shop_updatebasket`. Weitere sind auskommentiert aufgeführt (`content_move`, `content_hide`, `content_delete`,
`content_updatesection`, `content_read`, `content_updateobjectstate`, `user_activation`, `user_password` ...); eine
Installation aktiviert sie in einem Override. Ihre eigenen Klassen auf Basis von persistenten Objekten haben keine
Operationen: Lösen Sie stattdessen ein eigenes Ereignis aus ([Ereignisse und Listener](ereignisse-und-listener.md)).

## Was ein Ereignis antworten kann

`execute()` gibt eine Statuskonstante von `eZWorkflowType` zurück:

| Status | Wirkung |
|---|---|
| `STATUS_ACCEPTED` | mit dem nächsten Ereignis und danach mit der Operation fortfahren |
| `STATUS_REJECTED` | die Operation abbrechen |
| `STATUS_DEFERRED_TO_CRON` | warten; der Cronjob `workflow.php` (Teil `frequent`) führt den Prozess erneut aus |
| `STATUS_FETCH_TEMPLATE` | dem Benutzer eine Seite anzeigen (ein Formular, eine Bestätigung) |
| `STATUS_REDIRECT` | den Benutzer an eine andere Stelle schicken (einen Zahlungsanbieter) |
| `STATUS_WORKFLOW_CANCELLED` | den Workflow abbrechen |

Wartende Prozesse werden unter **Setup > Workflow-Prozesse** aufgelistet. Ohne den Cronjob-Teil `frequent` werden
Inhalte, die auf ein zurückgestelltes Ereignis warten, nie veröffentlicht ([Geplante Aufgaben](geplante-aufgaben-und-cronjobs.md)).

## Einen Ereignistyp schreiben

Aus [Einen Workflow-Ereignistyp schreiben](../ersteller/anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md):

```ini
# extension/expdemo/settings/workflow.ini.append.php
<?php /* #?ini charset="utf-8"?
[EventSettings]
ExtensionDirectories[]=expdemo
AvailableEventTypes[]=event_expnotify
*/ ?>
```

```php
<?php
// extension/expdemo/eventtypes/event/expnotify/expnotifytype.php
class expNotifyType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'expnotify';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'extension/expdemo', 'Notify the archive' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $object = eZContentObject::fetch( (int)$parameters['object_id'] );
        if ( !$object )
            return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;
        // queue slow work instead of doing it here
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( expNotifyType::WORKFLOW_TYPE_STRING, 'expNotifyType' );
```

## Im Vergleich mit anderen Frameworks

| Idee aus Frameworks | Exponential |
|---|---|
| Lifecycle-Events von Doctrine, Observer in Eloquent, `post_save` in Django, Callbacks in Rails | ein Trigger `after` auf `content_publish` |
| Validierung vor dem Speichern mit Vetorecht | ein Trigger `before`, dessen Ereignis ablehnt |
| Symfony Workflow, Gems für Zustandsautomaten | Objektzustände für den Status, Workflows für die Schritte ([Objektzustände](../../anleitungen/objektzustaende.md)) |
| Freigabewarteschlangen | der mitgelieferte Freigabe-Ereignistyp mit Kollaborationseinträgen |
| Prozessmanager, Sagas | ein Workflow, dessen Ereignisse an den Cronjob übergeben und danach fortgesetzt werden |
| Listener in der Warteschlange | ein Ereignis, das einen Job in die Warteschlange stellt ([Warteschlangen](warteschlangen-und-hintergrundjobs.md)) |

## Wann was einsetzen

| Verwenden Sie einen Workflow, wenn | Verwenden Sie einen Listener auf ein Kernel-Ereignis, wenn |
|---|---|
| Administratoren ihn ein- und ausschalten oder die Schritte ordnen sollen | das Verhalten immer Teil Ihrer Anwendung ist |
| der Schritt warten kann (Freigabe, Zahlung, ein Datum) | er synchron laufen und nie warten darf |
| er an eine der Trigger-Operationen gebunden ist | das Ereignis keinen Trigger hat |

## Verwandte Seiten

- [Leitfaden zu Workflows](../../anleitungen/workflows.md)
- [Das Muster Workflow-Ereignis](../../standards/muster/workflow-ereignis.md)
- [Inhaltsjobs](../../funktionen/6.0/inhaltsauftraege.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
