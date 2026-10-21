---
title: Non-profits glossary
audience: non-profit, businesses, government, editor
category: business-solutions
order: 95
summary: The words a non-profit meets when it runs its website on Exponential, explained in plain language - from administration interface, audit trail and CAPTCHA to information collector, object state, role, section, siteaccess, SQLite, Velocity and version - each with the page that explains it in full.
keywords: glossary, terms, words, definitions, non-profit, administration interface, audit trail, CAPTCHA, class, collected information, cronjob, double opt-in, extension, information collector, layout, object, role, section, siteaccess, SQLite, Velocity, version
type: help
---
# Non-profits glossary

The words this section uses, in alphabetical order. Each entry says what the word means for a non-profit and links to
the page that explains it. The general glossaries are the [Businesses glossary](../businesses/businesses-glossary.md),
the [Customers glossary](../customers/customers-glossary.md) and the [Exponential glossary](../../glossary.md).

## Contents

1. [A to D](#a-to-d)
2. [E to L](#e-to-l)
3. [M to R](#m-to-r)
4. [S to Z](#s-to-z)

## A to D

**Administration interface.** The part of the site, in a browser, where staff and volunteers edit content and change
settings. See the [Admin tour](../admin-tour.md).

**Alternative text.** A short description of an image for people who cannot see it. Image fields can be set to
require it; see [Alternative text for images](../government/accessibility/alternative-text-for-images.md).

**Approval workflow.** A setting that holds new or changed content until a named person approves it. See
[Approvals and review](../customers/day-to-day/approvals-and-review.md).

**Audit trail.** A tamper-evident record of who did what in the administration: sign-ins, publishing, removals,
settings, exports. On by default. See [The audit trail](../../features/6.0/audit-trail.md).

**CAPTCHA.** A small test on a form that keeps automated spam out. Exponential ships two: `hcaptcha` and `recaptcha`.
See [Spam protection for volunteer forms](volunteers/spam-protection-for-volunteer-forms.md).

**Class.** The definition of a kind of content: an article, an event, a file, a volunteer form. Each class lists its
fields (attributes). See [Content classes for websites](../creators/website-creators/content-classes-for-websites.md).

**Collected information.** The submissions sent through forms, read in **Setup > Collected information**. See
[Collected information](../../guides/collected-information.md).

**Cronjob.** A task the server runs on a schedule: sending newsletters, publishing at a date, cleaning up. If cron does
not run, these things do not happen. See [Cronjobs](../../guides/cronjobs.md).

**Double opt-in.** A subscriber confirms the subscription by clicking a link in a mail before receiving newsletters.
`cjw_newsletter` works this way. See [Supporter newsletter](communications/supporter-newsletter-with-cjw-newsletter.md).

**Draft.** A version being edited that is not yet published. See [Drafts, versions and undo](../customers/day-to-day/drafts-versions-and-undo.md).

## E to L

**Extension.** A package that adds features. The extensions this section names (`cjw_newsletter`, `ezpaypal`,
`ezauthorize`, `hcaptcha`, `recaptcha`, `enhancedezbinaryfile`, `ezwebin`) are installed through `composer.json`.

**Exponential Layouts.** The visual page builder for landing and campaign pages. See
[Landing pages with Layouts](../creators/website-creators/landing-pages-with-layouts.md).

**Exponential Intelligence.** A writing assistant in the administration that writes drafts only, never publishes. See
[Exponential Intelligence](../customers/growth/exponential-intelligence.md).

**Information collector.** The mechanism behind forms: fields of a class marked "Information collector" become a
form, and each submission is stored. See [Forms with the information collector](../creators/website-creators/forms-with-the-information-collector.md).

**Layout rule.** A rule that says which layout a page uses; it can carry a time condition so a campaign switches on and
off by date. See [Layout rules](../creators/website-creators/layout-rules.md).

## M to R

**Members area.** Pages only signed-in users of a group can see, built with sections and roles. See
[Member registration and areas](../creators/website-creators/member-registration-and-areas.md).

**Object.** One piece of content: one article, one event, one annual report file.

**Object state.** A label such as "draft for review" or "archived" that roles can use. See
[Sections and object states](../creators/website-creators/sections-and-object-states.md).

**Partner.** A company in the Exponential Partner Program that builds, hosts or supports sites. See
[Working with a partner](operations/working-with-a-partner.md).

**Payment gateway.** The connection between the shop and a payment provider: `ezpaypal` (PayPal) and `ezauthorize`
(Authorize.Net). See [Payment gateways](../creators/website-creators/payment-gateways.md).

**Role.** A set of permissions, assigned to a user or group, perhaps limited to a section or a part of the tree. See
[Roles for your team](../customers/day-to-day/roles-for-your-team.md).

**RSS feed.** A machine-readable list of new items that readers and other sites can follow. See
[RSS feeds for your site](../creators/website-creators/rss-feeds-for-your-site.md).

## S to Z

**Section.** A label on a part of the content tree (for example "Board" or "Volunteers only") that roles can limit by.
See [Sections and object states](../creators/website-creators/sections-and-object-states.md).

**Siteaccess.** One way into the installation, such as the public site, a second language or the administration.

**SQLite.** The default database: the whole site lives in one file, with no database server. See
[SQLite database support](../../features/6.0/sqlite-database.md).

**Two-factor login.** Sign-in with a password and a code from a phone app. See
[Two-factor login](../../guides/two-factor-login.md).

**Velocity.** Exponential's own web server, written in PHP. See
[Velocity web server](../../features/6.0/velocity-web-server.md).

**Version.** Every time content is published, the earlier state is kept as a version that can be compared or
restored. See [Versions of an object](../../guides/content-history.md).

## Related pages

- [Non-profits: start here](non-profits-start-here.md)
- [Non-profits FAQ](non-profits-faq.md)
- [Businesses glossary](../businesses/businesses-glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
