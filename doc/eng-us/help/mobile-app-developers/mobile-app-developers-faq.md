---
title: Mobile app developers FAQ
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 90
summary: Short answers to the questions app developers ask most about Exponential - 401 without a body, which version to call, PKCE, API keys in apps, image sizes, search, updates, CORS, languages, paging limits, SDKs and where to report a problem.
keywords: FAQ, questions, troubleshooting, 401, 403, 501, OAuth, PKCE, API key, v1, v2, image alias, search, update, CORS, translation, paging, SDK, expsite_app, mobile app
type: help
---
# Mobile app developers FAQ

Short answers to the questions app developers ask most. Each answer links to the page that explains it.

## Contents

1. [Connecting](#connecting)
2. [Reading content](#reading-content)
3. [Writing content](#writing-content)
4. [Platform and tooling](#platform-and-tooling)

## Connecting

**My request answers 401 with an empty body. Why?**
No valid credential reached PHP. Without one, the REST API answers `401` with the header
`WWW-Authenticate: OAuth realm='Exponential REST'` and no body (seen on the demonstration server). Check the header
name and scheme, and whether a proxy drops `Authorization`
([The Authorization header and proxies](auth/authorization-header-and-proxies.md)).

**Should my app call v1 or v2?**
Reads answer the same at both; writes exist at v2 only. New apps can use v2 throughout
([API versions v1 and v2](rest-api/api-versions-v1-and-v2.md)).

**Does the OAuth server support PKCE or public clients?**
No. The token endpoint requires `client_secret` and supports the grants `authorization_code` and `refresh_token`
only. Keep the secret on a server ([A back end for your app](auth/a-backend-for-your-app.md)).

**Can I ship a personal API key inside my app?**
No. A key belongs to one user, acts as that user and is meant for that user's own scripts. Anyone who unpacks the
app would have it ([Personal API keys for tools](auth/personal-api-keys-for-tools.md)).

**Does the app get the `state` parameter back from the authorize page?**
No. The authorize view says "state, not implemented yet". Correlate the redirect yourself
([OAuth2 authorization code flow](auth/oauth2-authorization-code-flow.md)).

**How long does a token live?**
`rest.ini [OAuthSettings] TokenTTL`, one hour by default; the token answer says so in `expires_in`
([OAuth2 authorization code flow](auth/oauth2-authorization-code-flow.md#refreshing-tokens)).

## Reading content

**Where is node 2, and which node should my app start from?**
Node 2 is the root of the content tree in a standard installation. Ask the editors for the folder your app shows,
and prefer its remote id over its number ([Node ids, remote ids and deep links](content-modeling/node-ids-remote-ids-and-deep-links.md)).

**Why does a list return only ten items?**
The default limit of the `list` route is 10. Add `/offset/<n>/limit/<n>`
([Listing children and pagination](rest-api/listing-children-and-pagination.md)).

**Why are my fields missing from the list?**
Lists give metadata only, unless you add `?ResponseGroups=Fields` ([Response groups](rest-api/response-groups.md)).

**How do I get an image in the size of my screen?**
REST gives the original file path only. Ask for an alias URL with `expimage::url`
([Image aliases for screen sizes](media-and-images/image-aliases-for-screen-sizes.md)).

**Is there a search route?**
Not in REST. The remote services have `expsearch::search` ([Searching content](json-endpoints/searching-content.md)).

**How do I read another language?**
Add `?Translation=<locale>` (for example `ger-DE`) to node, object and field reads
([Multilingual content](content-modeling/multilingual-content.md)).

**My article text has lost its formatting.**
Rich text fields are given as plain text in REST answers. See [Rich text in apps](content-modeling/rich-text-in-apps.md).

## Writing content

**Can the app update an existing item through REST?**
Not yet: the update route checks `content/edit` and then answers `501`. Use `expnode::update`
([Writing through expservices](json-endpoints/writing-through-expservices.md)).

**My create answers 400.**
`languageLocale` is missing, or `parentNodeID` or `classIdentifier` is missing or unknown
([Creating and removing content](rest-api/creating-and-removing-content.md)).

**Can users upload photos from the app?**
No documented route takes a file from a device ([Files and uploads](media-and-images/files-and-uploads.md)).

## Platform and tooling

**Is there an SDK?**
No. There are shell and Python clients for expservices and hand-written client snippets for other platforms
([Mobile app developers: start here](mobile-app-developers-start-here.md#what-there-is-not)).

**Is `expsite_app` a mobile app framework?**
No. It is the place for a site project's design overrides and settings, layered over the `simple` design
(its `README.md`).

**My browser app gets a CORS error.**
Exponential sends no CORS headers. Serve the app from the same origin or add the headers at the web server
([Web apps, CORS and CSP](offline-and-performance/web-apps-cors-and-csp.md)).

**Where do I test without breaking a real site?**
A local installation of your own is best; a public demo may not have every API active
([Testing](testing/README.md)).

**Where do I ask or report?**
The help's **Report an error** page and [When something goes wrong](../when-something-goes-wrong.md); the server-side
FAQ is [Application creators: frequently asked questions](../creators/app-creators/app-creator-faq.md).

## Related pages

- [Mobile app developers: start here](mobile-app-developers-start-here.md)
- [What the APIs do not do](what-the-apis-do-not-do.md)
- [Mobile app developers glossary](mobile-app-developers-glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
