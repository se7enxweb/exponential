---
title: A staff-only siteaccess
audience: government, admin
category: public-sector
order: 30
summary: How to set up an intranet siteaccess in Exponential that requires sign-in for every page (RequireUserLogin), with its own root folder, design and address, which views stay open, and how to keep it out of search engines.
keywords: intranet siteaccess, RequireUserLogin, AnonymousAccessList, staff only, sign-in required, root node, internal host, robots, siteaccess
type: help
---
# A staff-only siteaccess

This page is for administrators. A whole siteaccess can require sign-in, which suits an intranet
([Member registration and members-only areas](../../../creators/website-creators/member-registration-and-areas.md#a-members-only-area)).

## Contents

1. [The settings](#the-settings)
2. [Views that stay open](#views-that-stay-open)
3. [Address and network](#address-and-network)
4. [Keeping it out of search engines](#keeping-it-out-of-search-engines)
5. [Checks](#checks)

## The settings

Set up the siteaccess like a department site (see "Adding a department site" in the multisite section), with its root
folder and design, and add:

```ini
# settings/siteaccess/intranet/site.ini.append.php
[SiteAccessSettings]
RequireUserLogin=true
```

With `RequireUserLogin=true`, a visitor who is not signed in is asked to sign in before any page. Check the value your
installation's global settings and public siteaccesses use, so only the intranet requires it.

## Views that stay open

`[SiteAccessSettings] AnonymousAccessList[]` lists the modules or views that do not require sign-in. The shipped list
includes `user/register`, `user/success`, `user/activate` and `user/forgotpassword`; for an intranet, remove
`user/register` from the intranet siteaccess so nobody can create an account there:

```ini
AnonymousAccessList[]
AnonymousAccessList[]=user/forgotpassword
AnonymousAccessList[]=csp/report
```

Keep the entries your installation needs (check the comments in `settings/site.ini`).

## Address and network

Give the intranet its own host, ideally reachable only from the internal network or a VPN, set in the web server or
firewall. Sign-in is the second line of defense, not the only one.

## Keeping it out of search engines

Pages that require sign-in are not readable by crawlers. Add a `robots.txt` for the intranet host that disallows
everything ([robots.txt](../../../creators/website-creators/robots-txt.md)).

## Checks

- Open the intranet address in a private window: the sign-in page appears for every page.
- Sign in as a test staff account: internal pages open.
- The public site cannot show internal content (see "Sections and roles for intranet content").

## Related pages

- [Sections and roles for intranet content](sections-and-roles-for-intranet-content.md)
- [Going live: hosts, HTTPS and siteaccesses](../../../creators/website-creators/going-live-hosts-https.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
