---
title: Patterns in kernel/classes/subitems
audience: developer
category: standards
order: 507
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/subitems with its design patterns.
keywords: kernel/classes/subitems, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/subitems

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/subitems` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (24)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expSubitemsAttributeColumn` | class extends expSubitemsColumn | [Factory and static factory methods](factory.md) | `kernel/classes/subitems/expsubitemsattributecolumn.php:16` | An automatic subitems column for one class attribute, key attr:/. |
| `expSubitemsBuiltinColumn` | class extends expSubitemsColumn | – | `kernel/classes/subitems/expsubitemsbuiltincolumn.php:16` | One of the 15 built-in subitems columns (thumbnail, name, visibility, type, modifier, modified, published, translations, section, nodeid, noderemotei… |
| `expSubitemsCallableColumn` | class extends expSubitemsColumn | – | `kernel/classes/subitems/expsubitemscallablecolumn.php:14` | A subitems column whose value comes from a PHP callable named by Handler=class::method in its [Column_] block. |
| `expSubitemsColumn` | abstract class | [Template Method / abstract base class](template-method.md) | `kernel/classes/subitems/expsubitemscolumn.php:19` | The base class of a column of the admin subitems list ("Table options"). |
| `expSubitemsColumnHandlers` | class | [Utility (static helper)](utility.md) | `kernel/classes/subitems/columns/expsubitemscolumnhandlers.php:16` | Example Handler= columns of the admin subitems list: plain static methods an INI block names as Handler=expSubitemsColumnHandlers::. |
| `expSubitemsColumnRegistry` | class | [Registry](registry.md), [Singleton / instance()](singleton.md) | `kernel/classes/subitems/expsubitemscolumnregistry.php:23` | The registry of the admin subitems list columns ("Table options"). |
| `expSubitemsCSVExport` | class | [Utility (static helper)](utility.md) | `kernel/classes/subitems/expsubitemscsvexport.php:14` | The CSV of the admin subitems list (content/subitemsexport/). |
| `expSubitemsDateColumn` | class extends expSubitemsFieldColumn | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/subitems/columns/expsubitemsdatecolumn.php:16` | Subitems list columns about when things happened, in the forms the built-in Published and Modified columns do not show: relative ("3 days ago"), in d… |
| `expSubitemsFieldColumn` | abstract class extends expSubitemsColumn | [Template Method / abstract base class](template-method.md) | `kernel/classes/subitems/columns/expsubitemsfieldcolumn.php:22` | The common base of the shipped column families of the admin subitems list. |
| `expSubitemsMediaColumn` | class extends expSubitemsFieldColumn | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/subitems/columns/expsubitemsmediacolumn.php:20` | Subitems list columns about the images and files of the content object. |
| `expSubitemsNodeColumn` | class extends expSubitemsFieldColumn | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/subitems/columns/expsubitemsnodecolumn.php:18` | Subitems list columns read from the location (the ezcontentobject_tree row) and its children. |
| `expSubitemsObjectColumn` | class extends expSubitemsFieldColumn | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/subitems/columns/expsubitemsobjectcolumn.php:18` | Subitems list columns read from the content object: class, section, owner, languages, states and the size of its main text. |
| `expSubitemsPermissionColumn` | class extends expSubitemsFieldColumn | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/subitems/columns/expsubitemspermissioncolumn.php:18` | Subitems list columns that tell what the current user may do with each item, and technical facts about it. |
| `expSubitemsPreference` | class | [Utility (static helper)](utility.md) | `kernel/classes/subitems/expsubitemspreference.php:20` | The per-user choice of the admin subitems list: visible columns in order and the chosen preset per navigation part, the page size, and the user's own… |
| `expSubitemsReadingTimeColumn` | class extends expSubitemsColumn | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/subitems/columns/expsubitemsreadingtimecolumn.php:14` | The "Reading time" column of the admin subitems list: the minutes a reader needs for the item's main text, at WordsPerMinute= words a minute (default… |
| `expSubitemsRelationColumn` | class extends expSubitemsFieldColumn | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/subitems/columns/expsubitemsrelationcolumn.php:20` | Subitems list columns about what the object is related to, and what relates to it: object relations, tags (eztags) and ratings (ezstarrating). |
| `expSubitemsSearchWordLinkRow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/subitems/columns/expsubitemscolumntables.php:31` | A row of ezsearch_object_word_link: one indexed word position of an object. |
| `expSubitemsSEOColumn` | class extends expSubitemsFieldColumn | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/subitems/columns/expsubitemsseocolumn.php:32` | Subitems list columns for search engines and visitors: the page title in the browser, its length, and the meta title, description and keywords an xro… |
| `expSubitemsServerFunctions` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `kernel/classes/subitems/expsubitemsserverfunctions.php:24` | The ezjscore server functions of the admin subitems list ("Table options"), registered as [ezjscServer_expsubitems] in extension/ezjscore/settings/ez… |
| `expSubitemsTemplateColumn` | class extends expSubitemsColumn | – | `kernel/classes/subitems/expsubitemstemplatecolumn.php:14` | A subitems column rendered by a template named by Template=design:subitems/columns/.tpl in its [Column_] block. |
| `expSubitemsURLColumn` | class extends expSubitemsFieldColumn | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/subitems/columns/expsubitemsurlcolumn.php:20` | Subitems list columns about the addresses of a node: its URL alias, its system URL, the address visitors use on the public site, custom and historic… |
| `expSubitemsUserColumn` | class extends expSubitemsFieldColumn | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/subitems/columns/expsubitemsusercolumn.php:19` | Subitems list columns for user accounts: the rows below a user group. |
| `expSubitemsUserVisitRow` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/subitems/columns/expsubitemscolumntables.php:15` | A row of ezuservisit: a user's visits, logins and failed logins. |
| `expSubitemsVersionColumn` | class extends expSubitemsFieldColumn | [INI plugin point (extension point)](ini-plugin-point.md) | `kernel/classes/subitems/columns/expsubitemsversioncolumn.php:17` | Subitems list columns about the versions of the content object. |
