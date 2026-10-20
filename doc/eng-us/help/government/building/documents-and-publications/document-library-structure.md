---
title: Document library structure
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Where to keep documents on a public-sector Exponential site - the media library or next to the pages that use them, a Publication class as a landing page, relations instead of copies, folders by type and year, and a publications list visitors can filter.
keywords: document library, media library, publications, landing page, publication class, relations, folders, document types, year folders, publications list, filter
type: help
---
# Document library structure

This page is for website creators and project managers who decide where documents live and how visitors find them.
Exponential lets files live anywhere in the tree; the choice affects permissions, menus and upkeep. Sources:
[Organizing the media library](../../../creators/website-creators/organizing-the-media-library.md) and
[Relations and reuse](../../../creators/website-creators/relations-and-reuse.md).

## Contents

1. [Two places for files](#two-places-for-files)
2. [Publications with a landing page](#publications-with-a-landing-page)
3. [Folders](#folders)
4. [A publications list](#a-publications-list)
5. [Rules](#rules)

## Two places for files

| Place | Good for | Note |
|---|---|---|
| The media library (Media section) | files used by many pages: forms, logos, standard documents | in the default package the Anonymous role reads the Media section; check yours |
| Next to the page that uses them | documents that belong to one page (meeting papers below the meeting) | they take the page's section and permissions |

Either way, pages **relate** to File objects (a relation field, or an embed in rich text) rather than uploading a copy
each time.

## Publications with a landing page

For reports, plans and guidance, a **Publication** object is the page people find and cite; the file is attached to
it. A Publication class with title, summary, publication date, reference, type, topics and related files is proposed
in [Content model for national government](../../levels/national/content-model.md#class-to-add-publication).

| Benefit | Why |
|---|---|
| An HTML summary | readable without downloading; found by search |
| A stable address | the file can be replaced without changing the address people cite |
| Several files (language versions, annexes, data) | one relation list |
| Metadata | date, type and topics for lists and filters |

## Folders

```text
Publications                      Publication objects, sorted by publication date
├── Reports
├── Strategies and plans
├── Guidance
└── Data
Media
└── Files
    └── Publications <year>       the File objects of that year's publications
```

Type folders for visitors; year folders in the media library for the people who maintain the files. A Publication
listed under two types gets a second location.

## A publications list

A template fetch lists Publication objects below the Publications folder, newest first, and a selection or tag filter
narrows it (by type, topic or year). With eZ Find, facets do this for search results
([Search with eZ Find and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md)). The sub-items
table of the administration exports the same list as CSV for an inventory ([Content model and editing, 5](../../../../guides/content-model-and-editing.md#5-the-sub-items-table)).

## Rules

| Rule | Reason |
|---|---|
| One File object per document; never two copies | one place to correct |
| Readable file names (`annual-report-2025.pdf`) | they appear in the download address |
| No documents in sections that visitors cannot read unless they are meant to be restricted | broken downloads |
| Check reverse relations before removing a file | pages that link to it ([Reverse relation check](../../../../features/6.0/reverse-relation-check.md)) |

## Related pages

- [Documents and publications](README.md)
- [Metadata and tagging](metadata-and-tagging.md)
- [Structuring the content tree](../../../creators/website-creators/structuring-the-content-tree.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
