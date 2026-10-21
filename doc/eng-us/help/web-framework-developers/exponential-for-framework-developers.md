---
title: Exponential for web framework developers
audience: web-framework-developer, developer
category: web-frameworks
order: 10
summary: What Exponential is when you look at it with the eyes of a Symfony, Laravel, Django or Rails developer - a PHP application framework with a content model, permissions, templates and a server built in - which parts feel familiar, which are different, and where each part lives in the installation.
keywords: framework, application framework, CMF, CMS, overview, architecture, kernel, extension, module, view, siteaccess, INI, template language, Velocity, web framework developer
type: help
---
# Exponential for web framework developers

This page is for developers who know a web framework and want a first, honest picture of Exponential. Its README
describes it as "a professional PHP application framework with advanced CMS (content management system)
functionality", and both halves of that sentence matter: there is a framework underneath (front controller, routing to
modules and views, templates, a database layer, settings, a console), and on top of it a content model with versions,
translations, permissions and an administration that you get without writing code.

## Contents

1. [In one paragraph](#in-one-paragraph)
2. [What feels familiar](#what-feels-familiar)
3. [What is different](#what-is-different)
4. [Where things live](#where-things-live)
5. [How you add code](#how-you-add-code)
6. [Where to go next](#where-to-go-next)

## In one paragraph

Exponential 6 is a PHP code base with a long history: it was developed from 1999, first under the name eZ Publish,
which its creator discontinued after almost 18 years; 7x continued it, and the product has been called Exponential
since August 2025 ([Who is 7x?](../who-is-7x.md)). It is installed with Composer, runs on PHP 8, stores data in
SQLite, PostgreSQL, MySQL/MariaDB, Oracle or MongoDB ([Database support](../database-support.md)), and is served by
Apache or nginx with PHP-FPM, by Exponential Velocity (a persistent-worker server written in PHP that is part of the
product as of 6.0.15) or by FrankenPHP ([Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md)).

## What feels familiar

| You know | Exponential has |
|---|---|
| A front controller (`public/index.php`) | `index.php`, which builds `ezpKernel( new ezpKernelWeb() )` and returns its content |
| Routes to controller actions | modules and views: `/<module>/<view>/<params>`, declared in `module.php` ([Routing](routing-with-modules-and-views.md)) |
| A template engine | the template language (`.tpl` files compiled to PHP by `eZTemplate`) ([The view layer](templates-vs-twig-and-blade.md)) |
| An ORM or active record | `eZPersistentObject` for your own tables, and the content API for content ([ORM](orm-and-ezpersistentobject.md)) |
| A console | `bin/php/console` with `exp:*`, `ext:*` and `cron:*` commands ([Command line](cli-and-console-commands.md)) |
| Packages | extensions, installed as Composer packages ([Packages](packages-extensions-and-composer.md)) |
| Configuration files | INI files in layers ([Configuration](configuration-ini-files.md)) |
| Tests | PHPUnit, most suites without a database ([Testing](testing.md)) |

## What is different

- **Content is a first-class model.** Most data an editor touches is not a table you design, but a *content class*
  (a type with attributes) whose *objects* have *versions* and *translations* and sit at *nodes* in a tree. You
  define classes in the administration and get editing, history, permissions and search for free
  ([The content model for ORM users](the-content-model-for-orm-users.md)).
- **Siteaccesses instead of one application config.** A request is first matched to a *siteaccess* (a site, a
  language version, the administration), and the settings of that siteaccess are loaded before routing
  ([Siteaccesses](siteaccesses-hosts-and-tenants.md)).
- **No general dependency injection container.** The legacy kernel finds classes through INI settings, naming
  conventions and a generated class map. When it runs inside Symfony through the legacy bridge it can receive
  Symfony's container, but the kernel itself does not define services in one ([Dependency injection](dependency-injection-and-services.md)).
- **Templates are overridden, not extended.** A design chain and `override.ini` rules decide which template renders
  a view; there is no block inheritance as in Twig or Blade ([Template resolution](template-resolution-designs-and-overrides.md)).
- **Permissions are data.** Roles made of policies (module, function, limitations) are stored in the database and
  assigned in the administration; your module declares the functions, administrators grant them
  ([Authorization](authorization-roles-and-policies.md)).
- **Schema changes are not migrations classes.** Tables are described in database-neutral `.dba` files; upgrades are
  scripts you ship ([Migrations](migrations-and-dba-schema-files.md)).

## Where things live

From [Architecture for application creators](../creators/app-creators/architecture-for-app-creators.md):

```text
kernel/, lib/      the kernel: modules, classes, the template engine, the database layer
autoload/          the kernel's class map (autoload/ezp_kernel.php)
extension/         extensions: your application lives here
design/            designs: templates, stylesheets, images
settings/          shipped settings; settings/siteaccess/ and settings/override/ hold yours
var/               runtime: caches, logs, storage, the SQLite database
bin/php/console    one entry point for every command
cronjobs/          the scripts runcronjobs.php runs
index.php          the front controller
```

## How you add code

You never edit `kernel/` or `lib/`. Everything you write goes into an **extension**, the unit that plays the role of
a Symfony bundle, a Laravel package or a Django app: modules (your routes and screens), classes, templates, settings,
datatypes, workflow event types, cronjob parts and console commands. **Setup > RAD** (`/setup/rad`) lists every
extension point and writes the boilerplate for most of them ([RAD tools](../../features/6.0/rad-tools.md),
[Extension points](../../bc/6.0/rad-extension-points.md)).

## Where to go next

1. [Concept dictionary](concept-dictionary.md): your framework's words and their Exponential counterparts.
2. [The request lifecycle](the-request-lifecycle.md): what happens between `index.php` and the response.
3. [Task: build a small CRUD module](your-first-module-step-by-step.md): the whole path once, by hand.
4. The coming-from pages: [Symfony](coming-from-symfony.md), [Laravel](coming-from-laravel.md),
   [Slim, CodeIgniter, Yii, CakePHP or Laminas](coming-from-micro-and-classic-php-frameworks.md),
   [Django and Rails](coming-from-django-and-rails.md), [Express and Spring](coming-from-express-and-spring.md).

## Related pages

- [Web framework developers: start here](web-framework-developers-start-here.md)
- [Exponential as an application platform](../creators/app-creators/exponential-as-an-application-platform.md)
- [Kernel architecture](../../standards/architecture/kernel-architecture.md)
- [Extension development: the book](../extension-development/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
