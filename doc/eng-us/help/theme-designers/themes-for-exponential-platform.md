---
title: "Themes for Exponential Platform"
audience: theme-designer
category: theme-design
order: 35
summary: A short orientation for theme designers asked to design for Exponential Platform DXP, the Symfony line - Twig instead of TPL, designs as ordered theme lists, the @ibexadesign namespace, templates/themes folders, what carries over from this section, and where the DXP pages continue.
keywords: theme designer, Exponential Platform, DXP, Twig, design engine, ibexa/design-engine, ibexadesign, standard design, themes, Symfony, Encore
type: help
---
# Themes for Exponential Platform

This page is for theme designers who are asked to design for **Exponential Platform DXP**, the Symfony-based line of
the product family, rather than for Exponential 6 (the PHP kernel this section is about). The design work is largely
the same; the template language and the file layout are not. This page gives the differences in brief and points to
the DXP developer pages that cover them properly.

## Contents

1. [Which product are you designing for](#which-product-are-you-designing-for)
2. [The same idea, different words](#the-same-idea-different-words)
3. [Where theme files live](#where-theme-files-live)
4. [Stylesheets and scripts](#stylesheets-and-scripts)
5. [What carries over from this section](#what-carries-over-from-this-section)
6. [Where to continue](#where-to-continue)

## Which product are you designing for

Ask first. A site on Exponential 6 has `.tpl` templates, `design/` folders in extensions and `settings/*.ini` files. A
site on the DXP has `.html.twig` templates, a `templates/themes/` folder and YAML configuration under `config/`.
[Choosing DXP, Legacy, Nexus or Exponential 6](../dxp-developers/choosing-dxp-legacy-nexus-or-exponential-6.md) explains
the product lines.

## The same idea, different words

| Exponential 6 | Exponential Platform DXP |
|---|---|
| a design (folder of templates) | a theme (`templates/themes/<theme>/`) |
| the design cascade of a siteaccess | a design: an ordered list of themes, first match wins |
| `standard` at the end of the cascade | the `standard` design and theme, provided by `ibexa/standard-design` |
| TPL templates, `{include uri='design:...'}` | Twig templates, `{% include '@ibexadesign/...' %}` |
| `pagelayout.tpl` | `pagelayout.html.twig` |
| `node/view/full.tpl` and overrides by class | full and line view templates mapped to content types in the content view configuration |
| image aliases in `image.ini` | image variations ([Image variations](../dxp-developers/image-variations.md)) |

The DXP's design engine (`ibexa/design-engine`) resolves every `@ibexadesign/...` name through the current siteaccess's
design and its order of themes, much like the cascade. Templates always refer to `@ibexadesign/...`, never to a theme's
folder path ([Twig templates and the design engine](../dxp-developers/twig-templates-and-the-design-engine.md)).

## Where theme files live

A project theme is a folder under `templates/themes/`, and a design that lists it before `standard` is configured
under `ibexa_design_engine.design_list`; a siteaccess group then uses that design. Bundles can ship themes as well.
The steps, with the configuration, are in
[The standard design and your theme](../dxp-developers/the-standard-design-and-your-theme.md#building-your-theme).

## Stylesheets and scripts

The DXP has no ezjscore packer. Most projects build their stylesheets and scripts with Webpack Encore and include them
with the Encore Twig functions; the design engine can also resolve theme assets from `public/assets/themes/<theme>/`
([Building assets with Yarn and Encore](../dxp-developers/building-assets-with-yarn-and-encore.md),
[The standard design and your theme](../dxp-developers/the-standard-design-and-your-theme.md#theme-assets)).
The design principles stay the same: no inline scripts or styles, so a Content Security Policy can stay strict.

## What carries over from this section

Almost all of the design work in this section is product-neutral:

- the process: [From mockup to theme](from-mockup-to-theme.md), [Working with developers](working-with-developers.md),
  [Working with clients and reviews](working-with-clients-and-reviews.md);
- foundations: [Type scale](type-scale.md), [Spacing and grid](spacing-and-grid.md),
  [Color palettes and ramps](color-palettes-and-ramps.md), [From design tool variables to CSS custom properties](from-design-tool-variables-to-css-custom-properties.md);
- content design: [Mapping a design to content classes](mapping-a-design-to-content-classes.md) (content types and
  fields on the DXP), [Designing view modes](designing-view-modes.md), [Designing for editors](designing-for-editors.md);
- quality: the [design QA checklist](design-qa-checklist.md), accessibility and dark mode.

What does not carry over: TPL syntax, `override.ini`, `design.ini`, the packer, and anything in the designer help
about Exponential 6 settings files.

## Where to continue

- [DXP developers: start here](../dxp-developers/dxp-developers-start-here.md)
- [Twig templates and the design engine](../dxp-developers/twig-templates-and-the-design-engine.md)
- [The standard design and your theme](../dxp-developers/the-standard-design-and-your-theme.md)
- [Twig functions you use daily](../dxp-developers/twig-functions-you-use-daily.md)

## Related pages

- [What Exponential Platform DXP is](../dxp-developers/what-exponential-platform-dxp-is.md)
- [How Exponential themes differ](how-exponential-themes-differ.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
