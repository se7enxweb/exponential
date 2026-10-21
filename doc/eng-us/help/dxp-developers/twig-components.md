---
title: Twig components
audience: dxp-developer
category: dxp-development
order: 22
summary: What ibexa/twig-components adds to a DXP project - named groups of small components (script, stylesheet, template, controller, html, menu) configured in YAML and rendered with ibexa_twig_component_group - and how to use them without inline code.
keywords: Twig components, ibexa/twig-components, ibexa_twig_component_group, ibexa_twig_component, component group, script, stylesheet, template, controller, html, menu, priority
type: help
---
# Twig components

This page is for developers who want to add small pieces of output to places a template or bundle has prepared,
without editing that template. The package `ibexa/twig-components` is part of the DXP metapackage. The facts below
were read from its configuration class and the commented example the recipe writes to
`config/packages/ibexa_twig_components.yaml`.

## Contents

1. [The idea](#the-idea)
2. [Component types](#component-types)
3. [Configuring a group](#configuring-a-group)
4. [Rendering](#rendering)
5. [Good use](#good-use)

## The idea

A template renders a named **group**. Any bundle or your project can add **components** to that group. The group
renders every component in order of `priority`. A layout can thus offer, for example, a "head" group where several
packages add their stylesheet links.

## Component types

| Type | Renders |
|---|---|
| `script` | A `<script>` element for a file |
| `stylesheet` | A `<link rel="stylesheet">` element |
| `template` | A Twig template with parameters |
| `controller` | The output of a controller |
| `html` | A fixed piece of HTML |
| `menu` | A menu |

Any other `type` is refused with "Invalid type ... Allowed types: ..." when the container is built.

## Configuring a group

```yaml
# config/packages/ibexa_twig_components.yaml
ibexa_twig_components:
    exp_site_head:                       # the group name
        exp_site_print_css:              # the component name
            type: stylesheet
            priority: 10
            arguments:
                href: '/assets/app/print.css'
        exp_site_notice:
            type: template
            arguments:
                template: '@ibexadesign/parts/notice.html.twig'
```

Each component needs `type` and `arguments`; `priority` defaults to `0`. The arguments each type accepts are the
constructor arguments of its component class under `vendor/ibexa/twig-components/src/lib/Component/`; read them
before you configure one.

## Rendering

```twig
<head>
    {{ ibexa_twig_component_group('exp_site_head') }}
</head>
```

`ibexa_twig_component(...)` renders a single component.

## Good use

- Prefer `stylesheet`, `script` and `template` components over `html`: inline markup in configuration is hard to
  review, and inline scripts and styles break a strict Content Security Policy.
- Use `exp_` group and component names for your own; never reuse a name a bundle defined unless you mean to add to
  its group.
- After a change, clear the cache: the configuration is compiled into the container.

## Related pages

- [Twig templates and the design engine](twig-templates-and-the-design-engine.md)
- [Twig functions you use daily](twig-functions-you-use-daily.md)
- [No inline JavaScript](../designers/theme-design/no-inline-javascript.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
