---
title: "Von Hand: ein Workflow-Ereignis"
audience: developer, app-creator
category: extension-development
order: 160
summary: Ein Workflow-Ereignistyp „Log publishing“ von Hand - die Klasse, die Registrierung, das Ansichts-Template, ein Workflow und ein Trigger sowie die Protokollzeile, die er schreibt.
keywords: Tutorial, von Hand, Workflow-Ereignis, Trigger, Veröffentlichen, Protokoll, tutorial, by hand, workflow event, eZWorkflowEventType, registerEventType, workflow.ini, AvailableEventTypes, trigger, content publish after, eZLog
type: help
---
# Von Hand: ein Workflow-Ereignis

Tutorial 15. Sie fügen einen Ereignistyp hinzu, der für jede veröffentlichte Version eine Zeile in
`var/log/mysite_hello.log` schreibt. Die Klasse hat `php -l` bestanden; mit geladenem Kernel hat
`eZWorkflowType::createType( 'event_mysitepublishlog' )` sie erzeugt, und `isAllowed( 'content', 'publish', 'after' )`
gab wahr zurück (und falsch für `before`).

## 1. Die Klasse

`extension/mysite_hello/eventtypes/event/mysitepublishlog/mysitepublishlogtype.php`:

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

## 2. Die Registrierung

`extension/mysite_hello/settings/workflow.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[EventSettings]
ExtensionDirectories[]=mysite_hello
AvailableEventTypes[]=event_mysitepublishlog

*/ ?>
```

## 3. Das Template, das der Workflow-Editor zeigt

`extension/mysite_hello/design/standard/templates/workflow/eventtype/view/event_mysitepublishlog.tpl`:

```text
<div class="element">
    <p>{'Log publishing'|i18n( 'extension/mysite_hello' )}</p>
</div>
```

Ein Ereignis ohne Einstellungen braucht kein Bearbeitungs-Template; fügen Sie
`workflow/eventtype/edit/event_mysitepublishlog.tpl` hinzu, sobald es Einstellungen erhält
([Workflow-Ereignistypen](../workflow-ereignistypen.md#einstellungen-eines-ereignisses)).

## 4. Einschalten und verdrahten

```bash
php -l extension/mysite_hello/eventtypes/event/mysitepublishlog/mysitepublishlogtype.php
php bin/php/ezcache.php --clear-all
```

1. **Setup > Workflows**: Legen Sie in einer Workflow-Gruppe „Log publishing“ an, fügen Sie das Ereignis „Log
   publishing“ hinzu und speichern Sie.
2. **Setup > Trigger**: Wählen Sie für `content` / `publish` / after den Workflow und speichern Sie.
3. Veröffentlichen Sie einen Artikel, dann:

   ```bash
   tail -n 3 var/log/mysite_hello.log
   ```

   zeigt eine Zeile `Published object <id> version <n>: <name>`.

## 5. Den Trigger danach entfernen

Ein Trigger läuft bei jeder Veröffentlichung auf der Website. Wenn Sie mit dem Ausprobieren fertig sind, setzen Sie den
Trigger wieder auf keinen Workflow zurück.

## Verwandte Seiten

- Zurück: [Von Hand: ein Datentyp](von-hand-datentyp.md)
- Weiter: [Von Hand: ein Cronjob-Teil](von-hand-cronjob-teil.md)
- [Workflow-Ereignistypen](../workflow-ereignistypen.md), [Mit RAD: ein Workflow-Ereignis](rad-workflow-ereignis.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
