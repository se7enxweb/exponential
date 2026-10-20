---
title: "A plain layout for the legal pages"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1203
summary: Privacy Policy and Cookie Policy are articles but use Full / Article (Plain): a mapping with two Location targets and the article class condition at priority 150.
keywords: legal pages, privacy policy, cookie policy, plain layout, multiple targets
type: help
---
# A plain layout for the legal pages

Legal pages without the Prefooter and other teasers.

*Life cycle: Launch, example 4 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **Layouts > Layout mappings > New mapping**; **Link layout**: *Full / Article (Plain)*; **Priority**: 150 (above the article mapping, 130).
2. Target: **Location** Privacy Policy (node 90) and a second **Location** Cookie Policy (node 91) (*Any target can be met*), **Add target**.
3. Condition: **Class**: NG Article, NG Blog post, News, **Add condition**.
4. **Add mapping**.

## With templates

1. Same as any single-page override, twice.

## Which to choose

One mapping with two targets keeps the rule in one place.

## Check

- The privacy page has no "Most popular articles" band.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
