---
title: Listing children and pagination
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 150
summary: List the items of a folder for a list screen through the REST API - the list route and its defaults, offset and limit paging, infinite scroll, sorting by name, date or priority, the order of the path segments, the childrenCount in the answer, and filtering by permission.
keywords: list, children, folder, pagination, paging, offset, limit, sort, asc, desc, published, modified, priority, name, childrenCount, infinite scroll, REST, mobile app
type: help
---
# Listing children and pagination

This page is for app developers who build a list screen: the news of a folder, the products of a category. It
explains the `list` route of the REST API, how to page through it and how to sort it.

## Contents

1. [The route](#the-route)
2. [The answer](#the-answer)
3. [Paging](#paging)
4. [Sorting](#sorting)
5. [Counting](#counting)
6. [What the list leaves out](#what-the-list-leaves-out)

## The route

```text
GET /api/ezp/<version>/content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc|desc))
```

Every part in brackets is optional, **but the order is fixed**: offset before limit, limit before sort. The route is
a regular expression (`ezpList` in `rest_provider.php`) and the controller's own comment says so: "you can't provide
sorting params before limit params". Defaults from the controller: offset `0`, limit `10`, sort order `asc`, and no
sort key (the order then follows the default of the content query).

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v1/content/node/<node id>/list/offset/0/limit/20?ResponseGroups=Fields"
```

This exact pattern is the call of an existing mobile app, recorded in the guide's comparison of answers before and
after 6.0.15 ([Activation combinations](../../../guides/rest-api-v1-and-v2.md#7-activation-combinations-before-and-since)).

## The answer

```json
{
  "childrenNodes": [
    {"objectName": "...", "classIdentifier": "article", "datePublished": 0, "dateModified": 0,
     "objectRemoteId": "...", "objectId": 0, "nodeId": 0, "nodeRemoteId": "...", "fullUrl": "https://example.com/...",
     "link": "https://example.com/api/ezp/v1/content/node/<id>", "fields": {"...": "..."}},
    "..."
  ],
  "metadata": {"childrenCount": 0, "parentNodeId": "<id>"},
  "requestedResponseGroups": ["Fields", "Metadata"]
}
```

The values are placeholders; the keys are those of `getChildrenList()` and `doList()`. `fields` appears only with
`ResponseGroups=Fields`. `link` is the REST address of the child at the same version, with the request's
`Translation` and `OutputFormat` parameters.

## Paging

`metadata.childrenCount` is the number of all children the query finds, not of this page. Loop like this:

```text
offset = 0, limit = 20
repeat:
    GET .../list/offset/{offset}/limit/{limit}
    show childrenNodes
    offset = offset + limit
until offset >= metadata.childrenCount
```

For infinite scroll, request the next page when the user nears the end of the loaded items, and stop when `offset`
reaches `childrenCount`. Offsets shift when editors publish or remove items between two requests; deduplicate by
`nodeId` in the app. The shipped configuration sets no maximum limit for this route; keep pages small (20 to 50) so
the first screen appears fast on mobile networks.

## Sorting

```text
.../list/offset/0/limit/20/sort/published/desc
.../list/limit/50/sort/name
```

The sort key is passed to the content tree fetch as its `SortBy` value. The examples in the controller use `name`
and `published`; the node sort names used by the same fetch elsewhere in the code (the expservices list in
`expcontentbase.php`) are `path`, `published`, `modified`, `section`, `depth`, `class_identifier`, `class_name`,
`priority`, `name`, `modified_subnode`, `node_id` and `contentobject_id`. To follow the order editors chose in the
administration, sort by `priority`, or ask a remote service for the node's sort settings (`expnode::sortInfo`).

## Counting

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/<node id>/childrenCount
```

Answers `metadata` with `childrenCount` and `parentNodeId`, for a badge or to size a list before loading it.

## What the list leaves out

- **Only direct children** (depth 1). For a whole subtree use `expnode::subtree` of the remote services.
- **No class filter.** Filter in the app, or use `expnode::children` with a filter, or `expnode::byClass`
  ([Calling expservices from an app](../json-endpoints/calling-expservices-from-an-app.md)).
- **Children the user may not read.** A list checks `content/read` of its parent, "the children are filtered by the
  fetch" ([Permissions of the REST interface](../../../guides/api-keys.md#32-permissions-of-the-rest-interface)).

## Related pages

- [Response groups](response-groups.md)
- [Caching in the app](../offline-and-performance/caching-in-the-app.md)
- [REST API v1 and v2: the routes](../../../guides/rest-api-v1-and-v2.md#2-the-routes)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
