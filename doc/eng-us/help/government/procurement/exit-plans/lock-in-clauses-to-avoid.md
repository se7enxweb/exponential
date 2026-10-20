---
title: Lock-in clauses to avoid, and clauses to ask for
audience: government, project-manager, partner
category: public-sector
order: 110
summary: Contract terms that tie a public body to one supplier of an Exponential site - ownership of custom code, closed extensions, held backups and keys, private forks, exit fees - and model wording that keeps the freedom the GPL and the open export formats already give. Template wording, not legal advice.
keywords: lock-in, vendor lock-in, contract clauses, exit clause, source code ownership, escrow, GPL, intellectual property, data return, termination assistance, portability
type: help
---
# Lock-in clauses to avoid, and clauses to ask for

This page is for procurement officers and contract managers. Exponential's license and export tools make a change of
supplier technically possible; a contract can still take that freedom away. This page lists terms to avoid and gives
model wording to adapt.

> **Template** - adapt to your procurement rules; not legal advice.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [What you start with](#what-you-start-with)
2. [Clauses to avoid](#clauses-to-avoid)
3. [Clauses to ask for](#clauses-to-ask-for)
4. [Model wording](#model-wording)
5. [Checking a draft contract](#checking-a-draft-contract)

## What you start with

- The kernel is under the GNU GPL version 2 or later: anyone may run, study, change and pass it on
  ([Licensing questions](../../../project/licensing-faq.md)).
- Extensions and designs that a supplier distributes to you are derivative works and come under the GPL with their
  source, as the licensing FAQ explains; an agency building for you under contract acts as your agent.
- Content can be exported in plain formats ([Data export formats](data-export-formats.md)).

The contract should confirm these, not narrow them.

## Clauses to avoid

| Clause | Why it locks you in |
|---|---|
| Supplier owns the custom code and grants you only a license to use it | another supplier cannot legally change it |
| Custom extensions delivered without source, or obfuscated | nobody else can maintain them |
| Backups, settings and keys held only by the supplier | you cannot move without its cooperation |
| Changes made in a private fork of the kernel | every upgrade depends on the supplier ([Upgrade and maintenance costs](../total-cost-of-ownership/upgrade-and-maintenance-costs.md)) |
| Data returned "in a format chosen by the supplier" | you may receive something nobody can read |
| Exit assistance at rates not fixed in the contract | the exit price is set when you have no choice |
| Data deleted immediately at termination | you may lose data before you have checked your copy |
| Hosting bundled with support, not separable | you cannot change one without the other |
| Exclusive right to make changes to the site | other suppliers are excluded |

## Clauses to ask for

1. **Ownership or full rights** to custom code, documentation and designs made for you, delivered in Git with
   history, under the GPL where it builds on Exponential.
2. **No changes to shipped kernel files**; customization only through extensions and overrides.
3. **Upstream contribution** of general fixes where possible ([Contributing code back](../../../partners/contributing-code-back.md)).
4. **Regular delivery** of complete backups, settings and keys to you ([Backing up everything](backing-up-everything.md)).
5. **Named export formats** at exit (site archive, database dump, storage, layouts, form data, consent, audit).
6. **Exit assistance** for a fixed period after termination, at fixed rates.
7. **Deletion** only after you confirm receipt, with written confirmation.
8. **Separable services**: hosting, support and development can be ended separately.
9. **Documentation** as a deliverable at each milestone ([Documentation handover](documentation-handover.md)).

## Model wording

```text
1. Rights. All software, templates, designs and documentation created for the Customer under this contract are
   delivered to the Customer with their complete source code. Where they are derivative works of Exponential they are
   licensed to the Customer under the GNU General Public License version 2 or later. <Add ownership terms per your
   rules.>
2. Repository. The Supplier keeps all such code in a Git repository to which the Customer has read access at all
   times, and delivers a complete copy with history at each milestone and at the end of the contract.
3. Kernel. The Supplier makes no changes to files shipped with Exponential; customization is done in extensions
   and design overrides.
4. Data return. On request and at the end of the contract, the Supplier delivers within <n> working days: <list of
   formats from the exit plan>. The Supplier deletes the Customer's data only after the Customer has confirmed
   receipt in writing, and confirms deletion in writing.
5. Exit assistance. For <period> after termination, the Supplier provides exit assistance at <rate>, up to <hours>.
6. Separability. Hosting, support and development services may be terminated separately.
```

## Checking a draft contract

- [ ] Who owns or holds rights to custom code? Is the source delivered?
- [ ] Any private fork or kernel change allowed?
- [ ] Are backups, settings and keys delivered to you?
- [ ] Are export formats named?
- [ ] Are exit assistance rates and duration fixed?
- [ ] Can services be ended separately?
- [ ] Is deletion tied to your confirmation?

## Related pages
- [Exit plans: overview](README.md)
- [Exit plan template](exit-plan-template.md)
- [Evaluation: red flags and pitfalls](../evaluation/red-flags-and-pitfalls.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
