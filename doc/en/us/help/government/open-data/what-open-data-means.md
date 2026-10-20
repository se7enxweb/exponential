---
title: What open data means for a public body
audience: government, project-manager, editor, admin
category: public-sector
order: 20
summary: For people in a public body who are asked to "publish open data": what the term means in the Open Definition and the EU Open Data Directive, what DCAT is, and which parts of the job a content management system like Exponential can and cannot do.
keywords: open data, open government data, Open Definition, Open Data Directive, 2019/1024, DCAT, dataset, data catalog, public sector information, re-use, PSI
type: help
---
# What open data means for a public body

This page is for managers, editors and administrators in a public body who have been asked to publish open data
and want to know what the term means before they plan the work. It explains the three references most open data
programs use (the Open Definition, the EU Open Data Directive and the W3C DCAT vocabulary) and then separates the
part of the work Exponential can carry from the part it cannot. Product facts come from the Exponential
documentation that is linked in each section.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Open data in one paragraph](#open-data-in-one-paragraph)
2. [The Open Definition](#the-open-definition)
3. [The EU Open Data Directive](#the-eu-open-data-directive)
4. [DCAT: describing datasets for catalogs](#dcat-describing-datasets-for-catalogs)
5. [What a content management system contributes](#what-a-content-management-system-contributes)
6. [What Exponential does not do](#what-exponential-does-not-do)
7. [A first plan](#a-first-plan)

## Open data in one paragraph

Open data is data that anyone may use, change and pass on, for any purpose, with at most a requirement to name the
source and to keep it open. For a public body that usually means: datasets it already holds (budgets, registers,
statistics, timetables, service points) are published as files or through an interface, in a format a machine can
read, under an open license, at an address that keeps working, with a description that says what the data is,
who is responsible, how current it is and how to cite it.

## The Open Definition

The [Open Definition 2.1](https://opendefinition.org/od/2.1/en/) sets out when a work is "open". In practice, for
a dataset, check these points:

| Point of the definition | What it means for a dataset you publish |
|---|---|
| Open license | The dataset carries a license that allows use, redistribution and modification by anyone, for any purpose |
| Access | The whole dataset is available, preferably downloadable over the internet, at no more than a reasonable cost (normally free) |
| Machine readability | It is offered in a form a computer can process, for example CSV rather than a scanned table |
| Open format | The format can be read with free software and has no restrictions on its use |

A PDF page of a table can be a fine publication, but it is not open data in this sense. A CSV file next to it is.

## The EU Open Data Directive

In the European Union, [Directive (EU) 2019/1024](https://eur-lex.europa.eu/eli/dir/2019/1024/oj) on open data
and the re-use of public sector information sets the rules for documents held by public sector bodies that are
made available for re-use. Among the topics it covers are the formats in which documents should be offered, the
charges that may be asked, the licenses, the use of application programming interfaces for dynamic data, and a
group of high-value datasets. Each member state transposes it into its own law, so the exact duties of your body
depend on that national law; ask your legal counsel which apply.

Outside the EU, many jurisdictions have open data policies of their own. In the United States, the federal
portal for freedom of information is [FOIA.gov](https://www.foia.gov/); open data duties are set by separate laws
and policies that this page does not cover.

## DCAT: describing datasets for catalogs

The W3C [Data Catalog Vocabulary, version 3](https://www.w3.org/TR/vocab-dcat-3/) (DCAT 3) is the vocabulary
catalogs use to describe datasets. Its central terms are:

| DCAT term | Meaning | Typical field on your dataset page |
|---|---|---|
| `dcat:Catalog` | A curated collection of dataset descriptions | Your data catalog page |
| `dcat:Dataset` | One collection of data, published by one agent | One dataset page |
| `dcat:Distribution` | One accessible form of the dataset, such as a CSV file | One downloadable file |
| `dcat:DataService` | An interface that gives access to data | Your REST API or a feed |

National and European portals harvest DCAT descriptions (often in a profile such as DCAT-AP) from the catalogs of
public bodies. To be harvested, your catalog has to offer such a description in a machine-readable form.

## What a content management system contributes

Exponential is a content management system. For open data it is good at the publishing side:

- **Files at stable addresses.** A dataset file is a content object with a file field; the download address
  `content/download/<object ID>/<attribute ID>` checks permissions and keeps working while the object exists
  ([Files and downloads](../../creators/website-creators/files-and-downloads.md)).
- **Descriptions as structured content.** You can define a content class "Dataset" with fields for title,
  description, publisher, license, update frequency and files ([Content classes for websites](../../creators/website-creators/content-classes-for-websites.md)).
- **Feeds of new and changed datasets.** RSS 2.0 or Atom exports list the newest objects below a folder
  ([RSS feeds](../../../guides/rss-feeds.md)).
- **A JSON interface.** The REST API serves nodes, children and fields as JSON at `/api/ezp/v1/...` and
  `/api/ezp/v2/...` ([REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)).
- **Versions and an audit trail**, so you can show what changed and when
  ([Content history](../../../guides/content-history.md), [Audit trail](../../../features/6.0/audit-trail.md)).

## What Exponential does not do

Honest limits, checked in the repository:

- **No DCAT output and no CKAN connector.** Nothing in the kernel, the shipped settings or the documentation
  produces DCAT (RDF, JSON-LD) or speaks the CKAN API. A catalog that must be harvested needs a template that
  writes DCAT yourself, or a separate catalog product next to the site.
- **No dataset class ships.** You design the class; the pages of this section show how.
- **No data validation.** Exponential stores the file you upload; it does not check that a CSV is well formed or
  matches a schema.
- **No license field by default.** A license is a field you add to your class.

## A first plan

1. List the datasets you hold and decide which may be published (privacy, security, third-party rights).
2. Choose an open license per dataset, with your legal counsel.
3. Create the dataset class and a catalog folder in Exponential.
4. Publish each dataset as a page with its files, at an address you intend to keep.
5. Add a feed of new datasets, and decide whether your catalog needs DCAT for a national portal.

The other pages of this section take each step in turn.

## Related pages

- [Files and downloads](../../creators/website-creators/files-and-downloads.md)
- [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)
- [RSS feeds](../../../guides/rss-feeds.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
