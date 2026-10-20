---
title: Adding a site
audience: customer, businesses, hosting
category: customer-success
order: 7
summary: What a site owner arranges when a second brand, region or microsite is added to an Exponential installation that already runs - the siteaccess model in brief, what the business must provide, who does each step, shared content and teams, and the first month.
keywords: adding a site, multisite, second brand, microsite, siteaccess, domain, certificate, shared content, per-brand design, site owner
type: help
---
# Adding a site

This page is for the owner who wants a second brand, a regional site or a campaign microsite on the installation the
organization already runs. One Exponential installation can serve many sites: each is a **siteaccess**, a way into
the installation with its own name, address, language list, design and start page, usually sharing the code and the
database ([One installation, many sites](../../businesses/multisite/one-installation-many-sites.md)). Your partner
and host do the technical steps; you provide the decisions, the domain and the content.

## Contents

1. [Is a new site the right answer?](#is-a-new-site-the-right-answer)
2. [What the business must arrange](#what-the-business-must-arrange)
3. [The steps, and who does them](#the-steps-and-who-does-them)
4. [Teams and shared content](#teams-and-shared-content)
5. [The first month](#the-first-month)

## Is a new site the right answer?

| You want | Often enough | A new site when |
|---|---|---|
| A campaign | a landing page with a time rule ([Landing pages with layouts](landing-pages-with-layouts.md)) | it needs its own domain and design for months |
| A language | a language siteaccess ([Adding a language](adding-a-language.md)) | the market needs different content, not a translation |
| A department's pages | a folder and a section | the department is a separate brand to the public |
| A partner or member area | a members-only section ([Member areas and registration](member-areas-and-registration.md)) | it needs its own address and sign-in |

## What the business must arrange

From [One installation, many sites](../../businesses/multisite/one-installation-many-sites.md):

- [ ] **Domain names**: registered and pointing to the server.
- [ ] **Certificates**: every host name the brand uses is covered by HTTPS.
- [ ] **Analytics and search console accounts** per brand, if you measure brands separately.
- [ ] **Legal pages** per brand (imprint, privacy notice, terms), written by the brand owner.
- [ ] **A brand owner** who approves content and the launch.

## The steps, and who does them

Summarized from the same page:

| Step | Who |
|---|---|
| 1. A folder for the brand under the root | editor or site builder |
| 2. A settings directory for the brand: name, address, start page, design | developer |
| 3. The brand added to the list of siteaccesses, its host or path mapped to it | developer or administrator |
| 4. The host name in DNS, the web server or Velocity, and the certificate | hosting |
| 5. Caches cleared and the new address tested | administrator |
| 6. The brand added to the administration's site list, so previews and links can use it | administrator |

The exact settings are on [Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md).
A test host or staging address lets you see the brand before it is public.

## Teams and shared content

- Each brand has its own start page and menu; editors work in the brand's folder, and roles can keep them there
  ([One installation, many sites](../../businesses/multisite/one-installation-many-sites.md)).
- Content can be shared across brands on purpose, by a second location or by relations
  ([Shared content across brands](../../businesses/multisite/shared-content-across-brands.md)).
- Each brand can have its own design, built on the design fallback chain
  ([Per-brand designs](../../businesses/multisite/per-brand-designs.md)).
- Settings, logs and caches can be kept per site
  ([Per-site settings and logs](../../businesses/multisite/per-site-settings-and-logs.md)).
- Links between the brands: [Cross-site links](../../businesses/multisite/cross-site-links.md).

Update the [Team roles worksheet](../day-to-day/team-roles-worksheet.md): who edits which brand, who approves.

## The first month

- [ ] The weekly routine covers the new brand ([Weekly owner routine](../day-to-day/weekly-owner-routine.md)).
- [ ] Search statistics are recorded on the new public siteaccess, if you use them.
- [ ] The sitemap of the new brand is generated and submitted ([XML sitemaps](../../creators/website-creators/sitemaps.md)).
- [ ] Backups and monitoring cover the new host name; ask your host.
- [ ] The full [Multi-brand checklist](../../businesses/multisite/multi-brand-checklist.md) is done.

## Related pages

- [Multi-brand planning](../../businesses/multisite/multi-brand-planning.md)
- [Multisite on one installation for hosts](../../hosting/provisioning/multisite-on-one-installation.md)
- [Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
