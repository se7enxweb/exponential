---
title: "Tools for theme designers"
audience: theme-designer
category: theme-design
order: 34
summary: The tools a theme designer uses with Exponential - the settings that reveal which template drew what, the debug output, packed or unpacked stylesheets, the preview siteaccess, the template editor and the design extension wizard in the administration, and the browser tools that do the rest.
keywords: theme designer, tools, template path comments, ShowTemplatePathComments, debug output, ShowUsedTemplates, packer, DevelopmentMode, preview siteaccess, template editor, design wizard, browser devtools
type: help
---
# Tools for theme designers

This page is for theme designers who want to know which tools exist, inside Exponential and around it, and which
settings a developer switches on for them. Most of these are development settings: they belong on a development or
preview siteaccess, never on the live site. Each row links to the page that explains it in depth.

## Contents

1. [Seeing which template drew what](#seeing-which-template-drew-what)
2. [The debug output](#the-debug-output)
3. [Packed or unpacked stylesheets](#packed-or-unpacked-stylesheets)
4. [Trying a design next to the live one](#trying-a-design-next-to-the-live-one)
5. [Tools in the administration](#tools-in-the-administration)
6. [Browser tools](#browser-tools)
7. [Tools for checking quality](#tools-for-checking-quality)
8. [Which setting goes where](#which-setting-goes-where)

## Seeing which template drew what

| Setting (`site.ini`, `[TemplateSettings]`) | What you see | Page |
|---|---|---|
| `ShowTemplatePathComments=enabled` | every rendered template wrapped in `<!-- START ... -->` and `<!-- STOP ... -->` comments with its path; works with the template compiler on | [Template path comments](../designers/templates/template-path-comments.md) |
| `Debug=enabled` with `ShowXHTMLCode=enabled` | the older template debug markers in the page | [Template debugging](../designers/templates/template-debugging.md) |
| `ShowUsedTemplates=enabled` (needs the debug output) | a table of all templates used for the page | [Debugging a design](../designers/theme-design/debugging-a-design.md#which-templates-were-used-at-all) |

The settings file itself warns that these add markup to every output, including mails, JSON and feeds. Use them on a
development siteaccess only.

## The debug output

`[DebugSettings] DebugOutput=enabled` adds a report below the page with errors, warnings, timings and, with the setting
above, the templates used. It can be limited to certain addresses (`DebugByIP` with `DebugIPList[]`). Warnings about
missing templates or unknown operators are often the answer to "why does my change not show"
([Debugging a design](../designers/theme-design/debugging-a-design.md#errors-and-warnings)).

## Packed or unpacked stylesheets

In normal operation, ezjscore packs the stylesheets and scripts of a page into cached files. While working on a design,
unpacked files are easier to read in the browser's style inspector. The packer follows
`[TemplateSettings] DevelopmentMode` by default, and can be set explicitly with `[eZJSCore] Packer` in `ezjscore.ini`
(the settings comment says it accepts `enabled`, `disabled` or a level from 0 to 3). After a stylesheet change on a
site with packing on, the packed file must be renewed; a developer does that with the deploy steps
([The packer cache and deploys](../designers/theme-design/packer-cache-and-deploy.md)). Packed file names already carry
the modification time, so never add `?v=` to a link.

## Trying a design next to the live one

A **preview siteaccess** serves the same content with your design, at its own address, while visitors keep the live
design. It is the right place for reviews ([Previewing a design on a siteaccess](../designers/theme-design/previewing-a-design-on-a-siteaccess.md)).

## Tools in the administration

| Tool | Where | Use |
|---|---|---|
| Template editor | Design tab, Template Editor | see a siteaccess's templates and their overrides in order; quick edits on a test site ([The template editor](../designers/templates/template-editor-overrides.md)) |
| Design extension wizard | Setup > RAD, `/setup/designextension` | generate a complete theme extension to start from ([The design extension wizard](../designers/extensions/design-extension-wizard.md)) |
| Layouts editor | Layouts | build test layouts with every block view type ([The layouts editor](../layouts-editor.md)) |
| Class definitions | Setup > Classes | read fields for the [mapping sheet](mapping-a-design-to-content-classes.md) |

Changes made in the template editor live only on that installation; a theme belongs in files under version control.

## Browser tools

The browser's developer tools do most of the visual work:

- the element inspector, with template path comments visible in the DOM;
- device emulation at 375 px, and real zoom at 200% ([Testing at zoom levels](../designers/theme-design/testing-at-zoom-levels.md));
- color scheme emulation (light and dark) and reduced motion emulation;
- color-vision deficiency emulation;
- the network panel for requests to other hosts and the performance audit for LCP and CLS
  ([Designing for performance budgets](designing-for-performance-budgets.md#checking-a-page-against-the-budget));
- the console, to see Content Security Policy violations ([Checklist: CSP](../designers/theme-design/checklist-csp.md)).

## Tools for checking quality

| Check | Tool | Page |
|---|---|---|
| Contrast | any contrast checker; the ratios written into the palette sheet | [Color contrast](../designers/theme-design/color-contrast.md) |
| Accessibility | the browser's accessibility tree and an automated checker, plus keyboard and screen reader by hand | [Checklist: accessibility](../designers/theme-design/checklist-accessibility.md) |
| Visual changes between releases | screenshots in light, dark and at zoom, compared | [Visual regression checks](../designers/lifecycle/visual-regression-checks.md) |
| Template syntax | the template check command a developer runs | [Template debugging](../designers/templates/template-debugging.md#syntax-check) |
| Admin styling rules | the design test in the test suite | [The admin styling rules test](../designers/theme-design/admin-styling-rules-test.md) |

## Which setting goes where

Development settings go in the development siteaccess's `site.ini.append.php` or in a settings override of the
development installation, set by the developer or administrator. They never go into your theme extension, because a
theme that switches on debug output would switch it on for every site that installs it
([The design settings a theme ships](../designers/extensions/design-settings-a-theme-ships.md)).

## Related pages

- [Debugging a design](../designers/theme-design/debugging-a-design.md)
- [Lesson 1.3: Which template wrote this markup?](../trainers/theme-designers/module-01-designs-and-the-fallback-chain/which-template-wrote-this-markup.md)
- [Caching for designers](../designers/theme-design/caching-for-designers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
