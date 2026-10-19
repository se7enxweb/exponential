---
title: Security updates and independence
audience: government, admin, project-manager
category: public-sector
order: 220
summary: How a public body keeps an Exponential installation secure without depending on one vendor: where security fixes come from, how to learn about releases, how to apply updates with the upgrade check and ezupdate, and what to require from providers.
keywords: security updates, patches, releases, SECURITY.md, upgrade check, ezupdate, Composer, vulnerability disclosure, independence, maintenance, NIS2
type: help
---
# Security updates and independence

This page is for administrators and managers responsible for keeping the site patched. Security fixes for
Exponential are public, like the code, so any competent administrator can apply them.

## Contents

1. [Where fixes come from](#where-fixes-come-from)
2. [Learning about releases](#learning-about-releases)
3. [Applying updates](#applying-updates)
4. [Hardening and monitoring](#hardening-and-monitoring)
5. [What to require from providers](#what-to-require-from-providers)

## Where fixes come from

"Exponential 6.0 is the maintained line. Security fixes are made on the `main` branch ... and published with the next
6.0.x release"; versions 5.x and older are not supported; reports go privately to the address in SECURITY.md, with a
first answer promised within one business day (SECURITY.md, quoted in
[Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md#security-fixes)). Extensions are
released in their own repositories.

## Learning about releases

- Watch the GitHub releases of the kernel and of the extensions you use.
- **Setup > Updates and packages** shows which installed packages have newer versions ([ezupdate](../../../features/6.0/extensions/ezupdate.md)).
- Read the changelogs before updating ([6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) for the current line).

## Applying updates

1. Update a test installation first.
2. Run **Setup > Upgrade check** to compare files and database with what the release expects ([Upgrade check](../../../guides/upgrade-check.md)).
3. Back up, put the site in maintenance mode if the release asks for database changes ([Maintenance mode](../../../guides/maintenance-mode.md)).
4. Update with Composer or **Setup > Updates and packages** (check, dry run, backup confirmation).
5. Run the update scripts the release names, clear caches, and when Velocity serves the site, deploy it
   (`./console exp:velocity deploy`).
6. Check the site and the logs.

The full procedure: [Upgrading](../../../guides/upgrading.md); for hosts: [Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md).

## Hardening and monitoring

- [Security hardening](../../security-hardening.md) and [13. Security hardening](../../../install/13-security-hardening.md).
- The OWASP mapping and the status of each control: [Security standards](../../../standards/security.md).
- The audit trail's alert rules for brute-force sign-ins, role grants and mass deletes ([Audit trail](../../../features/6.0/audit-trail.md)).

Frameworks such as the [NIST Cybersecurity Framework 2.0](https://www.nist.gov/cyberframework) or, in the EU, the duties
that follow from the [NIS2 Directive (EU) 2022/2555](https://eur-lex.europa.eu/eli/dir/2022/2555/oj) for entities in its
scope, ask for a patch process; this page is the Exponential part of it.

## What to require from providers

- A stated time to apply security releases after publication.
- A monthly report of versions installed (the about page and `composer.lock` show them).
- Notification of incidents and of any unsupported component.
- Access for your own staff to verify the installed versions.

## Related pages

- [Security and audit](../../../guides/security-and-audit.md)
- [Updates and upgrades](../../creators/website-creators/updates-and-upgrades.md)
- [Composer packages and source on GitHub](composer-packages-and-source-on-github.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
