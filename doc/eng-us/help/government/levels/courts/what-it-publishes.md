---
title: What a court publishes
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: The content courts and tribunals typically publish - guidance for court users, locations, hearing information, decisions, rules, forms and fees, news - and what each needs from Exponential.
keywords: court website, tribunal, court users, guidance, court locations, hearings, court lists, decisions, judgments, rules, forms, fees, scope
type: help
---
# What a court publishes

This page is for project managers and website creators at a court or tribunal who define the scope of a site. It lists
what courts typically publish (general public-sector practice; jurisdictions differ widely) and what each needs.

## Contents

1. [The content](#the-content)
2. [What the CMS must do](#what-the-cms-must-do)
3. [What should not be on the website](#what-should-not-be-on-the-website)
4. [Questions to settle](#questions-to-settle)

## The content

| Content | Audience | Rhythm |
|---|---|---|
| Guidance for court users: starting a case, responding, preparing for a hearing, costs, help available | parties, often without a lawyer | stable, reviewed with rule changes |
| Court locations: addresses, opening hours, accessibility, security screening, interpreters | everyone attending | occasional |
| Hearing information: lists of hearings where published, changes, closures | parties, press | daily, at short notice |
| Decisions and judgments, where published | public, lawyers, researchers, press | continuous |
| Rules, practice directions, forms and fees | lawyers, parties | changes on dates set by rule |
| News and press information | press, public | occasional |
| About the court: jurisdiction, judges' public roles, annual report, complaints | everyone | yearly |

## What the CMS must do

| Need | Exponential | Read |
|---|---|---|
| Decisions in a restricted publishing flow | a dedicated section, a small role, an *Approve* event | [Structure, sections and roles for courts](structure-sections-and-roles.md) |
| Exact text, no silent change | versions and the audit trail | [Versions of an object](../../../../guides/content-history.md), [Audit trail](../../../../features/6.0/audit-trail.md) |
| Withdraw a decision quickly | hide, or remove to the trash, and remove versions | [Publishing decisions with care](publishing-decisions-with-care-for-personal-data.md) |
| Rules effective on a date | an effective date attribute; a *Wait until date* workflow event if publication must wait | [Workflows](../../../../guides/workflows.md) |
| Hearing changes and closures fast | a notices folder shown on the front page; urgent publishing without approval for the duty team | [The layouts editor](../../../layouts-editor.md) |
| Forms to download | File objects | [Files and downloads](../../../creators/website-creators/files-and-downloads.md) |
| Plain-language guidance | a Guide class with a short summary | [Content model for courts](content-model.md) |

## What should not be on the website

| Do not | Instead |
|---|---|
| Upload case documents of ongoing cases | they belong in the case management system |
| Publish hearing lists with more personal data than the rules allow | publish only what the rules require; agree the fields with the judiciary |
| Let a decision appear before it is anonymized and approved | a review step and a separate section |
| Offer filing of documents through a website form | e-filing systems have their own security and records rules |

## Questions to settle

- [ ] Which decisions are published, by whom, after which anonymization, and for how long?
- [ ] Which hearing information is published, and from which system?
- [ ] Who publishes a closure at short notice?
- [ ] Which languages must guidance be available in?

## Related pages

- [Courts](README.md)
- [Content model for courts](content-model.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
