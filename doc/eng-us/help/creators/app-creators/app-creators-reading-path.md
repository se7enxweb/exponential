---
title: The application creator's reading path
audience: app-creator, developer
category: app-creation
order: 20
summary: The pages of the application creators' section in the order an application is built - six stages from the first extension to a scaled production system - with what you should be able to do after each stage.
keywords: reading path, learning path, order, stages, application creator, course, curriculum, extension development, step by step
featured: true
type: help
---
# The application creator's reading path

This page is for application creators who want to learn in order. Six stages take you from a first extension to an
application in production. Each stage ends with what you should be able to do.

## Stage 1: orientation

1. [Exponential as an application platform](exponential-as-an-application-platform.md)
2. [Architecture for application creators](architecture-for-app-creators.md)
3. [Setting up a development environment](development-environment.md)
4. [Develop with Git](../../develop-with-git.md)

You can: run a local installation and explain how a request reaches a view.

## Stage 2: the first extension

1. [Your first extension](your-first-extension.md)
2. [Anatomy of an extension](extension-anatomy.md)
3. [Settings and INI files in your application](settings-and-ini-files.md)
4. [Autoloads and class naming](autoloads-and-class-naming.md)
5. [RAD tools: building faster](rad-tools.md)

You can: create, activate and change an extension with a module, a template and settings.

## Stage 3: screens and templates

1. [Custom modules and views](custom-modules-and-views.md) and [module.php reference](module-definition-reference.md)
2. [View results, templates and the pagelayout](view-results-and-the-pagelayout.md)
3. [Policy functions and limitations](policy-functions-and-limitations.md)
4. [Forms, CSRF protection and HTTP input](forms-csrf-and-http-input.md)
5. [Fetch functions](fetch-functions.md), [Writing your own fetch function](writing-a-fetch-function.md)
6. [Writing template operators](template-operators.md), [Template functions, includes and cache blocks](template-functions-and-includes.md)

You can: build screens with forms and permissions, and give templates your data.

## Stage 4: data and processes

1. [The content API in PHP](the-content-api.md), [Creating and updating content in code](creating-content-in-code.md)
2. [Persistent objects and custom tables](persistent-objects-and-custom-tables.md), [Database access and SQL](database-access.md), [Database schema and .dba files](schema-and-dba-files.md)
3. [Writing a custom datatype](custom-datatypes.md), [The datatype life cycle](datatype-lifecycle.md)
4. [Workflows, triggers and events](workflows-and-events.md), [Writing a workflow event type](writing-a-workflow-event-type.md)
5. [Cronjobs for applications](cronjobs-for-applications.md), [Writing a cronjob script](writing-a-cronjob-part.md), [Console commands](console-commands.md), [CLI scripts](cli-scripts.md)
6. [Queues with expqueue](queues-with-expqueue.md), [Content jobs](content-jobs.md), [Background work](background-work-patterns.md)

You can: store and change data safely, hook into publishing, and run work in the background.

## Stage 5: integration

1. [REST API v1 and v2](rest-api.md), [OAuth and API keys](oauth-and-api-keys.md)
2. [JSON endpoints with ezjscore](json-endpoints-with-ezjscore.md), [Remote services with expservices](remote-services-expservices.md)
3. [Headless Exponential](headless-exponential.md)
4. [Notifications and mail from applications](notifications-and-mail.md)
5. [Exponential Intelligence for applications](exponential-intelligence.md), [Extending Exponential Intelligence](extending-exponential-intelligence.md)

You can: connect other programs and AI assistance to your application.

## Stage 6: quality, release and production

1. [Security for applications](security-for-applications.md), [Content Security Policy for your code](content-security-policy.md), [Input validation and output escaping](input-validation-and-escaping.md), [Access control in code](access-control-in-code.md)
2. [Testing applications](testing-applications.md), [DB-less PHPUnit tests](db-less-phpunit-tests.md), [Continuous integration for extensions](ci-for-extensions.md), [Debugging and logs](debugging-and-logs.md)
3. [Packaging your application as an extension](packaging-as-an-extension.md), [Composer packages and Packagist](composer-and-packagist.md), [Versions and releases](versions-and-releases.md), [Content packages](packages-and-ezpkg.md)
4. [Deploying with Velocity](deploying-with-velocity.md), [A deployment pipeline](deployment-pipeline.md), [Environment settings and secrets](environment-settings-and-secrets.md)
5. [Scaling applications](scaling-applications.md), [Caching in applications](caching-in-applications.md), [Clustering and shared storage](clustering-and-shared-storage.md), [Profiling and performance](profiling-and-performance.md)
6. [Symfony and the legacy bridge](symfony-and-the-legacy-bridge.md), when your project spans both stacks

You can: ship a tested, secure, versioned extension and run it in production.

## Going further

The [standards](../../../standards/README.md) describe the rules and patterns of the code base; the website creators'
section covers site building; contributing back to the project starts with [Develop with Git](../../develop-with-git.md)
and [Working with the community](../../leadership/working-with-the-community.md).

## Related pages

- [Application creators: start here](app-creators-start-here.md)
- [Application creators: frequently asked questions](app-creator-faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
