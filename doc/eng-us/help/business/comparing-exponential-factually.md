---
title: Comparing Exponential factually
audience: sales-marketing
category: business
order: 50
summary: How to compare Exponential with other options using only properties a buyer can verify: the GPL license, self-hosting, the full Git history, the choice of databases, operating systems and web servers, published versions that never change, and the absence of lock-in.
keywords: comparison, compare, competitors, evaluation, RFP, tender, vendor lock-in, open source, GPL, self-hosting, Git history, databases, operating systems, web servers, PHP versions, CI, security policy, migration, fair comparison
type: help
---
# Comparing Exponential factually

This page is for sales and marketing people who are asked "how does Exponential compare?" in a meeting, a tender or a
request for proposal. It answers that question in the only way that survives a buyer's own checking: with properties
of Exponential that are documented and that the buyer can verify, each with the command or page that proves it. It
does not rank, name or criticize other products. A buyer who compares fairly will fill in the other column; your job
is to make the Exponential column complete, sourced and honest, including where Exponential is not the right fit.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../examples/web-servers/nginx.md), [LiteSpeed](../../examples/web-servers/litespeed.md),
> [Caddy](../../examples/web-servers/caddy.md), [IIS](../../examples/web-servers/iis.md), [Velocity](../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../hosting/panels/plesk.md), [cPanel](../hosting/panels/cpanel.md), [DirectAdmin](../hosting/panels/directadmin.md), [ISPConfig](../hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md).

## Contents

1. [The rules of a fair comparison](#the-rules-of-a-fair-comparison)
2. [The verifiable properties](#the-verifiable-properties)
3. [How a buyer checks each property](#how-a-buyer-checks-each-property)
4. [No lock-in, point by point](#no-lock-in-point-by-point)
5. [Questions to ask every option, with Exponential's answers](#questions-to-ask-every-option-with-exponentials-answers)
6. [A comparison grid to fill in](#a-comparison-grid-to-fill-in)
7. [Where Exponential is not the right fit](#where-exponential-is-not-the-right-fit)
8. [Talking about related projects](#talking-about-related-projects)
9. [Phrases: do and don't](#phrases-do-and-dont)
10. [A worked example: answering a tender question](#a-worked-example-answering-a-tender-question)

## The rules of a fair comparison

1. **Compare properties, not reputations.** A property is something the buyer can check: a license file, a commit
   history, a list of supported databases. A reputation is an opinion.
2. **Every claim has a source.** Use the links on this page. If you cannot find a source, leave the claim out or say
   "the documentation does not state this".
3. **Never describe another product.** You can say what Exponential does. What another product does is for the buyer to
   establish from that product's own documentation.
4. **Date your figures.** Counts such as commits or packages change. Give the date and the command the figure came
   from, as the history pages do ([Who is 7x?, 7x in numbers](../who-is-7x.md#7x-in-numbers)).
5. **Volunteer the limits.** The section [Where Exponential is not the right fit](#where-exponential-is-not-the-right-fit)
   is part of the comparison, not an appendix to hide.

## The verifiable properties

| Property | What is true for Exponential | Source |
|---|---|---|
| License | GNU General Public License, version 2 or any later version. "Using Exponential under the terms of the GNU GPL is free of charge. The Professional License is no longer available." | `README.md`, "License"; `LICENSE`; [Who is 7x?, Open source licenses](../who-is-7x.md#open-source-licenses) |
| Contributions stay free | "any modifications made to the project are also released under the same GPLv2 or later license" | `CONTRIBUTING.md` |
| Documentation license | GNU Free Documentation License 1.3 or later | [The license of this documentation](../../README-LICENSE.md), [Free documentation](../free-documentation.md) |
| Source and history | The full history is kept, back to the oldest commit of 2 September 2002; 27,242 commits on `main` on 18 October 2026 | [Who is 7x?, The eZ Publish years](../who-is-7x.md#the-ez-publish-years-1999-to-2021) |
| Lineage | Copyright notice "1998 - 2026 Exponential Foundation and 7x" next to "1999 - 2014 eZ Systems AS" | `COPYRIGHT.md` |
| Self-hosting | Runs on "almost any [operating system] that runs PHP 8.0 or later (PHP 7.4 usable without support)", in four documented tiers | [Operating system support](../os-support.md) |
| Databases | "1. SQLite 3 (the default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL" | [Database support](../database-support.md) |
| Web servers | Exponential Velocity (recommended), Apache, nginx, FrankenPHP, LiteSpeed, Caddy, IIS, lighttpd and others | [Operating system support, Web servers](../os-support.md#web-servers), `README.md` |
| PHP versions | PHP 8.0 to 8.5, Velocity included (from its release 0.0.4.54); PHP 7.4 usable without support from 6.0.15 | [Who is the Exponential Foundation?, PHP versions](../who-is-the-exponential-foundation.md#php-versions) |
| Release integrity | "A published tag is never changed": a fix arrives as the next release | `SECURITY.md` |
| Security process | Private reporting, a first answer "within one business day", fixes on `main` and in the next 6.0.x release | `SECURITY.md` |
| Continuous integration | Quality and PHPUnit on every push and pull request; PHPUnit on PHP 8.1 to 8.5; database tests on MySQL 8.0 and PostgreSQL 15; PHP 8.0 boot check | [Continuous integration](../../bc/6.0/ci.md) |
| Distribution | Composer packages on Packagist and Git repositories on GitHub under `se7enxweb` | [Getting the code](../../install/03-getting-the-code.md), [Develop with Git](../develop-with-git.md) |
| Stewardship | A company (7x) and a nonprofit (the Exponential Foundation) behind the project | [Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) |

## How a buyer checks each property

Give the buyer these steps; they need nothing from you.

```bash
# License and copyright
git clone https://github.com/se7enxweb/exponential.git
cd exponential
cat LICENSE COPYRIGHT.md

# The full history
git log --reverse --format='%ad %s' | head -1     # the oldest commit
git rev-list --count HEAD                          # the number of commits

# Every release, sorted correctly
git tag -l 'v6.0.*' --sort=version:refname

# Supported PHP versions
grep '"php"' composer.json
```

In a running installation, an administrator can show the database engine under **Setup > System information** (the
**Database** card), and whether the files of the installation match the release under **Setup > Upgrade check**
([The upgrade check](../../guides/upgrade-check.md)).

> **Tip**
> Offer the buyer a test installation they control. One command installs a working site on SQLite, with no database
> server: `./console exp:install --apply` ([Installing Exponential in one command](../../features/6.0/install-in-one-command.md)).

## No lock-in, point by point

"No vendor lock-in" is a claim buyers have heard before. Break it into parts that can each be checked:

| Lock-in risk | Why it does not apply to Exponential | Source |
|---|---|---|
| You may not run the software without a contract | The GPL "gives you the right to use, modify and redistribute Exponential", free of charge | `LICENSE`, `README.md` |
| Only the vendor may host it | It runs on the customer's own servers, containers or cloud machines | [Operating system support, Containers, cloud and hosting panels](../os-support.md#containers-cloud-and-hosting-panels) |
| Your content is in a proprietary store | Content sits in a standard database of the customer's choice, backed up with that database's own tools | [Database support, Backup and restore](../database-support.md#backup-and-restore) |
| You cannot change the database later | Documented tools move a site between SQL engines, into Oracle and into MongoDB | [Moving a site to another database](../database-support.md#moving-a-site-to-another-database) |
| Only the vendor can change the code | Every package is a public Git repository; changes are made on a branch and sent as pull requests | [Develop with Git](../develop-with-git.md) |
| Only the vendor can supply services | Anyone may provide services under the GPL; 7x is one provider, the code stays free | [Who is 7x?, Services and hosting](../who-is-7x.md#services-and-hosting) |
| Updates can silently change a version you rely on | Published tags never change | `SECURITY.md` |
| The documentation is behind a login | The documentation is in the repository, free under the GNU FDL | [Free documentation](../free-documentation.md) |

## Questions to ask every option, with Exponential's answers

Put the same neutral questions to every option the buyer considers. Exponential's answers are below; the buyer
collects the others.

| Question | Exponential's answer | Source |
|---|---|---|
| Under which license is the code, and does it cost anything to use? | GPL v2 or later, free of charge | `README.md`, "License" |
| Can we read the complete source and its history? | Yes, on GitHub, history from 2002 | [Who is 7x?](../who-is-7x.md#nearly-thirty-years-the-timeline) |
| Can we host it ourselves, on our operating system? | Yes; see the tier of your system | [Operating system support](../os-support.md#the-four-tiers) |
| Which databases are supported, and which are tested in CI? | Six supported; PostgreSQL and MySQL run database tests in CI, SQLite and MongoDB driver or adapter unit tests | [Database support, Test coverage](../database-support.md#test-coverage-ci) |
| How are vulnerabilities reported and fixed? | Privately by e-mail, first answer within one business day, fix in the next 6.0.x | `SECURITY.md` |
| Can a published release change after we install it? | No | `SECURITY.md` |
| How do we upgrade, and how do we check an upgrade? | Documented chapter, update files per engine, the upgrade check | [Upgrading](../../install/11-upgrading.md), [The upgrade check](../../guides/upgrade-check.md) |
| How do we leave, if we must? | Database dumps with standard tools, GPL code we keep | [Backup and restore](../database-support.md#backup-and-restore) |
| Who stands behind it? | 7x and the Exponential Foundation | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) |
| What does the project not publish? | No single "support policy" document; no board, governance or budget details for the Foundation | [The support commitment](../who-is-the-exponential-foundation.md#the-support-commitment), [What the sources do not say](../who-is-the-exponential-foundation.md#what-the-sources-do-not-say) |

## A comparison grid to fill in

Copy this grid into the buyer's evaluation document. The Exponential column is filled from the sources above; leave
the other columns for the buyer.

```text
| Criterion                         | Exponential 6.0.x                                   | Option B | Option C | How verified |
|-----------------------------------|-----------------------------------------------------|----------|----------|--------------|
| License of the code               | GPL v2 or later, free of charge                     |          |          | LICENSE      |
| License of the documentation      | GNU FDL 1.3 or later                                |          |          | doc/eng-us/README-LICENSE.md |
| Source and full history public    | Yes, since 2002                                     |          |          | git log      |
| Self-hosting                      | Yes, any OS with PHP 8.0+ (7.4 usable without support) (tiers)                   |          |          | os-support   |
| Databases                         | SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |          |          | database-support |
| Web servers                       | Velocity, Apache, nginx, FrankenPHP and others      |          |          | os-support   |
| PHP versions                      | 8.0 to 8.5                                          |          |          | composer.json |
| Published releases immutable      | Yes                                                 |          |          | SECURITY.md  |
| Security reporting                | Private, first answer within one business day       |          |          | SECURITY.md  |
| Multisite from one installation   | Yes (siteaccesses)                                  |          |          | README.md    |
| Content languages                 | 368 locale files; at most 62 per installation       |          |          | doc/eng-us/languages.md |
| Administration translations       | all complete (totals: languages page)               |          |          | doc/eng-us/languages.md |
| Visual page builder               | Exponential Layouts                                 |          |          | layouts-editor |
| Audit trail                       | Tamper-evident, on by default                       |          |          | audit-trail  |
| Writing assistant                 | Drafts only, region per provider                    |          |          | expai        |
| Migration paths documented        | From 3.10/4.x, 5.x, eZ Platform and Ibexa           |          |          | install/14-16 |
```

## Where Exponential is not the right fit

Say these yourself. Each is documented.

| Situation | The honest answer | Source |
|---|---|---|
| The customer must run Microsoft SQL Server | There is no driver in 6.0.x; it "is known to be possible with additional work" | [Database support](../database-support.md#microsoft-sql-server-possible-with-additional-work) |
| A Symfony-only project with no legacy kernel | Exponential Platform DXP, not Exponential 6, is the family member for that | [Which product is for you](../who-is-the-exponential-foundation.md#which-product-is-for-you) |
| Several replicas sharing one SQLite file | Not possible; several replicas need a database server, PostgreSQL first | [Operating system support](../os-support.md#containers-cloud-and-hosting-panels) |
| Shared hosting that forbids long-running processes | Velocity usually cannot run; Apache or LiteSpeed with `.htaccess` still serve the site | `README.md`, "When a traditional server is still the right answer" |
| Strict isolation of several customers on one server | Run one Velocity instance per site as its own system user | same |
| More than 62 content languages in one installation | The limit is 62 (30 on 32-bit PHP) | [Languages in Exponential](../../languages.md#11-the-limit-of-62-content-languages) |
| An administration fully translated into a language that is not complete today | Every shipped administration translation is complete; a language without one shows English (the current totals are at the top of the languages page) | [Support levels](../../languages.md#2-support-levels) |
| A requirement for CI-tested MariaDB or Oracle | Both are supported, neither is run in the project's CI | [Test coverage](../database-support.md#test-coverage-ci) |
| A demand for a published support policy or SLA document | The project publishes no single support policy; commitments are in the places listed on the Foundation page; contract terms come from a service provider | [The support commitment](../who-is-the-exponential-foundation.md#the-support-commitment) |

## Talking about related projects

Some buyers know the history. Use the tone of the history pages: factual, sourced, never dismissive.

- **The code base.** Exponential continues the code base of eZ Publish, a product its creator discontinued after almost
  18 years ([Who is 7x?, The eZ Publish years](../who-is-7x.md#the-ez-publish-years-1999-to-2021)).
- **No association.** "This project is not associated with the original eZ Publish software or its original developer,
  eZ Systems or Ibexa" (`README.md`, "Exponential Project Notice : 2025.08.12").
- **Credit where it is due.** Exponential Layouts brings the concept of the upstream layouts packages to the kernel; the LoveStack
  improvements of Mugo Web were rebuilt "with full credit to Mugo Web"; `cjw_newsletter` comes from CJW Network
  ([Who is 7x?, Contributors and friends of the project](../who-is-7x.md#contributors-and-friends-of-the-project)).
- **Customers on eZ Platform or Ibexa.** Do not argue about their current platform. Point to the documented migration
  paths: [chapter 16, Migrating from eZ Platform and Ibexa](../../install/16-migrating-from-ez-platform-and-ibexa.md),
  and for older sites [chapter 14](../../install/14-migrating-from-4x.md) and
  [chapter 15](../../install/15-migrating-from-5x-legacy.md).

## Phrases: do and don't

| Don't say | Say instead | Why |
|---|---|---|
| "We are better than X." | "Here is what Exponential does, and how you can check it." | No source can prove a ranking. |
| "X locks you in." | "With Exponential you keep the code under the GPL and your content in a standard database." | Describe Exponential, not others. |
| "It is the fastest CMS." | "The README documents 0.36 ms per cached page on a 12-core machine, reproducible with shipped scripts; let us measure on your hardware." | Figures need their context ([Benchmarking](../../guides/benchmarking.md)). |
| "It supports every language." | "368 locale files for content; administration translations (the current totals are at the top of [Languages in Exponential](../../languages.md))." | Exact and sourced. |
| "It is the new eZ Publish." | "Exponential continues the code base of eZ Publish, a product its creator discontinued after almost 18 years." | The product's name is Exponential. |
| "We guarantee support." | "Security reports get a first answer within one business day (SECURITY.md); service terms come from your provider." | Only what is written down. |
| "Hundreds of companies use it." | "The sources do not count users. The history shows hundreds of contributors over the years." | [Contributors](../who-is-7x.md#contributors-and-friends-of-the-project) |

## A worked example: answering a tender question

**The question in the tender:** "Describe how the proposed CMS avoids vendor lock-in and how the contracting authority
could change supplier."

**A sourced answer:**

```text
Exponential is licensed under the GNU General Public License, version 2 or any later version, and is free of
charge (LICENSE, README.md). The complete source code and its history since 2002 are public on GitHub
(github.com/se7enxweb/exponential). The authority can therefore use, modify and redistribute the software
without a license contract, and any supplier may provide services for it.

Content is stored in a standard database chosen by the authority: SQLite 3, PostgreSQL, MongoDB, MariaDB,
Oracle or MySQL (doc/eng-us/help/database-support.md), and is backed up with that database's own tools. Documented
tools move a site from one database engine to another.

Releases are published as Composer packages and Git tags; a published version is never changed afterwards
(SECURITY.md). The documentation is part of the repository and is free documentation under the GNU Free
Documentation License 1.3 (doc/eng-us/README-LICENSE.md), so it can be handed to a new supplier unchanged.
```

Each sentence of that answer points at a file the evaluator can open. That is the standard for every comparison.

## Related pages

- [Positioning and value proposition](positioning-and-value-proposition.md)
- [Feature highlights mapped to customer needs](feature-highlights-for-customers.md)
- [Handling objections: security, longevity and support](objection-handling.md)
- [Licensing explained for buyers: GPL code, GFDL documentation](licensing-for-buyers.md)
- [Brand and naming rules](brand-and-naming-rules.md)
- [Choosing hosting, database and operating system](../leadership/choosing-hosting-database-and-os.md)
- [Who is 7x?](../who-is-7x.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
