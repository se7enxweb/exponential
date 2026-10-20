---
title: "Governments: start here"
audience: government, project-manager, admin, editor
category: public-sector
order: 1
summary: The landing page for public bodies that run or plan to run Exponential - ministries, regions and states, councils, agencies, libraries, schools and universities, courts, parliaments, public health and emergency services. How this section is organized, what Exponential provides for public-sector requirements and what stays your responsibility, and reading paths by role.
keywords: government, public sector, public body, ministry, council, municipality, agency, library, school, university, court, parliament, public health, emergency services, accessibility, WCAG, EN 301 549, Section 508, security, privacy, GDPR, records, archiving, open data, FOI, FOIA, transparency, multilingual, plain language, digital sovereignty, self-hosting, open source, procurement, tender, RFP, vendor lock-in, start here, landing
featured: true
type: help
---
# Governments: start here

This section of the help is for **people who run public-sector websites and services with Exponential**, or who are
deciding whether to: web and digital teams in ministries, regional and state governments, councils and
municipalities, agencies, public libraries, schools and universities, courts, parliaments, public health bodies and
emergency services; the procurement officers and project managers who buy a system and the services around it; the
IT staff who host and operate it; and the editors who publish every day.

It answers three questions: what does Exponential provide for the requirements a public body has (accessibility,
security, privacy, records, transparency, languages, sovereignty), how do you build and run public-service sites
with it, and how do you buy, staff and eventually leave it responsibly.

> **Note**
> Every statement about Exponential in this section is taken from the repository (the standards pages in
> `doc/standards/`, the feature pages, the guides and the shipped settings) and links to its source. Laws and
> standards are named with a link to the official publication. Pages about laws describe features and common
> practice; they are **not legal advice**. No page describes a real government, deployment or customer: examples
> use obvious placeholders such as "your council" or `example.gov`.

## Contents

1. [Exponential for the public sector in one minute](#exponential-for-the-public-sector-in-one-minute)
2. [How this section is organized](#how-this-section-is-organized)
3. [What Exponential provides, and what stays with you](#what-exponential-provides-and-what-stays-with-you)
4. [Reading paths by role](#reading-paths-by-role)
5. [Your first month](#your-first-month)
6. [Related sections of the help](#related-sections-of-the-help)

## Exponential for the public sector in one minute

- **Open source you can run yourself.** The kernel is licensed under the GNU General Public License v2 (or any
  later version), and this documentation under the GNU Free Documentation License 1.3
  ([Free documentation](../free-documentation.md)). You can host it on your own servers or in a government cloud;
  the administration loads no scripts or fonts from other hosts
  ([Privacy standards](../../standards/privacy.md#gdpr)).
- **A content management kernel with a long history.** Exponential continues the code base of eZ Publish, which its
  creator discontinued after almost 18 years; 7x and the Exponential Foundation maintain it today
  ([Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).
- **Honest compliance statements.** The project publishes its own status against WCAG 2.2, EN 301 549, Section 508,
  the OWASP Top 10, the GDPR and more, with the evidence and the known gaps
  ([Standards](../../standards/README.md)). This section builds on those pages and never upgrades their status words.
- **Built for many sites and many languages.** One installation serves many siteaccesses (sites, languages,
  administration), with translations per content object and right-to-left support
  ([Internationalization standards](../../standards/internationalization.md),
  [Right-to-left languages](../../features/6.0/right-to-left.md)).
- **Accountable by default.** Every published change keeps its version history, and a tamper-evident audit trail is
  on by default ([Audit trail](../../features/6.0/audit-trail.md)).

## How this section is organized

| Section | What it answers |
|---|---|
| [Levels of government](levels/README.md) | What a ministry, a region or state, a council, an agency, a library, a school or university, a court, a parliament, a public health body or an emergency service typically publishes, a content model for it, roles, priorities and a first-90-days plan |
| [Accessibility](accessibility/README.md) | WCAG 2.2 AA, EN 301 549 and Section 508 mapped to what Exponential provides, with every claim backed by [Accessibility standards](../../standards/accessibility.md); statements, audits, accessible content |
| [Security](security/README.md) | OWASP, hardening, the audit trail, two-step sign-in, the Content Security Policy, request-shield |
| [Privacy](privacy/README.md) | GDPR features, data minimization, retention, consent records, erasure; other jurisdictions framed generally |
| [Records and archiving](records-and-archiving/README.md) | Versions, history, archiving notices, retention schedules, exports, preserving URLs |
| [Open data](open-data/README.md) | Publishing datasets with metadata, licenses and stable URLs, and the APIs and feeds that exist |
| [Transparency and FOI](transparency-and-foi/README.md) | Proactive disclosure, disclosure logs, publication schemes, decision registers |
| [Languages](languages/README.md) | Official languages, translation workflows, right-to-left, plain language |
| [Sovereignty](sovereignty/README.md) | Self-hosting, data location, open-source licensing and procurement, avoiding vendor lock-in |
| [Building public-service sites](building/README.md) | Service catalogs, forms, citizen accounts, news and alerts, events, documents, search, maps, newsletters, multi-site, intranets, extranets |
| [Operations](operations/README.md) | Hosting on-premise and in the government cloud, availability, backups, monitoring, incidents, upgrades, change management, staff roles, editorial workflows |
| [Procurement and lifecycle](procurement/README.md) | Requirements, tender templates, evaluation, total cost of ownership, exit plans, training, the product lifecycle |
| [Tasks](tasks/README.md) | Hundreds of step-by-step procedures: publish a council meeting agenda, add a service page with an accessible form, set up a department subsite, translate a page, archive last year's notices ... |
| [Checklists](checklists/README.md), [FAQ](faq/README.md) and [glossary](glossary/README.md) | Go-live, accessibility, security, privacy and readiness checklists; answers to common questions; terms |

## What Exponential provides, and what stays with you

| Requirement | What ships with Exponential | What stays with your organization | Source |
|---|---|---|---|
| Accessibility | admin4 and admin4l administration designed and regression-tested against WCAG 2.2 AA (status "Partly complies"); EN 301 549 and Section 508 "Unverified"; no VPAT or Accessibility Conformance Report | auditing your public design and content, publishing your accessibility statement | [Accessibility standards](../../standards/accessibility.md) |
| Security | mitigations for every OWASP Top 10 (2021) category, security headers, CSRF tokens, audit trail on by default, Content Security Policy (report-only on the administration by default) | enabling account lockout, two-step sign-in, HSTS and CSP enforcement; patching; monitoring | [Security standards](../../standards/security.md), [Security hardening](../security-hardening.md), then [Security](security/README.md) |
| Privacy | consent records for optional e-mail, erasure when an account is removed, pseudonymized audit records, masked logs with retention limits, only functional cookies from the kernel | purposes and legal bases, the privacy notice, the record of processing, any third-party service you add, requests from people | [Privacy standards](../../standards/privacy.md) |
| Records | version history per content object, trash and restore, object states and sections, tamper-evident audit records | your retention schedule, transfers to an archive | [Content history](../../guides/content-history.md), [Audit trail](../../features/6.0/audit-trail.md) |
| Languages | translations per object, a siteaccess per language, right-to-left interface and content | translation, terminology, plain-language editing | [Translations and languages](../../features/6.0/translations-and-languages.md) |
| Sovereignty | GPL source code, self-hosting, a choice of databases (SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL) | where you host, who supports you, your exit plan | [Database support](../database-support.md) |

## Reading paths by role

| If you are ... | Start with |
|---|---|
| a procurement officer or project manager | [Procurement and lifecycle](procurement/README.md), then the requirements and evaluation pages it lists |
| a head of digital or web manager | this page, [Levels of government](levels/README.md) for your kind of organization, then [Operations](operations/README.md) |
| an accessibility coordinator | [Accessibility standards](../../standards/accessibility.md), then [Accessibility](accessibility/README.md) |
| an information security officer | [Security standards](../../standards/security.md), [Security hardening](../security-hardening.md), then [Security](security/README.md) |
| a data protection officer | [Privacy standards](../../standards/privacy.md), then [Privacy](privacy/README.md) |
| a records manager or FOI officer | [Records and archiving](records-and-archiving/README.md) and [Transparency and FOI](transparency-and-foi/README.md) |
| a translation or communications lead | [Languages](languages/README.md), including plain language |
| an IT operations team | [Operations](operations/README.md) and the [hosting help](../hosting/hosting-companies-start-here.md), which covers servers in depth |
| an editor | the [tasks](tasks/README.md), and [Editing content](../editing-content.md) |
| a trainer | [Trainers: start here](../trainers/trainers-start-here.md) and [Training for a public body](procurement/training/README.md) |
| a website builder or supplier | [Website creators: start here](../creators/website-creators/website-creators-start-here.md) |

## Your first month

1. **Week 1: understand the evidence.** Read the four standards pages your requirements depend on:
   [accessibility](../../standards/accessibility.md), [security](../../standards/security.md),
   [privacy](../../standards/privacy.md) and [internationalization](../../standards/internationalization.md). Note
   each "Partly complies" or "Unverified" that matters to you.
2. **Week 2: try it.** Install a test site on your own machine with one command
   ([Install in one command](../../features/6.0/install-in-one-command.md)) and walk through the administration with
   the people who will edit ([Getting started](../getting-started.md), [Admin tour](../admin-tour.md)).
3. **Week 3: model your organization.** Read the page of your level of government in
   [Levels of government](levels/README.md): content model, sections and roles.
4. **Week 4: plan operations and people.** Read [Operations](operations/README.md) and decide who hosts, who
   backs up, who patches, who approves content and who trains editors
   ([Training for a public body](procurement/training/README.md)).

## Related sections of the help

- [Standards and compliance](../../standards/README.md)
- [Hosting companies: start here](../hosting/hosting-companies-start-here.md)
- [Trainers: start here](../trainers/trainers-start-here.md)
- [Website creators: start here](../creators/website-creators/website-creators-start-here.md)
- [Application creators: start here](../creators/app-creators/app-creators-start-here.md)
- [Partners](../partners/partners-start-here.md), for finding a support provider
- [Businesses: start here](../businesses/businesses-start-here.md), the companion section for companies, nonprofits and associations
- [Security hardening](../security-hardening.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
