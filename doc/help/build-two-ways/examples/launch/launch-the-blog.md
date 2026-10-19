---
title: "Launch the blog list page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1218
summary: A landing page with a dynamic List of NG Blog posts, mapped by Location; the posts use the article layout already.
keywords: blog, launch, dynamic list, ng_blog_post, landing page
type: help
---
# Launch the blog list page

A blog index that lists posts newest first.

*Life cycle: Launch, example 19 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Create the landing page "Blog" in the tree.
2. Create its layout with a List block, dynamic collection, parent the blog folder, sort Published descending.
3. Map it by **Location**.

## With templates

1. `fetch( 'content', 'list', hash( 'parent_node_id', <blog node>, 'class_filter_type', 'include', 'class_filter_array', array( 'ng_blog_post' ), 'sort_by', array( 'published', false() ) ) )` in a template block.

## Which to choose

The dynamic list keeps the index current without code.

## Check

- A new post appears first.

## Related pages

- [Manual and dynamic collections](../../manual-and-dynamic-collections.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
