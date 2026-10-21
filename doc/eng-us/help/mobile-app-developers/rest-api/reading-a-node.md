---
title: Reading a node
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 130
summary: Read one item for a detail screen through the REST API - by node id or object id, the metadata keys, the links map, locations and fields on request, and how to follow links without building URLs by hand.
keywords: read node, read object, detail screen, metadata, objectName, classIdentifier, datePublished, dateModified, nodeId, fullUrl, links, locations, isMain, REST, mobile app
type: help
---
# Reading a node

This page is for app developers who build a detail screen: one article, one product, one event. It shows the read
routes for a single item, what the answer contains and how to ask for more.

## Contents

1. [By node or by object](#by-node-or-by-object)
2. [The metadata](#the-metadata)
3. [Links to the fields](#links-to-the-fields)
4. [Locations](#locations)
5. [A detail screen in one call](#a-detail-screen-in-one-call)

## By node or by object

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/<node id>
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/object/<object id>
```

Use the node route when you came from a list (lists give `nodeId`) or a link of the site. Use the object route when
you stored an object id, for example from a relation. Both run the same controller action, `viewContent`, and both
check `content/read` for the current user first: `403 access_denied` if the user may not read it, `404` if it does
not exist.

## The metadata

The `Metadata` response group is always included. The keys, from `ezpRestContentModel`:

| Key | Type | Meaning |
|---|---|---|
| `objectName` | string | The name of the object in the language read |
| `classIdentifier` | string | Its content class, for example `article` |
| `datePublished` | integer | First publication, Unix timestamp |
| `dateModified` | integer | Last change, Unix timestamp |
| `objectRemoteId` | string | The remote id of the object |
| `objectId` | integer | The object id |
| `nodeId` | integer | Node reads only: the node id |
| `nodeRemoteId` | string | Node reads only: the remote id of the node |
| `fullUrl` | string | Node reads only: the full address of the node's page on the site |

Use `dateModified` to decide whether a cached copy in the app is still current
([Caching in the app](../offline-and-performance/caching-in-the-app.md)), and `fullUrl` for a "share" or "open in
browser" button.

## Links to the fields

Every read has a `links` map: one entry per field identifier with the URL of that field, and `*` with the URL of all
fields. The URLs repeat the `Translation` and `OutputFormat` parameters of the request. Follow them instead of
building paths in the app; it keeps the app working if a path ever changes.

## Locations

An object can sit in several places of the tree. Ask for them with `?ResponseGroups=Locations`:

```json
"locations": [
  {"nodeId": 61, "nodeRemoteId": "...", "fullUrl": "https://example.com/news/launch", "isMain": true},
  {"nodeId": 142, "nodeRemoteId": "...", "fullUrl": "https://example.com/products/launch", "isMain": false}
]
```

(The ids and addresses are invented for the example; the keys are those of `getLocationsByContent()`.) Use the main
location for shared links, so every user shares the same address.

## A detail screen in one call

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/<node id>?ResponseGroups=Metadata,Fields"
```

This gives the metadata and every field in one answer, which is usually all a detail screen needs. Add
`Locations` only if the screen uses them. The field values are described in
[Fields and field values](fields-and-field-values.md); images and rich text need extra care
([Images in REST answers](../media-and-images/images-in-rest-answers.md),
[Rich text in apps](../content-modeling/rich-text-in-apps.md)).

## Related pages

- [Response groups](response-groups.md)
- [Listing children and pagination](listing-children-and-pagination.md)
- [How Exponential content reaches an app](../how-exponential-content-reaches-an-app.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
