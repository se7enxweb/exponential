---
title: "How Exponential themes differ"
audience: theme-designer
category: theme-design
order: 4
summary: For theme designers who have built themes for other systems. The six ideas that make an Exponential theme different, each in a few sentences, with the page that explains it in depth.
keywords: theme designer, differences, design cascade, overrides, pagelayout, view modes, content classes, packer, coming from other systems, mental model
type: help
---
# How Exponential themes differ

This page is for theme designers who have made themes for another content management system or for static sites. It
gives the six ideas that work differently in Exponential, in a few sentences each, and points to the page that
explains each one properly. Read it before you open your design tool, because several of these ideas change what you
should draw.

## Contents

1. [A theme is a stack, not a single folder](#a-theme-is-a-stack-not-a-single-folder)
2. [Pages are content, not files](#pages-are-content-not-files)
3. [One item, many views](#one-item-many-views)
4. [The frame and the content are separate](#the-frame-and-the-content-are-separate)
5. [Assets are packed, never inline](#assets-are-packed-never-inline)
6. [Editors build some pages themselves](#editors-build-some-pages-themselves)
7. [What this means for your design files](#what-this-means-for-your-design-files)

## A theme is a stack, not a single folder

A siteaccess (one way into the installation, often one site or one language) names a list of designs. For every
template, stylesheet or image, the kernel searches that list in order and takes the first file it finds. This is the
**design cascade**. Your theme therefore only contains what differs from the designs below it; everything else falls
through to them.

For a designer this means you can start from a working site and change it piece by piece. You never need to design
"everything" before anything shows.

Read: [The design cascade](../designers/theme-design/the-design-cascade.md),
[What a design is](../designers/theme-design/what-a-design-is.md).

## Pages are content, not files

There are no page files in a theme. Every page is a **node** of a content object of a **content class** (article,
folder, event, product ...), and a template draws it. You design one article template, and it draws every article.
Specific nodes, sections or parent folders can get their own template through **overrides**.

For a designer this means your mockups should be per content class and per view, not per URL. A "page" in a mockup is
usually one class in its full view inside the frame.

Read: [Mapping a design to content classes](mapping-a-design-to-content-classes.md),
[Override basics](../designers/templates/override-ini-basics.md).

## One item, many views

The same article appears as a full page, as a line in a list, as a card in a block, as an embed inside another text.
Each of these is a **view mode** with its own template. A design that only shows the full page is half a design.

Read: [Designing view modes](designing-view-modes.md), [View modes](../designers/templates/view-modes.md).

## The frame and the content are separate

The **pagelayout** (`pagelayout.tpl`) is the frame: head, header, navigation, footer. The module (content view,
search, user login ...) produces the middle, which the pagelayout prints. Header and footer are designed once and
appear around everything, including search, login and error pages.

Read: [The pagelayout](../designers/theme-design/the-pagelayout.md),
[Designing headers and footers](designing-headers-and-footers.md).

## Assets are packed, never inline

Stylesheets and scripts are listed in settings and merged by the ezjscore **packer** into cached files. Templates
carry no `<style>` blocks, no `style=""` attributes and no inline scripts, so a strict Content Security Policy can be
used. Colors, spacing and fonts come from **design tokens** (CSS custom properties), each color with a dark value.

For a designer this means every visual value must be nameable as a token, and every variation must be expressible as
a class. Plan for that in your design tool.

Read: [Loading stylesheets](../designers/theme-design/loading-stylesheets.md),
[No inline CSS](../designers/theme-design/no-inline-css.md), [Design tokens](../designers/theme-design/design-tokens.md),
[From design tool variables to CSS custom properties](from-design-tool-variables-to-css-custom-properties.md).

## Editors build some pages themselves

With Exponential Layouts, editors place **blocks** (text, list, gallery, hero ...) into the **zones** of a layout and
choose a **view type** for each block. Your design supplies the look of every block and view type, but you do not
decide which blocks appear in which order on those pages.

Read: [Designing layout blocks](designing-layout-blocks.md),
[Layouts for designers](../designers/theme-design/layouts-for-designers.md).

## What this means for your design files

| Habit from elsewhere | In Exponential |
|---|---|
| One mockup per page | One mockup per content class and view mode, plus the frame |
| Hard-coded colors and sizes | Named tokens, with dark values |
| Pixel-perfect sample text | Real content at its shortest and longest ([Designing with real content](designing-with-real-content.md)) |
| A fixed home page | Often a layout built from blocks by editors |
| A theme replaces everything | A design overrides only what it changes |

## Related pages

- [Theme designers: start here](theme-designers-start-here.md)
- [Designers: start here](../designers/designers-start-here.md)
- [Choosing a starting theme](../designers/theme-design/choosing-a-starting-theme.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
