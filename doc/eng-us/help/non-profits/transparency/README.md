---
title: Transparency for non-profits
audience: non-profit, government, businesses, editor
category: non-profits
order: 10
summary: How a non-profit uses its Exponential site to be open about money, governance and decisions - annual publications and financial documents as downloads, board and minutes pages, version history and the tamper-evident audit trail as accountability, and open data through RSS feeds, exports and the REST API - with what the software provides and what remains your own process.
keywords: transparency, accountability, annual publications, financial statements, 501(c)(3), charity filings, board, minutes, governance, versions, audit trail, open data, RSS, REST API, trust
type: help
---
# Transparency for non-profits

This folder is for directors, treasurers, board secretaries and communications staff. Donors, funders, regulators and
the people you serve increasingly expect to see how a non-profit is run: where the money goes, who decides, and what
was decided. In some countries some of this is required by law, for example the public disclosure duties of US
501(c)(3) organizations or the filings of registered charities elsewhere; this help does not give legal advice, so
ask your accountant or lawyer what applies to you.

## Contents

1. [What Exponential provides](#what-exponential-provides)
2. [The pages of this folder](#the-pages-of-this-folder)
3. [A transparency page set](#a-transparency-page-set)
4. [What stays your own process](#what-stays-your-own-process)

## What Exponential provides

| Need | What exists | Source |
|---|---|---|
| Publish documents | File objects (`file` class, `ezbinaryfile`), downloads through `content/download` with a permission check | [Files and downloads](../../creators/website-creators/files-and-downloads.md) |
| Keep earlier versions | Every publication is a version; 45 per object by default, configurable per class | [Version history limit](../../government/records-and-archiving/version-history-limit.md) |
| Show who changed what | The tamper-evident audit trail, on by default | [The audit trail](../../../features/6.0/audit-trail.md) |
| Publish on a date | Scheduled publication with a workflow | [Scheduled publishing](../../customers/day-to-day/scheduled-publishing.md) |
| Board-only papers | Sections and roles | [Sections and object states](../../creators/website-creators/sections-and-object-states.md) |
| Feeds of new publications | RSS 2.0 and Atom exports | [RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md) |
| Machine-readable access | The REST API (`ezprestapi`, v1 and v2) | [REST API v1 and v2](../../../guides/rest-api-v1-and-v2.md) |

## The pages of this folder

| Page | What it answers |
|---|---|
| [Annual publications as downloads](annual-publications-as-downloads.md) | Annual reviews and filings as files, by year, accessible, with corrections |
| [Financial documents](financial-documents.md) | Accounts, budgets, spending on grants; what to publish and what to keep private |
| [Board and minutes pages](board-and-minutes-pages.md) | Board members, meetings, agendas and minutes; public and board-only |
| [Versions and the audit trail](versions-and-the-audit-trail.md) | Showing what a page said and who changed it |
| [Open data, RSS and REST](open-data-rss-and-rest.md) | Feeds, exports and the API for funders, networks and researchers |

Outline: [Annual publications page outline](../templates/annual-publications-page-outline.md).

## A transparency page set

A typical "Accountability" area of a non-profit site:

| Page | Content | Page in this folder |
|---|---|---|
| Who we are | Mission, legal name, registration details | |
| Our people | Board, leadership, how decisions are made | [Board and minutes pages](board-and-minutes-pages.md) |
| Our money | Annual publications, financial statements, where donations go | [Financial documents](financial-documents.md) |
| Our policies | Privacy, complaints, safeguarding, conflict of interest | [Annual publications as downloads](annual-publications-as-downloads.md) |
| Our impact | Outcomes, stories, evaluations | [Grant outcomes for funders](../grants/grant-outcomes-for-funders.md) |
| Contact | Who answers questions about these documents | |

Public bodies' practice is a good model; see [Transparency in Exponential at a glance](../../government/transparency-and-foi/transparency-in-exponential-at-a-glance.md)
and [Proactive disclosure](../../government/transparency-and-foi/proactive-disclosure.md).

## What stays your own process

- Deciding what must be published, and by when (law, regulator, funder).
- Redacting personal or confidential data before publishing: "redaction is a manual step"
  ([Redaction is a manual step](../../government/transparency-and-foi/redaction-is-a-manual-step.md)).
- Approving documents before they go live ([Approval before publishing](../../government/transparency-and-foi/approval-before-publishing.md)).
- Keeping the official records; the site is a publication channel, not your archive of record.

## Related pages

- [Non-profits: start here](../non-profits-start-here.md)
- [Governments: start here](../../government/government-start-here.md)
- [Transparency checklist (government)](../../government/transparency-and-foi/transparency-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
