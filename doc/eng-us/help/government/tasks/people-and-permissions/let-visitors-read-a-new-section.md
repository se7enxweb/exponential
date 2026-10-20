---
title: Let visitors read a new section
audience: government, admin
category: public-sector
order: 270
summary: Step by step - when a new public section (for example "Public documents") shows nothing to visitors, add it to the Anonymous role's content/read policy, and check the result in a private window.
keywords: Anonymous role, visitors, new section, content/read, public section, invisible content, permission, Section limitation, not shown
type: help
---
# Let visitors read a new section

This task is for administrators. A new section is invisible to visitors because the Anonymous role has `content/read`
(and `content/pdf`) limited to Section Standard. At the end, visitors read the new section too. This is the example of
[Sections, section 9.1](../../../../guides/sections.md#91-a-policy-with-a-section-limitation).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Pitfalls](#pitfalls)
5. [Related pages](#related-pages)

## Before you start

- An account with the `role` module.
- The section is meant to be public: double-check nothing in it is internal.

## Steps

1. **Users > Roles and policies**, open **Anonymous**, press **Edit**.
2. Find the policy *May read content, in section Standard* (`content/read`) and click **Limitations** next to it.
3. In the Section list, tick the new section in addition to Standard (Ctrl or Cmd). **OK**.
4. If visitors should also get PDF versions, do the same for `content/pdf`.
5. **Save**.
6. Clear the content view cache if pages were cached while hidden:

   ```bash
   php bin/php/ezcache.php --clear-tag=content --allow-root-user
   ```

## Check it worked

- In a private browser window, without signing in, open a page of the section: it is shown, and it appears in
  menus and search results.
- The section's page (**Setup > Sections**) lists Anonymous among the roles with policies limited to it.

## Pitfalls

- If other siteaccesses (a second public site) use other anonymous roles or users, repeat for them.
- If the public read policy also has a state limitation (for example only *Approved*), new content still needs that
  state ([Object states in permissions](../../operations/people-and-permissions/object-states-in-permissions.md)).
- Under Velocity or with the HTTP cache, clear the response cache as well
  ([Clear the Velocity response cache](../operations/clear-the-velocity-response-cache.md)).

## Related pages
- [Create a section for restricted content](create-a-section-for-restricted-content.md)
- [Sections and subtree limits](../../operations/people-and-permissions/sections-and-subtree-limits.md)
- [Clear caches after a settings change](../operations/clear-caches-after-a-settings-change.md)
- [Sections guide](../../../../guides/sections.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
