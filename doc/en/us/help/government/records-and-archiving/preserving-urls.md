---
title: Preserving URLs
audience: government, editor, admin
category: public-sector
order: 190
summary: For public bodies whose addresses are cited in decisions, letters and laws - how Exponential keeps URLs stable (URL aliases, history aliases that redirect after a rename or move, global aliases, wildcards), what breaks them (removal), and practices for citable addresses.
keywords: URLs, permanent links, persistent URLs, URL aliases, history aliases, redirects, renames, moves, global URL aliases, URL translator, URL wildcards, link rot, citations, content/view/full, public sector
type: help
---
# Preserving URLs

This page is for editors and administrators. Public bodies cite web addresses in letters, decisions and other
publications; an address that breaks makes those documents harder to use. Exponential keeps addresses stable through
renames and moves; removal is the main way to break them
([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)).

## Contents

1. [What happens automatically](#what-happens-automatically)
2. [Tools for stable addresses](#tools-for-stable-addresses)
3. [Practices](#practices)
4. [Checking](#checking)
5. [Related pages](#related-pages)

## What happens automatically

| Event | Result |
|---|---|
| a node is renamed | a new alias is made; the old one stays and redirects to the new one |
| a node is moved | the same: the old path redirects |
| a translation is renamed | the same, per language |
| a node is removed | the address answers "not found"; nothing redirects |

## Tools for stable addresses

| Need | Tool |
|---|---|
| a short, citable address for a page (`/accessibility`, `/privacy`) | an alias on the node (its **URL aliases** tab) |
| an address to a module view | **Setup > URL translator**: a global alias, optionally redirecting (301) |
| a group of old addresses after a restructuring | **Setup > URL wildcards** |
| a removed page's visitors sent somewhere useful | an alias or wildcard to the replacement or to an archive page |

Details: [Links, URL aliases, wildcards](../../../guides/urls-and-aliases.md),
[URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md).

## Practices

- Cite the alias, not the system URL (`/content/view/full/<node id>`); request rules or request-shield may refuse
  system URLs for visitors.
- Prefer archiving (moving, hiding with a notice page, an archive state) to removing pages that were cited.
- Before removing, check incoming links: Setup > Link management lists outgoing links; search your own documents for
  the address; look at `404.log` after the change.
- For documents (PDFs), cite the page that holds the file rather than the file's download address, so the
  document can be replaced without breaking the citation.

## Checking

```bash
curl -sI https://www.example.gov/<old address> | head -3     # 301 to the new address
```

and watch **Setup > Log files** > "Not found requests (404)" after restructurings.

## Related pages

- [Redirects after restructuring](redirects-after-restructuring.md)
- [Links, URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md)
- [URL wildcards](../../creators/website-creators/url-wildcards.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
