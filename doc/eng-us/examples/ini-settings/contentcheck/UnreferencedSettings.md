---
title: contentcheck.ini [UnreferencedSettings]
audience: developer, admin
category: examples
summary: "The settings of [UnreferencedSettings] in contentcheck.ini: AssetClassList, StorageParentClassList, MaxTrashBatch, IndexTTL, BuildOnRequest, ScanChunkSize, CandidateLimit, ObjectColumns ..."
keywords: contentcheck.ini, UnreferencedSettings, INI, setting, AssetClassList, StorageParentClassList, MaxTrashBatch, IndexTTL, BuildOnRequest, ScanChunkSize, CandidateLimit, ObjectColumns, NodeColumns, NodeListColumns
type: generated
generated: exp:help:examples
---
# contentcheck.ini [UnreferencedSettings]

The block `[UnreferencedSettings]` of `settings/contentcheck.ini`, with 10 setting(s).

Never edit `settings/contentcheck.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/contentcheck.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/contentcheck.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/contentcheck.ini.append.php` in an extension (read when the extension is active).

## AssetClassList

> The classes the list covers: assets that only matter when something uses them. A page, a  
> folder, a user or a settings object is reached through the tree (navigation, menus,  
> search) whether or not anything links to it, so it is never called unreferenced.  

Default: a list of 8: `image`, `file`, `video`, `audio`, `flash`, `quicktime`, `windows_media`, `real_video`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
# an empty "AssetClassList[]" first replaces the shipped list; without it the items are added
AssetClassList[]
AssetClassList[]=image
AssetClassList[]=file
AssetClassList[]=video
AssetClassList[]=audio
AssetClassList[]=flash
AssetClassList[]=quicktime
AssetClassList[]=windows_media
AssetClassList[]=real_video
*/ ?>
```

## StorageParentClassList

> An asset is listed only when every location's parent is of one of these classes (a folder  
> that only stores it). Below any other parent (a gallery, an article, an event) the  
> parent's template shows it, so it counts as used.  

Default: a list of 1: `folder`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
# an empty "StorageParentClassList[]" first replaces the shipped list; without it the items are added
StorageParentClassList[]
StorageParentClassList[]=folder
*/ ?>
```

## MaxTrashBatch

> At most this many objects are moved to the trash in one confirmation (1 to 200)  

Default: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
MaxTrashBatch=50
*/ ?>
```

## IndexTTL

> The reference index: every id, remote id and path that fields, links, layouts, settings,  
> templates, roles and the tables below use. It is kept this many seconds (0 to 604800).  

Default: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
IndexTTL=600
*/ ?>
```

## BuildOnRequest

> true: a page builds the index when it is older than IndexTTL (the move to the trash always  
> builds it). false (very large installations): only bin/php/contentcheck.php --build-index  
> or the cronjob part cronjobs/contentcheck.php builds it; the list then shows the last  
> index, and the move to the trash needs one younger than IndexTTL.  

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
BuildOnRequest=true
*/ ?>
```

## ScanChunkSize

> Fields read per query while building the index (100 to 20000)  

Default: `2000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
ScanChunkSize=2000
*/ ?>
```

## CandidateLimit

> At most this many candidates (assets below the node) are judged for one list (100 to  
> 500000); with more, the list asks for a smaller subtree or fewer classes  

Default: `20000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
CandidateLimit=20000
*/ ?>
```

## ObjectColumns

> Table columns that hold an object id, a node id, or a list or path of node ids. A row there  
> keeps the object or node from being called unreferenced. A table or column that does not  
> exist is skipped, so the tables of extensions can be listed here.  
> the shop: baskets, wish lists and orders hold product collections  
> collected information (forms) belongs to its object  
> indexing and other pending actions  
> role assignments to users and groups  
> the newsletter: articles of editions, sends, tracked links, schedules, approvals, lists  

Default: a list of 14: `ezproductcollection_item.contentobject_id`, `ezinfocollection.contentobject_id`, `ezpending_actions.param`, `ezuser_role.contentobject_id`, `cjwnl_edition_article.contentobject_id`, `cjwnl_edition_send.edition_contentobject_id`, `cjwnl_edition_send.list_contentobject_id`, `cjwnl_link.contentobject_id`, `cjwnl_schedule.template_edition_contentobject_id`, `cjwnl_schedule.list_contentobject_id`, `cjwnl_approval.edition_contentobject_id`, `cjwnl_approval.list_contentobject_id` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
# an empty "ObjectColumns[]" first replaces the shipped list; without it the items are added
ObjectColumns[]
ObjectColumns[]=ezproductcollection_item.contentobject_id
ObjectColumns[]=ezinfocollection.contentobject_id
ObjectColumns[]=ezpending_actions.param
ObjectColumns[]=ezuser_role.contentobject_id
ObjectColumns[]=cjwnl_edition_article.contentobject_id
ObjectColumns[]=cjwnl_edition_send.edition_contentobject_id
ObjectColumns[]=cjwnl_edition_send.list_contentobject_id
ObjectColumns[]=cjwnl_link.contentobject_id
ObjectColumns[]=cjwnl_schedule.template_edition_contentobject_id
ObjectColumns[]=cjwnl_schedule.list_contentobject_id
ObjectColumns[]=cjwnl_approval.edition_contentobject_id
ObjectColumns[]=cjwnl_approval.list_contentobject_id
ObjectColumns[]=cjwnl_subscription.list_contentobject_id
ObjectColumns[]=cjwnl_article_pool.list_contentobject_id
*/ ?>
```

## NodeColumns

> subtree notifications and bookmarks  
> where drafts and pending versions will be placed  

Default: a list of 3: `ezsubtree_notification_rule.node_id`, `ezcontentbrowsebookmark.node_id`, `eznode_assignment.parent_node`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
# an empty "NodeColumns[]" first replaces the shipped list; without it the items are added
NodeColumns[]
NodeColumns[]=ezsubtree_notification_rule.node_id
NodeColumns[]=ezcontentbrowsebookmark.node_id
NodeColumns[]=eznode_assignment.parent_node
*/ ?>
```

## NodeListColumns

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[UnreferencedSettings]
# an empty "NodeListColumns[]" first replaces the shipped list; without it the items are added
NodeListColumns[]
*/ ?>
```

## Related

- [All blocks of contentcheck.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/contentcheck.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
