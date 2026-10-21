---
title: Syncing and offline reading
audience: mobile-app-developer, developer
category: mobile-apps
order: 630
summary: Keep part of an Exponential site readable on a device without a network - choosing what to sync, a full and an incremental sync with the list route and dateModified, expnode::modifiedSince and its limit (publish date), detecting removed items, queued writes with remote ids, and conflicts.
keywords: offline, sync, synchronization, incremental sync, dateModified, modifiedSince, removed items, 404, childrenCount, queue, offline writes, remote id, conflicts, mobile app
type: help
---
# Syncing and offline reading

This page is for app developers whose app must work offline: a field guide, a catalog for sales staff, a manual on a
ship. Exponential has no change feed for the whole tree, so the app syncs what it needs with the routes there are.
The design below is a suggestion built on those routes.

## Contents

1. [Choose what to sync](#choose-what-to-sync)
2. [The first full sync](#the-first-full-sync)
3. [Incremental syncs](#incremental-syncs)
4. [Removed items](#removed-items)
5. [Writes made offline](#writes-made-offline)

## Choose what to sync

Sync folders, not the site: one or a few subtrees the app shows, agreed with the site team by remote id
([Node ids, remote ids and deep links](../content-modeling/node-ids-remote-ids-and-deep-links.md)). Decide which image
alias to store per item, and leave originals out.

## The first full sync

For each synced folder:

1. Page through `GET /api/ezp/v2/content/node/<id>/list/offset/<n>/limit/50?ResponseGroups=Fields` until `offset`
   reaches `metadata.childrenCount` ([Listing children and pagination](../rest-api/listing-children-and-pagination.md)).
2. For each child that is itself a container the app shows, repeat.
3. Fetch one alias URL per image (`expimage::url`) and download the files.
4. Store `nodeId`, `dateModified` and the answer; remember when the sync started.

## Incremental syncs

| Way | How | Catches |
|---|---|---|
| Sort by modification | `.../list/offset/0/limit/50/sort/modified/desc`, stop at the first item with `dateModified` older than the last sync | new and changed direct children |
| `expnode::modifiedSince::<node id>::<timestamp or date>::<limit>::<offset>` | the remote services, below a node at any depth | items **published** since that time: the service filters on the publish date (`published >=`), so edits of older items are not included |
| Compare `childrenCount` | `GET .../childrenCount` | that something was added or removed, cheaply |

For changed items deeper in the tree, combine: `modifiedSince` for new items, and a modification-sorted list per
synced folder for edits.

## Removed items

A removed item simply disappears from lists. Detect it by:

- a `404` when the app reads a detail it had cached, then delete it locally;
- a `childrenCount` lower than the number of local children, then re-read the folder's node ids (a list without
  `Fields` is light) and delete the ones that are gone.

Items hidden from the app's user, or moved out of the synced folder, look the same as removed ones from the app's side.

## Writes made offline

- Queue writes on the device with everything needed to send them later.
- Give each queued create a `remote_id` made on the device, and send it with `expnode::create`: if the connection
  drops after the server published it, the retry answers `409` instead of creating a duplicate
  ([Writing through expservices](../json-endpoints/writing-through-expservices.md#create)).
- Send queued writes one at a time, in order, when the device is online; stop the queue on `401` and ask the user to
  sign in.
- Updates made offline can conflict with edits made on the site meanwhile. Compare the item's `dateModified` with the
  value the user saw before you send an update, and ask the user when they differ. The server publishes whatever is
  sent last.

## Related pages

- [Caching in the app](caching-in-the-app.md)
- [Caching on the server](caching-on-the-server.md)
- [What the APIs do not do](../what-the-apis-do-not-do.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
