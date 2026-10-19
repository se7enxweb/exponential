---
title: "Lesson 10.2: A workflow event type of your own"
audience: trainer, developer
category: training
order: 102
summary: Learners write the event type Log publishing in their extension, register it in workflow.ini, add its edit and view templates, put it in a workflow on the after-publish trigger, and read the log line it writes. 60 minutes.
keywords: developers course, workflow event type, eZWorkflowEventType, execute, STATUS_ACCEPTED, STATUS_DEFERRED_TO_CRON, STATUS_WORKFLOW_CANCELLED, registerEventType, setTriggerTypes, workflow.ini, EventSettings, ExtensionDirectories, AvailableEventTypes, event_ templates, eZLog
type: help
---
# Lesson 10.2: A workflow event type of your own

An event type is code that runs as a step of a workflow: check something, change something, or wait. In this lesson
learners write the event type **Log publishing**, which writes a line to a log file every time a version is
published. It is small enough to finish in class and shows every part a real event type needs.

> **Note**
> Use your own training installation: the exercise connects a workflow to publishing.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 10: Workflows, events and cronjobs](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Advanced |
| Audience | Developers |
| Prerequisites | [Lesson 10.1](workflows-and-triggers.md) |

## Learning objectives

After this lesson, learners can:

- write an event type class extending `eZWorkflowEventType` with `execute()`;
- return the right status: `STATUS_ACCEPTED`, `STATUS_REJECTED`, `STATUS_DEFERRED_TO_CRON` or
  `STATUS_WORKFLOW_CANCELLED`;
- limit the triggers the event may be used with through `setTriggerTypes()`;
- register it in `workflow.ini [EventSettings]` and give it edit and view templates;
- use it in a workflow on the after-publish trigger and check its effect.

## Trainer's notes

### How to present it

Sources: `kernel/classes/ezworkflowtype.php` (statuses and loading), a kernel event type such as
`kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatetype.php`, and an extension's event type, for
example `extension/swark/eventtypes/event/autopriority/` if installed.

1. **Where the kernel looks** (4 minutes). For the type string `event_mysitelogpublish`, `eZWorkflowType` looks for
   `eventtypes/event/mysitelogpublish/mysitelogpublishtype.php` in every extension of `[EventSettings]
   ExtensionDirectories[]`. The type name contains no underscore: the part after `event_` is the folder.
2. **The class** (6 minutes). The constructor calls `parent::__construct( <type>, <name> )` and may limit triggers:
   `setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) )`. `execute( $process, $event )`
   reads `$process->attribute( 'parameter_list' )` (for publishing: `object_id` and `version`) and returns a status.
   The file ends with `eZWorkflowEventType::registerEventType( <type>, <class> )`.
3. **Statuses** (4 minutes). `STATUS_ACCEPTED` goes on; `STATUS_REJECTED` stops the operation;
   `STATUS_DEFERRED_TO_CRON` waits for the workflow cronjob; `STATUS_WORKFLOW_CANCELLED` ends the workflow.
4. **Templates** (2 minutes). The workflow editor includes `workflow/eventtype/edit/event_<type>.tpl` and
   `view/event_<type>.tpl`; an event without settings ships two near-empty templates.
5. **Before or after** (2 minutes). Logging is an *after* job. Checks that may stop publishing are *before* jobs.

### Common questions

**Can the event have settings in the workflow editor?** Yes: implement `fetchHTTPInput()` and store values in the
event's `data_*` fields, as the kernel's event types do; the edit template shows the form.

**Can it wait for something?** Return `STATUS_DEFERRED_TO_CRON` (or `_REPEAT`); the workflow cronjob runs it again.

**Is there a wizard?** **Setup** > **RAD**, the workflow event wizard (`/setup/workflowevent`), writes event types and
triggers.

### Pitfalls

- **An underscore in the type name.** `event_mysite_log` would look in the folder `mysite`; keep the name one word.
- **Slow work in a before event.** The editor waits for it. Defer heavy work to the cronjob or a content job.
- **Exceptions.** An uncaught exception in `execute()` breaks publishing for everyone; catch, log and return a status.

## Hands-on exercise

On your training installation, in `extension/mysite_hello`.

1. Create `eventtypes/event/mysitelogpublish/mysitelogpublishtype.php`:

   ```php
   <?php
   /**
    * Workflow event "Log publishing": writes one line per published version to var/log/mysite_publish.log.
    */
   class mysiteLogPublishType extends eZWorkflowEventType
   {
       const WORKFLOW_TYPE_STRING = 'mysitelogpublish';

       public function __construct()
       {
           parent::__construct( self::WORKFLOW_TYPE_STRING, 'Log publishing' );
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
               return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;
           }

           eZLog::write( 'Published object ' . $objectID . ' version ' . $version
                         . ' (' . $object->attribute( 'class_identifier' ) . ')', 'mysite_publish.log' );
           return eZWorkflowType::STATUS_ACCEPTED;
       }
   }

   eZWorkflowEventType::registerEventType( mysiteLogPublishType::WORKFLOW_TYPE_STRING, 'mysiteLogPublishType' );
   ```

2. Register it in `settings/workflow.ini.append.php`:

   ```bash
   php bin/php/console exp:ini add workflow.ini/EventSettings/ExtensionDirectories mysite_hello extension:mysite_hello
   php bin/php/console exp:ini add workflow.ini/EventSettings/AvailableEventTypes event_mysitelogpublish extension:mysite_hello
   ```

3. Create the two templates, each with the single line `{* No settings. *}`:
   `design/standard/templates/workflow/eventtype/edit/event_mysitelogpublish.tpl` and
   `design/standard/templates/workflow/eventtype/view/event_mysitelogpublish.tpl`.
4. Check the PHP file with `php -l`; clear the `ini` and template caches.
5. In **Setup** > **Workflows**, create the workflow `Course: log publishing` and add the event **Log publishing**
   (it is listed among the events).
6. In **Setup** > **Triggers**, connect it to publishing content, after, and apply.
7. Publish an article in your course folder.
8. Read the log: `tail -3 var/log/mysite_publish.log`.
9. Remove the workflow from the trigger again, and commit the files.

## Expected result

- Step 5: **Log publishing** is offered in the event list of the workflow editor.
- Step 7: the article is published as usual.
- Step 8: a line with the date, `Published object <ID> version <n> (article)`.
- The trainer checks one learner's log line.

## Quiz

1. Where does the kernel look for the event type `event_mysitelogpublish` of an extension?
2. Which status lets the operation continue?
   A. `STATUS_ACCEPTED` B. `STATUS_REJECTED` C. `STATUS_DEFERRED_TO_CRON` D. `STATUS_WORKFLOW_CANCELLED`
3. Which two settings register an extension's event type?
4. Why should the type name not contain an underscore?
5. Why is logging an *after* job?

<details>
<summary>Answers</summary>

1. `extension/<ext>/eventtypes/event/mysitelogpublish/mysitelogpublishtype.php`, for extensions listed in
   `[EventSettings] ExtensionDirectories[]`.
2. **A.** `STATUS_ACCEPTED`.
3. `workflow.ini [EventSettings] ExtensionDirectories[]` and `AvailableEventTypes[]=event_<type>`.
4. The type string is split at underscores; the part after `event_` must be the folder and file name.
5. It records something that happened and must never hold publishing back.

</details>

## Further reading

- [Workflows: approvals, triggers and the processes that wait](../../../../guides/workflows.md).
- [RAD tools](../../../../features/6.0/rad-tools.md): the workflow event wizard.
- [Debug output and logs for developers](../../../extension-development/part-1-from-user-to-developer/debug-output-and-logs-for-developers.md):
  `eZLog::write()`.

## Related pages

- [Module 10: Workflows, events and cronjobs](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
