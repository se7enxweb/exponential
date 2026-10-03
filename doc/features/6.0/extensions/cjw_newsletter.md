# cjw_newsletter: newsletters in the admin

`cjw_newsletter` ("CJW Newsletter") is a complete newsletter system for Exponential: you
write newsletter editions as content, visitors subscribe to lists with a double opt-in,
editions are sent by cron in the background, bounces and a blacklist keep the lists
clean, and every send is archived. It comes from the CJW Network, coolscreen.de, JAC
Systeme and Webmanufaktur (2007 to 2015) and was taken over for PHP 8.5 and Exponential 6
by the se7enxweb project in August 2026 (4.0.0.0 on 13 August). Releases 4.1.0 to 4.1.15
(22 September to 2 October 2026) made it run on PHP 8, on persistent workers (Velocity) and
on every database, and closed a large number of defects found by a systematic review.

Open it at **Newsletter** in the top admin menu (`/newsletter/index`). The extension keeps
its own documentation (`doc/cjw_newsletter_documentation.pdf`, `INSTALL`, `FAQ`); this page
explains what changed in the Exponential 6 releases and how to test it safely.

## The parts

| Part | Where | What it is |
|---|---|---|
| Newsletter systems, lists, editions | content tree, classes `cjw_newsletter_system`, `_list`, `_edition` (shipped as packages) | Content you edit like any other; the tree root is `[NewsletterSettings] RootFolderNodeId` |
| Subscriptions and users | `newsletter/subscription_list`, `newsletter/user_list`, `user_view`, `user_edit`, `user_create` | Who is subscribed to what, with status (pending, confirmed, approved, removed by the user or by an administrator, soft or hard bounced, blacklisted) |
| Public forms | `newsletter/subscribe`, `configure`, `unsubscribe`, `subscribe_infomail` | Subscribe, confirm, change and leave |
| Sending | `newsletter/send`, `send_abort`, cronjobs `cjw_newsletter_mailqueue_create`, `cjw_newsletter_mailqueue_process` | Schedule an edition, abort it, and let cron queue and process the mails |
| Preview and archive | `newsletter/preview`, `preview_archive`, `archive` | Test sends and the archive of what was sent |
| Bounces | `newsletter/mailbox_list`, `mailbox_edit`, `mailbox_item_list`, `mailbox_item_view` | Read a mailbox for bounces; after `BounceThresholdValue` bounces (default 3) a user stops receiving |
| Blacklist | `newsletter/blacklist_item_list`, `blacklist_item_add`, `blacklist_item_remove` | Addresses that never get mail |
| CSV | `newsletter/subscription_list_csvimport`, `subscription_list_csvexport`, `import_list`, `import_view` | Import and export subscribers |

Run the cron parts with the console:

```bash
php runcronjobs.php cjw_newsletter
php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_create
php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_process
```

## Test the whole thing without sending a single mail

`cjw_newsletter.ini` can write mail to files instead of sending it. This makes a complete
rehearsal (subscribe, double opt-in, send, cron, unsubscribe) possible without a mail server
and without a real recipient:

```ini
[NewsletterMailSettings]
TransportMethodCronjob=file
TransportMethodPreview=file
TransportMethodDirectly=file
FileTransportMailDir=var/log/mail
```

Each recipient gets one uniquely named file `<date>-<id>-<recipient>.eml` in
`FileTransportMailDir` (a relative directory is relative to the installation; it must be
writable by the web server user and by the user of the cron job; a directory that cannot be
written is an error result, not a silent loss). To send for real, set a method to `smtp`
(with `SmtpTransportServer`, `SmtpTransportPort`, `SmtpTransportUser`,
`SmtpTransportPassword`, `SmtpTransportConnectionType`: empty, `ssl`, `sslv2`, `sslv3` or
`tls`) or `sendmail`. Unknown transport methods, and the documented `mta` method, are handled.

## What changed in the Exponential 6 releases

### Runs on PHP 8.5 and on a persistent worker

* 4.0.0.0: PHP 8.5 deprecations fixed (optional-before-required parameters, `utf8_encode()`
  replaced by `mb_convert_encoding()`, `(boolean)` casts, `case X;`).
* 4.1.0: module views no longer declare functions or classes at file top level without a
  guard, so a server that keeps PHP alive across requests (Velocity) does not die on the
  second request. See [Velocity engines](../../../bc/6.0/velocity-engines.md).
* 4.1.1: the mailbox list and the subscribe view answered HTTP 500 on PHP 8 and now work.

### Runs on every database

Three admin functions failed on Oracle and are portable since 4.1.7: the **subscription list
CSV export** (the preview appended `LIMIT 10`, which Oracle rejects), the **user search**
(a double-quoted pattern is an identifier outside MySQL, and `DISTINCT` over CLOB columns
fails) and **aborting a send** (backticks and a trailing semicolon). They now use the
database driver's own limit, single-quoted strings, `EXISTS` and plain identifiers.

### Security and robustness review (4.1.15)

A review of the whole extension on 2 October 2026 fixed, among other things:

* `user_create` unserialized a hidden form field (object injection); the "old post" field is
  now plain data.
* `user_create`, `user_edit`, `user_remove` and `blacklist_item_remove` redirected to any URL
  the request named; redirect targets are paths of this site only.
* The configure and unsubscribe hashes were an `md5` of the time and `mt_rand()`; the random
  part is `random_bytes()` now.
* The public forms (`subscribe`, `unsubscribe`) and the `send` and `mailbox_edit` views read
  their input defensively: a posted string where a list was expected, unchecked keys, an
  unknown mailbox id (formerly fatal) and Cancel falling through to a template that was never
  set. `createSubscriptionByArray()` subscribed to any object id; it only accepts newsletter
  lists now.
* CSV import and export: the tab delimiter works on PHP 8, lines longer than 1000 characters
  are kept, a null value no longer shifts the following columns of a row, cells that start
  with `=`, `+`, `-` or `@` are not written as formulas, and the import view no longer reads
  any file a posted `CsvFilePath` names.
* A user whose address contains an apostrophe was never found (the
  value was escaped twice) and could subscribe again and again; lookups also ignore case and
  blanks, and an empty remote id matches nobody.
* The open send items of a bounced user were not stored as aborted, so they were still sent;
  they are now. A stored bounce that cannot be parsed is marked processed instead of being
  read again and again, and a bounce that carries only the `x-cjwnl-user` header finds its
  user.
* The bounce parser reads codes and headers the way a mail server writes them: 4xx
  diagnostics keep their enhanced status, `x-cjwnl-` headers match case-insensitively, values
  containing a colon are not cut, an unknown mailbox type and a server that cannot list its
  messages no longer produce undefined variables, and unique ids are escaped.
* Virtual lists (filter based) run their constructor on PHP 8, their send path works, several
  external filters are joined correctly, and the siteaccess filter keeps every siteaccess, not
  only the last.
* The preview, `preview_archive` and `archive` views answer an unknown edition, version or
  output format with a clean error; `?Debug=1` returns their content as a result. The
  preview printed an empty newsletter for any id before.
* The edition output and siteaccess ini are fetched through a command line call that quotes
  every argument and passes `--allow-root-user` when the process is root; previously the
  preview, a send created from the command line and the list's siteaccess information did
  not start when run as root.
* The list and filter views post each button to its own form (nested forms are dropped by
  browsers: "Create newsletter" used to submit the class of the last form).

### Interface

* 4.1.2 onwards: visible texts and translations name Exponential. 4.1.4: every visible text
  is a translation string with German; 4.1.6: the top menu tab has an English tooltip with a
  German translation; 4.1.12: English and German for every string the admin showed
  untranslated.
* 4.1.8: the newsletter list and the form builder filter use jQuery 4 (`.on()`, `.prop()`).
  4.1.9: the admin2 children list's YUI drag and drop, which loaded a script that no longer
  exists, is removed; priorities are still set in the list and saved with its button.
  See [YUI removal](../../../bc/6.0/yui-removal.md).

### Integrity manifest

`share/filelist.md5` is the manifest the upgrade check (**Setup > System Upgrade > File
consistency check**) compares your files against. Before 4.1.5 it did not match the shipped
files and the check listed files nobody had touched (nine files in 4.0.0.0; twenty-one in
4.1.4). It now lists every tracked file, 309 in 4.1.15 including the 33 under
`classes/runnable/`.

### Command line

Every command and cronjob part is a class under `classes/runnable/` that the old file calls,
each with a one line description shown by the console list; behaviour, `--help` text and exit
codes are unchanged. See
[CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md).

## Settings you will use

| File | Block and key | Default | Meaning |
|---|---|---|---|
| `cjw_newsletter.ini` | `[NewsletterSettings] RootFolderNodeId` | `1` | Node whose children are the newsletter systems |
| | `[NewsletterSettings] PhpCli` | `php` | PHP command to run CLI scripts |
| | `[NewsletterSettings] AvailableSkinArray[]` | `default` | Skins in `design:newsletter/skin/<name>` |
| | `[NewsletterMailSettings] TransportMethodCronjob`, `TransportMethodPreview`, `TransportMethodDirectly` | see the file | `smtp`, `sendmail` or `file` per kind of mail (newsletter, test send, subscribe and info mails) |
| | `[NewsletterMailSettings] FileTransportMailDir` | `var/log/mail` | Where `file` writes `.eml` files |
| | `[NewsletterMailSettings] EmailSubjectPrefix` | empty | Subject prefix of newsletter mails |
| | `[BounceSettings] BounceThresholdValue` | `3` | Bounces before a user is marked bounced |
| | `[NewsletterCsvImportSettings] DefaultCsvDelimiter` | `;` | CSV delimiter |
| | `[NewsletterUserSettings] UseTplForNameGeneration` | `disabled` | |

## Related

* [Chronicle](../../../history/extensions/cjw_newsletter.md) and [release notes](../../../changelogs/extensions/cjw_newsletter.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
