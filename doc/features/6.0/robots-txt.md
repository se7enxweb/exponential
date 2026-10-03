# A useful robots.txt out of the box

A new installation ships a `robots.txt` at the web root. Before September 2025
you had to write one yourself; now search engines get sensible rules from the
first request, and the file already keeps crawlers out of the pages that waste
their time and your bandwidth.

## What is in it

`robots.txt` (at the document root) starts with explanatory comments and then
holds these directives:

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

The disallow block lists each path in lower case and in its capitalised form
(`/Content/Search/`), because a crawler may have met either spelling of a URL. Search results, the advanced search form,
the "tip a friend" form and the printable layout add no value to a search index.
`/media/` and `/test-area/` are examples of sections you may not want indexed;
edit them to match your own tree.

The sitemap lines are present as comments. Uncomment and fill in the address of
your sitemap (for example the one created by the `bcgooglesitemaps` extension,
which is part of the [distribution](default-extension-distribution.md)).

## Make it yours

```bash
$EDITOR robots.txt
```

1. Remove or add `Disallow:` lines for the sections of your tree. Use the URL
   alias as visitors see it.
2. Set your sitemap: `Sitemap: https://www.example.com/sitemap.xml`.
3. Test it: `curl -s https://www.example.com/robots.txt`.

## How it is served

The example rewrite configuration (`.htaccess_root`) contains the rule
`RewriteRule ^robots\.txt - [L]`, which lets the web server hand the file to the
crawler directly instead of passing the request to `index.php`. If your site is
served by a different web server, make sure `/robots.txt` reaches the file.

A multi-site installation with several domains on one docroot serves the same
file to all of them. Give each domain its own docroot or generate the file per
host if the rules must differ.

## Related

[Chronicle: September 2025](../../history/2025/2025-09.md),
[Changelog 6.0.11](../../changelogs/6.0/6.0.11.md).
