---
title: URL aliases and routing
audience: cms-developer
category: cms-development
order: 22
summary: For CMS developers, how an address reaches a page. It covers the module/view system URL, URL aliases made from the tree, the transformation settings, history aliases after moves, global aliases and wildcards for redirects, view parameters, building links in templates and PHP, and rebuilding aliases.
keywords: URL alias, routing, nice URLs, system URL, content/view/full, module, view, URLTranslator, TransformationGroup, WordSeparator, history alias, redirect, global URL alias, URL wildcard, view parameters, ezurl, eZURI, updateniceurls, verify_aliases, slug
type: help
---
# URL aliases and routing

This page is for developers who need to know how Exponential turns an address into a page, and how to keep
addresses stable. There are no route files to write for content: every node gets its address from the tree, and
every other page is a module view with a fixed pattern.

## Contents

1. [Two kinds of address](#two-kinds-of-address)
2. [How a request is resolved](#how-a-request-is-resolved)
3. [How aliases are made](#how-aliases-are-made)
4. [When content moves or is renamed](#when-content-moves-or-is-renamed)
5. [Global aliases and wildcards](#global-aliases-and-wildcards)
6. [View parameters](#view-parameters)
7. [Building links](#building-links)
8. [Rebuilding and checking aliases](#rebuilding-and-checking-aliases)
9. [Pages of your own](#pages-of-your-own)

## Two kinds of address

| Kind | Example | Comes from |
|---|---|---|
| System URL | `/content/view/full/61`, `/user/login`, `/content/search` | `<module>/<view>/<parameters>`, defined in each module's `module.php` |
| URL alias | `/news/opening-of-the-new-hall` | the node's path in the tree, one alias per language |

Both work for every node; aliases are what visitors and search engines see. The siteaccess part of an address
(`/ger/...` with URI matching) is taken off before either is looked up ([Siteaccesses and multisite](siteaccesses-and-multisite.md)).

## How a request is resolved

1. The siteaccess is chosen from the host, port or first path element.
2. If `site.ini [URLTranslator] Translation=enabled` (shipped), the rest of the path is looked up in the URL alias
   table (`ezurlalias_ml`, `kernel/classes/ezurlaliasml.php`); a match gives the system URL, for a node
   `content/view/full/<node id>`.
3. If no alias matches and `WildcardTranslation=enabled`, the URL wildcards are tried.
4. The module and view run with the parameters; `content/view` checks `content/read` and renders the node with its
   templates.

## How aliases are made

A node's alias is its parent's alias plus a segment made from the object's name (or the class's URL alias pattern).
The segment is transformed by `[URLTranslator]` settings:

| Setting | Shipped | Effect |
|---|---|---|
| `TransformationGroup` | `urlalias` | the character transformation rules for segments |
| `WordSeparator` | `dash` | spaces become `-` |
| `UrlAliasNameLimit` | `255` | the longest segment |

Two siblings with the same name get distinct segments (a number is added). Each translation of an object has its
own alias in its language, so the German page of a node can have a German address.

## When content moves or is renamed

Renaming an object or moving a node creates new aliases and keeps the old ones as **history** entries, which still
lead to the node. Links that editors made to content inside rich text point to node or object IDs, not addresses, so
they follow moves automatically ([Rich text](rich-text.md)). Old addresses from another system are not history: add
them as custom aliases or wildcards ([Migration checklist](../../install/migration/checklist.md)).

## Global aliases and wildcards

In the administration (**Setup** > **URL translator** and **Setup** > **URL wildcards**) administrators manage:

| Tool | Page | Use |
|---|---|---|
| Global URL alias | `/content/urltranslator` | a fixed address to a system URL or node, as a 301 redirect or shown under the alias |
| URL wildcard | `/content/urlwildcards` | a pattern such as `old-blog/*` to a destination with `{1}`, redirecting or direct |
| Custom alias on a node | the node's URL aliases tab | an extra address for one node in one language |

Every page and button is described in [Links, URL aliases and wildcards](../../guides/urls-and-aliases.md).

## View parameters

Any address can carry `/(name)/value` pairs: `/news/(offset)/10` pages a list, and your templates read
`$view_parameters.offset`. View parameters are part of the view cache key, so each value is cached separately
([View parameters](../template-language/view-parameters.md)). Use them for paging and simple filters, and keep the
values to a known set, so robots cannot fill the cache with endless variants.

## Building links

In templates, never write addresses by hand; let `ezurl` add the siteaccess and the index file:

```smarty
<a href={$node.url_alias|ezurl}>{$node.name|wash()}</a>
<a href={'/content/search'|ezurl}>Search</a>
<link rel="canonical" href={$node.object.main_node.url_alias|ezurl( 'double', 'full' )}>
```

`ezurl` quotes the value itself, so write no quotes around it ([URL operators](../designers/templates/url-operators.md)).
In PHP, `eZURI::build( '/content/view/full/2' )` gives the same path, and `eZURI::build( $url, true )` the absolute
URL ([Links, URL aliases and wildcards: for developers](../../guides/urls-and-aliases.md#for-developers)). Links
between several sites in one tree: [Siteaccesses and multisite](siteaccesses-and-multisite.md#links-between-sites).

## Rebuilding and checking aliases

After a large import or a change of transformation settings:

```bash
./console exp:updateniceurls --help --allow-root-user
./console exp:verify_aliases --help --allow-root-user
```

`updateniceurls` rebuilds the aliases of all nodes; `verify_aliases` checks the alias table and, with `--fix`,
repairs what is safe ([Content languages, section 10](../../guides/content-languages.md#10-commands-for-the-shell)).
Run both on a copy first on a large site.

## Pages of your own

A page that is not content (a dashboard, a form handler, a JSON endpoint) is a view of a module in your extension:
`/exp_mysite/report/(year)/2026` is module `exp_mysite`, view `report`. Modules declare their views, parameters and
policy functions in `module.php` ([Modules and views](../extension-development/part-3-extension-developer/modules-and-views.md)).
A global URL alias can give such a page a friendlier address.

## Related pages

- [Objects, nodes and locations](objects-nodes-and-locations.md)
- [SEO basics](seo-basics.md)
- [Safe redirects](../../features/6.0/safe-redirects.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
