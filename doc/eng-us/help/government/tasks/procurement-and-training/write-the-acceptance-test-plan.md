---
title: Write the acceptance test plan
audience: government, project-manager, admin, editor
category: public-sector
order: 230
summary: Step by step, write the plan a public body uses to accept a delivered Exponential site - one test per requirement, functional, accessibility, security, privacy, performance, operations and documentation tests, pass criteria and how failures are handled.
keywords: acceptance test plan, acceptance testing, user acceptance, requirements, test cases, accessibility test, security test, performance, sign-off, procurement task
type: help
---
# Write the acceptance test plan

**Who:** the project manager with the evaluation team's specialists. **When:** before signing, so the contract can refer
to it; refine it during the build. Clause: [Acceptance testing clause](../../procurement/tender-templates/acceptance-testing-clause.md).

> **Note** This page describes common practice; it is not legal advice. Have the acceptance terms reviewed by your
> legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Test areas](#test-areas)
4. [Result](#result)
5. [Pitfalls](#pitfalls)

## Before you start

- The requirements, each with an identifier ([How to write verifiable requirements](../../procurement/requirements/how-to-write-verifiable-requirements.md)).
- A staging environment that matches production.

## Steps

1. **One test per requirement**, with the requirement's identifier, steps, test data and expected result.
2. **Name who tests**: editors test editing, the accessibility lead tests accessibility, and so on.
3. **Set pass criteria** and severity levels for failures; decide which severities block acceptance.
4. **Define the process**: test period, how failures are reported, fix and retest cycles, final sign-off.
5. **Use invented test data**, never personal data from production.
6. **Record results** per test with date, tester and evidence.

## Test areas

| Area | Example tests | Help |
|---|---|---|
| Functional | create, approve and publish each content type; forms; search; languages | [Editorial quality checklist](../../checklists/editorial-quality.md) |
| Accessibility | the agreed evaluation of templates and key pages; keyboard-only tasks | [Accessibility checklist](../../checklists/accessibility-checklist.md) |
| Security | hardening baseline, administrator 2FA, headers, audit chain verified | [Security checklist](../../checklists/security-checklist.md) |
| Privacy | consent texts, third parties as approved, retention settings | [Privacy checklist](../../checklists/privacy-checklist.md) |
| Performance | key pages under the agreed load, measured with `exp:benchmark` | [Benchmarking](../../../../guides/benchmarking.md) |
| Operations | backup and restore on staging, a routine update, cronjobs, monitoring | [Backup readiness](../../checklists/backup-readiness.md), [Upgrade readiness](../../checklists/upgrade-readiness.md) |
| Upgradability | **Setup > Upgrade check** shows no changed kernel files | [The upgrade check](../../../../guides/upgrade-check.md) |
| Documentation and handover | documents checked against the site; administrator accounts for your staff | [Hand over documentation at contract end](hand-over-documentation-at-contract-end.md) |
| Exit | an export drill on staging | [Run an exit drill](run-an-exit-drill.md) |

## Result

A plan both parties agreed, which turns "done" into a list of passed tests.

## Pitfalls

- Testing only what the demo showed.
- No pass criteria: then acceptance becomes a negotiation.
- Leaving accessibility and operations tests until after go-live.

## Related pages

- [Procurement and training tasks](README.md)
- [Go-live checklist for a public site](../../checklists/go-live-checklist.md)
- [Mapping requirements to evidence](../../procurement/requirements/mapping-requirements-to-evidence.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
