---
title: The eZWorkflowType API
audience: developer
category: extension-development
order: 210
summary: The methods of a workflow event type - execute() and its status constants, allowed triggers, the settings form, cleanup - and its registration, with signatures and a tested example.
keywords: eZWorkflowType, eZWorkflowEventType, execute, STATUS_ACCEPTED, STATUS_REJECTED, STATUS_DEFERRED_TO_CRON, STATUS_FETCH_TEMPLATE, setTriggerTypes, registerEventType, fetchHTTPInput, validateHTTPInput, attributeDecoder, cleanup
type: help
---
# The eZWorkflowType API

Part 8, reference 17. A workflow event type is a step administrators can put into a workflow, which a trigger runs
before or after an operation (publishing, adding to the basket, signing in). It is a class that extends
`eZWorkflowEventType` (`kernel/classes/ezworkfloweventtype.php`, itself based on `eZWorkflowType` in
`kernel/classes/ezworkflowtype.php`). The how-to is [Workflow event types](../part-3-extension-developer/workflow-event-types.md)
and the tutorials [A workflow event, by hand](../part-3-extension-developer/tutorials/by-hand-workflow-event.md) and
[With RAD: a workflow event](../part-3-extension-developer/tutorials/rad-workflow-event.md).

## Registration

| Call | Notes |
|---|---|
| `public function __construct( $typeString = '', $name = '' )` | call from your constructor: `parent::__construct( 'myevent', ezpI18n::tr( 'extension/myext', 'My event' ) );` |
| `eZWorkflowEventType::registerEventType( $typeString, $class_name )` (static) | once, at the end of the file |
| `setTriggerTypes( $allowedTypes )` | which operations it may run in, e.g. `array( 'content' => array( 'publish' => array( 'before', 'after' ) ) )` |

Plus `workflow.ini [EventSettings] ExtensionDirectories[]` and `AvailableEventTypes[]=event_<typestring>`.

## Running: execute()

```php
function execute( $process, $event )
```

It returns one of the status constants of `eZWorkflowType`:

| Constant | Meaning |
|---|---|
| `STATUS_ACCEPTED` | done; the workflow goes on |
| `STATUS_REJECTED` | stop; the operation does not happen |
| `STATUS_DEFERRED_TO_CRON`, `STATUS_DEFERRED_TO_CRON_REPEAT` | run later in the workflow cronjob, once or until accepted |
| `STATUS_FETCH_TEMPLATE`, `STATUS_FETCH_TEMPLATE_REPEAT` | show a template to the user (a form), and run again after it is posted |
| `STATUS_REDIRECT`, `STATUS_REDIRECT_REPEAT` | redirect the user |
| `STATUS_WORKFLOW_CANCELLED`, `STATUS_WORKFLOW_DONE`, `STATUS_WORKFLOW_RESET`, `STATUS_RUN_SUB_EVENT`, `STATUS_NONE` | special cases |

`$process->attribute( 'parameter_list' )` holds the operation's parameters (for publishing: `object_id` and `version`).

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

## The event's settings form

When the administrator adds the event to a workflow, it can have settings of its own:

| Method | Use |
|---|---|
| `initializeEvent( $event )` | defaults for a new event |
| `validateHTTPInput( $http, $base, $event, &$validation )` | check the posted settings |
| `fetchHTTPInput( $http, $base, $event )` | store them into the event (its `data_int1`, `data_text1` ... fields) |
| `attributeDecoder( $event, $attr )` | computed attributes of the event for templates |
| `typeFunctionalAttributes()` | the names of those attributes |
| `customWorkflowEventHTTPAction( $http, $action, $workflowEvent )` | custom buttons in the form |
| `storeEventData( $event, $version )`, `workflowEventContent( $event )` | events with data in tables of their own |

Templates: `workflow/eventtype/edit/event_<typestring>.tpl` and `workflow/eventtype/view/event_<typestring>.tpl`, found
through the design chain.

## Cleanup

| Method | Use |
|---|---|
| `needCleanup()` | return `true` if `cleanup()` must run |
| `cleanup( $process, $event )` | when the process ends |
| `cleanupAfterRemoving( $attr = array() )` | when the event is removed from a workflow |

## Related pages

- Previous: [The eZDataType API](ezdatatype-api.md)
- Next: [Content functions from PHP](content-functions-from-php.md)
- [Triggers and workflows for developers](../part-3-extension-developer/triggers-and-workflows-for-developers.md)
- [Workflows](../../../guides/workflows.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
