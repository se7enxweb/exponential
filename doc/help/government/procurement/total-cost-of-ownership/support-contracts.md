---
title: Support contracts in the cost of an Exponential site
audience: government, project-manager, partner, hosting
category: public-sector
order: 80
summary: The kinds of support available for Exponential (free community help, partner support, paid help from 7x, a supplier's own contract), what each promises as published, how to define the support you need, and how to compare support offers in a total cost of ownership.
keywords: support contract, maintenance contract, SLA, response time, severity, support levels, community support, paid support, partner, 7x, cost comparison
type: help
---
# Support contracts in the cost of an Exponential site

This page is for project managers who must decide what support to buy and how to compare offers. Free software
comes with free community help but no promise of a response time; that promise has to be bought from someone. The
facts on what each source promises come from [Getting support](../../../project/getting-support.md),
[Support levels and response times](../../../partners/support-levels-and-response-times.md) and `SECURITY.md`.

## Contents
1. [Sources of support](#sources-of-support)
2. [Define what you need first](#define-what-you-need-first)
3. [What a support offer should state](#what-a-support-offer-should-state)
4. [Comparing offers on cost](#comparing-offers-on-cost)
5. [Pitfalls](#pitfalls)

## Sources of support

| Source | Promise | Cost |
|---|---|---|
| Community (forums, Telegram, Discord, Slack) | none: "its developers are not obligated to provide free support" | free |
| Project security channel | first answer to a private security report within one business day | free |
| Partner support (for partners, from the program) | response times per partner level, as published | part of the partner's own costs |
| Paid help from 7x | agreed with 7x | quoted by 7x ([Who is 7x?](../../../who-is-7x.md)) |
| Your supplier's support contract | what the contract says | quoted in the bid |

The Exponential project publishes no SLA of its own ([SLA options](../../../hosting/business/sla-options.md)).

## Define what you need first

Before you ask for prices, write down:

| Item | Your decision |
|---|---|
| Support hours | business hours, extended, around the clock |
| Severities | what is critical (site down, security), high, normal, low ([Support tiers](../../../hosting/business/support-tiers.md)) |
| Response and resolution targets per severity | blanks for your needs |
| Scope | the site's code, extensions, hosting, content help, training questions |
| Security releases | applied within a time you set |
| Channel | ticket system, phone for critical issues |
| Reporting | monthly report of tickets and times |

## What a support offer should state

- Whether response time means first answer or resolution, and business or clock hours, and the time zone. The
  partner page notes these are not defined in the program's published levels; your contract must define them.
- How the supplier staffs the promise: the partner guidance warns partners never to promise a shorter response than
  their own staff can deliver.
- What is excluded (your own changes, third-party services, content mistakes).
- How security issues are escalated upstream ([Escalations and security issues](../../../partners/escalations-and-security-issues.md)).
- That support staff never ask for passwords by e-mail or in tickets, and handle personal data as agreed
  ([Protecting customer data](../../../partners/protecting-customer-data.md)).

## Comparing offers on cost

```text
Offer   Fixed fee/period   Included hours   Rate beyond   Critical response   Hours covered   Security patch time
A       <...>              <...>            <...>         <...>               <...>           <...>
B       <...>              <...>            <...>         <...>               <...>           <...>
Expected use per period (your estimate): <hours>  -> expected cost A: <...>  B: <...>
```

Estimate expected use from your proof of concept and from the number of editors and sites. Price the support for
the whole horizon of the TCO model, including the last year before exit.

## Pitfalls

- Buying a response time without the staffing behind it.
- Paying for support of a version line that receives no security fixes (5.x and older, `SECURITY.md`).
- Relying on community help for a public service with availability duties.
- Support scope that excludes the custom code the same supplier wrote.

## Related pages
- [Total cost of ownership: overview](README.md)
- [Evaluating a support partner](../evaluation/evaluating-a-support-partner.md)
- [Getting support](../../../project/getting-support.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
