---
title: Staff and skills in the cost of an Exponential site
audience: government, project-manager, admin
category: public-sector
order: 60
summary: The internal roles a public body needs to run a website or intranet on Exponential - editors, web team, site administrator, operations, security and data protection - the skills each needs, the courses that teach them, and how to estimate their time in a total cost of ownership.
keywords: staff cost, internal cost, roles, skills, editors, web manager, site administrator, system administrator, developer, staffing, FTE, sourcing, TCO
type: help
---
# Staff and skills in the cost of an Exponential site

This page is for managers who must count the internal staff time a site needs, and decide what to do in-house and
what to buy. Internal time rarely appears in a bid but is often a large part of the total cost of ownership. Each
role below links the course of the help that teaches its skills
([Course catalogue](../../../trainers/course-catalogue.md)).

## Contents
1. [Roles and their skills](#roles-and-their-skills)
2. [In-house or bought](#in-house-or-bought)
3. [Estimating staff time](#estimating-staff-time)
4. [Keeping skills over the years](#keeping-skills-over-the-years)
5. [Pitfalls](#pitfalls)

## Roles and their skills

| Role | Does | Skills | Course |
|---|---|---|---|
| Editor (in departments) | writes and updates pages | the administration, drafts, versions, images, accessible content | [Editors 101](../../../trainers/editors-101/README.md), [Editors 201](../../../trainers/editors-201/README.md) |
| Web manager | editorial standards, approvals, structure | content model, workflows, object states, languages | Editors 201, [Exponential Layouts](../../../trainers/layouts/README.md) |
| Site administrator | users, roles, sections, caches, cronjobs, logs, audit | administration of an installation, basic command line | [Site administration](../../../trainers/site-administrators/README.md) |
| System administrator | servers, PHP, database, backups, monitoring, updates | Linux, web servers, databases | [Hosting and operations](../../../trainers/hosting-operations/README.md) |
| Developer (in-house or supplier) | templates, extensions, integrations | PHP, Git, the template language | [Developers](../../../trainers/developers/README.md), [Theme design](../../../trainers/theme-designers/README.md) |
| Security officer | hardening, reviews, incidents | security practice | [Security and audit guide](../../../../guides/security-and-audit.md) |
| Data protection officer | consent, retention, requests | data protection law, the privacy features | [Privacy standards](../../../../standards/privacy.md) |
| Accessibility lead | testing, statement, editor coaching | WCAG, testing methods | [Testing accessibility](../../../creators/website-creators/testing-accessibility.md) |

## In-house or bought

| Usually in-house | Often bought | Either |
|---|---|---|
| editors, web manager | implementation project, design, penetration test | site administration |
| data protection decisions | hosting and system administration | development of templates and extensions |
| accessibility of your own content | accessibility audit of the delivered site | support and maintenance |

Keep at least one person in-house who can administer the site and judge the supplier's work. Without that, every
question becomes a support ticket and the exit becomes harder.

## Estimating staff time

Estimate per role, in hours per period, from your own experience or a pilot:

```text
Role                 People   Hours per month (each)   Months   Internal rate   Cost
Editor               <n>      <h>                       <m>      <rate>          <...>
Web manager          <n>      <h>                       <m>      <rate>          <...>
Site administrator   <n>      <h>                       <m>      <rate>          <...>
System administrator <n>      <h>                       <m>      <rate>          <...>
...
```

Measure routine tasks during the proof of concept: how long does it take to publish a page with an approval, to add
a user with a role, to apply a release on staging? Recurring operations tasks are described in
[Operating a site](../../../../guides/operating-a-site.md) and [Monthly operations](../../../hosting/checklists/monthly-operations.md).

Count only the extra time the site causes. Editors who wrote content before will write content after; count the
difference.

## Keeping skills over the years

- Staff turnover: plan onboarding for new editors (see the training folder of procurement).
- Documentation in the administration: the help opens from the "?" button and is readable by editors on a new
  installation ([Help system](../../../../features/6.0/help-system.md)).
- The course material is free documentation; you may adapt it into your own training
  ([Free documentation](../../../free-documentation.md)).

## Pitfalls

- Assuming that a hosted or managed service needs no internal administrator.
- Forgetting the time of department editors because it is spread over many people.
- Training once at launch and never again.

## Related pages
- [Total cost of ownership: overview](README.md)
- [Training costs](training-costs.md)
- [Course catalogue](../../../trainers/course-catalogue.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
