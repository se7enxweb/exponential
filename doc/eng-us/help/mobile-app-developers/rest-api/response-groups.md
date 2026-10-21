---
title: Response groups
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 140
summary: Ask the REST API for exactly the parts of an answer a screen needs with the ResponseGroups parameter - the groups each route knows (Metadata, Locations, Fields, FieldValues), the defaults that are always added, both ways of writing the parameter, and the echo in requestedResponseGroups.
keywords: ResponseGroups, response groups, Metadata, Locations, Fields, FieldValues, requestedResponseGroups, default, payload size, REST, mobile app
type: help
---
# Response groups

This page is for app developers who want small answers on a slow mobile connection and complete answers where a
screen needs them. Response groups are the REST API's switch for that.

## Contents

1. [The groups per route](#the-groups-per-route)
2. [Writing the parameter](#writing-the-parameter)
3. [Defaults are always added](#defaults-are-always-added)
4. [Choosing groups for screens](#choosing-groups-for-screens)

## The groups per route

From the constants of `ezp7xRestContentController`:

| Route | Group | Adds | Default |
|---|---|---|---|
| `GET /content/node/<id>`, `GET /content/object/<id>` | `Metadata` | the `metadata` object | yes |
| same | `Locations` | the `locations` list | no |
| same | `Fields` | the `fields` map | no |
| `GET .../fields`, `GET .../field/<identifier>` | `FieldValues` | the `fields` map (or the one field) | yes |
| same | `Metadata` | the `metadata` object of the content | no |
| `GET /content/node/<id>/list...` | `Metadata` | the list `metadata`: `childrenCount`, `parentNodeId` | yes |
| same | `Fields` | a `fields` map in each child | no |
| `GET /content/node/<id>/childrenCount` | `Metadata` | `metadata` with `childrenCount` | yes |

Names are case-sensitive: `Fields`, not `fields`.

## Writing the parameter

Both forms are accepted by the request parser:

```text
?ResponseGroups=Metadata,Locations,Fields
?ResponseGroups[]=Metadata&ResponseGroups[]=Locations&ResponseGroups[]=Fields
```

The second form is what some HTTP libraries make from an array. Before 6.0.15 it ended in an error page; the parser
now accepts it (comment in `http_parser.php`).

## Defaults are always added

A route's default groups are included whether you name them or not: asking for `?ResponseGroups=Fields` on a node
read still gives `metadata`. The answer lists the groups that were applied in `requestedResponseGroups`, which is
useful when you debug a missing key.

## Choosing groups for screens

| Screen | Call | Why |
|---|---|---|
| A list of titles | `.../list/offset/0/limit/20` | metadata has `objectName`, `classIdentifier`, `nodeId`; no fields needed |
| A list of cards with a teaser and an image | `.../list/offset/0/limit/20?ResponseGroups=Fields` | every child carries its fields |
| A detail screen | `/content/node/<id>?ResponseGroups=Fields` | metadata and all fields in one call |
| A badge with a count | `/content/node/<id>/childrenCount` | one number, no items |
| Refreshing one value | `/content/node/<id>/field/<identifier>` | one field only |

`Fields` on a list returns every field of every child. For classes with many or large fields, measure the size of
the answer on a phone connection and consider reading details only when the user opens an item.

## Related pages

- [Reading a node](reading-a-node.md)
- [Listing children and pagination](listing-children-and-pagination.md)
- [Fields and field values](fields-and-field-values.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
