---
title: Operations
audience: hosting, admin
category: hosting
order: 10
summary: The day-to-day running of hosted Exponential sites in one overview, with a link to each page on storage, clustering, backups, monitoring, logs, cron, mail, updates, deploys and maintenance mode.
keywords: operations, hosting, backups, restore, monitoring, logs, cron, cronjobs, mail, updates, upgrades, deploy, maintenance mode, storage, cluster
type: help
---
# Operations

This section is for the system administrators and support staff of a hosting company who keep Exponential sites
running after the day they go live. It answers four questions: what does an Exponential site keep on disk, how do you
back it up and get it back, how do you see that it is healthy, and how do you change it (cron, mail, updates,
deploys, maintenance windows) without surprising the customer.

Every page in this section names the commands, settings and paths it relies on and links the documentation in the
repository where they are described, so you can check each statement yourself. Start with
[Hosting companies: start here](../hosting-companies-start-here.md) if you are new to Exponential.

## Contents

1. [The pages of this section](#the-pages-of-this-section)
2. [The operations tools Exponential ships](#the-operations-tools-exponential-ships)
3. [Where to start](#where-to-start)
4. [Related pages](#related-pages)

## The pages of this section

### Storage and scaling

- [Storage layout](storage-layout.md): what lives under `var/`, what must be writable, what can be regenerated.
- [Clustering and shared storage](clustering-and-shared-storage.md): the DFS cluster file handler, its database
  table, the NFS mount and the commands that maintain it.
- [Several web nodes](several-web-nodes.md): what changes when more than one machine serves one site: database,
  files, sessions, caches, trusted proxies.

### Backups and restore

- [Backups: what to back up](backups-what-to-back-up.md): the database and the files, taken together, and the keys
  that must never be lost.
- [Backup schedules and retention](backup-schedules-and-retention.md): options for how often and how long, framed
  for a hosting offer.
- [Database backups per engine](database-backups-per-engine.md): the backup and restore commands for each of the six
  supported databases, in the official order.
- [Restore and restore tests](restore-and-restore-tests.md): the restore procedure and how to prove that a backup
  works.

### Monitoring and logs

- [Monitoring overview](monitoring-overview.md): the status commands, health checks and logs that tell you a site is
  well.
- [Request logs](request-logs.md): `404.log` and `security.log`, what goes into them and how they are kept.
- [Security log](security-log.md): the request classifier, `auth.log` and the audit trail as a host's view of attacks.
- [Log viewer and rotation](log-viewer-and-rotation.md): **Setup > Log files**, `exp:logs`, rotation, pruning and
  the disk budget.
- [The system card for hosts](system-card-for-hosts.md): `exp:expfetch` at login, its health hints, `--watch` and
  `exp:info --json` for monitoring and inventories.
- [Health checks and uptime](health-checks-and-uptime.md): what an external monitor should request and what a check
  inside the server should run.

### Scheduled work and mail

- [Cron jobs](cron-jobs.md): the cronjob parts, the crontab lines and who runs them.
- [The cronjob console](cronjob-console.md): **Setup > Cronjobs** for support staff and customers, pausing parts.
- [Mail delivery](mail-delivery.md): the sendmail, SMTP and file transports and what a host has to provide.
- [Mail consent and bounces](mail-consent-and-bounces.md): the mail gate, the suppression list and the bounce
  mailbox reader.

### Changing a site

- [Automated updates with ezupdate](automated-updates-with-ezupdate.md): the package manager in the administration,
  its switches and its command line.
- [Upgrading customer sites](upgrading-customer-sites.md): bringing an installation to a new Exponential release.
- [Deploying changes](deploying-changes.md): `exp:velocity deploy`, the cache order and configuration managed by the
  deployment.
- [Maintenance mode](maintenance-mode.md): taking a site offline for a window and bringing it back.

## The operations tools Exponential ships

Most of the work in this section is done with a handful of commands and administration pages. All of them are part of
the kernel (the commands live in `kernel/private/classes/commands/`).

| Task | Command line | Administration |
|---|---|---|
| Take a site offline | `php bin/php/maintenance.php on\|off\|status` | **Setup > Maintenance** |
| Read and rotate logs | `./console exp:logs` | **Setup > Log files** |
| Check the audit trail | `./console exp:audit status`, `exp:audit verify` | **Audit** tab |
| Run cronjobs | `php runcronjobs.php -s <site> <part>` | **Setup > Cronjobs** |
| Health and versions | `./console exp:systeminfo` | **Setup > System information** |
| Caches | `./console exp:cache status`, `php bin/php/ezcache.php` | **Setup > Caches** |
| Ship a code change | `./console exp:velocity deploy` | |
| Measure performance | `./console exp:benchmark` | |
| Mail preferences | `./console exp:mail:status` | **Setup > E-mail preferences** |

`./console` is the shortcut for `bin/php/console` in the installation root. Run every command as the site's own system
user; as `root` the scripts refuse to start unless `--allow-root-user` is added, and files they create then belong to
`root` ([chapter 11](../../../install/11-upgrading.md#111-before-you-start)).

> **Tip**
> `./console list` prints every command the installation has, with a one-line description. On a host with many
> customers, run it once per Exponential release you support and keep the output with your runbooks.

## Where to start

| You are | Read first |
|---|---|
| Building the backup service | [Backups: what to back up](backups-what-to-back-up.md), then [Database backups per engine](database-backups-per-engine.md) |
| Setting up monitoring | [Monitoring overview](monitoring-overview.md), then [Health checks and uptime](health-checks-and-uptime.md) |
| Answering support tickets | [Log viewer and rotation](log-viewer-and-rotation.md), [Cron jobs](cron-jobs.md), [Mail delivery](mail-delivery.md) |
| Planning upgrades for many customers | [Upgrading customer sites](upgrading-customer-sites.md), [Maintenance mode](maintenance-mode.md) |
| Designing a multi-server offer | [Several web nodes](several-web-nodes.md), [Clustering and shared storage](clustering-and-shared-storage.md) |

The installation book covers the same ground for a single site, in more depth: chapter 10,
[After installing](../../../install/10-after-installing.md), and chapter 12,
[Troubleshooting](../../../install/12-troubleshooting.md). The guide
[Operating a site](../../../guides/operating-a-site.md) is the short version for a site administrator.

## Related pages

- [Hosting companies: start here](../hosting-companies-start-here.md)
- [Hosting basics](../basics/README.md), [Security](../security/README.md), [Performance](../performance/README.md)
- [Checklists](../checklists/README.md) and [Runbooks](../runbooks/README.md)
- [Operating a site](../../../guides/operating-a-site.md) (guide)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
