---
title: Include
audience: designer, developer
category: templates
order: 100
summary: Splitting templates into parts with include: the design resource, passing parameters, what the included template sees, naming parts, and include versus node_view_gui and overrides.
keywords: include, include uri, design resource, template parts, partial template, parameters, pass variables, reuse template, sub template
type: help
---
# Include

This page is for designers who split templates into reusable parts: a header, a teaser, a share bar.

## Contents

1. [Syntax](#syntax)
2. [Passing parameters](#passing-parameters)
3. [What the included template sees](#what-the-included-template-sees)
4. [Dynamic paths](#dynamic-paths)
5. [Include or node_view_gui?](#include-or-node_view_gui)

## Syntax

```tpl
{include uri='design:pagelayout/header.tpl'}
```

`design:` means "find this path under `templates/` through the design cascade", so a design higher in the chain can
replace the part by placing a file at the same path ([The design cascade](../theme-design/the-design-cascade.md)).

## Passing parameters

Every other `name=value` becomes a variable in the included template:

```tpl
{include uri='design:parts/teaser.tpl' node=$child show_image=true() image_class='small'}
```

In the part, give optional parameters a default:

```tpl
{* design/mytheme/templates/parts/teaser.tpl *}
{default show_image=false() image_class='medium'}
<article class="teaser">
    {if and( $show_image, $node.data_map.image.has_content )}
        {attribute_view_gui attribute=$node.data_map.image image_class=$image_class}
    {/if}
    <h3><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></h3>
</article>
```

## What the included template sees

The part sees the variables of its includer plus the parameters. Variables it defines with `{def}` stay in the shared
namespace until `{undef}`; that is why parts should `{undef}` what they define and use prefixed names
([Variables and def](variables-and-def.md)).

## Dynamic paths

The path can be built:

```tpl
{include uri=concat( 'design:parts/icon_', $type, '.tpl' )}
```

The layouts engine includes block templates this way (`concat( 'design:explayouts/block/', $block.definition_identifier, '.tpl' )`).
A path that does not exist is a template error: make sure every possible value has a file.

## Include or node_view_gui?

| Use | When |
|---|---|
| `{include}` | a part of the frame, or markup that does not depend on the content class |
| `{node_view_gui content_node=$child view='line'}` | drawing a node: the view mode's template is chosen through overrides, per class ([node_view_gui](node-view-gui.md)) |

## Related pages

- [Variables and def](variables-and-def.md)
- [The pagelayout](../theme-design/the-pagelayout.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
