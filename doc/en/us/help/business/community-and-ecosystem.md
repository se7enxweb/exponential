---
title: The community and ecosystem as a selling point
audience: sales-marketing
category: business
order: 80
summary: How to present the Exponential community, the two organizations behind it and the open package ecosystem to a buyer, using only facts the documentation can prove, with talk tracks, a proof table and questions to ask.
keywords: community, ecosystem, open source, 7x, Exponential Foundation, forums, share, Telegram, eZpedia, GitHub, Packagist, extensions, sponsors, longevity, vendor lock-in, selling point
type: help
---
# The community and ecosystem as a selling point

This page is for people in sales and marketing who present Exponential to a buyer. It answers one question: what
can you truthfully say about the people, organizations and packages around Exponential, and how do you turn that
into a reason to choose it? Every statement below comes from a page of this help or a file of the repository, and
each one names its source, so you can show the proof to the buyer instead of asking them to trust you.

> **Note**
> The two background pages behind this one are [Who is 7x?](../who-is-7x.md) and
> [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md). Read both once before your first
> buyer meeting; this page tells you how to use them.

## Contents

1. [Why the ecosystem matters to a buyer](#why-the-ecosystem-matters-to-a-buyer)
2. [The three pillars in one table](#the-three-pillars-in-one-table)
3. [Pillar 1: two organizations, one project](#pillar-1-two-organizations-one-project)
4. [Pillar 2: open places to get help](#pillar-2-open-places-to-get-help)
5. [Pillar 3: an open package ecosystem](#pillar-3-an-open-package-ecosystem)
6. [Proof points you can show on screen](#proof-points-you-can-show-on-screen)
7. [Talk tracks](#talk-tracks)
8. [What you must not claim](#what-you-must-not-claim)
9. [Questions to ask the buyer](#questions-to-ask-the-buyer)
10. [A one-page ecosystem sheet to copy](#a-one-page-ecosystem-sheet-to-copy)

## Why the ecosystem matters to a buyer

A buyer of a content management system is not only buying software. They buy the assurance that the software will
still be maintained in five years, that they can find help when they are stuck, that they can hire people or
companies to work on it, and that they are not locked to one supplier. The ecosystem answers each of these worries
with something the buyer can check for themselves.

| Buyer worry | What the ecosystem offers | Where the proof is |
|---|---|---|
| "Will it still exist in five years?" | A code base with history back to 2002, kept alive after its original vendor's end of life in 2021, a maintained 6.0 line with releases since 1 January 2024 | [Who is 7x?](../who-is-7x.md#nearly-thirty-years-the-timeline), [The history of Exponential](../../history/README.md) |
| "Who is responsible for it?" | A company (7x) that leads development, and a nonprofit (the Exponential Foundation) that operates and funds the project | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-foundation-and-7x) |
| "Where do we get help?" | Public forums, a community chat, an encyclopedia, documentation and public issue trackers | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#community-places) |
| "Can we extend it without the vendor?" | Every package is a public GitHub repository and a Composer package; the code is GPL v2 or later | [Licensing explained for buyers](licensing-for-buyers.md) |
| "Are we locked in?" | Self-hosting on many operating systems and databases, full Git history, no license fees | [Database support](../database-support.md), [Operating system support](../os-support.md), [Develop with Git](../develop-with-git.md) |

## The three pillars in one table

Use this table as the backbone of any ecosystem slide or paragraph.

| Pillar | One sentence you can say | Source |
|---|---|---|
| Organizations | "Exponential is led by 7x, a North American company, and operated by the Exponential Foundation, a nonprofit founded in 2026." | [Who is 7x?](../who-is-7x.md#in-short), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#in-short) |
| Community | "Help is public: forums, a Telegram chat, eZpedia, documentation and public issue trackers." | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#community-places) |
| Packages | "The kernel requires 66 se7enxweb packages, and the se7enxweb vendor has 219 packages on Packagist, each with its own public repository." | [Who is 7x?](../who-is-7x.md#7x-in-numbers) |

> **Warning**
> Numbers such as 66 packages, 219 Packagist packages or 27,242 commits were counted on 18 October 2026 and change
> over time. When you use one, say the date, or recount it with the command the source page names.

## Pillar 1: two organizations, one project

The buyer should understand that Exponential has both a commercial backer and a nonprofit home. That is a strong
answer to "what if the company disappears?" and to "is this just one vendor's product?".

| | 7x | Exponential Foundation |
|---|---|---|
| What it is | A company: "a responsive North American web design, support, development and website hosting company" | A nonprofit corporation founded in Seattle, Washington, on 19 March 2026 |
| What it does for the project | Leads development, support, hosting and services; named by the Foundation as "the current leader in contributions" | Operates the project; its mission covers "Funding, Adoption, Support, Direction, Education, Development, Promotion and Documentation" |
| What it means for the buyer | There is a company that can be hired for a project, migration, support or hosting | The project does not depend on one company's business plan alone |

Source for every cell: [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-foundation-and-7x).

What the sources do **not** say, and what you must therefore not say: who sits on the Foundation's board, how it is
governed and what its budget is ([What the sources do not say](../who-is-the-exponential-foundation.md#what-the-sources-do-not-say)).
If a buyer asks, say that the public sources do not answer it and offer to ask the Foundation through its website.

### Longevity in one paragraph

You may say, with sources:

- The copyright notice runs from 1998 to 2026 for 7x and the Exponential Foundation, next to 1999 to 2014 for
  eZ Systems AS (`COPYRIGHT.md`).
- Exponential continues the code base of eZ Publish, a product its creator discontinued after almost 18 years
  ([Who is 7x?](../who-is-7x.md#the-ez-publish-years-1999-to-2021)).
- 7x took over leadership in 2023 and has published the 6.0.x releases since 6.0.0 on 1 January 2024
  ([Who is 7x?](../who-is-7x.md#the-60x-releases)).
- Security fixes are made on `main` and published with the next 6.0.x release (`SECURITY.md`).

## Pillar 2: open places to get help

All community places are public. A buyer's developers can look at them before signing anything.

| Place | What it is | Address (as text) |
|---|---|---|
| Share | The community portal: forums, blogs, tutorials and projects (extensions) | [share.exponential.earth](https://share.exponential.earth/) |
| Forums | Questions and discussions, including a forum for Exponential Platform | [share.exponential.earth/forums](https://share.exponential.earth/forums) |
| Telegram | "Share Exponential! Telegram Community Support Chat" | `t.me/exponentialcms` |
| eZpedia | "The free eZ Publish encyclopedia that anyone can edit", since 2006, under the GNU Free Documentation License | [ezpedia.exponential.earth](https://ezpedia.exponential.earth/) |
| Documentation | "The Official Documentation For Exponential 6 CMS" | [doc.exponential.earth](https://doc.exponential.earth/) |
| Training books | Developer training books from the eZ Publish years, compatible with Exponential, free to everyone | [mirror.exponential.se7enx.com repository](https://github.com/se7enxweb/mirror.exponential.se7enx.com) |
| Software issue tracker | Bugs and improvements of the software | [se7enxweb/exponential issues](https://github.com/se7enxweb/exponential/issues) |
| Community issue tracker | Anything about the project, community, ecosystem or websites | [se7enxweb/exponential-community issues](https://github.com/se7enxweb/exponential-community/issues) |
| Discord and Slack | Named among the support channels on the project website | named on `exponential.earth/about/project` |

Sources: [Who is 7x?](../who-is-7x.md#the-community), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#community-places),
[When something goes wrong](../when-something-goes-wrong.md#asking-for-help).

> **Note**
> No conferences or meetups are documented in the sources ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#community-places)).
> Do not promise events. If your organization runs one, it is your event, not the project's, until the project
> announces it.

### The help inside the product

The help you are reading is part of the ecosystem argument. It ships with the software, it is searchable and
filtered by audience, every page can be printed or downloaded as a PDF, and it is free documentation under the
GNU FDL 1.3 ([The help system](../../features/6.0/help-system.md), [Free documentation](../free-documentation.md)).
A buyer's team can read it before they buy, translate it and improve it.

## Pillar 3: an open package ecosystem

| Fact | Source |
|---|---|
| The kernel is the Composer package `se7enxweb/exponential`, license `GPL-2.0-or-later` | `composer.json` |
| `composer.json` requires 66 `se7enxweb/*` packages, many inactive until switched on | [Who is 7x?](../who-is-7x.md#the-extension-ecosystem), [The default extension distribution](../../features/6.0/default-extension-distribution.md) |
| Each extension has its own repository under `github.com/se7enxweb`, its own releases and changelog | [Who is 7x?](../who-is-7x.md#the-extension-ecosystem) |
| The product family: Exponential 6, Exponential Basic, Exponential Platform 3.2.9, Exponential Platform Legacy, Exponential Platform Nexus, Exponential Platform DXP, Exponential Velocity, Exponential Intelligence, Exponential Layouts, AdminUI and the help system | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-product-family-at-a-glance) |
| Work of other open source companies is built in and credited: Netgen (layouts concept), Mugo Web (LoveStack features), CJW Network (newsletter) | [Who is 7x?](../who-is-7x.md#contributors-and-friends-of-the-project) |
| Every pull request is merged, under its author's name; while it is open, the author squashes and rebases it onto `main` as the reviews ask | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#contributors-and-pull-requests) |
| The project gives back upstream, for example security pull requests to Zeta Components | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-php-stack-it-stands-on-and-gives-back-to) |

How to phrase it for a buyer: "Nothing you need is hidden behind a commercial edition. The kernel, the extensions
and the documentation are public, and you can install every one of them with Composer or Git." The README supports
the "no edition that holds features back" point for Exponential Platform DXP ("with no license fees and no edition
that holds features back"); for Exponential 6 the README says "Using Exponential under the terms of the GNU GPL is
free of charge. The Professional License is no longer available."

## Proof points you can show on screen

A claim the buyer can see is worth more than a claim on a slide. Plan five minutes of your meeting for these.

| Proof | What to open | What the buyer sees |
|---|---|---|
| The history is public | The help page [The history of Exponential](../../history/README.md) | Every change since December 2023, month by month |
| The code is public | [github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential) | The repository, its issues and pull requests |
| The packages are public | [packagist.org/packages/se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential) | The kernel package and its versions |
| Security has a process | `SECURITY.md` in the repository | Private reporting, the supported line, "a published tag is never changed" |
| The help is in the product | `/exphelp/dashboard` in a demo installation you control | Search, audiences, PDF download |
| The community talks | [share.exponential.earth/forums](https://share.exponential.earth/forums) | Public questions and answers |

See [Demo scripts](demo-scripts.md) for how to fit these into a 5, 15 or 45 minute demo.

## Talk tracks

### 30 seconds

"Exponential is open source under the GPL. It is led by 7x, a North American company, and operated by the
Exponential Foundation, a nonprofit. Its code base goes back to 1999, and everything, the code, the extensions and
the documentation, is public on GitHub and Packagist. You can see the project's health yourself before you decide."

### 2 minutes

1. **Who stands behind it**: a company and a nonprofit (Pillar 1).
2. **How long it has lived**: copyright since 1998/1999, history since 2002, releases since 2024 (Longevity).
3. **Where help is**: forums, chat, encyclopedia, documentation, issue trackers (Pillar 2).
4. **What you can extend**: dozens of public extensions, each a Composer package (Pillar 3).
5. **What you keep**: the license lets you change and self-host it; no license fees
   ([Licensing explained for buyers](licensing-for-buyers.md)).

### Answering "the community looks small"

Be honest. Do not invent member counts or activity figures; the sources do not state them. Say what you can show:
the public trackers, the forums, the history pages, the number of packages, and the fact that every pull request is
merged. Then turn it into an invitation: a buyer who adopts Exponential can shape it, because contributions are
accepted under a clear license and process ([Growing the community](growing-the-community.md)). For more answers
of this kind see [Handling objections](objection-handling.md).

## What you must not claim

| Do not say | Why | Say instead |
|---|---|---|
| A number of users, installations or customers | No source states one | "The sources do not publish installation counts; here is what is public: ..." |
| "Thousands of developers" or any community size | Not documented | Name the places and let the buyer look |
| Response times for the community | Only `SECURITY.md` names one: "a first answer within one business day" for security reports | Quote that sentence for security reports only |
| An SLA | No SLA is documented in the project; commercial terms are 7x's, "Prices and terms are on the 7x website, not here" | "Support contracts are offered by 7x; terms are on its website" |
| Events, certifications, partner programs | Not documented | Leave them out |
| That the project is associated with eZ Systems or Ibexa | The README says the opposite | "This project is not associated with the original eZ Publish software or its original developer, eZ Systems or Ibexa" |

## Questions to ask the buyer

Use these in discovery. The answers tell you which part of the ecosystem story matters most to them.

1. Who will maintain the site after launch: an in-house team, an agency, or 7x?
2. Does your organization require a supplier with a nonprofit or public governance element?
3. Do your developers already work with Git, Composer and GitHub?
4. Will you want to contribute fixes back, or only consume releases?
5. Which languages does your team need help in? (The help and the administration translations are listed on
   [Languages](../../languages.md).)
6. Do you need commercial support or hosting? (Point to 7x's services, without quoting prices.)

## A one-page ecosystem sheet to copy

```text
Exponential: the ecosystem in one page                  (facts as of <date>)

WHO STANDS BEHIND IT
- 7x: the company that leads development, support and hosting.
- Exponential Foundation: the nonprofit that operates and funds the project
  (founded 19 March 2026, Seattle, Washington).

HOW LONG IT HAS LIVED
- Code base since 1999; Git history since 2002.
- Exponential 6.0.x releases since 1 January 2024; newest: <version, date>.

WHERE HELP IS
- Forums and projects: share.exponential.earth
- Chat: t.me/exponentialcms
- Encyclopedia: ezpedia.exponential.earth
- Documentation: doc.exponential.earth and the help inside the product
- Issues: github.com/se7enxweb/exponential/issues

WHAT YOU CAN EXTEND
- <number> se7enxweb packages required by the kernel, each a public repository.
- Install with Composer or Git.

LICENSES
- Code: GNU GPL v2 or later.  Documentation: GNU FDL 1.3 or later.

PREPARED FOR: <buyer>     BY: <your name, organization>
```

Fill in the placeholders from the current sources before you send it.

## Related pages

- [Sales and marketing: start here](sales-and-marketing-start-here.md)
- [Positioning and value proposition](positioning-and-value-proposition.md)
- [Handling objections: security, longevity and support](objection-handling.md)
- [Growing the community](growing-the-community.md)
- [Licensing explained for buyers](licensing-for-buyers.md)
- [Working with the community and contributing back](../leadership/working-with-the-community.md)
- [Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
