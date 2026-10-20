---
title: Create a section for restricted content
audience: government, admin
category: public-sector
order: 260
summary: Step by step - create a section such as "Internal", put existing content into it with Assign content, and give only the right staff read access, so visitors never see it.
keywords: section, restricted content, internal content, new section, Assign content, section/assign, visitors, Anonymous role, intranet, confidential pages
type: help
---
# Create a section for restricted content

This task is for administrators. At the end, content in a new section, for example "Internal", is invisible to
visitors and readable only by the staff roles you choose. The section pages are described in
[Sections](../../../../guides/sections.md).

## Contents
1. [Before you start](#before-you-start)
2. [Create the section](#create-the-section)
3. [Put content into it](#put-content-into-it)
4. [Give staff read access](#give-staff-read-access)
5. [Check it worked](#check-it-worked)
6. [Pitfalls](#pitfalls)
7. [Related pages](#related-pages)

## Before you start

- `section/edit` (to create) and `section/assign` (to put content in); administrators have both.
- Know which staff roles must read the content.

A section is not encryption or a separate system: content in it is protected by roles only. Information whose
classification forbids a public web server does not belong on it at all.

## Create the section

1. **Setup > Sections** (`/section/list`), **New section**.
2. **Name**: `Internal`. **Identifier** is filled in from the name (`internal`); it must start with a letter and
   contain only letters, digits and `_`.
3. **Navigation part**: leave **Content structure**.
4. **Create section**. The list says "The section Internal was created."

The new section is empty and, because the Anonymous role reads only Section Standard, already invisible to visitors
([Sections, section 2](../../../../guides/sections.md#2-the-sections-of-a-new-installation)).

## Put content into it

1. On the section's card, **Assign content**.
2. Choose the top item of the internal content in the content browser and press **Select**.
3. For a large subtree (from `content.ini [ContentJobSettings] SynchronousLimit`, 50 items by default) choose to do
   it now or in the background as a content job.
4. The item and everything below it whose main location is there get the section. New content created below it
   takes the section of its parent.

## Give staff read access

For each staff role that must read it: **Users > Roles and policies**, the role, **Edit**, the `content/read`
policy, **Limitations**, tick "Internal" in the Section list next to the existing sections, **OK**, **Save**. Edit
rights work the same way.

## Check it worked

- In a private window without signing in, open the address of an internal page: it is not shown.
- Signed in as a staff member with the role: it is shown.
- The section's page lists the roles that use it.

## Pitfalls

- Secondary locations: an object has one section wherever it appears, so an internal object linked into a public
  folder stays internal (good), but a public object linked into the internal folder stays public.
- Search results and menus follow read rights, so visitors do not see internal items in them.
- Removing the section later is possible only when nothing uses it, and cannot be undone.

## Related pages
- [Let visitors read a new section](let-visitors-read-a-new-section.md)
- [Assign a role with a section limitation](assign-a-role-with-a-section-limitation.md)
- [Sections and subtree limits](../../operations/people-and-permissions/sections-and-subtree-limits.md)
- [Content jobs](../../../../features/6.0/content-jobs.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
