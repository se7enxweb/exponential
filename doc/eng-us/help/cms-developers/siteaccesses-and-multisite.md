---
title: Siteaccesses and multisite
audience: cms-developer
category: cms-development
order: 21
summary: For CMS developers, how one installation serves several faces such as public sites, languages, the administration and an editor interface. It covers how a request picks its siteaccess, what a siteaccess decides, subtree sites with PathPrefix, links between sites, settings that belong to an extension, and when separate installations are better.
keywords: siteaccess, multisite, several sites, MatchOrder, URI matching, host matching, port matching, AvailableSiteAccessList, RelatedSiteAccessList, PathPrefix, RootNode, IndexPage, ezurlmultisite, sevenx_multisite, admin, editor siteaccess, settings per site
type: help
---
# Siteaccesses and multisite

This page is for developers who build more than one site, language or interface on one installation. A
**siteaccess** is one way into the installation: a name, a folder of settings and a rule for which requests it
answers. All siteaccesses share one database and one content tree, so content can be shared between sites while
each site keeps its own design, languages and starting point.

## Contents

1. [What a siteaccess decides](#what-a-siteaccess-decides)
2. [How a request picks its siteaccess](#how-a-request-picks-its-siteaccess)
3. [The siteaccesses of a new installation](#the-siteaccesses-of-a-new-installation)
4. [Several sites in one tree](#several-sites-in-one-tree)
5. [Links between sites](#links-between-sites)
6. [Keeping site settings in an extension](#keeping-site-settings-in-an-extension)
7. [Reading what a siteaccess sees](#reading-what-a-siteaccess-sees)
8. [One installation or several](#one-installation-or-several)

## What a siteaccess decides

| Decides | Settings |
|---|---|
| The design | `site.ini [DesignSettings] SiteDesign`, `AdditionalSiteDesignList[]` |
| The content languages | `[RegionalSettings] SiteLanguageList[]`, `Locale` ([Translations and languages](translations-and-languages.md)) |
| Where the site starts in the tree | `content.ini [NodeSettings] RootNode`, `site.ini [SiteSettings] IndexPage`, `DefaultPage`, `[SiteAccessSettings] PathPrefix` |
| Whether visitors must sign in | `[SiteAccessSettings] RequireUserLogin` |
| Extensions on for this face only | `[ExtensionSettings] ActiveAccessExtensions[]` |
| Template rules | its own `override.ini.append.php` |

A commented public siteaccess is in
[Siteaccess settings](../extension-development/part-2-builder/siteaccess-settings.md#a-public-siteaccess-commented).

## How a request picks its siteaccess

`site.ini [SiteAccessSettings]` lists every siteaccess in `AvailableSiteAccessList[]` and tries the matchers of
`MatchOrder` in turn; the shipped order is `uri;host;port`.

| Matcher | Example | Typical use |
|---|---|---|
| URI (`URIMatchType=element`) | `example.com/admin/...`, `example.com/ger/...` | one host name, languages as path elements |
| Host (`HostMatchMapItems[]=www.example.com;site`) | `admin.example.com`, `www.example.de` | production, a certificate per host |
| Port | `:8081` | local work |

`RelatedSiteAccessList[]` names the siteaccesses that share content and links. Every matching rule and how to test
it: [Siteaccess matching by host and language](../../features/6.0/siteaccess-host-and-language-matching.md) and
[Multisite on one installation](../hosting/provisioning/multisite-on-one-installation.md#matching-by-url-host-or-port).

## The siteaccesses of a new installation

An installation has at least a public siteaccess and the administration; `exp:install` also creates an `editor`
siteaccess with a simpler interface for editors ([Setup wizard and editor siteaccess](../../features/6.0/setup-wizard-and-editor-siteaccess.md)).
The `sevenx_multisite` site package creates more: two demo sites (Fit & Healthy and Bold Agency), one siteaccess per
extra language, `admin`, `editor` and, with `exp_adminui`, `adminui`
([Siteaccesses and languages of sevenx_multisite](../site-packages/sevenx-multisite/siteaccesses-and-languages.md)).
Reading how that package wires its sites is the fastest way to learn a multisite setup.

## Several sites in one tree

Each site gets its own subtree below node 2 and its own siteaccess (or one per language). In the siteaccess:

- `content.ini [NodeSettings] RootNode` and `site.ini [SiteSettings] IndexPage` point at the site's top node, so the
  front page and menus start there;
- `[SiteAccessSettings] PathPrefix` hides the subtree's own path in the URLs, so `/bold-agency/services` is served as
  `/services` on the Bold site (`PathPrefixExclude[]` lists paths that keep the full address);
- a section per site lets roles and template rules tell the sites apart ([Sections, roles and policies](sections-roles-and-policies.md)).

Shared content (a media library, people, a news pool) lives outside the site subtrees and is related, embedded or
fetched by each site.

## Links between sites

When one site links to a node of another site, the default `ezurl` would put the current siteaccess in front of the
other site's path. The `ezurlmultisite` extension, required by the root `composer.json`, makes `ezurl` produce the
address on the siteaccess that serves the content; it is configured in `ezurlmultisite.ini [MultisiteSettings]`
(`SiteAccessList[]`, `RootNodeList[<siteaccess>]`) ([Cross-site links](../../features/6.0/multisite-cross-site-links.md)).

## Keeping site settings in an extension

A site's siteaccess settings belong under version control, in your site extension:
`extension/<ext>/settings/siteaccess/<siteaccess>/` for one siteaccess, and `extension/<ext>/settings/override/` for
settings that apply to every siteaccess of that extension
([INI overrides per site extension](../../features/6.0/multi-site-ini-overrides.md)). The theme extension
`sevenx_themes_media` ships the Bold siteaccesses this way. How the scopes win over each other:
[Settings and INI for CMS developers](settings-and-ini-for-cms-developers.md).

## Reading what a siteaccess sees

```bash
./console exp:ini scopes siteaccess
./console exp:ini get site.ini/DesignSettings/SiteDesign -s <siteaccess>
./console exp:ini where content.ini/NodeSettings/RootNode -s <siteaccess>
./console exp:ezrequestrules --help
```

In a template the current siteaccess is `$access_type.name`; in PHP, `eZSiteAccess::current()`.

## One installation or several

One installation fits sites that share content, editors or a design system, and that are upgraded together. Separate
installations fit sites that need separate databases, separate upgrade schedules or strict isolation between
customers ([One installation or several?](../hosting/provisioning/multisite-on-one-installation.md#one-installation-or-several)).
Logs and caches can be kept per site even on one installation
([Logs and cache paths per site](../../features/6.0/multi-site-log-and-cache-paths.md)).

## Related pages

- [URL aliases and routing](url-aliases-and-routing.md)
- [Designs and siteaccesses](../designers/theme-design/designs-and-siteaccesses.md)
- [Tutorial: two sites, two designs](../designers/theme-design/tutorial-two-sites-two-designs.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
