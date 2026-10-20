---
title: Advocating for security
audience: advocate
category: advocacy
order: 111
summary: How advocates promote good security practice around Exponential - private disclosure, prompt upgrades, hardening and two-step sign-in, safe examples in posts and tutorials, and calm, factual answers - without fear-mongering or promises.
keywords: security advocacy, upgrades, hardening, two-step sign-in, 2FA, responsible disclosure, safe examples, secrets, Content Security Policy, audit trail, community
type: help
---
# Advocating for security

This page is for advocates who want sites built with Exponential to be safer. You do not need to be a security
expert. Most of the good you can do is to spread a few habits and to model them in everything you publish.

## Contents

1. [The five habits to spread](#the-five-habits-to-spread)
2. [Model it in your own material](#model-it-in-your-own-material)
3. [Talking about security without fear](#talking-about-security-without-fear)
4. [Helping administrators](#helping-administrators)

## The five habits to spread

| Habit | Why | Source |
|---|---|---|
| Report vulnerabilities privately | So fixes come before misuse | [Security reports stay private](security-reports-stay-private.md) |
| Upgrade to the newest 6.0.x | Released tags are "fixed by upgrading to the newest 6.0.x" | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Follow the hardening checklist | Most incidents come from how a site is run | [Security hardening](../security-hardening.md#checklist) |
| Turn on two-step sign-in | Stolen passwords alone no longer suffice | [Two-step sign-in](../../guides/two-factor-login.md) |
| Never share secrets | Keys and passwords leak through screenshots and issues | [Live demo safety](live-demo-safety.md) |

## Model it in your own material

- Examples use placeholders (`<your-password>`), never real-looking credentials.
- Screenshots show no keys, settings files or server addresses.
- Tutorials that install something say "change the generated password and delete
  `var/log/initial-admin-password`" ([The default administrator password](../default-administrator-password.md)).
- Tutorials that touch the server mention the hardening guide.
- Never recommend turning a security feature off to "make it work"; find the documented way.

## Talking about security without fear

- Describe the process ([Talking points: security](talking-points-security.md)), not dangers.
- Do not spread rumors about vulnerabilities; point to release notes.
- When a release fixes a security problem, encourage upgrading with the release's own words, no more.

## Helping administrators

| They ask | Point to |
|---|---|
| "Where do I start?" | [Security hardening](../security-hardening.md) and [chapter 13 of the installation book](../../install/13-security-hardening.md) |
| "How do I see attacks?" | [Request logs](../../features/6.0/request-logs.md), [The audit trail](../../features/6.0/audit-trail.md) |
| "How do I enforce a CSP?" | [Content Security Policy, Switching to enforce](../../features/6.0/content-security-policy.md#switching-to-enforce-step-by-step) |
| "Can a shield sit in front?" | [A shield in front: request-shield](../security-hardening.md#a-shield-in-front-request-shield) |
| "How do we upgrade safely?" | [Upgrading](../../install/11-upgrading.md) |

## Related pages

- [Talking points: security](talking-points-security.md)
- [Objection: "Is open source secure?"](objection-open-source-security.md)
- [Security reports stay private](security-reports-stay-private.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
