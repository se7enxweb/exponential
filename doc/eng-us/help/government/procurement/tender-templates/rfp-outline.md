---
title: "RFP outline: a tender skeleton for a public-sector CMS"
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 20
summary: A complete skeleton for a request for proposals (RFP) for a web content management system and its services, section by section, with what goes in each and which clause templates and requirement pages fill it.
keywords: RFP, tender, request for proposals, skeleton, outline, invitation to tender, template, procurement, CMS, sections, annexes, evaluation criteria, pricing schedule
type: help
---
# RFP outline: a tender skeleton for a public-sector CMS

This page is for procurement officers who assemble a tender document for a website or intranet built on a content
management system, with services such as build, hosting, support and training. It gives the skeleton of the
document, says what each section holds, and links the clause templates and requirement pages of this help that fill
it.

**Template** - adapt to your procurement rules; not legal advice

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [The skeleton](#the-skeleton)
2. [Part A: the invitation](#part-a-the-invitation)
3. [Part B: the requirements](#part-b-the-requirements)
4. [Part C: the contract terms](#part-c-the-contract-terms)
5. [Part D: the response forms](#part-d-the-response-forms)
6. [Before you publish](#before-you-publish)

## The skeleton

```text
<Organization> - Request for proposals <reference>
Web content management system and related services

PART A  INVITATION
  A1  Introduction and background of <organization>
  A2  Purpose and scope of the procurement
  A3  Procedure, timetable and contact point
  A4  Eligibility and exclusion grounds (per <applicable rules>)
  A5  Evaluation method and award criteria
PART B  REQUIREMENTS
  B1  Current situation (sites, content volume, languages, users, integrations)
  B2  Functional requirements                     -> annex B2
  B3  Accessibility requirements                  -> annex B3
  B4  Security requirements                       -> annex B4
  B5  Privacy and data protection requirements    -> annex B5
  B6  Languages                                   -> annex B6
  B7  Hosting and data location                   -> annex B7
  B8  Integration                                 -> annex B8
  B9  Operations, support and maintenance         -> annex B9
  B10 Training and documentation                  -> annex B10
  B11 Migration of existing content (if any)      -> annex B11
PART C  CONTRACT TERMS
  C1  Licensing, source code and intellectual property
  C2  Service levels
  C3  Acceptance testing
  C4  Vulnerability handling and patching
  C5  Exit, data return and transition
  C6  Warranty of no lock-in
  C7  Term, renewal, price review
PART D  RESPONSE FORMS
  D1  Compliance matrix (one row per requirement)
  D2  Technical proposal
  D3  Implementation plan and team
  D4  Pricing schedule (one-off and recurring)
  D5  Declarations
```

## Part A: the invitation

- **A1-A2**: describe the organization and the purpose in plain language; say which sites are in scope.
- **A3**: give the dates of questions, demonstrations and the deadline. If you plan a proof of concept, say so here.
- **A5**: publish the weight of each criterion before bids arrive. The evaluation pages of this procurement section
  hold scoring sheets.

## Part B: the requirements

Fill each annex from the requirement pages, which give wording and the evidence to ask for:

| Annex | Source page |
|---|---|
| B2 | [Functional requirements for a public-sector CMS](../requirements/functional-requirements-for-a-public-cms.md) |
| B3 | [Accessibility requirements](../requirements/accessibility-requirements.md) |
| B4 | [Security requirements](../requirements/security-requirements.md) |
| B5 | [Privacy and data protection requirements](../requirements/privacy-and-data-protection-requirements.md) |
| B6 | [Language and multilingual requirements](../requirements/language-and-multilingual-requirements.md) |
| B7 | [Hosting and data location requirements](../requirements/hosting-and-data-location-requirements.md) |
| B8 | [Integration and API requirements](../requirements/integration-and-api-requirements.md) |
| B9 | [Operations and monitoring](../requirements/operations-and-monitoring-requirements.md), [Support and maintenance](../requirements/support-and-maintenance-requirements.md) |
| B10 | [Training clause](training-clause.md), [Documentation clause](documentation-clause.md) |
| B11 | [Migration from older systems](../../../../install/migration/README.md) for the technical background |

## Part C: the contract terms

| Section | Template |
|---|---|
| C1 | [Open source license and source availability](open-source-license-and-source-availability-clause.md), [Intellectual property of custom work](intellectual-property-of-custom-work-clause.md) |
| C2 | [Service levels](service-levels-clause.md), [Support and maintenance](support-and-maintenance-clause.md) |
| C3 | [Acceptance testing](acceptance-testing-clause.md) |
| C4 | [Security](security-clause.md), [Vulnerability disclosure and patching](vulnerability-disclosure-and-patching-clause.md) |
| C5 | [Exit and data return](exit-and-data-return-clause.md), [Hosting and data location](hosting-and-data-location-clause.md) |
| C6 | [Warranty of no lock-in](warranty-of-no-lock-in-clause.md) |
| privacy annex | [Privacy and data processing](privacy-and-data-processing-clause.md) |

## Part D: the response forms

A compliance matrix that bidders fill in keeps answers comparable:

```text
| Req. id | Requirement (short) | Met: Yes / Partly / No / Planned | Evidence (document, section, setting, test) | Gap and plan |
|---------|---------------------|----------------------------------|---------------------------------------------|--------------|
| ACC-02  | Admin WCAG 2.2 AA   |                                  |                                             |              |
```

Ask for one-off and recurring costs separately (build, migration, hosting per year, support per year, training,
upgrades), with no license fee line for GPL software unless the bidder adds proprietary components.

## Before you publish

- Every "must" has a verification method ([How to write verifiable requirements](../requirements/how-to-write-verifiable-requirements.md)).
- No requirement names a product or brand unless your rules allow it.
- No "must" asks for evidence that the market cannot provide
  ([What Exponential can and cannot evidence](../requirements/what-exponential-can-and-cannot-evidence.md) shows typical gaps).
- Legal counsel has reviewed Part C.

## Related pages
- [Tender templates](README.md)
- [Requirements for a public-sector CMS](../requirements/README.md)
- [Procurement](../README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
