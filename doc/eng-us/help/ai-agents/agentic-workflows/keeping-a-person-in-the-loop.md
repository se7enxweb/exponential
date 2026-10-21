---
title: Keeping a person in the loop
audience: ai-agents, developer, app-creator, admin, project-manager
category: ai-agents
order: 67
summary: How to make sure a person reviews what an AI agent writes before visitors see it - drafts of Exponential Intelligence, a hidden section with an approval workflow for REST and expservices writes, reports instead of writes, and the audit trail as the record of who decided what.
keywords: human in the loop, human review, approval workflow, hidden section, staging section, drafts, agent writes, REST create, review before publish, accountability
type: help
---
# Keeping a person in the loop

This page is for anyone who lets an AI agent produce content and wants a person to decide what is published. The
assistant inside the administration does this by design; an agent of your own needs a setup that does the same.

## Contents

1. [Why it matters](#why-it-matters)
2. [Pattern 1: let the assistant write drafts](#pattern-1-let-the-assistant-write-drafts)
3. [Pattern 2: a hidden section with an approval workflow](#pattern-2-a-hidden-section-with-an-approval-workflow)
4. [Pattern 3: reports instead of writes](#pattern-3-reports-instead-of-writes)
5. [Who decided what](#who-decided-what)

## Why it matters

A REST v2 create **creates and publishes**, and expservices writes take effect when they are made. An agent misled by
text in a feed, or simply wrong, would publish its mistake at once. A person in between catches it, and is the one
accountable for what goes public.

## Pattern 1: let the assistant write drafts

When the content is written for editors anyway, use Exponential Intelligence: it writes drafts as the editor, never
publishes, and "Send for approval" runs the normal publish operation so an approval workflow applies
([Drafts and human review](../expai/drafts-and-human-review.md)). For larger jobs, Vector's staged review lets the
editor accept each change field by field ([Vector and staged review](../expai/vector-and-staged-review.md)).

## Pattern 2: a hidden section with an approval workflow

For an agent of your own that creates content:

1. Create a folder for agent output, in a **section that visitors cannot read** (the Anonymous role has no
   `content/read` for it).
2. Give the agent's account `content/create` only there ([Roles and least privilege](../governance/roles-and-least-privilege.md)).
3. Add an approval workflow on `content / publish / before` for that section, with editors as approvers, if items
   must be approved before they exist even internally ([Workflows](../../../guides/workflows.md)).
4. Editors review the items, edit them, and move or copy approved ones to their public place.
5. Check the agent account's rights with `./console exp:access:check --user=anonymous --node=<agent folder>`: it must
   end with DENIED.

## Pattern 3: reports instead of writes

Often the agent does not need to write at all. Let it read through a read-only key and produce a report, a list of
suggested changes, or a draft text in a file, and let an editor apply it
([A read-only content report](workflow-read-only-content-report.md)). This removes the write credential entirely.

## Who decided what

The audit trail records the agent account's writes and the editors' publish, move and approval actions, each under the
account that made them ([The audit trail for AI](../governance/audit-trail-for-ai.md)). With a separate account per
agent, "who published this?" always has a person's name as the answer.

## Related pages

- [Choosing an interface for an agent](choosing-an-interface.md)
- [A content import workflow](workflow-content-import.md)
- [An AI use policy for your organization](../governance/an-ai-use-policy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
