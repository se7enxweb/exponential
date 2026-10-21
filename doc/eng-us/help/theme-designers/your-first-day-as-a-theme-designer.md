---
title: "Your first day as a theme designer"
audience: theme-designer
category: theme-design
order: 2
summary: A plan for the first working day of a theme designer new to Exponential - looking at a real site with the right eyes, finding which template drew what, collecting the content classes, and leaving with a list of what to design.
keywords: theme designer, first day, onboarding, getting started, template path comments, content classes, design inventory, demo site, plan
type: help
---
# Your first day as a theme designer

This page is for theme designers on their first day with Exponential, whether you joined a team that runs a site or
took on a client project. It gives a plan in five blocks of about ninety minutes. At the end of the day you know how
the site you will design is put together, and you have a list of what to draw. You do not write a theme today; the
[first week plan for designers](../designers/designers-start-here.md#your-first-week) covers that.

## Contents

1. [Before you start](#before-you-start)
2. [Block 1: look at the site as a visitor](#block-1-look-at-the-site-as-a-visitor)
3. [Block 2: look at it as an editor](#block-2-look-at-it-as-an-editor)
4. [Block 3: find the templates behind the pixels](#block-3-find-the-templates-behind-the-pixels)
5. [Block 4: the design you inherit](#block-4-the-design-you-inherit)
6. [Block 5: write the list](#block-5-write-the-list)
7. [What you have at the end of the day](#what-you-have-at-the-end-of-the-day)

## Before you start

Ask the developer or administrator you work with for:

- a **development installation** you may break, never the live site
  ([Previewing a design on a siteaccess](../designers/theme-design/previewing-a-design-on-a-siteaccess.md) explains
  how a design is tried next to a live one);
- an **editor account** on it (a test account with an `example.invalid` address, not a shared administrator);
- read access to the **design extension** of the site, usually a Git repository
  ([Putting a theme under Git](../designers/extensions/putting-a-theme-under-git.md)).

## Block 1: look at the site as a visitor

Open the site in a browser and write down, for every distinct page you see, what kind of page it is: a front page,
an article, a list of news, a search result, a contact form, a sign-in page, a "not found" page. Try it:

1. at 1440 px wide, at 960 px with 200% zoom, and at 375 px;
2. in light and dark color schemes, if the site offers both;
3. with the keyboard only (Tab, Shift+Tab, Enter).

Note what looks broken or inconsistent. That note is the first input to your design brief.

## Block 2: look at it as an editor

Sign in to the administration with the editor account and open a few objects you saw in block 1. For each, write
down the **content class** (the edit form shows it) and which fields are filled and which are empty. This is the
material your design must hold: see [Mapping a design to content classes](mapping-a-design-to-content-classes.md).

If the site uses Exponential Layouts, open one layout in the layouts editor and look at its zones and blocks
([The layouts editor](../layouts-editor.md)).

## Block 3: find the templates behind the pixels

Ask the developer to switch on **template path comments** on the development installation
(`[TemplateSettings] ShowTemplatePathComments=enabled`). Every rendered template is then wrapped in an HTML comment
naming its file, so the browser's "view source" or element inspector tells you which template drew a piece of the
page. [Template path comments](../designers/templates/template-path-comments.md) shows how to read them, and
[Lesson 1.3: Which template wrote this markup?](../trainers/theme-designers/module-01-designs-and-the-fallback-chain/which-template-wrote-this-markup.md)
is a short exercise. Never leave this setting on in production.

Pick three pages and write down the pagelayout, the full view template and one line view template each uses.

## Block 4: the design you inherit

Find out which designs the siteaccess uses and in which order: [Designs and siteaccesses](../designers/theme-design/designs-and-siteaccesses.md).
If the site is built on a shipped theme, take its tour:

| Theme | Tour |
|---|---|
| media | [A tour of the media theme](../designers/theme-design/media-theme-tour.md) |
| simple | [A tour of the simple theme](../designers/theme-design/simple-theme-tour.md) |
| super | [A tour of the super theme](../designers/theme-design/super-theme-tour.md) |
| starter | [A tour of the starter theme](../designers/theme-design/starter-theme-tour.md) |
| ezwebin | [The ezwebin design as a base](../designers/theme-design/ezwebin-as-a-base.md) |

Open the design's stylesheets folder and look for the token file. If there is none, note it: introducing tokens will
be part of your work ([Design tokens](../designers/theme-design/design-tokens.md)).

## Block 5: write the list

Write one page, in plain text, with four lists:

1. **Frame:** header, navigation, footer, what changes at small widths.
2. **Classes and views:** each class with the views it needs (full, line, card, embed).
3. **Special pages:** search, sign-in, errors, empty lists, forms.
4. **Blocks:** each block type and view type editors use, if the site uses layouts.

Share it with your developer before you draw anything. It is the scope of your design and the first version of your
[component inventory](component-inventory.md).

## What you have at the end of the day

- [ ] A development installation and an editor account.
- [ ] Template path comments working, and three pages traced to their templates.
- [ ] The list of designs in the siteaccess's cascade.
- [ ] A one-page list of frame, classes and views, special pages and blocks.
- [ ] A choice of reading path for the next days ([Reading paths for theme designers](reading-paths-for-theme-designers.md)).

## Related pages

- [Theme designers: start here](theme-designers-start-here.md)
- [How Exponential themes differ](how-exponential-themes-differ.md)
- [Debugging a design](../designers/theme-design/debugging-a-design.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
