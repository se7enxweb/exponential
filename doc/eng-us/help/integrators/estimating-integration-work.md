---
title: Estimating integration work
audience: integrator, project-manager, partner
category: integration
order: 440
summary: How to estimate an integration with Exponential - breaking it into flows and work items, the factors that drive effort (surface, volume, data quality, the other system's API, identity, testing, operations), a worksheet with assumptions, and the items most often forgotten.
keywords: estimate, estimating, effort, scoping, work breakdown, flows, complexity, assumptions, contingency, data quality, API documentation, hypercare, worksheet, fixed price, time and material
type: help
---
# Estimating integration work

This page is for integrators and account managers who quote integration projects. It gives a way to break the work
down and the factors that move the number. It contains no day rates or reference effort: those depend on your team
and market. The general guide to estimating Exponential projects is
[Estimating and scoping with extensions](../leadership/estimating-and-scoping-with-extensions.md).

## Contents

1. [Estimate per flow](#estimate-per-flow)
2. [Factors that drive effort](#factors-that-drive-effort)
3. [Work that is often forgotten](#work-that-is-often-forgotten)
4. [An estimate worksheet](#an-estimate-worksheet)
5. [Fixed price or time and material](#fixed-price-or-time-and-material)

## Estimate per flow

A **flow** is one kind of data moving in one direction between two systems ("ERP products into Exponential",
"form leads to the CRM"). Estimate each flow with the same work items:

| Work item | Contains |
|---|---|
| Analysis and specification | Mapping, keys, removal policy, errors, monitoring; the interface specification |
| Content model | Classes, attributes, sections, roles (when the flow brings new content) |
| Client of the other system | Authentication, calls, paging, error handling, a test double |
| Transformation | Mapping code, rich text conversion, media handling |
| Exponential side | Import or sender, hooks, cronjob parts, commands, tables |
| Tests | Unit and contract tests, dry runs, the idempotency test, end-to-end |
| Operations | Monitoring check, log lines, runbook section, alert setup |
| Go-live | Initial load, rehearsal, cut-over |

Add the shared items once per project: the extension skeleton, environments, service accounts, CI, the security review,
handover and hypercare.

## Factors that drive effort

| Factor | Lower effort | Higher effort |
|---|---|---|
| Surface | A shipped surface fits (RSS export, `xrowextract` schedule, REST reads) | A new surface (a search engine plugin, an SSO handler behind a proxy) |
| Direction | One way | Both ways, with conflicts to resolve |
| Timing | Nightly batch | Near real time with an outbox and retries |
| Volume | Hundreds of records | Hundreds of thousands, with performance work |
| Data quality | Clean, validated source | Free text, duplicates, missing keys |
| The other system's API | Documented, with a test instance and a contact | Undocumented, production only, slow answers |
| Content | Plain fields | Rich text with embedded elements, relations, media, several languages |
| Identity | None | LDAP with group mapping, SSO, provisioning and de-provisioning |
| Compliance | No personal data | Personal data, payment, audit requirements |
| Environments | Development, staging and production exist | They must be built |

Rate each factor per flow (low, medium, high) and let the ratings, not intuition, decide the contingency.

## Work that is often forgotten

- [ ] The removal policy and its implementation.
- [ ] The safety stop for truncated sources.
- [ ] Re-runs and the idempotency test.
- [ ] Rich text conversion and the report of unconvertible markup.
- [ ] Redirects for migrated content.
- [ ] Monitoring and alerts; the runbook.
- [ ] Key rotation procedure and its first rotation.
- [ ] Waiting for the other system's team (access, answers, fixes).
- [ ] Hypercare after go-live.
- [ ] Handover and operator training.
- [ ] The first upgrade after go-live, if the contract covers maintenance.

## An estimate worksheet

```text
Project: <customer> integrations                    Version: 1     Date: <date>     Author: <name>

Flow                         Surface              Dir   Timing    Volume   Quality  API   Content  Rating
1 ERP products -> site       cronjob + PHP API    in    nightly   20 000   medium   good  plain    medium
2 Form leads -> CRM          cronjob + CRM API    out   5 min     50/day   good     good  plain    low
3 Staff sign-in              LDAP login handler   in    on login  300      -        -     -        low

Work item (per flow)         Flow 1   Flow 2   Flow 3
Analysis and specification
Content model
Client of the other system
Transformation
Exponential side
Tests
Operations
Go-live
Subtotal

Shared items: extension skeleton, environments, service accounts, CI, security review, handover, hypercare
Contingency (from ratings):
Total:

Assumptions (each one a line in the statement of work):
- The customer provides test instances of the ERP and the CRM by <date>.
- The ERP export file format is as in the sample of <date>; changes are change requests.
- ...
```

## Fixed price or time and material

| Fixed price works when | Time and material is fairer when |
|---|---|
| The flows are specified and the other systems are known | The other system's API is unknown or still changing |
| Data quality was measured on real data | Data quality is unknown |
| The customer delivers access and answers on time (written down) | Several third parties are involved |

A common split: discovery and specification at time and material, the build at a fixed price based on the
specification, hypercare and operations as a service. Whatever the model, write the assumptions into the statement of
work ([Statements of work for integrations](statements-of-work-for-integrations.md)).

## Related pages

- [Estimating and scoping with extensions](../leadership/estimating-and-scoping-with-extensions.md)
- [Integration discovery worksheet](integration-discovery-worksheet.md)
- [Integration project playbook](integration-project-playbook.md)
- [Pre-sales support for partners](../partners/pre-sales-support.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
