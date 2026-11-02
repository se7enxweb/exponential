---
title: "Images, files and downloads"
audience: admin, website-creator, editor, hosting
category: administration
order: 15
summary: Which images, style sheets, scripts, fonts and downloads go into a static copy, how storage images of published content are copied, what is never copied, and how to keep a build within the host's size limits.
keywords: static publishing, images, image aliases, files, downloads, assets, Assets=referenced, referenced-and-storage, storage images, file size, large files, 100 MB, 1 GB, size limit, GitHub Pages, static site
type: help
---
# Images, files and downloads

## What you'll have at the end

A copy with every image and download it needs, and nothing that should not be public.

## What is copied

- Every **style sheet, script, font and image** the pages name, including the packed style sheets and scripts and the
  files those style sheets name in turn.
- Every **image alias** a page uses (a thumbnail, a large version). An alias that has not been made yet is made on the
  way, as when a visitor first sees it.
- Every **download** a page links to, when the site hands it to a visitor who is not signed in.
- With **Files: every image of published content** (`Assets=referenced-and-storage`, the default) also every image in
  the site's storage that belongs to the published version of content an anonymous visitor may read, linked from a
  page or not. Galleries and image pages that load pictures later then work too.

**Only what the pages name** (`Assets=referenced`) copies the first three and nothing else, which keeps the copy
smaller.

## What is never copied

- Images of **drafts** and older versions.
- The **originals** of uploaded files, unless a page links the download and the site serves it to anyone.
- Anything of content a visitor who is not signed in may not read.
- Files of pages you left out.

## Size

GitHub Pages allows 1 GB a site and 100 MB a file
([GitHub Pages limits and terms](github-pages-limits-and-terms.md)). The preview shows the size against the limit and
the largest file before you publish; a build over the limits is not pushed. To make it smaller:

1. Switch **Files** to **Only what the pages name**.
2. Leave out sections with large media (step 7, **Leave out**).
3. Put videos and large downloads on a video platform or a download host and link them from the page.

## Next step

[Build, preview and publish](build-preview-and-publish.md).
