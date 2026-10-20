---
title: Stable URLs for datasets
audience: government, website-creator, editor, admin
category: public-sector
order: 80
summary: How to give every dataset an address that keeps working for years: what Exponential does automatically after renames and moves, what breaks an address, and the rules a data team should agree on.
keywords: stable URL, persistent URL, permalink, URL alias, redirect, history alias, content/view/full, download address, node ID, moved content, open data
type: help
---
# Stable URLs for datasets

This page is for website creators, editors and administrators who publish data that other people will link to,
download by script or cite. Re-users build on your addresses; a broken one breaks their work. The facts about
aliases and redirects come from [Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)
and [URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md).

## Contents

1. [The addresses a dataset has](#the-addresses-a-dataset-has)
2. [What Exponential keeps working](#what-exponential-keeps-working)
3. [What breaks an address](#what-breaks-an-address)
4. [Rules for the data team](#rules-for-the-data-team)
5. [A short, permanent address per dataset](#a-short-permanent-address-per-dataset)
6. [Checking addresses](#checking-addresses)

## The addresses a dataset has

| Address | Example | Stable while |
|---|---|---|
| URL alias of the page | `/Open-data/Street-lighting-requests` | the node exists; old aliases redirect after a rename or move |
| System address of the node | `/content/view/full/<node ID>` | the node exists |
| Download of a file | `/content/download/<object ID>/<attribute ID>/file/<file name>` | the object exists and the file name stays |
| Feed of the catalog | `/rss/feed/<address>` | the RSS export exists and is active |
| REST address | `/api/ezp/v1/content/node/<node ID>` | the node exists and the API stays enabled |

The node ID and object ID never change for the life of the object, whatever editors do with names and places.

## What Exponential keeps working

| Event | Result |
|---|---|
| The page is renamed | a new alias is made; the old one stays and redirects |
| The page is moved | the old path redirects |
| A translation is renamed | the same, per language |
| The file is replaced with one of the same name | the download address stays the same |

So editors can correct titles and reorganize folders without breaking links.

## What breaks an address

| Event | Result | Prevention |
|---|---|---|
| The node is removed | "not found"; nothing redirects | Do not remove datasets; mark them withdrawn (see "Versioning and updating datasets") |
| The file is replaced with one of another name | the old download address no longer matches | Keep file names stable, or link to the page |
| The catalog moves to another siteaccess or host | addresses on the old host stop | Redirect in the web server ([Going live](../../creators/website-creators/going-live-hosts-https.md)) |
| A global alias or wildcard is removed | its address stops | Keep a list of the aliases you promised |

## Rules for the data team

1. Link to the dataset **page**, not to a file, in announcements and catalogs.
2. Never remove a published dataset; withdraw it with a notice instead.
3. Keep file names stable across updates, or publish each period as its own distribution.
4. Give every dataset a short, permanent alias (below) and print it on the page as "Cite as".
5. Record moved or retired addresses in a redirect table before a relaunch
   ([Relaunching a site: a redirect plan](../../creators/website-creators/redirects-and-moved-content.md#relaunching-a-site-a-redirect-plan)).

## A short, permanent address per dataset

Give each dataset an extra alias such as `/data/street-lighting`:

1. Open the dataset page, the **URL aliases** tab.
2. Add the alias. It points to the node, so it keeps working through renames and moves.

For a pattern of old addresses, use **Setup > URL wildcards** (for example `datasets/*` to `data/{1}`), with
"Redirecting URL" checked, and test it with **Try an address**
([URL wildcards](../../../guides/urls-and-aliases.md#4-url-wildcards)).

## Checking addresses

- `curl -sI https://www.example.gov/data/street-lighting` should answer `200`, or `301` to the current page.
- **Setup > Link management** lists links in your content and their state
  ([Link list](../../../guides/urls-and-aliases.md#1-the-link-list)).
- `./console exp:verify_aliases` checks the URL alias table; `--fix` repairs what is safe to repair
  ([Content languages, section 10](../../../guides/content-languages.md#10-commands-for-the-shell)).

## Related pages

- [Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)
- [URL wildcards and custom aliases](../../creators/website-creators/url-wildcards.md)
- [Links, URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
