---
title: Trying the demo and a test install
audience: adopter, customer, businesses, admin
category: adoption
order: 12
summary: How adopters see Exponential working before they commit - the public demonstration installation and its demo mode, a test installation on SQLite with one command, what to look at in an afternoon, and what not to put into a test system.
keywords: demo, demonstration, demo mode, test installation, SQLite, install in one command, admin tour, evaluate, afternoon
type: help
---
# Trying the demo and a test install

## Contents

1. [The public demo](#the-public-demo)
2. [A test installation of your own](#a-test-installation-of-your-own)
3. [What to look at in an afternoon](#what-to-look-at-in-an-afternoon)
4. [What not to do with a test system](#what-not-to-do-with-a-test-system)

## The public demo

The help names a live demonstration installation as a source of its facts
([Businesses: start here](../../businesses/businesses-start-here.md)). A public demo runs in demo mode on purpose:
"administrator passwords cannot be changed or reset there" ([Demo mode](../../demo-mode.md),
[Demo mode FAQ](../../demo-mode-faq.md)). Treat everything you type there as public.

## A test installation of your own

A test installation shows the software on your own machine, with your own content:

1. Install PHP 7.4 or later and Composer.
2. Follow [Install in one command](../../../features/6.0/install-in-one-command.md); SQLite is the default and needs no
   database server.
3. Serve it with Velocity or PHP's built-in server ([Serving the site](../../../install/08-serving-the-site.md)).

The step-by-step guide for first-time users is [Getting started](../../getting-started.md); the full installation
book starts at [Introduction](../../../install/01-introduction.md).

## What to look at in an afternoon

| Look at | Page |
|---|---|
| The administration and its designs | [Admin tour](../../admin-tour.md) |
| Creating and publishing content, versions | [Editing content](../../editing-content.md), [Publishing and versions](../../publishing-and-versions.md) |
| Building a page | [The layouts editor](../../layouts-editor.md) |
| Languages | [Languages](../../languages.md) |
| Search | [Searching](../../searching.md) |
| Images and files | [Media and uploads](../../media-and-uploads.md) |
| The help itself | the "?" in the administration |

Write down what surprised you, in both directions. Those notes feed the scorecard
([The evaluation scorecard](evaluation-scorecard.md)).

## What not to do with a test system

- Do not use real personal data of customers or staff.
- Do not expose PHP's built-in server to the internet: it is "Development only" (`README.md`).
- Do not reuse the test installation as production without reinstalling and hardening it
  ([Security hardening](../../security-hardening.md)).

## Related pages

- [Evaluating Exponential](README.md)
- [A proof of concept](proof-of-concept.md)
- [Getting started](../../getting-started.md)
- [Admin tour](../../admin-tour.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
