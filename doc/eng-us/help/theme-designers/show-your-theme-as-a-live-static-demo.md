---
title: "Show your theme as a live static demo"
audience: theme-designer, designer
category: theme-design
order: 44
summary: Put your Exponential theme in front of clients and users as a live demo site on GitHub Pages, free and always up, with example content and a link to the theme's repository.
keywords: theme designer, designer, theme demo, live demo, showcase, design system, free website, free hosting, static site, GitHub Pages, gh-pages, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing
type: help
---
# Show your theme as a live static demo

Screenshots sell a theme less well than a site people can click through. With static publishing you install your
theme on an Exponential site with example content, and publish a static copy of it to GitHub Pages: a live demo that
costs nothing and never needs a server update.

## What you'll have at the end

- A live demo at `https://<you>.github.io/<theme>-demo/`, linked from your theme's README and package page.
- Every demo version tagged, so you can show how the theme changed between releases.

## Set it up

1. Make a site with your theme and realistic example content: articles with images, a folder listing, a long page,
   a page in a second language if the theme supports it.
2. Make a target for its siteaccess. Use relative links so the demo works under `/<repository>/`. See
   [Links and base path](../static-publishing/links-and-base-path.md).
3. Build, open the preview, and check each page type at phone and desktop width.
4. Publish. Add the address to your theme's README.

## Design checks the build gives you

- **Missing files:** the link check of every build lists style sheets, fonts or images your templates name but
  that do not exist.
- **Absolute links:** a template that writes `/design/...` by hand still works, because the build rewrites it, but
  `ezurl` and `ezdesign` are the clean way.
- **Size:** the report shows how heavy your pages and images are.

## Things that look different in a static copy

Forms, comments, the login and the basket have no server behind them. They are hidden or replaced with a short text
and a link to the live site. Design these states on purpose; see
[Search, forms and comments on a static site](../static-publishing/search-forms-and-comments-on-a-static-site.md).

Search keeps working: the build writes a search page that searches in the browser. Style it like your search results.

## See an example

The demo site: [https://se7enxweb.github.io/exponential-demo-site-static/](https://se7enxweb.github.io/exponential-demo-site-static/) (coming soon).

## Make yours now

Follow [Make a free website in 15 minutes](../static-publishing/make-a-free-website-in-15-minutes.md) with your
theme's demo site.
