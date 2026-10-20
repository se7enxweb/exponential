---
title: "FAQ: many sites and many departments"
audience: government, admin, project-manager
category: public-sector
order: 180
summary: Answers to the questions public bodies ask about running many websites and departments on Exponential - several sites in one installation, separate designs and languages, shared content and users, permissions per department, separate logs and caches, and links between sites.
keywords: multisite FAQ, several sites, departments, agencies, siteaccess, shared content, shared users, permissions per department, sections, cross-site links, public sector
type: help
---
# FAQ: many sites and many departments

This page answers the questions public bodies ask when one organization runs several websites or many departments
publish on one site. The answers come from [Multisite setup](../../creators/website-creators/multisite-setup.md) and the
multisite feature pages.

## Contents

1. [Can one installation run several websites?](#can-one-installation-run-several-websites)
2. [Can each site have its own design and languages?](#can-each-site-have-its-own-design-and-languages)
3. [Can sites share content and users?](#can-sites-share-content-and-users)
4. [Can each department edit only its own content?](#can-each-department-edit-only-its-own-content)
5. [Are logs and caches separate per site?](#are-logs-and-caches-separate-per-site)
6. [Can one site link to another?](#can-one-site-link-to-another)
7. [One installation or several?](#one-installation-or-several)

## Can one installation run several websites?

Yes. Each site is reached through its own siteaccess, matched by host, path or port
([Multisite setup](../../creators/website-creators/multisite-setup.md),
[Siteaccess host and language matching](../../../features/6.0/siteaccess-host-and-language-matching.md)).

## Can each site have its own design and languages?

Yes: settings per siteaccess choose the design, languages and other settings
([Multi-site INI overrides](../../../features/6.0/multi-site-ini-overrides.md)).

## Can sites share content and users?

Yes. They share one content repository and one user base; an object can appear on several sites through locations,
and a user can sign in where their roles allow ([Relations and reuse](../../creators/website-creators/relations-and-reuse.md)).

## Can each department edit only its own content?

Yes, with sections and roles limited to a section or subtree ([Sections](../../../guides/sections.md),
[Roles and policies](../../../guides/roles-and-policies.md)). The [Onboarding a department](../checklists/onboarding-a-department.md)
checklist shows the steps.

## Are logs and caches separate per site?

They can be: log and cache paths can be set per site ([Multi-site log and cache paths](../../../features/6.0/multi-site-log-and-cache-paths.md)).

## Can one site link to another?

Yes, with cross-site links that resolve to the right host ([Cross-site links in a multisite](../../../features/6.0/multisite-cross-site-links.md)).

## One installation or several?

One installation saves upgrades and hosting, and makes sharing easy. Several installations isolate risk, permissions
and release schedules. Hosts describe both ([Multisite on one installation](../../hosting/provisioning/multisite-on-one-installation.md),
[Multi-tenant isolation](../../hosting/security/multi-tenant-isolation.md)). Training exists for either
([Multisite and site packages](../../trainers/multisite-and-packages/README.md)).

## Related pages

- [Government FAQ](README.md)
- [Multisite setup](../../creators/website-creators/multisite-setup.md)
- [Onboarding a department](../checklists/onboarding-a-department.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
