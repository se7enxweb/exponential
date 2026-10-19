---
title: Why plan the exit before you sign
audience: government, project-manager
category: public-sector
order: 20
summary: Why a public body should plan how it leaves a supplier or a CMS before it signs the contract - the risks of not doing so, what Exponential's open formats and license make possible, what still needs contract terms, and when to review the exit plan.
keywords: exit plan, exit strategy, vendor lock-in, supplier change, continuity, portability, open source, contract end, transition, reversibility
type: help
---
# Why plan the exit before you sign

This page is for project managers and procurement officers. Every contract ends, and every system is replaced one
day. An exit plan written before signing costs little; one improvised at the end of a contract costs time, money
and sometimes data. This page explains why, what Exponential already makes possible, and what still has to be agreed
with suppliers.

## Contents
1. [What can go wrong without a plan](#what-can-go-wrong-without-a-plan)
2. [What Exponential makes possible](#what-exponential-makes-possible)
3. [What still needs contract terms](#what-still-needs-contract-terms)
4. [Two kinds of exit](#two-kinds-of-exit)
5. [When to review the exit plan](#when-to-review-the-exit-plan)

## What can go wrong without a plan

| Situation | Consequence |
|---|---|
| The supplier holds the only copy of custom code | you cannot hand the site to anyone else |
| Backups do not include the generated keys | audit records can no longer be verified; mail unsubscribe links break ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)) |
| Nobody knows the content model or settings | a new supplier must rediscover the site |
| The host deletes data at contract end before you have it | data loss |
| Old addresses are not redirected | broken links from other sites, search engines and printed material |
| Personal data is exported without rules | a data protection breach |

## What Exponential makes possible

- **The license.** The GNU GPL version 2 or later lets anyone run, study and change the software, so another
  supplier can take over the same installation ([Licensing questions](../../../project/licensing-faq.md)).
- **Open storage.** "Your content is yours. Exponential stores it in an open database schema and ordinary files, and
  has tools that write it out in plain formats" ([Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)).
- **Export tools.** A self-describing site archive, packages, database dumps, layouts, form data, consent records and
  the audit log can all be exported (see [Data export formats](data-export-formats.md)).
- **Documented moves.** Moving the same site to another host is documented step by step
  ([Moving a site between hosts](../../../hosting/migrations/moving-a-site-between-hosts.md)).

## What still needs contract terms

The software makes exit possible; only the contract makes it happen:

- delivery of the source code of templates and extensions you paid for;
- delivery of complete backups, settings and keys, on request and at the end;
- documentation of the content model, settings and integrations;
- exit assistance: hours, rates and duration after the contract ends;
- timing of data deletion by the supplier, after you confirmed receipt.

See [Lock-in clauses to avoid](lock-in-clauses-to-avoid.md).

## Two kinds of exit

| Exit | You keep | Pages |
|---|---|---|
| Change of supplier or host, same software | the site as it is | [Transition to another provider](transition-to-another-provider.md) |
| Change of system | the content, users and addresses | [Transition to another system](transition-to-another-system.md) |

Most exits are the first kind. Plan for both.

## When to review the exit plan

- At contract signature (the plan is an annex).
- After go-live, once the real backup and export have been tested.
- Every year, and after any major change (new integration, new site, new extension).
- Before the contract ends, early enough to run a new tender in the time your procurement rules require.

## Related pages
- [Exit plans: overview](README.md)
- [Exit plan template](exit-plan-template.md)
- [Procurement: overview](../README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
