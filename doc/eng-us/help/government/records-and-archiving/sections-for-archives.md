---
title: Sections for archives
audience: government, admin
category: public-sector
order: 80
summary: For administrators - how an Exponential section can hold archived content so that roles decide who reads it (public archive, staff-only archive), how content gets into a section, what moving does to sections, and the rules for removing sections.
keywords: sections, archive section, section/assign, Assign content, Section limitation, Anonymous role, staff-only archive, navigation part, content tree, records, public sector
type: help
---
# Sections for archives

This page is for administrators. A **section** is a named group of objects; every object belongs to exactly one. Roles
can be limited to sections, which makes a section a simple way to give archived content its own read rules
([Sections](../../../guides/sections.md)).

## Contents

1. [When a section helps](#when-a-section-helps)
2. [Setting up an archive section](#setting-up-an-archive-section)
3. [Getting content into it](#getting-content-into-it)
4. [Pitfalls](#pitfalls)
5. [Related pages](#related-pages)

## When a section helps

| Need | Section approach |
|---|---|
| Archived content readable only by staff | an "Archive (internal)" section; the Anonymous role has no read policy for it |
| Archived content public but editable only by records staff | an "Archive" section readable by Anonymous; editors' `content/edit` limited to other sections |
| Different templates for archived pages (a banner "archived content") | template overrides by `Match[section_identifier]` |

The default installation has five sections: Standard (1), Users (2), Media (3), Setup (4), Design (5). The Anonymous
role may read Standard only, so content put into a new section is hidden from visitors until a policy allows it.

## Setting up an archive section

1. **Setup > Sections** (`/section/list`), **New section**: name, identifier (for example `archive`), navigation part.
2. Roles: add or limit policies with a Section limitation (Users > Roles and policies): for example `content/read`
   for Anonymous including `archive` if it should be public.
3. Templates: override the full view for `Match[section_identifier]=archive` to show an "archived" notice
   ([Sections: Templates and designs by section](../../../guides/sections.md#11-templates-and-designs-by-section)).

## Getting content into it

- **Assign content** (section page, or `section/assign`): the chosen item and everything below it get the section.
- **Moving** a subtree carries the section of the new parent to objects that still had the old parent's section; so
  moving notices into an archive folder whose section is `archive` puts them in the section.
- New content takes the section of its parent.

Section changes are recorded in the audit (`content.node.section`, `content.section.*`).

## Pitfalls

- A section is not a location: the URL stays where the content is in the tree.
- A section can be removed only when nothing uses it (objects, policies, role assignments); removal cannot be undone.
- Check every role after changing section limitations; policies of several roles add up.

## Related pages

- [Sections](../../../guides/sections.md)
- [Archiving old notices](archiving-old-notices.md)
- [Visitor permissions](../../creators/website-creators/visitor-permissions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
