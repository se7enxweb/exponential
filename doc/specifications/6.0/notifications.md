# Specification: the notification system

The reference for how notifications are made and handled: the tables, the flow from a published item to a mail, the
handlers and their rules, the digest, the service class with its lock, run record and audit event, the module views,
the templates, the policies and the audit. Read it when you change the system, write a handler, or need an exact answer.
For a first walk through, start with [the user's guide](../../features/6.0/notifications.md) or
[the administrator's guide](../../guides/notifications-administrator.md); settings are in [the INI reference](notifications-ini.md),
commands in [the command reference](notifications-cli.md), and extending in [the developer's guide](../../guides/notifications-developer.md).

## Data

| Table | Class | Meaning |
|---|---|---|
| `eznotificationevent` | `eZNotificationEvent` | An event. `event_type_string` (`ezpublish`, `ezcollaboration`, `ezcurrenttime` or your own), `data_int1..4`, `data_text1..4`, `status` (0 `STATUS_CREATED` pending, 1 `STATUS_HANDLED`). There is no date column: `createdAt()` reads the age from the content (see below) |
| `eznotificationcollection` | `eZNotificationCollection` | The message a handler made for an event: `event_id`, `handler`, `transport` (`ezmail`), `data_subject`, `data_text` |
| `eznotificationcollection_item` | `eZNotificationCollectionItem` | One recipient address of a collection: `collection_id`, `event_id`, `address`, `send_date` (0 = send now, otherwise the digest time as a Unix timestamp) |
| `ezsubtree_notification_rule` | `eZSubtreeNotificationRule` | A subscription: `user_id` (content object id of the user), `node_id`, `use_digest` |
| `ezgeneral_digest_user_settings` | `eZGeneralDigestUserSettings` | `user_id`, `receive_digest` (0/1), `digest_type` (3 daily, 1 weekly, 2 monthly), `day` (weekday name for weekly, day of month for monthly), `time` (`H:00`) |
| `ezcollab_notification_rule` | `eZCollaborationNotificationRule` | `user_id`, `collab_identifier` (for example `ezapprove`) |

## Event types

| Type | Made by | Fields | `createdAt()` reads |
|---|---|---|---|
| `ezpublish` | the publish operation (`eZContentOperationCollection::createNotificationEvent`) | `data_int1` object id, `data_int2` version | `created`/`modified` of the version |
| `ezcollaboration` | `eZCollaborationItem::createNotificationEvent()` | `data_int1` item id, `data_text1` collaboration identifier | `created`/`modified` of the item |
| `ezcurrenttime` | the notification run | `data_int1` the time | `data_int1` |

`createdAt()` returns false when the content is gone; `eZNotificationEvent::removeOlderThan()` leaves such events alone
unless told to remove them.

## The flow

1. Publishing a version makes an `ezpublish` event. A collaboration item (an approval, a comment) makes an
   `ezcollaboration` event. A run makes an `ezcurrenttime` event, unless it is told not to.
2. `eZNotificationEventFilter::process( $eventIDList = null )` fetches the pending events (all, or the given ids) and, for
   each, in one database transaction, calls `handle( $event )` of every handler of `[NotificationEventHandlerSettings]
   AvailableNotificationEventTypes[]`, in that order. A handler that throws an error is logged, counted in `failed` and does
   not stop the others; the event is not retried, because a handler that has already sent would send again.
3. After the handlers: an event without collection items is removed; otherwise it is kept with status handled until its items
   are gone.
4. After all events: `eZNotificationCollection::removeEmpty()` and `eZNotificationEvent::cleanupHandled()` (handled events
   nothing waits for) run. `process()` returns `events`, `removed`, `kept`, `failed`, `send_failed`, `dropped`, `retried` and `notes`.

### The subtree handler (`ezsubtree`)

For an `ezpublish` event it skips events whose version is not the current one, whose node is invisible, or whose parent or
class is missing. Otherwise it renders `notification/handler/ezsubtree/view/plain.tpl` (the template sets `subject`,
`from`, `message_id`, `reply_to`, `references`), makes a collection, and `eZSubtreeNotificationRule::fetchUserList()` finds
the subscribers: rules on the node or any ancestor of any location of the object, users that are enabled and not
invisible, and that hold a role with `content/read` (a policy limited by class, section, owner, node, subtree or state is
checked against the object). A subscriber with a digest (`ezgeneral_digest_user_settings.receive_digest = 1`) gets a
`send_date` from `eZNotificationSchedule::setDateForItem()`; the others are sent at once through `eZMailNotificationTransport`.

The weekly digest stores the weekday by its name in the locale of the user who chose it. The handler accepts that name,
the English name or a number 0 (Sunday) to 6, so a cronjob that runs in another locale still schedules it.

### The general digest handler (`ezgeneraldigest`)

For an `ezcurrenttime` event it finds every address with an item whose `send_date` is not after the event's time, renders
`notification/handler/ezgeneraldigest/view/plain.tpl` once per address (which includes
`notification/handler/<handler>/view/digest_plain.tpl` for every handler that has items), sends it, and removes the items
that were part of it, in chunks of `[RuleSettings] LimitDeleteElements`. The subject carries the time the digest was made.

### The collaboration handler (`ezcollaborationnotification`)

For an `ezcollaboration` event it asks the item's handler (`eZCollaborationItemHandler::handleCollaborationEvent()`)
which participants have a rule for the item's identifier, makes one collection per participant role (or one for all),
and sends at once. The approval mails are `notification/handler/ezcollaboration/view/ezapprove/*.tpl`.

### Mail the transport refuses

`eZMailNotificationTransport::send()` returns the answer of the mail transport. A handler that gets false keeps the items
(and so the handled event) and calls `eZNotificationEventFilter::noteDeliveryFailure()`:

- the general digest handler keeps the items of the failed address (`keepItemsOfFailedAddresses()`) and removes the others;
  an item that has been due for longer than `[RuleSettings] RetryHours` is removed and counted by `noteDropped()`;
- the subtree and collaboration handlers send first and remove their items only on success; a failure leaves the items with
  `send_date = 0` on a handled event.

`eZNotificationEventFilter::retryUnsent()` runs at the start of every `process()`: it finds the items with `send_date = 0`
whose event is handled (they can only be there after a failure), sends each collection again, removes the items on success
(`retried`), and gives up (`dropped`) those of an event older than `RetryHours` or with an address that cannot be mailed. The
digest items are retried by the next time event, because they are still due. `process()` returns `send_failed`, `dropped`, `retried`
and `notes` besides its other numbers. See [the upgrade notes](../../bc/6.0/notification-ui-and-commands.md#mail-the-transport-refuses-is-kept-and-tried-again).

## Time windows of the digest

`eZNotificationSchedule::setDateForItem( $item, array( 'frequency' => 'day|week|month', 'day' => n, 'hour' => h ) )`
sets `send_date` to the next such time after now: daily, the next time it is `h:00`; weekly, the next `day` (0 Sunday)
at `h:00`, within seven days; monthly, the next `day` of the month at `h:00`, with day 31 reduced to the last day of a short
month. The server's time zone is used. A run whose time event is at or after `send_date` sends the item.

## expNotificationService

`kernel/classes/expnotificationservice.php`. All callers (the cronjob part, the commands, the status page, the run-filter
page) go through it.

| Method | What it does |
|---|---|
| `run( $options )` | One pass under the lock. Options: `source` (`cron`, `console`, `web`), `at` (time of the time event), `time_event` (false: none), `events` (only these ids; no time event), `user`. Returns `result` (`ok`, `busy`, `failed`), `events`, `removed`, `kept`, `failed`, `send_failed`, `dropped`, `retried`, `mails`, `recipients` (what the transport took), `ms`, `error`. Records the run; emits the audit event for every source but `cron` |
| `plan( $options )` | The same pass in a transaction that is rolled back, with the mail observed and suppressed. Returns the planned `mails` (`subject`, `to`, masked `addresses`, `raw`). Refused where the tables cannot roll back |
| `status()` | The numbers of the status page; `problems()` and `problemText()` give the problem list |
| `subscriptions( $userID, $filter, $offset, $limit )` | Subtree subscriptions with `path`, `class`, `last_change`, `missing`; filters `q`, `class`, `missing`, `ids` |
| `subscribedClasses( $userID )` | The classes of the followed nodes, for the filter |
| `eventsReport()`, `cleanup()` | Event list; removal of handled orphans and old events |
| `lastRuns()`, `runningNow()` | The run record; who holds the lock |
| `parseAge()`, `maskAddress()` | `30d` and `j***@example.com` helpers |

### Lock

`var/<var dir>/notification/run.lock` is locked with `flock( LOCK_EX | LOCK_NB )`. The holder writes its process id,
start time and source into the file. The operating system releases the lock when the process ends, so a crashed run
leaves nothing behind (the file stays; its content is stale and ignored when the lock is free). A run that cannot get
the lock returns `busy`; if the file cannot be opened (the web server and the command line run as different users and the
file belongs to the other) it returns `failed` with the reason. The file is created world-writable for that reason.
`runcronjobs.php` has its own mutex per cronjob part (`[CronjobSettings] MaxScriptExecutionTime`); the two are independent.

### Run record

`runs.jsonl`: one JSON object per line, newest last, 200 kept (trimmed when it passes 400). Fields: `time`, `source`,
`dry`, `result`, `ms`, `events`, `removed`, `kept`, `failed`, `send_failed`, `dropped`, `retried`, `mails`, `recipients`, `error`, `user`. Dry runs are not
recorded. The status page and `exp:notification:status` read it.

### Audit event

A run started from the console or the web is audited as `system.command.run` with object type `notification`, id `run`,
and `after` carrying `source`, `events`, `mails`, `recipients`, `failed`, `ms`. The cronjob run is audited by `runcronjobs.php`
as `system.cronjob.run` or `system.cronjob.fail` for the script `notification`. See [the audit event model](audit-event-model.md).

### Observing the mail

`eZMailNotificationTransport::observe( $callback, $suppress )` registers a callback that receives `( $addresses, $subject,
$body, $parameters, $sent )` for every message (`$sent`, whether the transport took it, is absent when `$suppress` is true and nothing is handed to the transport). The service uses it
to count mail and to plan. `observe( null )` removes it.

### Background job

`expNotificationJob::start( $siteaccess, $dry, &$error )` starts `bin/php/notificationrun.php --job=<id> --source=web` detached
(`setsid`, the PHP command line found by `expProcessTools`), with its output going to `jobs/<id>.log`. The command writes
`{type: log, message}` lines and a final `{type: end, result}` to `jobs/<id>.jsonl`; `expNotificationJob::progress( $id, $offset )`
reads them. A job that has written nothing for two minutes and not ended is reported as stopped.

## Module `notification`

| View | Policy | Parameters and POST fields |
|---|---|---|
| `settings` | `use` | `(offset)`, `(q)`, `(class)`; POST `NewRule_ezsubtree`, `RemoveRule_ezsubtree`, `SelectedRuleIDArray_ezsubtree[]`, `UseConfirm`, `ConfirmRemoveRule`, `CancelRemoveRule`, `Store`, `SaveCollaboration`, `FilterSubscriptions`, `ClearFilter`, `Query`, `ClassFilter` |
| `addtonotification/<node>` | `use` | GET asks; POST `ConfirmAddNotification`, `ConfirmRemoveNotification`, `CancelNotification`, `RedirectURI` |
| `status` | `administrate` | POST `CleanupHandled`, `CleanupOld` with `OlderThan`, `RemoveMissingRules` |
| `job` | `administrate` | POST `Action=start` (`DryRun=1`), GET `job/<id>/<offset>` answers JSON `{events, offset, done}` |
| `runfilter` | `administrate` | POST `RunFilterButton`, `SpawnTimeEventButton` |

Every POST carries the form token (the `ezformtoken` extension). `UseConfirm` in the settings form asks the view to show
the confirmation before removing; a form without it (an older template) removes at once as before.

## Templates

| Template | Used for |
|---|---|
| `notification/settings.tpl` | The settings page; includes `handler/<id_string>/settings/edit.tpl` for each handler |
| `notification/addconfirm.tpl`, `addingresult.tpl`, `removeresult.tpl` | The add and remove pages |
| `notification/status.tpl`, `runfilter.tpl`, `parts/style.tpl`, `parts/notice.tpl` | admin4: status page, run-filter page, styles (`--nf-*` tokens on the `--a4-*` tokens), the inline notice |
| `notification/handler/<id_string>/view/plain.tpl` and others | The mails |

admin4 and admin4l have the reworked pages; `design/admin` and `design/standard` provide `addconfirm.tpl` and
`removeresult.tpl` and keep the earlier settings pages.

## Related pages

- [User's guide](../../features/6.0/notifications.md), [administrator's guide](../../guides/notifications-administrator.md), [developer's guide](../../guides/notifications-developer.md)
- [INI reference](notifications-ini.md), [command reference](notifications-cli.md)
- [Upgrade notes](../../bc/6.0/notification-ui-and-commands.md)
- [Audit event model](audit-event-model.md), [commands, cronjob parts and module views as classes](runnable-commands-cronjobs-views.md)
