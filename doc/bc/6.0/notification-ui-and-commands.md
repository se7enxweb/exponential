# Upgrade notes: notifications

What changed in the behaviour of the notification system, what you must do when you upgrade, and one known issue.
Read it if you run Exponential 6.0.15 or later, have overridden notification templates, or call the notification classes
from your own code. The pages that describe the system as it is now: [the user's guide](../../features/6.0/notifications.md),
[the administrator's guide](../../guides/notifications-administrator.md), [the specification](../../specifications/6.0/notifications.md).

## What you need to do

1. Make sure the cronjob part `notification` runs: it is in the group `frequent`, or run it alone with
   `php runcronjobs.php notification`. Open **Setup > Notification**: a red notice tells you if events wait and no run is recorded.
2. If you override `notification/addingresult.tpl` or the settings templates in your own design, read the sections below.
   Designs that do not override them need nothing.
3. Clear the caches after the upgrade (`php bin/php/ezcache.php --clear-all --allow-root-user`) and reload PHP-FPM or
   restart Velocity, as for any code change.
4. Optionally remove the events and subscriptions that piled up (section "Cleaning up").

## Behaviour changes

### Opening /notification/addtonotification/<node> no longer subscribes

Before: a GET of the address created the subscription at once (the content action redirected there after a form post). A link
or an image on any page could subscribe a logged-in user. Now: a GET shows a confirmation page ("Notify me about updates");
the buttons are a POST that carries the form token, and only **Notify me** subscribes. For an item the user already follows
the page offers **Stop notifications for this item**. The content menu entry **Notify me** (the form field
`ActionAddToNotification`) works as before and now ends on the confirmation page, one click more.

Templates: the view renders `notification/addconfirm.tpl` (new), then `addingresult.tpl` (as before) after adding, or the
new `removeresult.tpl` after removing. `design/standard` and `design/admin` provide `addconfirm.tpl` and `removeresult.tpl`; a design
that falls back to them needs nothing. If your design overrides `addingresult.tpl`, it is still used; it now receives
`redirect_uri` besides `redirect_url`. Code that links to the address and expects it to subscribe must post the form instead:
`POST /notification/addtonotification/<node>` with `ConfirmAddNotification` (and the form token).

### The settings page

admin4 and admin4l have a new overview (see the user's guide). The form fields of the handler templates keep their names
(`NewRule_ezsubtree`, `RemoveRule_ezsubtree`, `SelectedRuleIDArray_ezsubtree[]`, `Store`, `ReceiveDigest_ezgeneraldigest`,
`DigestType_`, `Time_`, `Weekday_`, `Monthday_`, `CollaborationHandlerSelection...`), so templates of other designs keep working.
New: `UseConfirm` in the subtree form makes the view ask before removing (a form without it removes at once, as before);
`FilterSubscriptions` and `ClearFilter`; the collaboration card saves with `SaveCollaboration` and no longer shares the `Store` button
(a `Store` post turns the digest off when its fields are missing, so a form that posts `Store` must carry the digest fields).
The view accepts the new URL parameters `(q)` and `(class)` besides `(offset)`. The view passes more template variables
(`subscriptions`, `subscription_total`, `subscription_all_total`, `subscription_limit`, `subscription_classes`, `filter_query`,
`filter_class`, `confirm_remove`, `notice`, `can_administrate`); a template of an included handler must have them passed
explicitly with `include`.

The settings are checked on save now: a digest type other than daily, weekly or monthly becomes daily, a time that is not one
of the offered hours becomes `0:00`, a weekday outside the week becomes the first weekday, a day of the month is held to 1 to 31.
A user who never opened the page (no settings row yet) can save digest settings. The subtree form refuses nodes the user may not
read, nodes that do not exist and the same node twice, and only removes the user's own rules.

### Handled events are cleaned up

Before: an event whose messages were kept for a digest stayed in the table for good after the digest was sent. Now
`eZNotificationEventFilter::process()` ends by removing handled events that nothing waits for
(`eZNotificationEvent::cleanupHandled()`). On a test installation, 7 such events were removed by a run. The status page and
`exp:notification:events cleanup` do the same on demand, and can remove events by age.

### The filter

`eZNotificationEventFilter::process( $eventIDList = null )` takes an optional list of event ids and returns an array (`events`,
`removed`, `kept`, `failed`) where it returned nothing. A handler that throws is logged and counted; the other handlers still
run and the event is not retried. Before, the error stopped the whole pass and left the rest of the events. An undefined
variable in the message for a missing handler is fixed.

### Smaller fixes

| Where | Before | Now |
|---|---|---|
| `eZNotificationCollection::addItem( $address, $sendDate )` | the send date was ignored (always 0) | it is stored |
| `eZNotificationCollectionItem::fetchByDate( $date )` | the condition overwrote itself and found nothing sensible | finds the items due before `$date` |
| `eZGeneralDigestHandler::fetchHandlersForUser()` | an undefined index for a handler that is listed under another name | the handler is found by id, or left out |
| digest `handle()` | an undefined template variable when no one had a digest due | handled |
| weekly digest | an undefined index when the cronjob ran in another language than the user's | the stored weekday name, the English name and 0 to 6 are accepted |
| `eZCollaborationNotificationHandler::rules()` | looked the rules up by e-mail address | by user id |
| `eZNotificationEventType::allowedTypes()` | read an undefined global and a setting (`AvailableEventTypes`) that does not exist | reads `AvailableNotificationEventTypes` |
| `eZMailNotificationTransport::send()` | a single address given as a string failed | accepted |
| `eZNotificationFunctionCollection::eventContent()` | a fatal error for an unknown event id | returns false |

### The mail transports

`eZFileTransport` writes to `[MailSettings] FileTransportDirectory` of `site.ini`. Not set or empty: `var/log/mail`, as before.
`eZMailNotificationTransport::observe( $callback, $suppress )` is new (used by the status numbers and the dry run).

### Cronjob

The part `notification` goes through `expNotificationService::run()`: locked (a second run is skipped with a message),
recorded, and shown on the status page. `[CronjobPart-notification]` in `cronjob.ini` is new. The output lines "Starting
notification event processing" and "Done" are kept; "Done" now says how many events and messages.

### New: status page, commands, views

`notification/status` and `notification/job` (policy `notification/administrate`), the Setup menu entry "Notification"
(`menu.ini [Leftmenu_setup] Links[notification]`, which was commented out since 3.5), and the commands
`exp:notification:status|run|events|subscriptions` with the aliases `exp:notify:*`. `notification/runfilter` keeps working and
goes through the service. Add the policy `notification/administrate` to roles that should see them (administrators have it).

## Cleaning up after the upgrade

A site where the cronjob did not run for a long time has many pending events. One run handles them all (4 seconds for
7259 events on the test installation). Dry-run first if you like:

```bash
./console exp:notification:status
./console exp:notification:run --dry-run
./console exp:notification:events cleanup --dry-run
./console exp:notification:subscriptions list --missing
```

Remove events that are older than you care about with `exp:notification:events cleanup --older-than=90d` (see
[the commands](../../specifications/6.0/notifications-cli.md)). Mail that these old events would have caused is sent by the
run that handles them: if the backlog is old, remove it first, or run once with `--mail-file-dir` to discard the mail.

## Known issue

**Digest items are removed even when the mail transport fails.** The general digest handler
(`eZGeneralDigestHandler::handle()`) sends each digest with `eZNotificationTransport::send()` and afterwards removes every
collection item that went into any digest of that run, without looking at the result of `send()`. When the transport fails
(the mail server is down, the address is refused), the digest is lost: the items are gone and nothing is retried. Mail
that is sent at once by the subtree and collaboration handlers has the same property: its items are removed before
`send()`.

What to do until it is fixed: watch the mail log of the server, keep the mail transport healthy, and use a transport
that queues (a local MTA in `sendmail` mode accepts the mail and retries by itself). The notification run records the number
of messages handed to the transport, not the number delivered. A fix would remove the items of an address only when its
`send()` returned true.

## Related pages

- [The user's guide](../../features/6.0/notifications.md), [the administrator's guide](../../guides/notifications-administrator.md), [the developer's guide](../../guides/notifications-developer.md)
- [The specification](../../specifications/6.0/notifications.md), [the INI reference](../../specifications/6.0/notifications-ini.md), [the command reference](../../specifications/6.0/notifications-cli.md)
- [Commands, cronjob parts and views as classes](cli_cronjob_view_abstractions.md)
