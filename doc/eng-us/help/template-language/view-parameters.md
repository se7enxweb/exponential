---
title: View parameters
audience: developer, designer, app-creator
category: development
order: 320
summary: The /(name)/value part of a URL - how eZURI parses it, how eZModule::run() maps it to a view's unordered parameters and UserParameters, what content/view turns it into ($view_parameters, one variable per name, content_info.offset), and how it changes the content view cache key - proven on a development installation.
keywords: view parameters, user parameters, $view_parameters, unordered_params, UserParameters, (offset), offset, pagination, eZURI, view cache key, namefilter
type: help
---
# View parameters

A URL such as `/content/view/full/144/(offset)/3/(color)/red` carries, after the view's own parameters, pairs of a
name in parentheses and a value. They are called user parameters (in PHP) or view parameters (in templates), and they
are how a page asks for "page 2" or "only the red items" without a query string. This page follows them from the URL
to the template.

## Contents

1. [Parsing the URL](#parsing-the-url)
2. [From the URL to the view](#from-the-url-to-the-view)
3. [What content/view makes of them](#what-contentview-makes-of-them)
4. [Using them in a template](#using-them-in-a-template)
5. [Building links with them](#building-links-with-them)
6. [View parameters and the view cache](#view-parameters-and-the-view-cache)
7. [Related pages](#related-pages)

## Parsing the URL

`eZURI` (`lib/ezutils/classes/ezuri.php`) splits the URI. An element that matches `^\([a-zA-Z0-9_]+\)` starts a
parameter; the next element is its value; further elements up to the next parameter are appended to the value with
`/`. The kernel passes the result, `$this->uri->userParameters()`, to the module (`ezpKernelWeb`). A read-only CLI
script on a development installation parsed some sample URIs with `new eZURI( $uri )`:

```text
content/view/full/96/(offset)/10
  elements:        content/view/full/96
  user parameters: {"offset":"10"}
content/view/full/96/(offset)/10/(color)/red
  elements:        content/view/full/96
  user parameters: {"offset":"10","color":"red"}
content/view/full/96/(tag)/strength/training
  elements:        content/view/full/96
  user parameters: {"tag":"strength\/training"}
content/view/full/96/(empty)
  elements:        content/view/full/96
  user parameters: {"empty":""}
content/view/full/96/(bad-name)/x
  elements:        content/view/full/96/(bad-name)/x
  user parameters: []
```

- The parameter part is removed from the path the module sees (`elements`).
- A value may contain slashes: `(tag)/strength/training` gives `strength/training`.
- A name without a value gives an empty string.
- A name with a character outside `a-z`, `A-Z`, `0-9` and `_` (here a hyphen) is not a parameter at all; it stays in
  the path and the view sees it as a positional parameter.

## From the URL to the view

A view declares in `module.php` which names it understands as **unordered parameters**; `content/view` in
`kernel/content/module.php`:

```php
$ViewList['view'] = array(
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart',
    'script' => 'view.php',
    'params' => array( 'ViewMode', 'NodeID' ),
    'unordered_params' => array( 'language' => 'Language',
                                 'offset' => 'Offset',
                                 'year' => 'Year',
                                 'month' => 'Month',
                                 'day' => 'Day' ) );
```

`eZModule::run()` (`lib/ezutils/classes/ezmodule.php`) then:

1. sets every unordered parameter to `false`, or to the value that follows its name in the **path**: an unordered
   name also works without parentheses, as in `/content/view/full/96/offset/20`;
2. copies every user parameter (the parenthesized ones) into `$Params['UserParameters']`, and, when its name is an
   unordered parameter, also into that variable (`(offset)/10` sets `$Params['Offset']`).

## What content/view makes of them

`content/view` (`kernel/private/classes/views/content/view.php`) casts `Offset`, `Year`, `Month` and `Day` to
integers, then builds the view parameters:

```php
$viewParameters = array(
    'offset' => $Offset,
    'year' => $Year,
    'month' => $Month,
    'day' => $Day,
    'namefilter' => false,
    '_custom' => $UserParameters
);
$viewParameters = array_merge( $viewParameters, $UserParameters );
```

Running the view on a development installation read-only (as on [Module view templates](module-view-templates.md)) showed the effect.
With the user parameters `offset=10` and `color=red`:

```text

content/view/full/96 user parameters {"offset":"10","color":"red"}
exit status: 1 (STATUS_OK)
$Result: array(11)
  content: string (7085 bytes)
  view_parameters: array(6)
    offset: '10'
    year: false
    month: false
    day: false
    namefilter: false
    color: 'red'
...
  content_info: array(25)
    ...
    offset: 10
```

and with `/offset/20` in the path instead:

```text
content/view/full/96/offset/20
$Result: array(11)
  content: string (7085 bytes)
  view_parameters: array(5)
    offset: 20
...
  content_info: array(25)
    ...
    offset: 20
```

Two things follow from the `array_merge()`:

- Every user parameter is in `$view_parameters`, whatever its name (`color` above), and a user parameter overrides
  the cast value: `(offset)/10` leaves the **string** `'10'` in `$view_parameters.offset`, while
  `content_info.offset` holds the integer. Cast with `|int` in templates.
- `_custom` is removed again before rendering, and each of its entries becomes a template variable of its own
  (`eZNodeviewfunctions::generateNodeViewData()`), so the node template also has `$color`.

## Using them in a template

Paging through the children of a node, with the hash that `content/view` builds for
`/content/view/full/144/(offset)/3/(color)/red` defined by hand (in a real node template the kernel sets it):

```tpl
{* the hash content/view sets for /content/view/full/144/(offset)/3/(color)/red *}
{def $view_parameters = hash( 'offset', '3', 'year', false(), 'month', false(), 'day', false(), 'namefilter', false(), 'color', 'red' )
     $node   = fetch( 'content', 'node', hash( 'node_id', 144 ) )
     $limit  = 3
     $offset = first_set( $view_parameters.offset, 0 )|int
     $total  = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )}
{$node.name|wash}: {$total} children, page {sum( div( $offset, $limit )|floor, 1 )} of {div( $total, $limit )|ceil}

{foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'offset', $offset, 'limit', $limit, 'sort_by', $node.sort_array ) ) as $child}
- {$child.name|wash}

{/foreach}
color: {first_set( $view_parameters.color, 'none' )}
```

Output:

```text
Recipes: 18 children, page 2 of 6
- Low Carb Almond Breakfast Muffins
- Overnight Oatmeal
- Hearty Vegetable Soup
color: red
```

`first_set()` gives a default when the parameter is missing; `|int` turns the string into a number. The designer
page [Pagination](../designers/templates/pagination.md) shows the navigator template that builds the page links.

## Building links with them

Append the pairs to the node's URL alias before `ezurl`:

```tpl
<a href={concat( $node.url_alias, '/(offset)/', sum( $offset, $limit ) )|ezurl}>Next</a>
```

Not run on a development installation: a link only shows its use in a browser; the parsing side is proven above. Use names made of
letters, digits and underscores only, or `eZURI` will not see them as parameters.

## View parameters and the view cache

`eZNodeviewfunctions::generateViewCacheFile()` adds the view parameters to the cache key of the content view cache:
for each parameter, sorted by name and skipping empty names and `_custom`, it appends `vp:<name>=<value>`. Every
distinct combination therefore gets its own cache file, which is what makes `(offset)` paging work with the view
cache on, and also means a crawler that invents parameters creates cache files. The tweak
`ignore_viewparameters` in `site.ini [ContentSettings] ViewCacheTweaks[<node id>]` or `ViewCacheTweaks[global]` leaves them out
of the key, for nodes whose templates
never read them.

## Related pages

- [Module view templates](module-view-templates.md)
- [Module result reference](module-result-reference.md)
- [Pagination](../designers/templates/pagination.md)
- [Template caches](template-caches.md)
- [Module definition reference](../creators/app-creators/module-definition-reference.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
