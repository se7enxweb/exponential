---
title: Siteaccess settings
audience: developer, designer, app-creator
category: extension-development
order: 30
summary: A siteaccess is one face of an installation - a public site, a language, the administration. How a request picks its siteaccess, which settings define one, and a complete commented example of a public siteaccess.
keywords: siteaccess, site access, MatchOrder, URIMatchType, AvailableSiteAccessList, RelatedSiteAccessList, SiteDesign, AdditionalSiteDesignList, RegionalSettings, multisite, admin siteaccess
type: help
---
# Siteaccess settings

Part 2, chapter 3. One Exponential installation serves several "faces" from the same database: the public site, the
same site in another language, a second site, the administration, an editor's interface. Each face is a
**siteaccess**. This chapter explains how a request chooses its siteaccess and what goes into a siteaccess's settings.

## What a siteaccess decides

A siteaccess is a name (for example `site`, `ger`, `admin`) and a folder of settings,
`settings/siteaccess/<name>/`. Those settings decide, for every request that uses it:

| Decides | Typical settings |
|---|---|
| Which design draws the pages | `site.ini [DesignSettings] SiteDesign`, `AdditionalSiteDesignList[]` |
| Which language content is shown in | `site.ini [RegionalSettings] Locale`, `ContentObjectLocale`, `SiteLanguageList[]` |
| Where the site starts in the content tree | `content.ini [NodeSettings] RootNode`, `site.ini [SiteSettings] IndexPage`, `DefaultPage` |
| Whether visitors must sign in | `site.ini [SiteAccessSettings] RequireUserLogin` |
| Which extensions are on for this face only | `site.ini [ExtensionSettings] ActiveAccessExtensions[]` |
| The site name and meta data | `site.ini [SiteSettings] SiteName`, `MetaDataArray[]` |

## How a request picks a siteaccess

The rules are in `site.ini [SiteAccessSettings]`:

| Setting | Meaning |
|---|---|
| `AvailableSiteAccessList[]` | Every siteaccess of the installation |
| `MatchOrder` | Which matchers are tried, in order; the default is `uri;host;port` |
| `URIMatchType` | How the address matches; `element` means the first path element names the siteaccess (`/admin/...`) |
| `HostMatchMapItems[]` and related host settings | Map host names to siteaccesses (`www.example.com;site`, `admin.example.com;admin`) |
| `RelatedSiteAccessList[]` | Siteaccesses that share content and links, for example the languages of one site |

With URI matching, `https://example.com/ger/about` uses the siteaccess `ger` and shows `/about`. With host matching,
each siteaccess has its own host name. Every rule and its debugging tool is described in
[Siteaccess matching by host and language](../../../features/6.0/siteaccess-host-and-language-matching.md) and the
request rules command:

```bash
./console exp:ezrequestrules
```

## A public siteaccess, commented

A typical `settings/siteaccess/<name>/site.ini.append.php` of a public site (database details left out):

```ini
[SiteSettings]
SiteName=My site
# The front page and where "home" links go
IndexPage=/content/view/full/2
DefaultPage=/content/view/full/2

[SiteAccessSettings]
# Visitors do not have to sign in
RequireUserLogin=false
# The siteaccesses that share this site's content
RelatedSiteAccessList[]
RelatedSiteAccessList[]=site
RelatedSiteAccessList[]=ger
RelatedSiteAccessList[]=admin

[DesignSettings]
# Your own design first, then the ones it falls back to
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base

[RegionalSettings]
Locale=eng-US
ContentObjectLocale=eng-US
SiteLanguageList[]
SiteLanguageList[]=eng-US
```

The administration's siteaccess looks similar, with `SiteDesign=admin4` (or another admin design),
`RequireUserLogin=true`, and its own `AdditionalSiteDesignList[]`.

## Siteaccesses and extensions

Two settings connect them:

- `ActiveAccessExtensions[]` in a siteaccess's `site.ini` switches an extension on for that siteaccess only. Use it for
  extensions that only make sense on one face (an admin tool, one site's theme).
- An extension can **own** a siteaccess by carrying `extension/<ext>/settings/siteaccess/<sa>/`. Then the whole
  siteaccess's settings live in the extension, under version control. See
  [Settings that live in an extension](extension-settings-for-builders.md).

## Reading siteaccess settings

```bash
./console exp:ini list site.ini/DesignSettings -s <siteaccess>
./console exp:ini get site.ini/DesignSettings/SiteDesign -s <siteaccess>
./console exp:ini scopes siteaccess
```

`-s` evaluates the settings as that siteaccess sees them. `scopes siteaccess` lists the siteaccesses that have a
settings folder.

In a template, the current siteaccess is available as `$access_type.name` in the pagelayout; in PHP, from
`eZSiteAccess::current()`.

## Common mistakes

| Symptom | Cause |
|---|---|
| A new siteaccess shows "module not found" or the wrong site | it is not in `AvailableSiteAccessList[]`, or the matching rule sends the request elsewhere |
| The design does not change | an extension's `extension/<ext>/settings/site.ini.append.php` sets `SiteDesign` and wins over the siteaccess folder (see the order in [Settings scopes](settings-scopes-and-their-order.md)) |
| Links jump to another site | `RelatedSiteAccessList[]` or the host mapping |
| Content appears in the wrong language | `SiteLanguageList[]` (the first entry is the main language), `ShowUntranslatedObjects` |

## Related pages

- Previous: [Settings scopes and the order they win in](settings-scopes-and-their-order.md)
- Next: [Settings that live in an extension](extension-settings-for-builders.md)
- [Siteaccess matching by host and language](../../../features/6.0/siteaccess-host-and-language-matching.md)
- [Request rules](../../../features/6.0/request-rules.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
