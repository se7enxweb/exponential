# A useful robots.txt out of the box

This page is for site owners who care how search engines crawl their site. A new installation ships a `robots.txt` at
the web root. Before September 2025 you had to write one yourself; now search engines get sensible rules from the
first request, and crawlers are kept out of pages that waste their time and your bandwidth.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../examples/web-servers/nginx.md), [LiteSpeed](../../examples/web-servers/litespeed.md),
> [Caddy](../../examples/web-servers/caddy.md), [IIS](../../examples/web-servers/iis.md), [Velocity](../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../help/hosting/panels/plesk.md), [cPanel](../../help/hosting/panels/cpanel.md), [DirectAdmin](../../help/hosting/panels/directadmin.md), [ISPConfig](../../help/hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md).

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
- [Changelog 6.0.11](../../../changelogs/6.0/6.0.11.md)
- [Chronicle: September 2025](../../history/2025/2025-09.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
