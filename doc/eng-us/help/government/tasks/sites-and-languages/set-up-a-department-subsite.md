---
title: Set up a department subsite
audience: government, admin
category: public-sector
order: 140
summary: Task for administrators: add a site for a department to an existing installation, with its own folder, siteaccess, host and administration link.
keywords: department site, subsite, siteaccess, RootNode, IndexPage, HostMatchMapItems, multisite, new site
type: help
---
# Set up a department subsite

This task is for administrators. Background: [Adding a department site](../../building/multisite/adding-a-department-site.md).

## Before you start

- The department's host name (for example `dept.example.gov`) in DNS and the certificate, or a path.
- A design for it, or the shared one.

## Steps

1. Create the department's folder under the root; note its node ID.
2. Copy `settings/siteaccess/site` to `settings/siteaccess/dept_example`; set `SiteName`, `SiteURL`, `IndexPage` and
   `DefaultPage` (`/content/view/full/<node id>`), `SiteDesign`, and `content.ini [NodeSettings] RootNode=<node id>`.
3. In `settings/override/site.ini.append.php`: `AvailableSiteAccessList[]=dept_example`,
   `RelatedSiteAccessList[]=dept_example`, and `HostMatchMapItems[]=dept.example.gov;dept_example` (keep existing
   entries).
4. Add `dept_example` to `[SiteSettings] SiteList[]` of the admin siteaccess.
5. Add the host to the web server or Velocity.
6. `php bin/php/ezcache.php --clear-tag=ini`.

## Check

`curl -sI https://dept.example.gov/ | head -1` answers 200; the front page is the department's folder.

## Problems

| Problem | Fix |
|---|---|
| The host shows the main site | `HostMatchMapItems[]` missing or `MatchOrder` lacks `host` |
| Links point to the wrong site | see "Link from one department site to another" |

## Related pages

- [Setting up several sites](../../../creators/website-creators/multisite-setup.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
