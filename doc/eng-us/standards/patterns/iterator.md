---
title: Iterator
audience: developer
category: standards
order: 333
summary: Walks over a collection without exposing how it is stored. 5 instances in Exponential.
keywords: design pattern, iterator, behavior, pattern catalogue
type: catalogue
---
# Iterator

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Walks over a collection without exposing how it is stored.

| | |
|---|---|
| Group | Behavior |
| Instances | 5 |
| Recognized by | Implements Iterator, IteratorAggregate or ArrayAccess. |

## Classes by area

### extension/sevenx_themes_media (1)

[All patterns of extension/sevenx_themes_media](area-extension-sevenx-themes-media.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sevenxThemesMediaField` | class | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php:3` | – | – |

### extension/xrowmetadata (1)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZClusterDirectoryIterator` | class | `extension/xrowmetadata/classes/ezclusterdirectoryiterator.php:3` | – | – |

### kernel/private (3)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpContentCriteriaSet` | class | `kernel/private/api/content/criteria_set.php:16` | This class handles a list of content query criteria | – |
| `ezpContentFieldSet` | class | `kernel/private/api/content/field_set.php:18` | Allows for reading/writing of content fields (attributes) through an array like interface. | – |
| `ezpContentList` | class | `kernel/private/api/content/list.php:18` | This class lets you browse a previously fetched content list, for instance using ezpContentRepository::query() It is more or less a ResultSet from a… | – |
