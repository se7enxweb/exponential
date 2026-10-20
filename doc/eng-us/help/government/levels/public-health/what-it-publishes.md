---
title: What a public health body publishes
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: The content health departments and public health agencies typically publish - health advice, alerts, services and clinics, professional guidance, data and reports - and what each needs from Exponential.
keywords: public health website, health advice, health topics, alerts, outbreak, vaccination, clinics, professional guidance, surveillance data, reports, scope
type: help
---
# What a public health body publishes

This page is for project managers and website creators at a health department or public health agency who define the
scope of a site. It lists what such bodies typically publish (general public-sector practice) and what each needs.

## Contents

1. [The content](#the-content)
2. [What the CMS must do](#what-the-cms-must-do)
3. [What belongs elsewhere](#what-belongs-elsewhere)
4. [Questions to settle](#questions-to-settle)

## The content

| Content | Audience | Rhythm |
|---|---|---|
| Health advice by topic: conditions, prevention, healthy living, vaccination | the public | reviewed on a cycle and when evidence changes |
| Alerts: outbreaks, heat, air quality, water, product warnings | the public, media | urgent; updated during the event |
| Services: clinics, vaccination sites, screening programs, how to book | the public | changes often |
| Guidance for professionals: case definitions, reporting duties, protocols | clinicians, schools, employers | changes with evidence |
| Data and reports: surveillance figures, annual health reports | professionals, media, researchers | weekly to yearly |
| Campaigns | target groups | seasonal |
| News and press | media | frequent during events |
| About us, contact, jobs | everyone | yearly |

## What the CMS must do

| Need | Exponential | Read |
|---|---|---|
| Clinical sign-off before publishing | an *Approve* event with clinical approvers | [Workflows](../../../../guides/workflows.md) |
| Alerts published in minutes, by an on-call team | excluded from approval; a front page area for alerts | [Alerts in many languages](alerts-multilingual-and-plain-language.md) |
| Advice reviewed on schedule | a review date attribute and a list of overdue pages | [Content model for public health](content-model.md) |
| Many languages | translations per object, a siteaccess per language | [Content languages](../../../../guides/content-languages.md) |
| Load during an outbreak | caches, static cache, Velocity | [Performance and caching](../../../creators/website-creators/performance-and-caching.md) |
| Updates to followers | notifications, RSS, newsletters | [RSS feeds](../../../../guides/rss-feeds.md) |
| Professionals' area | a separate folder; public by default, restricted only where needed | [Sections](../../../../guides/sections.md) |

## What belongs elsewhere

| Content | Where |
|---|---|
| Patient records, test results, bookings | clinical and booking systems; the site links |
| Case reporting by professionals | a secure reporting system; never an information collector form with patient data |
| Live dashboards of surveillance data | a data platform; the site links or embeds with care for accessibility |

A website form must never collect health data about identifiable people unless your data protection officer has
approved the purpose, the security and the retention.

## Questions to settle

- [ ] Who signs off health advice, and who may publish an alert at night?
- [ ] Which languages are needed for alerts, and who translates within hours?
- [ ] What traffic did the busiest day of past events bring?
- [ ] Which topics are reviewed how often?

## Related pages

- [Public health](README.md)
- [Content model for public health](content-model.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
