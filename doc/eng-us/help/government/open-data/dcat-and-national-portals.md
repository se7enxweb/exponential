---
title: DCAT and national portals
audience: government, developer, website-creator, project-manager
category: public-sector
order: 170
summary: Exponential has no DCAT output and no CKAN connector. This page explains the options for getting datasets published in Exponential into a national or European data portal: a DCAT template of your own, a separate catalog product, or manual registration.
keywords: DCAT, DCAT-AP, CKAN, harvesting, data portal, national portal, catalog, RDF, JSON-LD, Turtle, template, open data, metadata export
type: help
---
# DCAT and national portals

This page is for project managers and developers whose datasets must appear in a national or European open data
portal. Many portals harvest catalogs described in the W3C [DCAT 3](https://www.w3.org/TR/vocab-dcat-3/)
vocabulary, often in an application profile such as DCAT-AP. Exponential does not produce DCAT and has no CKAN
connector; this was checked by searching the kernel, the settings and the documentation for DCAT and CKAN. The
options below are what remains.

## Contents

1. [How harvesting works](#how-harvesting-works)
2. [Option A: a DCAT template in your design](#option-a-a-dcat-template-in-your-design)
3. [Option B: a separate catalog product](#option-b-a-separate-catalog-product)
4. [Option C: manual registration](#option-c-manual-registration)
5. [Choosing](#choosing)

## How harvesting works

A portal fetches a catalog document from your site at regular intervals. The document lists the datasets with their
metadata and the addresses of their distributions. The portal stores the description and links to your files; the
files stay on your site. So your addresses must be stable (see "Stable URLs for datasets") and the metadata complete.

Ask the portal which format and profile it harvests (RDF/XML, Turtle, JSON-LD; which DCAT-AP version) and which
fields it requires.

## Option A: a DCAT template in your design

A developer can write a template that renders the catalog folder as DCAT, from the fields of your dataset class
(see "Designing a dataset class", whose identifiers follow DCAT terms):

1. Create a view of the catalog folder, for example a separate siteaccess or a custom view mode, whose pagelayout
   sends `Content-Type: text/turtle` or `application/ld+json` and no HTML around the content.
2. In the template fetch the datasets below the catalog folder and print one `dcat:Dataset` per node and one
   `dcat:Distribution` per child, with `dcat:downloadURL` built from `content/download/<object ID>/<attribute ID>`.
3. Escape every value for the output format, not with HTML escaping.
4. Validate the output with the portal's validator before announcing the address.

Template basics: [Template overrides](../../creators/website-creators/template-overrides.md) and
[The pagelayout](../../creators/website-creators/the-pagelayout.md); fetching content in templates:
[the template language help](../../template-language/README.md). Such a template is your own code to maintain with
each change of the class or of the profile.

## Option B: a separate catalog product

Run a dedicated catalog software next to Exponential that harvests or imports your dataset descriptions and offers
DCAT and an API to portals. Exponential then remains the place where pages and files are published; the catalog
points to them. Data for the catalog can come from an `xrowextract` JSON or CSV export of the dataset class
(see "Exporting content as CSV, JSON or XML") or from the REST API (see "The REST API as a data interface").
This adds a system to run and secure.

## Option C: manual registration

Small programs with few datasets can register each dataset by hand in the portal's own web form, linking to the
dataset page in Exponential. It needs no development, but every change has to be repeated in the portal.

## Choosing

| Situation | Suggestion |
|---|---|
| Fewer than a dozen datasets, rarely changed | C |
| Many datasets, a developer available, one portal profile | A |
| A national program with many publishers, or a portal that needs its API | B |

Whatever you choose, keep Exponential's dataset fields complete: they are the source for all three.

## Related pages

- [What open data means for a public body](what-open-data-means.md)
- [Metadata fields for datasets](metadata-fields-for-datasets.md)
- [The pagelayout](../../creators/website-creators/the-pagelayout.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
