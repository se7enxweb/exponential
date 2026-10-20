---
title: Export audit records for an investigation
audience: government, admin
category: public-sector
order: 160
summary: Step by step - select the audit records an investigation needs, verify the chains, export them as a signed bundle or JSON lines with their sha256, and document the hand-over so the export can serve as evidence.
keywords: audit export, investigation, evidence, signed bundle, jsonl, sha256, exp:audit export, verify, chain of custody, background export, MaxExportRecords, audit/read, public sector
type: help
---
# Export audit records for an investigation

For administrators asked by legal, HR, internal audit or the police for records of what happened on the site. The
method is in [Audit records as evidence](../../records-and-archiving/audit-records-as-evidence.md); this page is the
procedure.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- A written request naming the scope (people, objects, period) and who receives the export.
- Shell access, or `audit/read` for the web export.
- A private working directory, for example `var/tmp/<case>/`, removed after the hand-over.

## Steps

1. If the period is older than 90 days, restore those days per channel:
   `./console exp:audit restore --channel=<channel> --date=<YYYY-MM-DD> --allow-root-user`.
2. Verify: `./console exp:audit verify --allow-root-user` (add `--archives` for archived days). Save the output.
3. Try the selection with `search` first, then export:

   ```bash
   ./console exp:audit search --user=<id> --from=<YYYY-MM-DD> --to=<YYYY-MM-DD> --limit=20 --allow-root-user
   ./console exp:audit export --user=<id> --from=<YYYY-MM-DD> --to=<YYYY-MM-DD> --format=bundle --out=var/tmp/<case>/audit-bundle --allow-root-user
   ./console exp:audit export --object=node:<node id> --format=jsonl --out=var/tmp/<case>/node.jsonl --allow-root-user
   ```

   Each export prints the number of records and the file's sha256.
4. In the browser: Audit > **Export** offers the console's current filter as CSV, JSON lines or JSON; **Export in the
   background** for large selections (a content job; the file appears on the export page for the user who asked).
5. Write the hand-over note: request, scope, commands, sha256 values, verify output, date, recipient.
6. Hand over on agreed media; remove the working directory.

## Check that it worked

- Recompute the sha256 of the file you hand over (`sha256sum <file>`) and compare with the printed value.
- The audit records the export itself as `system.audit.export` with the same sha256.

## Related pages

- [Audit records as evidence](../../records-and-archiving/audit-records-as-evidence.md)
- [Audit guide: Exporting](../../../../bc/6.0/audit.md#exporting)
- [Verify the audit trail](verify-the-audit-trail.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
