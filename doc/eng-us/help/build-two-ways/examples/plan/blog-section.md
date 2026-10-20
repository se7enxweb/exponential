---
title: "Plan a blog section"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1022
summary: Blog posts (class NG Blog post) already share the article mapping on Fit & Healthy; plan the listing page and its lists.
keywords: blog, ng_blog_post, article mapping, listing, section, planning
type: help
---
# Plan a blog section

A blog with its own list page and the article layout for the posts.

*Life cycle: Plan, example 23 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. The Full / Article mapping (priority 130) has the class condition `ng_article`, `ng_blog_post`, `ng_news`: posts get the article layout without a new mapping.
2. The blog's list page is a landing page with a dynamic List of the posts.

## With templates

1. A blog-specific full view would be an `override.ini` block for `ng_blog_post` in your extension.

## Which to choose

Reuse the article layout; give the list page its own layout.

## Check

- A new blog post shows with the article layout.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
