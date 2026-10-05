# Notifications: specification

## Data

| Table | Meaning |
| --- | --- |
| `eznotificationevent` | an event: `event_type_string` (ezpublish, ezcollaboration, ezcurrenttime), `data_int1..4`, `data_text1..4`, `status` (0 pending, 1 handled). No date column: the age is read from the content (`eZNotificationEvent::createdAt()`). |
| `eznotificationcollection` | the message a handler made for an event: handler id, transport, subject, text |
| `eznotificationcollection_item` | one recipient address of a collection; `send_date` 0 = send now, else the digest time |
| `ezsubtree_notification_rule` | user, node, `use_digest` |
| `ezgeneral_digest_user_settings` | user, `receive_digest`, `digest_type` (3 daily, 1 weekly, 2 monthly), `day` (weekday name or day of month), `time` (`H:00`) |
| `ezcollab_notification_rule` | user and collaboration identifier |

## Flow

1. Publishing makes an `ezpublish` event; a collaboration item makes `ezcollaboration`; the cronjob makes `ezcurrenttime`.
2. `eZNotificationEventFilter::process( $eventIDs = null )` hands each pending event to every handler of notification.ini
   `[NotificationEventHandlerSettings]` (`ezgeneraldigest`, `ezcollaborationnotification`, `ezsubtree`). A handler that throws is
   logged and counted; the others still run and the event is not retried.
3. The subtree handler makes a collection with the rendered mail and an item per subscriber (users with `content/read`, enabled,
   not hidden). A subscriber with a digest gets a `send_date` from `eZNotificationSchedule`; the rest are mailed at once
   through `eZMailNotificationTransport`.
4. A time event makes `ezgeneraldigest` send, per address, one mail with every item whose `send_date` is not after the event's time,
   and remove those items.
5. An event with no items left is removed; otherwise it stays handled until the items are gone. `eZNotificationEvent::cleanupHandled()`
   (run at the end of every pass) removes handled events nothing waits for. `removeOlderThan( $timestamp )` removes by age.

`process()` returns `events`, `removed`, `kept`, `failed`.

## expNotificationService

`run( $options )` (lock `var/<var dir>/notification/run.lock`, record `runs.jsonl`, newest 200, audit event `system.command.run` for
non-cron runs; the cron run is audited by runcronjobs as `system.cronjob.run`), `plan()` (dry run in a rolled-back transaction, mail
observed and suppressed; refused where tables are not transactional), `status()`, `problems()`, `subscriptions()`, `eventsReport()`,
`cleanup()`, `parseAge()`. `eZMailNotificationTransport::observe( $callback, $suppress )` is the hook for counts and dry runs.
`expNotificationJob` starts `bin/php/notificationrun.php --job=<id>` detached; progress is `jobs/<id>.jsonl`, output `jobs/<id>.log`.

## Settings

- notification.ini `[NotificationEventHandlerSettings] AvailableNotificationEventTypes[]`, `ExtensionDirectories[]`;
  `[MailSettings] EmailSender`; `[RuleSettings] LimitDeleteElements`.
- site.ini `[MailSettings] Transport` (sendmail, smtp, file) and the new `FileTransportDirectory` (default `var/log/mail`).
- cronjob.ini `[CronjobPart-frequent]` and `[CronjobPart-notification]`.
- menu.ini `[Leftmenu_setup] Links[notification]=notification/status`.

## Module notification

| View | Policy | Notes |
| --- | --- | --- |
| `settings` | use | parameters `(offset)`, `(q)`, `(class)`; POST fields `NewRule_ezsubtree`, `RemoveRule_ezsubtree`, `SelectedRuleIDArray_ezsubtree[]`, `UseConfirm`, `ConfirmRemoveRule`, `CancelRemoveRule`, `Store`, `FilterSubscriptions`, `ClearFilter` |
| `addtonotification/<node>` | use | GET asks (`addconfirm.tpl`); POST `ConfirmAddNotification`, `ConfirmRemoveNotification`, `CancelNotification` |
| `runfilter` | administrate | `RunFilterButton`, `SpawnTimeEventButton` |
| `status` | administrate | POST `CleanupHandled`, `CleanupOld` (+`OlderThan`), `RemoveMissingRules` |
| `job` | administrate | POST `Action=start` (`DryRun`), GET `job/<id>/<offset>` answers JSON |

Templates: `notification/settings.tpl`, `handler/<id>/settings/edit.tpl`, `addconfirm.tpl`, `addingresult.tpl`, `removeresult.tpl`,
`runfilter.tpl`, `status.tpl`, `parts/style.tpl` (tokens `--nf-*` on `--a4-*`), `parts/notice.tpl`.
