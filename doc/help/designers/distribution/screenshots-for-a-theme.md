---
title: "Screenshots for a theme"
audience: designer, sales-marketing
category: theme-distribution
order: 310
summary: Which screenshots a theme needs - front page, an article, a list, light and dark mode, a phone and a 200% zoom view - how to take them reproducibly, where to keep them, and what must never appear in them.
keywords: screenshots, images, front page, dark mode, mobile, zoom, README image, product page, demo content, privacy
type: help
---
# Screenshots for a theme

This page is for designers preparing the images that show a theme on GitHub, Packagist, a project page or a product
page. Screenshots are the first proof of quality people see.

## The set to make

| Screenshot | Size | Shows |
|---|---|---|
| Front page, light | 1440 px wide | The overall look |
| Front page, dark | 1440 px wide | That dark mode is designed, not an afterthought (if the theme has one) |
| An article (full view) | 1440 px wide | Typography and reading comfort |
| A list or search result | 1440 px wide | Line views and paging |
| Phone | 375 px wide | The responsive layout |
| 200% zoom | 960 px wide at scale 2 | That the layout holds when people enlarge text |

The Exponential project checks its own pages at 1440 px, 960 px at scale 2 and 375 px, in light and dark mode; using
the same sizes lets users compare.

## Step by step

1. Install the theme on a demo installation with demo content ([Demo content for a theme](demo-content.md)).
2. Use a browser window of the exact width, or a headless browser that you can run again for each release.
3. Take each screenshot of the table. Save as PNG or WebP with descriptive names:
   `front-light-1440.png`, `front-dark-1440.png`, `article-375.png`.
4. Keep them in the repository, for example `doc/screenshots/`, so the README can show them with a relative path.
5. Write a short alt text for each in the README: what the image shows.
6. Run the [Screenshot checklist](screenshot-checklist.md).

## Never in a screenshot

- Real customer content, names, email addresses or faces without consent.
- An administration page showing user names, server paths or settings.
- Browser extensions, bookmarks or other tabs.
- Content you have no right to show (others' photos, logos).

## Related pages

- [Running a demo site](demo-sites.md)
- Testing at zoom levels
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
