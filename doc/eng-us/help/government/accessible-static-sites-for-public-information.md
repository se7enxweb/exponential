---
title: "Accessible static sites for public information"
audience: government, project-manager, admin, editor
category: public-sector
order: 2
summary: For public bodies - publish public information as a static site that is fast, robust under load, cheap to run and easy to audit for accessibility, while editing, approval and records stay in Exponential.
keywords: government, public sector, municipality, public information, accessibility, WCAG, availability, emergency information, audit, static site, GitHub Pages, gh-pages, free hosting, free website, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing
type: help
---
# Accessible static sites for public information

Public information must be reachable by everyone, also on a bad connection, on old devices and on the day everybody
looks at once. A static site is well suited: plain HTML pages with nothing to compute when a visitor arrives.

## What you'll have at the end

- Your public information site served as static pages, at your own domain with HTTPS.
- Editing, review and approval in Exponential, with roles and versions as before.
- A tagged record of every publish: what was online when.

## Why static helps

- **Availability.** A static host serves a sudden rush of visitors without slowing down. Useful for emergency notices
  and election results.
- **Security.** The public site has no login, no database and no PHP to attack. The editing installation can stay on
  the internal network.
- **Accountability.** Each publish is one commit with a tag `static/<target>/<n>`, a time and the person who
  published. Rollbacks are new commits, so nothing is erased.
- **Cost.** The public side needs only a static host.

## Accessibility

Static publishing does not change your HTML: the copy is what your templates render, with links rewritten. So:

- the accessibility of the copy is the accessibility of your design; audit the copy with your usual tools, from the
  preview of the build before you publish;
- page titles, language attributes, `hreflang` alternates and headings are kept;
- the search page the build writes works with the keyboard and without a server.

Forms (complaints, requests) need a server. Post them to the live installation, or link to the form service you
already use. See [Search, forms and comments on a static site](../static-publishing/search-forms-and-comments-on-a-static-site.md).

## Hosting choice

GitHub Pages is free, but your rules may require hosting in your country or on your own servers. Static publishing
pushes to any git host, and the folder can be served by any web server.
See [Other git hosts: GitLab, Gitea and your own server](../static-publishing/other-git-hosts-gitlab-gitea-and-your-own-server.md).

Before you publish anything: [Keep private content private](../static-publishing/keep-private-content-private.md).

## Make yours now

Pilot it with one information site: [Make a free website in 15 minutes](../static-publishing/make-a-free-website-in-15-minutes.md).
