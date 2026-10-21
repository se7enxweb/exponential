---
title: DXP project checklist
audience: dxp-developer
category: dxp-development
order: 49
summary: A checklist for a DXP project from the first decision to launch and maintenance - product choice, install, security, content model, theme, APIs, performance, operations and handover - each item linked to the page that explains it.
keywords: checklist, launch, project, go live, security, content model, theme, APIs, performance, operations, handover, DXP
type: help
---
# DXP project checklist

This page is for developers and project leads who run a DXP project. Copy it into your project's tracker and tick
the items off.

## Contents

1. [Before the project](#before-the-project)
2. [Install and security](#install-and-security)
3. [Content and theme](#content-and-theme)
4. [APIs](#apis)
5. [Performance and operations](#performance-and-operations)
6. [Launch](#launch)
7. [After launch](#after-launch)

## Before the project

- [ ] The DXP is the right product, not Nexus, Legacy or Exponential 6 ([Choosing](choosing-dxp-legacy-nexus-or-exponential-6.md)).
- [ ] Every promised feature is in the package list ([Claims to avoid](claims-to-avoid.md)).
- [ ] Servers run PHP 8.3 or newer; Node.js 20 is available where you build ([Versions](php-symfony-and-node-versions.md)).

## Install and security

- [ ] Project created from the skeleton, under Git, `.env.local` and JWT keys ignored ([Installing](installing-with-the-skeleton.md)).
- [ ] The installer's administrator password changed; editors have their own accounts.
- [ ] `APP_ENV=prod`, `APP_DEBUG=0`, HTTPS only on production ([Web server setup](web-server-setup.md)).
- [ ] Permissions set with ACLs; nothing is 777 ([File permissions](file-permissions.md)).
- [ ] No upstream package installed beside its fork ([The forks](the-se7enxweb-forks-and-replace.md#checking-your-project)).
- [ ] Test users and mail use `example.invalid`; development mail goes to `null://null`.

## Content and theme

- [ ] Content types reviewed with editors and client developers ([Content model](content-types-and-locations.md#modeling-advice)).
- [ ] A design with your theme before `standard` ([The standard design and your theme](the-standard-design-and-your-theme.md)).
- [ ] Image variations for every size the design uses ([Image variations](image-variations.md)).
- [ ] Templates accessible, with no inline scripts or styles.

## APIs

- [ ] REST prefix checked and kept in client configuration ([The REST API](the-rest-api.md#where-it-lives)).
- [ ] JWT firewalls enabled only if needed; one user and role per client ([JWT](jwt-authentication.md)).
- [ ] GraphQL schema generated and committed ([GraphQL](graphql-and-graphiql.md)).
- [ ] CORS limited to your origins.

## Performance and operations

- [ ] HTTP cache on, purging tested ([HTTP cache](http-cache-and-varnish.md)).
- [ ] Cache pool chosen (filesystem or Redis) ([Application cache](application-cache-and-redis.md)).
- [ ] Search engine chosen and indexed ([Search](search-legacy-and-solr.md)).
- [ ] Cron line installed for the web server user ([Cron](cron.md)).
- [ ] Message workers supervised, if you use an async transport ([Messenger](messenger-and-notifications.md)).
- [ ] Backups of the database, `public/var/`, `.env.local` and the JWT keys, and a restore tested.

## Launch

- [ ] The deploy runs in the documented order ([Deploying](deploying-dxp.md)).
- [ ] Smoke tests of the site, `/adminui/` and the APIs pass.
- [ ] Logs are watched for the first days.

## After launch

- [ ] Updates on a schedule, on a branch, with `composer.lock` committed ([Updating](updating-dxp.md)).
- [ ] Release notes of the forks read before each update.
- [ ] Old versions pruned with `exponential:content:cleanup-versions`, tested first.
- [ ] Bugs found reported back ([Contributing](contributing-to-the-forks.md)).

## Related pages

- [DXP Developers: start here](dxp-developers-start-here.md)
- [Deploying](deploying-dxp.md)
- [Troubleshooting](troubleshooting-dxp.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
