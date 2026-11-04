---
title: PHP content management systems and frameworks
audience: admin, developer, businesses, partner
summary: Every widely used PHP content management system, commerce platform and framework, grouped by license, with where each one leaves a site owner who has no budget but needs a modern, reliable site for years, and what Exponential does instead.
keywords: competition, comparison, WordPress, Drupal, Joomla, TYPO3, Contao, Neos, Craft CMS, Statamic, Kirby, October CMS, Winter CMS, Grav, Pimcore, Sulu, Magento, PrestaShop, WooCommerce, Laravel, Symfony, license, free software, total cost
---

# PHP content management systems and frameworks

PHP runs most of the web's content management systems, and Exponential is one of them. This page compares Exponential
with every other widely used PHP content management system, commerce platform and framework, from one point of view:
**a site owner with no budget for licenses, subscriptions or a rebuild every few years, who still needs a modern site
that keeps working, and that can be made to do almost anything at no extra cost.** It is part of the book
[Exponential Competition](README.md).

The products are grouped by the terms of their license, because the license decides what you may do with the software
once you depend on it. Each entry says what the product is, its license and business model, one thing it does well,
where it leaves the unfunded or long-term user, and what Exponential does instead. Every fact about another product is
taken from its own repository, license file, website or a public reference, all listed under
[Sources](#9-sources); licenses, versions and activity were read from the projects' repositories on 4 November 2026.
Prices change: the figures quoted are the vendors' published list prices on that day.

## Contents

1. [The yardstick](#1-the-yardstick)
2. [What Exponential gives you, verified](#2-what-exponential-gives-you-verified)
3. [GPL: strong copyleft](#3-gpl-strong-copyleft)
4. [LGPL, MPL and OSL: weak copyleft](#4-lgpl-mpl-and-osl-weak-copyleft)
5. [MIT, BSD and Apache: permissive](#5-mit-bsd-and-apache-permissive)
6. [Source-available and proprietary](#6-source-available-and-proprietary)
7. [The frameworks: what you build yourself](#7-the-frameworks-what-you-build-yourself)
8. [At a glance](#8-at-a-glance)
9. [Sources](#9-sources)

## 1. The yardstick

A product passes for the user this page has in mind when it:

- **costs nothing to use**, in production, on any number of sites, with no license key, per-site fee or paid update
  channel;
- **keeps its features in the free product**, instead of moving the parts a business needs into a paid edition, a
  marketplace or a hosted service;
- **does modern things out of the box**: structured content, many languages, roles and permissions, workflows, APIs,
  search, layouts, two-factor sign-in, an audit trail;
- **does not depend on a handful of third-party plugins** for those basics, whose owners can change terms, stop
  maintaining them or disappear;
- **carries a site forward** from one major version to the next without a rebuild;
- **runs on ordinary hosting**, with a choice of databases, and on current PHP versions.

## 2. What Exponential gives you, verified

Every claim on this page about Exponential is one of these, and each links the page that documents it:

- **E1, free software, all of it.** The kernel is licensed GPL-2.0-or-later (`composer.json`, `LICENSE.md`); the
  extensions of the distributions are published on GitHub and Packagist. The code has no license key, edition check or
  per-site fee. See [Distributions of Exponential](../DISTRIBUTIONS.md).
- **E2, current PHP.** It runs on PHP 7.4 and is supported on PHP 8.0 to 8.5; continuous integration tests every one of
  those versions ([Installing Exponential 6.0](../INSTALL.md)).
- **E3, five database engines.** MySQL and MariaDB, PostgreSQL, SQLite, MongoDB and Oracle
  ([SQLite](../features/6.0/sqlite-database.md), [MongoDB](../features/6.0/mongodb-database-support.md)).
- **E4, continuity since 2002.** The content model, the template language and the extension structure go back to
  eZ publish 3, released in 2002; sites and packages from those lines are carried forward, not rebuilt
  ([History of Exponential](../HISTORY.md)).
- **E5, the business features are in the kernel**, not in plugins: content classes for structured content, versions
  and drafts, more than 150 interface languages and multilingual content ([Languages](../languages.md)), roles and
  policies, workflows, two-factor sign-in
  ([second factor](../features/6.0/second-factor-on-other-sign-in-paths.md)), an
  [audit trail](../features/6.0/audit-trail.md), a [GraphQL API](../features/6.0/graphql.md) next to REST,
  [migrations](../features/6.0/migrations.md), a built-in help center ([help system](../features/6.0/help-system.md)),
  the [static cache](../features/6.0/static-cache-generator.md) and
  [static publishing](../features/6.0/static-publishing.md).
- **E6, its own application server.** Exponential Velocity serves the site with persistent workers, next to the usual
  Apache and PHP-FPM ([persistent worker server](../features/6.0/velocity-persistent-worker-server.md)).
- **E7, layouts and front-end editing** with Exponential Layouts and the website toolbar, shipped with the distributions
  ([Distributions of Exponential](../DISTRIBUTIONS.md)).

Exponential has limits too: it is a full platform, so a one-page brochure site is simpler elsewhere, and its community
is smaller than WordPress's or Drupal's. The [Community](../COMMUNITY.md) page says where to find help.

## 3. GPL: strong copyleft

The GPL keeps the software free for everyone who receives it. That protects the user; the risks below come from the
business models and the plugin economies around these products, not from the license.

### WordPress

- **What it is:** the most widely used PHP publishing system, run by the WordPress.org project; WordPress.com is a
  separate hosted service by Automattic.
- **License and model:** GPL-2.0 or any later version (`license.txt` in the repository). WordPress.com sells hosted
  plans; what a site there may install depends on its plan.
- **Does well:** a very large ecosystem of themes, plugins, hosts and people who know it.
- **Where it leaves you:** structured content, custom fields, multilingual sites, workflows, role editing, two-factor
  sign-in and an audit trail are not in core; they come from plugins, many of them with paid tiers. That dependence is
  a real risk: in 2024, during a dispute between Automattic and WP Engine, WordPress.org blocked WP Engine from its
  servers and took over the directory listing of WP Engine's Advanced Custom Fields plugin as "Secure Custom Fields";
  WP Engine obtained a preliminary injunction on 10 December 2024. A site that relied on one plugin was caught between
  two companies.
- **Exponential instead:** content classes, translations, workflows, roles and policies, two-factor sign-in and the
  audit trail are part of the kernel (E5), under one free license (E1), with no plugin vendor between you and them.

### ClassicPress

- **What it is:** a fork of WordPress without the block editor (Gutenberg).
- **License and model:** GPL-2.0 (repository); community project.
- **Does well:** keeps the classic WordPress editing experience for sites that do not want blocks.
- **Where it leaves you:** it inherits WordPress's dependence on plugins for structured content and business
  features, with a much smaller project behind it (863 stars on GitHub against WordPress's 21,517).
- **Exponential instead:** the same features in the kernel (E5), and a code line that has been carried forward since
  2002 (E4).

### Drupal

- **What it is:** a powerful community CMS, strong in structured content and access control.
- **License and model:** GPL-2.0-or-later (`composer.json`); community project with a large agency ecosystem.
- **Does well:** content types, fields, views and permissions in core; a large contributed module library.
- **Where it leaves you:** Drupal 8 (2015) was a rewrite on Symfony components, so Drupal 7 sites moved by migration,
  in practice a rebuild, rather than by an in-place upgrade. Drupal 7's end of life was postponed twice and set for
  5 January 2025. Sites that could not pay for the migration were left on an unsupported version.
- **Exponential instead:** the same strengths (structured content, roles and policies) and an upgrade line that has
  not been broken since 2002 (E4, E5).

### Backdrop CMS

- **What it is:** a community fork of Drupal 7 with a built-in upgrade path from Drupal 7.
- **License and model:** GPL-2.0 (repository); community project.
- **Does well:** the best route for a Drupal 7 site that cannot afford a migration to modern Drupal.
- **Where it leaves you:** a much smaller project (1,051 stars), and many Drupal 7 contributed modules exist only if
  someone ports them.
- **Exponential instead:** a maintained platform whose own history is the same kind of continuity, with the business
  features in the kernel (E4, E5).

### Joomla

- **What it is:** a long-standing community CMS; its newest release is 6.2.0 (13 October 2026).
- **License and model:** GPL-2.0 (repository); community project.
- **Does well:** multilingual sites, user groups and access levels in core, a broad extension directory.
- **Where it leaves you:** business features beyond those come from third-party extensions, so a site depends on its
  extension vendors keeping up with every major version; Joomla has needed transition aids for that before (a "legacy
  mode" for older extensions in Joomla 1.5). Its history also records that the Joomla 4 project did not live up to its
  developers' expectations at first.
- **Exponential instead:** templates and extensions that keep working across releases (E4), and the business features
  in the kernel rather than in third-party extensions (E5).

### TYPO3

- **What it is:** an enterprise CMS, strong in German-speaking countries.
- **License and model:** GPL-2.0-or-later (`composer.json`). Each long-term support (LTS) version is maintained for a
  fixed time; after that, security fixes come through Extended Long Term Support (ELTS), a paid service of TYPO3 GmbH.
- **Does well:** multi-site, multilingual sites and a mature editorial interface.
- **Where it leaves you:** a site that cannot upgrade every LTS cycle must either pay for ELTS or run unpatched; each
  LTS step can require extension and template changes.
- **Exponential instead:** no paid support channel for security fixes: they are published in the free releases (E1),
  and the upgrade path keeps sites working (E4).

### Neos

- **What it is:** a content application platform with inline editing.
- **License and model:** GPL-3.0 (repository); community project with partner agencies. Newest release 9.2.0.
- **Does well:** true inline editing and content dimensions (language, market).
- **Where it leaves you:** a small project (265 stars on its development repository), so few hosts, agencies and
  extensions know it.
- **Exponential instead:** front-end editing with the website toolbar and layouts (E7), on a longer, stable line (E4).

### MODX

- **What it is:** a CMS and framework that leaves the markup entirely to the developer.
- **License and model:** GPL-2.0 (repository); community project.
- **Does well:** full control of the HTML; templates and chunks.
- **Where it leaves you:** business features (multilingual, workflows, structured content beyond template variables)
  come from add-ons; a site is only as maintained as its add-ons are.
- **Exponential instead:** those features in the kernel (E5).

### Textpattern

- **What it is:** a small, long-running publishing system; newest release 4.9.2 (1 November 2026).
- **License and model:** GPL-2.0 (repository); community project.
- **Does well:** clean, lightweight publishing for writers.
- **Where it leaves you:** built for blogs and small sites, not for roles, workflows or multilingual structured
  content.
- **Exponential instead:** grows from a small site to a large one on the same platform (E5).

### WooCommerce and OpenCart

- **What they are:** WooCommerce is a commerce plugin for WordPress; OpenCart is a standalone shop.
- **License and model:** WooCommerce GPL-2.0, OpenCart GPL-3.0-or-later (repositories). Both have marketplaces of
  extensions; on woocommerce.com many extensions are paid subscriptions ("Paid plans start at $10/month" is a typical
  listing).
- **Do well:** a quick start for a small shop.
- **Where they leave you:** payment, shipping, tax and subscription features a real shop needs are often paid
  extensions with yearly renewals, so the running cost of a "free" shop grows with the shop.
- **Exponential instead:** a built-in shop (orders, the shop dashboard) in the free kernel (E1, E5); see the shop pages
  of this help center.

## 4. LGPL, MPL and OSL: weak copyleft

### Contao

- **What it is:** a CMS popular with German agencies, on Symfony.
- **License and model:** LGPL-3.0 (repository); community project with a published release plan of supported
  versions.
- **Does well:** accessibility and a clear editorial back end.
- **Where it leaves you:** each version is supported for its planned period only; staying safe means following the
  release plan.
- **Exponential instead:** fixes for the line you run, in free releases, with an upgrade path that does not require a
  rebuild (E1, E4).

### ProcessWire

- **What it is:** a CMS and framework built around a page tree and custom fields.
- **License and model:** the core is MPL-2.0, the site profiles MIT (`LICENSE.TXT`); free.
- **Does well:** a clean API and flexible fields.
- **Where it leaves you:** about 96% of the commits in its repository come from one developer (GitHub's contributor
  statistics), a continuity risk for a site meant to last many years.
- **Exponential instead:** a platform maintained by a company and a foundation, with its continuity documented
  (E4; [History of Exponential](../HISTORY.md)).

### PrestaShop

- **What it is:** an open-source shop system.
- **License and model:** the core is OSL-3.0, the modules AFL-3.0 (`LICENSE.md`). The PrestaShop Addons marketplace
  sells modules and themes.
- **Does well:** a complete shop core, strong in Europe.
- **Where it leaves you:** many features a shop needs come from the marketplace, where modules and themes are sold,
  so the running cost grows with the shop.
- **Exponential instead:** the shop is part of the free kernel (E1, E5).

### Magento Open Source and Adobe Commerce

- **What it is:** a large commerce platform; Magento Open Source is free, Adobe Commerce is its licensed edition.
- **License and model:** Magento Open Source is OSL-3.0 (repository); Adobe Commerce is sold by Adobe, which publishes
  a lifecycle policy with fixed support periods per release.
- **Does well:** very large catalogs and complex commerce.
- **Where it leaves you:** Magento 1 reached its end of life on 30 June 2020, and moving to Magento 2 meant a rebuild.
  It needs more than PHP and a database (a search service such as OpenSearch), and many of its business
  features are in the paid Adobe edition.
- **Exponential instead:** commerce and content on ordinary hosting, with SQLite as an option for small sites (E3, E5),
  and no paid edition (E1).

### Akeneo PIM Community Edition

- **What it is:** a product information management system.
- **License and model:** OSL-3.0 (`LICENCE.txt`); Akeneo sells enterprise editions and a cloud service.
- **Does well:** product data for many channels.
- **Where it leaves you:** it is a product data tool, not a website platform; a site still needs a CMS beside it.
- **Exponential instead:** structured content classes that hold product data and publish it on the same platform (E5).

## 5. MIT, BSD and Apache: permissive

Permissive licenses let anyone, including the vendor, take the code closed later. Two products on this page did:
October CMS and Pimcore (section 6).

### Concrete CMS

- **What it is:** a CMS with in-context page editing.
- **License and model:** MIT (repository); the vendor sells hosting, including compliance-certified hosting.
- **Does well:** in-page editing that non-technical editors understand.
- **Where it leaves you:** the vendor's business is hosting and services, and its marketplace sells the add-ons for
  needs beyond the core.
- **Exponential instead:** front-end editing (E7) on any hosting you choose, with five databases (E3).

### Grav

- **What it is:** a flat-file CMS: no database, content in Markdown files.
- **License and model:** MIT (repository); a shop sells premium plugins and themes.
- **Does well:** fast, simple sites with no database to run.
- **Where it leaves you:** flat files do not scale to many editors, roles and workflows, and structured, relational
  content is limited.
- **Exponential instead:** SQLite gives the same "no database server" simplicity, with the full content model, roles
  and workflows (E3, E5).

### Winter CMS

- **What it is:** the community fork of October CMS, created in March 2021 "due to a difference in open source
  management philosophies" (its README), on Laravel.
- **License and model:** MIT (repository); community project.
- **Does well:** keeps October's developer experience free.
- **Where it leaves you:** a small project (1,523 stars) that depends on the Laravel release cycle underneath.
- **Exponential instead:** a free license that the project has kept for more than two decades (E1, E4).

### Bolt and Sulu

- **What they are:** two CMSs built on Symfony.
- **License and model:** both MIT (repositories). Sulu 3 requires PHP 8.2 or later and Symfony 6.4 to 7.4 (its README).
- **Do well:** modern Symfony codebases; Sulu is strong in multilingual and multi-portal sites.
- **Where they leave you:** they follow Symfony's major versions, so a site upgrades on Symfony's schedule; Sulu 3
  already needs PHP 8.2.
- **Exponential instead:** runs on PHP 7.4 to 8.5 (E2), and its Symfony based distributions are a choice, not a forced
  move ([Distributions of Exponential](../DISTRIBUTIONS.md)).

### Silverstripe CMS

- **What it is:** a CMS and framework, popular in New Zealand and Australia.
- **License and model:** BSD-3-Clause (repository); newest release 6.3.1.
- **Does well:** a clean framework for custom data models.
- **Where it leaves you:** a smaller ecosystem (516 stars), and much of a business site is built by developers as
  custom data models.
- **Exponential instead:** content classes that editors and administrators define in the browser (E5), on an upgrade
  path that keeps code working (E4).

### Bagisto, Twill and Filament

- **What they are:** Laravel based products: Bagisto is a shop, Twill a CMS toolkit (by AREA 17), Filament an
  administration panel builder.
- **License and model:** Bagisto MIT, Twill Apache-2.0, Filament MIT (repositories).
- **Do well:** fast development for Laravel teams.
- **Where they leave you:** they are toolkits: someone has to build and maintain the site on top, and the Laravel
  release cycle sets the pace of upgrades.
- **Exponential instead:** a complete platform that editors use on day one (E5, E7).

### ExpressionEngine

- **What it is:** a long-running commercial CMS that became free.
- **License and model:** Apache-2.0 (repository) and a free download.
- **Does well:** flexible channels and fields.
- **Where it leaves you:** a small project today (506 stars), and a permissive license that, as October CMS and
  Pimcore showed, lets an owner take later versions closed.
- **Exponential instead:** a copyleft license that keeps it free (E1).

### Small and flat-file systems

| Project | License | State on 4 November 2026 | Where it leaves you |
|---|---|---|---|
| Typemill | MIT | active, 2.28.0 | Markdown documentation sites, not a general CMS |
| Bludit | MIT | active, 3.22.0 | content in JSON files, no database; small sites only |
| WonderCMS | MIT | active, 3.7.2 | one PHP file and one JSON file; small sites only |
| Pico | MIT | last release 2.1.4 in August 2020 | no release for six years |
| Croogo | MIT | last release 4.0.7 in October 2020, last change July 2023 | inactive |
| Pagekit | MIT | archived on GitHub; last release 1.0.18 in January 2020 | discontinued |
| CMS Made Simple | see its website | not on GitHub | small community |

**Exponential instead:** SQLite for a site without a database server (E3), and a maintained platform that grows with the
site (E5).

## 6. Source-available and proprietary

These products publish their source code, but their licenses do not give the four freedoms of free software: you need
a paid license for production use, for more than one site or for updates.

### Craft CMS

- **What it is:** a polished CMS by Pixel & Tonic.
- **License and model:** the "Craft License", proprietary (`composer.json`: `proprietary`). Craft Solo is free for one
  user; Team costs $279 and Pro $399 per project, each with one year of updates and $99 a year for updates after that.
  The license is perpetual; not renewing removes access to updates.
- **Does well:** a well-designed content model and editor.
- **Where it leaves you:** every project needs its own license, more than one editor needs a paid edition, and staying
  updated costs a yearly fee per site.
- **Exponential instead:** any number of users and sites, all updates free (E1).

### Statamic

- **What it is:** a Laravel based CMS, flat-file or database.
- **License and model:** proprietary (`LICENSE.md`): Statamic Core is free; Pro costs $349 per site with one year of
  updates and $99 a year after that. The license forbids using one license on more than one project and altering the
  licensing features.
- **Does well:** a pleasant editor and a strong Laravel integration.
- **Where it leaves you:** the features a team needs are in Pro, paid per site and per year.
- **Exponential instead:** no editions (E1).

### Kirby

- **What it is:** a file-based CMS.
- **License and model:** proprietary. Its license states: "While Kirby's source code is publicly available, Kirby is
  **not free**. To use Kirby in production, you need to purchase a license", and each project, defined by its URL,
  needs its own license.
- **Does well:** a flexible panel and clean templates.
- **Where it leaves you:** a paid license per site, by its own terms.
- **Exponential instead:** free for every site (E1).

### October CMS

- **What it is:** a Laravel based CMS that started as free software and moved to a paid license.
- **License and model:** proprietary end-user license (`composer.json`: `proprietary`); a free license with one year of
  system updates, and paid licenses for continued updates. Winter CMS was forked from it in 2021 (section 5).
- **Does well:** a clean developer experience.
- **Where it leaves you:** a project that changed its license after people had built on it is the clearest example of
  the risk that permissive or vendor-owned licenses carry.
- **Exponential instead:** a copyleft license that keeps it free (E1).

### Pimcore

- **What it is:** a data and experience management platform (PIM, MDM, DAM, CMS, commerce).
- **License and model:** with version 12 Pimcore moved from open source to the Pimcore Open Core License (POCL), a
  proprietary license; its editions are Community (source-available, free, under POCL), Professional and Enterprise
  (commercial).
- **Does well:** very broad data management features.
- **Where it leaves you:** a platform that was open source is now under a license the vendor controls, with the
  professional features in paid editions.
- **Exponential instead:** free software that stays free (E1).

### Laravel Nova and Automad

- **Laravel Nova** is a paid administration panel for Laravel: a single license costs $99 and renews at $79 a year, an
  unlimited license $299, renewing at $249 a year.
- **Automad** is a flat-file CMS under the "O'Saasy" license: permissive, except that nobody may offer it as a hosted
  service that competes with its author. That restriction makes it source-available rather than free software.
- **Exponential instead:** free for any use, including hosting it for others (E1).

## 7. The frameworks: what you build yourself

Laravel (MIT), Symfony (MIT), Laminas and Mezzio (BSD-3-Clause), CakePHP (MIT), CodeIgniter (MIT), Yii (BSD-3-Clause)
and Slim (MIT) are excellent, free frameworks, all actively maintained (repositories). They are not content management
systems. With a framework, the site owner pays for building and maintaining everything an editor needs: the content
model, the editing screens, versions, translations, roles and permissions, workflows, search, media, an API, two-factor
sign-in and an audit trail. Every framework major version also sets a schedule of upgrades that the custom code must
follow.

**Exponential instead:** all of that exists and is maintained (E5); developers extend it with the same tools a
framework offers ([Extending Exponential 6.0](../EXTENDING.md)), and Exponential's Symfony based distributions bring
Symfony itself when a project wants it ([Distributions of Exponential](../DISTRIBUTIONS.md)).

## 8. At a glance

| Product | License | Free for every production site | Where the paid parts are |
|---|---|---|---|
| **Exponential** | GPL-2.0-or-later | yes | none: no editions, keys or update fees |
| WordPress | GPL-2.0 or later | yes | plugins with paid tiers; WordPress.com plans |
| Drupal | GPL-2.0-or-later | yes | the migration from 7 to 8 and later |
| Joomla | GPL-2.0 | yes | commercial extensions |
| TYPO3 | GPL-2.0-or-later | yes | security fixes after LTS (ELTS) |
| Contao | LGPL-3.0 | yes | none in the core |
| ProcessWire | MPL-2.0 | yes | none in the core |
| Magento Open Source | OSL-3.0 | yes | Adobe Commerce |
| PrestaShop | OSL-3.0 | yes | Addons marketplace |
| WooCommerce | GPL-2.0 | yes | extension subscriptions |
| Grav | MIT | yes | premium plugins and themes |
| Winter CMS | MIT | yes | none in the core |
| Sulu, Bolt | MIT | yes | none in the core |
| Craft CMS | proprietary | Solo (one user) only | Team, Pro and yearly updates |
| Statamic | proprietary | Core only | Pro per site and yearly updates |
| Kirby | proprietary | no | a license per site |
| October CMS | proprietary | free license, one year of updates | paid licenses |
| Pimcore | POCL (proprietary) | Community edition | Professional and Enterprise |
| Laravel Nova | proprietary | no | every license |
| Frameworks | MIT, BSD-3-Clause | yes | the site you build on them |

## 9. Sources

Licenses, newest releases, stars and last activity: each project's GitHub repository (repository data and license file),
read on 4 November 2026:
[WordPress](https://github.com/WordPress/WordPress), [ClassicPress](https://github.com/ClassicPress/ClassicPress),
[Drupal](https://github.com/drupal/drupal), [Backdrop](https://github.com/backdrop/backdrop),
[Joomla](https://github.com/joomla/joomla-cms), [TYPO3](https://github.com/TYPO3/typo3),
[Contao](https://github.com/contao/contao), [Concrete CMS](https://github.com/concretecms/concretecms),
[Neos](https://github.com/neos/neos-development-collection), [ProcessWire](https://github.com/processwire/processwire),
[MODX](https://github.com/modxcms/revolution), [Bolt](https://github.com/bolt/core),
[PrestaShop](https://github.com/PrestaShop/PrestaShop), [Magento](https://github.com/magento/magento2),
[WooCommerce](https://github.com/woocommerce/woocommerce), [OpenCart](https://github.com/opencart/opencart),
[Grav](https://github.com/getgrav/grav), [October CMS](https://github.com/octobercms/october),
[Winter CMS](https://github.com/wintercms/winter), [Pagekit](https://github.com/pagekit/pagekit),
[Statamic](https://github.com/statamic/cms), [Kirby](https://github.com/getkirby/kirby),
[Craft CMS](https://github.com/craftcms/cms), [Filament](https://github.com/filamentphp/filament),
[Twill](https://github.com/area17/twill), [Sulu](https://github.com/sulu/sulu), [Pimcore](https://github.com/pimcore/pimcore),
[Bagisto](https://github.com/bagisto/bagisto), [Akeneo PIM](https://github.com/akeneo/pim-community-dev),
[Typemill](https://github.com/typemill/typemill), [Bludit](https://github.com/bludit/bludit),
[WonderCMS](https://github.com/WonderCMS/wondercms), [Pico](https://github.com/picocms/Pico),
[Automad](https://github.com/marcantondahmen/automad), [Textpattern](https://github.com/textpattern/textpattern),
[Silverstripe CMS](https://github.com/silverstripe/silverstripe-cms), [Croogo](https://github.com/croogo/croogo),
[ExpressionEngine](https://github.com/ExpressionEngine/ExpressionEngine), [Laravel](https://github.com/laravel/framework),
[Symfony](https://github.com/symfony/symfony), [Laminas MVC](https://github.com/laminas/laminas-mvc),
[Mezzio](https://github.com/mezzio/mezzio), [CakePHP](https://github.com/cakephp/cakephp),
[CodeIgniter](https://github.com/codeigniter4/CodeIgniter4), [Yii](https://github.com/yiisoft/yii2),
[Slim](https://github.com/slimphp/Slim).

Business models, prices and history:

- WordPress.com plans: <https://wordpress.com/pricing/>
- The WP Engine dispute, the "Secure Custom Fields" takeover and the preliminary injunction of 10 December 2024:
  <https://en.wikipedia.org/wiki/WP_Engine>
- ClassicPress, "Forked from WordPress without the block editor": <https://www.classicpress.net/>
- Drupal 7 end of life, set for 5 January 2025: <https://www.drupal.org/about/drupal-7/d7eol> and
  <https://en.wikipedia.org/wiki/Drupal>
- Backdrop's upgrade path from Drupal 7: <https://backdropcms.org/>
- Joomla's history: <https://en.wikipedia.org/wiki/Joomla>
- TYPO3 roadmap and Extended Long Term Support: <https://typo3.org/cms/roadmap>,
  <https://typo3.com/services/extended-support-elts>
- Contao release plan: <https://contao.org/en/release-plan>
- ProcessWire license: <https://processwire.com/about/license/>
- PrestaShop and its Addons marketplace: <https://www.prestashop.com/en>
- Magento 1 end of life on 30 June 2020: <https://en.wikipedia.org/wiki/Magento>; Adobe Commerce lifecycle policy:
  <https://experienceleague.adobe.com/en/docs/commerce-operations/release/planning/lifecycle-policy>
- WooCommerce extensions and plans: <https://woocommerce.com/products/>
- Concrete CMS hosting: <https://www.concretecms.com/>
- Grav and its premium shop: <https://getgrav.org/>
- Craft CMS pricing: <https://craftcms.com/pricing>
- Statamic pricing: <https://statamic.com/pricing>
- Kirby licenses: <https://getkirby.com/buy>
- October CMS pricing and free license: <https://octobercms.com/pricing>
- Pimcore's license change and editions: <https://en.wikipedia.org/wiki/Pimcore>, <https://pimcore.com/en>
- Laravel Nova pricing: <https://nova.laravel.com/>
- ExpressionEngine: <https://expressionengine.com/>
- Bludit, WonderCMS, Typemill: <https://www.bludit.com/>, <https://www.wondercms.com/>, <https://typemill.net/>
- CMS Made Simple: <https://www.cmsmadesimple.org/>
- PHP supported versions: <https://www.php.net/supported-versions.php>
