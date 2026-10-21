---
title: Headless with the REST API
audience: businesses, app-creator, project-manager
category: business-solutions
order: 30
summary: What it means for a business to use Exponential as the content back end of an app or a separate front end - what stays in Exponential, what your developers build, the REST API's capabilities and limits, preview, caching, and the briefing points for an agency.
keywords: headless, REST API, mobile app, front end, single page application, content back end, ezprestapi, preview, caching, permissions, agency brief, business
type: help
---
# Headless with the REST API

This page is for managers and product owners who plan an app or a separate front end that shows content managed in
Exponential. "Headless" means Exponential keeps the content, editing and permissions, while another program does the
presentation. This page explains what that division means for your team and your budget, what the API can and cannot
do today, and what to put in a brief for developers. The technical guide is
[Headless Exponential](../../creators/app-creators/headless-exponential.md).

## Contents

1. [What stays in Exponential, and what you build](#what-stays-in-exponential-and-what-you-build)
2. [Which API for which job](#which-api-for-which-job)
3. [What the REST API does](#what-the-rest-api-does)
4. [Things to plan early](#things-to-plan-early)
5. [What does not exist (yet)](#what-does-not-exist-yet)
6. [Briefing developers or an agency](#briefing-developers-or-an-agency)

## What stays in Exponential, and what you build

From [Headless Exponential](../../creators/app-creators/headless-exponential.md#what-you-keep-and-what-you-replace):

| Kept in Exponential | Built by your front end |
|---|---|
| Content classes, editing, versions, translations, workflows | Page templates, menus, the page frame |
| Roles and policies, checked on every API call | Routing and URL handling |
| The administration and the layouts editor for editors | Rendering of layouts |
| Search, media storage, image aliases | Client-side state and caching |

Your editors' work does not change: they publish in the administration as before. What you add is a second program
that has to be designed, built, hosted and maintained.

## Which API for which job

| API | For | Read |
|---|---|---|
| REST v1 and v2 (`ezprestapi`) | Reading nodes, children and fields; creating and removing content; OAuth for apps | [REST API v1 and v2 for businesses](rest-api-v1-and-v2-for-businesses.md) |
| expservices | The administration's functions as services, with discovery and tokens | [Remote services](remote-services.md) |
| ezjscore server functions | Small JSON endpoints your own developers write | [JSON endpoints with ezjscore](../../creators/app-creators/json-endpoints-with-ezjscore.md) |

For an app that shows your content, the REST API is the usual choice.

## What the REST API does

From [REST API v1 and v2 for applications](../../creators/app-creators/rest-api.md):

- **Reads** a node, its children (in pages, with offset and limit), the number of children, and fields, by node or
  object id, at v1 and v2.
- **Creates and removes** content at v2 only.
- Gives a rich text field's value as plain text. `OutputFormat=xhtml` adds the node rendered by templates, which
  gives markup only once a developer has written the `rest` view templates for it.
- Gives an image field as the path of its original file only, without the resized versions (aliases). The app asks
  for alias addresses through the remote services (`expimage::url`, `expimage::urls`); define aliases for the sizes
  your app needs.
- **Checks the user's policies** on every read and write, whatever credential was used.
- **Requires authentication by default**; public front ends use "a read-only integration user" whose key stays on
  your server.

## Things to plan early

| Topic | Why it matters | Source |
|---|---|---|
| **Preview** | Editors preview with the site's templates; a headless front end "needs its own preview", a protected route that reads drafts. "Plan this early; it is the part teams forget." | [Preview and drafts](../../creators/app-creators/headless-exponential.md#preview-and-drafts) |
| **Caching and purging** | The REST framework has an answer cache; the front end or a CDN caches too, and must be told when content is published, for example by a workflow event that calls your purge hook | [Caching and performance](../../creators/app-creators/headless-exponential.md#caching-and-performance) |
| **Links inside content** | Links in rich text point to site addresses; the front end maps them to its own routes | [Rich text, images and links](../../creators/app-creators/headless-exponential.md#rich-text-images-and-links) |
| **Credentials** | Which key or token the app uses, and who can revoke it | [Apps and API keys](apps-and-api-keys.md) |
| **SEO** | Content inside an app is not indexed; a website still carries search visibility | [Mobile strategy](mobile-strategy.md#what-an-app-adds-to-your-workload) |

> **Tip**
> "Start hybrid unless there is a reason not to": keep the website in Exponential's templates and add the app on the
> API. Preview, layouts and SEO stay simple for the site
> ([Hybrid: templates and API together](../../creators/app-creators/headless-exponential.md#hybrid-templates-and-api-together)).

## What does not exist (yet)

Stated honestly, from the sources:

- The REST route `listAtom` answers `500` at v1 and v2; use the `list` route or the site's RSS export instead
  ([8. Known issue: listAtom](../../../guides/rest-api-v1-and-v2.md#8-known-issue-listatom)).
- The kernel has no GraphQL API. (The README describes REST and GraphQL APIs for Exponential Platform DXP, a
  different product line.)
- The documentation describes no push-notification service; notifications to a phone are your app's own back end.
- Layouts are rendered by the site's templates; a headless front end renders them itself.

## Briefing developers or an agency

Include in the brief:

- [ ] Which content the app reads (classes, folders, languages) and whether it writes anything.
- [ ] Which credential it uses, held where, rotated how ([Apps and API keys](apps-and-api-keys.md)).
- [ ] The integration user's role: read only, limited to the folders the app needs.
- [ ] How editors preview content in the app before publishing.
- [ ] How the app learns that content changed (polling with limits, or a purge hook on publish).
- [ ] Image sizes the app needs, so matching aliases are defined, and how the app fetches their addresses.
- [ ] What the app shows when the API does not answer ([App backend reliability](app-backend-reliability.md)).
- [ ] That every request goes over HTTPS (`rest.ini [Authentication] RequireHTTPS` can refuse plain HTTP).

## Related pages

- [REST API v1 and v2 for businesses](rest-api-v1-and-v2-for-businesses.md)
- [Apps and API keys](apps-and-api-keys.md)
- [Headless Exponential](../../creators/app-creators/headless-exponential.md)
- [REST API v1 and v2 for applications](../../creators/app-creators/rest-api.md)
- [Exponential as an application platform](../../creators/app-creators/exponential-as-an-application-platform.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
