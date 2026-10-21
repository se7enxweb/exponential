---
title: Individuals: start here
audience: individual, website-creator, customer
category: website-creation
order: 1
summary: The landing page for people who run a website of their own on Exponential - a personal site, a portfolio, a blog, a family site or a club site - on a small budget, mostly alone: what Exponential gives you, what it does not, and where every page of this section is.
keywords: individual, personal site, portfolio, blog, family site, club site, hobby, small budget, SQLite, Velocity, shared hosting, gallery, comments, RSS, privacy, two-factor, backups, updates, start here
featured: true
type: help
---
# Individuals: start here

This section is for people who build and run a website on their own: a personal home page, a portfolio of
photographs or drawings, a blog, a private site for a family, or the site of a club or a small hobby group. You
probably do most of the work yourself, in the evenings, on a small budget, and you want a site that keeps working
without constant attention.

Every page is written for one person doing the work. Where a task needs a developer, a hosting company or a
partner, the page says so plainly, and where a feature does not exist, it says that too.

> **Note**
> Every feature named in this section was checked against the Exponential repository: the code, the settings, the
> root `composer.json` and the existing documentation, which each page links. The section names no customers, quotes
> no statistics and gives no prices of its own.

## Contents

1. [Exponential for one person](#exponential-for-one-person)
2. [Find your kind of site](#find-your-kind-of-site)
3. [Setting it up](#setting-it-up)
4. [Running it alone](#running-it-alone)
5. [What Exponential does not do for you](#what-exponential-does-not-do-for-you)
6. [A first weekend](#a-first-weekend)
7. [Other sections of this help](#other-sections-of-this-help)

## Exponential for one person

Exponential is a content management system: you describe your content once (a blog post has a title, a body, a
date and tags; a photograph has a caption and an image) and the system stores, lists, searches and publishes it.
Most of what one person needs ships with it:

| You want | What Exponential has | Where it is explained |
|---|---|---|
| A site you can update from any browser | The administration interface and the editor | [Getting started](../getting-started.md), [Editing content](../editing-content.md) |
| A blog | *Blog* and *Blog post* classes, with optional comments | [Blogs](blogs/README.md) |
| A photo or art portfolio | *Gallery* and *Image* classes, image variations, an image editor (`ezie`) | [Portfolios](portfolios/README.md) |
| Feeds readers can follow | RSS and Atom exports, built in | [RSS feeds for your blog](blogs/rss-feeds-for-your-blog.md) |
| A private area for family or members | Sections and roles | [Private areas](family-sites/private-areas-with-roles-and-sections.md) |
| Members who sign up themselves | User registration with activation by mail | [Members and registration](club-sites/members-and-registration.md) |
| A newsletter for a club | The `cjw_newsletter` extension | [Club newsletters](club-sites/club-newsletters.md) |
| No database server to look after | SQLite 3, the default database | [Running on a small budget](setup/running-on-a-small-budget.md) |
| A safer sign-in | Two-step sign-in (`sevenx_authentication_2fa`) | [Two-factor sign-in](running-it/two-factor-sign-in.md) |
| Help with a first draft | Exponential Intelligence (`expai`), drafts only, with a provider key you bring | [The writing assistant](running-it/writing-assistant-expai.md) |

The software is free software under the GNU General Public License, version 2 or later: there is no license fee
([Licensing for buyers](../business/licensing-for-buyers.md)).

## Find your kind of site

| You want to run | Start with |
|---|---|
| A personal home page: who you are, what you do, how to reach you | [Personal sites](personal-sites/README.md) |
| A portfolio of photographs, drawings or other work | [Portfolios](portfolios/README.md) |
| A blog, alone or with a friend | [Blogs](blogs/README.md) |
| A site only your family can see | [Family sites](family-sites/README.md) |
| The site of a club, a team or a hobby group | [Club sites](club-sites/README.md) |
| Something else, or you are not sure Exponential fits | [Is Exponential right for you?](is-exponential-right-for-you.md) |

A registered charity or association with staff may find [Non-profits](../non-profits/non-profits-start-here.md)
closer to its needs, and a shop or a trade belongs in [Local businesses](../local-businesses/local-businesses-start-here.md).
If you are not used to technical words at all, read [Non-technical people](../non-technical-people/non-technical-people-start-here.md)
alongside this section.

## Setting it up

The [Setup](setup/README.md) pages walk through the decisions in order:

1. [Choosing an install method](setup/choosing-an-install-method.md): the browser wizard, the console install or the
   kickstarter.
2. [Choosing a site package and theme](setup/choosing-a-site-package-and-theme.md): what the shipped packages and
   themes give you.
3. [Running on a small budget](setup/running-on-a-small-budget.md): SQLite, one small server, few moving parts.
4. [Shared hosting or a small server](setup/shared-hosting-or-a-small-server.md): what works where, and why Velocity
   needs a server of your own.
5. [Domain names and HTTPS](setup/domain-names-and-https.md): what you arrange outside Exponential.

## Running it alone

The [Running it](running-it/README.md) pages cover the small routines that keep a one-person site healthy:
[backups](running-it/backups-for-one-person.md), [updates](running-it/updates-for-one-person.md),
[two-factor sign-in](running-it/two-factor-sign-in.md), [spam and security basics](running-it/spam-and-security-basics.md),
the [writing assistant](running-it/writing-assistant-expai.md), [when to hire help](running-it/when-to-hire-help.md)
and [learning with the Student partner level](running-it/learning-with-the-student-level.md).

The [checklists](checklists/README.md) turn them into lists you can print: a launch checklist, a monthly upkeep list,
a family privacy checklist and a site plan worksheet.

## What Exponential does not do for you

Some things are outside the software. Knowing them early saves a weekend:

- **Domain names and DNS** are bought from a registrar and pointed at your server; Exponential cannot register a
  domain ([Domain names and HTTPS](setup/domain-names-and-https.md)).
- **There is no one-click importer from another blog platform**; moving content is a small project
  ([Moving from another blog platform](blogs/moving-from-another-blog-platform.md)).
- **Comments come from the shipped *Comment* class**, not from a separate comment system; there is no `ezcomments`
  extension in this distribution ([Comments on your blog](blogs/comments-on-your-blog.md)).
- **The `birthday` extension stores dates of birth; it sends no reminders**
  ([Birthdays and family dates](family-sites/birthdays-and-family-dates.md)).
- **Mail must leave your server** for registration, password resets and newsletters; that is a hosting matter.

The full list is on [What exists and what does not](what-exists-and-what-does-not.md).

## A first weekend

| When | Do | Read |
|---|---|---|
| Friday evening | Write down what the site is for, who reads it and what must stay private | [Site plan worksheet](checklists/site-plan-worksheet.md) |
| Saturday morning | Install Exponential on your own computer with SQLite and look around | [Choosing an install method](setup/choosing-an-install-method.md), [Admin tour](../admin-tour.md) |
| Saturday afternoon | Create your first pages and pictures; try the editor | [Editing content](../editing-content.md), [A personal site in a weekend](personal-sites/a-personal-site-in-a-weekend.md) |
| Sunday morning | Decide where the site will live and what it costs | [Running on a small budget](setup/running-on-a-small-budget.md) |
| Sunday afternoon | Turn on two-step sign-in and take a first backup | [Two-factor sign-in](running-it/two-factor-sign-in.md), [Backups for one person](running-it/backups-for-one-person.md) |

The full order of reading is the [reading path](individuals-reading-path.md). Short answers are in the
[FAQ](individuals-faq.md) and the words are explained in the [glossary](individuals-glossary.md).

![The administration dashboard of Exponential](../images/admin-dashboard.webp)

## Other sections of this help

| Section | For |
|---|---|
| [Website creators](../creators/website-creators/website-creators-start-here.md) | The detailed building pages this section links to |
| [Customers](../customers/customers-start-here.md) | Owners of a site someone else built and runs |
| [Blogs (customers)](../customers/types/blog-start-here.md) | The owner's view of a blog |
| [Non-profits](../non-profits/non-profits-start-here.md) | Charities and associations |
| [Local businesses](../local-businesses/local-businesses-start-here.md) | Shops, trades and services |
| [Non-technical people](../non-technical-people/non-technical-people-start-here.md) | Plain-language help for everyone |

## Related pages

- [Individuals reading path](individuals-reading-path.md)
- [Is Exponential right for you?](is-exponential-right-for-you.md)
- [What exists and what does not](what-exists-and-what-does-not.md)
- [Website creators: start here](../creators/website-creators/website-creators-start-here.md)
- [Customers: start here](../customers/customers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
