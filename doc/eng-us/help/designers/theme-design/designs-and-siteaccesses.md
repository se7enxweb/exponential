---
title: Designs and siteaccesses
audience: designer, admin
category: theme-design
order: 20
summary: How a request reaches a siteaccess (by path, host or port), where a siteaccess names its designs, and how several sites of one installation share or separate their looks.
keywords: siteaccess, site access, design per site, SiteDesign, AdditionalSiteDesignList, MatchOrder, URIMatchType, HostMatchType, host, path prefix, multisite, preview site
type: help
---
# Designs and siteaccesses

This page is for designers who need to know which design a page is drawn with, and where to change that. It answers:
what a siteaccess is, how a request is matched to one, where a siteaccess names its designs, and how two sites can
share one design or use two.

## Contents

1. [What a siteaccess is](#what-a-siteaccess-is)
2. [How a request finds its siteaccess](#how-a-request-finds-its-siteaccess)
3. [Where a siteaccess names its designs](#where-a-siteaccess-names-its-designs)
4. [The siteaccesses of this installation](#the-siteaccesses-of-this-installation)
5. [Sharing settings between siteaccesses](#sharing-settings-between-siteaccesses)
6. [Which design is in use right now](#which-design-is-in-use-right-now)
7. [Common questions](#common-questions)

## What a siteaccess is

A siteaccess is one way into an installation. It has a name (a folder `settings/siteaccess/<name>/`) and its own
settings: which design, which languages, which start node, whether visitors must sign in. One installation usually
has at least two: a public site and the administration. Both read the same content database; they look different
because they use different designs.

A siteaccess is active when it is listed in `site.ini [SiteAccessSettings] AvailableSiteAccessList[]` (in the
installation's `settings/override/site.ini.append.php`, which an administrator maintains).

## How a request finds its siteaccess

`site.ini [SiteAccessSettings] MatchOrder` lists the methods tried, in order. The shipped default
(`settings/site.ini`) is:

```ini
MatchOrder=uri;host;port
URIMatchType=element
URIMatchElement=1
```

- **uri**: with `URIMatchType=element` and `URIMatchElement=1`, the first path element is the siteaccess name:
  `https://example.com/eng/news` is siteaccess `eng`. Other types are `map`, `text` and `regexp`.
- **host**: `HostMatchType=map` with `HostMatchMapItems[]=<host>;<siteaccess>` maps host names. Since 6.0 a method
  can be added per entry (`strict`, `start`, `end`, `part`), see
  [Siteaccess by host prefix](../../../features/6.0/siteaccess-host-and-language-matching.md).
- **port**: a port number maps to a siteaccess.

When nothing matches, `site.ini [SiteSettings] DefaultAccess` is used.

Matching is an administrator's job, but you need to know it to preview a design: a new siteaccess for your design is
reached by its path element (`/mytheme/`) as soon as it is listed and the uri method is in `MatchOrder`.

## Where a siteaccess names its designs

In `settings/siteaccess/<name>/site.ini.append.php`:

```ini
[DesignSettings]
SiteDesign=mytheme
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

- `SiteDesign` is searched first.
- `AdditionalSiteDesignList[]` are searched next, in the order listed. The empty `AdditionalSiteDesignList[]` line
  first clears what a lower settings file set.
- `StandardDesign` (default `standard`, from `settings/site.ini`) is searched last.

A theme extension may also ship these lines for a siteaccess of a known name, in
`extension/<extension>/settings/siteaccess/<siteaccess>/site.ini.append.php`. The media theme does so for `site` and
`bold`; the starter theme ships them for `cjw_starter`.

## The siteaccesses of this installation

The designs below are read from `settings/siteaccess/*/site.ini.append.php` of this installation (design lines only):

| Siteaccess | SiteDesign | Searched after it |
|---|---|---|
| `site` | `media` | `ezwebin`, `standard`, `base` |
| `bold` | `media` | `simple`, `ezwebin`, `standard`, `base` |
| `starter` | `starter` | `standard` |
| `portaljq` | `expportal_jquery` | `standard`, `base` |
| `admin` | `admin4l` | `admin4`, `admin3`, `admin2`, `admin` |
| `editor` | `editor` | `admin4l`, `admin4`, `admin3`, `admin2`, `admin` |
| `adminui` | `adminui` | `admin4l`, `admin4`, `admin3`, `admin2`, `admin` |
| `classic` | `admin` | (none) |

Two things to learn from this table:

- **One design can serve several siteaccesses.** `site` and `bold` both use `media`; only their fallback lists differ.
- **An administration design is a stack too.** `admin4l` is a small design on top of `admin4`, which sits on `admin3`
  and so on. A new admin look can be made the same way (see [Admin designs overview](admin-designs-overview.md)).

## Sharing settings between siteaccesses

A translation siteaccess (for example `starter_ger` next to `starter`) should look exactly like its original. Instead of
copying every file, `site.ini [SiteAccessSettings] ExtensionSettingsSiteAccess` names another siteaccess whose extension
settings it reads as well. `settings/siteaccess/starter/site.ini.append.php` sets:

```ini
[SiteAccessSettings]
ExtensionSettingsSiteAccess=cjw_starter
```

so the starter theme's settings in `extension/cjw_themes_jumper/settings/siteaccess/cjw_starter/` (its overrides,
its design lines) apply to `starter` and `starter_ger` alike. The setting is read only from the siteaccess's own
`site.ini.append.php` (comment in `settings/site.ini`).

## Which design is in use right now

- In a template, `$site.design` holds the `SiteDesign` of the current siteaccess (set by the kernel in
  `kernel/private/classes/ezpkernelweb.php`).
- With `site.ini [TemplateSettings] ShowTemplatePathComments=enabled`, every template writes an HTML comment with its
  file path, so the page source shows which design each part came from
  ([Template path comments](../../../features/6.0/template-path-comments.md)).
- With `ShowUsedTemplates=enabled` (debug output on), the debug panel lists every template used.

## Common questions

**I changed SiteDesign but nothing happened.** The INI cache still has the old value. Clear it with
`php bin/php/ezcache.php --clear-tag=ini` (the tag also covers the global INI cache), and the template override cache
with `--clear-id=template-override`.

**Can a signed-in user switch design?** In the administration, yes: the interface switch lets a user choose another
admin design ([Interface switch: design](../../../features/6.0/ui-switch-design.md)).

**Can two sites share templates but differ in color?** Yes: put the shared templates in one design and give each
siteaccess a small design of its own, with only its stylesheet, as `SiteDesign`, and the shared design in
`AdditionalSiteDesignList[]` (see [Designs in a multisite installation](designs-in-multisite.md)).

## Related pages

- [What a design is](what-a-design-is.md)
- [The design cascade](the-design-cascade.md)
- [Previewing a design on a siteaccess](previewing-a-design-on-a-siteaccess.md)
- [Multi-site INI overrides](../../../features/6.0/multi-site-ini-overrides.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
