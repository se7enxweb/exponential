---
title: Metadata and tagging of documents
audience: government, website-creator, editor
category: public-sector
order: 50
summary: Which metadata public documents need and how to store it with real Exponential datatypes - title, date, reference, type, language, owner, topics with eztags, search engine metadata with xrowmetadata - and how editors keep it consistent.
keywords: document metadata, publication date, reference number, document type, language, owner, topics, eztags, ezselection, ezdate, xrowmetadata, consistency, controlled vocabulary
type: help
---
# Metadata and tagging of documents

This page is for website creators and editors who describe documents. Metadata makes documents findable, listable and
manageable: the publication date for lists, the type for filters, topics for cross-references, an owner for upkeep.
Datatypes named are those of `kernel/classes/datatypes/` and the eztags and xrowmetadata extensions.

## Contents

1. [The metadata to keep](#the-metadata-to-keep)
2. [Topics with eztags](#topics-with-eztags)
3. [Search engine metadata](#search-engine-metadata)
4. [Keeping it consistent](#keeping-it-consistent)

## The metadata to keep

| Metadata | Datatype | Where |
|---|---|---|
| Title | Text line | the object name |
| Publication date (official) | Date (`ezdate`) | separate from the publish time, which may differ |
| Reference or document number | Text line | for citations |
| Document type | Selection (`ezselection`) | report, plan, guidance, form, decision, data |
| Language | the object's translation, or a Selection for files in one language only | |
| Owning unit | the section; or a Text line with the unit name | upkeep |
| Topics | Tags (`eztags`) | cross-references, topic pages |
| Review or expiry date | Date | for living documents |
| Summary | XML block | readable on the page; searchable |

These attributes sit on the Publication (landing page) class rather than on every File object; files are related to
it ([Document library structure](document-library-structure.md)).

## Topics with eztags

| Choice | Practice |
|---|---|
| One tag tree for the whole site | documents, news and services share topics |
| Closed vocabulary for documents | **Limit by tags subtree** on the attribute; editors choose, the web team adds tags |
| Synonyms | for the words people use ([Life events and topics](../service-catalog/life-events-and-topics.md)) |
| Tag pages | `/tags/view/<tag>` lists everything with the tag ([Tagging with eztags](../../../creators/website-creators/tagging-with-eztags.md)) |

## Search engine metadata

A Metadata attribute (`xrowmetadata`) on the Publication class gives the page a title and description for search
results and link previews ([Metadata and social sharing](../news-and-alerts/metadata-and-social-sharing.md)). PDF files
carry their own metadata (title, author, language) in the file's properties: set them in the source program before
export, because search engines show the file's own title for a PDF result.

## Keeping it consistent

| Measure | How |
|---|---|
| Required fields | tick **Required** for date, type and summary in the class |
| Selections instead of free text | types and languages as Selection |
| A review of tags yearly | merge duplicates in the Tags dashboard |
| An inventory | the sub-items table with metadata columns, exported as CSV ([Content model and editing, 5](../../../../guides/content-model-and-editing.md#5-the-sub-items-table)) |
| Open data catalogs | if documents are datasets, describe them with [DCAT 3](https://www.w3.org/TR/vocab-dcat-3/) in your catalog; Exponential does not produce DCAT by itself |

## Related pages

- [Documents and publications](README.md)
- [Versions of documents](versions-of-documents.md)
- [eztags](../../../../features/6.0/extensions/eztags.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
