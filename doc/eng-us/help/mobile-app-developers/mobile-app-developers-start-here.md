---
title: "Mobile app developers: start here"
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 1
summary: The landing page for developers who build iOS, Android, cross-platform or browser apps on top of Exponential content - the three HTTP interfaces (the REST content API at v1 and v2, the expservices remote services, ezjscore server functions), how apps authenticate, what the APIs do and do not do, and where every page of this section is.
keywords: mobile app, iOS, Android, Flutter, React Native, Kotlin, Swift, headless, REST API, v1, v2, ezprestapi, OAuth2, API key, expservices, ezjscore, JSON, start here, landing
featured: true
type: help
---
# Mobile app developers: start here

This section is for developers who build an app that shows or writes Exponential content: a native iOS or Android
app, a cross-platform app (Flutter, React Native, Kotlin Multiplatform and the like), a progressive web app, a kiosk,
a watch or TV client, or any other program that talks to an Exponential installation over HTTP. You do not need to
know PHP to use these pages. When you need to change the server side, the
[Application creators](../creators/app-creators/app-creators-start-here.md) section takes over.

> **Note**
> Every route, parameter and setting named in this section was checked in the code of the `ezprestapi` extension,
> the kernel's REST framework (`kernel/private/rest/`), `ezjscore`, `expservices` and `settings/rest.ini`, and in the
> existing guides it links to. Where something does not exist (an SDK, CORS headers, image aliases in REST answers,
> a search route in REST), the pages say so instead of describing a workaround as a feature.

## Contents

1. [Exponential for app developers in one minute](#exponential-for-app-developers-in-one-minute)
2. [The three HTTP interfaces](#the-three-http-interfaces)
3. [How an app signs in](#how-an-app-signs-in)
4. [Your first ten minutes](#your-first-ten-minutes)
5. [Pages in this section](#pages-in-this-section)
6. [What there is not](#what-there-is-not)
7. [Other sections of this help](#other-sections-of-this-help)

## Exponential for app developers in one minute

Exponential stores content as **objects** of **content classes** (article, folder, image, product, your own classes),
placed as **nodes** in a content tree. Editors work in the administration; your app reads the published result over
HTTP and gets JSON. Every call runs as a user, and the user's roles and policies decide what the call may see or
change. That is the whole model an app needs; [How Exponential content reaches an app](how-exponential-content-reaches-an-app.md)
explains it with real answers.

## The three HTTP interfaces

| Interface | URL pattern | Best for | Read |
|---|---|---|---|
| REST content API (`ezprestapi`) | `/api/ezp/v1/...` and `/api/ezp/v2/...` | reading nodes, children and fields; creating and removing content (v2) | [REST API](rest-api/README.md) |
| Remote services (`expservices`) | `/ezjscore/call/exp<domain>::<method>` | search, image alias URLs, URL aliases, languages, updates; over a thousand services | [JSON endpoints](json-endpoints/README.md) |
| ezjscore server functions | `/ezjscore/call/<name>::<function>` | a small JSON endpoint written for your app by a PHP developer | [ezjscore server functions for your app](json-endpoints/ezjscore-server-functions-for-your-app.md) |

Most apps read with the REST API and use one or two remote services for what REST does not cover. The decision is
laid out in [Which API for your app](which-api-for-your-app.md).

## How an app signs in

| Credential | Header | For | Read |
|---|---|---|---|
| OAuth2 access token | `Authorization: OAuth <token>` (or `Bearer <token>`) | an app whose users sign in with their own Exponential account | [OAuth2 authorization code flow](auth/oauth2-authorization-code-flow.md) |
| Personal API key `expk_...` | `Authorization: Bearer expk_...` | one person's own tool or script, never a published app | [Personal API keys for tools](auth/personal-api-keys-for-tools.md) |
| expservices token `expt_...` | `Authorization: Bearer expt_...` and `X-Exp-Token: expt_...` | calling the remote services as a user | [expservices tokens in apps](auth/expservices-tokens-in-apps.md) |

The REST API requires authentication by default (`rest.ini [Authentication] RequireAuthentication=enabled`). Choosing
is explained in [Choosing a credential](auth/choosing-a-credential.md).

## Your first ten minutes

1. Probe the installation you will work against: [Probe before you build](testing/testing-against-a-demo.md#probe-before-you-build).
2. Get a credential for development: a personal API key on a test installation is the quickest
   ([Personal API keys for tools](auth/personal-api-keys-for-tools.md)).
3. Make your first request: [Your first request](rest-api/your-first-request.md).
4. List a folder in pages: [Listing children and pagination](rest-api/listing-children-and-pagination.md).
5. Read the gaps before you design screens: [What the APIs do not do](what-the-apis-do-not-do.md).

The full order is in the [reading path](mobile-app-developers-reading-path.md). Short answers are in the
[FAQ](mobile-app-developers-faq.md) and the words are explained in the [glossary](mobile-app-developers-glossary.md).

## Pages in this section

| Folder | What it covers |
|---|---|
| [REST API](rest-api/README.md) | Routes, response groups, lists and paging, fields, XHTML output, writes, errors, versions |
| [Authentication](auth/README.md) | OAuth2 and refresh tokens, API keys, expservices tokens, secrets on devices, a back end for your app |
| [JSON endpoints](json-endpoints/README.md) | Calling expservices, search, writes, your own server functions |
| [Content modeling](content-modeling/README.md) | Content classes for apps, ids and deep links, languages, rich text |
| [Media and images](media-and-images/README.md) | Images in REST answers, image aliases for screen sizes, files and uploads |
| [Offline and performance](offline-and-performance/README.md) | Server caching, app caching, sync, browser apps, CORS and CSP |
| [Publishing workflow](publishing-workflow/README.md) | Drafts and preview, permissions for app users, auditing |
| [Testing](testing/README.md) | Testing against a demo, a local test installation |
| [Checklists](checklists/README.md) | Security, launch, app store review readiness |

## What there is not

No official mobile SDK ships with Exponential. The extension `expsite_app` is not one either: its own `README.md`
describes it as a site project's place for design overrides and project settings. What ships are the HTTP interfaces
above, the shell and Python clients of `expservices`, and client snippets for Kotlin, Swift, Objective-C, Qt and GTK
in the [expservices reference](../../bc/6.0/backend_ezjscore_services.md#clients), which states that the Kotlin, Swift,
Objective-C and Qt snippets were reviewed by hand but not compiled. The full list of gaps is in
[What the APIs do not do](what-the-apis-do-not-do.md).

## Other sections of this help

| Section | For |
|---|---|
| [Application creators](../creators/app-creators/app-creators-start-here.md) | PHP developers who extend the server: REST providers, modules, services |
| [Mobile apps, headless use and networks](../businesses/mobile-and-headless/README.md) | Owners and product leads who plan the app |
| [Website creators](../creators/website-creators/website-creators-start-here.md) | The people who build the site and its content classes |
| [Hosting companies](../hosting/hosting-companies-start-here.md) | The people who run the server your app talks to |

## Related pages

- [Which API for your app](which-api-for-your-app.md)
- [How Exponential content reaches an app](how-exponential-content-reaches-an-app.md)
- [Headless Exponential](../creators/app-creators/headless-exponential.md)
- [REST API v1 and v2: one extension](../../guides/rest-api-v1-and-v2.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
