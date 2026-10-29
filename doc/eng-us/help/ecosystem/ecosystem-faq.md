---
title: Ecosystem FAQ
audience: ecosystem, community, partner, developer, businesses
category: ecosystem
order: 6
summary: Short answers to common questions about the Exponential ecosystem - what belongs to it, the products and distributions, packages and versions, PHP and databases, the organizations, and how to take part.
keywords: FAQ, ecosystem, questions, products, distributions, DXP, Nexus, packages, Packagist, PHP versions, databases, 7x, Foundation, partners
type: help
---
# Ecosystem FAQ

## Contents

1. [The ecosystem](#the-ecosystem)
2. [Products](#products)
3. [Packages](#packages)
4. [Stack](#stack)
5. [Organizations and people](#organizations-and-people)

## The ecosystem

**What is the Exponential Ecosystem?**
"The greater ecosystem extends beyond the community to cover all aspects of the Exponential world within the PHP
stack: its users and creators" ([What the ecosystem is](what-the-ecosystem-is.md)).

**How is it different from the community?**
The community is people who take part; the ecosystem also includes products, packages, the stack, organizations, and
users who never take part ([Partners, community and ecosystem](../community/partners-community-and-ecosystem.md)).

## Products

**Which products are there?**
Exponential 6, Velocity, Layouts, Intelligence, AdminUI, Exponential Basic and the Platform line: Platform 3.2.9,
Legacy, Nexus and DXP ([Products](products/README.md)).

**Does the DXP include the Exponential 6 kernel or a layouts editor?**
No. It ships no layouts editor, approval workflow, scheduler or legacy kernel; Legacy and Nexus include the kernel
([Exponential Platform DXP](products/platform-dxp.md)).

**Is Velocity required?**
`composer.json` requires it since 6.0.15, so it is installed; you may still serve the site with Apache, Nginx or
another server ([Web servers and engines](php-stack/web-servers-and-engines.md)).

## Packages

**How many packages are there?**
On 18 October 2026 the kernel required 66 se7enxweb packages and the vendor had 219 packages on Packagist; recount
before you quote a number ([Composer and Packagist](packages/composer-and-packagist.md)).

**Can a published version change?**
No. "A published tag is never changed"; a fix comes as the next version ([How packages are versioned](packages/how-packages-are-versioned.md)).

**Where do I find an extension?**
See [Finding an extension](packages/finding-an-extension.md).

## Stack

**Which PHP versions?**
PHP 7.4 to 8.5 for Exponential 6.0.15 ([PHP versions](php-stack/php-versions.md)).

**Which databases?**
SQLite 3 (default), PostgreSQL, MongoDB, MariaDB, Oracle and MySQL, in that order of preference
([Databases](php-stack/databases.md)).

**Which operating systems?**
Almost any that runs PHP 7.4 or later, in four tiers ([Operating systems](php-stack/operating-systems.md)).

## Organizations and people

**Who leads Exponential?**
7x leads development; the Exponential Foundation operates the project ([Organizations](organizations/README.md)).

**Is Exponential associated with the original vendor?**
No: "This project is not associated with the original eZ Publish software or its original developer, eZ Systems or
Ibexa" (`README.md`).

**How do I take part?**
Start with [Community: start here](../community/community-start-here.md); companies, with
[Partners: start here](../partners/partners-start-here.md).

## Related pages

- [Ecosystem: start here](ecosystem-start-here.md)
- [Ecosystem glossary](ecosystem-glossary.md)
- [Community FAQ](../community/community-faq.md)
- [Project FAQ](../project/faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
