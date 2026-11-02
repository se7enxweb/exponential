---
title: "What static publishing is"
audience: admin, website-creator, editor, non-technical, non-profit, local-business, individual, student
category: administration
order: 3
summary: Static publishing in plain words - a copy of your site as ordinary files, served for free, while editing stays in Exponential; how it differs from the static cache and what visitors see.
keywords: static publishing, what is static publishing, static site, static copy, plain HTML, free hosting, free website, GitHub Pages, github.io, static cache, difference, Jekyll alternative, Netlify, Jamstack, deploy
featured: true
type: help
---
# What static publishing is

## What you'll have at the end

A clear picture of what static publishing does, what it does not do, and whether you should read on.

## In one sentence

Static publishing makes a copy of your website as ordinary files (HTML pages, images, style sheets) and puts that copy
somewhere that serves files for free, such as GitHub Pages.

## How it works

1. You and your editors work in Exponential as always: write, review, publish.
2. When you want the public site to change, Exponential **builds** a copy: it visits every page the way a visitor who
   is not signed in would, saves what it sees and fixes the links so they work without Exponential.
3. You look at the copy, then **publish** it. The copy goes to a git repository; GitHub Pages (or another host) serves
   it to the world.

The public copy has no database and no PHP behind it. There is nothing on it to update, patch or break into, it loads
fast from GitHub's network, and it costs nothing to host.

## What visitors see

The same pages, menus, layouts, images and downloads as on your site. What needs a server to answer changes:

| On your site | On the static copy |
|---|---|
| Pages, images, galleries, downloads | the same |
| Search | a search page that runs in the browser, when the target has it switched on |
| Contact forms | sent to your live site, or a short note with a link to it |
| Login, comments, basket | left out, with a short note |

The details are in [Search, forms and comments on a static site](search-forms-and-comments-on-a-static-site.md).

## Static publishing and the static cache

Exponential has had a **static cache** for a long time. Both write pages to files, but they serve different needs:

| | Static cache | Static publishing |
|---|---|---|
| Where the files are | on your own server, next to Exponential | in a repository, served by another host |
| Who serves them | your web server, falling back to PHP for the rest | the static host alone |
| Purpose | make your own site faster | a public copy that needs no server of yours |
| Links | as on your site | rewritten to work on their own, at any address |
| History | none | every publish kept, rollback in one step |

You can use both. Moving from one to the other: [From static cache to static publishing](../../guides/from-static-cache-to-static-publishing.md).

## Next step

[Is static right for my site?](is-static-right-for-my-site.md), then
[Make a free website in 15 minutes](make-a-free-website-in-15-minutes.md).
