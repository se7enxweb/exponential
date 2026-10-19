---
title: "Lesson 10.1: Workflows and triggers for developers"
audience: trainer, developer
category: training
order: 101
summary: Learners connect a workflow to the publish operation with a trigger, follow a workflow process through its states, and read how the kernel holds an operation back and the workflow cronjob resumes it. 45 minutes.
keywords: developers course, workflow, event, trigger, content/publish before, workflow process, memento, workflow cronjob, workflow.php, frequent part, Approve, Multiplexer, Setup Workflows, Setup Triggers, Workflow processes
type: help
---
# Lesson 10.1: Workflows and triggers for developers

Workflows let code run around kernel operations: before content is published, after a user registers, before an
order is confirmed. Before learners write an event type of their own (lesson 10.2), they need the model: workflows,
events, triggers and the processes that wait. This lesson builds a small workflow in the administration and follows
it through the kernel.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 10: Workflows, events and cronjobs](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Module 9](../module-09-the-console-and-persistent-objects/README.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- explain workflow, event, trigger, workflow process and memento;
- create a workflow and connect it to `content/publish` before with **Setup** > **Triggers**;
- explain the difference between a *before* and an *after* trigger;
- describe how a waiting process is resumed by the workflow cronjob (`workflow.php` in the *frequent* part);
- find waiting processes on **Setup** > **Workflow processes**.

## Trainer's notes

### How to present it

Source: [Workflows: approvals, triggers and the processes that wait](../../../../guides/workflows.md).

1. **The model** (5 minutes). Project the table of section 1 of the guide: a workflow is a list of events; a trigger
   connects a workflow to an operation (`content/publish`, before or after); a process is one run for one object and
   version; the memento is what the kernel saved of the held-back operation.
2. **The flow** (5 minutes). **Send for publishing** starts `content/publish`; the before trigger starts a process;
   an event that cannot finish answers "run me again later" and the version waits as pending; the workflow cronjob
   runs waiting processes again and finishes the operation.
3. **Before or after** (2 minutes). *Before* holds the operation until the workflow is done; *after* runs once the
   operation happened, for notifications and follow-up work.
4. **One workflow per trigger** (2 minutes). Several steps go into one workflow; the *Multiplexer* event runs other
   workflows by section or class.
5. **The cronjob** (3 minutes). Without `workflow.php` running, approved content is never published. The process
   list shows whether it is scheduled.

### Common questions

**Which operations have triggers?** **Setup** > **Triggers** lists every operation that can start a workflow, in
words, with the module and the connection type.

**Can a workflow run for one class only?** The *Multiplexer* event chooses a workflow by section, class or user;
an event of your own can check the class itself (lesson 10.2).

**Where does the waiting version show for the editor?** As pending, under **My pending items**.

### Pitfalls

- **A before-publish workflow on a shared training installation.** Every learner's publishing goes through it. Use
  separate installations or remove the trigger at the end.
- **No workflow cronjob.** Processes wait for ever; check the status bar of the process list.
- **Cancelling processes.** **Cancel...** turns a pending version back into a draft; it deletes no content, but tell
  the editor.

## Hands-on exercise

On the training installation, signed in to the administration as an administrator.

1. Open **Setup** > **Workflows**, open a workflow group (or create one with **New workflow group**).
2. Press **New workflow**, name it `Course: wait until date`.
3. Choose **Event / Wait until date** in the list of events and press **Add event**; press **OK** to store the
   workflow without further settings.
4. Open **Setup** > **Triggers**. Find the row for publishing content, before (`content/publish`), and note which
   workflow it runs now. Choose your workflow and apply the change.
5. Publish an article in your course folder. Note whether it is published at once.
6. Open **Setup** > **Workflow processes** and look for a process; read the status bar about the workflow cronjob.
7. Run the workflow cronjob once from the shell: `php runcronjobs.php -s <sa> --script=workflow.php`.
8. Set the trigger back to the workflow it ran before step 4 (or none) and apply.
9. Read the code that ends a process step: `grep -n "STATUS_" kernel/classes/ezworkflowtype.php | head -12`.

## Expected result

- Step 5: the article is published at once, because the *Wait until date* event without a date attribute set lets
  it through; with dates configured it would wait.
- Step 6: an empty process list is normal when nothing waits; the status bar names the frequent part.
- Step 7 runs `workflow.php` and ends without errors.
- Step 9 lists the status constants such as `STATUS_ACCEPTED`, `STATUS_REJECTED` and `STATUS_DEFERRED_TO_CRON`.
- The trainer checks that every learner set the trigger back.

## Quiz

1. What connects a workflow to an operation?
2. A workflow must decide before content is visible. Which connection type?
   A. after B. before C. either D. none, use a cronjob
3. What is a memento?
4. What happens to approved content when the workflow cronjob does not run?
5. Where do you see processes that wait?

<details>
<summary>Answers</summary>

1. A trigger.
2. **B.** Before: the operation is held back until the workflow is done.
3. What the kernel saved of the held-back operation (module, operation, trigger and parameters) so it can carry it
   out later.
4. It is never published; the processes wait.
5. **Setup** > **Workflow processes** (`/workflow/processlist`).

</details>

## Further reading

- [Workflows: approvals, triggers and the processes that wait](../../../../guides/workflows.md).
- [Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md).
- [My drafts and my pending items](../../../../guides/drafts-and-pending.md).

## Related pages

- [Module 10: Workflows, events and cronjobs](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
