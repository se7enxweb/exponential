---
title: Give a colleague cache-clearing rights
audience: government, admin
category: public-sector
order: 320
summary: Step by step - let a web team member clear caches from Setup > Cache management with the single function setup/managecache, without settings or full access, and explain what they should and should not clear.
keywords: cache, clear cache, setup/managecache, Cache management, web team, least privilege, operations rights, role, Setup
type: help
---
# Give a colleague cache-clearing rights

This task is for administrators. At the end, a colleague can clear caches from the administration without any other
setup right. **Setup > Cache management** (`/setup/cache`) needs the policy `setup/managecache`
([Caches guide](../../../../guides/caches.md)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [What to tell the colleague](#what-to-tell-the-colleague)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- An account with the `role` module and `setup/managecache` yourself.
- The colleague signs in to the administration (`user/login` on that siteaccess).

## Steps

1. **Users > Roles and policies > New role**, name it "Cache operator" (or add the policy to your existing "Web team"
   role: [Role design: the central web team](../../operations/people-and-permissions/role-design-web-team.md)).
2. **New policy**: module `setup`, function `managecache`, **Grant full access** for that function.
3. Optionally `setup` / `system_info`, so they can read **Setup > System information**; its HTTP cache, query cache
   and SQL profile buttons also need `setup/managecache`.
4. **Save**, then **Assign** to the colleague's group.

## What to tell the colleague

- Clear the smallest cache that fixes the problem: content view cache for stale pages, template caches after a
  template change; **All caches** only when unsure ([Clear caches after a template change](../operations/clear-caches-after-a-template-change.md)).
- After a code or settings change the caches must be cleared **after** PHP and Velocity run the new code; that is a
  deploy, not a cache button ([Deploy a PHP change](../operations/deploy-a-php-change.md)).
- Every clear is recorded in the audit trail's `system` channel.

## Check it worked

- Signed in as the colleague, the Setup menu shows **Cache management**, and **Clear** works.
- Other Setup pages (Maintenance, Cronjobs, Ini settings) are not offered.

## Related pages
- [Stale cache](../../operations/incidents/stale-cache.md)
- [Least-privilege principles](../../operations/people-and-permissions/least-privilege-principles.md)
- [Cache clearing and warming (hosting)](../../../hosting/performance/cache-clearing-and-warming.md)
- [Caches guide](../../../../guides/caches.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
