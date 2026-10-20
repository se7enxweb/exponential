---
title: Open data readiness
audience: government, admin, project-manager, developer
category: public-sector
order: 220
summary: What a public body checks before it publishes open data from an Exponential site - datasets as content, files, licenses, metadata, stable addresses, feeds and the REST API - with what Exponential provides and that it has no built-in data catalog standard export.
keywords: open data, datasets, data catalog, DCAT, license, metadata, stable URLs, RSS, REST API, CSV, Open Data Directive, public sector
type: help
---
# Open data readiness

This checklist is for the open data lead and site administrator of a public body that publishes datasets on its
Exponential site. Exponential is a content management system, not a data portal: datasets are published as content
with attached files, and the site offers feeds and a REST API. There is no built-in export in the DCAT vocabulary; if
you need one, plan it as custom work or use a separate catalog.

> **Note** This page describes features and common practice; it is not legal advice. In the EU see the
> [Open Data Directive](https://eur-lex.europa.eu/eli/dir/2019/1024/oj); check the rules that apply to you with your
> legal counsel.

## Contents

1. [Decide](#decide)
2. [Content model](#content-model)
3. [Files and formats](#files-and-formats)
4. [Discovery](#discovery)
5. [Machine access](#machine-access)
6. [Maintenance](#maintenance)

## Decide

- [ ] Which datasets you publish, who owns each, and how often it is updated.
- [ ] The license, preferably one that meets the [Open Definition](https://opendefinition.org/od/2.1/en/).
- [ ] Personal data removed or aggregated before publication.

## Content model

- [ ] A content class for datasets, with fields for title, description, publisher, license, update frequency, coverage and the data files ([Designing your content model](../../creators/website-creators/designing-your-content-model.md), [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md)).
- [ ] Field names that map to a catalog vocabulary such as [DCAT 3](https://www.w3.org/TR/vocab-dcat-3/), so a later export is simple.
- [ ] Tags for themes ([Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)).

## Files and formats

- [ ] Data in open, machine-readable formats (CSV, JSON), not only PDF ([Files and downloads](../../creators/website-creators/files-and-downloads.md)).
- [ ] Downloads served through `content/download`, which checks read access ([Security standards](../../../standards/security.md)).
- [ ] File sizes within the upload limits of the server ([PHP settings for Exponential](../../hosting/basics/php-settings-for-exponential.md)).
- [ ] A data dictionary for each dataset explaining columns and units.

## Discovery

- [ ] A catalog page listing every dataset ([Site search](../../creators/website-creators/site-search.md)).
- [ ] Stable, readable addresses that do not change when a file is updated ([URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md)).
- [ ] Sitemaps include the dataset pages ([Sitemaps](../../creators/website-creators/sitemaps.md)).
- [ ] Metadata for search engines and sharing ([Metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md)).

## Machine access

- [ ] An RSS feed of new and changed datasets ([RSS feeds](../../../guides/rss-feeds.md)).
- [ ] The REST API documented for reusers, with read access for anonymous users only where intended ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)).
- [ ] Rate limits or caching in front of the API decided with the host ([Caching layers](../../hosting/performance/caching-layers.md)).

## Maintenance

- [ ] Each dataset shows its last update date.
- [ ] Old versions kept or archived as your records rules say ([Records checklist](records-checklist.md)).
- [ ] Links checked regularly ([Content checks](../../../features/6.0/content-checks.md)).
- [ ] Feedback from reusers has a contact.

## Related pages

- [Government checklists](README.md)
- [FOI readiness](foi-readiness.md)
- The open data section of this government help (written by another author)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
