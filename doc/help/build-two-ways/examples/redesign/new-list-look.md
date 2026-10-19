---
title: "A new look for lists, chosen by editors"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1405
summary: Add a view type (one INI line, one template) instead of changing the existing ones.
keywords: view type, new look, list_compact, redesign, editors choose
type: help
---
# A new look for lists, chosen by editors

Editors can pick a compact list where they want, the old looks stay.

*Life cycle: Redesign, example 6 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. On the Design tab, **View type** offers `list_compact`.

## With templates

1. `[BlockDefinition_list] ViewTypes[]=list_compact` in your extension's `explayouts.ini.append.php`; `explayouts/block/list/list_compact.tpl` in your design ([Add a view type](../../add-a-view-type.md)).

## Which to choose

Additive redesigns keep existing pages unchanged.

## Check

- A list switched to `list_compact` renders `<ul class="list-compact">`.

## Related pages

- [Add a view type](../../add-a-view-type.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
