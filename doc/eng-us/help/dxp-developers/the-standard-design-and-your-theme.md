---
title: The standard design and your theme
audience: dxp-developer
category: dxp-development
order: 20
summary: What ibexa/standard-design really provides (the standard design and the default view templates), where the standard theme's templates come from, and how to build your own theme on top - pagelayout, full and line views, theme assets and the order of themes.
keywords: standard design, ibexa/standard-design, theme, pagelayout, viewbase_layout, full view, line view, embed, default templates, theme assets, assets/themes, override
type: help
---
# The standard design and your theme

This page is for developers and theme builders who give a DXP site its own look. The skeleton has "no demo design"
([DXP skeleton](../../features/6.0/platform-dxp-skeleton.md#what-it-is)), so every site starts from the standard
design and adds a theme.

## Contents

1. [What ibexa/standard-design provides](#what-ibexastandard-design-provides)
2. [Where the standard theme's templates come from](#where-the-standard-themes-templates-come-from)
3. [Building your theme](#building-your-theme)
4. [Theme assets](#theme-assets)
5. [A theme checklist](#a-theme-checklist)

## What ibexa/standard-design provides

In an installed project the package holds configuration only:

- it defines the design `standard` with the single theme `standard`;
- it points the default templates to `@ibexadesign`, so any theme can replace them:

| Default | Template |
|---|---|
| Page layout | `@ibexadesign/pagelayout.html.twig` |
| Base layout of content views | `@ibexadesign/viewbase_layout.html.twig` |
| Full, line, embed, embed image and linked text views | `@ibexadesign/default/content/<view>.html.twig` |
| Field rendering | `@ibexadesign/content_fields.html.twig` |
| Field definition settings | `@ibexadesign/fielddefinition_settings.html.twig` |

## Where the standard theme's templates come from

Bundles ship templates for the `standard` theme in their `Resources/views/themes/standard/` directories (the rich
text, matrix, search, messenger, notifications and Twig components packages among them). The design engine collects
every bundle's `themes/` directories and your project's `templates/themes/` into one map per theme.

## Building your theme

1. Create the theme directory: `templates/themes/my_theme/`.
2. Define a design that puts your theme before `standard`:

   ```yaml
   ibexa_design_engine:
       design_list:
           my_design: [my_theme, standard]
   ```

3. Give the site siteaccesses that design (`ibexa.system.site_group.design: my_design`).
4. Write `templates/themes/my_theme/pagelayout.html.twig` with your HTML skeleton and a block for the content.
5. Add view templates for your content types (for example `full/article.html.twig`) and map them to the content
   type in the content view configuration of your siteaccess group (the `content_view` settings of the core).
6. Clear the cache and open a page.

Anything your theme does not define falls back to `standard`.

## Theme assets

The design engine also resolves assets per theme: it looks in `public/assets/themes/<theme>/` at application level
and in bundles' public `themes/` directories. Most projects build their stylesheets and scripts with Webpack Encore
instead and include them with the Encore Twig functions ([Building assets](building-assets-with-yarn-and-encore.md)).

## A theme checklist

- Templates refer to `@ibexadesign/...`, never to a theme path.
- The page layout has landmarks, a skip link, a language attribute and a visible focus style
  ([Accessibility basics for themes](../designers/theme-design/accessibility-basics-for-themes.md)).
- Images use variations, not originals ([Image variations](image-variations.md)).
- No inline scripts or styles, so a Content Security Policy can stay strict
  ([CSP-safe design](../designers/theme-design/csp-safe-design.md)).
- Light and dark color schemes are tested if the site offers both.

## Related pages

- [Twig templates and the design engine](twig-templates-and-the-design-engine.md)
- [Twig functions you use daily](twig-functions-you-use-daily.md)
- [Designers: start here](../designers/designers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
