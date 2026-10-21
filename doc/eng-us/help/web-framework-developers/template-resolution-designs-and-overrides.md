---
title: "Template resolution: designs and overrides"
audience: web-framework-developer, developer, designer
category: web-frameworks
order: 110
summary: How Exponential decides which template file renders a view - the design chain of a siteaccess, extension designs, the design: resource, override.ini rules matched by class, node, section or view mode - compared with template paths, theme inheritance and Twig namespaces in other frameworks.
keywords: design, design chain, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, override.ini, Match, MatchFile, Priority, eZTemplateDesignResource, template override, theme, fallback, web framework developer
type: help
---
# Template resolution: designs and overrides

This page is for developers who know Twig's template paths and namespaces, Laravel's view paths and vendor overrides,
or Django's template loaders. Exponential resolves a template name such as `design:node/view/full.tpl` in two steps:
first through a **design chain** (a stack of directories), then through **override rules** that can swap the file
depending on what is being rendered.

## Contents

1. [The design chain](#the-design-chain)
2. [Override rules](#override-rules)
3. [Compared with other frameworks](#compared-with-other-frameworks)
4. [Where your templates go](#where-your-templates-go)
5. [Finding out which file rendered](#finding-out-which-file-rendered)

## The design chain

A design is a directory of templates, stylesheets and images. A siteaccess names its designs in
`site.ini [DesignSettings]`: `SiteDesign`, `AdditionalSiteDesignList[]` and `StandardDesign` (`standard`). Extensions
add their design directories with `design.ini [ExtensionSettings] DesignExtensions[]`. `eZTemplateDesignResource`
(`kernel/common/eztemplatedesignresource.php`) builds the chain, extension design directories first, then
`design/<design>/`; the first directory that has the file wins
([Design and override system](../../standards/architecture/kernel-architecture.md#design-and-override-system),
[Designs and the fallback chain](../creators/website-creators/designs-and-the-fallback-chain.md)).

## Override rules

`override.ini` (of the siteaccess and of every extension) has one group per rule:

```ini
[node/view/full#ng_article]
Source=node/view/full.tpl
MatchFile=node/view/full/full_ng_article.tpl
Subdir=templates
Match[class_identifier]=ng_article
Priority=1
```

When `node/view/full.tpl` is fetched for an object of the class `ng_article`, the file
`override/templates/node/view/full/full_ng_article.tpl` of the design chain is used instead. The first rule whose
conditions all match wins; rules are ordered by `Priority` (lower first) when one is given
([Resources and overrides](../template-language/resources-and-overrides.md#overrides),
[Template override ordering](../../features/6.0/template-override-ordering.md)).

Match keys are set by whoever renders: `content/view` sets the node's keys (`class_identifier`, `node`, `parent_node`,
`section`, `url_alias`, `depth`, `viewmode`, `state` and more); `node_view_gui` sets them per item, so a list can render
each child with its class's line template. A view of your own can add keys with
`eZTemplateDesignResource::instance()->setKeys()`.

## Compared with other frameworks

| Framework idea | Exponential |
|---|---|
| Template paths searched in order | the design chain |
| Bundle templates overridden in `templates/bundles/<Bundle>/` (Symfony), `resources/views/vendor/` (Laravel) | the same file name in a design earlier in the chain |
| Theme inheritance | `AdditionalSiteDesignList[]` falling back to `standard` |
| Choosing a template per content type in a controller | an override rule matched on `class_identifier` |
| Template selection by a view resolver (Spring) | the design resource with override rules |
| `{% extends %}` and blocks | no equivalent; whole templates are overridden, shared parts are included |

## Where your templates go

| Templates | Place |
|---|---|
| Your module's views | `extension/<ext>/design/standard/templates/<ext>/...`, with `DesignExtensions[]=<ext>` |
| Overrides for a site's design | `extension/<site>/design/<design>/override/templates/...` plus `override.ini.append.php` rules |
| Admin pages | the admin designs, following the admin styling rules ([admin4 styling](../../bc/6.0/admin4-styling.md)) |

Never edit `design/standard/` or the kernel's admin designs: override instead, so updates do not overwrite you.

## Finding out which file rendered

- Template path comments print the file name around each template's output
  ([Template path comments](../../features/6.0/template-path-comments.md)).
- The debug bar lists the templates used on a page ([Exp debug bar](../../features/6.0/exp-debug-bar.md)).
- After adding a new template file, clear the `template-override` cache; otherwise the old resolution stays cached
  ([Template caches](../template-language/template-caches.md)).

## Related pages

- [The view layer](templates-vs-twig-and-blade.md)
- [Template overrides](../creators/website-creators/template-overrides.md)
- [Templates and design guide](../../guides/templates-and-design.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
