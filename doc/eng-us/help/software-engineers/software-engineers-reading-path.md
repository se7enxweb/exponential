---
title: Reading path for software engineers
audience: software-engineer
category: software-engineering
order: 20
summary: The software engineering pages in a sensible order, with four shorter paths for the evaluator, the architect, the operator and the reviewer or contributor, and the deeper documents to read after each step.
keywords: reading path, learning path, onboarding, order, evaluator, architect, operator, reviewer, contributor, software engineer
type: help
---
# Reading path for software engineers

This page puts the software engineering pages in order. Read the core path once; then follow the path for the role
you have in your project. Each step names the deeper document to read when the help page is not enough.

## Contents

1. [The core path](#the-core-path)
2. [The evaluator's path](#the-evaluators-path)
3. [The architect's path](#the-architects-path)
4. [The operator's path](#the-operators-path)
5. [The reviewer's and contributor's path](#the-reviewers-and-contributors-path)

## The core path

| Step | Page | Deeper document |
|---|---|---|
| 1 | [Exponential for software engineers](exponential-for-software-engineers.md) | [What Exponential is](../leadership/what-exponential-is.md) |
| 2 | [Architecture overview](architecture-overview.md) | [Kernel architecture](../../standards/architecture/kernel-architecture.md) |
| 3 | [The request lifecycle](request-lifecycle.md) | [Kernel architecture](../../standards/architecture/kernel-architecture.md) |
| 4 | [The content model for engineers](content-model-for-engineers.md) | [Content model and editing](../../guides/content-model-and-editing.md) |
| 5 | [Siteaccesses and configuration](siteaccesses-and-configuration.md) | [INI override placements](../../specifications/6.0/ini-override-placements.md) |
| 6 | [Caching layers for engineers](caching-layers-for-engineers.md) | [Caches](../../guides/caches.md) |
| 7 | [Application servers](application-servers.md) | [Velocity engines](../../bc/6.0/velocity-engines.md) |
| 8 | [Testing strategy](testing-strategy.md) | [Quality checks](../../features/6.0/quality-checks.md) |
| 9 | [Release engineering](release-engineering.md) | [Source control and releases](../../standards/source-control-and-releases.md) |
| 10 | [Security engineering overview](security-engineering-overview.md) | [Security standards](../../standards/security.md) |

## The evaluator's path

You decide whether Exponential fits a project.

1. [Evaluating Exponential](evaluating-exponential.md): the questions to answer and how to answer them.
2. [Databases: choosing and running one](databases-and-choosing-one.md) and [Application servers](application-servers.md).
3. [Scaling and clustering](scaling-and-clustering.md) and [Performance measurement and benchmarks](performance-measurement-and-benchmarks.md).
4. [Security engineering overview](security-engineering-overview.md).
5. [Backward compatibility policy](backward-compatibility-policy.md) and [Dependency management with Composer](dependency-management-with-composer.md).
6. [Design review checklist](design-review-checklist.md) to write the result down.

## The architect's path

You design a system with Exponential at its center.

1. [Kernel, lib and extensions: the layering](kernel-lib-and-extensions.md).
2. [Modules, views and access control](modules-views-and-access-control.md).
3. [Persistence and the database layer](persistence-and-the-database-layer.md) and [Schema changes and migrations](schema-changes-and-migrations.md).
4. [Events, extension points and plugins](events-extension-points-and-plugins.md).
5. [API design: REST, ezjscore and headless](api-design-rest-ezjscore-headless.md).
6. [Multi-site installations](multi-site-installations.md) and [Internationalization and right to left](i18n-and-right-to-left.md).
7. [Console, cronjobs and background work](console-cron-and-background-work.md).

## The operator's path

You run Exponential in production.

1. [Deploying a change](deploying-a-change.md).
2. [Velocity persistent workers](velocity-persistent-workers.md).
3. [Cache invalidation and clearing](cache-invalidation-and-clearing.md).
4. [Observability: logs, debug bar and audit trail](observability-logs-debug-and-audit.md).
5. [Incident handling](incident-handling.md).
6. [HTTP security: headers, CSP, request rules and proxies](http-security-csp-and-proxies.md).
7. The hosting runbooks: [Hosting companies: start here](../hosting/hosting-companies-start-here.md).

## The reviewer's and contributor's path

You review changes or send them upstream.

1. [Git workflow](git-workflow.md).
2. [Static analysis and code style](static-analysis-and-code-style.md) and [DB-less tests and test suites](db-less-tests-and-test-suites.md).
3. [CI gates](ci-gates.md).
4. [Code review checklist](code-review-checklist.md) and [Security review checklist](security-review-checklist.md).
5. [Changelogs and upgrade notes](changelogs-and-bc-notes.md).
6. [Contributing upstream](contributing-upstream.md) and [Documentation as code](documentation-as-code.md).
7. [Handling technical debt](technical-debt.md).

## Related pages

- [Software engineers: start here](software-engineers-start-here.md)
- [Glossary for software engineers](glossary-for-software-engineers.md)
- [Reading paths of the extension development course](../extension-development/reading-paths.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
