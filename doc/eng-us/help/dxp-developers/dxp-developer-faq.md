---
title: DXP developer FAQ
audience: dxp-developer
category: dxp-development
order: 48
summary: Short answers to the questions DXP developers ask most - what it is, how it relates to Ibexa and Exponential 6, versions, databases, page building, APIs, URLs, commands, licensing and where to get help - each with the page that explains it.
keywords: FAQ, questions, answers, DXP, Exponential Platform DXP, Ibexa, Exponential 6, PHP version, SQLite, layouts, REST, GraphQL, license, help
type: help
---
# DXP developer FAQ

This page is for developers who want a quick answer. Every answer links to the page with the details.

## Contents

1. [The product](#the-product)
2. [Installing and running](#installing-and-running)
3. [Building](#building)
4. [APIs](#apis)
5. [People and licenses](#people-and-licenses)

## The product

**What is Exponential Platform DXP?**
A free, open source Symfony content platform, the community continuation of the eZ Platform and Ibexa OSS line,
maintained by 7x ([What it is](what-exponential-platform-dxp-is.md)).

**Is it Exponential 6?**
No. It is a separate kernel on Symfony 7.4. Exponential 6 is the PHP CMS kernel without Symfony
([Coming from Exponential 6](coming-from-exponential-6.md)).

**Does it include a page builder?**
No editor; only the Layouts core. Use Nexus or Exponential 6 for a page builder ([Layouts core](layouts-core-in-dxp.md)).

**Does it include workflows, scheduled publishing or a shop?**
No ([What the DXP ships](what-the-dxp-ships-and-what-it-does-not.md)).

**Is it affiliated with Ibexa?**
No. It uses and forks open source packages of that line; it is maintained by 7x and the community
([The se7enxweb forks](the-se7enxweb-forks-and-replace.md)).

## Installing and running

**Which PHP version?** 8.3 or newer; 8.3 or 8.5 recommended ([Versions](php-symfony-and-node-versions.md)).

**Which Node.js version?** 20 LTS for the DXP skeleton.

**Can I try it without a database server?** Yes, with SQLite ([SQLite for development](sqlite-for-development.md)).

**Can I run production on SQLite?** Not for public or multi-user sites ([Limits](sqlite-for-development.md#limits)).

**What is the administrator password after the install?**
The installer's account is documented publicly in the guide, which is why you change it immediately
([Installing](installing-with-the-skeleton.md#after-the-install)).

**Do I need Varnish or Redis?** No; both are optional ([HTTP cache](http-cache-and-varnish.md),
[Application cache](application-cache-and-redis.md)).

**Can I use Exponential Velocity to serve the DXP?** Velocity is documented as the engine of Exponential 6; the DXP's
guide documents Apache or Nginx with PHP-FPM and the Symfony CLI ([Web server setup](web-server-setup.md)).

## Building

**Where do my templates go?** `templates/themes/<theme>/`, referred to as `@ibexadesign/...`
([Twig and the design engine](twig-templates-and-the-design-engine.md)).

**Why does my YAML change not show?** Configuration is compiled; run `cache:clear`
([Day-to-day commands](day-to-day-commands.md)).

**Why do the `ibexa:` commands still work?** They are aliases of the `exponential:` names
([Console commands](console-commands-and-renames.md)).

## APIs

**Where is the REST API?** Under `ibexa.rest.path_prefix`, `/api/ibexa/v2` by default in the installed v5 package;
older guides show `/api/ezp/v2/` ([The REST API](the-rest-api.md#where-it-lives)).

**How do apps sign in?** With JWT, after you enable the firewalls ([JWT authentication](jwt-authentication.md)).

**When must I regenerate the GraphQL schema?** After every content type or field type change
([GraphQL](graphql-and-graphiql.md#the-schema)).

## People and licenses

**What is the license?** `(GPL-2.0-or-later or proprietary)` as declared ([Licensing](licensing.md)).

**Where do I report a bug?** In the repository concerned; security issues privately
([Contributing](contributing-to-the-forks.md)).

**Who makes it?** 7x, the company that leads Exponential, with the community; the Exponential Foundation is the
nonprofit ([Who is 7x?](../who-is-7x.md)).

## Related pages

- [DXP Developers: start here](dxp-developers-start-here.md)
- [Glossary for DXP developers](glossary-for-dxp-developers.md)
- [Troubleshooting](troubleshooting-dxp.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
