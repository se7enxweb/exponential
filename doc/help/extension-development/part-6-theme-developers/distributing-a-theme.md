---
title: Distributing a theme
audience: designer, developer, app-creator
category: extension-development
order: 80
summary: Publish a theme others can install - licenses of the code, fonts, icons and images it carries, metadata, Composer, content class packages, screenshots and a README - with a checklist before the first release.
keywords: distribute, theme, publish, license, fonts, icons, images, credits, composer, packagist, packages, ezpkg, README, screenshot
type: help
---
# Distributing a theme

Part 6, chapter 8. A theme is the most visible extension there is, and the easiest to reuse. Before you publish one,
you have to be sure you may give away everything in it: code is only part of a theme. This chapter covers what is
different about publishing a theme; the general release procedure is [Part 7](../part-7-releasing/README.md).

## Everything in a theme has a license

| Part | Typical license | What it requires |
|---|---|---|
| Templates, CSS, JavaScript you wrote | yours to choose; GPL-2.0-or-later matches the kernel | the license file |
| Kernel templates you copied and changed | the kernel's license, GPL 2 or later | keep it; your copy is GPL |
| A CSS or JS library (an icon font, a lightbox) | often MIT | its license file next to it |
| Fonts | often the SIL Open Font License | its license file; no selling the font alone |
| Icons | MIT, CC BY, or the set's own license | credit where CC BY asks for it |
| Photos and illustrations | anything, often not free | check each one; replace any you may not redistribute |
| Logos | usually not licensed at all | ship a neutral placeholder, never a company's real logo |

The last two rows are where themes go wrong. Demo photos bought for one site may not be passed on. A theme shipped
with a customer's logo gives that logo to everyone. Replace them with free images (with their credit) or simple
placeholders.

Note that a template copied from the kernel is GPL even if the rest of your theme is under another license, so the
simplest choice for a theme is GPL-2.0-or-later for all of it.

## Credits in the theme

- A `LICENSE` file for the theme.
- Each library's, font's and icon set's license file next to it (sevenx_themes_simple keeps its third-party CSS
  under `stylesheets/libs/`).
- An `Includes` entry in `ezinfo.php` for each bundled library or font, so the about page lists it
  ([Attribution and copyright notices](../part-5-extend-fork-contribute/attribution-and-copyright-notices.md)).
- A "Credits" section in the README naming images and their authors where their license asks for it.

## Content classes: packages

A theme's templates expect content classes (`article`, `blog_post`, `event`). A site that installs the theme may not
have them. Ship them as packages: export each class as an `.ezpkg` file into the theme's `packages/` folder, as
sevenx_themes_simple does, and say in the README how to import them
([Packages and the package creator](../part-7-releasing/packages-ezpkg.md)). Never ship a database dump.

## Settings the site must make

The site still has to choose your design ([Themes and siteaccesses](themes-and-siteaccesses.md)). Write the exact
lines in the README. If you ship `settings/siteaccess/<name>/` in the theme, say which siteaccess names it expects.

## composer.json

A theme is installed like any extension ([Modernizing composer.json](../part-4-upgrading/modernizing-composer-json.md)):

```json
{
    "name": "your-account/mytheme",
    "description": "My theme: a clean, responsive theme for Exponential 6 sites, with dark mode.",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "keywords": ["exponential", "theme", "design"],
    "require": {
        "php": "^8.0",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": {
        "ezpublish-legacy-extension-name": "mytheme"
    }
}
```

## The README of a theme

A theme's README should show the theme. Include:

1. a screenshot of the front page and of one content page, in light and dark mode;
2. what it needs (content classes, other extensions such as ezjscore);
3. installation: Composer, activation, the siteaccess settings, importing the packages;
4. what can be customized by settings, and how to build a variant on top of it
   ([Themes and siteaccesses](themes-and-siteaccesses.md), "A theme on top of a theme");
5. credits and licenses.

## Before the first release

- [ ] Every image, font, icon and library is licensed for redistribution, with its license file.
- [ ] No real company logo, no customer content, no photos of real people without their permission.
- [ ] No node IDs or host names of your own site in templates or settings.
- [ ] No editor backup files or unused minified copies.
- [ ] Works in light and dark mode, and at phone width.
- [ ] Works under a Content Security Policy with `'self'` ([Theme assets, the packer and CSP](theme-assets-the-packer-and-csp.md)).
- [ ] `ezinfo.php`, `extension.xml` and `composer.json` carry the version and license.
- [ ] The README has screenshots and installation steps.

Then release it as [Part 7](../part-7-releasing/README.md) describes, and tell people where they look for themes
([Growing the community through code](../part-5-extend-fork-contribute/growing-the-community-through-code.md)).

## Related pages

- Previous: [Dark mode and design tokens in themes](dark-mode-and-tokens-in-themes.md)
- Next: [Where the designers' help continues](where-the-designers-help-continues.md)
- [Licenses and how they combine](../part-5-extend-fork-contribute/licenses-and-compatibility.md)
- [Packages](../../../guides/packages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
