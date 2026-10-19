---
title: Managed hosting
audience: hosting
category: hosting
order: 90
summary: What a managed Exponential hosting service can take over from the customer, with the tools the software provides for each task (updates with ezupdate, the upgrade check, backups in maintenance mode, system information, log viewer, cronjob page, audit trail, configuration managed by deployment), a responsibility matrix to adapt, and what 7x itself offers.
keywords: managed hosting, managed service, responsibilities, updates, ezupdate, upgrade check, backups, maintenance mode, monitoring, system information, log viewer, cronjobs, audit trail, ManagedByDeployment, security, 7x
type: help
---
# Managed hosting

This page is for hosting companies that want to offer **managed** Exponential hosting, where the host does not only
provide the machine but also runs the site's platform for the customer: updates, backups, monitoring, security. It
answers which tasks such a service usually takes over, which tool in Exponential supports each one, how to write down
who does what, and what the project's steward 7x offers itself. It names no prices or service levels: those are
choices a host makes ([SLA options](../business/sla-options.md)).

![Setup, System information: the PHP, database and web server a managed host keeps up to date](../../images/hosting/system-info-managed.webp)

## Contents

1. [What "managed" can cover](#what-managed-can-cover)
2. [The tools for each task](#the-tools-for-each-task)
3. [A responsibility matrix](#a-responsibility-matrix)
4. [Keeping the customer's changes and yours apart](#keeping-the-customers-changes-and-yours-apart)
5. [Access for your staff](#access-for-your-staff)
6. [7x as a managed host](#7x-as-a-managed-host)
7. [Checklist](#checklist)

## What "managed" can cover

| Area | Unmanaged | Managed (options) |
|---|---|---|
| Machine, operating system, PHP | host or customer | host |
| Web server or Velocity, certificates | customer | host |
| Exponential and extension updates | customer | host, on an agreed schedule and with a test step |
| Database and file backups, restore tests | customer | host |
| Monitoring and log review | customer | host |
| Security hardening and incident response | customer | host, with the customer for content and users |
| Content, users, roles | customer | customer |
| Custom code (extensions, designs) | customer or their agency | customer or their agency; the host deploys it |

## The tools for each task

| Task | Tool in Exponential | Where it is described |
|---|---|---|
| Updates and new packages | **ezupdate**: **Setup > Updates and packages** (`/update/dashboard`); "a check that changes nothing, a dry run, a backup confirmation, and Composer's own output as it runs"; also on the command line (`php extension/ezupdate/bin/php/ezupdate.php`) | [ezupdate](../../../features/6.0/extensions/ezupdate.md), [Automated updates with ezupdate](../operations/automated-updates-with-ezupdate.md) |
| Before an upgrade | **Setup > Upgrade check** (`/setup/systemupgrade`): file and database consistency, read-only | [The upgrade check](../../../guides/upgrade-check.md) |
| Upgrading | the update files and upgrade steps of the release | [Chapter 11](../../../install/11-upgrading.md), [Upgrading customer sites](../operations/upgrading-customer-sites.md) |
| Backups | maintenance mode, the database tool of the engine, `tar` of the files | [10.9 Backups and restore](../../../install/10-after-installing.md#109-backups-and-restore) |
| Taking the site offline | **Setup > Maintenance**, or `php bin/php/maintenance.php on ... / off / status` | [Maintenance mode](../../../guides/maintenance-mode.md) |
| Health | **Setup > System information** (`/setup/info`) and `exp:system:info` | [System information](../../../guides/system-information.md) |
| Logs | **Setup > Log files** (`setup/logs`), with permalinks; rotation | [Log viewer](../../../features/6.0/log-viewer.md), [Log viewer and rotation](../operations/log-viewer-and-rotation.md) |
| Scheduled jobs | **Setup > Cronjobs**, which runs parts and proposes crontab lines | [Cronjobs](../../../guides/cronjobs.md) |
| Who did what | the audit trail | [The audit trail](../../../features/6.0/audit-trail.md), [Audit trail for hosts](../security/audit-trail.md) |
| Deploying code changes | `exp:velocity deploy` (autoloads, caches, PHP-FPM reload, Velocity restart in order) | [8.3.14](../../../install/08-serving-the-site.md#8314-deploying-a-change-expvelocity-deploy) |
| Performance checks | `exp:benchmark` | [Benchmark](../../../features/6.0/benchmark.md) |

> **Note**
> A managed backup is the database **and** `var/storage`, `settings/override` and `settings/siteaccess`, taken at
> the same moment, stored off the server, and restored once on another machine to prove it works. The first-week
> checklist of the installation book asks for exactly that
> ([10.15](../../../install/10-after-installing.md#1015-a-checklist-for-the-first-week)).

## A responsibility matrix

Write one per customer and keep it with the contract. A starting point:

```text
Customer: <customer>     Site: <domain>     Plan: <plan name>     Reviewed: <date>

Task                                   Host   Customer   Agency   Notes
Operating system and PHP updates        R                          window: <when>
Exponential and extension updates       R      A                   test on staging first
Custom extensions and designs                  A          R        host deploys
Backups (database + files)              R                          kept: <generations>
Restore test                            R      I                   last: <date>
Certificates                            R
Monitoring and alerts                   R      I
Security incidents                      R      I          C
Content, users and roles                       R

R = does it, A = approves, C = consulted, I = informed
```

## Keeping the customer's changes and yours apart

On a managed installation, settings changed in the administration and settings deployed by you can contradict each
other. Without precautions, "the INI editor, the toolbar and menu editors and the template editor write
`*.ini.append.php` and `.tpl` files into the release directory, where version control does not see them and the next
deployment drops them" ([Configuration managed by the deployment](../../../features/6.0/managed-by-deployment.md)).

Two options:

- **`ManagedByDeployment=enabled`** per siteaccess (`[ConfigurationSettings]`): the configuration comes from the
  deployment only; the writing views answer as if switched off, and `eZINI::save()` refuses. Read-only views, the logs
  and the command line stay open. This suits a host that deploys every configuration change.
- **Leave it off** and agree that configuration changes in the browser are the customer's; back up `settings/` and
  compare before every deployment.

## Access for your staff

- Give each member of your staff their own administrator account, never a shared one, so the audit trail names the
  person ([Restricting the administration](../security/restricting-the-administration.md)).
- Use two-factor sign-in for those accounts ([Two-factor sign-in](../security/two-factor-sign-in.md)).
- Shell access as the site's user, not as root, so files your staff create stay writable for the site
  ([8.7.2](../../../install/08-serving-the-site.md#872-one-user-or-one-group)).

## 7x as a managed host

7x, the steward of the project, "sells services around the open source software; the software itself stays free".
Its offer includes "managed hosting: 'Exponential Website Hosting' and 'Exponential Platform SSD Hosting'", support
and maintenance contracts and upgrades from older versions. "Prices and terms are on the 7x website, not here."
([Who is 7x?](../../who-is-7x.md#services-and-hosting))

Exponential is free software: any host may offer managed Exponential hosting, and a customer may move between hosts.
See [Working with the project and 7x](../business/working-with-the-project-and-7x.md) and
[Licensing for hosts](../business/licensing-for-hosts.md).

## Checklist

- [ ] The tasks the service takes over are written down per plan.
- [ ] A responsibility matrix exists for every customer.
- [ ] Updates: ezupdate installed, check and dry run before every update, a staging step.
- [ ] Backups with maintenance mode, off-site, restore tested.
- [ ] Monitoring: system information, logs and cron output reviewed on a schedule.
- [ ] `ManagedByDeployment` decided per siteaccess.
- [ ] Personal staff accounts with two-factor sign-in.

## Related pages

- [Choosing a hosting model](choosing-a-hosting-model.md)
- [Support tiers](../business/support-tiers.md), [SLA options](../business/sla-options.md)
- [Checklist: monthly operations](../checklists/monthly-operations.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
