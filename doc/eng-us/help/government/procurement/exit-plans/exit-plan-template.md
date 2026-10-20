---
title: Exit plan template for an Exponential site
audience: government, project-manager, admin, partner
category: public-sector
order: 30
summary: A copy-and-fill exit plan for a public website or intranet on Exponential - inventory, deliverables with formats, roles, timeline, verification, personal data rules, redirects and sign-off - to attach to the contract and test once a year.
keywords: exit plan template, exit strategy, handover plan, transition plan, contract annex, deliverables, data export, verification, sign-off
type: help
---
# Exit plan template for an Exponential site

This page is for the project manager who writes the exit plan, usually as an annex to the contract with the
implementation, hosting or support supplier. Copy the blocks into your document and fill the blanks. The deliverables
and their formats come from [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)
and [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md).

> **Template** - adapt to your procurement rules; not legal advice.

## Contents
1. [Part 1 - Scope and roles](#part-1---scope-and-roles)
2. [Part 2 - Inventory](#part-2---inventory)
3. [Part 3 - Deliverables](#part-3---deliverables)
4. [Part 4 - Timeline](#part-4---timeline)
5. [Part 5 - Verification](#part-5---verification)
6. [Part 6 - Personal data and deletion](#part-6---personal-data-and-deletion)
7. [Part 7 - Sign-off](#part-7---sign-off)
8. [Testing the plan](#testing-the-plan)

## Part 1 - Scope and roles

```text
System:            <site names and addresses>          Exponential version: <...>
Contract:          <reference>, ends <date>
Exit trigger:      end of contract | termination | change of system | supplier failure
Exit owner (us):   <role>        Supplier exit lead: <role>        Receiving party: <role or new supplier>
Data protection:   <role>        Security: <role>                  Records: <role>
```

## Part 2 - Inventory

Keep this current; review it yearly.

```text
Sites and siteaccesses:          <list>                     (Setup > System information shows them)
Languages:                       <list>
Content classes:                 <count, list>             (Setup > Classes)
Active extensions:               <list, with versions>      (Setup > Extensions)
Custom extensions and designs:   <list, Git repositories>
Integrations:                    <systems, direction, protocol>
Database engine and version:     <...>
Engine:                          Velocity | Apache/nginx with PHP-FPM | FrankenPHP
Hosting:                         <provider, location of data and backups>
Scheduled jobs:                  <cron entries>             (Setup > Cronjobs)
Third-party services:            <analytics, captcha, maps, mail, ...>
```

## Part 3 - Deliverables

| # | Deliverable | Format | Source |
|---|---|---|---|
| D1 | Site archive of all content | xrowextract archive: CSV per class, `manifest.json`, `README.txt` (ZIP or TAR) | [Data export formats](data-export-formats.md) |
| D2 | Full database dump | engine's dump tool, or `exp:ezsqldumpschema` | [Database backups per engine](../../../hosting/operations/database-backups-per-engine.md) |
| D3 | Files | `var/storage/` and `var/<site>/storage/` | [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md) |
| D4 | Settings | `settings/override/`, `settings/siteaccess/` (private transfer) | [Keys and secrets handover](keys-and-secrets-handover.md) |
| D5 | Code | Git repositories of custom extensions and designs, with history | [Documentation handover](documentation-handover.md) |
| D6 | Layouts (if used) | `exp:layouts export` ZIP | [Leaving Exponential](../../../../install/migration/leaving-exponential.md) |
| D7 | Form submissions | `ext:bccie:export` CSV | same |
| D8 | Consent records | `exp:mailconsent` CSV | same |
| D9 | Audit trail | audit directory with signed manifests; `exp:audit` export | [Audit trail](../../../../features/6.0/audit-trail.md) |
| D10 | Address list for redirects | from the archive plus `ezurlalias_ml`, `ezurlwildcard` | [URL redirects after exit](url-redirects-after-exit.md) |
| D11 | Documentation | content model, settings, integrations, runbooks | [Documentation handover](documentation-handover.md) |
| D12 | Versions note | Exponential, PHP, database, engine, extensions | [Customer offboarding](../../../hosting/checklists/customer-offboarding.md) |

## Part 4 - Timeline

```text
T-<n> weeks   exit plan reviewed; receiving party named
T-<n> weeks   test export delivered and verified (D1-D3)
T-<n> days    content freeze announced to editors
T             maintenance mode on; final export D1-D12; maintenance off (or site handed over)
T+<n> days    receiving party confirms completeness in writing
T+<n> days    supplier deletes data as agreed and confirms in writing
```

## Part 5 - Verification

- [ ] `manifest.json` of D1 opened; row counts per class match the counts in the administration.
- [ ] A sample of files from D3 opens; images referenced in D1 exist in D3.
- [ ] D2 restored once on a test machine; the site starts ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md)).
- [ ] D9: the audit chain verifies (`./console exp:audit verify`).
- [ ] D5 builds; no file is missing compared with production.

## Part 6 - Personal data and deletion

- Which personal data goes to the receiving party, and on which legal basis: <...>
- Password hashes: not in the site archive; users reset passwords on the target unless agreed otherwise
  ([Personal data at exit](personal-data-at-exit.md)).
- Supplier deletes: production, staging, backups, logs - on <date>, confirmed in writing.

## Part 7 - Sign-off

```text
Exit completed: <date>     Our exit owner: <name, signature>     Supplier: <name, signature>
Open items: <list>
```

## Testing the plan

Run Parts 3 and 5 once a year on a copy, with the supplier. An export that was never tested is a hope, not a plan.

## Related pages
- [Exit plans: overview](README.md)
- [Why plan the exit](why-plan-the-exit.md)
- [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
