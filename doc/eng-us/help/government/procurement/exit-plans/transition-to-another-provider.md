---
title: Transition to another provider, same software
audience: government, project-manager, admin, hosting, partner
category: public-sector
order: 70
summary: How a public body moves its Exponential site to a new hosting provider or support partner without changing the software - roles of the old and new provider, the documented move procedure, settings that name the old machine, the cut-over, and closing the old contract.
keywords: transition, change of provider, change of supplier, hosting move, migration between hosts, cut-over, DNS, handover, offboarding, onboarding
type: help
---
# Transition to another provider, same software

This page is for the project manager who changes the hosting provider or the support partner of an Exponential site
and keeps the software. Because the software is free and the site is ordinary files plus a database, a new provider
can take over the same installation at the same version. The technical procedure is documented in
[Moving a site between hosts](../../../hosting/migrations/moving-a-site-between-hosts.md); this page wraps it in the
project steps a public body needs.

## Contents
1. [Who does what](#who-does-what)
2. [Before the move](#before-the-move)
3. [The move](#the-move)
4. [Settings that name the old machine](#settings-that-name-the-old-machine)
5. [After the move](#after-the-move)
6. [Changing only the support partner](#changing-only-the-support-partner)

## Who does what

| Party | Responsibilities |
|---|---|
| You | exit owner; approves dates; holds your own backup; informs editors; signs off |
| Old provider | delivers the export, settings and code; keeps the site in maintenance mode until DNS has moved; deletes data afterwards ([Customer offboarding](../../../hosting/checklists/customer-offboarding.md)) |
| New provider | prepares the target; restores; tests; takes over monitoring and backups ([New customer site go-live](../../../hosting/checklists/new-customer-site-go-live.md)) |

## Before the move

- [ ] The target meets the requirements, with the same PHP major version or a newer supported one
      ([Requirements at a glance](../../../hosting/basics/requirements-at-a-glance.md)).
- [ ] Same database engine, or a planned change ([Changing the database engine](../../../hosting/migrations/changing-the-database-engine.md)).
- [ ] DNS time-to-live of the site's domains lowered in advance.
- [ ] A test restore of the latest backup on the target has worked once.
- [ ] Editors told of the content freeze window.
- [ ] Your own complete copy taken ([Backing up everything](backing-up-everything.md)).

## The move

The documented order, in short:

1. Maintenance mode on, on the old machine:
   `php bin/php/maintenance.php on --message="Moving to a new server" --until=1h --allow-root-user`.
2. Back up the database with the engine's tool and the files (`var/storage`, `settings/override`, `settings/siteaccess`).
3. On the target: the same tag of the kernel and `composer install`, plus custom extensions from Git.
4. Restore the database and files; set owners and permissions.
5. Adjust the settings listed below.
6. Regenerate autoloads and clear caches:
   `php bin/php/ezpgenerateautoloads.php -e` and `php bin/php/ezcache.php --clear-all --allow-root-user`.
7. Start the engine or reload PHP-FPM.
8. Test through a hosts-file entry, then switch DNS.
9. Leave the old machine in maintenance mode until the DNS change has reached everyone.

## Settings that name the old machine

| Setting | Where |
|---|---|
| Database server, user, password | `settings/override/site.ini.append.php`, `[DatabaseSettings]` |
| Site addresses and siteaccess matching | `settings/override/` and `settings/siteaccess/*/` |
| Mail transport and server | `[MailSettings]` |
| Cluster storage | `settings/override/file.ini.append.php`, if clustered |
| Velocity ports and certificates | `settings/override/velocity.ini.append.php`, if present |

The database password changes at the new host; the old one should be retired.

## After the move

- [ ] The checks of the move page pass; a sample of pages, images and forms works.
- [ ] Sign-in, two-step sign-in and roles work for a test editor.
- [ ] Cron jobs run on the new host ([Cron jobs](../../../hosting/operations/cron-jobs.md)).
- [ ] Monitoring and backups run at the new host; first backup verified.
- [ ] Audit trail continues; `./console exp:audit verify` reports the chain intact.
- [ ] Old provider's staff accounts removed from the administration.
- [ ] Old provider confirms deletion in writing, on the agreed date.

## Changing only the support partner

When the host stays and only the support partner changes:

- give the new partner access with personal accounts and roles, never shared accounts
  ([Roles and policies](../../../../guides/roles-and-policies.md));
- remove the old partner's accounts and keys, and rotate any shared credentials;
- hand over code in Git and the documentation ([Documentation handover](documentation-handover.md));
- ask the new partner to run **Setup > Upgrade check** to see whether shipped files were changed
  ([Upgrade check](../../../../guides/upgrade-check.md)).

## Related pages
- [Exit plans: overview](README.md)
- [Moving a site between hosts](../../../hosting/migrations/moving-a-site-between-hosts.md)
- [Customer offboarding checklist](../../../hosting/checklists/customer-offboarding.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
