---
title: How Exponential content reaches an app
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 10
summary: The content model explained for app developers - classes, objects, nodes, fields, versions and languages - and how each appears in the JSON of the REST API, with the real shape of a node answer and a list answer.
keywords: content model, class, object, node, location, field, attribute, version, language, JSON, metadata, childrenNodes, fullUrl, remote id, mobile app, explanation
type: help
---
# How Exponential content reaches an app

This page is for app developers who have never worked with Exponential and need a mental model before they write the
first request. It explains the handful of concepts the APIs expose and shows where each one appears in the JSON of
the REST API. The shapes below come from the `ezprestapi` content model class (`ezpRestContentModel`) and the
examples in [Personal API keys](../../guides/api-keys.md#3-using-a-key-with-curl).

## Contents

1. [Five concepts](#five-concepts)
2. [A node answer](#a-node-answer)
3. [A list answer](#a-list-answer)
4. [Fields and their values](#fields-and-their-values)
5. [What decides what you see](#what-decides-what-you-see)

## Five concepts

| Concept | What it is | In the REST JSON |
|---|---|---|
| Content class | The type of an item, with its fields: `folder`, `article`, `image`, or a class the site defines | `classIdentifier` |
| Content object | One item: one article, one image | `objectId`, `objectRemoteId`, `objectName` |
| Node (location) | Where an object sits in the content tree. An object can have several nodes; one is its main node | `nodeId`, `nodeRemoteId`, `fullUrl`, the `locations` list |
| Field (attribute) | One value of an object, of a datatype such as `ezstring`, `ezxmltext`, `ezimage` | the `fields` map, keyed by identifier |
| Version and language | Every publish makes a new version; each version can hold several translations | only the published version is read; `Translation=<locale>` picks a language |

Node 2 is the root of the content tree in a standard installation, which is why the examples in the documentation
read `/content/node/2`. Your app usually starts from a folder node that an editor tells you about, not from the root.
How to keep such ids stable across installations is in
[Node ids, remote ids and deep links](content-modeling/node-ids-remote-ids-and-deep-links.md).

## A node answer

`GET /api/ezp/v2/content/node/2` with a valid credential answers (from the guide, shortened):

```json
{"metadata":{"objectName":"Websites","classIdentifier":"folder","datePublished":1033917596,
 "dateModified":1791063800,"objectId":1,"nodeId":2,"fullUrl":"https:\/\/example.com\/websites"},"links":{...}}
```

The `metadata` of a node read holds `objectName`, `classIdentifier`, `datePublished`, `dateModified` (Unix
timestamps), `objectRemoteId`, `objectId`, and for a node also `nodeId`, `nodeRemoteId` and `fullUrl` (the address of
the page on the site). `links` maps every field identifier to the URL of that one field, plus `*` for all fields.
More is added only when you ask for it with response groups ([Response groups](rest-api/response-groups.md)).

## A list answer

`GET /api/ezp/v1/content/node/<node id>/list/offset/0/limit/20` answers with:

| Key | Holds |
|---|---|
| `childrenNodes` | one entry per child: the object metadata, `nodeId`, `nodeRemoteId`, `fullUrl`, and `link`, the REST address of the child |
| `metadata` | `childrenCount` (all children, not only this page) and `parentNodeId` |
| `requestedResponseGroups` | the response groups the server applied |

With `?ResponseGroups=Fields` every child also carries its `fields`. The details are in
[Listing children and pagination](rest-api/listing-children-and-pagination.md).

## Fields and their values

Each field is an object with `type` (the datatype string), `identifier`, `value`, `id` and `classattribute_id`. The
`value` is a string representation, with three special cases in the code:

| Datatype | `value` |
|---|---|
| `ezxmltext` (rich text) | the rendered text with all HTML tags removed |
| `ezimage` | the path of the original image file, without the alternative text |
| a datatype in `site.ini [ContentSettings] DatatypeBlackListForExternal[]` (`ezuser` by default) | `null` |
| any other datatype | what the datatype's `toString()` gives |

Read [Fields and field values](rest-api/fields-and-field-values.md) before you parse values, and
[Rich text in apps](content-modeling/rich-text-in-apps.md) if your app shows articles.

## What decides what you see

Every REST call runs as a user: the user of the OAuth token or API key, or the default user when authentication is
switched off. The content controller asks that user's policies on every read and write, the same way the content
module of the site does (`content/read`, `content/create`, `content/edit`, `content/remove`), through the kernel's
`expRestContentPermission` ([REST API v1 and v2](../../guides/rest-api-v1-and-v2.md#in-short)). A child the user may
not read is not in the list; a node the user may not read answers `403`. Plan the app's user accounts with that in
mind: [Permissions for app users](publishing-workflow/permissions-for-app-users.md).

## Related pages

- [Which API for your app](which-api-for-your-app.md)
- [Content classes for apps](content-modeling/content-classes-for-apps.md)
- [The content model and editing content](../../guides/content-model-and-editing.md)
- [Headless Exponential](../creators/app-creators/headless-exponential.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
