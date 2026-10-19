---
title: Metadata fields for datasets
audience: government, editor, website-creator
category: public-sector
order: 50
summary: A reference for editors and site builders that maps each common dataset metadata term (DCAT 3) to an Exponential datatype, with guidance on what to write in it and which fields a catalog usually requires.
keywords: metadata, DCAT, dataset metadata, title, description, publisher, license, theme, keywords, issued, modified, frequency, datatype mapping, open data
type: reference
---
# Metadata fields for datasets

This reference is for editors who describe datasets and for site builders who design the dataset class. It lists
the metadata terms of the W3C [Data Catalog Vocabulary (DCAT 3)](https://www.w3.org/TR/vocab-dcat-3/), the
Exponential datatype that holds each one, and what good content for it looks like. The datatypes are those listed
in [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md).

## Contents

1. [Dataset terms](#dataset-terms)
2. [Distribution terms](#distribution-terms)
3. [Writing good metadata](#writing-good-metadata)
4. [Fields Exponential fills for you](#fields-exponential-fills-for-you)

## Dataset terms

| DCAT term | Label for editors | Datatype | What to write |
|---|---|---|---|
| `dct:title` | Title | `ezstring` | A name a person can search for: "Street lighting maintenance requests 2025", not "SLMR_v3" |
| `dct:description` | Description | `ezxmltext` | What the data is, how it was collected, known gaps, how often it changes |
| `dct:publisher` | Publisher | `ezstring` or `ezobjectrelation` | The organization, for example "Department of Example" |
| `dcat:contactPoint` | Contact | `ezemail` | A team mailbox, never a personal address |
| `dct:license` | License | `ezselection` | One of the licenses your body approved |
| `dcat:theme` | Theme | `eztags` or `ezselection` | One or more themes from a fixed list |
| `dcat:keyword` | Keywords | `ezkeyword` | Free words people search for, in each language |
| `dct:issued` | First published | `ezdate` | The date the dataset was first made public |
| `dct:modified` | Last changed | `ezdate` | The date the data last changed (not the page text) |
| `dct:accrualPeriodicity` | Update frequency | `ezselection` | Daily, weekly, monthly, yearly, irregular, never |
| `dct:spatial` | Area covered | `ezstring` | The area the data is about |
| `dct:temporal` | Period covered | `ezstring` | For example "January 2020 to December 2025" |
| `dct:language` | Language of the data | `ezselection` | The language of the values in the file, which can differ from the page |
| `dct:conformsTo` | Standard or schema | `ezurl` | A link to the schema or standard the data follows |

## Distribution terms

| DCAT term | Label | Datatype | What to write |
|---|---|---|---|
| `dct:title` | Title | `ezstring` | "CSV, all years" |
| `dcat:downloadURL` | File | `ezbinaryfile` | The file itself; its download address is made by Exponential |
| `dcat:accessURL` | Address | `ezurl` | When the data is served elsewhere (an API, another portal) |
| `dct:format` | Format | `ezselection` | CSV, JSON, XML, XLSX, ODS ... |
| `dcat:byteSize` | Size | shown by the file datatype | Exponential shows the size of an uploaded file |
| `dcat:mediaType` | Media type | shown by the file datatype | Exponential stores the MIME type of the upload |

## Writing good metadata

- **Describe the data, not the project.** Say what one row is, what each column means and where the data came
  from. A short data dictionary in the description, or a separate CSV of column definitions, helps re-users most.
- **Use the same words as your users.** Check **Setup > Search statistics** for what visitors type
  ([Search statistics](../../../guides/urls-and-aliases.md#5-search-statistics)), and add those words to the keywords.
- **Keep dates true.** "Last changed" is about the data. Exponential records when the page changed in its version
  history; that is a different date.
- **One license per dataset.** If parts are under different terms, split the dataset.
- **Plain language** in titles and descriptions; avoid internal codes and abbreviations.

## Fields Exponential fills for you

| Information | Where it comes from |
|---|---|
| Created and modified times of the page | the object's versions ([Content history](../../../guides/content-history.md)) |
| Who published it | the version's creator, and the audit trail ([Audit trail](../../../features/6.0/audit-trail.md)) |
| Address of the page | the URL alias of the node |
| Size and MIME type of a file | the file datatype |
| Languages the page exists in | the object's translations ([Content languages](../../../guides/content-languages.md)) |

## Related pages

- [Designing a dataset class](designing-a-dataset-class.md)
- [Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)
- [Metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
