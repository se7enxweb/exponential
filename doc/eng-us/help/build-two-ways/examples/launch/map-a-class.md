---
title: "Give every article the article layout"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1201
summary: A Subtree mapping with a Class condition: Full / Article on Subtree 89 for ng_article, ng_blog_post, ng_news at priority 130.
keywords: class condition, subtree target, article layout, mapping, Full / Article
type: help
---
# Give every article the article layout

All articles, blog posts and news below the site root share one layout.

*Life cycle: Launch, example 2 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **Layouts > Layout mappings > New mapping**; **Link layout**: *Full / Article*; **Priority**: 130.
2. Target: **Subtree** with the site root (node 89), **Add target**.
3. Condition: **Class**: NG Article, NG Blog post, News, **Add condition**.
4. **Add mapping**.

## With templates

1. The template way is `override.ini` `Match[class_identifier]=ng_article` for `node/view/full.tpl`, as the theme does in `extension/sevenx_themes_media/settings/siteaccess/site/override.ini.append.php`.

## Which to choose

The class mapping is the backbone of a Layouts site; full view overrides still render what the layout's blocks include.

## Check

- A newly published article gets the layout (its `pre_footer` shows the Prefooter).

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
