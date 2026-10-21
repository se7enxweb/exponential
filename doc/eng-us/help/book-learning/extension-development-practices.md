---
title: "Extension development practices"
audience: developer
category: books
order: 540
summary: The planning advice that opens the book's extension development chapter (goals, tests, timescales, learning curve, requirements, tools, sharing, upgrading, documentation) with the tools and habits of today.
keywords: extension development, planning, goals, testing, version control, git, license, sharing, upgrading, documentation, Learning book
type: help
---
# Extension development practices

Chapter 6 of the book builds two extensions in detail, a payment module for a provider of its day and a category datatype.
Those provider-specific parts have aged out, and the shop of Exponential 6 has its own payment gateway event
([payment gateway event](../e-commerce/checkout/payment-gateway-event.md)). What has not aged is the first section, the
practice of developing an extension. Each point of it is still a good rule.

## Design it first

- **Goals and targets.** Write down what the extension must do, and what it must not. A clear goal prevents scope creep, which
  costs time and money, and lets you separate the mandatory from the nice to have.
- **Plan the tests.** For each goal write how you will test it. At the end the list tells you whether you built what you
  needed. Today that is a test suite as well: [Tests for extensions](../extension-development/part-3-extension-developer/tests-for-extensions.md).
- **Timescales.** Put the deadlines, the development time and the release dates of what you build on early on paper. Add time
  for the first projects while you learn the libraries.
- **The learning curve.** Expect to need help. [Asking the community](../extension-development/part-1-from-user-to-developer/asking-the-community.md)
  says where and how to ask so that you get an answer.

## Requirements and the environment

Use the same versions as your live server: a feature that exists only in a newer PHP or database fails at deployment.
Exponential 6 runs on PHP 8.0 to 8.5 (see [Getting started](../../guides/getting-started.md)), and each extension names its
own requirements in `composer.json`. Test early on the platform you will deploy to. A development installation that is a
copy of live is the way ([Setting up a development installation](../extension-development/part-1-from-user-to-developer/setting-up-a-development-installation.md)).

## Tools

The book recommends version control (CVS and Subversion then) and an editor that understands HTML, PHP, SQL and template
files. Version control now means Git, and every extension is a Git repository ([Develop with Git](../develop-with-git.md)).
Use `php -l` on every PHP file you change and the debug output while you work
([Debugging extensions](../extension-development/part-3-extension-developer/debugging-extensions.md)).

## Sharing with the community

The book describes a dual license that obliged you to publish your code unless you paid. The license of Exponential is the
GNU General Public License 2.0 or later, which means that you may use and change it as you like, and that if you pass your
changes on, the receiver gets the same freedom. [Reading licenses before reusing](../extension-development/part-1-from-user-to-developer/reading-licenses-before-reusing.md)
and [Contribute extensions back](../contribute-extensions-back.md) cover what to do. Sharing is by a repository and a package
on Packagist, not a contribution page and a Subversion server.

## Upgrading

Keep a note of the major requirements of your extension (kernel version, PHP version, other extensions) so that you can
check them at each release of Exponential. The guidelines the book promises for updates exist: [Part 4: Upgrading](../extension-development/part-4-upgrading/README.md).

## Document it

If you ever returned to your own work after months and could not read it, you know why. Write what the extension is for, how to
install and set it up, what each setting means, and what the templates expect. [Documenting your extension](../extension-development/part-3-extension-developer/documenting-your-extension.md).

## Integrating existing code

The book asks "create a new application or hack an existing one?" and warns: linking another application doubles the
complexity and the support burden. Check whether native features, or a small extension, are cheaper than bridging a separate
package. When you do bridge, the book's two strategies still describe the choice: **import** the data into your own database
(simple and fast, but it can become stale), or **call** the other system when needed (current, but dependent on it being up).
Bridge by an interface, today the REST API or a feed, not by touching the other system's tables. See
[Remote services and apps](../../guides/remote-services-and-apps.md).

Next: [deploying](deploying-planning-and-checking.md).

## Source and license

Adapted from chapter 6, "Extension Development", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: kept the general practices, replaced tools and licensing by today's; the provider-specific examples are left out as obsolete.
