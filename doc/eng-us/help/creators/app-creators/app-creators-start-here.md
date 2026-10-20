---
title: "Application creators: start here"
audience: app-creator, developer
category: app-creation
order: 10
summary: The landing page for application creators - what Exponential gives an application, a first-week plan, and every page of this section grouped by task, from the first extension to APIs, AI, queues, testing, deployment, security and scaling.
keywords: application creator, start here, build applications, extension development, modules, APIs, REST, expai, expqueue, testing, deployment, Velocity, security, scaling
featured: true
type: help
---
# Application creators: start here

This page is for developers who build applications on Exponential: custom modules and screens, integrations, headless
back ends, process tools, AI-assisted features. It tells you what this section offers, what to do in your first week,
and where every answer is. Exponential is the open source content management system and application framework that runs
in this administration; it continues the code base of eZ Publish, a product its creator discontinued after almost 18
years, and is led by 7x together with the Exponential Foundation ([Who is 7x?](../../who-is-7x.md),
[Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md)).

## Contents

1. [Your first week](#your-first-week)
2. [Foundations](#foundations)
3. [Modules, views and templates](#modules-views-and-templates)
4. [Data](#data)
5. [Processes and background work](#processes-and-background-work)
6. [APIs and AI](#apis-and-ai)
7. [Packaging and releases](#packaging-and-releases)
8. [Testing, deployment and operations](#testing-deployment-and-operations)
9. [Security](#security)
10. [Scaling](#scaling)
11. [Other documentation you will use](#other-documentation-you-will-use)

## Your first week

| Day | Do | Page |
|---|---|---|
| 1 | Read what the platform gives you and how a request flows | [Exponential as an application platform](exponential-as-an-application-platform.md), [Architecture](architecture-for-app-creators.md) |
| 2 | Set up a local installation | [Setting up a development environment](development-environment.md) |
| 3 | Build the first extension, then let Setup > RAD build another | [Your first extension](your-first-extension.md), [RAD tools](rad-tools.md) |
| 4 | Add a view with a form and a policy | [Custom modules and views](custom-modules-and-views.md), [Forms, CSRF protection and HTTP input](forms-csrf-and-http-input.md) |
| 5 | Read and write content in PHP; write a test | [The content API](the-content-api.md), [DB-less PHPUnit tests](db-less-phpunit-tests.md) |

The [reading path](app-creators-reading-path.md) puts every page below in order.

## Foundations

- [Exponential as an application platform](exponential-as-an-application-platform.md)
- [Architecture for application creators](architecture-for-app-creators.md)
- [Setting up a development environment](development-environment.md)
- [Your first extension](your-first-extension.md)
- [Anatomy of an extension](extension-anatomy.md)
- [Settings and INI files in your application](settings-and-ini-files.md)
- [Autoloads and class naming](autoloads-and-class-naming.md)
- [RAD tools: building faster](rad-tools.md)

## Modules, views and templates

- [Custom modules and views](custom-modules-and-views.md)
- [module.php reference: views, parameters and functions](module-definition-reference.md)
- [View results, templates and the pagelayout](view-results-and-the-pagelayout.md)
- [Policy functions and limitations](policy-functions-and-limitations.md)
- [Forms, CSRF protection and HTTP input](forms-csrf-and-http-input.md)
- [Fetch functions: reading data in templates](fetch-functions.md)
- [Writing your own fetch function](writing-a-fetch-function.md)
- [Writing template operators](template-operators.md)
- [Template functions, includes and cache blocks](template-functions-and-includes.md)

## Data

- [The content API in PHP](the-content-api.md)
- [Creating and updating content in code](creating-content-in-code.md)
- [Persistent objects and custom tables](persistent-objects-and-custom-tables.md)
- [Database access and SQL](database-access.md)
- [Database schema and .dba files](schema-and-dba-files.md)
- [Writing a custom datatype](custom-datatypes.md)
- [The datatype life cycle](datatype-lifecycle.md)

## Processes and background work

- [Workflows, triggers and events](workflows-and-events.md)
- [Writing a workflow event type](writing-a-workflow-event-type.md)
- [Cronjobs for applications](cronjobs-for-applications.md)
- [Writing a cronjob script](writing-a-cronjob-part.md)
- [Console commands](console-commands.md)
- [CLI scripts with eZScript](cli-scripts.md)
- [Content jobs](content-jobs.md)
- [Queues with expqueue](queues-with-expqueue.md)
- [Background work: queues, cronjobs and content jobs](background-work-patterns.md)

## APIs and AI

- [REST API v1 and v2 for applications](rest-api.md)
- [JSON endpoints with ezjscore](json-endpoints-with-ezjscore.md)
- [OAuth and API keys](oauth-and-api-keys.md)
- [Remote services with expservices](remote-services-expservices.md)
- [Headless Exponential](headless-exponential.md)
- [Notifications and mail from applications](notifications-and-mail.md)
- [Exponential Intelligence (expai) for applications](exponential-intelligence.md)
- [Extending Exponential Intelligence](extending-exponential-intelligence.md)

## Packaging and releases

- [Packaging your application as an extension](packaging-as-an-extension.md)
- [Composer packages and Packagist](composer-and-packagist.md)
- [Versions and releases of your extension](versions-and-releases.md)
- [Content packages (.ezpkg)](packages-and-ezpkg.md)

## Testing, deployment and operations

- [Testing applications](testing-applications.md)
- [DB-less PHPUnit tests](db-less-phpunit-tests.md)
- [Continuous integration for extensions](ci-for-extensions.md)
- [Debugging and logs](debugging-and-logs.md)
- [Deploying with Velocity](deploying-with-velocity.md)
- [A deployment pipeline](deployment-pipeline.md)
- [Environment settings and secrets](environment-settings-and-secrets.md)

## Security

- [Security for applications](security-for-applications.md)
- [Content Security Policy for your code](content-security-policy.md)
- [Input validation and output escaping](input-validation-and-escaping.md)
- [Access control in code](access-control-in-code.md)

## Scaling

- [Scaling applications](scaling-applications.md)
- [Caching in applications](caching-in-applications.md)
- [Clustering and shared storage](clustering-and-shared-storage.md)
- [Profiling and performance](profiling-and-performance.md)
- [Symfony and the legacy bridge](symfony-and-the-legacy-bridge.md)
- [Application creators: frequently asked questions](app-creator-faq.md)

## Other documentation you will use

| For | Read |
|---|---|
| Rules and patterns of the code base | [Standards](../../../standards/README.md), [Patterns](../../../standards/patterns/README.md) |
| Every extension point and how it is registered | [Extension points](../../../bc/6.0/rad-extension-points.md) |
| Site building (templates, layouts, SEO) | [Website creators: start here](../website-creators/website-creators-start-here.md) |
| Working with Git and contributing | [Develop with Git](../../develop-with-git.md) |
| Installing and serving | [Installation book](../../../install/README.md) |
| The extension developer book | `doc/eng-us/help/extension-development/`, when it is in your installation |

> **Tip**
> The help's search finds every page of this section: filter it by the audience **Application creators**.

## Related pages

- [The application creator's reading path](app-creators-reading-path.md)
- [Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
