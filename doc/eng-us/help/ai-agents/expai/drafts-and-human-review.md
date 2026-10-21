---
title: Drafts and human review
audience: ai-agents, editor, admin
category: ai-agents
order: 22
summary: Why Exponential Intelligence never publishes on its own - every result is a draft of the editor, "Send for approval" runs the normal publish operation so approval workflows hold the version, and every version it wrote carries the Exponential Intelligence label - plus how to set up an approval step for AI-written content.
keywords: drafts only, human review, human in the loop, Send for approval, Save as drafts, approval workflow, ezapprove, pending, label, Exponential Intelligence label, My drafts, publish
type: help
---
# Drafts and human review

This page is for editors who review what the assistant writes, and for administrators who want a person to approve
AI-written content before it goes public. It explains the one rule the assistant never breaks: it writes drafts, and
a person decides what is published.

## Contents

1. [Everything is a draft](#everything-is-a-draft)
2. [Save as drafts or Send for approval](#save-as-drafts-or-send-for-approval)
3. [Knowing beforehand what will happen](#knowing-beforehand-what-will-happen)
4. [The approval path](#the-approval-path)
5. [The label on AI-written versions](#the-label-on-ai-written-versions)
6. [Setting up an approval step for AI content](#setting-up-an-approval-step-for-ai-content)

## Everything is a draft

Everything the assistant writes is an ordinary draft of the editor:

- **a new object**: version 1 with status draft, owned by the editor, placed under the chosen parent, in the
  parent's section;
- **a change**: a new version with status draft; the published version stays as it is.

These drafts are in **My drafts**, open in the normal edit form and can be discarded there. Vector's staged changes,
chains, alt text reports and translation sync all end in drafts too. The extension has no setting that lets it
publish on its own ([expai FAQ](https://github.com/se7enxweb/expai/blob/main/doc/faq.md)).

## Save as drafts or Send for approval

| Button | What happens |
|---|---|
| **Save as drafts** | The drafts stay drafts. You open them later, edit and publish them as any other draft |
| **Send for approval** | Runs the regular publish operation **as you**, with its notifications and audit records. If an approval workflow applies, the version waits for the approver; if none applies, it is published |
| Discard | Removes the draft, as the kernel's own discard does |

Before sending, the assistant checks that the version is still your own draft, that you may edit it, and that every
required field has content.

Read the second row carefully: "Send for approval" **publishes at once** where no approval workflow applies. The name
describes the intended setup, not a guarantee. If you need a person to approve every AI-written version, set up an
approval workflow ([below](#setting-up-an-approval-step-for-ai-content)).

## Knowing beforehand what will happen

Each draft says in advance which of three cases applies, by evaluating the approve events of the publish trigger the
way the kernel does (section, excluded groups, approvers, language, version option):

| Shown as | Meaning |
|---|---|
| Waits for approval when sent | An approve event applies |
| May wait for approval when sent | An approve event sits behind a multiplexer, whose conditions are not evaluated |
| Published at once when sent | No approval applies (for example, you are an approver yourself) |

## The approval path

1. The editor sends the draft. The approve event creates a collaboration item for the approvers; the version is
   **pending**, and the panel and the dashboard show "Waiting for approval".
2. The approver opens the item. The page shows a note that the version was written with Exponential Intelligence,
   and the model.
3. **Approve**: the workflow cronjob continues the workflow and publishes the version. **Deny**: the version becomes
   the editor's draft again, to edit and send again.

This path was run end to end with real model calls in a copy of a live installation, as the extension's
[approval page](https://github.com/se7enxweb/expai/blob/main/doc/approval-workflow.md) records.

## The label on AI-written versions

Every version the assistant wrote carries the label **Exponential Intelligence**: in My drafts, the drafts page,
version views, edit forms and approval items. It is drawn from the assistant's own draft log, so it marks the version,
not the object: a later version written by hand has no label.

## Setting up an approval step for AI content

The approval is the kernel's own workflow event, not a feature of the assistant. A typical setup:

1. Create a workflow with the event **Approve** and choose the sections it covers and the approvers
   ([Workflows, worked example](../../../guides/workflows.md#3-worked-example-approval-before-publishing)).
2. Connect it to the trigger **content / publish / before**.
3. Exclude no editor group that uses the assistant.
4. Make sure the cronjob that continues workflows runs, so approved versions get published.
5. Test it with an editor account: write a draft with the assistant, check that it says "Waits for approval when
   sent", send it, approve it.

Because the event is the kernel's, it applies to hand-written content in the same sections as well. If you want
approval only for AI-written content, use a section or a group of editors for that work; the assistant does not add a
separate approval path.

## Related pages

- [What Exponential Intelligence does](what-exponential-intelligence-does.md)
- [Reviewing AI drafts](../checklists/reviewing-ai-drafts.md)
- [My drafts and my pending items](../../../guides/drafts-and-pending.md)
- [Publishing, versions and the trash](../../publishing-and-versions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
