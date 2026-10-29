---
title: Talking points for administrators
audience: advocate
category: advocacy
order: 13
summary: Talking points for system administrators and hosting people, about installation, databases, operating systems, engines, hardening, logs, the audit trail, upgrades and the upgrade check, each with its source and a command or screen to show.
keywords: talking points, administrators, sysadmin, hosting, install, SQLite, PostgreSQL, databases, operating systems, Velocity, FrankenPHP, PHP-FPM, hardening, logs, audit trail, upgrade check, backups
type: help
---
# Talking points for administrators

This page is for advocates who talk to the people who install, run and secure sites: system administrators, hosting
staff, DevOps people. They want to know what it needs, what can go wrong and how they will see it.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../examples/web-servers/nginx.md), [LiteSpeed](../../examples/web-servers/litespeed.md),
> [Caddy](../../examples/web-servers/caddy.md), [IIS](../../examples/web-servers/iis.md), [Velocity](../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../hosting/panels/plesk.md), [cPanel](../hosting/panels/cpanel.md), [DirectAdmin](../hosting/panels/directadmin.md), [ISPConfig](../hosting/panels/ispconfig.md).

## Contents

1. [What administrators want to know](#what-administrators-want-to-know)
2. [The points](#the-points)
3. [Commands and screens to show](#commands-and-screens-to-show)
4. [Questions administrators ask](#questions-administrators-ask)
5. [Do not say](#do-not-say)

## What administrators want to know

- "What does it need, and can it run on what we have?"
- "How do I install, upgrade and back it up?"
- "How do I harden it and see attacks?"
- "What do I do at 3 a.m. when it breaks?"

## The points

| Say | Source |
|---|---|
| "One command installs a complete site on SQLite, with no database server." | [Installing Exponential in one command](../../features/6.0/install-in-one-command.md) |
| "Six databases: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle and MySQL, in that official order." | [Database support](../database-support.md#the-supported-databases-in-order) |
| "PHP 8.0 to 8.5 are supported, so the stock PHP of Red Hat Enterprise Linux 9 runs it; PHP 7.4 is usable without support for hosts that cannot leave it yet." | [PHP versions](../who-is-the-exponential-foundation.md#php-versions) |
| "Operating systems in four documented tiers, from tested to theoretical." | [Operating system support](../os-support.md#the-four-tiers) |
| "Velocity is the recommended engine; FrankenPHP is the production-ready alternative; Apache or nginx with PHP-FPM also serve it." | [Velocity](../../features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it), [Web servers](../os-support.md#web-servers) |
| "A threat-by-threat hardening guide with a checklist." | [Security hardening](../security-hardening.md#checklist) |
| "A new installation generates a random administrator password when none or a well-known one is given." | [The default administrator password](../default-administrator-password.md) |
| "Probes and attacks are classified into a security log, with masked addresses." | [Request logs](../../features/6.0/request-logs.md) |
| "A tamper-evident audit trail, on by default." | [The audit trail](../../features/6.0/audit-trail.md) |
| "A log viewer in the administration, and error references you can look up." | [Log viewer](../../features/6.0/log-viewer.md), [Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md) |
| "**Setup > Upgrade check** compares the files with the release manifest and the database with the release schema." | [The upgrade check](../../guides/upgrade-check.md) |
| "Big removals and moves run as background jobs, so they do not time out." | [Content jobs](../../features/6.0/content-jobs.md) |
| "Standard dumps per database, and documented ways to move a site between databases." | [Backup and restore](../database-support.md#backup-and-restore), [Moving a site](../database-support.md#moving-a-site-to-another-database) |

## Commands and screens to show

```bash
./console exp:install --dry-run         # what an install would do, nothing written
./console exp:velocity status           # every engine, its state and URL
./console exp:audit status              # the audit trail on the command line
```

Screens: **Setup > System information** (the database, PHP and engine), **Setup > Log files**, **Setup > Upgrade
check**, the **Audit** tab.

Check the command names on the installation you use; `--help` on each command lists its options
([Velocity](../../features/6.0/velocity-persistent-worker-server.md#start-check-stop),
[The audit trail, From the command line](../../features/6.0/audit-trail.md#from-the-command-line)).

## Questions administrators ask

| Question | Answer | Source |
|---|---|---|
| "Does it run on shared hosting?" | Apache or LiteSpeed with `.htaccess` can serve it; without shell access the setup wizard installs it. Velocity usually cannot run there. | [Containers, cloud and hosting panels](../os-support.md#containers-cloud-and-hosting-panels) |
| "Which database should we use?" | SQLite 3 is the default; PostgreSQL is the recommended server | [Database support](../database-support.md) |
| "How are security fixes delivered?" | On `main`, published with the next 6.0.x release | [Security fixes](../who-is-the-exponential-foundation.md#security-fixes) |
| "Can we run cron as root?" | The hardening guide covers cron and command-line scripts as root | [Security hardening](../security-hardening.md#cron-and-command-line-scripts-as-root) |
| "How do we upgrade safely?" | Back up first, never run an installer over an existing site; update files per database | [Upgrading](../../install/11-upgrading.md) |
| "Can a WAF sit in front?" | request-shield of CJW Network is documented as a companion | [A shield in front](../security-hardening.md#a-shield-in-front-request-shield) |

## Do not say

- "It is secure." Say what the process is and how they verify it ([Talking points: security](talking-points-security.md)).
- "It runs on any hosting." Name the tiers and the limits of shared hosting.
- A response time or uptime figure: none is documented ([Claims you must not make](claims-you-must-not-make.md)).

## Related pages

- [Talking points: hosting, databases and freedom](talking-points-hosting-databases-and-freedom.md)
- [Talking points: security](talking-points-security.md)
- [Advocating for security](advocating-for-security.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
