---
title: Exit planning
audience: government, project-manager, admin
category: public-sector
order: 210
summary: How to plan, document and test an exit: moving an Exponential site to another provider or host, or migrating away from Exponential, with what to collect, who to involve, and a test that proves the plan works.
keywords: exit plan, exit strategy, change provider, migration, handover, backup, restore test, export, contract end, sovereignty, continuity
type: help
---
# Exit planning

This page is for project managers and administrators. A public body should be able to leave any provider, and in
the end any platform, in an orderly way. With Exponential the license allows it; the plan makes it practical. The
procurement section of this help has templates for exit plans ([Exit plans](../procurement/exit-plans/README.md)).

## Contents

1. [Two kinds of exit](#two-kinds-of-exit)
2. [What to collect](#what-to-collect)
3. [Changing provider or host](#changing-provider-or-host)
4. [Leaving the platform](#leaving-the-platform)
5. [Testing the plan](#testing-the-plan)

## Two kinds of exit

| Exit | Effort | Main risk |
|---|---|---|
| Same platform, other provider or host | low to medium | missing pieces (code, settings, files, accounts) |
| Another platform | high | content conversion, especially rich text, and lost features |

## What to collect

- The code repository with every custom extension and design, and `composer.lock`.
- The settings overrides (`settings/override/`, siteaccess settings), with secrets handed over separately and safely.
- A database dump and the `var/<site>/storage/` files ([Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md)).
- The audit records and their keys, if you must keep them (`settings/override/audit.ini.append.php`).
- The list of cronjobs, mail settings, DNS, certificates and accounts.
- The installation document (see "Keeping the skills in house").

## Changing provider or host

Moving an installation between hosts is documented in the hosting help ([Moving a site between hosts](../../hosting/migrations/moving-a-site-between-hosts.md)).
In short: freeze publishing, take the backup, restore at the new host, switch DNS, check, keep the old host read-only
for a period. The database engine can change in the same move if needed ([Database support](../../database-support.md#moving-a-site-to-another-database)).

## Leaving the platform

1. Inventory the content classes and the features in use (workflows, forms, languages).
2. Export content per class (see "Exporting content and packages") and the files.
3. Plan the conversion of rich text XML to the target format.
4. Keep the addresses: build a redirect table from old URL aliases to new addresses
   ([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md#relaunching-a-site-a-redirect-plan)).
5. Archive what must be kept as records before the old system goes.

## Testing the plan

Once a year, or before a contract renewal: restore the latest backup on a separate machine from the documented
material alone, by someone who did not set up the site. Record the time it took and what was missing, and fix the
documentation.

## Related pages

- [Avoiding vendor lock-in](avoiding-vendor-lock-in.md)
- [Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md)
- [Customer offboarding](../../hosting/checklists/customer-offboarding.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
