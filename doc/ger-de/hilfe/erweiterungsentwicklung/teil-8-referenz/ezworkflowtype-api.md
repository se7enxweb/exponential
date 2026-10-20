---
title: Die eZWorkflowType-API
audience: developer
category: extension-development
order: 210
summary: Die Methoden eines Workflow-Ereignistyps - execute() und seine Statuskonstanten, erlaubte Trigger, das Einstellungsformular, Aufräumen - und seine Registrierung, mit Signaturen und einem getesteten Beispiel.
keywords: Workflow-Ereignistyp, Workflow, Trigger, Statuskonstanten, Einstellungsformular, Aufräumen, eZWorkflowType, eZWorkflowEventType, execute, STATUS_ACCEPTED, STATUS_REJECTED, STATUS_DEFERRED_TO_CRON, STATUS_FETCH_TEMPLATE, setTriggerTypes, registerEventType, fetchHTTPInput, validateHTTPInput, attributeDecoder, cleanup
type: help
---
# Die eZWorkflowType-API

Teil 8, Referenz 17. Ein Workflow-Ereignistyp ist ein Schritt, den Administratoren in einen Workflow einfügen können
und den ein Trigger vor oder nach einer Operation ausführt (Veröffentlichen, in den Warenkorb legen, Anmelden). Er ist
eine Klasse, die `eZWorkflowEventType` erweitert (`kernel/classes/ezworkfloweventtype.php`, das selbst auf
`eZWorkflowType` in `kernel/classes/ezworkflowtype.php` aufbaut). Die Anleitung ist
[Workflow-Ereignistypen](../teil-3-erweiterungsentwickler/workflow-ereignistypen.md), dazu die Tutorials
[Ein Workflow-Ereignis, von Hand](../teil-3-erweiterungsentwickler/tutorials/von-hand-workflow-ereignis.md) und
[Mit RAD: ein Workflow-Ereignis](../teil-3-erweiterungsentwickler/tutorials/rad-workflow-ereignis.md).

## Registrierung

| Aufruf | Hinweise |
|---|---|
| `public function __construct( $typeString = '', $name = '' )` | aus Ihrem Konstruktor aufrufen: `parent::__construct( 'myevent', ezpI18n::tr( 'extension/myext', 'My event' ) );` |
| `eZWorkflowEventType::registerEventType( $typeString, $class_name )` (statisch) | einmal, am Ende der Datei |
| `setTriggerTypes( $allowedTypes )` | in welchen Operationen er laufen darf, z. B. `array( 'content' => array( 'publish' => array( 'before', 'after' ) ) )` |

Dazu `workflow.ini [EventSettings] ExtensionDirectories[]` und `AvailableEventTypes[]=event_<typestring>`.

## Ausführen: execute()

```php
function execute( $process, $event )
```

Die Methode gibt eine der Statuskonstanten von `eZWorkflowType` zurück:

| Konstante | Bedeutung |
|---|---|
| `STATUS_ACCEPTED` | erledigt; der Workflow geht weiter |
| `STATUS_REJECTED` | anhalten; die Operation findet nicht statt |
| `STATUS_DEFERRED_TO_CRON`, `STATUS_DEFERRED_TO_CRON_REPEAT` | später im Workflow-Cronjob ausführen, einmal oder bis zur Annahme |
| `STATUS_FETCH_TEMPLATE`, `STATUS_FETCH_TEMPLATE_REPEAT` | dem Benutzer ein Template (ein Formular) zeigen und nach dem Absenden erneut ausführen |
| `STATUS_REDIRECT`, `STATUS_REDIRECT_REPEAT` | den Benutzer weiterleiten |
| `STATUS_WORKFLOW_CANCELLED`, `STATUS_WORKFLOW_DONE`, `STATUS_WORKFLOW_RESET`, `STATUS_RUN_SUB_EVENT`, `STATUS_NONE` | Sonderfälle |

`$process->attribute( 'parameter_list' )` enthält die Parameter der Operation (beim Veröffentlichen: `object_id` und
`version`).

```php
<?php
class myExtLogPublishType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'myextlogpublish';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'extension/myext', 'Log publishing' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        eZLog::write( 'Published object ' . (int)$parameters['object_id'], 'myext.log' );
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( myExtLogPublishType::WORKFLOW_TYPE_STRING, 'myExtLogPublishType' );
```

## Das Einstellungsformular des Ereignisses

Wenn der Administrator das Ereignis einem Workflow hinzufügt, kann es eigene Einstellungen haben:

| Methode | Verwendung |
|---|---|
| `initializeEvent( $event )` | Vorgaben für ein neues Ereignis |
| `validateHTTPInput( $http, $base, $event, &$validation )` | die abgeschickten Einstellungen prüfen |
| `fetchHTTPInput( $http, $base, $event )` | sie im Ereignis speichern (in dessen Feldern `data_int1`, `data_text1` ...) |
| `attributeDecoder( $event, $attr )` | berechnete Attribute des Ereignisses für Templates |
| `typeFunctionalAttributes()` | die Namen dieser Attribute |
| `customWorkflowEventHTTPAction( $http, $action, $workflowEvent )` | eigene Schaltflächen im Formular |
| `storeEventData( $event, $version )`, `workflowEventContent( $event )` | Ereignisse mit Daten in eigenen Tabellen |

Templates: `workflow/eventtype/edit/event_<typestring>.tpl` und `workflow/eventtype/view/event_<typestring>.tpl`,
gefunden über die Design-Kette.

## Aufräumen

| Methode | Verwendung |
|---|---|
| `needCleanup()` | geben Sie `true` zurück, wenn `cleanup()` laufen muss |
| `cleanup( $process, $event )` | wenn der Prozess endet |
| `cleanupAfterRemoving( $attr = array() )` | wenn das Ereignis aus einem Workflow entfernt wird |

## Verwandte Seiten

- Zurück: [Die eZDataType-API](ezdatatype-api.md)
- Weiter: [Inhaltsfunktionen aus PHP](inhaltsfunktionen-aus-php.md)
- [Trigger und Workflows für Entwickler](../teil-3-erweiterungsentwickler/trigger-und-workflows-fuer-entwickler.md)
- [Workflows](../../../anleitungen/workflows.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
