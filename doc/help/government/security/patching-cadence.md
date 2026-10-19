---
title: Patching cadence
audience: government, admin, project-manager, hosting
category: public-sector
order: 270
summary: For public bodies that must keep systems patched - how Exponential releases and supports versions, what to watch (kernel, extensions, Composer dependencies, PHP, database, engine), a practical update routine with test copy, composer audit and file consistency check, and records to keep.
keywords: patching, updates, upgrades, release cadence, supported versions, SECURITY.md, composer outdated, composer audit, Packagist, changelog, test copy, exp:velocity deploy, file consistency, vulnerability management, public sector
type: help
---
# Patching cadence

This page is for administrators, hosting teams and project managers responsible for keeping a public site up to date.
Vulnerability management usually requires known vulnerabilities to be patched within set times. Exponential and its
extensions are Composer packages with immutable releases; the routine below follows
[13.15 Keeping up to date](../../../install/13-security-hardening.md#1315-keeping-up-to-date).

## Contents

1. [What to watch](#what-to-watch)
2. [How Exponential releases](#how-exponential-releases)
3. [A routine](#a-routine)
4. [Records to keep](#records-to-keep)
5. [Related pages](#related-pages)

## What to watch

| Component | How to see what is new |
|---|---|
| Exponential and its extensions | `composer outdated`; the changelogs (`doc/changelogs/`); GitHub releases |
| Composer dependencies | `composer audit` lists known vulnerabilities in the locked dependencies |
| PHP, database server, operating system | your distribution's or vendor's security notices |
| Velocity's engine and FrankenPHP | engine releases; FrankenPHP's version and checksums are pinned in `velocity.ini [FrankenPHPSettings]` |
| request-shield (if used) | its releases |

The project's CI does not run a dependency vulnerability scan ([Security standards: Known gaps](../../../standards/security.md#known-gaps)),
so `composer audit` is your job.

## How Exponential releases

- 6.0 is the maintained line; fixes go to `main` and out with the next 6.0.x release
  ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
- Release tags are never changed; a fix is always a new version.
- Each release carries a changelog and, where needed, upgrade notes ([Upgrading](../../../guides/upgrading.md)).

## A routine

| When | Do |
|---|---|
| weekly | `composer outdated` and `composer audit` on the production lock file; read security notices |
| monthly (or at once for security fixes) | update in a copy, test, then production |
| after every update | file consistency check, smoke test, audit verify |

Update steps, from the installation guide:

1. In a copy of the site: `composer update <package>`, run the database updates the release names, then
   `./console exp:velocity deploy --allow-root-user` (or autoloads, cache clear and a PHP-FPM reload by hand).
2. Test the main editor tasks and the public pages; run your accessibility and security spot checks.
3. Repeat on production in a maintenance window ([Maintenance mode](../../../guides/maintenance-mode.md)).
4. **Setup > Upgrade check**: file consistency and database consistency
   ([The upgrade check](../../../guides/upgrade-check.md)).
5. `./console exp:audit verify --allow-root-user`.

Keep `composer.lock` in version control so production installs exactly what you tested.

## Records to keep

- date, versions before and after, who updated;
- `composer audit` output before and after;
- file consistency result;
- for security fixes: the time from release to production, against your policy's target.

## Related pages

- [13.15 Keeping up to date](../../../install/13-security-hardening.md#1315-keeping-up-to-date)
- [Upgrading](../../../guides/upgrading.md)
- [Updates and upgrades for website creators](../../creators/website-creators/updates-and-upgrades.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
