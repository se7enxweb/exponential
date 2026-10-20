---
title: "Tender template: exit and data return clause"
audience: government, project-manager, partner, admin
category: public-sector
order: 160
summary: Clause text for the end of a CMS contract - exit plan, complete data return in open formats, source and documentation hand-over, transition support, secure credential hand-over and deletion - with the Exponential export tools that make each step concrete.
keywords: exit clause, data return, transition, handover, export, open formats, database dump, storage, redirects, credentials, deletion, termination assistance, tender template
type: help
---
# Tender template: exit and data return clause

This page is for procurement officers who want the end of a contract to be routine. The clause applies whether you
move to another supplier with the same software or leave the product. The export tools named below are documented
in [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md). Background:
[Exit and portability requirements](../requirements/exit-and-portability-requirements.md).

**Template** - adapt to your procurement rules; not legal advice

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [The clause](#the-clause)
2. [The return package](#the-return-package)
3. [Verifying the return](#verifying-the-return)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## The clause

```text
E. EXIT AND DATA RETURN

E.1 Exit plan. Within <period> of the start of the contract the Supplier delivers
    an exit plan describing the return package, the steps, the time needed and
    the roles. The plan is updated <yearly>.

E.2 Trigger. The exit starts on expiry, on termination for any reason, or on the
    Customer's request for a return package during the contract.

E.3 Return package. Within <time> of the trigger the Supplier delivers the return
    package defined in <annex>, by <secure transfer method>.

E.4 Formats. Content exports are in open, documented formats with a manifest.
    Database dumps are in the database's standard dump format. Files are in
    their original form.

E.5 Transition support. The Supplier answers the Customer's and the successor's
    questions and attends <number> transition meetings during <period>, at
    <rates / no charge>.

E.6 Continuity. The Supplier keeps the service running at the agreed service
    levels until the Customer confirms the transition, up to <period>.

E.7 Access and secrets. The Supplier hands over all credentials and keys
    securely, after which the Customer rotates them; the Supplier removes its
    accounts and confirms in writing.

E.8 Deletion. After the Customer confirms receipt and completeness, the Supplier
    deletes the Customer's data from all systems and backups within <time> and
    confirms in writing.

E.9 Costs. The return package and E.7 are included in the contract price.
```

## The return package

| Item | Tool or location (Exponential) |
|---|---|
| Full database dump | the database's own tool, or `./console exp:ezsqldumpschema --output-types=all ...` ([Database backups per engine](../../../hosting/operations/database-backups-per-engine.md)) |
| All uploaded files | `var/storage/` and `var/<site>/storage/` |
| Configuration | `settings/override/`, `settings/siteaccess/`, `config.php` and related files; the audit signing keys in `settings/override/audit.ini.append.php` |
| Source | the repository with full history: extensions, designs, `composer.json`, `composer.lock` |
| Content export | xrowextract site archive: `./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>` (CSV per class, `manifest.json`) |
| Layouts | `./console exp:layouts export --all --output=<file.zip>` |
| Form submissions | `./console ext:bccie:export --object=<id> --format=csv` |
| Consent records | `./console exp:mailconsent` (CSV) |
| Audit trail | `var/<site>/log/audit/` with signed manifests; `./console exp:audit export` |
| Address list for redirects | URL alias column of the site archive, plus tables `ezurlalias_ml` and `ezurlwildcard` |
| Documentation | per the documentation clause |

Two facts for the plan: the site archive never contains password hashes, so users of a new product set new
passwords; rich text is stored as XML and must be rendered or transformed into HTML for another product.

## Verifying the return

1. Restore the database and files on a separate environment and sign in to the administration.
2. Run `./console exp:audit verify`; it exits 1 when a chain is broken.
3. Compare the site archive's row counts per class with **Setup > Classes**.
4. Open a sample of files and check that images display.
5. Keep the old site read-only until the new one is signed off.

## Notes for the evaluator

- E.1 makes bidders think about exit before signing; score the quality of the plan.
- E.9 avoids a price for your own data at the moment you have the least leverage.

## Related pages
- [Tender templates](README.md)
- [Warranty of no lock-in clause](warranty-of-no-lock-in-clause.md)
- [Hosting and data location clause](hosting-and-data-location-clause.md)
- [Customer offboarding (for hosts)](../../../hosting/checklists/customer-offboarding.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
