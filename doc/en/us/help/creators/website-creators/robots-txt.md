---
title: robots.txt for your site
audience: website-creator, admin
category: website-creation
order: 400
summary: Adapt the robots.txt that ships at the web root - what it already blocks, adding your sitemap and your own sections, how it is served, and per-host files on a multisite.
keywords: robots.txt, crawlers, search engines, Disallow, Sitemap, Crawl-delay, web root, rewrite rules, multisite, website creator
type: help
---
# robots.txt for your site

This page is for website creators who tell search engine crawlers what to skip. A new installation ships a
`robots.txt` at the web root with sensible rules ([A useful robots.txt out of the box](../../../features/6.0/robots-txt.md)).

## Contents

1. [What ships](#what-ships)
2. [Make it yours](#make-it-yours)
3. [How it is served](#how-it-is-served)
4. [Several sites](#several-sites)
5. [What robots.txt is not](#what-robotstxt-is-not)

## What ships

```text
User-agent: *
Allow: /

Disallow: /content/search/
Disallow: /content/advancedsearch/
Disallow: /content/tipafriend/
Disallow: /layout/set/print/
Disallow: /media/
Disallow: /test-area/

Crawl-delay: 1
```

Each disallowed path is also listed in its capitalized form, because a crawler may have met either spelling. The file
starts with a block of comments you may remove, and holds commented sitemap lines.

## Make it yours

1. Remove `/media/` and `/test-area/` if they do not match your tree; add your own sections, using the addresses
   visitors see (URL aliases).
2. Add your sitemap: `Sitemap: https://www.example.com/sitemap.xml` ([XML sitemaps](sitemaps.md)).
3. Check: `curl -s https://www.example.com/robots.txt`.

Keep it in version control with your deployment, not edited on the server.

## How it is served

`.htaccess_root` has `RewriteRule ^robots\.txt - [L]`, so Apache hands the file over directly. Velocity's built-in
rewrite rules are the same list ([8.1 The choice in one table](../../../install/08-serving-the-site.md#81-the-choice-in-one-table)).
On nginx make sure `/robots.txt` reaches the file.

## Several sites

One file serves every host of the installation. When sites need different rules, let the web server serve a file per
host (for example `robots-site-b.txt` for `www.site-b.example`), or keep the rules general enough for all.

## What robots.txt is not

- It is not access control: anyone can read what you disallow. Protect private content with permissions
  ([Sections and object states](sections-and-object-states.md)).
- A disallowed page can still be listed by address if other sites link to it; use a `noindex` meta tag in its
  template to keep it out of results.
- Staging and test sites should require a login rather than rely on `robots.txt`.

## Related pages

- [SEO basics for Exponential sites](seo-basics.md)
- [XML sitemaps](sitemaps.md)
- [Serving the site](../../../install/08-serving-the-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
