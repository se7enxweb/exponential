---
title: Emergency services
audience: government, project-manager, admin, website-creator
category: public-sector
order: 10
summary: Index for fire and rescue services, civil protection and emergency management bodies planning an Exponential site - what they publish, content model, structure and roles, requirements, a 90-day plan, and alerts with high availability, maintenance mode and static cache.
keywords: emergency services, fire service, rescue, civil protection, emergency management, alerts, incidents, preparedness, high availability, static cache
type: help
---
# Emergency services

This folder is for project managers, administrators and website creators at a fire and rescue service, a civil
protection authority or an emergency management body. Their websites have two lives: a quiet one (prevention advice,
recruitment, reports) and an emergency one, when many people need current, correct information at once and the site
must stay up. The pages answer what emergency services publish, how to build it in Exponential, which requirements
weigh most, how to run the first 90 days, and how to keep alerts available with caches, static pages and maintenance
mode.

Product facts come from the documentation of this repository; descriptions of emergency services are general
public-sector practice. A website is never the only warning channel; emergency warning systems, broadcast and
telephone networks are outside Exponential.

## Contents

1. [The pages](#the-pages)
2. [Reading order](#reading-order)
3. [What sets emergency services apart](#what-sets-emergency-services-apart)

## The pages

| Page | The answer in one line |
|---|---|
| [What an emergency service publishes](what-it-publishes.md) | Incident and warning information, preparedness advice, prevention, services, recruitment, reports |
| [Content model for emergency services](content-model.md) | Shipped classes plus Incident update and Preparedness guide classes, small and fast to fill |
| [Structure, sections and roles for emergency services](structure-sections-and-roles.md) | An incident area at the top, an on-call publishing team without approval, everything else reviewed |
| [Requirements emphasis for emergency services](requirements-emphasis.md) | Availability, speed, accuracy, accessibility under stress, languages, security |
| [The first 90 days for emergency services](first-90-days.md) | A plan that proves availability and the incident procedure before launch |
| [Alerts, availability, maintenance mode and static cache](alerts-availability-maintenance-and-static-cache.md) | Keeping incident information online under load and during failures, with the tools that exist |

## Reading order

1. **Alerts, availability, maintenance mode and static cache**: it shapes the rest.
2. **What an emergency service publishes**.
3. **Requirements emphasis**.
4. **Content model** and **Structure, sections and roles**.
5. **First 90 days**.

Up: [Levels of government](../README.md). The landing page of the Governments section, [Governments: start here](../../government-start-here.md),
is the starting point for every reader. Health alerts are covered in
[Alerts in many languages and plain language](../public-health/alerts-multilingual-and-plain-language.md).

## What sets emergency services apart

| Topic | Why it matters |
|---|---|
| Peaks | traffic during an incident can be far above normal |
| Speed | information is useful for minutes or hours |
| Failures | the incident may affect power, networks or the hosting itself |
| Stress | readers are frightened; clarity and accessibility matter most |
| Night and weekend | publishing happens at any hour, by few people |

## Related pages

- [Levels of government](../README.md)
- [Public health](../public-health/README.md)
- [Maintenance mode](../../../../guides/maintenance-mode.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
