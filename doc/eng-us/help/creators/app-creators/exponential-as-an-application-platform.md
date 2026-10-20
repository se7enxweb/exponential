---
title: Exponential as an application platform
audience: app-creator, developer, project-manager
category: app-creation
order: 30
summary: What Exponential gives an application out of the box - content model, permissions, versions, languages, modules, templates, workflows, cron, REST, queues, AI - and how to decide what to build on it and what to keep outside.
keywords: application platform, CMF, framework, build applications, content model, permissions, modules, REST, headless, workflows, cronjobs, queues, Exponential Intelligence, app creator
type: help
---
# Exponential as an application platform

This page is for application creators who consider building an application on Exponential: an intranet tool, a
portal, a customer area, a data-driven site, a headless back end. Exponential is "a professional PHP application
framework with advanced CMS functionality" (the README); this page lists what that framework gives you and how to
decide what to build on it.

## Contents

1. [What you get without writing code](#what-you-get-without-writing-code)
2. [What you write](#what-you-write)
3. [Kinds of applications](#kinds-of-applications)
4. [When to build on it, and when not](#when-to-build-on-it-and-when-not)
5. [Where to go next](#where-to-go-next)

## What you get without writing code

| Need of an application | Exponential gives | Read |
|---|---|---|
| A data model | content classes with typed attributes, versions, translations, a tree | [The content model](../../../guides/content-model-and-editing.md) |
| Users and permissions | users, groups, roles, policies with limitations | [Roles and policies](../../../guides/roles-and-policies.md) |
| An admin interface | the administration for every content class, for free | [The admin tour](../../admin-tour.md) |
| Business processes | workflows on triggers, object states | [Workflows](../../../guides/workflows.md), [Object states](../../../guides/object-states.md) |
| Scheduled work | cronjob parts | [Cronjobs](../../../guides/cronjobs.md) |
| Background jobs | the `expqueue` job queue and content jobs | [Queues with expqueue](queues-with-expqueue.md) |
| An API | REST v1/v2 with OAuth2 and API keys; over a thousand named services in `expservices` | [REST API v1 and v2](rest-api.md), [Remote services](remote-services-expservices.md) |
| Search | built-in or Solr | [Site search](../website-creators/site-search.md) |
| Audit | an audit trail of changes | [Audit trail](../../../features/6.0/audit-trail.md) |
| AI assistance | Exponential Intelligence (`expai`) | [Exponential Intelligence for applications](exponential-intelligence.md) |
| Serving | Velocity, Apache, nginx, FrankenPHP | [Serving the site](../../../install/08-serving-the-site.md) |

## What you write

Everything specific to your application lives in an **extension**: a directory under `extension/` with modules
(addresses and views), classes, templates, settings, datatypes, workflow events, cronjob scripts and console commands.
Setup > RAD lists 68 extension points and writes working skeletons for most of them
([RAD tools](rad-tools.md)).

## Kinds of applications

| Kind | Built from |
|---|---|
| A data application with forms and lists | content classes + custom views for special screens |
| A portal or members area | sections, roles, registration, custom modules |
| A headless back end for apps | content classes + REST or expservices ([Headless Exponential](headless-exponential.md)) |
| A process tool (approvals, requests) | object states, workflows, notifications |
| An integration hub | cronjobs, queues, REST clients in PHP classes |

## When to build on it, and when not

Build on it when the data is content-like (people edit it, it has versions, permissions and languages), when the
built-in administration saves you an admin interface, and when you want one place for site and application.

Keep it outside when the core is high-volume transactional data with no editorial life (a ledger, telemetry); store that
in your own tables ([Persistent objects and custom tables](persistent-objects-and-custom-tables.md)) or a separate
service, and connect it.

## Where to go next

- [Architecture for application creators](architecture-for-app-creators.md)
- [Setting up a development environment](development-environment.md)
- [Your first extension](your-first-extension.md)
- The extension developer book in `doc/eng-us/help/extension-development/`, when it is in your installation.

## Related pages

- [Application creators: start here](app-creators-start-here.md)
- [What Exponential is: the product family](../../leadership/what-exponential-is.md)
- [Standards](../../../standards/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
