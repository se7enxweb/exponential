---
title: "New in 6.0.15: the REST API updates content"
audience: developer, app-creator, mobile-app-developer, integrator, cms-developer, php-developer, admin, government, press
category: getting-started
order: 4
summary: With Exponential 6.0.15 and ezprestapi 1.2.9 the REST API creates, updates and removes content through the kernel's own content writer - an update publishes a new version, values go as form fields or JSON, files come from public addresses, and nxc_powercontent is no longer needed. What changed in ezprestapi 1.2.1 to 1.2.9, and where to start.
keywords: REST API, new in 6.0.15, what's new, ezprestapi, 1.2.9, update content, PUT, JSON, expContentWriter, nxc_powercontent, v1, v2, permissions, API keys, OAuth2, headless, mobile app, integration, open data, announcement
type: help
---
# New in 6.0.15: the REST API updates content

Exponential 6.0.15 with `ezprestapi` 1.2.9 closes the gap that integrators and app developers ran into most: the REST
API can now **update** content, not only create and remove it, and every write goes through the kernel itself. The
extension `nxc_powercontent`, which the REST writes needed until now, is no longer required.

## What you'll have at the end

An overview of what the REST API does since 6.0.15, what changed release by release, and the page to read next.

## What it brings

- **Updates over REST**: `POST` or `PUT /api/ezp/v2/content/node/<node id>` publishes a new version of the node's
  object with the values sent. The other attributes keep their values, the old version stays in the history, and a
  language the object does not have yet adds that translation.
- **JSON bodies**: create and update take a JSON object (`Content-Type: application/json`) as well as form fields.
- **Values as an editor would give them**: rich text as HTML, an image or file as a public `https` address,
  everything else in its text form.
- **The kernel writes**: create, update and delete go through the kernel's
  [expContentWriter](../features/6.0/content-writer.md) and the `content/publish` operation, so workflows (an approval,
  for example), caches, the search index and notifications run as for an edit in the administration. A version a
  workflow holds is answered with `202`.
- **One extension fewer**: `nxc_powercontent` is no longer required; Composer removes it on the update when nothing else
  requires it.
- **Safer file values**: a file is downloaded only from a public address. A path on the server and an address in a
  private network are refused, so a REST caller cannot make the server read its own files or reach internal services.
- **Permissions on every call** (since 1.2.5): reads and writes check the user's policies, whatever the request was
  authenticated with.

The update in one call:

```bash
curl -s -X PUT -H "Authorization: Bearer $EXP_API_KEY" -H "Content-Type: application/json" \
     -d '{"languageLocale":"eng-US","title":"Opening moved to eleven","intro":"<p>Doors open at <strong>eleven</strong>.</p>"}' \
     https://www.example.com/api/ezp/v2/content/node/<node id>
# {"message":"Updated","objectId":<object id>,"nodeId":<node id>,"version":<version>,"languageLocale":"eng-US"}
```

## What changed, release by release

| ezprestapi | Date | What changed |
|---|---|---|
| 1.2.1 | 28 September 2026 | The extension states its version, license (GNU General Public License v2.0 or later) and website in `extension.xml` and `ezinfo.php`; `info()` is static. |
| 1.2.2 | 30 September 2026 | The about page names the product Exponential. |
| 1.2.3, 1.2.4 | 2 October 2026 | The command line scripts, cronjob parts and module views are classes; the commands describe themselves and start through the shared command helpers. |
| 1.2.5 | 6 October 2026 | **Permission checks on every read and write**: `content/read`, `content/create` of the class below the parent in the language, `content/edit`, `content/remove` of every location; a refusal answers `403`. **Reads answer at v1 again**, for existing clients such as mobile apps. Create answers `201` with `objectId` and `nodeId`; delete takes `DELETE` as well as `POST` and answers `200`. |
| 1.2.6 | 16 October 2026 | **REST API v1 and v2 in one extension**: one activation, one provider (`ezp7xRestApiProvider`) for the prefixes `ezp` and `ezpl`, one OAuth2 setup; `ezprestapiprovider` becomes a deprecated compatibility shim. `OutputFormat=xhtml` renders again. The v1 answers stay byte for byte the same. |
| 1.2.7 | 21 October 2026 | The children list (`/content/node/<id>/list/...`) answers again on installations without `ezprestapiprovider`, which includes every new installation; it answered `500` before. |
| 1.2.8 | 29 October 2026 | One copyright notice, Exponential Foundation and 7x. |
| 1.2.9 | 12 November 2026 | **Updates over REST v2**, JSON bodies, writes through the kernel's `expContentWriter`, file values from public addresses only, `nxc_powercontent` no longer required. |

What the kernel of 6.0.15 adds around it:

- the permission helper `expRestContentPermission` the checks of 1.2.5 use, and routes that answer at more than one
  version (needed for v1 and v2 in one provider);
- [personal API keys](../guides/api-keys.md) with scopes (`read`, `publish`, `edit`, `remove`);
- [OAuth token hardening](../bc/6.0/oauth-token-hardening.md) and [PKCE](../bc/6.0/oauth-pkce.md);
- [CORS for the REST API](../bc/6.0/rest-cors.md), off by default;
- a `404` for an address that names no REST provider on an installation without `ezprestapi`, where it ended in a
  fatal error loop and a `500` before.

## Where to start

| You want to | Read |
|---|---|
| Call the API: routes, authentication, curl examples | [REST API v1 and v2](../guides/rest-api-v1-and-v2.md) |
| Update content over REST: answers and values | [Updating and the values of a write](../guides/rest-api-v1-and-v2.md#21-updating-and-the-values-of-a-write) |
| Upgrade an installation and remove `nxc_powercontent` | [Upgrade notes: the REST writes through the kernel](../bc/6.0/content-writer-and-rest-writes.md) |
| Write content from your own PHP code the same way | [Writing content from PHP with expContentWriter](../guides/writing-content-from-php.md) |
| Build an app on it | [Creating and removing content from an app](mobile-app-developers/rest-api/creating-and-removing-content.md) |
| Connect another system | [The REST API for integrators](integrators/rest-api-for-integrators.md) |
| Give a tool a key with only the rights it needs | [Personal API keys](../guides/api-keys.md) |
| Know every route and setting of the extension | [ezprestapi](../features/6.0/extensions/ezprestapi.md) |

## Upgrading

Update the extension with `composer update se7enxweb/ezprestapi` together with the kernel 6.0.15, clear the caches and
reload PHP. Existing clients keep working: the routes, the answers of reads and the authentication are unchanged. Two
things a client may notice: a file value that is a path on the server is now refused with `400`, and an attribute
identifier the class does not have is now refused with `400` instead of being ignored. Details:
[Upgrade notes: the REST writes through the kernel, nxc_powercontent no longer needed](../bc/6.0/content-writer-and-rest-writes.md).

## Next step

[REST API v1 and v2](../guides/rest-api-v1-and-v2.md).

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](partners/partners-start-here.md) and [How to apply](partners/how-to-apply.md).
<!-- exp-partner-block:end -->
