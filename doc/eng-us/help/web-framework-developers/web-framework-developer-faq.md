---
title: Web framework developer FAQ
audience: web-framework-developer, developer
category: web-frameworks
order: 520
summary: Short answers to the questions developers from Symfony, Laravel, Django, Rails, Express and Spring ask most about Exponential - is it a framework, where is the container, can I use Twig or Composer libraries, how do migrations work, is there an ORM, async, GraphQL, tests, Octane-style workers, and where to start.
keywords: FAQ, questions, answers, framework, container, Twig, Composer, migrations, ORM, GraphQL, PSR, Octane, async, tests, start, web framework developer
type: help
---
# Web framework developer FAQ

This page is for developers with a quick question. Each answer links to the page with the details.

## Contents

1. [Is Exponential a framework or a CMS?](#is-exponential-a-framework-or-a-cms)
2. [Where is the service container?](#where-is-the-service-container)
3. [Is there a router with named routes?](#is-there-a-router-with-named-routes)
4. [Is there an ORM?](#is-there-an-orm)
5. [How do migrations work?](#how-do-migrations-work)
6. [Can I use Twig or Blade?](#can-i-use-twig-or-blade)
7. [Can I use Composer libraries?](#can-i-use-composer-libraries)
8. [Does it follow PSR-7 and PSR-15?](#does-it-follow-psr-7-and-psr-15)
9. [Is there middleware?](#is-there-middleware)
10. [Is there an equivalent of Octane or RoadRunner?](#is-there-an-equivalent-of-octane-or-roadrunner)
11. [Is there GraphQL?](#is-there-graphql)
12. [How do I test?](#how-do-i-test)
13. [How do I run background jobs?](#how-do-i-run-background-jobs)
14. [Which databases?](#which-databases)
15. [Where do I start?](#where-do-i-start)

## Is Exponential a framework or a CMS?

Both. Its README calls it "a professional PHP application framework with advanced CMS (content management system)
functionality" ([Exponential for web framework developers](exponential-for-framework-developers.md)).

## Where is the service container?

There is none in the Exponential 6 kernel. Classes are found through generated class maps, configurable
implementations through handler settings ([Dependency injection and services](dependency-injection-and-services.md)).

## Is there a router with named routes?

No. Addresses follow `/<module>/<view>/<parameters>`, declared in `module.php`; content gets URL aliases automatically
([Routing](routing-with-modules-and-views.md), [Pretty URLs](url-aliases-and-wildcards.md)).

## Is there an ORM?

`eZPersistentObject` maps your own tables to classes; content uses the content API with versions and translations
([ORM](orm-and-ezpersistentobject.md), [The content model](the-content-model-for-orm-users.md)).

## How do migrations work?

Tables are described in database-neutral `.dba` files; you ship an install command and upgrade steps. There is no
migration runner ([Migrations](migrations-and-dba-schema-files.md)).

## Can I use Twig or Blade?

Not in Exponential 6 alone. Twig is available when Exponential 6 runs inside Symfony through the legacy bridge
([The view layer](templates-vs-twig-and-blade.md#can-i-use-twig-instead)).

## Can I use Composer libraries?

Yes. `autoload.php` loads Composer's autoloader, and extensions are Composer packages
([Packages](packages-extensions-and-composer.md)).

## Does it follow PSR-7 and PSR-15?

The kernel predates them: requests are read through `eZHTTPTool`, responses are result arrays or direct output
([Coming from Slim, CodeIgniter, Yii, CakePHP or Laminas](coming-from-micro-and-classic-php-frameworks.md#psr-standards)).

## Is there middleware?

Request rules, `request/input` listeners and `response/output` filters cover most middleware uses
([Middleware](middleware-and-request-rules.md)).

## Is there an equivalent of Octane or RoadRunner?

Yes: Exponential Velocity runs the installation in persistent workers and is part of Exponential as of 6.0.15;
FrankenPHP is the alternative ([Servers and deployment](servers-and-deployment.md)).

## Is there GraphQL?

Not in Exponential 6; its APIs are REST, ezjscore and expservices. The Symfony-based platform products ship GraphQL
([REST and JSON APIs](rest-and-json-apis.md), [The platform products](the-platform-products.md)).

## How do I test?

PHPUnit; most tests without a database, integration tests against a throwaway database
([Testing](testing.md)).

## How do I run background jobs?

With the `expqueue` job queue, cronjob parts and content jobs ([Queues](queues-and-background-jobs.md)).

## Which databases?

SQLite (the default), PostgreSQL, MongoDB, MariaDB, Oracle and MySQL ([Database support](../database-support.md)).

## Where do I start?

At [Web framework developers: start here](web-framework-developers-start-here.md), then the
[reading path](web-framework-developers-reading-path.md).

## Related pages

- [Concept dictionary](concept-dictionary.md)
- [Glossary for web framework developers](glossary-for-framework-developers.md)
- [Application creator FAQ](../creators/app-creators/app-creator-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
