---
title: Visitor statistics and analytics
audience: website-creator, admin
category: website-creation
order: 680
summary: Measure how a site is used - what Exponential records itself (search statistics, view counts, request logs, shop statistics), adding an analytics service with bcwebsitestatistics or your own script, and privacy and CSP considerations.
keywords: statistics, analytics, Google Analytics, bcwebsitestatistics, search statistics, view count, request logs, shop statistics, privacy, consent, CSP, website creator
type: help
---
# Visitor statistics and analytics

This page is for website creators who want to know how a site is used. Exponential records some things itself; for
visitor analytics you add a service.

## Contents

1. [What Exponential records](#what-exponential-records)
2. [Adding an analytics service](#adding-an-analytics-service)
3. [Privacy and consent](#privacy-and-consent)
4. [Under a Content Security Policy](#under-a-content-security-policy)

## What Exponential records

| Source | What | Where |
|---|---|---|
| Search statistics | phrases searched, counts, average results (off by default) | **Setup > Search statistics** ([URLs and search statistics](../../../guides/urls-and-aliases.md#5-search-statistics)) |
| View counts | page views per node, counted by the `updateviewcount` cron part from the access log | `fetch( 'content', 'view_top_list', ... )` |
| Request logs | requests with timing, for operations | [Request logs](../../../features/6.0/request-logs.md) |
| Shop statistics | orders and revenue | `shop/statistics`, the Store dashboard |
| Audit trail | who changed what in the administration | [Audit trail](../../../features/6.0/audit-trail.md) |

These answer operational questions ("what do visitors search and not find", "which pages are popular"). They are not a
replacement for an analytics service.

## Adding an analytics service

- **`bcwebsitestatistics`** adds the Google Analytics script to pages and can report shop orders through the
  `ezreceipt` workflow type; set `Urchin` to your own property in
  `settings/override/bcwebsitestatistics.ini.append.php` ([bcwebsitestatistics](../../../features/6.0/extensions/bcwebsitestatistics.md)).
- **Another service**: add its script as a file through your design's script list
  ([Stylesheets and scripts](stylesheets-and-scripts.md)), with its settings passed in a JSON data block.

## Privacy and consent

Analytics usually needs the visitor's consent in many jurisdictions. Load the script only after consent, describe it in
the privacy page, and prefer services you can self-host or that do not set cookies when your audience expects it. This
is not legal advice; ask whoever is responsible for data protection.

## Under a Content Security Policy

An analytics script from another host must be allowed: `AdditionalSources[]=script-src https://<host>` and
`connect-src` for where it sends data, in `[ContentSecurityPolicy]` of the site
([Content Security Policy](../../../features/6.0/content-security-policy.md)). Inline snippets the service suggests
must be moved into a file.

## Related pages

- [SEO basics for Exponential sites](seo-basics.md)
- [Measuring a project: KPIs](../../leadership/kpis-from-logs-and-audit.md)
- [Site search](site-search.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
