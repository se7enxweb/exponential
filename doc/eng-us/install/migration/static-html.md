---
title: Static HTML sites and Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 49
summary: Moving a hand-made or generated static HTML site into Exponential 6 and back - reading the files or crawling the site, extracting the content area, HTML to XmlText, images and documents, keeping .html addresses, and producing a static copy from Exponential.
keywords: static HTML, static site generator, Jekyll, Hugo, crawl, HTML extraction, XmlText, .html addresses, redirects, makestaticcache, static copy, migration
type: install
---
# Static HTML sites and Exponential

A static site is the simplest source: every page is a file you can read. The work is in separating the content from
the repeated layout around it, and in keeping the addresses.

[Migration hub](README.md) · [Checklist](checklist.md) · [Mapping](content-model-mapping.md) · [Tools](exponential-tools.md)

## 1. Read the files, or crawl

| You have | Read |
|---|---|
| The files (FTP, Git) of a hand-made site | the `.html` files directly |
| A static site generator (Jekyll, Hugo, Eleventy ...) | the **sources** (Markdown with front matter), not the generated HTML; see [JavaScript and Node.js apps, 2](javascript-apps.md#2-content-into-exponential) for Markdown |
| Only the live site | a crawl: sitemap, links, server log; save each page |

## 2. Extract the content

1. **Group the pages by template** (pages that share the same layout).
2. **Per template, find the content area** (a `<main>`, an `<article>`, a `<div id="content">`) and the parts to keep:
   title (`<h1>` or `<title>`), date, author, meta description.
3. **Clean the HTML**: drop navigation, scripts, inline styles and layout tables; keep headings, paragraphs, lists,
   tables, links and images. Reduce it to Exponential's simplified input (`<p>`, `<strong>`, `<em>`, `<a href>`,
   `<h1>`-`<h6>`, `<ul>`/`<ol>`/`<li>`, `<table>`/`<tr>`/`<td>`), which the kernel parses into XmlText
   ([tools, section 4](exponential-tools.md#4-a-custom-import-script-the-skeleton)).
4. **Images and documents**: collect every `<img src>` and linked PDF/office file, import them as image and file
   objects, and replace the references with embeds or links to the new objects.
5. **The tree**: the directory structure usually is the navigation: `/products/widgets/index.html` becomes a folder
   *Products*, a folder *Widgets*, and so on.

## 3. Import

Write JSON rows (remote id from the old path, for example `html-/products/widgets.html`), then import with the
[script skeleton](exponential-tools.md#4-a-custom-import-script-the-skeleton) or xrowextract's importer. A folder of
images and documents can come in through the upload page's ZIP importer, keeping its folder structure.

## 4. Addresses

Static addresses end in `.html` or are directories with `index.html`. Exponential's aliases are clean paths
(`products/widgets`). For each old address, add a redirecting custom URL alias on the new node, and check what the
alias cleaning makes of the dot and the extension; when the old addresses follow one pattern, a URL wildcard or a
web-server rule (strip `.html`, `index.html`) covers them all ([checklist, 4](checklist.md#4-urls-and-redirects)).

## 5. From Exponential to a static copy

Exponential can write static pages of its public siteaccesses:
`./console exp:makestaticcache` crawls them and generates static pages (`--site`, `--max-pages`, `--max-depth`). For
an archive copy of a site you are switching off, a crawl with a mirroring tool of the live site works as well; keep
the storage directory for the originals of the images.

## 6. Checklist

- [ ] Address list complete (files, sitemap, log); pages grouped by template.
- [ ] Extractor per template tested; HTML reduced to the simplified input.
- [ ] Images and documents imported; references rewritten.
- [ ] Tree built from the directory structure; remote ids from old paths.
- [ ] `.html` addresses redirected and tested.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
