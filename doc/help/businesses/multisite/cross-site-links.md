---
title: Links between your sites
audience: businesses, project-manager, admin
category: business-solutions
order: 50
summary: Why a link from one brand's page into another brand's content can lead to a 404 on a multisite installation, how the ezurlmultisite extension sends visitors to the right site, and what to test before launch.
keywords: cross-site links, ezurlmultisite, multisite, 404, PathPrefix, SiteAccessList, RootNodeList, brands, broken links, business
type: help
---
# Links between your sites

This page is for managers and project leads of organizations that run several brands from one content tree. When a
page of one brand lists content that belongs to another (a tag page, a search result, a "related" teaser), the link
must lead to the other brand's address. This page explains why that does not happen by itself, what fixes it, and
what to check, so that visitors never land on an error page between your own sites.

## Contents

1. [The problem in plain words](#the-problem-in-plain-words)
2. [The fix: ezurlmultisite](#the-fix-ezurlmultisite)
3. [How a link finds its site](#how-a-link-finds-its-site)
4. [What stays unchanged](#what-stays-unchanged)
5. [What the business should ask for](#what-the-business-should-ask-for)
6. [Testing before launch](#testing-before-launch)

## The problem in plain words

Each brand hides the path of its own folder from its addresses. A page of brand B keeps brand B's path in its address,
and the link builder of the current site puts the current brand in front of it. The result is an address on brand A
that does not exist, and the visitor gets a 404
([Links to content of another site of a multisite installation](../../../features/6.0/multisite-cross-site-links.md#in-short)).

The feature page uses the multisite demo as its example: Fit & Healthy on `/site` or `/`, Bold Agency on `/bold`, and
their translations. A link from Bold Agency to a Fit & Healthy video would become `/bold/fit-healthy/video/...`,
which answers "module not found".

For a business this matters because such links are often created automatically: lists by tag, search results and
related items. Nobody types them, so nobody notices until a customer does.

## The fix: ezurlmultisite

The extension `ezurlmultisite` (`se7enxweb/ezurlmultisite`, required by the root `composer.json`) makes the standard
link builder `ezurl`, and the media theme's own link operators, give the address on the siteaccess that serves the
content (same source).

| | |
|---|---|
| Configuration | `settings/override/ezurlmultisite.ini.append.php`: the list of participating sites, in order of preference, and the root node of each site's subtree |
| New installations | The multisite installers (`sevenx_multisite`, `sevenx_multisite_clean`, `cjw_multisite`) activate the extension and write the file with the node ids of the installation they create |
| Without the file | Public siteaccesses with a path prefix take part, each with the node of its root or start page |
| Cost | No database query per link; the siteaccess table is read once per request (once per worker under Exponential Velocity) |

> **Warning**
> The node ids in the configuration differ between installations. They must never be copied from another
> installation; your technical team reads them from your own siteaccess settings.

## How a link finds its site

In short, from the feature page:

1. For a piece of content, the site is the one whose root folder is the deepest on the content's path.
2. When that is another site, the address is built from that site's own address, without its hidden path.
3. On the same host the link stays host-relative, so scheme and port are kept; on another host it becomes a full
   address such as `https://bold.example.com/services`.
4. A site and its translations share a subtree: the current siteaccess wins, then one in the current language, then
   the first in the list.

That last rule matters for multilingual brands: a German visitor following a link into the other brand stays in
German where that brand has a German siteaccess.

## What stays unchanged

- Links within the current site.
- Module links such as the login page or search.
- Files and design paths.
- Every link made on a siteaccess that does not take part, which includes every administration siteaccess.

## What the business should ask for

- [ ] Is `ezurlmultisite` active on our installation, and does its configuration list every public brand?
- [ ] Were the node ids taken from **our** installation?
- [ ] After adding a new brand, was the configuration updated and were the caches cleared?
- [ ] Do custom templates or extensions build links themselves? If so, do they ask the extension too? (The feature
      page shows developers how, under "For extension developers".)

After a change, the technical team regenerates autoloads and clears the INI, template and content caches, because
compiled templates keep the old link code until they are compiled again. Under Velocity, `./console exp:velocity
deploy` does all of it (same source).

## Testing before launch

| Test | Expected |
|---|---|
| A tag page on brand A lists an item from brand B; click it | Brand B's page opens, at brand B's address |
| A search on brand A finds brand B content; click it | Brand B's page, no 404 |
| The same in each language siteaccess | The visitor stays in their language where possible |
| A link inside brand A | Unchanged, stays on brand A |
| An administration preview | Unchanged |

Add these to the launch checklist of every new brand ([Multi-brand checklist](multi-brand-checklist.md)).

## Related pages

- [Sharing content across brands](shared-content-across-brands.md)
- [Links to content of another site of a multisite installation](../../../features/6.0/multisite-cross-site-links.md)
- [Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md#links-between-the-sites)
- [Broken links and content checks](../../creators/website-creators/link-and-content-checks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
