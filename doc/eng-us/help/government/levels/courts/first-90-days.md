---
title: The first 90 days for courts
audience: government, project-manager, admin
category: public-sector
order: 60
summary: A 90-day plan for a court or tribunal starting with Exponential - the decision publication policy first, then model, sections, review and security, then guidance migration and launch - with checks.
keywords: project plan, 90 days, court, tribunal, decision publication policy, anonymization, review, security, launch, checks
type: help
---
# The first 90 days for courts

This page is for project managers and administrators who start a court or tribunal site on Exponential. Nothing about
decisions is built before the publication policy is agreed. It assumes an installation exists
([Getting started](../../../../guides/getting-started.md)).

## Contents

1. [Days 1 to 30: policy and inventory](#days-1-to-30-policy-and-inventory)
2. [Days 31 to 60: build and secure](#days-31-to-60-build-and-secure)
3. [Days 61 to 90: guidance, rehearsal, launch](#days-61-to-90-guidance-rehearsal-launch)
4. [Risks](#risks)

## Days 1 to 30: policy and inventory

| Step | Output |
|---|---|
| Agree the decision publication policy with the judiciary, legal and data protection: what, when, how anonymized, for how long, who reviews | a written policy |
| Agree the withdrawal procedure: who decides, who acts, within what time | a written procedure ([Publishing decisions with care](publishing-decisions-with-care-for-personal-data.md)) |
| Inventory guidance, forms and rules; mark what is outdated | an inventory |
| Agree how hearing information and closures are published, and by whom | a rule and a duty list |

Check: the policy names the people with each role in the publishing flow.

## Days 31 to 60: build and secure

| Step | Check |
|---|---|
| Create Decision, Court location and Guide classes ([Content model](content-model.md)) | test objects publish |
| Create the state group `decision_status` | a test decision shows *Under review* |
| Create sections `decisions_review`, `decisions`, `notices` and the roles ([Structure](structure-sections-and-roles.md)) | logged out, `decisions_review` is closed |
| Set up the decision review workflow and confirm the workflow cronjob runs | a test decision waits in **Setup > Workflow processes** |
| Enforce two-step sign-in for the decision team and administrators | `Enforce2FA` tested |
| Turn on account lockout and review the hardening checklist | [Security hardening](../../../security-hardening.md#checklist) |
| Decide on a CSP for the site and test it report-only first | [Content Security Policy](../../../../features/6.0/content-security-policy.md) |

## Days 61 to 90: guidance, rehearsal, launch

| Step | Read |
|---|---|
| Rewrite top guidance in plain language | [Requirements emphasis](requirements-emphasis.md) |
| Rehearse a full decision cycle: prepare, review, publish, correct, withdraw | [Publishing decisions with care](publishing-decisions-with-care-for-personal-data.md) |
| Rehearse a closure notice by the duty team | the notices folder |
| Test accessibility of templates | [Testing accessibility](../../../creators/website-creators/testing-accessibility.md) |
| Launch | [Launch checklist](../../../creators/website-creators/launch-checklist.md) |

Checks at launch:

- [ ] The withdrawal rehearsal took less time than the procedure allows, including removing old versions.
- [ ] **Setup > Audit** shows the rehearsal's actions with the right users.
- [ ] No test decision remains visible.

## Risks

| Risk | Response |
|---|---|
| Personal data in a published decision | the withdrawal procedure; inform the data protection officer |
| Review becomes a bottleneck and decisions pile up | add reviewers; watch the oldest waiting process |
| Old versions keep personal data after a correction | include version removal in every correction for personal data |
| A decision team account is compromised | two-step sign-in, lockout, audit alerts |

## Related pages

- [Courts](README.md)
- [Publishing decisions with care for personal data](publishing-decisions-with-care-for-personal-data.md)
- [Launch checklist](../../../creators/website-creators/launch-checklist.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
