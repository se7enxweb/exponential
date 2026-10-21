---
title: Mobile app developers reading path
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 2
summary: The order in which to read the Mobile app developers section - a one-hour path to a first working list screen, then four stages (understand, connect, build, ship), and extra pages for apps that write content, work offline or run in a browser.
keywords: reading path, order, learning path, where to begin, first hour, mobile app, REST, OAuth, expservices, offline, launch, checklist
type: help
---
# Mobile app developers reading path

This page is for app developers who want to know what to read, and in which order, to get from nothing to a shipped
app on Exponential content. You do not need every page of the section. Follow the one-hour path first, then the
stage you are in.

## Contents

1. [The first hour](#the-first-hour)
2. [Stage 1: understand](#stage-1-understand)
3. [Stage 2: connect](#stage-2-connect)
4. [Stage 3: build](#stage-3-build)
5. [Stage 4: ship](#stage-4-ship)
6. [Extra paths](#extra-paths)
7. [Who should read what](#who-should-read-what)

## The first hour

Six pages, about ten minutes each, ending with a list screen that loads real data:

1. [Mobile app developers: start here](mobile-app-developers-start-here.md)
2. [How Exponential content reaches an app](how-exponential-content-reaches-an-app.md)
3. [Testing against a demo](testing/testing-against-a-demo.md), to find an installation that answers
4. [Personal API keys for tools](auth/personal-api-keys-for-tools.md), for a development credential
5. [Your first request](rest-api/your-first-request.md)
6. [Listing children and pagination](rest-api/listing-children-and-pagination.md)

## Stage 1: understand

- [Which API for your app](which-api-for-your-app.md)
- [What the APIs do not do](what-the-apis-do-not-do.md)
- [Content classes for apps](content-modeling/content-classes-for-apps.md)
- [API versions v1 and v2](rest-api/api-versions-v1-and-v2.md)

## Stage 2: connect

- [Choosing a credential](auth/choosing-a-credential.md)
- [OAuth2 authorization code flow](auth/oauth2-authorization-code-flow.md)
- [A back end for your app](auth/a-backend-for-your-app.md)
- [Storing secrets on devices](auth/storing-secrets-on-devices.md)
- [The Authorization header and proxies](auth/authorization-header-and-proxies.md)

## Stage 3: build

- [Reading a node](rest-api/reading-a-node.md), [Response groups](rest-api/response-groups.md) and
  [Fields and field values](rest-api/fields-and-field-values.md)
- [Images in REST answers](media-and-images/images-in-rest-answers.md) and
  [Image aliases for screen sizes](media-and-images/image-aliases-for-screen-sizes.md)
- [Calling expservices from an app](json-endpoints/calling-expservices-from-an-app.md) and
  [Searching content](json-endpoints/searching-content.md)
- [Node ids, remote ids and deep links](content-modeling/node-ids-remote-ids-and-deep-links.md)
- [Errors and status codes](rest-api/errors-and-status-codes.md)
- [Caching in the app](offline-and-performance/caching-in-the-app.md)

## Stage 4: ship

- [Security checklist](checklists/security-checklist.md)
- [Launch checklist](checklists/launch-checklist.md)
- [App store review readiness](checklists/app-store-review-readiness.md)
- [Permissions for app users](publishing-workflow/permissions-for-app-users.md)

## Extra paths

| Your app | Add |
|---|---|
| writes content (field reports, user submissions) | [Creating and removing content](rest-api/creating-and-removing-content.md), [Writing through expservices](json-endpoints/writing-through-expservices.md), [Files and uploads](media-and-images/files-and-uploads.md) |
| works offline | [Syncing and offline reading](offline-and-performance/syncing-and-offline-reading.md), [Caching on the server](offline-and-performance/caching-on-the-server.md) |
| runs in a browser (PWA, single-page app) | [Web apps, CORS and CSP](offline-and-performance/web-apps-cors-and-csp.md) |
| serves several languages | [Multilingual content](content-modeling/multilingual-content.md) |
| shows articles | [Rich text in apps](content-modeling/rich-text-in-apps.md), [Rendered XHTML output](rest-api/rendered-xhtml-output.md) |
| needs a preview for editors | [Drafts, preview and publishing](publishing-workflow/drafts-preview-and-publishing.md) |

## Who should read what

| Role | Pages |
|---|---|
| App developer | the whole path above |
| Technical lead | Stage 1, [A back end for your app](auth/a-backend-for-your-app.md), the checklists |
| Server-side PHP developer | [Application creators: start here](../creators/app-creators/app-creators-start-here.md), then [REST API for applications](../creators/app-creators/rest-api.md) |
| Product owner | [Mobile apps, headless use and networks](../businesses/mobile-and-headless/README.md) and the [launch checklist](checklists/launch-checklist.md) |

## Related pages

- [Mobile app developers FAQ](mobile-app-developers-faq.md)
- [Mobile app developers glossary](mobile-app-developers-glossary.md)
- [The application creator's reading path](../creators/app-creators/app-creators-reading-path.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
