---
title: The system card for hosts
audience: hosting, admin
category: hosting
order: 135
summary: How system administrators and support staff use ./console exp:expfetch on a hosted server - the health hints at login, --watch for load, memory and Velocity workers, and exp:info --json for monitoring, inventories and comparing servers. Read-only, fast and free of secrets.
keywords: exp:expfetch, exp:info, system card, health hints, --watch, --json, monitoring, inventory, Velocity workers, load, memory, disk, PHP end of life, OPcache, hosting, support
type: help
---
# The system card for hosts

This page is for system administrators and support staff who look after Exponential servers. The system card
(`./console exp:expfetch`) shows on one screen what runs on a machine and what needs attention. The full reference is
[The system card](../../../features/6.0/system-card.md).

## At login: read the hints

```bash
cd /var/www/example.org && ./console exp:expfetch --allow-root-user
```

The card ends with a **Hints** block, worst first. The ones a host acts on most:

| Hint | What to do |
|---|---|
| a disk is 90% full (error at 97%) | clear old caches and logs (`./console exp:cache`, `./console exp:logs`), or add space before uploads fail |
| memory 90% used, load above twice the cores | look at what runs (`top`) before a deploy or reindex; lower Velocity's workers |
| PHP loses security support soon, or has lost it | plan the PHP upgrade ([OS and PHP support](../../os-support.md)) |
| OPcache off, recommended extension missing, `memory_limit` low | fix php.ini (the card shows the command-line PHP; check the web PHP on Setup > System information) |
| the database does not answer | check the database server and `site.ini [DatabaseSettings]` |
| demo mode on, debug output for everyone | right for a demo server, wrong for a customer site |
| no cronjob for two days | check the crontab and the cronjob logs ([Cron jobs](cron-jobs.md)) |
| Composer packages disagree | see Setup > Updates before the next update ([Automated updates](automated-updates-with-ezupdate.md)) |

Add `--allow-root-user` when you work as root, as with every console command.

## Live: --watch

```bash
./console exp:expfetch --watch=2 --section=system,engine
```

The card redraws in place every two seconds like `top`: load, memory, disk and the live Velocity workers refresh,
the rest stays. Ctrl-C stops it. Useful while a deploy, an import or a load test runs.

## Monitoring and inventories: --json

```bash
./console exp:info --json --no-logo > /var/backups/inventory/$(hostname)-$(date +%F).json
./console exp:expfetch --json | jq '[.hints[] | select(.severity=="error")] | length'
```

The JSON has every fact with a stable key and a severity, the hints, and how long each provider took. A monitoring
check can alert on the number of `error` hints; a nightly `exp:info --json` gives an inventory per server that diffs
well between two days or two machines. A slow part (a database that hangs) shows `n/a (timeout)` instead of hanging
the check: each provider has a time budget.

## Safe to paste

The card never prints a password, key, token, database user or full database address, nor the values of Velocity's
envvars. You can attach it to a ticket as it is. See [No secrets, ever](../../../features/6.0/system-card.md#no-secrets-ever).

## Related pages

- [The system card](../../../features/6.0/system-card.md)
- [Monitoring overview](monitoring-overview.md)
- [Health checks and uptime](health-checks-and-uptime.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
