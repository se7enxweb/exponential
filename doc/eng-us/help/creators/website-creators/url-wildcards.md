---
title: URL wildcards and custom aliases
audience: website-creator, admin
category: website-creation
order: 440
summary: Make short addresses and pattern redirects in the administration - global URL aliases for module views, URL wildcards with placeholders, redirect or direct, testing an address, and the order in which they are tried.
keywords: URL wildcards, global URL alias, URL translator, urltranslator, urlwildcards, placeholders, redirect, direct, short URL, campaign URL, website creator
type: help
---
# URL wildcards and custom aliases

This page is for website creators who need addresses the content tree does not make by itself: short campaign
addresses, friendly addresses for module pages (`/login`), and patterns that send a whole family of old addresses to
new ones. Reference: [Links, URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md).

## Contents

1. [Three kinds of addresses](#three-kinds-of-addresses)
2. [Global URL aliases](#global-url-aliases)
3. [URL wildcards](#url-wildcards)
4. [Redirect or direct](#redirect-or-direct)
5. [Order of resolution](#order-of-resolution)
6. [Examples](#examples)

## Three kinds of addresses

| Kind | Made where | Example |
|---|---|---|
| Node alias | automatically, and on the node's URL aliases tab | `/About-us` |
| Global alias | **Setup > URL translator** (`/content/urltranslator`) | `login` for `user/login` |
| Wildcard | **Setup > URL wildcards** (`/content/urlwildcards`) | `news/*` to `articles/{1}` |

## Global URL aliases

Fields: **New URL alias** (without host and leading slash), **Destination** (a module view such as `user/login` or an
existing address), **Language**, **Include in other languages**, and **Alias should redirect to its destination**.
An alias to `content/view/full/<node>` becomes an alias of that node and appears on the node's tab instead. The list
marks aliases whose module no longer exists.

![URL translator: the global URL aliases and the form to add one](../../images/creators/url-translator.webp)

## URL wildcards

Each `*` in the pattern matches any text; `{1}`, `{2}` in the destination put it back. The page refuses a destination
that uses a placeholder the pattern has no `*` for. **Try an address** shows which wildcard an address matches, what it
becomes and whether visitors are redirected, without changing anything.

![URL wildcards: address patterns that redirect or forward, and the form to add one](../../images/creators/url-wildcards.webp)

| Pattern | Destination | `news/2026/october` becomes |
|---|---|---|
| `news/*` | `articles/{1}` | `articles/2026/october` |
| `news/*/*` | `archive/{2}/{1}` | `archive/october/2026` |
| `old-blog/*` | `https://blog.example.org/{1}` | a redirect to the other site |

## Redirect or direct

| | Redirect (301) | Direct |
|---|---|---|
| Visitor sees | the destination's address | the address they typed |
| Search engines | index the destination | may index both |
| Use for | old addresses, short campaign addresses | friendly addresses for module views |

Prefer redirect: one address per page is better for search engines.

## Order of resolution

1. URL aliases (node and global) first.
2. Wildcards only when no alias matches, in the order they were created; the first match wins.
3. Otherwise the module view itself (`/content/view/...`), else "not found".

A wildcard that "does nothing" is usually shadowed by an alias or an older wildcard.

## Examples

- Print campaign: `autumn` redirects to `/Campaigns/Autumn-2026`.
- Sign-in: `login` to `user/login`, direct.
- Old site: `products/*.html` cannot be expressed literally; `products/*` to `Products/{1}` with the old `.html`
  addresses handled as aliases on the nodes that differ.

## Related pages

- [Redirects and moved content](redirects-and-moved-content.md)
- [URL aliases and nice URLs](url-aliases-and-nice-urls.md)
- [Settings page](../../../guides/settings-page.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
