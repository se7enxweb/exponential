---
title: "Lesson 3.1: Roles, policies and limitations"
audience: trainer, admin
category: training
order: 31
summary: Learners explain how roles, policies and limitations combine into what a user may do, and read any role's page as plain sentences, including full access and roles that can change roles. 35 minutes.
keywords: site administration, roles, policies, limitations, permissions, module, function, section, class, subtree, full access, can change roles, role list
type: help
---
# Lesson 3.1: Roles, policies and limitations

Every "may I?" question in Exponential is answered by roles. This lesson builds the model learners need for the whole
module: a role is a set of policies, a policy names a module and a function, limitations narrow it, and a role does
nothing until it is assigned. Then learners read real roles on the role pages, which spell each policy out as a
sentence.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 3: Roles and policies](README.md) |
| Duration | 35 minutes (15 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Module 2](../module-02-users-and-groups/README.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- explain role, policy, module, function and limitation in their own words;
- say how limitations combine within one policy and how policies combine with each other;
- read a role's page and its figures (policies, assignments, users affected in all);
- recognize the badges *Full access*, *Can change roles* and *Not assigned* on the role list.

## Trainer's notes

### How to present it

Source: [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md) and
[Sections in roles and policies](../../../../guides/sections.md#9-sections-in-roles-and-policies).

1. **The model** (5 minutes). Draw it: *role* contains *policies*; a *policy* = *module* (`content`, `user`, `setup`,
   ...) + *function* (`read`, `edit`, `login`, ...) + optional *limitations* (Section, Class, Subtree, Owner,
   SiteAccess, Language, State ...). A role is *assigned* to users or groups. A user has every policy of every role
   assigned to them or to one of their groups.
2. **Combining** (4 minutes). Several limitations on one policy must **all** be met (AND); several values of one
   limitation are alternatives (OR); several policies of the same function add up (OR). There is no "deny": what is
   not granted is refused.
3. **The role list** (3 minutes). **Users > Roles and policies**: one card per role with its ID, policies and
   assignments, the actions **View**, **Edit**, **Assign**, **Copy**, and the badges *Full access* (red),
   *Can change roles* (amber) and *Not assigned*.
4. **A role page** (3 minutes). Open **Anonymous**: the figures, then each policy as a sentence, for example
   "May read content, in section Standard" with the stored form below it (`content / read · Section: Standard`).

### Common questions

**Can a policy forbid something?** No. Policies only grant. To take something away, remove or narrow the policy that
grants it, or the assignment.

**Why is the Administrator role marked red?** It has a policy over every module (`*/*`), which is full access.

**Why is "Can change roles" a warning?** A user who may change roles can give themselves any access. Since 6.0.15
`[RoleSettings] PreventPrivilegeEscalation=enabled` stops them granting more than they have, but give it carefully.

**What does "(no handler, denies)" under a limitation mean?** No code evaluates that limitation, so the policy gives
no access. It usually comes from a removed extension.

### Pitfalls

- **Learners expect a deny rule.** Repeat it: grants only, OR between policies.
- **Confusing sections and the tree.** A Section limitation is not a subtree; objects of one section can be anywhere.
  Module 4 explains sections in depth.

## Hands-on exercise

On your training installation, signed in as an administrator:

1. Open **Users > Roles and policies**. Write down every role and its number of policies and assignments.
2. Note which roles carry the badges *Full access*, *Can change roles* or *Not assigned*.
3. Use **Find a role** to find the role **Editor**. Change the order to **Policies** and back to **Name**.
4. Open the role **Anonymous**. Read the figures: how many users are affected in all?
5. Read the policies of **Anonymous** as sentences. Which sections may anonymous visitors read?
6. Find the `user / login` policy. On which siteaccesses may anonymous visitors sign in?
7. Order the policies **By module** and note how the list changes.
8. Open the role **Editor** and find one policy with more than one limitation. Write it as a sentence in your own
   words, and say whether its limitations combine with AND or OR.

## Expected result

- **Administrator** carries *Full access*. **Anonymous** has policies such as reading content in section Standard and
  logging in on the public siteaccesses.
- The sentence of step 8 names every limitation, joined by "and".
- The trainer checks by asking two learners to read their step 8 sentence aloud.

## Quiz

1. What does a role do before it is assigned?
2. A policy has the limitations Class( Article ) and Section( Standard ). Which objects does it cover?
   A. All articles and everything in Standard B. Articles in section Standard C. Nothing D. Only the class Article
3. Two policies grant `content/read` for section Standard and for section Media. What may the user read?
4. Which badge marks a role with a policy over every module?
5. True or false: a policy can deny access that another role grants.

<details>
<summary>Answers</summary>

1. Nothing. A role does nothing until it is assigned to users or groups
   ([Roles, policies and unactivated users](../../../../guides/roles-and-policies.md#in-short)).
2. **B.** Limitations of one policy combine by AND.
3. Content in Standard or in Media: policies of the same function combine by OR.
4. *Full access*, in red.
5. **False.** Policies only grant.

</details>

## Further reading

- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md): the role list, a role page,
  the editor.
- [Sections](../../../../guides/sections.md): Section limitations and assignments limited to a section.
- [Content limitation handlers](../../../../features/6.0/content-limitation-handlers.md): how limitations are
  evaluated.

## Related pages

- [Module 3: Roles and policies](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
