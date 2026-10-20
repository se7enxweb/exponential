---
title: Onboarding a department
audience: government, admin, editor, project-manager
category: public-sector
order: 120
summary: How to bring a new department of a public body onto an existing Exponential site - content area, section, user group, roles, workflow, design, training and go-live - step by step with the admin pages involved.
keywords: onboarding, department, new department, section, user group, role, subtree, workflow, approval, training, multisite, public sector
type: help
---
# Onboarding a department

This checklist is for the site administrator and the web team of a public body when a department joins the central
site (or gets its own site in a multisite installation). It uses sections, user groups and roles so the department
can publish its own content without touching others'.

## Contents

1. [Agree the scope](#agree-the-scope)
2. [Structure](#structure)
3. [People and permissions](#people-and-permissions)
4. [Process](#process)
5. [Training and launch](#training-and-launch)

## Agree the scope

- [ ] Content the department will publish, its kinds (existing content classes, or new ones needed) and its volume.
- [ ] Owner in the department and a deputy.
- [ ] Languages ([Multilingual checklist](multilingual-checklist.md)).
- [ ] A subtree of the main site, or its own site in the same installation ([Multisite setup](../../creators/website-creators/multisite-setup.md)).

## Structure

- [ ] Create the department's subtree in the content tree ([Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md)).
- [ ] Create a section for it in **Setup > Sections** and assign the subtree ([Sections](../../../guides/sections.md)).
- [ ] Menus and navigation include the new area ([Menus and navigation](../../creators/website-creators/menus-and-navigation.md)).
- [ ] For a separate site: siteaccess, design and settings ([Multi-site INI overrides](../../../features/6.0/multi-site-ini-overrides.md)).

## People and permissions

- [ ] A user group for the department's editors.
- [ ] A role limited to the department's section or subtree, assigned to the group ([Roles and policies](../../../guides/roles-and-policies.md)).
- [ ] An approver role, if approval is required.
- [ ] Accounts created for named people; nobody gets the role before their training ([Onboarding new staff](../procurement/training/onboarding-new-staff.md)).
- [ ] Check with **Check user accounts** (`/user/hygiene`) later that the accounts are used ([Check user accounts](../../../features/6.0/user-hygiene.md)).

## Process

- [ ] Approval workflow, if needed: workflow with an Approve event and a trigger on publishing ([Workflows](../../../guides/workflows.md#3-worked-example-approval-before-publishing)).
- [ ] Object states for the department's stages, if needed ([Object states](../../../guides/object-states.md)).
- [ ] Notifications for approvers ([Notifications](../../../guides/notifications-administrator.md)).
- [ ] Editorial guide shared ([Editorial quality](editorial-quality.md), [Plain language checklist](plain-language-checklist.md)).

## Training and launch

- [ ] Editors trained (Editors 101), approvers in approvals ([Roles to courses](../procurement/training/roles-to-courses.md)).
- [ ] First content created and reviewed before the area is visible.
- [ ] Accessibility and links checked ([Accessibility checklist](accessibility-checklist.md)).
- [ ] Area made visible; department announced.
- [ ] Added to the next [Quarterly access review](quarterly-access-review.md).

## Related pages

- [Government checklists](README.md)
- [Sections and object states](../../creators/website-creators/sections-and-object-states.md)
- [Handing over to editors](../../creators/website-creators/handing-over-to-editors.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
