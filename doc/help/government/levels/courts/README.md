---
title: Courts
audience: government, project-manager, admin, website-creator
category: public-sector
order: 10
summary: Index for courts and tribunals planning an Exponential site - what they publish, content model, structure and roles, requirements, a 90-day plan, and publishing decisions with care for personal data.
keywords: court, tribunal, judiciary, judgments, decisions, hearings, court lists, court website, personal data, anonymization
type: help
---
# Courts

This folder is for project managers, administrators and website creators at a court, a tribunal or a judicial
administration. Court websites tell people how to bring or answer a case, where and when hearings take place, and,
in many jurisdictions, publish decisions. Publishing decisions is where most care is needed: they can contain personal
data about parties, witnesses and children. The pages answer what courts publish, how to build it in Exponential,
which requirements weigh most, how to run the first 90 days, and how to publish decisions with care.

Product facts come from the documentation of this repository; descriptions of courts are general public-sector
practice. What a court must or may publish is set by the law and the rules of its jurisdiction.

## Contents

1. [The pages](#the-pages)
2. [Reading order](#reading-order)
3. [What sets courts apart](#what-sets-courts-apart)

## The pages

| Page | The answer in one line |
|---|---|
| [What a court publishes](what-it-publishes.md) | Guidance for court users, locations and contacts, hearing information, decisions, rules and forms, news |
| [Content model for courts](content-model.md) | Shipped classes plus Decision, Court location and Guide classes, with a publication status |
| [Structure, sections and roles for courts](structure-sections-and-roles.md) | Separate sections for decisions, a small trained publishing team, review before publishing |
| [Requirements emphasis for courts](requirements-emphasis.md) | Personal data, accuracy, accessibility, security, records |
| [The first 90 days for courts](first-90-days.md) | A plan with the decision publication policy agreed before anything else |
| [Publishing decisions with care for personal data](publishing-decisions-with-care-for-personal-data.md) | Anonymize before upload, review, publish, correct and withdraw, and know what stays behind |

## Reading order

1. **What a court publishes**, then **Publishing decisions with care for personal data**.
2. **Requirements emphasis**.
3. **Content model** and **Structure, sections and roles**.
4. **First 90 days**.

Up: [Levels of government](../README.md). The landing page of the Governments section, government-start-here.md,
is the starting point for every reader.

## What sets courts apart

| Topic | Why it matters |
|---|---|
| Personal data in decisions | names, health, children, criminal matters; mistakes cannot be fully undone once copied |
| Independence and authority | the published text of a decision must be exactly what the court decided |
| Court users under stress | people who represent themselves need plain, accurate guidance |
| Hearing information | lists change at short notice |
| Security | courts attract attacks; a defaced or altered decision is serious |

Exponential provides the publishing mechanics: sections and roles, approval, versions, hiding and removal, and the
audit trail. Anonymizing decisions is done before upload, by people or by a dedicated tool; Exponential does not
detect or remove personal data in documents.

## Related pages

- [Levels of government](../README.md)
- [Parliaments](../parliaments/README.md)
- [Privacy standards](../../../../standards/privacy.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
