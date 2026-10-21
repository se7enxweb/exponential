---
title: "Learning path: designer"
audience: student
category: learning
order: 8
summary: A step-by-step learning path for design and front-end students, built from existing help pages - what a design is, the design cascade, the pagelayout, templates and overrides, stylesheets and scripts, accessibility and right to left, the layouts editor, and themes as extensions - with a practice task and a check for every step.
keywords: learning path, designer, front-end, design, theme, template, override, pagelayout, stylesheet, CSS, accessibility, right to left, layouts, track
type: help
---
# Learning path: designer

This path is for learners who will shape how sites built with Exponential look: designers, front-end developers and
students of web design. You should know HTML and CSS. Finish the [editor path](learning-path-editor.md) first, so you
know the content your designs will show.

The designers' own section of the help is large; this path picks the pages a learner needs first. The full map is
[Designers: start here](../designers/designers-start-here.md).

## Contents

1. [The steps](#the-steps)
2. [Rules to learn early](#rules-to-learn-early)
3. [Going further](#going-further)
4. [Where the path leads next](#where-the-path-leads-next)

## The steps

### Step 1: What a design is (1 hour)

- Read: [Designers: start here](../designers/designers-start-here.md#the-design-system-in-five-sentences),
  [What a design is](../designers/theme-design/what-a-design-is.md).
- Practice: find the design of your study site's public siteaccess with
  `php bin/php/console exp:ini where site/DesignSettings/SiteDesign site`
  ([Getting started](../../guides/getting-started.md#7-first-template-change)).
- Check: where can a design live? (In `design/` of the installation or in `extension/<name>/design/<design>/`,
  as [Templates and design](../../guides/templates-and-design.md#1-how-a-template-is-found) says.)

### Step 2: The design cascade (1 hour)

- Read: [The design cascade](../designers/theme-design/the-design-cascade.md),
  [Templates and design](../../guides/templates-and-design.md) section 1.
- Check: why do you never edit `design/standard/templates/`? (It is the fallback; you override it instead.)

### Step 3: See which template wrote what (1 hour)

- Read: [Template path comments](../../features/6.0/template-path-comments.md),
  [Templates and design](../../guides/templates-and-design.md#3-see-which-template-wrote-which-markup).
- Practice: switch the path comments on for your study site, find three templates in the page source, switch them
  off again.

### Step 4: The pagelayout (1 hour)

- Read: [The pagelayout](../designers/theme-design/the-pagelayout.md).
- Practice: find the pagelayout template of your design with the path comments.

### Step 5: Templates and overrides (3 hours)

- Read: [Templates overview](../designers/templates/templates-overview.md),
  [Override basics](../designers/templates/override-ini-basics.md),
  [Learning the template language](learning-the-template-language.md).
- Practice: [project 3](project-03-a-template-override.md).

### Step 6: Pages with blocks (2 hours)

- Read: [The layouts editor](../layouts-editor.md).
- Practice: [project 4](project-04-a-page-with-layouts.md). Look at which block views your design offers in the
  block's **Design** tab.

### Step 7: Themes as extensions (2 hours)

- Read: [Themes are extensions](../extension-development/part-6-theme-developers/themes-are-extensions.md),
  [A theme by hand](../extension-development/part-6-theme-developers/a-theme-by-hand.md),
  [A theme with the RAD wizard](../extension-development/part-6-theme-developers/a-theme-with-the-rad-wizard.md).
- Practice: create a design extension with the wizard at `/setup/designextension`
  ([Extensions](../../guides/extensions.md#62-let-the-rad-tools-write-it)), switch it on, and override one template in it.

### Step 8: Accessibility and right to left (2 hours)

- Read: the trainers' [Theme design](../trainers/theme-designers/README.md) course, module 5, and
  [Languages](../languages.md).
- Practice: check one page of your design with the keyboard only, and note what you cannot reach.

## Rules to learn early

- **No inline CSS in administration templates.** The administration designs keep all CSS in stylesheets, use
  shared components and tokens, and give every new token a dark-mode value; a test enforces it
  ([Styling the administration](../../bc/6.0/admin4-styling.md)).
- **Escape what users type.** `wash` on every value from content or visitors
  ([Templates and design](../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes)).
- **Override, do not edit.** Changes go in your own design or extension, so updates do not overwrite them.

## Going further

| Topic | Pages |
|---|---|
| Every designer topic | [Designers: start here](../designers/designers-start-here.md) |
| Publishing a theme | [Distribution: start here](../designers/distribution/distribution-start-here.md) |
| Maintaining a theme | [Lifecycle: start here](../designers/lifecycle/lifecycle-start-here.md) |
| Dark mode and tokens | [Dark mode and tokens in themes](../extension-development/part-6-theme-developers/dark-mode-and-tokens-in-themes.md) |

## Where the path leads next

- Study projects: [2](project-02-a-small-site-structure.md), [3](project-03-a-template-override.md),
  [4](project-04-a-page-with-layouts.md), [capstone](project-10-capstone-a-complete-small-site.md).
- With a trainer: [Theme design](../trainers/theme-designers/README.md), 8 modules, about 24 hours.
- Next path: [developer](learning-path-developer.md), when you want to write the PHP behind a design.

## Related pages

- [Choosing a learning path](choosing-a-learning-path.md)
- [Learning the template language](learning-the-template-language.md)
- [Building a portfolio](building-a-portfolio.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
