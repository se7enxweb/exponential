---
title: Security updates and disclosure
audience: hosting, admin
category: hosting
order: 100
summary: Which Exponential versions receive security fixes, how fixes are published, how a host follows releases and rolls updates out to customer sites, how to check that files were not changed, what else needs updates, and how to report a vulnerability privately.
keywords: security updates, supported versions, SECURITY.md, releases, immutable tags, changelog, composer outdated, composer audit, upgrade check, filelist.md5, checkmanifest.php, ezupdate, vulnerability, disclosure, security@se7enx.com, advisory, patch management
type: help
---
# Security updates and disclosure

This page is for the security lead and the system administrators of a host who keep many Exponential sites up to date,
and for support staff who receive a report about a possible vulnerability. It says which versions are supported, how a
fix reaches a site, and how to report a problem. The policy itself is the repository's
[SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md); the update routine is
[13.15 Keeping up to date](../../../install/13-security-hardening.md#1315-keeping-up-to-date).

## Contents

1. [Supported versions](#supported-versions)
2. [How fixes are published](#how-fixes-are-published)
3. [Following releases](#following-releases)
4. [Rolling updates out](#rolling-updates-out)
5. [Checking files after an update](#checking-files-after-an-update)
6. [Everything else that needs updates](#everything-else-that-needs-updates)
7. [Reporting a vulnerability](#reporting-a-vulnerability)
8. [Telling customers](#telling-customers)
9. [Checklist](#checklist)

## Supported versions

From `SECURITY.md`:

| Version | Supported |
|---|---|
| 6.0.x, current line: `main` (`dev-main`) and the next tag cut from it | yes |
| 6.0.x, released tags `v6.0.0` to `v6.0.14` | fixed by upgrading to the newest 6.0.x |
| 5.x, 4.x, 3.x and older | no |

Security fixes are made on `main` and published with the next 6.0.x release. Sites on older lines, including those
that still run the product its creator discontinued after almost 18 years, get no fixes; plan their migration
([Migrating customers: overview](../migrations/README.md)).

## How fixes are published

**A published tag is never changed.** A fix for a problem in an earlier tag reaches you as the next release, not as a
corrected copy of the old one. Releases are immutable tags on Packagist and GitHub, with changelogs. For a host this
means:

- the version number a site reports is reliable: the same version is the same code everywhere;
- "is this site patched?" is answered by comparing its version with the release that fixed the problem;
- every extension is its own Composer package with its own releases; follow those too.

## Following releases

```bash
composer outdated                 # what is newer than the locked versions
composer audit                    # known vulnerabilities in the locked dependencies
composer show se7enxweb/exponential   # the version a site runs
```

Run these per installation from a central job and collect the results, or use the **Setup > Updates and packages**
screen of the ezupdate extension, whose check for updates changes nothing
([ezupdate](../../../features/6.0/extensions/ezupdate.md),
[Automated updates with ezupdate](../operations/automated-updates-with-ezupdate.md)). Keep `composer.lock` in each
site's version control so the result is the same on every machine.

## Rolling updates out

1. **Read the changelog and the upgrade notes** of the release ([Upgrading](../../../guides/upgrading.md),
   [Chapter 11](../../../install/11-upgrading.md)).
2. **Update a copy first** (a staging copy of the customer's site, or a representative test site).
3. **Then the live site**: `composer update <package>`, the database updates the release names, then
   `php bin/php/console exp:velocity deploy` (or autoloads, cache clear and a PHP-FPM reload by hand).
4. **Check file consistency** (below).
5. **Smoke test** the front page and the administration sign-in.

For many sites at once: [Upgrading customer sites](../operations/upgrading-customer-sites.md) and
[Deploying changes](../operations/deploying-changes.md). Use [maintenance mode](../operations/maintenance-mode.md)
where the update needs database changes.

> **Note**
> Whether security updates are applied by the host automatically, within a fixed window, or only on the customer's
> approval is a decision for each plan. The sources set no time frame; the host chooses one and states it in its
> service description ([SLA options](../business/sla-options.md)).

## Checking files after an update

**Setup > Upgrade check** compares every file with the release's `share/filelist.md5` ("Check file consistency") and the
schema with the release's ("Check database consistency"). Both only read. The same from a shell, without a database:

```bash
php bin/php/checkmanifest.php --all                 # exit 0 when nothing is modified or missing
php bin/php/checkmanifest.php --all --extensions    # also extensions with their own manifest
```

A modified kernel file nobody changed deserves a look; see [Compromised site](../runbooks/compromised-site.md)
([The upgrade check](../../../guides/upgrade-check.md)).

## Everything else that needs updates

- **PHP**, the **database server** and the **operating system**.
- **Velocity's engine** (its releases fix security issues too), then `exp:velocity deploy`.
- **FrankenPHP's binary**: version and checksums are pinned in `[FrankenPHPSettings] Version` and `Sha256[]`; change
  them together.
- **The customer's own extensions**: review them for the patterns fixed in the kernel (SQL built from request data,
  shell commands without `escapeshellarg()`, output without escaping), and regenerate extensions made by the RAD
  wizards before their fix ([RAD tools: security](../../../bc/6.0/rad-security.md),
  [6.0.13 hardening notes](../../../bc/6.0/hardening.md)).
- **request-shield**, when installed, as its own package.

## Reporting a vulnerability

Report security issues **privately**, never in a public issue tracker or discussion:

- by mail to **security@se7enx.com**, or
- as a private advisory on GitHub: <https://github.com/se7enxweb/exponential/security/advisories/new>.

`SECURITY.md` says a first answer can be expected within one business day. Include:

- the version (`composer show se7enxweb/exponential`, or the tag or commit) and the PHP version;
- the engine that serves the site (Velocity, FrankenPHP, Apache or nginx with PHP-FPM) and the database;
- what an attacker can do, and the steps to reproduce it.

Leave out passwords, secrets and personal data, including those of the customer whose site showed the problem. The
reporter is told whether the report is accepted and kept informed while a fix is prepared; an accepted issue is fixed
on `main` and released with the next 6.0.x version. Problems in a third-party extension go to that extension's
maintainers; the help's [report page](../../when-something-goes-wrong.md#security-problems) explains the trackers.

## Telling customers

When a release fixes a security problem, a host may choose to:

- inform affected customers which sites were updated and when;
- for sites that cannot be updated at once, document the gap with a date to close it, as the
  [Security review checklist](../checklists/security-review.md) asks;
- never publish details of an unfixed problem before the project has released the fix.

## Checklist

- [ ] Every site on the newest 6.0.x, or the gap documented with a date.
- [ ] `composer outdated` and `composer audit` run regularly for every installation; `composer.lock` in version control.
- [ ] Updates tested on a copy first; file consistency checked after every update.
- [ ] PHP, database server, operating system, Velocity's engine and extensions included in the routine.
- [ ] A named owner who reads release notes and receives security mail.
- [ ] Support staff know the private reporting route and never use a public tracker for security reports.

## Related pages

- [Security for hosts: overview](README.md)
- [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)
- [Security hardening: Reporting a vulnerability](../../security-hardening.md#reporting-a-vulnerability)
- [Working with the project and 7x](../business/working-with-the-project-and-7x.md)
- [Upgrading customer sites](../operations/upgrading-customer-sites.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
