---
title: Drafts, preview and publishing
audience: mobile-app-developer, app-creator, developer, editor
category: mobile-apps
order: 710
summary: How drafts and versions of Exponential content relate to an app - REST reads only published content, reading drafts and versions through the expversion services with versionread rights, building an editor preview for app screens, publishing a draft, and what editors should know.
keywords: drafts, versions, preview, publishing, expversion, versionread, dataMap, viewUrl, createDraft, publish, editor preview, staging, mobile app
type: help
---
# Drafts, preview and publishing

This page is for app developers who are asked "can editors see how this looks in the app before it goes live?" and
for the editors who ask. It explains what the APIs offer for unpublished content and how to build a preview.

## Contents

1. [What the REST API reads](#what-the-rest-api-reads)
2. [Versions and drafts through the remote services](#versions-and-drafts-through-the-remote-services)
3. [Building an app preview](#building-an-app-preview)
4. [Publishing from an app](#publishing-from-an-app)
5. [What editors should know](#what-editors-should-know)

## What the REST API reads

Node and object reads build the answer from the content as published; the REST routes have no version or draft
parameter ([Routes reference](../rest-api/routes-reference.md)). An editor's draft is invisible to the app until it is
published.

## Versions and drafts through the remote services

The `expversion` domain (declared in `extension/expservices/classes/content/expversionservices.php`) reads versions:

| Service | Arguments | Access |
|---|---|---|
| `expversion::list` | object id, limit, offset | `content/versionread` |
| `expversion::drafts` | object id | `content/versionread` |
| `expversion::myDrafts` | limit, offset | `content/edit` |
| `expversion::dataMap` | object id, version, language | `content/versionread` |
| `expversion::viewUrl` | object id, version, language | `content/versionread`; the relative URL that previews a version on the site |
| `expversion::publish` (write) | object id, version | `content/publish` |

`content/versionread` is an editor's right. A public app's user must not have it.

## Building an app preview

The pattern recommended for headless front ends is "read the draft version through an authenticated, editor-level
call and render it in a protected preview route" ([Headless Exponential](../../creators/app-creators/headless-exponential.md#preview-and-drafts)).
For an app:

1. A preview build of the app (or a preview mode behind an editor sign-in) that signs in as the editor.
2. The editor opens an item; the app lists its drafts with `expversion::drafts` and reads one with
   `expversion::dataMap::<object id>::<version>::<language>`.
3. The app renders the draft with the same screens as published content, marked clearly as a preview.

`expversion::dataMap` answers in the expservices attribute format, not the REST field format, so the preview needs a
small mapping layer; test it with every class the app shows.

## Publishing from an app

An editor app can publish a draft with `expversion::publish` (POST, `content/publish`), or create and publish in one
step with REST create or `expnode::create`. The publish workflow of the site runs as for the administration, so
approval steps still apply.

## What editors should know

- Publishing makes the item visible to the app, possibly after the server's answer cache expires (ten minutes by
  default) and the app's next refresh.
- Fields the app shows (teaser, main image) need content even when the website template does not show them.
- Removing or moving an item out of a folder the app shows makes it disappear from the app at its next sync.

## Related pages

- [Drafts and pending items](../../../guides/drafts-and-pending.md)
- [Content history](../../../guides/content-history.md)
- [Publishing and versions](../../publishing-and-versions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
