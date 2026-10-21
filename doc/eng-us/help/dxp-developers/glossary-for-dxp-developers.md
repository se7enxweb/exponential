---
title: Glossary for DXP developers
audience: dxp-developer
category: dxp-development
order: 50
summary: The terms of Exponential Platform DXP in one list - from admin UI and content type to siteaccess, theme, variation and replace - each with a one-line meaning, its Exponential 6 counterpart where there is one, and the page that explains it.
keywords: glossary, terms, definitions, content type, location, siteaccess, design, theme, field type, variation, metapackage, skeleton, fork, replace, recipe, JWT
type: help
---
# Glossary for DXP developers

This page is for developers who meet a term on these pages and want its meaning in one line. Terms are in
alphabetical order.

## Contents

1. [A to F](#a-to-f)
2. [G to M](#g-to-m)
3. [N to S](#n-to-s)
4. [T to Z](#t-to-z)

## A to F

| Term | Meaning | Exponential 6 | Page |
|---|---|---|---|
| Admin UI | The administration interface at `/adminui/` | the admin designs | [The admin UI](the-admin-ui.md) |
| Alias (command) | An old command name that still works, such as `ibexa:reindex` | | [Console commands](console-commands-and-renames.md) |
| Content item | One piece of content of a content type | content object | [Content](content-types-and-locations.md) |
| Content type | The definition of a kind of content with its fields | content class | same |
| Design | A named, ordered list of themes | design chain | [Twig and the design engine](twig-templates-and-the-design-engine.md) |
| DXP | Digital Experience Platform; here, the distribution `se7enxweb/exponential-platform-dxp` | | [What it is](what-exponential-platform-dxp-is.md) |
| Encore | Webpack Encore, which builds stylesheets and scripts | the packer | [Building assets](building-assets-with-yarn-and-encore.md) |
| Field type | The kind of a field (text line, image, relation) | datatype | [Field types](the-field-types-it-ships.md) |
| Flex | Symfony's Composer plugin that applies recipes | | [Project layout](the-project-directory-layout.md#what-flex-writes) |
| Fork | A se7enxweb copy of an upstream package, changed for this line | | [The forks](the-se7enxweb-forks-and-replace.md) |

## G to M

| Term | Meaning | Exponential 6 | Page |
|---|---|---|---|
| GraphiQL | The in-browser GraphQL explorer, development only | | [GraphQL](graphql-and-graphiql.md#the-explorer) |
| HTTP cache | Whole responses cached and purged by tags | static and view caches | [HTTP cache](http-cache-and-varnish.md) |
| JWT | A signed token an app sends instead of a session | | [JWT](jwt-authentication.md) |
| Location | A place of a content item in the tree | node | [Content](content-types-and-locations.md#the-tree) |
| Metapackage | A Composer package without code that lists other packages | | [Reading the metapackage](reading-the-metapackage.md) |
| Messenger | Symfony's message queue component | | [Messenger](messenger-and-notifications.md) |

## N to S

| Term | Meaning | Exponential 6 | Page |
|---|---|---|---|
| Nexus | The Exponential Platform distribution with Netgen Layouts and a demo site | | [Choosing](choosing-dxp-legacy-nexus-or-exponential-6.md) |
| Recipe | Configuration a Flex package writes into your project | | [Project layout](the-project-directory-layout.md#what-flex-writes) |
| Replace | A Composer declaration that a package provides another | | [The forks](the-se7enxweb-forks-and-replace.md#how-replace-works) |
| Repository | The content storage and its PHP services | the kernel's content classes | [Content](content-types-and-locations.md) |
| Scope | Where a setting applies: `default`, a group, a siteaccess, `global` | INI override order | [Siteaccesses](siteaccesses-site-and-admin.md#how-settings-are-resolved) |
| Siteaccess | A named configuration chosen per request | siteaccess | [Siteaccesses](siteaccesses-site-and-admin.md) |
| Skeleton | The project template you create a DXP project from | | [Installing](installing-with-the-skeleton.md) |

## T to Z

| Term | Meaning | Exponential 6 | Page |
|---|---|---|---|
| Theme | A directory of templates and assets inside a design | a design directory | [The standard design and your theme](the-standard-design-and-your-theme.md) |
| Twig component | A small output piece added to a named group | | [Twig components](twig-components.md) |
| Variation | A generated size of an image | image alias | [Image variations](image-variations.md) |
| Version | One state of a content item: draft, published, archived | version | [Content](content-types-and-locations.md#versions-and-translations) |

## Related pages

- [Glossary for advocates](../advocates/glossary-for-advocates.md)
- [Glossary for extension developers](../extension-development/part-8-reference/glossary-for-extension-developers.md)
- [DXP developer FAQ](dxp-developer-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
