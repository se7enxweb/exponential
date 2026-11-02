---
title: "Offer static hosting to your clients"
audience: integrator, developer, admin
category: integration
order: 551
summary: For integrators and resellers - add static publishing to your offer, with GitHub Pages or your own git host, one target per client, credentials per repository, and a clear line between what a static copy can and cannot do.
keywords: integrator, reseller, agency, service offer, managed hosting, static hosting, free hosting, static site, GitHub Pages, gh-pages, GitLab Pages, Gitea, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing, multisite
type: help
---
# Offer static hosting to your clients

Static publishing is built into Exponential 6.0.15, so you can offer it without new software: the client edits in
Exponential, and the public site is a static copy on GitHub Pages, on another git host's pages, or on a plain web
server you run.

## What you'll have at the end

- A service you can describe in one sentence: "Edit in Exponential, published as a fast static site."
- A repeatable setup: one target per client site, one repository and one credential per target.

## Three ways to offer it

| Offer | Where the copy lives | Who pays |
|---|---|---|
| Free | GitHub Pages, public repository in the client's account | nobody; you charge for setup and care |
| Managed | your GitLab, Gitea or Forgejo with pages, or a static web server | you, in your hosting fee |
| Hybrid | GitHub Pages for the public site, your server for forms and search | the client pays only for the editing installation |

Any git host works for the push: [Other git hosts: GitLab, Gitea and your own server](../static-publishing/other-git-hosts-gitlab-gitea-and-your-own-server.md).

## Running many clients

- One installation with many sites: each front siteaccess gets its own target, its own repository and its own
  credential name. A target never pushes to another client's repository.
- Give each client's editors the `staticpublish/*` policies limited to their target.
- Credentials are names in Setup > Secrets or environment variables; their values never appear in settings, logs or
  site packages. See [Targets and credentials](../../guides/static-publishing-targets-and-credentials.md).
- The cronjob part `staticpublish` retries pending pushes and runs schedules for all targets.

## Be clear about the limits

Write them into your offer: no shop, no member area, forms only through the live site, GitHub's size limits and
terms. See [GitHub Pages limits and terms](../static-publishing/github-pages-limits-and-terms.md) and
[Is static right for my site?](../static-publishing/is-static-right-for-my-site.md)

## Talking to clients

The editing installation can sit behind your firewall or a VPN; the public site has no PHP and no database, so there
is nothing to patch on that side. Every publish is a tagged commit, and going back is one click.

## Make yours now

Set up one client site as a pilot: [Make a free website in 15 minutes](../static-publishing/make-a-free-website-in-15-minutes.md).
