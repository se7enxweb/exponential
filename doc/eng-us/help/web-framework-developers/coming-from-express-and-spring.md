---
title: Coming from Express or Spring
audience: web-framework-developer, developer
category: web-frameworks
order: 470
summary: A guide for Node.js (Express, NestJS) and Java (Spring, Spring Boot) developers - the PHP request model compared with an event loop and a servlet container, routing, middleware and interceptors, dependency injection, data access, configuration profiles, security, scheduling and messaging, and how to put Exponential behind a JavaScript front end.
keywords: Express, NestJS, Node.js, Spring, Spring Boot, Java, coming from, event loop, servlet, middleware, interceptors, dependency injection, profiles, Spring Security, @Scheduled, JPA, Hibernate, web framework developer
type: help
---
# Coming from Express or Spring

This page is for developers who build web applications with Express or NestJS on Node.js, or with Spring and Spring
Boot on the JVM. The biggest shift is not the language but the process model; after that, most concepts have a
counterpart, and a few that Spring developers rely on (a DI container, annotations) do not.

## Contents

1. [The process model](#the-process-model)
2. [Side by side](#side-by-side)
3. [Keeping your front end](#keeping-your-front-end)
4. [What you will miss](#what-you-will-miss)

## The process model

| | Express, NestJS | Spring | Exponential 6 |
|---|---|---|---|
| Process | one long-running process, event loop | long-running JVM, a thread per request | PHP-FPM: a fresh run per request; Velocity: persistent workers, one request at a time each |
| State between requests | in memory, shared | singletons, shared | none under PHP-FPM; per worker under Velocity, which you must not rely on |
| Concurrency inside a request | async I/O | threads, reactive stacks | synchronous; slow work goes to a queue |
| Real-time | WebSockets | WebSocket support | Velocity's WebSockets and Server-Sent Events ([Velocity WebSockets](../../features/6.0/velocity-websockets-and-events.md)) |

Under Velocity, the rules resemble those of a long-running process: reset per-request state, register listeners once
([Servers and deployment](servers-and-deployment.md)).

## Side by side

| Express / NestJS | Spring | Exponential 6 | Page |
|---|---|---|---|
| `app.get()`, controllers with decorators | `@RequestMapping` | `module.php` views | [Routing](routing-with-modules-and-views.md) |
| middleware, guards, interceptors | filters, `HandlerInterceptor` | request rules, listeners, runnable events | [Middleware](middleware-and-request-rules.md) |
| NestJS providers | beans, `@Autowired` | no container; handler classes from INI | [Dependency injection](dependency-injection-and-services.md) |
| an ORM (TypeORM, Prisma, Sequelize) | JPA, Hibernate, Spring Data | content API; `eZPersistentObject` | [ORM](orm-and-ezpersistentobject.md) |
| `.env`, config modules | `application.yml`, profiles | INI files, `EXP_ENV` | [Configuration](configuration-ini-files.md) |
| Passport.js, guards | Spring Security | login handlers, roles and policies | [Authentication](authentication-and-sessions.md), [Authorization](authorization-roles-and-policies.md) |
| template engines (EJS, Pug, Handlebars) | Thymeleaf | the template language | [The view layer](templates-vs-twig-and-blade.md) |
| job queues (BullMQ) | `@Async`, JMS, Spring Batch | `expqueue`, content jobs | [Queues](queues-and-background-jobs.md) |
| node-cron | `@Scheduled` | cronjob parts | [Scheduled tasks](scheduled-tasks-and-cronjobs.md) |
| EventEmitter | `ApplicationEventPublisher` | `ezpEvent` | [Events](events-and-listeners.md) |
| npm scripts, CLIs | `CommandLineRunner` | `bin/php/console` | [Command line](cli-and-console-commands.md) |
| Jest, Supertest | JUnit, MockMvc | PHPUnit, HTTP checks | [Testing](testing.md) |

## Keeping your front end

Many teams from these ecosystems keep a JavaScript or Java front end and use Exponential as the content back end: the
REST API for content, OAuth2 or API keys for credentials, editors in the administration
([Headless and decoupled front ends](headless-and-decoupled-front-ends.md), [REST and JSON APIs](rest-and-json-apis.md)).

## What you will miss

- Annotations or decorators for routes, security and transactions.
- A DI container and typed configuration properties.
- In-process async I/O; use queues for slow calls.
- Generated API documentation (OpenAPI) for the REST API.

## Related pages

- [Concept dictionary](concept-dictionary.md)
- [When to choose Exponential](when-to-choose-exponential.md)
- [PHP developers: start here](../php-developers/php-developers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
