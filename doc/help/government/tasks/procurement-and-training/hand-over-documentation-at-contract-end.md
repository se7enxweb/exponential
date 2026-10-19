---
title: Hand over documentation at contract end
audience: government, project-manager, admin, partner
category: public-sector
order: 220
summary: Step by step, collect and check the documentation an outgoing supplier hands over when a contract for an Exponential site ends - content model, roles, settings, code, deployment, operations, open issues - so a successor or your own staff can take over.
keywords: documentation handover, contract end, supplier change, content model, roles, settings, deployment, runbooks, knowledge transfer, procurement task
type: help
---
# Hand over documentation at contract end

**Who:** the contract manager, with the site administrator who will check each item. **When:** during the handover
period at the end of a contract. Background: [Documentation handover](../../procurement/exit-plans/documentation-handover.md),
[The end of a contract](../../procurement/lifecycle/end-of-a-contract.md).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Handover list](#handover-list)
4. [Result](#result)
5. [Pitfalls](#pitfalls)

## Before you start

- The exit plan and the documentation clause of the contract ([Documentation clause](../../procurement/tender-templates/documentation-clause.md)).
- Named people on your side to receive and check each part.

## Steps

1. **Send the handover list** below to the supplier at the start of the handover period.
2. **Receive each item** into a place your organization controls (repository, document system), never only as e-mail
   attachments.
3. **Check each item against the live site**: does the content model description match **Setup > Classes**? Do the roles
   match **Roles** (`/role/list`)? Does the deployment description work on staging?
4. **Walk through the operations** with the supplier: one routine update and one restore on staging, using only the
   handed-over documentation.
5. **Record gaps** and give the supplier a date to close them.
6. **Sign off** when the checks pass; then close the supplier's access ([Offboarding a contractor](../../checklists/offboarding-a-contractor.md)).

## Handover list

| Item | Check against |
|---|---|
| Content model: classes, fields, purpose | **Setup > Classes** ([Class groups](../../../../guides/class-groups.md)) |
| Sections, roles, policies, user groups | **Sections**, **Roles** ([Roles and policies](../../../../guides/roles-and-policies.md)) |
| Workflows, triggers, object states | **Setup > Workflows**, **Triggers**, **States** |
| Siteaccesses, designs, settings changed from defaults | the settings page ([The settings page](../../../../guides/settings-page.md)) |
| Custom extensions and designs, in your repository | **Setup > Upgrade check** for changed kernel files |
| Deployment procedure and environments | a deployment to staging ([Deploying](../../../../guides/deploying.md)) |
| Cronjobs and their schedule | **Setup > Cronjobs** ([Cronjobs](../../../../guides/cronjobs.md)) |
| Backup and restore procedure | a restore test ([Backup readiness](../../checklists/backup-readiness.md)) |
| Third-party services and their accounts | the active extensions list |
| Open issues, known risks, planned work | the ticket system |

## Result

Documentation your organization holds and has tested, enough for a successor to take over.

## Pitfalls

- Accepting documents without checking them against the site.
- Documentation stored only in the supplier's tools.
- Closing the supplier's access before the walk-through.

## Related pages

- [Procurement and training tasks](README.md)
- [Write an exit plan](write-an-exit-plan.md)
- [Exit readiness](../../checklists/exit-readiness.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
