---
title: Extension updates
audience: government, admin, developer
category: public-sector
order: 80
summary: Keeping the extensions of a public-sector Exponential site current - finding what runs and what is outdated, updating with a dry run through ezupdate or Composer, third-party extensions, and the checks after an update.
keywords: extensions, extension updates, ezupdate, Updates and packages, composer outdated, dry run, third-party extensions, extension versions, ezinfo.php, extension.xml, checkclasses, supply chain
type: help
---
# Extension updates

This page is for administrators and developers who maintain the extensions of a public website. Extensions are
Composer packages and directories under `extension/`; each carries its version in `ezinfo.php` and `extension.xml`.
The tools are **Setup > Extensions** ([The Extensions page](../../../../guides/extensions-page.md)) and **Setup >
Updates and packages**, provided by the ezupdate extension ([ezupdate](../../../../features/6.0/extensions/ezupdate.md)).

## Contents
1. [Know what you run](#know-what-you-run)
2. [Find what is outdated](#find-what-is-outdated)
3. [Update under change control](#update-under-change-control)
4. [Third-party extensions](#third-party-extensions)
5. [Checks after an update](#checks-after-an-update)
6. [Related pages](#related-pages)

## Know what you run

- **Setup > Extensions** lists every extension on disk with its loading order, version and activation.
- **Setup > Updates and packages**, **Installed packages** (`/update/installed`) compares `composer.json`,
  `composer.lock`, `vendor/composer/installed.json` and the extension directories, and names what does not agree;
  JSON and CSV downloads give you a software inventory.
- Keep the list in your configuration record with versions and the reason each extension is active.

## Find what is outdated

**Check for updates** on the ezupdate overview runs `composer outdated` and changes nothing. On the shell:

```bash
php extension/ezupdate/bin/php/ezupdate.php --help
```

Read each extension's changelog between your version and the new one
([Extension changelogs](../../../../../changelogs/extensions/README.md)).

## Update under change control

1. Raise a change ([Change management and advisory boards](change-management-and-advisory-boards.md)); minor
   extension updates may be a standard change in your process, new extensions a normal change.
2. On staging: **Preview (dry run)** in ezupdate shows what Composer would do; then **Install updates**, which asks for
   a backup confirmation and runs in the background with Composer's output on the run page.
3. Or with Composer in your deployment pipeline, committing the new `composer.lock`.
4. Deploy the same `composer.lock` to production. With [ManagedByDeployment](managed-by-deployment.md) on production,
   updating and installing through the browser are locked there by design.

## Third-party extensions

Extensions from outside the project (and those that call outside services, such as captcha or map services) need the
same review as any software you procure: license, maintainer, last release, what data leaves your site. The privacy
standard notes that third-party extensions are off by default ([Privacy standard](../../../../standards/privacy.md)).
Keep a short assessment per extension.

## Checks after an update

```bash
php bin/php/ezpgenerateautoloads.php -e
./console exp:checkclasses --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
./console exp:velocity deploy --dry-run --allow-root-user
```

Then **Setup > Upgrade check**: extensions with their own manifest are checked too
([Upgrade check, extension manifests](../../../../guides/upgrade-check.md#extension-manifests)); **Check database
consistency** reports tables an updated extension expects. Look at **Setup > Log files**, **Error digest** after a day.

## Related pages
- [Reading release notes](reading-release-notes.md)
- [Automated updates with ezupdate (hosting)](../../../hosting/operations/automated-updates-with-ezupdate.md)
- [Extension loading order](../../../../features/6.0/extension-loading-order.md)
- [Extensions guide](../../../../guides/extensions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
