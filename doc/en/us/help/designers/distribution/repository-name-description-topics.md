---
title: "Repository name, description and topics"
audience: designer, sales-marketing
category: theme-distribution
order: 40
summary: Choose a repository name, a one-line description and GitHub topics that let people find a theme, with the choices the published Exponential themes made.
keywords: repository name, description, topics, GitHub topics, search, discoverability, keywords, responsive-design, exponential
type: help
---
# Repository name, description and topics

This page is for designers who want their theme found by search on GitHub, on Packagist and on the web.

## Name

Use the extension name as the repository name (`cjw_themes_jumper`, `sevenx_themes_simple`). Then the repository,
the folder and the settings all say the same thing ([Naming your theme extension](../extensions/naming-your-theme-extension.md)).

## Description

One sentence: what it is, for what, on what. Real examples:

| Theme | Description (GitHub) |
|---|---|
| `sevenx_themes_simple` | "7x Exponential : Default Design Theme : Simple" |
| `sevenx_themes_media` | "Converted Netgen media-site theme for Exponential Legacy / Exponential 6 - hand-maintained eZ4 templates, media design, template operators and siteaccess settings ..." (it continues with the demo sites it serves) |
| `cjw_themes_jumper` | "Converted Exponential Platform Nexus v2.5+ media-site based design theme for Exponential Legacy / Exponential 6 - hand-maintained eZ4 templates, ... for the CJW JAC Example Startersite demo site." |

The two long ones show a trade-off: complete, but cut off in search results after about a line. A good pattern: "<Name>: a <kind of> theme for Exponential 6, with <the two features people look for>." Use the same
sentence as `description` in `composer.json` so Packagist shows it too.

## Topics

GitHub topics are lower case words with dashes. The published themes use, for example:

| Theme | Topics |
|---|---|
| `cjw_themes_jumper` | `exponential`, `exponential-layouts`, `responsive`, `responsive-design`, `responsive-layout`, `responsive-theme`, `responsive-ui` |
| `sevenx_themes_media` | `exponential-layouts-design`, `responsive`, `responsive-design`, `responsive-layout` |
| `sevenx_themes_simple` | `responsive-design` |

Choose topics people would type: the product (`exponential`), the kind (`theme`, `design`), the qualities
(`responsive-design`, `accessibility`, `dark-mode`), the purpose (`blog`, `ecommerce`).

## Step by step

1. Write the sentence and five to ten topics.
2. Apply them:

   ```bash
   gh repo edit yourvendor/mytheme --description "..." --add-topic exponential --add-topic theme
   ```

3. Put the same sentence into `composer.json` `description` and `keywords`.
4. Search GitHub for `topic:exponential` a day later and check that your theme appears.

## Related pages

- [A README that explains and sells](readme-that-explains-and-sells.md)
- [Writing a theme's product page](../sales/product-page.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
