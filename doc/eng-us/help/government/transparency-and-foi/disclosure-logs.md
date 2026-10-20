---
title: Disclosure logs
audience: government, editor, website-creator
category: public-sector
order: 50
summary: How to publish a disclosure log, the public list of information released in answer to requests, in Exponential: a class for log entries without requester data, a list page, a feed, and the order of steps so nothing personal is published.
keywords: disclosure log, released information, FOI responses, FOIA reading room, request log, transparency, published responses, register
type: help
---
# Disclosure logs

This page is for editors and site builders who publish a log of information released in response to requests.
Some bodies publish such a log because the law requires it, others by policy. The log is content in Exponential: one
entry per released response, listed on one page, with the released documents attached.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What an entry contains and what it never contains](#what-an-entry-contains-and-what-it-never-contains)
2. [A class for log entries](#a-class-for-log-entries)
3. [The log page](#the-log-page)
4. [Publishing an entry](#publishing-an-entry)
5. [Feed and search](#feed-and-search)
6. [Checklist](#checklist)

## What an entry contains and what it never contains

| Contains | Never contains |
|---|---|
| a reference number of your own | the requester's name, e-mail or address |
| the date of the response | the text of the request if it identifies the requester |
| a neutral summary of what was asked | internal notes or deliberations |
| the outcome (released, partly released, withheld) and the grounds | unredacted documents |
| the released documents as files | documents that still need redaction checks |

The request itself, with personal data, stays in your case system or in **Setup > Collected information**, which is
not public (see "Handling requests from collected information").

## A class for log entries

| Attribute | Datatype | Note |
|---|---|---|
| Reference | Text line | your own number, for example "FOI-2026-0123" |
| Title | Text line | a neutral summary as title |
| Date of response | Date | |
| Subject | Tags or Selection | theme of the request |
| Outcome | Selection | Released, Partly released, Withheld, Information not held |
| Summary of the request | Text block | no personal data |
| Response | Rich text | the answer, or a summary of it |
| Grounds for withholding | Text block | the exemptions applied |

Released documents go below the entry as File objects, so the entry is a container. Object name pattern:
`<reference> <title>`.

## The log page

A folder "Disclosure log" with the entries as children, sorted by date of response, newest first. Give the entry
class a line view that shows reference, title, date and outcome ([Template overrides](../../creators/website-creators/template-overrides.md)).
For a large log, group entries in one folder per year.

## Publishing an entry

1. Confirm the documents passed the redaction check (see "Redaction is a manual step").
2. Under **Sub items** of the year folder, press **Create new**, choose the entry class, fill in the fields.
3. Add the released files below the entry.
4. **Send for publishing**; with an approval workflow a second person approves.
5. Open the entry signed out and check every file opens and shows only what was meant to be released.

## Feed and search

- An RSS export of the log folder lets journalists and researchers follow new releases ([RSS feeds](../../../guides/rss-feeds.md)).
- Make Title, Summary and Response **Searchable** so the site search finds earlier releases; with
  `binaryfile.ini` handlers the text of PDFs can be indexed too ([Files and downloads](../../creators/website-creators/files-and-downloads.md#searching-inside-files)).
- Search statistics show what people look for and do not find ([Search statistics](../../../guides/urls-and-aliases.md#5-search-statistics)).

## Checklist

- [ ] No entry and no file name contains personal data of requesters.
- [ ] Redaction check recorded before publishing.
- [ ] Entries sorted by date; a feed exists.
- [ ] Withheld outcomes are logged too, with grounds.

## Related pages

- [Publishing a FOI response](publishing-a-foi-response.md)
- [Redaction is a manual step](redaction-is-a-manual-step.md)
- [Search and findability of disclosures](search-and-findability-of-disclosures.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
