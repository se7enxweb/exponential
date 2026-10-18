---
title: Patterns in extension/AdminAid
audience: developer
category: standards
order: 400
summary: Every class, view, fetch function, operator and plugin point of extension/AdminAid with its design patterns.
keywords: extension/AdminAid, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/AdminAid

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/AdminAid` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (1)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ezAdminAidInfo` | class | [Value object / struct](value-object.md) | `extension/AdminAid/ezinfo.php:27` | – |

## Module views (6)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/aid/user_switch` | `user_switch.php` | user_switch | `extension/AdminAid/modules/aid/module.php:18` |
| `/aid/object_list` | `object_list.php` | object_view | `extension/AdminAid/modules/aid/module.php:19` |
| `/aid/object_view` | `object_view.php` | object_view | `extension/AdminAid/modules/aid/module.php:20` |
| `/aid/class_translation` | `class_translation.php` | class_translate | `extension/AdminAid/modules/aid/module.php:21` |
| `/aid/class_attribute_select` | `class_attribute_select.php` | class_translate | `extension/AdminAid/modules/aid/module.php:22` |
| `/aid/search` | `search.php` | search | `extension/AdminAid/modules/aid/module.php:23` |

## Policy functions (4)

| Function | Limitations | Defined in |
|---|---|---|
| `aid/user_switch` | – | `extension/AdminAid/modules/aid/module.php:27` |
| `aid/object_view` | – | `extension/AdminAid/modules/aid/module.php:28` |
| `aid/class_translate` | – | `extension/AdminAid/modules/aid/module.php:29` |
| `aid/search` | – | `extension/AdminAid/modules/aid/module.php:30` |

## INI plugin points (18)

| Setting | Class | Set in |
|---|---|---|
| `[SearchDatabase] ClassList[]` | `eZContentObject` | `extension/AdminAid/settings/aid.ini.append.php:26` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectTreeNode` | `extension/AdminAid/settings/aid.ini.append.php:30` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectAttribute` | `extension/AdminAid/settings/aid.ini.append.php:34` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectVersion` | `extension/AdminAid/settings/aid.ini.append.php:38` |
| `[SearchDatabase] ClassList[]` | `eZContentClass` | `extension/AdminAid/settings/aid.ini.append.php:41` |
| `[SearchDatabase] ClassList[]` | `eZContentClassAttribute` | `extension/AdminAid/settings/aid.ini.append.php:45` |
| `[SearchDatabase] ClassList[]` | `eZUser` | `extension/AdminAid/settings/aid.ini.append.php:49` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectState` | `extension/AdminAid/settings/aid.ini.append.php:53` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateGroup` | `extension/AdminAid/settings/aid.ini.append.php:57` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateLanguage` | `extension/AdminAid/settings/aid.ini.append.php:61` |
| `[SearchDatabase] ClassList[]` | `eZContentObjectStateGroupLanguage` | `extension/AdminAid/settings/aid.ini.append.php:65` |
| `[SearchDatabase] ClassList[]` | `eZContentLanguage` | `extension/AdminAid/settings/aid.ini.append.php:69` |
| `[SearchDatabase] ClassList[]` | `eZContentBrowseBookmark` | `extension/AdminAid/settings/aid.ini.append.php:73` |
| `[SearchDatabase] ClassList[]` | `eZImageFile` | `extension/AdminAid/settings/aid.ini.append.php:77` |
| `[SearchDatabase] ClassList[]` | `eZInformationCollectionAttribute` | `extension/AdminAid/settings/aid.ini.append.php:81` |
| `[SearchDatabase] ClassList[]` | `eZNodeAssignment` | `extension/AdminAid/settings/aid.ini.append.php:85` |
| `[SearchDatabase] ClassList[]` | `eZURL` | `extension/AdminAid/settings/aid.ini.append.php:88` |
| `[SearchDatabase] ClassList[]` | `eZWorkflow` | `extension/AdminAid/settings/aid.ini.append.php:96` |
