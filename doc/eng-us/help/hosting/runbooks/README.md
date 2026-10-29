---
title: Runbooks for hosts: overview
audience: hosting
category: hosting
order: 10
summary: Step-by-step runbooks for the incidents a host meets with Exponential sites: site down, slow site, disk full, restore from backup, compromised site, PHP upgrade, certificate expiry and Velocity that will not start.
keywords: runbook, runbooks, incident, outage, site down, slow, disk full, restore, compromised, hacked, PHP upgrade, certificate, Velocity, on-call
type: help
---
# Runbooks for hosts: overview

A runbook is the page on-call staff open when something is wrong. Each one starts with the symptoms, then the first
minutes, then diagnosis and fix, and ends with what to do afterwards. The commands are those of the installation
book's troubleshooting chapter ([Chapter 12](../../../install/12-troubleshooting.md)), arranged for an incident.

## Pages in this section

| Runbook | When |
|---|---|
| [Site down](site-down.md) | pages return errors, or nothing at all |
| [Slow site](slow-site.md) | pages load, but slowly |
| [Disk full](disk-full.md) | writes fail, uploads fail, logs stop |
| [Restore from backup](restore-from-backup.md) | data was lost or damaged and must come back from a backup |
| [Compromised site](compromised-site.md) | you suspect an attacker changed something |
| [PHP upgrade](php-upgrade.md) | a planned move to a newer PHP version |
| [Certificate expiry](certificate-expiry.md) | HTTPS warnings, or a certificate about to expire |
| [Velocity will not start](velocity-will-not-start.md) | the Velocity engine does not come up |
| [Demo mode problems](demo-mode-problems.md) | demo mode does not show, a demo build fails with a generated password, or demo mode shows on a real site |

## Rules for every incident

1. **Write down the time** you started and every step you take. The log viewer and the audit trail use exact times.
2. **Read before you change.** The first look is always `var/log/error.log` and the engine's or web server's log
   ([12.2 Where the logs are](../../../install/12-troubleshooting.md#122-where-the-logs-are)).
3. **Change one thing at a time**, and clear the smallest cache that covers it.
4. **Run commands as the installation's owner**, not as root; a script run as root leaves files the site cannot
   change ([12.3](../../../install/12-troubleshooting.md#123-permissions-and-ownership)).
5. **Tell the customer** early, with what you know and when you will update them.

## The commands you will use most

```bash
./console exp:logs --tail=var/log/error.log --lines=50     # the last 50 errors
./console exp:logs --reference=ERR-0123456789               # one error page's details
php bin/php/console exp:velocity status --allow-root-user   # is Velocity running and answering?
php bin/php/maintenance.php status --allow-root-user        # is maintenance mode on?
php bin/php/ezcache.php --clear-all --allow-root-user       # when unsure which cache
php bin/php/console exp:velocity cache clear --apply --allow-root-user  # Velocity's response cache, after a restart
```

## Related pages

- [Checklists for hosts](../checklists/README.md)
- [Monitoring overview](../operations/monitoring-overview.md)
- [When something goes wrong](../../when-something-goes-wrong.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
