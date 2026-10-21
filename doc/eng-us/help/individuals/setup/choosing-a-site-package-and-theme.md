---
title: Choosing a site package and theme
audience: individual, website-creator
category: personal-sites
order: 1632
summary: Which site package and theme an individual should start from on Exponential - the default sevenx_multisite and its clean variant, the simpler sevenx_site, the older ezwebin, ezflow and ezdemo packages, the themes behind them including sevenx_themes_simple and sevenx_themes_media, and how much you can change without programming.
keywords: site package, theme, design, sevenx_multisite, sevenx_multisite_clean, sevenx_site, ezwebin, ezflow, ezdemo, plain_site, sevenx_themes_simple, sevenx_themes_media, look and feel, branding, templates, individual
type: help
---
# Choosing a site package and theme

This page is for individuals who are choosing what their new site starts from. A **site package** is the starting
point of an installation: content classes, a design, content, roles and settings. A **theme** is the design part,
kept as an extension ([Site packages and the themes that ship](../../creators/website-creators/site-packages-and-shipped-themes.md)).

## Contents

1. [The packages you will meet](#the-packages-you-will-meet)
2. [Full or clean](#full-or-clean)
3. [The themes](#the-themes)
4. [Which start for which site](#which-start-for-which-site)
5. [How much you can change without programming](#how-much-you-can-change-without-programming)
6. [Changing your mind later](#changing-your-mind-later)

## The packages you will meet

The package index of this version offers eleven site packages; these are the ones that matter
([4.3 Site packages](../../../install/04-choosing-an-install-method.md#43-site-packages-what-is-installed)):

| Package | What you get |
|---|---|
| `sevenx_multisite` | The default: classes, design, demo content, roles (Members, Partners) and a Restricted section |
| `sevenx_multisite_clean` | The same without demo content |
| `sevenx_site`, `sevenx_site_clean` | The simpler website interface |
| `ezwebin_site`, `ezflow_site`, `ezdemo_site` (each also `_clean`), `plain_site` | Older packages, kept for sites built on them |

The default package installs the classes this section relies on: *Blog*, *Blog post*, *Comment*, *Gallery*, *Image*,
*Event*, *Event calendar*, *Poll*, *Feedback form* and the forum classes (the `sevenx_classes` package).

## Full or clean

| | Full | `_clean` |
|---|---|---|
| Classes, design, roles | Yes | Yes |
| Demo articles and images | Yes | No |
| Use it for | Learning: see how every class is meant to look | Your real site |

Install the full package once on your own computer to learn, and the clean one for the real site.

## The themes

| Theme (extension) | Design | Notes |
|---|---|---|
| `sevenx_themes_simple` | `simple` | Used by the default installation; has the blog, comment and gallery templates ([sevenx_themes_simple](../../../features/6.0/extensions/sevenx_themes_simple.md)) |
| `ezwebin` | `ezwebin` | The older website interface; the source of many shared templates ([ezwebin](../../../features/6.0/extensions/ezwebin.md)) |
| `ezdemo` | `ezdemo` | The demo package's design ([ezdemo](../../../features/6.0/extensions/ezdemo.md)) |
| `ezflow` | `ezflow` | Block-based landing pages of the older kind ([ezflow](../../../features/6.0/extensions/ezflow.md)) |
| `sevenx_themes_media` | `media` | A complete media-site front end built for Exponential Layouts; installed with the code (root `composer.json`), not one of the site packages above, and a larger theme than a personal site usually needs |

Check the installed versions on the about page (`/ezinfo/about`).

## Which start for which site

| Your site | Start from |
|---|---|
| Personal site, blog, portfolio | `sevenx_multisite_clean` |
| Family site | `sevenx_multisite_clean` (it already has a Restricted section and member roles) |
| Club site | `sevenx_multisite_clean` |
| You want to learn first | `sevenx_multisite` (full) on your own computer |
| You continue an old site built on `ezwebin` | Keep its package and design |

## How much you can change without programming

| Change | How | Skill |
|---|---|---|
| Site name, logo, some colors | Settings and the design's options ([Branding your site](../../creators/website-creators/branding-your-site.md)) | None |
| Menu entries and order | The content tree ([Menus and navigation](../../creators/website-creators/menus-and-navigation.md)) | None |
| Page layouts with blocks | Exponential Layouts ([Layouts editor](../../layouts-editor.md)) | None |
| Fonts, colors in detail, spacing | Stylesheets ([Stylesheets and scripts](../../creators/website-creators/stylesheets-and-scripts.md)) | CSS |
| How a page type looks | Template overrides ([Template overrides](../../creators/website-creators/template-overrides.md)) | HTML and the template language |
| New fields | **Setup > Classes** ([Choosing datatypes](../../creators/website-creators/choosing-datatypes.md)) | Care; test first |

Make design changes in your own design extension, not in the shipped theme, so updates do not overwrite them
([Designs and the fallback chain](../../creators/website-creators/designs-and-the-fallback-chain.md)).

## Changing your mind later

- A package is a snapshot; after the install you change classes and design in the site itself.
- Switching to another theme later is possible but means checking every page type; plan it like a small redesign
  ([Choosing a design or theme](../../creators/website-creators/choosing-a-design-or-theme.md)).
- Reinstalling to try another package replaces the database: never do it on a site with real content.

## Related pages

- [Setup for individuals](README.md)
- [Site packages and the themes that ship](../../creators/website-creators/site-packages-and-shipped-themes.md)
- [Site packages](../../site-packages/README.md)
- [Choosing a design or theme](../../creators/website-creators/choosing-a-design-or-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
