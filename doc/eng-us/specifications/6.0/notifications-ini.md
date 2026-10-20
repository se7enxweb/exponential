# Specification: notification settings

Every setting that changes how notifications are made, handled, sent and run, with its default and its effect. Read it
when you configure notifications for an installation or write an extension that adds handlers or event types. The
defaults are the ones in the files of the distribution: `settings/notification.ini`, `settings/site.ini`,
`settings/cronjob.ini` and `settings/menu.ini`. Change them in `settings/override/<file>.append.php` or in the settings
of a siteaccess or an extension, never in the distribution file (see [INI override placements](ini-override-placements.md)).
After a change clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

## notification.ini

### [RuleSettings]

| Key | Default | Effect |
|---|---|---|
| `LimitDeleteElements` | `50` | How many digest items the general digest handler removes in one query after it has sent a digest. It keeps the query below the limits of the database (statement length, number of values in `IN ()`). A value of 0 or empty is treated as 50 |
| `RetryHours` | `72` | How many hours a notification that the mail transport refused is tried again, at every run. Counted from when the digest was due, or from the time of the content for a message sent at once. After that it is given up, removed and counted as dropped (status page: **given up**). A value of 0 or less means 72 |
| `RepositoryDirectories[]`, `ExtensionDirectories[]`, `Alias[]` | `kernel/notification/rules`, none, `advanced=ezadvanced`, `general=ezgeneral`, `keyword=ezkeyword` | Left over from rule types of earlier versions. Nothing in the kernel reads them any more |

### [NotificationEventTypeSettings]

| Key | Default | Effect |
|---|---|---|
| `RepositoryDirectories[]` | `kernel/classes/notification/event/` | Directories searched for an event type. The class file of the type `<name>` is `<directory>/<name>/<name>type.php` and it registers itself with `eZNotificationEventType::register( '<name>', '<ClassName>' )` |
| `ExtensionDirectories[]` | none | Names of extensions. Each is searched in `extension/<name>/notificationtypes/<type>/<type>type.php` |
| `AvailableNotificationEventTypes[]` | `ezpublish`, `eznewcontent`, `ezcurrenttime`, `ezcollaboration` | The event types that may be loaded all at once (`eZNotificationEventType::loadAndRegisterAllTypes()`). A type is also loaded the first time an event of that type is created or read, whether it is listed or not. `eznewcontent` has no class in the distribution: loading it logs "Notification event type not found"; no code in the kernel creates such events |

### [NotificationEventHandlerSettings]

| Key | Default | Effect |
|---|---|---|
| `RepositoryDirectories[]` | `kernel/classes/notification/handler/` | Directories searched for a handler. The class file of the handler `<name>` is `<directory>/<name>/<name>handler.php` and the class is `<name>handler` |
| `ExtensionDirectories[]` | none | Names of extensions. Each is searched in `extension/<name>/notification/handler/<name>/<name>handler.php` |
| `AvailableNotificationEventTypes[]` | `ezgeneraldigest`, `ezcollaborationnotification`, `ezsubtree` | The handlers that run, in this order. Every pending event is given to every listed handler. The key is called `AvailableNotificationEventTypes` although it lists handlers. A listed handler whose file is not found is logged as an error and skipped. Remove a name to switch that handler off (for example `ezcollaborationnotification` on a site without collaboration) |

The id a handler gives itself (`id_string`) names its settings templates: `notification/handler/<id_string>/settings/edit.tpl`
and its mail templates in `notification/handler/<id_string>/view/`. The collaboration handler is listed as
`ezcollaborationnotification` but its id is `ezcollaboration`, so its templates are in `notification/handler/ezcollaboration/`.

### [TransportSettings]

| Key | Default | Effect |
|---|---|---|
| `DefaultTransport` | `mail` | The notification transport used when a caller names none. The built-in handlers always name `ezmail` (the class `eZMailNotificationTransport`, which sends through the mail transport of `site.ini`), so this value is not used by them |
| `TransportPluginPath[]` | none | More directories searched for a notification transport. A transport `<name>` is the file `<path>/<name>notificationtransport.php` with the class `<name>notificationtransport`. `kernel/classes/notification/` is always searched first |

### [MailSettings]

| Key | Default | Effect |
|---|---|---|
| `EmailSender` | empty | The sender address of notification mail. If empty, `site.ini` `[MailSettings] EmailSender` is used, then its `AdminEmail`. The status page reports an error when no valid sender is left |

## site.ini

### [MailSettings]

| Key | Default | Effect on notifications |
|---|---|---|
| `Transport` | `sendmail` | How mail leaves the server: `sendmail`, `smtp` or `file`. With `file` the mail is written to files and nothing is sent. The status page notes it |
| `TransportAlias[]` | `file=eZFileTransport`, `sendmail=eZSendmailTransport`, `smtp=eZSMTPTransport` | Maps a `Transport` value to its class. Add one to bring your own |
| `FileTransportDirectory` | empty (means `var/log/mail`) | The directory the `file` transport writes to. Not in the distribution file; added in this version, see [the upgrade notes](../../bc/6.0/notification-ui-and-commands.md). Relative to the installation root |
| `EmailSender`, `AdminEmail` | empty, `nospam@ez.no` | Fallback sender, see above. Change `AdminEmail` on every installation |
| `EmailReplyTo` | empty | Reply-to of mail made through `eZMail` defaults |
| `UserAgent` | empty | The `User-Agent` header of every mail made through `eZMail`, notification mail included (ASCII only). Empty keeps `Exponential, Version <version>` (sendmail, file) and `Apache Zeta Components` (SMTP) |
| `DebugSending`, `DebugReceiverEmail` | `disabled`, empty | When enabled, the sendmail and SMTP transports send every mail to `DebugReceiverEmail` instead of the real recipients. A safety net on a staging copy of a real site. Does not apply to the `file` transport |
| `TransportServer`, `TransportPort`, `TransportConnectionType`, `TransportUser`, `TransportPassword`, `SenderHost` | empty, `25`, empty, empty, empty, `localhost` | SMTP connection |
| `SendmailOptions[]`, `SendmailInsertUndisclosedRecipient` | none, `enabled` | Sendmail options; notification mail has its recipients in `Bcc` and `To: undisclosed-recipients:;` |
| `ExcludeHeaders[]` | none | Headers left out of the message (SMTP only) |

### Other settings that matter

| File, block, key | Default | Effect |
|---|---|---|
| `site.ini` `[SiteSettings] SiteURL` | per installation | The host name written into the links and the footer of the mails, and into the digest subject |
| `site.ini` `[DatabaseSettings] Transactions` | `enabled` | Needed for `exp:notification:run --dry-run`, which rolls a transaction back |
| `site.ini` `[RunnableSettings] Listeners[]`, `Implementation[]` | none | Hook into or replace the commands, the cronjob part and the views, see [the runnable classes](runnable-commands-cronjobs-views.md) |
| `site.ini` `[ExtensionSettings] ActiveExtensions[]` | per installation | An extension that adds handlers or event types must be active |
| The mail templates | | Ordinary templates: override them as any other, see [template override ordering](../../features/6.0/template-override-ordering.md) |

## cronjob.ini

| Block, key | Default | Effect |
|---|---|---|
| `[CronjobPart-frequent] Scripts[]` | contains `notification.php` | The group that `php runcronjobs.php frequent` runs, with the workflow, content jobs and audit parts. This is where the notification part normally runs |
| `[CronjobPart-notification] Scripts[]` | `notification.php` | The notification part on its own: `php runcronjobs.php notification` |
| `[CronjobSettings] MaxScriptExecutionTime` | `43200` | Seconds after which a locked cronjob part is taken over by the next run (twice that and the old one is ended). A per-part value `MaxScriptExecutionTime` in `[CronjobPart-<name>]` overrides it |

The notification service has its own run lock (`var/<var dir>/notification/run.lock`) that is not a setting: it is
released by the system when the process ends, so a crashed run leaves no lock behind.

## menu.ini

| Block, key | Default | Effect |
|---|---|---|
| `[Leftmenu_setup] Links[notification]` | `notification/status` | The Setup menu entry "Notification" that opens the status page |
| `[Leftmenu_setup] PolicyList_notification[]` | `notification/administrate` | Who sees it |
| `[Leftmenu_my] Links[my_notifications]`, `PolicyList_my_notifications[]` | `notification/settings`, `notification/use` | The "My notification settings" entry |

## Policies

| Module / function | Gives access to |
|---|---|
| `notification/use` | `settings`, `addtonotification` (a user's own settings) |
| `notification/administrate` | `status`, `job`, `runfilter` |

## Files written

| Path (under the siteaccess var directory, for example `var/site/`) | Content |
|---|---|
| `notification/runs.jsonl` | One JSON line per run, the newest 200 are kept |
| `notification/run.lock` | The run lock; holds the process id and start time of the run in progress |
| `notification/jobs/<id>.jsonl`, `<id>.log` | Progress and output of a run started from the status page; the newest 20 are kept |

## Related pages

- [The notification specification](notifications.md), [the commands](notifications-cli.md)
- [The administrator's guide](../../guides/notifications-administrator.md), [the developer's guide](../../guides/notifications-developer.md)
- [INI override placements](ini-override-placements.md)
