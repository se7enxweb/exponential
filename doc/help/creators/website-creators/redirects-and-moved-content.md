---
title: Redirects and moved content
audience: website-creator, admin
category: website-creation
order: 430
summary: Keep old addresses working - automatic redirects after a rename or move, global aliases and wildcards for old site addresses, server redirects for hosts and HTTPS, safe redirects in forms, and a relaunch redirect plan.
keywords: redirects, 301, moved content, renamed, old URLs, history alias, relaunch, migration, wildcard, global alias, safe redirects, host redirect, website creator
type: help
---
# Redirects and moved content

This page is for website creators who move, rename or relaunch content and want old links to keep working. Broken
addresses lose visitors and search ranking; Exponential handles most cases on its own.

## Contents

1. [What happens automatically](#what-happens-automatically)
2. [Redirects you set up](#redirects-you-set-up)
3. [Relaunching a site: a redirect plan](#relaunching-a-site-a-redirect-plan)
4. [Redirects in the web server](#redirects-in-the-web-server)
5. [Safe redirects inside the application](#safe-redirects-inside-the-application)
6. [Checklist](#checklist)

## What happens automatically

| Event | Result |
|---|---|
| A node is renamed | a new alias is made; the old one stays and redirects to the new one |
| A node is moved | the same: the old path redirects |
| A translation is renamed | the same, per language |
| A node is removed | the address answers "not found"; nothing redirects |

So editors may rename and move freely. Removing is different: when a page goes, decide where its visitors should go
instead and make an alias or a wildcard for it.

## Redirects you set up

| Need | Tool |
|---|---|
| One old address to one page | the node's **URL aliases** tab: add the old address as an alias of the node |
| One address to a module view | **Setup > URL translator**: a global alias, "Alias should redirect" checked (301) |
| A group of old addresses | **Setup > URL wildcards**: `news/*` to `articles/{1}`, "Redirecting URL" checked |
| Addresses of another site | a wildcard to an `https://` destination |

Wildcards are only consulted when no alias matches, in the order they were created; **Try an address** shows which
wildcard an address matches without changing anything
([URL wildcards](../../../guides/urls-and-aliases.md#4-url-wildcards)).

## Relaunching a site: a redirect plan

1. Export the old site's addresses (its sitemap, or the search engine's list of indexed pages, or the server's logs).
2. For each, decide the new destination: same page, a better page, or none (a "gone" page).
3. Add patterns as wildcards and single pages as aliases of their node.
4. Before launch, test every old address with `curl -sI <old address>` and check for a `301` to the right place.
5. After launch, watch the error log and the search engines' crawl errors for misses.

| Old address | New destination | Kind |
|---|---|---|
| `/news/2019/*` | `/News/{1}` | wildcard |
| `/about.html` | node "About us" | alias on the node |
| `/shop/cart` | `shop/basket` | global alias |

## Redirects in the web server

Host and scheme redirects belong in the web server or Velocity, before Exponential runs: `http` to `https`, `example.com`
to `www.example.com`. See [Going live: hosts, HTTPS and siteaccesses](going-live-hosts-https.md) and
[Velocity HTTPS certificates](../../../features/6.0/velocity-https-certificates.md).

## Safe redirects inside the application

Forms and buttons that send visitors "back where they came from" only follow addresses of the site itself, so an
attacker cannot use them to send visitors elsewhere ([Safe redirects](../../../features/6.0/safe-redirects.md)). Use
the same functions in your own code (Forms, CSRF protection and HTTP input).

## Checklist

- [ ] Editors know renaming and moving keep old addresses working, removing does not.
- [ ] Removed pages get an alias or wildcard to a replacement.
- [ ] A relaunch has a tested redirect table.
- [ ] Host and HTTPS redirects are in the web server.
- [ ] Redirects are 301 (permanent) unless the move is temporary.

## Related pages

- [URL aliases and nice URLs](url-aliases-and-nice-urls.md)
- [URL wildcards and custom aliases](url-wildcards.md)
- [Moving from an older installation](../../../install/14-migrating-from-4x.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
