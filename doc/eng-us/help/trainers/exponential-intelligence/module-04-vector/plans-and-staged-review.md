---
title: "Lesson 4.2: Plans and staged review"
audience: trainer, editor
category: training
order: 42
summary: Learners ask a Vector thread for a plan, read its items and cost estimate, approve and run it, then review the proposed changes part by part in the Changes tab and write the accepted ones to drafts. 60 minutes.
keywords: exponential intelligence, vector, plan mode, plan tab, approve and run, ask for changes, reject, estimate, staged review, changes tab, accept, reject, accept field, write accepted changes to drafts, conflict
type: help
---
# Lesson 4.2: Plans and staged review

For anything bigger than one small edit, Vector works in two gates. First a **plan** with a cost estimate, which the
editor approves. Then **staged changes**, which the editor accepts or rejects part by part before anything is written.
Even then, the result is drafts. This lesson walks through both gates.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 4: Vector](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Experienced editors with the `expai/vector` policy |
| Prerequisites | [Lesson 4.1](threads-and-scope.md): a thread scoped to the learner's course folder |

## Learning objectives

After this lesson, learners can:

- run a request in plan mode and read the plan's items, estimate, assumptions, questions and risks;
- **Approve and run**, **Ask for changes** or **Reject** a plan, and untick items;
- review staged changes in the **Changes** tab: accept, reject or edit parts and fields;
- write accepted changes with **Write accepted changes to drafts**;
- explain what a conflict is and the three ways out.

## Trainer's notes

### How to present it

Sources: the plan mode and review pages under **EI: Help** (`extension/expai/doc/plan-mode.md`,
`extension/expai/doc/review.md`).

1. **Plan** (5 minutes). In plan mode the thread reads and proposes a plan; it writes nothing. The **Plan** tab shows a
   table of items (a Run checkbox, action, target, languages, fields, estimate) with editable instructions, the
   estimate as a range, the limits it touches, assumptions, questions and risks. **Approve and run**, **Ask for
   changes**, **Reject**. Unticking an item estimates again.
2. **Staged changes** (5 minutes). The write tools propose changes instead of writing: one per item, language and
   field. The **Changes** tab groups them by item and language, then field, then parts: what goes in red, what comes
   in green, word by word. Actions: **Accept**, **Reject** (keys `a` and `x`), **Accept field**, **Reject field**,
   **Edit** for plain text, **Accept all**, **Reject all**, then **Write accepted changes to drafts**.
3. **Protection** (3 minutes). A part that would remove embeds, formulas or anchors from rich text says so and cannot
   be accepted. Datatypes the assistant does not write (images, relations, files) are skipped.
4. **Conflicts** (4 minutes). If a colleague published the item meanwhile, the change becomes a conflict and is not
   written. Ways out: **Keep current**, compare with the current version, or ask the AI to rebase.
5. **The result** (3 minutes). Drafts with the label, listed under *Drafts written*, published or sent for approval as
   usual.

### Common questions

**Can I change the plan before running it?** Edit an item's instruction, untick items, or **Ask for changes** for a new
plan.

**What if I accept nothing?** Nothing is written.

**Is the estimate the cost?** A range from token counts and the price table; the actual cost appears in **Cost**.

### Pitfalls

- **Accept all by habit.** Read the parts; the tool is built for reading.
- **Big plans in class.** Keep the course folder small (three to five articles) so a plan runs in minutes.
- **Conflicts in class.** Two learners on one folder create conflicts; keep scopes separate (or create one on purpose
  to show it).

## Hands-on exercise

Use your thread from lesson 4.1 (scope: your course folder as subtree, *Article*, one language; Plan; Staged).

1. In the message field, type: `Make the intro of every article in this folder at most two sentences, keep the
   meaning. Plan first.` Press **Run**.
2. When the thread waits in *Needs you*, open the **Plan** tab. Read the items, the estimate and the risks.
3. Untick one item and watch the estimate change. Tick it again.
4. Press **Approve and run**.
5. When changes are staged, open the **Changes** tab. For each article, read the parts.
6. Accept the parts you agree with (**Accept** or `a`), reject one (**Reject** or `x`), and edit one plain-text part
   with **Edit** if offered.
7. Press **Write accepted changes to drafts**.
8. Open one of the drafts from *Drafts written* in the normal edit form and read it. Publish it, or discard it.

## Expected result

- A plan with one item per article was approved; changes were staged, partly accepted and written to drafts.
- Rejected parts kept the published text; nothing was published by Vector.
- The trainer checks by opening one learner's **Changes** tab and the resulting draft on the projector.

## Quiz

1. What does a thread do in plan mode?
2. Name the three buttons of the **Plan** tab.
3. What do the keys `a` and `x` do in the **Changes** tab?
4. True or false: **Write accepted changes to drafts** publishes the changes.
5. A colleague published an article after the change was staged. What happens, and what can you do?

<details>
<summary>Answers</summary>

1. It reads and proposes a plan; it writes nothing.
2. **Approve and run**, **Ask for changes**, **Reject**.
3. Accept and reject the current part.
4. **False.** It writes drafts; nothing is published.
5. The change becomes a conflict and is not written; keep the current version, compare with it, or ask the AI to
   rebase.

</details>

## Further reading

- The plan mode, review, parallel runs and diff pages under **EI: Help**.

## Related pages

- [Module 4: Vector](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
