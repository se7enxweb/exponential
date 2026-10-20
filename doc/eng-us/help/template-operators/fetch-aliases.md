---
title: Fetch aliases
audience: developer, designer, app-creator
category: development
order: 600
summary: What fetch_alias() does, the fetchalias.ini syntax (Module, FunctionName, Parameter[], Constant[]), every alias defined on alpha with what it maps to and an example rendered on alpha, how parameters and constants combine, and how to add your own or change an existing one from an extension.
keywords: fetch_alias, fetchalias.ini, fetch alias, Module, FunctionName, Parameter, Constant, children, children_count, subtree, object, by_identifier, news_list, comments, reviews, xrowextract, eZFunctionHandler::executeAlias
type: help
---
# Fetch aliases

`fetch_alias()` calls a fetch function by a short name defined in `fetchalias.ini`, with some of its parameters
fixed and others renamed. `{fetch_alias( 'children', hash( 'parent_node_id', 89 ) )}` is the same as
`{fetch( 'content', 'list', hash( 'parent_node_id', 89, ... ) )}` with the settings of the alias filled in. The
operator is part of `eZTemplateExecuteOperator` (`lib/eztemplate/classes/eztemplateexecuteoperator.php`, the
same class as `fetch`), and the work is done by `eZFunctionHandler::executeAlias()`
(`lib/ezutils/classes/ezfunctionhandler.php`).

## Contents

1. [Syntax](#syntax)
2. [fetchalias.ini](#fetchaliasini)
3. [How parameters and constants combine](#how-parameters-and-constants-combine)
4. [The aliases on alpha](#the-aliases-on-alpha)
5. [Adding your own alias](#adding-your-own-alias)
6. [Changing an existing alias](#changing-an-existing-alias)
7. [When to use an alias](#when-to-use-an-alias)

## Syntax

```tpl
{fetch_alias( alias_name, hash( key, value, ... ) )}
```

| Parameter | Type | Required | Default |
|---|---|---|---|
| `function_name` | string, the section name in `fetchalias.ini` | yes | |
| `function_parameters` | hash | no | `array()` |

It takes no piped input and returns what the fetch function returns (`null` when the alias does not exist).

## fetchalias.ini

Each alias is a section. The kernel's `settings/fetchalias.ini` documents the keys:

```ini
# [<parameter>]         # name of first parameter in fetch_alias
# Module=          # which module to use
# FunctionName=    # name of function defined in kernel/<module>/function_definition.php
# Parameter[key]=<value>  # key is the name of the parameter defined for the function in kernel/<module>/function_definition.php
#                         # value is the key of the hash entry in fetch_alias
# Constant[key]=<value>   # key is the name of the parameter defined for the function in kernel/<module>/function_definition.php
#                         # value is a constant defined in this ini file. use ; to separate elements if the value is supposed to be an array
```

- `Module` and `FunctionName` name the fetch function, exactly as the first two arguments of `fetch()`.
- `Parameter[<function parameter>]=<alias key>` takes the value of `<alias key>` from the hash you pass and gives it
  to `<function parameter>`. The two names are usually the same, but an alias can rename them: `[object]` maps the
  function's `object_id` to the key `id`.
- `Constant[<function parameter>]=<value>` fixes a value. When the function parameter is of type `array` in the
  module's `function_definition.php`, the value is split at `;` (write `\;` for a semicolon inside an item; empty
  items are dropped, `eZFunctionHandler::constantList()`): `Constant[sort_by]=published;0` becomes
  `array( 'published', '0' )`.

## How parameters and constants combine

`eZFunctionHandler::executeAlias()` builds the parameters of the fetch function in this order:

1. each `Parameter[]` entry: the value of its alias key from your hash, or `null` when you did not pass it (a
   `null` parameter then gets the function's default);
2. each `Constant[]` entry, unless your hash contains a key with the **function parameter's** name, which then
   wins over the constant;
3. every other key of your hash, passed through unchanged under its own name.

So you can pass any parameter of the fetch function to an alias, also ones the alias does not mention, and
override its constants. The `subtree` alias sorts by `priority;0`; a `sort_by` in the hash replaces that:

```tpl
{* subtree has Constant[sort_by]=priority;0 - a sort_by given to the alias replaces it *}
{foreach fetch_alias( 'subtree', hash( 'parent_node_id', 144, 'limit', 3, 'sort_by', array( 'name', true() ) ) ) as $item}
- {$item.name|wash}

{/foreach}
```

Output on alpha:

```text
- Almond French Toast
- Avocado Kale Pesto Grilled Cheese
- Gluten-Free Shrimp Pad Thai with Tofu
```

Compiled templates take a different path for `fetch_alias` calls whose alias name is a constant
(`eZTemplateExecuteOperator::fetchTransform()`): the call is turned into PHP at compile time, from the
function's parameter list. All examples on this page gave the same result interpreted and compiled on alpha. Keep
to the documented use (alias keys from `Parameter[]`, function parameter names for anything else) and the two paths
agree.

## The aliases on alpha

Alpha's `site` siteaccess has the eight aliases of the kernel's `settings/fetchalias.ini` and two from extension
xrowextract (`extension/xrowextract/settings/fetchalias.ini.append.php`).

| Alias | Fetch function | Your keys | Fixed |
|---|---|---|---|
| `object` | `content/object` | `id` (as `object_id`) | |
| `news_list` | `content/tree` | | `sort_by` published, descending; `class_id` 2; `parent_node_id` 2; `class_filter_type` include; `class_filter_array` 2; `limit` 10 |
| `by_identifier` | `content/object_by_attribute` | `attr_id` (as `identifier`) | |
| `comments` | `content/list` | `parent_node_id` | `sort_by` published, descending; only class `comment` |
| `children` | `content/list` | `parent_node_id`, `sort_by`, `offset`, `limit` | |
| `children_count` | `content/list_count` | `parent_node_id` | |
| `subtree` | `content/tree` | `parent_node_id`, `offset`, `limit` | `sort_by` priority, descending |
| `reviews` | `content/list` | `parent_node_id` | `sort_by` published, descending; only class `review` |
| `xrowextract_recent_content` | `content/tree` | `parent_node_id`, `limit` | `sort_by` modified, descending |
| `xrowextract_by_section` | `content/tree` | `parent_node_id`, `attribute_filter` | `sort_by` name, ascending |

The fetch functions' parameters are documented in [the content fetch functions](../../examples/fetch-functions/content/README.md).

### object

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $object = fetch_alias( 'object', hash( 'id', $node.contentobject_id ) )}
{$object.id}: {$object.name|wash} ({$object.class_identifier})
```

Output on alpha:

```text
87: Fit &amp; Healthy (ng_frontpage)
```

### children and children_count

```tpl
{def $children = fetch_alias( 'children', hash( 'parent_node_id', 89,
                                                 'sort_by', array( 'name', true() ),
                                                 'limit', 5 ) )}
{fetch_alias( 'children_count', hash( 'parent_node_id', 89 ) )} children, the first {$children|count} by name:
{foreach $children as $child}
- {$child.name|wash} ({$child.class_identifier})

{/foreach}
```

Output on alpha:

```text
16 children, the first 5 by name:- Authors (ng_container)
- Contact (ng_contact_form)
- Cookie Policy (ng_article)
- Fit &amp; Healthy site info - [SITEINFO] (ng_site_info)
- Fitness (ng_landing_page)
```

### subtree

Node 144 is the Recipes category; `subtree` goes to any depth, sorted by priority:

```tpl
{def $page = fetch_alias( 'subtree', hash( 'parent_node_id', 144, 'offset', 0, 'limit', 4 ) )}
{foreach $page as $item}
- {$item.name|wash} (depth {$item.depth})

{/foreach}
```

Output on alpha:

```text
- Gluten-Free Shrimp Pad Thai with Tofu (depth 4)
- Vegan Granola with Almonds, Walnuts, Cranberries, and Goji Berries (depth 4)
- Zucchini Zoodles with Ground Turkey Tomato Sauce (depth 4)
- Kale Salad (depth 4)
```

### news_list

`news_list` has its whole query fixed: articles of class 2 below node 2. On alpha class 2 exists, but the site's
articles are of class `ng_article`, so the alias finds nothing. It is an example from the original kernel settings,
not something to rely on; define your own instead ([below](#adding-your-own-alias)).

```tpl
{def $class = fetch( 'content', 'class', hash( 'class_id', 2 ) )
     $news = fetch_alias( 'news_list' )}
Class 2 is "{$class.identifier}"; news_list returned {$news|count} item(s).
{foreach $news as $item}
- {$item.name|wash}

{/foreach}
```

Output on alpha:

```text
Class 2 is "article"; news_list returned 0 item(s).
```

### by_identifier

`content/object_by_attribute` (`eZContentFunctionCollection::fetchObjectByAttribute()`) returns the object that has
a text line (`ezstring`) attribute whose sort key equals the value, through
`eZContentObjectAttribute::fetchByIdentifier()`. The sort key of a text line is its lowercased text:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )
     $found = fetch_alias( 'by_identifier', hash( 'attr_id', $node.name|downcase ) )}
Looked up "{$node.name|downcase|wash}": {if $found}object {$found.id}, {$found.name|wash}{else}nothing{/if}
```

Output on alpha:

```text
Looked up "privacy policy": object 72, Privacy policy
```

When several objects match, it returns one of them. It searches all text line attributes of all classes, so use
it only with values you know are unique.

### comments and reviews

Both list children of one class (`comment`, `review`) newest first. Alpha has neither class in use below node 89:

```tpl
{def $comments = fetch_alias( 'comments', hash( 'parent_node_id', 89 ) )
     $reviews = fetch_alias( 'reviews', hash( 'parent_node_id', 89 ) )}
comments: {$comments|count}, reviews: {$reviews|count}
```

Output on alpha:

```text
comments: 0, reviews: 0
```

### xrowextract_recent_content and xrowextract_by_section

Defined by extension xrowextract for its export view, and usable from any template on the siteaccesses where the
extension is active:

```tpl
{def $recent = fetch_alias( 'xrowextract_recent_content', hash( 'parent_node_id', 144, 'limit', 3 ) )}
{foreach $recent as $item}
- {$item.name|wash}

{/foreach}
```

Output on alpha:

```text
- Zucchini Zoodles with Ground Turkey Tomato Sauce
- Vibrant Guacamole
- Vegan Granola with Almonds, Walnuts, Cranberries, and Goji Berries
```

### An alias that does not exist

```tpl
[{fetch_alias( 'latest_news', hash( 'parent_node_id', 89 ) )}]
```

Output on alpha:

```text
[]
```

The result is empty. In an interpreted template `eZFunctionHandler::executeAlias()` writes the warning
`Could not execute. Function latest_news not found.` to the debug output; a compiled template logs the template
warning `Fetch alias 'latest_news' is not defined in fetchalias.ini` when it is compiled.

## Adding your own alias

Put the section in your extension's `settings/fetchalias.ini.append.php` (or a siteaccess's), never in the
kernel's `settings/fetchalias.ini`:

```ini
<?php /* #?ini charset="utf-8"?

[c11_latest_articles]
Module=content
FunctionName=list
Parameter[parent_node_id]=parent
Parameter[limit]=limit
Constant[sort_by]=published;0
Constant[class_filter_type]=include
Constant[class_filter_array]=ng_article

*/ ?>
```

The INI files of all active extensions are merged, so the alias exists on every siteaccess where the extension is
active. Clear the INI cache afterwards (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) and the
template cache (compiled templates contain the alias's resolved parameters):

```tpl
{foreach fetch_alias( 'c11_latest_articles', hash( 'parent', 89, 'limit', 3 ) ) as $article}
- {$article.name|wash}, published {$article.object.published|l10n( 'shortdate' )}

{/foreach}
```

Output on alpha:

```text
- Cookie Policy, published 01/25/2023
- Privacy Policy, published 01/25/2023
```

Not run with a file on alpha: the script that rendered this example added the section above to `fetchalias.ini`
in memory only, for its own process.

Name aliases with your extension's prefix: alias names share one INI namespace across all extensions.

## Changing an existing alias

An append that names an existing section is merged into it key by key; it does not replace the section. To
change the sort order of the kernel's `children` alias, an extension needs only:

```ini
[children]
Constant[sort_by]=name;1
```

`children` has no `Constant[sort_by]` of its own (it takes `sort_by` from your hash), so the constant applies
when the template does not pass `sort_by`. Without the change, `content/list` without a `sort_by` orders by the
nodes' path (`path_string` ascending, the default of `eZContentObjectTreeNode::createSortingSQLStrings()`):

```tpl
{foreach fetch_alias( 'children', hash( 'parent_node_id', 89, 'limit', 4 ) ) as $child}
- {$child.name|wash}

{/foreach}
```

Output on alpha:

```text
- Privacy Policy
- Cookie Policy
- Fitness
- Healthy eating
```

With it, the same template sorts by name:

```tpl
{foreach fetch_alias( 'children', hash( 'parent_node_id', 89, 'limit', 4 ) ) as $child}
- {$child.name|wash}

{/foreach}
```

Output on alpha:

```text
- Authors
- Contact
- Cookie Policy
- Fit &amp; Healthy site info - [SITEINFO]
```

(Rendered with the change made in memory, like the previous example.) The risk is the same as with any shared
setting: every template on the siteaccess that uses `children` now gets the new behavior, including templates of
other extensions. An array key (`Parameter[...]`, `Constant[...]`) cannot be removed by an append, only replaced;
a line `Constant[]` with empty brackets would empty the whole list. Prefer a new alias with your own name.

## When to use an alias

- For a query used in many templates of a site: one place to change its fixed parts.
- Not to hide expensive queries: an alias runs exactly the fetch it names, every time it is called (put the call in
  a `cache-block` when its result can be shared).
- Not for a query used once: `fetch()` with all parameters is easier to read.

## Related pages

- [Fetch functions: how they work](fetch-functions-how-they-work.md)
- [Execute operators](execute-operators.md): `fetch` and `fetch_alias` as operators
- [Fetch basics](../designers/templates/fetch-basics.md)
- [The fetch function reference](../../examples/fetch-functions/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
