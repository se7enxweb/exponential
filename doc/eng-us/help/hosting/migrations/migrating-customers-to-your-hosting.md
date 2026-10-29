---
title: Migrating customers to your hosting
audience: hosting
category: hosting
order: 20
summary: The end-to-end process a host follows to bring a customer's site onto its Exponential hosting: assessment questions, the offer, a staging copy, the cut-over with maintenance mode and DNS, verification, rollback and aftercare.
keywords: migration, onboarding, customer site, assessment, inventory, staging, cut-over, DNS, maintenance mode, rollback, verification, aftercare, backup, restore
featured: true
type: help
---
# Migrating customers to your hosting

This page is for the migration team of a hosting company: the project lead, the system administrator and the support
contact. It answers: what do you ask before you make an offer, how do you stage the move, how do you switch with the
least downtime, how do you verify, and what if it goes wrong?

## Contents

1. [The phases](#the-phases)
2. [Assessment: what to ask](#assessment-what-to-ask)
3. [The offer](#the-offer)
4. [Staging copy](#staging-copy)
5. [Cut-over](#cut-over)
6. [Verification](#verification)
7. [Rollback](#rollback)
8. [Aftercare](#aftercare)
9. [Responsibilities](#responsibilities)

## The phases

| Phase | Result |
|---|---|
| 1. Assessment | you know the version, size, database, extensions and risks |
| 2. Offer | a scope, a plan and a date the customer accepts |
| 3. Staging | a working copy on your platform, checked by the customer |
| 4. Cut-over | the production site runs on your platform |
| 5. Verification | agreed checks pass |
| 6. Aftercare | the old host is released; backups and monitoring run |

## Assessment: what to ask

| Question | Why it matters | How to find out |
|---|---|---|
| Which version runs? | a 6.0.x site moves; an older one is upgraded | the About page (**Info > About**), or `composer show se7enxweb/exponential` |
| Which database and version? | decides backup and restore tools; maybe an engine change | `[DatabaseSettings]` in `settings/override/site.ini.append.php` ([Which database is my site using?](../../database-support.md#which-database-is-my-site-using)) |
| Which PHP version? | the target must offer it | `php -v` on the old host |
| Which engine serves it? | Velocity, FrankenPHP, Apache or nginx | the old host |
| How large are `var/storage` and the database? | transfer time, plan size | `du -s`, the database tools |
| Which extensions, and are they in Git? | custom code moves too | `settings/override/site.ini.append.php` `[ExtensionSettings]`, the `extension/` directory |
| Clustered storage? | the files may be in the database or a shared mount | `settings/override/file.ini.append.php` |
| Cron jobs, mail, search server? | they must run on your side too | the old crontab, `[MailSettings]`, the search setup |
| Domains, TLS, redirects? | DNS and certificates must be ready | the customer |

## The offer

- Separate the **move** (same version, new servers) from an **upgrade or platform change**; they differ in effort by
  far. See [From older Exponential versions](from-older-exponential-versions.md) and
  [From eZ Platform and Ibexa](from-ez-platform-and-ibexa.md).
- State the content freeze: nothing may be published between the final copy and the switch.
- State who tests what on the staging copy, and who signs it off.

## Staging copy

1. Get a full backup from the old host: the database and `var/storage`, `settings/override`, `settings/siteaccess`
   and custom extensions and designs ([What to back up](../operations/backups-what-to-back-up.md)).
2. Get the code from Git or Composer at the same version, rather than copying the old host's vendor directory
   ([Composer and Git on servers](../basics/composer-and-git-on-servers.md)).
3. Restore the database and files on your platform ([Restore and restore tests](../operations/restore-and-restore-tests.md)).
4. Adjust the settings that name the old host: database connection, site addresses in the siteaccess settings,
   paths, mail transport.
5. Clear every cache and regenerate autoloads:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

6. Give the customer the staging address and a test plan.

## Cut-over

1. Lower the DNS time-to-live of the customer's domains days ahead.
2. Announce the window. On the old host, switch maintenance mode on so nothing changes:

   ```bash
   php bin/php/maintenance.php on --message="Moving to a new server" --until=2h --allow-root-user
   ```

   (`./console exp:maintenance on` is the same; see [Maintenance mode](../operations/maintenance-mode.md)).
3. Take the final database and file backup, transfer it, restore it on your platform.
4. Clear caches; with Velocity, clear its response cache (`php bin/php/console exp:velocity cache clear --apply --allow-root-user`).
5. Test on your platform through a hosts-file entry before DNS changes.
6. Switch DNS. Keep the old host in maintenance mode, not deleted.

## Verification

Use the checks of the installation book: compare row counts of `ezcontentobject`, `ezcontentobject_tree` and
`ezcontentobject_attribute` with the old site, sign in, publish a test object, upload an image, run a search
([Database support](../../database-support.md#moving-a-site-to-another-database)). Then go through the
[go-live checklist](../checklists/new-customer-site-go-live.md).

## Rollback

Until the old host is released, rollback is: switch DNS back and switch maintenance mode off on the old host.
Content published on the new host after the switch is lost in a rollback; say so in the plan, and decide in advance
how long the rollback option stays open.

## Aftercare

- Backups run on your platform, and one has been restored.
- Monitoring and the log viewer are watched ([Monitoring overview](../operations/monitoring-overview.md)).
- Cron jobs run ([Cron jobs](../operations/cron-jobs.md)).
- Mail leaves the server with the right sender ([Mail delivery](../operations/mail-delivery.md)).
- The old host is released only after the agreed period.

## Responsibilities

| Task | Host | Customer |
|---|---|---|
| Backup from the old host | receives | provides or authorizes access |
| Code, database, files on the new platform | does | |
| Test plan and acceptance | supports | does |
| Content freeze | announces | keeps |
| DNS change | does, or instructs | does, if they own DNS |
| Custom extensions that fail on a new PHP version | reports | fixes, or orders the fix |

## Related pages

- [Moving a site between hosts](moving-a-site-between-hosts.md)
- [Changing the database engine](changing-the-database-engine.md)
- [Restore from backup (runbook)](../runbooks/restore-from-backup.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
