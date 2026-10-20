---
title: "With RAD: a workflow event"
audience: developer, app-creator
category: extension-development
order: 50
summary: The Workflow event wizard writes an event type that logs every publication: triggers and statuses, the generated class and templates, and the one method to fill in.
keywords: RAD, workflow event wizard, setup/workflowevent, workflow event, eZWorkflowEventType, triggers, statuses, STATUS_ACCEPTED, event settings, execute, content/publish/after
type: help
---
# With RAD: a workflow event

Tutorial 5. The **Workflow event** wizard (`/setup/workflowevent`) writes a workflow event type: the class, its
registration, the templates the workflow editor shows, and, if you ask for them, settings per event stored in the
columns `eZWorkflowEvent` provides. The output below was produced by the wizard's generator
(`expWorkflowEventWizard::files()`); every PHP file passed `php -l`, and the generated class was created with
`eZWorkflowType::createType()` and asked `isAllowed()` with the kernel loaded.

## 1. The form

| Field | Value |
|---|---|
| Extension name | `mysite_hello` |
| Event identifier | `mysitepublishlog` |
| Name in the workflow editor | `Log publishing` |
| Triggers | `content / publish / after` |
| Statuses it can return | `STATUS_ACCEPTED` |

The wizard lists only triggers that exist (from the operation definitions) and always keeps `STATUS_ACCEPTED`: as its
code says, "An event with no way to say it is done would hang every workflow it is put in". Settings per event, if you
add any, are mapped onto the four integer and five text columns of `eZWorkflowEvent`; one that cannot get a column is
dropped "rather than silently sharing".

## 2. The files

```text
.gitignore
LICENSE
README.md
composer.json
design/standard/templates/workflow/eventtype/edit/event_mysitepublishlog.tpl
design/standard/templates/workflow/eventtype/view/event_mysitepublishlog.tpl
eventtypes/event/mysitepublishlog/mysitepublishlogtype.php
extension.xml
ezinfo.php
settings/design.ini.append.php
settings/workflow.ini.append.php
```

The registration:

```ini
[EventSettings]
ExtensionDirectories[]=mysite_hello
AvailableEventTypes[]=event_mysitepublishlog
```

The class (comments removed):

```php
<?php
class mysitepublishlogType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'mysitepublishlog';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING,
                             ezpI18n::tr( 'kernel/workflow/event', 'Log publishing' ) );
        $this->setTriggerTypes( array(
            'content' => array(
                'publish' => array( 'after' ) ) ) );
    }

    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        if ( isset( $parameters['object_id'] ) )
        {
            $object = eZContentObject::fetch( $parameters['object_id'] );
            if ( !$object instanceof eZContentObject )
            {
                eZDebug::writeNotice( 'Object ' . $parameters['object_id'] . ' is gone; nothing to do.',
                                      __METHOD__ );
                return eZWorkflowType::STATUS_ACCEPTED;
            }
        }
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( mysitepublishlogType::WORKFLOW_TYPE_STRING, 'mysitepublishlogType' );
```

The templates are in `design/standard`, so the workflow editor finds them in any administration design.

## 3. Fill in execute()

Add the work where the object is known:

```php
eZLog::write( sprintf( 'Published object %d version %d: %s',
                       (int)$parameters['object_id'], (int)$parameters['version'], $object->attribute( 'name' ) ),
              'mysite_hello.log' );
```

## 4. Switch it on and use it

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

**Setup > Workflows**: create a workflow with the event "Log publishing"; **Setup > Triggers**: connect it to
`content` / `publish` / after. Publish an article and read `var/log/mysite_hello.log`.

## Related pages

- Previous: [With RAD: a module with views](rad-module.md)
- Next: [With RAD: a design extension](rad-design-extension.md)
- [Workflow event types](../workflow-event-types.md), [By hand: a workflow event](by-hand-workflow-event.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
