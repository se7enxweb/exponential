---
title: Customers FAQ
audience: customer, businesses
category: customer-success
order: 3
summary: Answers to the questions site owners on Exponential ask most: costs and licenses, upgrades, backups, who to call, reporting bugs and security problems, the store, languages, assistants and moving to another provider.
keywords: FAQ, questions, answers, site owner, license, cost, upgrade, backup, support, bug, security report, store, VAT, GDPR, languages, Exponential Intelligence, partner, host, move
type: help
---
# Customers FAQ

Short answers to the questions site owners ask most, each with the page that explains more.

## The software

**Do we pay a license fee for Exponential?**
No. Exponential is free software under the GNU General Public License, and using it under the GPL is free of charge
(`README.md`, "License"). You pay for the work around it: building, hosting, support and upkeep.
See [What you own](what-you-own.md).

**Who makes Exponential?**
It is led by the company 7x and operated by the nonprofit Exponential Foundation; see
[Who is 7x?](../who-is-7x.md) and [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md).

**Which version should we run?**
The maintained line is Exponential 6.0; security fixes are published with the next 6.0.x release, and older major
lines are not supported (`SECURITY.md`). See [Release line and support](healthy/release-line-and-support.md).

**Our version number has not changed for a long time. Is that a problem?**
It can be. Fixes reach you only through upgrades, because a published version is never changed (`SECURITY.md`). Ask
your provider for the upgrade calendar ([How updates work](healthy/how-updates-work.md)).

## Upkeep

**Who does the upgrades?**
Usually your host or partner; your part is to agree on the timing, to test and to accept
([Upgrades with your provider](healthy/upgrades-with-your-provider.md)).

**How do we know our backups work?**
Only by restoring one. Ask for a restore test at least once a year, and after every change of host
([Restore test](healthy/restore-test.md)).

**The site is down. What do we do first?**
Follow [When the site is down](healthy/when-the-site-is-down.md): check from another network, call the contact on
your escalation list, and note the time and what you saw.

**Whom do we call for what?**
Content questions: your own team or your partner. Code, templates and features: your partner. Server, mail,
certificates and backups: your host. A bug in Exponential itself: the issue tracker. The full table is in
[When to call whom](partners-and-hosts/when-to-call-whom.md).

## Problems and reports

**We found a bug. Where do we report it?**
First to your partner, who can tell whether it is in your site's code or in Exponential. Bugs in Exponential go to
<https://github.com/se7enxweb/exponential/issues>; the help's report page (`/exphelp/report`) prepares the report
([GitHub issues](healthy/github-issues.md), [The help's report page](healthy/the-help-report-page.md)).

**We think we found a security problem.**
Do not report it in public. Send it by e-mail to security@se7enx.com, as `SECURITY.md` asks, with the version and
the steps, and without passwords or personal data ([Private security reports](healthy/private-security-reports.md)).

**The site shows an error with a reference. What is it?**
A reference your administrator can look up in the logs. Copy it into your report
([Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md)).

## Store

**Where do we see how the store is doing?**
In the Store dashboard, the first entry of the **Store** tab (`doc/eng-us/features/6.0/store-dashboard.md`;
[Store reports](commerce/store-reports.md)).

**Does Exponential handle VAT for us?**
It applies the VAT types and rules you set up. Which rates and rules are right for you is a question for your
accountant; the pages describe the software, not the law ([VAT types and rules](commerce/vat-types-and-rules.md)).

**A customer asks for all the data we hold about them.**
See [GDPR requests](commerce/gdpr-requests.md) and [Data export and erasure](commerce/data-export-and-erasure.md).
These pages are not legal advice.

## Content and growth

**Can editors lay out pages without a developer?**
Yes, with Exponential Layouts, where your site uses it ([Landing pages with Layouts](growth/landing-pages-with-layouts.md)).

**Can the site write or translate for us?**
Exponential Intelligence writes drafts, never publishes on its own, and sends nothing anywhere until a provider key
is added (`README.md`, "Exponential Intelligence"). See [Exponential Intelligence for owners](growth/exponential-intelligence.md).

**Can we add a language or a second site later?**
Yes; both are projects with your partner ([Adding a language](growth/adding-a-language.md),
[Adding a site](growth/adding-a-site.md)).

**Can an app read our content?**
Yes, through the REST API ([REST API for owners](growth/rest-api-for-owners.md)).

## Providers

**Can we change our partner or host?**
Yes. The software is free and any provider can work on it. Prepare with the [handover checklist](partners-and-hosts/handover-checklist.md).

**What should our support agreement say?**
See [Contracts and SLAs](partners-and-hosts/contracts-and-slas.md) and the [SLA template](partners-and-hosts/sla-template.md).
They are templates, not legal advice.

## Related pages

- [Customers glossary](customers-glossary.md)
- [Customers: start here](customers-start-here.md)
- [Store FAQ](commerce/store-faq.md)
- [Businesses FAQ](../businesses/businesses-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
