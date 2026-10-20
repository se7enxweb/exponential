---
title: "Role design: the central web team"
audience: government, admin, project-manager
category: public-sector
order: 60
summary: A least-privilege role for the central web team of a public body - full content work across the site, caches, cronjobs and logs, but not roles, settings or the audit's management - built only from policy functions that exist in Exponential.
keywords: web team, role design, central team, webmaster, content management, setup/managecache, setup/logs, section/assign, state/assign, least privilege, without full access
type: help
---
# Role design: the central web team

This page is for the administrator who sets up the role of the central web team: the people who run the site's
structure and help the departments, but who are not system administrators. It gives a role built only from policy
functions defined in `kernel/<module>/module.php`, with the reason for each line. Adapt the limitations to your tree.

## Contents
1. [What the web team does](#what-the-web-team-does)
2. [The role "Web team"](#the-role-web-team)
3. [What is deliberately left out](#what-is-deliberately-left-out)
4. [Setting it up](#setting-it-up)
5. [Checking it](#checking-it)
6. [Related pages](#related-pages)

## What the web team does

Typical duties: create and arrange pages anywhere on the public site, fix other teams' content, manage the media
library, move content between sections and states, clear caches after a change, check that cronjobs ran and read the
logs when something looks wrong. They usually do **not** create roles, change settings, install extensions or manage
the audit trail; those stay with the system administrators (see [Separation of duties](separation-of-duties.md)).

## The role "Web team"

| Module | Function | Limitations | Why |
|---|---|---|---|
| user | login | SiteAccess: the administration and the public siteaccesses | sign in to the administration |
| content | read | none | see everything to support the departments |
| content | create, edit, publish, hide, move, manage_locations, translate | Section: Standard, Media | content work on the public site and the media library, not on user accounts (section Users) |
| content | remove | Section: Standard, Media | remove pages; the trash still allows restore |
| content | versionread, diff | Section: Standard, Media | read and compare versions when helping editors |
| content | restore | none | bring items back from the trash |
| content | dashboard, pendinglist | none | the dashboard and pending list |
| section | view, assign | assign with NewSection limited to your public and internal sections | put content into the right section |
| state | assign | NewState: the states of your review group | move content through review |
| setup | managecache | none | clear caches after a change ([Caches](../../../../guides/caches.md)) |
| setup | managecronjobs | none | see and run cronjob parts on **Setup > Cronjobs** |
| setup | logs | none | read **Setup > Log files**, masked |
| setup | system_info | none | read **Setup > System information** |

The function names come from `kernel/content/module.php`, `kernel/section/module.php`, `kernel/state/module.php`,
`kernel/setup/module.php` and `kernel/user/module.php`. Check the role page after saving: each policy is written as a
sentence, which is the record you keep.

Add `content/publish_without_notification` only if `notification.ini [NotificationSettings] PublishWithoutNotification`
is enabled and the team corrects typos often
([Publish without notification](../../../../features/6.0/publish-without-notification.md)).

## What is deliberately left out

| Left out | Why |
|---|---|
| the `role` module | would let the team change roles and grant themselves anything |
| `setup/administrate`, `setup/setup` | settings and the setup pages stay with system administrators |
| `setup/managelogs`, `setup/unmasklogs` | rotating, emptying logs and unmasked addresses are incident tasks |
| `audit/read`, `audit/manage` | the audit is for auditors and system administrators ([Role design: auditors](role-design-auditors.md)) |
| `user/hygiene`, `user/deactivate` | account administration is a separate duty |
| `content/cleantrash` | emptying the trash is final |
| content in section Users | editing user accounts is account administration |

## Setting it up

1. **Users > Roles and policies > New role**, name it "Web team".
2. Add each line of the table with **New policy**: choose the module, the function, then **Grant limited access**
   where a limitation is listed, or **Grant full access** for that function where none is.
3. **Save**.
4. Create a user group "Web team" in the Users tree if you do not have one
   ([Create a user group for a department](../../tasks/people-and-permissions/create-a-user-group-for-a-department.md)).
5. On the role page, **Assign** the role to the group.

## Checking it

- Sign in as a member in a private browser window: the Setup menu shows Cache management, Cronjobs, Log files and
  System information, and no Roles and policies under Users.
- Open `/role/list` by typing it: the page is refused, and the refusal is recorded in the audit trail as
  `access.permission.refused` ([Audit trail](../../../../features/6.0/audit-trail.md#who-may-look)).
- The role page shows no *Full access* or *Can change roles* badge.

## Related pages
- [Role design: department editors](role-design-department-editors.md)
- [Least-privilege principles](least-privilege-principles.md)
- [Give a colleague cache-clearing rights](../../tasks/people-and-permissions/give-a-colleague-cache-clearing-rights.md)
- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
