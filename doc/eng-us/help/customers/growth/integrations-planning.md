---
title: Planning an integration
audience: customer, businesses, partner
category: customer-success
order: 11
summary: A planning guide and template for site owners who connect an Exponential site with another system - CRM, shop system, newsletter tool, app, intranet directory, analytics - choosing the interface, the account and credentials, data protection, failure handling and ownership.
keywords: integration, planning, CRM, API, REST, remote services, feeds, syndication, API key, data protection, ownership, template
type: help
---
# Planning an integration

This page is for the owner who is asked to connect the site with another system. Every integration is a small
project: it needs a purpose, an interface, an account, a plan for when it fails, and an owner. Exponential offers
several interfaces; your partner chooses the technical one, but the decisions on this page are yours.

## Contents

1. [The interfaces Exponential offers](#the-interfaces-exponential-offers)
2. [Seven questions](#seven-questions)
3. [Data protection](#data-protection)
4. [When it fails](#when-it-fails)
5. [Integration plan template](#integration-plan-template)

## The interfaces Exponential offers

| Interface | Good for | Read |
|---|---|---|
| REST API (`ezprestapi`) | apps and front ends that read content; writes at v2 | [The REST API for owners](rest-api-for-owners.md) |
| Remote services (`expservices`) | scripts and portals that do what the administration does | [Remote services (expservices)](remote-services-expservices.md) |
| RSS, Atom and podcast feeds | other sites, aggregators and directories that read your news | [RSS and podcast feeds](rss-and-podcast-feeds.md) |
| RSS import | bringing another site's feed in as content | [RSS feeds](../../../guides/rss-feeds.md) |
| The `syndication` extension | sharing content between installations, one-way or two-way | [Feeds and syndication](../../businesses/mobile-and-headless/feeds-and-syndication.md) |
| Directory sign-in (LDAP and others) | staff signing in with their company account | [Directory sign-in and LDAP](../../businesses/intranets/directory-sign-in-and-ldap.md) |
| Payment gateways | the store's payments | [Payment gateways for businesses](../../businesses/stores/payment-gateways-for-businesses.md) |
| CSV export | form submissions, account lists, sub-item lists | [Collected information](../../../guides/collected-information.md) |

## Seven questions

1. **What is it for?** One sentence; what changes for visitors or staff.
2. **Which direction?** The site reads, the site is read, or both.
3. **Which data?** Content, users, orders, form submissions; how much, how often.
4. **Which account?** A dedicated account with a narrow role, not a person's account
   ([The REST API for owners](rest-api-for-owners.md)).
5. **Which credential?** OAuth2 for apps whose users sign in, a personal API key or a remote services token for a
   program ([Apps and API keys](../../businesses/mobile-and-headless/apps-and-api-keys.md)).
6. **Who owns it?** A person in your organization who decides about it, and a partner who maintains it.
7. **What does it cost to run?** Hosting, licenses of the other system, the partner's maintenance time.

## Data protection

- Write down which personal data goes from where to where, and why. This is not legal advice; ask whoever is
  responsible for data protection ([Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md)).
- Keep credentials out of documents and e-mail; keys and tokens are shown once and only a hash is stored
  ([Personal API keys](../../../guides/api-keys.md)).
- Every API key event and every remote services write is in the audit trail, which gives you a record of what the
  integration did ([The audit trail](../../../features/6.0/audit-trail.md)).

## When it fails

| Question | Answer to agree |
|---|---|
| How do we notice? | an alert, a daily report, or a person who looks |
| What does the other system do when the site does not answer (maintenance, upgrade)? | retry later, queue, or show cached data ([App backend reliability](../../businesses/mobile-and-headless/app-backend-reliability.md)) |
| What does the site do when the other system does not answer? | |
| Who can switch it off, and how? | revoke the credential, disable the account, or the service switch |
| Who is told? | |

Tell the integration's maintainer before every upgrade of the site
([Release and upgrade planning](../../leadership/release-and-upgrade-planning.md)).

## Integration plan template

**Template.**

```text
INTEGRATION PLAN
Name: ______________________   Owner (our side): ______________   Maintainer: ______________
Purpose (one sentence): ________________________________________________
Other system: ______________   Direction: site reads / site is read / both
Interface: REST API / remote services / feed / import / syndication / other: __________
Data exchanged: ________________________________   Personal data? yes / no
Account used on the site: ______________   Role: ______________
Credential: OAuth2 / API key / token   Expires: __________   Stored in: ______________
Failure: how noticed ______________   who switches off ______________
Reviewed with data protection on: __________   Go-live date: __________
Next review: __________
```

## Related pages

- [Mobile and apps](mobile-and-apps.md)
- [Selling through networks and partners](../../businesses/mobile-and-headless/selling-through-networks-and-partners.md)
- [Estimating and scoping with extensions](../../leadership/estimating-and-scoping-with-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
