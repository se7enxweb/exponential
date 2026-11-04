---
title: Content management in other languages than PHP
audience: admin, developer, businesses, partner, decision-maker
summary: The content management systems, frameworks and site builders written in Python, JavaScript and TypeScript, Ruby, Java and Kotlin, .NET, Go, Rust, C++, Swift, Elixir and other languages, with their licenses and business models, and where each leaves a site without funding or with a long life ahead, compared with Exponential.
keywords: competition, comparison, Python, Django, Wagtail, Plone, JavaScript, Node.js, Strapi, Ghost, Payload, Directus, Next.js, Ruby, Rails, Jekyll, Java, Liferay, Magnolia, OpenCms, dotCMS, .NET, Umbraco, Orchard Core, Go, Hugo, Rust, Zola, static site generator, low-code, license, source-available, Business Source License
---

# Content management in other languages than PHP

This page is part of [Exponential Competition](README.md). It covers the systems that are not written in PHP: the
content management systems, the headless back ends, the web frameworks people build sites with, the static site
generators and the hosted site builders. The PHP systems are on [PHP CMS and frameworks](php-cms-and-frameworks.md),
the proprietary and hosted platforms on [Proprietary DXP and SaaS](proprietary-dxp-and-saas.md).

Every product gets the same five points: what it is, its license and business model, what it does well, where it
leaves a user who has no budget for the site but still needs it to do modern things for many years, and what
Exponential does instead. The facts were checked against each project's own repository or website in November 2026;
they are listed under [Sources](#sources). Products change, so check the current state before you decide.

## Contents

1. [What Exponential brings to every comparison](#what-exponential-brings-to-every-comparison)
2. [Python](#python)
3. [JavaScript and TypeScript](#javascript-and-typescript)
4. [Ruby](#ruby)
5. [Java, Kotlin and the JVM](#java-kotlin-and-the-jvm)
6. [.NET and C#](#net-and-c)
7. [Go](#go)
8. [Rust](#rust)
9. [C and C++](#c-and-c)
10. [Swift](#swift)
11. [Elixir](#elixir)
12. [CFML](#cfml)
13. [Haskell, Clojure and Perl](#haskell-clojure-and-perl)
14. [Lua, Nim, Crystal, Dart and Zig](#lua-nim-crystal-dart-and-zig)
15. [Hosted site builders without code](#hosted-site-builders-without-code)
16. [Static site generators as a category](#static-site-generators-as-a-category)
17. [Summary](#summary)
18. [Sources](#sources)

## What Exponential brings to every comparison

So that each entry below can stay short, here is what Exponential offers in every case. Each point is documented in
this help center.

- **One license, for everyone.** The kernel and its bundled extensions are published under the GNU General Public
  License, version 2 or later. There is no license key, no revenue limit, no edition that holds features back, and no
  term after which the license changes.
- **A complete content management system out of the box.** Content classes, versions and drafts, translations,
  object states, roles and policies with limitations, workflows, an audit trail
  ([Audit trail](../features/6.0/audit-trail.md)), review holds ([Review hold](../features/6.0/review-hold.md)),
  two-step sign-in, an administration interface translated into more than 150 languages
  ([Languages](../languages.md)) and a help center.
- **Your choice of database.** MySQL and MariaDB, PostgreSQL, SQLite ([SQLite](../features/6.0/sqlite-database.md)),
  MongoDB ([MongoDB](../features/6.0/mongodb-database-support.md)) and Oracle; see
  [Database support](../help/database-support.md).
- **Ordinary hosting.** PHP from 8.0 to 8.5 is supported, and 7.4 still runs it. A shared host with PHP and SQLite is
  enough to start; the optional Velocity server ([Velocity](../features/6.0/velocity-web-server.md)) runs the same
  site as a persistent application server.
- **A static copy when you want one.** [Static publishing](../features/6.0/static-publishing.md) turns a site into
  plain files and publishes them to any git host, such as free GitHub Pages
  ([Host static sites on GitHub Pages](../guides/host-static-sites-on-github-pages.md)), while editors keep working in
  Exponential.
- **APIs.** REST, GraphQL ([GraphQL](../features/6.0/graphql.md)), WebDAV and SOAP; see
  [Integrating With Exponential 6.0](../INTEGRATING.md).
- **A long, unbroken line.** The kernel continues the code base that began as eZ publish 3 in 2002; content models
  built then still open today ([History of Exponential](../HISTORY.md)).

## Python

### Django (framework)

- **What it is:** the most widely used Python web framework, with an automatic admin for data models.
- **License and model:** BSD-3-Clause, run by the Django Software Foundation, a non-profit.
- **Does well:** a mature, well-documented framework with a large ecosystem and long-term support releases.
- **Where it leaves you:** it is a framework, not a content management system. Page trees, versions, editorial
  workflows, translations of content, media handling and an editor-friendly interface have to be built or assembled
  from add-ons, and then maintained by a developer for the life of the site.
- **Exponential instead:** all of that ships in the box and is configured, not programmed.

### django CMS

- **What it is:** a page-based CMS on top of Django.
- **License and model:** BSD-3-Clause; maintained by the django CMS Association.
- **Does well:** in-place page editing and a plugin model familiar to Django developers.
- **Where it leaves you:** you run and update a Django project of your own, with its Python dependencies, and its
  major versions come with upgrade work for that project. There is no built-in static export, and the database choice
  is whatever Django supports.
- **Exponential instead:** a content model that stayed compatible since 2002, five database engines, and static
  publishing built in.

### Wagtail

- **What it is:** a Django-based CMS with a structured page model and the StreamField editor.
- **License and model:** BSD-3-Clause; developed largely by Torchbox and contributors.
- **Does well:** a clean editing interface and a good content model for developers.
- **Where it leaves you:** every Wagtail site is a Django project that a developer writes and maintains; page types are
  Python code, so a change to the content model is a code change and a deployment. Feature releases come several times
  a year, with upgrade notes to follow.
- **Exponential instead:** content classes are edited in the administration interface, without a deployment.

### Plone

- **What it is:** one of the oldest open-source enterprise CMSs, written in Python.
- **License and model:** GPL-2.0; owned by the non-profit Plone Foundation.
- **Does well:** strong permissions, workflows and a long security record.
- **Where it leaves you:** it needs a Python application server and its own object database (ZODB) or a relational
  storage layer, specialized knowledge that few hosts and agencies have; the move to Plone 6 introduced a separate
  React front end (Volto) to run and update.
- **Exponential instead:** the same kind of permissions and workflows on ordinary PHP hosting and a database of your
  choice.

### Mezzanine

- **What it is:** a Django-based CMS and blog.
- **License and model:** BSD-2-Clause, community-maintained.
- **Does well:** a simple, conventional Django CMS.
- **Where it leaves you:** development has slowed: its latest release, 6.1.1, came out in June 2025. A site that needs
  years of updates depends on a small group of volunteers.
- **Exponential instead:** an actively released kernel with a public release history.

### FeinCMS

- **What it is:** a toolkit for building a CMS inside a Django project.
- **License and model:** BSD-3-Clause.
- **Does well:** flexible content regions for developers.
- **Where it leaves you:** it publishes no packaged releases on GitHub and is a building block, not a finished system;
  the developer builds and maintains the rest.
- **Exponential instead:** a finished system.

### Odoo Website

- **What it is:** the website builder of the Odoo business suite.
- **License and model:** Odoo Community is LGPL-3.0; Odoo Enterprise is a paid, licensed edition with additional apps.
- **Does well:** a site tied directly to Odoo's ERP, shop and CRM.
- **Where it leaves you:** the site lives inside a whole ERP that must be hosted and upgraded, and several features are
  only in the paid edition. Upgrading between Odoo major versions is a migration project of its own.
- **Exponential instead:** a content platform with one license and integrations through REST, GraphQL and webhooks.

### Pelican, Nikola and MkDocs

- **What they are:** Python static site generators (MkDocs focuses on documentation).
- **License and model:** Pelican AGPL-3.0, Nikola MIT, MkDocs BSD-2-Clause; all volunteer projects.
- **Do well:** fast, simple sites from Markdown files.
- **Where they leave you:** see [Static site generators as a category](#static-site-generators-as-a-category). MkDocs'
  latest release is 1.6.1 from August 2024.
- **Exponential instead:** an editor's interface and static output.

### Flask (framework)

- **What it is:** a minimal Python web framework.
- **License and model:** BSD-3-Clause, by the Pallets project.
- **Does well:** small, flexible applications.
- **Where it leaves you:** it has no content management at all; every CMS feature is custom code.
- **Exponential instead:** the CMS features exist already.

## JavaScript and TypeScript

### Strapi

- **What it is:** a headless CMS for Node.js.
- **License and model:** the Community Edition is MIT; everything under the `ee/` directories is the Enterprise Edition
  under a commercial license (per the repository's LICENSE). Strapi Cloud is a paid hosting service.
- **Does well:** quick content APIs for a separate front end.
- **Where it leaves you:** headless only, so you build, host and maintain a second application for the website itself.
  Major versions (v4 to v5) required migration work, and some features sit in the paid edition.
- **Exponential instead:** a site and its APIs from one installation, and every feature under one license.

### Ghost

- **What it is:** a publishing platform for blogs and newsletters.
- **License and model:** MIT; run by the Ghost Foundation, a non-profit, which also sells Ghost(Pro) hosting.
- **Does well:** a polished writing experience and built-in memberships.
- **Where it leaves you:** Ghost's documentation names MySQL 8 as the only supported production database, with Node.js
  and nginx, so it needs a server you administer. It is built for publications, not for general structured content.
- **Exponential instead:** any content structure, five database engines, and ordinary PHP hosting.

### Payload

- **What it is:** a TypeScript headless CMS and application framework built into Next.js.
- **License and model:** MIT, developed by Payload, Inc.
- **Does well:** code-first schemas and strong typing.
- **Where it leaves you:** the content model is code, so changes need a developer and a deployment; the site is a
  Next.js application to host and keep current.
- **Exponential instead:** content classes managed in the administration interface.

### KeystoneJS

- **What it is:** a Node.js headless CMS and GraphQL API framework.
- **License and model:** MIT, developed by Thinkmill.
- **Does well:** a GraphQL API from a schema.
- **Where it leaves you:** headless and code-first; the front end and its hosting are yours to build and maintain.
- **Exponential instead:** a complete site plus GraphQL.

### Directus

- **What it is:** a data platform that turns a SQL database into an API and an admin app.
- **License and model:** published under the Monospace Sustainable Core License 1.0 (MSCL-1.0-GPL). Its permitted
  purposes exclude a "competing use", and the license forbids circumventing its license-key functionality, per the
  repository's license file.
- **Does well:** a good admin interface over an existing database.
- **Where it leaves you:** it is source-available rather than free software under the Open Source Definition; what you
  may do with it is set by its licensor's terms, which have changed before.
- **Exponential instead:** the GPL, with no use restrictions and no license keys.

### ApostropheCMS

- **What it is:** a Node.js CMS with in-context editing.
- **License and model:** MIT for the core; Apostrophe Technologies sells Pro and Assembly editions.
- **Does well:** editing on the page.
- **Where it leaves you:** it needs Node.js and MongoDB as its database; its pricing page lists the multisite
  architecture and the multitenant dashboard only for the paid Assembly edition.
- **Exponential instead:** multisite in the kernel, and a choice of database engines.

### TinaCMS and Decap CMS

- **What they are:** editing interfaces that write Markdown or JSON into a git repository for a static site.
- **License and model:** TinaCMS Apache-2.0, with TinaCloud as a paid hosted back end; Decap CMS MIT (named Netlify CMS
  until February 2023).
- **Do well:** editing for static sites without a server-side CMS.
- **Where they leave you:** content lives as files in git, so there are no real permissions per item, no workflows
  beyond pull requests, and no structured queries across content.
- **Exponential instead:** a real content repository with permissions and workflows, and static output when you want it.

### Builder.io

- **What it is:** a hosted visual editor and headless CMS.
- **License and model:** the SDKs are MIT; the service is commercial.
- **Does well:** visual page building for marketing teams.
- **Where it leaves you:** the content and editor live on the vendor's service, on its plans and terms.
- **Exponential instead:** software you run yourself.

### Medusa

- **What it is:** a Node.js commerce engine.
- **License and model:** MIT, except the Enterprise Edition materials, which are under a separate license and agreement.
- **Does well:** a modular commerce back end.
- **Where it leaves you:** commerce only; the storefront and the content site are separate applications to build.
- **Exponential instead:** content and a shop in one system.

### Next.js, Nuxt, SvelteKit, Remix, Astro and Gatsby

- **What they are:** JavaScript frameworks for building websites and web applications.
- **License and model:** MIT; Next.js is developed by Vercel, Gatsby by Gatsby Inc., which Netlify acquired in 2023.
  Remix's features were merged into React Router 7.
- **Do well:** fast front ends, server rendering and modern developer tooling.
- **Where they leave you:** none of them manages content. A site needs a separate CMS, and the framework moves quickly:
  several of them have had breaking major versions or changes of direction within a few years (the Remix merge, Gatsby's
  slower releases since the acquisition, with 5.16.1 in February 2026). Someone has to keep each site current.
- **Exponential instead:** a front end rendered by the CMS itself, with its templates kept compatible.

### Docusaurus and Eleventy

- **What they are:** a documentation site generator (Docusaurus, by Meta) and a general static site generator
  (Eleventy).
- **License and model:** MIT.
- **Do well:** documentation and simple static sites.
- **Where they leave you:** see [Static site generators as a category](#static-site-generators-as-a-category).
- **Exponential instead:** an editing interface, and static publishing.

## Ruby

### Ruby on Rails (framework)

- **What it is:** the Ruby web framework.
- **License and model:** MIT.
- **Does well:** productive development of web applications.
- **Where it leaves you:** no content management; every CMS feature is custom code, and Rails major upgrades are work
  for a Ruby developer.
- **Exponential instead:** CMS features ready to configure.

### Refinery CMS, Spina, LocomotiveCMS, Alchemy CMS and Camaleon CMS

- **What they are:** CMS engines for Rails.
- **License and model:** Refinery MIT, Spina MIT, LocomotiveCMS MIT, Alchemy BSD-3-Clause, Camaleon MIT; all community
  or small-company projects.
- **Do well:** a CMS inside a Rails application.
- **Where they leave you:** each needs a Rails application to host and upgrade. Activity varies: Refinery's v4.1.0
  (May 2026) followed a long gap, and LocomotiveCMS's engine has no packaged GitHub release and its last commit was in
  August 2025.
- **Exponential instead:** a standalone system with an active release line.

### Spree and Solidus

- **What they are:** Rails commerce platforms.
- **License and model:** BSD-3-Clause.
- **Do well:** a full shop on Rails.
- **Where they leave you:** commerce first, content second; a Rails application to run.
- **Exponential instead:** content and shop in one.

### Jekyll, Middleman and Bridgetown

- **What they are:** Ruby static site generators; Jekyll powers GitHub Pages' built-in builds.
- **License and model:** MIT.
- **Do well:** simple static sites.
- **Where they leave you:** see [Static site generators as a category](#static-site-generators-as-a-category). Jekyll's
  latest release is 4.4.1 from January 2025.
- **Exponential instead:** an editor's interface, and its own path to GitHub Pages.

## Java, Kotlin and the JVM

### Liferay

- **What it is:** a Java portal and digital experience platform.
- **License and model:** Liferay Portal is under LGPL-2.1-or-later or the Liferay DXP EULA (per its source headers);
  Liferay DXP is a paid subscription. The latest general-availability release of Liferay Portal on GitHub is 7.4.3.132
  from February 2025.
- **Does well:** a broad portal feature set for large organizations.
- **Where it leaves you:** it needs a Java application server and substantial hardware and expertise; most enterprise
  features and support come with the subscription.
- **Exponential instead:** enterprise content features on ordinary PHP hosting, under one license.

### Magnolia

- **What it is:** a Java digital experience platform.
- **License and model:** a commercial product, sold by Magnolia International with cloud and support offerings.
- **Does well:** headless and hybrid delivery for large teams.
- **Where it leaves you:** without a commercial agreement there is no product to run, and it needs Java hosting and
  expertise.
- **Exponential instead:** the whole product under the GPL.

### Jahia

- **What it is:** a Java digital experience platform.
- **License and model:** its core repository ships under Apache-2.0 and the Jahia Sustainable Enterprise License (both
  license files are in the repository); the enterprise distribution is commercial.
- **Does well:** structured content and personalization for enterprises.
- **Where it leaves you:** Java hosting and a commercial relationship for the enterprise product.
- **Exponential instead:** the GPL and ordinary hosting.

### OpenCms

- **What it is:** a Java CMS by Alkacon.
- **License and model:** LGPL-2.1; Alkacon sells support.
- **Does well:** a long-lived open-source Java CMS.
- **Where it leaves you:** a Java servlet container to run and maintain.
- **Exponential instead:** PHP hosting that almost every provider offers.

### Bloomreach Experience Manager (formerly Hippo CMS)

- **What it is:** the Java CMS that was Hippo CMS before Bloomreach acquired Hippo.
- **License and model:** an Apache-2.0 community edition on GitHub; the full product is commercial.
- **Does well:** structured content for enterprises.
- **Where it leaves you:** the vendor's focus is its commercial products; the community edition needs Java hosting.
- **Exponential instead:** a platform whose free edition is the whole product.

### CrafterCMS

- **What it is:** a git-based Java CMS.
- **License and model:** GPL-3.0, with a commercial enterprise edition.
- **Does well:** content in git with a headless delivery tier.
- **Where it leaves you:** Java, Elasticsearch/OpenSearch and several services to operate.
- **Exponential instead:** a single PHP application.

### dotCMS

- **What it is:** a Java headless and hybrid CMS.
- **License and model:** Business Source License 1.1. Production use is free only while the user's "Total Finances"
  (revenue, budget or funding) stay at or below 5 million US dollars, counting the clients an agency builds for; each
  version changes to GPL-3.0 four years after August 1, 2025 (per the repository's LICENSE).
- **Does well:** a capable headless CMS.
- **Where it leaves you:** a successful organization outgrows the free grant, and the license, not the software,
  decides what it may do.
- **Exponential instead:** the GPL from day one, for organizations of every size.

### Alfresco

- **What it is:** an enterprise content and document management system, now owned by Hyland.
- **License and model:** the Community Edition is LGPL-3.0; the Enterprise Edition is commercial.
- **Does well:** document management.
- **Where it leaves you:** it is a document repository, not a website CMS; websites need another product.
- **Exponential instead:** web content management with file and document handling.

### JBake, Spring Boot and Ktor

- **What they are:** a Java static site generator (JBake), and the Java and Kotlin frameworks sites are built with.
- **License and model:** JBake MIT, Spring Boot Apache-2.0, Ktor Apache-2.0 (JetBrains).
- **Do well:** solid foundations for developers.
- **Where they leave you:** no content management; see also
  [Static site generators as a category](#static-site-generators-as-a-category).
- **Exponential instead:** the content management already exists.

### Apache Lenya and other retired Java CMSs

- **What they are:** earlier open-source Java CMSs, such as Apache Lenya, which the Apache Software Foundation retired
  to its Attic.
- **Where they leave you:** no updates at all; sites on them had to migrate.
- **Exponential instead:** a code line that has been carried forward since 2002 instead of being abandoned.

## .NET and C#

### Umbraco

- **What it is:** the best-known open-source .NET CMS.
- **License and model:** MIT; Umbraco HQ sells Umbraco Cloud and add-on products.
- **Does well:** a friendly back office and a large .NET community.
- **Where it leaves you:** Umbraco alternates short-term and long-term support majors: Umbraco 18 (June 2026) is a
  short-term release that reaches end of life in June 2027, and the long-term Umbraco 13 ends in December 2026. Sites
  face an upgrade project every year or two, on .NET hosting.
- **Exponential instead:** a stable kernel line and PHP hosting.

### Orchard Core, Piranha CMS, Oqtane and DNN

- **What they are:** open-source .NET CMSs and application frameworks; DNN was formerly DotNetNuke.
- **License and model:** Orchard Core BSD-3-Clause; Piranha, Oqtane and DNN MIT.
- **Do well:** CMS features for .NET teams.
- **Where they leave you:** they need .NET developers and hosting; much of the work of building a site is code.
- **Exponential instead:** configuration in the administration interface.

### Kentico Xperience, Sitefinity, Sitecore and Optimizely

- **What they are:** commercial .NET digital experience platforms.
- **License and model:** proprietary licenses or subscriptions (Kentico, Progress Sitefinity); Sitecore and Optimizely are
  covered on [Proprietary DXP and SaaS](proprietary-dxp-and-saas.md).
- **Do well:** marketing features for funded teams.
- **Where they leave you:** without a budget there is no license, and the site cannot outlive the subscription.
- **Exponential instead:** free software that keeps running whatever happens to a vendor.

## Go

### Hugo

- **What it is:** the fastest-known static site generator.
- **License and model:** Apache-2.0, volunteer-maintained.
- **Does well:** very fast builds of large static sites.
- **Where it leaves you:** it is still a 0.x release line (v0.169.0 in October 2026), and its templates have changed
  between versions; see also [Static site generators as a category](#static-site-generators-as-a-category).
- **Exponential instead:** stable templates and an editing interface.

### Ponzu

- **What it is:** a Go headless CMS.
- **License and model:** BSD-3-Clause.
- **Where it leaves you:** its latest release, v0.11.0, dates from September 2019.
- **Exponential instead:** an active release line.

### PocketBase

- **What it is:** an open-source back end in a single file (database, auth, files, admin).
- **License and model:** MIT. Its GitHub repository is archived and points to a new home outside GitHub; its versions
  are still 0.x.
- **Does well:** a quick back end for small applications.
- **Where it leaves you:** it is not a CMS; pages, editing and publishing are yours to build.
- **Exponential instead:** a finished CMS.

### Gitea

- **What it is:** a self-hosted git forge, sometimes used to host static sites.
- **License and model:** MIT.
- **Where it leaves you:** it hosts code, not content; a site still needs a generator or a CMS.
- **Exponential instead:** a CMS whose static output can be pushed to any git host, Gitea included.

## Rust

### Zola and Cobalt

- **What they are:** static site generators in Rust.
- **License and model:** Zola EUPL-1.2, Cobalt Apache-2.0.
- **Do well:** a single fast binary.
- **Where they leave you:** both are still 0.x releases; see
  [Static site generators as a category](#static-site-generators-as-a-category).
- **Exponential instead:** static output from a full CMS.

### Loco, Axum and Actix Web

- **What they are:** Rust web frameworks; Loco is modeled on Rails.
- **License and model:** Loco Apache-2.0, Axum MIT, Actix Web MIT or Apache-2.0.
- **Where they leave you:** no content management; every feature is Rust code that few web agencies can maintain.
- **Exponential instead:** a widely known language and a finished CMS.

## C and C++

### CppCMS and Wt

- **What they are:** C++ web frameworks; despite its name, CppCMS is a framework, not a CMS.
- **License and model:** CppCMS MIT; Wt GPL-2.0 with a commercial license from Emweb.
- **Do well:** very high performance.
- **Where they leave you:** no content management, and C++ web developers are rare, so a site depends on a few people.
  This is why almost no content management systems are written in C or C++.
- **Exponential instead:** a CMS in one of the most widely known web languages.

## Swift

### Publish, Ignite and Vapor

- **What they are:** Swift static site generators (Publish, Ignite) and a Swift web framework (Vapor).
- **License and model:** MIT.
- **Where they leave you:** Publish's latest release, 0.9.0, is from April 2022; Ignite is still 0.x; Vapor has no
  content management. Swift on the server is a niche skill.
- **Exponential instead:** an active, complete CMS.

## Elixir

### Beacon, Phoenix and Tableau

- **What they are:** a CMS built on Phoenix (Beacon), the Phoenix framework, and an Elixir static site generator
  (Tableau).
- **License and model:** MIT.
- **Do well:** Phoenix's real-time features.
- **Where they leave you:** Beacon is still 0.x (v0.5.1, April 2025), and Elixir hosting and developers are uncommon.
- **Exponential instead:** a mature CMS on common hosting.

## CFML

### Lucee

- **What it is:** the open-source CFML engine, an alternative to Adobe ColdFusion (covered on
  [Proprietary DXP and SaaS](proprietary-dxp-and-saas.md)).
- **License and model:** LGPL-2.1, by the Lucee Association.
- **Does well:** runs CFML applications without an Adobe license.
- **Where it leaves you:** it is a language engine, not a CMS, and CFML developers are few.
- **Exponential instead:** a complete CMS in PHP.

## Haskell, Clojure and Perl

### Hakyll and Cryogen

- **What they are:** static site generators in Haskell (Hakyll) and Clojure (Cryogen).
- **License and model:** Hakyll BSD-3-Clause, Cryogen EPL-1.0.
- **Where they leave you:** Cryogen's latest release, 0.4.0, is from December 2020; both need developers of rare
  languages. See [Static site generators as a category](#static-site-generators-as-a-category).

### Movable Type

- **What it is:** a Perl blogging and publishing system by Six Apart.
- **License and model:** proprietary; its open-source edition was discontinued years ago.
- **Where it leaves you:** a paid license and Perl hosting.
- **Exponential instead:** free software.

### Bricolage

- **What it is:** a Perl content management and publishing system.
- **Where it leaves you:** its repository's last commit is from February 2015; it is no longer developed.

### Mojolicious

- **What it is:** a Perl web framework.
- **License and model:** Artistic-2.0.
- **Where it leaves you:** no content management.

## Lua, Nim, Crystal, Dart and Zig

- **Lapis** (Lua and MoonScript, MIT) and **Sailor** (Lua, MIT, last commit October 2022): web frameworks without
  content management.
- **Jester** (Nim, MIT): a web framework; latest release v0.6.0 from June 2023.
- **Amber** and **Lucky** (Crystal, MIT): web frameworks without content management.
- **Jaspr** (Dart, MIT): a web framework, still 0.x.
- **Zine** (Zig, MIT): a static site generator, still 0.x.

All of them are frameworks or generators for developers of small language communities; none offers content
management, editing or permissions for non-developers.

## Hosted site builders without code

These are proprietary hosted services. The general case for and against hosted platforms is on
[Proprietary DXP and SaaS](proprietary-dxp-and-saas.md); these are the builders not covered there.

- **Bubble:** an application builder whose applications run on Bubble's own platform.
- **Framer:** a design-focused site builder hosted by Framer.
- **Notion Sites:** pages published from a Notion workspace.
- **Google Sites:** a free site builder within a Google account.
- **Carrd:** single-page sites, with paid plans for more features.

**What they do well:** quick results with no technical skill.
**Where they leave you:** the site exists only on the vendor's service, within its plans, prices and terms. It cannot
be moved to your own server as a working site, and it ends when the account or the service does.
**Exponential instead:** software you own a copy of, which runs anywhere PHP runs, with a free static copy on GitHub
Pages if you want no server at all.

## Static site generators as a category

Static site generators (Pelican, Nikola, MkDocs, Hugo, Jekyll, Eleventy, Docusaurus, Zola and the others above) are
excellent at turning text files into fast pages. For a business site that has to keep working for years they leave
the same gaps:

- **No editing for non-developers.** Content is files in a repository; editors need git, or an add-on such as TinaCMS
  or Decap CMS.
- **No permissions or workflows.** Anyone who can write to the repository can change everything; review is a pull
  request, not an editorial workflow with states and roles.
- **Nothing dynamic.** Search, forms, comments, sign-in and shops need third-party services, each with its own account,
  terms and costs.
- **Structure lives in conventions.** Front matter and folder names replace a content model; queries across content
  are limited to what the generator supports.
- **Version churn.** Several of the generators are still 0.x and change templates between releases.

**Exponential instead:** [static publishing](../features/6.0/static-publishing.md) gives you the same static files on a
free host, generated from a real CMS with editors, permissions, workflows and versions, and it reports what will not
work statically ([Dynamic features in a static copy](../guides/static-publishing-dynamic-features.md)).

## Summary

| Group | Typical license | Typical gap for an unfunded, long-lived site |
|---|---|---|
| Python CMSs | BSD, GPL | A Django or Python project to maintain; content model in code |
| JavaScript headless CMSs | MIT with paid editions, source-available | A second application for the front end; features in paid editions |
| JavaScript frameworks | MIT | No content management; fast-moving major versions |
| Ruby | MIT, BSD | A Rails application to maintain; uneven activity |
| Java and .NET | LGPL, GPL, BSL, commercial | Heavy hosting and expertise; enterprise features paid |
| Go, Rust, Swift, Elixir, others | MIT, Apache, EUPL | Frameworks or 0.x generators for small developer communities |
| Hosted builders | Proprietary | The site lives only on the vendor's service |
| Exponential | GPL-2.0-or-later | A complete CMS on ordinary hosting, with static output and APIs, in one license |

## Sources

Checked in November 2026. License facts come from each repository's license file or GitHub's license detection;
release and activity facts from each repository's releases and commit history.

- Python: <https://github.com/django/django>, <https://github.com/django-cms/django-cms>,
  <https://github.com/wagtail/wagtail>, <https://github.com/plone/Products.CMFPlone>, <https://plone.org>,
  <https://github.com/stephenmcd/mezzanine>, <https://github.com/feincms/feincms>, <https://github.com/odoo/odoo>,
  <https://www.odoo.com/page/editions>, <https://github.com/getpelican/pelican>, <https://github.com/getnikola/nikola>,
  <https://github.com/mkdocs/mkdocs>, <https://github.com/pallets/flask>
- JavaScript and TypeScript: <https://github.com/strapi/strapi>, <https://github.com/TryGhost/Ghost>,
  <https://ghost.org/docs/faq/supported-databases/>, <https://github.com/payloadcms/payload>,
  <https://github.com/keystonejs/keystone>, <https://github.com/directus/directus>,
  <https://github.com/apostrophecms/apostrophe>, <https://apostrophecms.com/pricing>,
  <https://github.com/tinacms/tinacms>,
  <https://github.com/decaporg/decap-cms>, <https://www.netlify.com/blog/netlify-cms-to-become-decap-cms/>,
  <https://github.com/BuilderIO/builder>, <https://github.com/medusajs/medusa>, <https://github.com/vercel/next.js>,
  <https://github.com/nuxt/nuxt>, <https://github.com/sveltejs/kit>, <https://github.com/remix-run/remix>,
  <https://remix.run/blog/merging-remix-and-react-router>, <https://github.com/withastro/astro>,
  <https://github.com/gatsbyjs/gatsby>,
  <https://www.netlify.com/press/netlify-acquires-gatsby-inc-to-accelerate-adoption-of-composable-web-architectures/>,
  <https://github.com/facebook/docusaurus>, <https://github.com/11ty/eleventy>
- Ruby: <https://github.com/rails/rails>, <https://github.com/refinery/refinerycms>, <https://github.com/SpinaCMS/Spina>,
  <https://github.com/locomotivecms/engine>, <https://github.com/AlchemyCMS/alchemy_cms>,
  <https://github.com/owen2345/camaleon-cms>, <https://github.com/spree/spree>, <https://github.com/solidusio/solidus>,
  <https://github.com/jekyll/jekyll>, <https://github.com/middleman/middleman>,
  <https://github.com/bridgetownrb/bridgetown>
- Java, Kotlin and the JVM: <https://github.com/liferay/liferay-portal>, <https://www.liferay.com>,
  <https://www.magnolia-cms.com/>, <https://github.com/Jahia/jahia>, <https://github.com/alkacon/opencms-core>,
  <https://github.com/bloomreach/brxm>, <https://github.com/craftercms/craftercms>, <https://github.com/dotCMS/core>,
  <https://github.com/Alfresco/alfresco-community-repo>, <https://github.com/jbake-org/jbake>,
  <https://github.com/spring-projects/spring-boot>, <https://github.com/ktorio/ktor>, <https://attic.apache.org/projects/lenya.html>
- .NET: <https://github.com/umbraco/Umbraco-CMS>,
  <https://umbraco.com/products/knowledge-center/long-term-support-and-end-of-life/>, <https://github.com/OrchardCMS/OrchardCore>,
  <https://github.com/PiranhaCMS/piranha.core>, <https://github.com/oqtane/oqtane.framework>,
  <https://github.com/dnnsoftware/Dnn.Platform>, <https://www.kentico.com/>, <https://www.progress.com/sitefinity-cms>
- Go: <https://github.com/gohugoio/hugo>, <https://github.com/ponzu-cms/ponzu>,
  <https://github.com/pocketbase/pocketbase>, <https://github.com/go-gitea/gitea>
- Rust: <https://github.com/getzola/zola>, <https://github.com/cobalt-org/cobalt.rs>, <https://github.com/loco-rs/loco>,
  <https://github.com/tokio-rs/axum>, <https://github.com/actix/actix-web>
- C++, Swift, Elixir, CFML: <https://github.com/artyom-beilis/cppcms>, <https://github.com/emweb/wt>,
  <https://github.com/JohnSundell/Publish>, <https://github.com/twostraws/Ignite>, <https://github.com/vapor/vapor>,
  <https://github.com/BeaconCMS/beacon>, <https://github.com/phoenixframework/phoenix>,
  <https://github.com/elixir-tools/tableau>, <https://github.com/lucee/Lucee>
- Haskell, Clojure, Perl and others: <https://github.com/jaspervdj/hakyll>,
  <https://github.com/cryogen-project/cryogen-core>, <https://github.com/movabletype/movabletype>,
  <https://www.movabletype.com>, <https://github.com/bricoleurs/bricolage>, <https://github.com/mojolicious/mojo>,
  <https://github.com/leafo/lapis>, <https://github.com/sailorproject/sailor>, <https://github.com/dom96/jester>,
  <https://github.com/amberframework/amber>, <https://github.com/luckyframework/lucky>,
  <https://github.com/schultek/jaspr>, <https://github.com/kristoff-it/zine>
- Hosted builders: <https://bubble.io>, <https://www.framer.com>, <https://www.notion.com/product/sites>,
  <https://sites.google.com>, <https://carrd.co>
