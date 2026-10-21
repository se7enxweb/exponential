---
title: Escalation for resellers
audience: reseller, partner, hosting
category: partnership
order: 410
summary: How a reseller escalates a customer problem it cannot solve alone - inside your company first, then partner support tickets, the partner team, paid 7x support, the public issue tracker for bugs, and security@se7enx.com for vulnerabilities - with what to send and what to tell the customer meanwhile.
keywords: escalation, escalate, support ticket, partner support, partner team, paid support, issue tracker, bug report, security, security@se7enx.com, vulnerability, customer communication
type: help
---
# Escalation for resellers

This page is for support leads at resellers. Your customer escalates to you; you escalate onward. The customer never
needs to know the third line exists: your contract makes you responsible
([Contracts with customers](contracts-with-customers.md)).

## Contents

1. [The escalation ladder](#the-escalation-ladder)
2. [Bugs in Exponential](#bugs-in-exponential)
3. [Security vulnerabilities](#security-vulnerabilities)
4. [What to tell the customer meanwhile](#what-to-tell-the-customer-meanwhile)
5. [Escalation contacts sheet](#escalation-contacts-sheet)

## The escalation ladder

| Step | When | How |
|---|---|---|
| 1. Inside your company | First line cannot solve it | Second line: the developer or administrator who knows the site; runbooks ([Runbooks](../../hosting/runbooks/README.md)) |
| 2. Partner support ticket | Second line cannot solve it | A GitHub issue ticket under your partner plan ([Opening a partner support ticket](../../partners/opening-a-partner-support-ticket.md)) |
| 3. Remind | The response time of your level has passed | Comment on the ticket with the time it was opened and your level |
| 4. The partner team | Production impact continues | Write to <partners@exponential.earth> with the ticket link |
| 5. Paid support | You need guaranteed help now | Buy paid support from 7x (<https://exponential.earth/support>) |

Steps 3 to 5 follow the partners' ladder ([Escalations and security issues](../../partners/escalations-and-security-issues.md)).
Keep escalations factual and polite: what is broken, since when, what it costs, what you need.

## Bugs in Exponential

A reproducible bug in Exponential itself goes to the software issue tracker, as described on
[When something goes wrong](../../when-something-goes-wrong.md). Before you report:

- [ ] Reproduce it on a test installation of the newest 6.0.x release.
- [ ] Note the version, PHP version, engine and database.
- [ ] Include the error reference and the matching log lines ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).
- [ ] Remove passwords, secrets and personal data from logs and screenshots.

Fixes arrive in a release; you then apply it ([Keeping customers current](keeping-customers-current.md)). Meanwhile,
look for a workaround you can apply yourself.

## Security vulnerabilities

- Report privately to **security@se7enx.com**, never in a public issue, forum, chat or a ticket others can read.
- "You can expect a first answer within one business day" (`SECURITY.md`).
- Include the version, PHP version, engine and database, what an attacker can do and the steps to reproduce; leave out
  passwords, secrets and personal data.
- Do not tell the public before a fix is released. Protect affected customers with measures you can apply yourself
  ([Security hardening](../../security-hardening.md)).
- After the release, update every affected site and tell customers what was done, without details that help attackers.

A suspected compromise of a customer site is severity 1: follow [Compromised site](../../hosting/runbooks/compromised-site.md).

## What to tell the customer meanwhile

```text
Status update <date, time>
What we see: <symptom, in plain words>
What we have done: <steps>
Workaround: <if any>
Next update: <time>
```

Send updates on the promised rhythm even when nothing has changed. Never blame 7x, the project or another supplier
to the customer; you are responsible for the service.

## Escalation contacts sheet

```text
Our first line: <channel, hours>
Our second line: <names/roles, phone for S1>
Partner plan: <level>, tickets used this month: ____ of ____
Partner team: partners@exponential.earth
Paid support: https://exponential.earth/support
Security: security@se7enx.com (private)
Bug tracker: as described on "When something goes wrong"
Hosting supplier (if any): <contact, contract number>
```

The customer's own version of such a sheet is [Escalation contacts template](../../customers/partners-and-hosts/escalation-contacts-template.md).

## Related pages

- [Escalations and security issues](../../partners/escalations-and-security-issues.md)
- [Security updates and disclosure](../../hosting/security/security-updates-and-disclosure.md)
- [Support tiers you offer](../offers/support-tiers-you-offer.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
