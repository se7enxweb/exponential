---
title: "Handling objections: security, longevity and support"
audience: sales-marketing, project-manager
category: business
order: 70
summary: The objections buyers raise about Exponential (security, whether it is maintained, longevity, support, PHP versions, hosting lock-in, skills, upgrades, AI) with an answer built only from sources, the page that proves it, and the honest answer where the sources say no.
keywords: objections, objection handling, sales, buyer concerns, security, maintained, longevity, support, support commitment, PHP versions, lock-in, hosting, skills, developers, training, upgrades, migration, AI, data protection, community, risk, FAQ
type: help
---
# Handling objections: security, longevity and support

This page is for sales and marketing people and project managers who hear a buyer's doubts and want to answer them
with facts. Each objection gets an answer built only from documented sources, the page or file that proves it, and,
where the sources say "no" or say nothing, the honest answer. A buyer who checks your answer and finds it true will
trust the next one. A buyer who finds one overstatement will doubt everything, so this page prefers "the sources do
not state that" over a guess.

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

## Contents

1. [A method for any objection](#a-method-for-any-objection)
2. [Quick reference](#quick-reference)
3. [Security](#security)
4. ["Is it maintained?"](#is-it-maintained)
5. [Longevity: "will it still be here in ten years?"](#longevity-will-it-still-be-here-in-ten-years)
6. [Support](#support)
7. [PHP versions and modern technology](#php-versions-and-modern-technology)
8. [Hosting and lock-in](#hosting-and-lock-in)
9. [Skills: "can we find people who know it?"](#skills-can-we-find-people-who-know-it)
10. [Upgrades and migration](#upgrades-and-migration)
11. [Exponential Intelligence and data protection](#exponential-intelligence-and-data-protection)
12. [Languages, accessibility and performance](#languages-accessibility-and-performance)
13. [When the honest answer is no](#when-the-honest-answer-is-no)
14. [Record new objections](#record-new-objections)

## A method for any objection

1. **Listen to the end.** Write the objection down in the buyer's words.
2. **Acknowledge it.** The concern is reasonable; most of them are about risk.
3. **Ask what is behind it.** "When you say support, do you mean security fixes, someone to call, or PHP versions?"
   The answer tells you which row of this page to use.
4. **Answer with a source.** Say the fact, then name where it is written. Offer to send the link.
5. **Offer a check.** Where possible, give the buyer a way to verify it themselves (a command, a page, a test
   installation).
6. **Say what you do not know.** "The documentation does not state that. I will find out and send you the source."

## Quick reference

| Objection | Short answer from the sources | Proof |
|---|---|---|
| "Open source is less secure." | Private vulnerability reporting with a first answer within one business day; fixes in the next 6.0.x release; a CSP, an audit trail, two-step sign-in and a hardening guide | `SECURITY.md`, [Security hardening](../security-hardening.md) |
| "Is it still maintained?" | Fifteen 6.0.x releases from 1 January 2024 to 7 June 2026, 6.0.15 in development; CI on every push | [Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases), [CI](../../bc/6.0/ci.md) |
| "Will it last?" | A code base kept since 1999, a company and a nonprofit behind it, and a GPL license that keeps the code available to everyone | `COPYRIGHT.md`, [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) |
| "Who supports us?" | Documented commitments for PHP, security fixes, releases and documentation; service contracts from 7x or any provider; community places | [The support commitment](../who-is-the-exponential-foundation.md#the-support-commitment) |
| "Which PHP versions?" | PHP 8.0 to 8.5, Velocity included (from its release 0.0.4.54) | [PHP versions](../who-is-the-exponential-foundation.md#php-versions) |
| "We will be locked in." | GPL code, six databases, almost any operating system, documented moves between databases | [Comparing Exponential factually](comparing-exponential-factually.md#no-lock-in-point-by-point) |
| "Nobody knows this system." | The sources do not count developers; they document the stack and a lot of free learning material | [Skills](#skills-can-we-find-people-who-know-it) |
| "Upgrades are risky." | Published versions never change; update files per database; an upgrade check in the administration | [Upgrading](../../install/11-upgrading.md), [The upgrade check](../../guides/upgrade-check.md) |
| "AI will publish nonsense or leak data." | Drafts only, a region per provider, rules per section and class, one switch to turn it off | [Exponential Intelligence](../../features/6.0/extensions/expai.md) |
| "Isn't it the old discontinued product?" | Exponential continues the code base of eZ Publish, a product its creator discontinued after almost 18 years; fifteen 6.0.x releases have followed since 1 January 2024 | [Who is 7x?](../who-is-7x.md#stewardship-from-a-dormant-code-base-to-exponential-6) |

## Security

| Objection | Answer | Source |
|---|---|---|
| "How are vulnerabilities handled?" | "Please report security issues privately by e-mail ... You can expect a first answer within one business day." An accepted issue is fixed on `main` and released with the next 6.0.x version. | `SECURITY.md` |
| "Which versions get fixes?" | 6.0.x is the maintained line. Released tags are "fixed by upgrading to the newest 6.0.x"; 5.x, 4.x, 3.x and older are not supported. | `SECURITY.md` |
| "Can a fix change the version we certified?" | No. "A published tag is never changed": a fix reaches you as the next release. | `SECURITY.md` |
| "What about cross-site scripting?" | A Content Security Policy from `site.ini [ContentSecurityPolicy]`; by default report-only on the administration and off on the sites, with steps to enforce it. New administration templates carry no inline styles or scripts so a strict policy can be enforced. | [Content Security Policy](../../features/6.0/content-security-policy.md), `CONTRIBUTING.md` |
| "Can we see who did what?" | A tamper-evident audit trail, on by default; every record carries the hash of the one before; `verify` reports edits or cuts. | [The audit trail](../../features/6.0/audit-trail.md) |
| "Passwords get stolen." | Two-step sign-in with an authenticator app or an e-mail code. | [Two-step sign-in](../../guides/two-factor-login.md) |
| "Do you notice attacks?" | Probes and attacks are classified into `security.log`, with masked addresses; nothing is blocked by the classifier itself, listeners can act. | [Request logs](../../features/6.0/request-logs.md) |
| "Default passwords are a risk." | New installations generate a random administrator password when none or a well-known one is given. | [The default administrator password](../default-administrator-password.md) |
| "What should our administrators do?" | A threat-by-threat hardening guide and a checklist. | [Security hardening](../security-hardening.md#checklist), [chapter 13](../../install/13-security-hardening.md) |
| "Do you care about your dependencies?" | The se7enxweb profile README reports 14 security pull requests to Zeta Components in April 2026. | [The PHP stack it stands on](../who-is-the-exponential-foundation.md#the-php-stack-it-stands-on-and-gives-back-to) |

**Don't say** "it is secure" or "it has never been hacked": no source says either. Say what the process is and how the
customer can verify it.

## "Is it maintained?"

**Answer.** Yes, and the buyer can check it in minutes:

- Releases: 6.0.0 on 1 January 2024 to 6.0.14 on 7 June 2026, with 6.0.15 in development
  ([Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases)).
- Changes: "6556 ledger changes in 53 repositories since December 2023. 1037 of them were made by the se7enxweb team"
  ([The Exponential platform ecosystem](../../history/ecosystem.md)); every month is told in
  [the history](../../history/README.md).
- Tests: Quality and PHPUnit run on every push and pull request, and a weekly CI health report runs on Mondays
  ([Continuous integration](../../bc/6.0/ci.md)).
- The change log of the current line: [the 6.0.15 changelog](../../../changelogs/6.0/6.0.15.md).

**Let the buyer check.**

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git tag -l 'v6.0.*' --sort=version:refname    # every release, in the right order
git log -5 --format='%ad %s'                   # the latest changes
```

> **Note**
> Sort versions with `--sort=version:refname`: a plain sort by name puts `0.1.10` before `0.1.9`
> ([Who is the Exponential Foundation?, Releases and version numbers](../who-is-the-exponential-foundation.md#releases-and-version-numbers)).

## Longevity: "will it still be here in ten years?"

Nobody can promise the future, and you should not. What the sources show is a record and a structure:

| Fact | Source |
|---|---|
| The code base carries copyright years from 1999 (eZ Systems AS) and 1998 to 2026 (7x and the Exponential Foundation) | `COPYRIGHT.md` |
| "Exponential has made it beyond it's end of life in 2021 and survived" | `README.md`, "Exponential Project Status" |
| 7x took over leadership of the project in 2023; the company worked with this software since its first versions | [Who is 7x?](../who-is-7x.md#from-brookins-consulting-to-7x-in-three-stages) |
| The Exponential Foundation, a nonprofit founded on 19 March 2026, operates the project; its mission includes "Funding, Adoption, Support, Direction, Education, Development, Promotion and Documentation" | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#what-the-foundation-is) |
| The full history since 2002 is public; the GPL lets anyone keep, change and share the code | [Who is 7x?](../who-is-7x.md#principles-you-can-see-in-the-code), `LICENSE` |

**Say:** "The software has outlived its original vendor once already, because its code is free. Whatever happens to
any company, you keep the code, the history and the right to have anyone maintain it."

**Honest limits.** The sources do not state who sits on the Foundation's board, how it is governed, or its budget
([What the sources do not say](../who-is-the-exponential-foundation.md#what-the-sources-do-not-say)). Do not fill
that gap; tell the buyer to ask the Foundation through its website.

## Support

The project "does not publish one 'support policy' document"; its commitments are written down in several places
([The support commitment](../who-is-the-exponential-foundation.md#the-support-commitment)). Present them as a table:

| Kind of support | What exists | Source |
|---|---|---|
| Security fixes | On `main`, released with the next 6.0.x | [Security fixes](../who-is-the-exponential-foundation.md#security-fixes) |
| PHP compatibility | 8.0 to 8.5, with CI on 8.1 to 8.5 and a boot check on 8.0 | [PHP versions](../who-is-the-exponential-foundation.md#php-versions) |
| Release discipline | Published versions never change; the last position counts on | [Releases and version numbers](../who-is-the-exponential-foundation.md#releases-and-version-numbers) |
| Documentation | In the repository, free under the GNU FDL, shown in the administration | [Help system](../../features/6.0/help-system.md) |
| Bug reports | The Exponential issue tracker, and the **Report an error** page of the help (`/exphelp/report`) | [When something goes wrong](../when-something-goes-wrong.md) |
| Questions | The community forums at [share.exponential.earth](https://share.exponential.earth/forums) and the Telegram community chat named in `README.md` | [Community places](../who-is-the-exponential-foundation.md#community-places) |
| Paid services | 7x lists support and maintenance contracts, development, training, migration and managed hosting; prices and terms are on 7x's own website | [Who is 7x?, Services and hosting](../who-is-7x.md#services-and-hosting) |

**Don't say** a response time, an SLA or an uptime figure: the only documented time is the first answer to a security
report, "within one business day" (`SECURITY.md`). Service levels belong in a contract with a provider.

## PHP versions and modern technology

| Objection | Answer | Source |
|---|---|---|
| "It needs an old PHP." | PHP 8.0 is the oldest supported version; 8.1 to 8.5 are supported; `composer.json` allows up to 8.8. | [PHP versions](../who-is-the-exponential-foundation.md#php-versions) |
| "We run Red Hat Enterprise Linux 9 with its stock PHP." | PHP 8.0, the stock PHP of RHEL 9 and its rebuilds, runs it under Apache, PHP-FPM or Velocity (from Velocity 0.0.4.54). | `README.md`, [PHP 8.0 support](../../bc/6.0/php-8.0-support.md) |
| "We are still on PHP 7.4." | 6.0.7 is the last release for PHP 7.4; 7x "strongly recommends you to upgrade". | `README.md` |
| "The interface looks dated." | admin4, with light and dark mode; jQuery 4 and TinyMCE 8 in the 6.0.15 line; AdminUI as an alternative look. | [The admin4 design](../../features/6.0/admin4-design.md), [October 2026](../../history/2026/2026-10.md), [AdminUI](../../features/6.0/exp-adminui.md) |
| "We need headless delivery." | A REST API, versions 1 and 2. For a Symfony project with GraphQL, the family offers Exponential Platform DXP. | [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md), [Which product is for you](../who-is-the-exponential-foundation.md#which-product-is-for-you) |

## Hosting and lock-in

| Objection | Answer | Source |
|---|---|---|
| "We must host with you." | No. Anyone may host it: on Linux, BSD, macOS, Windows, containers or cloud machines, in documented tiers. | [Operating system support](../os-support.md#the-four-tiers) |
| "We need our own database." | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle or MySQL. | [Database support](../database-support.md#the-supported-databases-in-order) |
| "We need a database server we already run." | PostgreSQL is the recommended server; MySQL and MariaDB share one driver; Oracle through `ezoracle`. | same |
| "We cannot get our data out." | Standard dumps per database; documented tools to move between engines. | [Backup and restore](../database-support.md#backup-and-restore), [Moving a site](../database-support.md#moving-a-site-to-another-database) |
| "We only have shared hosting." | Apache or LiteSpeed with `.htaccess` serve it; without shell access the setup wizard installs it. Velocity usually cannot run there. | [Containers, cloud and hosting panels](../os-support.md#containers-cloud-and-hosting-panels), `README.md` |

## Skills: "can we find people who know it?"

This is the objection to answer most carefully, because the sources do not count developers, agencies or users.
Never invent a number.

**What you can say:**

1. **The stack is plain.** PHP 8, Composer, a template language, INI settings and the database of the customer's
   choice ([Who is 7x?, The application stack](../who-is-7x.md#the-application-stack)).
2. **The learning material is free and large.** This help; the installation book ([Contents](../../install/README.md));
   the guides; the official documentation site [doc.exponential.earth](https://doc.exponential.earth/); the community
   encyclopedia [eZpedia](https://ezpedia.exponential.earth/); the mirror of "three original developer training books
   (700+ pages combined)" and the book "Learning eZ Publish" under the GFDL
   ([Community places](../who-is-the-exponential-foundation.md#community-places)).
3. **There is a path from user to developer.** [Develop with Git](../develop-with-git.md) shows step by step how to
   grow from user to developer.
4. **Training can be bought.** 7x lists training among its services ([Services and hosting](../who-is-7x.md#services-and-hosting)).
5. **The code base has had many contributors.** 194 different commit authors before 2016, and pull requests from
   several organizations since December 2023 ([Contributors](../who-is-7x.md#contributors-and-friends-of-the-project)).

**What you must not say:** "There are thousands of Exponential developers", or anything about the size of the job
market. Turn it into a planning step instead: put onboarding and training into the project plan
([Planning an Exponential project](../leadership/planning-an-exponential-project.md)).

## Upgrades and migration

| Objection | Answer | Source |
|---|---|---|
| "Upgrades break sites." | The upgrade chapter starts with one rule: back up, and never run an installer over an existing site. Update files exist per database engine. | [Upgrading](../../install/11-upgrading.md) |
| "How do we know the upgrade is complete?" | **Setup > Upgrade check** compares the files with the release manifest and the database with the release schema. | [The upgrade check](../../guides/upgrade-check.md) |
| "We need to know what changes." | Changelogs per version and notes on backward compatibility for 6.0. | [Changelogs](../../../changelogs/README.md), [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) |
| "Our old site is from the 4.x or 5.x days." | Documented migrations from 3.10 and 4.x, from the 5.x legacy stack, and from eZ Platform and Ibexa. | [Chapter 14](../../install/14-migrating-from-4x.md), [chapter 15](../../install/15-migrating-from-5x-legacy.md), [chapter 16](../../install/16-migrating-from-ez-platform-and-ibexa.md) |
| "Migration is expensive." | The effort depends on the site; the chapters list what to take stock of. 7x offers migration as a service. | [Chapter 14, Take stock of the old site](../../install/14-migrating-from-4x.md), [Services](../who-is-7x.md#services-and-hosting) |

## Exponential Intelligence and data protection

| Objection | Answer | Source |
|---|---|---|
| "AI will publish wrong content." | It writes drafts only and never publishes on its own; **Send for approval** runs the normal publish operation, so an approval workflow holds the version. | [Exponential Intelligence](../../features/6.0/extensions/expai.md) |
| "Our content must not leave the EU (or the building)." | Each provider states its region: `us`, `eu` or `local`; rules per section and class decide which content may go where; a local open-weight model keeps the data on the server. | same, `README.md` |
| "We cannot control costs." | Every request is logged with its estimated cost; limits per user, day, month, site and provider; budgets for queued work. | same |
| "Our data protection officer must approve it." | **EI: Summary** is one printable page for a data protection officer. | same |
| "We do not want it at all." | It sends nothing until a provider key is added, and `[AISettings] Enabled` turns it off everywhere or per siteaccess. | `README.md` |

Questions of law (for example GDPR) are for the customer's counsel. Give them the EI: Summary and the audit facts;
do not give legal advice.

## Languages, accessibility and performance

| Objection | Answer | Source |
|---|---|---|
| "Our language is not supported." | 368 locale files exist; check the customer's language in the list. Every administration translation is complete (the current totals are at the top of the languages page); a language without one shows English. | [Languages in Exponential](../../languages.md) |
| "We need Arabic or Hebrew." | Right-to-left in content and in the administration; Hebrew and Arabic administration translations are complete. | [Right to left](../../features/6.0/right-to-left.md), [Support levels](../../languages.md#2-support-levels) |
| "Is the administration accessible?" | Skip link, landmarks, keyboard patterns, labeled fields, contrast and target-size tests in admin4. | [The admin4 design](../../features/6.0/admin4-design.md#landmarks-and-names) |
| "PHP is slow." | `README.md` documents 0.36 ms per cached page and 8,941 requests per second on a 12-core machine with Velocity, and states that most of the speed came from fixes that also apply on Apache or Nginx. Offer a measurement on the customer's hardware. | `README.md`, [Benchmarking](../../guides/benchmarking.md) |

## When the honest answer is no

Some objections are right. Say so, and offer the alternative the sources name.

| The buyer needs | Honest answer | Alternative |
|---|---|---|
| Microsoft SQL Server | No driver in 6.0.x | "Possible with additional work", documented ([Database support](../database-support.md#microsoft-sql-server-possible-with-additional-work)) |
| A Symfony-only stack | Exponential 6 is not that | Exponential Platform DXP ([Which product is for you](../who-is-the-exponential-foundation.md#which-product-is-for-you)) |
| A published SLA from the project | There is none | A service contract with a provider |
| More than 62 content languages in one installation | Not possible | Split into installations, or check the real need ([The limit of 62](../../languages.md#11-the-limit-of-62-content-languages)) |
| Velocity on shared hosting | Usually not possible | Apache or LiteSpeed with `.htaccess` (`README.md`) |
| PHP 7.4 with new features | No; PHP 7.4 stops at 6.0.7 | Upgrade PHP ([PHP versions](../who-is-the-exponential-foundation.md#php-versions)) |

## Record new objections

Every objection this page does not cover is a gap in the documentation. Keep a log and bring it to your team's
regular meeting ([Working together](../leadership/working-together.md)):

```text
Date: <date>     Context: <meeting type, industry>     Role of the person asking: <role, no names>
Objection, in their words: <text>
What we answered: <text>
Source we used, or "none found": <link or file>
Follow-up promised: <what, by when>
Documentation gap to report: <yes/no; link to the issue when filed>
```

When a gap is real, improve the help: every help page can be edited on GitHub, and the pages are free documentation
([Free documentation](../free-documentation.md)). Report errors through the help's **Report an error** page.

## Related pages

- [Comparing Exponential factually](comparing-exponential-factually.md)
- [Feature highlights mapped to customer needs](feature-highlights-for-customers.md)
- [Licensing explained for buyers: GPL code, GFDL documentation](licensing-for-buyers.md)
- [The community and ecosystem as a selling point](community-and-ecosystem.md)
- [Risk management for Exponential projects](../leadership/risk-management.md)
- [Release and upgrade planning](../leadership/release-and-upgrade-planning.md)
- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
