# Exponential Community

Exponential is made by its community. The **Exponential Community** is everyone who uses, contributes to, promotes
and develops software using Exponential worldwide: site owners and editors, developers and designers, agencies and
hosting partners, trainers and students, and the two organizations that hold the project together, the
**Exponential Foundation** and **7x**.

This book is the map. It says where the community meets, where to get help and how to ask for it, how to contribute
code, translations and documentation, how the project releases and decides, and which public websites belong to the
project. Every channel and site named here was checked to exist when this page was written (November 2026). Each
section links to the detailed help pages instead of repeating them.

## Contents

1. [Where to start](#1-where-to-start)
2. [Who the community is](#2-who-the-community-is)
3. [The community websites](#3-the-community-websites)
4. [Where to talk and get help](#4-where-to-talk-and-get-help)
5. [Reporting a bug well](#5-reporting-a-bug-well)
6. [Reporting a security problem](#6-reporting-a-security-problem)
7. [Contributing code](#7-contributing-code)
8. [Translations](#8-translations)
9. [Documentation](#9-documentation)
10. [Extensions, designs and packages](#10-extensions-designs-and-packages)
11. [Your role in the community](#11-your-role-in-the-community)
12. [Releases, the roadmap and decisions](#12-releases-the-roadmap-and-decisions)
13. [Recognition and copyright](#13-recognition-and-copyright)
14. [Supporting the project](#14-supporting-the-project)
15. [Exponential elsewhere on the internet](#15-exponential-elsewhere-on-the-internet)
16. [Questions](#16-questions)

## 1. Where to start

| You want to | Start here |
|---|---|
| Ask a question | The [Share Exponential forums](https://share.exponential.earth/forums), or a chat ([section 4](#4-where-to-talk-and-get-help)) |
| Read the documentation | This help center, or <https://doc.exponential.earth> |
| Try Exponential | The public demo at <https://demo.exponential.earth> |
| Report a bug | The [issue tracker](https://github.com/se7enxweb/exponential/issues), after reading [section 5](#5-reporting-a-bug-well) |
| Report a vulnerability | security@se7enx.com, never a public issue ([section 6](#6-reporting-a-security-problem)) |
| Contribute code | [CONTRIBUTING.md](../../CONTRIBUTING.md) and [section 7](#7-contributing-code) |
| Find an extension | [Exponential Projects](https://projects.exponential.earth) and [the package server](https://packages.exponential.earth) |
| Find your way as a member | [Community: start here](help/community/community-start-here.md) |

## 2. Who the community is

**Exponential Community members use, contribute to, promote and develop software using Exponential worldwide.** You
belong as soon as you do one of the four; you do not need to do all of them
([What the community is](help/community/what-the-community-is.md)).

Two organizations hold the project together ([COPYRIGHT.md](../../COPYRIGHT.md)):

- **The Exponential Foundation** (<https://foundation.exponential.earth>), the nonprofit that operates the project for
  its users and community, and the steward of the software's rights, license and history.
- **7x** (<https://se7enx.com>), known as Brookins Consulting before 2022, the company that leads the development,
  maintains the code, reviews and merges the contributions and supports customers.

Around them are the contributors, the partners and agencies that build sites with Exponential, the hosting companies,
the trainers, and the users. How they relate is described in
[Partners, community and ecosystem](help/community/partners-community-and-ecosystem.md).

The code itself is much older than the name. It began as eZ publish, written by eZ Systems AS from 1999; that product
was discontinued, and its legacy kernel was continued and renamed Exponential. The whole story is in the
[History of Exponential](HISTORY.md).

## 3. The community websites

These are the public websites of the project, run by the Exponential Foundation and 7x. Each was checked over HTTPS;
"Runs on" names the software only where the site itself says so (its generator tag or `X-Powered-By` header).

### The project

| Site | Address | What it is | Runs on | Language |
|---|---|---|---|---|
| Exponential | <https://exponential.earth> | The project's main website: the software, downloads, news, the about and community pages | Exponential | English |
| Share Exponential | <https://share.exponential.earth> | The community site: forums (<https://forums.exponential.earth>), blogs (<https://planet.exponential.earth>), articles, the roadmaps | Exponential | English |
| Exponential Projects | <https://projects.exponential.earth> | The project forge: extensions, designs and other projects, with news, downloads and source links | Exponential | English |
| eZpedia | <https://ezpedia.exponential.earth> | The community wiki, "the documentation encyclopedia" | The legacy kernel (its header still reports eZ Publish) | English |
| Exponential Foundation | <https://foundation.exponential.earth> (also <https://exponentialfoundation.earth>) | The nonprofit's website | Exponential | English |
| Exponential Support | <https://support.exponential.earth> | The official support network | Exponential | English |
| Exponential Search | <https://search.exponential.earth> | Search across the Exponential family of sites | Exponential | English |
| Project status | <https://status.exponential.earth> | Whether the project's services are up | Exponential | English |

### Documentation

| Site | Address | What it is | Runs on | Language |
|---|---|---|---|---|
| Exponential documentation | <https://doc.exponential.earth> (served at <https://exponential.doc.exponential.earth>) | The official documentation of Exponential 6 | Static pages built by Exponential | English |
| Core documentation | <https://doc.core.exponential.earth> | The core documentation of Exponential | Static pages built by Exponential | English |
| SDK documentation | <https://doc.sdk.exponential.earth> | The Exponential SDK: developer documentation, manual and API reference | Static pages built by Exponential | English |
| Platform documentation | <https://platform.doc.exponential.earth> | Documentation for the Platform distributions, built from the upstream Ibexa documentation | MkDocs | English |

### Distributions, packages and downloads

| Site | Address | What it is | Runs on | Language |
|---|---|---|---|---|
| Exponential Platform | <https://platform.exponential.earth> | The website of the Platform distributions ([Distributions of Exponential](DISTRIBUTIONS.md)) | Exponential | English |
| Exponential Basic | <https://basic.exponential.earth> | The website of Exponential Basic | Exponential | English |
| Extension packages | <https://packages.exponential.earth> | The package server: extension and site packages for the setup wizard | Exponential | English |
| Package directory | <https://exponential.packages.exponential.earth> | A plain file index of the packages | Static file index | - |
| Download mirror | <https://mirror.exponential.se7enx.com> | A plain file index of older downloads and books | Static file index | - |

### Demos

| Site | Address | What it is | Runs on | Language |
|---|---|---|---|---|
| Exponential demo | <https://demo.exponential.earth> (administration: <https://edit.demo.exponential.earth>) | The public demo of the latest Exponential 6 ("Fit & Healthy"), reset regularly; the sign-in details are on the [community page](https://exponential.earth/about/community) | Exponential | English |
| Latest demo | <https://latest.demo.exponential.earth> | The same demo, rebuilt from the newest code | Exponential | English |
| 7x demo | <https://demo.se7enx.com> | The same demo on 7x's domain | Exponential | English |
| MongoDB demo | <https://mongodb.demo.exponential.earth> | Exponential running on the MongoDB database driver | Exponential | English |
| CJW demo | <https://site.cjw.demo.exponential.earth> | A demo with Exponential Layouts, by the partner JAC Systeme | Exponential | German |

### 7x

| Site | Address | What it is | Runs on | Language |
|---|---|---|---|---|
| 7x | <https://se7enx.com> | The company: hosting, design, development and support | Exponential | English |
| 7x Software | <https://software.se7enx.com> | 7x's software downloads for Exponential | Exponential | English |
| Sponsor | <https://sponsor.se7enx.com> | Sponsoring the development of Exponential | Exponential | English |
| 7x Blog | <https://blog.se7enx.com> | 7x's blog, "The Continuum" | Exponential | English |
| The Exponential Blog | <https://graham.se7enx.com> | The blog of Exponential's lead developer about maintaining Exponential | Exponential | English |
| 7x status | <https://status.se7enx.com> | 7x's network status | Exponential | English |

Some of these sites answer at further addresses as well (for example the older `*.ezpublish.one` names lead to the
same sites); the addresses above are the ones to use. Short links such as <https://issues.exponential.earth> and
<https://github.exponential.earth> lead to GitHub.

## 4. Where to talk and get help

All of these are free. [Community channels](help/project/community-channels.md) describes each one in detail, and
[Getting support](help/project/getting-support.md) says which to try first. The guide [Getting Support](SUPPORT.md)
also shows how to ask the communities of PHP, the database and the web server, and when to hire 7x.

| Channel | What it is for | Address |
|---|---|---|
| **Share Exponential forums** | Questions, answers and discussions that others should find later | <https://share.exponential.earth/forums> |
| **Telegram** | Real-time chat; members of the development team are there | <https://t.me/exponentialcms> |
| **Discord** | Community chat, development talk, feedback | <https://discord.gg/zXzGMgfzej> |
| **Slack** | Chat, especially about development | <https://exponentialcms.slack.com/> |
| **GitHub issues** | Errors and feature requests for the software | <https://github.com/se7enxweb/exponential/issues> |
| **GitHub discussions** | Open questions about the kernel, and about the project | <https://github.com/se7enxweb/exponential/discussions>, <https://github.com/se7enxweb/exponential-community/discussions> |
| **Project issues** | The websites, the content, the community: everything that is not an error in software | <https://github.com/se7enxweb/exponential-community/issues> |

For a question that needs a long answer, prefer the forums: a chat scrolls away, a forum thread stays searchable.
Nobody promises an answer time in the free channels; paid support from 7x exists for when you cannot wait
([Getting support](help/project/getting-support.md)).

How to ask so you get an answer: [Asking a good question](help/community/joining/asking-a-good-question.md).

## 5. Reporting a bug well

1. **Check that it is a bug.** Search the issue tracker and the forums first. Some behavior is deliberate, and other
   installations depend on it (CONTRIBUTING.md calls this a *negative feature*; see
   [Getting support: negative features](help/project/getting-support.md#negative-features)).
2. **Use the right tracker.** Software errors go to
   [se7enxweb/exponential](https://github.com/se7enxweb/exponential/issues) (or to the repository of the extension);
   problems with the websites or the community go to
   [exponential-community](https://github.com/se7enxweb/exponential-community/issues).
3. **Say what you run**: the Exponential version (`./console --version`), the PHP version, the database, and the
   engine that serves the site (Velocity, Apache or nginx with PHP-FPM).
4. **Give the steps**: what you did, what you expected, what happened instead, and the error reference shown on the
   error page ([Finding an error by its reference](guides/finding-an-error-by-its-reference.md)).
5. **Leave out** passwords, secrets and personal data.

[Filing a bug](help/community/contributing/filing-a-bug.md) has a template and examples.

## 6. Reporting a security problem

**Never report a vulnerability in a public issue, discussion, forum or chat.** Send it privately to
**security@se7enx.com**, as the [Security Policy](../../SECURITY.md) says: with the version, the PHP version, the engine
and database, what an attacker can do and how to reproduce it. You can expect a first answer within one business day.
An accepted issue is fixed on `main` and released with the next 6.0.x version.

## 7. Contributing code

The rules are in [CONTRIBUTING.md](../../CONTRIBUTING.md); in short:

- **Fork, branch, pull request.** One focused change per pull request, with tests that fail without it.
- **Commit messages** start with what the change does: `Added:`, `Updated:`, `Fixed:`, `Removed:` or `Renamed:`. The
  `commit-msg` hook (`make hooks`) and CI check it ([the quality checks](features/6.0/quality-checks.md)).
- **Coding standards**: PSR and [the project's coding standards](standards/coding-standards.md); new kernel code uses
  the `exp` prefix; administration templates follow [Styling the administration](bc/6.0/admin4-styling.md).
- **Keep the manifest current** (`make manifest-fix`) and run the tests before you push.
- **The CLA.** A contribution to the core kernel is merged once its author's signed Contributor Licensing Agreement
  is on file; you sign once. Your own extensions and sites are not affected
  ([CONTRIBUTING.md, Copyright assignment](../../CONTRIBUTING.md#copyright-assignment-cla),
  [The CLA explained](help/community/contributing/the-cla-explained.md)).
- **Review.** Expect several review rounds, and squash and rebase your pull request onto `main` while it is open.
  The maintainers merge the contributions they receive: a pull request is reviewed, extended where it needs more (tests,
  documentation, the other designs), and merged; it is not turned away.

Your first contribution, step by step: [Your first contribution](help/community/contributing/your-first-contribution.md)
and [Contributing code](help/community/contributing/contributing-code.md).

## 8. Translations

The administration is translated into **156 languages** (see [Languages](languages.md) for how complete each one is),
and the help center is written in English with a complete German translation. Ways to help:

- improve the interface translation of your language (`share/translations/<locale>/translation.ts`);
- review machine-assisted translations and fix what reads wrong;
- translate help pages: they are free documentation under the GNU FDL
  ([Free documentation](help/free-documentation.md)).

[Contributing translations](help/community/contributing/contributing-translations.md) explains where the files live and
how to work with other speakers of your language.

## 9. Documentation

The help center is built from Markdown files in the repository (`doc/eng-us/`, `doc/ger-de/`), so documentation is
contributed like code: a pull request. [Contributing documentation and help pages](help/community/contributing/contributing-documentation-and-help-pages.md)
explains the conventions.

If you write an extension, document it where the help center finds it: `README.md` and `INSTALL.md` at the top of
`extension/<name>/`, every other page as Markdown (`.md`) in `extension/<name>/doc/`, in English
([Documenting an extension](guides/documenting-an-extension.md)).

## 10. Extensions, designs and packages

- **Share what you build.** An extension you publish yourself is yours, under the GPL; giving it back makes it usable
  by everyone ([Giving extensions back](help/contribute-extensions-back.md),
  [Sharing extensions and themes](help/community/contributing/sharing-extensions-and-themes.md)).
- **Where extensions live**: in their own GitHub repositories, installed with Composer from
  [Packagist](https://packagist.org/packages/se7enxweb/), listed on [Exponential Projects](https://projects.exponential.earth).
- **Packages for the setup wizard** come from the [package server](https://packages.exponential.earth); their sources
  are the `-ezpackage` repositories of the se7enxweb organization ([Distributions of Exponential](DISTRIBUTIONS.md)).
- **Design and accessibility**: [Designers: start here](help/designers/designers-start-here.md).

## 11. Your role in the community

The help center has a start page for many roles. A few of them:

| You are | Start here |
|---|---|
| A community member | [Community: start here](help/community/community-start-here.md) |
| An editor or site owner | [Getting started](help/getting-started.md), [Editing content](help/editing-content.md) |
| A PHP developer | [PHP developers: start here](help/php-developers/php-developers-start-here.md) |
| An integrator | [Integrators: start here](help/integrators/integrators-start-here.md), [Integrating With Exponential 6.0](INTEGRATING.md) |
| A designer | [Designers: start here](help/designers/designers-start-here.md) |
| An agency or partner | [Partners: start here](help/partners/partners-start-here.md) |
| A hosting company | [Hosting companies: start here](help/hosting/hosting-companies-start-here.md) |
| A trainer | [Trainers: start here](help/trainers/trainers-start-here.md) |
| A student | [Students: start here](help/students/students-start-here.md) |
| A supporter | [Supporters: start here](help/supporters/supporters-start-here.md) |
| An advocate | [Advocates: start here](help/advocates/advocates-start-here.md) |
| A sponsor | [Sponsors: start here](help/sponsors/sponsors-start-here.md) |

Helping others is a contribution too: answering questions, mentoring, welcoming newcomers
([Helping others](help/community/helping-others/README.md)).

## 12. Releases, the roadmap and decisions

- **Releases.** Exponential 6.0 is the maintained line; fixes are made on `main` and published with the next 6.0.x
  release. **A published tag is never changed**: a fix reaches you as the next release
  ([Security Policy](../../SECURITY.md), [Updating Exponential 6.0](UPDATE.md)). What changed in each release is in the
  changelogs under `doc/changelogs/` and in the [branch history](branch-overview.md).
- **The roadmap** lives on Share Exponential: the official roadmap at <https://share.exponential.earth/roadmap> and
  the community roadmap of feature requests at <https://share.exponential.earth/community-roadmap>
  ([The roadmap](help/project/roadmap.md)).
- **Decisions.** 7x leads the development and reviews and merges the contributions; the Exponential Foundation
  operates the project and decides on Code of Conduct matters ([COPYRIGHT.md](../../COPYRIGHT.md),
  [CONTRIBUTING.md](../../CONTRIBUTING.md#code-of-conduct)). How the community takes part:
  [How it works](help/community/how-it-works/README.md).

## 13. Recognition and copyright

- **Credit** goes to contributors in the changelogs, in the commit authorship, and on the recognition pages.
- **Copyright.** The copyright of Exponential is held by the Exponential Foundation and 7x together, and the notice of
  the original developer, eZ Systems AS, stays in every file that contains its code ([COPYRIGHT.md](../../COPYRIGHT.md),
  [Copyright and copyleft](help/copyright-and-copyleft.md)). Credit is not a copyright claim.
- **The license** is the GNU GPL, version 2 or later; the documentation is free under the GNU FDL
  ([README-LICENSE](README-LICENSE.md)).

## 14. Supporting the project

You can support the project without writing code: become a partner, donate, take a sustaining support subscription,
or sponsor new development ([Give and donate](help/project/give-and-donate.md),
<https://sponsor.se7enx.com>). Telling others about Exponential counts as well
([Advocates: start here](help/advocates/advocates-start-here.md)).

## 15. Exponential elsewhere on the internet

### Social media

These accounts are the ones the footers and community pages of exponential.earth, se7enx.com, Share Exponential,
Exponential Projects and the documentation link to, and the GitHub profile of the se7enxweb organization names. Follow
them for news; ask questions in the channels of [section 4](#4-where-to-talk-and-get-help).

| Platform | Exponential | 7x |
|---|---|---|
| LinkedIn | [Exponential group](https://www.linkedin.com/groups/14819081/) | [7x company page](https://www.linkedin.com/company/7x/) |
| X | [@ExponentialCMS](https://x.com/ExponentialCMS) | [@se7enxweb](https://x.com/se7enxweb) |
| Bluesky | [exponentialcms.bsky.social](https://bsky.app/profile/exponentialcms.bsky.social) | [7xweb.bsky.social](https://bsky.app/profile/7xweb.bsky.social) |
| Instagram | [exponentialcms](https://www.instagram.com/exponentialcms), [exponentialfoundation](https://www.instagram.com/exponentialfoundation) | [se7enxweb](https://www.instagram.com/se7enxweb/) |
| YouTube | [ExponentialCMSInsights](https://www.youtube.com/@ExponentialCMSInsights) | [7xwebsitehosting](https://www.youtube.com/@7xwebsitehosting) |
| Facebook | | [7x](https://www.facebook.com/people/7x/61559920663779/) |
| Mastodon | | [@7x@mastodon.cloud](https://mastodon.cloud/@7x) |
| GitLab | | [se7enx](https://gitlab.com/se7enx) |
| GitHub | [se7enxweb/exponential](https://github.com/se7enxweb/exponential) | [se7enxweb](https://github.com/se7enxweb) |
| Chat | [Telegram](https://t.me/exponentialcms), [Discord](https://discord.gg/zXzGMgfzej), [Slack](https://exponentialcms.slack.com/) | |

### Sites by 7x that feature Exponential

7x also runs public sites of its own that are built with the legacy kernel or Exponential and write about it in their
content. They are not part of the project's documentation or support, but they are places where Exponential appears
in public.

| Site | Address | What it is, and how it features Exponential |
|---|---|---|
| SlashNews | <https://slashnews.earth> | A news and networking site for tech professionals by 7x; it has an "Exponential" topic with posts about Exponential releases, and it runs on Exponential |
| Hacker News Delta | <https://hackernewsdelta.com> | A social news site for tech professionals; its footer says it is powered by Exponential and hosted by 7x |
| Digg One | <https://digg.one> | A social news site for everyone; it has an `ezpublish` category, and runs on the legacy kernel (its header reports eZ Publish) |
| kracker.org | <https://kracker.org> | A personal hub of activity and projects; its menu has a "Try Exponential!" section with "What was eZ Publish?" and links to Share Exponential and the project. The multi-site view count statistics of Exponential 6.0.15 came from work on this site |

## 16. Questions

| Question | Answer |
|---|---|
| Do I have to sign something to ask a question or report a bug? | No. The CLA concerns code you submit to the kernel ([section 7](#7-contributing-code)). |
| Where do I ask in my own language? | The forums and chats are in English; local groups are described in [Local groups](help/community/local-groups/README.md). |
| Is there a code of conduct? | Yes: the Contributor Covenant 2.1 with Exponential additions ([CODE_OF_CONDUCT.md](../../CODE_OF_CONDUCT.md)). Report a problem to community@se7enx.com. |
| A community site does not answer. | Look at <https://status.exponential.earth> first. |
| Can I run a site for my user group? | Yes: [A free site for your user group](help/community/a-free-site-for-your-user-group.md). |
| Where are the community's own questions answered? | [Community FAQ](help/community/community-faq.md). |

## Related pages

- [History of Exponential (1999 - 2026)](HISTORY.md)
- [Distributions of Exponential](DISTRIBUTIONS.md)
- [Community channels](help/project/community-channels.md)
- [Getting support](help/project/getting-support.md)
- [Getting Support](SUPPORT.md): from helping yourself to the PHP ecosystem, the Foundation and 7x
- [Contribute to the project](help/project/contribute-to-the-project.md)
- [CONTRIBUTING.md](../../CONTRIBUTING.md), [CODE_OF_CONDUCT.md](../../CODE_OF_CONDUCT.md), [SECURITY.md](../../SECURITY.md)
