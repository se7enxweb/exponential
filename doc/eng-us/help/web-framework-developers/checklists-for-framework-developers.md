---
title: Checklists for framework developers
audience: web-framework-developer, developer
category: web-frameworks
order: 510
summary: Four checklists for developers coming from other frameworks - evaluating Exponential for a project, setting up a development environment, reviewing a first module before it is merged, and taking an application live - each item linked to the page that explains it.
keywords: checklist, evaluation, setup, code review, go-live, launch, deployment, security, caching, testing, web framework developer
type: help
---
# Checklists for framework developers

This page is for developers who want to tick boxes rather than read chapters. Each item links to the page that
explains it.

## Contents

1. [Evaluating Exponential for a project](#evaluating-exponential-for-a-project)
2. [Setting up a development environment](#setting-up-a-development-environment)
3. [Reviewing a first module](#reviewing-a-first-module)
4. [Taking an application live](#taking-an-application-live)

## Evaluating Exponential for a project

- [ ] The data editors manage is listed, and each kind is either a content class or a table of your own
      ([The content model](the-content-model-for-orm-users.md)).
- [ ] Sites, languages and audiences are mapped to siteaccesses ([Siteaccesses](siteaccesses-hosts-and-tenants.md)).
- [ ] Permissions are described as roles with functions and limitations ([Authorization](authorization-roles-and-policies.md)).
- [ ] Every feature you count on is checked in the product and distribution you will install, not assumed from another
      framework ([The platform products](the-platform-products.md), [Concept dictionary: no direct equivalent](concept-dictionary.md#no-direct-equivalent)).
- [ ] The team has read [When to choose Exponential](when-to-choose-exponential.md).

## Setting up a development environment

- [ ] A local installation: `./console exp:install --apply` (SQLite) ([Installing in one command](../../features/6.0/install-in-one-command.md)).
- [ ] `config.env.php` with `EXP_ENV=dev`; debug output and the `file` mail transport in the `dev` variant
      ([Environments and secrets](environments-and-secrets.md)).
- [ ] Velocity with `Engine=qbix`, or Apache or nginx with PHP-FPM ([Servers and deployment](servers-and-deployment.md)).
- [ ] Template path comments and the debug bar on ([Debugging](debugging-logging-and-errors.md)).
- [ ] An extension of your own, active, in its own Git repository ([Packages](packages-extensions-and-composer.md),
      [Development environment](../creators/app-creators/development-environment.md)).

## Reviewing a first module

- [ ] Every view names its policy functions in `module.php` ([Routing](routing-with-modules-and-views.md)).
- [ ] Parameters are cast and checked; redirect targets are checked ([Validation](validation.md)).
- [ ] Every printed value is washed ([The view layer](templates-vs-twig-and-blade.md)).
- [ ] Forms post with the form token; no `$_POST` read directly ([Forms and CSRF](forms-and-csrf-protection.md)).
- [ ] No SQL into content tables; persistent objects or escaped SQL for your own tables ([Queries](querying-content-and-tables.md)).
- [ ] No per-request state in static properties ([Servers and deployment](servers-and-deployment.md#rules-for-persistent-workers)).
- [ ] Settings documented in your extension's INI file, secrets only in `settings/override/` ([Configuration](configuration-ini-files.md)).
- [ ] Logic in classes with unit tests that run without a database ([Testing](testing.md)).
- [ ] No inline `<style>` or `style=""` in admin templates ([admin4 styling](../../bc/6.0/admin4-styling.md)).

## Taking an application live

- [ ] Your schema and classes install with one command; upgrade steps exist for later releases ([Migrations](migrations-and-dba-schema-files.md)).
- [ ] The `frequent` and `infrequent` cronjob parts, your own parts and queue workers are scheduled ([Scheduled tasks](scheduled-tasks-and-cronjobs.md), [Queues](queues-and-background-jobs.md)).
- [ ] Debug output is off for visitors; secrets are set per machine with mode 0640.
- [ ] `[HTMLForms] Secret` is set for the installation ([Forms and CSRF](forms-and-csrf-protection.md)).
- [ ] Caches and HTTP headers are decided: view cache, cache blocks, HTTP cache, Velocity response cache ([Caching](caching-layers.md), [HTTP caching](http-caching-and-reverse-proxies.md)).
- [ ] The release sequence is written down and tried on staging ([Servers and deployment](servers-and-deployment.md#a-release-step-by-step)).
- [ ] The security hardening guide has been read ([Security hardening](../security-hardening.md)).
- [ ] The website launch checklist is done for the public site ([Launch checklist](../creators/website-creators/launch-checklist.md)).

## Related pages

- [Ten pitfalls for framework developers](pitfalls-for-framework-developers.md)
- [Web framework developer FAQ](web-framework-developer-faq.md)
- [Security for applications](../creators/app-creators/security-for-applications.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
