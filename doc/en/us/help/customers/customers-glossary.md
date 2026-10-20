---
title: Customers glossary
audience: customer, businesses, editor
category: customer-success
order: 4
summary: The words a site owner meets when running a site on Exponential, explained in plain language: from administration, approval and audit trail to version, VAT type and workflow.
keywords: glossary, terms, words, definitions, site owner, administration, siteaccess, content class, node, version, draft, workflow, role, policy, section, cache, upgrade, VAT, order status
type: help
---
# Customers glossary

The words you will hear from your team, your partner and your host, in plain language. Each entry links to a page
that explains more. The full glossary of the documentation is in `doc/eng-us/glossary.md`.

## A to C

**Administration**: the part of the site where your team works, usually at `/admin` after your site's address. See
[A tour of the administration](../admin-tour.md).

**Approval**: a step that holds a new version until someone with the right to approve accepts it. It is a workflow
event called *Approve* ([Workflows](../../guides/workflows.md), [Approvals and review](day-to-day/approvals-and-review.md)).

**Audit trail**: the tamper-evident record of who did what in the administration, with its own **Audit** tab
([Audit trail](../../features/6.0/audit-trail.md)).

**Backup**: a copy of the database and the stored files from which the site can be restored. Only a restore proves
a backup ([Backups](healthy/backups.md)).

**Cache**: stored results that make pages fast. After some changes the cache is cleared so visitors see the new
version; your administrator does this ([Speed and caching](healthy/speed-and-caching.md)).

**Content class**: the kind of a content item (Article, Product, Folder ...) and the fields it has. Your site's
classes were chosen when it was built ([Content classes for websites](../creators/website-creators/content-classes-for-websites.md)).

**Content item** (also *object*): one piece of content, such as an article or a product, with its versions and
translations.

**Content tree**: the hierarchy in which items are placed; one item can have places in several branches.

## D to M

**Draft**: a version being written that the public does not see ([Publishing and versions](../publishing-and-versions.md)).

**Exponential Intelligence**: the writing assistant built into the administration; it writes drafts only and sends
nothing until a provider key is added (`README.md`) ([Exponential Intelligence for owners](growth/exponential-intelligence.md)).

**Exponential Layouts**: the visual page builder with layouts, zones and blocks ([The Layouts editor](../layouts-editor.md)).

**Exponential Velocity**: an application server of Exponential's own, next to Apache, Nginx and the others
(`README.md`, "How Exponential is served").

**Extension**: a package that adds features, designs or settings to the installation. Most of the code written for
your site lives in extensions ([What you own](what-you-own.md)).

**Hosting company (host)**: the provider that runs the server, the database and usually the backups.

**Implementation partner (partner)**: the company that built or maintains your site's code and design.

**Maintenance mode**: a state in which visitors see a maintenance page while work is done; the administration can
stay reachable (**Setup > Maintenance**, [Maintenance mode](../../features/6.0/maintenance-mode.md)).

## N to S

**Node** (also *location*): one place of a content item in the tree, with its own number.

**Object state**: a label on a content item (such as Draft, Review, Released) that roles can use to decide who may
do what ([Object states](../../guides/object-states.md)).

**Order status**: the stage of an order in the store, from received to delivered ([Order statuses](commerce/order-statuses.md)).

**Payment gateway**: the connection between the store and a payment provider ([Payment gateways](commerce/payment-gateways.md)).

**Policy**: one permission inside a role, such as "may edit content in this section" ([Roles and policies](../../guides/roles-and-policies.md)).

**Release**: a published version of Exponential, such as 6.0.14. A published release never changes (`SECURITY.md`).

**REST API**: the interface through which apps and other systems read and write content ([REST API for owners](growth/rest-api-for-owners.md)).

**Role**: a set of policies given to users or user groups ([Roles for your team](day-to-day/roles-for-your-team.md)).

**Section**: a label on a part of the content tree that roles use to separate what people may see or edit
([Sections](../../guides/sections.md)).

**Siteaccess**: one way into the installation (a public site, a language, the administration), matched by address
or host. Several sites of one installation are several siteaccesses.

**SLA (service level agreement)**: the part of a contract that says what a provider promises and how fast
([Contracts and SLAs](partners-and-hosts/contracts-and-slas.md)).

## T to Z

**Translation**: a content item in another language; each item can have several ([Languages](../languages.md)).

**Upgrade**: moving the installation to a newer release of Exponential ([How updates work](healthy/how-updates-work.md)).

**Upgrade check**: **Setup > Upgrade check**, which compares the installed files and the database with what the
release expects ([Upgrade check](../../guides/upgrade-check.md)).

**URL alias**: the readable address of a page, such as `/news/our-new-shop` ([URLs and aliases](../../guides/urls-and-aliases.md)).

**VAT type and VAT rule**: the tax rates and the rules that pick one for a product and a country in the store
([VAT types and rules](commerce/vat-types-and-rules.md)).

**Version**: one saved state of a content item. Publishing creates a new version and keeps the earlier ones.

**Workflow**: a list of steps (events) that runs when something happens, such as before publishing
([Workflows](../../guides/workflows.md)).

## Related pages

- [Customers FAQ](customers-faq.md)
- [Hosting glossary](../hosting/glossary-for-hosts.md)
- [Businesses glossary](../businesses/businesses-glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
