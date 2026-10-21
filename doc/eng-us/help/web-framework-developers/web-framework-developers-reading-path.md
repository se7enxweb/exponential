---
title: Reading path for web framework developers
audience: web-framework-developer, developer
category: web-frameworks
order: 30
summary: The order in which to read this audience's pages - an orientation day, the request and its pieces, data, the cross-cutting concerns, operations, and the bridge to other frameworks - with time estimates and the pages from the application creators and the extension development book to read next.
keywords: reading path, learning path, curriculum, order, onboarding, first week, web framework developer
type: help
---
# Reading path for web framework developers

This page is for developers who want an order to read in. Each stage takes about half a day; the task in stage 2 is the
hands-on part. Skip the stages your project does not need.

## Contents

1. [Stage 1: orientation](#stage-1-orientation)
2. [Stage 2: the request and your first module](#stage-2-the-request-and-your-first-module)
3. [Stage 3: data](#stage-3-data)
4. [Stage 4: cross-cutting concerns](#stage-4-cross-cutting-concerns)
5. [Stage 5: background work and APIs](#stage-5-background-work-and-apis)
6. [Stage 6: operating it](#stage-6-operating-it)
7. [Stage 7: other frameworks alongside](#stage-7-other-frameworks-alongside)
8. [After this path](#after-this-path)

## Stage 1: orientation

1. [Exponential for web framework developers](exponential-for-framework-developers.md)
2. Your "coming from" page: [Symfony](coming-from-symfony.md), [Laravel](coming-from-laravel.md),
   [Slim, CodeIgniter, Yii, CakePHP or Laminas](coming-from-micro-and-classic-php-frameworks.md),
   [Django or Rails](coming-from-django-and-rails.md), [Express or Spring](coming-from-express-and-spring.md)
3. [Concept dictionary](concept-dictionary.md)
4. [When to choose Exponential](when-to-choose-exponential.md)

## Stage 2: the request and your first module

1. [The request lifecycle](the-request-lifecycle.md)
2. [Siteaccesses](siteaccesses-hosts-and-tenants.md)
3. [Routing](routing-with-modules-and-views.md), [Pretty URLs](url-aliases-and-wildcards.md)
4. [Controllers](controllers-as-module-views.md), [Responses](responses-redirects-and-errors.md)
5. [The view layer](templates-vs-twig-and-blade.md), [Template resolution](template-resolution-designs-and-overrides.md),
   [Fetch functions](fetch-functions-and-template-data.md)
6. Do it: [Task: build a small CRUD module](your-first-module-step-by-step.md)

## Stage 3: data

1. [The content model for ORM users](the-content-model-for-orm-users.md)
2. [ORM: eZPersistentObject](orm-and-ezpersistentobject.md)
3. [Queries](querying-content-and-tables.md)
4. [Migrations and seed data](migrations-and-dba-schema-files.md)

## Stage 4: cross-cutting concerns

1. [Configuration](configuration-ini-files.md), [Environments and secrets](environments-and-secrets.md)
2. [Dependency injection](dependency-injection-and-services.md), [Extension points](extension-points-and-handlers.md)
3. [Middleware](middleware-and-request-rules.md), [Events and listeners](events-and-listeners.md),
   [Workflows](workflows-triggers-and-domain-events.md)
4. [Authentication](authentication-and-sessions.md), [Authorization](authorization-roles-and-policies.md)
5. [Forms and CSRF](forms-and-csrf-protection.md), [Validation](validation.md)
6. [Caching](caching-layers.md), [HTTP caching](http-caching-and-reverse-proxies.md)
7. [Translations and right-to-left](i18n-l10n-and-rtl.md)

## Stage 5: background work and APIs

1. [Command line](cli-and-console-commands.md)
2. [Queues](queues-and-background-jobs.md), [Scheduled tasks](scheduled-tasks-and-cronjobs.md)
3. [REST and JSON APIs](rest-and-json-apis.md), [Headless front ends](headless-and-decoupled-front-ends.md)

## Stage 6: operating it

1. [Testing](testing.md), [Debugging](debugging-logging-and-errors.md)
2. [Servers and deployment](servers-and-deployment.md), [Packages](packages-extensions-and-composer.md)
3. [Ten pitfalls](pitfalls-for-framework-developers.md), [Checklists](checklists-for-framework-developers.md)

## Stage 7: other frameworks alongside

1. [Mixing Exponential and a framework](mixing-exponential-and-a-framework.md)
2. [The Symfony legacy bridge](symfony-and-the-legacy-bridge.md)
3. [The platform products](the-platform-products.md)

## After this path

- The application creators' guides go deeper into each topic ([Application creators: start here](../creators/app-creators/app-creators-start-here.md),
  [their reading path](../creators/app-creators/app-creators-reading-path.md)).
- The extension development book teaches every extension point step by step ([Extension development](../extension-development/README.md)).
- The template language section explains the language in depth ([The template language](../template-language/README.md)).

## Related pages

- [Web framework developers: start here](web-framework-developers-start-here.md)
- [Web framework developer FAQ](web-framework-developer-faq.md)
- [Glossary for web framework developers](glossary-for-framework-developers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
