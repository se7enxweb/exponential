---
title: The first 90 days for public health
audience: government, project-manager, admin
category: public-sector
order: 60
summary: A 90-day plan for a health department or public health agency starting with Exponential - the alert procedure first, then advice with clinical review, languages and load, then launch - with checks.
keywords: project plan, 90 days, public health, alert procedure, clinical review, translations, load test, static cache, launch, checks
type: help
---
# The first 90 days for public health

This page is for project managers and administrators who start a public health site on Exponential. Get the alert
procedure working first: an outbreak will not wait for the launch date. It assumes an installation exists
([Getting started](../../../../guides/getting-started.md)).

## Contents

1. [Days 1 to 30: alerts first](#days-1-to-30-alerts-first)
2. [Days 31 to 60: advice and review](#days-31-to-60-advice-and-review)
3. [Days 61 to 90: languages, load and launch](#days-61-to-90-languages-load-and-launch)
4. [Ongoing](#ongoing)

## Days 1 to 30: alerts first

| Step | Check |
|---|---|
| Agree who publishes alerts at any hour (at least two people), and who translates them | an on-call list |
| Create the Alert class and the Alerts folder ([Content model](content-model.md)) | a test alert publishes |
| Show current alerts on the front page (a template list or a layouts block) | the test alert appears on the front page |
| Configure `[HideSettings]` for alerts | a test alert with a past end time is hidden after `hide.php` runs |
| Create the Alert publisher role, with two-step sign-in | an alert publisher can do nothing outside Alerts |
| Rehearse: publish, update, translate, end | timed; written down ([Alerts in many languages](alerts-multilingual-and-plain-language.md)) |

## Days 31 to 60: advice and review

| Step | Check |
|---|---|
| Create Health topic and Clinic classes | test objects publish |
| Create sections and roles; set up clinical review ([Structure](structure-sections-and-roles.md)) | a writer's change waits; a reviewer approves it |
| Confirm the workflow cronjob runs often enough | **Setup > Workflow processes** status bar |
| Migrate the most visited topics, rewritten in plain language | each has a summary, review date and reviewer |
| Accessibility test of templates | no open serious finding ([Testing accessibility](../../../creators/website-creators/testing-accessibility.md)) |

## Days 61 to 90: languages, load and launch

| Step | Read |
|---|---|
| Set up language siteaccesses and translator roles | [Content languages](../../../../guides/content-languages.md) |
| Translate alerts templates and top topics into the priority languages | |
| Load test with caches on, and decide on static cache or a CDN for peaks | [Performance and caching](../../../creators/website-creators/performance-and-caching.md) |
| Rehearse a maintenance window and a restore | [Maintenance mode](../../../../guides/maintenance-mode.md) |
| Launch | [Launch checklist](../../../creators/website-creators/launch-checklist.md) |

Checks at launch:

- [ ] An alert published by the on-call team appears on the front page in every language siteaccess within the time
  you set, including after cache refresh.
- [ ] Every migrated topic shows its review date.
- [ ] The site holds the load test's peak with caches on.

## Ongoing

| When | Task |
|---|---|
| Weekly | list topics past their review date; send to reviewers |
| After each alert | review what took too long |
| Quarterly | rehearse the alert procedure with a new team member |

## Related pages

- [Public health](README.md)
- [Alerts in many languages and plain language](alerts-multilingual-and-plain-language.md)
- [Launch checklist](../../../creators/website-creators/launch-checklist.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
