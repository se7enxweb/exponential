---
title: Evaluation checklist template for a public-sector CMS
audience: government, project-manager, sales-marketing
category: public-sector
order: 20
summary: A copy-and-adapt checklist an evaluation panel uses to examine Exponential (or any CMS) against a tender's requirements, with the evidence to look for in the repository for each line.
keywords: evaluation checklist, tender evaluation, bid evaluation, CMS assessment, compliance matrix, evidence, procurement, public sector, template
type: help
---
# Evaluation checklist template for a public-sector CMS

This page is for the members of an evaluation panel and the procurement officer who prepares their work. It gives a
checklist you can copy into your own evaluation file, and for each line the place where the evidence for Exponential
can be found. The evidence sources are the standards pages in `doc/standards/` and the feature documentation of the
repository; nothing on this page replaces your own tests.

> **Template** - adapt to your procurement rules; not legal advice.

## Contents
1. [How to use the checklist](#how-to-use-the-checklist)
2. [Header of the evaluation file](#header-of-the-evaluation-file)
3. [Functional checks](#functional-checks)
4. [Accessibility checks](#accessibility-checks)
5. [Security and privacy checks](#security-and-privacy-checks)
6. [Languages and content checks](#languages-and-content-checks)
7. [Operations, support and exit checks](#operations-support-and-exit-checks)
8. [Recording the result](#recording-the-result)

## How to use the checklist

1. Copy the lines below into your evaluation file and delete those your tender did not ask for.
2. Keep the wording of each line identical to the requirement in your tender, so a reviewer can trace it.
3. For every line, record three things: the answer the bidder gave, the evidence you checked, and your finding
   (met, partly met, not met, not evidenced).
4. Treat "Unverified" in a vendor's own documentation as "not evidenced" unless the bid adds evidence. The
   Exponential standards pages use exactly these status words: "Complies", "Partly complies", "Unverified" and "Does
   not comply" ([Accessibility standards](../../../../standards/accessibility.md)).
5. Score afterwards, with the scoring sheet template (a separate page of this folder).

## Header of the evaluation file

```text
Tender:            <reference and title>
Bid:               <bidder name> / product: Exponential <version>
Evaluator:         <name, role>
Date(s):           <dates of review, demo and proof of concept>
Conflict of interest declared: <yes/no, date>
Sources checked:   repository at tag/commit <...>; demo on <date>; proof of concept on <date>
```

## Functional checks

| # | Check | Where the evidence for Exponential is |
|---|---|---|
| F1 | Structured content types defined by administrators | [Content model and editing](../../../../guides/content-model-and-editing.md) |
| F2 | Drafts, versions, compare and restore | [Versions of an object](../../../../guides/content-history.md), [Drafts and pending](../../../../guides/drafts-and-pending.md) |
| F3 | Approval before publishing | [Workflows](../../../../guides/workflows.md) |
| F4 | Roles and permissions per section and content type | [Roles and policies](../../../../guides/roles-and-policies.md), [Sections](../../../../guides/sections.md) |
| F5 | Several sites from one installation | [Multisite setup](../../../creators/website-creators/multisite-setup.md) |
| F6 | Forms that collect submissions | [Collected information](../../../../guides/collected-information.md) |
| F7 | Site search | [Site search](../../../creators/website-creators/site-search.md), [Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md) |
| F8 | RSS feeds and PDF export | [RSS feeds](../../../../guides/rss-feeds.md), [PDF exports](../../../../guides/pdf-exports.md) |
| F9 | Content API for other systems | [REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md) |

## Accessibility checks

| # | Check | Evidence |
|---|---|---|
| A1 | Status against WCAG 2.2 AA stated per component | [Accessibility standards](../../../../standards/accessibility.md): admin4/admin4l "Partly complies"; public designs not audited |
| A2 | EN 301 549 and Section 508 position | same page: both "Unverified"; no VPAT or Accessibility Conformance Report exists |
| A3 | Who makes the public site accessible | the bidder's own design and testing plan; see [Testing accessibility](../../../creators/website-creators/testing-accessibility.md) |
| A4 | Accessibility statement for the site | the bidder must write it; no template ships ([Accessibility standards](../../../../standards/accessibility.md#known-gaps-2)) |

## Security and privacy checks

| # | Check | Evidence |
|---|---|---|
| S1 | OWASP Top 10 mapping | [Security standards](../../../../standards/security.md): "Partly complies"; ASVS "Unverified" |
| S2 | Two-step sign-in | [Two-step sign-in](../../../../guides/two-factor-login.md) (extension, opt-in) |
| S3 | Audit trail of who did what | [Audit trail](../../../../features/6.0/audit-trail.md), on by default |
| S4 | Vulnerability disclosure and supported versions | `SECURITY.md` in the repository root, summarized in [Security standards](../../../../standards/security.md#vulnerability-disclosure) |
| S5 | Hardening plan for production | [Security hardening](../../../security-hardening.md), [chapter 13](../../../../install/13-security-hardening.md) |
| P1 | Consent records, erasure, log minimization | [Privacy standards](../../../../standards/privacy.md) |
| P2 | Cookie consent | none shipped: the bidder must add one if the site uses analytics or embeds ([Privacy standards](../../../../standards/privacy.md#eprivacy-directive-cookies)) |

## Languages and content checks

| # | Check | Evidence |
|---|---|---|
| L1 | Content in several languages with fallback | [Content languages](../../../../guides/content-languages.md) |
| L2 | Right-to-left languages | [Right-to-left languages](../../../../features/6.0/right-to-left.md) |
| L3 | Administration in the staff's language | [Languages](../../../languages.md) |

## Operations, support and exit checks

| # | Check | Evidence |
|---|---|---|
| O1 | Supported PHP versions and databases | [Requirements](../../../../install/02-requirements.md) |
| O2 | Backups of database and files | [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md) |
| O3 | Upgrade path and check | [Upgrading](../../../../guides/upgrading.md), [Upgrade check](../../../../guides/upgrade-check.md) |
| O4 | Release policy | [Source control and releases](../../../../standards/source-control-and-releases.md) |
| O5 | Support offer with named response times | the bid itself; compare with [Support levels and response times](../../../partners/support-levels-and-response-times.md) |
| X1 | Complete data export at exit | [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md) |
| X2 | License with no fee and source available | [Licensing questions](../../../project/licensing-faq.md) |

## Recording the result

```text
Line | Bid answer (quote) | Evidence checked (link, test, demo minute) | Finding | Evaluator initials
F1   | ...                | ...                                         | met     | ...
```

Keep the evidence links in the file. A finding without a source is not defensible when a bidder asks for a debrief.

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Accessibility standards](../../../../standards/accessibility.md)
- [Security standards](../../../../standards/security.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
