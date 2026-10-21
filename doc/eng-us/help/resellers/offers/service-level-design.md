---
title: Service level design for resellers
audience: reseller, partner, hosting
category: partnership
order: 190
summary: How a reseller designs service levels it can keep - why the response times of the partner plans are what 7x promises you and not what you may promise your customers, how to derive your own times from your staff, what the project itself commits to, and a service level statement to copy.
keywords: service level, SLA, response time, first response, resolution, uptime, partner plan, 7x response time, business hours, on call, severity, credits, security fixes
type: help
---
# Service level design for resellers

This page is for owners and support leads who write the service levels in a reseller's offers. The most common
mistake in reselling is to copy a supplier's promise into your own contract. This page shows how to avoid it.

## Contents

1. [Whose promise is it?](#whose-promise-is-it)
2. [What the project and 7x commit to](#what-the-project-and-7x-commit-to)
3. [Deriving your own times](#deriving-your-own-times)
4. [Uptime and availability](#uptime-and-availability)
5. [Security fixes](#security-fixes)
6. [A service level statement to copy](#a-service-level-statement-to-copy)

## Whose promise is it?

> **Note**
> The response times of the partner plans (≤ 36 h for Student, Now and Starter, ≤ 24 h for Basic, ≤ 12 h for
> Professional and Silver, ≤ 4 h for Gold, ≤ 3 h for Platinum) are what **7x promises you**, the partner, for your
> GitHub issue tickets. They are **not** response times you may promise your customers.

Three reasons:

1. **Your customer's problem reaches 7x only after you.** Your own diagnosis time comes first.
2. **Not published:** whether response time means first answer or resolution, business or clock hours, and the time
   zone ([Support levels and response times](../../partners/support-levels-and-response-times.md)).
3. **A response is not a fix.** A bug in Exponential is fixed in the next 6.0.x release (`SECURITY.md`), which you
   then apply.

So your promise rests on your own staff. Partner support is your back-up.

## What the project and 7x commit to

| Source | Commitment |
|---|---|
| `SECURITY.md` | A first answer to a private security report "within one business day"; accepted issues fixed on `main` and released with the next 6.0.x version; "a published tag is never changed" |
| Community support | None: "its developers are not obligated to provide free support" (<https://exponential.earth/support>) |
| Partner plan | Tickets and response times per level, to you ([Partner plans and prices](../../partners/partner-plans-and-prices.md#the-plans)) |
| The project | No release calendar, no end-of-life date for 6.0, no service level agreement ([Release line and support](../../customers/healthy/release-line-and-support.md#what-the-project-does-not-publish)) |

## Deriving your own times

Work from your team, not from a target:

```text
Staff who can handle severity 1 for this customer ........ ____ people
Hours they are reachable ................................. ____ (days, hours, time zone)
Measured median first response last quarter ............. ____ h
Measured 90th percentile first response .................. ____ h
Promise = 90th percentile, rounded up .................... ____ h
```

| Promise | Needs at least |
|---|---|
| Next business day | One trained person and a deputy |
| Same business day for severity 1 and 2 | Two people who know the site; runbooks |
| Four hours, around the clock, for severity 1 | A paid on-call rota of several people, monitoring that wakes them |

Measure first response and resolution separately, and promise resolution only for things in your control (restoring
a backup, rolling back an update), never for fixes upstream.

## Uptime and availability

- Promise uptime only for what you operate, measured by your own monitoring
  ([Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md)).
- Exclude announced maintenance windows; use maintenance mode for them
  ([Maintenance mode](../../../guides/maintenance-mode.md)).
- If you resell someone else's hosting, your uptime promise cannot be better than theirs.
- Options for service credits and their wording are on [SLA options](../../hosting/business/sla-options.md).

## Security fixes

The only way a site receives a fix is an update to the newest 6.0.x release. A realistic promise:

```text
When an Exponential release carries a security fix, we apply it to your site within <n> business days
of its publication, after a test on <staging / a copy>. Critical issues: within <n> business days.
```

Base `<n>` on how long a tested update across all your customers really takes
([Keeping customers current](../customers/keeping-customers-current.md)).

## A service level statement to copy

```text
Service levels for <Customer>, effective <date>
Support hours: <days, hours, time zone>
First response: S1 <time>, S2 <time>, S3 <time>, S4 <time> (within support hours unless stated)
Severity 1 outside support hours: <yes, phone <role number> / no>
Availability of the hosting we operate: <percentage> per month, excluding announced maintenance
Backups: <frequency>, kept <days>; restore tested <frequency>
Security releases: applied within <n> business days of publication
Measurement and reporting: <monthly report>
Not covered: fixes in Exponential itself (reported upstream, applied when released), third-party services
```

## Related pages

- [Support levels and response times](../../partners/support-levels-and-response-times.md)
- [SLA template](../../customers/partners-and-hosts/sla-template.md)
- [Support tiers you offer](support-tiers-you-offer.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
