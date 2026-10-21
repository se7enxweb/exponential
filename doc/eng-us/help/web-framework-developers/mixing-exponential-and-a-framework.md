---
title: Mixing Exponential and a framework
audience: web-framework-developer, developer
category: web-frameworks
order: 490
summary: Patterns for projects that use Exponential and another framework together - Exponential as the content back end of a framework site, a framework service called from Exponential, Exponential inside Symfony through the legacy bridge, shared users and single sign-on questions, shared hosting under one domain, and the rules that keep both sides consistent.
keywords: integration, hybrid, mixing, microservices, legacy bridge, REST, headless, Composer libraries, shared domain, reverse proxy, single sign-on, OAuth2, web framework developer
type: help
---
# Mixing Exponential and a framework

This page is for teams that want Exponential for content and a framework for something else. Four patterns cover most
projects. Each keeps one rule: **content is written through Exponential's content API**, so versions, caches,
permissions and search stay consistent.

## Contents

1. [Pattern 1: Exponential as the content back end](#pattern-1-exponential-as-the-content-back-end)
2. [Pattern 2: a framework service called from Exponential](#pattern-2-a-framework-service-called-from-exponential)
3. [Pattern 3: Exponential inside Symfony](#pattern-3-exponential-inside-symfony)
4. [Pattern 4: libraries inside an extension](#pattern-4-libraries-inside-an-extension)
5. [Users across both sides](#users-across-both-sides)
6. [One domain, two applications](#one-domain-two-applications)
7. [Rules](#rules)

## Pattern 1: Exponential as the content back end

The framework application (Laravel, Django, Rails, a Node.js front end) renders pages and reads content from
Exponential's REST API with an integration user's API key or an OAuth2 client; editors work in Exponential's
administration ([Headless and decoupled front ends](headless-and-decoupled-front-ends.md)). Purge the framework's cache
on publish with a `content/cache` listener or a workflow event that calls the framework.

## Pattern 2: a framework service called from Exponential

A service written in another framework (billing, a recommendation engine, a search index) is called from an
Exponential extension: a class wraps the HTTP calls, an `expqueue` job makes them in the background, and a workflow
event or listener enqueues the job after publishing ([Queues](queues-and-background-jobs.md)). The service calls back
through the REST API or expservices when it must change content.

## Pattern 3: Exponential inside Symfony

The legacy bridge loads the Exponential 6 kernel inside a Symfony platform installation: Symfony controllers and
Twig for the public site, the legacy administration for editors, one database
([The Symfony legacy bridge](symfony-and-the-legacy-bridge.md)). This is the only pattern with one process for both,
and the one with the most moving parts to upgrade.

## Pattern 4: libraries inside an extension

Many framework components are ordinary Composer libraries (HTTP clients, validators, Markdown parsers, mailers). Require
them in your extension's `composer.json`; `autoload.php` loads Composer's autoloader, so your classes can use them. Keep
framework-specific runtime pieces (a container, a router, an ORM connection) out of the kernel's request path.

## Users across both sides

Exponential keeps its own users (content objects) and sessions ([Authentication](authentication-and-sessions.md)).
Options when both sides need the same people:

- The framework side acts as one integration user through the REST API, with its own role.
- The framework side asks users to authorize it through Exponential's OAuth2 server
  ([OAuth and API keys](../creators/app-creators/oauth-and-api-keys.md)).
- Exponential checks credentials against another system through a login handler of your own
  ([User login handler](../../bc/6.0/rad-extension-points.md#user-login-handler)).

Plan this early; there is no ready-made single sign-on between Exponential and a framework application.

## One domain, two applications

A reverse proxy can route paths to two applications (`/shop/` to a framework, everything else to Exponential). Then:
list the proxy in `TrustedProxies[]`, keep cookie names distinct (`[Session] SessionNamePrefix`), and make sure
Exponential's URL aliases never claim the other application's paths
([Behind a reverse proxy](../../install/08-serving-the-site.md#88-behind-a-reverse-proxy)).

## Rules

- Content is written through the content API (`eZContentFunctions`, REST, expservices), never by SQL into content tables.
- Each side owns its own tables; share data through APIs, not foreign keys.
- Use remote IDs to refer to content from the other side; numeric IDs differ between installations.
- Test the integration on every release of either side.

## Related pages

- [When to choose Exponential](when-to-choose-exponential.md)
- [Remote services with expservices](../creators/app-creators/remote-services-expservices.md)
- [The platform products](the-platform-products.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
