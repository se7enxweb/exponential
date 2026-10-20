---
title: "Part 6: Theme developers"
audience: designer, developer
category: extension-development
order: 60
summary: Part 6 of the book: themes are extensions. Build one with the RAD wizard or by hand, connect it to siteaccesses, load its assets under a Content Security Policy, support dark mode, distribute it.
keywords: book, part 6, theme, design extension, siteaccess, pagelayout, stylesheets, packer, CSP, dark mode, tokens, distribute
type: help
---
# Part 6: Theme developers

This is the sixth part of [Extension development: the book](../README.md). A theme is how a site looks: its
pagelayout, its templates for each kind of content, its stylesheets, scripts and images. In Exponential, a theme is an
extension with a design folder in it, so everything the earlier parts said about extensions applies, and this part
adds what is special about designs.

## The chapters

| Chapter | What it answers |
|---|---|
| [Themes are extensions](themes-are-extensions.md) | why a theme is packaged as an extension, and what that gives you |
| [The anatomy of a design extension](design-extension-anatomy.md) | every folder and file of a theme, from a real one |
| [A theme with the RAD wizard](a-theme-with-the-rad-wizard.md) | the quickest start, and what to check in what it writes |
| [A theme by hand](a-theme-by-hand.md) | the same, file by file, so you understand each one |
| [Themes and siteaccesses](themes-and-siteaccesses.md) | which site uses which theme, and how themes stack |
| [Theme assets, the packer and CSP](theme-assets-the-packer-and-csp.md) | stylesheets, scripts and images that load fast and pass the policy |
| [Dark mode and design tokens in themes](dark-mode-and-tokens-in-themes.md) | one set of variables, two color schemes |
| [Distributing a theme](distributing-a-theme.md) | licenses of fonts and images, Composer, packages |
| [Where the designers' help continues](where-the-designers-help-continues.md) | the template language and design help beyond this book |

## Who this part is for

- **Designers** who know HTML and CSS and want their design to become a site theme. Part 2 of this book
  ([The builder](../part-2-builder/README.md)) explains templates and overrides; read at least
  [The design chain](../part-2-builder/the-design-chain.md) first.
- **Developers** who package a theme for others, or for several sites of their own.

## Related pages

- Previous: [Case study: a pull request to an upstream author](../part-5-extend-fork-contribute/case-study-a-pull-request-to-an-upstream-author.md)
- Next: [Themes are extensions](themes-are-extensions.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [A design inside an extension](../part-3-extension-developer/design-in-an-extension.md)
- [Extension development: the book](../README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
