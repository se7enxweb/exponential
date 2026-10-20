---
title: Requirements emphasis for parliaments
audience: government, project-manager, admin
category: public-sector
order: 50
summary: The requirements that weigh most for a parliament site - completeness and permanence of the record, open data, accessibility of documents and video, languages, security and load at peak times - and what Exponential provides.
keywords: requirements, parliament, record, permanence, open data, DCAT, accessibility, captions, transcripts, languages, security, load, peak traffic
type: help
---
# Requirements emphasis for parliaments

This page is for project managers and administrators of a parliament or legislative assembly site.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Completeness and permanence of the record](#completeness-and-permanence-of-the-record)
2. [Open data](#open-data)
3. [Accessibility](#accessibility)
4. [Languages](#languages)
5. [Security and load](#security-and-load)

## Completeness and permanence of the record

| Requirement | Exponential | Your work |
|---|---|---|
| Stable addresses for every record item, for decades | URL aliases; old aliases redirect after a rename or move ([Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md)) | a naming rule (session, number) that does not change |
| Nothing removed silently | versions and the audit trail | a removal procedure |
| Full history of each page | the version history limit: `[VersionManagement] DefaultVersionHistoryLimit` (45 in the shipped `settings/content.ini`) and `VersionHistoryClass[<class id>]` per class | raise it for record classes or keep the record in the source systems |
| Preservation | none built in | archive to your records system; [ISO 14721 (OAIS)](https://www.iso.org/standard/57284.html) describes long-term preservation |

## Open data

Parliamentary data (members, votes, bills) is widely reused. In the EU the Open Data Directive,
[Directive (EU) 2019/1024](https://eur-lex.europa.eu/eli/dir/2019/1024/oj), sets rules for re-use of public sector
information. Exponential offers:

| Channel | Read |
|---|---|
| RSS exports of agendas, bills and votes | [RSS feeds](../../../../guides/rss-feeds.md) |
| The REST API for content | [REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md) |
| Data files (CSV, XML) as File objects | [Files and downloads](../../../creators/website-creators/files-and-downloads.md) |

It does not generate a [DCAT](https://www.w3.org/TR/vocab-dcat-3/) catalog; describe datasets on a page and, if you
need a catalog, publish one from the source systems. State an open license ([Open Definition](https://opendefinition.org/od/2.1/en/)).

## Accessibility

Parliaments publish long documents and video. In the EU, [Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)
covers public sector bodies' websites; parliaments' own status depends on national transposition. Exponential's
status: admin4 administration **partly complies** with WCAG 2.2 AA; EN 301 549 and Section 508 **unverified**; public
designs not audited ([Accessibility standards](../../../../standards/accessibility.md)).

| Content | Practice |
|---|---|
| Video of sittings | captions and the transcript linked from the video page |
| Bills and reports | an HTML summary on the Bill page; accessible PDFs from the drafting system |
| Vote tables | real tables with headers (the Matrix datatype renders a table) |

## Languages

Several official languages are common. Translations per object and a siteaccess per language
([Content languages](../../../../guides/content-languages.md)); record documents usually exist per language as separate
files related from the translated object.

## Security and load

| Topic | Exponential | Read |
|---|---|---|
| Staff accounts | two-step sign-in, enforceable | [Two-step sign-in](../../../../guides/two-factor-login.md) |
| Status | OWASP Top 10 **Partly complies** | [Security standards](../../../../standards/security.md) |
| Peaks during important votes | view cache, static cache, Velocity response cache | [Static cache and Velocity](../../../creators/website-creators/static-cache-and-velocity.md) |
| Defacement or tampering detected | audit trail, file consistency check | [Audit trail](../../../../features/6.0/audit-trail.md), [File consistency check](../../../../features/6.0/file-consistency-check.md) |

Load-test with caches on before the first important vote after launch.

## Related pages

- [Parliaments](README.md)
- [The first 90 days for parliaments](first-90-days.md)
- [Performance and caching](../../../creators/website-creators/performance-and-caching.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
