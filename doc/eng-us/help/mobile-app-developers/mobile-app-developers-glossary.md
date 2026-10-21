---
title: Mobile app developers glossary
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 95
summary: The words used in the Mobile app developers section explained for app developers - from node, object, class and remote id to response group, OutputFormat, provider, ezpl, OAuth client, expk and expt tokens, envelope, image alias and siteaccess - each with a link to where it is explained in full.
keywords: glossary, terms, definitions, node, object, class, field, remote id, response group, OutputFormat, Translation, provider, ezp, ezpl, OAuth, TokenTTL, API key, expk, expt, expservices, envelope, ezjscore, image alias, siteaccess, mobile app
type: help
---
# Mobile app developers glossary

The words used in this section, explained for app developers. Each entry links to the page that explains it in
full. The technical glossary of the whole documentation is in [Glossary](../../glossary.md).

## Contents

1. [Content](#content)
2. [The REST API](#the-rest-api)
3. [Credentials](#credentials)
4. [Remote services and ezjscore](#remote-services-and-ezjscore)
5. [Server terms you will hear](#server-terms-you-will-hear)

## Content

| Term | Meaning | Read |
|---|---|---|
| Content class | The type of an item and its list of fields, identified by `classIdentifier` (`folder`, `article`) | [Content classes for apps](content-modeling/content-classes-for-apps.md) |
| Content object | One item; `objectId` in REST answers | [How Exponential content reaches an app](how-exponential-content-reaches-an-app.md) |
| Node (location) | A place of an object in the content tree; `nodeId`. An object can have several, one of them the main node | same |
| Field (attribute) | One value of an object, of one datatype | [Fields and field values](rest-api/fields-and-field-values.md) |
| Datatype | The kind of a field: `ezstring`, `ezxmltext`, `ezimage` and others; `type` in a field answer | same |
| Remote id | A string id of an object or node that can be kept stable across installations; `objectRemoteId`, `nodeRemoteId` | [Node ids, remote ids and deep links](content-modeling/node-ids-remote-ids-and-deep-links.md) |
| Version | A saved state of an object; reads return the published one | [Drafts, preview and publishing](publishing-workflow/drafts-preview-and-publishing.md) |
| Locale | A language code such as `eng-US` or `ger-DE`, used by `Translation` and `languageLocale` | [Multilingual content](content-modeling/multilingual-content.md) |
| URL alias | The readable address of a node on the site; `fullUrl` in a REST answer | [Node ids, remote ids and deep links](content-modeling/node-ids-remote-ids-and-deep-links.md) |

## The REST API

| Term | Meaning | Read |
|---|---|---|
| `ezprestapi` | The extension that serves the REST content API at v1 and v2 | [REST API](rest-api/README.md) |
| Provider | The class that serves the routes of a prefix; `ezp7xRestApiProvider` for `ezp` and `ezpl` | [Routes reference](rest-api/routes-reference.md) |
| `ezp`, `ezpl` | The two prefixes of the content API: `/api/ezp/...` and its alias `/api/ezpl/...` | same |
| v1, v2 | The API versions in the path; reads at both, writes at v2 | [API versions v1 and v2](rest-api/api-versions-v1-and-v2.md) |
| Response group | A part of the answer you ask for with `ResponseGroups=` (`Metadata`, `Locations`, `Fields`, `FieldValues`) | [Response groups](rest-api/response-groups.md) |
| `OutputFormat=xhtml` | Asks for `renderedOutput`, the node rendered by a site template | [Rendered XHTML output](rest-api/rendered-xhtml-output.md) |
| `ezprestapiprovider` | The deprecated older v1 extension, now a shim on top of `ezprestapi` | [API versions v1 and v2](rest-api/api-versions-v1-and-v2.md) |
| Answer cache | The REST framework's server-side cache of `GET` answers, per user | [Caching on the server](offline-and-performance/caching-on-the-server.md) |

## Credentials

| Term | Meaning | Read |
|---|---|---|
| OAuth client | An application registered by an administrator in **Setup > oAuth admin**, with an identifier, a secret and a redirect address | [OAuth2 authorization code flow](auth/oauth2-authorization-code-flow.md) |
| Authorization code | The short-lived code the authorize page sends to the redirect address | same |
| Access token, refresh token | What the token endpoint answers; the access token lives `TokenTTL` seconds | same |
| `oauth/authorize` | The policy a user needs to allow an app | same |
| Personal API key | `expk_...`, made by a user for their own scripts, with scopes and a lifetime | [Personal API keys for tools](auth/personal-api-keys-for-tools.md) |
| Scope | What a personal API key may be used for: `read`, `publish`, `edit`, `remove` | same |
| expservices token | `expt_...`, a personal token for the remote services | [expservices tokens in apps](auth/expservices-tokens-in-apps.md) |
| Form token | The CSRF token a browser session needs for writes; not needed with a bearer token | [Writing through expservices](json-endpoints/writing-through-expservices.md) |

## Remote services and ezjscore

| Term | Meaning | Read |
|---|---|---|
| ezjscore | The extension whose `/ezjscore/call/` interface calls registered PHP functions and answers JSON | [ezjscore server functions for your app](json-endpoints/ezjscore-server-functions-for-your-app.md) |
| expservices | Over a thousand named services on top of ezjscore, called as `exp<domain>::<method>` | [Calling expservices from an app](json-endpoints/calling-expservices-from-an-app.md) |
| Envelope | The shape of every expservices answer: `{"ok":true,"data":...,"meta":...}` or `{"ok":false,"error":...}` | [The envelope](json-endpoints/calling-expservices-from-an-app.md#the-envelope) |
| Catalog | The list of every service with its arguments, from `expservices::catalog` | [Calling expservices from an app](json-endpoints/calling-expservices-from-an-app.md#discovering-services) |
| Image alias | A named size of an image (`small`, `medium`, `large` and the site's own), made on demand | [Image aliases for screen sizes](media-and-images/image-aliases-for-screen-sizes.md) |

## Server terms you will hear

| Term | Meaning | Read |
|---|---|---|
| Siteaccess | One configuration of the installation (a site, a language, the administration), chosen by host or path | [Multilingual content](content-modeling/multilingual-content.md) |
| Role and policy | What a user may do: a role holds policies such as `content/read`, limited to sections or subtrees | [Permissions for app users](publishing-workflow/permissions-for-app-users.md) |
| Velocity | Exponential's own application server; after configuration changes it is restarted | [Deploying with Velocity](../creators/app-creators/deploying-with-velocity.md) |
| Demo mode | A public demo that refuses administrator password changes | [Demo mode](../demo-mode.md) |
| Audit trail | The record of key and token events, refusals and writes | [Permissions for app users](publishing-workflow/permissions-for-app-users.md#auditing-app-activity) |

## Related pages

- [Mobile app developers FAQ](mobile-app-developers-faq.md)
- [Glossary](../../glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
