---
title: Adding a department site
audience: government, admin, website-creator
category: public-sector
order: 30
summary: Step by step for administrators: give a department its own site in an existing Exponential installation, with a content folder, a siteaccess with its root node and design, registration and host matching, DNS and certificate, and the checks.
keywords: department site, subsite, new site, siteaccess, RootNode, IndexPage, SiteDesign, HostMatchMapItems, AvailableSiteAccessList, SiteList, multisite
type: help
---
# Adding a department site

This page is for administrators and site builders. It follows the steps of
[Setting up several sites in one installation](../../../creators/website-creators/multisite-setup.md#step-by-step-a-second-site)
for a department of a public body.

## Contents

1. [Content](#content)
2. [The siteaccess](#the-siteaccess)
3. [Register and match it](#register-and-match-it)
4. [Host, DNS and certificate](#host-dns-and-certificate)
5. [The administration](#the-administration)
6. [Checks](#checks)

## Content

Create a folder for the department under the root, for example "Department of Example", and note its node ID. Assign
it to a section for the department if editors of different departments must be kept apart (see "Editors and roles per
department").

## The siteaccess

Copy `settings/siteaccess/site` to `settings/siteaccess/dept_example` and set:

```ini
# settings/siteaccess/dept_example/site.ini.append.php
[SiteSettings]
SiteName=Department of Example
SiteURL=dept.example.gov
IndexPage=/content/view/full/<node id>
DefaultPage=/content/view/full/<node id>

[DesignSettings]
SiteDesign=dept_example
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=simple
```

and in `dept_example/content.ini.append.php`:

```ini
[NodeSettings]
RootNode=<node id>
```

## Register and match it

```ini
# settings/override/site.ini.append.php
[SiteAccessSettings]
AvailableSiteAccessList[]=dept_example
RelatedSiteAccessList[]=dept_example
MatchOrder=host;uri
HostMatchType=map
HostMatchMapItems[]=dept.example.gov;dept_example
```

Keep the entries the installer wrote; add yours.

## Host, DNS and certificate

Add the host name to DNS, to the web server or Velocity, and to the certificate
([Going live: hosts, HTTPS and siteaccesses](../../../creators/website-creators/going-live-hosts-https.md)).

## The administration

Add `dept_example` to `[SiteSettings] SiteList[]` of the admin siteaccess, so previews and links can use it.

## Checks

```bash
php bin/php/ezcache.php --clear-tag=ini
curl -sI https://dept.example.gov/ | head -1
```

Open the site signed out; check the front page, a page, search, and that pages of other departments are not reachable
under this host unless intended.

## Related pages

- [Per-site designs](per-site-designs.md)
- [Per-site settings in extensions](per-site-settings-in-extensions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
