---
title: "Tender template: acceptance testing clause"
audience: government, project-manager, partner, admin
category: public-sector
order: 150
summary: Clause text for acceptance of an Exponential site - test plan, environments, functional, accessibility, security, performance, language, backup and exit tests, defect classes and the acceptance decision - with the product checks testers can use.
keywords: acceptance testing, acceptance clause, test plan, UAT, defects, go-live, accessibility test, security test, restore test, exit test, performance, tender template
type: help
---
# Tender template: acceptance testing clause

This page is for project managers and procurement officers who decide when a delivered site is accepted and payment
milestones are due. The clause defines what is tested, by whom and how defects block acceptance. It links the
product's own checks so that testers know what to look at.

**Template** - adapt to your procurement rules; not legal advice

## Contents
1. [The clause](#the-clause)
2. [The test areas](#the-test-areas)
3. [Defect classes](#defect-classes)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## The clause

```text
A. ACCEPTANCE

A.1 Test plan. The Supplier delivers a test plan <period> before acceptance
    testing, covering every requirement in <annex> and the test areas below. The
    Customer approves the plan.

A.2 Environment. Acceptance testing takes place on <staging environment>
    identical in software versions and configuration to production.

A.3 Execution. The Supplier runs the tests and records results; the Customer
    witnesses and may run any test itself. Results and evidence are delivered in
    <format>.

A.4 Defects. Defects are classified as in <defect classes>. Acceptance requires
    zero open defects of class 1 and 2, and an agreed plan for class 3.

A.5 Decision. The Customer accepts or rejects in writing within <period> after
    the test report. A rejection names the defects. Retesting follows the same
    rules.

A.6 Go-live check. Before go-live the Supplier completes the go-live checklist
    in <annex>.

A.7 Warranty period. Acceptance does not end the Supplier's obligation to
    correct defects found within <period>.
```

## The test areas

| Area | What to test | Product checks and pages |
|---|---|---|
| Functions | every functional requirement, by role | [Roles and policies](../../../../guides/roles-and-policies.md), [Workflows](../../../../guides/workflows.md) |
| Accessibility | WCAG-EM evaluation of the public pages in scope; keyboard and screen reader use of the editor | [Accessibility clause](accessibility-clause.md), [Testing accessibility](../../../creators/website-creators/testing-accessibility.md) |
| Security configuration | every setting in the secure configuration annex; headers; HTTPS | [Security clause](security-clause.md), [13.16 Go-live checklist](../../../../install/13-security-hardening.md#1316-go-live-checklist) |
| System health | **Setup > System information** health checks without errors | [System information](../../../../guides/system-information.md) |
| File integrity | **Setup > Upgrade check** lists differences from the release | [The upgrade check](../../../../guides/upgrade-check.md) |
| Content integrity | broken relations, missing files | [Content checks](../../../../features/6.0/content-checks.md), [Missing files](../../../../guides/missing-files.md) |
| Languages | every language and direction | [Multilingual clause](multilingual-clause.md) |
| Performance | page times under the expected load | [Benchmarking](../../../../guides/benchmarking.md) |
| Audit | sign-in, publish and remove appear in the audit trail; `./console exp:audit verify` passes | [Audit trail](../../../../features/6.0/audit-trail.md) |
| Backup and restore | a full restore on a separate environment | [Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md) |
| Exit | a full export, checked against counts | [Exit and data return clause](exit-and-data-return-clause.md) |
| Documentation | a third party follows the installation documentation | [Documentation clause](documentation-clause.md) |

A launch list for site builders is in [Launch checklist](../../../creators/website-creators/launch-checklist.md); a
host's version in [New customer site go-live](../../../hosting/checklists/new-customer-site-go-live.md).

## Defect classes

| Class | Meaning (adapt) | Blocks acceptance |
|---|---|---|
| 1 | Security weakness, data loss, legal non-compliance, site unusable | yes |
| 2 | A "must" requirement not met; an accessibility failure on a page in scope | yes |
| 3 | A "should" not met; an error with a workaround | no, with a plan |
| 4 | Cosmetic | no |

## Notes for the evaluator

- Ask bidders for a sample test plan; score its coverage of accessibility, restore and exit, which are often missing.
- Testing with real content (or a realistic sample) finds problems that demo content hides.

## Related pages
- [Tender templates](README.md)
- [How to write verifiable requirements](../requirements/how-to-write-verifiable-requirements.md)
- [Service levels clause](service-levels-clause.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
