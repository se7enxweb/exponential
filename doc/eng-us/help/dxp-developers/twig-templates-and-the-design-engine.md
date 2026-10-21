---
title: Twig templates and the design engine
audience: dxp-developer
category: dxp-development
order: 19
summary: How the DXP renders with Twig - designs as ordered lists of themes, the @ibexadesign namespace with fallback, theme directories under templates/themes, override paths, the configuration keys of ibexa/design-engine, and how a siteaccess picks its design.
keywords: Twig, design engine, ibexa/design-engine, @ibexadesign, design_list, templates_theme_paths, templates_override_paths, themes, fallback, design, siteaccess, templates/themes
type: help
---
# Twig templates and the design engine

This page is for developers and theme builders who render a DXP site. The DXP renders with Twig, and the package
`ibexa/design-engine` adds designs and themes with a fallback chain, much like the design chain of Exponential 6.
The configuration keys below were read from the package's `Configuration` class in an installed project.

## Contents

1. [Designs and themes](#designs-and-themes)
2. [The @ibexadesign namespace](#the-ibexadesign-namespace)
3. [Where theme templates live](#where-theme-templates-live)
4. [Configuration keys](#configuration-keys)
5. [Giving a siteaccess its design](#giving-a-siteaccess-its-design)
6. [Debugging which template was used](#debugging-which-template-was-used)

## Designs and themes

The package's own description: "A design is a labeled collection of themes. Theme order defines the fallback
order." A design `my_design` with themes `[my_theme, standard]` looks for a template in `my_theme` first and falls
back to `standard`. The `standard` design is always defined; `ibexa/standard-design` provides its templates
([The standard design and your theme](the-standard-design-and-your-theme.md)).

## The @ibexadesign namespace

Refer to theme templates through the `@ibexadesign` Twig namespace, never by a theme's path:

```twig
{% extends '@ibexadesign/pagelayout.html.twig' %}
{% include '@ibexadesign/parts/header.html.twig' %}
```

The engine resolves the name through the current siteaccess's design and its theme order. The skeleton README
mentions an `@ezdesign` namespace; on Platform v5 the installed engine registers `@ibexadesign`.

## Where theme templates live

By convention, each theme is a directory under `templates/themes/`:

```text
templates/themes/
  my_theme/
    pagelayout.html.twig
    full/article.html.twig
    line/article.html.twig
```

Bundles can ship themes too, in their `Resources/views/themes/<theme>/`.

## Configuration keys

Under `ibexa_design_engine` in `config/packages/`:

| Key | Meaning |
|---|---|
| `design_list` | Designs and their themes in fallback order, for example `my_design: [my_theme, standard]` |
| `templates_theme_paths` | Extra template directories per theme ("Each path MUST exist") |
| `templates_override_paths` | Directories checked before every theme directory |
| `disable_assets_pre_resolution` | When true, asset paths are not pre-resolved at compile time (defaults to the debug setting) |

```yaml
ibexa_design_engine:
    design_list:
        my_design: [my_theme, standard]
```

## Giving a siteaccess its design

```yaml
ibexa:
    system:
        site_group:
            design: my_design
```

The administration siteaccesses use their own design; leave it as installed.

## Debugging which template was used

- In `APP_ENV=dev`, the Symfony web profiler lists the rendered templates, and the design engine adds a data
  collector to it.
- `php bin/console debug:twig` lists Twig functions, filters and namespaces.
- `php bin/console exponential:debug:config-resolver design --siteaccess=site` shows the design a siteaccess uses.

## Related pages

- [The standard design and your theme](the-standard-design-and-your-theme.md)
- [Twig functions you use daily](twig-functions-you-use-daily.md)
- [The design chain in Exponential 6](../extension-development/part-2-builder/the-design-chain.md), for comparison
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
