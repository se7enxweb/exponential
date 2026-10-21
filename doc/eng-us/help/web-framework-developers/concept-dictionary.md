---
title: "Concept dictionary: framework terms in Exponential"
audience: web-framework-developer, developer
category: web-frameworks
order: 20
summary: A lookup table from the words of Symfony, Laravel, Django, Rails, Express and Spring to the Exponential term, the class or setting that implements it, and the page that explains it - including the concepts that have no direct equivalent.
keywords: dictionary, concept map, terminology, translation table, controller, route, middleware, ORM, migration, service container, event, queue, cache, template, Twig, Blade, Eloquent, Doctrine, web framework developer
type: help
---
# Concept dictionary: framework terms in Exponential

This page is for developers who think in the terms of another framework and want the matching Exponential term
quickly. Each row names the term, what Exponential uses instead, the class or setting where it lives, and the page
that explains it. Where Exponential has no direct equivalent, the row says so.

## Contents

1. [Request and routing](#request-and-routing)
2. [Views and templates](#views-and-templates)
3. [Data and persistence](#data-and-persistence)
4. [Configuration and wiring](#configuration-and-wiring)
5. [Security](#security)
6. [Background work, APIs and tooling](#background-work-apis-and-tooling)
7. [No direct equivalent](#no-direct-equivalent)

## Request and routing

| Framework term | Exponential term | Where | Read |
|---|---|---|---|
| Front controller | `index.php` with `ezpKernel` and `ezpKernelWeb` | `kernel/private/classes/ezpkernelweb.php` | [The request lifecycle](the-request-lifecycle.md) |
| Host or tenant resolution, app per domain | siteaccess matching | `site.ini [SiteAccessSettings] MatchOrder` | [Siteaccesses](siteaccesses-hosts-and-tenants.md) |
| Route, URL pattern | module and view (`/<module>/<view>/...`) | `extension/<ext>/modules/<module>/module.php` | [Routing](routing-with-modules-and-views.md) |
| Route parameter | ordered `params` and unordered `(name)/value` parameters | `$ViewList[...]['params']`, `unordered_params` | [Routing](routing-with-modules-and-views.md#parameters) |
| Slug, permalink | URL alias | `eZURLAliasML` | [Pretty URLs](url-aliases-and-wildcards.md) |
| Redirect route, rewrite | URL wildcard | `eZURLWildcard` | [Pretty URLs](url-aliases-and-wildcards.md#url-wildcards) |
| Controller, action | module view (script or `Exponential\Runnable\ModuleView` class) | `kernel/private/classes/runnable/moduleview.php` | [Controllers](controllers-as-module-views.md) |
| Response object | the `$Result` array, or output plus `eZExecution::cleanExit()` | `eZModule` | [Responses](responses-redirects-and-errors.md) |
| Middleware, before filter | request rules; `request/input` listeners | `settings/requestrules.ini`, `site.ini [Event]` | [Middleware](middleware-and-request-rules.md) |
| After filter, response middleware | `response/output` filter listeners | `site.ini [Event] Listeners[]` | [Events and listeners](events-and-listeners.md) |

## Views and templates

| Framework term | Exponential term | Where | Read |
|---|---|---|---|
| Twig, Blade, Jinja, ERB | the template language (`.tpl`) | `lib/eztemplate/` | [The view layer](templates-vs-twig-and-blade.md) |
| Base layout, `extends` | the pagelayout (`pagelayout.tpl`) around `$module_result` | design of the siteaccess | [The view layer](templates-vs-twig-and-blade.md#layouts) |
| Template inheritance, theme override | design chain and `override.ini` | `site.ini [DesignSettings]`, `override.ini` | [Template resolution](template-resolution-designs-and-overrides.md) |
| View helper, filter, Twig extension | template operator | `eztemplateautoload.php` | [Fetch functions and operators](fetch-functions-and-template-data.md) |
| View composer, view model | fetch function (`fetch( 'module', 'function', hash() )`) | `function_definition.php` | [Fetch functions and operators](fetch-functions-and-template-data.md) |
| Partial, include | `{include uri='design:...'}` | template language | [The view layer](templates-vs-twig-and-blade.md) |
| Fragment cache | `{cache-block}` | template language | [Caching](caching-layers.md) |
| Translation function | the `i18n` operator, `ezpI18n::tr()` | `kernel/common/ezpi18n.php` | [Translations](i18n-l10n-and-rtl.md) |

## Data and persistence

| Framework term | Exponential term | Where | Read |
|---|---|---|---|
| Model, entity, active record | `eZPersistentObject` subclass | `kernel/classes/ezpersistentobject.php` | [ORM](orm-and-ezpersistentobject.md) |
| Content type, CMS model | content class | `eZContentClass` | [The content model](the-content-model-for-orm-users.md) |
| Record of a content type | content object, its versions and its nodes | `eZContentObject`, `eZContentObjectTreeNode` | [The content model](the-content-model-for-orm-users.md) |
| Field type, column type | datatype | `extends eZDataType` | [The content model](the-content-model-for-orm-users.md#datatypes) |
| Query builder | fetch parameters (`subTreeByNodeID()`), `eZPersistentObject` conditions, `eZDB` SQL | `lib/ezdb/` | [Queries](querying-content-and-tables.md) |
| Database connection | `eZDB::instance()` | `lib/ezdb/classes/` | [Queries](querying-content-and-tables.md) |
| Migration | `.dba` schema file plus your upgrade scripts | `extension/<ext>/share/db_schema.dba` | [Migrations](migrations-and-dba-schema-files.md) |
| Seeder, fixture | `db_data.dba`, content packages, `eZContentFunctions` | `share/`, `.ezpkg` | [Migrations](migrations-and-dba-schema-files.md#seed-data-and-content) |

## Configuration and wiring

| Framework term | Exponential term | Where | Read |
|---|---|---|---|
| `config/*.yaml`, `config/*.php`, `settings.py` | INI files in layers | `settings/`, `extension/<ext>/settings/` | [Configuration](configuration-ini-files.md) |
| `.env`, `APP_ENV` | `EXP_ENV` in `config.env.php` and settings variants | `config.env.php.example` | [Environments and secrets](environments-and-secrets.md) |
| Service container, providers | none in the kernel; handler classes named in INI | `eZExtension::getHandlerClass()` | [Dependency injection](dependency-injection-and-services.md) |
| Bundle, package, app | extension | `extension/<ext>/` | [Packages](packages-extensions-and-composer.md) |
| Event dispatcher | `ezpEvent` | `kernel/private/classes/ezpevent.php` | [Events and listeners](events-and-listeners.md) |
| Model observers, signals, callbacks | workflows and triggers on operations; event listeners | `workflow.ini`, `site.ini [Event]` | [Workflows](workflows-triggers-and-domain-events.md) |

## Security

| Framework term | Exponential term | Where | Read |
|---|---|---|---|
| Authentication, guard, firewall | login handlers, sessions, siteaccess login check | `site.ini [UserSettings] LoginHandler[]` | [Authentication](authentication-and-sessions.md) |
| Authorization, voter, gate, policy | role, policy, function, limitation | `eZRole`, `eZPolicy`, `$FunctionList` | [Authorization](authorization-roles-and-policies.md) |
| CSRF middleware | `ezformtoken` | `extension/ezformtoken/` | [Forms and CSRF](forms-and-csrf-protection.md) |
| Form request, validator | module actions plus your checks; datatype validation for content | `eZInputValidator` | [Validation](validation.md) |
| API tokens | personal API keys, OAuth2, expservices tokens | `rest.ini`, `apikey` module | [REST and JSON APIs](rest-and-json-apis.md) |

## Background work, APIs and tooling

| Framework term | Exponential term | Where | Read |
|---|---|---|---|
| Console, artisan, manage.py | `bin/php/console` | `bin/php/` | [Command line](cli-and-console-commands.md) |
| Queue, job, worker | `expqueue` jobs and handlers | `extension/expqueue/` | [Queues](queues-and-background-jobs.md) |
| Scheduler | cronjob parts run by `runcronjobs.php` | `settings/cronjob.ini` | [Scheduled tasks](scheduled-tasks-and-cronjobs.md) |
| REST resource, API controller | REST provider, ezjscore server function, expservices | `ezprestapi`, `ezjscore.ini` | [REST and JSON APIs](rest-and-json-apis.md) |
| Profiler toolbar | the Exp debug bar | `site.ini [DebugSettings]` | [Debugging](debugging-logging-and-errors.md) |
| Application server (Octane, RoadRunner) | Exponential Velocity, FrankenPHP | `settings/velocity.ini` | [Servers and deployment](servers-and-deployment.md) |

## No direct equivalent

Say these out loud early in a project, so nobody looks for them:

- **A general dependency injection container with autowiring.** Not in the legacy kernel.
- **Annotation or attribute routing.** Routes are declared in `module.php` arrays only.
- **Migration classes with up and down methods and a migrations table.** Schema files and your own upgrade scripts
  take their place.
- **Template inheritance with blocks.** Overrides and includes take its place.
- **Automatic output escaping.** Templates escape with `|wash` where you write it
  ([No automatic escaping](../template-language/smarty-differences-in-depth.md#no-automatic-escaping)).

## Related pages

- [Glossary for web framework developers](glossary-for-framework-developers.md)
- [PHP developers: start here](../php-developers/php-developers-start-here.md)
- [Application creators: start here](../creators/app-creators/app-creators-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
