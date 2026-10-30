---
title: Go-live checklist for a customer site
audience: hosting, admin
category: hosting
order: 30
summary: The checklist a host works through before a customer's Exponential site goes public: administrator password and accounts, settings, caches, HTTPS and headers, cron, mail, search, backups with a restore test, monitoring, logs, and the handover to the customer.
keywords: go-live, launch, checklist, customer site, production, administrator password, debug off, caches, HTTPS, CSP, cron, mail, search index, backup, restore test, monitoring, handover, DNS
featured: true
type: help
---
# Go-live checklist for a customer site

This checklist is for the host's system administrator and the customer's site owner, together, in the days before a
site goes public on your platform. It assumes the server passed the [New server checklist](new-server.md).

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../panels/plesk.md), [cPanel](../panels/cpanel.md), [DirectAdmin](../panels/directadmin.md), [ISPConfig](../panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

```text
Site: <domain>        Customer: <customer>        Go-live: <date>        Checked by: <names>
```

## Accounts and passwords

- [ ] The administrator password set at installation is not a default one and is known only to the people who need
      it ([The default administrator password](../../default-administrator-password.md),
      [Change the admin password](../../change-the-admin-password.md)).
- [ ] Test accounts created during development are removed or disabled.
- [ ] Each editor has a personal account; nobody shares the administrator account.
- [ ] Two-factor sign-in decided for administrators ([Two-factor sign-in](../security/two-factor-sign-in.md)).

## Settings

- [ ] Debug output is off for the public siteaccesses (`[DebugSettings] DebugOutput=disabled` in the site's settings).
- [ ] The site addresses and siteaccess matching name the production domains.
- [ ] Mail transport and sender are the production ones ([Mail delivery](../operations/mail-delivery.md)).
- [ ] The installation name shown in pages is the production one, where used.
- [ ] `settings/override` is readable only by the site's system user and its engine or PHP-FPM pool.

## Caches and performance

- [ ] The view cache and template cache are on for the public siteaccesses ([Caching layers](../performance/caching-layers.md)).
- [ ] With Velocity: the response cache is on and cleared after the last deploy
      (`php bin/php/console exp:velocity cache clear --apply --allow-root-user`).
- [ ] The front page and the main section pages answer from a warm cache within your plan's target; measured with
      the benchmark command ([Benchmarking a hosted site](../performance/benchmarking-a-hosted-site.md)).

## HTTPS and headers

- [ ] The certificate covers every production domain and renews automatically ([TLS and HTTPS](../security/tls-and-https.md)).
- [ ] HTTP redirects to HTTPS.
- [ ] The Content Security Policy produces no reports on the main pages ([Content Security Policy](../security/content-security-policy.md)).
- [ ] The administration is restricted as agreed ([Restricting the administration](../security/restricting-the-administration.md)).

## Cron, search, mail

- [ ] The site's crontab runs `runcronjobs.php` with its parts ([Cron jobs](../operations/cron-jobs.md)); the
      cronjob console shows the last runs ([Cronjob console](../operations/cronjob-console.md)).
- [ ] The search index is complete (`php bin/php/updatesearchindex.php` has run after the last import)
      ([Search backends](../basics/search-backends.md)).
- [ ] A test mail from the site (for example a password reset for a test account) arrives and is not marked as spam.

## Backups and restore

- [ ] The backup job for this site runs: database plus `var/storage`, `settings/override`, `settings/siteaccess`
      ([What to back up](../operations/backups-what-to-back-up.md)).
- [ ] One backup of this site has been **restored** on another machine and the restored site opens
      ([Restore and restore tests](../operations/restore-and-restore-tests.md)).

## Monitoring and logs

- [ ] An external check of a page that touches the database ([Health checks and uptime](../operations/health-checks-and-uptime.md)).
- [ ] The log viewer shows no unexplained errors in `error.log` from the last test days
      ([Log viewer and rotation](../operations/log-viewer-and-rotation.md)).
- [ ] `security.log` is watched ([Security log](../operations/security-log.md)).

## DNS and cut-over

- [ ] DNS time-to-live lowered in advance; the new records ready.
- [ ] The site tested on the production machine through a hosts-file entry before DNS changes.
- [ ] Rollback agreed: how, until when ([Migrating customers to your hosting](../migrations/migrating-customers-to-your-hosting.md#rollback)).

## Handover

- [ ] The customer knows how to report a problem and what to include (page, time, error reference)
      ([Support tiers](../business/support-tiers.md)).
- [ ] The customer's editors know where the help is: the help dashboard of their administration.
- [ ] The SLA and the backup retention are confirmed in writing.
- [ ] The site's record is complete: versions, database engine, engine, extensions, contacts.

**Demo mode.**

- [ ] Demo mode off: `EXP_DEMO_MODE` is set for none of the site's processes (pool, virtual host, `.htaccess`, unit, crontab); no **Demo mode** banner in the administration; no `demo/mode` line in `var/log/security.log` ([Demo mode for hosts](../security/demo-mode-for-hosts.md)).

## Related pages

- [New server checklist](new-server.md)
- [Security review checklist](security-review.md)
- [Chapter 10: After installing](../../../install/10-after-installing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
