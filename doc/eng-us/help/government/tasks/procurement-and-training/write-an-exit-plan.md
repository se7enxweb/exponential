---
title: Write an exit plan
audience: government, project-manager, admin
category: public-sector
order: 190
summary: Step by step, write the exit plan for an Exponential site before signing a contract - scenarios, what is exported in which format, who does what in the handover, keys and access, timing, and how the plan is tested - using the exit-plan template.
keywords: exit plan, write, contract, handover, export, transition, keys, access, timing, lock-in, procurement task
type: help
---
# Write an exit plan

**Who:** the contract manager with the site administrator. **When:** before signing; update it every year. Template:
[Exit plan template](../../procurement/exit-plans/exit-plan-template.md).

> **Note** This page describes features and common practice; it is not legal advice. Have the exit clauses reviewed by
> your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Result](#result)
4. [Pitfalls](#pitfalls)

## Before you start

- Read why the exit is planned at the start ([Why plan the exit](../../procurement/exit-plans/why-plan-the-exit.md)).
- Know the export tools ([Data export formats](../../procurement/exit-plans/data-export-formats.md),
  [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)).

## Steps

1. **Name the scenarios**: change of supplier, change of host, leaving Exponential, the supplier stops trading.
2. **List the data and its format** for each scenario:
   - site archive per class (CSV, XML or JSON with a manifest), via xrowextract;
   - the storage directory (original files);
   - a database dump;
   - layouts, form submissions, consent records, audit records where needed.
3. **List the non-data handover**: code repository, documentation, settings, runbooks
   ([Documentation handover](../../procurement/exit-plans/documentation-handover.md)).
4. **List keys and access**: administrator accounts, server and panel access, DNS, audit signing keys, the e-mail
   preferences secret, TLS keys ([Keys and secrets handover](../../procurement/exit-plans/keys-and-secrets-handover.md)).
5. **Set roles and timing**: who exports, who checks, the handover period, the overlap with the successor.
6. **Cover personal data**: what is transferred, what is deleted, confirmation of deletion by the supplier
   ([Personal data at exit](../../procurement/exit-plans/personal-data-at-exit.md)).
7. **Cover addresses**: a redirect table from URL aliases and wildcards ([URL redirects after exit](../../procurement/exit-plans/url-redirects-after-exit.md)).
8. **Plan the test**: an export drill once a year ([Run an exit drill](run-an-exit-drill.md)).
9. **Annex it** to the contract, with the clause that obliges the supplier to cooperation
   ([Exit and data return clause](../../procurement/tender-templates/exit-and-data-return-clause.md)).

## Result

A short, concrete plan annexed to the contract, which a successor could follow.

## Pitfalls

- A plan that says "the supplier will hand over the data" without formats or a check.
- Forgetting keys that cannot be recreated.
- Never testing the export.

## Related pages

- [Procurement and training tasks](README.md)
- [Exit plans](../../procurement/exit-plans/README.md)
- [Exit readiness](../../checklists/exit-readiness.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
