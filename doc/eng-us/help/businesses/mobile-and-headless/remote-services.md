---
title: Remote services
audience: businesses, app-creator, admin
category: business-solutions
order: 60
summary: What the expservices remote services give a business - the administration's functions as over a thousand named HTTP services for portals, scripts, partner tools and remote admin apps, with discovery, personal tokens, permissions per service, audited writes and sample portals.
keywords: expservices, remote services, HTTP API, portal, remote administration, scripts, integration, discovery, tokens, audit, ezjscore, business
type: help
---
# Remote services

This page is for managers and product owners who want other programs to do more than read content: a portal for
customers or partners, a remote administration app, a script that runs the shop's daily routine, an integration with
another business system. Exponential's `expservices` extension turns the administration's functions into named
services that such programs call over HTTP. This page explains what that offers, how it is controlled, and when to
use it instead of the REST API.

## Contents

1. [What expservices is](#what-expservices-is)
2. [What can be done through it](#what-can-be-done-through-it)
3. [How it is kept safe](#how-it-is-kept-safe)
4. [Portals built on it](#portals-built-on-it)
5. [REST API or remote services](#rest-api-or-remote-services)
6. [Questions before you rely on it](#questions-before-you-rely-on-it)

## What expservices is

From [Remote services and apps](../../../guides/remote-services-and-apps.md): `expservices` turns the
administration's functions into "over a thousand named services (content, users, roles, caches, cronjobs, shop, feeds,
media, tags, layouts, the audit and more)", served by the ezjscore call interface under `/ezjscore/call/`. A service
is called by name and answers JSON. It is "part of the default distribution", and needs no build step and no extra
server.

**Discovery** lists every service with its parameters, so programs do not hard-code the catalog
([Remote services with expservices](../../creators/app-creators/remote-services-expservices.md#calling-a-service)).

## What can be done through it

The service groups, from [Remote services: the whole admin as an HTTP API](../../../features/6.0/remote-services-expservices.md#domains):

| Group | Examples |
|---|---|
| Core | session, system information, settings (read), caches, cronjobs, extensions, packages, workflows, Velocity |
| Content | nodes, objects, classes, versions, translations, relations, locations, trash, sections, states, URL aliases, search, content jobs |
| Users and access | users, groups, roles, policies, sessions, preferences, notifications, account flows |
| Commerce, community, feeds | products, basket and checkout, orders, VAT, currencies, discounts, wish lists, shipping, payments, collected forms, polls, forums, comments, reviews, RSS exports and imports, RSS, Atom and JSON Feed output of any subtree |
| Media and other | images and aliases, files and signed download links, audio and video, tags, layouts, audit (read only), newsletters, sitemaps, statistics, designs, languages |

For a business, the commerce and community services are the interesting part: a partner portal or an app can show
products, run a basket and checkout, and list a customer's orders, through the same rules as the website.

## How it is kept safe

From the feature page's "Rules every service follows" and settings:

- Each service declares its access: public, any signed-in user, or a specific policy. "401 without login, 403 without
  the policy."
- **Writes** need a POST, the form token and the policy, and "every successful write records" an event in the audit
  trail.
- Large operations go to background content jobs instead of blocking a request.
- A **master switch** (`expservices.ini [Services] Enabled`): when disabled, every service answers 403.
- Lists are paged: 25 items by default, at most 200.
- **Personal tokens** for scripts and apps are shown once, stored only as a hash, expire after 1 to 365 days, and are
  recorded in the audit trail when made, revoked or refused ([Apps and API keys](apps-and-api-keys.md#expservices-tokens)).

> **Note**
> Every service runs as a user, with that user's policies. A portal for partners should use partner accounts with
> partner roles, never an administrator's account ([Extranets](../extranets/README.md)).

## Portals built on it

The extension ships sample front ends that render "news, shop, forums, media, feeds, search and login in the browser
without a build step": `expportal_jquery` (a jQuery 4 reference) and `expportal_reactive` (React with React
Bootstrap), among four designs under `extension/expservices/design/`
([Portals built on it](../../../features/6.0/remote-services-expservices.md#portals-built-on-it)). They call the
services by name. For a business they are a starting point and a demonstration, not a finished product: your
developers adapt them to your brand and your rules.

## REST API or remote services

From [When to use it](../../creators/app-creators/remote-services-expservices.md#when-to-use-it):

| Need | Use |
|---|---|
| A remote admin client, a portal, scripts that manage the site | expservices |
| Content for apps and front ends, OAuth for third parties | REST ([Headless with the REST API](headless-with-the-rest-api.md)) |
| Your own page's JavaScript | an ezjscore server function |
| AI tools that act inside the administration | Exponential Intelligence tools |

In short: REST for **showing content** to many users through an app, with OAuth; remote services for **doing
things**: orders, users, caches, feeds, as a signed-in person or a script.

## Questions before you rely on it

- [ ] Which services does the program need? (Ask developers for the list from the discovery catalog.)
- [ ] Which user account runs them, with which role?
- [ ] Is the master switch on only where needed, and are write tokens required (the default)?
- [ ] Who receives the audit events of writes, and who reviews them?
- [ ] Behind Apache with `mod_proxy_fcgi`, does the `Authorization` header reach PHP? (Otherwise the header
      `X-Exp-Token` can carry the token.)
- [ ] What happens in the portal when the site is in maintenance mode or unreachable
      ([App backend reliability](app-backend-reliability.md))?

## Related pages

- [Apps and API keys](apps-and-api-keys.md)
- [Selling through networks and partners](selling-through-networks-and-partners.md)
- [Remote services and apps](../../../guides/remote-services-and-apps.md)
- [Remote services with expservices](../../creators/app-creators/remote-services-expservices.md)
- [Remote services: the whole admin as an HTTP API](../../../features/6.0/remote-services-expservices.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
