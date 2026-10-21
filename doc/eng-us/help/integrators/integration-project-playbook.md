---
title: Integration project playbook
audience: integrator, project-manager, partner
category: integration
order: 430
summary: A phase-by-phase playbook for integration projects around Exponential - discovery, interface design, environments and access, build, test, go-live, hypercare and handover - with deliverables, the roles involved on both sides, and the risks to manage.
keywords: project, playbook, integration project, phases, discovery, interface specification, environments, build, test, go-live, hypercare, handover, roles, RACI, risks, project management
type: help
---
# Integration project playbook

This page is for project leads at integration companies and for customers' project managers. It adapts the
[implementation project playbook](../partners/implementation-project-playbook.md) of the partner section to projects
whose main subject is the connection between systems. It is a playbook, not a method you must follow: adapt it to
your contracts.

## Contents

1. [The phases](#the-phases)
2. [Roles on both sides](#roles-on-both-sides)
3. [Risks to manage](#risks-to-manage)
4. [Rules of thumb](#rules-of-thumb)

## The phases

| Phase | Deliverables | Pages |
|---|---|---|
| 1. Discovery | Systems, data flows, directions, volumes, timing, owners, constraints; the discovery worksheet filled | [Discovery worksheet](integration-discovery-worksheet.md), [What integrators do](what-integrators-do-with-exponential.md) |
| 2. Interface design | One interface specification per flow: surface, credential, mapping, keys, errors, monitoring | [Interface specification template](interface-specification-template.md), [Choosing an integration surface](choosing-an-integration-surface.md) |
| 3. Estimate and agreement | Estimate with assumptions, statement of work, acceptance criteria | [Estimating](estimating-integration-work.md), [Statements of work](statements-of-work-for-integrations.md) |
| 4. Environments and access | Development and staging installations from Git; test instances of the other systems; service accounts; credentials stored | [Develop with Git](../develop-with-git.md), [Service accounts](service-accounts-roles-and-policies.md) |
| 5. Build | The integration extension: mapping, client, import or sender, hooks, cronjob parts, tests | [Building an integration as an extension](building-an-integration-as-an-extension.md), [Writing an import](writing-an-import-script.md) |
| 6. Test | Unit and contract tests in CI, dry runs on a copy, the idempotency test, end-to-end in staging, security review | [Testing](testing-integrations.md), [Security checklist](integration-security-checklist.md) |
| 7. Go-live | Initial load, schedules on, monitoring on, rollback plan ready | [Go-live checklist](integration-go-live-checklist.md) |
| 8. Hypercare | Two to four weeks of close watching; fixes; tuning of thresholds | [Monitoring](monitoring-integrations.md) |
| 9. Handover | Runbook, credentials inventory, documentation, training of operators | [Handover of an integration](handover-of-an-integration.md) |
| 10. Operations | Upgrades, key rotation, reconciliation, changes of the other systems | [Upgrades](upgrades-without-breaking-integrations.md) |

## Roles on both sides

| Role | Integrator | Customer |
|---|---|---|
| Project lead | Plans, reports, manages scope | Decides priorities, signs acceptance |
| Integration architect | Designs flows and surfaces | Provides the landscape and constraints |
| Integration developer | Builds and tests the extension | |
| Owner of each other system | | Provides test instances, credentials, API documentation, a contact who answers within a day |
| Data owner | | Decides master data, mapping and removal policies |
| Operations | Prepares runbook and monitoring | Runs it after handover (or contracts it) |
| Security and data protection | Follows the security checklist | Approves data flows and processors |

The most common cause of delay is not code: it is waiting for access to the other system's test instance, its
credentials or an answer about its data. Put those dates into the plan as customer deliverables.

## Risks to manage

| Risk | Mitigation |
|---|---|
| The other system's API is undocumented or changes | Contract tests with recorded answers; a named contact; a change notice clause in the agreement |
| Data quality at the source is worse than assumed | Dry run on real data in discovery; a budget line for cleansing rules; a report of rejected records |
| Volumes are larger than assumed | Measure in discovery; design batch and delta from the start |
| No test instance of the other system | A test double for development; a short supervised window on production with read-only credentials |
| Editors change imported fields | Ownership of each field in the mapping; roles that prevent it; training |
| Key person dependency | Everything in the extension's repository and documentation; pairing |
| Silent failure after go-live | Monitoring with alerts before go-live, not after the first incident |

## Rules of thumb

- Build the thinnest end-to-end flow first (one record, one field, all the way), then widen it.
- Agree the removal policy before the first import; it is the decision most often forgotten.
- Never go live on a Friday or before a holiday.
- Plan hypercare in the budget; it is when the thresholds are tuned.
- Keep the customer's own team involved; they will run it.

## Related pages

- [Implementation project playbook](../partners/implementation-project-playbook.md)
- [Planning an Exponential project](../leadership/planning-an-exponential-project.md)
- [Project templates](../leadership/project-templates.md)
- [Risk management](../leadership/risk-management.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
