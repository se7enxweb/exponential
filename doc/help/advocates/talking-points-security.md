---
title: "Talking points: security"
audience: advocate
category: advocacy
order: 16
summary: How to talk about Exponential's security honestly, as a process with evidence rather than a promise, with sourced points on vulnerability reporting, supported versions, the Content Security Policy, the audit trail, two-step sign-in, hardening and dependencies.
keywords: talking points, security, vulnerability, SECURITY.md, responsible disclosure, Content Security Policy, CSP, audit trail, two-step sign-in, 2FA, hardening, request logs, default password, Zeta Components, supply chain
type: help
---
# Talking points: security

This page is for advocates who are asked "is it secure?" No honest person can answer "yes" to that question for any
software. What you can do is describe the **process** and the **evidence**, and show the person how to check it.

## Contents

1. [The rule: process, not promises](#the-rule-process-not-promises)
2. [The points](#the-points)
3. [What to show](#what-to-show)
4. [Do not say](#do-not-say)
5. [When someone reports a vulnerability to you](#when-someone-reports-a-vulnerability-to-you)

## The rule: process, not promises

| Instead of | Say |
|---|---|
| "It is secure." | "Vulnerabilities are reported privately, answered within one business day, and fixed in the next release." |
| "It has never been hacked." | "Here is the hardening guide and the checklist your administrator can follow." |
| "We take security seriously." | "Here is what the project does, with the source for each point." |

## The points

| Say | Source |
|---|---|
| "Security issues are reported privately by e-mail, not in public, and you get a first answer within one business day." | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| "6.0.x is the maintained line. Fixes are made on `main` and published with the next 6.0.x release." | [Security fixes](../who-is-the-exponential-foundation.md#security-fixes) |
| "A published version never changes. A fix reaches you as the next release, so what you certified stays what it was." | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| "The administration can send a Content Security Policy; by default it is report-only there, with documented steps to enforce it." | [Content Security Policy](../../features/6.0/content-security-policy.md) |
| "New administration templates carry no inline styles or scripts, so a strict policy can be enforced." | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md), [Styling the administration](../../bc/6.0/admin4-styling.md) |
| "A tamper-evident audit trail records who did what; every record carries the hash of the one before." | [The audit trail](../../features/6.0/audit-trail.md) |
| "Two-step sign-in with an authenticator app or an e-mail code." | [Two-step sign-in](../../guides/two-factor-login.md) |
| "New installations generate a random administrator password when none or a well-known one is given." | [The default administrator password](../default-administrator-password.md) |
| "Probes and attacks are classified into a security log, with masked addresses." | [Request logs](../../features/6.0/request-logs.md) |
| "Administrators get a threat-by-threat hardening guide and a checklist." | [Security hardening](../security-hardening.md) |
| "The project sends security fixes upstream too: 14 pull requests to Zeta Components in April 2026, as the se7enxweb profile reports." | [The PHP stack it stands on](../who-is-the-exponential-foundation.md#the-php-stack-it-stands-on-and-gives-back-to) |

## What to show

On an installation you control, as an administrator:

1. The **Audit** tab: "Your sign-in is already recorded."
2. `/user2fa/setup`: the two-step sign-in setup.
3. The [hardening checklist](../security-hardening.md#checklist) on screen.
4. [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) in the browser.

Never show settings files, keys, passwords or server addresses on a shared screen ([Live demo safety](live-demo-safety.md)).

## Do not say

- "It is secure", "unhackable", "bank-grade", "military-grade".
- "It has never had a vulnerability." No source says so, and every serious project has had some.
- "The CSP protects you." It is report-only on the administration by default and off on the sites
  ([Content Security Policy](../../features/6.0/content-security-policy.md)); the administrator decides.
- Anything about a specific vulnerability that is not public.

## When someone reports a vulnerability to you

People may tell an advocate first. Then:

1. **Thank them** and ask them **not** to post details publicly.
2. **Point them** to the private address in `SECURITY.md`, or GitHub's private vulnerability reporting of the
   repository ([When something goes wrong, Security problems](../when-something-goes-wrong.md#security-problems)).
3. **Do not** forward the details in a public channel, and do not try to reproduce it on someone else's site.
4. **Do not** promise a fix date.

The full routine is on [Security reports stay private](security-reports-stay-private.md).

## Related pages

- [Advocating for security](advocating-for-security.md)
- [Objection: "Is open source secure?"](objection-open-source-security.md)
- [Handling objections, Security](../business/objection-handling.md#security)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
