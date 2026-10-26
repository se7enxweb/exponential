---
title: Businesses FAQ
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 90
summary: Short answers, each with its source, to the questions owners and managers ask before they run their business on Exponential: cost, license, hosting, support, stores, apps, languages, data protection, migration and what happens if a partner leaves.
keywords: FAQ, questions, answers, cost, license fee, GPL, hosting, support, 7x, community, store, payments, app, REST, languages, GDPR, migration, eZ Publish, partner, lock-in, backups, upgrades, small business
type: help
---
# Businesses FAQ

Short answers to the questions owners and managers ask most. Each answer names its source and links to the page with
the details.

## Contents

1. [Cost and license](#cost-and-license)
2. [Running it](#running-it)
3. [Selling and serving customers](#selling-and-serving-customers)
4. [Data, security and compliance](#data-security-and-compliance)
5. [People and support](#people-and-support)
6. [Moving to Exponential](#moving-to-exponential)

## Cost and license

**Does Exponential cost a license fee?**
No. It is free software under the GNU General Public License, version 2 or later
([Licensing for buyers](../business/licensing-for-buyers.md)). You pay for hosting, for the work of building and
maintaining your site, and for any support contract you choose, not for the software.
See [What you do not pay for](costs/what-you-do-not-pay-for.md).

**Then what does it cost to run?**
Hosting, people's time for updates, backups and content, and the work on custom features. The pages
[Total cost of ownership](costs/total-cost-of-ownership.md) and [Hosting cost drivers](costs/hosting-cost-drivers.md)
list every item; the worked examples use example assumptions you replace with your own.

**Can I use it for a commercial site?**
Running a website is not distributing the software: section 0 of the license says "The act of running the Program is
not restricted". The conditions of the GPL apply when someone distributes copies, for example a product built on
Exponential sold to your own customers. Details, and the questions to settle with a legal adviser, are in
[Licensing for buyers](../business/licensing-for-buyers.md#running-a-website-is-not-distributing).

## Running it

**What does a server need?**
PHP 8.0 or later on almost any operating system; a web server (Apache, Nginx and the others, or the bundled
Velocity); and a database, where SQLite 3, the default, needs no database server (`README.md`, "Requirements";
[Requirements at a glance](../hosting/basics/requirements-at-a-glance.md)).

**Can it run on shared hosting?**
Usually yes with Apache or LiteSpeed and `.htaccess`. Velocity needs a long-lived process, which shared hosting
"usually will not" allow (`README.md`, "When a traditional server is still the right answer").
See [Hosting models compared](choosing/hosting-models-compared.md).

**How fast is it?**
The documented figures for the stack are 0.36 ms per cached page and 8,941 requests per second sustained on a 12-core
machine, with their methodology (`README.md`, "What that measures as"). Your own figures depend on your hardware and
pages. See [Velocity for businesses](reliability/velocity-for-businesses.md).

**What is the uptime?**
The project does not publish an uptime figure and this help does not invent one. Availability depends on the hosting,
the monitoring and the way the site is run. See [Uptime practice](reliability/uptime-practice.md) and
[Contracts and SLA basics](choosing/contracts-and-slas-basics.md).

**How are updates delivered?**
As new 6.0.x releases; "A published tag is never changed" (`SECURITY.md`). The administration checks an installation
against the release before an upgrade ([Upgrade check](../../guides/upgrade-check.md)).
See [Upgrades without surprises](reliability/upgrades-without-surprises.md).

## Selling and serving customers

**Is there a built-in shop?**
Yes: "Advanced e-Commerce Stores (Built in)" (`README.md`), with products, basket, orders, VAT and currencies, and
payment gateways such as PayPal through `ezpaypal`. See [Online stores](stores/README.md).

**Can we have a mobile app?**
Yes, through the REST API ("A headless CRUD REST API", `README.md`), which checks the same permissions as the website
([REST API v1 and v2](../../guides/rest-api-v1-and-v2.md)). Many businesses start with a responsive site.
See [Mobile and headless](mobile-and-headless/README.md).

**How many languages?**
268 locale files can be chosen as content languages and siteaccess locales, 40 of them written right to left; the
administration has 54 translations, 51 complete (25 October 2026) ([Languages in Exponential](../../languages.md)).
See [Multilingual businesses](multilingual/README.md).

**Can we run several brands from one installation?**
Yes, "Multi-site support" is built in (`README.md`). See [Multi-site brands](multisite/README.md).

**Can staff, partners and the public use the same installation?**
Yes. Roles, policies, sections and siteaccesses decide who sees what
([Roles, policies and unactivated users](../../guides/roles-and-policies.md)). See [Intranets](intranets/README.md)
and [Extranets](extranets/README.md).

## Data, security and compliance

**Where is our data?**
In your database and your file storage, on the server you choose. Nothing is sent elsewhere unless you configure it,
for example a payment gateway or the optional writing assistant, which "sends nothing anywhere until you add a
provider key" (`README.md`, "Exponential Intelligence"). See [Personal data in Exponential](security/personal-data-in-exponential.md).

**Does Exponential make us GDPR compliant?**
No software does that alone. It gives you tools (permissions, an audit trail, account management) that help; the
obligations are yours. See [GDPR basics](security/gdpr-basics.md), which is basics, not legal advice.

**How are security problems handled?**
Report privately to security@se7enx.com, with "a first answer within one business day"; fixes are published with the
next 6.0.x release (`SECURITY.md`). See [Security updates and disclosure](security/security-updates-and-disclosure.md).

**Can we prove who changed what?**
Yes, with the tamper-evident audit trail ([The audit trail](../../features/6.0/audit-trail.md)).
See [Compliance evidence from the audit trail](security/compliance-evidence-from-the-audit-trail.md).

## People and support

**Who stands behind Exponential?**
7x leads the project and the nonprofit Exponential Foundation operates it ([Who is 7x?](../who-is-7x.md),
[Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).

**Where do we get support?**
From your partner or hosting company; from 7x, which sells "support and maintenance contracts for existing sites" and
managed hosting ([Who is 7x?](../who-is-7x.md#services-and-hosting)); and from the community: the forums at
<https://share.exponential.earth/forums>, the Telegram chat and the issue trackers (`README.md`, "Where to get more
help", "Issue tracker"). See [Working with 7x and the project](choosing/working-with-7x-and-the-project.md).

**What if our partner stops working with us?**
The code is free software and the content is in your database, so another partner can take over. Make sure you have
access to your repository, your server and your backups. See [Choosing a partner](choosing/choosing-a-partner.md).

**Can we fund the project?**
Yes: sponsorships and donations are listed in `README.md`, "Donate and make a support subscription"
(<https://github.com/sponsors/se7enxweb> among others).

## Moving to Exponential

**We have a site from the eZ Publish years. Can we keep it?**
Yes. The installation book has migration chapters for the 4.x line, the 5.x legacy stack and eZ Platform or Ibexa
([14. Migrating from the 4.x line](../../install/14-migrating-from-4x.md),
[15. Migrating from the 5.x legacy stack](../../install/15-migrating-from-5x-legacy.md),
[16. Migrating from eZ Platform and Ibexa](../../install/16-migrating-from-ez-platform-and-ibexa.md)).

**We use another system. How do we switch?**
See [Switching to Exponential](choosing/switching-to-exponential.md).

## Related pages

- [Businesses: start here](businesses-start-here.md)
- [Businesses glossary](businesses-glossary.md)
- [Website creator FAQ](../creators/website-creators/website-creator-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
