---
title: Newcomer FAQ
audience: advocate
category: advocacy
order: 92
summary: The questions newcomers ask advocates most often about Exponential, each with a short, sourced answer to give and the page to link - what it is, cost, who makes it, how to try it, requirements, languages, help, security, contributing.
keywords: FAQ, newcomer questions, beginners, answers, what is Exponential, cost, try it, requirements, languages, help, contribute, security
type: help
---
# Newcomer FAQ

This page is for advocates who answer newcomers. Each answer is short enough to say or paste, with the page that
proves it. Check the source before you answer: facts change with releases.

## About the project

| Question | Answer | Page |
|---|---|---|
| What is Exponential? | A PHP application framework with advanced CMS functionality; its best feature is a fully customizable content model | [Exponential 6](../who-is-the-exponential-foundation.md#exponential-6-the-kernel) |
| Is it free? | Yes: the code is under the GNU GPL v2 or later, the documentation under the GNU FDL | [Talking points: open source](talking-points-open-source-and-licensing.md) |
| Who makes it? | 7x leads development; the Exponential Foundation, a nonprofit, operates the project | [7x and the Foundation](7x-and-the-foundation-for-advocates.md) |
| Is it maintained? | Fifteen 6.0.x releases since 1 January 2024; 6.0.15 in development | [Is it still alive?](objection-is-it-still-alive.md) |
| Isn't that eZ Publish? | It continues that code base; eZ Publish was discontinued by its creator; Exponential is not associated with eZ Systems or Ibexa | [Writing about the history correctly](writing-about-the-history-correctly.md) |
| Which Exponential do I need? | Depends on PHP-only or Symfony, framework or ready site | [Knowing the product family](knowing-the-product-family.md) |

## Trying and running it

| Question | Answer | Page |
|---|---|---|
| How do I try it? | `./console exp:install --apply`: a complete site on SQLite in one command | [Install in one command](../../features/6.0/install-in-one-command.md) |
| What does it need? | PHP 8.0 or later; a database (SQLite needs no server) | [Operating system support](../os-support.md), [Database support](../database-support.md) |
| Which web server? | Velocity is recommended; FrankenPHP or Apache/nginx with PHP-FPM work too | [Velocity](../../features/6.0/velocity-persistent-worker-server.md) |
| Shared hosting? | Possible with Apache or LiteSpeed; Velocity usually not | [Containers, cloud and hosting panels](../os-support.md#containers-cloud-and-hosting-panels) |
| Is my language supported? | 368 locale files for content; administration translations (the current totals are at the top of [Languages in Exponential](../../languages.md)) | [Talking points: languages](talking-points-languages-and-right-to-left.md) |

## Getting help and helping

| Question | Answer | Page |
|---|---|---|
| Where is the documentation? | In the administration (the "?" button) and at doc.exponential.earth | [The help system](../../features/6.0/help-system.md) |
| Where do I ask? | The forums or the Telegram chat | [The community channels](community-channels.md) |
| I found a bug | Use the help's Report an error page and the right tracker | [Which tracker for which report](which-tracker-for-which-report.md) |
| I found a security problem | Privately, as SECURITY.md describes | [Security reports stay private](security-reports-stay-private.md) |
| How can I help? | Answer a question, fix a help page, translate, report bugs | [The advocacy ladder](the-advocacy-ladder.md) |
| Can I pay someone to build my site? | Yes, any provider; 7x is one | [Services and hosting](../who-is-7x.md#services-and-hosting) |

**Why does the demo say "Demo mode"?** The public demo publishes its administrator password and runs in demo mode, so nobody can change or reset it and the next visitor can still sign in. It is rebuilt from the current code every few hours. [Demo mode FAQ](../demo-mode-faq.md).

## Related pages

- [Welcoming newcomers](welcoming-newcomers.md)
- [Pointing to the right help page](pointing-to-the-right-help-page.md)
- [Advocate FAQ](advocate-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
