---
title: Which layout does a page get?
audience: editor, website-creator, admin, designer, developer
category: build-two-ways
order: 270
summary: Layout mappings decide which layout a page uses - targets (location, subtree, path), conditions (content class, siteaccess), priority and the first match. The real mappings of the Fit & Healthy site and what a page without a mapping shows.
keywords: layout mappings, rules, resolver, target, subtree, location, path prefix, condition, content class, siteaccess, priority, enabled, Default Layout, which layout
type: help
---
# Which layout does a page get?

**Layouts > Layout mappings** lists the rules that tie layouts to pages. The page explains it in one sentence: *A
mapping links a layout to pages: the first enabled mapping by priority whose targets and conditions match the page
wins.*

![The Layout mappings page: each mapping with its layout name, Enabled, its targets and conditions, and the buttons Details, Edit layout, Link other layout and More actions](images/lm-01-layout-mappings.webp)

## Contents

1. [Targets and conditions](#targets-and-conditions)
2. [The mappings of Fit & Healthy](#the-mappings-of-fit--healthy)
3. [Create a mapping](#create-a-mapping)
4. [Finding the layout of one page](#finding-the-layout-of-one-page)
5. [Templates and mappings](#templates-and-mappings)

## Targets and conditions

| Part | Kinds seen on this installation | Example |
|---|---|---|
| Target | Location (one node), Subtree (a node and everything below), path prefix | Subtree: `starter` |
| Condition | content class, siteaccess | class: NG Article; siteaccess: starter, starter_ger |
| Priority | a number; higher is tried first | 210 for the home page |
| Enabled | on or off | |

## The mappings of Fit & Healthy

From the database of the installation (enabled mappings for the Fit & Healthy tree, node 89):

| Priority | Target | Condition | Layout |
|---|---|---|---|
| 210 | Location 89 (the home page) | | Landing Page / Home |
| 160 | Subtree 89 | class `ng_recipe` | Full / Recipe |
| 130 | Subtree 89 | class `ng_article`, `ng_blog_post`, `ng_news` | Full / Article |
| 120 | Subtree 89 | class `ng_topic` | Full / Topic |
| 10 | path prefix `/` | | Default Layout |

Location mappings tie the landing pages Fitness (92), Healthy eating (130), Running (163), Video (164) and Showcase
(192) to their layouts; further mappings serve the Bold Agency and
Starter sites. A recipe matches the recipe mapping (160) before the article one (130); a page no other
mapping takes falls through to **Default Layout** (10), which links its `header` and `footer` to Header / Footer.

## Create a mapping

**New mapping** opens a panel: **Link layout** (the layout), **Priority**, **Enabled**, then targets and conditions. The
target types offered are Path, Path prefix, Path regex, Location, Subtree and Route (Route never matches on the site);
the condition types Siteaccess, Class, Query parameter, Route parameter and Time. *Any target can be met*; every
condition must be.

![The New mapping panel: Mapped layout, Link layout, Priority with Enabled, No targets with a Path choice and Add target, No conditions with a Siteaccess choice and Add condition](images/mp-01-new-mapping.webp)

A Siteaccess condition is a list of the installation's siteaccesses to choose from:

![A Siteaccess condition with the list site, admin, classic, bold](images/mp-04-siteaccess-condition.webp)

The resolver remembers its answer per address and siteaccess for `[ResolverSettings] CacheTTL` (3600 seconds on this
installation), without the query string. So a Query parameter or Time condition takes effect only for addresses whose
remembered answer has expired: on the test copy `/showcase` and `/showcase?variant=b` always showed the same layout,
the one of whichever was requested first.

## Finding the layout of one page

1. In the site's HTML, the layout's wrapper names its type without the underscore (`<div class="zone-layout-layout2">`
   for `layout_2`) and every zone is a
   `<section class="zone zone-<identifier>">`.
2. In the administration, a content page has a **Layouts** tab.
3. In Layout mappings, go down from the highest priority: the first mapping whose target and conditions fit the page
   is the one.

## Templates and mappings

A page with no matching mapping, or whose layout has no blocks, is drawn by the design's plain page layout
(`pagelayout.tpl` with `pagelayout/header.tpl`, the module result and `pagelayout/footer.tpl`). So a site can run
without any mapping: the template way alone.

## Related pages

- [Shared layouts and zone linking](shared-layouts-and-zone-linking.md)
- [Why doesn't my change appear?](why-doesnt-my-change-appear.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
