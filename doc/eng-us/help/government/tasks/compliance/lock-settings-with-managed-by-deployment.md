---
title: Lock settings with ManagedByDeployment
audience: government, admin, developer
category: public-sector
order: 360
summary: Step by step - switch on ManagedByDeployment for the production administration siteaccess so the web interface cannot change settings, templates, extensions or packages, after moving those settings into version control, and check the lock.
keywords: ManagedByDeployment, configuration lock, change management, settings editor, template editor, setup wizard, packages, RAD, version control, production, exp:ini, public sector
type: help
---
# Lock settings with ManagedByDeployment

For administrators and developers of public bodies with change management. With the switch on, the administration can no
longer write `*.ini.append.php` or `.tpl` files, activate extensions, run the setup wizard or code generators, or
install packages ([Configuration managed by the deployment](../../security/managed-by-deployment.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- Every setting and template the administration changed on the server is in version control. Compare the server's
  `settings/siteaccess/` and design folders with the repository first.
- Secrets stay in `settings/override/`, outside version control.
- The command line keeps working (`exp:ini`, `bin/php/*.php`); administrators need a change process for settings.

## Steps

1. In the administration siteaccess's versioned `site.ini.append.php` (or an extension's versioned settings):

   ```ini
   [ConfigurationSettings]
   ManagedByDeployment=enabled
   ```

2. Deploy as usual; clear the INI cache and reload PHP (or `./console exp:velocity deploy --allow-root-user`).

## Check that it worked

- **Setup > Ini settings** shows values, with no edit links.
- `/setup/rad`, `/setup/extensions` and the template editor answer like a switched-off view (404 by default).
- Packages: the list offers no upload or install.
- A setting write from the debug bar is refused.

Keys the code generates on first use (audit keys, mail preference secret) are still written.

## Related pages

- [Configuration managed by the deployment](../../../../features/6.0/managed-by-deployment.md)
- [Deploying](../../../../guides/deploying.md)
- [File permissions and ownership](../../security/file-permissions-and-ownership.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
