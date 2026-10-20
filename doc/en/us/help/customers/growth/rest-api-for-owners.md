---
title: The REST API for owners
audience: customer, businesses, partner
category: customer-success
order: 9
summary: What a site owner needs to know about the REST API of Exponential - what it serves, reads at v1 and v2 and writes at v2, how programs authenticate, why permissions still apply, who controls the credentials, and what to ask before an integration goes live.
keywords: REST API, headless, ezprestapi, v1, v2, OAuth2, API key, permissions, integration, app, site owner
type: help
---
# The REST API for owners

This page is for owners whose site will feed an app, another website or an internal system. The REST API lets programs
read content and, at version 2, create, update and delete it, as JSON over HTTP
([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)). You do not need to read the routes; you need to know
what it means for control and safety.

## Contents

1. [In short](#in-short)
2. [What stays in your control](#what-stays-in-your-control)
3. [Which credential for which program](#which-credential-for-which-program)
4. [Questions before going live](#questions-before-going-live)
5. [Once a year](#once-a-year)

## In short

From [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md):

- One extension serves it: `ezprestapi`. The older `ezprestapiprovider` is deprecated; its final release is a
  compatibility shim on top of `ezprestapi`.
- **Reads** answer at v1 and v2 (`/api/ezp/v1/...`, `/api/ezp/v2/...`); **writes** answer at **v2 only**.
- **Every read and write checks the user's policies** (`content/read`, `content/create`, `content/edit`,
  `content/remove`), whatever the request was authenticated with.
- Programs authenticate with an OAuth2 token or a personal API key sent as `Authorization: Bearer expk_...`.

The business view is on [REST API v1 and v2 for businesses](../../businesses/mobile-and-headless/rest-api-v1-and-v2-for-businesses.md).

## What stays in your control

| Control | How |
|---|---|
| What a program may read or change | the policies of the account it acts as; a program never gets more than that account |
| Which content is public | the same sections and roles as the website: content anonymous visitors may not read is not served to anonymous requests |
| Who may create keys | the policy `apikey/create` ([Personal API keys](../../../guides/api-keys.md)) |
| What a key may do | scopes chosen when it is made, never beyond the owner's policies |
| Ending access | revoke the key; it takes effect at the next request on every server |
| Evidence | every key made, revoked, first used or refused is in the audit trail as `access.apikey.*` |

> **Tip**
> Give each integration its **own account** with a role that allows only what it needs, rather than a key on a
> person's account. When that person leaves, the integration keeps working, and you can see in the audit what the
> integration did.

## Which credential for which program

From [Apps and API keys](../../businesses/mobile-and-headless/apps-and-api-keys.md):

| Program | Credential |
|---|---|
| An app whose users sign in | OAuth2 (the users authorize the app) |
| A script or integration acting as one account | a personal API key with scopes |
| A script using the remote services | a remote services token ([Remote services (expservices)](remote-services-expservices.md)) |

Keys are shown **once** when they are made; only a hash is stored, so nobody can show a key again, not even an
administrator ([Personal API keys](../../../guides/api-keys.md)). Store them in the program's secret store, never in a
document or e-mail.

## Questions before going live

- [ ] Which account does the integration use, and what role does it have?
- [ ] Read only, or also writes? If it writes, is there an approval or a review of what it creates?
- [ ] Where is the key kept, who can see it, and when does it expire?
- [ ] What does the program do when the API answers with an error or the site is in maintenance?
- [ ] Who is told when the integration fails?
- [ ] Has the partner checked the version of `ezprestapi` and that the old provider extension is no longer active?

More questions are on [Planning an integration](integrations-planning.md).

## Once a year

- Administrators see every key at **Setup > oAuth admin > API keys**, with search and filters; revoke the ones nobody
  needs ([Personal API keys](../../../guides/api-keys.md)).
- Include the integration accounts in the [access review](../../businesses/security/access-reviews.md).
- Ask the partner whether an upgrade changes anything for API clients.

## Related pages

- [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)
- [Headless with the REST API](../../businesses/mobile-and-headless/headless-with-the-rest-api.md)
- [Mobile and apps](mobile-and-apps.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
