---
title: "Lesson 3.3: Assigning roles, with and without limitation"
audience: trainer, admin
category: training
order: 33
summary: Learners assign a role to a user group, assign the same role limited to a subtree or a section, read who a role reaches, and remove assignments. 40 minutes.
keywords: site administration, assign role, assign with limitation, subtree, section, user group, assignments, users affected, remove assignment, privilege escalation
type: help
---
# Lesson 3.3: Assigning roles, with and without limitation

A role takes effect only when it is assigned. Assigning it to a group, rather than to single users, keeps access
manageable; assigning it with a limitation lets one role serve several teams, each in its own part of the site. This
lesson assigns the role built in lesson 3.2 both ways.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 3: Roles and policies](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 3.2](building-a-role-with-limited-policies.md): the role `News editors`; the group `Training team` from lesson 2.1 |

## Learning objectives

After this lesson, learners can:

- assign a role to a user group with **Assign**;
- assign a role limited to a subtree or a section with **Assign with limitation**;
- read the role's figures: assignments, groups, users assigned directly, users affected in all;
- remove an assignment without removing users or the role;
- explain why the same role can be assigned several times with different limitations.

## Trainer's notes

### How to present it

Source: [Assign the role](../../../../guides/roles-and-policies.md#5-assign-the-role) and
[A role assigned with the limitation to a section](../../../../guides/sections.md#92-a-role-assigned-with-the-limitation-to-a-section).

1. **Assign to groups** (3 minutes). On the role page, **Assign** opens the content browser on the user tree. Choose
   groups: every member, now and later, gets the role. Assign to a single user only for a temporary exception.
2. **Assign with limitation** (5 minutes). Choose *Subtree* or *Section*, then the subtree or section, then the users
   or groups. Every policy of the role then applies only there. One "Editor" role, assigned to Marketing limited to
   its section and to Sales limited to its own, replaces two nearly identical roles.
3. **Reading the reach** (3 minutes). The figures on the role page: assignments, user groups, users assigned
   directly, and **users affected in all** (members of its groups counted once). The line below says how many
   assignments are limited. The group's own **Roles** tab shows the same from the other side.
4. **Removing** (2 minutes). Tick assignments, **Remove selected**, confirm *Remove the ticked assignments*. The users
   and groups lose the role at once; nothing else is removed.

### Common questions

**What is the difference between a Subtree limitation on a policy and an assignment limited to a subtree?** The first
narrows one policy everywhere the role is assigned; the second narrows the whole role for that one assignment.

**Can I assign a role to a group of deactivated users?** No. **Assign** leaves the group for deactivated accounts out
and says so on the role's page.

**Why was my assignment refused?** With `PreventPrivilegeEscalation=enabled`, a user who is not a full administrator
cannot assign a role that grants more than they have themselves (*The role was not assigned*).

### Pitfalls

- **Assigning to a person instead of the group.** Works, but six months later nobody remembers why. Ask learners to
  explain every direct assignment.
- **Limited to the wrong subtree.** The content browser starts in the user tree for the users; the subtree for the
  limitation is chosen first. Go slowly on the projector.
- **A section limitation that matches nothing.** Content that is not in the section is not covered; check with
  lesson 3.4 before telling the team it works.

## Hands-on exercise

On your training installation, signed in as an administrator:

1. In **Content structure**, create a folder `News` below the top of the site (if your site package already has a
   news folder, use it).
2. Open **Users > Roles and policies**, then the role `News editors`.
3. Choose *Subtree* for **Assign with limitation**, pick the `News` folder, then pick the group `Training team`.
4. Read the role page: the figures and the assignment line with its limitation.
5. Open the group `Training team` and its **Roles** tab. Check that the role and its limitation are listed.
6. Back on the role page, use **Assign** without limitation and pick the user `sam.sample` only.
7. Read the figures again: how many users are assigned directly, and how many are affected in all?
8. Tick the direct assignment of `sam.sample`, press **Remove selected** and confirm.
9. Check that `sam.sample` still exists in **Users**.
10. Explain to your neighbor in one sentence what members of `Training team` may now do.

## Expected result

- `News editors` is assigned once, to `Training team`, limited to the subtree of `News`.
- After step 6 the figures count one user assigned directly; after step 8 none.
- `sam.sample` still exists; only the assignment was removed.
- The trainer checks the role page of one learner and the sentence of step 10.

![The users and groups a role is assigned to, with Assign and Assign with limitation](../../../images/trainers/sa-assign-role.webp)

## Quiz

1. Why assign roles to groups rather than to users?
2. Which button gives a role only inside one subtree?
   A. **Assign** B. **Assign with limitation** C. **Copy** D. **Edit**
3. What happens to users when you remove an assignment?
4. One role "Editor" must work for Marketing and for Sales, each only in its own section. How?
5. What does the figure "users affected in all" count?

<details>
<summary>Answers</summary>

1. Every member of the group, now and later, gets the role
   ([Assign the role](../../../../guides/roles-and-policies.md#5-assign-the-role)).
2. **B.** Choose *Subtree* or *Section* first.
3. Nothing happens to them except that they lose the role at once; they are not removed.
4. Assign the role twice with limitation: to Marketing limited to its section, to Sales limited to its section
   ([Sections](../../../../guides/sections.md#92-a-role-assigned-with-the-limitation-to-a-section)).
5. Every user the role reaches, counting members of its groups once each, including limited assignments.

</details>

## Further reading

- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md): assignments and the escalation
  check.
- [Sections](../../../../guides/sections.md): roles limited to sections, with worked examples.
- [Role assignment paging](../../../../features/6.0/role-assignment-paging.md): roles with many assignments.

## Related pages

- [Module 3: Roles and policies](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
