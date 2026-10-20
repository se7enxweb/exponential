---
title: Policies of the content module
audience: developer, admin
category: examples
summary: The policy functions of the content module (26) and their limitations.
keywords: content, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Policies of the content module

The policy functions of `content` and their limitations, from `$FunctionList` in `kernel/content/module.php`. In the administration they are chosen under Roles and policies; a policy on `content/*` grants every function.

Some functions also take one limitation per object state group of the site, named `StateGroup_<group identifier>` (`eZContentObjectStateGroup::limitations()`, read from the database, so not listed below).

## bookmark

Checked by the views: [bookmark](../module-views/content/bookmark.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'bookmark' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'bookmark' ) )}
    ...
{/if}
```

## cleantrash

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'cleantrash' )` or the `user/has_access_to` fetch).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'cleantrash' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'cleantrash' ) )}
    ...
{/if}
```

## create

Checked by the views: [copysubtree](../module-views/content/copysubtree.md), [edit](../module-views/content/edit.md), [upload](../module-views/content/upload.md).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Language | `eZContentLanguage::fetchLimitationList()` |
| Node |  |
| ParentClass | `eZContentClass::fetchList()` |
| ParentDepth | `eZContentObjectTreeNode::parentDepthLimitationList()` |
| ParentGroup | fixed: Self (1) |
| ParentOwner | fixed: Self (1), Self or anonymous users per HTTP session (2) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'create', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'create' ) )}
    ...
{/if}
```

## dashboard

Checked by the views: [dashboard](../module-views/content/dashboard.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'dashboard' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'dashboard' ) )}
    ...
{/if}
```

## diff

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'diff' )` or the `user/has_access_to` fetch).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fixed: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'diff', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'diff' ) )}
    ...
{/if}
```

## edit

Checked by the views: [draft](../module-views/content/draft.md), [edit](../module-views/content/edit.md), [history](../module-views/content/history.md), [move](../module-views/content/move.md), [multiedit](../module-views/content/multiedit.md), [queued](../module-views/content/queued.md), [removeassignment](../module-views/content/removeassignment.md), [removenode](../module-views/content/removenode.md), [urlalias](../module-views/content/urlalias.md).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Group | fixed: Self (1) |
| Language | `eZContentLanguage::fetchLimitationList()` |
| Node |  |
| Owner | fixed: Self (1), Self or anonymous users per HTTP session (2) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'edit', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'edit' ) )}
    ...
{/if}
```

## hide

Checked by the views: [hide](../module-views/content/hide.md).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Group | fixed: Self (1) |
| Language | `eZContentLanguage::fetchLimitationList()` |
| Node |  |
| Owner | fixed: Self (1), Self or anonymous users per HTTP session (2) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'hide', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'hide' ) )}
    ...
{/if}
```

## jobs

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'jobs' )` or the `user/has_access_to` fetch).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'jobs' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'jobs' ) )}
    ...
{/if}
```

## manage_locations

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'manage_locations' )` or the `user/has_access_to` fetch).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Owner | fixed: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'manage_locations', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'manage_locations' ) )}
    ...
{/if}
```

## move

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'move' )` or the `user/has_access_to` fetch).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'move' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'move' ) )}
    ...
{/if}
```

## pdf

Checked by the views: [pdf](../module-views/content/pdf.md).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fixed: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'pdf', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'pdf' ) )}
    ...
{/if}
```

## pendinglist

Checked by the views: [pendinglist](../module-views/content/pendinglist.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'pendinglist' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'pendinglist' ) )}
    ...
{/if}
```

## publish

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'publish' )` or the `user/has_access_to` fetch).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Group | fixed: Self (1) |
| Language | `eZContentLanguage::fetchLimitationList()` |
| Node |  |
| Owner | fixed: Self (1), Self or anonymous users per HTTP session (2) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'publish', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'publish' ) )}
    ...
{/if}
```

## publish_without_notification

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'publish_without_notification' )` or the `user/has_access_to` fetch).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'publish_without_notification' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'publish_without_notification' ) )}
    ...
{/if}
```

## read

Checked by the views: [action](../module-views/content/action.md), [advancedsearch](../module-views/content/advancedsearch.md), [browse](../module-views/content/browse.md), [collectedinfo](../module-views/content/collectedinfo.md), [collectinformation](../module-views/content/collectinformation.md), [copy](../module-views/content/copy.md), [download](../module-views/content/download.md), [history](../module-views/content/history.md), [job](../module-views/content/job.md), [jobs](../module-views/content/jobs.md), [keyword](../module-views/content/keyword.md), [new](../module-views/content/new.md), [removeeditversion](../module-views/content/removeeditversion.md), [removemediaobject](../module-views/content/removemediaobject.md), [removeobject](../module-views/content/removeobject.md), [removeuserobject](../module-views/content/removeuserobject.md), [search](../module-views/content/search.md), [subitemsexport](../module-views/content/subitemsexport.md), [tipafriend](../module-views/content/tipafriend.md), [translation](../module-views/content/translation.md), [treemenu](../module-views/content/treemenu.md), [view](../module-views/content/view.md).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Group | fixed: Self (1) |
| Node |  |
| Owner | fixed: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'read', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'read' ) )}
    ...
{/if}
```

## remove

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'remove' )` or the `user/has_access_to` fetch).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fixed: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'remove', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'remove' ) )}
    ...
{/if}
```

## restore

Checked by the views: [restore](../module-views/content/restore.md), [trash](../module-views/content/trash.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'restore' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'restore' ) )}
    ...
{/if}
```

## reverserelatedlist

Checked by the views: [reverserelatedlist](../module-views/content/reverserelatedlist.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'reverserelatedlist' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'reverserelatedlist' ) )}
    ...
{/if}
```

## tipafriend

Checked by the views: [tipafriend](../module-views/content/tipafriend.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'tipafriend' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'tipafriend' ) )}
    ...
{/if}
```

## translate

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'translate' )` or the `user/has_access_to` fetch).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Language | `eZContentLanguage::fetchLimitationList()` |
| Node |  |
| Owner | fixed: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'translate', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'translate' ) )}
    ...
{/if}
```

## translations

Checked by the views: [translations](../module-views/content/translations.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'translations' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'translations' ) )}
    ...
{/if}
```

## urltranslator

Checked by the views: [urltranslator](../module-views/content/urltranslator.md), [urlwildcards](../module-views/content/urlwildcards.md).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'urltranslator' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'urltranslator' ) )}
    ...
{/if}
```

## versionread

Checked by the views: [versionview](../module-views/content/versionview.md).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fixed: Self (1) |
| Section | `eZSection::fetchList()` |
| Status | `eZContentObjectVersion::statusList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'versionread', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'versionread' ) )}
    ...
{/if}
```

## versionremove

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'versionremove' )` or the `user/has_access_to` fetch).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fixed: Self (1) |
| Section | `eZSection::fetchList()` |
| Status | `eZContentObjectVersion::statusList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'versionremove', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'versionremove' ) )}
    ...
{/if}
```

## view_embed

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'view_embed' )` or the `user/has_access_to` fetch).

| Limitation | Values come from |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fixed: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'view_embed', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'view_embed' ) )}
    ...
{/if}
```

## view_system_url

No view of the module names it; code checks it (`eZUser::hasAccessTo( 'content', 'view_system_url' )` or the `user/has_access_to` fetch).

No limitations: the policy is all or nothing.

```php
// Give a role the policy (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'view_system_url' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'view_system_url' ) )}
    ...
{/if}
```

## Related

- [All modules' policies](README.md)
- [The views of content](../module-views/content/README.md)
- [Roles and policies](../../guides/roles-and-policies.md)

---

Generated by `./console exp:help:examples` from `kernel/content/module.php`, `kernel/classes/ezrole.php`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
