---
title: Einen Workflow-Ereignistyp schreiben
audience: php-developer, developer
category: php-development
order: 290
summary: Die PHP-Seite eines Workflow-Ereignistyps - die Klasse, die eZWorkflowEventType erweitert, registerEventType(), setTriggerTypes(), execute() und seine Status, die Prozessparameter, Einstellungen pro Ereignis, das Verschieben auf Cron und wie das Veröffentlichen für Redakteure schnell bleibt.
keywords: Workflow-Ereignistyp, Workflow-Ereignis, Trigger, Status, Veröffentlichen, workflow event type, eZWorkflowEventType, eZWorkflowType, registerEventType, setTriggerTypes, execute, STATUS_ACCEPTED, STATUS_REJECTED, STATUS_DEFERRED_TO_CRON, parameter_list, triggers, content publish, workflow.ini, AvailableEventTypes
type: help
---
# Einen Workflow-Ereignistyp schreiben

Diese Seite richtet sich an PHP-Entwickler, die einen Schritt hinzufügen, den Administratoren in Workflows einsetzen
können: ein externes System benachrichtigen, wenn Inhalte veröffentlicht werden, ein Feld vor dem Veröffentlichen
prüfen, auf eine Freigabe warten. Ein Workflow läuft, wenn ein Trigger auslöst (zum Beispiel `content/publish/before`
oder `after`); jedes seiner Ereignisse ist eine Instanz eines Ereignistyps, also einer Klasse, die `eZWorkflowEventType`
erweitert. Das vollständige Beispiel mit Templates finden Sie unter
[Einen Workflow-Ereignistyp schreiben](../ersteller/anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md).

## Inhalt

1. [Registrierung](#registrierung)
2. [Die Klasse](#die-klasse)
3. [Was execute() erhält](#was-execute-erhält)
4. [Die Status](#die-status)
5. [Einstellungen pro Ereignis](#einstellungen-pro-ereignis)
6. [Auf Cron verschieben](#auf-cron-verschieben)
7. [Regeln](#regeln)
8. [Ereignistyp oder Event-Listener?](#ereignistyp-oder-event-listener)

## Registrierung

```text
extension/acmereport/eventtypes/event/acmenotify/acmenotifytype.php
extension/acmereport/settings/workflow.ini.append.php
```

```ini
<?php /* #?ini charset="utf-8"?
[EventSettings]
ExtensionDirectories[]=acmereport
AvailableEventTypes[]=event_acmenotify
*/ ?>
```

Einträge in `AvailableEventTypes[]` sind die Gruppe (`event`) und der Typ-String, verbunden durch `_`, wie
`event_ezapprove` des Kernels in `settings/workflow.ini`.

## Die Klasse

```php
<?php
class acmeNotifyType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'acmenotify';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'extension/acmereport', 'Notify the archive' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $object = eZContentObject::fetch( (int)$parameters['object_id'] );
        if ( !$object )
            return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;

        acmeReportQueue::add( $object->attribute( 'id' ), (int)$parameters['version'] );
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( acmeNotifyType::WORKFLOW_TYPE_STRING, 'acmeNotifyType' );
```

`setTriggerTypes()` begrenzt die Trigger, mit denen das Ereignis verwendet werden kann, sodass Administratoren es nicht
dort anhängen können, wo es keinen Sinn ergibt. `eZWaitUntilDateType` des Kernels
(`kernel/classes/workflowtypes/event/ezwaituntildate/`) ist ein vollständiges Beispiel.

## Was execute() erhält

| Argument | Ist |
|---|---|
| `$process` | der `eZWorkflowProcess`; `$process->attribute( 'parameter_list' )` enthält die Parameter der Operation |
| `$event` | das `eZWorkflowEvent`, die konfigurierte Instanz: Ihre Einstellungen stehen in ihren Attributen `data_int1`, `data_text1` ... |

Für die Operation zum Veröffentlichen von Inhalten enthalten die Parameter `object_id` und `version`. Casten Sie sie: Es
sind Werte, keine Objekte.

## Die Status

`eZWorkflowType` (`kernel/classes/ezworkflowtype.php`) definiert die Antworten von `execute()`:

| Status | Wirkung |
|---|---|
| `STATUS_ACCEPTED` | der Schritt ist erledigt; das nächste Ereignis läuft |
| `STATUS_REJECTED` | die Operation wird abgelehnt |
| `STATUS_DEFERRED_TO_CRON`, `STATUS_DEFERRED_TO_CRON_REPEAT` | warten; der Workflow-Cronjob führt den Schritt erneut aus |
| `STATUS_FETCH_TEMPLATE`, `STATUS_FETCH_TEMPLATE_REPEAT` | dem Benutzer ein Template zeigen (eine Auswahl, ein Formular) |
| `STATUS_REDIRECT`, `STATUS_REDIRECT_REPEAT` | den Benutzer anderswohin schicken, zum Beispiel zu einem Zahlungsanbieter |
| `STATUS_WORKFLOW_CANCELLED` | den Workflow stoppen |
| `STATUS_RUN_SUB_EVENT`, `STATUS_WORKFLOW_RESET`, `STATUS_WORKFLOW_DONE` | besondere Abläufe des Multiplexers und Zurücksetzungen |

## Einstellungen pro Ereignis

Was ein Administrator im Workflow-Editor für das Ereignis einstellt, wird durch Überschreiben von `fetchHTTPInput()` und
`validateHTTPInput()` gelesen und in den Spalten des Ereignisses gespeichert. Liefern Sie die Templates
`workflow/eventtype/edit/event_acmenotify.tpl` und `workflow/eventtype/view/event_acmenotify.tpl` in Ihrem Design mit,
wie `event_ezapprove.tpl` des Kernels.

## Auf Cron verschieben

Ein Ereignis, das wartet (auf eine Freigabe, ein Datum, eine externe Antwort), gibt `STATUS_DEFERRED_TO_CRON_REPEAT`
zurück; das Cronjob-Skript `workflow` führt `execute()` später erneut aus, und **Setup > Workflow-Prozesse** zeigt die
wartenden Prozesse. Der Redakteur sieht die Veröffentlichung als ausstehend, bis das Ereignis akzeptiert.

## Regeln

- **Schnell.** `execute()` läuft innerhalb der Veröffentlichungsanfrage des Redakteurs. Stellen Sie langsame Arbeit
  (externe Aufrufe, Mail, Bildverarbeitung) in eine Warteschlange, statt sie dort zu erledigen
  ([Hintergrundarbeit](../ersteller/anwendungsentwickler/muster-fuer-hintergrundarbeit.md)).
- **Keine Exceptions aus `execute()` heraus.** Fangen Sie ab, was fehlschlagen kann, protokollieren Sie es und geben
  Sie einen Status zurück.
- **Idempotent.** Ein verschobenes Ereignis läuft mehr als einmal; machen Sie den zweiten Lauf harmlos.
- **Logik außerhalb der Klasse.** Legen Sie die Entscheidung in einen Dienst, den Sie ohne Workflow testen können.

## Ereignistyp oder Event-Listener?

| Bedarf | Verwenden Sie |
|---|---|
| Administratoren entscheiden, wo es läuft, in welchem Workflow, in welcher Reihenfolge | einen Workflow-Ereignistyp |
| ein Schritt kann eine Operation ablehnen oder pausieren | einen Workflow-Ereignistyp |
| Ihr Code reagiert immer auf etwas, das der Kernel tut | einen Event-Listener ([Ereignisse, Listener und Filter](ereignisse-listener-und-filter.md)) |

## Verwandte Seiten

- [Workflows, Trigger und Ereignisse](../ersteller/anwendungsentwickler/workflows-und-ereignisse.md)
- [eZWorkflowType-API](../erweiterungsentwicklung/teil-8-referenz/ezworkflowtype-api.md)
- [Das Muster Workflow-Ereignis](../../standards/muster/workflow-ereignis.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
