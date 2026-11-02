---
title: "Static publishing and your hosting"
audience: hosting
category: hosting
order: 901
summary: For hosting providers and administrators - with static publishing the dynamic installation can stay internal, behind a firewall or VPN, while the public site is static; what the build needs from the network, the server and the cluster, and what it costs.
keywords: hosting, hosting provider, firewall, VPN, internal installation, security, cost, cluster, DFS, static site, GitHub Pages, gh-pages, free hosting, free website, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing, outbound git
type: help
---
# Static publishing and your hosting

Static publishing splits a site in two: the Exponential installation where people edit, and a static copy that
visitors get. For hosting, that changes what must be public, what must be patched quickly and what it costs.

## What you'll have at the end

A plan for where the editing installation runs, what it needs from the network, and which hosting costs disappear.

## The editing installation can stay internal

The public site is served by GitHub Pages or another static host. The Exponential installation then only needs to be
reachable by editors: on an internal network, behind a VPN, or behind an IP allow list.

What it needs:

- **Outbound** HTTPS (or SSH) to the git host for the push, and to `api.github.com` for the GitHub setup.
- **Its own web server reachable from itself**: the build crawls the site over HTTP. Set `CrawlURL` to an internal
  address such as `http://127.0.0.1/` when the public name does not resolve inside.
- **Inbound only for forms** if you use the form endpoint: the static site's visitors post to
  `staticpublish/form` on a public address. Without that, forms show a link instead.

## Resources

- A build renders every page once. Plan for a few minutes of CPU for hundreds of pages; partial builds of single
  pages run on content publish.
- Disk: the target folder holds one copy of the site plus its git history. Builds are kept below
  `var/<site>/static-publish/`.
- Builds and publishes run as console processes, never inside a web request, and only one per target at a time.

## Clusters and Velocity

Files below `var/` are read through the cluster file handler, so DFS clusters work. Under Velocity the job starts as a
detached console process. See the [feature page](../../features/6.0/static-publishing.md#platforms).

## Costs

- The public site's hosting: free on GitHub Pages for public repositories, or the cost of a plain static web server.
- The editing installation: smaller than a public one, because it carries editors only, not visitors.
- Less on-call work: a security release of PHP or Exponential is no longer an emergency for the public site.

Limits of GitHub Pages: [GitHub Pages limits and terms](../static-publishing/github-pages-limits-and-terms.md).
Keeping private content off the copy: [Keep private content private](../static-publishing/keep-private-content-private.md).

## Make yours now

Try it on a staging site: [Make a free website in 15 minutes](../static-publishing/make-a-free-website-in-15-minutes.md).
