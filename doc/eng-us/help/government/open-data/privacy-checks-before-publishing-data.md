---
title: Privacy checks before publishing data
audience: government, editor, project-manager
category: public-sector
order: 160
summary: A check routine for data teams before a dataset goes public: personal data in columns and free text, small counts that identify people, metadata inside files, and what Exponential itself adds or records when you publish.
keywords: privacy, personal data, GDPR, anonymization, aggregation, small numbers, metadata, file properties, re-identification, open data, data protection
type: help
---
# Privacy checks before publishing data

This page is for data publishers and their managers. Publishing a dataset is permanent in practice: once
downloaded, a file cannot be called back. This routine helps catch personal data before it leaves. Exponential stores
and serves the file you upload; it does not inspect its contents, so these checks are a human step.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The rules that usually apply](#the-rules-that-usually-apply)
2. [The check routine](#the-check-routine)
3. [Metadata inside files](#metadata-inside-files)
4. [What Exponential records when you publish](#what-exponential-records-when-you-publish)
5. [If personal data was published](#if-personal-data-was-published)

## The rules that usually apply

In the EU the [GDPR, Regulation (EU) 2016/679](https://eur-lex.europa.eu/eli/reg/2016/679/oj) governs personal data,
and the [Open Data Directive (EU) 2019/1024](https://eur-lex.europa.eu/eli/dir/2019/1024/oj) applies to re-use of
public sector information without affecting the protection of personal data. Other jurisdictions have their own
privacy laws. Your data protection officer decides; this page gives the practical checks.

## The check routine

Run it for every new dataset and every new file of an existing one.

| # | Check | How |
|---|---|---|
| 1 | No direct identifiers | Look for columns with names, e-mail addresses, phone numbers, street addresses of individuals, ID numbers, account names |
| 2 | No identifiers in free text | Read or search comment and description columns; people write names into them |
| 3 | No small counts | In tables of counts, cells with very small numbers can identify people; aggregate or suppress them according to your body's rule |
| 4 | No rare combinations | Age, district and date together can single out one person; coarsen them (age bands, months) |
| 5 | No hidden data | Hidden spreadsheet columns or sheets, comments, tracked changes, previous values |
| 6 | Correct file | The file name and the content match the dataset page; not a draft or the internal full version |
| 7 | Second pair of eyes | A steward approves (see "Who may publish datasets") |

Record the check (who, when, which file) in your internal system or in a field of the dataset that is not shown
publicly, such as an internal note that your template does not print.

## Metadata inside files

Office documents and PDFs carry properties: author, organization, comments, sometimes the path on the author's
computer. Remove them with the program that made the file, or export to a format without them (CSV has none).
Exponential stores the file unchanged; it does not strip properties.

## What Exponential records when you publish

| What | Where | Visible to the public |
|---|---|---|
| The version and its creator | the object's version history | no, staff with `content/versionread` |
| Publish and remove events | the audit trail, `content` channel | no, staff with `audit/read` |
| The original file name | the file field and the download address | yes, the file name appears in the address |
| The owner of the object | object information | depends on your templates; do not print it on public pages |

Choose file names without personal data, because they appear in the download address
([Files and downloads](../../creators/website-creators/files-and-downloads.md)).

## If personal data was published

1. Remove the file from the distribution at once and publish; then remove the older versions that still hold it
   on the object's **Versions** page ([Content history](../../../guides/content-history.md)).
2. Clear the caches in front of the site so copies stop being served ([Caches](../../../guides/caches.md)).
3. Tell your data protection officer; they decide on notification duties.
4. Find out where it went: the request logs and, if reads are switched on, the audit trail's `read` channel can show
   downloads ([Request logs](../../../features/6.0/request-logs.md), [Audit trail](../../../features/6.0/audit-trail.md)).
5. Publish the corrected file and a notice on the dataset page.

## Related pages

- [Privacy standards](../../../standards/privacy.md)
- [Versioning and updating datasets](versioning-and-updating-datasets.md)
- [Who may publish datasets](who-may-publish-datasets.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
