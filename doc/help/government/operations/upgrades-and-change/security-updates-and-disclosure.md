---
title: Security updates and disclosure
audience: government, admin, project-manager
category: public-sector
order: 130
summary: Which Exponential versions get security fixes, how fixes reach a public body (the next release, never a changed tag), how to track and apply them under change management, and how to report a vulnerability privately.
keywords: security updates, patches, vulnerability, supported versions, SECURITY.md, disclosure, security@se7enx.com, release, changelog, patch management, NIS2, fast-track change, vulnerability management
type: help
---
# Security updates and disclosure

This page is for security officers, service owners and administrators who must keep a public website patched and
report vulnerabilities responsibly. The policy is in the file `SECURITY.md` at the root of the repository; the
hosting view is [Security updates and disclosure (hosting)](../../../hosting/security/security-updates-and-disclosure.md).

## Contents
1. [What is supported](#what-is-supported)
2. [How a fix reaches you](#how-a-fix-reaches-you)
3. [Tracking what you need](#tracking-what-you-need)
4. [Applying a security fix](#applying-a-security-fix)
5. [Reporting a vulnerability](#reporting-a-vulnerability)
6. [Related pages](#related-pages)

## What is supported

From `SECURITY.md`:

| Version | Supported |
|---|---|
| 6.0.x, the current line (`main`, and the next tag cut from it) | yes |
| 6.0.x released tags (`v6.0.0` to `v6.0.14`) | fixed by upgrading to the newest 6.0.x |
| 5.x, 4.x, 3.x and older | no |

A site on an unsupported line needs an upgrade plan before anything else ([Upgrade overview](upgrade-overview-for-public-sector.md)).

## How a fix reaches you

Security fixes are made on `main` and published with the next 6.0.x release. A published tag is never changed: a fix
for `v6.0.14` reaches you as the next release, not as a corrected copy of the old one
([Immutable releases and versions](immutable-releases-and-versions.md)). Extensions follow the same rule in their own
repositories.

## Tracking what you need

| Source | What it tells you |
|---|---|
| The changelog of the line ([6.0.15](../../../../changelogs/6.0/6.0.15.md)) | every change, including security changes |
| [Security standard](../../../../standards/security.md) | the OWASP mapping and the known gaps |
| **Setup > Updates and packages** (ezupdate), **Check for updates** | which installed packages have newer versions (`composer outdated`); changes nothing |
| **Setup > Upgrade check** | whether files differ from the release you run |
| Your operating system's and PHP's own advisories | the platform below Exponential |

The security standard notes that the project's CI runs no dependency vulnerability scan; if your policy needs one,
run your own (for example `composer audit` in your pipeline) ([Security standard, "Known gaps"](../../../../standards/security.md#known-gaps)).

Some public bodies have legal duties on vulnerability handling and incident reporting, for example under the EU's
[NIS2 Directive](https://eur-lex.europa.eu/eli/dir/2022/2555/oj) where it applies to them; check with your
information security and legal teams.

## Applying a security fix

1. Read the release notes; decide the change kind (normal on a fast track, or emergency) with your change manager
   ([Change management and advisory boards](change-management-and-advisory-boards.md)).
2. Apply on staging first ([Staging and approval](staging-and-approval.md)), unless the risk of waiting is higher.
3. Back up, open a [maintenance window](maintenance-windows.md) if the database changes, update, deploy:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ./console exp:velocity deploy --dry-run --allow-root-user
   ```

4. Verify with **Setup > Upgrade check** and the version on **Setup > System information**.
5. Record the change and the version now running.

## Reporting a vulnerability

Report privately by e-mail to the address in `SECURITY.md` (security@se7enx.com), not in the public issue tracker; a
first answer is promised within one business day. Include the version, PHP version, the engine and database, what an
attacker can do and how to reproduce it. Leave out passwords, secrets and personal data. The installation ships no
`security.txt` file ([RFC 9116](https://www.rfc-editor.org/rfc/rfc9116)); a public body that wants one for its own site
adds it to its web root itself.

## Related pages
- [Extension updates](extension-updates.md)
- [Reading release notes](reading-release-notes.md)
- [Security hardening](../../../security-hardening.md)
- [Hardening the host](../hosting/hardening-the-host.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
