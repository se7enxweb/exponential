# History of Exponential (1999 - 2026)

This book tells the whole story of the code you run. The first part is the history of Exponential itself: how the
legacy kernel was taken over in 2023, every 6.0 release, the rename, the features that changed what the software can
do, the Exponential Foundation, the distributions and the community. The second part goes further back, release by
release, to where the source code came from: eZ publish, written by eZ Systems AS in Norway from 1999, through the
3.x and 4.x lines, the Symfony based eZ Publish 5, eZ Platform and the company that is called Ibexa today, and how
the legacy kernel lived on until it became Exponential.

Read it to understand why the code looks the way it does, why an identifier still starts with `ez`, which old
documentation still applies, and where a feature first appeared. Every date in the Exponential part comes from the
repository itself (its tags, commits and changelogs); every date about eZ Systems and Ibexa comes either from the
repository's own historical files or from a public source listed in [Sources](#16-sources). Where a date could not be
confirmed, this page says "around" or leaves it out.

## Contents

1. [In short](#1-in-short)
2. [The family tree of the code](#2-the-family-tree-of-the-code)
3. [Exponential: taking over the legacy kernel (2023)](#3-exponential-taking-over-the-legacy-kernel-2023)
4. [Exponential 6.0.0 to 6.0.9 (2024 - 2025)](#4-exponential-600-to-609-2024---2025)
5. [The name Exponential (August 2025)](#5-the-name-exponential-august-2025)
6. [Exponential 6.0.10 to 6.0.14 (2025 - 2026)](#6-exponential-6010-to-6014-2025---2026)
7. [The 6.0.15 line (2026)](#7-the-6015-line-2026)
8. [The Exponential Foundation and 7x](#8-the-exponential-foundation-and-7x)
9. [Distributions, packages and the community](#9-distributions-packages-and-the-community)
10. [eZ Systems and eZ publish 1.x and 2.x (1999 - 2002)](#10-ez-systems-and-ez-publish-1x-and-2x-1999---2002)
11. [eZ publish 3 (2002 - 2007)](#11-ez-publish-3-2002---2007)
12. [eZ Publish 4 (2007 - 2012)](#12-ez-publish-4-2007---2012)
13. [eZ Publish 5, eZ Platform and Ibexa (2012 - today)](#13-ez-publish-5-ez-platform-and-ibexa-2012---today)
14. [How the legacy kernel lived on (2014 - 2023)](#14-how-the-legacy-kernel-lived-on-2014---2023)
15. [Licenses, version numbers and names over time](#15-licenses-version-numbers-and-names-over-time)
16. [Sources](#16-sources)

## 1. In short

| Year | What happened |
|---|---|
| 1999 | eZ Systems AS was founded in Norway; the eZ publish project started the same year. |
| 2000 | eZ publish 1.0, a GPL licensed PHP content management system, appeared in November. |
| 2001 | The 2.x line (2.0 to 2.2) made eZ publish a widely used open source CMS; Debian packaged 2.2. |
| 2002 | The rewrite that became eZ publish 3 began (first commit of this repository: 2 September 2002). |
| 2003 | eZ publish 3.0 was released (tag `3.0-1`, 24 March 2003): the kernel, module and template architecture that this code still has. |
| 2003 - 2007 | Ten 3.x minor lines, from 3.1 to 3.10. |
| 2007 | eZ Publish 4.0 brought PHP 5 (tag `4.0.0`, 4 December 2007). |
| 2009 - 2012 | 4.1 to 4.7; from 2011 the free edition shipped as the eZ Publish Community Project (4.2011, then 2011.5 and later builds). |
| 2012 | eZ Publish 5 put a Symfony based stack next to the old kernel, which was called the legacy kernel from then on. |
| 2014 | The last eZ Publish releases by eZ Systems: 5.4 and the community build 2014.11 (17 November 2014). |
| 2015 | eZ Platform, without the legacy kernel, was released on 15 December 2015. |
| 2016 - 2021 | The legacy kernel lived on as `ezsystems/ezpublish-legacy` (releases 2017.10 to 2019.03.x) and in a fork by Netgen. |
| 2020 | eZ Systems announced its new name, Ibexa, on 27 April 2020. |
| 2023 | se7enxweb forked the legacy kernel on GitHub (5 October 2023) and merged its first outside change on 11 December 2023. |
| 2024 | Exponential's predecessor release 6.0.0 became stable on 1 January 2024, with an SQLite driver; the admin3 design followed in August. |
| 2025 | PHP 8.4 (April) and PHP 8.5 (December) support; the product was renamed Exponential in August 2025 (6.0.10). |
| 2026 | MongoDB (6.0.14, June), Velocity, the admin4 design, the help center, the Exponential Foundation as copyright holder, and the 6.0.15 line. |

## 2. The family tree of the code

The code in this installation has one continuous history. Each step below kept the previous one's source and its
version control history; nothing was rewritten from scratch after 2002.

1. **eZ publish 1.x and 2.x** (1999 - 2002), by eZ Systems. A different code base; its ideas, not its files, went into 3.0.
2. **eZ publish 3.x** (2002 - 2007). The first commit of this repository is from 2 September 2002, in the
   Subversion repository "nextgen" at eZ Systems. The early version numbers 2.9-1 to 2.9-8 were the betas of 3.0.
3. **eZ Publish 4.x** (2007 - 2012). The same kernel, moved to PHP 5.
4. **eZ Publish 5.x "legacy"** (2012 - 2014). The same kernel, run inside or next to the new Symfony stack.
5. **`ezsystems/ezpublish-legacy`** on GitHub (repository created in June 2010; community releases until 2019.03.x;
   archived since, with the note "Succeeded by eZ Platform, no further feature releases planned").
6. **`netgen/ezpublish-legacy`** (from February 2016), "maintained by Netgen for compatibility with eZ Platform".
7. **`se7enxweb/exponential`** (from 5 October 2023), forked from Netgen's repository, released as 6.0.x, and
   renamed Exponential in August 2025.

You can walk this history yourself:

```bash
git log --reverse --format='%ad %h %s' --date=short | head -3     # 2002-09-02 008ec26cca Added trunk dir.
git for-each-ref --sort=creatordate --format='%(creatordate:short) %(refname:short)' refs/tags | head
git tag -l 'v6.0.*' --sort=version:refname
```

The repository holds nearly 30,000 commits. By year, the activity tells the story on its own: thousands of
commits a year from 2002 to 2011 at eZ Systems, a few hundred a year while the 5.x line was maintained, a trickle
from 2015 to 2022, and then the se7enxweb era from 2023, with more than 8,000 commits in 2026 alone.

## 3. Exponential: taking over the legacy kernel (2023)

By 2023 the legacy kernel had been without a vendor release for years (the last `ezsystems/ezpublish-legacy` tag,
2019.03.6.1, was made in September 2021), but many sites still ran it. 7x (known as Brookins Consulting before 2022),
a company that had worked with eZ Publish for a long time, forked Netgen's maintained copy on GitHub as
`se7enxweb/exponential` on 5 October 2023.

- **11 December 2023:** the first outside change of the fork was merged (upstream pull request #40).
- **December 2023:** the setup wizard was made to work on PHP 8, and update scripts from 5.4 were published, so a
  site on the last eZ Systems release could move to the new line.

The month by month story of this period, and of every month since, is in the [history chronicle](history/README.md).

## 4. Exponential 6.0.0 to 6.0.9 (2024 - 2025)

The new line was numbered 6.0, the next major number after the 5.x releases of eZ Systems. The dates are the dates of
the release tags in this repository.

| Release | Date | Headline |
|---|---|---|
| 6.0.0 | 1 January 2024 | Stable on PHP 8; SQLite arrived as a database that needs no server (driver, schema handler, demo database). |
| 6.0.1 | 29 January 2024 | Fixes after the first stable release. |
| 6.0.2 | 29 February 2024 | Fixes; the root of the content tree can be viewed (March). |
| 6.0.3 | 2 April 2024 | Maintenance release. |
| 6.0.4 | 5 September 2024 | admin3, a complete responsive administration design (introduced 22 August 2024). |
| 6.0.5 | 1 October 2024 | Maintenance release. |
| 6.0.6 | 17 December 2024 | admin3 corrected after testing on real devices. |
| 6.0.7 | 1 February 2025 | Subtree copy; the last release for PHP 7.4 sites. |
| 6.0.8 | 2 May 2025 | PHP 8.4 support; PHP 7 support ended. |
| 6.0.9 | 10 June 2025 | Further PHP 8.4 work and headless use. |

In July 2025 the licensing and credit were made explicit with `COPYRIGHT.md` and `LICENSE.md` (GNU GPL version 2 or
later).

## 5. The name Exponential (August 2025)

In August 2025 Ibexa asked 7x to stop using the trademarked name "eZ Publish" for the new releases. 7x renamed the
product to **Exponential** at once: the rename was made in the repository on 12 August 2025, and 6.0.10, the first
release under the new name, followed on 28 August 2025.

What changed and what did not:

- **Changed:** the product name in the user interface, documentation, packages and messages.
- **Not changed:** identifiers, class names (`eZContentObject`, `eZINI`), file paths (`bin/php/ezcache.php`), database
  tables (`ezcontentobject`) and settings. Every existing site, extension and template kept working.
- **New code** uses the prefix `exp` (for example `expHelp`, `exp:velocity`), so you can tell the eras apart.

From then on the old name is used only for history, as on this page.

## 6. Exponential 6.0.10 to 6.0.14 (2025 - 2026)

| Release | Date | Headline |
|---|---|---|
| 6.0.10 | 28 August 2025 | The first release named Exponential. |
| 6.0.11 | 30 December 2025 | PHP 8.5 support (nullable types, raised Zeta Components). |
| 6.0.12 | 10 February 2026 | Maintenance; the test tool chain moved to PHPUnit 13 in March. |
| 6.0.13 | 20 April 2026 | Maintenance and extension updates. |
| 6.0.14 | 7 June 2026 | MongoDB 8 as a database (arrived 1 June 2026). |

## 7. The 6.0.15 line (2026)

6.0.15 is the largest line in the history of the code. It is in development as this is written and has no release
tag yet; its full notes are in the [6.0.15 changelog](../changelogs/6.0/6.0.15.md). The main steps, in the order they
arrived:

- **July 2026, the command line grew up:** the package manager joined the console, a one command installer and the
  Kickstarter arrived, and a password reset tool.
- **August 2026:** template debugging that names the template that really ran, six security patches, and tests on
  every change.
- **September 2026:** long admin lists that are paged and sortable, a one command install, a hardening sweep, and
  **Velocity**, a persistent worker web server for Exponential, which arrived on 22 September and became the
  recommended engine within two days.
- **October 2026:** jQuery 4, the **admin4** design, the audit trail, `exp:ini`, a debug bar, content jobs and
  TinyMCE 8; PHP 8.0 became the oldest supported version; the **help center** with its books in English and
  German; Exponential Intelligence (`expai`); the front-end editing toolbar `expwt`; administration translations
  in 156 languages, among them Quechua, Greenlandic, Hawaiian and Guarani.
- **November 2026:** Oracle and MongoDB tested next to MySQL, PostgreSQL and SQLite for every site package, and the
  site packages rebuilt with new demo content.

## 8. The Exponential Foundation and 7x

The copyright of Exponential is held by the **Exponential Foundation** (<https://foundation.exponential.earth>), the
nonprofit that operates the project for its users and community, and **7x** (<https://se7enx.com>), the company that
leads the development and supports the customers. On 29 October 2026 the copyright moved from 7x alone to the
Exponential Foundation and 7x together, and every notice of the project became one notice:
`Exponential Copyright © 1998-2026 Exponential Foundation and 7x`.

The original developer's notice stays, as the license requires: Exponential was originally developed by eZ Systems AS
as eZ Publish, `Copyright (C) 1999 - 2014 eZ Systems AS`. The details are in `COPYRIGHT.md` in the installation root.

## 9. Distributions, packages and the community

Around the kernel the se7enxweb team maintains a family of repositories (53 of them are covered by the history
ledger):

- **Distributions:** Exponential 6 (this kernel), and the Symfony based Exponential Platform lines: 3.x, Platform
  Legacy 2.5, the 4.6 DXP skeleton, Platform v5 with the Nexus starter, and the CJW flavor of Nexus. See the
  [ecosystem overview](history/ecosystem.md).
- **The floor packages** `se7enxweb/exponential-php-floor` (lines 1.0 to 1.3) bring the bundled extensions that fit
  each PHP range.
- **Site packages** (ezwebin_site, ezflow_site, ezdemo_site, sevenx_site, sevenx_multisite, cjw_multisite) install
  complete demo sites; in November 2026 they were rebuilt at release 15-stable with new content about Exponential.
- **Demos** run at demo.exponential.earth and related hosts, rebuilt regularly from the current code.
- **Documentation:** the help center in this installation, doc.exponential.earth, and the archives of the old
  community material that 7x keeps online.

## 10. eZ Systems and eZ publish 1.x and 2.x (1999 - 2002)

eZ Systems AS was founded in Norway in 1999. Its co-founder Aleksander Farstad described it as a company started by
his brother, two friends and himself, first as a consultancy for Linux and open source solutions; he was its chief
executive from 1999 to 2009 and again later. Contemporary sources disagree on the town of the founding: one company
history names Kongsberg (May 1999), while the company's long time headquarters was Skien, from where its press
releases were dated. Later the company was headquartered in Oslo.

- **The eZ publish project started in 1999.** The product was written in PHP and published as free software under
  the GNU General Public License.
- **eZ publish 1.0** appeared in November 2000.
- **eZ publish 2.x** (2.0 to 2.2, around 2001 to 2002) made it a widely used open source CMS. Debian packaged
  eZ publish 2.2 in October 2001, recording its license as GPL version 2. eZ Systems later offered the 2.x line under
  two licenses: the GPL, and a paid "eZ publish Professional" license for those who could not accept the GPL.
- The 2.x code base was not the ancestor of this repository's files. The team started a new design, internally
  called "nextgen", which became 3.0.

## 11. eZ publish 3 (2002 - 2007)

eZ publish 3 is the architecture that Exponential still has: the kernel with its modules and views
(`kernel/content`, `kernel/user` and so on), the content model of classes, objects, versions and nodes, the
datatypes, the INI settings with their override layers, the siteaccesses, the template language with its operators
and fetch functions, the design and override system, extensions, workflows and roles with policies.

The development started in the Subversion repository "nextgen" (first commit 2 September 2002). The betas were
numbered 2.9-1 to 2.9-8 ([release overview](release-overview)). The release dates below are the dates of the tags in
this repository; the headline features are taken from the feature notes in `doc/eng-us/features/` and the changelogs
in [doc/changelogs](../changelogs/README.md).

| Line | First release | Headlines |
|---|---|---|
| 3.0 | 24 March 2003 (3.0-1) | The new kernel, content classes and objects, versions, the template language, multiple sites. Betas from 1 November 2002 (2.9-3). |
| 3.1 | 26 August 2003 | Maintenance and the first improvements of the new kernel. |
| 3.2 | 16 September 2003 | Further development; 3.2 received fixes until May 2004 (3.2-6). |
| 3.3 | 7 January 2004 | Maintenance releases into 2005 (3.3-7). |
| 3.4 | 2 June 2004 (3.4.0) | A new version scheme with three numbers; 3.4.8 in July 2005. |
| 3.5 | 22 December 2004 | Class editing and removal checks, content cache clearing, custom navigation parts, more datatype information, hiding of content. |
| 3.6 | 30 May 2005 | The admin toolbar, copy of a subtree, advanced related objects, database transactions, upload handlers. |
| 3.7 | 12 October 2005 | Siteaccess settings in extensions. |
| 3.8 | 2 May 2006 | The approval workflow, clustering, content diff, the table of contents operator, the left menu of the admin, edit handlers. |
| 3.9 | 18 December 2006 | Class names in many languages, locked accounts after failed logins, alphabetical navigation, debug per user, sorting defaults per class. |
| 3.10 | 9 October 2007 | Cluster improvements, multilingual URL aliases, SSL proxy support, the `ezmodule` operator. |

The 3.x line ended with 3.10.1 and the last 3.9 release in August 2008.

## 12. eZ Publish 4 (2007 - 2012)

eZ Publish 4.0 was the 3.10 kernel moved to PHP 5. eZ Systems announced it from Skien on 6 December 2007; the tag in
this repository is dated 4 December 2007. Around this time the product name was written with a capital P:
"eZ Publish".

| Line | First release | Headlines |
|---|---|---|
| 4.0 | 4 December 2007 | PHP 5, class autoloading, the static cache by user agent; 4.0.20 in February 2009. |
| 4.1 | 19 March 2009 | Cache improvements, clustering, a Windows installer, the language switcher, output filters, system information. |
| 4.2 | 13 November 2009 | The eZDFS cluster file handler for several web servers. |
| 4.3 | 14 July 2010 | Mail sending, the LDAP login handler, shop improvements. |
| 4.4 | 29 September 2010 | `extension.xml` and extension ordering, host and URI siteaccess matching, INI and session improvements. |
| 4.5 | 2011 | Asynchronous publishing, `autoload` ignore lists, database errors as exceptions, registration through a workflow. |
| 4.6 | around late 2011 | PHP namespace support in the autoloader. |
| 4.7 | 2012 | Cluster events and the cluster index. |

**The Community Project.** From 2011 eZ Systems split the product: the paid **eZ Publish Enterprise** edition with a
subscription, and a free edition built by the **eZ Publish Community Project**. The first build was named 4.2011; from
the second build (2011.5, released in May 2011) the builds were named by year and month (`yyyy.mm`), for example
2011.9, 2012.9 or 2013.1. Enterprise subscribers could choose a proprietary license instead of the GPL (see
[section 15](#15-licenses-version-numbers-and-names-over-time)).

## 13. eZ Publish 5, eZ Platform and Ibexa (2012 - today)

**eZ Publish 5 (2012 - 2014).** In 2012 eZ Systems announced that the next version would use the Symfony full stack
framework with Twig templates. eZ Publish 5.0 (code name "Kilimanjaro", community build 2012.10) put a new content
repository API and the Symfony stack next to the existing kernel, which was called the **legacy kernel** from then on.
Both worked on the same database; old modules and templates ran through a fallback. 5.1 added the public APIs, and
5.2, 5.3 and 5.4 followed until 2014. The last community build, 2014.11, was released on 17 November 2014, and the last
eZ Publish release by eZ Systems came out in December 2014.

**eZ Platform (2015 - 2020).** eZ Systems released eZ Platform, the successor without legacy code, on 15 December
2015, with an Enterprise edition next to it. eZ Platform 2.5, a long term supported version, was released in March
2019, and eZ Platform 3.0 on 2 April 2020.

**Ibexa (2020 - today).** On 27 April 2020 eZ Systems announced its new name, Ibexa, and its repositioning as a
digital experience platform (DXP) vendor. The product continued as **Ibexa DXP**: 3.2 on 26 October 2020, 4.0 in
February 2022, 4.6, a long term supported version, on 13 February 2024, and 5.0 in 2025. Ibexa DXP is offered in
several editions (Content, renamed Headless in 4.6; Experience; Commerce). QNTM Group acquired Ibexa on 19 November
2021; Ibexa merged with Quable in December 2024 and announced a merger with Raptor in January 2025. Ibexa is
headquartered in Oslo, Norway, and its website is <https://www.ibexa.co>.

Exponential is not affiliated with Ibexa. It continues the legacy kernel that eZ Systems published under the GPL,
and it credits eZ Systems as the original developer, as the license requires.

## 14. How the legacy kernel lived on (2014 - 2023)

After 2014 the legacy kernel was no longer eZ Systems' product, but it was not abandoned:

- **eZ Systems** kept publishing community releases of `ezsystems/ezpublish-legacy` for sites that ran eZ Platform
  together with legacy code through the legacy bridge: 2017.10 (30 October 2017) up to the 2019.03 series (2019.03.0
  in May 2019; the last tag, 2019.03.6.1, in September 2021). The internal version "5.90" in this repository is an
  alias for those releases from 2017.08 on. The repository is archived today.
- **Netgen**, a Croatian agency that built many eZ sites and later Netgen Layouts, kept its own fork,
  `netgen/ezpublish-legacy` (from February 2016), "maintained for compatibility with eZ Platform", with PHP 7 fixes.
- Commits from 2019 to 2023 in this repository show that work: PHP 7.2 to 7.4 fixes, and in 2022 the allowed PHP
  versions raised to 7.4 and 8.1.
- **se7enxweb** forked Netgen's repository in October 2023, which is where [section 3](#3-exponential-taking-over-the-legacy-kernel-2023)
  begins. Netgen Layouts, ported to this kernel as Exponential Layouts, came back into the code base in 2026.

## 15. Licenses, version numbers and names over time

**Licenses.**

| Period | License |
|---|---|
| 1999 - 2002 (1.x, 2.x) | GNU GPL; from 2.x also a paid "eZ publish Professional" license |
| 2003 - 2014 (3.x to 5.x) | GNU GPL version 2; for Enterprise subscribers also the eZ Proprietary Use License (eZPUL), and from 4.7 Enterprise the eZ Business Use License (eZ BUL) |
| 2015 - today (Ibexa) | Open source and commercial editions of eZ Platform and Ibexa DXP, under their own terms |
| 2023 - today (Exponential) | GNU GPL version 2 or any later version; documentation under the GFDL 1.3 ([README-LICENSE](README-LICENSE.md)) |

**Version numbers.**

- 2.9-x: the betas of 3.0 (2002 - 2003).
- 3.0-1, 3.1-1 ... 3.3-7: the early 3.x releases, numbered by line and build.
- 3.4.0 to 4.7.x: three part versions.
- 4.2011, then `yyyy.mm` (2011.5 to 2014.11): the Community Project builds.
- 5.0 to 5.4: eZ Publish 5; 5.90: the alias of the legacy releases from 2017.08 on.
- 2017.10 to 2019.03.x: the community releases of `ezsystems/ezpublish-legacy`.
- 6.0.0 to 6.0.15: the se7enxweb line, eZ Publish 6 until August 2025 and Exponential since.

**Names.** "eZ publish" (with a small p) in the early years, "eZ Publish" from about 2007, "eZ Platform" for the
successor from 2015, "Ibexa DXP" from 2020, and "Exponential" for this code since August 2025. The prefix `ez` in
class, file and table names comes from that history and stays, because changing it would break every site and
extension.

## 16. Sources

The Exponential part and the dates of the 2.9 to 4.4 releases come from this repository: its tags
(`git for-each-ref refs/tags`), its commit history, [the release overview](release-overview), the
[changelogs](../changelogs/README.md), the feature notes (`doc/eng-us/features/`), the [history chronicle](history/README.md),
`COPYRIGHT.md`, and the GitHub data of the repositories `ezsystems/ezpublish-legacy`, `netgen/ezpublish-legacy` and
`se7enxweb/exponential`. The other facts come from these public sources (checked on 4 November 2026):

- Wikipedia, "eZ Publish": <https://en.wikipedia.org/wiki/EZ_Publish>
- Wikipedia, "Ibexa DXP": <https://en.wikipedia.org/wiki/Ibexa_DXP>
- Ibexa, "eZ Systems Announces Company Name Change to Ibexa" (April 2020): <https://www.ibexa.co/blog/ez-systems-announces-company-name-change-to-ibexa>
- Netgen, "Interview with Aleksander Farstad, founder of eZ Systems and eZ Publish open source CMS" (5 October 2011): <https://netgen.io/blog/interview-with-aleksander-farstad-founder-of-ez-systems-and-ez-publish-open-source-cms>
- SitePoint, "Interview – Bard Farstad of eZ systems": <https://www.sitepoint.com/bard-farstad-ez-systems/>
- tek.no, company profile of eZ Systems: <https://www.tek.no/artikkel/i/opl94R/bedriftsprofil-ez-systems-norden>
- PR.com, "eZ Systems Introduces eZ Publish 4.0" (6 December 2007): <https://www.pr.com/press-release/63111>
- LinuxFr.org, "eZ Publish 4.0, première version d'eZ Publish supportant PHP5": <https://linuxfr.org/news/ez-publish-40-premiere-version-dez-publish-supportant-php5>
- Debian, copyright file of the ezpublish 2.2.8 package: <https://sources.debian.org/src/ezpublish/2.2.8-2/debian/copyright>
- Symfony blog, "Symfony2 meets eZ Publish 5": <https://symfony.com/blog/symfony2-meets-ez-publish-5>
- CMSWire, "eZ Publish Releases Version 5.1": <https://www.cmswire.com/cms/customer-experience/ez-publish-releases-version-51-021277.php>
- Ibexa, "eZ Business Use License Agreement (eZ BUL) version 2.1": <https://www.ibexa.co/software-information/licenses-and-agreements/ez-business-use-license-agreement-ez-bul-version-2.1>
- Ibexa developer portal, product launch of Ibexa DXP v4.0 (8 February 2022): <https://developers.ibexa.co/topics/uuid/39?page=3>
- Ibexa documentation, 4.6: <https://doc.ibexa.co/en/4.6>
- GitHub: <https://github.com/ezsystems/ezpublish-legacy>, <https://github.com/netgen/ezpublish-legacy>, <https://github.com/se7enxweb/exponential>

## Related pages

- [The history chronicle, month by month](history/README.md)
- [The platform ecosystem](history/ecosystem.md)
- [Branch history](branch-overview.md)
- [Release overview](release-overview)
- [Distributions of Exponential](DISTRIBUTIONS.md)
- [Exponential Community](COMMUNITY.md): where the community meets today, and every public project website
- [Installing Exponential 6.0](INSTALL.md)
- [Glossary](glossary.md)
