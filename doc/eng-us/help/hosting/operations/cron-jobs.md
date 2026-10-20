---
title: Cron jobs
audience: hosting, admin
category: hosting
order: 140
summary: What Exponential's cronjob parts do, which ones a hosted site needs and how often, the crontab lines to install for each customer (as which user, with which PHP), what to do on hosts that limit cron, and how cron behaves during maintenance and on several nodes.
keywords: cron, cronjobs, crontab, runcronjobs.php, cronjob parts, frequent, infrequent, cache_cleanup, session_gc, PhpCliPath, exponential.cron, cron:frequent, shared hosting cron, scheduled tasks
type: help
---
# Cron jobs

This page is for system administrators who provision Exponential sites and for support staff who answer "why was
the notification never sent?". It answers: which scheduled jobs an Exponential site needs, what each part does, how
to install them in a customer's crontab, and what goes wrong when they do not run.

The complete guide is [Cronjobs: running, scheduling and following them](../../../guides/cronjobs.md); the
installation book's version is [10.3 Cronjobs](../../../install/10-after-installing.md#103-cronjobs). The administration
page **Setup > Cronjobs** is described on [The cronjob console](cronjob-console.md).

## Contents

1. [Parts and scripts](#parts-and-scripts)
2. [What a hosted site needs](#what-a-hosted-site-needs)
3. [A crontab for a customer site](#a-crontab-for-a-customer-site)
4. [Which user, which PHP](#which-user-which-php)
5. [Hosts that limit cron](#hosts-that-limit-cron)
6. [Cron during maintenance and on several nodes](#cron-during-maintenance-and-on-several-nodes)
7. [When jobs do not run](#when-jobs-do-not-run)
8. [Checklist](#checklist)

## Parts and scripts

A **script** is a PHP file in a cronjob directory (`cronjobs/`, or an extension's). A **part** is a named list of
scripts in `settings/cronjob.ini` (`[CronjobPart-<name>] Scripts[]`) that run together, in one process, on one schedule.
`php runcronjobs.php <part>` runs a part; without a part name it runs the scripts of `[CronjobSettings] Scripts[]`, the
default part (the administration page calls it **global**).

| Part | Scripts | What they do |
|---|---|---|
| default (global) | `unpublish.php`, `rssimport.php`, `indexcontent.php`, `hide.php`, `internal_drafts_cleanup.php` | scheduled unpublish and hide, RSS imports, delayed search indexing, old internal drafts |
| `frequent` | `notification.php`, `workflow.php`, `contentjobs.php`, `audit.php`, `mailbounces.php` | notifications, pending workflows, content jobs, the audit trail's sinks and alerts, the bounce mailbox |
| `infrequent` | `basket_cleanup.php`, `linkcheck.php`, `mailpreferences.php` | abandoned baskets, link checking, e-mail preference retention |
| `cache_cleanup` | `cachecleanup.php`, `httpcache_cleanup.php`, `querycache_cleanup.php` | caches that can never be served again |
| `logs` | `explogrotate.php` | rotate, prune and compress logs (in no default part) |
| `cleanuprss` | `cleanuprss.php` | trims RSS-imported content; does nothing until configured |
| `cluster_maintenance` | `clusterpurge.php` | purges expired cluster files |
| `unlock` | `unlock.php` | releases objects stuck in a locked state |

Single parts also exist for `contentjobs`, `audit`, `notification`, `mailpreferences`, `mailbounces` and
`contentcheck`. Extensions add their own parts (sitemaps, newsletters, Oracle maintenance); `./console list cron` shows
every part with a description. `php runcronjobs.php --list` lists every part and its scripts.

## What a hosted site needs

| Part | Needed when | Schedule the sources suggest |
|---|---|---|
| `frequent` | always: notifications, workflows, content jobs and the audit trail depend on it | every few minutes (`*/5 * * * *` in the examples) |
| default (global) | always: scheduled publishing and delayed indexing | `*/15 * * * *` in the examples |
| `infrequent` | always: link check, e-mail preference retention | hourly or daily |
| `cache_cleanup` | busy sites, to keep cache directories from growing | once a night |
| `session_gc.php` | where the operating system's PHP session cleanup does not run | once a night, with `--script=` |
| `logs` | when the kernel, not logrotate, manages logs | once a night |

The audit trail's alert rules, rotation and archiving run inside `frequent`; "an installation running only the frequent
group needs no extra crontab line" for them (`settings/cronjob.ini`). Without `frequent`, notifications are not sent and
the audit's alerts never fire.

## A crontab for a customer site

From [10.3, A crontab](../../../install/10-after-installing.md#a-crontab), in the site user's crontab:

```text
# m   h  dom mon dow  command
*/5   *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site frequent >> var/log/cron-frequent.log 2>&1
*/15  *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site >> var/log/cron-default.log 2>&1
17    *  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site infrequent >> var/log/cron-infrequent.log 2>&1
40    3  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site cache_cleanup >> var/log/cron-cleanup.log 2>&1
50    3  *   *   *    cd /path/to/installation && php runcronjobs.php -q -s site --script=session_gc.php >> var/log/cron-cleanup.log 2>&1
```

- `-s site` names the siteaccess the jobs run in; indexing, sitemaps and notifications act on the content and URLs of
  that site. Use the customer's own public siteaccess.
- `-q` prints nothing but errors; the redirection keeps what is printed. Rotate `cron-*.log` with the system's
  logrotate, since the kernel does not write them.
- The repository ships an example file, `exponential.cron` (the old name `ezpublish.cron` is a kept copy), with a
  variable for the installation root and the PHP binary ([ExponentialSDK and exponential.cron](../../../bc/6.0/exponentialsdk-and-exponential-cron.md)).
- **Setup > Cronjobs** proposes a crontab line for every part nothing runs yet, written with the installation's root
  and PHP binary; the schedule comes from `cronjob.ini [AdminSettings] CrontabSchedule_<part>`
  ([Cronjobs, section 12](../../../guides/cronjobs.md#12-scheduling-with-the-crontab)).

## Which user, which PHP

- **The site's user.** Install the lines in the crontab of the user the web server runs the site as
  (`crontab -e -u <site user>`, or `./console crontab:edit` as that user), "so the files the jobs write belong to it".
  As `root`, scripts refuse to start without `--allow-root-user`, and files they create belong to root afterwards.
- **The same PHP as the site.** "Use the same PHP binary the web server uses (`/opt/.../php` on hosts with several PHP
  versions); `php -v` in the cron environment may differ from your shell's." Write the full path of the binary in the
  line. For jobs started from the administration page, set `cronjob.ini [AdminSettings] PhpCliPath` to the same binary.
- **Locks.** Each script takes a lock while it runs; a second run of the same script prints `Cronjob part locked by
  other process` and skips it. A lock older than `[CronjobSettings] MaxScriptExecutionTime` (shipped `43200` seconds)
  is taken over.

## Hosts that limit cron

Some hosting plans allow few cron entries or a minimum interval. Options:

- **One line for `frequent`** is the minimum for notifications and the audit; add the default part to the same
  schedule if only one line is allowed per interval (two commands joined with `&&`).
- **A panel's cron feature** works the same way, as long as it runs the command in the installation root as the
  site's user; see [Cron and shell access](../panels/cron-and-shell-access.md).
- **The Velocity scheduler** is not a replacement: "It does not run an arbitrary command-line PHP script; for that,
  keep using the system's cron" ([Velocity scheduler](../../../features/6.0/velocity-scheduler.md)).

## Cron during maintenance and on several nodes

- **Maintenance mode does not stop cron.** "Cronjobs run in a shell and are not affected." Pause the parts that must
  not run during a window (imports, index rebuilds) on **Setup > Cronjobs**, or remove the lines for the window
  ([Maintenance mode guide](../../../guides/maintenance-mode.md#10-installations-cronjobs-and-deploys)).
- **Several nodes: one crontab.** Run the jobs on one node only; script locks are local to a node. A shared
  `[PauseSettings] Directory` makes a pause hold for every node ([Several web nodes](several-web-nodes.md)).

## When jobs do not run

| Symptom | Look at |
|---|---|
| Notifications are not sent | is `frequent` in the crontab? `./console exp:notificationstatus` |
| Scheduled unpublishing did not happen | the default part, and the `-s` siteaccess |
| A job runs but fails | its log (`cron-*.log`) and `var/log/error.log` |
| "Cronjob part locked by other process" every time | a previous run still running or killed; see the lock rules above |
| A part is skipped with "is paused" | it was paused on **Setup > Cronjobs**; skipped runs are logged in `cronjob_skipped.log` |
| Jobs write files the site cannot replace | the crontab runs as root; move it to the site user |

The troubleshooting section of the guide covers more cases: [Cronjobs, section 17](../../../guides/cronjobs.md#17-troubleshooting).

## Checklist

- [ ] `frequent`, the default part and `infrequent` are scheduled for every site.
- [ ] The crontab belongs to the site's user and names the PHP binary the site runs on.
- [ ] `-s` names the customer's public siteaccess.
- [ ] Output goes to log files that logrotate rotates, or to `/dev/null` deliberately.
- [ ] On several nodes, only one node runs the crontab.
- [ ] Runbooks for maintenance windows say which parts to pause.

**Cron on a public demo.** A demo in demo mode needs `EXP_DEMO_MODE=1` in its crontab too, or its cronjobs run with demo mode off. Each cronjob process then logs `demo/mode ... sapi=cli` once in `security.log`. [`crontab-demo`](../../../examples/demo-mode/crontab-demo), [Demo mode for hosts](../security/demo-mode-for-hosts.md).

## Related pages

- [The cronjob console](cronjob-console.md)
- [Cron and shell access](../panels/cron-and-shell-access.md)
- [Maintenance mode](maintenance-mode.md)
- [Mail consent and bounces](mail-consent-and-bounces.md)
- [New customer site go-live](../checklists/new-customer-site-go-live.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
