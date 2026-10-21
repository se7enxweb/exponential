---
title: Project launch checklist
audience: cms-developer
category: cms-development
order: 46
summary: For CMS developers taking a site live, one checklist from a week before launch to the week after. It covers content and model, templates and design, settings, server and caches, cronjobs, security, search, SEO and redirects, accessibility, backups, handover to editors, and what to watch after launch.
keywords: launch, go live, go-live checklist, launch checklist, production, handover, cut-over, deployment, cronjobs, backups, debug output, redirects, sitemap, monitoring, editors, training, after launch
type: help
---
# Project launch checklist

This page is for developers about to take a site live. Copy it into your project, tick each item, and write down who
checked it. Each item links to the page that explains it. Hosts have their own list for the server side
([New customer site go-live](../hosting/checklists/new-customer-site-go-live.md)).

## Contents

1. [A week before](#a-week-before)
2. [Content and model](#content-and-model)
3. [Templates and design](#templates-and-design)
4. [Settings](#settings)
5. [Server, caches and cron](#server-caches-and-cron)
6. [Security](#security)
7. [Search, SEO and redirects](#search-seo-and-redirects)
8. [Accessibility](#accessibility)
9. [Backups and handover](#backups-and-handover)
10. [Launch day](#launch-day)
11. [The week after](#the-week-after)

## A week before

- [ ] The live machine is deployed the way it will run: same server engine, same PHP, same database engine as staging
      ([Deploying a site](deploying-a-site.md)).
- [ ] The project's repositories are tagged or recorded at the commit that goes live.
- [ ] A rollback plan exists: database dump, previous code, the steps and who decides.

## Content and model

- [ ] The content model passed its review ([Content model review checklist](content-model-review-checklist.md)).
- [ ] Classes and starter content are in packages in the repository; layouts exported ([Site packages and installers](site-packages-and-installers.md)).
- [ ] Test and demo content removed or hidden; the trash emptied of test items.
- [ ] Imported content checked by an editor on a sample; inner links and images resolved ([Importing content](importing-content.md)).

## Templates and design

- [ ] Every class has a full view and the list views it needs; no generic attribute dumps on public pages
      ([View modes](view-modes.md)).
- [ ] Error pages (404, access denied) designed ([Error templates](../designers/templates/error-templates.md)).
- [ ] No inline CSS or JavaScript; assets through the packer; no `?v=` on asset links.
- [ ] Pages checked in light and dark mode, at 1440 px and at 200% zoom.

## Settings

- [ ] Debug output and `ShowUsedTemplates` off; `TemplateCompile` on.
- [ ] Site names, `MetaDataArray`, site URLs and mail senders set for live; test mail goes to `example.invalid` only
      until launch.
- [ ] `settings/override/` written on the machine, not copied; per-environment variants in place
      ([Settings and INI for CMS developers](settings-and-ini-for-cms-developers.md)).
- [ ] `ManagedByDeployment=enabled` where configuration comes from version control.

## Server, caches and cron

- [ ] Velocity (or the chosen server) runs with OPcache; `exp:cache status` shows the expected layers
      ([Performance](performance.md)).
- [ ] Benchmark of the main pages recorded as the baseline (`./console exp:benchmark`).
- [ ] Cronjobs scheduled for every public siteaccess; workflows, notifications and delayed indexing tested with cron
      running ([Cronjobs](cronjobs.md)).
- [ ] Caches warmed after the final deployment ([Preloading caches](../../guides/preloading-caches.md)).

## Security

- [ ] The [Security checklist](security-checklist.md) is complete, including the go-live hardening.
- [ ] The installer's administrator password changed; personal administrator accounts with two-factor sign-in.
- [ ] Roles tested with test accounts; the Anonymous role reviewed.

## Search, SEO and redirects

- [ ] The search index rebuilt after the last import; the search page tested ([Search](search.md)).
- [ ] Redirects for old addresses in place; canonical links, titles and descriptions checked
      ([SEO basics](seo-basics.md)).
- [ ] `robots.txt` has the live rules and the sitemap address; no rule left over that blocks the whole site.

## Accessibility

- [ ] Keyboard, zoom and contrast tested on the main templates; forms labeled ([Accessibility](accessibility.md)).

## Backups and handover

- [ ] Backups of database and storage run, leave the machine, and one restore was tested
      ([What to back up](../hosting/operations/backups-what-to-back-up.md)).
- [ ] Editors trained and given their own accounts; a short guide to the site's classes and layouts handed over
      ([Editors 101](../trainers/editors-101/README.md)).
- [ ] The project documentation (model, extensions, deployment, cron, contacts) is in the repository.

## Launch day

1. Freeze content changes on the old site; run a final delta import if migrating.
2. Deploy, switch DNS or the proxy, check HTTPS.
3. Check the front page, a deep page per class, search, a form, sign-in, the administration.
4. Watch `var/log/error.log` and the 404s for the first hours.

## The week after

- [ ] 404 list reviewed daily; missing redirects added.
- [ ] Error log and audit alerts read.
- [ ] Cron runs confirmed in the Cronjobs page.
- [ ] Benchmark repeated under real traffic and compared with the baseline.
- [ ] Feedback from editors collected and the first fixes planned.

## Related pages

- [Migration checklist](../../install/migration/checklist.md)
- [Security hardening go-live checklist](../../install/13-security-hardening.md#1316-go-live-checklist)
- [Checklist: theme launch](../designers/theme-design/checklist-theme-launch.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
