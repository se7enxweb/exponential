---
title: Data protection impact assessment for a website
audience: government, project-manager
category: public-sector
order: 290
summary: For public bodies deciding whether and how to assess the privacy risks of a site built with Exponential - when a DPIA is commonly considered, the facts the product supplies for the description and the risk analysis, and the measures available to reduce risks.
keywords: DPIA, data protection impact assessment, GDPR article 35, risk assessment, privacy risks, measures, description of processing, necessity, proportionality, public sector, website, member area, AI
type: help
---
# Data protection impact assessment for a website

This page is for data protection officers and project managers. A DPIA (GDPR art. 35) is required where processing is
likely to result in a high risk; your supervisory authority's lists say when. A plain information site often does not
need one; a site with citizen accounts, sensitive forms, profiling or AI features may. Exponential does not decide this
for you; it supplies facts and measures.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [When it is commonly considered](#when-it-is-commonly-considered)
2. [Describing the processing](#describing-the-processing)
3. [Risks and measures](#risks-and-measures)
4. [Related pages](#related-pages)

## When it is commonly considered

- Accounts for citizens with personal profiles, or member areas with sensitive topics.
- Forms collecting special categories of data (health, social situation) or data about children.
- Monitoring of staff through the audit trail beyond security purposes.
- Analytics or tracking across services.
- AI processing of content or images that contain personal data (the optional Exponential Intelligence extension sends
  content to providers under its data policy).

## Describing the processing

Use the pages of this section:

| DPIA part | Source |
|---|---|
| Data and where it is kept | "Where personal data lives in Exponential" |
| Retention | "Retention of personal data: an overview" |
| Recipients | "Controller, processor and the software", "Third-party extensions and privacy" |
| Data subjects' rights | "The right of access", "The right to erasure", "Restriction and Deactivate and move" |
| Technical measures | the security section of this help |

## Risks and measures

| Risk | Measures available in Exponential |
|---|---|
| Unauthorized access to accounts | two-step sign-in, password policy, lockout, network restriction of the administration |
| Excessive access by staff | roles limited by section or subtree; `infocollector/read`, `audit/read`, `mailpreferences/export` given to few |
| Data kept too long | audit, log and consent retention settings; removal routines for forms |
| Leaks through logs | masking in request logs and the log viewer |
| Data to third parties | extensions inactive by default; CSP restricting hosts; consent before loading |
| Undetected misuse | audit trail on by default, alert rules, SIEM forwarding |
| Mistaken publication | versions, preview, approval workflows, trash ([Workflows](../../../guides/workflows.md)) |

## Related pages

- [Mapping the GDPR to Exponential](gdpr-mapping.md)
- [Privacy by design checklist](privacy-by-design-checklist.md)
- [Privacy standards](../../../standards/privacy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
