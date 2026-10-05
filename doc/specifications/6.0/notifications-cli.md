# Specification: the notification commands

This page is the reference for the four console commands of the notification system: what each option does, the
exit codes, and sample output. Read it if you run notifications from a shell, a crontab or a deployment script. For the
page in the administration that does the same, see [the administrator's guide](../../guides/notifications-administrator.md).

Every example on this page was run on a test installation with the mail transport forced to files (see
`--mail-file-dir` below), a test user on the address `nottest-ui@nottest.invalid` and test content; no mail was sent.
Counts, ids and times differ on your installation. Commands run from the installation root. `--allow-root-user` is
needed when you run as root; `--no-colors` is left out of the samples where it does not matter.

## Names and aliases

Each command is a script in `bin/php/` and a class in `kernel/private/classes/commands/`. The console lists the real
name and the aliases (`./console list exp` shows them):

| Real name (`./console ...`) | Aliases | Script |
|---|---|---|
| `exp:notificationstatus` | `exp:notification:status`, `exp:notify:status` | `bin/php/notificationstatus.php` |
| `exp:notificationrun` | `exp:notification:run`, `exp:notify:run` | `bin/php/notificationrun.php` |
| `exp:notificationevents` | `exp:notification:events`, `exp:notify:events` | `bin/php/notificationevents.php` |
| `exp:notificationsubscriptions` | `exp:notification:subscriptions`, `exp:notify:subscriptions` | `bin/php/notificationsubscriptions.php` |

```bash
./console list exp | grep notif
./console help exp:notification:run
./console exp:notify:events list --limit=2 --allow-root-user
```

`--help` on any of them shows the description and the options. The standard options of every script apply too:
`-s` / `--siteaccess=<name>`, `-q` / `--quiet`, `-d` / `--debug`, `--no-colors`, `-r` / `--allow-root-user`.

## Exit codes

| Code | Meaning |
|---|---|
| 0 | The command did what it was asked; the last line is `PASS` (or `PASS: ...`) |
| 1 | The work failed: the mail transport refused a message (it is kept for the next run), a run could not start (another run holds the lock, the lock file cannot be written, a dry run is refused because the database cannot roll back), an unknown user, or `exp:notification:status` found a problem of level error |
| 2 | Usage error: an unknown action, a bad `--older-than`, `--status` or `--at`, `--event` without ids |

## exp:notification:status

Shows pending events, the messages kept for a digest, the subscriptions, the mail sent in the last 24 hours, the last ten
runs and what looks wrong. It changes nothing.

| Option | Effect |
|---|---|
| `--json` | One JSON object with the same numbers, `runs`, `last_run`, `running` and `problems` (each `level` and `text`) |

Ends with `PASS`, or `FAIL: n problem(s) of level error` and exit code 1, so a monitoring check can use it. Warnings and
notes do not fail it.

```text
$ ./console exp:notification:status --allow-root-user
Events
  pending                  1   (ezpublish 1)
  oldest pending           2026-10-04 22:39:18
  handled, kept            0   (0 with nothing left to send)
Messages
  collections              0
  items to send now        0
  items kept for a digest  0   (0 due)
Subscriptions
  subtree                  1 by 1 users
  collaboration rules      0
  digest                   daily 1, weekly 0, monthly 0
Mail
  transport                sendmail   sender info@se7enx.com
  last 24 hours            1 messages to 1 recipients in 3 runs
Handlers                    ezgeneraldigest, ezcollaborationnotification, ezsubtree
Runs
  2026-10-04 22:39:19  console         ok     events 0, removed 0, kept 0, messages 0, 23 ms
  2026-10-04 22:39:17  console         ok     events 33, removed 33, kept 0, messages 0, 48 ms
  2026-10-04 22:39:16  console         ok     events 1, removed 1, kept 0, messages 1, 100 ms
Problems
  none
PASS
```

`./console exp:notification:status --json` begins like this (shortened):

```text
{
    "time": 1791178751,
    "pending": { "ezpublish": 33 },
    "pending_total": 33,
    "handled_kept": 0,
    "handled_orphans": 0,
    "collections": 0,
    "items_total": 0, "items_now": 0, "items_digest": 0, "items_due": 0,
    "subscriptions": 0, "subscribers": 0, "collab_rules": 0,
    "digest": { "daily": 0, "weekly": 0, "monthly": 0 },
    "runs": [ { "time": 1791178297, "source": "web", "result": "ok", "events": 1, "mails": 0, "recipients": 0, "ms": 42, ... } ],
    "running": false,
    "transport": "sendmail",
    "sender": "info@se7enx.com",
    "handlers": [ "ezgeneraldigest", "ezcollaborationnotification", "ezsubtree" ],
    "problems": []
}
```

The meaning of each line is in [the administrator's guide](../../guides/notifications-administrator.md#every-number-on-the-status-page).

## exp:notification:run

One pass of the notification filter, as the cronjob part does it: a time event is made, every pending event goes through
every handler, messages are sent or kept for a digest, events nothing waits for are removed. The run is locked,
recorded and audited (see [the specification](notifications.md)).

| Option | Effect |
|---|---|
| `--dry-run` | Lists what would be sent and changes nothing: the pass runs inside a transaction that is rolled back and no mail is handed to the transport. Refused (exit 1) where the database cannot roll back (MySQL tables that are not InnoDB, or `[DatabaseSettings] Transactions` not enabled) |
| `--at=<time>` | The time of the time event: a date (`2026-10-05 08:00`) or a Unix timestamp. Default: now. Use it to try digest windows |
| `--event=<ids>` | Only these events, comma separated. No time event is made then. The installation's other pending events are not touched |
| `--no-time-event` | Make no time event: only what is pending is handled, and no digest becomes due because of this run |
| `--mail-file-dir=<dir>` | Forces the file transport for this process only, writing the mail to `<dir>`; nothing is written to a settings file. Use it for every trial |
| `--addresses` | Dry run: show whole recipient addresses (masked by default: `n***@nottest.invalid`) |
| `--json` | One JSON object instead of the text |
| `--job=<id>` | The id of a background job; the progress is written to its file. The status page uses this, you normally do not |
| `--source=<console\|web>` | What the record of the run says about who started it (default `console`) |

A dry run (the subject carries the title of the test article; the address is masked):

```text
$ ./console exp:notification:run --dry-run --no-time-event --mail-file-dir=var/tmp/notification-mail/doc --allow-root-user
Mail goes to files in var/tmp/notification-mail/doc (file transport, this process only).
Dry run: nothing is sent, marked or removed.

  would send "Article "NOTTEST first article" was published [alpha.se7enx.com - NOTTEST UI folder]" to 1 recipient(s): n***@nottest.invalid
PASS: dry run, 34 event(s) handled, 1 message(s) would be sent, 0 event(s) would be kept for a digest.
```

With `--addresses` the last part of the message line reads `to 1 recipient(s): nottest-ui@nottest.invalid`. With `--json`:

```text
{
    "result": "ok",
    "error": "",
    "events": 34,
    "removed": 34,
    "kept": 0,
    "failed": 0,
    "mails": [
        { "subject": "Article \"NOTTEST first article\" was published [...]", "to": 1, "addresses": [ "n***@nottest.invalid" ] }
    ]
}
```

A real run of one event; the mail goes to a file:

```text
$ ./console exp:notification:run --event=21561 --mail-file-dir=var/tmp/notification-mail/doc --allow-root-user
Mail goes to files in var/tmp/notification-mail/doc (file transport, this process only).
Starting notification event processing

PASS: 1 event(s) handled, 1 removed, 0 kept for a digest, 1 message(s) to 1 recipient(s), 100 ms.
```

The numbers: events handled; removed (nothing waited for them any more); kept for a digest (a message waits for its
digest time); messages and recipients sent now. `, n handler failure(s)` is added when a handler threw an error.

A digest in two steps (the subscriber chose a daily digest at 8:00). The first run keeps the message; a run whose time
event is after 8:00 sends it:

```text
$ ./console exp:notification:run --event=21599 --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
PASS: 1 event(s) handled, 0 removed, 1 kept for a digest, 0 message(s) to 0 recipient(s), 79 ms.

$ ./console exp:notification:run --at="2026-10-05 06:00" --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
Time event at 2026-10-05 06:00:00.
PASS: 13 event(s) handled, 13 removed, 0 kept for a digest, 0 message(s) to 0 recipient(s), 61 ms.

$ ./console exp:notification:run --dry-run --at="2026-10-05 09:00" --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
  would send "[alpha.se7enx.com] Digest for Sunday October 04 2026 10:40:33 pm" to 1 recipient(s): n***@nottest.invalid
PASS: dry run, 1 event(s) handled, 1 message(s) would be sent, 0 event(s) would be kept for a digest.

$ ./console exp:notification:run --at="2026-10-05 09:00" --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
PASS: 1 event(s) handled, 1 removed, 0 kept for a digest, 1 message(s) to 1 recipient(s), 125 ms.
```

The date in the digest subject is when the digest was made, not the time of the time event.

A second run while another holds the lock is refused with exit code 1. A dry run needs the lock too:

```text
$ ./console exp:notification:run --no-time-event --event=1 --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
FAIL: Another run holds the lock (process 0).

$ ./console exp:notification:run --dry-run --no-time-event --event=1 --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
FAIL: A run is in progress.
```

(The process number is 0 here because the lock was held by the `flock` tool, which does not write one; a run of
Exponential writes its process id.) Other failures: `FAIL: --at needs a date or a timestamp`, `FAIL: --event needs event
ids` (both exit code 2), `FAIL: The lock file ... cannot be opened (permissions ...)`.

Transport failures. When the mail transport refuses a message the run still does its work, keeps the message and ends with exit
code 1. Here the file transport is pointed at a directory it cannot write (the only way the transport "fails" without a mail
server; no mail can leave), and then at a good one:

```text
$ ./console exp:notification:run --event=21935 --mail-file-dir=/proc/nottest-no-such-directory
PASS: 1 event(s) handled, 0 removed, 1 kept for a digest, 0 message(s) to 0 recipient(s), 150 ms.
FAIL: the mail transport refused 1 message(s); they are kept and tried again at the next run (for 72 hours).

$ ./console exp:notification:status
Problems
  [error] 1 messages could not be handed to the mail transport and wait for the next run; each is given up after 72 hours.
  [error] The mail transport refused 1 messages in the last run. Check the mail server and site.ini MailSettings.
FAIL: 2 problem(s) of level error

$ ./console exp:notification:run --no-time-event --event=1 --mail-file-dir=var/tmp/notification-mail/doc3
PASS: 0 event(s) handled, 0 removed, 0 kept for a digest, 1 message(s) to 1 recipient(s), 37 ms.
1 message(s) that failed earlier were sent now.
```

A message that is given up after `RetryHours` is reported as `WARNING: n message(s) were given up (older than 72 hours, or an address that cannot be mailed).`

Never run without `--mail-file-dir` on a trial: without it the mail goes through the transport of `site.ini`.

## exp:notification:events

| Action | Effect |
|---|---|
| `list` (default) | The newest events: id, status, type, age, messages waiting |
| `cleanup` | Removes the handled events nothing waits for; with `--older-than` also the events (and their messages) older than that |

| Option | Effect |
|---|---|
| `--status=<pending\|handled>` | `list`, `cleanup --older-than`: only events in that status (default both) |
| `--limit=<n>` | `list`: how many (default 50) |
| `--older-than=<age>` | `cleanup`: `90s`, `15m`, `12h`, `30d`, `2w`, a plain number of days, or a date (`2026-09-01`) |
| `--include-unknown` | `cleanup`: also remove events whose age cannot be told |
| `--dry-run` | `cleanup`: count, remove nothing |
| `--json` | `list`: JSON (`id`, `status`, `type`, `created`, `items`) |

An event has no date column. Its age is read from what it is about: the time of a time event, the creation of the
published version of an `ezpublish` event, the creation of the collaboration item. If that content is gone the age is
unknown (`age unknown`), and such events are removed only with `--include-unknown`.

```text
$ ./console exp:notification:events list --status=handled
  21599  handled  ezpublish        2026-10-04 22:40:31  1 message(s) waiting
PASS: 1 event(s) shown

$ ./console exp:notification:events cleanup --dry-run
Would remove: 1 handled event(s) with nothing left to send
PASS

$ ./console exp:notification:events cleanup --older-than=30d --dry-run
Would remove: 1 handled event(s) with nothing left to send
Would remove: 1 event(s) older than 30d (1 newer kept, 0 of unknown age left alone)
PASS

$ ./console exp:notification:events cleanup --older-than=30d
Removed: 1 handled event(s) with nothing left to send
Removed: 1 event(s) older than 30d (0 newer kept, 0 of unknown age left alone)
PASS

$ ./console exp:notification:events cleanup --older-than=soon
FAIL: --older-than is a number of days, or 90s, 15m, 12h, 30d, 2w, or a date          (exit code 2)

$ ./console exp:notification:events frobnicate
FAIL: the actions are list and cleanup                                                    (exit code 2)
```

## exp:notification:subscriptions

| Action | Effect |
|---|---|
| `list` (default) | The subtree subscriptions: rule id, user, node, name, class, last change below the node |
| `remove-missing` | Removes the subscriptions whose node no longer exists |

| Option | Effect |
|---|---|
| `--user=<login or id>` | Only this user (a login, or the user's content object id). An unknown login: `FAIL: no user with the login ...`, exit code 1 |
| `--q=<text>` | Only nodes whose name contains the text (`%` and `_` are plain characters) |
| `--class=<identifier>` | Only nodes of this class |
| `--missing` | Only subscriptions whose node is gone |
| `--limit=<n>`, `--offset=<n>` | Paging (default limit 50) |
| `--addresses` | Also show the user's e-mail address (hidden by default) |
| `--dry-run` | `remove-missing`: count, remove nothing |
| `--json` | `list`: JSON (`total`, `rows` with `path`, `class_identifier`, `last_change`, `missing`, `use_digest`) |

```text
$ ./console exp:notification:subscriptions list --user=nottest-ui
#345   nottest-ui       node 21931  NOTTEST UI folder                folder         changed 2026-10-04
PASS: 1 of 1 subscription(s) shown

$ ./console exp:notification:subscriptions list --user=nottest-ui --addresses
#345   nottest-ui       node 21931  NOTTEST UI folder                folder         changed 2026-10-04  nottest-ui@nottest.invalid
PASS: 1 of 1 subscription(s) shown

$ ./console exp:notification:subscriptions list --missing
#346   nottest-ui       node 99999999 (content is gone)
PASS: 1 of 1 subscription(s) shown

$ ./console exp:notification:subscriptions remove-missing --dry-run
would remove #346 (user 21300, node 99999999)
PASS: 1 subscription(s) would be removed

$ ./console exp:notification:subscriptions remove-missing
removing #346 (user 21300, node 99999999)
PASS: 1 subscription(s) removed

$ ./console exp:notification:subscriptions list --user=nobody-here
FAIL: no user with the login nobody-here                                                  (exit code 1)
```

The JSON of `list --json` (one row):

```text
{ "total": 1, "rows": [ { "id": 345, "user_id": 21300, "login": "nottest-ui", "node_id": 21931, "name": "NOTTEST UI folder",
    "path": [ "Websites" ], "class_identifier": "folder", "class_name": "Folder", "section_id": 1,
    "last_change": 1791178752, "missing": false, "use_digest": 0 } ] }
```

## The cronjob part

```bash
php runcronjobs.php -s site notification --allow-root-user
```

```text
Using siteaccess site for cronjob
Running cronjob part 'notification'
Running cronjobs/notification.php at: 10/04/2026 10:40 pm
Starting notification event processing

Done: 1 events, 0 messages to 0 recipients
Completing cronjobs/notification.php at: 10/04/2026 10:40 pm
Elapsed time: 00:00:00
```

`php runcronjobs.php -s site --list` shows the part twice: in `CronjobPart-frequent` and in `CronjobPart-notification`.
A cron run is recorded with source `cron`. If another run holds the lock the part prints `Skipped: Another run holds the
lock ...` and does not fail. That sample ran on an installation with nothing pending and nobody following anything,
so no mail could be made; on an installation with subscribers the cron run uses the transport of `site.ini`.

## Related pages

- [The administrator's guide](../../guides/notifications-administrator.md), [the user's guide](../../features/6.0/notifications.md), [the developer's guide](../../guides/notifications-developer.md)
- [The notification specification](notifications.md), [the INI reference](notifications-ini.md)
- [Commands, cronjob parts and module views as classes](runnable-commands-cronjobs-views.md)
- [Upgrade notes](../../bc/6.0/notification-ui-and-commands.md)
