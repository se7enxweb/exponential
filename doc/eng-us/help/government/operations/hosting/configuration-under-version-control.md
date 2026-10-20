---
title: Configuration under version control
audience: government, hosting, admin, developer
category: public-sector
order: 120
summary: For public-sector IT teams that must control and trace every change to a live site. How to keep Exponential's code and settings in version control, lock the administration against settings changes with ManagedByDeployment, and leave secrets out.
keywords: version control, Git, ManagedByDeployment, configuration management, change control, settings/override, siteaccess settings, composer.lock, deployment, traceability, audit, immutable configuration
type: help
---
# Configuration under version control

This page is for IT teams in public bodies whose change process says that every change to a production system must be
traceable to an approved request. It answers how to keep an Exponential site's code and settings in version control,
how to stop the administration from changing settings on the server behind your process, and what must stay out of
the repository. The feature reference is
[Configuration managed by the deployment](../../../../features/6.0/managed-by-deployment.md).

## Contents

1. [What belongs in version control](#what-belongs-in-version-control)
2. [The problem with settings edited in the browser](#the-problem-with-settings-edited-in-the-browser)
3. [Locking it with ManagedByDeployment](#locking-it-with-managedbydeployment)
4. [Secrets](#secrets)
5. [Traceability](#traceability)
6. [Checklist](#checklist)

## What belongs in version control

| Path | In the repository | Note |
|---|---|---|
| `composer.json`, `composer.lock` | yes | rebuild `vendor/` at the same versions |
| your own extensions and designs | yes | |
| `settings/siteaccess/` | yes | siteaccess settings |
| `settings/override/` | yes, without secrets | see [Secrets](#secrets) |
| environment variants such as `site.ini.test.append.php` | yes | per environment settings ([Environments for development, test and production](environments-dev-test-prod.md)) |
| `config.env.php` | no | names the machine's environment; `.gitignore` keeps it out |
| `var/` | no | runtime data, backed up separately |
| `vendor/` | no | rebuilt by Composer |

How code reaches a server with Composer and Git is on
[Composer and Git on servers](../../../hosting/basics/composer-and-git-on-servers.md).

## The problem with settings edited in the browser

Without a lock, the INI editor, the toolbar and menu editors and the template editor of the administration write
`*.ini.append.php` and `.tpl` files into the release directory. Version control does not see them, and the next
deployment drops them ([Configuration managed by the deployment](../../../../features/6.0/managed-by-deployment.md)).
For a public body this is also an audit finding: a production change that bypassed the change process.

## Locking it with ManagedByDeployment

One switch per siteaccess, off by default, since 6.0.15:

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php
[ConfigurationSettings]
ManagedByDeployment=enabled
```

With it enabled, in web requests:

- the views that write configuration answer like a disabled view (by default a 404): `settings/edit`, the toolbar and
  menu editors, the template editor, the setup wizard, extension activation (`setup/extensions`), the code generators
  and package upload, install, uninstall, create and export;
- menus and links to those views are hidden;
- `eZINI::save()` refuses, which also stops writers the list does not know.

What stays open: read-only views (`settings/view`, `setup/cache`, `setup/info`, the logs), command line scripts and
the deployment itself, including `exp:ini` and `bin/php/*.php`, and key material the code generates on first use (the
audit keys, the mail preference secret). The list of locked views lives in the class, not in an INI file, so a
settings file cannot weaken it. It applies under PHP-FPM, Apache, FrankenPHP and Velocity alike.

Limits: installing a package from the command line still writes to the database, and file permissions on the release
directory remain the stronger measure.

Put the setting itself in version control, never in a file the administration could write. Settings changes then go
through your repository, your review and your deployment:

```bash
./console exp:ini get site.ini/ConfigurationSettings/ManagedByDeployment --allow-root-user
```

## Secrets

Keep database passwords and generated keys out of the repository, or encrypted in it:

| Secret | Where it is |
|---|---|
| database credentials | `site.ini [DatabaseSettings]` in `settings/override/` |
| audit signing and pseudonym keys | `settings/override/audit.ini.append.php` |
| mail preference secret | `settings/override/mailpreferences.ini.append.php` |
| Velocity certificate keys | Velocity's configuration tree |

A common pattern is a small, unversioned settings file per environment for the credentials, deployed from your
secrets store. Back those files up separately ([Keys and secrets in backups](../backups-and-recovery/keys-and-secrets-in-backups.md)).

## Traceability

With code and settings in version control, the history of the repository says what changed, when and in which
reviewed commit. Exponential adds its side: the audit trail records settings writes, cache clears, deploys and package
installs in the `system` channel ([Audit trail](../../../../features/6.0/audit-trail.md)). Together they let you answer
an auditor's "who changed this setting, and was it approved?". The change process itself is described on
[Change management and advisory boards](../upgrades-and-change/change-management-and-advisory-boards.md).

## Checklist

- [ ] Code, extensions, designs and settings are in version control; `var/`, `vendor/` and `config.env.php` are not.
- [ ] Secrets are kept out of the repository or encrypted in it.
- [ ] `ManagedByDeployment=enabled` on every administration and editor siteaccess of production.
- [ ] The release directory is not writable by the web server's user where nothing needs to write it.
- [ ] Deployments run from the repository, not by hand edits on the server.

## Related pages

- [Hosting](README.md) index of this folder
- [Managed by deployment](../upgrades-and-change/managed-by-deployment.md) in the change pages
- [Deploying changes](../../../hosting/operations/deploying-changes.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
