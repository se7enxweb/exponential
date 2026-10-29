---
title: "Module 5: Approvals and collaboration"
audience: trainer, editor
category: training
order: 50
summary: The fifth module of Editors 201. Learners see a version wait for approval, approve and deny in the Collaboration inbox in pairs, and handle edit conflicts and handovers as a team. 2 hours 10 minutes.
keywords: editors 201, module 5, approval, workflow, pending, collaboration, approve, deny, edit conflict, team, notifications
type: help
---
# Module 5: Approvals and collaboration

Editorial teams share content and responsibility. This module covers the approval workflow from both sides and the
habits that keep a team from overwriting each other's work. Most exercises are done in pairs.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Duration | 2 hours 10 minutes (with a 10-minute break) |
| Lessons | 3 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 5.1: When publishing needs approval](when-publishing-needs-approval.md) | 30 minutes | Send an article into approval and read it on **My pending items** |
| [Lesson 5.2: Approving and denying](approving-and-denying.md) | 45 minutes | Deny with a comment, correct, approve; swap roles |
| [Lesson 5.3: Working as a team](working-as-a-team.md) | 45 minutes | Resolve an edit conflict, follow a colleague's folder, write team rules |

## Preparing this module

- An approval workflow on the training installation that covers the learners' author accounts, connected to
  "before publishing content", with approver accounts that are not authors. Two ways:
  - follow [the worked example](../../../../guides/workflows.md#3-worked-example-approval-before-publishing); or
  - create the sample approvals and remove them after the class:

    ```bash
    ./console exp:collaborationsampledata --dry-run --allow-root-user
    ./console exp:collaborationsampledata --apply --allow-root-user
    ./console exp:collaborationsampledata --apply --remove --allow-root-user
    ```

- The workflow cronjob must run (check **Setup** > **Workflow processes**: its status bar says when it runs next), or
  be ready to run the frequent part yourself after each approval.
- Approver accounts need access to **Collaboration** in the siteaccess they use; check by signing in with one.
- Pairs of accounts for lesson 5.3 and articles in the learners' course folders.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 5.1 | 30 |
| Lesson 5.2 | 45 |
| Break | 10 |
| Lesson 5.3 | 45 |
| **Total** | **130** |

## Related pages

- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
