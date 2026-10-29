---
title: "Our goals: why Exponential exists and why we invite you to join"
audience: user, editor, admin, developer, partner, supporter, advocate, community, press, businesses, individual
category: project
summary: Why 7x and the developers of Exponential keep this code alive and current, what "the problems are already answered" means in features a site gets without extra work, what the freedoms of the GPL give you and others, how 7x earns a living without taking them away, honest answers to people who doubt our intentions, and the concrete ways to join.
keywords: goals, why, mission, intentions, motives, altruism, free software, open source, four freedoms, GPL, GFDL, 7x, Exponential Foundation, community, join, contribute, translate, catch, will it stay free, who owns the code, CLA, if 7x stops, continuity, batteries included, sustainability
type: help
---
# Our goals: why Exponential exists and why we invite you to join

This page is written by the people who make Exponential: 7x, the developers who work on the code every day, and the
Exponential Foundation that operates the project. It says why we do this work, what we want to give you, and why we
would like you to take part. It also answers the questions of people who doubt our intentions. Those are fair
questions, and the answers below point to the pages and files where you can check them yourself.

Every fact on this page comes from another page of this documentation, and the link is given. Where something is our
view rather than a fact, the page says so.

## Contents

1. [In short](#in-short)
2. [Why the project exists](#why-the-project-exists)
3. ["The problems are already answered"](#the-problems-are-already-answered)
4. [Why it is free software, and what that gives you](#why-it-is-free-software-and-what-that-gives-you)
5. [Who 7x is, and how 7x earns a living](#who-7x-is-and-how-7x-earns-a-living)
6. [Questions about our intentions](#questions-about-our-intentions)
7. [How to join](#how-to-join)
8. [Related pages](#related-pages)

## In short

- **Continuity.** The code you use has been developed since 1999, first as eZ Publish, a product its creator discontinued
  after almost eighteen years. 7x carried it forward and has published the 6.0.x releases since 1 January 2024
  ([Who is 7x?](help/who-is-7x.md#in-short)).
- **Problems answered once, for everyone.** Installing, choosing a database, translating, two-step sign-in, page
  layouts, approvals and upgrade checks are already built in, so a site does not have to build them again.
- **Free software.** The code is under the GNU GPL version 2 or later, the documentation under the GNU FDL 1.3 or
  later. You may use, study, change and share both, and so may everyone you give a copy to.
- **A company and a nonprofit.** 7x leads the project and sells services around it; the Exponential Foundation, a
  nonprofit, operates it. The software itself stays free ([Who is 7x?, Services and hosting](help/who-is-7x.md#services-and-hosting)).
- **An open invitation.** Report a problem, translate, write help, send a pull request, or run a site and tell others
  about it. Every way is listed in [How to join](#how-to-join).

## Why the project exists

### Continuity for the sites and the people who depend on it

Organizations built sites, intranets and shops on this code for more than twenty years. When its first vendor stopped,
those sites did not stop needing updates. "The platform originally known as eZ Publish reached end-of-life in 2021.
We kept going" ([Who is the Exponential Foundation?, Timeline](help/who-is-the-exponential-foundation.md#timeline)).
The [history of Exponential](history/README.md) begins with "the month a dormant code base changed hands", December
2023, when the maintainers made the setup wizard work on PHP 8, published the database update scripts and set the
first beta of 6.0.0 ([December 2023](history/2023/2023-12.md)).

7x reached that point in three stages: the idea in 2014, the start of the project in 2022 and its launch in December
2023 ([Who is 7x?, From Brookins Consulting to 7x](help/who-is-7x.md#from-brookins-consulting-to-7x-in-three-stages)).
The company had worked with this software since its first versions.

Our first goal is simple: a site built on this code should be able to keep running, safely and on current PHP,
without being rebuilt on something else. A migration of a site built in the eZ Publish years onto Exponential 6 is
one of the services 7x offers ([Services and hosting](help/who-is-7x.md#services-and-hosting)), and the code to do it
is free for anyone.

### Long-term care of a free CMS and DXP

Exponential is "a professional PHP application framework with advanced CMS (content management system)
functionality" ([Who is 7x?, The product](help/who-is-7x.md#the-product-what-exponential-is)). Next to it, 7x
maintains the Symfony based Exponential Platform distributions, Exponential Basic and Exponential Velocity
([Who is the Exponential Foundation?, The product family](help/who-is-the-exponential-foundation.md#the-product-family-at-a-glance)).

Long-term care means the unglamorous work that keeps software usable for years: every release is tagged and never
changed afterwards, so "a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next release, not as a
corrected copy of the old one"; security reports get "a first answer within one business day"
([The support commitment](help/who-is-the-exponential-foundation.md#the-support-commitment)); and every change since
December 2023 is told in [the history](history/README.md) and recorded in [the ledgers](history/ledger/README.md).

### Keeping it current

| What | Where it stands | Source |
|---|---|---|
| PHP | 8.0 to 8.5 supported; the test suite runs on PHP 8.1 to 8.5 on every push and pull request | [PHP versions](help/who-is-the-exponential-foundation.md#php-versions) |
| Security | Private reports to security@se7enx.com; fixes on `main`, published with the next 6.0.x release; a Content Security Policy, a tamper-evident audit trail and a hardening guide | [Security fixes](help/who-is-the-exponential-foundation.md#security-fixes), [Audit trail](features/6.0/audit-trail.md), [Security hardening](help/security-hardening.md) |
| Accessibility | The admin4 design follows WCAG rules that tests check (contrast in light and dark mode, target sizes); right-to-left layouts; keyboard shortcuts | [Languages and accessibility](help/who-is-the-exponential-foundation.md#languages-and-accessibility), [Right to left](features/6.0/right-to-left.md), [Keyboard shortcuts](help/keyboard-shortcuts.md) |
| The stack around it | Maintained forks and patches of Zeta Components, Symfony, Twig and Doctrine pieces, with security pull requests sent upstream | [The PHP stack it stands on and gives back to](help/who-is-the-exponential-foundation.md#the-php-stack-it-stands-on-and-gives-back-to) |

## "The problems are already answered"

Our view, in the words of 7x (26 October 2026): the world is harder to work in outside the Exponential family of CMS
and DXP products. Inside it, many problems have already been answered and can be completed in minutes, not days. We
solve the problems so that you do not have to do anything extra, wherever that is possible.

That is a view, and you should judge it by what the software does. These are the things a site gets without extra
work today, each with the page that describes it:

| The problem | What is already there | Read more |
|---|---|---|
| Getting a working site | `./console exp:install --apply` installs a multisite package on SQLite and prints the addresses and the login: "a working Exponential site in a few minutes, without writing a `kickstart.ini` and without a database server" | [Installing Exponential in one command](features/6.0/install-in-one-command.md) |
| Choosing a database | Six supported databases, in order of preference: SQLite 3 (the default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL; documented tools move a site between them | [Database support](help/database-support.md) |
| Choosing a machine | Operating systems in documented tiers, Apache, Nginx and others, or the bundled Velocity server | [Operating system support](help/os-support.md) |
| Many languages | Locale files for content, many of them right to left, and administration translations; the totals are at the top of the languages page | [Languages in Exponential](languages.md) |
| Help for the people who use it | The documentation inside the administration: searchable, by audience, with context help on the pages | [The help system](features/6.0/help-system.md) |
| Approvals before publishing | Workflows, events and triggers, an approval step with the collaboration inbox | [Workflows](guides/workflows.md) |
| Safer sign-in | Two-step sign-in (TOTP and e-mail codes), passkeys, and "Verify it's you" before sensitive actions | [Two-step sign-in](guides/two-factor-login.md), [Passkeys](guides/passkeys.md) |
| Building pages | Exponential Layouts: layouts, zones, blocks and rules, edited in the browser | [The layouts editor](help/layouts-editor.md) |
| Knowing whether an upgrade is safe | Setup > Upgrade check compares every file and the database schema with the release, and only reads | [The upgrade check](guides/upgrade-check.md) |
| Knowing who changed what | A tamper-evident audit trail of sign-ins, content changes and settings writes, on by default | [Audit trail](features/6.0/audit-trail.md) |
| Large removes, copies and moves | Content jobs run them in the background in batches and resume where they stopped, instead of failing on a time limit | [Content jobs](features/6.0/content-jobs.md) |
| A first draft | Exponential Intelligence writes drafts, never publishes on its own; hosted providers in the US or the EU, or a local model | [Exponential Intelligence](features/6.0/extensions/expai.md) |
| Staying up to date | Git clones with their full history, updated by hand or automatically | [Develop with Git](help/develop-with-git.md), [Update with Git](help/update-with-git.md) |

None of this is a separate edition. It is in the same free package, and the newest of it is listed in the
[6.0.15 changelog](../changelogs/6.0/6.0.15.md) while 6.0.15 is in development.

## Why it is free software, and what that gives you

Exponential is licensed under the GNU General Public License, version 2 or any later version. "Using Exponential
under the terms of the GNU GPL is free of charge. The Professional License is no longer available"
([Who is 7x?, Open source licenses](help/who-is-7x.md#open-source-licenses)). The documentation, this page included,
is under the GNU Free Documentation License 1.3 or later ([The license of this documentation](README-LICENSE.md)).

We chose to keep it that way because the freedoms are what made this code survive. When its first vendor stopped,
the license let others carry it on. We want the next person to have the same chance.

### The four freedoms, for you and for others

| Freedom | What it means here | For others |
|---|---|---|
| **Use** | Run Exponential for any purpose, commercial work included, on your own servers, containers or cloud machines | The sites you build for others may be run by them without a contract with anyone |
| **Study** | Read every line: the code and its history back to 2002 are public on GitHub | Students, trainers and reviewers can check any claim, including the ones on this page |
| **Change** | Change it on a branch; keep your changes private if you never distribute them | Your extension or fix can solve someone else's problem when you share it |
| **Share** | Give copies away or sell them, under the GPL version 2 or later | Whoever receives a copy gets the same four freedoms |

Sources: [Licensing questions](help/project/licensing-faq.md), [Comparing Exponential factually](help/business/comparing-exponential-factually.md).

### What the freedoms mean for each of you

- **Site owners**: no license fee, no lock-in to one host or one vendor. Content sits in a standard database of your
  choice, and any company may support your site, not only 7x
  ([Comparing Exponential factually](help/business/comparing-exponential-factually.md)).
- **Agencies**: build and sell sites and services for clients; the GPL allows commercial work. Extensions and designs
  you distribute go out under the GPL; what you never distribute stays yours
  ([Licensing questions](help/project/licensing-faq.md); what you owe your clients when you hand a site over:
  [License violations](help/project/licensing-faq.md#license-violations)).
- **Editors**: the help you read is free documentation; you may copy it into your own training material, translate
  it and improve it ([Free documentation](help/free-documentation.md)).
- **Translators**: a translation reaches every user of a language, often people who would never read an English
  manual ([Contributing translations](help/community/contributing/contributing-translations.md)). The 71 complete
  administration translations are a working example of "for others": each was done once and is now there for every
  installation ([Languages in Exponential](languages.md)).
- **Developers**: the whole stack is public Git repositories; a fix is a pull request away, and the code you learn
  here is plain PHP ([Develop with Git](help/develop-with-git.md)).

## Who 7x is, and how 7x earns a living

7x is "a responsive North American web design, support, development and website hosting company"; it was called
Brookins Consulting until it took the name 7x, and it "took over leadership of the project and it's development,
support, adoption and community growth in 2023" ([Who is 7x?](help/who-is-7x.md#in-short)). The Exponential
Foundation is a separate organization, "a 501(c)(3) Non-Profit Corporation founded in Seattle, Washington, USA" on
19 March 2026, which operates the project and funds it
([Who is the Exponential Foundation?](help/who-is-the-exponential-foundation.md#in-short)).

What the documentation says about money:

- **7x sells services around the open source software; the software itself stays free.** The services are web design
  and development, e-commerce and payment integration, training, migrations of sites built in the eZ Publish years
  onto Exponential 6, support and maintenance contracts, and managed hosting
  ([Services and hosting](help/who-is-7x.md#services-and-hosting)).
- **The project is funded by donations of time and resources.** "The Exponential Open Source Project is funded by
  donations of time and resources by 7x (and Exponential Sponsors)"; sponsoring 7x funds "the company that keeps the
  lights on", sponsoring the Foundation funds "the open-source project itself"
  ([The Foundation and 7x](help/who-is-the-exponential-foundation.md#the-foundation-and-7x)).
- **Partners** contribute financially and with code, and work directly with 7x and the Foundation
  ([Partners: start here](help/partners/partners-start-here.md)).

What the documentation does not say: the budget of either organization, and who sits on the Foundation's board
([What the sources do not say](help/who-is-the-exponential-foundation.md#what-the-sources-do-not-say)). We do not
guess at them here.

None of these ways of earning money takes a freedom away. You do not need to buy anything from 7x to use, change or
share Exponential, and nobody needs 7x's permission to offer the same services.

## Questions about our intentions

### What's the catch?

There is no license fee and no paid edition: "The Professional License is no longer available". The costs you do have
are your own: your server, your time, and the services you choose to buy, from 7x or from anyone else. The usual
lock-in risks are listed one by one, with the reason each does not apply, on
[Comparing Exponential factually](help/business/comparing-exponential-factually.md).

There is one condition you should know before you contribute code to the kernel: 7x and the Foundation require a
signed Contributor Licensing Agreement, and by signing and submitting, you assign the copyright in that contribution
to 7x, which may then use, change, relicense and distribute it without asking or paying you. Your own extensions are
not affected ([Copyright assignment (CLA)](../../CONTRIBUTING.md#copyright-assignment-cla),
[The CLA explained](help/community/contributing/the-cla-explained.md)). We say this plainly because you should decide
with all the facts.

### Will it stay free?

The license of the code is GPL version 2 or later and the license of the documentation is GNU FDL 1.3 or later; the
community pages list both as fixed ([Who decides](help/community/how-it-works/who-decides.md)). Every release is
published under that license and a published version is never changed afterwards
([Published versions never change](help/press/published-versions-never-change.md)). A copy you have received under
the GPL comes with the GPL's freedoms, and it is public on GitHub and Packagist with its full history.

We do not promise more than the documents say. We point you to what you can hold on to: the license, the public
history and the code itself.

### Who owns the code?

The copyright notice of the kernel reads "Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.", next to "Copyright
(C) 1999 - 2014 eZ Systems AS", and the code carries the work of hundreds of contributors over the years
([Who is 7x?, 7x and the Exponential Foundation](help/who-is-7x.md#7x-and-the-exponential-foundation),
[Contributors and friends of the project](help/who-is-7x.md#contributors-and-friends-of-the-project)). New code
contributions to the kernel are assigned to 7x through the CLA described above. In one sentence: 7x leads the project,
7x and the Foundation hold the copyright, and the license is the GPL
([Common errors in coverage](help/press/common-errors-in-coverage.md)).

The extensions, designs and sites you write are yours.

### What happens if 7x stops?

The answer is the same one this code has already lived through once. The code, its history back to 2002, the
documentation and every package are public: on GitHub under se7enxweb and on Packagist. The license lets anyone keep
and maintain the code ([Claims you must not make](help/advocates/claims-you-must-not-make.md)), and the
documentation lets anyone copy and continue it. The Foundation exists as a separate nonprofit so that the project is
not only one company's work ([What the Foundation is](help/who-is-the-exponential-foundation.md#what-the-foundation-is)).

We do not say "it will never go away". We say: you will never be left without the means to carry it on.

### Is this not just marketing for 7x?

Judge it by what you can check. The help pages name a source for every fact, list what the sources do not say, and
tell reporters and advocates which claims not to make: no user counts, no speed comparisons that were not published,
no promises of future dates ([What the sources do not say](help/press/what-the-sources-do-not-say.md),
[Writing a fair review](help/press/writing-a-fair-review.md)). Exponential is not associated with eZ Systems or Ibexa
([Not associated with eZ Systems or Ibexa](help/press/not-associated-with-ez-systems-or-ibexa.md)).

### Why should I contribute rather than just use it?

Using it is enough, and you owe nobody anything for it: you do not have to share your site's code, and an extension
you keep inside your organization does not have to be published ([Licensing questions](help/project/licensing-faq.md)).

Contributing is worth it for practical reasons. A fix you send back reaches every installation with the next release,
and you no longer carry it alone through each upgrade. A shared extension gets feedback, bug reports and improvements
back ([Contribute extensions back](help/contribute-extensions-back.md)). Every pull request is merged once its author
has revised it as the reviews ask ([Story: every pull request is merged](help/press/story-every-pull-request-merged.md)).
And the next person with your problem finds it already answered.

### Who decides what happens next?

The Foundation operates the project, 7x leads development, maintainers review and tag releases, and pull requests,
issues, the forums and testing are how community members influence decisions. There is no documented vote or
council ([Who decides](help/community/how-it-works/who-decides.md)).

## How to join

| Way | First step |
|---|---|
| Report a problem | [When something goes wrong](help/when-something-goes-wrong.md), [Filing a bug](help/community/contributing/filing-a-bug.md) |
| Ask and answer questions | The [forums](https://share.exponential.earth/forums) and the [Telegram chat](https://t.me/exponentialcms); [Getting support](help/project/getting-support.md) |
| Translate | [Contributing translations](help/community/contributing/contributing-translations.md), [Languages in Exponential](languages.md) |
| Write or improve the help | Every help page has "Edit on GitHub"; [Contributing documentation and help pages](help/community/contributing/contributing-documentation-and-help-pages.md) |
| Send a pull request | [Develop with Git](help/develop-with-git.md), [Contributing code](help/community/contributing/contributing-code.md), [CONTRIBUTING.md](../../CONTRIBUTING.md) |
| Share an extension or a theme | [Contribute extensions back](help/contribute-extensions-back.md), [Sharing extensions and themes](help/community/contributing/sharing-extensions-and-themes.md) |
| Run a site and tell others | [Advocates: start here](help/advocates/advocates-start-here.md) |
| Test what is coming | [Testing releases](help/community/contributing/testing-releases.md) |
| Fund the work | [Sponsors: start here](help/sponsors/sponsors-start-here.md), [Partners: start here](help/partners/partners-start-here.md) |

The overview of every way to help is [Contribute to the project](help/project/contribute-to-the-project.md), and the
community's own start page is [Community: start here](help/community/community-start-here.md).

## Related pages

- [Who is 7x?](help/who-is-7x.md) and [Who is the Exponential Foundation?](help/who-is-the-exponential-foundation.md)
- [The history of Exponential](history/README.md)
- [The Exponential project](help/project/README.md) and its [FAQ](help/project/faq.md)
- [Fact sheet for journalists](help/press/fact-sheet-for-journalists.md)
- [Free documentation](help/free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
