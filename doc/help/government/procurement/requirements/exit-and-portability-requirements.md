---
title: Exit and portability requirements
audience: government, project-manager, admin, partner
category: public-sector
order: 140
summary: Requirement wording that keeps a public body free to leave a supplier or the product - complete exports in open formats, files, configuration, source, documentation, redirects and a tested exit - with the export tools Exponential documents.
keywords: exit, portability, lock-in, data export, data return, open formats, CSV, JSON, XML, database dump, packages, redirects, transition, handover, tender, requirements
type: help
---
# Exit and portability requirements

This page is for procurement teams who want to be sure that the organization can leave: change supplier, change
hosting, or move to another product, without losing content or paying a ransom for its own data. It gives
requirement wording and the export tools that Exponential documents in
[Migrating away from Exponential](../../../../install/migration/leaving-exponential.md).

## Contents
1. [Two kinds of exit](#two-kinds-of-exit)
2. [Requirement wording](#requirement-wording)
3. [What Exponential can export](#what-exponential-can-export)
4. [Testing the exit before you need it](#testing-the-exit-before-you-need-it)
5. [Pitfalls](#pitfalls)

## Two kinds of exit

| Exit | What moves | What you need |
|---|---|---|
| **Change supplier, keep Exponential** | the whole installation: code, database, files, settings | source, a database dump, the storage directory, `settings/override/`, documentation |
| **Leave Exponential** | content, files, users (without passwords), addresses | content exports in open formats, a redirect table, rich text as HTML |

Both are easier when written into the contract at the start than negotiated at the end.

## Requirement wording

| Id | Requirement |
|---|---|
| EXI-01 | On request and at the end of the contract, the supplier delivers within <time>: a full database dump, the storage directory with all uploaded files, the complete configuration, the source of all code, and the documentation. |
| EXI-02 | All content can be exported by the customer, without the supplier, in open, documented formats (for example CSV, JSON, XML) with a manifest describing the fields. |
| EXI-03 | The export includes, for every item, its identifiers, its address, its location in the structure, its dates and every field, in every language. |
| EXI-04 | A list of all page addresses, including custom addresses and wildcards, is delivered so redirects can be built on the new system. |
| EXI-05 | Credentials, keys and secrets are handed over securely and then rotated; the supplier's access is removed and confirmed in writing. |
| EXI-06 | The supplier cooperates with the successor for <period> at the rates in <annex>. |
| EXI-07 | An exit test is carried out within <period> of go-live: an export is made and checked against counts in the administration. |
| EXI-08 | After hand-over, the supplier deletes the customer's data from its systems and backups within <time> and confirms it. |

## What Exponential can export

From [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md):

| Data | Tool | Format |
|---|---|---|
| All content of a site | `./console ext:xrowextract:archive --set=everything --format=zip --output=<dir>` | CSV per class, `manifest.json`, `README.txt` |
| One class | `./console ext:xrowextract:csv --class=<identifier> ...` | CSV, XML or JSON with a typed manifest |
| Classes and content for another Exponential | packages | `.ezpkg` ([Packages](../../../../guides/packages.md)) |
| Layouts | `./console exp:layouts export --all --output=<file.zip>` | ZIP |
| Files | `var/<site>/storage/` | the original files |
| The whole database | `./console exp:ezsqldumpschema --output-types=all ...` or the database's own tool | neutral format or SQL |
| Form submissions | `./console ext:bccie:export --object=<id> --format=csv` | CSV |
| E-mail consent records | `./console exp:mailconsent` | CSV |
| Content over HTTP | REST v1/v2 | JSON |

Two facts to plan for: the site archive **never contains password hashes**, so users set new passwords on the target
unless it can verify standard PHP `password_hash()` values; and rich text is stored as XML, which must be rendered or
transformed into HTML for another product.

## Testing the exit before you need it

The training course [Migrating to Exponential](../../../trainers/migration/README.md) has a lesson that runs the
complete export on a training installation and checks it
([Lesson 8.1: Migrating away from Exponential](../../../trainers/migration/module-08-leaving-and-capstone/leaving-exponential.md)).
Use the same steps for EXI-07: make the archive, compare row counts per class with **Setup > Classes**, and confirm
that no password hash is in it.

## Pitfalls

- Accepting "we will export your data" without formats, timing or price.
- Forgetting `settings/override/` and the audit signing keys; without them the audit records can no longer be
  verified ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)).
- Switching off the old site before the new one is signed off; keep it read-only until then.

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Tender template: exit and data return clause](../tender-templates/exit-and-data-return-clause.md)
- [Tender template: warranty of no lock-in](../tender-templates/warranty-of-no-lock-in-clause.md)
- [Exponential's import and export tools](../../../../install/migration/exponential-tools.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
