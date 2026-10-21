---
title: Evaluating Exponential
audience: software-engineer
category: software-engineering
order: 30
summary: A structured technical evaluation of Exponential for a project - the questions to answer about fit, runtime, data, performance, security, quality, maintenance and licensing, how to answer each one from the code and the documentation, and how to set up a test installation for it.
keywords: evaluation, due diligence, technical assessment, fit, proof of concept, selection, criteria, risk, license, GPL, maintenance, software engineer
type: help
---
# Evaluating Exponential

This page is for engineers asked "should we build on Exponential?" It turns that question into smaller ones that the
repository and the documentation can answer, so the result is evidence rather than impression. Write the answers down
with their sources; the [Design review checklist](design-review-checklist.md) is a template for the result.

## Contents

1. [Set up a test installation](#set-up-a-test-installation)
2. [The questions, by area](#the-questions-by-area)
3. [Reading the project's own status statements](#reading-the-projects-own-status-statements)
4. [Risks to weigh](#risks-to-weigh)

## Set up a test installation

Evaluate on a real installation, not on screenshots. The quickest is SQLite, which needs no database server:

```bash
composer install
./console exp:install --help
```

`exp:install` uses SQLite when no `--db` is given ([Database support](../database-support.md)). The installation book
describes every install method ([Choosing an install method](../../install/04-choosing-an-install-method.md)). Serve it
with Velocity to see the recommended runtime ([Application servers](application-servers.md)).

## The questions, by area

| Area | Question | Where to find the answer |
|---|---|---|
| Fit | Does the content model express our data (typed attributes, versions, translations, relations, tree locations)? | [The content model for engineers](content-model-for-engineers.md) |
| Fit | Which product of the family do we need, and does it contain the parts we need? | [What Exponential is](../leadership/what-exponential-is.md) |
| Runtime | Which PHP versions and extensions do our servers have? | `composer.json`, [Requirements](../../install/02-requirements.md) |
| Runtime | Which server will serve it? | [Application servers](application-servers.md) |
| Data | Which database, and is it tested in CI? | [Databases: choosing and running one](databases-and-choosing-one.md) |
| Performance | What does a cached and an uncached page cost on our hardware? | [Performance measurement and benchmarks](performance-measurement-and-benchmarks.md) |
| Scale | Do we need several web nodes, and what must be shared? | [Scaling and clustering](scaling-and-clustering.md) |
| Security | What are the defaults, and how are vulnerabilities reported and fixed? | [Security engineering overview](security-engineering-overview.md) |
| Integration | Which APIs do our other systems need? | [API design: REST, ezjscore and headless](api-design-rest-ezjscore-headless.md) |
| Quality | What do CI and the local gates check? | [CI gates](ci-gates.md) |
| Maintenance | What counts as a breaking change, and how is it announced? | [Backward compatibility policy](backward-compatibility-policy.md) |
| Releases | How often are releases published, and how are they numbered? | [Release engineering](release-engineering.md), `doc/changelogs/` |
| License | Is the GNU GPL, version 2 or later, compatible with how we distribute our code? | `LICENSE`, [Contributing upstream](contributing-upstream.md) |

## Reading the project's own status statements

The standards pages in `doc/eng-us/standards/` rate the project against public standards with **Complies**, **Partly
complies**, **Does not comply** or **Unverified**, and list known gaps. They are the most honest single source for an
evaluation, because they name what is missing:

- [Security standards](../../standards/security.md): OWASP Top 10, ASVS, CSP.
- [Source control and releases](../../standards/source-control-and-releases.md): branch protection and signed
  commits are listed as gaps.
- [Database and SQL standards](../../standards/databases-and-sql.md) and [Testing and CI](../../standards/testing-and-ci.md).
- [Internationalization standards](../../standards/internationalization.md) and [Accessibility](../../standards/accessibility.md).

## Risks to weigh

- **Inherited code.** The kernel is old and large; PHPStan runs at level 1 with a baseline of findings that existed
  when the check was introduced ([Handling technical debt](technical-debt.md)).
- **Manual database updates.** There is no migration runner that records which statements ran
  ([Schema changes and migrations](schema-changes-and-migrations.md)).
- **Policy, not enforcement.** Some repository rules (no force push, merge style) are policy without branch
  protection ([Source control and releases](../../standards/source-control-and-releases.md)).
- **A smaller ecosystem** than the largest PHP frameworks. Check the extension catalog for what you need before you
  plan to write it ([The se7enxweb extension catalog](../extension-development/part-1-from-user-to-developer/the-se7enxweb-extension-catalogue.md)).

## Related pages

- [Exponential for software engineers](exponential-for-software-engineers.md)
- [Design review checklist](design-review-checklist.md)
- [Software engineer FAQ](software-engineer-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
