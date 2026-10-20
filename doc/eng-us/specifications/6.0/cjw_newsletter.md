# Specification: the cjw_newsletter extension

This page is the reference for the `cjw_newsletter` extension (4.1.17). It lists the admin views and their
parameters, the console commands, the cronjob parts, the background runs, the audit events, the tables and the
settings you can change. Read it if you install, schedule, script or debug the newsletter. The tour for editors and
the rehearsal without mail is on the [feature page](../../features/6.0/extensions/cjw_newsletter.md).

## In short

- Admin entry point: `/newsletter/index` (the **Newsletter** tab): a dashboard with the lists and subscribers, the editions, the last sends, the transport and the outbox, the last runs and the problems found.
- Three cronjob parts do the work: `cjw_newsletter_mailqueue_create` and `cjw_newsletter_mailqueue_process` (the part `cjw_newsletter` runs both) and `cjw_newsletter_mailbox` for bounces.
- Five console commands run the same code by hand: `ext:cjw_newsletter:queue`, `mailbox`, `import`, `repair` and `status`.
- Ten tables `cjwnl_*`, created from `sql/<database>/schema.sql` or from `share/db_schema.dba`.
- The left menu comes from `design/standard/templates/parts/newsletter/menu.tpl` (navigation part `eznewsletternavigationpart`) in every admin design.

## Example: send a newsletter from the shell and look at the result

Switch the transport to files (see the feature page), publish an edition, press **Send Newsletter** in the admin, then:

```bash
./console ext:cjw_newsletter:queue --dry-run     # what a run would do, nothing changed
./console ext:cjw_newsletter:queue               # create the queue, send the mails
./console ext:cjw_newsletter:status              # lists, users, sends, outbox, last runs, problems
```

`queue` prints `Queue: 1 sends, 4 items created.` and `Sent 4 mails, 0 failed, 1 sends finished.`; each mail is
a file in the outbox. A second run at the same time prints `Another queue run is active.` and exits with 1.

## Module `newsletter`

Views of the admin and of the public forms. A view keeps its URL since 4.0; the parameters below are the `(name)/value`
pairs of the URL.

| View | Policy function | Parameters | What it does |
|---|---|---|---|
| `index` | `index` | `(job)` | The dashboard; starts "Send now", "Count only", "Process the mail accounts" and "Remove them" in the background |
| `job` | `index` | `JobID` | The state of a background run as JSON (`status`, `result`, `log`) |
| `user_list` | `user_list` | `(q)`, `(status)`, `(list)`, `(sort)`, `(order)`, `(offset)`, `(limit)` | Users: `status` is `confirmed`, `pending`, `removed`, `bounced` or `blacklisted`; `sort` is `email`, `name`, `status`, `created` or `id`; `limit` is 10, 25, 50 or 100 |
| `user_view`, `user_edit`, `user_create`, `user_remove` | `user_view`, `user_edit`, `user_create`, `user_remove` | `NewsletterUserId` | One user; removal also removes the subscriptions |
| `subscription_list`, `subscription_view` | `subscription_list`, `subscription_view` | `NodeId`, `SubscriptionId` | Subscriptions of a list |
| `blacklist_item_list` | `blacklist_item` | `(q)`, `(sort)`, `(order)`, `(offset)`, `(limit)` | The blacklist; `sort` is `created`, `email` or `id` |
| `blacklist_item_add`, `blacklist_item_remove` | `blacklist_item` | none | Add (validated); remove asks first (`ConfirmRemoveButton`) |
| `mailbox_list`, `mailbox_edit` | `mailbox_list`, `mailbox_edit` | `MailboxId` | Mail accounts; the form validates every field, never shows the password and removal asks first |
| `mailbox_item_list`, `mailbox_item_view` | `mailbox_item_list`, `mailbox_item_view` | `(job)`, `(limit)`, `(offset)`, `MailboxItemId` | Collected mails; "Collect all mails" and "Parse mails" are POST buttons that start a background run |
| `import_list`, `import_view` | `import_list`, `import_view` | `(limit)`, `(offset)`, `ImportId` | CSV imports, newest first |
| `subscription_list_csvimport`, `subscription_list_csvexport` | `subscription_list_csvimport`, `subscription_list_csvexport` | `NodeId`, `ImportId` | CSV import (the policy `subscription_list_csvimport_import` allows "Import all") and export |
| `send`, `send_abort`, `preview`, `preview_archive`, `archive` | `send`, `preview`, `archive` | see the view | Sending, test mail, archive |
| `subscribe`, `subscribe_infomail`, `configure`, `unsubscribe` | `subscribe`, `configure`, `unsubscribe` | hashes | The public forms; the back link of a form is a path of this site only |

## Console commands

All of them start with `./console ext:cjw_newsletter:<name>` or `php extension/cjw_newsletter/bin/php/<name>.php`, take
`--help`, and have the alias `nl-<name>`. Exit code 0 is success, 1 is a failure or another run holding the lock.

| Command | Options | What it does |
|---|---|---|
| `queue` | `--dry-run`, `--create-only`, `--send-only`, `--job=ID` | Confirms users that waited for their eZ user, wakes the sends whose time has come, creates the queue and sends the mails |
| `mailbox` | `--dry-run`, `--collect-only`, `--parse-only`, `--job=ID` | Reads the active mail accounts and parses the bounces |
| `import` | `--import-id=N` (required), `--delimiter=comma\|semicolon\|pipe\|tab`, `--formats=0-1`, `--first-row-label`, `--dry-run`, `--job=ID` | Imports an uploaded CSV file of the import folder |
| `repair` | `--dry-run`, `--job=ID` | Removes subscriptions of removed users and unsent mails that can never be sent |
| `status` | none | Prints what the dashboard shows |

`--job` is set by the admin when it starts a command in the background: the command writes its state to
`var/cjw_newsletter/jobs/<id>.json` and its output to `<id>.log`, and the page shows both while it polls
`newsletter/job/<id>`.

## Cronjob parts

| Part | Script | What it does |
|---|---|---|
| `cjw_newsletter` | both scripts below | Creates the queue, then sends |
| `cjw_newsletter_mailqueue_create` | `cronjobs/cjw_newsletter_mailqueue_create.php` | As `queue --create-only` |
| `cjw_newsletter_mailqueue_process` | `cronjobs/cjw_newsletter_mailqueue_process.php` | As `queue --send-only` |
| `cjw_newsletter_mailbox` | `cronjobs/cjw_newsletter_mailbox.php` | As `mailbox`; not in the part `cjw_newsletter` |

The script files are one line each; the code is in `classes/runnable/cronjobs/`, and the cron parts, the commands
and the admin all call `CjwNewsletterRunner`. A run takes a file lock (`queue_create`, `queue_process`, `mailbox`,
`repair`, `import`), so two runs of one kind never overlap, whoever starts them. The time, the user (`cron`,
`console`, `job`) and the totals of the last run of each kind are kept in `ezsite_data` (`cjw_newsletter_last_*`).

## Audit events

One event per run, written when the audit is on: `system.cjw_newsletter.queue_create`, `queue_process`, `mailbox`,
`import` and `repair`. `object` is `cjw_newsletter:<run>`, `after` holds who started it and the totals; `result` is
`failed` with the reason `run` when the run had errors. A dry run writes nothing. The branch is registered in
`settings/audit.ini.append.php`.

## Settings you can change

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `cjw_newsletter.ini` | `NewsletterCsvImportSettings` | `ImportInBackground` | `enabled` | `enabled` starts the import of "Import all" as a command in the background; `disabled` (or a server without `proc_open`) imports inside the request |
| `cjw_newsletter.ini` | `NewsletterMailSettings` | `TransportMethodCronjob` | `file` | `smtp`, `sendmail` or `file` for the mails of the queue |
| `cjw_newsletter.ini` | `NewsletterMailSettings` | `FileTransportMailDir` | `var/log/mail` | Where `file` writes `.eml` files; the dashboard counts them |
| `cjw_newsletter.ini` | `NewsletterUserSettings` | `CustomFieldMappingArray[]`, `[CustomFieldMapping_<field>] Name` | not set | Public names of the custom data fields in the user view |
| `cronjob.ini` | `CronjobPart-cjw_newsletter_mailbox` | `Scripts[]` | `cjw_newsletter_mailbox.php` | The part that reads the mail accounts |

## Problems the dashboard reports

| Problem | Meaning | What to do |
|---|---|---|
| Tables missing | `cjwnl_*` tables are not created | Create them from `share/db_schema.dba` |
| Newsletter folder missing | `RootFolderNodeId` names no node | Create the tree (`CjwNewsletterClassInstaller::installTree()`) or set the node |
| Mails are written to files | `TransportMethodCronjob` is `file` | Intended for a rehearsal; switch to `smtp` or `sendmail` to send |
| Default sender address | `EmailSender` is still `@example.*` | Set it in the ini or on each list |
| Orphans | Subscriptions of removed users, unsent mails of removed users | Press **Remove them** or run `repair` |
| Stuck sends | A send waits for more than a day | Check that cron runs `cjw_newsletter` |
| Bounced users, unparsed mails, no active mail account | See the text | The bounces page and the mail accounts |

## Related pages

- [Feature page](../../features/6.0/extensions/cjw_newsletter.md)
- [Runnable commands, cronjob parts and views](runnable-commands-cronjobs-views.md)
- [Audit event model](audit-event-model.md)
