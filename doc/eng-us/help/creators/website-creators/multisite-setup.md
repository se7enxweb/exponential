---
title: Setting up several sites in one installation
audience: website-creator, admin
category: website-creation
order: 240
summary: Run several websites from one Exponential installation - one siteaccess per site, a root folder per site, per-site designs and settings in extensions, host matching, cross-site links and the operational trade-offs.
keywords: multisite, multi-site, several sites, siteaccess, host matching, HostMatchMapItems, RootNode, IndexPage, RelatedSiteAccessList, site extension, ezurlmultisite, website creator
type: help
---
# Setting up several sites in one installation

This page is for website creators who build more than one site on one Exponential installation: several brands,
regions, or a main site and microsites. Each site is one or more **siteaccesses**: a name, its settings in
`settings/siteaccess/<name>/`, and a rule for which requests it answers
([10.2 Siteaccesses and site addresses](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).

## Contents

1. [Shapes of a multisite](#shapes-of-a-multisite)
2. [Step by step: a second site](#step-by-step-a-second-site)
3. [Settings per site in an extension](#settings-per-site-in-an-extension)
4. [Links between the sites](#links-between-the-sites)
5. [Editors and the administration](#editors-and-the-administration)
6. [Trade-offs](#trade-offs)

## Shapes of a multisite

| Shape | How |
|---|---|
| Separate content per site | one folder per site under the root; each siteaccess uses its folder as root |
| Shared content, different designs | the same root, a different `SiteDesign` per siteaccess |
| One site in several languages | one siteaccess per language ([Building a multilingual site](multilingual-sites.md)) |
| Each site in its own extension | settings that apply only while the site's siteaccesses run ([Per-site settings](../../../features/6.0/multi-site-ini-overrides.md)) |

The default site package, `sevenx_multisite`, is itself a multisite installation.

## Step by step: a second site

1. **Content.** Create a folder `Site B` under the root and note its node ID.
2. **Siteaccess.** Copy `settings/siteaccess/site` to `settings/siteaccess/site_b` and set in
   `site_b/site.ini.append.php`:

   ```ini
   [SiteSettings]
   SiteName=Site B
   SiteURL=www.site-b.example
   IndexPage=/content/view/full/<node id of Site B>
   DefaultPage=/content/view/full/<node id of Site B>

   [DesignSettings]
   SiteDesign=siteb
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=simple
   ```

   and in `site_b/content.ini.append.php`:

   ```ini
   [NodeSettings]
   RootNode=<node id of Site B>
   ```

3. **Register and match it** in `settings/override/site.ini.append.php`:

   ```ini
   [SiteAccessSettings]
   AvailableSiteAccessList[]=site_b
   RelatedSiteAccessList[]=site_b
   MatchOrder=host;uri
   HostMatchType=map
   HostMatchMapItems[]=www.site-b.example;site_b
   ```

   Keep the entries the installer wrote for the other siteaccesses; add yours. `HostMatchMethod=start` also lets a
   test host such as `www.site-b.example.test.local` reach the same siteaccess
   ([Siteaccess by host prefix](../../../features/6.0/siteaccess-host-and-language-matching.md)).
4. **Web server and DNS.** Add the host name to DNS, to the web server or Velocity, and to the certificate
   ([Going live: hosts, HTTPS and siteaccesses](going-live-hosts-https.md)).
5. **Clear caches and check.**

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   curl -sI https://www.site-b.example/ | head -1
   ```

6. **Let the administration know the site**: add `site_b` to `[SiteSettings] SiteList[]` of the admin siteaccess, so
   previews and links can use it.

## Settings per site in an extension

When each site is an extension (`extension/site_b/`), it can carry `settings/override/` files that apply only while
one of its own siteaccesses runs, plus `settings/siteaccess/<name>/` per siteaccess. Clearing the INI cache clears it
for every site ([Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md)). That keeps
each site's design, settings and templates together in one Git repository.

## Links between the sites

A link in the content of site A to a page that lives in site B's folder must point to site B's host. The `ezurlmultisite`
extension resolves such links ([Links to content of another site](../../../features/6.0/multisite-cross-site-links.md)).

## Editors and the administration

- One administration can serve every site; roles limited by subtree keep each team in its site's folder
  ([Roles and policies](../../../guides/roles-and-policies.md)).
- Several administration siteaccesses are possible when teams should not see each other's sites at all.

## Trade-offs

| Strength | Constraint |
|---|---|
| one kernel, one set of extensions to update and secure | every site moves with the others on an upgrade |
| shared content and media, one search index | a heavy site slows the others on the same machine |
| one backup | a restore restores all sites |

Write the trade-off into the plan ([Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md)).

## Related pages

- [Building a multilingual site](multilingual-sites.md)
- [Structuring the content tree](structuring-the-content-tree.md)
- Multisite siteaccess cookbook (`doc/eng-us/examples/cookbook/multisite-siteaccess.md`)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
