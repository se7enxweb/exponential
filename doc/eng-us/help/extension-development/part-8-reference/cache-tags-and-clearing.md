---
title: Cache tags, cache blocks and clearing caches
audience: developer
category: extension-development
order: 260
summary: The caches an extension author meets: ids and tags of ezcache.php and exp:cache, what to clear after which change, cache-block in templates, and clearing an object's view cache from PHP.
keywords: cache, ezcache.php, exp:cache, clear-tag, clear-id, ini, template, template-block, template-override, design_base, content cache, view cache, cache-block, eZContentCacheManager, clearContentCacheIfNeeded
type: help
---
# Cache tags, cache blocks and clearing caches

Part 8, reference 22. Exponential caches settings, compiled templates, rendered pages and more. When a change "does
not work", a cache is the usual reason. This page lists the caches by what you changed, and shows how your own code
clears caches correctly.

## The commands

```bash
php bin/php/ezcache.php --clear-tag=ini            # by tag: every cache with that tag
php bin/php/ezcache.php --clear-id=template,template-override
php bin/php/ezcache.php --clear-all
php bin/php/ezcache.php --list-tags
./console exp:cache status                          # every group, and the newer caches
./console exp:cache clear --tag=ini --dry-run       # what it would do
```

`exp:cache` does what the administration's cache page does, and also covers the static cache, the HTTP cache, the
SQL query cache and Velocity ([Cache control from the console](../../../bc/6.0/cache-console.md)). Add
`--allow-root-user` when running as root, but prefer running as the web server's user.

## The cache ids and their tags

From `kernel/classes/ezcache.php`:

| Id | Tags | Holds |
|---|---|---|
| `ini`, `global_ini` | ini | parsed settings |
| `active_extensions` | ini | the active extension list |
| `template` | template | compiled templates |
| `template-override` | template | the override rules' lookup |
| `template-block` | template, content | output of `cache-block` |
| `design_base` | template | the design chain |
| `content` | content | rendered content views |
| `exphttpcache` | content, template | the HTTP response cache |
| `querycache` | content, ini | the SQL query cache |
| `graphql` | content, ini | the answers of the GraphQL API's persisted queries and the class catalogues of its schema |
| `classid`, `sortkey`, `urlalias`, `content_tree_menu`, `state_limitations`, `content_language` | content | lookups about content |
| `translation`, `chartrans` | i18n | translations |
| `imagealias` | image | image variations |
| `user_info_cache` | user | user and role data |

## What to clear after what

| You changed | Clear |
|---|---|
| an INI file | `--clear-tag=ini` (the tag, not `--clear-id=ini`, which leaves the global INI cache) |
| activated an extension | `--clear-tag=ini`, then `--clear-id=design_base,template-override` and regenerate the autoloads |
| a template | usually nothing; `--clear-id=template` if compiled templates are cached |
| added a template file used by an override rule | `--clear-id=template-override` |
| a stylesheet or script in a packer list | `--clear-id=template-block` (the packed links are in cache blocks), then a hard reload |
| a translation | `--clear-tag=i18n` |
| a class or a policy | `--clear-tag=content` and `--clear-id=user_info_cache` |

## cache-block in templates

```text
{cache-block keys=array( $node.node_id, $current_user.contentobject_id ) expiry=600}
    {* expensive fetches and markup *}
{/cache-block}
```

| Parameter | Meaning |
|---|---|
| `keys` | what makes the content differ: the node, the user, the language. Leave nothing out, or one user sees another's block |
| `expiry` | seconds before it is built again |
| `subtree_expiry` | the block expires when content under that node is published |
| `ignore_content_expiry` | do not expire on every publish |

Never cache per-user content without the user in `keys`. The designer help explains the template side in detail
([cache-block](../../designers/templates/cache-block.md)).

## From your PHP code

When your code changes content through the kernel's functions, the kernel clears the view caches itself. When your
extension changes something that affects how an object is displayed (its own table that a template reads), clear that
object's caches:

```php
<?php
eZContentCacheManager::clearContentCacheIfNeeded( $objectID );
```

`clearContentCacheIfNeeded( $objectID, $versionNum = true, $additionalNodeList = false )` follows the site's
view cache settings (which related nodes to clear). For your own file caches under `var/`, delete or regenerate them
through the cluster file handler ([eZFile, eZDir and cluster file handlers](ezfile-ezdir-and-cluster-files.md)).

## Related pages

- Previous: [Sending mail with eZMail](ezmail.md)
- Next: [Built-in template operators](template-operators-reference.md)
- [Caching and templates](../part-2-builder/caching-and-templates.md)
- [Performance in extensions](../part-3-extension-developer/performance-in-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
