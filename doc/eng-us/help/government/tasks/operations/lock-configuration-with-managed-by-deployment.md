---
title: Lock configuration with ManagedByDeployment
audience: government, admin, developer
category: public-sector
order: 340
summary: Step by step - switch on ManagedByDeployment for the production administration siteaccess so settings, templates, extensions and packages can only change through version control, and check what is locked and what stays open.
keywords: ManagedByDeployment, configuration lock, change control, settings editor, template editor, packages, version control, deployment, production, siteaccess
type: help
---
# Lock configuration with ManagedByDeployment

This task is for administrators who want every configuration change on production to pass version control and the
change board. Background: [Configuration managed by deployment](../../operations/upgrades-and-change/managed-by-deployment.md).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Pitfalls](#pitfalls)
5. [Related pages](#related-pages)

## Before you start

- Settings and templates of the site are in version control, and changes reach production by deployment.
- Nobody relies on the browser's settings or template editor on production (move that work to test).
- A change approved for the lock itself.

## Steps

1. In version control, add to the production administration siteaccess's settings (never written from the admin):

   ```ini
   # settings/siteaccess/<admin siteaccess>/site.ini.append.php
   [ConfigurationSettings]
   ManagedByDeployment=enabled
   ```

   To lock only production while test stays editable, put it in the production variant file
   (`site.ini.prod.append.php`, with `EXP_ENV=prod`) ([Set up environment-specific settings](set-up-environment-specific-settings.md)).
   Lock every siteaccess an administrator can use.
2. Deploy, then `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` and reload
   (`./console exp:velocity deploy --allow-root-user`).

## Check it worked

- **Setup > Ini settings** shows settings but offers no edit; `/settings/edit` answers like a disabled view (404 by
  default).
- **Setup > Extensions** offers no activation; package upload and install are gone; the template editor is not offered.
- **Setup > Cache management**, **System information** and **Log files** still work.
- On the shell, `./console exp:ini` and `bin/php/*.php` still work (the deployment's tools).

## Pitfalls

- Roles, workflows, sections and states are data in the database: not locked; govern them with roles and the audit trail.
- Installing a package from the command line still writes to the database.
- File permissions on the release directory remain the stronger guard
  ([Check file and directory permissions](check-file-and-directory-permissions.md)).

## Related pages
- [Change management and advisory boards](../../operations/upgrades-and-change/change-management-and-advisory-boards.md)
- [Configuration under version control](../../operations/hosting/configuration-under-version-control.md)
- [Managed by deployment (feature)](../../../../features/6.0/managed-by-deployment.md)
- [Restricting the administration (hosting)](../../../hosting/security/restricting-the-administration.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
