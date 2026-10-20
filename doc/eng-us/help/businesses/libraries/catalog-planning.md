---
title: Catalog planning
audience: businesses, project-manager, editor
category: business-solutions
order: 10
summary: The decisions a library, archive or documentation center takes before building a catalog site on Exponential - scope, audiences, record types, vocabularies, who catalogs, how records arrive, what is public, and what success looks like.
keywords: catalog planning, library website plan, record types, cataloging workflow, vocabulary, import, public and restricted records, archive, documentation center
type: help
---
# Catalog planning

This page is for library directors, archivists, documentation managers and project leads. It lists the decisions to
take before anybody builds content classes, in plain language, and points to the feature that carries out each one. A
catalog is easy to start and hard to restructure once thousands of records exist, so this planning pays back.

## Contents

1. [Scope: what goes in the catalog](#scope-what-goes-in-the-catalog)
2. [Audiences and what they do](#audiences-and-what-they-do)
3. [Record types](#record-types)
4. [Vocabularies](#vocabularies)
5. [How records arrive](#how-records-arrive)
6. [Who catalogs and who checks](#who-catalogs-and-who-checks)
7. [Public, restricted and internal](#public-restricted-and-internal)
8. [Languages](#languages)
9. [Measuring success](#measuring-success)
10. [A planning worksheet](#a-planning-worksheet)

## Scope: what goes in the catalog

Write down what the site must hold and what stays elsewhere:

| Question | Typical answers |
|---|---|
| Is this the full catalog or a showcase? | A selection, a special collection, digitized items only |
| Does the library system stay the master? | Yes for loans and holdings; the site holds the public description |
| Which items have files? | Digitized reports, images, recordings |
| What is the expected size in records? | Matters for search and hosting ([Catalog search](catalog-search.md)) |

Exponential has no circulation, holds or MARC support (see [Libraries and catalogs](README.md#what-exponential-is-not));
plan the site around description, discovery and digital delivery.

## Audiences and what they do

List your audiences and their main task on the site: readers looking for a title, researchers browsing a subject,
teachers downloading material, partner institutions reusing your new acquisitions list. Each task should map to one of
browsing by subject (tags), searching (search), downloading (files), subscribing (feeds or newsletter), or printing
(PDF export).

## Record types

Each kind of record becomes a **content class** with its own fields
([Content classes for websites](../../creators/website-creators/content-classes-for-websites.md)). Keep the number of
classes small; a record type deserves its own class when its fields differ, not when its subject differs. Typical set:

- [ ] Book or monograph
- [ ] Article or report
- [ ] Audio or video recording
- [ ] Person or organization (authors, creators) as their own objects, related to records
- [ ] Collection or series (a container for records)

Details in [Modeling catalog records](modeling-catalog-records.md).

## Vocabularies

Decide which lists are controlled and who owns them:

| Vocabulary | Tool |
|---|---|
| Subject headings, with broader and narrower terms | `eztags` tag tree ([Tagging and subject headings](tagging-and-subject-headings.md)) |
| A short fixed list (format, language of the item) | `ezselection` field ([Choosing datatypes](../../creators/website-creators/choosing-datatypes.md)) |
| Free keywords | open `eztags` vocabulary or `ezkeyword` |
| Where an item sits in the menu | the content tree, not tags |

## How records arrive

| Route | When | Where it is documented |
|---|---|---|
| Typed by catalogers in the administration | Small collections, special items | [Editing content](../../editing-content.md) |
| Imported by a developer script | An existing export from another system | [Creating and updating content in code](../../creators/app-creators/creating-content-in-code.md) |
| Created through the REST API | Another system pushes records | [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md) |
| Read from another site's RSS feed | A partner publishes a feed of items | [RSS feeds](../../../guides/rss-feeds.md) |
| Uploaded Writer documents | Reports written in LibreOffice or OpenOffice | [ezodf](../../../features/6.0/extensions/ezodf.md) |

For imports, the developer's guide recommends remote ids for idempotent imports, so running an import twice updates
records instead of duplicating them ([Creating and updating content in code](../../creators/app-creators/creating-content-in-code.md)).

## Who catalogs and who checks

- Catalogers need create and edit rights in the catalog subtree, and `tags/add` only below the tags they curate
  ([Tagging with eztags, Permissions](../../creators/website-creators/tagging-with-eztags.md#permissions)).
- If records must be checked before they go public, use an approval workflow
  ([Workflows](../../../guides/workflows.md)).
- Every publication keeps its versions, so you can see who changed a record and when
  ([Content history](../../../guides/content-history.md)).

## Public, restricted and internal

Use sections to separate what everybody may read from what only members or staff may read
([Sections](../../../guides/sections.md),
[Sections and object states](../../creators/website-creators/sections-and-object-states.md)). Downloads go through a
permission check, so a file in a members-only section can only be downloaded by members
([Files and downloads](../../creators/website-creators/files-and-downloads.md#how-a-download-works)).

## Languages

If you describe records in several languages, decide which fields are translatable (titles and descriptions usually
are, identifiers such as an ISBN are not) and whether tags are translated
([Multilingual sites](../../creators/website-creators/multilingual-sites.md)).

## Measuring success

Choose a few measures you will review: searches that find nothing (search statistics,
[Catalog search](catalog-search.md#learning-from-what-readers-search)), downloads, feed subscribers, and records added
per month. Use your own figures; this documentation does not provide benchmarks.

## A planning worksheet

```text
Catalog scope:                ...
Master system for holdings:   ...
Expected records (now / 3 y): ...
Record types (classes):       ...
Controlled vocabularies:      ...
Vocabulary owner:             ...
How records arrive:           typed / script import / REST / RSS import
Approval before publishing:   yes / no
Restricted material:          ... (section: ...)
Languages:                    ...
Search engine:                built-in / Solr (ezfind)
Measures reviewed monthly:    ...
```

## Related pages

- [Libraries and catalogs](README.md)
- [Modeling catalog records](modeling-catalog-records.md)
- [Planning your website](../../creators/website-creators/planning-your-website.md)
- [Content modeling workshops](../../leadership/content-modeling-workshops.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
