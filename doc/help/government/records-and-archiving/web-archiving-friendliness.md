---
title: Making the site friendly to web archiving
audience: government, admin, developer
category: public-sector
order: 210
summary: For public bodies whose sites are harvested by a national or institutional web archive - Exponential's shipped robots.txt and what to change in it, sitemaps from bcgooglesitemaps or xrowmetadata, stable URLs, request-shield and rate limits that may block crawlers, and checks.
keywords: web archiving, crawler, harvesting, robots.txt, Crawl-delay, sitemaps, bcgooglesitemaps, xrowmetadata, stable URLs, request-shield, verified search engines, print layout, national archive, public sector
type: help
---
# Making the site friendly to web archiving

This page is for administrators and developers. Many national and institutional archives harvest public websites with
crawlers. A site that crawlers can walk, with stable addresses and a sitemap, is archived better. Exponential ships a
useful `robots.txt` and supports sitemaps; some security measures need an exception for the archive's crawler.

## Contents

1. [robots.txt](#robotstxt)
2. [Sitemaps](#sitemaps)
3. [Security measures and crawlers](#security-measures-and-crawlers)
4. [Stable, crawlable pages](#stable-crawlable-pages)
5. [Checking](#checking)
6. [Related pages](#related-pages)

## robots.txt

A new installation ships `robots.txt` at the web root, served directly by the rewrite rule `RewriteRule ^robots\.txt -
[L]` ([A useful robots.txt out of the box](../../../features/6.0/robots-txt.md)). Its directives:

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

- `/media/` and `/test-area/` are examples: edit them to match your tree. If your documents live under a media section
  the archive should keep, remove that line.
- Replace the commented sitemap lines with your own `Sitemap:` line.
- If your archive asks for specific rules for its crawler, add a `User-agent:` block for it.

## Sitemaps

Two extensions write sitemaps as cronjob parts: `bcgooglesitemaps` (part of the distribution) and `xrowmetadata`
([Sitemaps](../../creators/website-creators/sitemaps.md)). Register the sitemap in `robots.txt`.

## Security measures and crawlers

- **request-shield**, if used, slows clients past 120 pages a minute with a browser check and bans after repeated
  refusals; verified search engines are never banned, but an archive's crawler may not be recognized. Agree its address
  range or user agent with the archive and add a rule for it ([request-shield](../security/request-shield.md)).
- System URLs (`/content/view/full/<id>`) may be refused to visitors by request rules or request-shield; crawlers then
  follow aliases, which is what you want.
- Velocity's response cache and the HTTP cache serve crawlers quickly; no change needed.

## Stable, crawlable pages

- Content reachable through normal links and the sitemap, not only through search forms.
- Stable URL aliases (see "Preserving URLs").
- Documents linked from pages.
- Pages that render without scripts where possible.

## Checking

```bash
curl -s https://www.example.gov/robots.txt
curl -sI https://www.example.gov/sitemap.xml | head -3
```

Ask the archive for its crawl log after the first harvest and fix what it could not reach.

## Related pages

- [A useful robots.txt out of the box](../../../features/6.0/robots-txt.md)
- [robots.txt for website creators](../../creators/website-creators/robots-txt.md)
- [Sitemaps](../../creators/website-creators/sitemaps.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
