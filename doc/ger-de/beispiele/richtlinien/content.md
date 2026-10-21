---
title: Richtlinien des Moduls content
audience: developer, admin
category: examples
summary: Die Richtlinienfunktionen des Moduls content (26) und ihre Einschränkungen.
keywords: Richtlinie, Richtlinien, Rolle, Einschränkung, Berechtigung, content, policy, policies, role, limitation, permission
type: generated
generated: exp:help:examples
---
# Richtlinien des Moduls content

Die Richtlinienfunktionen von `content` und ihre Einschränkungen, aus `$FunctionList` in `kernel/content/module.php`. In der Administration werden sie unter Rollen und Richtlinien gewählt; eine Richtlinie auf `content/*` erlaubt jede Funktion.

Manche Funktionen nehmen außerdem eine Einschränkung pro Objektzustandsgruppe der Website, namens `StateGroup_<group identifier>` (`eZContentObjectStateGroup::limitations()`, aus der Datenbank gelesen und daher unten nicht aufgeführt).

## bookmark

Geprüft von den Ansichten: [bookmark](../modulansichten/content/bookmark.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'bookmark' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'bookmark' ) )}
    ...
{/if}
```

## cleantrash

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'cleantrash' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'cleantrash' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'cleantrash' ) )}
    ...
{/if}
```

## create

Geprüft von den Ansichten: [copysubtree](../modulansichten/content/copysubtree.md), [edit](../modulansichten/content/edit.md), [upload](../modulansichten/content/upload.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Language | `eZContentLanguage::fetchLimitationList()` |
| Node |  |
| ParentClass | `eZContentClass::fetchList()` |
| ParentDepth | `eZContentObjectTreeNode::parentDepthLimitationList()` |
| ParentGroup | fest: Self (1) |
| ParentOwner | fest: Self (1), Self or anonymous users per HTTP session (2) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'create', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'create' ) )}
    ...
{/if}
```

## dashboard

Geprüft von den Ansichten: [dashboard](../modulansichten/content/dashboard.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'dashboard' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'dashboard' ) )}
    ...
{/if}
```

## diff

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'diff' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fest: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'diff', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'diff' ) )}
    ...
{/if}
```

## edit

Geprüft von den Ansichten: [draft](../modulansichten/content/draft.md), [edit](../modulansichten/content/edit.md), [history](../modulansichten/content/history.md), [move](../modulansichten/content/move.md), [multiedit](../modulansichten/content/multiedit.md), [queued](../modulansichten/content/queued.md), [removeassignment](../modulansichten/content/removeassignment.md), [removenode](../modulansichten/content/removenode.md), [urlalias](../modulansichten/content/urlalias.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Group | fest: Self (1) |
| Language | `eZContentLanguage::fetchLimitationList()` |
| Node |  |
| Owner | fest: Self (1), Self or anonymous users per HTTP session (2) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'edit', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'edit' ) )}
    ...
{/if}
```

## hide

Geprüft von den Ansichten: [hide](../modulansichten/content/hide.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Group | fest: Self (1) |
| Language | `eZContentLanguage::fetchLimitationList()` |
| Node |  |
| Owner | fest: Self (1), Self or anonymous users per HTTP session (2) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'hide', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'hide' ) )}
    ...
{/if}
```

## jobs

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'jobs' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'jobs' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'jobs' ) )}
    ...
{/if}
```

## manage_locations

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'manage_locations' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Owner | fest: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'manage_locations', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'manage_locations' ) )}
    ...
{/if}
```

## move

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'move' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'move' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'move' ) )}
    ...
{/if}
```

## pdf

Geprüft von den Ansichten: [pdf](../modulansichten/content/pdf.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fest: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'pdf', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'pdf' ) )}
    ...
{/if}
```

## pendinglist

Geprüft von den Ansichten: [pendinglist](../modulansichten/content/pendinglist.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'pendinglist' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'pendinglist' ) )}
    ...
{/if}
```

## publish

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'publish' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Group | fest: Self (1) |
| Language | `eZContentLanguage::fetchLimitationList()` |
| Node |  |
| Owner | fest: Self (1), Self or anonymous users per HTTP session (2) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'publish', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'publish' ) )}
    ...
{/if}
```

## publish_without_notification

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'publish_without_notification' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'publish_without_notification' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'publish_without_notification' ) )}
    ...
{/if}
```

## read

Geprüft von den Ansichten: [action](../modulansichten/content/action.md), [advancedsearch](../modulansichten/content/advancedsearch.md), [browse](../modulansichten/content/browse.md), [collectedinfo](../modulansichten/content/collectedinfo.md), [collectinformation](../modulansichten/content/collectinformation.md), [copy](../modulansichten/content/copy.md), [download](../modulansichten/content/download.md), [history](../modulansichten/content/history.md), [job](../modulansichten/content/job.md), [jobs](../modulansichten/content/jobs.md), [keyword](../modulansichten/content/keyword.md), [new](../modulansichten/content/new.md), [removeeditversion](../modulansichten/content/removeeditversion.md), [removemediaobject](../modulansichten/content/removemediaobject.md), [removeobject](../modulansichten/content/removeobject.md), [removeuserobject](../modulansichten/content/removeuserobject.md), [search](../modulansichten/content/search.md), [subitemsexport](../modulansichten/content/subitemsexport.md), [tipafriend](../modulansichten/content/tipafriend.md), [translation](../modulansichten/content/translation.md), [treemenu](../modulansichten/content/treemenu.md), [view](../modulansichten/content/view.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Group | fest: Self (1) |
| Node |  |
| Owner | fest: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'read', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'read' ) )}
    ...
{/if}
```

## remove

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'remove' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fest: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'remove', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'remove' ) )}
    ...
{/if}
```

## restore

Geprüft von den Ansichten: [restore](../modulansichten/content/restore.md), [trash](../modulansichten/content/trash.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'restore' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'restore' ) )}
    ...
{/if}
```

## reverserelatedlist

Geprüft von den Ansichten: [reverserelatedlist](../modulansichten/content/reverserelatedlist.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'reverserelatedlist' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'reverserelatedlist' ) )}
    ...
{/if}
```

## tipafriend

Geprüft von den Ansichten: [tipafriend](../modulansichten/content/tipafriend.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'tipafriend' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'tipafriend' ) )}
    ...
{/if}
```

## translate

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'translate' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Language | `eZContentLanguage::fetchLimitationList()` |
| Node |  |
| Owner | fest: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'translate', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'translate' ) )}
    ...
{/if}
```

## translations

Geprüft von den Ansichten: [translations](../modulansichten/content/translations.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'translations' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'translations' ) )}
    ...
{/if}
```

## urltranslator

Geprüft von den Ansichten: [urltranslator](../modulansichten/content/urltranslator.md), [urlwildcards](../modulansichten/content/urlwildcards.md).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'urltranslator' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'urltranslator' ) )}
    ...
{/if}
```

## versionread

Geprüft von den Ansichten: [versionview](../modulansichten/content/versionview.md).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fest: Self (1) |
| Section | `eZSection::fetchList()` |
| Status | `eZContentObjectVersion::statusList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'versionread', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'versionread' ) )}
    ...
{/if}
```

## versionremove

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'versionremove' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fest: Self (1) |
| Section | `eZSection::fetchList()` |
| Status | `eZContentObjectVersion::statusList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'versionremove', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'versionremove' ) )}
    ...
{/if}
```

## view_embed

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'view_embed' )` oder der Fetch `user/has_access_to`).

| Einschränkung | Werte stammen aus |
|---|---|
| Class | `eZContentClass::fetchList()` |
| Node |  |
| Owner | fest: Self (1) |
| Section | `eZSection::fetchList()` |
| Subtree |  |

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'view_embed', array( 'Class' => array( /* values */ ) ) );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'view_embed' ) )}
    ...
{/if}
```

## view_system_url

Keine Ansicht des Moduls nennt sie; Code prüft sie (`eZUser::hasAccessTo( 'content', 'view_system_url' )` oder der Fetch `user/has_access_to`).

Keine Einschränkungen: Die Richtlinie gilt ganz oder gar nicht.

```php
// Einer Rolle die Richtlinie geben (eZRole::appendPolicy( $module, $function, $limitations ))
$role = eZRole::fetchByName( 'Editor' );
$role->appendPolicy( 'content', 'view_system_url' );
```

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'view_system_url' ) )}
    ...
{/if}
```

## Siehe auch

- [Die Richtlinien aller Module](README.md)
- [Die Ansichten von content](../modulansichten/content/README.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/content/module.php`, `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
