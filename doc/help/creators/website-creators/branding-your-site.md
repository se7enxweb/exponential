---
title: "Branding: site name, logo, favicon and meta tags"
audience: website-creator, admin
category: website-creation
order: 160
summary: Put your brand on an Exponential site - the site name and description in site.ini, the logo and footer through the page design object, favicons and the web manifest in your design, and the meta tags of every page.
keywords: branding, site name, SiteName, SiteDescription, MetaDataArray, logo, favicon, web manifest, page design, template_look, powered by, meta tags, Open Graph, website creator
type: help
---
# Branding: site name, logo, favicon and meta tags

This page is for website creators who replace the defaults with their own brand: the site name, the logo, the favicon,
the footer and the tags in the page head.

## Contents

1. [Site name and description](#site-name-and-description)
2. [The logo and the page design object](#the-logo-and-the-page-design-object)
3. [Favicons and the web manifest](#favicons-and-the-web-manifest)
4. [Meta tags](#meta-tags)
5. [Footer and "Powered by"](#footer-and-powered-by)
6. [Checklist](#checklist)

## Site name and description

In `site.ini [SiteSettings]` of each public siteaccess:

```ini
# settings/siteaccess/<site>/site.ini.append.php
[SiteSettings]
SiteName=Example Inc.
SiteURL=www.example.com
SiteDescription=Example Inc. builds bridges.
MetaDataArray[author]=Example Inc.
```

| Key | Used for |
|---|---|
| `SiteName` | titles (`$site.title`), mail subjects, `og:site_name` in the simple theme |
| `SiteURL` | links in mail, feeds and sitemaps; no scheme, no trailing slash |
| `SiteDescription` | the description meta tag in the simple theme's `page_head.tpl` |
| `MetaDataArray[]` | meta tags the pagelayout prints |

Clear the INI cache afterwards: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`. Several languages? Set
the name per language siteaccess ([Building a multilingual site](multilingual-sites.md)).

## The logo and the page design object

The ezwebin-based designs, the shipped simple theme included, read the logo from a **page design** object (class
identifier `template_look`, found with `fetch( 'content', 'class', hash( 'class_id', 'template_look' ) )` in the
pagelayout). Editors change the logo by editing that object in the administration; its image field holds the logo, with
its alternative text. Without an image, the theme falls back to `images/logo.png` of the design
(`{'logo.png'|ezimage}`).

For your own design you choose: a logo file in `images/` of your design extension (simplest), or a content object so
editors can change it.

## Favicons and the web manifest

The simple theme links its favicons in `link.tpl`, from `images/favicon/` of the design:

```text
<link rel="icon" type="image/png" sizes="32x32" href="{"favicon/favicon-32x32.png"|ezimage(no)}">
<link rel="apple-touch-icon" sizes="180x180" href="{"favicon/apple-icon-180x180.png"|ezimage(no)}">
<link rel="manifest" href="{"images/favicon/site.webmanifest.json"|ezdesign(no)}">
```

To replace them, put files with the same names in `images/favicon/` of your own design extension (listed before the
theme): the design chain finds yours first ([Designs and the fallback chain](designs-and-the-fallback-chain.md)).
A `favicon.ico` at the site root is served from the installation root.

## Meta tags

| Tag | Where it comes from in the simple theme |
|---|---|
| `<title>` | `$site.title`, built from the page and `SiteName` |
| `description` | `SiteDescription` |
| `og:title`, `twitter:title` | the page title |
| `og:site_name` | `SiteName` |
| `og:image`, `twitter:image` | `images/favicon/favicon-banner.png` of the design |

Per-page descriptions and keywords need a metadata field on the content; see
[Page metadata and social sharing](metadata-and-social-sharing.md).

## Footer and "Powered by"

The simple theme prints a "Powered by Exponential" notice in `page_footer.tpl` unless the **Hide powered by**
attribute (`hide_powered_by`) of the page design object is set; edit that object, no template change is needed
([sevenx_themes_simple](../../../features/6.0/extensions/sevenx_themes_simple.md)). Keeping the notice is a friendly way
to support the project.

## Checklist

- [ ] `SiteName`, `SiteURL` and `SiteDescription` set for every public siteaccess and language.
- [ ] The logo has a meaningful alternative text.
- [ ] Favicons and the manifest replaced in your design extension, not in the theme.
- [ ] The social sharing image shows your brand.
- [ ] Mail sent by the site carries the site name (Notifications and mail).

## Related pages

- [Choosing a design or theme](choosing-a-design-or-theme.md)
- [Brand and naming rules](../../business/brand-and-naming-rules.md)
- [SEO basics for Exponential sites](seo-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
