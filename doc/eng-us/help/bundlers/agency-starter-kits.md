---
title: Agency starter kits
audience: bundler, developer, project-manager
category: bundling
order: 320
summary: How an agency bundles Exponential, its extensions, a theme and a site package into a starter kit for new projects - the repository layout, Composer, a reviewed kickstart.ini per environment, its own site package, second extension root, ManagedByDeployment, handover to the customer, and keeping kits current.
keywords: starter kit, agency, project template, repository, Composer, kickstart.ini, site package, theme, extension_src, AdditionalExtensionDirectories, ManagedByDeployment, handover, bundle
type: help
---
# Agency starter kits

This page is for agencies that start every customer project from the same base: Exponential plus their own
extensions, a theme and a site package. A starter kit is a bundle whose recipients are your own project teams first
and your customers second.

## Contents

1. [What goes into a kit](#what-goes-into-a-kit)
2. [Repository layout](#repository-layout)
3. [Installing a new project from the kit](#installing-a-new-project-from-the-kit)
4. [Deployment discipline](#deployment-discipline)
5. [Handing a project to the customer](#handing-a-project-to-the-customer)
6. [Keeping the kit current](#keeping-the-kit-current)

## What goes into a kit

| Part | Form | Page |
|---|---|---|
| Exponential at a pinned version | `composer.json` + `composer.lock` | [Composer-based bundles](composer-based-bundles.md) |
| Your extensions | Composer packages from your repositories, or a second extension root | [Adding and removing extensions](adding-and-removing-extensions.md) |
| Your theme | an extension with a design | [Publishing a theme](../partners/publishing-a-theme.md) |
| Your site package | an `.ezpkg` with your classes, base content, roles and settings | [Site packages in a bundle](site-packages-in-a-bundle.md) |
| Install configuration | a `kickstart.ini` template per environment | [kickstart.ini templates](kickstart-ini-templates.md) |
| Web server and Velocity settings | example files | [Web server and engine choices](web-server-and-engine-choices.md) |
| Your documentation | getting started, conventions, handover | [Documentation to ship](documentation-to-ship.md) |

## Repository layout

```text
starter-kit/
    composer.json, composer.lock
    extension_src/            your own extensions (AdditionalExtensionDirectories[])
    packages/                 your site package source and the built .ezpkg
    deploy/
        kickstart.ini.template
        velocity.ini.append.php.example
    docs/
    README.md, LICENSE, NOTICE.txt
```

`extension_src/` follows the layout recommended in
[Additional extension directories](../../bc/6.0/AdditionalExtensionDirectories.md#move-an-extension-to-a-second-root):
"third-party and community extensions" in `extension/`, "project and customer extensions" in `extension_src/`, with
`site.ini [ExtensionSettings] AdditionalExtensionDirectories[]` naming the second root.

## Installing a new project from the kit

```bash
git clone https://git.example.com/agency/starter-kit.git customer-site
cd customer-site
composer install
for f in packages/*.ezpkg; do php ezpm.php import "$f"; done
umask 077
envsubst < deploy/kickstart.ini.template > kickstart.ini
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
rm -f kickstart.ini
```

Then deliver the generated password from `var/log/initial-admin-password` to the project lead and delete the file
([Default credentials and first sign-in](default-credentials-and-first-sign-in.md)).

## Deployment discipline

- Keep `settings/override/` and `settings/siteaccess/` out of version control: they hold credentials
  ([4.4](../../install/04-choosing-an-install-method.md#settings)).
- Sites deployed from version control "can lock their settings with `[ConfigurationSettings]
  ManagedByDeployment=enabled`, which stops the administration from writing settings and templates on the server"
  (shipped `disabled`; [ManagedByDeployment](../../features/6.0/managed-by-deployment.md)).
- Use `config.env.php` with `EXP_ENV` to keep per-environment settings files (`site.ini.test.append.php` next to
  `site.ini.append.php`); copy `config.env.php.example`, and keep the copy out of version control.
- Deploy code changes with the documented order ([Caches and autoloads after building](caches-and-autoloads-after-building.md)).

## Handing a project to the customer

- [ ] The customer has their own administrator accounts; `admin` is retired as a sign-in
      ([Account hygiene](../default-administrator-password.md#account-hygiene)).
- [ ] The customer knows which parts are Exponential (GPL), which are your extensions, and under which license
      those are delivered ([GPL obligations](gpl-obligations-when-distributing.md)).
- [ ] The customer has the source of what you delivered, including your extensions if you distributed them.
- [ ] Support and update responsibilities are written down ([Support boundaries](support-boundaries.md)).
- [ ] Editors have the handover material ([Handing the site over to editors](../creators/website-creators/handing-over-to-editors.md)).

## Keeping the kit current

- Rebuild the kit's lock file at each Exponential release you adopt; test; tag the kit
  ([Versions, tags and pinning](versions-tags-and-pinning.md)).
- Projects started from an older kit update like any installation ([Updates and upgrade paths](updates-and-upgrade-paths.md)).
- Generic fixes go upstream, so the kit does not carry patches forever ([Contributing fixes back](contributing-bundle-fixes-back.md)).

## Related pages

- [Implementation partners](../partners/implementation-partners.md), [Implementation project playbook](../partners/implementation-project-playbook.md)
- [Making your own site package](../site-packages/your-own/README.md)
- [Composer-based bundles](composer-based-bundles.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
