---
title: Go-live checklist for a public site
audience: government, admin, project-manager, editor, hosting
category: public-sector
order: 20
summary: Everything to check before a public body opens an Exponential site to the public - content, accessibility, security, privacy, operations, monitoring and sign-off - as an actionable checklist with the page that explains each item.
keywords: go-live, launch, checklist, public site, production, sign-off, HTTPS, backups, accessibility statement, privacy notice, monitoring, public sector
type: help
---
# Go-live checklist for a public site

This checklist is for the project manager and site administrator of a public body in the last weeks before launch.
It adds the public-sector items (accessibility statement, privacy notice, records, sign-off) to the general launch
lists in [Launch checklist](../../creators/website-creators/launch-checklist.md) and, for hosts,
[New customer site go-live](../../hosting/checklists/new-customer-site-go-live.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Content](#content)
2. [Accessibility](#accessibility)
3. [Security](#security)
4. [Privacy](#privacy)
5. [Operations](#operations)
6. [Launch day](#launch-day)
7. [Sign-off](#sign-off)

## Content

- [ ] Every page in the content inventory is migrated, reviewed and published ([Site map and content inventory](../../creators/website-creators/site-map-and-content-inventory.md)).
- [ ] Redirects from old addresses are in place ([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)).
- [ ] No broken links or relations: **Setup > Link management** and **Broken relations** ([Content checks](../../../features/6.0/content-checks.md)).
- [ ] Contact details, opening hours and legal pages (imprint, terms) are current.
- [ ] Test content and test accounts are removed.
- [ ] Every language version is complete, or the missing ones are hidden ([Content languages](../../../guides/content-languages.md)).

## Accessibility

- [ ] Key templates and pages tested against WCAG 2.2 AA ([Testing accessibility](../../creators/website-creators/testing-accessibility.md)); the public design is your responsibility, it is not audited by the project ([Accessibility standards](../../../standards/accessibility.md#known-gaps)).
- [ ] Images have alternative text; headings are in order; link texts make sense alone.
- [ ] An accessibility statement is published; Exponential ships no template for it (W3C guidance: [w3.org/WAI/planning/statements](https://www.w3.org/WAI/planning/statements/)).
- [ ] The full list is in the [Accessibility checklist](accessibility-checklist.md).

## Security

- [ ] HTTPS everywhere; HSTS set once the whole site is HTTPS ([TLS and HTTPS](../../hosting/security/tls-and-https.md), [Security standards](../../../standards/security.md#known-gaps)).
- [ ] The initial administrator password is changed and `var/log/initial-admin-password` removed ([Installing Exponential in one command](../../../features/6.0/install-in-one-command.md#safer-first-login)).
- [ ] Debug output off for visitors; hardening applied ([13. Security hardening for production](../../../install/13-security-hardening.md)).
- [ ] Administrator accounts use two-step sign-in where your site has it ([Two-step sign-in](../../../guides/two-factor-login.md)).
- [ ] Audit trail on and alert recipients set: `./console exp:audit status` and `./console exp:audit alerts recipients` ([Security and audit](../../../guides/security-and-audit.md)).
- [ ] The full list is in the [Security checklist](security-checklist.md).

## Privacy

- [ ] Privacy notice published, naming every third party the site uses ([Privacy standards](../../../standards/privacy.md#third-party-services-in-shipped-extensions)).
- [ ] Cookie consent in place if the site adds analytics, embeds or advertising; Exponential has no banner of its own ([Privacy standards](../../../standards/privacy.md#status-1)).
- [ ] Forms show a consent text and have a retention rule ([Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md)).
- [ ] The full list is in the [Privacy checklist](privacy-checklist.md).

## Operations

- [ ] Backups run and one restore has been tested ([Backups and restores](../../creators/website-creators/backups-and-restores.md)).
- [ ] Cronjobs run: **Setup > Cronjobs** ([Cronjobs](../../../guides/cronjobs.md)).
- [ ] **Setup > System information** shows no open health check ([System information](../../../guides/system-information.md)).
- [ ] An external uptime check watches a page that touches the database ([Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md)).
- [ ] The incident contact list and runbooks are known ([Incident readiness](incident-readiness.md)).
- [ ] Caches are warmed so the first visitors do not wait ([Preloading caches](../../../guides/preloading-caches.md)).

## Launch day

- [ ] Switch DNS in a quiet period; keep the old site read-only until sign-off.
- [ ] Check the home page, search, a form, sign-in and a download from outside your network.
- [ ] Watch **Setup > Log files** and the audit dashboard (`/audit/dashboard`) for the first hours ([Log viewer](../../../features/6.0/log-viewer.md)).
- [ ] Announce the site through your organization's channels.

## Sign-off

- [ ] Acceptance tests passed and recorded.
- [ ] Documentation and administrator access handed over to named people in your organization.
- [ ] Editors trained ([Training in procurement](../procurement/training/README.md)).
- [ ] The exit plan exists ([Exit readiness](exit-readiness.md)).

## Related pages

- [Government checklists](README.md)
- [Launch checklist](../../creators/website-creators/launch-checklist.md)
- [Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
