---
title: "Lesson 4.1: Sections"
audience: trainer, admin
category: training
order: 41
summary: Learners read the section list, create a section, assign a subtree to it, and open it to a group of users with a Section-limited policy, building a members area. 45 minutes.
keywords: site administration, sections, section list, new section, identifier, navigation part, assign content, members area, section limitation, anonymous, remove section
type: help
---
# Lesson 4.1: Sections

A section is a named group of content objects. Every object belongs to exactly one, and roles, the administration's
top menu and designs can all depend on it. Sections are the administrator's main tool for "this part of the site is
for members only" or "this team edits only its own pages". This lesson builds a members area from scratch.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 4: Sections, siteaccesses and object states](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Module 3](../module-03-roles-and-policies/README.md): roles, assignments and testing |

## Learning objectives

After this lesson, learners can:

- read **Setup > Sections**: its figures, filters and the card of each section;
- explain what a section is and what it is not (not a place in the tree, not a siteaccess, not a state);
- create a section with a valid identifier;
- put a subtree into a section with **Assign content**;
- give a group read access to a section and test it as a visitor and as a member.

## Trainer's notes

### How to present it

Source: [Sections](../../../../guides/sections.md), especially sections 1, 2, 5, 8.3, 9 and 13.1.

1. **What a section is** (4 minutes). A name, an identifier, an ID and a navigation part. It has no root node and no
   URL: objects of one section can be anywhere in the tree. An object with two locations has one section.
2. **The five sections of a new installation** (2 minutes): Standard (1), Users (2), Media (3), Setup (4), Design (5).
   Anonymous may read Standard only; that is what keeps users and media folders away from visitors.
3. **The list** (3 minutes). **Setup > Sections**: the overview figures (sections, published objects, used by roles,
   without published objects, can be removed, need attention), **Find a section**, the filters and one card per
   section with **View**, **Edit** and **Assign content**.
4. **New content and moves** (3 minutes). New content takes the section of its parent. To change existing content,
   use **Assign content** on the section; it changes the chosen item and everything below it whose main location is
   there.
5. **Removing** (3 minutes). A section can only be removed when nothing uses it: no object (drafts included), no
   policy and no limited assignment. The page names what blocks it.

### Common questions

**Should I use sections or subtrees for permissions?** Subtrees follow the tree; sections follow the objects wherever
they are. Use a section when content of one kind is spread over the tree, or when the same subtree rule would be
repeated in many roles.

**Visitors still see content I moved to the members area.** A cached page: clear the static cache and any HTTP cache
too. **Assign content** clears the content view cache itself.

**Why do some sections show "Need attention"?** Typically a section without an identifier, which templates and
settings cannot name.

### Pitfalls

- **Content disappears for visitors.** After **Assign content** to a new section, visitors lose access until a policy
  allows the section. That is the point of the exercise, but on a live site plan it.
- **Invalid identifiers.** The identifier must start with a letter and contain only letters, digits and `_`. The
  form fills it in from the name; let learners see the message once.
- **Secondary locations.** Objects whose main location is elsewhere keep their old section.

## Hands-on exercise

On your training installation, signed in as an administrator, with a private window for testing:

1. Open **Setup > Sections**. Write down the overview figures and the sections with their IDs.
2. Press **New section**. **Name**: `Members area`; check the **Identifier** filled in (`members_area`); leave the
   navigation part as content structure. Press **Create section**.
3. In **Content structure**, create a folder `Members` with one article inside.
4. In the private window, signed out, open the `Members` folder on the public site. Note that you can read it.
5. On the `Members area` card, press **Assign content**, choose the `Members` folder and press **Select**.
6. Reload the page in the private window. Note the result.
7. Open **Users > Roles and policies**. Create a role `Members area readers` with one policy: `content` / `read`,
   limited to the Section `Members area`. **Save**.
8. Assign the role to the group **Members** (or `Training team`).
9. In the private window, sign in to the public site as `sam.sample` (a member since lesson 2.2) and open `Members`.
10. Back in **Setup > Sections**, open the `Members area` card's **View** page: which roles use it, and can it be
    removed?

## Expected result

- Step 6: signed out, the folder is refused (access denied or not found in menus).
- Step 9: signed in as a member, the folder and its article are readable.
- Step 10: the section's page lists `Members area readers` and the objects; it cannot be removed while they use it.
- The trainer checks by opening the `Members` folder in a private window on one learner's site.

## Quiz

1. How many sections can one object belong to?
2. Which section may anonymous visitors read on a new installation?
3. What does **Assign content** change?
   A. Only the chosen item B. The chosen item and everything below whose main location is there C. Every location in the
   site D. Only new content
4. Name the three things that keep a section from being removed.
5. Why did the folder disappear for visitors after step 5?

<details>
<summary>Answers</summary>

1. Exactly one ([Sections](../../../../guides/sections.md#in-short)).
2. Standard.
3. **B.** ([Assign content](../../../../guides/sections.md#83-assign-content-to-a-subtree-sectionassign)).
4. Objects (drafts and archived ones included), policies with a Section limitation naming it, role assignments
   limited to it.
5. The Anonymous role may read section Standard only, and the folder is now in `Members area`.

</details>

## Further reading

- [Sections](../../../../guides/sections.md): the complete guide with more worked examples (intranet departments,
  staging).
- [Content jobs](../../../../features/6.0/content-jobs.md): large assignments in the background.
- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md): Section limitations.

## Related pages

- [Module 4: Sections, siteaccesses and object states](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
