---
title: "Plan which lists are hand-picked and which fill themselves"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1006
summary: For every list, decide manual collection (editors pick) or dynamic collection (a query) before building.
keywords: manual collection, dynamic collection, lists, curation, planning
type: help
---
# Plan which lists are hand-picked and which fill themselves

No list without an owner: either an editor curates it or the tree feeds it.

*Life cycle: Plan, example 7 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Curated lists (Prefooter "Most popular articles"): **Manual collection**.
2. Section lists ("newest in Fitness"): **Dynamic collection** with a parent location and sort.

## With templates

1. A template list is a `fetch( 'content', 'list', ... )` and belongs to the developer.

## Which to choose

Switching a manual list to dynamic later removes its manual items (the editor warns), so decide early.

## Check

- Each list in the plan is marked manual or dynamic.

## Related pages

- [Manual and dynamic collections](../../manual-and-dynamic-collections.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
