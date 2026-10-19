---
title: Libraries and catalogs
audience: businesses, project-manager, website-creator, editor
category: business-solutions
order: 1
summary: The landing page of the libraries section - what a library, archive, documentation center or any organization with a catalog can build with Exponential, what it is not, the pages of the section and the order to read them.
keywords: library, catalog, archive, collection, records, subject headings, digital collection, documentation center, library website
type: help
---
# Libraries and catalogs

This section is for libraries, archives, documentation centers, schools and any organization that publishes a catalog
of records: books, reports, standards, recordings, products with long data sheets. It shows how the content model,
tagging, search, files, PDF exports and feeds of Exponential combine into a public catalog site, and it says clearly
where a dedicated library system is still needed.

## Contents

1. [What you can build](#what-you-can-build)
2. [What Exponential is not](#what-exponential-is-not)
3. [The pages of this section](#the-pages-of-this-section)
4. [Suggested reading order](#suggested-reading-order)
5. [Neighboring sections](#neighboring-sections)

## What you can build

| Need of a library | Exponential feature | Page in this section |
|---|---|---|
| A record per item with structured fields | Content classes with datatypes such as `ezisbn`, `ezdate`, `ezobjectrelationlist` | [Modeling catalog records](modeling-catalog-records.md) |
| Subject headings with a hierarchy and synonyms | The `eztags` extension, shipped with the product | [Tagging and subject headings](tagging-and-subject-headings.md) |
| Finding records by words, and later by facets | The built-in search engine; Solr through `ezfind` when the catalog grows | [Catalog search](catalog-search.md) |
| Digitized documents and downloads | File objects, permission-checked downloads, text extraction for search | [Files and digital collections](files-and-digital-collections.md) |
| A printed or PDF catalog | PDF exports of a node or a tree | [PDF and print exports](pdf-and-print-exports.md) |
| "New in the library" for readers and other sites | RSS 2.0, Atom and other feed exports | [Feeds for new acquisitions](feeds-for-new-acquisitions.md) |

All of these are part of a standard installation except Solr: `eztags`, `enhancedezbinaryfile` and `xrowmetadata` are
in the `require` block of `composer.json`, and `se7enxweb/ezfind` is in its `suggest` block, with the note "Install it
with: composer require se7enxweb/ezfind:~3.0.7".

## What Exponential is not

Exponential is a content management system. In the repository there is no support for library exchange formats or
library operations: no MARC import or export, no Z39.50 or SRU interface, no circulation, loans, holds or patron
fines. A search of the code and documentation for these terms finds nothing. If you need them, keep your integrated
library system for them and use Exponential for the public site, the digital collection and the news around it.
Records can be brought in by a developer script or through the REST API
([Creating and updating content in code](../../creators/app-creators/creating-content-in-code.md),
[REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md)).

> **Note**
> When this section says something is possible, it names the feature and the page that documents it. If a feature you
> need is not mentioned, assume it is not there and ask your developer or partner to check.

## The pages of this section

| Page | What it answers |
|---|---|
| [Catalog planning](catalog-planning.md) | Scope, audiences, the record types, who catalogs, and the decisions to take first |
| [Modeling catalog records](modeling-catalog-records.md) | Turning a record into a content class: fields, datatypes, relations, required fields |
| [Tagging and subject headings](tagging-and-subject-headings.md) | A controlled vocabulary with eztags: hierarchy, synonyms, translations, permissions |
| [Catalog search](catalog-search.md) | The built-in search, search statistics, and when Solr is worth it |
| [Files and digital collections](files-and-digital-collections.md) | Digitized items, protected downloads, licenses, large files, missing files |
| [PDF and print exports](pdf-and-print-exports.md) | A PDF of a node or a tree, generated once or on the fly, and what is public |
| [Feeds for new acquisitions](feeds-for-new-acquisitions.md) | Feeds of new records for readers, partner sites and newsletters |
| [Library site checklist](library-site-checklist.md) | One checklist from planning to launch and the routine afterwards |

## Suggested reading order

1. [Catalog planning](catalog-planning.md)
2. [Modeling catalog records](modeling-catalog-records.md)
3. [Tagging and subject headings](tagging-and-subject-headings.md)
4. [Catalog search](catalog-search.md)
5. [Files and digital collections](files-and-digital-collections.md)
6. [Feeds for new acquisitions](feeds-for-new-acquisitions.md) and [PDF and print exports](pdf-and-print-exports.md)
7. [Library site checklist](library-site-checklist.md)

## Neighboring sections

- [Newsletters](../newsletters/README.md): a newsletter of new acquisitions.
- [Members](../members/README.md): reader accounts and members-only content.
- Non-profits and Associations and small groups: many
  libraries are run by one or support one.
- [Multilingual sites](../multilingual/README.md): catalogs in several languages.
- Public libraries run by a government body: see also
  Governments: start here.

## Related pages

- Businesses: start here
- [Designing your content model](../../creators/website-creators/designing-your-content-model.md)
- [What you can build](../../creators/website-creators/what-you-can-build.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
