---
title: REST API v1 and v2 for businesses
audience: businesses, app-creator, admin
category: business-solutions
order: 40
summary: The REST content API of Exponential 6.0.15 explained for decision makers - what v1 and v2 do, what changed for existing apps (nothing for their answers), the permission checks added, the deprecated ezprestapiprovider, known gaps, and what to ask your administrator.
keywords: REST API, v1, v2, ezprestapi, ezprestapiprovider, deprecated, permissions, mobile app, compatibility, upgrade, known issues, listAtom, business
type: help
---
# REST API v1 and v2 for businesses

This page is for managers and product owners whose app, partner integration or front end already uses Exponential's
REST API, or will. It explains the two API versions in plain language, what the 6.0.15 release changed and did not
change for existing clients, which gaps are known, and what to ask the person who runs your installation. The full
technical guide is [REST API v1 and v2: one extension](../../../guides/rest-api-v1-and-v2.md).

## Contents

1. [Two versions, one extension](#two-versions-one-extension)
2. [What each version can do](#what-each-version-can-do)
3. [What 6.0.15 changed for existing apps](#what-6015-changed-for-existing-apps)
4. [Permissions on every call](#permissions-on-every-call)
5. [Known gaps](#known-gaps)
6. [Questions for your administrator](#questions-for-your-administrator)

## Two versions, one extension

Since Exponential 6.0.15, both versions of the REST content API come from **one** extension, `ezprestapi`
(1.2.6 or later). Reads answer at v1 and v2 (`/api/ezp/v1/...`, `/api/ezp/v2/...`); writes answer at v2 only
([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md#in-short)).

The older extension `ezprestapiprovider` is **deprecated**. Its final release, 6.0.4, is a compatibility shim on top
of `ezprestapi`; installations are asked to activate `ezprestapi` and remove `ezprestapiprovider`
([6. Migrating from ezprestapiprovider](../../../guides/rest-api-v1-and-v2.md#6-migrating-from-ezprestapiprovider)).
The 6.0.15 root `composer.json` requires `ezprestapi` and no longer requires the old one.

## What each version can do

From [2. The routes](../../../guides/rest-api-v1-and-v2.md#2-the-routes):

| Task | v1 | v2 |
|---|---|---|
| Read one item (node or object), its fields or one field | yes | yes |
| List the children of a folder, in pages | yes | yes |
| Count the children | yes | yes |
| Create and publish an item | no | yes |
| Remove an item (with everything below it) | no | yes |
| Update an item | no | checks the edit permission, then answers "not implemented yet" (501) |

For a business that means: an app that **shows** content works with either version; an app or integration that
**adds** content (orders imported from a marketplace, entries from a field app) uses v2; changing existing content
through the REST API is not available yet. For administration tasks beyond content, the remote services offer more
([Remote services](remote-services.md)).

## What 6.0.15 changed for existing apps

The guide states: "Nothing changes for clients: the v1 and v2 answers are byte for byte the same as before, except
that `OutputFormat=xhtml` fills `renderedOutput` again." It reports that this was checked against recorded answers
of 33 URLs on Apache and on Velocity, before and after the change, including the list call of an existing mobile app
([7. Activation combinations](../../../guides/rest-api-v1-and-v2.md#7-activation-combinations-before-and-since)).

The same section documents what went wrong before, with some combinations of the two extensions: v2 addresses that
answered 404, and v1 reads that ran without the permission checks. With the current releases every combination
behaves the same.

> **Note**
> If your app was built years ago against v1, it keeps working. The upgrade is an administrator's task on the server,
> not an app release.

## Permissions on every call

"Every read and write checks the user's policies (`content/read`, `content/create`, `content/edit`,
`content/remove`), whatever the request was authenticated with" (same guide). Authentication is required by default.
Two credentials are accepted: OAuth2 tokens for apps that act for their users, and personal API keys for a user's own
scripts ([Apps and API keys](apps-and-api-keys.md)).

For a business, this is the key safety property: an app can never see or change more than the user account behind it
may, and a public app should use an account that may only read what the public may read.

## Known gaps

| Gap | Effect | Workaround from the sources |
|---|---|---|
| The Atom list route `listAtom` | Answers 500 at v1 and v2, before and after the change | Use the `list` route, or the site's RSS export ([Feeds and syndication](feeds-and-syndication.md)) |
| Updating content through REST | Answers 501 after the permission check | Create a new item, or use the administration or the remote services |
| Apache with FastCGI | The `Authorization` header must reach PHP | A server configuration task ([REST API for applications](../../creators/app-creators/rest-api.md#configuration)) |

## Questions for your administrator

- [ ] Is `ezprestapi` 1.2.6 or later active, and `ezprestapiprovider` removed from the active extensions?
- [ ] Do `/api/ezp/v1/content/node/2` and `/api/ezp/v2/content/node/2` answer `200` with a valid key? (The guide's
      check after migrating.)
- [ ] Is authentication required (`RequireAuthentication=enabled`, the default), and is plain HTTP refused
      (`RequireHTTPS`)?
- [ ] What is the token lifetime for OAuth apps (`TokenTTL`, one hour by default)?
- [ ] After a change to `rest.ini`, were the INI cache cleared, PHP-FPM reloaded and Velocity restarted, as the guide
      says?
- [ ] Which user accounts do our apps use, and what may each of them do?

## Related pages

- [Headless with the REST API](headless-with-the-rest-api.md)
- [Apps and API keys](apps-and-api-keys.md)
- [REST API v1 and v2: one extension](../../../guides/rest-api-v1-and-v2.md)
- [REST API v1 and v2 for applications](../../creators/app-creators/rest-api.md)
- [ezprestapi](../../../features/6.0/extensions/ezprestapi.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
