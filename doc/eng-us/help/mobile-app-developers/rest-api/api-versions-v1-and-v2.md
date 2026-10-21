---
title: API versions v1 and v2
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 195
summary: Which REST API version an app should call - reads identical at v1 and v2, writes at v2 only, links that stay in their version, the ezpl alias, the deprecated ezprestapiprovider shim, what 6.0.15 changed for existing apps, and how to keep an app working across server upgrades.
keywords: versions, v1, v2, ezpl, ezprestapi, ezprestapiprovider, deprecated, shim, compatibility, upgrade, existing app, 6.0.15, REST, mobile app
type: help
---
# API versions v1 and v2

This page is for app developers who start a new app, and for those who maintain an app that already calls the REST
API. It explains what the version in the path means today and what to expect from server upgrades.

## Contents

1. [What the version means](#what-the-version-means)
2. [Which version a new app should call](#which-version-a-new-app-should-call)
3. [Existing apps on v1](#existing-apps-on-v1)
4. [The deprecated provider](#the-deprecated-provider)
5. [Keeping an app working across upgrades](#keeping-an-app-working-across-upgrades)

## What the version means

Since Exponential 6.0.15 one extension, `ezprestapi` 1.2.6 or later, serves both versions with one provider
(`ezp7xRestApiProvider`) and one controller ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md#in-short)):

- **Reads** answer at `/api/ezp/v1/...` and `/api/ezp/v2/...` with the same bodies.
- **Writes** (create, update, delete) answer at v2 only; a write sent to v1 does not exist there and is refused.
- **Links inside an answer stay in the version of the request**, so a v1 client keeps following v1 links.
- **`ezpl`** is an alias prefix of `ezp`: `/api/ezpl/v2/...` answers the same as `/api/ezp/v2/...`.

## Which version a new app should call

Use **v2 for everything**. It has every read v1 has, plus the writes, and one base URL keeps the client simple.
There is no reason to start on v1.

## Existing apps on v1

An app built years ago against v1 keeps working. The guide reports that the v1 and v2 answers were compared with
recorded answers of 33 URLs, on Apache and on Velocity, before and after the change, including the mobile app's
`GET /api/ezp/v1/content/node/<id>/list/offset/0/limit/20?ResponseGroups=Fields`, and were byte for byte identical,
except that `OutputFormat=xhtml` fills `renderedOutput` again
([Activation combinations](../../../guides/rest-api-v1-and-v2.md#7-activation-combinations-before-and-since)).

What did change for older installations is on the server: with some activation orders of the two old extensions,
v2 addresses answered `404` and v1 reads ran without permission checks. Since the current releases every combination
behaves the same, and every read is checked. If an old app suddenly gets `403` after a server upgrade, the account it
uses lacks `content/read` for that content; before, the check was skipped.

## The deprecated provider

`ezprestapiprovider` was the original v1 provider. Its final release, 6.0.4, is a compatibility shim on top of
`ezprestapi`, and installations are asked to activate `ezprestapi` and remove `ezprestapiprovider`
([Migrating from ezprestapiprovider](../../../guides/rest-api-v1-and-v2.md#6-migrating-from-ezprestapiprovider)).
Nothing changes for OAuth clients, tokens, API keys or v1 URLs. For an app developer this is the server team's
task, not an app release.

## Keeping an app working across upgrades

- **Read the base URL from configuration**, not from code, so a move to another host or prefix needs no release.
- **Ignore unknown keys** in answers; new keys may appear.
- **Probe the API at start-up in debug builds** (`/content/node/<id>` with a known id) and log the status.
- **Watch the release notes** of the server: [ezprestapi](../../../features/6.0/extensions/ezprestapi.md) and the
  6.0 changelogs.
- **Test against the next server version** before it reaches production
  ([A local test installation](../testing/a-local-test-installation.md)).

## Related pages

- [REST API v1 and v2 for businesses](../../businesses/mobile-and-headless/rest-api-v1-and-v2-for-businesses.md)
- [ezprestapiprovider](../../../features/6.0/extensions/ezprestapiprovider.md)
- [Routes reference](routes-reference.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
