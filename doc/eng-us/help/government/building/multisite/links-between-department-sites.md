---
title: Links between department sites
audience: government, admin, website-creator
category: public-sector
order: 70
summary: Why links from one department site to content of another can point into the wrong site, how the ezurlmultisite extension makes ezurl produce the address of the site that serves the content, and how to configure and check it.
keywords: cross-site links, ezurlmultisite, PathPrefix, RootNodeList, SiteAccessList, ezurl, multisite links, 404, department sites
type: help
---
# Links between department sites

This page is for administrators and site builders of an installation where one content tree serves several sites. A
page of one site that lists content of another (a tag page, a search result, a related item) can otherwise produce
addresses that do not exist ([Links to content of another site](../../../../features/6.0/multisite-cross-site-links.md)).

## Contents

1. [The problem](#the-problem)
2. [The fix: ezurlmultisite](#the-fix-ezurlmultisite)
3. [Configuration](#configuration)
4. [Checking](#checking)

## The problem

Each site hides its subtree's path with `PathPrefix`. A node of another site keeps that site's path in its URL alias,
and `ezurl` puts the current siteaccess in front, giving an address that answers "not found".

## The fix: ezurlmultisite

The extension `ezurlmultisite` (required by the root `composer.json`) makes `ezurl` and the media theme's link
operators give the address on the siteaccess that serves the content. Links within the current site, module links,
files and every link made on a siteaccess that does not take part (all administration siteaccesses included) stay as
they were. It costs no database query per link.

## Configuration

`settings/override/ezurlmultisite.ini.append.php`, `[MultisiteSettings]`:

| Key | Meaning |
|---|---|
| `SiteAccessList[]` | the sites, in order of preference |
| `RootNodeList[<siteaccess>]=<node id>` | the root node of each site's subtree |

Without the file, the public siteaccesses with a `PathPrefix` take part, each with the node of its `content.ini`
`RootNode` or of its `IndexPage`.

## Checking

On site A, open a page that lists an item of site B (a search for it, a tag page); the link must point to site B's
host and open the item. Check also the language versions.

## Related pages

- [Links to content of another site](../../../../features/6.0/multisite-cross-site-links.md)
- [Shared content across sites](shared-content-across-sites.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
