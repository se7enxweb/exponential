---
title: "Lesson 5.2: Approving and denying"
audience: trainer, editor
category: training
order: 52
summary: Learners act as approvers in the Collaboration inbox, preview a waiting version, approve or deny it with a comment, and act as authors on a denied version. 45 minutes.
keywords: editors 201, approve, deny, approver, collaboration, approval, comment, add comment, preview, waiting for approval, denied, author
type: help
---
# Lesson 5.2: Approving and denying

Approvers decide in the **Collaboration** inbox. This lesson is played in pairs: one learner sends, the other
approves or denies with a comment, and then they swap. Both sides learn what the other sees.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Module | [Module 5: Approvals and collaboration](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors and approvers (chief editors, reviewers) |
| Prerequisites | [Lesson 5.1](when-publishing-needs-approval.md); author and approver training accounts |

## Learning objectives

After this lesson, learners can:

- open the **Collaboration** inbox and an approval in it;
- preview the waiting version and read its messages and participants;
- **Approve** or **Deny** with a comment (**Add Comment**);
- as an author, continue after a denial: edit the draft and send it again.

## Trainer's notes

### How to present it

Source: [Workflows: Try it](../../../../guides/workflows.md#35-try-it).

1. **The inbox** (3 minutes). Approvers open **Collaboration** (on the dashboard's left column of the administration,
   where their role has it). It lists approval requests; an empty inbox says *Nothing to handle yet*.
2. **An approval page** (5 minutes). Title **Approval**, and *The content object ... needs your approval before it
   can be published.* The page shows the version (*Version n of the content, saved ... by ...*), **Preview**, **Open
   the content**, the **Participants**, **Messages** with **Comment** and **Add Comment**, and the question *Do you
   approve of the content object being published?* with **Approve** and **Deny**. Only an approver can decide.
3. **After the decision** (4 minutes). Approved: *... was approved and will be published when the publishing workflow
   continues.* Denied: *... was not accepted but will be available as a draft for the author.* The author can edit the
   draft and publish it again; a new approval is then required.
4. **Comments** (3 minutes). Teach approvers to deny with a reason. A comment before **Deny** tells the author what to
   change. When an approval is closed, comments can no longer be added.

### Common questions

**Where does the author see the denial?** The version is a draft again: under **My drafts**, and the approval with its
messages stays readable from **My pending items** or the collaboration inbox while it exists.

**Can there be several approvers?** Yes; the workflow names users or groups. One decision is enough.

**Do approvers get an e-mail?** Where notifications for approvals are switched on (**My notification settings** >
**Collaboration notification**, **Approval**), and the site sends mail.

### Pitfalls

- **Approvers who are also authors.** Their own content passes without approval; use separate accounts.
- **The workflow cronjob.** After **Approve**, publishing happens when the cronjob runs. On a training installation,
  run the frequent part yourself or wait a few minutes.
- **Denying without a comment.** Authors cannot guess what to change.

## Hands-on exercise

Work in pairs with an author account and an approver account (the trainer prepared both).

1. Author: create `Approval test` in your course folder and press **Send for publishing**.
2. Approver: open **Collaboration**. Find the request and open it.
3. Approver: press **Preview** and read the version. Then type a comment such as `Please add a second sentence to the
   intro.` and press **Add Comment**.
4. Approver: press **Deny**. Read the message.
5. Author: open **My drafts** and find `Approval test`. Open the approval from **My pending items** if it is still
   listed, and read the comment.
6. Author: edit the draft, add the sentence, and press **Send for publishing** again.
7. Approver: open the new request, preview it, and press **Approve**.
8. Both: wait for the workflow cronjob (the trainer runs it or says how long), then check that `Approval test` is
   published.
9. Swap roles and repeat steps 1 to 8 with a second article.

## Expected result

- The first version was denied with a comment and came back to the author as a draft.
- The second version was approved and is published after the cronjob ran.
- Both learners saw the approval page from both sides.
- The trainer checks by opening one approval page with its messages on the projector.

## Quiz

1. Where do approvers find the requests waiting for them?
2. What happens to a denied version?
   A. It is deleted B. It goes to the trash C. It becomes a draft for the author D. It is published anyway
3. Why should an approver write a comment before denying?
4. True or false: after **Approve**, the version is published in the same second.
5. The author changes a denied draft and sends it again. Is another approval needed?

<details>
<summary>Answers</summary>

1. In the **Collaboration** inbox ([Workflows](../../../../guides/workflows.md#35-try-it)).
2. **C.**
3. So the author knows what to change.
4. **False.** It is published when the workflow cronjob carries on.
5. Yes: a new approval is required.

</details>

## Further reading

- [Workflows](../../../../guides/workflows.md#3-worked-example-approval-before-publishing): setting up an approval,
  for the trainer.
- [My drafts and my pending items](../../../../guides/drafts-and-pending.md): both sides of an approval.
- [Your account and signing in](../../../accounts-and-sign-in.md#e-mail-and-notifications): notifications.

## Related pages

- [Module 5: Approvals and collaboration](README.md)
- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
