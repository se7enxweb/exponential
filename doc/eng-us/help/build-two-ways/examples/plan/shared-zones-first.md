---
title: "Plan the shared zones first"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1002
summary: Decide which zones every page shares (header, footer) and which some share (prefooter), before building page layouts.
keywords: shared layout, shared zones, header, footer, prefooter, zone linking, planning
type: help
---
# Plan the shared zones first

One place to change what many pages show.

*Life cycle: Plan, example 3 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. List what repeats: on Fit & Healthy the header and footer on every page (shared layout **Header / Footer**), the "Most popular articles" band on articles and recipes (**Prefooter**).
2. For each repeated area, plan one shared layout and which zone of which page layouts links to it.

## With templates

1. Repeated chrome that never changes per page belongs in the page layout template (`pagelayout.tpl`, `pagelayout/header.tpl`); on this theme it is placed by template blocks in Header / Footer.

## Which to choose

Shared layouts for what editors arrange; templates for what is fixed.

## Check

- `exp:layouts list` shows every page layout linking `header` and `footer` to Header / Footer.

## Related pages

- [Shared layouts and zone linking](../../shared-layouts-and-zone-linking.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
