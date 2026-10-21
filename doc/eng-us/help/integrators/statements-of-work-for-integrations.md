---
title: Statements of work for integrations
audience: integrator, project-manager, partner
category: integration
order: 450
summary: What a statement of work for an Exponential integration should contain - scope per flow, deliverables, customer obligations, assumptions, acceptance, change requests, ownership and licensing of the code, data protection, operations and maintenance - with a template outline.
keywords: statement of work, SOW, contract, scope, deliverables, assumptions, acceptance criteria, change request, customer obligations, ownership, intellectual property, GPL, data processing, warranty, maintenance, template
type: help
---
# Statements of work for integrations

This page is for integrators and their customers who write the statement of work (SOW) for an integration project.
Integrations depend on systems and people the integrator does not control, so a good SOW is precise about scope,
about what the customer delivers, and about what happens when the other system changes. This page is a practical
outline, **not legal advice**; have contracts reviewed by your own counsel.

## Contents

1. [The sections of a SOW](#the-sections-of-a-sow)
2. [Scope per flow](#scope-per-flow)
3. [Customer obligations and assumptions](#customer-obligations-and-assumptions)
4. [Ownership and licensing](#ownership-and-licensing)
5. [A template outline](#a-template-outline)

## The sections of a SOW

| Section | Purpose |
|---|---|
| Background and goals | Why the integration exists, in the customer's words |
| Scope | The flows, each with its interface specification as an annex |
| Out of scope | What is explicitly not included (for example: changes in the other systems, data cleansing at the source) |
| Deliverables | The integration extension, documentation, runbook, tests, training |
| Customer obligations | Access, test instances, credentials, contacts, answers, decisions, by dates |
| Assumptions | Each assumption of the estimate, so a broken one becomes a change request rather than a dispute |
| Timeline and milestones | Phases from the [playbook](integration-project-playbook.md), with dependencies on the customer |
| Acceptance | Acceptance cases per flow, the acceptance period, what counts as a defect |
| Change requests | How changes are requested, estimated and approved |
| Commercials | Price model, payment milestones, expenses |
| Ownership and licensing | Who owns which code; licenses of what is used |
| Data protection | Roles under data protection law, processors, data flows (not legal advice) |
| Hypercare, warranty, maintenance | What happens after go-live and for how long |

## Scope per flow

Describe each flow in the same way, and attach its interface specification:

| Item | Example |
|---|---|
| Name and direction | ERP products into Exponential |
| Trigger and timing | Nightly at 02:15; a delta every hour from 07:00 to 19:00 |
| Volume | Up to 25 000 products; up to 2 000 changes a day |
| Mapping | Annex A, mapping table of the class `product` |
| Keys | `erp-product-<ARTNR>` |
| Removals | Products missing from a full export are hidden |
| Errors | Invalid records skipped and reported; a run stops when the file has less than 80% of the products |
| Monitoring | Alerts as in Annex B |
| Acceptance cases | Annex C |

## Customer obligations and assumptions

Write them as dated obligations, not as hopes:

- [ ] Test instance of each other system available by `<date>`, with test data resembling production.
- [ ] Credentials for test and production provided through an agreed secure channel by `<date>`.
- [ ] A technical contact per other system who answers within one working day.
- [ ] Data owner decisions (mapping, removals, master data) within five working days of a question.
- [ ] Changes to the other systems' APIs announced at least `<n>` weeks ahead.
- [ ] The Exponential installation runs a supported version (for example 6.0.15) with the agreed extensions.

## Ownership and licensing

Agree explicitly:

- **Customer-specific code** (the mapping extension): usually owned by or exclusively licensed to the customer.
- **Generic connectors** the integrator brings or builds: often stay with the integrator, licensed to the customer; or
  are published as open source with the customer's consent ([Contributing integrations back](contributing-integrations-back.md)).
- **Exponential and its extensions** are GPL v2 (or any later version); the customer receives them under that license,
  and code distributed together with them must respect it ([Licensing for partners](../partners/licensing-for-partners.md)).
- **Credentials and data** belong to the customer and are handed over at the end ([Handover](handover-of-an-integration.md)).

## A template outline

```text
STATEMENT OF WORK  <project name>                       between <customer> and <integrator>     version <n>, <date>

1  Background and goals
2  Scope
   2.1 Flow 1: <name> (interface specification: Annex A1)
   2.2 Flow 2: <name> (Annex A2)
3  Out of scope
4  Deliverables
   - Extension <name> in repository <location>, release <version>
   - Documentation and runbook; tests; operator training (<hours>)
5  Customer obligations (dated)
6  Assumptions
7  Timeline and milestones
8  Acceptance (cases: Annex C; period: <days>; defect classes)
9  Change requests
10 Commercials
11 Ownership and licensing
12 Data protection (data flows: Annex D)
13 Hypercare (<weeks>), warranty (<period>), maintenance (separate agreement)
Annexes: A interface specifications, B monitoring plan, C acceptance cases, D data flows
Signatures
```

## Related pages

- [Estimating integration work](estimating-integration-work.md)
- [Interface specification template](interface-specification-template.md)
- [Project templates](../leadership/project-templates.md)
- [The partnership agreement](../partners/the-partnership-agreement.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
