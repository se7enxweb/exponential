---
title: Security updates and patching cadence
audience: government, project-manager, admin, hosting
category: public-sector
order: 70
summary: How security fixes reach an Exponential site - private reporting, fixes on main, the next release - how a public body learns about them, and how to agree a patching time with a supplier or host without inventing the project's side of the promise.
keywords: security updates, patching, vulnerability, SECURITY.md, disclosure, release, patch window, response time, supplier obligation, monitoring releases, public sector
type: help
---
# Security updates and patching cadence

This page is for security officers and contract managers in a public body. It explains how a vulnerability in
Exponential is handled from report to fix, what the project promises and what it does not, and how to turn that into a
patching obligation for a supplier. The sources are [`SECURITY.md`](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md),
[Security standards](../../../../standards/security.md#vulnerability-disclosure) and
[SLA options for Exponential hosting](../../../hosting/business/sla-options.md).

## Contents

1. [From report to fix](#from-report-to-fix)
2. [What the project promises](#what-the-project-promises)
3. [How you learn about a fix](#how-you-learn-about-a-fix)
4. [Agreeing a patching time](#agreeing-a-patching-time)
5. [Your own reporting channel](#your-own-reporting-channel)

## From report to fix

1. Someone reports a vulnerability privately by e-mail to the address in `SECURITY.md`, not in the public tracker.
2. The reporter gets a first answer and is told whether the report is accepted.
3. An accepted issue is fixed on `main`.
4. The fix is published with the next 6.0.x release. Published tags are never changed.
5. Sites receive it by upgrading to that release ([Planning upgrades in the budget](planning-upgrades-in-the-budget.md)).

## What the project promises

| Promise | Source |
|---|---|
| Private reporting channel | `SECURITY.md` |
| "A first answer within one business day" | `SECURITY.md` |
| Fix on `main`, released as the next 6.0.x | `SECURITY.md` |
| Supported line: 6.0.x; 5.x and older not | `SECURITY.md` |

Not promised: a time to fix, advance notice to users, a security advisory feed. There is no `security.txt` file
shipped ([Security standards](../../../../standards/security.md#vulnerability-disclosure)). The hosting SLA page
advises: promise to apply a released fix within a time, not that a vulnerability will be fixed within a time; "the
second depends on the project, the first on you" ([SLA options](../../../hosting/business/sla-options.md#security-updates)).

## How you learn about a fix

- Watch the releases of the repository on GitHub, and the changelog of the current line
  ([6.0.15 changelog](../../../../../../changelogs/6.0/6.0.15.md)).
- The administration dashboard has a panel "Stay secure: keep Exponential up to date" with links to the Git Manager
  and Composer updates.
- **Setup > Updates and packages** lists newer versions of the installed packages
  ([Update with Git](../../../update-with-git.md#track-b-the-administration)).
- Hosts: [Security updates and disclosure](../../../hosting/security/security-updates-and-disclosure.md) and
  [Working with the project and 7x](../../../hosting/business/working-with-the-project-and-7x.md).
- Press guidance on how fixes are announced: [Covering security fixes](../../../press/covering-security-fixes.md).

Make one named role responsible for watching. Without an owner, nobody notices a release.

## Agreeing a patching time

```text
Security updates
1. The Supplier monitors the releases of Exponential and of every extension used on the Site.
2. When a release fixes a security issue that affects the Site, the Supplier applies it to the
   production Site within <time> of the release, after a test on the staging copy.
3. If the release cannot be applied in time, the Supplier informs <role> within <time> and applies a
   documented mitigation (for example a web server rule, maintenance mode, or disabling the affected
   feature) until it can.
4. The Supplier records each security update in the <period> report: release, date applied, site checked.
```

Choose the times with your security officer, considering your risk and the supplier's capacity. A shorter promise
than the supplier can staff is worthless; see the advice for partners in
[Support levels and response times](../../../partners/support-levels-and-response-times.md#aligning-your-own-slas).

Mitigations that exist in Exponential and are documented: maintenance mode ([Maintenance mode](../../../../guides/maintenance-mode.md)),
request rules and a request filter in front of the site ([Security hardening](../../../security-hardening.md#a-shield-in-front-request-shield)).

## Your own reporting channel

Your public site should tell visitors where to report a security problem with it. Exponential ships no `security.txt`
(RFC 9116, [rfc-editor.org/rfc/rfc9116](https://www.rfc-editor.org/rfc/rfc9116)); an operator can add one. Route
reports about the software itself to the project as `SECURITY.md` describes, without personal data.

## Related pages

- [The product lifecycle](README.md)
- [Supported versions](supported-versions.md)
- [Security hardening](../../../security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
