# 10. After installing: the operations handbook

The installer leaves you with a site that answers its first request. This chapter turns it into a site you can
run: securing the administrator account, getting the site addresses right, scheduling the cronjobs that publish,
notify and clean up, sending real mail, knowing which cache to clear and when, keeping the class autoloads and the
search index current, reading and rotating the logs, taking backups you have actually restored, watching the site,
tuning it for speed, managing extensions and their order, and adding languages. Each part explains what the
mechanism does, gives the commands with the settings they read, and ends with a way to check the result. Work
through it once in order after the install; afterwards it serves as a reference.

[Previous: 9. Databases](09-databases.md) | [Next: 11. Upgrading](11-upgrading.md) | [Contents](README.md)

## Contents

- [Conventions in this chapter](#conventions-in-this-chapter)
- [10.1 First login and the administrator password](#101-first-login-and-the-administrator-password)
- [10.2 Siteaccesses and site addresses](#102-siteaccesses-and-site-addresses)
- [10.3 Cronjobs](#103-cronjobs)
- [10.4 Mail: the file transport and real sending](#104-mail-the-file-transport-and-real-sending)
- [10.5 Notifications and newsletters](#105-notifications-and-newsletters)
- [10.6 Caches](#106-caches)
- [10.7 Class autoloads](#107-class-autoloads)
- [10.8 Search indexing and image handling](#108-search-indexing-and-image-handling)
- [10.9 Backups and restore](#109-backups-and-restore)
- [10.10 Logs and log rotation](#1010-logs-and-log-rotation)
- [10.11 Monitoring and health checks](#1011-monitoring-and-health-checks)
- [10.12 Performance tuning](#1012-performance-tuning)
- [10.13 Extension management](#1013-extension-management)
- [10.14 Multi-language sites](#1014-multi-language-sites)
- [10.15 A checklist for the first week](#1015-a-checklist-for-the-first-week)
- [References](#references)

## Conventions in this chapter

- Every command runs from the installation root, the directory that holds `index.php`.
- `php bin/php/console` is the command console; many installations link it as `./console`. `php bin/php/console list`
  shows every command, `php bin/php/console list cron` one namespace.
- Scripts refuse to run as `root` unless given `--allow-root-user`. Run routine work, and above all cron, as the
  site's own user; then the flag is not needed and files are created with the right owner. The examples carry the
  flag so they can be copied as they are. One exception: `bin/php/ezpgenerateautoloads.php` does not know the flag
  (see [10.7](#107-class-autoloads)).
- Settings are never edited in `settings/*.ini`. Overrides go to `settings/override/<file>.ini.append.php` (the whole
  installation) or `settings/siteaccess/<name>/<file>.ini.append.php` (one siteaccess). `php bin/php/console exp:ini`
  reads and writes them in every scope ([exp:ini](../features/6.0/exp-ini-command.md)); after a hand edit run
  `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
- `site` and `admin` stand for your public and administration siteaccess names (the defaults of `exp:install`).

## 10.1 First login and the administrator password

1. **Find the address and the password.** The installer's summary printed the admin login address (with the default
   URL access, `/admin/user/login` below the site address) and the user, `admin`. A generated password was shown
   once and written to `var/log/initial-admin-password`, readable by its owner only.
2. **Sign in and change the password** at once: in the administration, **Change password** in the user menu, or
   directly `/admin/user/password`. The page lists the requirements beside the field: at least
   `[UserSettings] MinPasswordLength` characters (shipped `10`, `settings/site.ini`) and not a well-known password.
   The user gets an e-mail that the password was changed (`ChangeNotificationMail`), which is also the first test of
   your mail settings ([10.4](#104-mail-the-file-transport-and-real-sending)).
3. **Remove the password file** once you have signed in with the new password:

   ```bash
   rm var/log/initial-admin-password
   ```

4. **Give the administrator a real e-mail address** (edit the administrator's user object in the administration), so the password reset
   by mail reaches someone.

A lost password is reset on the command line. As the operating system's `root` user no other login is needed:

```bash
php bin/php/resetuserpassword.php -u admin -g --allow-root-user
```

`-g` generates a password (`-l <length>`, default 16) and prints it; `-p <password>` sets one; `-a <login>` with
`-ap <password>` authorises the reset with another administrator's login instead. See
[Reset a user password](../features/6.0/reset-user-password.md) and
[Changing your password](../features/6.0/modern-password-change.md).

## 10.2 Siteaccesses and site addresses

A **siteaccess** is one way into the installation: a name, a set of settings in `settings/siteaccess/<name>/`, and a
rule for which requests it answers. The installer creates at least the public siteaccess and the administration.
The rules live in `site.ini [SiteAccessSettings]`:

| Key | Shipped | Meaning |
|---|---|---|
| `AvailableSiteAccessList[]` | set by the installer | Every siteaccess the installation knows. |
| `MatchOrder` | `uri;host;port` | The ways a request is matched, tried in order: `uri`, `host`, `host_uri`, `port`, `servervar`. |
| `URIMatchType`, `URIMatchElement` | `element`, `1` | With `uri`: the first path element names the siteaccess (`/admin/...`). |
| `HostMatchType`, `HostMatchMapItems[]` | `map` | With `host`: a host name maps to a siteaccess, `HostMatchMapItems[]=www.example.com;site`. |
| `RelatedSiteAccessList[]` | | Siteaccesses that share one database. |
| `ForceVirtualHost` | `false` | `true` when the web server rewrites every address to `index.php`, which the shipped rules do. |
| `RemoveSiteAccessIfDefaultAccess` | `disabled` | `enabled` leaves the default siteaccess's name out of generated links. |
| `RequireUserLogin` | `true` in `site.ini` | Overridden per siteaccess; a public site sets it to `false`. |

In `site.ini [SiteSettings]` of each siteaccess:

| Key | Meaning |
|---|---|
| `SiteURL` | The site's address without the scheme (`www.example.com`, or `www.example.com/admin` for URL access). Used in mails, feeds, sitemaps and by `exp:benchmark`. |
| `DefaultAccess` | The siteaccess a request gets when no rule matched. |
| `SiteList[]` | The public siteaccesses, used by the administration for previews and links. |

The installer chose one of three layouts (`exp:install --access=url|host|port`):

- **URL** (`/admin`): one host name for everything; simplest behind any web server.
- **Host** (`admin.example.com`): a host name per siteaccess; best for production and for HTTPS certificates per host.
- **Port** (`:8081`): one port per siteaccess; useful for local work.

To change from URL to host access after the install, for example:

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]
HostMatchMapItems[]=www.example.com;site
HostMatchMapItems[]=admin.example.com;admin
```

and in `settings/siteaccess/site/site.ini.append.php` and `settings/siteaccess/admin/site.ini.append.php` set
`[SiteSettings] SiteURL=www.example.com` and `admin.example.com`. Clear the INI cache, add the host names to the web
server ([chapter 8](08-serving-the-site.md)) and to DNS, and check which siteaccess an address reaches:

```bash
php bin/php/console exp:ezrequestrules --help
curl -sI https://admin.example.com/ | head -3
```

Settings per site inside an extension, for hosting several sites from one installation, are explained in
[Per-site settings inside extensions](../features/6.0/multi-site-ini-overrides.md).

## 10.3 Cronjobs

Cronjobs do the work no visitor waits for: they publish and hide content on schedule, send notifications, resume
content jobs, run the audit trail's maintenance, import feeds, index content and clean up. Without them a site
works, but scheduled content never appears, notifications never go out and caches and drafts pile up on disk.

### How runcronjobs.php works

`runcronjobs.php` runs **parts**: named lists of scripts in `settings/cronjob.ini`. Extensions add their own parts
and directories through `[CronjobSettings] ExtensionDirectories[]`. Run without a part name, it runs the scripts of
`[CronjobSettings] Scripts[]`, the default part.

```bash
php runcronjobs.php --list --allow-root-user                       # every part and its scripts
php runcronjobs.php -s site frequent --allow-root-user              # one part
php runcronjobs.php -s site --script=notification.php --allow-root-user   # one script
php runcronjobs.php -s site --allow-root-user                       # the default part
```

`-s` (`--siteaccess`) chooses the siteaccess whose settings the scripts read; use the public siteaccess unless a
part says otherwise (the Oracle parts want the administration). `-q` suppresses output except errors. In `--list`
each script appears once per directory searched; that repetition is normal. Every part also has a console alias,
`php bin/php/console cron:<part>` (for example `cron:frequent`), and the administration runs any part from
**Setup > Cronjobs**, where the output follows while it runs and the page proposes crontab lines. Parts listed in
`[AdminSettings] ForbiddenParts[]` (shipped: `cluster_maintenance`, `unlock`) cannot be started from there.
`MaxScriptExecutionTime` (shipped `43200` seconds) bounds a script's run.

### The parts and their scripts

The kernel's parts (`settings/cronjob.ini`):

| Part | Script | What it does |
|---|---|---|
| default | `unpublish.php` | Carries out scheduled unpublish actions. |
| default | `rssimport.php` | Fetches the configured RSS imports into the content tree. |
| default | `indexcontent.php` | Indexes content whose indexing was delayed (`[SearchSettings] DelayedIndexing`). |
| default | `hide.php` | Carries out scheduled hide and unhide actions. |
| default | `internal_drafts_cleanup.php` | Removes internal drafts older than the configured age. |
| `frequent` | `notification.php` | Processes pending notification events and sends the messages and digests. |
| `frequent` | `workflow.php` | Runs pending workflow events and advances stalled workflows. |
| `frequent` | `contentjobs.php` | Resumes content jobs whose worker died and starts queued ones (large removes and copies). |
| `frequent` | `audit.php` | The audit trail: sink spools, alert rules, rotation by day, archives, retention, checkpoints. |
| `frequent` | `mailbounces.php` | Reads the bounce mailbox: hard bounces and complaints go on the suppression list. |
| `infrequent` | `basket_cleanup.php` | Removes abandoned shop baskets. |
| `infrequent` | `linkcheck.php` | Checks the internal and external links in published content. |
| `infrequent` | `mailpreferences.php` | E-mail preference retention: consent log, expired confirmations, old suppression entries. |
| `cache_cleanup` | `cachecleanup.php`, `httpcache_cleanup.php` | Removes view cache and cache-block files that can never be served again, and expired entries of the HTTP cache (`[CacheCleanupSettings] MaxAge`, shipped two days). |
| `cleanuprss` | `cleanuprss.php` | Trims RSS-imported content to the newest items of each feed; does nothing until `content.ini [RSSImportCleanupSettings]` names its classes. |
| `cluster_maintenance` | `clusterpurge.php` | Purges expired files of a database cluster backend. |
| `unlock` | `unlock.php` | Releases content objects stuck in a locked editing state. |

Single parts exist for `contentjobs`, `audit`, `notification`, `mailpreferences` and `mailbounces`, so each can run
on its own schedule. `cronjobs/` holds more scripts that no shipped part lists, to be run with `--script=` or added
to a part of your own: `session_gc.php` (expired sessions, see below), `old_drafts_cleanup.php`, `trashpurge.php`
(empties the trash: only if you mean it), `staticcache_cleanup.php`, `subtreeexpirycleanup.php` and
`updateviewcount.php`.

Active extensions add more parts; `php bin/php/console list cron` shows them with a description, for example the
newsletter parts ([10.5](#105-notifications-and-newsletters)), the sitemap parts of `bcgooglesitemaps` and
`xrowmetadata`, and the Oracle parts ([chapter 9](09-databases.md#maintenance)).

### A crontab

Install it in the crontab of the site user (`crontab -e -u <site user>`, or `php bin/php/console crontab:edit`;
`crontab:list` shows the current one):

```
# m   h  dom mon dow  command
*/5   *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site frequent >> var/log/cron-frequent.log 2>&1
*/15  *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site >> var/log/cron-default.log 2>&1
17    *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site infrequent >> var/log/cron-infrequent.log 2>&1
40    3  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site cache_cleanup >> var/log/cron-cleanup.log 2>&1
50    3  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site --script=session_gc.php >> var/log/cron-cleanup.log 2>&1
```

- Use the same PHP binary the web server uses (`/opt/.../php` on hosts with several PHP versions); `php -v` in the
  cron environment may differ from your shell's.
- `frequent` every few minutes is what notifications and content jobs expect; `infrequent` hourly or daily.
- `session_gc.php` removes sessions older than `site.ini [Session] SessionTimeout` (shipped `259200` s, three days),
  where the operating system's PHP session cleanup does not run. The same by hand:
  `php bin/php/ezsessiongc.php --allow-root-user`.
- Rotate the `cron-*.log` files with the system's logrotate ([10.10](#1010-logs-and-log-rotation)).

Check that the jobs run: the files grow, **Setup > Cronjobs** shows the last output, and the notification status
command reports the last run ([10.5](#105-notifications-and-newsletters)).

Velocity has a [scheduler](../features/6.0/velocity-scheduler.md) of its own that runs handler functions at times or
intervals while the server runs. It does not replace the system cron for these parts out of the box: it needs a
handler you write, and it has no catch-up for times missed while the server was down.

## 10.4 Mail: the file transport and real sending

Exponential sends mail for password changes, registrations, forms, notifications and newsletters. The kernel's
transport is set in `site.ini [MailSettings]`:

| Key | Shipped | Meaning |
|---|---|---|
| `Transport` | `sendmail` | `sendmail`, `SMTP` or `file` (`TransportAlias[]` maps them to `eZSendmailTransport`, `eZSMTPTransport`, `eZFileTransport`). |
| `TransportServer`, `TransportPort` | empty, `25` | The SMTP server. Secure SMTP is usually 465 (`ssl`) or 587 (`tls`). |
| `TransportConnectionType` | empty | empty (no encryption), `ssl` or `tls`. |
| `TransportUser`, `TransportPassword` | empty | SMTP login. |
| `SenderHost` | `localhost` | The name given in the SMTP `EHLO`. |
| `AdminEmail` | a placeholder | The administrator's address; the sender when nothing else is set. **Change it.** |
| `EmailSender`, `EmailReplyTo` | empty | `From` and `Reply-To` unless a template sets them. |
| `ContentType`, `OutputCharset` | `text/plain`, `utf-8` | |
| `SendmailOptions[]` | empty | Extra options for sendmail, one per line, e.g. `-f` with the envelope sender. |
| `DebugSending`, `DebugReceiverEmail` | `disabled` | `enabled` sends every mail to one test address instead of its recipients. |
| `ExcludeHeaders[]` | empty | Headers removed before sending over SMTP (add `bcc` if your server does not hide them). |

**The file transport** writes every mail as a file instead of sending it: `Transport=file` puts them in
`var/log/mail/` (or the directory of `[MailSettings] FileTransportDirectory`), one `<time>-<random>.mail` file per
message. Use it on a test or staging site, to see exactly what would go out without anyone receiving it:

```bash
ls -lt var/log/mail/ | head
```

**Real sending.** For production, use SMTP to your organisation's mail server or a relay service:

```ini
# settings/override/site.ini.append.php
[MailSettings]
Transport=SMTP
TransportServer=smtp.example.com
TransportPort=587
TransportConnectionType=tls
TransportUser=<smtp user>
TransportPassword=<smtp password>
SenderHost=www.example.com
AdminEmail=webmaster@example.com
EmailSender=noreply@example.com
```

Mail that should reach real inboxes needs a sender address of a domain you control, with SPF (and preferably DKIM
and DMARC) records that allow the sending server, for every address family the server sends from: if the server
sends over IPv6 and the SPF record lists only IPv4 addresses, large providers reject the mail. Send a test by
changing a test user's password, or by a form with an e-mail receiver, and read the mail server's log.

Some extensions have their own transport settings. The newsletter extension's are described in
[10.5](#105-notifications-and-newsletters); the e-mail preference system (categories, consent, suppression list, the
bounce mailbox) in [E-mail preferences: setting them up and running them](../guides/mail-preferences-administrator.md).
`php bin/php/console exp:mailstatus` shows the preference system's state.

## 10.5 Notifications and newsletters

### Notifications

Users subscribe to subtrees; publishing creates **events**; the `notification.php` script (in `frequent`, and as
the part `notification`) turns events into mails and digests. The settings are in `settings/notification.ini`.

```bash
php runcronjobs.php -s site notification --allow-root-user          # run it once by hand
php bin/php/console exp:notificationstatus                          # pending events, last runs, problems
php bin/php/console exp:notificationrun --dry-run                   # who would get what, nothing sent
```

The status page in the administration and the status command flag the usual faults: events waiting with no run
recorded (cron is not set up), a failed run, an overdue digest, the file transport still active, a missing sender.
The complete procedure, with each fault and its fix, is
[Notifications: running them and fixing problems](../guides/notifications-administrator.md).

### Newsletters (cjw_newsletter)

When the newsletter extension `cjw_newsletter` is active, sending is a two-step job run by cron:

| Part | Scripts | What it does |
|---|---|---|
| `cjw_newsletter` | `cjw_newsletter_mailqueue_create.php`, `cjw_newsletter_mailqueue_process.php` | Checks pending subscribers, builds the mail queue of the editions due, and sends it. |
| `cjw_newsletter_mailqueue_create` | `cjw_newsletter_mailqueue_create.php` | Only builds the queue. |
| `cjw_newsletter_mailqueue_process` | `cjw_newsletter_mailqueue_process.php` | Only sends what waits in the queue. |
| `cjw_newsletter_mailbox` | `cjw_newsletter_mailbox.php` | Collects the mails of the bounce accounts and parses them (not part of `cjw_newsletter`). |

```
*/10  *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site cjw_newsletter >> var/log/cron-newsletter.log 2>&1
25    *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site cjw_newsletter_mailbox >> var/log/cron-newsletter.log 2>&1
```

The extension has its **own transports**, in `cjw_newsletter.ini [NewsletterMailSettings]` (override in
`settings/override/cjw_newsletter.ini.append.php`):

| Key | Shipped | Used for |
|---|---|---|
| `TransportMethodCronjob` | `file` | the newsletter editions the cronjob sends |
| `TransportMethodPreview` | `sendmail` | test newsletters from the editor |
| `TransportMethodDirectly` | `file` | subscription confirmations and info mails |
| `FileTransportMailDir` | `var/log/mail` | where the file transport writes, one `.eml` file per mail |
| `SmtpTransportServer`, `SmtpTransportPort`, `SmtpTransportUser`, `SmtpTransportPassword`, `SmtpTransportConnectionType` | | the SMTP connection when a method is `smtp` |

So a fresh installation **simulates** newsletter sending: the editions and confirmations land as files in
`var/log/mail/`, which lets you test subscription, double opt-in, sending and unsubscribing without a mail server
and without real recipients. To send for real, set the methods to `smtp` (with the `SmtpTransport*` keys) or
`sendmail`. The directory must be writable by the web server user and by the cron user. Deliverability, throttling
and bounce handling are documented with the extension and in
[the cjw_newsletter specification](../specifications/6.0/cjw_newsletter.md).

## 10.6 Caches

Exponential caches at many levels, and most "my change does not show" questions are cache questions. The rule is:
**clear the smallest cache that explains the problem**, and clear caches in front of PHP only after PHP runs the new
code.

### The caches by id and tag

`bin/php/ezcache.php` clears by **id** (one cache) or **tag** (every cache that carries it). The list below is
what `--list-ids --verbose` prints on a default installation, with the tags from `kernel/classes/ezcache.php` and the
extensions' `[Cache_*]` blocks:

| Id | What it holds | Tags | Clear it when |
|---|---|---|---|
| `content` | the content view cache (rendered pages' main area) | content | content looks stale; done automatically on publish |
| `exphttpcache` | the role-aware HTTP cache (when `httpcache.ini` enables it) | content, template | after a template change, when it is on |
| `querycache` | SQL query results (when `querycache.ini` enables it) | content, ini | data was changed outside Exponential |
| `global_ini` | the global INI cache | ini | a setting changed |
| `ini` | the INI cache | ini | a setting changed |
| `codepage` | character set mappings | codepage | rarely |
| `classid` | class identifier lookups | content | a class identifier changed |
| `sortkey` | sort key lookups | content | |
| `urlalias` | URL alias wildcard cache | content | URL aliases misbehave |
| `chartrans` | character transformation tables (URL aliases) | i18n | transformation rules changed |
| `imagealias` | generated image variations | image | an alias definition in `image.ini` changed |
| `template` | compiled templates | template | a `.tpl` changed while compilation caching is on |
| `template-block` | `{cache-block}` output | template, content | menus or blocks look stale; a stylesheet changed (the packed link is cached in one) |
| `template-override` | the override rules of `override.ini` | template | a new template file or override rule (else: a 200 page with an empty content area) |
| `texttoimage` | text-to-image output | template | |
| `rss_cache` | generated feeds | content | |
| `user_info_cache` | user and role information | user | roles or policies changed and a user does not see it |
| `content_tree_menu` | the admin tree menu in the browser | content | |
| `state_limitations` | object state limitations | content | |
| `content_language` | the language list | content | a language was added |
| `design_base` | the list of design directories | template | a design or extension was added |
| `active_extensions` | the list of active extensions | ini | `ActiveExtensions` changed |
| `translation` | compiled `.ts` translations | i18n | a translation file changed |
| `sslzones` | SSL zone settings | ini | |
| `rest`, `rest-routes` | REST application and route caches | content, rest / rest | REST configuration changed |
| `ezjscore-packer` | packed JavaScript and CSS files | content, template | a script or stylesheet of the packer changed |

The tags are therefore `content`, `template`, `ini`, `codepage`, `i18n`, `image`, `user` and `rest`.

```bash
php bin/php/ezcache.php --list-ids --verbose --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=template-override,design_base --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
# remove the files for real (saves disk space), only those older than two days:
php bin/php/ezcache.php --clear-id=content --purge --expiry="-2 days" --allow-root-user
```

Without `--purge` a clear only moves an expiry timestamp (or moves a directory aside), so it is instant; the
`cache_cleanup` cron part removes what can never be served again. `-s <siteaccess>` clears the cache of another
siteaccess's var directory.

### Which to clear

| You changed | Clear |
|---|---|
| an `.ini` setting | `--clear-tag=ini` (not `--clear-id=ini`, which misses the global INI cache) |
| a `.tpl` file | `--clear-tag=template`: compiled templates are not compared with their source unless `[TemplateSettings] DevelopmentMode=enabled`, which live sites should leave off |
| a new `.tpl` file, an override rule, an extension or design | `--clear-id=template-override,design_base` |
| a stylesheet or script | `--clear-id=ezjscore-packer,template-block`, then the Velocity cache |
| content looks stale | `--clear-tag=content` |
| roles and policies | `--clear-tag=user` |
| an image alias definition | `--clear-id=imagealias` |
| a translation | `--clear-tag=i18n` |
| unsure | `--clear-all` (costs a slower first view of every page afterwards) |

### The caches in front of PHP

`php bin/php/cache.php` (console: `exp:cache`) does every **Setup > Cache** action from the command line, ends with
PASS or FAIL, accepts `--dry-run` and `--json`, and also reaches the caches `ezcache.php` does not:

```bash
php bin/php/cache.php list --sizes --allow-root-user     # every cache, its tag, size and how it is cleared
php bin/php/cache.php status --allow-root-user           # static, HTTP, query, Velocity, precompress, PHP caches
php bin/php/cache.php velocity clear --allow-root-user   # Velocity's response cache
php bin/php/cache.php httpcache status --allow-root-user
php bin/php/cache.php opcache status --allow-root-user
```

**Velocity's response cache** answers anonymous repeat requests in the server's parent process, without PHP.
After a template or stylesheet change clear it (no restart needed):

```bash
php bin/php/console exp:velocity cache clear --allow-root-user
php bin/php/console exp:velocity cache stats --allow-root-user
```

### The order after a code change

A page rendered by the old code between a cache clear and a PHP reload is cached again and looks as if the change
had not worked. The order is: INI and template caches, then reload PHP-FPM and restart Velocity, and **only then**
the content, HTTP, template-block and response caches. Under Velocity one command does it, with PASS or FAIL per step
and a stop at the first failure:

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user   # what it would do
php bin/php/console exp:velocity deploy --allow-root-user             # do it
php bin/php/console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

`--packer` also clears the packed scripts and styles; `--no-fpm`, `--no-velocity` and `--no-autoload` skip steps.
The PHP-FPM service it reloads is `velocity.ini [DeploySettings] PhpFpmService` (`auto` finds it). See
[Velocity engines: deploying a PHP change](../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy).

## 10.7 Class autoloads

Exponential finds classes through generated autoload arrays in `autoload/` (the kernel) and `var/autoload/` (the
extensions). A class that is not in an array is not found, even if its file is in place: "Class ... not found"
right after adding an extension or a class is the classic symptom.

```bash
php bin/php/ezpgenerateautoloads.php -e        # extensions: after adding, renaming or removing a class
php bin/php/ezpgenerateautoloads.php -k        # the kernel: only when kernel/ or lib/ changed
php bin/php/ezpgenerateautoloads.php -o        # kernel overrides
php bin/php/ezpgenerateautoloads.php -e -n     # dry run: report, write nothing
php bin/php/ezpgenerateautoloads.php -e -p     # with progress output
```

This script does **not** accept `--allow-root-user`: given the flag it prints its usage and does nothing (with exit
status 0, so a script does not notice). Run it as the site user, or as root without the flag.

Which directories it walks is limited by **`.autoloadignore`** in the installation root, one directory per line,
anchored at the root, `#` for comments. The shipped file excludes `vendor` (Composer resolves those classes itself),
`ai`, `var` and `.claude` (working copies, which would otherwise map every kernel class to a copy). Add a directory
there when it holds PHP files that are not application classes; `--exclude` does the same for one run.

After regenerating, clear the INI cache and, under Velocity, restart the workers: they keep the arrays they loaded
when they started. `exp:velocity deploy` (with `--kernel` for the kernel array) does all three.
`php bin/php/console exp:checkclasses` loads every declared class and reports those PHP refuses to load.

## 10.8 Search indexing and image handling

### Search

The built-in engine is `eZSearchEngine` (`site.ini [SearchSettings] SearchEngine`); it keeps its index in the
database. Content is indexed when it is published, unless `DelayedIndexing` is `enabled` (or `classbased` with
`DelayedIndexingClassList[]`): then publishing only queues it and the default cron part's `indexcontent.php`
indexes it later, which makes publishing faster for large objects.

Rebuild the whole index after an import, a database move or when search results are wrong:

```bash
php bin/php/updatesearchindex.php --clean --allow-root-user      # --clean removes the old index first
```

Search statistics (`LogSearchStats`) are disabled by default, because they store every phrase visitors type, which
can be personal data. For a Solr server, `php bin/php/console exp:solr` controls it once installed and configured.

### Images

Uploaded images live in `var/<site var dir>/storage/images`; their variations (**aliases**) are generated on first
use. The rules are in `settings/image.ini`:

- `[AliasSettings] AliasList[]` names the aliases (`reference`, `small`, `tiny`, `medium`, `large`, `rss` and those
  of your design), each with a block of filters.
- `[ImageConverterSettings] ImageConverters[]` lists the converters in order: **GD** first (PHP's own extension,
  no external program per image), **ImageMagick** as the fallback for formats GD cannot read (PSD, TIFF, PDF, WebP)
  and filters only it has. `[ImageMagick] ExecutablePath` and `Executable` (`convert`) find the program; check
  with `convert -version`.
- `[OutputSettings] AllowedOutputFormat[]` (JPEG, PNG, WebP, GIF) and `LockTimeout` (seconds a process waits for
  another generating the same alias).

After changing an alias definition, remove the generated variations so they are made again:

```bash
php bin/php/cache.php imagealias clear --allow-root-user
```

## 10.9 Backups and restore

A backup is the **database** and the **files**, taken at the same moment. Files that matter:

| Path | Why |
|---|---|
| `var/storage` and `var/<site>/storage` | uploaded images and files |
| `settings/override`, `settings/siteaccess` | the installation's settings (they contain passwords: keep the backup private) |
| your own extensions and designs | if they are not in a repository |
| `var/storage/sqlite3/` | the database, on SQLite (taken separately, see below) |
| `var/log` | optional, for later investigation |

Caches (`var/cache`, `var/<site>/cache`) need no backup: they are regenerated.

### Procedure

1. Put the site in maintenance mode, so no write happens between the database and the file backup. Visitors get a
   503 page; `--allow-admin` keeps the administration reachable, `--allow-ip` lets given addresses through:

   ```bash
   php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
   php bin/php/maintenance.php status --allow-root-user
   ```

2. Back up the database with the tool of its engine:

   | Engine | Backup | Restore |
   |---|---|---|
   | SQLite | `sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/db.sqlite'"` | `sqlite3 var/storage/sqlite3/exponential.db ".restore '/backup/db.sqlite'"` |
   | MySQL, MariaDB | `mysqldump --single-transaction -u USER -p DATABASE > /backup/db.sql` | `mysql -u USER -p DATABASE < /backup/db.sql` |
   | PostgreSQL | `pg_dump -U USER -Fc DATABASE > /backup/db.dump` | `pg_restore -U USER -d DATABASE --clean /backup/db.dump` |
   | MongoDB | `mongodump --uri="mongodb://USER@HOST/DATABASE" --out=/backup/mongo` | `mongorestore --uri="..." --drop /backup/mongo` |
   | Oracle | `php bin/php/console ext:ezoracle:datapump --export=db.dmp` (or `expdp`) | `ext:ezoracle:datapump --import=db.dmp` |

   `-p` makes the client ask for the password; never write it on the command line. For SQLite read the details in
   [chapter 9](09-databases.md#sqlite-backup-and-restore): the online backup is safe while the site runs, a plain copy
   of a busy file is not.

3. Back up the files:

   ```bash
   tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
   ```

   (add `var/<site>/storage` when the site's var directory is separate, and your own extensions).

4. Switch maintenance off: `php bin/php/maintenance.php off --allow-root-user`.

Store backups away from the server, and keep several generations. On SQLite and MySQL with InnoDB the database
backup is consistent without maintenance mode; maintenance mode makes the database and the files agree.

### Restore

1. Maintenance on. Under Velocity also stop the server (`php bin/php/console exp:velocity stop --allow-root-user`),
   so no worker holds the old database.
2. Restore the database (table above) and unpack the files over the installation; fix the owner and mode
   ([chapter 8](08-serving-the-site.md)).
3. Clear every cache and regenerate the autoloads:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

4. Start Velocity, clear its response cache, switch maintenance off, and open the front page and the administration.

**Test a restore** on another machine at least once and after every major upgrade: a backup that was never
restored is a hope, not a backup. See [Maintenance mode](../features/6.0/maintenance-mode.md) and
[Operating a site, part 6](../guides/operating-a-site.md#6-back-up-and-restore).

## 10.10 Logs and log rotation

| File (under `var/log/` unless noted) | Written by | Read it for |
|---|---|---|
| `error.log` | the kernel, always (`site.ini [DebugSettings] AlwaysLog[]=error`) | failures: the first place to look |
| `warning.log`, `notice.log`, `debug.log`, `strict.log` | the kernel, when enabled | investigations |
| `kickstart.log`, `exp-install-<date>.ini` | the installers (passwords masked) | install problems |
| `mail/` | the file transports | the mails that would have gone out |
| `oracle-slow.log` | `ezoracle`, with `SlowQueryThreshold` | slow Oracle statements |
| `cron-*.log` | your crontab redirections | cron output |
| `<site var dir>/log/cronjobs/output.log`, `error.log` | jobs started from **Setup > Cronjobs** (`cronjob.ini [AdminSettings] LogFile`, `ErrorFile`) | browser-started jobs |
| Velocity's logs | the server (`velocity.ini [LogSettings]`) | the application server |
| the audit trail | `audit.php`; read with `php bin/php/audit.php` | who did what |

```bash
tail -n 50 var/log/error.log
php bin/php/audit.php status --allow-root-user
php bin/php/audit.php tail --channel=access --lines=20 --allow-root-user
```

**Built-in rotation.** The kernel rotates its own logs: when a file passes 200 KB it becomes `.1`, the older ones
move up, and three are kept (`error.log.1` to `error.log.3`). Change the limits in `config.php`, the
installation's PHP configuration file (`config.php-RECOMMENDED` lists them):

```php
define( 'EZPUBLISH_LOG_MAX_FILE_SIZE', 10485760 );   // 10 MB per file
define( 'EZPUBLISH_LOG_ROTATE_FILES', 10 );          // keep ten; 0 switches built-in rotation off
define( 'CUSTOM_LOG_MAX_FILE_SIZE', 10485760 );       // logs written through eZLog
define( 'CUSTOM_LOG_ROTATE_FILES', 10 );
```

**System rotation.** Files the kernel does not write itself (your `cron-*.log` files) need the system's logrotate.
If you switch the built-in rotation off, logrotate takes over the kernel's logs as well:

```
# /etc/logrotate.d/exponential
/path/to/installation/var/log/cron-*.log {
    weekly
    rotate 8
    compress
    missingok
    notifempty
    copytruncate
    su <site user> <site group>
}
```

`copytruncate` because the writers keep the file open. Never leave `[DebugSettings] DebugOutput=enabled` on in
production: it shows internals to visitors and writes a lot.

## 10.11 Monitoring and health checks

There is no single health URL; combine a check from outside with the status commands inside. Every command below
ends with a recognisable status, and most have `--json` for a monitoring system.

| Check | Command | Healthy |
|---|---|---|
| The site answers | `curl -s -o /dev/null -w '%{http_code} %{time_total}\n' https://www.example.com/` | `200` and a time you know |
| The administration answers | `curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/admin/user/login` | `200` |
| The caches | `php bin/php/cache.php status --json --allow-root-user` | ends in `PASS` |
| Velocity | `php bin/php/console exp:velocity status --json --allow-root-user` | running, workers as configured |
| Notifications | `php bin/php/console exp:notificationstatus` | no events waiting without a recent run |
| The audit trail | `php bin/php/audit.php status --allow-root-user` | every channel `intact` |
| New errors | `tail -n 100 var/log/error.log` | nothing you cannot explain |
| Cron | `php bin/php/console crontab:list` and the `cron-*.log` dates | entries present, logs recent |
| Disk | `df -h /path/to/installation` | room for caches, logs and a backup |
| The database | engine specific; SQLite: [chapter 9](09-databases.md#monitoring-a-sqlite-site); Oracle: `ext:ezoracle:health` | |
| Performance | `php bin/php/console exp:benchmark --baseline=var/benchmark/base.json` | exit status 0 (no regression) |

A small script in the site user's crontab that runs these and mails on a non-zero exit status is enough for a
single server. In the administration, **Setup > System information** shows versions, PHP and the server engine.

## 10.12 Performance tuning

Measure before and after every change. `exp:benchmark` requests the front page, the first menu pages and the admin
login page, 100 times each, 4 at a time, and prints percentiles; `kernel` mode times the parts of a page
in-process (boot, render cold and warm, INI load, content fetch, database round trip, cache write and read):

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
# ... make a change ...
php bin/php/console exp:benchmark --baseline=var/benchmark/base.json --threshold=15 --allow-root-user
php bin/php/console exp:benchmark kernel --repeat=30 --allow-root-user
php bin/php/console exp:benchmark --compare=https://www.example.com,https://www.example.com:8443 --allow-root-user
php bin/php/console exp:benchmark --cold --requests=50 --allow-root-user   # past the response caches
```

It is polite by default: against another host than this machine more than 1000 requests per URL or 16 at a time
needs `--force`. See [Benchmark: exp:benchmark](../features/6.0/benchmark.md).

### The levers, in order of effect

1. **Serve with Velocity** and its response cache ([chapter 8](08-serving-the-site.md)). A cached anonymous page is
   answered without PHP; persistent workers (`ForkPerRequest=disabled`) keep the application, the database
   connection and the compiled files warm.
2. **Keep the content view cache and template-block cache on** (`[ContentSettings] ViewCaching=enabled`,
   `[TemplateSettings] TemplateCache=enabled`, both shipped). Warm the site after a clear:
   `php bin/php/warm.php --allow-root-user` requests every published page.
3. **OPcache** (next part).
4. **APCu** for Velocity's response cache memory tier and the SQL query cache in `shared` mode:
   `velocity.ini [PHPSettings] IniOptions[]=apc.shm_size=256M` is shipped; too small and the two evict each other.
5. **The SQL query cache** (`querycache.ini [QueryCacheSettings] Mode=request` or `shared`), measured on your site
   ([SQL query cache](../bc/6.0/sql-query-cache.md)).
6. **The database**: [chapter 9](09-databases.md) (SQLite `ANALYZE`, Oracle persistent connections and DRCP).

**Leave the static cache off.** `[ContentSettings] StaticCache=disabled` is the shipped value, and the installer
writes it so. With the static cache on, every publish regenerated every affected page on every public siteaccess over
HTTP inside the editor's request, so a publish that changed nothing took seconds instead of a fraction of one.
Velocity's response cache gives the same benefit to visitors without that cost.

### OPcache

Under **PHP-FPM**, enable OPcache in the pool's `php.ini` ([OPcache configuration](https://www.php.net/manual/en/opcache.configuration.php)):

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.interned_strings_buffer=32
; keep timestamp checks on: Exponential rewrites PHP cache files (INI caches,
; compiled templates, expiry.php) under the same name
opcache.validate_timestamps=1
opcache.revalidate_freq=2
```

Never set `opcache.validate_timestamps=0` on an Exponential site: a cache file rewritten after a clear would be served
in its old form until PHP restarts.

Under **Velocity**, the server passes its own PHP options (`settings/velocity.ini [PHPSettings] IniOptions[]`), and two
of them differ from PHP-FPM for a reason worth understanding. OPcache compares a file's time against the **request's
start time**, and a Velocity worker is one long request that started when the worker did:

- `opcache.revalidate_freq=0`: with the default of 2 a worker never checked a file again, so a rewritten cache file
  was served as it was when the worker first compiled it.
- `opcache.file_update_protection=0`: OPcache does not keep a script changed less than this many seconds before the
  request's start. For a worker, every file written after it started (INI caches, override caches, compiled
  templates, all regenerated after a deploy or clear) counted as "too new" for the worker's whole life and was
  compiled again on every include: the cache served a few percent of includes and a render took about four times the
  CPU it takes under PHP-FPM. The protection is not needed because the server reads includes whole and drops the
  cached copy of a file changed in the last two seconds itself.

The shipped `velocity.ini` sets both, with `opcache.enable_cli=1` (the command-line PHP that runs the server may have
OPcache off system-wide), `memory_consumption=256`, `max_accelerated_files=20000` and `interned_strings_buffer=32`.
Check with `php bin/php/cache.php opcache status --allow-root-user`; read
[Velocity and the opcode cache](../features/6.0/velocity-opcode-cache-and-profile.md) for how to profile it. Any PHP
setting that compares against "the request time" behaves as frozen in a Velocity worker; suspect that first when
something works under PHP-FPM and not under Velocity.

## 10.13 Extension management

Extensions live in `extension/<name>/` (more roots with `site.ini [ExtensionSettings]
AdditionalExtensionDirectories[]`). Most arrive through Composer (`composer.json`); some are copied in.

**Activating.** An extension does nothing until it is active:

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=myextension
```

`ActiveAccessExtensions[]` in a siteaccess's `site.ini.append.php` activates one for that siteaccess only (loaded
after the siteaccess is chosen). The administration does the same under **Setup > Extensions**. After activating:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=template-override,design_base,active_extensions --allow-root-user
```

and restart Velocity (or run `exp:velocity deploy`). If the extension brings database tables, install them as its
documentation says (often `share/db_schema.dba` with `bin/php/ezsqlinsertschema.php`).

**Order.** When two extensions provide the same setting, template or design file, the **earlier** one in
`ActiveExtensions[]` wins: each extension's settings directory is prepended in turn, so a later extension ends up
with lower priority. `settings/override/` always wins over every extension, and a siteaccess's settings over the
global ones. `[ExtensionSettings] ExtensionOrdering=enabled` (shipped) also sorts extensions by the dependencies they
declare in `extension.xml`, which cannot fix every case (it does not reorder across `ActiveExtensions` and
`ActiveAccessExtensions`). So:

- a site or theme extension that should override another goes **before** it;
- an extension that only adds settings to one that ships a full file (for example connection settings for
  `ezoracle`) goes **before** that extension;
- change the order in **Setup > Extensions**, card *Loading order*, which saves only `ActiveExtensions` and keeps a
  backup copy ([Extension loading order](../features/6.0/extension-loading-order.md)).

Check the effective value of a setting with `php bin/php/console exp:ini get <file>/<Block>/<Key> --allow-root-user`
after every change of order. The installed versions, licences and websites are listed on the about page
(`/ezinfo/about`). Building and releasing extensions of your own: [Extensions](../guides/extensions.md).

## 10.14 Multi-language sites

Content languages and interface languages are separate:

- **Content languages** are the languages objects are translated into. Add one in the administration under
  **Setup > Languages** (the `content/translations` view); afterwards editors can translate objects into it.
- **The interface language** of a siteaccess is `site.ini [RegionalSettings] Locale` (shipped `eng-GB`); an
  administrator can choose their own in the user preferences.

Per siteaccess, `[RegionalSettings]` decides what a visitor sees:

```ini
# settings/siteaccess/site_de/site.ini.append.php
[RegionalSettings]
Locale=ger-DE
ContentObjectLocale=ger-DE
# the languages shown, in order of preference; the first that exists is used
SiteLanguageList[]
SiteLanguageList[]=ger-DE
SiteLanguageList[]=eng-GB
# disabled: objects in none of these languages are not shown
ShowUntranslatedObjects=disabled
TextTranslation=enabled
```

A typical multi-language site has one public siteaccess per language (`site_en`, `site_de`), matched by URL element or
host name ([10.2](#102-siteaccesses-and-site-addresses)), and lists them all in `RelatedSiteAccessList[]` because they
share one database. Do not change `ContentObjectLocale` of an existing site lightly: objects that do not exist in the
new default language disappear from listings. At install time, give every language in `--languages=` (or
`Languages[]` in `kickstart.ini`): a site package whose content uses a locale that is not installed shows pages
without images or sub-items.

Interface translations: `php bin/php/ezchecktranslation.php ger-DE --allow-root-user` reports how complete one is,
and after a change to a `.ts` file clear `--clear-tag=i18n`. See
[Translations and languages](../features/6.0/translations-and-languages.md).

## 10.15 A checklist for the first week

1. The administrator password is changed, `var/log/initial-admin-password` is gone, the admin's e-mail is real.
2. `AdminEmail` and the mail transport are set; a password-change mail arrived in a real inbox.
3. The crontab runs `frequent`, the default part, `infrequent` and `cache_cleanup`; the logs grow.
4. `php bin/php/cache.php status --allow-root-user` ends in `PASS`.
5. A backup was taken **and restored** on another machine.
6. `var/log/error.log` holds nothing you cannot explain.
7. A benchmark baseline is saved in `var/benchmark/`.
8. Logs rotate (built-in limits in `config.php`, logrotate for cron logs).

## References

In this repository:

- [Operating a site](../guides/operating-a-site.md): caches, cronjobs, static cache, preload, backups, logs, repairs.
- [Getting started](../guides/getting-started.md), [Deploying](../guides/deploying.md),
  [Security and audit](../guides/security-and-audit.md), [Extensions](../guides/extensions.md),
  [Upgrading](../guides/upgrading.md), [the learning path](../guides/README.md).
- [Notifications: running them and fixing problems](../guides/notifications-administrator.md),
  [Notifications specification](../specifications/6.0/notifications.md),
  [E-mail preferences for administrators](../guides/mail-preferences-administrator.md),
  [cjw_newsletter specification](../specifications/6.0/cjw_newsletter.md).
- [Cronjobs console](../features/6.0/cronjobs-console.md),
  [Commands, cronjob parts and views as classes](../specifications/6.0/runnable-commands-cronjobs-views.md),
  [Content jobs](../features/6.0/content-jobs.md), [Velocity scheduler](../features/6.0/velocity-scheduler.md).
- [Cache console](../bc/6.0/cache-console.md), [Cache clears that move directories aside](../features/6.0/cache-clear-rename-aside.md),
  [HTTP cache](../bc/6.0/http-caching.md), [SQL query cache](../bc/6.0/sql-query-cache.md),
  [Velocity response cache](../features/6.0/velocity-response-cache.md),
  [Static cache defaults](../bc/6.0/static-cache-defaults.md), [Preload Sites view](../features/6.0/preload-sites-view.md).
- [Velocity engines and exp:velocity deploy](../bc/6.0/velocity-engines.md),
  [Velocity persistent-worker server](../features/6.0/velocity-persistent-worker-server.md),
  [Velocity engine settings](../specifications/6.0/velocity-engine-settings.md),
  [Velocity and the opcode cache](../features/6.0/velocity-opcode-cache-and-profile.md).
- [Benchmark](../features/6.0/benchmark.md), [Maintenance mode](../features/6.0/maintenance-mode.md),
  [Reset a user password](../features/6.0/reset-user-password.md), [Changing your password](../features/6.0/modern-password-change.md),
  [Audit trail](../features/6.0/audit-trail.md).
- [exp:ini](../features/6.0/exp-ini-command.md), [Extension loading order](../features/6.0/extension-loading-order.md),
  [Additional extension directories](../bc/6.0/AdditionalExtensionDirectories.md),
  [Per-site settings inside extensions](../features/6.0/multi-site-ini-overrides.md),
  [Translations and languages](../features/6.0/translations-and-languages.md).
- [Repairing an installation](../bc/6.0/repair.md), [Glossary](../glossary.md).
- The settings themselves: [`settings/site.ini`](../../settings/site.ini), [`settings/cronjob.ini`](../../settings/cronjob.ini),
  [`settings/image.ini`](../../settings/image.ini), [`settings/velocity.ini`](../../settings/velocity.ini),
  [`settings/querycache.ini`](../../settings/querycache.ini), [`.autoloadignore`](../../.autoloadignore),
  [`config.php-RECOMMENDED`](../../config.php-RECOMMENDED).

External:

- PHP: [OPcache configuration](https://www.php.net/manual/en/opcache.configuration.php) (including
  `opcache.file_update_protection` and `opcache.revalidate_freq`), [APCu](https://www.php.net/manual/en/book.apcu.php),
  [session garbage collection](https://www.php.net/manual/en/session.configuration.php).
- [crontab(5)](https://man7.org/linux/man-pages/man5/crontab.5.html), [logrotate(8)](https://man7.org/linux/man-pages/man8/logrotate.8.html).
- Mail: [SPF (RFC 7208)](https://www.rfc-editor.org/rfc/rfc7208), [DMARC (RFC 7489)](https://www.rfc-editor.org/rfc/rfc7489).
- Databases: the references of [chapter 9](09-databases.md#references).

[Previous: 9. Databases](09-databases.md) | [Next: 11. Upgrading](11-upgrading.md) | [Contents](README.md)
