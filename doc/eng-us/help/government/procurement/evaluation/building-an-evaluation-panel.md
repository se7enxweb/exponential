---
title: Building an evaluation panel for a CMS tender
audience: government, project-manager
category: public-sector
order: 50
summary: Who should sit on the panel that evaluates a website or intranet CMS, which part of the evidence each member checks, how to prepare them with the Exponential documentation, and the rules that keep the panel's work fair.
keywords: evaluation panel, evaluation team, evaluators, roles, moderator, subject matter expert, conflict of interest, tender, procurement
type: help
---
# Building an evaluation panel for a CMS tender

This page is for the project manager who assembles the people who will evaluate bids for a content management
system. It lists the roles a panel needs, the part of the evidence each role checks, and how to prepare each member
with pages of this documentation. It describes common practice; your procurement rules decide the formal roles.

## Contents
1. [The roles a panel needs](#the-roles-a-panel-needs)
2. [Which evidence each role checks](#which-evidence-each-role-checks)
3. [Preparing the panel](#preparing-the-panel)
4. [Rules for a fair evaluation](#rules-for-a-fair-evaluation)
5. [A panel charter template](#a-panel-charter-template)

## The roles a panel needs

| Role | Brings | Typical member |
|---|---|---|
| Chair or moderator | process, timing, consensus; usually does not score | procurement officer |
| Editorial lead | how editors will work every day | head of web or communications |
| Accessibility specialist | judges accessibility evidence | accessibility officer, or an external expert |
| Security specialist | judges security evidence and hosting | information security officer |
| Data protection specialist | privacy and records | data protection officer, records manager |
| Technical lead | architecture, hosting, integration, exit | IT architect or operations lead |
| Language lead (when relevant) | official languages, translation workflow | translation service |
| User representative | the view of citizens or staff using the site | service designer |

A small body may combine roles; keep at least the editorial, technical and accessibility views separate, because they
reach different conclusions from the same demo.

## Which evidence each role checks

| Role | Reads first | Tests in the demo or proof of concept |
|---|---|---|
| Editorial lead | [Content model and editing](../../../../guides/content-model-and-editing.md), [Versions](../../../../guides/content-history.md), [Workflows](../../../../guides/workflows.md) | creating, previewing, approving and publishing a page |
| Accessibility specialist | [Accessibility standards](../../../../standards/accessibility.md) | keyboard-only use of the admin; the bidder's public design; see the page on evaluating accessibility evidence |
| Security specialist | [Security standards](../../../../standards/security.md), [Security hardening](../../../security-hardening.md) | two-step sign-in, roles, audit trail entries |
| Data protection specialist | [Privacy standards](../../../../standards/privacy.md), [E-mail preferences](../../../../features/6.0/mail-preferences.md) | consent records, removing an account, log masking |
| Technical lead | [Requirements](../../../../install/02-requirements.md), [Deploying](../../../../guides/deploying.md), [Leaving Exponential](../../../../install/migration/leaving-exponential.md) | install in one command, export of a site archive |
| Language lead | [Content languages](../../../../guides/content-languages.md), [Right to left](../../../../features/6.0/right-to-left.md) | translating a page; a right-to-left page |

## Preparing the panel

1. **Briefing session.** Walk through the tender's criteria and the scoring scale. Show where the product's own
   documentation states its status, and what those status words mean
   ([Standards: how to read the status](../../../../standards/README.md#how-to-read-the-status)).
2. **Hands-on time before the demos.** An evaluator who has used a CMS for an hour asks better questions. A test
   installation on SQLite takes one command ([Installing in one command](../../../../features/6.0/install-in-one-command.md)).
3. **Self-paced reading.** The first modules of [Editors 101](../../../trainers/editors-101/README.md) teach the
   basics an evaluator needs; the [admin tour](../../../admin-tour.md) is shorter.
4. **Agree the demo script.** Give every bidder the same script; see the page on demo scripts for evaluators.

## Rules for a fair evaluation

- Every member signs a declaration of conflicts of interest and confidentiality before reading bids.
- Members score alone first, then meet for consensus; the moderator records reasons.
- Questions to bidders go through the procurement officer, in writing, and answers are shared as your rules require.
- No member contacts a bidder privately during the evaluation.
- Evidence is recorded by link or by demo minute; impressions are not evidence.
- Use placeholder data in demos and proofs of concept, never real personal data of citizens or staff.

## A panel charter template

```text
Evaluation panel charter - <tender reference>
Chair / moderator:   <name, role>        (does not score: yes/no)
Members and roles:   <name> - <role> - criteria scored: <list>
Advisers (no score): <name> - <expertise>
Timeline:            reading <dates>; demos <dates>; proof of concept <dates>; consensus <date>
Evidence accepted:   written bid; demo; proof of concept; public documentation of the product at <version>
Records kept:        individual sheets, consensus sheet, demo notes, PoC log - stored at <location>
Declarations signed: <date>
```

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Trainers: course catalogue](../../../trainers/course-catalogue.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
