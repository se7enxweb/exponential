---
title: The first 90 days for emergency services
audience: government, project-manager, admin
category: public-sector
order: 60
summary: A 90-day plan for a fire and rescue service or emergency management body starting with Exponential - incident procedure and availability first, then content, then a launch after an incident rehearsal - with checks.
keywords: project plan, 90 days, emergency services, incident procedure, availability, load test, static cache, rehearsal, launch, checks
type: help
---
# The first 90 days for emergency services

This page is for project managers and administrators who start an emergency service site on Exponential. Prove that
incident information can be published fast and served under load before anything else. It assumes an installation
exists ([Getting started](../../../../guides/getting-started.md)).

## Contents

1. [Days 1 to 30: incident procedure](#days-1-to-30-incident-procedure)
2. [Days 31 to 60: availability](#days-31-to-60-availability)
3. [Days 61 to 90: content, rehearsal, launch](#days-61-to-90-content-rehearsal-launch)
4. [Ongoing](#ongoing)

## Days 1 to 30: incident procedure

| Step | Check |
|---|---|
| Agree who publishes incident information, at which hours, and who checks it | an on-call list with at least two people per shift |
| Create the Incident update class and the Incidents folder ([Content model](content-model.md)) | a test update publishes |
| Create the Incident publisher role and accounts, with two-step sign-in ([Structure](structure-sections-and-roles.md)) | every on-call person has signed in from a phone |
| Show ongoing incidents at the top of the front page | the test update appears there |
| Configure `[HideSettings]` and confirm the global cronjob part runs | a test update with a past end time is hidden |
| Write the procedure: publish, update, end, correct, take down a false update | a one-page procedure |

## Days 31 to 60: availability

| Step | Read |
|---|---|
| Measure normal and peak load from past incidents | your statistics |
| Configure the static cache and the offload rules; generate it | [Static cache generator](../../../../features/6.0/static-cache-generator.md) |
| Decide on Velocity's response cache and a CDN | [Velocity response cache](../../../../features/6.0/velocity-response-cache.md), [HTTP caching and CDNs](../../../creators/website-creators/http-caching-and-cdns.md) |
| Load test with caches on | a measured result above the expected peak |
| Test: stop PHP or the database on a test installation; do static pages still answer? | [Alerts, availability](alerts-availability-maintenance-and-static-cache.md) |
| Rehearse maintenance mode | [Maintenance mode](../../../../guides/maintenance-mode.md) |
| Plan for a failure of the hosting site | a written plan (second site, DNS, CDN) |

## Days 61 to 90: content, rehearsal, launch

| Step | Read |
|---|---|
| Write preparedness and prevention guides in plain language | [Requirements emphasis](requirements-emphasis.md) |
| Translate key instructions into the area's main languages | [Content languages](../../../../guides/content-languages.md) |
| Full incident rehearsal at night with the on-call team | the procedure |
| Accessibility test on a phone with a screen reader | [Testing accessibility](../../../creators/website-creators/testing-accessibility.md) |
| Launch | [Launch checklist](../../../creators/website-creators/launch-checklist.md) |

Checks at launch:

- [ ] In the rehearsal, an update appeared to visitors within the agreed time, including through every cache.
- [ ] The emergency number is on every page.
- [ ] Every on-call person has published at least once.

## Ongoing

| When | Task |
|---|---|
| Each shift change | the on-call list is current |
| Monthly | a short publishing drill |
| After each incident | a review of what took too long |
| Yearly | preparedness guides reviewed; load test repeated |

## Related pages

- [Emergency services](README.md)
- [Alerts, availability, maintenance mode and static cache](alerts-availability-maintenance-and-static-cache.md)
- [Launch checklist](../../../creators/website-creators/launch-checklist.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
