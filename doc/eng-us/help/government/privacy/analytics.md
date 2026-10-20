---
title: Analytics and visitor statistics
audience: government, project-manager, admin
category: public-sector
order: 220
summary: For public bodies measuring use of their site - what Exponential records itself (search statistics, view counts, request logs), what the bcwebsitestatistics extension sends to Google Analytics, consent and privacy notice implications, and less invasive alternatives.
keywords: analytics, visitor statistics, Google Analytics, bcwebsitestatistics, Urchin, Tag Manager, view counts, search statistics, consent, privacy notice, self-hosted analytics, CSP, public sector
type: help
---
# Analytics and visitor statistics

This page is for project managers, communication teams and data protection officers. Public bodies need to know which
services people use and what they cannot find; they also have to respect visitors' privacy. Exponential offers some
statistics of its own; a full analytics service is an extension and a third party
([Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What Exponential records itself](#what-exponential-records-itself)
2. [bcwebsitestatistics and Google Analytics](#bcwebsitestatistics-and-google-analytics)
3. [Privacy steps](#privacy-steps)
4. [Related pages](#related-pages)

## What Exponential records itself

| Source | What | Personal data |
|---|---|---|
| Search statistics | phrases, counts, average results; **off by default** (`LogSearchStats`) | phrases can contain names |
| View counts | page views per node, counted by the `updateviewcount` cron part from the access log | none stored per visitor |
| Request logs | not-found and attack requests, masked | masked addresses |
| Audit trail | administration activity | staff data |

These answer operational questions; they are not an analytics service.

## bcwebsitestatistics and Google Analytics

The extension adds the Google Analytics script to pages and can report shop orders through the `ezreceipt` workflow
type ([bcwebsitestatistics](../../../features/6.0/extensions/bcwebsitestatistics.md)). Settings in
`settings/override/bcwebsitestatistics.ini.append.php`; always set `Urchin` to your own property. Per the project's
privacy statement, it has **no consent gate of its own**.

## Privacy steps

1. Decide the purpose and the legal basis with your DPO.
2. Load the script only after consent where consent is needed (see "Cookie banners and consent").
3. Configure the service for minimal data (its own settings, such as address shortening, where it offers them).
4. Name the service, the data and the recipient in the privacy notice.
5. Under a Content Security Policy, allow the service's hosts in `script-src` and `connect-src` for the public
   siteaccess, and move any inline snippet into a file.
6. Reconsider yearly whether the figures are used.

Alternatives: self-hosted analytics, aggregated server logs, and Exponential's own search statistics (reset regularly).

## Related pages

- [Visitor statistics and analytics](../../creators/website-creators/statistics-and-analytics.md)
- [Third-party extensions and privacy](third-party-extensions.md)
- [Search phrases](search-phrases.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
