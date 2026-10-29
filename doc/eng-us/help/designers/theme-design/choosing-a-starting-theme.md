---
title: Choosing a starting theme
audience: designer, project-manager
category: theme-design
order: 80
summary: Whether to start a new design from scratch, from the simple, media or starter theme, or from ezwebin, with the trade-offs of each and a short decision table.
keywords: starting theme, base theme, which theme, from scratch, simple theme, media theme, starter theme, ezwebin, choose design, new site look, theme decision
type: help
---
# Choosing a starting theme

This page is for designers and project managers at the start of a site project. It answers: should the new look be
built from nothing, or on top of a design this installation already has, and which one.

## Contents

1. [The four ways to start](#the-four-ways-to-start)
2. [Decision table](#decision-table)
3. [Start from scratch](#start-from-scratch)
4. [Start on top of simple or ezwebin](#start-on-top-of-simple-or-ezwebin)
5. [Start from media or starter](#start-from-media-or-starter)
6. [Whatever you choose](#whatever-you-choose)

## The four ways to start

1. **From scratch**: your own design with its own pagelayout, falling back only to `standard` (and `base`).
2. **On top of a classic design**: `simple` (extension `sevenx_themes_simple`) or `ezwebin` in your
   `AdditionalSiteDesignList[]`, so you inherit their content views and only change what you need.
3. **On top of a layouts-based design**: `media` (extension `sevenx_themes_media`) or `starter` (extension
   `cjw_themes_jumper`), which draw pages with the layouts editor's zones and blocks.
4. **A fork**: copy a theme extension under a new name and change it freely (covered in the life cycle pages of this
   help).

## Decision table

| If your project ... | Start |
|---|---|
| has its own complete HTML/CSS from an agency or a style guide | from scratch ([Tutorial: port a static HTML design](tutorial-port-a-static-html-design.md)) |
| uses the content classes of the default installation (article, blog, folder, product ...) and wants a quick result | on top of `simple` |
| needs pages that editors compose from blocks (landing pages, campaigns) | on top of `media` or `starter`, or from scratch with the layouts templates ([Layouts for designers](layouts-for-designers.md)) |
| must look like an existing site that uses ezwebin | on top of `ezwebin` |
| is a small brochure site | from scratch: a pagelayout, one stylesheet, a few overrides |

## Start from scratch

**What you get:** full control, no unused CSS or JavaScript, markup you know line by line.

**What it costs:** you write the pagelayout, the menu, the breadcrumb and the full and line views of every class the
site shows. Everything else falls back to `design/standard`, whose markup is plain and unstyled.

**Settings:**

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=standard
```

This is what the `starter` siteaccess of this installation does (its design `starter` with only `standard` below it).

## Start on top of simple or ezwebin

**What you get:** working templates for the ezwebin family of classes (articles, blogs, galleries, events, products,
forums), a header and footer, paging, search pages.

**What it costs:** their markup and class names come with them; your CSS styles their HTML. Their stylesheets are
loaded through `CSSFileList[]`; leave them out by giving your design its own page head template.

**Settings:**

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=simple
AdditionalSiteDesignList[]=ezwebin
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

Copy only the templates you want to change into `mytheme`, at the same path.

## Start from media or starter

**What you get:** complete, modern designs with many view templates, the layouts editor's block templates and a tested
responsive and accessible frame.

**What it costs:** they are large (the media design has several hundred templates and about 166 override rules, by its
own README) and depend on their content classes (`ng_*` classes of the media site data). They suit sites that use
that content model.

## Whatever you choose

- [ ] Keep your design in an extension of your own ([Design extensions](design-extensions.md)).
- [ ] Never edit the files of a shipped or Composer-managed theme; override them from your design instead.
- [ ] Plan dark mode, right-to-left and accessibility from the first stylesheet, not at the end
      ([Design tokens](design-tokens.md), [Right-to-left designs](right-to-left-designs.md),
      [Accessibility basics for themes](accessibility-basics-for-themes.md)).
- [ ] Decide early whether pages are composed with the layouts editor; it changes the pagelayout.

## Related pages

- [The themes on this installation](themes-that-ship-with-exponential.md)
- [Tutorial: create a theme extension](tutorial-create-a-theme-extension.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
