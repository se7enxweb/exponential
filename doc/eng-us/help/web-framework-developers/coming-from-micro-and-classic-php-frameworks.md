---
title: Coming from Slim, CodeIgniter, Yii, CakePHP or Laminas
audience: web-framework-developer, developer
category: web-frameworks
order: 450
summary: A guide for developers from Slim, CodeIgniter, Yii, CakePHP and Laminas (Zend Framework) - how their controllers, models, views, configuration, middleware and modules map to Exponential, which conventions will feel familiar, and what is different about a content-first framework.
keywords: Slim, CodeIgniter, Yii, CakePHP, Laminas, Zend Framework, Mezzio, MVC, coming from, controllers, models, views, active record, modules, middleware, PSR-7, PSR-15, web framework developer
type: help
---
# Coming from Slim, CodeIgniter, Yii, CakePHP or Laminas

This page is for developers whose PHP background is one of the classic MVC frameworks or a micro framework. The good
news: Exponential's PHP style (classes with static helpers, active record, convention over configuration, arrays as
configuration) is closer to CodeIgniter, Yii 1 and CakePHP than to container-heavy frameworks. The difference is that
Exponential is content-first: a model of content, permissions and an administration are already there.

## Contents

1. [The MVC pieces](#the-mvc-pieces)
2. [Framework by framework](#framework-by-framework)
3. [PSR standards](#psr-standards)
4. [What is new for you](#what-is-new-for-you)

## The MVC pieces

| Piece | In your framework | In Exponential 6 |
|---|---|---|
| Controller | a class with action methods | a module view per address ([Controllers](controllers-as-module-views.md)) |
| Model | active record (CodeIgniter models, Yii `ActiveRecord`, CakePHP tables and entities) or a table gateway (Laminas) | `eZPersistentObject` for your tables, the content API for content ([ORM](orm-and-ezpersistentobject.md)) |
| View | PHP templates, Twig or Plates | the template language ([The view layer](templates-vs-twig-and-blade.md)) |
| Routes | a route file or conventions | `/<module>/<view>` conventions ([Routing](routing-with-modules-and-views.md)) |
| Configuration | PHP arrays | INI files in layers ([Configuration](configuration-ini-files.md)) |
| Modules, plugins | HMVC modules, Yii modules, CakePHP plugins, Laminas modules | extensions ([Packages](packages-extensions-and-composer.md)) |

## Framework by framework

| Framework | Familiar | Different |
|---|---|---|
| **Slim** | small core, you choose the parts; middleware thinking | no PSR-15 pipeline; request rules and listeners instead ([Middleware](middleware-and-request-rules.md)) |
| **CodeIgniter** | `/controller/method/param` addresses look like `/module/view/param`; helpers and libraries | permissions, sessions and CSRF are built in differently ([Authentication](authentication-and-sessions.md), [Forms and CSRF](forms-and-csrf-protection.md)) |
| **Yii** | active record, behaviors, modules, a code generator (Gii) | the RAD tools take the place of Gii ([RAD tools](../../features/6.0/rad-tools.md)); RBAC is roles and policies stored as data ([Authorization](authorization-roles-and-policies.md)) |
| **CakePHP** | conventions, bake, plugins, ORM with tables | schema files instead of migrations plugin ([Migrations](migrations-and-dba-schema-files.md)) |
| **Laminas, Mezzio** | modules, configuration merging, event manager | no service manager ([Dependency injection](dependency-injection-and-services.md)); `ezpEvent` instead of the event manager ([Events](events-and-listeners.md)) |

## PSR standards

Exponential 6 is installed with Composer and loads Composer's autoloader, so PSR-4 libraries work in your extension.
The kernel itself predates PSR-7 and PSR-15: requests are read through `eZHTTPTool` and `eZSys`, responses are a result
array or direct output, and there is no PSR-11 container. Do not plan on passing PSR-7 objects through the kernel.

## What is new for you

- **Content classes** replace most tables you would design ([The content model](the-content-model-for-orm-users.md)).
- **Siteaccesses** select a whole configuration per request ([Siteaccesses](siteaccesses-hosts-and-tenants.md)).
- **Template overrides** choose templates by class, node or view mode ([Template resolution](template-resolution-designs-and-overrides.md)).
- **Workflows** hook into publishing and checkout ([Workflows](workflows-triggers-and-domain-events.md)).
- **Persistent workers** under Velocity: no per-request state in statics ([Servers and deployment](servers-and-deployment.md)).

Start with [Task: build a small CRUD module](your-first-module-step-by-step.md); it will feel like the first controller
you wrote in your framework.

## Related pages

- [Concept dictionary](concept-dictionary.md)
- [Coming from Symfony](coming-from-symfony.md), [Coming from Laravel](coming-from-laravel.md)
- [Autoloads and class naming](../creators/app-creators/autoloads-and-class-naming.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
