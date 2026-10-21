---
title: Einen Workflow-Ereignistyp schreiben
audience: app-creator, developer
category: app-creation
order: 270
summary: Einen eigenen Workflow-Schritt schreiben - die Unterklasse von eZWorkflowEventType, Trigger-Typen, execute() und seine Status, Ereigniseinstellungen im Workflow-Editor, Registrierung in workflow.ini und Testen mit dem Workflow-Cronjob.
keywords: Workflow-Ereignistyp, Workflow, Trigger, Status, Registrierung, Anwendungsentwickler, workflow event type, eZWorkflowEventType, execute, STATUS_ACCEPTED, STATUS_DEFERRED_TO_CRON, setTriggerTypes, registerEventType, AvailableEventTypes, workflow.ini, app creator
type: help
---
# Einen Workflow-Ereignistyp schreiben

Diese Seite richtet sich an Anwendungsentwickler, die einen Schritt hinzufügen, den Administratoren in Workflows
einsetzen können: beim Veröffentlichen ein externes System benachrichtigen, vor dem Veröffentlichen ein Feld prüfen, auf
eine Freigabe von außen warten. **Setup > RAD > Workflow-Ereignis-Erweiterung** (`/setup/workflowevent`) schreibt ein
funktionierendes Gerüst ([RAD-Werkzeuge](rad-werkzeuge.md)).

![Setup, RAD, der Assistent für Workflow-Ereignisse, der das Gerüst eines Ereignistyps schreibt](../../../../eng-us/help/images/creators/workflow-event-wizard.webp)

## Inhalt

1. [Dateien und Registrierung](#dateien-und-registrierung)
2. [Die Klasse](#die-klasse)
3. [Status](#status)
4. [Einstellungen je Ereignis](#einstellungen-je-ereignis)
5. [Testen](#testen)

## Dateien und Registrierung

```text
extension/expdemo/eventtypes/event/expnotify/expnotifytype.php
extension/expdemo/settings/workflow.ini.append.php
```

```ini
<?php /* #?ini charset="utf-8"?
[EventSettings]
ExtensionDirectories[]=expdemo
AvailableEventTypes[]=event_expnotify
*/ ?>
```

Einträge in `AvailableEventTypes[]` bestehen aus der Gruppe und der Typ-Zeichenkette, verbunden durch `_`, wie die
mitgelieferten `event_ezapprove` und `event_ezwaituntildate` in `settings/workflow.ini`.

## Die Klasse

```php
<?php
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

        // Do not call slow services here: queue the work instead
        expDemoArchiveQueue::add( $object->attribute( 'id' ), (int)$parameters['version'] );
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( expNotifyType::WORKFLOW_TYPE_STRING, 'expNotifyType' );
```

Das folgt dem mitgelieferten `eZWaitUntilDateType` (`kernel/classes/workflowtypes/event/ezwaituntildate/`).
`setTriggerTypes()` schränkt ein, mit welchen Triggern das Ereignis verwendet werden darf.

## Status

`eZWorkflowType` (`kernel/classes/ezworkflowtype.php`) definiert die Antworten von `execute()`:

| Status | Wirkung |
|---|---|
| `STATUS_ACCEPTED` | der Schritt ist erledigt; der nächste läuft |
| `STATUS_REJECTED` | die Operation wird abgelehnt |
| `STATUS_DEFERRED_TO_CRON`, `STATUS_DEFERRED_TO_CRON_REPEAT` | warten; der Workflow-Cronjob führt den Schritt erneut aus |
| `STATUS_FETCH_TEMPLATE`, `STATUS_FETCH_TEMPLATE_REPEAT` | dem Benutzer ein Template zeigen (eine Auswahl, ein Formular) |
| `STATUS_REDIRECT`, `STATUS_REDIRECT_REPEAT` | den Benutzer woandershin schicken (zu einem Zahlungsanbieter) |
| `STATUS_WORKFLOW_CANCELLED` | den Workflow stoppen |
| `STATUS_RUN_SUB_EVENT`, `STATUS_WORKFLOW_RESET`, `STATUS_WORKFLOW_DONE` | besondere Abläufe (Multiplexer, Zurücksetzen) |

Halten Sie `execute()` schnell: Es läuft innerhalb des Veröffentlichens durch den Redakteur. Langsame Arbeit gehört in
eine Warteschlange ([Muster für Hintergrundarbeit](muster-fuer-hintergrundarbeit.md)).

## Einstellungen je Ereignis

Einstellungen, die ein Administrator dem Ereignis im Workflow-Editor gibt, werden in den Spalten des Ereignisses
gespeichert (`data_int1`, `data_text1` ...). Überschreiben Sie `fetchHTTPInput()` und `validateHTTPInput()`, um sie zu
lesen, und liefern Sie in Ihrem Design Templates zum Bearbeiten und Anzeigen aus,
`workflow/eventtype/edit/event_expnotify.tpl` und `workflow/eventtype/view/event_expnotify.tpl`, wie die
`event_ezapprove.tpl` des Kernels.

## Testen

1. Generieren Sie die Autoloads neu; leeren Sie den INI-Cache.
2. **Setup > Workflows**: Legen Sie einen Workflow mit dem Ereignis an; **Setup > Trigger**: Verbinden Sie ihn mit
   `content/publish/after`.
3. Veröffentlichen Sie ein Objekt; prüfen Sie die Wirkung.
4. Bei verschiebenden Ereignissen: Führen Sie `php runcronjobs.php frequent` aus und beobachten Sie
   **Setup > Workflow-Prozesse**.
5. Testen Sie die Entscheidungslogik als Unit-Test in einer separaten Klasse ohne den Workflow.

## Verwandte Seiten

- [Workflows und Ereignisse](workflows-und-ereignisse.md)
- [Erweiterungspunkte: Workflow-Ereignistyp](../../../bc/6.0/rad-erweiterungspunkte.md#workflow-ereignistyp)
- [Workflows](../../../anleitungen/workflows.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
