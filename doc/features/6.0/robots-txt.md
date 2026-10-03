# A useful robots.txt out of the box

This page is for site owners who care how search engines crawl their site. A new installation ships a `robots.txt` at
the web root. Before September 2025 you had to write one yourself; now search engines get sensible rules from the
first request, and crawlers are kept out of pages that waste their time and your bandwidth.

## Make it yours in three steps

1. Open `robots.txt` in the document root and remove or add `Disallow:` lines for the sections of your tree. Use the
   URL alias as visitors see it.
2. Replace the commented sitemap lines with your own sitemap, for example:

   ```
   Sitemap: https://www.example.com/sitemap.xml
   ```

   The sitemap can come from the `bcgooglesitemaps` extension, which is part of the
   [distribution](default-extension-distribution.md).
3. Test it:

   ```bash
   curl -s https://www.example.com/robots.txt
   ```

   The file you edited is printed.

## What is in it

The file starts with a long block of explanatory comments (ASCII art and links to the product's documentation sites;
remove it if you do not want to publish it), then holds these directives:

```
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

- The disallow block lists each path in lower case and in its capitalised form (`/Content/Search/`), because a crawler
  may have met either spelling of a URL.
- Search results, the advanced search form, the "tip a friend" form and the printable layout add no value to a search
  index.
- `/media/` and `/test-area/` are examples of sections you may not want indexed; edit them to match your own tree.
- The sitemap lines are present as comments and point at the product's own sites.

## How it is served

The example rewrite configuration (`.htaccess_root`) contains the rule `RewriteRule ^robots\.txt - [L]`, which lets the
web server hand the file to the crawler directly instead of passing the request to `index.php`. If your site is served
by a different web server, make sure `/robots.txt` reaches the file.

## Limits

A multi-site installation with several domains on one document root serves the same file to all of them. Give each
domain its own document root, or generate the file per host, if the rules must differ.

## Related pages

- [A clean installation that fits shared hosting](clean-install-defaults.md)
- [bcgooglesitemaps](extensions/bcgooglesitemaps.md), [xrowmetadata](extensions/xrowmetadata.md)
- [Changelog 6.0.11](../../changelogs/6.0/6.0.11.md)
- [Chronicle: September 2025](../../history/2025/2025-09.md)
