---
title: Sovereignty checklist
audience: government, project-manager, admin
category: public-sector
order: 230
summary: One checklist for a public body that wants to stay in control of its Exponential platform: license and code, hosting and data location, external services, formats and exports, skills and providers, updates and exit, each linked to the page that explains it.
keywords: checklist, sovereignty, independence, lock-in, self-hosting, data location, third parties, exit plan, skills, updates, review
type: help
---
# Sovereignty checklist

This checklist is for managers and administrators. Use it when you start a project, at each contract renewal and once
a year. Each item names the page of this section that explains it.

## Contents

1. [License and code](#license-and-code)
2. [Hosting and data](#hosting-and-data)
3. [External services](#external-services)
4. [Formats and exports](#formats-and-exports)
5. [People and providers](#people-and-providers)
6. [Updates and exit](#updates-and-exit)

## License and code

- [ ] The license situation is documented for management (see "The open source license (GPL)").
- [ ] All custom code is in a repository the body owns (see "Avoiding vendor lock-in").
- [ ] No kernel patches; customizations are extensions and overrides.
- [ ] A copy of every package the installation depends on is kept (see "Composer packages and source on GitHub").

## Hosting and data

- [ ] The hosting model and provider are chosen and documented (see "Government cloud and hosting models").
- [ ] The data location statement lists every place data is kept, including backups (see "Data location").
- [ ] The database choice is documented with its restore procedure (see "Database choice").

## External services

- [ ] The active extensions are known; each third-party service was reviewed and approved (see "Third-party
  services to review").
- [ ] Public designs load no assets from other hosts without a decision (see "No required external services").

## Formats and exports

- [ ] Main content types can be exported as CSV or JSON today (see "Exporting content and packages").
- [ ] Documents are published in open formats, tables also as CSV (see "Open formats and data portability").

## People and providers

- [ ] Two people per key role hold the necessary skills (see "Keeping the skills in house").
- [ ] Providers are chosen by documented criteria; responsibilities written down (see "Choosing a support provider").
- [ ] Contracts include source delivery and exit assistance (see "Open source procurement").
- [ ] The body has decided how it contributes back (see "Contributing back as a public body").

## Updates and exit

- [ ] Security releases are watched and applied within a stated time (see "Security updates and independence").
- [ ] An exit plan exists and a restore test was done this year (see "Exit planning").

## Related pages

- [Digital sovereignty at a glance](digital-sovereignty-at-a-glance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
