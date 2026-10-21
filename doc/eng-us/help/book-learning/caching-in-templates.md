---
title: "Caching in templates: overall caches and cache blocks"
audience: builder, designer, developer
category: books
order: 430
summary: The settings that switch template compiling and the view cache on or off while you develop, how {cache-block} keys and expiry work, and the PHP operator list that adds simple operators.
keywords: cache, cache-block, keys, expiry, ignore_content_expiry, ViewCaching, TemplateCompile, TemplateCache, DevelopmentMode, PHPOperatorList, template.ini, Learning book
type: help
---
# Caching in templates: overall caches and cache blocks

## The overall caches

The main caches are controlled by settings in `site.ini`:

```ini
[TemplateSettings]
TemplateCompile=enabled
TemplateCache=enabled
DevelopmentMode=disabled

[ContentSettings]
ViewCaching=enabled
```

(`TemplateCompile` and `TemplateCache` handle the compiled templates; `ViewCaching` the content view cache.) While you develop
templates you want your edits at once, and the book suggests switching them off. Today the way to get that is
`DevelopmentMode=enabled` in `[TemplateSettings]` (on a development installation only) or clearing the smallest cache after
each change ([Which cache do I clear?](../manual/which-cache-do-i-clear.md)). The book's tip that saving a template in
the administration clears the right parts of the cache for you is not the case for template files on disk;
you clear the caches yourself. The whole subject is in [Caching and templates](../extension-development/part-2-builder/caching-and-templates.md).

## Cache blocks

A `{cache-block}` keeps the result of a part of a template as a file. The next request loads the file instead of running
the template code and the fetches inside it. It needs **keys** (what makes the output differ) and an **expiry**.

```
{cache-block keys=array( $module_result.node_id ) expiry=300}
    {* a comment list, rebuilt every five minutes *}
{/cache-block}
```

**Keys.** The system already adds the template file, the position of the block and the siteaccess to the key, so several
blocks in one template never collide. You add keys for what changes the output: for a view template at least the node ID,
and the user (or the user's role ID list, when many users share a few roles) if the output depends on who looks. A
navigation menu that is the same for everybody needs no keys.

**Expiry.** Without an `expiry` a block expires after two hours, or when anything is published. That is the default,
and it is why one publish empties every block. Two ways to change it:

```
{cache-block ignore_content_expiry}
    {* kept regardless of new content *}
{/cache-block}

{cache-block expiry=300}...{/cache-block}   {* seconds *}
```

Also available: `subtree_expiry` (expire when something under a given node changes). When a view template uses cache blocks you
generally want the view cache off for that view, which a template does by setting `cache_ttl` to 0.

Clearing: `php bin/php/ezcache.php --clear-id=template-block --allow-root-user` (or `./console exp:cache`, see its `--help`).
After a change to a template that sits in a block, remember to clear it, or the old output stays until the expiry.

## Custom operators from PHP functions

Plain PHP functions that take one argument can become template operators with a line in `template.ini`, without code of your
own:

```ini
[PHP]
PHPOperatorList[]
PHPOperatorList[lefttrim]=ltrim
PHPOperatorList[striptags]=strip_tags
```

The shipped file already lists `urlencode` and `urldecode`. The rule: one parameter, a return value (not by reference).
For real operators see [Creating an operator, step by step](../template-operators/creating-an-operator-step-by-step.md)
and [PHP function operators](../template-operators/php-function-operators.md).

Next: [overriding templates](overriding-templates.md).

## Source and license

Adapted from chapter 3, "Displaying Content with eZ publish Templates", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; syntax, operators, settings and examples updated to Exponential 6 (foreach, current settings, no inline style or script).
