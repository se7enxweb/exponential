---
title: Questions to ask during a CMS demo
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 70
summary: Questions an evaluation panel asks a bidder during a demonstration of Exponential, grouped by topic, with the answer the documentation supports so the panel can tell a precise answer from a vague one.
keywords: demo questions, vendor questions, evaluation questions, due diligence, accessibility, security, privacy, support, exit, hosting, CMS demo
type: help
---
# Questions to ask during a CMS demo

This page is for evaluators who attend a demonstration of a content management system based on Exponential. Each
question below comes with the answer the project's documentation supports and a link to it. A bidder may give a
better answer for its own services; it should never give a stronger answer about the product than its documentation.

## Contents
1. [Accessibility](#accessibility)
2. [Security](#security)
3. [Privacy and records](#privacy-and-records)
4. [Languages](#languages)
5. [Hosting and operations](#hosting-and-operations)
6. [Releases and support](#releases-and-support)
7. [Exit and licensing](#exit-and-licensing)
8. [How to use the answers](#how-to-use-the-answers)

## Accessibility

| Ask | What the documentation supports |
|---|---|
| Which WCAG level does the administration meet? | admin4 and admin4l: WCAG 2.2 AA "Partly complies", designed and regression-tested against AA criteria; not a conformance claim ([Accessibility standards](../../../../standards/accessibility.md)) |
| Do you have a VPAT or Accessibility Conformance Report? | none exists for the product; a bidder may produce one for the delivered site |
| Is the public design accessible? | public designs are not audited by the project; it is the site builder's responsibility |
| Is the rich text editor accessible? | the TinyMCE integrations were not audited: "Unverified" |
| Who writes our accessibility statement? | no template ships; the bidder or you write it |

## Security

| Ask | What the documentation supports |
|---|---|
| How do you map to the OWASP Top 10? | per-category mitigations, "Partly complies" ([Security standards](../../../../standards/security.md)) |
| Has an ASVS assessment been done? | no: "Unverified"; the default minimum password length (10) is below ASVS level 1 (12) unless changed |
| Is there two-step sign-in? | yes, as an opt-in extension ([Two-step sign-in](../../../../guides/two-factor-login.md)) |
| Is account lockout on? | off by default (`MaxNumberOfFailedLogin=0`); brute force is detected by audit alert rules |
| Is a Content Security Policy enforced? | report-only on administration siteaccesses, none on public sites by default; enforcement is the operator's decision |
| How are vulnerabilities reported and fixed? | privately to the address in `SECURITY.md`; fixes come in the next 6.0.x release; published tags never change |

## Privacy and records

| Ask | What the documentation supports |
|---|---|
| Do you have a cookie banner? | no; the kernel sets only functional cookies, and a site that adds analytics must add consent ([Privacy standards](../../../../standards/privacy.md)) |
| Can we export all data about one person? | no single function; mail preferences and audit records have exports, other data through separate tools |
| What is logged about staff? | the audit trail, with truncated addresses and pseudonymization after a set time ([Audit trail](../../../../features/6.0/audit-trail.md)) |
| How long are logs kept? | audit 90 days live and 2 years archived per channel; some logs have age limits, others rotate by size ([Privacy standards](../../../../standards/privacy.md#known-gaps)) |

## Languages

| Ask | What the documentation supports |
|---|---|
| Can each page exist in several languages? | yes, with translations per object ([Content languages](../../../../guides/content-languages.md)) |
| Right-to-left scripts? | `dir="rtl"` in content and administration ([Right to left](../../../../features/6.0/right-to-left.md)) |
| Which administration languages are complete? | see [Languages](../../../languages.md); ask for your language specifically |

## Hosting and operations

| Ask | What the documentation supports |
|---|---|
| Which databases and PHP versions? | PHP 7.4 to 8.5; SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL ([Requirements](../../../../install/02-requirements.md)) |
| Can we host it ourselves? | yes; the software is free and runs on your own servers ([Choosing a hosting model](../../../hosting/models/choosing-a-hosting-model.md)) |
| How are backups taken? | database and files together, with maintenance mode ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)) |
| How do we check an upgrade? | **Setup > Upgrade check** ([Upgrade check](../../../../guides/upgrade-check.md)) |

## Releases and support

| Ask | What the documentation supports |
|---|---|
| Which versions get security fixes? | the 6.0.x line; 5.x and older are not supported (`SECURITY.md`) |
| What does the project promise for response time? | a first answer to a private security report within one business day; no other SLA is published ([SLA options](../../../hosting/business/sla-options.md)) |
| Who supports us? | the bidder's own offer; the project offers community help and paid help from 7x ([Getting support](../../../project/getting-support.md)) |

## Exit and licensing

| Ask | What the documentation supports |
|---|---|
| What does the license cost? | no license fee; GNU GPL version 2 or later ([Licensing questions](../../../project/licensing-faq.md)) |
| Do we get the source of what you build for us? | ask the bidder to commit to it in the contract; the GPL governs distribution |
| How do we leave? | site archive, packages, database dump, files, layouts, form data ([Leaving Exponential](../../../../install/migration/leaving-exponential.md)) |

## How to use the answers

- Write down the exact words of the answer and the minute of the demo.
- If the answer goes beyond the documentation, ask: "Where is that written?" and record the source given.
- An honest "no, that is a gap, and here is how we would close it" is a good answer; score the plan.

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
