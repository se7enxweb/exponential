---
title: Remote services (expservices)
audience: customer, businesses, partner
category: customer-success
order: 10
summary: What the remote services of Exponential (the expservices extension) mean for a site owner - the administration's functions as named HTTP services for scripts, apps and portals, the same permissions as the person, personal tokens, the audit record, the off switch, and what to agree with whoever uses them.
keywords: remote services, expservices, HTTP API, scripts, portals, tokens, automation, permissions, audit, ezjscore, site owner
type: help
---
# Remote services (expservices)

This page is for owners whose partner or own staff want to automate work on the site: a script that imports products
every night, a dashboard that reads figures, a portal front end, a desktop or phone app for editors. The extension
`expservices` turns the administration's functions into over a thousand named services, called over HTTP and
answering JSON ([Remote services and apps](../../../guides/remote-services-and-apps.md)). It is part of the default
distribution.

## Contents

1. [What it is, in owner's terms](#what-it-is-in-owners-terms)
2. [What keeps it safe](#what-keeps-it-safe)
3. [Tokens](#tokens)
4. [What to agree before anyone uses it](#what-to-agree-before-anyone-uses-it)
5. [Switching it off](#switching-it-off)

## What it is, in owner's terms

A service is called by name, `exp<domain>::<method>`, under `/ezjscore/call/`
([Remote services](../../../features/6.0/remote-services-expservices.md)). The services cover:

| Group | Examples |
|---|---|
| Core | session, system, settings (read), caches, cronjobs, extensions, packages, workflows |
| Content | nodes, objects, classes, versions, translations, relations, trash, sections, states, URL aliases, search, content jobs |
| Users and access | users, groups, roles, policies, sessions, preferences, notifications |
| Commerce, community, feeds | products, basket and checkout, orders, VAT, currencies, discounts, collected forms, RSS |
| Media and other | images, files, tags, layouts, audit (read only), newsletters, sitemaps, statistics |

Sample portal front ends built on the services ship in the extension
([Remote services](../../../features/6.0/remote-services-expservices.md)). For reading content into a separate app,
the REST API may be the simpler choice ([The REST API for owners](rest-api-for-owners.md)).

## What keeps it safe

From [Remote services](../../../features/6.0/remote-services-expservices.md):

- Each service is `public`, needs a signed-in user, or needs a policy; without login it answers 401, without the
  policy 403. A script can never do more than the account it uses.
- **Writes** need POST, the form token and the policy, and every successful write is recorded in the audit trail as
  `service.<domain>.<method>`.
- Large operations are routed to content jobs, which run in the background in batches.
- No write is open without a policy.

## Tokens

A signed-in user can create a personal token for a script: it is shown once, only its hash is stored, it lasts 1 to
365 days (90 by default), and requests with it run with the same policies as the user. Expired, revoked or
disabled-user tokens answer 401, and a token cannot create tokens. Creating, revoking and failed use go to the audit
trail ([Remote services](../../../features/6.0/remote-services-expservices.md)).

> **Tip**
> As with API keys: give each script its **own account** with a narrow role, and a token with an expiry. A token on a
> person's administrator account can do everything that person can.

## What to agree before anyone uses it

| Question | Your answer |
|---|---|
| Which tasks will be automated? | |
| Which account and role does each script use? | |
| Read only, or writes? | |
| When does each token expire, and who renews it? | |
| Where are tokens kept? | |
| Who looks at the audit for the script's writes, and how often? | |
| What happens if the script misbehaves (who switches it off)? | |

Copy the table into the plan of the integration ([Planning an integration](integrations-planning.md)).

## Switching it off

The installation-wide switch is `[Services] Enabled` in `extension/expservices/settings/expservices.ini`; when it is
`disabled`, every service answers 403 ([Remote services](../../../features/6.0/remote-services-expservices.md)). Your
administrator can also revoke one token, or disable the account a script uses
([Offboarding staff](../day-to-day/offboarding-staff.md)). Know which of these you would use before you need it.

## Related pages

- [Remote services and apps](../../../guides/remote-services-and-apps.md)
- [Remote services for businesses](../../businesses/mobile-and-headless/remote-services.md)
- [Planning an integration](integrations-planning.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
