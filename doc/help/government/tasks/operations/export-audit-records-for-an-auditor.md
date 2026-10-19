---
title: Export audit records for an auditor
audience: government, admin, project-manager
category: public-sector
order: 200
summary: Step by step - export audit trail records for an internal or external auditor as CSV, JSON lines or JSON from Audit > Export or with exp:audit export, with a verification result attached and the export handled as personal data.
keywords: audit export, auditor, evidence, CSV, JSON, exp:audit export, Audit Export, Download now, Export in the background, verification, personal data
type: help
---
# Export audit records for an auditor

This task is for administrators and audit owners who hand evidence to an auditor, an investigator or a records
officer. At the end the auditor has the records they asked for, with proof the trail was intact.

## Contents
1. [Before you start](#before-you-start)
2. [In the administration](#in-the-administration)
3. [On the shell](#on-the-shell)
4. [The evidence package](#the-evidence-package)
5. [Related pages](#related-pages)

## Before you start

- The request: period, channels (`access`, `content`, `system`, `commerce`), users or objects, format.
- `audit/read` for the channels (the **Export** page), or shell access.

## In the administration

1. **Audit** tab, **Console**: set the filter (channel, event names, user, time range, text).
2. **Export** (`/audit/export`): the console's filter as CSV, JSON lines or JSON.
3. **Download now** streams the file at once; **Export in the background** runs a content job for a large filter, and
   the file is offered on the export page when done, only to the user who asked.

## On the shell

```bash
./console exp:audit export --channel=access --from=2026-09-01 --to=2026-10-01 --format=csv --out=var/tmp/evidence/access.csv --allow-root-user
./console exp:audit export --name='access.*' --from=2026-09-01 --format=bundle --out=var/tmp/evidence/bundle --allow-root-user
./console exp:audit verify --allow-root-user > var/tmp/evidence/verify-result.txt
```

Each export prints the number of records and the SHA-256 of the file. The `bundle` format writes `records.jsonl`
with a signed `manifest.json`, which suits evidence that must be checked later. `export` takes the same filters as
`search` (`--channel`, `--name`, `--user`, `--login`, `--object`, `--result`, `--from`, `--to`, `--query` ...)
([the audit guide, section 3.3](../../../../bc/6.0/audit.md#33-the-command-expaudit)). For a data-subject access
request, `--subject-user=<id>` returns every record by or about that user.

## The evidence package

| Item | Why |
|---|---|
| The export file | the records |
| The filter used, the date and who exported | reproducibility |
| The `verify` output of the same day | integrity |
| A note on what is not recorded (reads only when switched on; masked addresses) | interpretation |

Exports contain user names and may contain other personal data: send them through a protected channel, keep them only
as long as needed, and record the handover. The export itself is an audit event.

## Related pages
- [Verify the audit trail](verify-the-audit-trail.md)
- [Role design: auditors](../../operations/people-and-permissions/role-design-auditors.md)
- [Give an auditor read access to the audit trail](../people-and-permissions/give-an-auditor-read-access-to-the-audit-trail.md)
- [Audit trail feature](../../../../features/6.0/audit-trail.md#exports-and-the-channels-of-extensions)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
