---
title: "Designers: start here"
audience: designer
category: theme-design
order: 1
summary: The landing page for designers: what Exponential's design system is, a first-week plan, the six sections of the designer help (theme design, templates, theme extensions, distribution, sales, maintenance), the tutorials and checklists, and the rules every design follows.
keywords: designers, start here, theme designer, front-end, web designer, design system, themes, templates, onboarding, first week, landing page
featured: true
type: help
---
# Designers: start here

This page is for designers and front-end developers who build the look of sites with Exponential: new themes, changes to
existing ones, landing pages, administration styling, and themes you publish or sell. It answers: what you need to know
first, what to do in your first week, and where every topic lives in this help.

> **Note**
> Exponential is the open source content management system and framework that runs this administration. It continues
> the code base of eZ Publish, a product its creator discontinued after almost 18 years, and is led by **7x** together
> with the **Exponential Foundation** ([Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).

## Contents

1. [The design system in five sentences](#the-design-system-in-five-sentences)
2. [Your first week](#your-first-week)
3. [The six sections](#the-six-sections)
4. [Tutorials](#tutorials)
5. [Checklists](#checklists)
6. [Rules every design follows](#rules-every-design-follows)
7. [Every page](#every-page)

## The design system in five sentences

1. A **design** is a named folder of templates, stylesheets, scripts and images, usually inside an extension
   ([What a design is](theme-design/what-a-design-is.md)).
2. A **siteaccess** (one way into the installation) names its designs, and the kernel searches them in order, the
   **design cascade**, taking the first file it finds ([The design cascade](theme-design/the-design-cascade.md)).
3. The **pagelayout** frames every page around the module's result ([The pagelayout](theme-design/the-pagelayout.md)).
4. **Templates** in the TPL language draw content, and **overrides** choose a template per class, node or section
   ([Templates overview](templates/templates-overview.md), Override basics).
5. Stylesheets and scripts reach the browser **packed** by ezjscore, with no inline code
   ([Loading stylesheets](theme-design/loading-stylesheets.md), [CSP-safe design](theme-design/csp-safe-design.md)).

## Your first week

### Day 1: look around

- [ ] Read [What a design is](theme-design/what-a-design-is.md) and [Designs and siteaccesses](theme-design/designs-and-siteaccesses.md).
- [ ] Browse [The themes that ship with Exponential](theme-design/themes-on-alpha.md); open one theme's `pagelayout.tpl`.

### Day 2: the template language

- [ ] [TPL syntax basics](templates/tpl-syntax-basics.md), [Variables and def](templates/variables-and-def.md),
      [Foreach loops](templates/foreach-loops.md), [Wash and escaping](templates/wash-and-escaping.md).

### Day 3: your own theme

- [ ] Follow [Tutorial: create a theme extension](theme-design/tutorial-create-a-theme-extension.md) on a development
      installation.

### Day 4: content and overrides

- [ ] Fetch a list ([Fetch basics](templates/fetch-basics.md)) and override one full view
      ([Tutorial: override a full view](theme-design/tutorial-override-a-full-view.md)).

### Day 5: quality

- [ ] Add dark mode ([Tutorial: add dark mode](theme-design/tutorial-add-dark-mode.md)) and run the
      [accessibility checklist](theme-design/checklist-accessibility.md) on your pages.

## The six sections

| Section | What it covers | Start with |
|---|---|---|
| Theme design | designs, cascade, pagelayout, CSS and JS, images and fonts, responsive, accessibility, dark mode, right to left, CSP, performance, layouts editor | [The design cascade](theme-design/the-design-cascade.md) |
| Templates | the TPL language, operators and functions, fetching content, view modes, overrides, debugging | [Templates overview](templates/templates-overview.md) |
| Theme extensions | metadata, settings, packaging, the RAD design and package wizards | [Theme extensions: start here](extensions/theme-extensions-start-here.md) |
| Distribution | repositories, versions, releases, Packagist, licenses, publishing | [Distributing a theme: start here](distribution/distribution-start-here.md) |
| Sales | selling themes and services | Selling themes and services: start here |
| Maintenance life cycle | compatibility, upgrades, security, deprecation, end of life, forks | The maintenance life cycle: start here |

## Tutorials

| Tutorial | Time |
|---|---|
| [Create a theme extension](theme-design/tutorial-create-a-theme-extension.md) | about two hours |
| [Override a full view](theme-design/tutorial-override-a-full-view.md) | 30 minutes |
| [Build a landing page with layouts](theme-design/tutorial-build-a-landing-page-with-layouts.md) | one to two hours |
| [Add dark mode](theme-design/tutorial-add-dark-mode.md) | two to four hours |
| [Internationalize a theme](theme-design/tutorial-internationalize-a-theme.md) | one to two hours |
| [A right-to-left theme](theme-design/tutorial-right-to-left-theme.md) | half a day |
| [Port a static HTML design](theme-design/tutorial-port-a-static-html-design.md) | about a day |
| [Two sites, two designs](theme-design/tutorial-two-sites-two-designs.md) | two hours |
| [Add an admin module stylesheet](theme-design/tutorial-add-an-admin-module-stylesheet.md) | one hour |

## Checklists

[Theme launch](theme-design/checklist-theme-launch.md) ·
[Accessibility](theme-design/checklist-accessibility.md) ·
[Performance](theme-design/checklist-performance.md) ·
[CSP](theme-design/checklist-csp.md) ·
[Dark mode](theme-design/checklist-dark-mode.md) ·
[Right to left](theme-design/checklist-right-to-left.md) ·
[Code review for templates](theme-design/checklist-code-review-for-templates.md) ·
[The theme extension is ready](extensions/checklist-theme-extension-ready.md)

## Rules every design follows

- Your design lives in your own extension; shipped designs are never edited, only overridden.
- No `<style>`, no `style=""`, no inline scripts or `on*=` handlers ([No inline CSS](theme-design/no-inline-css.md),
  [No inline JavaScript](theme-design/no-inline-javascript.md)).
- Colors, spacing and fonts from tokens, with a dark value for every color ([Design tokens](theme-design/design-tokens.md)).
- Every printed value washed, every interface text through `i18n`.
- Tested in light and dark, at 1440 px, at 960 px with scale 2 (200% zoom) and at 375 px, with the keyboard.
- No `?v=` version queries on asset links ([No version queries on asset links](theme-design/no-version-queries-on-asset-links.md)).

## Every page

The index of every designer page, section by section: [Designer help, every page](README.md). Terms explained:
[Design glossary](theme-design/design-glossary.md).

## Related pages

- [Getting started](../getting-started.md)
- [The layouts editor](../layouts-editor.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
