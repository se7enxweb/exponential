---
title: Workflows and triggers as integration hooks
audience: integrator, developer
category: integration
order: 170
summary: Using workflow events on triggers as the "on publish" hook of an integration - before and after triggers, an outbox event that queues changes for a cronjob, an external check that accepts or rejects a publication, deferring to the cronjob, which operations can be triggered, and the traps.
keywords: workflow, trigger, workflow event, eZWorkflowEventType, content publish after, content publish before, outbox, STATUS_ACCEPTED, STATUS_REJECTED, STATUS_DEFERRED_TO_CRON, AvailableOperationList, eZOperationHandler, on publish hook, push integration
type: help
---
# Workflows and triggers as integration hooks

This page is for integration developers who need Exponential to tell another system **when something happens**, or
to ask another system **before something happens**. The kernel runs operations (publish, move, hide, checkout ...)
through `eZOperationHandler`; each operation can have a **trigger** before and after it, and a builder connects a
**workflow** to a trigger. A workflow is a list of **events**, and an event type is a class your extension provides.
The developer chapters are [Workflow event types](../extension-development/part-3-extension-developer/workflow-event-types.md)
and [Triggers and workflows for developers](../extension-development/part-3-extension-developer/triggers-and-workflows-for-developers.md);
the administration is described in [Workflows](../../guides/workflows.md).

## Contents

1. [Before or after](#before-or-after)
2. [The outbox pattern: notice fast, send later](#the-outbox-pattern-notice-fast-send-later)
3. [An external check before publishing](#an-external-check-before-publishing)
4. [Which operations can be triggered](#which-operations-can-be-triggered)
5. [Connecting the event](#connecting-the-event)
6. [Traps](#traps)

## Before or after

| Trigger | Use it for | Statuses that make sense |
|---|---|---|
| `content / publish / before` | Asking another system or a person whether this version may be published | `STATUS_ACCEPTED`, `STATUS_REJECTED`, `STATUS_DEFERRED_TO_CRON` |
| `content / publish / after` | Telling other systems that a version was published (search, CDN, CRM, archive) | `STATUS_ACCEPTED` |
| `shop / checkout` and `shop / confirmorder` (before) | Passing an order to an ERP, checking stock, payment | `STATUS_ACCEPTED`, `STATUS_REJECTED`, redirects for payment |

An event that only reacts and never waits or rejects can also be an **event listener** ([Event listeners for
integrations](event-listeners-for-integrations.md)), which needs no workflow configuration. A workflow is the right
choice when builders must be able to see and switch the hook in the administration.

## The outbox pattern: notice fast, send later

The event runs inside the editor's publishing request. A slow or failing remote call there makes editors wait or
breaks publishing. So the event only **records** the change; a cronjob part **sends** it, with retries.

`extension/myintegration/settings/workflow.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[EventSettings]
ExtensionDirectories[]=myintegration
AvailableEventTypes[]=event_myintegrationoutbox

*/ ?>
```

`extension/myintegration/eventtypes/event/myintegrationoutbox/myintegrationoutboxtype.php`:

```php
<?php
/**
 * Workflow event "Queue for the CRM": records each published version in myintegration_outbox.
 * Use it on the trigger content / publish / after. The cronjob part myintegration_outbox sends the rows.
 */
class myIntegrationOutboxType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'myintegrationoutbox';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING,
                             ezpI18n::tr( 'extension/myintegration', 'Queue for the CRM' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    public function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $objectID = isset( $parameters['object_id'] ) ? (int)$parameters['object_id'] : 0;
        $version = isset( $parameters['version'] ) ? (int)$parameters['version'] : 0;
        if ( $objectID > 0 )
        {
            $db = eZDB::instance();
            $db->query( 'INSERT INTO myintegration_outbox ( object_id, version, created, sent )'
                      . ' VALUES ( ' . $objectID . ', ' . $version . ', ' . time() . ', 0 )' );
        }
        // never block publishing because of the integration
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( myIntegrationOutboxType::WORKFLOW_TYPE_STRING, 'myIntegrationOutboxType' );
```

The table `myintegration_outbox` is part of your extension's schema
([Database schema and dba files](../extension-development/part-3-extension-developer/database-schema-and-dba-files.md)).
The cronjob part reads unsent rows, sends them, and marks them sent; a failed send stays unsent and is tried again
on the next run ([Writing an export or an outbound sync](writing-an-export-or-outbound-sync.md)).

## An external check before publishing

On `content / publish / before`, an event can ask another system (a legal check, a product master) and decide:

| Return | Effect |
|---|---|
| `STATUS_ACCEPTED` | The next event runs, then the publication continues |
| `STATUS_REJECTED` | The publication does not happen |
| `STATUS_DEFERRED_TO_CRON` | The process waits; the workflow cronjob (`workflow.php`, part `frequent`) runs the event again later, and when it finally returns accepted the cronjob finishes the publication |

Deferring is the safe choice when the other system answers slowly or asynchronously: store the request on the first
run, and on later runs look only at **stored state** (the answer that arrived), never at the request that started
the process. An event that defers must be safe to run many times. Editors see the version waiting, and administrators
see the process at **Setup > Workflow processes**.

## Which operations can be triggered

`settings/workflow.ini [OperationSettings] AvailableOperationList[]` decides which operations appear in
**Setup > Triggers**. Shipped: `content_publish`, `before_shop_confirmorder`, `shop_checkout`, `shop_addtobasket`,
`shop_updatebasket`. The file lists others commented out (`content_move`, `content_hide`, `content_addlocation`,
`content_removelocation`, `content_sort`, `content_swap` ...). To offer one, add it in your extension:

```ini
[OperationSettings]
AvailableOperationList[]=content_hide
```

The kernel warns that the code behind the extra operations may change without the usual compatibility rules; test
after every upgrade ([Upgrades without breaking integrations](upgrades-without-breaking-integrations.md)).

Code of yours that performs such an action should call the operation, so the triggers run:

```php
<?php
$result = eZOperationHandler::execute( 'content', 'publish',
                                       array( 'object_id' => $objectID, 'version' => $versionNumber ) );
```

## Connecting the event

1. Regenerate the autoloads (`php bin/php/ezpgenerateautoloads.php -e`) and clear the INI cache.
2. **Setup > Workflows**: create a workflow in a group, add the event "Queue for the CRM".
3. **Setup > Triggers** (`/trigger/list`): connect the workflow to `content / publish / after`.
4. Publish a test object and check that a row arrived in your outbox.

One trigger takes one workflow (the table `eztrigger` is unique per module, operation and before or after). If an approval workflow already sits on `content / publish / before`, add your event
to that workflow, or use the multiplexer event to run several workflows.

## Traps

- **Imports publish too.** Content created with `eZContentFunctions::createAndPublishObject()` runs the publish
  operation and its triggers. A bulk import therefore fills your outbox; decide whether that is wanted, and filter by
  creator or class in `execute()` if not.
- **Approval on the same trigger.** With an *Approve* event before publishing, imported versions wait for approval.
  Give the import's service account a section or class the approval does not cover, or let it publish elsewhere.
- **Never echo output** in `execute()`, and catch exceptions: an exception aborts publishing for the editor.
- **Velocity.** A new event type class needs `exp:velocity restart` (or `exp:velocity deploy`) before Velocity workers
  know it.

## Related pages

- [Workflows](../../guides/workflows.md)
- [Workflow event types](../extension-development/part-3-extension-developer/workflow-event-types.md)
- [The eZWorkflowType API](../extension-development/part-8-reference/ezworkflowtype-api.md)
- [Writing a workflow event type](../creators/app-creators/writing-a-workflow-event-type.md)
- [Outgoing webhooks from the audit trail](outgoing-webhooks-from-the-audit-trail.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
