---
title: "Lesson 1.3: Who may use it, and how"
audience: trainer, editor, admin
category: training
order: 13
summary: Learners name the main expai policies, explain that every lookup and write runs with the editor's own content permissions, and design roles for an editor, a power user and a data protection officer. 30 minutes.
keywords: exponential intelligence, expai, permissions, policies, expai/write, expai/log, expai/vector, expai/chain, expai/summary, expai/model_use, ezjscore call, roles, data protection officer
type: help
---
# Lesson 1.3: Who may use it, and how

What the assistant may do is never more than what the editor may do. On top of that, the site decides with policies
who may use the assistant at all, and which of its parts. This lesson explains both layers and designs three roles.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 1: Introducing Exponential Intelligence](README.md) |
| Duration | 30 minutes (15 presenting, 15 exercise) |
| Level | Beginner |
| Audience | Editors, and the administrators who give them access |
| Prerequisites | [Lesson 1.2](the-exponential-intelligence-dashboard.md) |

## Learning objectives

After this lesson, learners can:

- explain that every lookup and write runs as the editor, with the editor's content permissions;
- name the policies `expai/write`, `expai/log`, `expai/vector`, `expai/chain`, `expai/model_use` and `expai/summary`
  and what each opens;
- explain why `ezjscore/call` is needed too;
- design a role for an editor, a power user and a data protection officer.

## Trainer's notes

### How to present it

Source: the extension's permissions page (`extension/expai/doc/permissions.md`, under **EI: Help**).

1. **As the editor** (4 minutes). Reading goes through the kernel's checks for the current user. Creating a draft
   checks `content/create` on the parent for that class and language, with every limitation of the role. Changing
   checks `content/edit`. The assistant never uses another account and never switches users.
2. **The main policies** (6 minutes):

   | Policy | Opens |
   |---|---|
   | `expai/write` | The Intelligence tab, the edit form controls, the dashboard page and block, the log of one's own requests, the "Missing alt text" report, the help pages |
   | `expai/log` | Everyone's requests and versions; the **Site** view of the dashboard |
   | `expai/vector` | Vector (with limitations Subtree, Section, Scope, Review) |
   | `expai/chain` | AI chains (limitation Chain) |
   | `expai/model_use` | Models of providers outside the open list (limitation Provider) |
   | `expai/summary` | The printable **EI: Summary** page, without any content access |

   Further policies exist for model management, settings, web tools, MCP tools, service tools, translation sync and
   a fast lane; the trainer names them only for administrators.
3. **ezjscore** (2 minutes). The browser reaches the assistant through `ezjscore/call`; the role needs it, unlimited
   or with the function list `expai`. Many installations give it to all signed-in users.
4. **Not shown** (1 minute). Without `expai/write`, the tab, the menu entry and the dashboard block are not shown at
   all.

### Common questions

**Can an editor use the assistant to create content where they may not?** No. Creating and changing go through the
editor's own content permissions.

**Can Vector be limited to one part of the site?** Yes: the `expai/vector` policy has the limitations Subtree,
Section, Scope (node, subtree, class) and Review (staged, direct).

**Who should get `expai/summary`?** A data protection officer: it needs no other expai policy and no content access.

### Pitfalls

- **Giving `expai/log` to everyone.** It shows everyone's requests; editors need only their own.
- **Forgetting `ezjscore/call`.** The tab appears but nothing works.
- **Direct review for bulk work.** Keep Vector staged for most editors (lesson 4.2).

## Hands-on exercise

Use paper and, on the projector, the trainer's administrator session on the training installation.

1. The trainer opens the role used for this course under **Users** > **Roles and policies** and shows its `expai`
   policies.
2. Write down which of the six main policies the course role has.
3. In pairs, design on paper:
   - an "Editor with assistant" role: `expai/write`, `ezjscore/call` (function list `expai`), and the content policies
     the editors already have;
   - a "Power editor" role: plus `expai/vector` with Review **staged** and a subtree limitation, plus `expai/chain`;
   - a "Data protection officer" role: `expai/summary` only.
4. For each role, write one sentence on what the person can and cannot do.
5. The trainer compares two pairs' designs with the table.

## Expected result

- Each pair has three role designs consistent with the table.
- Each learner can explain why the assistant cannot do more than the editor.
- The trainer checks two designs on the projector.

## Quiz

1. Whose content permissions apply when the assistant creates a draft?
2. Which policy shows the Intelligence tab and the dashboard page?
   A. `expai/log` B. `expai/write` C. `expai/summary` D. `expai/vector`
3. Why does a role also need `ezjscore/call`?
4. What does `expai/summary` give a data protection officer?
5. Name two limitations of `expai/vector`.

<details>
<summary>Answers</summary>

1. The signed-in editor's.
2. **B.**
3. The browser reaches the assistant through ezjscore calls of the function list `expai`.
4. The printable **EI: Summary** page, without any other expai policy or content access.
5. Any two of Subtree, Section, Scope, Review.

</details>

## Further reading

- [Roles and policies](../../../../guides/roles-and-policies.md): the role editor.
- The permissions page under **EI: Help** (`extension/expai/doc/permissions.md`).

## Related pages

- [Module 1: Introducing Exponential Intelligence](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
