---
title: "Use the component style variants"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1428
summary: The exp_component_* blocks come with style view types (hero_style_1 to 3, features_style_1 to 7, about_style_1 to 5, logos_style_1 and 2, lead_style_1 and 2, quote_style_1).
keywords: components, style variants, exp_component, view types, redesign
type: help
---
# Use the component style variants

Choose among ready variants instead of building new ones.

*Life cycle: Redesign, example 29 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. The component blocks imported with the theme (`exp_component_hero` ...) offer their style variants as view types; the media design renders them with `explayouts/block/exp_component_<name>.tpl`.

## With templates

1. Override `exp_component_<name>.tpl` in your extension for a new variant look.

## Which to choose

Try the variants before writing a template.

## Check

- The wrapper shows `ngl-vt-<style>`.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
