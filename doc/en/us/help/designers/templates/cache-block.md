---
title: Cache-block
audience: designer, developer
category: templates
order: 130
summary: The cache-block function in detail: keys, expiry (default 7200 seconds), ignore_content_expiry, subtree_expiry, what invalidates a block, safe keys for menus and user-dependent parts, and the mistakes that show one visitor's page to another.
keywords: cache-block, keys, expiry, ignore_content_expiry, subtree_expiry, template cache, cache keys, menu cache, user specific cache, DelayedCacheBlockCleanup, TemplateCache
type: help
---
# Cache-block

This page is for designers who make expensive parts of a template cheap. A `{cache-block}` stores the output of its
body and reuses it until its keys change or it expires. Implementation: `lib/eztemplate/classes/eztemplatecachefunction.php`
and `eztemplatecacheblock.php`.

## Contents

1. [Syntax and parameters](#syntax-and-parameters)
2. [Keys](#keys)
3. [Expiry](#expiry)
4. [Safe patterns](#safe-patterns)
5. [Mistakes](#mistakes)
6. [Settings and clearing](#settings-and-clearing)

## Syntax and parameters

```tpl
{cache-block keys=array( $module_result.uri, $current_user.role_id_list|implode( ',' ) ) expiry=3600}
    ...
{/cache-block}
```

| Parameter | Meaning |
|---|---|
| `keys` | a value or array; each different combination gets its own cached copy |
| `expiry` | lifetime in seconds; default 7200 (`DEFAULT_TTL`); `0` means no time limit |
| `ignore_content_expiry` | keep the copy when content is published (by default a publish expires cache-blocks) |
| `subtree_expiry` | expire when content below the given node (id or path) is published, instead of on every publish |

Without `keys`, the block's position in the template is the only key: one copy for every page and visitor.

## Keys

Every value that changes the output must be in the keys:

| The block shows | Add to keys |
|---|---|
| a menu marking the current page | `$module_result.uri` or the current node id |
| items depending on permissions | `$current_user.role_id_list\|implode( ',' )` and `$current_user.limited_assignment_value_list\|implode( ',' )` |
| the visitor's name or basket | `$current_user.contentobject_id` (or do not cache it) |
| language-dependent text | nothing extra when each language is its own siteaccess: the siteaccess name is part of every block's cache path |

`subtree_expiry` also implies `ignore_content_expiry`: such a block expires only for publishes below its node. The
simple design keys its whole frame by URI, basket state and user id; the admin pagelayout builds a user hash from
role and limitation lists.

## Expiry

- Default: two hours, and on every content publish.
- A footer that never changes with content: `ignore_content_expiry` and a long `expiry`.
- A menu of a section: `subtree_expiry` with the section's root node, so publishing elsewhere keeps it.

## Safe patterns

```tpl
{* a top menu: same for everyone with the same roles on the same page *}
{cache-block keys=array( $module_result.uri, $current_user.role_id_list|implode( ',' ) ) subtree_expiry=2}
    {include uri='design:pagelayout/menu.tpl'}
{/cache-block}
```

```tpl
{* a footer: same everywhere, changes rarely *}
{cache-block ignore_content_expiry expiry=86400}
    {include uri='design:pagelayout/footer.tpl'}
{/cache-block}
```

## Mistakes

- **User data without the user in the keys**: one visitor's name shown to others.
- **`csp_nonce()` inside a block**: the stored nonce is wrong for every later request, and the script is blocked.
- **A fetch inside the block whose result is used outside it**: on a cache hit, the fetch does not run.
- **Keys with many distinct values** (full query strings, timestamps): one file per value, and no reuse. Cast or reduce
  them (the admin pagelayout casts its section filter to a number for this reason).
- **Nested cache-blocks**: work, but make invalidation hard to reason about; avoid them.

## Settings and clearing

- `site.ini [TemplateSettings] TemplateCache=enabled` turns cache-blocks on (disabled: never used).
- `DelayedCacheBlockCleanup` (default disabled) keeps expired subtree blocks on disk for a cronjob to remove.
- Clear: `php bin/php/ezcache.php --clear-id=template-block`.

## Related pages

- [Caching for designers](../theme-design/caching-for-designers.md)
- [Run-once](run-once.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
