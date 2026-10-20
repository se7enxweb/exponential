---
title: A workflow event
audience: developer, admin
category: examples
order: 70
summary: Write the workflow event explogpublish, which logs every object published, as an eZWorkflowEventType subclass registered in workflow.ini, and connect it to the publish trigger in Setup > Workflows and Setup > Triggers.
keywords: workflow, workflow event, event type, eZWorkflowEventType, eZWorkflowType, execute, STATUS_ACCEPTED, setTriggerTypes, workflow.ini, EventSettings, AvailableEventTypes, ExtensionDirectories, trigger, content/publish, event_view_gui
type: example
---
# A workflow event

A workflow is a list of events the kernel runs when an operation happens (publishing, a sign-in, a checkout),
connected to the operation by a trigger, *before* or *after* it. You write the event **Log publishing**
(`explogpublish`): it writes one line per published object to `var/log/expexample.log` and lets the publishing go on.
Then you build a workflow with it and connect it to *After publishing content*.

Prerequisites: the extension of [An extension skeleton](extension-skeleton.md), switched on; the `workflow` and
`trigger` policies (an administrator).

## 1. Where the kernel looks

`eZWorkflowType::loadAndRegisterType()` includes `<directory>/<group>/<type>/<type>type.php` from `workflow.ini
[EventSettings] RepositoryDirectories[]` and from `eventtypes/` of each extension in `ExtensionDirectories[]`. The
group of an event is `event`; `AvailableEventTypes[]` names `<group>_<type>`.
`extension/expexample/settings/workflow.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[EventSettings]
# Look for eventtypes/event/<type>/<type>type.php in this extension
ExtensionDirectories[]=expexample
AvailableEventTypes[]=event_explogpublish

*/ ?>
```

## 2. The event class

`extension/expexample/eventtypes/event/explogpublish/explogpublishtype.php`:

```php
<?php
/**
 * The workflow event "Log publishing": writes one line to var/log/expexample.log for every object published
 * through a workflow that contains the event, then lets the operation carry on.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

class expLogPublishType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'explogpublish';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'extension/expexample', 'Log publishing' ) );
        // Offered only for these triggers: content/publish, before and after
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'before', 'after' ) ) ) );
    }

    /**
     * @param eZWorkflowProcess $process
     * @param eZWorkflowEvent $event
     * @return int an eZWorkflowType::STATUS_* constant
     */
    public function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $objectID = isset( $parameters['object_id'] ) ? (int)$parameters['object_id'] : 0;
        $version  = isset( $parameters['version'] ) ? (int)$parameters['version'] : 0;

        $object = eZContentObject::fetch( $objectID );
        if ( !$object instanceof eZContentObject )
            return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;

        eZLog::write( sprintf( 'published object %d version %d (%s) by user %d',
                               $objectID, $version, $object->attribute( 'class_identifier' ),
                               eZUser::currentUserID() ),
                      'expexample.log' );

        // ACCEPTED: the next event of the workflow runs, then the operation itself
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( expLogPublishType::WORKFLOW_TYPE_STRING, 'expLogPublishType' );
```

- `parameter_list` holds the parameters of the operation; for `content/publish` they are `object_id` and `version`
  (`kernel/content/operation_definition.php`).
- `execute()` returns one of the `eZWorkflowType::STATUS_*` constants:

| Status | Means |
|---|---|
| `STATUS_ACCEPTED` | done; the next event runs, then the operation |
| `STATUS_REJECTED` | the operation does not happen |
| `STATUS_DEFERRED_TO_CRON`, `STATUS_DEFERRED_TO_CRON_REPEAT` | wait; the workflow cronjob runs the event again later (approval does this) |
| `STATUS_FETCH_TEMPLATE`, `STATUS_REDIRECT` | show a page or send the user elsewhere, then come back |
| `STATUS_WORKFLOW_CANCELLED` | stop the workflow |

Keep an event on the *before* side short: the editor waits for it. Slow work (a remote call, a mail) belongs
*after* publishing, or deferred to the cronjob.

## 3. Its templates

The workflow pages show each event with `event_view_gui` and `event_edit_gui`, which look for
`workflow/eventtype/view/<workflow type string>.tpl` and `.../edit/...` (`event_explogpublish.tpl`). This event has no
settings, so both are short. In `extension/expexample/design/standard/templates/`:

`workflow/eventtype/view/event_explogpublish.tpl`:

```tpl
{* The event in the workflow view: it has no settings to show *}
<p>{'Writes a line to var/log/expexample.log for each published object.'|i18n( 'extension/expexample' )}</p>
```

`workflow/eventtype/edit/event_explogpublish.tpl`:

```tpl
{* The event in the workflow edit form: no settings *}
<p>{'This event has no settings.'|i18n( 'extension/expexample' )}</p>
```

An event with settings stores them in the `ezworkflow_event` row (`data_int1`, `data_text1` ...) through
`fetchHTTPInput()` and `validateHTTPInput()`; `kernel/classes/workflowtypes/event/ezwaituntildate/` is a complete
example.

## 4. Switch it on

```bash
php -l extension/expexample/eventtypes/event/explogpublish/explogpublishtype.php
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=template-override --allow-root-user
```

Restart Velocity, or [deploy](velocity-deploy.md), if it serves the site.

1. **Setup > Workflows**: open a group (or create one), **New workflow**, name it *Log publishing*.
2. Choose the event type *Event / Log publishing*, **Add event**, **OK**.
3. **Setup > Triggers**: in the row **After publishing content** (`content/publish`, after) choose *Log publishing*,
   **Apply changes**. One workflow per trigger: to run this beside another, put both in a workflow with a
   *Multiplexer* event.

## 5. Check it

Publish any object, then:

```bash
tail -n 3 var/log/expexample.log
```

Expected: a line `published object <id> version <n> (<class identifier>) by user <id>`. With `VarDir` per site and
`UseGlobalLogDir=disabled` the log is in the site's log directory instead.

Checked for this page (2026-10-18): `php -l` on the class; `bin/php/eztemplatecheck.php` reports both templates valid
(syntax); the base class, the statuses and the template lookup are those of `kernel/classes/ezworkfloweventtype.php`,
`kernel/classes/ezworkflowtype.php` and `kernel/common/eztemplateautoload.php`. The event was not run against a database
for this page.

## Common mistakes

| Symptom | Cause |
|---|---|
| The type is not in the list of event types | `AvailableEventTypes[]` lacks `event_explogpublish`, or `ExtensionDirectories[]` is missing; the debug output names the directories searched |
| *Workflow type not found* in the log | the folder or file name differs from the type string (`explogpublish/explogpublishtype.php`) |
| Publishing hangs or stays a draft | `execute()` returned nothing (`STATUS_NONE`) or `REJECTED`; look at **Setup > Workflow processes** |
| The workflow does not run for the trigger | it is connected to *before* when you expected *after*, or another workflow holds the trigger |

## Derived from

- `kernel/classes/ezworkflowtype.php` (`loadAndRegisterType()`, the `STATUS_*` constants, `setTriggerTypes()`)
- `kernel/classes/ezworkfloweventtype.php`, `kernel/classes/workflowtypes/event/ezfinishuserregister/ezfinishuserregistertype.php`
- `kernel/common/eztemplateautoload.php` (`event_view_gui`, `event_edit_gui`), `settings/workflow.ini`
- `doc/eng-us/guides/workflows.md`, section 3

## Related pages

- [Cookbook](README.md)
- [Workflows: approvals, triggers and the processes that wait](../../guides/workflows.md)
- [Workflow events](../workflow-events/README.md): the event types of the kernel
- [A cronjob](cronjob.md): the workflow cronjob resumes deferred events
- [RAD tools](../../features/6.0/rad-tools.md): `/setup/workflowevent` writes an event skeleton
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
