---
title: Sections and roles for intranet content
audience: government, admin
category: public-sector
order: 40
summary: How to keep internal content internal in Exponential: a section for intranet content that the Anonymous role cannot read, a Staff role that can, department sections for restricted material, and how to test from both sides.
keywords: intranet section, sections, roles, Anonymous role, Staff role, content/read, section limitation, internal content, confidentiality, access test
type: help
---
# Sections and roles for intranet content

This page is for administrators. Every object belongs to exactly one section, and policies can be limited to
sections; the Anonymous role may read Standard only in a new installation, so content in a new section is hidden from
visitors until a policy allows it ([Sections](../../../../guides/sections.md)).

## Contents

1. [Sections](#sections)
2. [Roles](#roles)
3. [Restricted material](#restricted-material)
4. [Testing from both sides](#testing-from-both-sides)

## Sections

1. **Setup > Sections**: create "Intranet" (identifier `intranet`).
2. Create the intranet folder and **Assign content**: the folder and everything below it get the section. New content
   takes the section of its parent.
3. Check the Anonymous role does not read `intranet` ([Visitor permissions](../../../creators/website-creators/visitor-permissions.md)).

## Roles

| Role | Policies |
|---|---|
| Staff (all staff) | `content/read` limited to the sections Intranet and Standard; `user/login` for the intranet siteaccess |
| Intranet editors | `content/create`, `content/edit` limited to section Intranet (or a subtree) |

Assign Staff to the staff user group (or the groups LDAP creates; see "LDAP sign-in").

## Restricted material

For material only some staff may read (HR, legal), use a further section (for example "Intranet restricted") read
only by a group. Remember that a person with a role allowing `content/read` on all sections reads everything; keep
such roles few.

## Testing from both sides

- Signed out on the public site: an intranet page's address must not open, and intranet content must not appear in
  search or lists.
- Signed in as a test staff account (not a real person's): intranet pages open; restricted pages do not.
- Refused views are recorded in the audit trail's `access` channel as `access.permission.refused`
  ([Audit trail](../../../../features/6.0/audit-trail.md)).

## Related pages

- [Sections and object states for site builders](../../../creators/website-creators/sections-and-object-states.md)
- [Roles and policies](../../../../guides/roles-and-policies.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
