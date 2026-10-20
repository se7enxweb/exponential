---
title: Security updates and disclosure
audience: businesses, admin, project-manager
category: business-solutions
order: 70
summary: What a business needs to know about security fixes for Exponential - which versions are supported, how fixes are published, how to tell whether a site is patched, who applies updates and how fast, and how to report a vulnerability privately.
keywords: security updates, patches, supported versions, SECURITY.md, vulnerability, responsible disclosure, private report, security@se7enx.com, release, 6.0.x, composer audit, upgrade check, update policy
type: help
---
# Security updates and disclosure

This page is for owners and managers who must make sure their site gets security fixes, and who need to know what
to do if someone finds a vulnerability. It explains the project's policy from `SECURITY.md`, what it means for your
budget and contracts, and how to report a problem the right way.

> **Note**
> Basics, not legal advice. If a vulnerability exposed personal data, notification duties may apply to you; ask a
> lawyer or your data protection officer at once.

## Contents

1. [Which versions get fixes](#which-versions-get-fixes)
2. [How fixes are published](#how-fixes-are-published)
3. [Is my site patched?](#is-my-site-patched)
4. [Who applies updates, and how fast](#who-applies-updates-and-how-fast)
5. [Reporting a vulnerability](#reporting-a-vulnerability)
6. [Extensions and everything around the site](#extensions-and-everything-around-the-site)
7. [Checklist](#checklist)

## Which versions get fixes

From `SECURITY.md`:

| Version | Supported |
|---|---|
| 6.0.x, the current line (`main` and the next tag cut from it) | yes |
| 6.0.x released tags (`v6.0.0` to `v6.0.14`) | fixed by upgrading to the newest 6.0.x |
| 5.x, 4.x, 3.x and older | no |

If your site still runs a version from the eZ Publish years (3.x, 4.x or 5.x), it gets no security fixes. Moving
it to Exponential 6 is a migration project; see [Switching to Exponential](../choosing/switching-to-exponential.md).

## How fixes are published

- "Security fixes are made on the `main` branch ... and published with the next 6.0.x release." (`SECURITY.md`)
- "A published tag is never changed: a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next
  release, not as a corrected copy of the old one." (`SECURITY.md`)

For a business this is good news: a version number always means the same code, so "are we patched?" has a clear
answer, and a version you tested does not change under you
([Security updates and disclosure for hosts](../../hosting/security/security-updates-and-disclosure.md#how-fixes-are-published)).

## Is my site patched?

Ask your administrator or host to tell you:

1. The version the site runs (`composer show se7enxweb/exponential`), compared with the newest 6.0.x release.
2. Whether any locked dependency has a known vulnerability (`composer audit`).
3. Whether **Setup > Upgrade check** reports modified or missing files; it only reads
   ([The upgrade check](../../../guides/upgrade-check.md)).
4. Whether extensions are current; **Setup > Updates and packages** of `ezupdate` checks without changing anything
   ([Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md)).

A modified kernel file that nobody changed on purpose deserves a look
([Compromised site](../../hosting/runbooks/compromised-site.md)).

## Who applies updates, and how fast

The project sets no time frame for applying a fix; that is your decision or your host's
([SLA options](../../hosting/business/sla-options.md#security-updates)). Decide and write down:

| Question | Example answer to adapt |
|---|---|
| Who watches for releases? | our administrator, monthly and when the project announces one |
| Who applies them? | our hosting partner, under the contract |
| Within how long after a security release? | [number] working days, in a planned window |
| What if it cannot be applied at once? | the gap is documented with a date to close it |
| Who approves? | the site owner, or pre-approved for security releases |

> **Tip**
> Promise to apply a released fix within a time, not that a vulnerability will be fixed within a time: the second
> depends on the project, the first on you ([SLA options](../../hosting/business/sla-options.md#security-updates)).

How an update goes, in short: read the changelog, update a staging copy, then the live site, check file consistency,
and test the front page and the sign-in ([Security updates and disclosure for hosts](../../hosting/security/security-updates-and-disclosure.md#rolling-updates-out)).
Budget the hours with [Budgeting for upgrades and maintenance](../costs/budgeting-for-upgrades-and-maintenance.md).

## Reporting a vulnerability

If you, a customer or a researcher finds a security problem in Exponential:

1. **Do not** post it in the public issue tracker, the forums or social media.
2. Report it privately to **security@se7enx.com** (`SECURITY.md`), or as a private advisory on GitHub
   (<https://github.com/se7enxweb/exponential/security/advisories/new>).
3. Include the version, the PHP version, the engine (Velocity, FrankenPHP, Apache or nginx with PHP-FPM), the
   database, what an attacker can do and the steps to reproduce.
4. Leave out passwords, secrets and personal data.

"You can expect a first answer within one business day." You are told whether the report is accepted and kept
informed while a fix is prepared (`SECURITY.md`). Problems in a third-party extension go to its maintainers
([When something goes wrong](../../when-something-goes-wrong.md#security-problems)).

Tell your own staff and partners where to send such reports, for example in your
[account and password policy](user-accounts-and-passwords.md#a-policy-text-to-adapt). Partners have their own
escalation route ([Escalations and security issues](../../partners/escalations-and-security-issues.md)).

## Extensions and everything around the site

A site is only as current as its weakest part:

- [ ] Each extension is its own Composer package with its own releases.
- [ ] PHP, the database server and the operating system need updates too.
- [ ] Velocity's engine has its own releases.
- [ ] Your own extensions and templates must be reviewed for unsafe patterns; the project's hardening notes list them
      ([Security updates and disclosure for hosts](../../hosting/security/security-updates-and-disclosure.md#everything-else-that-needs-updates)).

## Checklist

- [ ] The site runs a supported 6.0.x version.
- [ ] Someone named watches for releases.
- [ ] The time to apply a security release is written in your contract or policy.
- [ ] Updates go to a staging copy first.
- [ ] The upgrade check is clean after each update.
- [ ] Staff know to report vulnerabilities privately to security@se7enx.com.
- [ ] A plan exists for informing customers if their data was affected.

## Related pages

- [Release line and support policy](../reliability/release-line-and-support-policy.md)
- [Upgrades without surprises](../reliability/upgrades-without-surprises.md)
- [Security review checklist for hosts](../../hosting/checklists/security-review.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
