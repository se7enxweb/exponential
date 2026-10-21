---
title: Design review checklist
audience: software-engineer
category: software-engineering
order: 550
summary: A checklist and template for reviewing the technical design of a system built on Exponential - requirements and fit, product and stack, content model, data and database, runtime and caching, scaling and availability, security, integration, operations, quality, upgrades and ownership - with the page that answers each question.
keywords: design review, architecture review, checklist, template, decision record, system design, requirements, risks, non-functional requirements, software engineer
type: help
---
# Design review checklist

This page is a checklist and a template for the design review of a system built on Exponential: a new site, an
application, a migration or a large extension. Copy the sections into your decision record and answer each item with
a decision and its source.

## Contents

1. [Fit and scope](#fit-and-scope)
2. [Content model and data](#content-model-and-data)
3. [Runtime, caching and scale](#runtime-caching-and-scale)
4. [Security](#security)
5. [Integration](#integration)
6. [Operations](#operations)
7. [Quality, upgrades and ownership](#quality-upgrades-and-ownership)
8. [A decision record template](#a-decision-record-template)

## Fit and scope

- [ ] Which product of the family, and does it contain what we need? ([What Exponential is](../leadership/what-exponential-is.md))
- [ ] Which needs are met by existing features or extensions, and which need new code?
      ([Evaluating Exponential](evaluating-exponential.md))
- [ ] Is the license (GNU GPL, version 2 or later) compatible with how we distribute our code?

## Content model and data

- [ ] Content classes, attributes and datatypes; which need custom datatypes?
      ([The content model for engineers](content-model-for-engineers.md))
- [ ] The shape of the content tree, sections and object states, and how permissions follow them.
- [ ] Languages: how many content languages (at most 62), which fallbacks, right-to-left needs
      ([Internationalization and right to left](i18n-and-right-to-left.md)).
- [ ] Custom tables: persistent classes, schema files for every driver ([Schema changes and migrations](schema-changes-and-migrations.md)).
- [ ] Which database, and is it tested in CI? ([Databases: choosing and running one](databases-and-choosing-one.md))

## Runtime, caching and scale

- [ ] Which application server ([Application servers](application-servers.md)), and is our code safe in persistent
      workers?
- [ ] Which caches are on, what they are keyed on, how they are invalidated ([Caching layers for engineers](caching-layers-for-engineers.md)).
- [ ] One machine or several nodes; what is shared ([Scaling and clustering](scaling-and-clustering.md)).
- [ ] Expected traffic, measured page costs, and the headroom ([Performance measurement and benchmarks](performance-measurement-and-benchmarks.md)).
- [ ] One installation or several sites ([Multi-site installations](multi-site-installations.md)).

## Security

- [ ] Roles and policies for every kind of user, written down.
- [ ] Two-factor sign-in for staff; lockout; session handler ([Authentication, sessions and two-factor sign-in](authentication-sessions-and-2fa.md)).
- [ ] Trusted proxies, security headers, CSP plan ([HTTP security](http-security-csp-and-proxies.md)).
- [ ] `ManagedByDeployment` in production.
- [ ] Where secrets live, and who rotates them.

## Integration

- [ ] Which APIs: REST, ezjscore endpoints, remote services; authentication for each
      ([API design: REST, ezjscore and headless](api-design-rest-ezjscore-headless.md)).
- [ ] Background work: cronjob parts, content jobs, expqueue ([Console, cronjobs and background work](console-cron-and-background-work.md)).
- [ ] Mail: transport, preferences and consent.

## Operations

- [ ] Deploy procedure and its order ([Deploying a change](deploying-a-change.md)).
- [ ] Monitoring, logs, audit trail, alert recipients ([Observability](observability-logs-debug-and-audit.md)).
- [ ] Backups and a tested restore.
- [ ] Incident procedure and runbooks ([Incident handling](incident-handling.md)).

## Quality, upgrades and ownership

- [ ] Tests and CI for our own code ([Testing strategy](testing-strategy.md)).
- [ ] How we follow kernel and extension releases, and who reads the upgrade notes ([Backward compatibility policy](backward-compatibility-policy.md)).
- [ ] Composer constraints and how we repin ([Dependency management with Composer](dependency-management-with-composer.md)).
- [ ] What we contribute back, and whether contributors have signed the CLA ([Contributing upstream](contributing-upstream.md)).
- [ ] Known risks and debt we accept ([Handling technical debt](technical-debt.md)).

## A decision record template

| Field | Content |
|---|---|
| Title | the decision in one line |
| Date and status | the date; proposed, accepted, or replaced by another record |
| Context | the requirement and the constraint |
| Options | two or three, each with its source page |
| Decision | the option chosen |
| Consequences | what becomes easier, what becomes harder, what to watch |
| Review date | when to look at it again, for example after the next 6.0.x release |

## Related pages

- [Code review checklist](code-review-checklist.md)
- [Planning an Exponential project](../leadership/planning-an-exponential-project.md)
- [Risk management](../leadership/risk-management.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
