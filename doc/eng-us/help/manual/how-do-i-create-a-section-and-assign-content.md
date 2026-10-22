---
title: How do I create a section and put content into it?
audience: admin
category: manual
order: 600
summary: Create the section under Setup > Sections with a name, an identifier and a navigation part, then use Assign content on its card to move a part of the content tree into it.
keywords: section, new section, assign content, section/assign, section/edit, identifier, navigation part, subtree, permissions, remove section
context: section/list
type: help
---
# How do I create a section and put content into it?

A section is a label that every object carries, exactly one per object. Roles use it to decide who may read or edit
what, the administration uses it to choose the active top menu tab, and designs can give a section its own
templates. You create the label first, then you put content into it.

## Who can do this

- Creating and editing sections needs the policy `section/edit`.
- Putting content into a section needs `section/assign`. That policy can be narrowed: its **NewSection** limitation
  lists the sections you may hand out, and its Class, Owner and Section limitations decide which objects you may
  change.
- Administrators have both.

## Steps

1. Click **Setup** in the top menu and choose **Sections**.
   The list of sections opens, with figures for each one.
2. Click **New section**.
3. Fill in **Name**, for example "Members area".
   The **Identifier** is suggested from the name while you type (here `members_area`). Change it if you like: letters,
   digits and `_`, starting with a letter, not used by another section.
4. Leave **Navigation part** at **Content structure** for ordinary pages.
5. Click **Create section**.
   The list opens again and says that the section was created. It is empty.
6. On the new section's card, click **Assign content**.
   The content browser opens.
7. Find the item at the top of the part of the tree that belongs in the section, select it with its radio button and
   click **Select**.
   The item and the content below it now carry the section. For a very large subtree you are asked whether to do it
   now or as a background job.
8. Open the section with **View** and check the number of published objects.

To give a single object another section, open it, go to the **Details** tab, choose the section and click **Set**.

## What can go wrong

- **The content disappeared from the public site.** That is the usual first effect: visitors may only read the
  sections their role names, normally just Standard. Add a `content/read` policy for the new section to the role that
  should see it ([How do I set up a members-only area?](how-do-i-set-up-a-members-only-area.md)).
- **Some objects below the item kept their old section.** Assigning changes the objects whose main location is in
  that part of the tree. An object that only has an extra location there keeps its section, because an object has one
  section, whichever place it is shown in.
- **Assign content is greyed out.** Your `section/assign` policy does not offer this section. Its tooltip names the
  reason.
- **New pages land in the wrong section.** New content takes the section of its parent when it is published. Moving a
  subtree carries the new parent's section along to the objects that still had the old parent's section.
- **The section cannot be removed.** **Remove selected** refuses a section that still holds objects (drafts and
  archived versions count) or that a policy or a role assignment names. Move the objects to another section with
  **Assign content** on that section's card, and remove the limitation from the roles first.
- **A renamed identifier broke a template.** Override rules, fetches and templates may name the section by its
  identifier. Roles name it by its ID, so renaming the section itself changes no permission.

## Related pages

- [Sections](../../guides/sections.md): every page and rule of sections in detail
- [How do I give a group edit rights on one section only?](how-do-i-give-a-group-edit-rights-on-one-section.md)
- [How do I set up a members-only area?](how-do-i-set-up-a-members-only-area.md)
- [Sections, roles and policies](../cms-developers/sections-roles-and-policies.md)
- [Override by section](../designers/templates/override-by-section.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
