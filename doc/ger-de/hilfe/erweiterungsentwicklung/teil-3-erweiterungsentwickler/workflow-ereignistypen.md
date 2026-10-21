---
title: Workflow-Ereignistypen
audience: developer, app-creator
category: extension-development
order: 180
summary: Einen Schritt zu Workflows hinzufügen - eine eZWorkflowEventType-Klasse, ihre Registrierung und Trigger, execute() und die Status, die es zurückgeben kann, Prozessparameter und ein geprüftes Beispiel.
keywords: Workflow-Ereignis, Workflow-Ereignistyp, Status, Trigger, Prozessparameter, Registrierung, workflow event, eZWorkflowEventType, eZWorkflowType, execute, STATUS_ACCEPTED, STATUS_REJECTED, STATUS_DEFERRED_TO_CRON, setTriggerTypes, registerEventType, AvailableEventTypes, parameter_list, eventtypes
type: help
---
# Workflow-Ereignistypen

Teil 3, Kapitel 18. Ein **Workflow-Ereignistyp** ist ein Schritt, den Builder in einen Workflow einbauen können (siehe
[Workflows für Builder](../teil-2-builder/workflows-fuer-builder.md)):
genehmigen, bis zu einem Datum warten und was immer Ihre Erweiterung hinzufügt. Dieses Kapitel zeigt, wie Sie einen
schreiben. Das Beispiel hat `php -l` bestanden und wurde mit geladenem Kernel erzeugt und abgefragt; die
Referenzklassen sind `eZWorkflowType` und `eZWorkflowEventType` (`kernel/classes/`).

## Registrierung

`extension/mysite_hello/settings/workflow.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[EventSettings]
ExtensionDirectories[]=mysite_hello
AvailableEventTypes[]=event_mysitepublishlog

*/ ?>
```

Die Typzeichenkette lautet `event_<type>`; der Kernel (`eZWorkflowType::loadAndRegisterType()`) zerlegt sie in die
Gruppe `event` und den Typ `mysitepublishlog` und bindet
`extension/mysite_hello/eventtypes/event/mysitepublishlog/mysitepublishlogtype.php` ein. Die Erweiterung `swark`
registriert ihre beiden Ereignisse (`event_autopriority`, `event_defertocron`) auf dieselbe Weise.

## Die Klasse

```php
<?php
/**
 * Workflow event "Log publishing": writes one line per published version to var/log/mysite_hello.log.
 * Use it on the trigger content / publish / after.
 */
class mysitePublishLogType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'mysitepublishlog';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING,
                             ezpI18n::tr( 'extension/mysite_hello', 'Log publishing' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    public function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $objectID = isset( $parameters['object_id'] ) ? (int)$parameters['object_id'] : 0;
        $version = isset( $parameters['version'] ) ? (int)$parameters['version'] : 0;

        $object = eZContentObject::fetch( $objectID );
        if ( !$object instanceof eZContentObject )
        {
            // nothing to log; let the operation continue
            return eZWorkflowType::STATUS_ACCEPTED;
        }

        eZLog::write( sprintf( 'Published object %d version %d: %s', $objectID, $version, $object->attribute( 'name' ) ),
                      'mysite_hello.log' );

        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( mysitePublishLogType::WORKFLOW_TYPE_STRING, 'mysitePublishLogType' );
```

Mit geladenem Kernel lieferte `eZWorkflowType::createType( 'event_mysitepublishlog' )` die Klasse mit dem Namen
„Log publishing“, und `isAllowed( 'content', 'publish', 'after' )` war wahr, während `'before'` falsch war.

| Teil | Bedeutung |
|---|---|
| `parent::__construct( $typeString, $name )` | `eZWorkflowEventType` legt den Typ in die Gruppe `event` |
| `setTriggerTypes()` | wo Builder es einsetzen dürfen: `array( <module> => array( <operation> => array( 'before', 'after' ) ) )` oder `array( '*' => true )` für überall |
| `execute( $process, $event )` | die Arbeit; gibt einen Status zurück |
| `$process->attribute( 'parameter_list' )` | die Parameter der Operation; bei `content/publish`: `object_id` und `version` |
| `registerEventType()` | verknüpft die Zeichenkette mit der Klasse |

## Was execute() zurückgeben kann

Aus `eZWorkflowType` (`kernel/classes/ezworkflowtype.php`):

| Status | Wirkung |
|---|---|
| `STATUS_ACCEPTED` | erledigt; das nächste Ereignis läuft, danach wird die Operation fortgesetzt |
| `STATUS_REJECTED` | anhalten; die Operation findet nicht statt |
| `STATUS_DEFERRED_TO_CRON` | warten: Der Workflow-Cronjob führt das Ereignis später erneut aus (das Ereignis Approve nutzt das) |
| `STATUS_DEFERRED_TO_CRON_REPEAT` | dasselbe, wiederholt, bis ein anderer Status zurückgegeben wird |
| `STATUS_WORKFLOW_CANCELLED` | den ganzen Workflow abbrechen |
| `STATUS_FETCH_TEMPLATE`, `STATUS_FETCH_TEMPLATE_REPEAT` | dem Benutzer ein Template anzeigen (ein interaktiver Schritt) |
| `STATUS_REDIRECT`, `STATUS_REDIRECT_REPEAT` | den Benutzer woandershin schicken und danach zurückkommen |
| `STATUS_RUN_SUB_EVENT` | einen anderen Workflow ausführen (der Multiplexer) |

Das Ereignis `defertocron` von `swark` ist das kleinste echte Beispiel: Sein `execute()` gibt nur
`STATUS_DEFERRED_TO_CRON` zurück, sodass das Veröffentlichen vom Cronjob abgeschlossen wird statt während des Requests
des Redakteurs.

## Davor oder danach?

| Trigger | Verwenden Sie ihn, um |
|---|---|
| `content / publish / before` | zu entscheiden, ob veröffentlicht werden darf (prüfen, genehmigen, ablehnen) |
| `content / publish / after` | auf veröffentlichte Inhalte zu reagieren (protokollieren, benachrichtigen, anderswo indizieren) |

Ein Ereignis, das nur reagiert und nie wartet oder ablehnt, ist als **Event-Listener** oft einfacher
([Events, Listener und Filter](ereignisse-und-filter.md)):
Es muss kein Workflow eingerichtet werden.

## Einstellungen eines Ereignisses

Ein Ereignis kann pro Workflow eigene Einstellungen haben (wie die Genehmigenden von Approve): Implementieren Sie
`fetchHTTPInput()`, `validateHTTPInput()`, `workflowEventContent()` und `storeEventData()` und fügen Sie Ihrem Design
die Templates `workflow/eventtype/edit/event_<type>.tpl` und `workflow/eventtype/view/event_<type>.tpl` hinzu. Das
Approve-Ereignis des Kernels (`kernel/classes/workflowtypes/event/ezapprove/`) ist die vollständige Referenz.

## Vorsicht in execute()

- Es läuft innerhalb der Veröffentlichungsoperation: Halten Sie es kurz und geben Sie nie etwas mit echo aus.
- Schreiben Sie innerhalb von `$db->begin()` / `$db->commit()` in die Datenbank, wenn Sie mehrere Dinge ändern.
- Wandeln Sie jede ID, die Sie in SQL einsetzen, um; `autopriority` von `swark` liest Knoten-IDs aus dem Objekt und
  verwendet sie in einem `UPDATE`.
- Eine Ausnahme bricht das Veröffentlichen für den Redakteur ab. Fangen Sie ab, was Sie erwarten, protokollieren Sie es
  mit `eZDebug::writeError()` und geben Sie einen Status zurück.

## RAD

**Assistent für Workflow-Ereignisse** (`/setup/workflowevent`) schreibt die Klasse, die Einstellungen und die Templates,
einschließlich Ereigniseinstellungen und Triggern; siehe
[Mit RAD: ein Workflow-Ereignis](tutorials/rad-workflow-ereignis.md).

## Verwandte Seiten

- Zurück: [Speicherung, Validierung und Templates von Datentypen](datentyp-speicherung-validierung-und-templates.md)
- Weiter: [Trigger und Workflows für Entwickler](trigger-und-workflows-fuer-entwickler.md)
- [Die API von eZWorkflowType](../teil-8-referenz/ezworkflowtype-api.md)
- [Workflows](../../../anleitungen/workflows.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
