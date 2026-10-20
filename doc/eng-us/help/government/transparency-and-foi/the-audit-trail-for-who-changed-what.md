---
title: The audit trail for who changed what
audience: government, admin, project-manager
category: public-sector
order: 150
summary: How a public body uses Exponential's audit trail (on by default, hash-chained, with signed archives) to answer who published, changed, moved or removed content, who changed roles and settings, how to prove the record is intact, and how long records are kept.
keywords: audit trail, audit, who changed what, accountability, tamper-evident, hash chain, verify, exp:audit, audit console, retention, content channel, access channel
type: help
---
# The audit trail for who changed what

This page is for administrators, security officers and managers who must answer "who did that?" and show that the
answer can be trusted. Exponential records security-relevant events in a tamper-evident log, on by default, readable
in the administration and on the command line ([Audit trail](../../../features/6.0/audit-trail.md); full reference:
[the audit guide](../../../bc/6.0/audit.md)).

## Contents

1. [What is recorded](#what-is-recorded)
2. [Finding who changed a page](#finding-who-changed-a-page)
3. [Proving the record is intact](#proving-the-record-is-intact)
4. [How long records are kept](#how-long-records-are-kept)
5. [Who may read the audit](#who-may-read-the-audit)
6. [Privacy in the audit](#privacy-in-the-audit)

## What is recorded

| Channel | Examples |
|---|---|
| `content` | publish, move, copy, remove, hide, sections, states, trash, versions, URL aliases, classes, content jobs |
| `access` | sign-in, failed sign-in, refused view, account lock, role assignment, password reset |
| `system` | settings writes, cache clears, cronjob runs, commands, package installs, maintenance, deploys |
| `commerce` | orders, payments (never card data), VAT, exports and imports |
| `read` | sampled views, searches and downloads, only when reads are switched on |

A subtree removal is one parent event with one child per node, so "who removed this folder and everything in it" is
one answer.

## Finding who changed a page

In the administration: the **Audit** tab (`/audit/dashboard`), then **Console**. Filter by **Channel** `content`,
**Event name** (for example `content.node.remove` or a prefix such as `content.*`), **Object**, **From** and **To**,
**Login** or **User ID**. Each row shows **Time**, **Event**, **Actor**, **Object**, **Result** and **Request**.

On the command line:

```bash
./console exp:audit search --channel=content --name='content.node.*' --from=2026-10-01 --to=2026-10-15
./console exp:audit show <event id>
```

Combine it with the version history of the object, which shows the content before and after (see "Version history
as accountability").

## Proving the record is intact

Each record carries the hash of the record before it; daily files are closed, archived and listed in manifests
signed with an HMAC.

```bash
./console exp:audit verify               # live files; exit 1 when a chain is broken
./console exp:audit verify --archives    # everything
```

**Verify now** in the console does the same for the channels you may read. Run verify before handing records to
anyone (an auditor, a court, an inquiry). If it reports BROKEN: change nothing, copy the directory, and compare with
copies that left the server (syslog, webhook receiver). Records can also go to syslog or a signed webhook as they are
written, so a copy exists outside the server.

## How long records are kept

Shipped: 90 days live and 730 days archived for content, access, system and commerce; 30 and 90 for read
(`settings/audit.ini`, blocks `[AuditChannel_<channel>]`, keys `LiveDays`, `ArchiveDays`). If your records schedule
needs longer, set the values in `settings/override/audit.ini.append.php`, or export records to your archive before
they expire (`./console exp:audit export ...`, CSV or JSON). Back up the override file: it holds the signing and
pseudonym keys.

## Who may read the audit

`audit/read` (optionally limited by channel) and `audit/manage`; only Administrator holds them in a new
installation. A refused attempt is itself recorded.

## Privacy in the audit

Addresses are truncated, sessions hashed, passwords and tokens never recorded; records are pseudonymized after a
period. To answer a data subject access request, `./console exp:audit export --subject-user=<id>` returns the
records by or about one user ([Audit guide 4.12](../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks)).

## Related pages

- [Audit trail](../../../features/6.0/audit-trail.md)
- [Security and audit](../../../guides/security-and-audit.md)
- [Version history as accountability](version-history-as-accountability.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
