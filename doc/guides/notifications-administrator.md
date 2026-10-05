# Notifications: running them, reading the status page and fixing problems

This guide is for the administrator who keeps the notification system working: the person who sets up the cronjob, looks
at the status page, tries a run safely and finds out why a mail did not come. By the end of it you can start the
notification cronjob, read every number on the status page, try a run without sending anything, clean up old events and
solve the usual problems. About 30 minutes, a shell on the server and an administrator login. Commands run from the
installation root; add `--allow-root-user` when you run as root. Every command and output in this guide was run on
a test installation with the mail transport forced to files and test addresses on `nottest.invalid`: no mail was sent.
Names, ids and times on your installation differ.

What users see is in [the user's guide](../features/6.0/notifications.md); the commands in full are in
[the command reference](../specifications/6.0/notifications-cli.md).

## 1. How it works in one minute

| Step | Who |
|---|---|
| Publishing content, or a step in a collaboration item, writes an **event** to the database. | the kernel, at once |
| Every pending event is given to the **handlers**: they look up who follows the content, write a **message** for each recipient and either send it now or keep it for a **digest**. | the notification **run** |
| A **time event** made by each run makes the digests that are due ready: one mail per address. | the same run |
| An event that nothing waits for any more is removed. | the same run |

Nothing is sent until a run happens. A run is started by the cronjob (the normal way), by the **Run now** button on the
status page, or by `exp:notification:run`. If no run happens, events pile up in the database and nobody gets mail.

## 2. Set up the cronjob

The notification part is in the cronjob group `frequent` (with the workflow and content-job parts) and can also run alone.
See what is installed:

```bash
php runcronjobs.php -s site --list --allow-root-user | grep -n "CronjobPart-notification\|CronjobPart-frequent\|notification.php"
```

Expected (the script is listed in both groups; the repeats are the three directories searched):

```text
35:CronjobPart-frequent:
36:		 cronjobs/notification.php
...
62:CronjobPart-notification:
63:		 cronjobs/notification.php
```

Run it once by hand:

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

Replace `site` with your public siteaccess. The sample ran on an installation where nothing was pending and no one followed
anything, so no mail could be made; on a live installation this run sends mail through the transport of `site.ini`.

Then add it to the crontab. How often? A run is cheap when nothing is pending (about 20 ms in the samples), so every
five minutes is a good default; a digest is sent at the first run after its hour, so the frequency also sets how late a digest can be.

```cron
*/5 * * * * cd /path/to/installation && php runcronjobs.php -s site frequent --allow-root-user >> var/log/cron-frequent.log 2>&1
```

If your crontab already runs the `frequent` group, the notification part is already included; do not add a second line.
`./console crontab:edit` opens the crontab. The cronjob guide is [Operating a site](operating-a-site.md#3-run-cronjobs).
If you run Velocity, its own [scheduler](../features/6.0/velocity-scheduler.md) can start the part without the system cron.

Two things protect against double runs: `runcronjobs.php` locks each part, and the notification service has its own lock
(section 6), so a cron run, a console run and **Run now** never overlap. A second run is refused, not queued.

## 3. The status page

Open **Setup > Notification** (`/notification/status`; policy `notification/administrate`). From the dashboard menu,
**My notification settings** has a **Notification status** button for administrators. The console version is
`./console exp:notification:status`.

### Every number on the status page

| Where | What it counts |
|---|---|
| **Events waiting to be handled** | Events with status pending. They are waiting for the next run. After a run it should be 0 (or the few made since) |
| **Messages kept for a digest** (and "n due") | Recipient items with a send date in the future. "Due" are those whose date has passed: the next run sends them |
| **Subscriptions by n users** | Rows of subtree subscriptions and how many users have them |
| **Messages to n recipients, last 24 hours** | The sum of the messages and recipients of the runs of the last day that are in the run record. A mail with 5 recipients counts 1 message and 5 recipients |
| **Last run** (and who started it) | The time of the last recorded run and its source: `cron`, `console` or `web` |
| **Recent runs** | The last ten: time, source, events handled, messages sent / recipients, result (OK, or n failed handlers, or the failure) and the duration |
| **Events** | The ten newest events: id, type, status (Waiting, or Handled with the number of messages that wait for a digest), and when the content they are about was made. A dash means the age is unknown (the content is gone) |
| **Subscriptions** | The first subscriptions with user and item; a badge marks those whose content no longer exists |
| **Mail and handlers** | The mail transport and sender that notifications use, the active handlers, how many users chose a daily, weekly and monthly digest, how many collaboration rules exist, how many messages wait for sending now |

"Messages waiting now" should be 0 except during a run: items with no send date are sent in the run that makes them.

### The problem list

Above the numbers, notices of three kinds: **Problem** (red), **Attention** (orange), **Note** (blue); or "Nothing looks
wrong". The exact texts, produced by the same code as the page (a made-up status for each case):

```text
[events wait, no run recorded]
  error: 12 events wait and no run of the notification cronjob is recorded. Add the cronjob part "frequent" to the crontab, or run exp:notification:run.
[events wait, last run two hours ago]
  error: 12 events wait and the last run was more than an hour ago (2026-10-04 20:40). Is the notification cronjob running?
[no run recorded, nothing waits]
  warning: No run is recorded yet. The notification cronjob (part "frequent") sends the notifications.
[last run failed]
  error: The last run failed: RuntimeException: example
[a handler failed]
  warning: A handler failed on 3 events in the last run; see the debug log.
[digest overdue]
  warning: 4 digest messages are overdue; a run sends them.
[handled orphans]
  info: 7 handled events have nothing left to send; Remove old events clears them.
[file transport, no sender, no subtree handler]
  info: Mail is written to files, not sent (site.ini MailSettings Transport=file).
  error: There is no valid sender address: set EmailSender in notification.ini or site.ini.
  warning: The subtree handler is not available, so no one is notified about published content.
```

Two more warnings come from the data: "n subscriptions point to content that no longer exists." and "n subscriptions belong
to users that no longer exist." The meaning and the cure of each are in section 7.

### Run now and the preview

**Preview (dry run)** starts `exp:notification:run --dry-run` in the background and shows its output in a console box on
the page. It lists each message that would be sent (the subject and the number of recipients), and changes nothing.
**Run now** asks "Run the notifications now? Messages that are due are sent." and then starts a real run in the background.
Both show the output as it comes and end with "Finished. Reload the page to see the new numbers." or "The run failed or
stopped. See the lines above." While a run is in progress both buttons are off, and a notice names the process. A
run started here is recorded with source `web`.

The run is a separate process started with `setsid`, so it goes on if you close the page. It needs `proc_open`, `setsid`
(util-linux) and the PHP command line; if one is missing the page says so ("Run now needs a background process: ...")
and offers the console command. Its output is also kept in `jobs/<id>.log` next to the progress file (section 7).
Without JavaScript the **Run notification filter** button of the old page (`/notification/runfilter`) does a run in the request.

### Cleanup

| Button | Effect |
|---|---|
| **Remove handled events with nothing left to send** | Removes events that were handled and have no message waiting. They were left behind in earlier versions; a run now removes them by itself |
| **Remove events older than** 30 days / 90 / 180 / 1 year | Removes the events, and their waiting messages, whose content is older than that, after a confirmation. Events of unknown age are kept. Minimum one day |
| **Remove n subscriptions whose content is gone** | Removes subscriptions to deleted content (shown only when there are some) |

Console: `exp:notification:events cleanup --dry-run`, `... cleanup --older-than=30d`, `exp:notification:subscriptions
remove-missing`. Try with `--dry-run` first:

```text
$ ./console exp:notification:events cleanup --older-than=30d --dry-run
Would remove: 1 handled event(s) with nothing left to send
Would remove: 1 event(s) older than 30d (1 newer kept, 0 of unknown age left alone)
PASS
```

## 4. Try a run without sending mail

Three safe ways, from the lightest:

1. **Dry run**: `./console exp:notification:run --dry-run`. It runs inside a transaction that is rolled back and hands nothing
   to the mail transport. Addresses are masked (`n***@nottest.invalid`) unless you add `--addresses`.
2. **File transport**: `./console exp:notification:run --mail-file-dir=var/tmp/notification-mail` makes a real run (events are
   handled and removed, digests sent) but the mail is written to files in that directory, for this process only. Open a
   file to see exactly what would go out.
3. **Only one event**: add `--event=<id>` (find the id with `exp:notification:events list`) so a run does not touch the
   other events.

A complete rehearsal on a test installation (a test user follows a folder and has a daily digest at 8:00; an article is
published; the event is 21599):

```text
$ ./console exp:notification:run --event=21599 --mail-file-dir=var/tmp/notification-mail/doc2
PASS: 1 event(s) handled, 0 removed, 1 kept for a digest, 0 message(s) to 0 recipient(s), 79 ms.

$ ./console exp:notification:events list --status=handled
  21599  handled  ezpublish        2026-10-04 22:40:31  1 message(s) waiting
PASS: 1 event(s) shown

$ ./console exp:notification:run --at="2026-10-05 06:00" --mail-file-dir=var/tmp/notification-mail/doc2
Time event at 2026-10-05 06:00:00.
PASS: 13 event(s) handled, 13 removed, 0 kept for a digest, 0 message(s) to 0 recipient(s), 61 ms.

$ ./console exp:notification:run --dry-run --at="2026-10-05 09:00" --mail-file-dir=var/tmp/notification-mail/doc2
  would send "[alpha.se7enx.com] Digest for Sunday October 04 2026 10:40:33 pm" to 1 recipient(s): n***@nottest.invalid
PASS: dry run, 1 event(s) handled, 1 message(s) would be sent, 0 event(s) would be kept for a digest.

$ ./console exp:notification:run --at="2026-10-05 09:00" --mail-file-dir=var/tmp/notification-mail/doc2
PASS: 1 event(s) handled, 1 removed, 0 kept for a digest, 1 message(s) to 1 recipient(s), 125 ms.
```

The run at 06:00 sent nothing: the digest hour is 8:00. At 09:00 the digest goes. (The 13 events in the 06:00 run were
other pending events of the installation; a run without `--event` handles them all.)

Never test on a live site with the real transport and real subscribers. On a copy of a production database, also set
`[MailSettings] DebugSending=enabled` and `DebugReceiverEmail=` your own address in `site.ini` so that every mail goes to you.

## 5. Where the mail goes

`[MailSettings] Transport` in `site.ini` decides: `sendmail`, `smtp` or `file`. With `file` the mail is written to
`var/log/mail` (or the directory of `FileTransportDirectory`) as `<time>-<number>.mail`, one file per message, and no
mail leaves. See [the INI reference](../specifications/6.0/notifications-ini.md). The sender is `EmailSender` of
`notification.ini`, else of `site.ini`, else `AdminEmail`.

## 6. Velocity and Apache

| | Apache with PHP-FPM | Velocity |
|---|---|---|
| The cronjob | system cron runs `php runcronjobs.php`; unaffected | the same, or the Velocity scheduler |
| Run now / Preview | the page starts the PHP command line with `setsid`; it runs as the pool's user (on the test installation: the site user, PHP from the Plesk 8.5 installation) | the same code, started through the server's own process helper |
| Files in `var/` | created by whoever runs: the pool's user for the web, root for a console run | Velocity runs as root, so the files it makes belong to root |
| After you change PHP code | reload the PHP-FPM that serves the site (on Plesk: its own `plesk-php85-fpm` unit, not the system `php-fpm`), then clear the content cache | restart Velocity (its workers load classes once), then clear its response cache |
| Template changes | cleared by `php bin/php/ezcache.php --clear-all`; new template files need the template-override cache cleared too | the same, then clear Velocity's response cache |

Because a console run (root) and a web run (the site user) share `var/<var dir>/notification/`, the lock file and the run
record are created world-writable. If you move or recreate that directory by hand, keep it writable by both. The status
page and the console must see the same run record: run the console with the same siteaccess (`-s`) as the web, else the
var directory differs.

What was exercised on the test installation: the cronjob part, every command, and the status page with **Run now** and the
preview under Apache with PHP-FPM, in Chromium and Firefox. Run now under Velocity was not exercised in this run; it uses
the same command and progress file.

## 7. Troubleshooting

| Symptom | Cause and cure |
|---|---|
| "events wait and no run ... is recorded", thousands of pending events | The cronjob does not run (not in the crontab, wrong siteaccess, wrong user). Run `php runcronjobs.php -s site notification` by hand and read its output, then fix the crontab. A backlog is handled by one run (a test installation had 7259 events from weeks without a run: one run took 4 seconds). The run is also recorded only in the var directory of the siteaccess it ran with |
| "Another run holds the lock" | A run is going (check `exp:notification:status`: "running now ... process n") or a previous process is still alive (`ps`). The lock is released when the process ends, even when it crashed; a leftover `run.lock` file with old content is harmless. If a process hangs for hours, end it; `runcronjobs.php` also takes over a part after `MaxScriptExecutionTime` |
| "The lock file ... cannot be opened (permissions ...)" | The file belongs to another user and is not writable. Make `var/<var dir>/notification/run.lock` and `runs.jsonl` writable by the web user and the console user (`chmod 666`) |
| Nothing is sent although events were handled | Does the content have subscribers (`exp:notification:subscriptions list`)? Do they have `content/read` for it? Is the user enabled? Is the node hidden? Is the transport `file`? Check the notice **Mail is written to files** on the status page. Do a dry run: it lists what would be sent |
| A user gets nothing right after publishing | They chose a digest (messages wait; see `items kept for a digest`) or the cronjob did not run yet |
| The digest was not sent | A digest is sent by the first run after its time. Check that runs happen after the hour (`Recent runs`); the status page says "n digest messages are overdue" when due items wait. Weekly: the weekday is stored by name; a run in another language accepts the English name and numbers |
| The digest came at the wrong hour | The hour is the time zone of PHP on the server. The web and the command line can have different `date.timezone` settings: compare `php -i | grep timezone` and the PHP of the pool |
| The status page says "No run is recorded yet" after the crontab was set up | The cron runs another siteaccess or another var directory, or runs as a user that cannot write the record. Run it by hand with `-s` of the siteaccess the administration uses |
| **Run now** does nothing or says it failed | Read `var/<var dir>/notification/jobs/<id>.log` (the newest). Missing `setsid` or the PHP command line is reported on the page. A fatal error in the command lands in the log |
| "n handled events have nothing left to send" | Left over from before the system removed them itself; press the cleanup button, or `exp:notification:events cleanup` |
| "n subscriptions point to content that no longer exists" | The content was deleted after it was followed. Remove them: button, or `exp:notification:subscriptions remove-missing` |
| "A handler failed on n events" | A handler threw an error; the debug log (see [Operating a site](operating-a-site.md)) names the event and the message. The other handlers still ran; the event is not retried |
| A mail failed to send | The digest of that moment is lost (known issue, see the [upgrade notes](../bc/6.0/notification-ui-and-commands.md#known-issue)); look at the mail log of the server |

To look at the raw state: `./console exp:notification:status --json` has everything the page shows.

## Related pages

- [The user's guide](../features/6.0/notifications.md), [the developer's guide](notifications-developer.md)
- [The command reference](../specifications/6.0/notifications-cli.md), [the INI reference](../specifications/6.0/notifications-ini.md), [the specification](../specifications/6.0/notifications.md)
- [Operating a site](operating-a-site.md), [cronjobs console](../features/6.0/cronjobs-console.md), [the audit trail](../features/6.0/audit-trail.md)
- [Upgrade notes](../bc/6.0/notification-ui-and-commands.md)
