---
title: Coming from Django or Rails
audience: web-framework-developer, developer
category: web-frameworks
order: 460
summary: A guide for Django and Ruby on Rails developers moving to a PHP framework with a CMS built in - URLconf and routes, views and controllers, the ORM and migrations, the Django admin, templates, signals and callbacks, management commands and rake tasks, Celery and Active Job, each with its Exponential counterpart, plus the PHP habits to pick up.
keywords: Django, Rails, Ruby on Rails, Python, Ruby, coming from, URLconf, routes, ORM, ActiveRecord, migrations, Django admin, signals, callbacks, management commands, rake, Celery, Active Job, Wagtail, web framework developer
type: help
---
# Coming from Django or Rails

This page is for developers who know Django or Rails and are new to PHP or to Exponential. Both frameworks share
Exponential's spirit of "batteries included": Django ships an admin, Rails ships generators and conventions. Exponential
ships an editorial administration, a content model with versions and translations, permissions, and a template
language.

## Contents

1. [Side by side](#side-by-side)
2. [The admin you already know, and more](#the-admin-you-already-know-and-more)
3. [PHP for Python and Ruby developers](#php-for-python-and-ruby-developers)
4. [What you will miss](#what-you-will-miss)

## Side by side

| Django | Rails | Exponential 6 | Page |
|---|---|---|---|
| `urls.py` | `config/routes.rb` | `module.php`, URL aliases | [Routing](routing-with-modules-and-views.md) |
| views | controllers | module views | [Controllers](controllers-as-module-views.md) |
| templates (DTL, Jinja) | ERB | the template language | [The view layer](templates-vs-twig-and-blade.md) |
| models, ORM | ActiveRecord | content classes; `eZPersistentObject` | [The content model](the-content-model-for-orm-users.md) |
| migrations | migrations | `.dba` files and upgrade scripts | [Migrations](migrations-and-dba-schema-files.md) |
| `settings.py` | `config/environments/*.rb` | INI files, `EXP_ENV` | [Configuration](configuration-ini-files.md) |
| middleware | Rack middleware, filters | request rules, listeners | [Middleware](middleware-and-request-rules.md) |
| signals | callbacks | `ezpEvent`, workflows | [Events](events-and-listeners.md) |
| `manage.py` commands | `bin/rails`, rake | `bin/php/console` | [Command line](cli-and-console-commands.md) |
| Celery | Active Job, Sidekiq | `expqueue` | [Queues](queues-and-background-jobs.md) |
| `contrib.auth`, permissions | Devise, Pundit | users as content, roles and policies | [Authentication](authentication-and-sessions.md), [Authorization](authorization-roles-and-policies.md) |
| `sites` framework | multiple apps or engines | siteaccesses | [Siteaccesses](siteaccesses-hosts-and-tenants.md) |
| gettext | I18n | `.ts` catalogues, translated content | [Translations](i18n-l10n-and-rtl.md) |
| Gunicorn, Uvicorn | Puma | PHP-FPM, Velocity, FrankenPHP | [Servers and deployment](servers-and-deployment.md) |

## The admin you already know, and more

The Django admin generates forms for models; Exponential's administration is built for editors rather than developers:
a content tree, drafts and versions, translations side by side, a rich text editor, image variations, approval
workflows, a page builder, a trash, an audit trail and roles. Content-first Python and Ruby CMSs exist too; if you know
one, Exponential's content classes will feel natural. Start with the
[administration tour](../admin-tour.md) before writing code.

## PHP for Python and Ruby developers

- Each request starts from `index.php`; under PHP-FPM nothing survives between requests, under Velocity the workers
  persist like a Gunicorn or Puma worker would ([The request lifecycle](the-request-lifecycle.md)).
- Composer is the package manager (pip, Bundler) and Packagist the index.
- Arrays are both lists and dictionaries; much configuration in `module.php` and `definition()` is arrays.
- Naming: kernel classes use the `eZ` prefix, the project's new code uses `exp`, and your own classes take a prefix of
  your organization or extension ([Autoloads and class naming](../creators/app-creators/autoloads-and-class-naming.md)).
- The PHP developers' pages cover the language side ([PHP developers: start here](../php-developers/php-developers-start-here.md)).

## What you will miss

- Model-driven migrations generated from code changes.
- A model-to-form layer like Django forms or `form_with`.
- An in-process test client.
- Generators for every piece; the RAD tools cover the common ones ([RAD tools](../../features/6.0/rad-tools.md)).

## Related pages

- [Concept dictionary](concept-dictionary.md)
- [Coming from Express or Spring](coming-from-express-and-spring.md)
- [Task: build a small CRUD module](your-first-module-step-by-step.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
