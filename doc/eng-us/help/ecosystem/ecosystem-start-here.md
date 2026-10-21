---
title: "Ecosystem: start here"
audience: ecosystem, community, partner, developer
category: ecosystem
order: 1
summary: The landing page for the Exponential Ecosystem - the greater Exponential world within the PHP stack, its users and creators. A map of the products, packages, organizations, people and PHP projects around Exponential, how they relate to partners and the community, and where every page of this section is.
keywords: ecosystem, start here, Exponential world, PHP stack, products, distributions, packages, extensions, Packagist, Composer, Velocity, Layouts, Platform, Nexus, DXP, 7x, Exponential Foundation, partners, community, users, creators
featured: true
type: help
---
# Ecosystem: start here

**The greater ecosystem extends beyond the community to cover all aspects of the Exponential world within the PHP
stack: its users and creators.**

The ecosystem is everything around Exponential: the kernel and its sister products, the extensions and themes, the
servers and databases it runs on, the PHP projects it stands on and gives back to, the companies and the nonprofit
behind it, the people who build with it, and the people who use what they build. Many of them never post in a forum,
and they are part of it all the same.

This section is a map. It tells you what exists, how the parts fit together, and where to read more.

> **Note**
> Every fact in this section is taken from the repository (`README.md`, `composer.json`, `doc/`) or from another page
> of this help, and the source is named. Counts such as the number of packages were taken from the source pages
> with the date they give; they change over time.

## Contents

1. [Partners, Community and Ecosystem](#partners-community-and-ecosystem)
2. [The ecosystem in one minute](#the-ecosystem-in-one-minute)
3. [The map](#the-map)
4. [Map of the ecosystem pages](#map-of-the-ecosystem-pages)
5. [Neighboring sections](#neighboring-sections)

## Partners, Community and Ecosystem

| Circle | Who | Read |
|---|---|---|
| Partners | Exponential Partners contribute financially to the sustained future of the project, contribute code and more back to the project, and engage directly with 7x and the Exponential Foundation. | [Partners: start here](../partners/partners-start-here.md) |
| Community | Exponential Community members use, contribute to, promote and develop software using Exponential worldwide. | [Community: start here](../community/community-start-here.md) |
| Ecosystem | The greater ecosystem extends beyond the community to cover all aspects of the Exponential world within the PHP stack: its users and creators. | this section |

How the three relate: [Partners, community and ecosystem](../community/partners-community-and-ecosystem.md) and
[What the ecosystem is](what-the-ecosystem-is.md).

## The ecosystem in one minute

- **A kernel and a product family.** Exponential 6 is the PHP CMS, CMF and shop kernel. Around it 7x maintains
  Exponential Basic, the Symfony-based Exponential Platform line (Platform, Legacy, Nexus, DXP), the Velocity
  application server, Exponential Layouts, Exponential Intelligence and AdminUI
  ([Products](products/README.md)).
- **Open packages.** Every package is a public GitHub repository under se7enxweb and a Composer package; the kernel's
  `composer.json` required 66 se7enxweb packages, and the vendor had 219 packages on Packagist, both counted on
  18 October 2026 ([Packages](packages/README.md)).
- **Standing on the PHP stack.** PHP, Composer, Zeta Components, Symfony, Twig, Doctrine, Netgen's projects and more,
  with fixes given back upstream ([The PHP stack](php-stack/README.md)).
- **Two organizations and many companies.** 7x leads development; the nonprofit Exponential Foundation operates the
  project; partners, hosts and agencies build businesses on it ([Organizations](organizations/README.md)).
- **Users and creators.** Site owners, editors and visitors on one side; developers, designers, translators and
  writers on the other ([People](people/README.md)).
- **Free software throughout.** Code under the GNU GPL version 2 or later, documentation under the GNU FDL 1.3 or
  later.

## The map

```text
                         PHP, Composer, Packagist
                                   |
   Zeta Components -- Exponential 6 kernel -- Velocity (application server)
                          |        |       \
         66 bundled se7enxweb   Layouts,    admin designs
         extensions             Intelligence, AdminUI
                          |
   Exponential Platform line (Symfony): Platform 3.2.9, Legacy, Nexus, DXP
                          |
   Sites, site packages, themes, integrations, apps
                          |
   Users: owners, editors, members, visitors
```

The [ecosystem map](ecosystem-map.md) explains every box.

## Map of the ecosystem pages

| Part | What it covers |
|---|---|
| [What the ecosystem is](what-the-ecosystem-is.md) | The definition and its parts |
| [The ecosystem map](ecosystem-map.md) | How products, packages, stack, organizations and people connect |
| [A short history of the ecosystem](a-short-history-of-the-ecosystem.md) | From eZ Publish to Exponential, in the past tense |
| [Products](products/README.md) | The kernel, Velocity, Layouts, Intelligence, AdminUI, Basic, the Platform line, choosing a product |
| [Packages](packages/README.md) | Bundled extensions, Composer and Packagist, versions, site packages, themes, compatibility |
| [The PHP stack](php-stack/README.md) | PHP versions, databases, web servers and engines, operating systems, upstream projects, standards |
| [Organizations](organizations/README.md) | 7x, the Foundation, partners, companion projects, community sites |
| [People](people/README.md) | Users and creators, and how to join and publish into the ecosystem |
| [Reading path](ecosystem-reading-path.md), [Checklists](ecosystem-checklists.md), [FAQ](ecosystem-faq.md), [Glossary](ecosystem-glossary.md) | Reference |

## Neighboring sections

| Section | Read when |
|---|---|
| [Community](../community/community-start-here.md) | You want to take part |
| [Partners](../partners/partners-start-here.md) | Your company wants to work directly with 7x and the Foundation |
| [Developers: Develop with Git](../develop-with-git.md) | You build on the kernel |
| [Extension development](../extension-development/README.md) | You write and publish extensions |
| [Application creators](../creators/app-creators/app-creators-start-here.md) | You build applications and integrations |
| [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) | You want the product family in one page |

## Related pages

- [What the ecosystem is](what-the-ecosystem-is.md)
- [Ecosystem reading path](ecosystem-reading-path.md)
- [Community: start here](../community/community-start-here.md)
- [Partners: start here](../partners/partners-start-here.md)
- [The Exponential platform ecosystem (history)](../../history/ecosystem.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
