---
title: Designing a dataset class
audience: government, website-creator, admin
category: public-sector
order: 40
summary: For site builders in a public body who need dataset pages: a content class "Dataset" with a "Distribution" child class, built in Setup > Classes from the datatypes Exponential ships, with identifiers that map to DCAT terms.
keywords: dataset class, content class, distribution, DCAT, metadata, class editor, datatypes, ezbinaryfile, ezselection, ezdate, ezurl, open data
type: help
---
# Designing a dataset class

This page is for website creators and administrators who build the place where a public body publishes its
datasets. Exponential ships no dataset class, so you make one in the class editor. The design below uses only
datatypes that ship with the kernel, and names its fields after the
[DCAT 3](https://www.w3.org/TR/vocab-dcat-3/) terms catalogs use, so a later DCAT export can be written from
them. The class editor steps come from [Content classes for websites](../../creators/website-creators/content-classes-for-websites.md).

## Contents

1. [Two classes: dataset and distribution](#two-classes-dataset-and-distribution)
2. [The Dataset class](#the-dataset-class)
3. [The Distribution class](#the-distribution-class)
4. [Creating the classes](#creating-the-classes)
5. [Decisions to write down](#decisions-to-write-down)
6. [Pitfalls](#pitfalls)

## Two classes: dataset and distribution

DCAT separates a **dataset** (the collection of data, with its description) from its **distributions** (each
file or format you offer: a CSV, a JSON file, a spreadsheet). The same split works well as content:

| Content | Class | Container | DCAT term |
|---|---|---|---|
| Data catalog folder | Folder (shipped) | yes | `dcat:Catalog` |
| One dataset page | Dataset (yours) | yes | `dcat:Dataset` |
| One file below it | Distribution (yours), or the shipped File class | no | `dcat:Distribution` |

Making the dataset a container keeps its files together, so the dataset page can list its children and an RSS
export of the catalog folder can follow new datasets.

## The Dataset class

| Attribute name | Identifier | Datatype | Required | Translatable | DCAT term |
|---|---|---|---|---|---|
| Title | `title` | Text line (`ezstring`) | yes | yes | `dct:title` |
| Description | `description` | Rich text (`ezxmltext`) | yes | yes | `dct:description` |
| Publisher | `publisher` | Text line, or an object relation to an organization page | yes | no | `dct:publisher` |
| Contact point | `contact_point` | E-mail (`ezemail`) of a team mailbox, never a person | yes | no | `dcat:contactPoint` |
| License | `license` | Selection (`ezselection`) | yes | no | `dct:license` |
| Theme | `theme` | Tags (`eztags`) or Selection | no | no | `dcat:theme` |
| Keywords | `keywords` | Keywords (`ezkeyword`) | no | yes | `dcat:keyword` |
| Issued | `issued` | Date (`ezdate`) | yes | no | `dct:issued` |
| Modified | `modified` | Date (`ezdate`) | no | no | `dct:modified` |
| Update frequency | `accrual_periodicity` | Selection | no | no | `dct:accrualPeriodicity` |
| Spatial coverage | `spatial` | Text line | no | yes | `dct:spatial` |
| Temporal coverage | `temporal` | Text line, such as "2020-2025" | no | yes | `dct:temporal` |
| Conformance or schema | `conforms_to` | URL (`ezurl`) | no | no | `dct:conformsTo` |

Set the object name pattern to `<title>`. Mark Title, Description and Keywords **Searchable**. The contact point is
a function mailbox (for example `opendata@example.gov`) so the page names no individual.

## The Distribution class

| Attribute name | Identifier | Datatype | DCAT term |
|---|---|---|---|
| Title | `title` | Text line | `dct:title` |
| File | `file` | File (`ezbinaryfile`) | `dcat:downloadURL` (the download address) |
| Or: access address | `access_url` | URL, for data served elsewhere | `dcat:accessURL` |
| Format | `format` | Selection: CSV, JSON, XML, XLSX, GeoJSON ... | `dct:format` |
| Description | `description` | Text block (`eztext`) | `dct:description` |

The shipped **File** class (`file`) also works when you do not need a format field: its name and file are enough,
and the MIME type of the upload is shown by the file datatype.

## Creating the classes

1. Open **Setup > Classes**, the group **Content**, then **New class**.
2. Name `Dataset`, identifier `dataset`, object name pattern `<title>`, tick **Container**.
3. For each row of the table: choose the datatype under **Add attribute**, press **Add**, give the name and the
   identifier, set **Required**, **Searchable** and **Translatable** as in the table.
4. For the License selection, enter the licenses your body has approved (see "Licensing datasets").
5. Press **OK**. Repeat for `distribution`.
6. Allow the classes where they belong: give the data team `content/create` for the classes `dataset` and
   `distribution` below the catalog folder ([Roles and policies](../../../guides/roles-and-policies.md)).

## Decisions to write down

- Which fields are required: few required fields make drafts easy, but license, publisher and contact point should
  never be empty on a published dataset.
- Which fields are translated: titles and descriptions yes; dates, licenses and formats no.
- Who owns the class: a change to an identifier later breaks every template that reads it.

## Pitfalls

- **Removing an attribute deletes its data from every object.** Add new fields freely; remove them only after a
  backup ([Content classes for websites](../../creators/website-creators/content-classes-for-websites.md#changing-a-class-on-a-live-site)).
- **Do not reuse the article class** for datasets: the fields differ, and templates and feeds become hard to keep
  apart.
- **No schema check.** The Format field records what the editor chose; Exponential does not inspect the file.

## Related pages

- [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md)
- [Designing your content model](../../creators/website-creators/designing-your-content-model.md)
- [Content model and editing](../../../guides/content-model-and-editing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
