---
title: "Give the home page its layout"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1200
summary: A Location mapping to the site root node: on Fit & Healthy, Landing Page / Home on Location 89 at priority 210.
keywords: home page, front page, Location target, mapping, Landing Page / Home
type: help
---
# Give the home page its layout

The front page shows its own layout, not the default one.

*Life cycle: Launch, example 1 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **Layouts > Layout mappings > New mapping**; **Link layout**: *Landing Page / Home*; **Priority**: the highest of the site (210 on Fit & Healthy).
2. Target: **Location** with the site root (Fit & Healthy, node 89), **Add target**.
3. **Add mapping**.

The resolver treats the address `/` (and `home`) as the siteaccess' index page (`[SiteSettings] IndexPage`, `/content/view/full/89` here).

## With templates

1. Without Layouts, the front page is the full view of its class (NG Frontpage) through `override.ini`.

## Which to choose

Layouts: the home page changes often and is composed of many blocks (28 on Fit & Healthy).

## Check

- The front page source has the home layout's blocks; `exp:layouts list` shows Rules 2 for Landing Page / Home.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
