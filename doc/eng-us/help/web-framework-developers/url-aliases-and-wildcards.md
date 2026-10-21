---
title: "Pretty URLs: URL aliases and URL wildcards"
audience: web-framework-developer, developer
category: web-frameworks
order: 70
summary: How Exponential turns readable addresses into module addresses - automatic URL aliases for content nodes, global aliases for module views, wildcard patterns for redirects and rewrites, history redirects when content moves - compared with slugs, permalinks and redirect routes in other frameworks.
keywords: URL alias, nice URLs, slug, permalink, URL wildcard, redirect, rewrite, eZURLAliasML, eZURLWildcard, URLTranslator, updateniceurls, global alias, web framework developer
type: help
---
# Pretty URLs: URL aliases and URL wildcards

This page is for developers who would add a `slug` column and a route with a `{slug}` parameter. In Exponential you
do not: every content node has a **URL alias** built from the names of its path, and the kernel translates the alias
to `content/view/full/<node>` before routing ([The request lifecycle](the-request-lifecycle.md)). Two more mechanisms
cover what route tables usually do for redirects: **global aliases** and **URL wildcards**.

## Contents

1. [Three kinds of addresses](#three-kinds-of-addresses)
2. [Node aliases: slugs you never write](#node-aliases-slugs-you-never-write)
3. [Global aliases for module views](#global-aliases-for-module-views)
4. [URL wildcards](#url-wildcards)
5. [Using them in code](#using-them-in-code)
6. [Compared with other frameworks](#compared-with-other-frameworks)

## Three kinds of addresses

From [URL wildcards and custom aliases](../creators/website-creators/url-wildcards.md):

| Kind | Made where | Example |
|---|---|---|
| Node alias | automatically, and on the node's URL aliases tab | `/About-us` |
| Global alias | **Setup > URL translator** (`/content/urltranslator`) | `login` for `user/login` |
| Wildcard | **Setup > URL wildcards** (`/content/urlwildcards`) | `news/*` to `articles/{1}` |

## Node aliases: slugs you never write

- An alias is made from the object's name, or from the class's URL alias name pattern, transformed by
  `site.ini [URLTranslator] TransformationGroup` (`urlalias` shipped; `urlalias_iri` keeps letters such as accents).
- Each translation has its own alias in its language.
- When a node is renamed or moved, the new alias is created and the **old one stays as a redirect**: no redirect table
  to maintain.
- A node can have extra aliases (campaign addresses, an old site's address) on its URL aliases tab.
- After an import or a change of the transformation group, rebuild with `php bin/php/updateniceurls.php`.

Details: [URL aliases and nice URLs](../creators/website-creators/url-aliases-and-nice-urls.md). The table behind it is
handled by `eZURLAliasML` (`kernel/classes/ezurlaliasml.php`).

## Global aliases for module views

Your module views live at `/<module>/<view>`. A global alias gives one a friendlier address (`login` for
`user/login`), either as a **redirect** (301 to the destination) or **direct** (the visitor keeps the typed
address). Create them in the administration or ship them as part of your installation steps.

## URL wildcards

A wildcard is a pattern with `*` and a destination with `{1}`, `{2}` placeholders, again redirecting or direct:

| Pattern | Destination | `news/2026/october` becomes |
|---|---|---|
| `news/*` | `articles/{1}` | `articles/2026/october` |
| `old-blog/*` | `https://blog.example.org/{1}` | a redirect to the other site |

Wildcards are translated by `eZURLWildcard::translate()` (`kernel/classes/ezurlwildcard.php`), switched by
`site.ini [URLTranslator] WildcardTranslation` (`enabled` shipped), with `MaximumWildcardIterations` (20) to stop
loops. A URL alias that matches an address wins over a wildcard. **Try an address** on the wildcards page shows what an
address becomes without changing anything ([Links, URL aliases, wildcards](../../guides/urls-and-aliases.md#4-url-wildcards)).

## Using them in code

| Need | Use |
|---|---|
| Link to a node | `{$node.url_alias\|ezurl}` in templates, `eZURI::build( $node->attribute( 'url_alias' ) )` in PHP |
| Find a node by its alias | `eZContentObjectTreeNode::fetchByURLPath( 'about_us' )` ([The content API](../creators/app-creators/the-content-api.md#fetching-one-item)) |
| Never print a system address | rules can redirect or refuse `/content/view/full/<id>` typed by users ([Request rules](../../features/6.0/request-rules.md)) |

## Compared with other frameworks

| Framework habit | In Exponential |
|---|---|
| A `slug` field and a unique index | the URL alias, generated and kept per translation |
| A redirects table or package for moved pages | the history alias, made automatically |
| `Route::redirect()`, `RedirectView`, `redirect()` in routes | global alias (redirect) or wildcard |
| Catch-all route that looks up a path | the alias translation already does it, for content |
| Localized routes | one alias per translation; siteaccesses per language ([Siteaccesses](siteaccesses-hosts-and-tenants.md)) |

## Related pages

- [Routing: modules, views and parameters](routing-with-modules-and-views.md)
- [Redirects and moved content](../creators/website-creators/redirects-and-moved-content.md)
- [Safe redirects](../../features/6.0/safe-redirects.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
