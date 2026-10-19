---
title: System information and health checks
audience: government, admin, hosting
category: public-sector
order: 100
summary: What Setup > System information tells an in-house team - which server answered, the health checks with what to do, the overview cards, the support report with secrets left out - and how to run the same checks from the shell in monitoring.
keywords: system information, health checks, Setup System information, exp:system:info, support report, PHP version, OPcache, disk space, debug output, cronjobs, mail, database, monitoring
type: help
---
# System information and health checks

This page is for administrators and IT teams who need a quick, reliable picture of an installation's health, and for
anyone preparing a support request. **Setup > System information** (`/setup/info`, needs `setup/system_info`) reads
and changes nothing. Reference: [System information](../../../../guides/system-information.md).

![System information with the health checks: failures, warnings, notes and checks in order](../../../images/government/gov-health.webp)

## Contents
1. [Which server answered](#which-server-answered)
2. [The health checks](#the-health-checks)
3. [The overview and the report](#the-overview-and-the-report)
4. [From the shell and in monitoring](#from-the-shell-and-in-monitoring)
5. [Related pages](#related-pages)

## Which server answered

The page first names the server of this request: Apache with PHP-FPM (and its pool), Exponential Velocity, FrankenPHP
or PHP's built-in server. Each has its own PHP, OPcache and settings, so open the page through each engine you run.

## The health checks

Failures and warnings come first, each with what to do. A selection, from the guide:

| Check | Failure or warning when |
|---|---|
| PHP version | below the minimum (failure); a branch out of security support (warning) |
| PHP extensions | one the kernel or the database driver needs is missing (failure) |
| OPcache | off, full, or hit rate below 90 % on Apache and FPM |
| memory_limit | below 128M (failure), below 256M (warning) |
| var directory | `var/`, cache or storage not writable (failure) |
| Free disk | under 1 GB or 5 % (failure), under 5 GB or 10 % (warning) |
| Debug output | on for every visitor (failure) |
| display_errors | PHP shows errors in the page |
| Caches | view caching or template compilation off |
| Cronjobs | no run found, or the last one more than two days ago |
| Mail | `Transport=file` (note: nothing is sent); SMTP without a server (failure) |
| Database | no connection (failure); not UTF-8 (warning) |
| Time zone | none set |

For a public website, a red **Debug output** or **display_errors** is a security finding (internal details shown to
the public); fix it before anything else.

## The overview and the report

Overview cards: Exponential (version, the database's installed version, siteaccesses, extensions), Server, PHP,
OPcache and APCu, Database, Storage, Caches, Cronjobs, Mail, Locale and time, Machine. Further down: active extensions,
PHP settings, site addresses, siteaccesses, the role-aware HTTP cache, database queries and connection.

**Download report (.txt)**, **Download as JSON** and **Copy report as text** give the same report for a support
request or a change record. Passwords, keys, tokens, session ids, credentials in addresses and full server paths are
never in it ([System information, section 7](../../../../guides/system-information.md#7-what-is-never-shown)). Still,
the page says a lot about the installation: give `setup/system_info` to administrators only.

## From the shell and in monitoring

```bash
./console exp:system:info --allow-root-user            # the text report
./console exp:system:info --json --allow-root-user     # JSON
./console exp:system:info --sizes --allow-root-user    # with directory and database sizes
```

It reports the command-line PHP (OPcache usually off there; not a warning) and exits 1 when a check fails, so a
monitoring agent or a deploy script can use it. Run the page in the browser too, for the web server's PHP.

## Related pages
- [Read system information for a support request](../../tasks/operations/read-system-information-for-a-support-request.md)
- [Monitoring overview](monitoring-overview.md)
- [Hardening the host](../hosting/hardening-the-host.md)
- [System information guide](../../../../guides/system-information.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
