---
title: "Theme designer FAQ"
audience: theme-designer
category: theme-design
order: 42
summary: Short answers to the questions theme designers ask most about Exponential - do I need to write templates, why no inline styles, how dark mode works, why my change does not show, how editors change pages, fonts, images, multisite, the DXP, and selling themes - each linking to the full page.
keywords: theme designer, faq, questions, answers, templates, inline styles, dark mode, cache, fonts, images, layouts, multisite, DXP, selling themes
type: help
---
# Theme designer FAQ

This page is for theme designers who have a quick question. Each answer is short and links to the page with the
details.

## Contents

1. [Getting started](#getting-started)
2. [Rules and why](#rules-and-why)
3. [When something does not show](#when-something-does-not-show)
4. [Content, editors and layouts](#content-editors-and-layouts)
5. [Images, fonts and languages](#images-fonts-and-languages)
6. [Other products and business](#other-products-and-business)

## Getting started

**Do I have to write templates to design for Exponential?**
No. Many designers hand over designs and review the built pages; others write stylesheets, and some write templates
too. Agree who owns what ([Working with developers](working-with-developers.md#who-owns-what)). Knowing how templates
and view modes work makes your designs easier to build ([How Exponential themes differ](how-exponential-themes-differ.md)).

**Where do I start on a site that already exists?**
With the [first day plan](your-first-day-as-a-theme-designer.md), then [Redesigning an existing site](redesigning-an-existing-site.md).

**Should I start from a shipped theme or from nothing?**
It depends on the project; the designer help has a decision table ([Choosing a starting theme](../designers/theme-design/choosing-a-starting-theme.md)).

## Rules and why

**Why are inline styles not allowed?**
So a strict Content Security Policy can be used, styles stay in one place, and tokens and dark mode work everywhere.
The one documented exception is newsletter mail skins, because mail programs need inline styles
([No inline CSS](../designers/theme-design/no-inline-css.md), [Designing newsletter emails](designing-newsletter-emails.md#mail-is-not-the-web)).

**Why must every color have a dark value?**
Because a token added without one breaks dark mode the day it is used, usually as a white box on a dark page. It is a
project rule ([Design tokens](../designers/theme-design/design-tokens.md#rules-that-keep-tokens-useful)).

**Why no `?v=` on stylesheet links?**
The packer already puts the modification time into the packed file's name, so browsers get the new file
([No version queries on asset links](../designers/theme-design/no-version-queries-on-asset-links.md)).

**Which widths do I test?**
1440 px, 960 px at 200% zoom, and 375 px, in light and dark, with the keyboard ([Design QA checklist](design-qa-checklist.md)).

## When something does not show

**I changed a stylesheet and nothing changed.**
Packed files and caches need renewing after a change; ask the developer to deploy it, then hard-reload
([The packer cache and deploys](../designers/theme-design/packer-cache-and-deploy.md)).

**My new template is ignored.**
New template files need the template override cache cleared, and the file must be in a design of that siteaccess
([Debugging a design](../designers/theme-design/debugging-a-design.md#my-new-template-or-override-is-ignored)).

**Which template drew this part of the page?**
Switch on template path comments on a development siteaccess ([Tools for theme designers](tools-for-theme-designers.md#seeing-which-template-drew-what)).

## Content, editors and layouts

**Can editors break my design?**
Only if it depends on content being just right. Design for the extremes and for empty fields
([Designing for editors](designing-for-editors.md)).

**Can editors build pages themselves?**
Yes, with the layouts editor, from the blocks and view types your design provides ([Designing layout blocks](designing-layout-blocks.md)).

**How do I give one page a special look?**
An override for that page, a layout, or a class of its own; each has a cost
([Override a single node](../designers/templates/override-a-single-node.md#alternatives), [Designing the front page](designing-the-front-page.md)).

**A field I need does not exist.**
Ask the developer, with the reason and the details; the content model is decided together
([Mapping a design to content classes](mapping-a-design-to-content-classes.md#when-the-design-needs-a-field-that-does-not-exist)).

## Images, fonts and languages

**Can images be cropped around the subject automatically?**
Not with the shipped filters: they scale and cut at fixed positions. Use CSS crops, or editors crop with the image
editor where it is installed ([Designing image crops and art direction](designing-image-crops-and-art-direction.md)).

**Can I use a font from a font service?**
Fonts are self-hosted in the design, for privacy and speed, and only with a license that allows it
([Fonts in a design](../designers/theme-design/fonts-in-a-design.md)).

**How do I design for Arabic or Hebrew?**
Use logical CSS properties and mirror the key screens ([Designing for many languages](designing-for-many-languages.md#right-to-left)).

**Several brands on one installation?**
A shared design plus a small brand design per site, mostly tokens and logos
([Designing for several brands in multisite](designing-for-several-brands-in-multisite.md)).

## Other products and business

**The client runs Exponential Platform DXP. Does this section apply?**
The design process does; the template language and file layout differ ([Themes for Exponential Platform](themes-for-exponential-platform.md)).

**Can I sell themes I make?**
Yes, within the GPL; the designer sales section explains models and terms
([Selling themes and services: start here](../designers/sales/selling-start-here.md)).

**Is there an official Exponential design certification?**
The partner program names a Design certification, but its syllabus and exams are not published
([Certifications](../partners/certifications.md)).

**Is Exponential related to eZ Systems or Ibexa?**
No. Exponential continues the code base of eZ Publish, a product discontinued by its creator after almost 18 years,
and is led by 7x with the Exponential Foundation ([Who is 7x?](../who-is-7x.md)).

## Related pages

- [Theme designers: start here](theme-designers-start-here.md)
- [Theme designer glossary](theme-designer-glossary.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
