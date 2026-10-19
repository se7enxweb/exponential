---
title: Library site checklist
audience: businesses, project-manager, website-creator, editor
category: business-solutions
order: 80
summary: One checklist for a library, archive or catalog site on Exponential - scope and planning, the record model, subject headings, search, files and licenses, exports and feeds, accessibility, launch, and the monthly routine - each item linked to the page that explains it.
keywords: library checklist, catalog launch, archive website, go-live, record model, subject headings, search, digital collection, feeds, accessibility, monthly routine
type: help
---
# Library site checklist

This page is for the project lead of a library, archive or catalog site. It gathers the checks of this section in the
order of a project, so you can copy it into your tracker and tick it off. Every item links to the page that explains
it; nothing here is new.

## Contents

1. [Planning](#planning)
2. [Record model](#record-model)
3. [Subject headings](#subject-headings)
4. [Records in](#records-in)
5. [Search](#search)
6. [Files and digital collections](#files-and-digital-collections)
7. [Exports and feeds](#exports-and-feeds)
8. [Accessibility and readers](#accessibility-and-readers)
9. [Launch](#launch)
10. [Every month](#every-month)
11. [Every year](#every-year)

## Planning

From [Catalog planning](catalog-planning.md):

- [ ] Scope written: what is in the site, and which system stays master for holdings and loans.
- [ ] It is understood that there is no MARC, Z39.50 or circulation support ([Libraries and catalogs](README.md#what-exponential-is-not)).
- [ ] Expected record numbers now and in three years.
- [ ] Record types listed; audiences and their tasks listed.
- [ ] Who catalogs, who approves, who owns the vocabulary.
- [ ] Languages of description decided.
- [ ] Measures for the monthly review chosen.

## Record model

From [Modeling catalog records](modeling-catalog-records.md):

- [ ] One class per record type; containers for collections and series.
- [ ] Fields with value datatypes for everything sorted, filtered or browsed (`ezdate`, `ezselection`, relations, tags).
- [ ] Identifiers such as the ISBN in their own fields, not translatable.
- [ ] Creators as Person or Organization objects, related with `ezobjectrelationlist`.
- [ ] Required, searchable and translatable set on purpose.
- [ ] Object name and URL alias patterns give readable names.
- [ ] Classes built on a development copy and moved as a package.

## Subject headings

From [Tagging and subject headings](tagging-and-subject-headings.md):

- [ ] Tag tree built: top terms, narrower terms, synonyms; shallow where possible.
- [ ] How related terms are handled is decided (they are not a built-in tag relation).
- [ ] Subjects field closed; a separate open keywords field only if wanted.
- [ ] Visitors have `tags/read` and `tags/view`; catalogers `add` only below their tags.
- [ ] Subject pages have a scope note.
- [ ] Tags translated for each language of description.

## Records in

- [ ] Route decided: typed, script import, REST, RSS import or Writer import ([Catalog planning](catalog-planning.md#how-records-arrive)).
- [ ] Imports use remote ids so they can run again without duplicates
      ([Creating and updating content in code](../../creators/app-creators/creating-content-in-code.md)).
- [ ] A sample of real records reviewed by catalogers before the full import.
- [ ] Old collections published before feeds are announced.

## Search

From [Catalog search](catalog-search.md):

- [ ] Searchable fields reviewed; index rebuilt after every large import.
- [ ] Results template shows counts, paging and what to try when nothing is found.
- [ ] Advanced search offered if readers need class or date filters.
- [ ] Text extraction programs installed if file contents must be found; scans made searchable before upload.
- [ ] Search statistics decided with the data protection contact.
- [ ] A dated decision on Solr (`ezfind`).

## Files and digital collections

From [Files and digital collections](files-and-digital-collections.md):

- [ ] Open, member and staff material separated by sections; downloads tested logged out.
- [ ] No direct links into `var/.../storage/`.
- [ ] Download links show format and size.
- [ ] Default license set for the installation and per record class; per-item licenses where they differ.
- [ ] Upload limits and disk space planned with the host.
- [ ] Visitor upload forms use `enhancedezbinaryfile` with a sensible `MaxFiles` and spam protection.

## Exports and feeds

From [PDF and print exports](pdf-and-print-exports.md) and [Feeds for new acquisitions](feeds-for-new-acquisitions.md):

- [ ] Stored PDF exports hold only public material; restricted lists generated on the fly.
- [ ] A TrueType font configured if titles use non-Latin scripts.
- [ ] A named person regenerates stored exports.
- [ ] Feeds for new records active, one source per record class, links to the public site.
- [ ] Autodiscovery links in the pagelayout; `/rss/feed/` not blocked in `robots.txt`.
- [ ] A recurring newsletter of new acquisitions, if planned, set up with consent
      ([Newsletters](../newsletters/README.md)).

## Accessibility and readers

- [ ] Templates follow [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md):
      landmarks, one `h1`, labels, contrast, 200% zoom.
- [ ] Covers and scans have alternative texts.
- [ ] Tested with [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md).
- [ ] Reader accounts and members-only areas checked (Members).

## Launch

- [ ] The website creator's [launch checklist](../../creators/website-creators/launch-checklist.md) passed.
- [ ] Backups of database and storage run, and a restore tested
      ([Backups and restores](../../creators/website-creators/backups-and-restores.md)).
- [ ] Sitemaps generated and registered ([Sitemaps](../../creators/website-creators/sitemaps.md)).
- [ ] Catalogers trained; a short editor guide written
      ([Handing the site over to editors](../../creators/website-creators/handing-over-to-editors.md)).

## Every month

- [ ] Search phrases with no results reviewed; synonyms or records added.
- [ ] Content checks run: unreferenced files, broken relations, missing files
      ([Content checks](../../../features/6.0/content-checks.md)).
- [ ] Feeds opened in a feed reader; stored PDFs regenerated where needed.
- [ ] Roles reviewed for staff and volunteers who changed (Access reviews).

## Every year

- [ ] Vocabulary reviewed: headings without records, headings with very many.
- [ ] Record classes reviewed against what catalogers actually fill in.
- [ ] The Solr decision reviewed against the catalog's size.
- [ ] Licenses of digital items reviewed with the rights holders.

## Related pages

- [Libraries and catalogs](README.md)
- Go-live checklist
- Annual review checklist
- [Launch checklist](../../creators/website-creators/launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
