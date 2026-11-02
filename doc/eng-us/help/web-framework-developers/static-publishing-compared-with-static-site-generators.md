---
title: "Static publishing compared with static site generators"
audience: web-framework-developer, developer
category: web-frameworks
order: 531
summary: An honest comparison of Exponential's static publishing with Jekyll, Hugo, Eleventy, Astro and headless setups - where a CMS that publishes static copies wins, and where a generator is the better tool.
keywords: static site generator, SSG, Jekyll, Hugo, Eleventy, Astro, Gatsby, Next.js export, Jamstack, headless CMS, comparison, Jekyll alternative, Netlify, static site, GitHub Pages, gh-pages, free hosting, free website, CNAME, custom domain, deploy, static publishing
type: help
---
# Static publishing compared with static site generators

If you know Jekyll, Hugo, Eleventy or Astro, static publishing will look familiar in its output and different in how
it gets there. This page compares them honestly.

## What you'll have at the end

A clear idea of when to use Exponential's static publishing, when a generator, and when both.

## The basic difference

| | Static site generator | Exponential static publishing |
|---|---|---|
| Content lives in | Markdown files in git, or a headless CMS | Exponential's content repository, edited in its editor |
| Who edits | developers, or editors through a git-based UI | editors in a full CMS, with versions, workflows, roles and translations |
| How the site is made | templates render files at build time | the real site is rendered, then crawled as an anonymous visitor and rewritten |
| Preview | a local dev server | the dynamic site itself, plus a preview of each build |
| History | git commits of the sources | versions in Exponential, plus one tagged commit per publish |
| Build speed | very fast | a crawl; minutes for hundreds of pages, partial builds for single pages |

## Where static publishing wins

- **Editors who are not developers.** They get a real editor, images, relations, scheduling and roles.
- **An existing Exponential site.** The same templates make the dynamic and the static site; nothing is written twice.
- **Mixed sites.** Search runs in the browser, forms can post to the live site, member areas stay dynamic.
- **Safety.** Only what an anonymous visitor sees is published; drafts and private content never reach the build.

## Where a generator wins

- **Docs-as-code** with pull-request review of Markdown, where everyone edits in git anyway.
- **Very large sites rebuilt very often**, where a crawl is slower than rendering from files.
- **No server at all**, not even for editing. Exponential needs PHP and a database somewhere, if only on a laptop or
  in CI.

## Using both

The output is plain files in a folder, so anything that deploys a folder deploys it: GitHub Pages, GitLab Pages,
Netlify, Cloudflare Pages or rsync to any web server. You can also keep a generator for your docs and publish the
marketing site from Exponential.

Details of the output: [The build format](../../specifications/6.0/static-publishing-build-format.md).
Command line and CI: [Static publishing from the command line and CI](../php-developers/static-publishing-from-the-command-line-and-ci.md).

## Make yours now

Judge it on a real site: [Make a free website in 15 minutes](../static-publishing/make-a-free-website-in-15-minutes.md).
